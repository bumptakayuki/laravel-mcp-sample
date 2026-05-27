@extends('layouts.ats')

@section('title', 'パイプライン')

@section('content')
    @php
        $funnelOrder = ['applied', 'screening', 'interview', 'offer', 'hired'];
        $funnelJa = [
            'applied' => '応募',
            'screening' => '書類選考',
            'interview' => '面接',
            'offer' => '内定',
            'hired' => '採用',
        ];
        $funnelColors = [
            'applied' => 'linear-gradient(90deg, #0c4a6e, #075985)',
            'screening' => 'linear-gradient(90deg, #0369a1, #0284c7)',
            'interview' => 'linear-gradient(90deg, #0284c7, #0ea5e9)',
            'offer' => 'linear-gradient(90deg, #b45309, #d97706)',
            'hired' => 'linear-gradient(90deg, #047857, #10b981)',
        ];
        $funnelMax = max(array_map(fn ($k) => $stageTotals[$k] ?? 0, $funnelOrder)) ?: 1;
        $stageChartLabels = ['応募', '書類', '面接', '内定', '見送り', '採用'];
        $stageChartSeries = [
            $stageTotals['applied'],
            $stageTotals['screening'],
            $stageTotals['interview'],
            $stageTotals['offer'],
            $stageTotals['rejected'],
            $stageTotals['hired'],
        ];
    @endphp

    <h1 class="ats-page-title">求人別パイプライン</h1>
    <p class="ats-page-desc">求人ごとの選考ステージ件数と、全体のファネル概要です。</p>

    <div class="ats-grid-2">
        <div class="ats-panel">
            <h2 class="ats-panel-title">全体ファネル（応募の現在フェーズ）</h2>
            <div class="ats-funnel">
                @foreach($funnelOrder as $stage)
                    @php
                        $n = (int) ($stageTotals[$stage] ?? 0);
                        $pct = round(($n / $funnelMax) * 100);
                    @endphp
                    <div class="ats-funnel-row">
                        <span class="ats-funnel-label">{{ $funnelJa[$stage] }}</span>
                        <div class="ats-funnel-track">
                            <div class="ats-funnel-bar" style="width: {{ max($pct, $n > 0 ? 12 : 0) }}%; background: {{ $funnelColors[$stage] }};">{{ $n }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="ats-panel">
            <h2 class="ats-panel-title">フェーズ別件数（全求人）</h2>
            <div class="ats-chart-wrap">
                <canvas id="chartPipelineStages" aria-label="フェーズ別件数"></canvas>
            </div>
        </div>
    </div>

    <div class="ats-panel" style="margin-bottom:0;">
        <h2 class="ats-panel-title">求人別内訳</h2>
        <div style="overflow-x:auto;">
            <table class="ats-table">
                <thead>
                <tr>
                    <th>求人名</th>
                    <th>applied</th>
                    <th>screening</th>
                    <th>interview</th>
                    <th>offer</th>
                    <th>rejected</th>
                    <th>hired</th>
                </tr>
                </thead>
                <tbody>
                @foreach($summaries as $row)
                    <tr>
                        <td>{{ $row['job_title'] }}</td>
                        @foreach(['applied','screening','interview','offer','rejected','hired'] as $stage)
                            <td>{{ $row['stages'][$stage] ?? 0 }}</td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const tickColor = prefersDark ? '#94a3b8' : '#64748b';
            const gridColor = prefersDark ? 'rgba(148,163,184,0.12)' : 'rgba(15,23,42,0.06)';
            const ctx = document.getElementById('chartPipelineStages');
            if (ctx && typeof Chart !== 'undefined') {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($stageChartLabels),
                        datasets: [{
                            label: '件数',
                            data: @json($stageChartSeries),
                            backgroundColor: [
                                'rgba(12, 74, 110, 0.88)',
                                'rgba(3, 105, 161, 0.88)',
                                'rgba(2, 132, 199, 0.88)',
                                'rgba(217, 119, 6, 0.88)',
                                'rgba(100, 116, 139, 0.88)',
                                'rgba(5, 150, 105, 0.88)',
                            ],
                            borderRadius: 8,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: {
                                ticks: { color: tickColor, maxRotation: 45 },
                                grid: { display: false },
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { color: tickColor, stepSize: 1 },
                                grid: { color: gridColor },
                            },
                        },
                    },
                });
            }
        })();
    </script>
    @endpush
@endsection
