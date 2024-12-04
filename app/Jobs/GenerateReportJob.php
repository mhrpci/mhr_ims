<?php

namespace App\Jobs;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    public function handle()
    {
        $reportData = $this->report->getReportData();
        $csv = Writer::createFromString('');

        // Add headers
        $headers = $this->getHeaders();
        $csv->insertOne($headers);

        // Add data
        foreach ($reportData as $item) {
            $csv->insertOne($this->formatRow($item));
        }

        $filename = $this->report->type . '_report_' . $this->report->id . '.csv';
        Storage::put('reports/' . $filename, $csv->getContent());

        $this->report->update([
            'status' => 'completed',
            'file_path' => 'reports/' . $filename,
        ]);
    }

    private function getHeaders()
    {
        switch ($this->report->type) {
            case 'stock_in':
                return ['Product', 'Vendor', 'Branch', 'Quantity', 'Unit Price', 'Total Price', 'Date'];
            case 'stock_out':
                return ['Product', 'Customer', 'Branch', 'Quantity', 'Unit Price', 'Total Price', 'Date'];
            case 'inventory':
                return ['Product', 'Branch', 'Quantity'];
            case 'product_performance':
                return ['Product', 'Total Sold', 'Total Revenue'];
            default:
                return [];
        }
    }

    private function formatRow($item)
    {
        switch ($this->report->type) {
            case 'stock_in':
            case 'stock_out':
                return [
                    $item->product->name,
                    $item->{$this->report->type === 'stock_in' ? 'vendor' : 'customer'}->name ?? 'N/A',
                    $item->branch->name,
                    $item->quantity,
                    $item->unit_price,
                    $item->total_price,
                    $this->formatDate($item->date),
                ];
            case 'inventory':
                return [
                    $item->product->name,
                    $item->branch->name,
                    $item->quantity,
                ];
            case 'product_performance':
                return [
                    $item->name,
                    $item->total_sold ?? 0,
                    $item->total_revenue ?? 0,
                ];
            default:
                return [];
        }
    }

    private function formatDate($date)
    {
        if ($date instanceof \DateTime) {
            return $date->format('Y-m-d');
        } elseif (is_string($date)) {
            return date('Y-m-d', strtotime($date));
        }
        return '';
    }
}
