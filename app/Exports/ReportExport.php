<?php

namespace App\Exports;

use App\Models\Report;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromArray, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    public function array(): array
    {
        // Ensure we have data to export
        if (empty($this->report->data)) {
            return [['No data available']];
        }

        // Transform data if needed
        return collect($this->report->data)->map(function ($row) {
            return collect($row)->map(function ($value) {
                // Handle different data types
                if (is_array($value)) {
                    return json_encode($value);
                }
                if (is_null($value)) {
                    return '';
                }
                return $value;
            })->toArray();
        })->toArray();
    }

    public function headings(): array
    {
        // Convert snake_case to Title Case for headings
        return array_map(function ($key) {
            return ucwords(str_replace('_', ' ', $key));
        }, array_keys($this->report->data[0] ?? []));
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F8F9FA']
                ]
            ]
        ];
    }

    public function title(): string
    {
        return $this->report->formatted_type;
    }
}