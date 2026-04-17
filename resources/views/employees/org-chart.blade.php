@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-3">
        <div class="col-md-12 text-center">
            <h4 class="fw-bold text-primary">🏢 โครงสร้างองค์กร (Organization Chart)</h4>
            <p class="text-muted">สายการบังคับบัญชาของ RotiSoft</p>
        </div>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 rounded-4 overflow-auto">
                <div class="card-body p-4 d-flex justify-content-center" style="min-height: 500px; overflow-x: auto;">
                    <div id="chart_div"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {packages:["orgchart"]});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Name');
        data.addColumn('string', 'Manager');
        data.addColumn('string', 'ToolTip');

        // รับข้อมูล JSON ที่ส่งมาจาก Controller
        var chartData = @json($chartData);
        
        // แมปข้อมูลใส่ตาราง
        var rows = chartData.map(function(item) {
            return [
                { v: item[0].v, f: item[0].f }, // ID และ HTML
                item[1], // Manager ID
                item[2]  // Tooltip
            ];
        });

        data.addRows(rows);

        // สร้าง Chart
        var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
        
        // สั่งวาดแผนผัง พร้อมเปิดโหมดใช้ HTML
        chart.draw(data, {
            allowHtml: true,
            size: 'medium',
            nodeClass: 'org-node',
            selectedNodeClass: 'org-node-selected'
        });
    }
</script>

<style>
    .org-node {
        background-color: #ffffff !important;
        border: 2px solid #0d6efd !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;
        padding: 10px 20px !important;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    .org-node:hover {
        transform: scale(1.05);
    }
    .org-node-selected {
        background-color: #e9ecef !important;
        border: 2px solid #198754 !important;
    }
    /* เปลี่ยนสีเส้นเชื่อม */
    .google-visualization-orgchart-lineleft, 
    .google-visualization-orgchart-lineright, 
    .google-visualization-orgchart-linebottom {
        border-color: #adb5bd !important;
        border-width: 2px !important;
    }
</style>
@endsection