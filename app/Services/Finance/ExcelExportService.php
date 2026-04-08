<?php

namespace App\Services\Finance;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * تصدير Excel بعربي 100% مع ترميز UTF-8 بدون مشاكل
 * - خط يدعم العربية (Tahoma)
 * - اتجاه من اليمين لليسار (RTL)
 * - ترميز UTF-8 (XLSX يدعمه افتراضياً)
 */
class ExcelExportService
{
    /** خط عربي واضح في Excel */
    private const ARABIC_FONT = 'Tahoma';

    /**
     * إنشاء ملف وتدفق التصدير للتحميل
     * @param bool $useDiskCaching false للاستضافة المشتركة إذا فشل التصدير
     */
    public function download(Spreadsheet $spreadsheet, string $filename, bool $useDiskCaching = true): StreamedResponse
    {
        $filename = $this->sanitizeFilename($filename);
        if (!str_ends_with(strtolower($filename), '.xlsx')) {
            $filename .= '.xlsx';
        }

        $safeAscii = preg_replace('/[^\x20-\x7E]/', '', $filename) ?: 'export.xlsx';
        if (!str_ends_with(strtolower($safeAscii), '.xlsx')) {
            $safeAscii = 'export.xlsx';
        }
        $encodedFilename = rawurlencode($filename);

        return new StreamedResponse(function () use ($spreadsheet, $useDiskCaching) {
            $writer = new Xlsx($spreadsheet);
            $writer->setUseDiskCaching($useDiskCaching);
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $safeAscii . '"; filename*=UTF-8\'\'' . $encodedFilename,
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * إعداد ورقة بعربية كاملة: RTL + خط عربي
     */
    public function setupArabicSheet(Spreadsheet $spreadsheet, string $sheetTitle = 'بيانات'): \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($this->ensureUtf8($sheetTitle));
        $sheet->setRightToLeft(true);
        $sheet->getDefaultColumnDimension()->setWidth(18);
        $sheet->getDefaultRowDimension()->setRowHeight(18);
        return $sheet;
    }

    /**
     * تطبيق تنسيق رأس جدول عربي
     */
    public function styleHeaderRow(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, int $row, int $colCount): void
    {
        $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
        $range = "A{$row}:{$endCol}{$row}";
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'color' => ['argb' => 'FFFFFFFF'],
                'bold'  => true,
                'size'  => 11,
                'name'  => self::ARABIC_FONT,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF333333'],
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);
    }

    /**
     * تطبيق خط عربي على نطاق
     */
    public function setArabicFont(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->getFont()->setName(self::ARABIC_FONT);
        $sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }

    /**
     * تعيين قيمة خلية بالعمود والصف (بديل متوافق مع PhpSpreadsheet بعد إزالة setCellValueByColumnAndRow)
     */
    public function setCellValueByColumnAndRow(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, int $columnIndex, int $row, $value): void
    {
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex) . $row, $value);
    }

    /**
     * دمج خلايا بالعمود والصف (بديل متوافق مع PhpSpreadsheet بعد إزالة mergeCellsByColumnAndRow)
     */
    public function mergeCellsByColumnAndRow(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, int $col1, int $row1, int $col2, int $row2): void
    {
        $start = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col1) . $row1;
        $end = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col2) . $row2;
        $sheet->mergeCells("{$start}:{$end}");
    }

    /**
     * التأكد من أن النص UTF-8 (بدون تخريب الترميز)
     */
    public function ensureUtf8(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }
        $supported = array_intersect(
            ['UTF-8', 'ASCII', 'ISO-8859-6'],
            mb_list_encodings()
        );
        $encodings = $supported !== [] ? array_values($supported) : ['UTF-8', 'ASCII'];
        $enc = @mb_detect_encoding($text, $encodings, true);
        if ($enc && $enc !== 'UTF-8') {
            $out = @mb_convert_encoding($text, 'UTF-8', $enc);
            $text = $out !== false ? $out : $text;
        }
        return $text;
    }

    private function sanitizeFilename(string $name): string
    {
        $name = preg_replace('/[^\p{L}\p{N}\s\-_\.]/u', '', $name);
        return $name ?: 'export';
    }

    /**
     * إنشاء كائن Spreadsheet جديد مع إعدادات عربية
     */
    public function newSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);
        $spreadsheet->getDefaultStyle()->getFont()->setName(self::ARABIC_FONT);
        $spreadsheet->getDefaultStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        return $spreadsheet;
    }
}
