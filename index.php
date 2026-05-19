<?php
    $DomainPostFix = 'test';
    if (! empty($_GET['q'])) {
    switch ($_GET['q']) {
        case 'info':
            phpinfo();
            exit;
            break;

        case 'open':
            $baseDir = dirname(__FILE__);

            // اگر dir خالی بود، روت باز شود
            if (empty($_GET['dir'])) {
                $fullPath = $baseDir;
            } else {
                $requestedDir = $_GET['dir'];

                // Security: only allow simple folder names (no path traversal)
                if (! preg_match('/^[a-zA-Z0-9_\-\.]+$/', $requestedDir)) {
                    header('Content-Type: application/json');
                    http_response_code(400);
                    echo json_encode(['ok' => false, 'error' => 'Invalid directory name']);
                    exit;
                }

                $fullPath = $baseDir . DIRECTORY_SEPARATOR . $requestedDir;

                if (! is_dir($fullPath)) {
                    header('Content-Type: application/json');
                    http_response_code(404);
                    echo json_encode(['ok' => false, 'error' => 'Directory not found']);
                    exit;
                }
            }

            // Open in Windows Explorer
            $escapedPath = str_replace('/', '\\', $fullPath);
            pclose(popen('start "" explorer.exe "' . $escapedPath . '"', 'r'));

            header('Content-Type: application/json');
            echo json_encode(['ok' => true]);
            exit;
            break;

    }
    }

    function stringToColor($string)
    {
    $colors = [
        '#3B82F6', '#8B5CF6', '#EC4899', '#10B981', '#F59E0B',
        '#EF4444', '#06B6D4', '#84CC16', '#F97316', '#6366F1',
    ];
    return $colors[abs(crc32($string)) % count($colors)];
    }

    $Directories = glob(dirname(__FILE__) . '/*', GLOB_ONLYDIR);
    $projects    = [];
    foreach ($Directories as $value) {
    if (basename($value) != 'bootstrap-5.3.1') {
        $projects[] = basename($value);
    }
    }
    natcasesort($projects);
    $projectCount = count($projects);

    // Get MySQL version if available
    $mysqlVersion = 'N/A';
    if (function_exists('mysqli_connect')) {
    $mysqlVersion = 'Available';
    }
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laragon — Dev Environment</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='8' fill='%237C6EFA'/><text x='50%25' y='50%25' dominant-baseline='central' text-anchor='middle' font-family='monospace' font-weight='700' font-size='18' fill='white'>L</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&family=Fira+Code:wght@300..700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0A0A0F;
            --surface: #111118;
            --surface2: #16161F;
            --border: rgba(255,255,255,0.07);
            --border-hover: rgba(255,255,255,0.15);
            --text: #E8E8F0;
            --text-muted: #6B6B80;
            --text-dim: #4A4A5A;
            --accent: #7C6EFA;
            --accent2: #5BC8AF;
            --accent3: #F2A65A;
            --glow: rgba(124,110,250,0.15);
            --radius: 12px;
            --radius-sm: 8px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Vazirmatn', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ─── Grid Background ─── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(124,110,250,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(124,110,250,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
            z-index: 0;
        }

        /* Ambient glow */
        body::after {
            content: '';
            position: fixed;
            top: -200px;
            left: 50%;
            transform: translateX(-50%);
            width: 800px;
            height: 400px;
            background: radial-gradient(ellipse, rgba(124,110,250,0.12) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        /* ─── Navbar ─── */
        nav {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(10,10,15,0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--text);
            font-weight: 600;
            font-size: 15px;
            letter-spacing: -0.3px;
        }

        .nav-brand .logo-badge {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, var(--accent), #A78BFA);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: white;
            font-family: 'Fira Code', monospace;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-pill {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 20px;
            font-size: 13px;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.2s;
            font-family: 'Fira Code', monospace;
        }
        .nav-pill:last-child{
            font-family:'Vazirmatn';
        }

        .nav-pill:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: rgba(124,110,250,0.08);
        }

        .nav-pill .dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--accent2);
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }

        /* ─── Main Layout ─── */
        .wrapper {
            position: relative;
            z-index: 1;
            max-width: 1400px;
            margin: 0 auto;
            padding: 32px 24px 64px;
        }

        /* ─── Header ─── */
        .page-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 32px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .page-title-block h1 {
            font-size: clamp(22px, 3vw, 28px);
            font-weight: 700;
            letter-spacing: -0.5px;
            color: var(--text);
            line-height: 1.2;
        }

        .page-title-block p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* ─── Search ─── */
        .search-wrap {
            position: relative;
        }

        .search-wrap svg {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-dim);
            pointer-events: none;
        }

        #search {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text);
            font-family: 'Vazirmatn', sans-serif;
            font-size: 13px;
            padding: 9px 40px 9px 16px;
            width: 240px;
            outline: none;
            transition: all 0.2s;
            direction: rtl;
        }

        #search::placeholder { color: var(--text-dim); }

        #search:focus {
            border-color: var(--accent);
            background: rgba(124,110,250,0.05);
            box-shadow: 0 0 0 3px rgba(124,110,250,0.1);
            width: 280px;
        }

        /* ─── Stats Row ─── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: border-color 0.2s;
        }

        .stat-card:hover {
            border-color: var(--border-hover);
        }

        .stat-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 16px;
        }

        .stat-icon.php { background: rgba(124,110,250,0.12); }
        .stat-icon.projects { background: rgba(91,200,175,0.12); }
        .stat-icon.server { background: rgba(242,166,90,0.12); }
        .stat-icon.mysql { background: rgba(59,130,246,0.12); }

        .stat-info { min-width: 0; }
        .stat-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--text);
            font-family: 'Fira Code', monospace;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-label {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* ─── Toolbar ─── */
        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            gap: 12px;
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .count-badge {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2px 10px;
            font-size: 11px;
            font-family: 'Fira Code', monospace;
            color: var(--text-muted);
        }

        .view-btns {
            display: flex;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 3px;
            gap: 2px;
        }

        .view-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px 8px;
            border-radius: 5px;
            color: var(--text-dim);
            transition: all 0.15s;
            display: flex;
            align-items: center;
        }

        .view-btn.active, .view-btn:hover {
            background: var(--surface);
            color: var(--text);
        }

        /* ─── Project Grid ─── */
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 10px;
        }

        .projects-grid.list-view {
            grid-template-columns: 1fr;
        }

        .project-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            min-width: 0;
        }

        .project-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 3px;
            height: 100%;
            opacity: 0;
            transition: opacity 0.2s;
            border-radius: 0 0 0 0;
        }

        .project-card:hover {
            border-color: var(--border-hover);
            background: var(--surface2);
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        }

        .project-card:hover::before { opacity: 1; }

        .project-avatar {
            width: 36px; height: 36px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            color: white;
            flex-shrink: 0;
            font-family: 'Fira Code', monospace;
        }

        .project-info {
            min-width: 0;
            flex: 1;
        }

        .project-name {
            font-size: 15px;
            font-weight: 500;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing:1px;
        }

        .project-url {
            font-size: 11px;
            color: var(--text-dim);
            font-family: 'Fira Code', monospace;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-top: 2px;
        }

        .project-arrow {
            color: var(--text-dim);
            opacity: 0;
            transition: opacity 0.2s, transform 0.2s;
            flex-shrink: 0;
        }

        .project-card:hover .project-arrow {
            opacity: 1;
            transform: translateX(-3px);
        }

        /* ─── Folder Button ─── */
        .folder-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            border-radius: 6px;
            color: var(--text-dim);
            opacity: 0;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .project-card:hover .folder-btn {
            opacity: 1;
        }

        .folder-btn:hover {
            background: rgba(242,166,90,0.15);
            color: #F2A65A;
        }

        .folder-btn.success {
            color: var(--accent2);
            animation: pop 0.3s ease;
        }

        @keyframes pop {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }

        /* List view adjustments */
        .list-view .project-card {
            padding: 12px 16px;
        }

        /* ─── Tools Section ─── */
        .tools-section {
            margin-top: 28px;
        }

        .tools-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 10px;
            margin-top: 16px;
        }

        .tool-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 13px;
            transition: all 0.2s;
        }

        .tool-card:hover {
            border-color: var(--accent);
            color: var(--text);
            background: rgba(124,110,250,0.05);
        }

        .tool-icon {
            width: 30px; height: 30px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        /* ─── Info Footer ─── */
        .info-footer {
            margin-top: 28px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .info-item strong {
            color: var(--text);
            font-family: 'Fira Code', monospace;
            font-size: 11px;
            font-weight: 500;
        }

        .info-item a {
            color: var(--accent);
            text-decoration: none;
            font-size: 11px;
        }

        .info-item a:hover { text-decoration: underline; }

        /* ─── Empty State ─── */
        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: var(--text-muted);
            display: none;
        }

        .empty-state svg { opacity: 0.3; margin-bottom: 12px; }
        .empty-state p { font-size: 13px; }

        /* ─── Responsive ─── */
        @media (max-width: 640px) {
            .wrapper { padding: 20px 16px 48px; }
            .page-header { flex-direction: column; align-items: flex-start; }
            #search, #search:focus { width: 100%; }
            .search-wrap { width: 100%; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
        }

        /* ─── Scrollbar ─── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: var(--surface2); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--border-hover); }
    </style>
</head>
<body>

<!-- Navbar -->
<nav>
    <a class="nav-brand" href="https://localhost">
        <div class="logo-badge">L</div>
        Laragon
    </a>
    <div class="nav-right">
        <a href="https://localhost/phpmyadmin/" class="nav-pill">
            <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor"><path d="M4.318 2.687C5.234 2.271 6.536 2 8 2s2.766.27 3.682.687C12.644 3.125 13 3.627 13 4c0 .374-.356.875-1.318 1.313C10.766 5.729 9.464 6 8 6s-2.766-.27-3.682-.687C3.356 4.875 3 4.373 3 4c0-.374.356-.875 1.318-1.313M13 5.698V7c0 .374-.356.875-1.318 1.313C10.766 8.729 9.464 9 8 9s-2.766-.27-3.682-.687C3.356 7.875 3 7.373 3 7V5.698c.271.202.58.378.904.525C4.978 6.711 6.427 7 8 7s3.022-.289 4.096-.777A5 5 0 0 0 13 5.698M14 4c0-1.007-.875-1.755-1.904-2.223C11.022 1.289 9.573 1 8 1s-3.022.289-4.096.777C2.875 2.245 2 2.993 2 4v9c0 1.007.875 1.755 1.904 2.223C4.978 15.71 6.427 16 8 16s3.022-.289 4.096-.777C13.125 14.755 14 14.007 14 13z"/></svg>
            phpMyAdmin
        </a>
        <a href="/?q=info" class="nav-pill">
            <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/></svg>
            PHP Info
        </a>
        <span class="nav-pill">
            <span class="dot"></span>
            سرور فعال
        </span>
    </div>
</nav>

<!-- Main -->
<div class="wrapper">

    <!-- Header -->
    <div class="page-header">
        <div class="page-title-block">
            <h1>محیط توسعه محلی</h1>
            <p>مدیریت پروژه‌ها و ابزارها</p>
        </div>
        <div class="search-wrap">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor">
                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
            </svg>
            <input type="text" id="search" placeholder="جستجوی پروژه..." autocomplete="off" tabindex="1">
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon php">
                <svg width="18" height="18" viewBox="0 0 16 16" fill="#7C6EFA"><path d="M10.478 1.647a.5.5 0 1 0-.956-.294l-4 13a.5.5 0 0 0 .956.294zM4.854 4.146a.5.5 0 0 1 0 .708L1.707 8l3.147 3.146a.5.5 0 0 1-.708.708l-3.5-3.5a.5.5 0 0 1 0-.708l3.5-3.5a.5.5 0 0 1 .708 0m6.292 0a.5.5 0 0 0 0 .708L14.293 8l-3.147 3.146a.5.5 0 0 0 .708.708l3.5-3.5a.5.5 0 0 0 0-.708l-3.5-3.5a.5.5 0 0 0-.708 0"/></svg>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?php echo phpversion() ?></div>
                <div class="stat-label">نسخه PHP</div>
            </div>
        </div>

        <div class="stat-card"
                data-dir=""
                title="باز کردن پوشه در Explorer"
                onclick="event.preventDefault(); event.stopPropagation(); openFolder(this);">
            <div class="stat-icon projects">
                <svg width="18" height="18" viewBox="0 0 16 16" fill="#5BC8AF"><path d="M1 3.5A1.5 1.5 0 0 1 2.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9 4h4.5A1.5 1.5 0 0 1 15 5.5v7a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 12.5z"/></svg>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?php echo $projectCount ?></div>
                <div class="stat-label">پروژه‌ها</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon server">
                <svg width="18" height="18" viewBox="0 0 16 16" fill="#F2A65A"><path d="M1.333 2.667C1.333 1.194 4.318 0 8 0s6.667 1.194 6.667 2.667V4c0 1.473-2.985 2.667-6.667 2.667S1.333 5.473 1.333 4zm0 3.667v3C1.333 10.805 4.318 12 8 12s6.667-1.194 6.667-2.667V6.334a6.5 6.5 0 0 1-1.458.79C11.81 7.684 9.967 8 8 8s-3.809-.317-5.208-.876a6.5 6.5 0 0 1-1.458-.79zm0 5v1.666C1.333 14.806 4.318 16 8 16s6.667-1.194 6.667-2.667v-1.666a6.5 6.5 0 0 1-1.458.789c-1.4.56-3.242.876-5.21.876-1.966 0-3.809-.316-5.208-.876a6.5 6.5 0 0 1-1.458-.79z"/></svg>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="font-size:13px;"><?php echo isset($_SERVER['SERVER_SOFTWARE']) ? explode(' ', $_SERVER['SERVER_SOFTWARE'])[0] : 'Apache' ?></div>
                <div class="stat-label">وب سرور</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon mysql">
                <svg width="18" height="18" viewBox="0 0 16 16" fill="#3B82F6"><path d="M4.318 2.687C5.234 2.271 6.536 2 8 2s2.766.27 3.682.687C12.644 3.125 13 3.627 13 4c0 .374-.356.875-1.318 1.313C10.766 5.729 9.464 6 8 6s-2.766-.27-3.682-.687C3.356 4.875 3 4.373 3 4c0-.374.356-.875 1.318-1.313M13 5.698V7c0 .374-.356.875-1.318 1.313C10.766 8.729 9.464 9 8 9s-2.766-.27-3.682-.687C3.356 7.875 3 7.373 3 7V5.698c.271.202.58.378.904.525C4.978 6.711 6.427 7 8 7s3.022-.289 4.096-.777A5 5 0 0 0 13 5.698m0 3V10c0 .374-.356.875-1.318 1.313C10.766 11.729 9.464 12 8 12s-2.766-.27-3.682-.687C3.356 10.875 3 10.373 3 10V8.698c.271.202.58.378.904.525C4.978 9.71 6.427 10 8 10s3.022-.289 4.096-.777c.324-.147.633-.323.904-.525m0 3v1.302C13 13.806 10.015 15 8 15s-5-1.194-5-2.667v-1.302c.271.202.58.378.904.525C4.978 12.11 6.427 12.5 8 12.5s3.022-.39 4.096-.944c.324-.147.633-.323.904-.556z"/></svg>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?php echo defined('PHP_INT_SIZE') && PHP_INT_SIZE === 8 ? '64-bit' : '32-bit' ?></div>
                <div class="stat-label">معماری PHP</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(99,102,241,0.12);">
                <svg width="18" height="18" viewBox="0 0 16 16" fill="#6366F1"><path d="M5 3.5h6A1.5 1.5 0 0 1 12.5 5v6a1.5 1.5 0 0 1-1.5 1.5H5A1.5 1.5 0 0 1 3.5 11V5A1.5 1.5 0 0 1 5 3.5"/></svg>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?php echo ini_get('memory_limit') ?></div>
                <div class="stat-label">حافظه PHP</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(236,72,153,0.12);">
                <svg width="18" height="18" viewBox="0 0 16 16" fill="#EC4899"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/></svg>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?php echo ini_get('max_execution_time') ?>s</div>
                <div class="stat-label">Max Execution</div>
            </div>
        </div>
    </div>

    <!-- Projects -->
    <div class="toolbar">
        <div class="toolbar-left">
            <span class="section-title">پروژه‌ها</span>
            <span class="count-badge" id="count-display"><?php echo $projectCount ?></span>
        </div>
        <div class="view-btns">
            <button class="view-btn active" id="grid-btn" title="نمای شبکه">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5zm8 0A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3A1.5 1.5 0 0 1 13.5 15h-3A1.5 1.5 0 0 1 9 13.5z"/></svg>
            </button>
            <button class="view-btn" id="list-btn" title="نمای لیست">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/></svg>
            </button>
        </div>
    </div>

    <div class="projects-grid" id="projects-grid">
        <?php foreach ($projects as $Dir):
                $firstChar = strtoupper(substr($Dir, 0, 1));
                $color     = stringToColor(strtolower($firstChar));
        ?>
        <a href="https://<?php echo htmlspecialchars($Dir) ?>.<?php echo $DomainPostFix ?>"
           class="project-card"
           data-name="<?php echo strtolower(htmlspecialchars($Dir)) ?>"
           style="--card-color: <?php echo $color ?>">
            <div class="project-avatar" style="background: <?php echo $color ?>20; color: <?php echo $color ?>; border: 1px solid <?php echo $color ?>30;">
                <?php echo $firstChar ?>
            </div>
            <div class="project-info">
                <div class="project-name"><?php echo htmlspecialchars($Dir) ?></div>
                <div class="project-url"><?php echo htmlspecialchars($Dir) ?>.<?php echo $DomainPostFix ?></div>
            </div>

            <button class="folder-btn"
                    data-dir="<?php echo htmlspecialchars($Dir) ?>"
                    title="باز کردن پوشه در Explorer"
                    onclick="event.preventDefault(); event.stopPropagation(); openFolder(this);">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M1 3.5A1.5 1.5 0 0 1 2.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9 4h4.5A1.5 1.5 0 0 1 15 5.5v7a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 12.5z"/>
                </svg>
            </button>
            <div style="position:absolute;top:0;right:0;width:3px;height:100%;background:<?php echo $color ?>;opacity:0;transition:opacity 0.2s;" class="card-indicator"></div>
        </a>
        <?php endforeach; ?>
    </div>

    <div class="empty-state" id="empty-state">
        <svg width="40" height="40" viewBox="0 0 16 16" fill="currentColor"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/></svg>
        <p>پروژه‌ای یافت نشد</p>
    </div>

    <!-- Tools -->
    <div class="tools-section">
        <div class="toolbar-left" style="margin-bottom:16px;">
            <span class="section-title">ابزارها</span>
        </div>
        <div class="tools-grid">
            <a href="https://localhost/phpmyadmin/" class="tool-card">
                <div class="tool-icon" style="background:rgba(59,130,246,0.12);">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="#3B82F6"><path d="M4.318 2.687C5.234 2.271 6.536 2 8 2s2.766.27 3.682.687C12.644 3.125 13 3.627 13 4c0 .374-.356.875-1.318 1.313C10.766 5.729 9.464 6 8 6s-2.766-.27-3.682-.687C3.356 4.875 3 4.373 3 4c0-.374.356-.875 1.318-1.313M13 5.698V7c0 .374-.356.875-1.318 1.313C10.766 8.729 9.464 9 8 9s-2.766-.27-3.682-.687C3.356 7.875 3 7.373 3 7V5.698c.271.202.58.378.904.525C4.978 6.711 6.427 7 8 7s3.022-.289 4.096-.777A5 5 0 0 0 13 5.698m0 3V10c0 .374-.356.875-1.318 1.313C10.766 11.729 9.464 12 8 12s-2.766-.27-3.682-.687C3.356 10.875 3 10.373 3 10V8.698c.271.202.58.378.904.525C4.978 9.71 6.427 10 8 10s3.022-.289 4.096-.777c.324-.147.633-.323.904-.525m0 3v1.302C13 13.806 10.015 15 8 15s-5-1.194-5-2.667v-1.302c.271.202.58.378.904.525C4.978 12.11 6.427 12.5 8 12.5s3.022-.39 4.096-.944c.324-.147.633-.323.904-.556z"/></svg>
                </div>
                phpMyAdmin
            </a>
            <a href="/?q=info" class="tool-card">
                <div class="tool-icon" style="background:rgba(124,110,250,0.12);">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="#7C6EFA"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/></svg>
                </div>
                PHP Info
            </a>
            <a href="https://localhost/redis/?overview" class="tool-card">
                <div class="tool-icon" style="background:rgba(239,68,68,0.12);">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="#EF4444"><path d="M14 10a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-1a1 1 0 0 1 1-1zM2 9a2 2 0 0 0-2 2v1a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-1a2 2 0 0 0-2-2z"/><path d="M14 3a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zM2 2a2 2 0 0 0-2 2v1a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z"/></svg>
                </div>
                Redis WA
            </a>
            <a href="https://localhost/memcached/" class="tool-card">
                <div class="tool-icon" style="background:rgba(91,200,175,0.12);">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="#5BC8AF"><path d="M1.333 2.667C1.333 1.194 4.318 0 8 0s6.667 1.194 6.667 2.667V4c0 1.473-2.985 2.667-6.667 2.667S1.333 5.473 1.333 4z"/><path d="M1.333 6.334v3C1.333 10.805 4.318 12 8 12s6.667-1.194 6.667-2.667V6.334a6.5 6.5 0 0 1-1.458.79C11.81 7.684 9.967 8 8 8s-3.809-.317-5.208-.876a6.5 6.5 0 0 1-1.458-.79z"/></svg>
                </div>
                Memcached
            </a>
            <a href="https://localhost:8025/" class="tool-card">
                <div class="tool-icon" style="background:rgba(242,166,90,0.12);">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="#F2A65A"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/></svg>
                </div>
                MailPit
            </a>
            <a href="https://localhost/adminer/" class="tool-card">
                <div class="tool-icon" style="background:rgba(99,102,241,0.12);">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="#6366F1"><path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm15 2h-4v3h4zm0 4h-4v3h4zm0 4h-4v3h3a1 1 0 0 0 1-1zm-5 3v-3H6v3zm-4 0v-3H2v2a1 1 0 0 0 1 1zm-4-4h4V8H2zm0-4h4V4H2zm5-3v3h4V4zm4 4H7v3h4z"/></svg>
                </div>
                Adminer
            </a>
            <a href="#" class="tool-card"
                    data-dir=""
                    title="باز کردن پوشه در Explorer"
                    onclick="event.preventDefault(); event.stopPropagation(); openFolder(this);">
                <div class="tool-icon" style="background:rgba(99,102,241,0.12);">
                    <svg width="18" height="18" viewBox="0 0 16 16" fill="#5BC8AF"><path d="M1 3.5A1.5 1.5 0 0 1 2.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9 4h4.5A1.5 1.5 0 0 1 15 5.5v7a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 12.5z"></path></svg>
                </div>
                WWW
            </a>
        </div>
    </div>

    <!-- Info Footer -->
    <div class="info-footer">
        <div class="info-item">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor"><path d="M1.333 2.667C1.333 1.194 4.318 0 8 0s6.667 1.194 6.667 2.667V4c0 1.473-2.985 2.667-6.667 2.667S1.333 5.473 1.333 4z"/><path d="M1.333 6.334v3C1.333 10.805 4.318 12 8 12s6.667-1.194 6.667-2.667V6.334a6.5 6.5 0 0 1-1.458.79C11.81 7.684 9.967 8 8 8s-3.809-.317-5.208-.876a6.5 6.5 0 0 1-1.458-.79z"/><path d="M14.667 11.668a6.5 6.5 0 0 1-1.458.789c-1.4.56-3.242.876-5.21.876-1.966 0-3.809-.316-5.208-.876a6.5 6.5 0 0 1-1.458-.79v1.666C1.333 14.806 4.318 16 8 16s6.667-1.194 6.667-2.667z"/></svg>
            Server: <strong><?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') ?></strong>
        </div>
        <div class="info-item">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor"><path d="M10.478 1.647a.5.5 0 1 0-.956-.294l-4 13a.5.5 0 0 0 .956.294z"/></svg>
            PHP: <strong><?php echo phpversion() ?></strong>
            <a href="/?q=info">phpinfo</a>
        </div>
        <div class="info-item">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor"><path d="M1 3.5A1.5 1.5 0 0 1 2.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9 4h4.5A1.5 1.5 0 0 1 15 5.5v7a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 12.5z"/></svg>
            Root: <strong style="font-size:10px;"><?php echo htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? '') ?></strong>
        </div>
        <div class="info-item">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/></svg>
            Upload: <strong><?php echo ini_get('upload_max_filesize') ?></strong>
        </div>
    </div>

</div>

<script>
    // Open folder in Explorer
    function openFolder(btn) {
        const dir = btn.dataset.dir;
        const innerhtml = btn.innerHTML;
        fetch(`/?q=open&dir=${encodeURIComponent(dir)}`)
            .then(r => r.json())
            .then(data => {
                if (data.ok) {
                    btn.classList.add('success');
                    btn.innerHTML = `<svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/></svg>`;
                    setTimeout(() => {
                        btn.classList.remove('success');
                        //btn.innerHTML = `<svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><path d="M1 3.5A1.5 1.5 0 0 1 2.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9 4h4.5A1.5 1.5 0 0 1 15 5.5v7a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 12.5z"/></svg>`;
                        btn.innerHTML = innerhtml;
                    }, 1500);
                }
            })
            .catch(() => {});
    }

    // Search
    const searchInput = document.getElementById('search');
    const cards = document.querySelectorAll('.project-card');
    const countDisplay = document.getElementById('count-display');
    const emptyState = document.getElementById('empty-state');
    const grid = document.getElementById('projects-grid');

    searchInput.addEventListener('input', function() {
        const val = this.value.toLowerCase().trim();
        let visible = 0;
        cards.forEach(card => {
            const name = card.dataset.name || '';
            const show = !val || name.includes(val);
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        countDisplay.textContent = visible;
        emptyState.style.display = visible === 0 ? 'block' : 'none';
    });

    // View toggle
    const gridBtn = document.getElementById('grid-btn');
    const listBtn = document.getElementById('list-btn');

    gridBtn.addEventListener('click', () => {
        grid.classList.remove('list-view');
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
    });

    listBtn.addEventListener('click', () => {
        grid.classList.add('list-view');
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
    });

    // Card indicator hover
    document.querySelectorAll('.project-card').forEach(card => {
        const indicator = card.querySelector('.card-indicator');
        card.addEventListener('mouseenter', () => { if(indicator) indicator.style.opacity = '1'; });
        card.addEventListener('mouseleave', () => { if(indicator) indicator.style.opacity = '0'; });
    });

    searchInput.focus();
</script>
</body>
</html>