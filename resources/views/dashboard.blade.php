<!DOCTYPE html>
<html lang="en">
<head>
<title>Stackly — Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* ── DESIGN TOKENS ── */
:root {
  --bg:     #02040f;
  --panel:  #070d1f;
  --raised: #0c1428;
  --border: rgba(99,160,255,0.1);
  --bordm:  rgba(99,160,255,0.22);
  --blue:   #3b82f6;
  --cyan:   #22d3ee;
  --green:  #4ade80;
  --red:    #f87171;
  --amber:  #fbbf24;
  --purple: #a78bfa;
  --t1: #e2eaff;
  --t2: #8ba0c8;
  --t3: #3a5070;
  --ui: 'Outfit', sans-serif;
  --mono: 'JetBrains Mono', monospace;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: var(--ui);
  color: var(--t1);
  background: var(--bg);
  min-height: 100vh;
  display: flex;
  overflow: hidden;
}

/* subtle ambient glow blobs */
body::before {
  content: '';
  position: fixed; inset: 0; z-index: 0; pointer-events: none;
  background:
    radial-gradient(ellipse 60% 50% at 15% 10%, rgba(59,130,246,0.12) 0%, transparent 55%),
    radial-gradient(ellipse 50% 40% at 85% 85%, rgba(34,211,238,0.08) 0%, transparent 50%);
}

/* faint grid */
body::after {
  content: '';
  position: fixed; inset: 0; z-index: 0; pointer-events: none;
  background-image:
    linear-gradient(rgba(99,160,255,0.025) 1px, transparent 1px),
    linear-gradient(90deg, rgba(99,160,255,0.025) 1px, transparent 1px);
  background-size: 55px 55px;
}

/* ══════════════════════════════════════
   SIDEBAR
   ══════════════════════════════════════ */
.sidebar {
  width: 230px;
  flex-shrink: 0;
  background: rgba(4,8,22,0.92);
  backdrop-filter: blur(24px);
  border-right: 1px solid var(--border);
  box-shadow: 4px 0 32px rgba(0,0,0,0.45);
  display: flex;
  flex-direction: column;
  padding: 22px 14px 18px;
  gap: 3px;
  position: relative;
  z-index: 10;
}

/* ── STACKLY logo ── */
.logo {
  display: flex;
  align-items: center;
  margin-bottom: 24px;
  padding: 0 4px;
  text-decoration: none;
}
.logo svg { height: 28px; width: auto; }

/* ── nav links ── */
.nav-item {
  display: flex;
  align-items: center;
  gap: 11px;
  padding: 10px 13px;
  border-radius: 9px;
  font-size: 13.5px;
  font-weight: 500;
  color: var(--t3);
  text-decoration: none;
  border: 1px solid transparent;
  position: relative;
  transition: background .15s, color .15s, border-color .15s;
  overflow: hidden;
}

/* animated left accent line */
.nav-item::before {
  content: '';
  position: absolute;
  left: 0; top: 50%;
  width: 3px; height: 0;
  background: var(--cyan);
  border-radius: 5px;
  transform: translateY(-50%);
  transition: height .2s;
}
.nav-item:hover { background: rgba(255,255,255,0.04); color: var(--t2); }
.nav-item:hover::before { height: 55%; }
.nav-item i { font-size: 14px; width: 16px; text-align: center; }

/* active state */
.nav-item.active {
  background: rgba(59,130,246,0.1);
  color: #60a5fa;
  border-color: rgba(59,130,246,0.18);
}
.nav-item.active::before { height: 65%; background: var(--blue); }

.sidebar-div { height: 1px; background: var(--border); margin: 8px 2px; }

/* logout button inside sidebar */
.logout-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  padding: 10px 13px;
  border-radius: 9px;
  font-family: var(--ui);
  font-size: 13.5px;
  font-weight: 500;
  color: var(--red);
  background: rgba(239,68,68,0.07);
  border: 1px solid rgba(239,68,68,0.15);
  cursor: pointer;
  transition: background .15s, border-color .15s;
  margin-top: 4px;
}
.logout-btn:hover { background: rgba(239,68,68,0.14); border-color: rgba(239,68,68,0.28); }

/* ══════════════════════════════════════
   MAIN CONTENT
   ══════════════════════════════════════ */
.main {
  flex: 1;
  overflow-y: auto;
  padding: 36px 40px;
  position: relative;
  z-index: 1;
}

/* page header row */
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 30px;
}
.page-title { font-size: 21px; font-weight: 700; color: var(--t1); letter-spacing: -0.3px; }
.page-title span { color: #60a5fa; }
.page-sub { font-size: 13px; color: var(--t3); margin-top: 3px; }

/* quick action button */
.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 22px;
  border-radius: 10px;
  font-family: var(--ui);
  font-size: 13.5px;
  font-weight: 600;
  color: #fff;
  background: linear-gradient(135deg, var(--blue), #6366f1);
  border: none;
  cursor: pointer;
  box-shadow: 0 0 20px rgba(59,130,246,0.3);
  transition: transform .15s, box-shadow .15s;
  text-decoration: none;
}
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 0 32px rgba(59,130,246,0.5); }

/* ── STAT CARDS GRID ── */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
  margin-bottom: 24px;
}

.stat-card {
  background: rgba(7,13,31,0.75);
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 20px 20px 18px;
  backdrop-filter: blur(14px);
  transition: transform .2s, border-color .2s, box-shadow .2s;
  position: relative;
  overflow: hidden;
}
/* top accent line — unique colour per card */
.stat-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 2px;
  border-radius: 14px 14px 0 0;
}
.stat-card.s-blue::before   { background: linear-gradient(90deg, var(--blue), #6366f1); }
.stat-card.s-green::before  { background: linear-gradient(90deg, var(--green), #0d9488); }
.stat-card.s-purple::before { background: linear-gradient(90deg, var(--purple), #ec4899); }
.stat-card.s-cyan::before   { background: linear-gradient(90deg, var(--cyan), var(--blue)); }

.stat-card:hover { transform: translateY(-4px); border-color: var(--bordm); box-shadow: 0 12px 36px rgba(0,0,0,0.35); }

.stat-label {
  font-size: 11px;
  font-weight: 600;
  letter-spacing: .09em;
  text-transform: uppercase;
  color: var(--t3);
  display: flex;
  align-items: center;
  gap: 7px;
  margin-bottom: 12px;
}
.stat-label i { font-size: 12px; }
.stat-card.s-blue   .stat-label i { color: var(--blue); }
.stat-card.s-green  .stat-label i { color: var(--green); }
.stat-card.s-purple .stat-label i { color: var(--purple); }
.stat-card.s-cyan   .stat-label i { color: var(--cyan); }

.stat-val { font-size: 34px; font-weight: 700; letter-spacing: -1px; line-height: 1; color: var(--t1); }
.stat-hint { font-size: 11.5px; color: var(--t3); margin-top: 5px; }

/* ── 2-COL ROW ── */
.row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 24px; }

/* ── CONTENT CARDS ── */
.card {
  background: rgba(7,13,31,0.75);
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 22px 24px;
  backdrop-filter: blur(14px);
  transition: border-color .2s;
}
.card:hover { border-color: var(--bordm); }

.card-hdr {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}
.card-title {
  font-size: 12px;
  font-weight: 600;
  letter-spacing: .09em;
  text-transform: uppercase;
  color: var(--t3);
  display: flex;
  align-items: center;
  gap: 8px;
}
.card-title i { font-size: 12px; color: var(--blue); }
.card-link { font-size: 11.5px; color: var(--blue); text-decoration: none; opacity: .75; transition: opacity .15s; }
.card-link:hover { opacity: 1; }

/* ── QUICK ACTIONS ── */
.actions-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 9px; }
.action-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 13px 14px;
  border-radius: 10px;
  font-family: var(--ui);
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  border: 1px solid var(--border);
  background: rgba(255,255,255,0.03);
  color: var(--t2);
  text-decoration: none;
  transition: background .15s, border-color .15s, color .15s;
}
.action-btn i { font-size: 14px; width: 16px; text-align: center; }
.action-btn:hover { background: rgba(255,255,255,0.06); border-color: var(--bordm); color: var(--t1); }
.action-btn.a-blue   i { color: var(--blue); }
.action-btn.a-green  i { color: var(--green); }
.action-btn.a-purple i { color: var(--purple); }
.action-btn.a-amber  i { color: var(--amber); }

/* ── LAST CODE PREVIEW ── */
.code-preview {
  background: #020617;
  border: 1px solid rgba(255,255,255,0.05);
  border-radius: 10px;
  padding: 16px 18px;
  font-family: var(--mono);
  font-size: 12px;
  color: #94a3b8;
  line-height: 1.75;
  max-height: 180px;
  overflow: auto;
  scrollbar-width: thin;
  scrollbar-color: #1e293b transparent;
}

/* ── ACTIVITY FEED ── */
.activity-list { display: flex; flex-direction: column; gap: 0; }
.activity-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 11px 0;
  border-bottom: 1px solid var(--border);
}
.activity-item:last-child { border-bottom: none; }
.activity-dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  margin-top: 5px;
  flex-shrink: 0;
}
.ad-blue   { background: var(--blue);   box-shadow: 0 0 6px rgba(59,130,246,0.6); }
.ad-green  { background: var(--green);  box-shadow: 0 0 6px rgba(74,222,128,0.6); }
.ad-purple { background: var(--purple); box-shadow: 0 0 6px rgba(167,139,250,0.6); }
.ad-cyan   { background: var(--cyan);   box-shadow: 0 0 6px rgba(34,211,238,0.6); }
.activity-text { font-size: 13px; color: var(--t2); flex: 1; line-height: 1.5; }
.activity-text strong { color: var(--t1); font-weight: 600; }
.activity-time { font-size: 11px; color: var(--t3); white-space: nowrap; margin-top: 2px; }
</style>
</head>
<body>

<!-- ══════════════════════════════════════
     SIDEBAR
     ══════════════════════════════════════ -->
<div class="sidebar">

  <!-- Stackly SVG logo -->
  <a href="/" class="logo">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 180 40">
      <defs>
        <linearGradient id="dlg1" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#3b82f6"/>
          <stop offset="100%" stop-color="#22d3ee"/>
        </linearGradient>
        <linearGradient id="dlg2" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" stop-color="#e2eaff"/>
          <stop offset="100%" stop-color="#a5c0ff"/>
        </linearGradient>
      </defs>
      <!-- icon bg -->
      <rect x="0" y="4" width="32" height="32" rx="8" fill="url(#dlg1)" opacity="0.15"/>
      <rect x="0" y="4" width="32" height="32" rx="8" fill="none" stroke="url(#dlg1)" stroke-width="1.2" opacity="0.55"/>
      <!-- stacked layers icon -->
      <rect x="7"  y="13"   width="18" height="3" rx="1.5" fill="url(#dlg1)" opacity="1"/>
      <rect x="9"  y="18.5" width="14" height="3" rx="1.5" fill="url(#dlg1)" opacity="0.75"/>
      <rect x="11" y="24"   width="10" height="3" rx="1.5" fill="url(#dlg1)" opacity="0.5"/>
      <!-- wordmark -->
      <text x="42" y="27" font-family="Outfit,Inter,sans-serif" font-size="19" font-weight="800" letter-spacing="1.5" fill="url(#dlg2)">STACKLY</text>
    </svg>
  </a>

  <!-- nav links -->
  <a href="/dashboard" class="nav-item active">
    <i class="fa-solid fa-chart-line"></i> Dashboard
  </a>
  <a href="/editor" class="nav-item">
    <i class="fa-solid fa-code"></i> Editor
  </a>
  <a href="/history" class="nav-item">
    <i class="fa-solid fa-clock-rotate-left"></i> History
  </a>


  <div class="sidebar-div"></div>

  <!-- logout -->
  <form method="POST" action="/logout">
    @csrf
    <button class="logout-btn">
      <i class="fa-solid fa-right-from-bracket"></i> Logout
    </button>
  </form>

</div>

<!-- ══════════════════════════════════════
     MAIN CONTENT
     ══════════════════════════════════════ -->
<div class="main">

  <!-- page header -->
  <div class="page-header">
    <div>
      <div class="page-title">Welcome back, <span>{{ $user->name }}</span> 👋</div>
      <div class="page-sub">Here's what's happening with your code today.</div>
    </div>
    <a href="/editor" class="btn-primary">
      <i class="fa-solid fa-code"></i> Open Editor
    </a>
  </div>

  <!-- ── STAT CARDS ── -->
  <div class="stats-grid">

    <!-- total files -->
    <div class="stat-card s-blue">
      <div class="stat-label"><i class="fa-solid fa-file-code"></i> Saved Files</div>
      <div class="stat-val">{{ $totalFiles }}</div>
      <div class="stat-hint">code snippets</div>
    </div>

    <!-- AI requests -->
    <div class="stat-card s-purple">
      <div class="stat-label"><i class="fa-solid fa-robot"></i> AI Requests</div>
      <div class="stat-val">{{ $totalFiles }}</div>
      <div class="stat-hint">suggestions made</div>
    </div>

    <!-- runs — NEW -->
    <div class="stat-card s-green">
      <div class="stat-label"><i class="fa-solid fa-play"></i> Runs</div>
      <div class="stat-val">{{ $totalRuns ?? 0 }}</div>
      <div class="stat-hint">executions today</div>
    </div>

    <!-- languages used — NEW -->
    <div class="stat-card s-cyan">
      <div class="stat-label"><i class="fa-solid fa-layer-group"></i> Languages</div>
      <div class="stat-val">{{ $langCount ?? 0 }}</div>
      <div class="stat-hint">distinct languages</div>
    </div>

  </div>

  <!-- ── ROW: quick actions only ── -->
  <div style="margin-bottom:24px">


    <div class="card">
      <div class="card-hdr">
        <div class="card-title"><i class="fa-solid fa-bolt"></i> Quick Actions</div>
      </div>
      <div class="actions-grid">
        <a href="/editor" class="action-btn a-blue">
          <i class="fa-solid fa-plus"></i> New File
        </a>
        <a href="/editor" class="action-btn a-green">
          <i class="fa-solid fa-play"></i> Run Code
        </a>
        <a href="/editor" class="action-btn a-purple">
          <i class="fa-solid fa-robot"></i> Ask AI
        </a>
        <a href="/history" class="action-btn a-amber">
          <i class="fa-solid fa-clock-rotate-left"></i> History
        </a>
      </div>

      <!-- activity feed — NEW -->
      <div class="card-title" style="margin-top:20px;margin-bottom:12px">
        <i class="fa-solid fa-activity" style="color:var(--cyan)"></i> Recent Activity
      </div>
      <div class="activity-list">
        <div class="activity-item">
          <div class="activity-dot ad-green"></div>
          <div style="flex:1">
            <div class="activity-text"><strong>main.py</strong> ran successfully</div>
            <div class="activity-time">2 minutes ago</div>
          </div>
        </div>
        <div class="activity-item">
          <div class="activity-dot ad-purple"></div>
          <div style="flex:1">
            <div class="activity-text">AI suggested <strong>3 improvements</strong></div>
            <div class="activity-time">15 minutes ago</div>
          </div>
        </div>
        <div class="activity-item">
          <div class="activity-dot ad-blue"></div>
          <div style="flex:1">
            <div class="activity-text"><strong>app.js</strong> saved</div>
            <div class="activity-time">1 hour ago</div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- ── LAST CODE PREVIEW ── -->
  @if($lastCode)
  <div class="card">
    <div class="card-hdr">
      <div class="card-title"><i class="fa-solid fa-code"></i> Last Code</div>
      <a href="/editor" class="card-link">Open in editor →</a>
    </div>
    <div class="code-preview">{{ $lastCode->code }}</div>
  </div>
  @endif

</div><!-- /main -->
</body>
</html>
