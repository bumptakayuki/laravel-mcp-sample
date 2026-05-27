@extends('layouts.ats')

@section('title', $candidate->name)

@section('content')
    @php
        $stageJa = [
            'applied' => '応募',
            'screening' => '書類選考',
            'interview' => '面接',
            'offer' => '内定',
            'rejected' => '見送り',
            'hired' => '採用',
        ];
        $skills = $candidate->skills ?? [];
    @endphp

    <p class="ats-page-desc" style="margin-top:0;margin-bottom:0.75rem;">
        <a href="{{ route('ats.candidates') }}">候補者一覧</a>
        <span style="color: var(--ats-muted); margin: 0 0.35rem;">/</span>
        <span style="color: var(--ats-muted);">{{ $candidate->name }}</span>
    </p>
    <h1 class="ats-page-title">{{ $candidate->name }}</h1>
    <p class="ats-page-desc">プロフィールと応募中の求人です。</p>

    <div class="ats-grid-2">
        <div class="ats-panel">
            <h2 class="ats-panel-title">プロフィール</h2>
            <table class="ats-table" style="box-shadow:none;border-radius:10px;">
                <tbody>
                <tr>
                    <th style="width:38%;">メール</th>
                    <td>{{ $candidate->email }}</td>
                </tr>
                <tr>
                    <th>現職企業</th>
                    <td>{{ $candidate->current_company ?: '—' }}</td>
                </tr>
                <tr>
                    <th>職種</th>
                    <td>{{ $candidate->current_position ?: '—' }}</td>
                </tr>
                <tr>
                    <th>スキル</th>
                    <td>{{ count($skills) ? implode('、', $skills) : '—' }}</td>
                </tr>
                <tr>
                    <th>流入経路</th>
                    <td>{{ $candidate->source ?: '—' }}</td>
                </tr>
                <tr>
                    <th>ステータス</th>
                    <td><span class="pill">{{ $candidate->status }}</span></td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="ats-panel">
            <h2 class="ats-panel-title">応募・選考</h2>
            @if($candidate->applications->isEmpty())
                <p class="ats-note" style="margin:0;">この候補者に紐づく応募はまだありません。</p>
            @else
                <div style="overflow-x:auto;">
                    <table class="ats-table" style="box-shadow:none;border-radius:10px;">
                        <thead>
                        <tr>
                            <th>求人</th>
                            <th>部署</th>
                            <th>フェーズ</th>
                            <th>スコア</th>
                            <th>メモ</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($candidate->applications as $app)
                            <tr>
                                <td>{{ $app->job?->title ?? '—' }}</td>
                                <td>{{ $app->job?->department ?? '—' }}</td>
                                <td>
                                    <span class="pill">{{ $stageJa[$app->stage] ?? $app->stage }}</span>
                                </td>
                                <td>{{ $app->score !== null ? $app->score : '—' }}</td>
                                <td>{{ $app->memo ? str($app->memo)->limit(80) : '—' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
