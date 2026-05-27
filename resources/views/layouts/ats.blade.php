<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ATS Demo') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700&display=swap" rel="stylesheet" />
    {{-- DOM 末尾のインラインから Chart を参照するため defer なし --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --ats-sidebar: linear-gradient(165deg, #071528 0%, #0c2744 48%, #0a1f36 100%);
            --ats-sidebar-border: rgba(56, 189, 248, 0.12);
            --ats-nav-active: rgba(56, 189, 248, 0.18);
            --ats-nav-hover: rgba(255, 255, 255, 0.06);
            --ats-accent: #38bdf8;
            --ats-accent-dim: #0ea5e9;
            --ats-main-bg: linear-gradient(145deg, #e8eef6 0%, #f0f4fa 40%, #e2ebf5 100%);
            --ats-main-bg-dark: linear-gradient(145deg, #0b1220 0%, #111827 50%, #0f172a 100%);
            --ats-card: rgba(255, 255, 255, 0.85);
            --ats-card-dark: rgba(30, 41, 59, 0.92);
            --ats-text: #0f172a;
            --ats-text-dark: #e2e8f0;
            --ats-muted: #64748b;
            --ats-muted-dark: #94a3b8;
            --ats-border: rgba(15, 23, 42, 0.08);
            --ats-border-dark: rgba(148, 163, 184, 0.15);
            --ats-shadow: 0 4px 24px rgba(15, 23, 42, 0.06), 0 0 0 1px var(--ats-border);
            --ats-shadow-dark: 0 8px 32px rgba(0, 0, 0, 0.35), 0 0 0 1px var(--ats-border-dark);
            --ats-radius: 14px;
            font-family: 'DM Sans', system-ui, sans-serif;
        }
        @media (prefers-color-scheme: dark) {
            :root { color-scheme: dark; }
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            color: var(--ats-text);
            background: var(--ats-main-bg);
            line-height: 1.55;
            font-family: 'DM Sans', system-ui, sans-serif;
        }
        @media (prefers-color-scheme: dark) {
            body {
                color: var(--ats-text-dark);
                background: var(--ats-main-bg-dark);
            }
        }
        a { color: var(--ats-accent-dim); text-decoration: none; }
        a:hover { text-decoration: underline; }

        .ats-shell {
            display: flex;
            min-height: 100vh;
        }

        .ats-sidebar {
            width: 258px;
            flex-shrink: 0;
            background: var(--ats-sidebar);
            color: #e2e8f0;
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--ats-sidebar-border);
            box-shadow: 4px 0 32px rgba(0, 0, 0, 0.15);
        }
        .ats-brand {
            padding: 1.35rem 1.25rem 1.25rem;
            border-bottom: 1px solid var(--ats-sidebar-border);
        }
        .ats-brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #7dd3fc;
            margin-bottom: 0.35rem;
        }
        .ats-brand-badge svg { opacity: 0.9; }
        .ats-brand-title {
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.3;
            color: #f8fafc;
        }
        .ats-brand-sub {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 0.35rem;
        }

        .ats-nav {
            flex: 1;
            padding: 1rem 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }
        .ats-nav a {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.65rem 0.85rem;
            border-radius: 10px;
            color: #cbd5e1;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.15s ease, color 0.15s ease, transform 0.12s ease;
        }
        .ats-nav a svg {
            flex-shrink: 0;
            opacity: 0.85;
        }
        .ats-nav a:hover {
            background: var(--ats-nav-hover);
            color: #f1f5f9;
        }
        .ats-nav a.is-active {
            background: var(--ats-nav-active);
            color: #f0f9ff;
            box-shadow: inset 3px 0 0 var(--ats-accent);
        }
        .ats-sidebar-foot {
            padding: 1rem 1.25rem 1.25rem;
            border-top: 1px solid var(--ats-sidebar-border);
            font-size: 0.72rem;
            color: #64748b;
            line-height: 1.45;
        }

        .ats-main {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }
        .ats-main-inner {
            flex: 1;
            padding: clamp(1.25rem, 3vw, 2rem) clamp(1.25rem, 4vw, 2.5rem) 2.5rem;
            max-width: 1280px;
            width: 100%;
            margin: 0 auto;
        }

        .ats-page-title {
            font-size: clamp(1.35rem, 2.5vw, 1.65rem);
            font-weight: 700;
            letter-spacing: -0.03em;
            margin: 0 0 0.35rem;
        }
        .ats-page-desc {
            font-size: 0.9rem;
            color: var(--ats-muted);
            margin: 0 0 1.5rem;
            max-width: 52ch;
        }
        @media (prefers-color-scheme: dark) {
            .ats-page-desc { color: var(--ats-muted-dark); }
        }

        .ats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .ats-card {
            background: var(--ats-card);
            backdrop-filter: blur(12px);
            border-radius: var(--ats-radius);
            padding: 1.1rem 1.2rem;
            border: 1px solid var(--ats-border);
            box-shadow: var(--ats-shadow);
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }
        .ats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(15, 23, 42, 0.08), 0 0 0 1px var(--ats-border);
        }
        @media (prefers-color-scheme: dark) {
            .ats-card {
                background: var(--ats-card-dark);
                border-color: var(--ats-border-dark);
                box-shadow: var(--ats-shadow-dark);
            }
            .ats-card:hover {
                box-shadow: 0 16px 48px rgba(0, 0, 0, 0.45), 0 0 0 1px var(--ats-border-dark);
            }
        }
        .ats-card .label {
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--ats-muted);
        }
        @media (prefers-color-scheme: dark) {
            .ats-card .label { color: var(--ats-muted-dark); }
        }
        .ats-card .num {
            font-size: 1.85rem;
            font-weight: 700;
            margin-top: 0.35rem;
            letter-spacing: -0.03em;
            background: linear-gradient(120deg, #0284c7, #0ea5e9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        @media (prefers-color-scheme: dark) {
            .ats-card .num {
                background: linear-gradient(120deg, #38bdf8, #7dd3fc);
                -webkit-background-clip: text;
                background-clip: text;
            }
        }
        .ats-kpi-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 0.5rem;
        }
        .ats-card-icon {
            opacity: 0.35;
            flex-shrink: 0;
        }

        .ats-panel {
            background: var(--ats-card);
            backdrop-filter: blur(12px);
            border-radius: var(--ats-radius);
            padding: 1.25rem 1.35rem;
            border: 1px solid var(--ats-border);
            box-shadow: var(--ats-shadow);
            margin-bottom: 1.25rem;
        }
        @media (prefers-color-scheme: dark) {
            .ats-panel {
                background: var(--ats-card-dark);
                border-color: var(--ats-border-dark);
                box-shadow: var(--ats-shadow-dark);
            }
        }
        .ats-panel-title {
            font-size: 0.95rem;
            font-weight: 600;
            margin: 0 0 1rem;
            letter-spacing: -0.02em;
        }

        .ats-chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.25rem;
        }
        .ats-chart-wrap {
            position: relative;
            height: 260px;
        }

        .ats-funnel {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .ats-funnel-row {
            display: grid;
            grid-template-columns: 100px 1fr;
            align-items: center;
            gap: 0.75rem;
        }
        @media (max-width: 520px) {
            .ats-funnel-row { grid-template-columns: 1fr; gap: 0.25rem; }
        }
        .ats-funnel-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--ats-muted);
        }
        @media (prefers-color-scheme: dark) {
            .ats-funnel-label { color: var(--ats-muted-dark); }
        }
        .ats-funnel-track {
            height: 36px;
            border-radius: 8px;
            background: rgba(15, 23, 42, 0.06);
            overflow: hidden;
            position: relative;
        }
        @media (prefers-color-scheme: dark) {
            .ats-funnel-track { background: rgba(255, 255, 255, 0.06); }
        }
        .ats-funnel-bar {
            height: 100%;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 0.65rem;
            font-size: 0.8rem;
            font-weight: 700;
            color: #fff;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
            min-width: 2.5rem;
            transition: width 0.5s cubic-bezier(0.33, 1, 0.68, 1);
        }

        table.ats-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.88rem;
            border-radius: var(--ats-radius);
            overflow: hidden;
            border: 1px solid var(--ats-border);
            background: var(--ats-card);
            box-shadow: var(--ats-shadow);
        }
        @media (prefers-color-scheme: dark) {
            table.ats-table {
                border-color: var(--ats-border-dark);
                background: var(--ats-card-dark);
                box-shadow: var(--ats-shadow-dark);
            }
        }
        table.ats-table th, table.ats-table td {
            padding: 0.7rem 0.85rem;
            text-align: left;
            border-bottom: 1px solid var(--ats-border);
            vertical-align: top;
        }
        @media (prefers-color-scheme: dark) {
            table.ats-table th, table.ats-table td {
                border-bottom-color: var(--ats-border-dark);
            }
        }
        table.ats-table tr:last-child td { border-bottom: none; }
        table.ats-table th {
            background: rgba(14, 165, 233, 0.1);
            font-weight: 600;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--ats-muted);
        }
        @media (prefers-color-scheme: dark) {
            table.ats-table th {
                background: rgba(56, 189, 248, 0.12);
                color: var(--ats-muted-dark);
            }
        }

        .pill {
            display: inline-block;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 600;
            background: rgba(14, 165, 233, 0.15);
            color: #0284c7;
        }
        @media (prefers-color-scheme: dark) {
            .pill {
                background: rgba(56, 189, 248, 0.18);
                color: #7dd3fc;
            }
        }

        .ats-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }
        @media (max-width: 900px) {
            .ats-grid-2 { grid-template-columns: 1fr; }
        }

        .ats-note {
            font-size: 0.85rem;
            color: var(--ats-muted);
            margin-top: 0.5rem;
        }
        @media (prefers-color-scheme: dark) {
            .ats-note { color: var(--ats-muted-dark); }
        }

        @media (max-width: 720px) {
            .ats-shell { flex-direction: column; }
            .ats-sidebar {
                width: 100%;
                flex-direction: row;
                flex-wrap: wrap;
                align-items: center;
            }
            .ats-brand { border-bottom: none; flex: 1; min-width: 200px; }
            .ats-nav {
                flex-direction: row;
                flex-wrap: wrap;
                flex: 1 1 100%;
                padding-top: 0;
            }
            .ats-nav a { flex: 1 1 auto; justify-content: center; min-width: 120px; }
            .ats-sidebar-foot { display: none; }
        }
    </style>
    @stack('head')
</head>
<body>
<div class="ats-shell">
    <aside class="ats-sidebar" aria-label="メインナビゲーション">
        <div class="ats-brand">
            <div class="ats-brand-badge">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                </svg>
                ATS LT Demo
            </div>
            <div class="ats-brand-title">採用ダッシュボード</div>
            <div class="ats-brand-sub">Laravel + MCP サンプル</div>
        </div>
        <nav class="ats-nav">
            <a href="{{ route('ats.dashboard') }}" class="{{ request()->routeIs('ats.dashboard') ? 'is-active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                ダッシュボード
            </a>
            <a href="{{ route('ats.candidates') }}" class="{{ request()->routeIs('ats.candidates', 'ats.candidates.show') ? 'is-active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                候補者
            </a>
            <a href="{{ route('ats.jobs') }}" class="{{ request()->routeIs('ats.jobs', 'ats.jobs.show') ? 'is-active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                求人
            </a>
            <a href="{{ route('ats.pipeline') }}" class="{{ request()->routeIs('ats.pipeline') ? 'is-active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M7 16l4-4 4 4 6-6"/></svg>
                パイプライン
            </a>
        </nav>
        <footer class="ats-sidebar-foot">
            MCP は Service 層経由・監査ログ付き。デモ用 UI です。
        </footer>
    </aside>
    <div class="ats-main">
        <div class="ats-main-inner">
            @yield('content')
        </div>
    </div>
</div>
@stack('scripts')
</body>
</html>
