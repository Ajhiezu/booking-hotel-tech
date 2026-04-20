<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Hotel;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reportService) {}

    protected function getOwnerHotel(): ?Hotel
    {
        return auth()->user()->hotels()->approved()->first();
    }

    public function index(Request $request)
    {
        $hotel = $this->getOwnerHotel();
        if (!$hotel) return redirect()->route('owner.dashboard')->with('error', 'No approved hotel found.');

        $financials  = $this->reportService->getHotelRevenue($hotel, $request->from, $request->to);
        $monthlyData = $this->reportService->getMonthlyBreakdown($hotel, $request->get('year', now()->year));

        $expenses = Expense::where('hotel_id', $hotel->id)
            ->when($request->from, fn($q) => $q->whereDate('expense_date', '>=', $request->from))
            ->when($request->to,   fn($q) => $q->whereDate('expense_date', '<=', $request->to))
            ->latest('expense_date')->paginate(15);

        return view('owner.reports.index', compact('hotel', 'financials', 'monthlyData', 'expenses'));
    }

    public function addExpense(Request $request)
    {
        $request->validate([
            'hotel_id'     => 'required|exists:hotels,id',
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0',
            'category'     => 'required|in:maintenance,renovation,utilities,salary,supplies,marketing,other',
            'expense_date' => 'required|date',
            'description'  => 'nullable|string',
            'receipt'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Guard: Ensure user owns this hotel and it is approved
        $hotel = auth()->user()->hotels()->approved()->where('id', $request->hotel_id)->first();
        if (!$hotel) {
            return back()->with('error', 'Invalid hotel selected or hotel is not approved.');
        }

        $data = $request->only(['title', 'amount', 'category', 'expense_date', 'description', 'hotel_id']);

        if ($request->hasFile('receipt')) {
            $data['receipt'] = $request->file('receipt')->store('expenses/receipts', 'public');
        }

        Expense::create($data);
        return back()->with('success', 'Expense recorded successfully.');
    }

    public function exportPdf(Request $request)
    {
        $hotel = $this->getOwnerHotel();
        if (!$hotel) return redirect()->route('owner.dashboard')->with('error', 'No approved hotel found.');

        $financials = $this->reportService->getHotelRevenue($hotel, $request->from, $request->to);
        $pdf        = Pdf::loadView('owner.reports.pdf', compact('hotel', 'financials'));
        return $pdf->download("report-{$hotel->slug}.pdf");
    }
}
