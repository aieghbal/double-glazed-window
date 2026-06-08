<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ایجاد فاکتور توری پیوندی</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* تمام استایل‌های قبلی شما اینجا قرار می‌گیرد */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 15px;
            min-height: 100vh;
        }
        .invoice-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .invoice-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .invoice-body { padding: 20px; }
        .info-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 15px;
        }
        .info-item { flex: 1; min-width: 180px; }
        .info-item label { font-weight: 600; color: #2c3e50; display: block; margin-bottom: 5px; }
        .info-item input, .info-item select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .table-wrapper { overflow-x: auto; margin: 20px 0; }
        table {
            width: 100%;
            min-width: 800px;
            border-collapse: collapse;
            direction: rtl;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th { background-color: #34495e; color: white; }
        td input {
            width: 100%;
            min-width: 70px;
            padding: 6px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
        }
        .readonly-cell { background-color: #f8f9fa; font-weight: 500; }
        .total-price { font-size: 15px !important; font-weight: bold !important; background-color: #e8f4f8 !important; }
        .extra-row {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            flex-wrap: wrap;
            margin: 15px 0;
        }
        .extra-item {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 10px;
        }
        .extra-item input { width: 120px; padding: 6px 10px; }
        .total-row {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            justify-content: space-between;
            padding: 15px 20px;
            border-radius: 10px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-calculate { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn-reset { background: #95a5a6; color: white; }
        .btn-print { background: #2ecc71; color: white; }
        .btn-save { background: #e67e22; color: white; }
        .note {
            margin-top: 20px;
            padding: 12px;
            background: #fff3cd;
            border-radius: 10px;
            border-right: 4px solid #ffc107;
            font-size: 11px;
            text-align: center;
        }
        @media print {
            .button-group, .note, .btn-save { display: none !important; }
            .empty-row { display: none !important; }
            td input { border: none !important; background: transparent !important; }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
<div class="invoice-container">
    <div class="invoice-header">
        <h1>🏭 پیش‌فاکتور توری پیوندی</h1>
        <p>فروش توری بصورت تمام نقدی می‌باشد</p>
    </div>

    <div class="invoice-body">
        <div class="info-row">
            <div class="info-item">
                <label>📝 نام مشتری:</label>
                <input type="text" id="customerName" placeholder="مثال: آقای پیوندی">
            </div>
            <div class="info-item">
                <label>📅 تاریخ:</label>
                <input type="text" id="date" placeholder="مثال: 1405.02.30">
            </div>
        </div>

        <div class="table-wrapper">
            <table id="itemsTable">
                <thead>
                <tr><th>ردیف</th><th>نوع سفارش</th><th>عرض (cm)</th><th>ارتفاع (cm)</th><th>مساحت</th><th>تعداد</th><th>متراژ</th><th>قیمت واحد</th><th>قیمت توری</th></tr>
                </thead>
                <tbody>
                @for ($i = 1; $i <= 10; $i++)
                    <tr class="item-row" data-row="{{ $i }}">
                        <td>{{ $i }}</td>
                        <td><input type="text" class="order-type" placeholder="نوع سفارش"></td>
                        <td><input type="text" class="width number-input" step="any" placeholder="0"></td>
                        <td><input type="text" class="height number-input" step="any" placeholder="0"></td>
                        <td class="area readonly-cell">0</td>
                        <td><input type="text" class="quantity number-input" value="{{ $i <= 3 ? '1' : '0' }}" step="1"></td>
                        <td class="meterage readonly-cell">0</td>
                        <td><input type="text" class="unit-price number-input" step="any" placeholder="0"></td>
                        <td class="total-price readonly-cell">0</td>
                    </tr>
                @endfor
                </tbody>
            </table>
        </div>

        <div class="extra-row">
            <div class="extra-item"><label>🚚 هزینه ارسال:</label><input type="text" id="shippingCost" class="number-input" placeholder="0"></div>
            <div class="extra-item"><label>🔧 اجرت نصب:</label><input type="text" id="installationCost" class="number-input" placeholder="0"></div>
        </div>

        <div class="total-row">
            <span class="label">💰 جمع کل (تومان):</span>
            <span class="value" id="grandTotal">0</span>
        </div>

        <div class="button-group">
            <button class="btn btn-calculate" id="calculateBtn">🧮 محاسبه</button>
            <button class="btn btn-reset" id="resetBtn">🗑️ پاک کردن فرم</button>
            <button class="btn btn-save" id="saveBtn">💾 ذخیره فاکتور</button>
            <button class="btn btn-print" id="printBtn">🖨️ پرینت</button>
        </div>

        <div class="note">
            <p>⏰ مدت اعتبار پیش‌فاکتور 24 ساعت می‌باشد | 💰 فروش توری بصورت تمام نقدی می‌باشد</p>
            <p>📏 متراژ = IF(مساحت < 0.8, 0.8, مساحت) × تعداد (حداقل متراژ 0.8 مترمربع)</p>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        function isRowEmpty(row) {
            let width = $(row).find('.width').val();
            let height = $(row).find('.height').val();
            let quantity = $(row).find('.quantity').val();
            let unitPrice = $(row).find('.unit-price').val();
            let orderType = $(row).find('.order-type').val();
            let clean = v => v ? v.replace(/,/g, '') : '';
            return !(clean(width) && parseFloat(clean(width)) !== 0 ||
                clean(height) && parseFloat(clean(height)) !== 0 ||
                clean(quantity) && parseFloat(clean(quantity)) !== 0 ||
                clean(unitPrice) && parseFloat(clean(unitPrice)) !== 0 ||
                (orderType && orderType.trim() !== ''));
        }

        function updateEmptyRowsClass() {
            $('.item-row').each(function() {
                if (isRowEmpty($(this))) $(this).addClass('empty-row');
                else $(this).removeClass('empty-row');
            });
        }

        function formatNumberWithCommas(value) {
            if (!value) return '';
            let clean = value.toString().replace(/[^0-9.]/g, '');
            let parts = clean.split('.');
            let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            return parts.length > 1 ? integerPart + '.' + parts[1] : integerPart;
        }

        function parseNumberFromCommas(value) {
            if (!value) return NaN;
            return parseFloat(value.toString().replace(/,/g, ''));
        }

        function formatNumber(value, decimals=4) {
            if (isNaN(value) || value === 0) return '0';
            return parseFloat(value.toFixed(decimals)).toString();
        }

        function formatPrice(value) {
            if (isNaN(value) || value === 0) return '0';
            return Math.round(value).toLocaleString('fa-IR');
        }

        function calculateRow(row) {
            let width = parseNumberFromCommas($(row).find('.width').val());
            let height = parseNumberFromCommas($(row).find('.height').val());
            let quantity = parseNumberFromCommas($(row).find('.quantity').val());
            let unitPrice = parseNumberFromCommas($(row).find('.unit-price').val());

            if (isNaN(width) || isNaN(height) || width <= 0 || height <= 0 || isNaN(quantity) || quantity === 0 || isNaN(unitPrice)) {
                $(row).find('.area').text('0');
                $(row).find('.meterage').text('0');
                $(row).find('.total-price').text('0');
                return 0;
            }

            let area = (width * height) / 10000;
            let meterage = (area < 0.8 ? 0.8 : area) * quantity;
            let totalPrice = unitPrice * meterage;

            $(row).find('.area').text(formatNumber(area, 4));
            $(row).find('.meterage').text(formatNumber(meterage, 4));
            $(row).find('.total-price').text(formatPrice(totalPrice));
            return totalPrice;
        }

        function calculateAll() {
            let sum = 0;
            $('.item-row').each(function() { let t = calculateRow($(this)); if(!isNaN(t)) sum += t; });
            let shipping = parseNumberFromCommas($('#shippingCost').val()) || 0;
            let install = parseNumberFromCommas($('#installationCost').val()) || 0;
            let grand = sum + shipping + install;
            $('#grandTotal').text(formatPrice(grand));
            updateEmptyRowsClass();
            return grand;
        }

        function getItemsArray() {
            let items = [];
            $('.item-row').each(function(index) {
                let row = $(this);
                let width = parseNumberFromCommas(row.find('.width').val()) || 0;
                let height = parseNumberFromCommas(row.find('.height').val()) || 0;
                let quantity = parseNumberFromCommas(row.find('.quantity').val()) || 0;
                let unitPrice = parseNumberFromCommas(row.find('.unit-price').val()) || 0;
                let orderType = row.find('.order-type').val() || '';
                let area = (width * height) / 10000;
                let meterage = (area < 0.8 && area > 0 ? 0.8 : area) * quantity;
                let totalPrice = unitPrice * meterage;

                items.push({
                    row: index+1,
                    order_type: orderType,
                    width: width,
                    height: height,
                    area: area,
                    quantity: quantity,
                    meterage: meterage,
                    unit_price: unitPrice,
                    total_price: totalPrice
                });
            });
            return items;
        }

        function saveInvoice() {
            let grand = calculateAll();
            let invoiceData = {
                customer_name: $('#customerName').val(),
                date: $('#date').val(),
                items: getItemsArray(),
                shipping_cost: parseNumberFromCommas($('#shippingCost').val()) || 0,
                installation_cost: parseNumberFromCommas($('#installationCost').val()) || 0,
                grand_total: grand
            };

            $.ajax({
                url: '/api/invoices',
                type: 'POST',
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: JSON.stringify(invoiceData),
                success: function(response) {
                    alert('فاکتور با موفقیت ذخیره شد. شماره: ' + response.id);
                },
                error: function(xhr) {
                    alert('خطا در ذخیره فاکتور: ' + xhr.responseText);
                }
            });
        }

        function bindEvents() {
            $('.number-input').on('input', function() {
                let val = $(this).val();
                let cursor = this.selectionStart;
                let oldLen = val.length;
                let formatted = formatNumberWithCommas(val);
                $(this).val(formatted);
                let newLen = formatted.length;
                this.setSelectionRange(cursor + (newLen - oldLen), cursor + (newLen - oldLen));
                calculateAll();
            });
            $('#calculateBtn').on('click', () => calculateAll());
            $('#resetBtn').on('click', () => {
                $('.item-row .number-input').val('');
                $('.item-row').each(function(i) { $(this).find('.quantity').val(i < 3 ? '1' : '0'); });
                $('#shippingCost, #installationCost, #customerName, #date').val('');
                calculateAll();
            });
            $('#saveBtn').on('click', () => saveInvoice());
            $('#printBtn').on('click', () => { updateEmptyRowsClass(); window.print(); });
        }

        bindEvents();
        calculateAll();
    });
</script>
</body>
</html>
