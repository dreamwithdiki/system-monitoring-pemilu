<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ReportExcelExport implements WithStyles, WithDrawings
{
    protected $visitOrder;
    protected $date;
    protected $checklistGroup;
    protected $checklistAnswer;
    protected $latest_visit_order_history;

    public function __construct($visitOrder, $date, $checklistGroup, $checklistAnswer, $latest_visit_order_history)
    {
        $this->visitOrder = $visitOrder;
        $this->date = $date;
        $this->checklistGroup = $checklistGroup;
        $this->checklistAnswer = $checklistAnswer;
        $this->latest_visit_order_history = $latest_visit_order_history;
    }

    public function styles(Worksheet $sheet)
    {
        $alphabet = range('A', 'Z');
        $lastRow = 0;

        // Setting sheet
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Times New Roman');
        $sheet->getParent()->getDefaultStyle()->getFont()->setSize(9);
        for ($i = 0; $i < 12; $i++) {
            $sheet->getColumnDimension($alphabet[$i])->setWidth(13.5);
        }

        // Title sheet
        $sheet->mergeCells('A1:F1')->setCellValue('A1', 'No: ' . $this->visitOrder->visit_order_number);
        $sheet->mergeCells('G1:L1')->setCellValue('G1', 'Lampiran 3');
        $sheet->mergeCells('A2:L2')->setCellValue('A2', 'Laporan Site Visit');
        $sheet->getStyle('G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2')->getFont()->setBold(true);

        // Body Sheet
        $sheet->mergeCells('A3:B3')->setCellValue('A3', 'Nama Debitur');
        $sheet->mergeCells('C3:F3')->setCellValue('C3', ': ' . $this->visitOrder->debtor->debtor_name);
        $sheet->mergeCells('G3:H3')->setCellValue('G3', 'Tanggal Peninjauan');
        $sheet->mergeCells('I3:L3')->setCellValue('I3', ': ' . $this->latest_visit_order_history);
        $sheet->mergeCells('A4:B4')->setCellValue('A4', 'Cabang Pemohon'.(($this->visitOrder->visit_order_custom_number) ? ' & Kode' : ''));
        $sheet->mergeCells('C4:F4')->setCellValue('C4', ': ' . $this->visitOrder->site->site_name.(($this->visitOrder->visit_order_custom_number) ? '('.$this->visitOrder->visit_order_custom_number.')' : ''));
        $sheet->mergeCells('G4:H4')->setCellValue('G4', 'Nama AO');
        $sheet->mergeCells('I4:L4')->setCellValue('I4', ': ' . $this->visitOrder->site_contact->site_contact_fullname);
        $sheet->mergeCells('A5:B5')->setCellValue('A5', 'Lokasi/Alamat Agunan');
        $sheet->mergeCells('C5:F5')->setCellValue('C5', ': ' . $this->visitOrder->visit_order_location);
        $sheet->mergeCells('G5:H5')->setCellValue('G5', 'Foto');
        $sheet->mergeCells('I5:L5')->setCellValue('I5', ': ');
        $sheet->getRowDimension(5)->setRowHeight(23.0);
        $sheet->getStyle('A5:L5')->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
        $sheet->getStyle('A3:L5')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

        // Visual Image
        if ($this->visitOrder->visit_order_visual->count() > 3) {
            foreach ($this->visitOrder->visit_order_visual->chunk(4) as $key => $listVisitOrderVisual) {
                $no = 6 + $key;
                $colNo = 0;
                foreach ($listVisitOrderVisual as $num => $visual) {
                    $sheet->mergeCells($alphabet[$colNo] . $no . ':' . $alphabet[$colNo + 2] . $no)->setCellValue($alphabet[$colNo] . $no, $visual->visit_order_visual_file_name);
                    $sheet->getStyle($alphabet[$colNo] . $no)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $colNo += 3;
                }
                for ($i = 4; $i > $listVisitOrderVisual->count(); $i--) {
                    $sheet->mergeCells($alphabet[$colNo] . $no . ':' . $alphabet[$colNo + 2] . $no)->setCellValue($alphabet[$colNo] . $no, '');
                    $colNo += 3;
                }
                $sheet->getStyle('A' . $no . ':L' . $no)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getRowDimension($no)->setRowHeight(150.0);
                $lastRow = $no;
            }
        } else {
            foreach ($this->visitOrder->visit_order_visual->chunk(3) as $key => $listVisitOrderVisual) {
                $no = 6 + $key;
                $colNo = 0;
                foreach ($listVisitOrderVisual as $num => $visual) {
                    $sheet->mergeCells($alphabet[$colNo] . $no . ':' . $alphabet[$colNo + 3] . $no)->setCellValue($alphabet[$colNo] . $no, $visual->visit_order_visual_file_name);
                    $sheet->getStyle($alphabet[$colNo] . $no)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $colNo += 4;
                }
                for ($i = 3; $i > $listVisitOrderVisual->count(); $i--) {
                    $sheet->mergeCells($alphabet[$colNo] . $no . ':' . $alphabet[$colNo + 3] . $no)->setCellValue($alphabet[$colNo] . $no, '');
                    $colNo += 4;
                }
                $sheet->getStyle('A' . $no . ':L' . $no)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getRowDimension($no)->setRowHeight(150.0);
                $lastRow = $no;
            }
        }

        $lastRow++;
        $sheet->mergeCells('A' . $lastRow . ':L' . $lastRow)->setCellValue('A' . $lastRow, 'Keterangan:');
        $sheet->getStyle('A' . $lastRow . ':L' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Checklist 
        foreach ($this->checklistGroup as $key => $group) {
            $lastRow++;
            $sheet->mergeCells('A' . $lastRow . ':L' . $lastRow)->setCellValue('A' . $lastRow, ($key + 1) . '. ' . $group->checklist_group_name);
            $sheet->getStyle('A' . $lastRow . ':L' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            if (12 % $group->checklist_active->count() != 0 || $group->checklist_active->count() == 12) {
                foreach ($group->checklist_active->chunk(6) as $checklistChunk) {
                    $lastRow++;
                    $colNo = 0;
                    $tooLong = false;
                    foreach ($checklistChunk as $key => $checklist) {
                        $isSame = false;
                        $textAnswer = '';
                        foreach ($this->checklistAnswer as $answer) {
                            if ($checklist->checklist_id == $answer->checklist_id) {
                                $isSame = true;
                                ($checklist->checklist_is_freetext == 2) ? $textAnswer = $answer->checklist_answer : $textAnswer = '';
                            }
                        }
                        $sheet->mergeCells($alphabet[$colNo] . $lastRow . ':' . $alphabet[$colNo + 1] . $lastRow)->setCellValue($alphabet[$colNo] . $lastRow, ($textAnswer != '') ? (($isSame) ? '☑ ' . $checklist->checklist_name . ': ' . $textAnswer : '☐ ' . $checklist->checklist_name . ': ' . $textAnswer) : (($isSame) ? '☑ ' . $checklist->checklist_name : '☐ ' . $checklist->checklist_name));
                        $sheet->getStyle($alphabet[$colNo] . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                        if ($isSame) {
                            $sheet->getStyle($alphabet[$colNo] . $lastRow)->getFont()->setBold(true);
                        }
                        if (strlen($checklist->checklist_name) > 27) {
                            $sheet->getStyle($alphabet[$colNo] . $lastRow)->getFont()->setSize(7);
                        }
                        if (strlen($checklist->checklist_name) > 35) {
                            $tooLong = true;
                        }
                        $colNo += 2;
                    }
                    for ($i = 6; $i > $checklistChunk->count(); $i--) {
                        $sheet->mergeCells($alphabet[$colNo] . $lastRow . ':' . $alphabet[$colNo + 1] . $lastRow)->setCellValue($alphabet[$colNo] . $lastRow, '');
                        $colNo += 2;
                    }
                    $sheet->getStyle('A' . $lastRow . ':L' . $lastRow)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle('A' . $lastRow . ':L' . $lastRow)->getAlignment()->setWrapText(true);
                    if ($tooLong) {
                        $sheet->getRowDimension($lastRow)->setRowHeight(23.0);
                    }
                }
            } else {
                $colTo = 12 / $group->checklist_active->count();
                foreach ($group->checklist_active->chunk($group->checklist_active->count()) as $checklistChunk) {
                    $lastRow++;
                    $colNo = 0;
                    foreach ($checklistChunk as $key => $checklist) {
                        $isSame = false;
                        $textAnswer = '';
                        foreach ($this->checklistAnswer as $answer) {
                            if ($checklist->checklist_id == $answer->checklist_id) {
                                $isSame = true;
                                ($checklist->checklist_is_freetext == 2) ? $textAnswer = $answer->checklist_answer : $textAnswer = '';
                            }
                        }
                        $sheet->mergeCells($alphabet[$colNo] . $lastRow . ':' . $alphabet[$colNo + $colTo - 1] . $lastRow)->setCellValue($alphabet[$colNo] . $lastRow, ($textAnswer != '') ? (($isSame) ? '☑ ' . $checklist->checklist_name . ': ' . $textAnswer : '☐ ' . $checklist->checklist_name . ': ' . $textAnswer) : (($isSame) ? '☑ ' . $checklist->checklist_name : '☐ ' . $checklist->checklist_name));
                        $sheet->getStyle($alphabet[$colNo] . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                        if ($isSame) {
                            $sheet->getStyle($alphabet[$colNo] . $lastRow)->getFont()->setBold(true);
                        }
                        if (strlen($checklist->checklist_name) > 27) {
                            $sheet->getStyle($alphabet[$colNo] . $lastRow)->getFont()->setSize(7);
                        }
                        $colNo += $colTo;
                    }
                    for ($i = $group->checklist_active->count(); $i > $checklistChunk->count(); $i--) {
                        $sheet->mergeCells($alphabet[$colNo] . $lastRow . ':' . $alphabet[$colNo + 3] . $lastRow)->setCellValue($alphabet[$colNo] . $lastRow, '');
                        $colNo += $colTo;
                    }
                    $sheet->getStyle('A' . $lastRow . ':L' . $lastRow)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle('A' . $lastRow . ':L' . $lastRow)->getAlignment()->setWrapText(true);
                }
            }
        }

        // Signature Head
        $lastRow++;
        $sheet->mergeCells('A' . $lastRow . ':D' . $lastRow)->setCellValue('A' . $lastRow, "Surveyor");
        $sheet->mergeCells('E' . $lastRow . ':H' . $lastRow)->setCellValue('E' . $lastRow, "Account Manager");
        $sheet->mergeCells('I' . $lastRow . ':L' . $lastRow)->setCellValue('I' . $lastRow, "Account Officer");
        $sheet->getStyle('A' . $lastRow . ':L' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $lastRow . ':L' . $lastRow)->getFont()->setBold(true);

        // Signature Name
        $lastRow++;
        $sheet->mergeCells('A' . $lastRow . ':D' . $lastRow + 2)->setCellValue('A' . $lastRow, session('user_uniq_name'));
        $sheet->mergeCells('E' . $lastRow . ':H' . $lastRow + 2)->setCellValue('E' . $lastRow, "Steve");
        $sheet->mergeCells('I' . $lastRow . ':L' . $lastRow + 2)->setCellValue('I' . $lastRow, $this->visitOrder->site_contact->site_contact_fullname);
        $sheet->getStyle('A' . $lastRow . ':L' . $lastRow + 2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_BOTTOM);
    }

    public function drawings()
    {
        $alphabet = range('A', 'Z');
        $listDraw = [];
        if ($this->visitOrder->visit_order_visual->count() > 3) {
            foreach ($this->visitOrder->visit_order_visual->chunk(4) as $key => $listVisitOrderVisual) {
                $no = 6 + $key;
                $colNo = 0;
                foreach ($listVisitOrderVisual as $num => $value) {
                    $drawing = new Drawing();
                    $drawing->setName($value->visit_order_visual_file_name);
                    $drawing->setDescription($value->visit_order_visual_file_desc ?? '');
                    if (file_exists(public_path('storage/visit_order_visual_uploads/' . $this->date . '/' . $value->visit_order_visual_file))) {
                        $drawing->setPath(public_path('storage/visit_order_visual_uploads/' . $this->date . '/' . $value->visit_order_visual_file));
                    } else {
                        $drawing->setPath(public_path('assets/img/no-image-asset.jpg'));
                    }
                    $drawing->setWidthAndHeight(240, 185);
                    $drawing->setCoordinates($alphabet[$colNo] . $no);
                    array_push($listDraw, $drawing);
                    $colNo += 3;
                }
            }
        } else {
            foreach ($this->visitOrder->visit_order_visual->chunk(3) as $key => $listVisitOrderVisual) {
                $no = 6 + $key;
                $colNo = 0;
                foreach ($listVisitOrderVisual as $num => $value) {
                    $drawing = new Drawing();
                    $drawing->setName($value->visit_order_visual_file_name);
                    $drawing->setDescription($value->visit_order_visual_file_desc ?? '');
                    if (file_exists(public_path('storage/visit_order_visual_uploads/' . $this->date . '/' . $value->visit_order_visual_file))) {
                        $drawing->setPath(public_path('storage/visit_order_visual_uploads/' . $this->date . '/' . $value->visit_order_visual_file));
                    } else {
                        $drawing->setPath(public_path('assets/img/no-image-asset.jpg'));
                    }
                    $drawing->setWidthAndHeight(320, 185);
                    $drawing->setCoordinates($alphabet[$colNo] . $no);
                    array_push($listDraw, $drawing);
                    $colNo += 4;
                }
            }
        }
        return $listDraw;
    }
}
