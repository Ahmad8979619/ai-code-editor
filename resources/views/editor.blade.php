<!DOCTYPE html>
<html>
<head>
<title>AI Code Editor</title>

<!-- CSRF token used by Laravel for all POST requests -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Google Fonts: Outfit for UI text, JetBrains Mono for code -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Font Awesome 6 icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
  /* ─────────────────────────────────────────
     DESIGN TOKENS
     All colours and glow effects defined here
     so they can be reused consistently.
  ───────────────────────────────────────── */
  :root {
    --bg-deep:       #02040f;
    --bg-mid:        #070d1f;
    --bg-surface:    #0c1428;
    --bg-raised:     #111c35;
    --border:        rgba(99,160,255,0.12);
    --border-bright: rgba(99,160,255,0.28);

    --cyan:    #22d3ee;
    --blue:    #3b82f6;
    --indigo:  #818cf8;
    --violet:  #a78bfa;
    --green:   #4ade80;
    --amber:   #fbbf24;
    --red:     #f87171;

    --text-1: #e2eaff;
    --text-2: #8ba0c8;
    --text-3: #4a6080;

    --glow-blue:  0 0 18px rgba(59,130,246,0.45), 0 0 40px rgba(59,130,246,0.2);
    --glow-cyan:  0 0 18px rgba(34,211,238,0.45), 0 0 40px rgba(34,211,238,0.2);
    --glow-green: 0 0 18px rgba(74,222,128,0.45), 0 0 40px rgba(74,222,128,0.2);
    --glow-red:   0 0 18px rgba(248,113,113,0.45), 0 0 40px rgba(248,113,113,0.2);
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'Outfit', sans-serif;
    color: var(--text-1);
    background: var(--bg-deep);
    height: 100vh;
    overflow: hidden;
    position: relative;
  }

  /* Animated radial gradient mesh sitting behind everything */
  body::before {
    content: '';
    position: fixed;
    inset: 0;
    background:
      radial-gradient(ellipse 80% 60% at 10% -10%, rgba(59,130,246,0.12) 0%, transparent 60%),
      radial-gradient(ellipse 60% 50% at 90% 110%, rgba(129,140,248,0.1) 0%, transparent 55%),
      radial-gradient(ellipse 50% 40% at 50% 50%, rgba(34,211,238,0.04) 0%, transparent 60%);
    pointer-events: none;
    z-index: 0;
    animation: meshDrift 20s ease-in-out infinite alternate;
  }

  @keyframes meshDrift {
    0%   { opacity: 1; transform: scale(1) translateY(0); }
    100% { opacity: 0.7; transform: scale(1.05) translateY(-15px); }
  }

  /* Subtle scanline texture overlay */
  body::after {
    content: '';
    position: fixed;
    inset: 0;
    background: repeating-linear-gradient(
      0deg, transparent, transparent 2px,
      rgba(0,0,0,0.08) 2px, rgba(0,0,0,0.08) 4px
    );
    pointer-events: none;
    z-index: 0;
  }

  /* ─────────────────────────────────────────
     NAVBAR
  ───────────────────────────────────────── */
  .navbar {
    position: relative;
    z-index: 10;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 24px;
    height: 56px;
    background: rgba(7,13,31,0.85);
    backdrop-filter: blur(20px) saturate(180%);
    border-bottom: 1px solid var(--border);
    box-shadow: 0 1px 0 rgba(99,160,255,0.08), 0 4px 24px rgba(0,0,0,0.4);
  }

  .logo {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--text-1);
  }

  .logo-icon {
    width: 30px; height: 30px;
    border-radius: 8px;
    background: linear-gradient(135deg, var(--blue), var(--cyan));
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
    box-shadow: var(--glow-blue);
    animation: logoPulse 3s ease-in-out infinite;
  }

  @keyframes logoPulse {
    0%, 100% { box-shadow: var(--glow-blue); }
    50%       { box-shadow: 0 0 28px rgba(34,211,238,0.6), 0 0 60px rgba(34,211,238,0.25); }
  }

  .logo-badge {
    font-size: 9px; font-weight: 600; letter-spacing: 0.15em;
    color: var(--cyan);
    background: rgba(34,211,238,0.08);
    border: 1px solid rgba(34,211,238,0.2);
    padding: 2px 7px; border-radius: 20px; margin-left: 4px;
  }

  .right { display: flex; gap: 8px; align-items: center; }

  /* Language selector — icon injected via ::before */
  .lang-wrap { position: relative; display: flex; align-items: center; }

  .lang-wrap::before {
    content: '\f121';
    font-family: 'Font Awesome 6 Free'; font-weight: 900;
    position: absolute; left: 11px; font-size: 12px;
    color: var(--text-3); pointer-events: none; z-index: 1;
  }

  select {
    appearance: none;
    padding: 7px 14px 7px 32px;
    border-radius: 9px;
    background: rgba(11,18,40,0.8);
    color: var(--text-1);
    border: 1px solid var(--border);
    font-family: 'Outfit', sans-serif; font-size: 13px;
    cursor: pointer;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
  }

  select:hover, select:focus {
    border-color: var(--border-bright);
    box-shadow: 0 0 0 3px rgba(59,130,246,0.08);
  }

  select option { background: #0c1428; }

  /* ─────────────────────────────────────────
     BUTTONS — shared base + per-variant colour
  ───────────────────────────────────────── */
  button {
    border: none; padding: 7px 15px; border-radius: 9px;
    cursor: pointer; color: white;
    display: inline-flex; align-items: center; gap: 7px;
    font-size: 13px; font-weight: 500;
    font-family: 'Outfit', sans-serif;
    transition: transform 0.18s, box-shadow 0.18s, filter 0.18s;
    position: relative; overflow: hidden; letter-spacing: 0.01em;
  }

  /* Shine overlay revealed on hover */
  button::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(160deg, rgba(255,255,255,0.18) 0%, transparent 60%);
    opacity: 0; transition: opacity 0.2s; border-radius: inherit;
  }

  button:hover::after { opacity: 1; }
  button:hover        { transform: translateY(-2px); filter: brightness(1.15); }
  button:active       { transform: translateY(0); }

  .btn-suggest { background: linear-gradient(135deg,#22c55e,#0d9488); box-shadow:0 0 12px rgba(34,197,94,0.3); }
  .btn-suggest:hover  { box-shadow: var(--glow-green); }

  .btn-apply   { background: linear-gradient(135deg,#3b82f6,#6366f1); box-shadow:0 0 12px rgba(59,130,246,0.3); }
  .btn-apply:hover    { box-shadow: var(--glow-blue); }

  .btn-run     { background: linear-gradient(135deg,#22d3ee,#3b82f6); box-shadow:0 0 12px rgba(34,211,238,0.3); }
  .btn-run:hover      { box-shadow: var(--glow-cyan); }

  .btn-history { background: linear-gradient(135deg,#a78bfa,#818cf8); box-shadow:0 0 12px rgba(167,139,250,0.3); }
  .btn-history:hover  { box-shadow: 0 0 18px rgba(167,139,250,0.55),0 0 40px rgba(167,139,250,0.2); }

  .btn-logout  { background: linear-gradient(135deg,#ef4444,#dc2626); box-shadow:0 0 12px rgba(239,68,68,0.3); }
  .btn-logout:hover   { box-shadow: var(--glow-red); }

  /* ─────────────────────────────────────────
     MAIN LAYOUT — sidebar | editor | AI panel
  ───────────────────────────────────────── */
  .container {
    position: relative; z-index: 1;
    display: flex; gap: 10px; padding: 12px;
    height: calc(100vh - 56px);
  }

  /* ─── SIDEBAR ─── */
  .sidebar {
    width: 200px; flex-shrink: 0;
    background: rgba(7,13,31,0.7); backdrop-filter: blur(20px);
    border: 1px solid var(--border); border-radius: 14px;
    padding: 14px 10px;
    display: flex; flex-direction: column; gap: 4px;
    overflow: auto;
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.04);
  }

  .sidebar-header {
    font-size: 10px; font-weight: 600; letter-spacing: 0.12em;
    text-transform: uppercase; color: var(--text-3);
    padding: 0 6px 10px;
    display: flex; align-items: center; gap: 8px;
    border-bottom: 1px solid var(--border); margin-bottom: 6px;
  }

  .file-list { flex: 1; display: flex; flex-direction: column; gap: 2px; }

  .file {
    padding: 7px 10px; border-radius: 8px; cursor: pointer;
    display: flex; align-items: center; gap: 9px;
    font-size: 13px; color: var(--text-2);
    border: 1px solid transparent;
    transition: background 0.15s, color 0.15s;
  }

  .file:hover  { background:rgba(59,130,246,0.08); border-color:var(--border); color:var(--text-1); }
  .file.active { background:rgba(59,130,246,0.12); border-color:rgba(59,130,246,0.2); color:var(--cyan); }
  .file i      { font-size: 12px; opacity: 0.7; }

  .new-file-btn {
    margin-top: auto; width: 100%; justify-content: center;
    background: rgba(34,211,238,0.07);
    border: 1px dashed rgba(34,211,238,0.25);
    color: var(--cyan); border-radius: 9px;
    padding: 8px; font-size: 12px;
    transition: background 0.2s, border-color 0.2s, box-shadow 0.2s;
  }

  .new-file-btn:hover {
    background:rgba(34,211,238,0.12);
    border-color:rgba(34,211,238,0.45);
    box-shadow:var(--glow-cyan); transform:none;
  }

  /* ─── EDITOR BOX ─── */
  .editor-box {
    flex: 1; min-width: 300px;
    display: flex; flex-direction: column;
    border-radius: 14px; border: 1px solid var(--border);
    background: rgba(7,13,31,0.6); backdrop-filter: blur(20px);
    overflow: hidden;
    position: relative;   /* anchor for the absolute popup */
    box-shadow:
      0 0 0 1px rgba(255,255,255,0.03) inset,
      0 0 30px rgba(59,130,246,0.08),
      0 0 60px rgba(34,211,238,0.04);
    transition: box-shadow 0.4s;
  }

  .editor-box:focus-within {
    box-shadow:
      0 0 0 1px rgba(99,160,255,0.12) inset,
      0 0 40px rgba(59,130,246,0.15),
      0 0 80px rgba(34,211,238,0.07);
    border-color: var(--border-bright);
  }

  /* Tab bar */
  .tabs {
    display: flex; gap: 4px; padding: 8px 10px 0;
    background: rgba(2,4,15,0.6);
    border-bottom: 1px solid var(--border);
    overflow-x: auto; scrollbar-width: none; flex-shrink: 0;
  }
  .tabs::-webkit-scrollbar { display: none; }

  .tab {
    padding: 6px 13px;
    background: rgba(11,18,40,0.5);
    border: 1px solid var(--border); border-bottom: none;
    border-radius: 8px 8px 0 0; cursor: pointer;
    display: inline-flex; align-items: center; gap: 7px;
    font-size: 12px; font-family: 'JetBrains Mono', monospace;
    color: var(--text-2); white-space: nowrap;
    transition: color 0.15s, background 0.15s;
  }

  .tab:hover { color:var(--text-1); background:rgba(59,130,246,0.08); }

  /* Active tab sits 1px lower to merge with the editor border */
  .tab.active {
    background: var(--bg-surface); border-color: var(--border-bright);
    color: var(--cyan); border-bottom: 1px solid var(--bg-surface);
    position: relative; top: 1px;
  }

  .close {
    opacity: 0.4; cursor: pointer; font-size: 11px;
    transition: opacity 0.15s, color 0.15s; line-height: 1; padding: 2px;
  }
  .close:hover { opacity:1; color:var(--red); }

  #editor { flex: 1; min-height: 0; }

  /* Animated bottom border when editor is focused */
  .glow-line {
    height: 1px;
    background: linear-gradient(90deg,transparent,var(--blue),var(--cyan),var(--blue),transparent);
    opacity: 0; transition: opacity 0.4s; flex-shrink: 0;
  }
  .editor-box:focus-within .glow-line { opacity: 1; }

  /* ─────────────────────────────────────────
     AUTOCOMPLETE POPUP
     Floated absolutely inside .editor-box.
     JS positions it just below the cursor.
     Shows a list of AI-generated completions.
     Keyboard: ↑/↓ navigate, Enter/Tab accept, Esc dismiss.
  ───────────────────────────────────────── */
  #autocomplete-popup {
    position: absolute;
    z-index: 100;           /* above Monaco UI layers            */
    display: none;          /* hidden until a suggestion arrives */
    flex-direction: column;
    min-width: 300px;
    max-width: 500px;
    background: rgba(6,12,28,0.97);
    border: 1px solid var(--border-bright);
    border-radius: 10px;
    box-shadow: 0 8px 40px rgba(0,0,0,0.7), 0 0 20px rgba(59,130,246,0.15);
    overflow: hidden;
    backdrop-filter: blur(24px);
    animation: popupIn 0.14s ease;
  }

  @keyframes popupIn {
    from { opacity:0; transform:translateY(-5px); }
    to   { opacity:1; transform:translateY(0);    }
  }

  /* Header: shows language label + spinner while loading */
  .popup-header {
    display: flex; align-items: center; gap: 8px;
    padding: 7px 12px;
    background: rgba(59,130,246,0.07);
    border-bottom: 1px solid var(--border);
    font-size: 11px; color: var(--text-3);
    font-family: 'Outfit', sans-serif; letter-spacing: 0.06em;
  }

  /* Spinning ring shown while the fetch is in-flight */
  .popup-spinner {
    width: 10px; height: 10px;
    border: 1.5px solid rgba(59,130,246,0.25);
    border-top-color: var(--blue);
    border-radius: 50%;
    animation: spin 0.65s linear infinite;
    flex-shrink: 0;
  }

  @keyframes spin { to { transform: rotate(360deg); } }

  /* Scrollable list of suggestion rows */
  .popup-items { max-height: 200px; overflow-y: auto; }

  /* Single suggestion row */
  .popup-item {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 8px 12px; cursor: pointer;
    border-bottom: 1px solid rgba(99,160,255,0.05);
    transition: background 0.1s;
  }
  .popup-item:last-child { border-bottom: none; }

  /* Highlighted on hover or keyboard navigation */
  .popup-item:hover,
  .popup-item.selected { background: rgba(59,130,246,0.13); }

  /* The code text inside each suggestion row */
  .popup-item-code {
    font-family: 'JetBrains Mono', monospace;
    font-size: 12px; color: var(--cyan);
    white-space: pre; overflow: hidden; text-overflow: ellipsis;
    flex: 1; line-height: 1.5;
  }

  /* Small wand icon badge on the left of each row */
  .popup-item-icon {
    font-size: 11px; color: var(--blue);
    margin-top: 2px; flex-shrink: 0;
  }

  /* Footer: keyboard shortcut reminder */
  .popup-footer {
    padding: 5px 12px;
    background: rgba(2,4,15,0.5);
    border-top: 1px solid var(--border);
    font-size: 10px; color: var(--text-3);
    font-family: 'Outfit', sans-serif;
    display: flex; gap: 12px;
  }

  .popup-footer kbd {
    background: rgba(99,160,255,0.1);
    border: 1px solid var(--border-bright);
    border-radius: 4px; padding: 1px 5px;
    font-size: 10px; color: var(--text-2);
    font-family: 'JetBrains Mono', monospace;
  }

  /* ─── DRAG DIVIDER ─── */
  #divider {
    width: 6px; cursor: col-resize;
    background: var(--border); border-radius: 10px;
    transition: background 0.2s, box-shadow 0.2s;
    flex-shrink: 0; margin: 4px 0;
  }
  #divider:hover, #divider.dragging {
    background: var(--blue); box-shadow: var(--glow-blue);
  }

  /* ─── AI PANEL ─── */
  .ai-box {
    width: 320px; flex-shrink: 0;
    background: rgba(7,13,31,0.7); backdrop-filter: blur(20px);
    border-radius: 14px; border: 1px solid var(--border);
    display: flex; flex-direction: column; overflow: hidden;
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.04);
  }

  .panel-section {
    flex: 1; min-height: 0;
    display: flex; flex-direction: column; overflow: hidden;
  }
  .panel-section + .panel-section { border-top: 1px solid var(--border); }

  .panel-header {
    padding: 11px 16px;
    display: flex; align-items: center; gap: 9px;
    font-size: 11px; font-weight: 600; letter-spacing: 0.1em;
    text-transform: uppercase; color: var(--text-3);
    background: rgba(2,4,15,0.3);
    border-bottom: 1px solid var(--border); flex-shrink: 0;
  }
  .panel-header i { font-size: 13px; }

  /* Status dot: grey = idle, green + blink = active */
  .panel-header .dot {
    width:7px; height:7px; border-radius:50%;
    margin-left:auto; background:var(--text-3); flex-shrink:0;
  }
  .panel-header .dot.active {
    background:var(--green);
    box-shadow:0 0 8px rgba(74,222,128,0.7);
    animation:dotBlink 2s ease-in-out infinite;
  }

  @keyframes dotBlink {
    0%,100% { opacity:1; } 50% { opacity:0.4; }
  }

  .panel-body {
    flex:1; overflow:auto; padding:12px;
    scrollbar-width:thin; scrollbar-color:var(--bg-raised) transparent;
  }
  .panel-body::-webkit-scrollbar       { width:4px; }
  .panel-body::-webkit-scrollbar-track { background:transparent; }
  .panel-body::-webkit-scrollbar-thumb { background:var(--bg-raised); border-radius:4px; }

  pre {
    font-family:'JetBrains Mono',monospace; font-size:12px;
    line-height:1.7; color:var(--text-2);
    white-space:pre-wrap; word-break:break-all;
  }
  pre.has-content          { color:var(--text-1); }
  pre.output-content       { color:var(--green); }
  pre.output-content.has-error { color:var(--red); }

  .empty-state {
    color:var(--text-3); font-size:12px;
    font-style:italic; padding:6px 0;
  }

  /* Loading skeleton shimmer */
  .shimmer { display:none; gap:6px; flex-direction:column; padding:4px 0; }
  .shimmer.visible { display:flex; }

  .shimmer-line {
    height:10px; border-radius:5px;
    background:linear-gradient(90deg,var(--bg-raised) 25%,rgba(59,130,246,0.15) 50%,var(--bg-raised) 75%);
    background-size:200% 100%;
    animation:shimmerAnim 1.4s infinite;
  }
  @keyframes shimmerAnim {
    0%   { background-position:200% 0; }
    100% { background-position:-200% 0; }
  }

  * { scrollbar-width:thin; scrollbar-color:var(--bg-raised) transparent; }
  a  { text-decoration:none; }
</style>
</head>

<body>

<!-- ═══════════════════════════════════════
     NAVBAR
═══════════════════════════════════════ -->
<div class="navbar">
  <div class="logo">
    <div class="logo-icon"><i class="fa-solid fa-bolt"></i></div>
    AiEditor
    <span class="logo-badge">BETA</span>
  </div>

  <div class="right">

    <!-- Language selector — drives Monaco syntax highlight + AI language param -->
    <div class="lang-wrap">
      <select id="language" onchange="changeLanguage()">
        <option value="python">Python</option>
        <option value="javascript">JavaScript</option>
        <option value="php">PHP</option>
        <option value="java">Java</option>
        <option value="cpp">C++</option>
        <option value="csharp">C#</option>
        <option value="go">Go</option>
        <option value="rust">Rust</option>
        <option value="html">HTML</option>
        <option value="css">CSS</option>
        <option value="json">JSON</option>
      </select>
    </div>

    <!-- Suggest: POST code to /suggest → show AI response in right panel -->
    <button class="btn-suggest" onclick="askAI()">
      <i class="fa-solid fa-robot"></i> Suggest
    </button>

    <!-- Apply: copy last AI response back into the editor -->
    <button class="btn-apply" onclick="applyFix()">
      <i class="fa-solid fa-wand-magic-sparkles"></i> Apply
    </button>

    <!-- Run: POST code to /run → show stdout/stderr in right panel -->
    <button class="btn-run" onclick="runCode()">
      <i class="fa-solid fa-play"></i> Run
    </button>

    <!-- History: navigate to session history page -->
    <a href="/history">
      <button class="btn-history">
        <i class="fa-solid fa-clock-rotate-left"></i> History
      </button>
    </a>

    <!-- Logout: submit Laravel logout POST form -->
    <form method="POST" action="/logout" style="margin:0">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <button class="btn-logout">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
      </button>
    </form>

  </div>
</div>

<!-- ═══════════════════════════════════════
     MAIN LAYOUT
═══════════════════════════════════════ -->
<div class="container">

  <!-- ── SIDEBAR: file explorer ── -->
  <div class="sidebar">
    <div class="sidebar-header">
      <i class="fa-solid fa-folder-open"></i> Explorer
    </div>
    <!-- File entries built dynamically by renderFiles() -->
    <div class="file-list" id="files"></div>
    <button class="new-file-btn" onclick="newFile()">
      <i class="fa-solid fa-plus"></i> New File
    </button>
  </div>

  <!-- ── EDITOR BOX ── -->
  <div class="editor-box" id="editorBox">

    <!-- Tab bar — built dynamically by renderTabs() -->
    <div id="tabs" class="tabs"></div>

    <!-- Monaco editor mounts here -->
    <div id="editor"></div>

    <!-- Bottom glow line (visible on focus) -->
    <div class="glow-line"></div>

    <!-- ── AUTOCOMPLETE POPUP ──────────────────────────────
         Positioned absolutely by positionPopup() at runtime.
         Appears after 900 ms of idle typing.
         Keyboard shortcuts: ↑/↓ to navigate, Enter or Tab
         to accept, Esc to dismiss.                          -->
    <div id="autocomplete-popup">
      <!-- Header row: spinner + status label -->
      <div class="popup-header">
        <div class="popup-spinner" id="popup-spinner"></div>
        <span id="popup-label">AI suggestions</span>
      </div>
      <!-- Suggestion rows injected here by renderPopupItems() -->
      <div class="popup-items" id="popup-items"></div>
      <!-- Footer: keyboard hint -->
      <div class="popup-footer">
        <span><kbd>↑</kbd><kbd>↓</kbd> navigate</span>
        <span><kbd>↵</kbd> or <kbd>⇥</kbd> accept</span>
        <span><kbd>Esc</kbd> dismiss</span>
      </div>
    </div>

  </div>

  <!-- Draggable resize handle -->
  <div id="divider"></div>

  <!-- ── AI PANEL ── -->
  <div class="ai-box">

    <!-- Section 1: AI Response (from /suggest) -->
    <div class="panel-section" style="flex:1.2">
      <div class="panel-header">
        <i class="fa-solid fa-brain" style="color:var(--indigo)"></i>
        AI Response
        <div class="dot" id="aiDot"></div>
      </div>
      <div class="panel-body">
        <div class="shimmer" id="aiShimmer">
          <div class="shimmer-line" style="width:90%"></div>
          <div class="shimmer-line" style="width:70%"></div>
          <div class="shimmer-line" style="width:80%"></div>
          <div class="shimmer-line" style="width:55%"></div>
          <div class="shimmer-line" style="width:75%"></div>
        </div>
        <pre id="result" class="empty-state">— ask AI to suggest improvements —</pre>
      </div>
    </div>

    <!-- Section 2: Code Output (from /run) -->
    <div class="panel-section">
      <div class="panel-header">
        <i class="fa-solid fa-terminal" style="color:var(--cyan)"></i>
        Output
        <div class="dot" id="runDot"></div>
      </div>
      <div class="panel-body">
        <div class="shimmer" id="runShimmer">
          <div class="shimmer-line" style="width:80%"></div>
          <div class="shimmer-line" style="width:60%"></div>
        </div>
        <pre id="output" class="empty-state">— run code to see output —</pre>
      </div>
    </div>

  </div>
</div>

<!-- Monaco Editor loader from unpkg CDN -->
<script src="https://unpkg.com/monaco-editor@latest/min/vs/loader.js"></script>

<script>
/* ═══════════════════════════════════════════════════════════════
   FILE SYSTEM STATE
   files      — { filename: content } map stored in memory
   openTabs   — ordered array of filenames shown in tab bar
   currentFile — which file is active in the editor
═══════════════════════════════════════════════════════════════ */
let files       = { "main.py": "print('hello world')" };
let openTabs    = ["main.py"];
let currentFile = "main.py";

/* ═══════════════════════════════════════════════════════════════
   ICON HELPERS
   Returns the correct Font Awesome class string for an extension.
   Brand icons exist for js, py, php, html, css, java.
   Everything else uses a generic solid icon.
═══════════════════════════════════════════════════════════════ */
function getIconClass(name) {
  const ext    = name.split('.').pop().toLowerCase();
  const brands = {
    js:'fa-brands fa-js', py:'fa-brands fa-python',
    php:'fa-brands fa-php', html:'fa-brands fa-html5',
    css:'fa-brands fa-css3-alt', java:'fa-brands fa-java',
  };
  const solid = {
    json:'fa-solid fa-code', ts:'fa-solid fa-code',
    rs:'fa-solid fa-code', go:'fa-solid fa-code',
    cpp:'fa-solid fa-file-code', cs:'fa-solid fa-file-code',
  };
  return brands[ext] || solid[ext] || 'fa-solid fa-file-code';
}

/* Returns a colour for the file icon based on extension */
function getIconColor(name) {
  const ext = name.split('.').pop().toLowerCase();
  const map = {
    js:'#fbbf24', py:'#60a5fa', php:'#a78bfa', html:'#fb923c',
    css:'#38bdf8', json:'#34d399', ts:'#38bdf8', rs:'#fb923c',
    go:'#22d3ee', java:'#f87171',
  };
  return map[ext] || '#8ba0c8';
}

/* ═══════════════════════════════════════════════════════════════
   SIDEBAR — rebuilds the file list from the `files` object
═══════════════════════════════════════════════════════════════ */
function renderFiles() {
  let html = '';
  for (let name in files) {
    const active = name === currentFile ? 'active' : '';
    html += `
      <div class="file ${active}" onclick="openFile('${name}')">
        <i class="${getIconClass(name)}" style="color:${getIconColor(name)}"></i>
        ${name}
      </div>`;
  }
  document.getElementById("files").innerHTML = html;
}

/* ═══════════════════════════════════════════════════════════════
   TAB BAR — rebuilds tabs from the openTabs array
═══════════════════════════════════════════════════════════════ */
function renderTabs() {
  let html = '';
  openTabs.forEach(file => {
    html += `
      <div class="tab ${file === currentFile ? 'active' : ''}" onclick="switchTab('${file}')">
        <i class="${getIconClass(file)}" style="color:${getIconColor(file)}"></i>
        ${file}
        <i class="fa-solid fa-xmark close" onclick="closeTab(event,'${file}')"></i>
      </div>`;
  });
  document.getElementById("tabs").innerHTML = html;
}

/* Opens a file: adds to openTabs if not present, loads into editor */
function openFile(name) {
  if (!openTabs.includes(name)) openTabs.push(name);
  currentFile = name;
  editor.setValue(files[name]);
  renderTabs(); renderFiles();
}

/* Switches the editor to an already-open tab */
function switchTab(name) {
  currentFile = name;
  editor.setValue(files[name]);
  renderTabs(); renderFiles();
}

/* Closes a tab; always keeps at least one tab open */
function closeTab(e, name) {
  e.stopPropagation();
  openTabs = openTabs.filter(f => f !== name);
  if (openTabs.length === 0) openTabs = [Object.keys(files)[0]];
  if (currentFile === name) {
    currentFile = openTabs[0];
    editor.setValue(files[currentFile] || '');
  }
  renderTabs(); renderFiles();
}

/* Prompts for a filename and creates a new empty file */
function newFile() {
  const name = prompt("File name:");
  if (name) {
    files[name] = '';
    openTabs.push(name);
    currentFile = name;
    editor.setValue('');
    renderFiles(); renderTabs();
  }
}

/* ═══════════════════════════════════════════════════════════════
   MONACO EDITOR INIT
   Everything that depends on the editor lives in this callback.
═══════════════════════════════════════════════════════════════ */
require.config({ paths: { vs: 'https://unpkg.com/monaco-editor@latest/min/vs' } });

require(['vs/editor/editor.main'], function () {

  /* ── Create editor ── */
  window.editor = monaco.editor.create(document.getElementById('editor'), {
    value:                      files[currentFile],
    language:                   'python',
    theme:                      'vs-dark',
    automaticLayout:            true,   /* auto-resize on container changes */
    minimap:                    { enabled: false },
    fontSize:                   13,
    fontFamily:                 "'JetBrains Mono', monospace",
    fontLigatures:              true,
    lineHeight:                 22,
    padding:                    { top: 16, bottom: 16 },
    scrollbar:                  { verticalScrollbarSize: 4, horizontalScrollbarSize: 4 },
    renderLineHighlight:        'gutter',
    cursorBlinking:             'expand',
    cursorSmoothCaretAnimation: 'on',
    smoothScrolling:            true,
    bracketPairColorization:    { enabled: true },
    /* Disable Monaco's built-in completion popup so ours is the only one */
    quickSuggestions:           false,
    suggestOnTriggerCharacters: false,
    wordBasedSuggestions:       'off',
  });

  /* Sync every keystroke to the in-memory file store */
  editor.onDidChangeModelContent(() => {
    files[currentFile] = editor.getValue();
  });

  /* Make editor background transparent so the CSS gradient shows through */
  const domNode = editor.getDomNode();   /* official Monaco API */
  if (domNode) domNode.style.background = 'transparent';

  /* ═══════════════════════════════════════════════════════════
     AUTOCOMPLETE POPUP SYSTEM
     ─────────────────────────────────────────────────────────
     Flow:
       1. User types → onDidChangeModelContent fires
       2. Popup hides immediately (stale suggestion gone)
       3. 900 ms debounce timer starts
       4. On timer fire: snapshot cursor position, POST to /autocomplete
       5. Backend returns { suggestions: ["...", "..."] }
          (also handles legacy { suggestion: "..." } format)
       6. positionPopup() places the div just below the cursor
       7. renderPopupItems() builds clickable rows
       8. Keyboard: ↑/↓ highlight, Enter/Tab insert, Esc hide
       9. Click on a row: acceptSuggestion(index)
      10. Accepted text is inserted via executeEdits() — undoable

     Works for ALL languages because we send the language
     name to the backend and show whatever the API returns.
  ═══════════════════════════════════════════════════════════ */

  /* DOM refs for the popup elements */
  const popup        = document.getElementById('autocomplete-popup');
  const popupItems   = document.getElementById('popup-items');
  const popupLabel   = document.getElementById('popup-label');
  const popupSpinner = document.getElementById('popup-spinner');

  let acTimer      = null;   /* debounce setTimeout handle                */
  let acController = null;   /* AbortController for the current fetch     */
  let acList       = [];     /* array of suggestion strings currently shown */
  let acIndex      = -1;     /* keyboard-highlighted row index            */
  let acPosition   = null;   /* Monaco Position at the time of the fetch  */

  /* ── positionPopup ────────────────────────────────────────
     Uses Monaco's getScrolledVisiblePosition() to get the
     pixel location of the cursor, then offsets by the tab
     bar height and gutter width to place the popup correctly
     inside .editor-box. Clamps to avoid right-edge overflow. */
  function positionPopup() {
    const pos = editor.getPosition();
    if (!pos) return;

    const coords = editor.getScrolledVisiblePosition(pos);
    if (!coords) return;

    const layout      = editor.getLayoutInfo();
    const tabsH       = document.getElementById('tabs').offsetHeight;
    const editorW     = document.getElementById('editorBox').offsetWidth;
    const POPUP_W     = 320;
    const LINE_H      = 22;   /* matches lineHeight in editor config */

    /* Pixel position relative to .editor-box */
    let left = layout.contentLeft + coords.left;
    let top  = tabsH + coords.top + LINE_H;   /* one line below cursor */

    /* Clamp horizontally so the popup never clips the right edge */
    left = Math.min(left, editorW - POPUP_W - 12);
    left = Math.max(8, left);

    popup.style.left = left + 'px';
    popup.style.top  = top  + 'px';
  }

  /* ── renderPopupItems ─────────────────────────────────────
     Builds the suggestion row list from acList.
     The .selected class highlights the keyboard-active row.
     mousedown (not click) is used to prevent editor blur.   */
  function renderPopupItems() {
    popupItems.innerHTML = '';
    acList.forEach((text, i) => {
      const row     = document.createElement('div');
      row.className = 'popup-item' + (i === acIndex ? ' selected' : '');
      /* Show only the first line in the row to keep it compact */
      row.innerHTML = `
        <span class="popup-item-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></span>
        <span class="popup-item-code">${escHtml(text.split('\n')[0])}</span>`;
      row.addEventListener('mousedown', (e) => {
        e.preventDefault();       /* prevent editor from losing focus */
        acceptSuggestion(i);
      });
      popupItems.appendChild(row);
    });
  }

  /* ── showPopup ────────────────────────────────────────────
     Stores the snapshot position, renders rows, positions the
     div, and makes it visible.                               */
  function showPopup(suggestions, position) {
    acList     = suggestions;
    acIndex    = 0;           /* pre-select the first suggestion */
    acPosition = position;   /* remember where to insert text   */
    popupSpinner.style.display = 'none';
    popupLabel.textContent = suggestions.length === 1
      ? '1 suggestion'
      : `${suggestions.length} suggestions`;
    renderPopupItems();
    positionPopup();
    popup.style.display = 'flex';
  }

  /* ── hidePopup ────────────────────────────────────────────
     Hides the popup div and clears internal state.           */
  function hidePopup() {
    popup.style.display = 'none';
    acList  = [];
    acIndex = -1;
  }

  /* ── acceptSuggestion ─────────────────────────────────────
     Inserts the selected suggestion at acPosition using
     Monaco's executeEdits so the action is in the undo stack.
     Then moves the cursor to the end of the inserted text.   */
  function acceptSuggestion(index) {
    const text = acList[index];
    if (!text || !acPosition) return;

    editor.executeEdits('ai-autocomplete', [{
      range: new monaco.Range(
        acPosition.lineNumber, acPosition.column,
        acPosition.lineNumber, acPosition.column
      ),
      text: text,
    }]);

    /* Compute new cursor position after insertion */
    const lines  = text.split('\n');
    const newLine = acPosition.lineNumber + lines.length - 1;
    const newCol  = lines.length > 1
      ? lines[lines.length - 1].length + 1
      : acPosition.column + text.length;

    editor.setPosition({ lineNumber: newLine, column: newCol });
    editor.focus();
    hidePopup();
  }

  /* ── escHtml ──────────────────────────────────────────────
     Minimal HTML escaping so code snippets don't break the
     popup row's innerHTML.                                   */
  function escHtml(str) {
    return str
      .replace(/&/g,'&amp;').replace(/</g,'&lt;')
      .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  /* ── fetchAutocomplete ────────────────────────────────────
     Cancels any previous in-flight request, then POSTs to
     /autocomplete with:
       code      — text from line 1 to the cursor (context)
       full_code — entire editor content
       language  — current language selector value
     Expected response: { suggestions: ["...", "..."] }
     Also handles legacy: { suggestion: "..." }              */
  async function fetchAutocomplete(position) {
    /* Cancel the previous request to avoid race conditions */
    if (acController) acController.abort();
    acController = new AbortController();

    const model    = editor.getModel();
    const language = document.getElementById('language').value;

    /* Text from line 1 col 1 up to cursor (gives the AI context of what's above) */
    const textUntilCursor = model.getValueInRange({
      startLineNumber: 1, startColumn: 1,
      endLineNumber: position.lineNumber, endColumn: position.column,
    });

    const fullCode = model.getValue();
    if (fullCode.trim().length < 3) { hidePopup(); return; }

    /* Show the popup immediately in "loading" state */
    popupSpinner.style.display = 'block';
    popupLabel.textContent     = 'thinking…';
    popupItems.innerHTML       = '';
    positionPopup();
    popup.style.display        = 'flex';

    try {
      const res = await fetch('/autocomplete', {
        method:  'POST',
        signal:  acController.signal,
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
          code:      textUntilCursor,  /* partial code up to cursor  */
          full_code: fullCode,         /* full file for context      */
          language:  language,         /* e.g. "python", "javascript" */
        }),
      });

      if (!res.ok) { hidePopup(); return; }

      const data = await res.json();

      /* Normalise: support both array and single-string response formats */
      let suggestions = [];
      if (Array.isArray(data.suggestions) && data.suggestions.length > 0) {
        suggestions = data.suggestions.map(s => s.trim()).filter(Boolean);
      } else if (typeof data.suggestion === 'string' && data.suggestion.trim()) {
        suggestions = [data.suggestion.trim()];
      }

      if (suggestions.length === 0) { hidePopup(); return; }

      showPopup(suggestions, position);

    } catch (e) {
      /* AbortError is expected when a newer request cancelled this one */
      if (e.name !== 'AbortError') console.warn('Autocomplete error:', e);
      hidePopup();
    }
  }

  /* ── Trigger autocomplete on content change ─────────────
     Debounced 900 ms so the API is not called on every key.
     The popup hides immediately on each keystroke to prevent
     stale suggestions showing while the user is still typing.
     We snapshot the cursor BEFORE the timeout so the position
     is stable even if the cursor moves during the delay.     */
  editor.onDidChangeModelContent(() => {
    files[currentFile] = editor.getValue(); /* keep file store in sync */
    hidePopup();
    clearTimeout(acTimer);

    /* Snapshot cursor now — it may move before the timer fires */
    const position = editor.getPosition();

    acTimer = setTimeout(() => {
      fetchAutocomplete(position);
    }, 900); /* 900 ms idle before sending the request */
  });

  /* ── Keyboard navigation inside the popup ────────────────
     onKeyDown intercepts ↑ ↓ Enter Tab Esc only when the
     popup is open; all other keys pass through to Monaco.   */
  editor.onKeyDown((e) => {
    /* Nothing to intercept when popup is not visible */
    if (popup.style.display !== 'flex') return;

    if (e.keyCode === monaco.KeyCode.UpArrow) {
      /* Move highlight up — stop at first item */
      e.preventDefault(); e.stopPropagation();
      acIndex = Math.max(0, acIndex - 1);
      renderPopupItems();

    } else if (e.keyCode === monaco.KeyCode.DownArrow) {
      /* Move highlight down — stop at last item */
      e.preventDefault(); e.stopPropagation();
      acIndex = Math.min(acList.length - 1, acIndex + 1);
      renderPopupItems();

    } else if (e.keyCode === monaco.KeyCode.Enter || e.keyCode === monaco.KeyCode.Tab) {
      /* Accept the highlighted suggestion */
      e.preventDefault(); e.stopPropagation();
      if (acIndex >= 0) acceptSuggestion(acIndex);

    } else if (e.keyCode === monaco.KeyCode.Escape) {
      /* Dismiss without inserting */
      e.preventDefault(); e.stopPropagation();
      hidePopup();
    }
  });

  /* Hide popup when the editor loses focus (e.g. user clicks elsewhere) */
  editor.onDidBlurEditorWidget(() => hidePopup());

}); /* end require() */

/* ═══════════════════════════════════════════════════════════════
   changeLanguage
   Called when the language <select> changes.
   Updates Monaco's syntax highlighting model.
═══════════════════════════════════════════════════════════════ */
function changeLanguage() {
  const lang = document.getElementById('language').value;
  monaco.editor.setModelLanguage(editor.getModel(), lang);
}

/* ═══════════════════════════════════════════════════════════════
   setLoading
   Shows/hides the shimmer skeleton and toggles the green
   status dot in the AI panel header.
   type: 'ai' (suggest panel) | 'run' (output panel)
═══════════════════════════════════════════════════════════════ */
function setLoading(type, on) {
  document.getElementById(type + 'Shimmer').classList.toggle('visible', on);
  const dot = document.getElementById(type + 'Dot');
  on ? dot.classList.add('active') : dot.classList.remove('active');
}

/* ═══════════════════════════════════════════════════════════════
   askAI  (Suggest button)
   POSTs code + language to /suggest.
   Displays the text response in the AI Response panel.
═══════════════════════════════════════════════════════════════ */
async function askAI() {
  const resultEl = document.getElementById('result');
  resultEl.className = '';
  resultEl.textContent = '';
  setLoading('ai', true);

  try {
    const res  = await fetch('/suggest', {
      method:  'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({
        code:     editor.getValue(),
        language: document.getElementById('language').value,
      }),
    });
    const data = await res.json();
    setLoading('ai', false);
    resultEl.className   = 'has-content';
    resultEl.textContent = data.result;
  } catch (e) {
    setLoading('ai', false);
    resultEl.className   = 'empty-state';
    resultEl.textContent = '— error fetching suggestion —';
  }
}

/* ═══════════════════════════════════════════════════════════════
   applyFix  (Apply button)
   If the AI Response panel has content (not a placeholder),
   replace the editor with it so the user can run the fixed code.
═══════════════════════════════════════════════════════════════ */
function applyFix() {
  const content = document.getElementById('result').textContent;
  if (content && !content.startsWith('—')) {
    editor.setValue(content);
    files[currentFile] = content;
  }
}

/* ═══════════════════════════════════════════════════════════════
   runCode  (Run button)
   POSTs code + language to /run.
   Shows stdout in green or stderr in red in the Output panel.
═══════════════════════════════════════════════════════════════ */
async function runCode() {
  const outputEl = document.getElementById('output');
  outputEl.className   = '';
  outputEl.textContent = '';
  setLoading('run', true);

  try {
    const res  = await fetch('/run', {
      method:  'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({
        code:     editor.getValue(),
        language: document.getElementById('language').value,
      }),
    });
    const data = await res.json();
    setLoading('run', false);
    outputEl.className = 'output-content';
    if (data.error) {
      outputEl.classList.add('has-error');
      outputEl.textContent = data.error;
    } else {
      outputEl.textContent = data.output;
    }
  } catch (e) {
    setLoading('run', false);
    outputEl.className   = 'empty-state';
    outputEl.textContent = '— error running code —';
  }
}

/* ═══════════════════════════════════════════════════════════════
   DRAGGABLE DIVIDER
   Allows the user to resize the editor vs AI panel by
   dragging the thin vertical handle between them.
═══════════════════════════════════════════════════════════════ */
const divider   = document.getElementById('divider');
const editorBox = document.getElementById('editorBox');
let isDragging  = false;

/* Start drag on mousedown */
divider.addEventListener('mousedown', () => {
  isDragging = true;
  divider.classList.add('dragging');
});

/* During drag: recompute editor width from mouse X position */
document.addEventListener('mousemove', (e) => {
  if (!isDragging) return;
  /* containerLeft = right edge of the sidebar */
  const containerLeft = editorBox.parentElement.getBoundingClientRect().left
    + 12    /* container left padding */
    + 200   /* sidebar width          */
    + 10;   /* gap between sidebar and editor */
  editorBox.style.flex  = 'none';
  editorBox.style.width = Math.max(300, e.clientX - containerLeft) + 'px';
});

/* End drag on mouseup anywhere on the page */
document.addEventListener('mouseup', () => {
  isDragging = false;
  divider.classList.remove('dragging');
});

/* ── Initial render of sidebar + tabs ── */
renderFiles();
renderTabs();
</script>
</body>
</html>
