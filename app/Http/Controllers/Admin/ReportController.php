<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingsExport;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reportService) {}

    public function index(Request $request)
    {
        $from = $request->get('from');
        $to   = $request->get('to');

        $metrics = $this->reportService->getFilteredStats($from, $to);

        $monthlyData = [];
        $year = $request->get('year', now()->year);
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[] = [
                'month'    => date('M', mktime(0, 0, 0, $m, 1)),
                'revenue'  => Booking::whereIn('status', ['confirmed', 'checked_in', 'completed'])
                    ->whereMonth('created_at', $m)->whereYear('created_at', $year)->sum('total_amount'),
                'bookings' => Booking::whereMonth('created_at', $m)->whereYear('created_at', $year)->count(),
            ];
        }

        $auditLogs = AuditLog::with('user')->latest()->paginate(20);

        return view('admin.reports.index', compact('metrics', 'monthlyData', 'auditLogs', 'year'));
    }

    public function exportPdf(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to',   now()->endOfMonth()->toDateString());

        $bookings = Booking::with(['user', 'hotel', 'room'])
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->get();

        $revenue = $bookings->whereIn('status', ['confirmed', 'checked_in', 'completed'])->sum('total_amount');

        $pdf = Pdf::loadView('admin.reports.pdf', compact('bookings', 'revenue', 'from', 'to'));
        return $pdf->download("report-{$from}-to-{$to}.pdf");
    }

    public function exportExcel(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to',   now()->endOfMonth()->toDateString());
        return Excel::download(new BookingsExport($from, $to), "bookings-{$from}-to-{$to}.xlsx");
    }
}
