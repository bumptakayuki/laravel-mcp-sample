@extends('layouts.ats')

@section('title', $job->title)

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
        $skills = $job->required_skills ?? [];
    @endphp

    <p class="ats-page-desc" style="margin-top:0;margin-bottom:0.75rem;">
        <a href="{{ route('ats.jobs') }}">求人一覧</a>
        <span style="color: var(--ats-muted); margin: 0 0.35rem;">/</span>
        <span style="color: var(--ats-muted);">{{ $job->title }}</span>
    </p>
    <h1 class="ats-page-title">{{ $job->title }}</h1>
    <p class="ats-page-desc">求人票とこの求人への応募・選考状況です。</p>

    <div class="ats-grid-2">
        <div class="ats-panel">
            <h2 class="ats-panel-title">求人概要</h2>
            <table class="ats-table" style="box-shadow:none;border-radius:10px;">
                <tbody>
                <tr>
                    <th style="width:38%;">部署</th>
                    <td>{{ $job->department ?: '—' }}</td>
                </tr>
                <tr>
                    <th>必須スキル</th>
                    <td>{{ count($skills) ? implode('、', $skills) : '—' }}</td>
                </tr>
                <tr>
                    <th>職務内容</th>
                    <td>
                        @if($job->description)
                            <div style="white-space: pre-wrap;">{{ $job->description }}</div>
                        @else
                            —
                        @endif
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="ats-panel">
            <h2 class="ats-panel-title">応募・選考</h2>
            @if($job->applications->isEmpty())
                <p class="ats-note" style="margin:0;">この求人への応募はまだありません。</p>
            @else
                <div style="overflow-x:auto;">
                    <table class="ats-table" style="box-shadow:none;border-radius:10px;">
                        <thead>
                        <tr>
                            <th>候補者</th>
                            <th>フェーズ</th>
                            <th>スコア</th>
                            <th>メモ</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($job->applications as $app)
                            <tr>
                                <td>
                                    @if($app->candidate)
                                        <a href="{{ route('ats.candidates.show', $app->candidate) }}">{{ $app->candidate->name }}</a>
                                    @else
                                        —
                                    @endif
                                </td>
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
