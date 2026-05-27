@extends('layouts.ats')

@section('title', '候補者')

@section('content')
    <h1 class="ats-page-title">候補者一覧</h1>
    <p class="ats-page-desc">シードデータの候補者プロフィールです。</p>
    <table class="ats-table">
        <thead>
        <tr>
            <th>名前</th>
            <th>現職企業</th>
            <th>職種</th>
            <th>スキル</th>
            <th>ステータス</th>
        </tr>
        </thead>
        <tbody>
        @foreach($candidates as $c)
            <tr>
                <td><a href="{{ route('ats.candidates.show', $c) }}">{{ $c->name }}</a></td>
                <td>{{ $c->current_company }}</td>
                <td>{{ $c->current_position }}</td>
                <td>{{ implode('、', $c->skills ?? []) }}</td>
                <td><span class="pill">{{ $c->status }}</span></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
