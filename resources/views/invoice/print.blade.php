<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>چاپ فاکتور شماره {{ $invoice->id }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background: #f3f4f6;
            color: #111827;
            direction: rtl;
        }
        .page {
            max-width: 1120px;
            margin: 30px auto;
            padding: 24px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 32px;
        }
        .header h1 {
            font-size: 28px;
            margin: 0;
        }
        .meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(180px, 1fr));
            gap: 12px;
            margin-top: 20px;
        }
        .meta-item {
            padding: 14px 16px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
        }
        .meta-item strong {
            display: block;
            margin-bottom: 6px;
            color: #0f172a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 12px 14px;
            text-align: center;
            vertical-align: middle;
        }
        th {
            background: #1f2937;
            color: white;
            font-weight: 600;
        }
        .summary {
            margin-top: 24px;
            display: grid;
            grid-template-columns: repeat(3, minmax(160px, 1fr));
            gap: 14px;
        }
        .summary-item {
            padding: 16px 18px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
        }
        .summary-item strong {
            display: block;
            margin-bottom: 8px;
            color: #0f172a;
        }
        .footer {
            margin-top: 32px;
            text-align: center;
            color: #4b5563;
            font-size: 14px;
        }
        @media print {
            body {
                background: white;
            }
            .page {
                box-shadow: none;
                margin: 0;
                border-radius: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">
<div class="page">
    <div class="header">
        <div>
            <h1>فاکتور فروش</h1>
            <p>شماره فاکتور: {{ $invoice->id }}</p>
        </div>
        <div class="meta">
            <div class="meta-item">
                <strong>نام مشتری</strong>
                {{ $invoice->customer_name ?: '-' }}
            </div>
            <div class="meta-item">
                <strong>تاریخ</strong>
                {{ $invoice->date ?: '-' }}
            </div>
            <div class="meta-item">
                <strong>تاریخ ثبت</strong>
                {{ $invoice->created_at ? $invoice->created_at->format('Y-m-d H:i') : '-' }}
            </div>
            <div class="meta-item">
                <strong>آخرین بروزرسانی</strong>
                {{ $invoice->updated_at ? $invoice->updated_at->format('Y-m-d H:i') : '-' }}
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ردیف</th>
                <th>نوع سفارش</th>
                <th>عرض</th>
                <th>ارتفاع</th>
                <th>مساحت</th>
                <th>تعداد</th>
                <th>متراژ</th>
                <th>قیمت واحد</th>
                <th>جمع سطر</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item['order_type'] ?: ($item['description'] ?? '-') }}</td>
                    <td>{{ $item['width'] ?? 0 }}</td>
                    <td>{{ $item['height'] ?? $item['length'] ?? 0 }}</td>
                    <td>{{ $item['area'] ?? 0 }}</td>
                    <td>{{ $item['quantity'] ?? 0 }}</td>
                    <td>{{ $item['meterage'] ?? 0 }}</td>
                    <td>{{ number_format($item['unit_price'] ?? 0, 2) }}</td>
                    <td>{{ number_format($item['total_price'] ?? ($item['line_total'] ?? 0), 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-item">
            <strong>هزینه ارسال</strong>
            {{ number_format($invoice->shipping_cost ?? 0, 2) }}
        </div>
        <div class="summary-item">
            <strong>هزینه نصب</strong>
            {{ number_format($invoice->installation_cost ?? 0, 2) }}
        </div>
        <div class="summary-item">
            <strong>جمع کل</strong>
            {{ number_format($invoice->grand_total ?? 0, 2) }}
        </div>
    </div>

    <div class="footer">
        <p>فاکتور فروش</p>
    </div>
</div>
</body>
</html>
