<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: normal;
            src: url("{{ public_path('fonts/THSarabunNew.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: bold;
            src: url("{{ public_path('fonts/THSarabunNew-Bold.ttf') }}") format('truetype');
        }
        body {
            font-size: 18px; 
            line-height: 1.2;
        }
        .thai-font {
            font-family: 'THSarabunNew', sans-serif;
        }
        body { 
            font-family: 'THSarabunNew', sans-serif; font-size: 16px; line-height: 1; 
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .table th, .table td { border: 1px solid #000; padding: 5px; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
    </style>
</head>
<body class="thai-font">
    <div class="header text-center">
        <h2>สลิปเงินเดือน (PAYSLIP)</h2>
        <p>ประจำเดือน: {{ \Carbon\Carbon::parse($pr->period)->translatedFormat('F Y') }}</p>
    </div>

    <table class="table">
        <tr>
            <td><strong>รหัสพนักงาน:</strong> {{ $pr->employee->employee_code }}</td>
            <td><strong>ชื่อ-สกุล:</strong> {{ $pr->employee->first_name }} {{ $pr->employee->last_name }}</td>
        </tr>
        <tr>
            <td><strong>แผนก:</strong> {{ $pr->employee->department->name ?? '-' }}</td>
            <td><strong>ตำแหน่ง:</strong> {{ is_object($pr->employee->position) ? $pr->employee->position->title : $pr->employee->position }}</td>
        </tr>
    </table>

    <table class="table">
        <thead style="background-color: #eee;">
            <tr>
                <th>รายการรายรับ (Earnings)</th>
                <th>จำนวนเงิน</th>
                <th>รายการรายหัก (Deductions)</th>
                <th>จำนวนเงิน</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>เงินเดือนพื้นฐาน</td>
                <td class="text-right">{{ number_format($pr->base_salary, 2) }}</td>
                <td>ประกันสังคม</td>
                <td class="text-right">{{ number_format($pr->social_security, 2) }}</td>
            </tr>
            <tr>
                <td>โอที (OT)</td>
                <td class="text-right">{{ number_format($pr->ot_amount, 2) }}</td>
                <td>ภาษีหัก ณ ที่จ่าย</td>
                <td class="text-right">{{ number_format($pr->tax_amount, 2) }}</td>
            </tr>
            @php
                // ดึงรายการ Items มาแสดงในตารางหลัก
                $additions = $pr->items->where('item_type', 'Addition');
                $deductions = $pr->items->where('item_type', 'Deduction');
                $maxRows = max($additions->count(), $deductions->count());
            @endphp
            @for ($i = 0; $i < $maxRows; $i++)
            <tr>
                <td>{{ $additions->values()[$i]->item_name ?? '' }}</td>
                <td class="text-right">{{ isset($additions->values()[$i]) ? number_format($additions->values()[$i]->amount, 2) : '' }}</td>
                <td>{{ $deductions->values()[$i]->item_name ?? '' }}</td>
                <td class="text-right">{{ isset($deductions->values()[$i]) ? number_format($deductions->values()[$i]->amount, 2) : '' }}</td>
            </tr>
            @endfor
        </tbody>
        <tfoot>
            <tr style="background-color: #eee; font-weight: bold;">
                <td>รวมเงินได้</td>
                <td class="text-right">{{ number_format($pr->base_salary + $pr->ot_amount + $additions->sum('amount'), 2) }}</td>
                <td>รวมเงินหัก</td>
                <td class="text-right">{{ number_format($pr->social_security + $pr->tax_amount + $deductions->sum('amount'), 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px; border: 2px solid #000; padding: 10px; background-color: #f9f9f9;">
        <h3 class="text-center" style="margin: 0;">รายรับสุทธิ (Net Pay): {{ number_format($pr->net_salary, 2) }} บาท</h3>
    </div>

    <p style="font-size: 12px; margin-top: 30px;">* เอกสารนี้จัดทำโดยระบบอัตโนมัติ ไม่ต้องมีลายมือชื่อพนักงานและผู้อนุมัติ</p>
</body>
</html>