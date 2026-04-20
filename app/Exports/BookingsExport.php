<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BookingsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function __construct(protected string $from, protected string $to) {}

    public function query()
    {
        return Booking::with(['user', 'hotel', 'room'])
            ->whereDate('created_at', '>=', $this->from)
            ->whereDate('created_at', '<=', $this->to);
    }

    public function headings(): array
    {
        return [
            'Booking Code', 'Guest Name', 'Guest Email', 'Hotel',
            'Room', 'Check In', 'Check Out', 'Nights', 'Guests',
            'Total Amount', 'Status', 'Booking Date',
        ];
    }

    public function map($booking): array
    {
        return [
            $booking->booking_code,
            $booking->guest_name,
            $booking->guest_email,
            $booking->hotel?->name,
            $booking->room?->name,
            $booking->check_in->format('Y-m-d'),
            $booking->check_out->format('Y-m-d'),
            $booking->nights,
            $booking->guests,
            'Rp ' . number_format($booking->total_amount, 0, ',', '.'),
            ucfirst($booking->status),
            $booking->created_at->format('Y-m-d H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
