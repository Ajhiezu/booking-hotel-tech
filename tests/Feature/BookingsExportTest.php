<?php

namespace Tests\Feature;

use App\Exports\BookingsExport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class BookingsExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_report_can_be_exported_as_a_styled_xlsx_file(): void
    {
        $export = new BookingsExport('2026-11-01', '2026-11-30');
        $content = Excel::raw($export, ExcelFormat::XLSX);
        $file = tmpfile();
        $spreadsheet = null;

        try {
            fwrite($file, $content);
            $spreadsheet = IOFactory::load(stream_get_meta_data($file)['uri']);
            $sheet = $spreadsheet->getActiveSheet();

            $this->assertSame('Booking Code', $sheet->getCell('A1')->getValue());
            $this->assertSame('Booking Date', $sheet->getCell('L1')->getValue());
            $this->assertTrue($sheet->getStyle('A1')->getFont()->getBold());
            $this->assertSame(1, $sheet->getHighestRow());
        } finally {
            $spreadsheet?->disconnectWorksheets();
            fclose($file);
        }
    }
}
