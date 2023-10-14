<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class VisitOrderExport implements WithStyles
{
    protected $visitOrder;
    protected $period;
    protected $dateNow;

    public function __construct($visitOrder, $period, $dateNow)
    {
        $this->visitOrder = $visitOrder;
        $this->period = $period;
        $this->dateNow = $dateNow;
    }

    public function styles(Worksheet $sheet)
    {
        $status = ['Open', 'Assigned', 'Cancelled', 'Revisit', 'Visited', 'Validated', 'Can\'t Billed', 'Paid to Partner', 'Paid from Client'];
        // Setting sheet
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Times New Roman');
        $sheet->getParent()->getDefaultStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
        $sheet->getColumnDimension('A')->setWidth(7.5);
        $sheet->getColumnDimension('B')->setWidth(18.2);
        $sheet->getColumnDimension('C')->setWidth(17.0);
        $sheet->getColumnDimension('D')->setWidth(20.0);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setWidth(33.0);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setWidth(14.2);
        $sheet->getColumnDimension('L')->setWidth(18.0);
        $sheet->getColumnDimension('M')->setAutoSize(true);
        $sheet->getColumnDimension('N')->setAutoSize(true);
        $sheet->getColumnDimension('O')->setAutoSize(true);
        $sheet->getStyle('A5:O5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Title
        $sheet->mergeCells('A1:D1')->setCellValue('A1', 'Report Visit Order');
        $sheet->setCellValue('A2', 'Period');
        $sheet->setCellValue('A3', 'Print On');
        $sheet->mergeCells('B2:C2')->setCellValue('B2', ': '. $this->period);
        $sheet->mergeCells('B3:C3')->setCellValue('B3', ': '. $this->dateNow);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(20);

        // Table Head
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Date');
        $sheet->setCellValue('C5', 'Visit Order Number');
        $sheet->setCellValue('D5', 'Debtor Name');
        $sheet->setCellValue('E5', 'Client Name');
        $sheet->setCellValue('F5', 'Site Name');
        $sheet->setCellValue('G5', 'Site Contact Name');
        $sheet->setCellValue('H5', 'Location (address)');
        $sheet->setCellValue('I5', 'Province Name');
        $sheet->setCellValue('J5', 'Regency Name');
        $sheet->setCellValue('K5', 'Visit Order Date');
        $sheet->setCellValue('L5', 'Visit Order Due Date');
        $sheet->setCellValue('M5', 'Partner Name');
        $sheet->setCellValue('N5', 'Partner NIK');
        $sheet->setCellValue('O5', 'Status');

        // Table Data
        foreach ($this->visitOrder as $key => $value) {
            $colName = 6 + $key;
            $sheet->setCellValue('A' . $colName, 1 + $key);
            $sheet->setCellValue('B' . $colName, $value->visit_order_created_date);
            $sheet->setCellValue('C' . $colName, $value->visit_order_number);
            $sheet->setCellValue('D' . $colName, $value->debtor->debtor_name);
            $sheet->setCellValue('E' . $colName, $value->client->client_name);
            $sheet->setCellValue('F' . $colName, $value->site->site_name);
            $sheet->setCellValue('G' . $colName, ($value->site_contact->site_contact_name) ? $value->site_contact->site_contact_name : "-");
            $sheet->setCellValue('H' . $colName, $value->visit_order_location);
            $sheet->setCellValue('I' . $colName, $value->province->name);
            $sheet->setCellValue('J' . $colName, $value->regency->name);
            $sheet->setCellValue('K' . $colName, $value->visit_order_date);
            $sheet->setCellValue('L' . $colName, $value->visit_order_due_date);
            $sheet->setCellValue('M' . $colName, ($value->partner) ? $value->partner->partner_name : '-');
            $sheet->setCellValue('N' . $colName, ($value->partner) ? $value->partner->partner_nik : '-');
            $sheet->setCellValue('O' . $colName, $status[$value->visit_order_status - 1]);
            $sheet->getStyle('A'.$colName.':O'.$colName)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        $sheet->getStyle('D1:D'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getStyle('H1:H'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);
    }
}
