<?php

namespace App\Http\Controllers;

use App\Models\Bom;
use App\Models\ProductService;
use App\Models\ProductionOrder;
use App\Models\ProductionConsumption;
use App\Models\ProductionOutput;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Support\TrashedSelect;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductionExport;

class ProductionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function nextCode(): string
    {
        $last = ProductionOrder::mine()->latest()->first();
        $n = $last ? ((int) filter_var($last->code, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;
        return 'PRD-' . str_pad($n, 4, '0', STR_PAD_LEFT);
    }

    private function statusToString($status): string
    {
        return match ((string)$status) {
            '0' => 'draft',
            '1' => 'in_process',
            '2' => 'finished',
            '3' => 'cancelled',
            default => 'draft',
        };
    }

    private function resolveProduct($modelWithMaybeRelation, $productId)
    {
        $loaded = method_exists($modelWithMaybeRelation, 'relationLoaded')
            && $modelWithMaybeRelation->relationLoaded('product');

        $prod = $loaded ? $modelWithMaybeRelation->getRelation('product') : null;

        if (!$prod) {
            $prod = TrashedSelect::findWithTrashed(ProductService::class, $productId);
            if ($prod && method_exists($prod, 'loadMissing')) {
                $prod->loadMissing('unit');
            }
        }

        return $prod;
    }

    private function doStart(ProductionOrder $order): void
    {
        if ((int)$order->status !== 0) {
            return;
        }

        $order->load([
            'bom.inputs.product'  => fn ($q) => $q->withTrashed(),
            'bom.outputs.product' => fn ($q) => $q->withTrashed(),
        ]);

        $shortages = [];
        foreach ($order->bom->inputs as $in) {
            $prod = $this->resolveProduct($in, $in->product_id);
            $in->setRelation('product', $prod);

            $required = (float)$in->qty_per_batch * (float)$order->multiplier;
            $on_hand  = (float)($prod->quantity ?? 0);
            if ($on_hand < $required) {
                $shortages[] = ($prod->name ?? ('#'.$in->product_id)) . " (need {$required}, have {$on_hand})";
            }
        }
        if (!empty($shortages)) {
            abort(422, __('Insufficient stock for: ') . implode(', ', $shortages));
        }

        DB::transaction(function () use ($order) {
            foreach ($order->bom->inputs as $in) {
                $product  = $this->resolveProduct($in, $in->product_id);
                $required = (float)$in->qty_per_batch * (float)$order->multiplier;

                $unitCost  = (float)($product->purchase_price ?? ($product->sale_price ?? 0));
                $totalCost = round($unitCost * $required, 2);

                Utility::total_quantity('minus', $required, $product->id);
                $type = 'production';
                $desc = $required . ' ' . __('consumed for production') . ' ' . $order->code;
                Utility::addProductStock($product->id, $required, $type, $desc, $order->id);

                ProductionConsumption::create([
                    'production_order_id' => $order->id,
                    'product_id'          => $product->id,
                    'qty_required'        => $required,
                    'qty_issued'          => $required,
                    'unit_cost'           => $unitCost,
                    'total_cost'          => $totalCost,
                ]);
            }

            foreach ($order->bom->outputs as $out) {
                $planned = (float)$out->qty_per_batch * (float)$order->multiplier;
                ProductionOutput::create([
                    'production_order_id' => $order->id,
                    'product_id'          => $out->product_id,
                    'qty_planned'         => $planned,
                ]);
            }

            $order->update([
                'status'     => 1,
                'started_at' => Carbon::now(),
            ]);
        });
    }

    private function doFinish(ProductionOrder $order, ?float $manufacturingCost = null): void
    {
        if ((int)$order->status !== 1) {
            return;
        }

        $order->load('outputs');

        DB::transaction(function () use ($order, $manufacturingCost) {
            foreach ($order->outputs as $out) {
                if ($out->qty_good === null && $out->qty_planned !== null) {
                    $out->qty_good  = (float)$out->qty_planned;
                    $out->qty_scrap = 0.0;
                    $out->save();
                }
            }

            $totalGood = (float)ProductionOutput::where('production_order_id', $order->id)->sum('qty_good');
            $rawTotal  = (float)ProductionConsumption::where('production_order_id', $order->id)->sum('total_cost');

            $mfgCost = $manufacturingCost !== null
                ? (float)$manufacturingCost
                : (float)($order->manufacturing_cost ?? 0);

            $grand = $rawTotal + $mfgCost;

            $outs = ProductionOutput::where('production_order_id', $order->id)->get();
            foreach ($outs as $out) {
                $share = $totalGood > 0 ? ((float)$out->qty_good / $totalGood) : 0;
                $alloc = round($grand * $share, 2);
                $out->cost_allocated = $alloc;
                $out->save();

                if ((float)$out->qty_good > 0) {
                    Utility::total_quantity('plus', (float)$out->qty_good, $out->product_id);

                    $type = 'production';
                    $desc = $out->qty_good . ' ' . __('produced in production') . ' ' . $order->code;
                    Utility::addProductStock($out->product_id, (float)$out->qty_good, $type, $desc, $order->id);
                }
            }

            $order->update([
                'status'             => 2,
                'finished_at'        => Carbon::now(),
                'manufacturing_cost' => $mfgCost,
                'total_cost'         => $grand,
            ]);
        });
    }

    private function doCancel(ProductionOrder $order): void
    {
        if ((int)$order->status === 1) {
            DB::transaction(function () use ($order) {
                $cons = $order->consumptions ?? ProductionConsumption::where('production_order_id', $order->id)->get();
                foreach ($cons as $c) {
                    if ((float)$c->qty_issued > 0) {
                        Utility::total_quantity('plus', (float)$c->qty_issued, $c->product_id);
                        $desc = $c->qty_issued . ' ' . __('returned to stock from cancelled production') . ' ' . $order->code;
                        Utility::addProductStock($c->product_id, (float)$c->qty_issued, 'production', $desc, $order->id);
                    }
                }
                $order->update(['status' => 3]);
            });
            return;
        }
        $order->update(['status' => 3]);
    }

    public function index()
    {
        $this->authorize('manage production');

        $jobs = ProductionOrder::mine()
            ->with('bom')
            ->latest()
            ->get();

        foreach ($jobs as $job) {
            $job->status = $this->statusToString($job->status);
            $rawTotal    = (float) ProductionConsumption::where('production_order_id', $job->id)->sum('total_cost');
            $mfgCost     = (float) ($job->manufacturing_cost ?? 0);
            $job->total_cost = $rawTotal + $mfgCost;
        }

        return view('production.index', compact('jobs'));
    }

    public function create(Request $request)
    {
        $this->authorize('create production');

        $bomOptions = Bom::mine()
            ->where('is_active', 1)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->prepend(__('Select BOM'), '');

        $preselected_bom_id = (int)$request->query('bom_id', 0);

        $bomComponents = collect();
        $bomOutputs    = collect();

        return view('production.create', compact(
            'bomOptions',
            'preselected_bom_id',
            'bomComponents',
            'bomOutputs'
        ));
    }

    public function store(Request $request)
    {
        $this->authorize('create production');

        $data = $request->validate([
            'bom_id'             => 'required|integer|exists:boms,id',
            'batch_multiplier'   => 'nullable|numeric|min:0.0001',
            'target_good_qty'    => 'nullable|numeric|min:0',
            'planned_date'       => 'nullable|date',
            'labor_cost'         => 'nullable|numeric|min:0',
            'overhead_cost'      => 'nullable|numeric|min:0',
            'other_cost'         => 'nullable|numeric|min:0',
            'notes'              => 'nullable|string',
            'action'             => 'nullable|string|in:save_draft,start,finish',
        ]);

        $manufacturing_cost =
            (float)($data['labor_cost'] ?? 0) +
            (float)($data['overhead_cost'] ?? 0) +
            (float)($data['other_cost'] ?? 0);

        $action     = $data['action'] ?? 'save_draft';
        $multiplier = max((float)($data['batch_multiplier'] ?? 1), 0.0001);

        if (in_array($action, ['start', 'finish'], true)) {
            $bom = Bom::mine()
                ->with(['inputs.product' => fn ($q) => $q->withTrashed(),
                        'outputs.product' => fn ($q) => $q->withTrashed()])
                ->findOrFail((int)$data['bom_id']);

            $shortages = $this->computeShortages($bom, $multiplier);
            if (!empty($shortages)) {
                return back()->withInput()->with('error', __('Insufficient stock for: ') . implode(', ', $shortages));
            }
        }

        try {
            $order = ProductionOrder::create([
                'bom_id'             => (int)$data['bom_id'],
                'code'               => $this->nextCode(),
                'status'             => 0,
                'multiplier'         => $multiplier,
                'planned_date'       => $data['planned_date'] ?? null,
                'manufacturing_cost' => $manufacturing_cost,
                'notes'              => $data['notes'] ?? null,
                'created_by'         => \Auth::user()->creatorId(),
            ]);

            if ($action === 'save_draft') {
                return redirect()->route('production.show', $order->id)
                    ->with('success', __('Production order created (Draft).'));
            }

            if ($action === 'start') {
                $this->doStart($order);
                return redirect()->route('production.show', $order->id)
                    ->with('success', __('Production started. Raw materials deducted and held.'));
            }

            if ($action === 'finish') {
                if ((int)$order->status === 0) {
                    $this->doStart($order);
                }
                $order->refresh();
                $this->doFinish($order, $manufacturing_cost);

                return redirect()->route('production.show', $order->id)
                    ->with('success', __('Production finished. Finished stock added.'));
            }

            return redirect()->route('production.show', $order->id)
                ->with('success', __('Production order created (Draft).'));

        } catch (\Throwable $e) {
            if (isset($order) && $order->exists && (int)$order->status === 0) {
                try { $order->delete(); } catch (\Throwable $ignore) {}
            }
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

public function edit(ProductionOrder $production)
{
    $this->authorize('edit production');
    abort_unless($production->created_by == \Auth::user()->creatorId(), 403);

    if ((int)$production->status !== 0) {
        return redirect()->route('production.show', $production->id)
            ->with('error', __('Only Draft orders can be edited.'));
    }

    $bomOptions = Bom::mine()
        ->where('is_active', 1)
        ->orderBy('name')
        ->pluck('name', 'id')
        ->prepend(__('Select BOM'), '');

    $preselected_bom_id = (int) $production->bom_id;

    $bom = Bom::mine()
        ->with(['inputs.product' => fn ($q) => $q->withTrashed(),
                'outputs.product' => fn ($q) => $q->withTrashed()])
        ->find($preselected_bom_id);

    $bomComponents = $bom?->inputs ?? collect();
    $bomOutputs    = $bom?->outputs ?? collect();

    $production->status = $this->statusToString($production->status);

    return view('production.edit', [
        'job'               => $production,
        'bomOptions'        => $bomOptions,
        'preselected_bom_id'=> $preselected_bom_id,
        'bomComponents'     => $bomComponents,
        'bomOutputs'        => $bomOutputs,
    ]);
}

public function show(ProductionOrder $production)
{
    $this->authorize('manage production');
    abort_unless($production->created_by == \Auth::user()->creatorId(), 403);

    $production->load([
        'bom.inputs.product'  => fn ($q) => $q->withTrashed(),
        'bom.outputs.product' => fn ($q) => $q->withTrashed(),
        'consumptions',
        'outputs.product'     => fn ($q) => $q->withTrashed(),
    ]);

    foreach ($production->bom->inputs as $in) {
        $in->setRelation('product', $this->resolveProduct($in, $in->product_id));
    }
    foreach ($production->bom->outputs as $out) {
        $out->setRelation('product', $this->resolveProduct($out, $out->product_id));
    }
    foreach ($production->consumptions as $c) {
        $c->setRelation('product', $this->resolveProduct($c, $c->product_id));
    }
    foreach ($production->outputs as $o) {
        $o->setRelation('product', $this->resolveProduct($o, $o->product_id));
    }

    $job = $production;
    $job->status = $this->statusToString($job->status);

    if ($job->status === 'draft') {
        $multiplier = max((float)($job->multiplier ?? 1), 0.0001);

        $components = collect();
        $compTotal  = 0.0;
        foreach ($job->bom->inputs as $in) {
            $prod = $in->getRelation('product');
            $req  = (float)$in->qty_per_batch * $multiplier;
            $unit = (float)($prod->purchase_price ?? ($prod->sale_price ?? 0));
            $line = round($unit * $req, 2);

            $row = (object)[
                'product'    => $prod,
                'qty'        => $req,
                'line_cost'  => $line,
            ];
            $components->push($row);
            $compTotal += $line;
        }
        $job->components       = $components;
        $job->components_total = (float)$components->sum('line_cost');

        $mfgCost        = (float)($job->manufacturing_cost ?? 0);
        $job->total_cost = $job->components_total + $mfgCost;

        $totalPlannedQty = 0.0;
        foreach ($job->bom->outputs as $out) {
            $totalPlannedQty += (float)$out->qty_per_batch * $multiplier;
        }

        $outs = collect();
        foreach ($job->bom->outputs as $out) {
            $prod  = $out->getRelation('product');
            $qty   = (float)$out->qty_per_batch * $multiplier;
            $share = $totalPlannedQty > 0 ? ($qty / $totalPlannedQty) : 0.0;
            $alloc = round($job->total_cost * $share, 2);

            $outs->push((object)[
                'product'         => $prod,
                'qty'             => $qty,
                'allocated_cost'  => $alloc,
            ]);
        }
        $job->outputs = $outs;

    } else {
        $components = $job->consumptions->map(function ($c) {
            $c->qty       = (float)$c->qty_issued;
            $c->line_cost = (float)$c->total_cost;
            return $c;
        });
        $job->components       = $components;
        $job->components_total = (float)$components->sum('line_cost');

        $outs = $job->outputs->map(function ($o) {
            $qty = $o->qty_good ?? $o->qty_planned ?? 0;
            $o->qty            = (float)$qty;
            $o->allocated_cost = (float)($o->cost_allocated ?? 0);
            return $o;
        });
        $job->outputs = $outs;

        $rawTotal        = (float)$job->components_total;
        $manufacturing   = (float)($job->manufacturing_cost ?? 0);
        $job->total_cost = $rawTotal + $manufacturing;
    }

    return view('production.show', compact('job'));
}

    public function destroy(ProductionOrder $production)
    {
        $this->authorize('delete production');
        abort_unless($production->created_by == \Auth::user()->creatorId(), 403);

        try {
            DB::transaction(function () use ($production) {
                $status = (int)$production->status;

                if ($status === 1) {
                    $cons = ProductionConsumption::where('production_order_id', $production->id)->get();
                    foreach ($cons as $c) {
                        if ((float)$c->qty_issued > 0) {
                            Utility::total_quantity('plus', (float)$c->qty_issued, $c->product_id);
                            $desc = $c->qty_issued . ' ' . __('returned to stock (deleted in-process)') . ' ' . $production->code;
                            Utility::addProductStock($c->product_id, (float)$c->qty_issued, 'production', $desc, $production->id);
                        }
                    }
                    ProductionConsumption::where('production_order_id', $production->id)->delete();
                    ProductionOutput::where('production_order_id', $production->id)->delete();
                } else {
                    ProductionConsumption::where('production_order_id', $production->id)->delete();
                    ProductionOutput::where('production_order_id', $production->id)->delete();
                }

                $production->delete();
            });
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('production.index')->with('success', __('Production order deleted.'));
    }

    public function transition(Request $request, ProductionOrder $production)
    {
        $this->authorize('edit production');
        abort_unless($production->created_by == \Auth::user()->creatorId(), 403);

        $to = $request->input('to');

        try {
            if ($to === 'in_process') {
                if ((int)$production->status !== 1) {
                    $this->doStart($production);
                }
                return back()->with('success', __('Production started. Raw materials deducted and held.'));
            }

            if ($to === 'finished') {
                if ((int)$production->status === 0) {
                    $this->doStart($production);
                    $production->refresh();
                }
                $this->doFinish($production);
                return back()->with('success', __('Production finished. Finished stock added.'));
            }

            if ($to === 'cancelled') {
                $this->doCancel($production);
                return back()->with('success', __('Production cancelled.'));
            }
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back();
    }

    private function computeShortages(Bom $bom, float $multiplier): array
    {
        $shortages = [];

        $bom->loadMissing([
            'inputs.product' => fn ($q) => $q->withTrashed(),
        ]);

        foreach ($bom->inputs as $in) {
            $prod = $this->resolveProduct($in, $in->product_id);
            $in->setRelation('product', $prod);

            $required = (float)$in->qty_per_batch * (float)$multiplier;
            $onHand   = (float)($prod->quantity ?? 0);
            if ($onHand < $required) {
                $shortages[] = ($prod->name ?? ('#'.$in->product_id)) . " (need {$required}, have {$onHand})";
            }
        }
        return $shortages;
    }

    public function export()
    {
        $this->authorize('manage production');

        $companyName = \Auth::user()->name ?? 'Company';
        $companyName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $companyName);
        $date = date('Y-m-d_H-i-s');
        $filename = "production_orders_{$companyName}_{$date}.xlsx";

        return Excel::download(new ProductionExport(), $filename);
    }

    public function exportSelected(Request $request)
    {
        $this->authorize('manage production');

        $ids = $request->input('ids');
        if (is_array($ids)) {
            $ids = collect($ids)->map(fn($v) => (int)$v)->filter()->unique()->values()->all();
        } else {
            $ids = collect(explode(',', (string)$ids))->map(fn($v) => (int)$v)->filter()->unique()->values()->all();
        }

        if (empty($ids)) {
            return back()->with('error', __('Please select at least one production order.'));
        }

        $companyName = \Auth::user()->name ?? 'Company';
        $companyName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $companyName);
        $date = date('Y-m-d_H-i-s');
        $filename = "production_selected_{$companyName}_{$date}.xlsx";

        return Excel::download(new ProductionExport($ids), $filename);
    }

    public function bulkDestroy(Request $request)
    {
        $this->authorize('delete production');

        $ids = $request->input('ids');
        if (is_array($ids)) {
            $ids = collect($ids)->map(fn($v) => (int)$v)->filter()->unique()->values();
        } else {
            $ids = collect(explode(',', (string)$ids))->map(fn($v) => (int)$v)->filter()->unique()->values();
        }

        if ($ids->isEmpty()) {
            return back()->with('error', __('Please select at least one production order.'));
        }

        $orders = ProductionOrder::whereIn('id', $ids)
            ->where('created_by', \Auth::user()->creatorId())
            ->get();

        $deleted = 0;
        foreach ($orders as $production) {
            try {
                DB::transaction(function () use ($production) {
                    $status = (int)$production->status;

                    if ($status === 1) {
                        $cons = ProductionConsumption::where('production_order_id', $production->id)->get();
                        foreach ($cons as $c) {
                            if ((float)$c->qty_issued > 0) {
                                Utility::total_quantity('plus', (float)$c->qty_issued, $c->product_id);
                                $desc = $c->qty_issued . ' ' . __('returned to stock (deleted in-process)') . ' ' . $production->code;
                                Utility::addProductStock($c->product_id, (float)$c->qty_issued, 'production', $desc, $production->id);
                            }
                        }
                        ProductionConsumption::where('production_order_id', $production->id)->delete();
                        ProductionOutput::where('production_order_id', $production->id)->delete();
                    } else {
                        ProductionConsumption::where('production_order_id', $production->id)->delete();
                        ProductionOutput::where('production_order_id', $production->id)->delete();
                    }

                    $production->delete();
                });
                $deleted++;
            } catch (\Throwable $e) {
            }
        }

        return back()->with('success', trans_choice(':count production order deleted.|:count production orders deleted.', $deleted, ['count' => $deleted]));
    }
}
