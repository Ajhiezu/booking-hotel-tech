<!DOCTYPE html>
<html>
<head>
    <title>Global System Report</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
        .title { font-size: 20px; font-weight: bold; color: #1e3a8a; margin-bottom: 5px; }
        .subtitle { color: #6b7280; font-size: 14px; }
        .stats-grid { width: 100%; margin-bottom: 30px; }
        .stat-box { background: #f3f4f6; padding: 15px; border-radius: 8px; text-align: center; }
        .stat-value { font-size: 18px; font-weight: bold; color: #3b82f6; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f9fafb; color: #4b5563; font-weight: bold; text-align: left; padding: 10px; border-bottom: 1px solid #e5e7eb; text-transform: uppercase; font-size: 10px; }
        td { padding: 10px; border-bottom: 1px solid #f3f4f6; }
        .text-right { text-align: right; }
        .badge { padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: bold; }
        .badge-success { background: #def7ec; color: #03543f; }
        .badge-warning { background: #fdf6b2; color: #723b13; }
        .badge-danger { background: #fde8e8; color: #9b1c1c; }
        .badge-info { background: #e1effe; color: #1e429f; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #9ca3af; padding: 10px 0; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">StayEase Global System Report</div>
        <div class="subtitle">Period: {{ $from }} to {{ $to }}</div>
    </div>

    <table class="stats-grid">
        <tr>
            <td style="width: 50%; border-bottom: none;">
                <div class="stat-box">
                    <div style="font-size: 10px; color: #6b7280; margin-bottom: 5px;">TOTAL BOOKINGS</div>
                    <div class="stat-value">{{ $bookings->count() }}</div>
                </div>
            </td>
            <td style="width: 50%; border-bottom: none;">
                <div class="stat-box">
                    <div style="font-size: 10px; color: #6b7280; margin-bottom: 5px;">TOTAL REVENUE</div>
                    <div class="stat-value">Rp {{ number_format($revenue, 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <h3 style="color: #111827; border-left: 4px solid #3b82f6; padding-left: 10px;">Booking Details</h3>
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Guest</th>
                <th>Hotel</th>
                <th>Dates</th>
                <th>Status</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings as $booking)
                <tr>
                    <td style="font-family: monospace;">{{ $booking->booking_code }}</td>
                    <td>
                        {{ $booking->guest_name }}<br>
                        <small style="color: #6b7280;">{{ $booking->guest_email }}</small>
                    </td>
                    <td>{{ $booking->hotel?->name }}</td>
                    <td>
                        {{ $booking->check_in->format('d/m/y') }}<br>
                        {{ $booking->check_out->format('d/m/y') }}
                    </td>
                    <td>
                        @php 
                            $badgeClass = match($booking->status) {
                                'completed' => 'badge-success',
                                'pending' => 'badge-warning',
                                'cancelled' => 'badge-danger',
                                default => 'badge-info'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($booking->status) }}</span>
                    </td>
                    <td class="text-right">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('d M Y H:i:s') }} | StayEase Hotel Booking System
    </div>
</body>
</html>
