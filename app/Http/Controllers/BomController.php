<?php

namespace App\Http\Controllers;

use App\Models\Bom;
use App\Models\BomInput;
use App\Models\BomOutput;
use App\Models\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Plan;
use App\Models\User;
use App\Support\TrashedSelect;
use App\Exports\BomExport;
use Maatwebsite\Excel\Facades\Excel;

class BomController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function nextCode(): string
    {
        $last = Bom::mine()->latest()->first();
        $n = $last ? ((int) filter_var($last->code, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;
        return 'BOM-'.str_pad($n, 4, '0', STR_PAD_LEFT);
    }

    public function index()
    {
        $this->authorize('manage bom');

        $boms = Bom::mine()
            ->withCount(['inputs','outputs'])
            ->latest()
            ->get();

        return view('bom.index', compact('boms'));
    }

    public function create()
    {
        $this->authorize('create bom');

        $code = $this->nextCode();

        // Separate options:
        // - Raw list contains material_type IN ('raw','both')
        // - Finished list contains material_type IN ('finished','both')
        $creatorId = \Auth::user()->creatorId();

        $rawProducts = ProductService::query()
            ->where('created_by', $creatorId)
            ->whereNull('deleted_at')
            ->where('type', 'Product')
            ->whereIn('material_type', ['raw','both'])
            ->orderBy('name')
            ->pluck('name', 'id')
            ->prepend(__('Select Item'), '');

        $finishedProducts = ProductService::query()
            ->where('created_by', $creatorId)
            ->whereNull('deleted_at')
            ->where('type', 'Product')
            ->whereIn('material_type', ['finished','both'])
            ->orderBy('name')
            ->pluck('name', 'id')
            ->prepend(__('Select Item'), '');

        return view('bom.create', compact('code','rawProducts','finishedProducts'));
    }

    public function store(Request $request)
    {
        [$ok, $msg] = $this->withinQuota();
        if (!$ok) {
            return redirect()->back()->with('error', $msg);
        }

        $this->authorize('create bom');

        $data = $request->validate([
            'name'                    => 'required|string|max:255',
            'code'                    => 'required|string|max:50',
            'is_active'               => 'nullable|in:0,1',
            'notes'                   => 'nullable|string',
            'inputs'                  => 'required|array|min:1',
            'inputs.*.product_id'     => 'required|integer',
            'inputs.*.qty_per_batch'  => 'required|numeric|min:0.0001',
            'outputs'                 => 'required|array|min:1',
            'outputs.*.product_id'    => 'required|integer',
            'outputs.*.qty_per_batch' => 'required|numeric|min:0.0001',
        ]);

        DB::transaction(function () use ($data) {
            $bom = Bom::create([
                'code'       => $data['code'] ?: $this->nextCode(),
                'name'       => $data['name'],
                'is_active'  => (int)($data['is_active'] ?? 1),
                'notes'      => $data['notes'] ?? null,
                'created_by' => \Auth::user()->creatorId(),
            ]);

            foreach ($data['inputs'] as $in) {
                BomInput::create([
                    'bom_id'        => $bom->id,
                    'product_id'    => (int)$in['product_id'],
                    'qty_per_batch' => (float)$in['qty_per_batch'],
                    'scrap_pct'     => 0,
                ]);
            }

            foreach ($data['outputs'] as $out) {
                BomOutput::create([
                    'bom_id'        => $bom->id,
                    'product_id'    => (int)$out['product_id'],
                    'qty_per_batch' => (float)$out['qty_per_batch'],
                    'is_primary'    => 0,
                ]);
            }
        });

        return redirect()->route('bom.index')->with('success', __('BOM created.'));
    }

    public function show(Bom $bom)
    {
        $this->authorize('manage bom');
        abort_unless($bom->created_by == \Auth::user()->creatorId(), 403);

        // Load raw relations first
        $bom->load(['inputs','outputs']);

        // Attach product even if soft-deleted; also load unit if present
        foreach ($bom->inputs as $in) {
            $prod = TrashedSelect::findWithTrashed(ProductService::class, $in->product_id);
            if ($prod) $prod->loadMissing('unit');
            $in->setRelation('product', $prod);
        }
        foreach ($bom->outputs as $out) {
            $prod = TrashedSelect::findWithTrashed(ProductService::class, $out->product_id);
            if ($prod) $prod->loadMissing('unit');
            $out->setRelation('product', $prod);
        }

        return view('bom.show', compact('bom'));
    }

    public function edit(Bom $bom)
    {
        $this->authorize('edit bom');
        abort_unless($bom->created_by == \Auth::user()->creatorId(), 403);

        $bom->load(['inputs','outputs']);

        $creatorId = \Auth::user()->creatorId();

        // Build separate option lists and ensure currently used products are present,
        // even if they don't match the current filters or are trashed.
        $usedInputIds  = $bom->inputs->pluck('product_id')->filter()->unique()->values()->all();
        $usedOutputIds = $bom->outputs->pluck('product_id')->filter()->unique()->values()->all();

        $rawProducts = ProductService::query()
            ->where('created_by', $creatorId)
            ->whereNull('deleted_at')
            ->where('type', 'Product')
            ->whereIn('material_type', ['raw','both'])
            ->orderBy('name')
            ->pluck('name', 'id');

        $finishedProducts = ProductService::query()
            ->where('created_by', $creatorId)
            ->whereNull('deleted_at')
            ->where('type', 'Product')
            ->whereIn('material_type', ['finished','both'])
            ->orderBy('name')
            ->pluck('name', 'id');

        // Add back any currently used inputs/outputs (even if trashed or filtered out)
        if (!empty($usedInputIds)) {
            $usedInputs = ProductService::withTrashed()
                ->whereIn('id', $usedInputIds)
                ->get(['id','name']);
            foreach ($usedInputs as $p) {
                if (!$rawProducts->has($p->id)) {
                    $rawProducts->put($p->id, $p->name . ($p->deleted_at ? ' '.__('(deleted)') : ''));
                }
            }
        }
        if (!empty($usedOutputIds)) {
            $usedOutputs = ProductService::withTrashed()
                ->whereIn('id', $usedOutputIds)
                ->get(['id','name']);
            foreach ($usedOutputs as $p) {
                if (!$finishedProducts->has($p->id)) {
                    $finishedProducts->put($p->id, $p->name . ($p->deleted_at ? ' '.__('(deleted)') : ''));
                }
            }
        }

        // Prepend "Select Item"
        $rawProducts = collect(['' => __('Select Item')]) + $rawProducts;
        $finishedProducts = collect(['' => __('Select Item')]) + $finishedProducts;

        return view('bom.edit', compact('bom','rawProducts','finishedProducts'));
    }

    public function update(Request $request, Bom $bom)
    {
        $this->authorize('edit bom');
        abort_unless($bom->created_by == \Auth::user()->creatorId(), 403);

        $data = $request->validate([
            'name'                    => 'required|string|max:255',
            'is_active'               => 'nullable|in:0,1',
            'notes'                   => 'nullable|string',
            'inputs'                  => 'required|array|min:1',
            'inputs.*.product_id'     => 'required|integer',
            'inputs.*.qty_per_batch'  => 'required|numeric|min:0.0001',
            'outputs'                 => 'required|array|min:1',
            'outputs.*.product_id'    => 'required|integer',
            'outputs.*.qty_per_batch' => 'required|numeric|min:0.0001',
        ]);

        DB::transaction(function () use ($bom, $data) {
            $bom->update([
                'name'      => $data['name'],
                'is_active' => (int)($data['is_active'] ?? 1),
                'notes'     => $data['notes'] ?? null,
            ]);

            $bom->inputs()->delete();
            $bom->outputs()->delete();

            foreach ($data['inputs'] as $in) {
                BomInput::create([
                    'bom_id'        => $bom->id,
                    'product_id'    => (int)$in['product_id'],
                    'qty_per_batch' => (float)$in['qty_per_batch'],
                    'scrap_pct'     => 0,
                ]);
            }

            foreach ($data['outputs'] as $out) {
                BomOutput::create([
                    'bom_id'        => $bom->id,
                    'product_id'    => (int)$out['product_id'],
                    'qty_per_batch' => (float)$out['qty_per_batch'],
                    'is_primary'    => 0,
                ]);
            }
        });

        return redirect()->route('bom.index')->with('success', __('BOM updated.'));
    }

    public function destroy(Bom $bom)
    {
        $this->authorize('delete bom');
        abort_unless($bom->created_by == \Auth::user()->creatorId(), 403);

        $bom->delete();
        return redirect()->route('bom.index')->with('success', __('BOM deleted.'));
    }

    public function duplicate(Bom $bom)
    {
        $this->authorize('create bom');
        abort_unless($bom->created_by == \Auth::user()->creatorId(), 403);

        DB::transaction(function () use ($bom) {
            $copy = Bom::create([
                'code'       => $bom->code.'-COPY',
                'name'       => $bom->name.' (Copy)',
                'is_active'  => $bom->is_active,
                'notes'      => $bom->notes,
                'created_by' => \Auth::user()->creatorId(),
            ]);

            foreach ($bom->inputs as $i) {
                BomInput::create([
                    'bom_id'        => $copy->id,
                    'product_id'    => $i->product_id,
                    'qty_per_batch' => $i->qty_per_batch,
                    'scrap_pct'     => 0,
                ]);
            }
            foreach ($bom->outputs as $o) {
                BomOutput::create([
                    'bom_id'        => $copy->id,
                    'product_id'    => $o->product_id,
                    'qty_per_batch' => $o->qty_per_batch,
                    'is_primary'    => 0,
                ]);
            }
        });

        return back()->with('success', __('BOM duplicated.'));
    }

    // JSON for Production create preview
    public function details(Bom $bom)
    {
        abort_unless($bom->created_by == \Auth::user()->creatorId(), 403);

        $bom->load(['inputs','outputs']);

        foreach ($bom->inputs as $in) {
            $prod = TrashedSelect::findWithTrashed(ProductService::class, $in->product_id);
            if ($prod) $prod->loadMissing('unit');
            $in->setRelation('product', $prod);
        }
        foreach ($bom->outputs as $out) {
            $prod = TrashedSelect::findWithTrashed(ProductService::class, $out->product_id);
            if ($prod) $prod->loadMissing('unit');
            $out->setRelation('product', $prod);
        }

        return response()->json($bom);
    }

    public function generateCode()
    {
        $last = Bom::orderBy('id', 'desc')->first();
        $nextId = $last ? ($last->id + 1) : 1;
        $code = 'BOM-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return response()->json(['code' => $code]);
    }

    protected function withinQuota(): array
    {
        $creatorId = \Auth::user()->creatorId();
        $creator   = User::find($creatorId);
        $plan      = $creator ? Plan::find($creator->plan) : null;

        $max = $plan->manufacturing_quota ?? $plan->manufacturing_quota ?? -1;

        if ((int)$max === -1) {
            return [true, null];
        }

        $current = Bom::where('created_by', $creatorId)->count();
        if ($current >= (int)$max) {
            return [false, __('Your BOM limit is over, Please change plan.')];
        }

        return [true, null];
    }

    // --- Export ALL BOMs ---
    public function export()
    {
        $this->authorize('manage bom');

        $companyName = \Auth::user()->name ?? 'Company';
        $companyName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $companyName);
        $date = date('Y-m-d_H-i-s');
        $filename = "boms_{$companyName}_{$date}.xlsx";

        return Excel::download(new BomExport(), $filename);
    }

    // --- Export SELECTED BOMs ---
    public function exportSelected(Request $request)
    {
        $this->authorize('manage bom');

        $ids = collect((array) $request->input('ids'))
            ->flatMap(fn ($v) => is_array($v) ? $v : explode(',', (string) $v))
            ->map(fn ($v) => (int) $v)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($ids)) {
            return redirect()->back()->with('error', __('Please select at least one BOM.'));
        }

        $companyName = \Auth::user()->name ?? 'Company';
        $companyName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $companyName);
        $date = date('Y-m-d_H-i-s');
        $filename = "boms_selected_{$companyName}_{$date}.xlsx";

        return Excel::download(new BomExport($ids), $filename);
    }

    // --- BULK DELETE BOMs ---
    public function bulkDestroy(Request $request)
    {
        $this->authorize('delete bom');

        $ids = collect((array) $request->input('ids'))
            ->flatMap(fn ($v) => is_array($v) ? $v : explode(',', (string) $v))
            ->map(fn ($v) => (int) $v)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($ids)) {
            return redirect()->back()->with('error', __('Please select at least one BOM.'));
        }

        $boms = Bom::whereIn('id', $ids)
            ->where('created_by', \Auth::user()->creatorId())
            ->get();

        $deleted = 0;
        foreach ($boms as $bom) {
            // remove children first to be safe
            $bom->inputs()->delete();
            $bom->outputs()->delete();
            $bom->delete();
            $deleted++;
        }

        return redirect()->back()->with(
            'success',
            trans_choice(':count BOM deleted.|:count BOMs deleted.', $deleted, ['count' => $deleted])
        );
    }
}
