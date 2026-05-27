@extends('layouts.ats')

@section('title', '求人')

@section('content')
    <h1 class="ats-page-title">求人一覧</h1>
    <p class="ats-page-desc">公開中の求人（テーブル <code style="font-size:0.85em;">job_postings</code>）です。</p>
    <table class="ats-table">
        <thead>
        <tr>
            <th>求人名</th>
            <th>部署</th>
            <th>必須スキル</th>
            <th>概要</th>
        </tr>
        </thead>
        <tbody>
        @foreach($jobs as $job)
            <tr>
                <td><a href="{{ route('ats.jobs.show', $job) }}">{{ $job->title }}</a></td>
                <td>{{ $job->department }}</td>
                <td>{{ implode('、', $job->required_skills ?? []) }}</td>
                <td>{{ \Illuminate\Support\Str::limit($job->description, 120) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
