<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'name',
        'purchase_date',
        'supported_date',
        'amount',
        'depreciation_rate',
        'description',
        'created_by',
    ];

    protected $dates = [
        'purchase_date',
        'supported_date',
    ];

    public function getCurrentBookValue()
    {
        $yearsDepreciated = $this->getYearsDepreciated();
        $totalDepreciation = $this->getTotalDepreciation();
        
        return max(0, $this->amount - $totalDepreciation);
    }

    public function getYearsDepreciated()
    {
        $purchaseDate = \Carbon\Carbon::parse($this->purchase_date);
        $currentDate = \Carbon\Carbon::now();
        
        return $purchaseDate->diffInYears($currentDate);
    }

    public function getAnnualDepreciation()
    {
        if ($this->depreciation_rate == 0) {
            return 0;
        }
        
        return ($this->amount * $this->depreciation_rate) / 100;
    }

    public function getTotalDepreciation()
    {
        $yearsDepreciated = $this->getYearsDepreciated();
        $annualDepreciation = $this->getAnnualDepreciation();
        
        return min($this->amount, $yearsDepreciated * $annualDepreciation);
    }

    public function getDepreciationForPeriod($startDate, $endDate)
    {
        if ($this->depreciation_rate == 0) {
            return 0;
        }

        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        $purchase = \Carbon\Carbon::parse($this->purchase_date);

        if ($purchase->greaterThan($end)) {
            return 0;
        }

        $effectiveStart = $purchase->greaterThan($start) ? $purchase : $start;
        $daysInPeriod = $effectiveStart->diffInDays($end) + 1;
        $dailyDepreciation = $this->getAnnualDepreciation() / 365;

        return min($this->getCurrentBookValue(), $daysInPeriod * $dailyDepreciation);
    }

    public function createDepreciationEntry($amount, $date = null)
    {
        if ($amount <= 0) {
            return null;
        }

        $date = $date ?: \Carbon\Carbon::now()->format('Y-m-d');
        
        $depreciationExpenseAccount = ChartOfAccount::where('name', 'Depreciation Expense')
            ->where('created_by', $this->created_by)
            ->first();
            
        $accumulatedDepreciationAccount = ChartOfAccount::where('name', 'like', '%Accum.depreciation%')
            ->where('created_by', $this->created_by)
            ->first();

        if (!$depreciationExpenseAccount || !$accumulatedDepreciationAccount) {
            return null;
        }

        $journalEntry = JournalEntry::create([
            'date' => $date,
            'reference' => 'DEP-' . $this->id . '-' . date('Ymd'),
            'description' => 'Depreciation for asset: ' . $this->name,
            'journal_id' => 0,
            'created_by' => $this->created_by,
        ]);

        JournalItem::create([
            'journal' => $journalEntry->id,
            'account' => $depreciationExpenseAccount->id,
            'debit' => $amount,
            'credit' => 0,
        ]);

        JournalItem::create([
            'journal' => $journalEntry->id,
            'account' => $accumulatedDepreciationAccount->id,
            'debit' => 0,
            'credit' => $amount,
        ]);

        return $journalEntry;
    }

    public static function calculateMonthlyDepreciation($month = null, $year = null)
    {
        $month = $month ?: date('m');
        $year = $year ?: date('Y');
        
        $startDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $assets = self::where('depreciation_rate', '>', 0)->get();
        $totalDepreciation = 0;

        foreach ($assets as $asset) {
            $monthlyDepreciation = $asset->getDepreciationForPeriod($startDate, $endDate);
            if ($monthlyDepreciation > 0) {
                $asset->createDepreciationEntry($monthlyDepreciation, $endDate->format('Y-m-d'));
                $totalDepreciation += $monthlyDepreciation;
            }
        }

        return $totalDepreciation;
    }

}
