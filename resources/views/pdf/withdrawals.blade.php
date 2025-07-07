<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdrawal Requests Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-item h3 {
            margin: 0;
            color: #007bff;
            font-size: 18px;
        }
        
        .summary-item p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 11px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .status {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-processing {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-cancelled {
            background-color: #e2e3e5;
            color: #383d41;
        }
        
        .method {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .method-bank {
            background-color: #d4edda;
            color: #155724;
        }
        
        .method-opay {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .method-palmpay {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .method-airtime {
            background-color: #e2e3e5;
            color: #383d41;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        .amount {
            font-weight: bold;
            color: #007bff;
        }
        
        .reference {
            font-family: monospace;
            font-size: 9px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Withdrawal Requests Report</h1>
        <p>Generated on: {{ $generated_at }}</p>
    </div>
    
    <div class="summary">
        <div class="summary-item">
            <h3>{{ number_format($total_count) }}</h3>
            <p>Total Requests</p>
        </div>
        <div class="summary-item">
            <h3>₦{{ number_format($total_amount, 2) }}</h3>
            <p>Total Amount</p>
        </div>
        <div class="summary-item">
            <h3>{{ $withdrawals->where('status', 'pending')->count() }}</h3>
            <p>Pending</p>
        </div>
        <div class="summary-item">
            <h3>{{ $withdrawals->where('status', 'completed')->count() }}</h3>
            <p>Completed</p>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Reference</th>
                <th>User</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
                <th>Details</th>
                <th>Requested At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($withdrawals as $withdrawal)
                <tr>
                    <td class="reference">{{ $withdrawal->reference }}</td>
                    <td>
                        <strong>{{ $withdrawal->user->name }}</strong><br>
                        <small>{{ $withdrawal->user->email }}</small>
                    </td>
                    <td class="amount">₦{{ number_format($withdrawal->amount, 2) }}</td>
                    <td>
                        @php
                            $method = $withdrawal->metadata['withdrawal_method'] ?? 'unknown';
                            $methodClass = match($method) {
                                'bank_account' => 'method-bank',
                                'opay' => 'method-opay', 
                                'palmpay' => 'method-palmpay',
                                'airtime' => 'method-airtime',
                                default => 'method-bank'
                            };
                            $methodLabel = match($method) {
                                'bank_account' => 'Bank Transfer',
                                'opay' => 'Opay',
                                'palmpay' => 'PalmPay', 
                                'airtime' => 'Airtime',
                                default => ucfirst($method)
                            };
                        @endphp
                        <span class="method {{ $methodClass }}">{{ $methodLabel }}</span>
                    </td>
                    <td>
                        @php
                            $statusClass = match($withdrawal->status) {
                                'pending' => 'status-pending',
                                'processing' => 'status-processing',
                                'completed' => 'status-completed',
                                'failed' => 'status-failed',
                                'cancelled' => 'status-cancelled',
                                default => 'status-pending'
                            };
                        @endphp
                        <span class="status {{ $statusClass }}">{{ ucfirst($withdrawal->status) }}</span>
                    </td>
                    <td>
                        @if($method === 'bank_account')
                            <strong>{{ $withdrawal->metadata['bank_name'] ?? 'N/A' }}</strong><br>
                            {{ $withdrawal->metadata['account_number'] ?? 'N/A' }}<br>
                            <small>{{ $withdrawal->metadata['account_name'] ?? 'N/A' }}</small>
                        @elseif(in_array($method, ['opay', 'palmpay']))
                            <strong>{{ ucfirst($method) }}</strong><br>
                            {{ $withdrawal->metadata['phone_number'] ?? 'N/A' }}
                        @elseif($method === 'airtime')
                            <strong>{{ $withdrawal->metadata['network'] ?? 'N/A' }}</strong><br>
                            {{ $withdrawal->metadata['phone_number'] ?? 'N/A' }}
                        @endif
                        
                        @if(isset($withdrawal->metadata['net_amount']))
                            <br><small>Net: ₦{{ number_format($withdrawal->metadata['net_amount'], 2) }}</small>
                        @endif
                    </td>
                    <td>{{ $withdrawal->created_at->format('M j, Y g:i A') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px; color: #666;">
                        No withdrawal requests found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        <p>This report was generated automatically by the system.</p>
        <p>For any questions or concerns, please contact the system administrator.</p>
    </div>
</body>
</html>