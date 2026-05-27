@extends('layouts.ats')

@section('title', 'ダッシュボード')

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

    <h1 class="ats-page-title">ダッシュボード</h1>
    <p class="ats-page-desc">候補者・求人・選考の一覧指標と、ステータス別・選考フェーズ別の分布を確認できます。</p>

    <div class="ats-cards">
        <div class="ats-card">
            <div class="ats-kpi-head">
                <div class="label">候補者</div>
                <span class="ats-card-icon" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </span>
            </div>
            <div class="num">{{ $candidateCount }}</div>
        </div>
        <div class="ats-card">
            <div class="ats-kpi-head">
                <div class="label">求人</div>
                <span class="ats-card-icon" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </span>
            </div>
            <div class="num">{{ $jobCount }}</div>
        </div>
        <div class="ats-card">
            <div class="ats-kpi-head">
                <div class="label">応募・選考</div>
                <span class="ats-card-icon" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M16 8V5H2v14h14v-3"/><path d="M8 12h8"/><path d="M14 10l4 2-4 2"/></svg>
                </span>
            </div>
            <div class="num">{{ $applicationCount }}</div>
        </div>
    </div>

    <div class="ats-chart-grid">
        <div class="ats-panel">
            <h2 class="ats-panel-title">候補者ステータス</h2>
            <div class="ats-chart-wrap">
                <canvas id="chartCandidateStatus" aria-label="候補者ステータス分布"></canvas>
            </div>
        </div>
        <div class="ats-panel">
            <h2 class="ats-panel-title">選考フェーズ（応募単位）</h2>
            <div class="ats-chart-wrap">
                <canvas id="chartStages" aria-label="選考フェーズ件数"></canvas>
            </div>
        </div>
    </div>

    <div class="ats-panel">
        <h2 class="ats-panel-title">選考ファネル（スナップショット）</h2>
        <p class="ats-note" style="margin-top:-0.5rem;margin-bottom:1rem;">各フェーズに現在いる応募数の相対比です（見送りは別グラフ参照）。</p>
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

    <p class="ats-note">MCP 経由の操作は Service 層と監査ログを通します（DB 直操作なし）。</p>

    @push('scripts')
    <script>
        (function () {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const tickColor = prefersDark ? '#94a3b8' : '#64748b';
            const gridColor = prefersDark ? 'rgba(148,163,184,0.12)' : 'rgba(15,23,42,0.06)';

            const statusLabels = @json(array_values($candidateStatusLabels));
            const statusCounts = @json(array_values($candidateStatusCounts));
            const statusColors = ['#0ea5e9', '#0284c7', '#0369a1', '#d97706', '#64748b', '#059669'];

            const ctx1 = document.getElementById('chartCandidateStatus');
            if (ctx1 && typeof Chart !== 'undefined') {
                new Chart(ctx1, {
                    type: 'doughnut',
                    data: {
                        labels: statusLabels,
                        datasets: [{
                            data: statusCounts,
                            backgroundColor: statusColors,
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: tickColor, font: { size: 11 } },
                            },
                        },
                    },
                });
            }

            const stageLabels = @json($stageChartLabels);
            const stageCounts = @json($stageChartSeries);
            const ctx2 = document.getElementById('chartStages');
            if (ctx2 && typeof Chart !== 'undefined') {
                new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels: stageLabels,
                        datasets: [{
                            label: '件数',
                            data: stageCounts,
                            backgroundColor: [
                                'rgba(12, 74, 110, 0.85)',
                                'rgba(3, 105, 161, 0.85)',
                                'rgba(2, 132, 199, 0.85)',
                                'rgba(217, 119, 6, 0.85)',
                                'rgba(100, 116, 139, 0.85)',
                                'rgba(5, 150, 105, 0.85)',
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
