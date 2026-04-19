<!DOCTYPE html>
<html>
<head>
<title>AiEditor</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Geist:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* ═══════════════════════════════════════════════════════
   DESIGN TOKENS — clean minimal dark theme
   Single accent color, clear 3-level bg hierarchy
═══════════════════════════════════════════════════════ */
:root {
  /* Backgrounds — 3 distinct levels, no more */
  --bg-base:    #0f1117;
  --bg-panel:   #161b27;
  --bg-raised:  #1c2333;
  --bg-hover:   #222d42;

  /* Borders — subtle */
  --border:     rgba(255,255,255,0.07);
  --border-mid: rgba(255,255,255,0.12);
  --border-hi:  rgba(255,255,255,0.2);

  /* Single accent — blue */
  --accent:     #4f8ef7;
  --accent-dim: rgba(79,142,247,0.12);
  --accent-glow: 0 0 18px rgba(79,142,247,0.25);

  /* Status colors */
  --green: #3ecf7e;
  --red:   #f06a6a;
  --amber: #f5a623;
  --cyan:  #38bdf8;

  /* Text hierarchy */
  --text-1: #e8edf5;
  --text-2: #7a8ba8;
  --text-3: #3f4f68;

  /* Typography */
  --font-ui:   'Geist', sans-serif;
  --font-code: 'JetBrains Mono', monospace;

  /* Layout */
  --navbar-h:  52px;
  --sidebar-w: 210px;
  --radius:    10px;
  --radius-sm: 6px;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: var(--font-ui);
  background: var(--bg-base);
  color: var(--text-1);
  height: 100vh;
  overflow: hidden;
  font-size: 14px;
  line-height: 1.5;
}

/* Subtle radial gradient atmosphere */
body::before {
  content: '';
  position: fixed; inset: 0;
  background:
    radial-gradient(ellipse 70% 60% at 15% 0%, rgba(79,142,247,0.05) 0%, transparent 55%),
    radial-gradient(ellipse 50% 40% at 85% 100%, rgba(56,189,248,0.03) 0%, transparent 50%);
  pointer-events: none; z-index: 0;
}

/* ═══════════════════════════════════════════════════════
   NAVBAR
   Redesigned: primary actions visible, secondary collapsed
   to icon-only buttons. Much less visual noise.
═══════════════════════════════════════════════════════ */
.navbar {
  position: relative; z-index: 50;
  height: var(--navbar-h);
  display: flex; align-items: center; gap: 6px;
  padding: 0 14px;
  background: var(--bg-panel);
  border-bottom: 1px solid var(--border);
}

/* Logo */
.logo {
  display: flex; align-items: center; gap: 8px;
  font-size: 14px; font-weight: 600; color: var(--text-1);
  letter-spacing: -0.01em; margin-right: 4px;
}
.logo-icon {
  width: 26px; height: 26px; border-radius: 7px;
  background: var(--accent);
  display: flex; align-items: center; justify-content: center;
  font-size: 11px; color: #fff;
  box-shadow: 0 0 12px rgba(79,142,247,0.4);
}
.logo-beta {
  font-size: 9px; font-weight: 600; letter-spacing: 0.1em;
  color: var(--accent); background: var(--accent-dim);
  border: 1px solid rgba(79,142,247,0.22); padding: 2px 6px; border-radius: 20px;
}

/* Separator */
.nav-sep { width: 1px; height: 20px; background: var(--border); margin: 0 4px; flex-shrink: 0; }

/* Language selector */
.lang-wrap { position: relative; display: flex; align-items: center; }
.lang-wrap i { position: absolute; left: 9px; font-size: 10px; color: var(--text-3); pointer-events: none; z-index: 1; }
select {
  appearance: none; padding: 5px 10px 5px 26px;
  border-radius: var(--radius-sm); background: var(--bg-raised);
  color: var(--text-1); border: 1px solid var(--border);
  font-family: var(--font-ui); font-size: 12.5px; cursor: pointer; outline: none;
  transition: border-color 0.15s;
}
select:hover, select:focus { border-color: var(--border-mid); }
select option { background: var(--bg-raised); }

/* Autosave pill */
.autosave-pill {
  display: flex; align-items: center; gap: 5px; padding: 4px 10px;
  border-radius: 20px; font-size: 11px; color: var(--text-3);
  background: var(--bg-raised); border: 1px solid var(--border);
  white-space: nowrap; transition: color 0.2s, border-color 0.2s;
}
.autosave-pill.saving { color: var(--amber); border-color: rgba(245,166,35,0.25); }
.autosave-pill.saved  { color: var(--green); border-color: rgba(62,207,126,0.25); }
.autosave-pill i      { font-size: 9px; }

.nav-spacer { flex: 1; }

/* ─────────────────────────────
   NAVBAR BUTTONS
   Primary: text + icon, visible
   Secondary: icon-only with title tooltips
───────────────────────────── */
.nav-btn {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 5px 11px; border-radius: var(--radius-sm);
  font-family: var(--font-ui); font-size: 12.5px; font-weight: 500;
  cursor: pointer; border: 1px solid transparent;
  transition: background 0.12s, border-color 0.12s, transform 0.1s;
  white-space: nowrap; color: var(--text-2); background: transparent;
}
.nav-btn:hover { color: var(--text-1); background: var(--bg-raised); border-color: var(--border); transform: translateY(-1px); }
.nav-btn:active { transform: translateY(0); }

/* Icon-only variant */
.nav-btn.icon-only { padding: 5px 7px; color: var(--text-3); }
.nav-btn.icon-only:hover { color: var(--text-1); }

/* Primary — Run button */
.nav-btn.primary {
  background: var(--accent); color: #fff; border: none;
  padding: 5px 16px; box-shadow: var(--accent-glow);
}
.nav-btn.primary:hover { background: #6a9ef9; box-shadow: 0 0 22px rgba(79,142,247,0.45); border: none; color: #fff; }

/* Suggest */
.nav-btn.suggest {
  color: var(--accent); border-color: rgba(79,142,247,0.2); background: var(--accent-dim);
}
.nav-btn.suggest:hover { background: rgba(79,142,247,0.2); border-color: rgba(79,142,247,0.4); }

/* Preview toggled-on state */
.nav-btn.preview-on { color: var(--green); border-color: rgba(62,207,126,0.25); background: rgba(62,207,126,0.08); }

/* Danger */
.nav-btn.danger { color: var(--red); }
.nav-btn.danger:hover { background: rgba(240,106,106,0.1); border-color: rgba(240,106,106,0.2); color: var(--red); }

/* ═══════════════════════════════════════════════════════
   APP LAYOUT
═══════════════════════════════════════════════════════ */
.app-layout {
  position: relative; z-index: 1;
  display: flex; height: calc(100vh - var(--navbar-h));
  overflow: hidden;
}

/* ─────────────────────────────
   SIDEBAR
───────────────────────────── */
.sidebar {
  width: var(--sidebar-w); flex-shrink: 0;
  background: var(--bg-panel); border-right: 1px solid var(--border);
  display: flex; flex-direction: column; overflow: hidden;
}

.sidebar-header {
  display: flex; align-items: center; padding: 11px 13px 9px;
  font-size: 10px; font-weight: 600; letter-spacing: 0.1em;
  text-transform: uppercase; color: var(--text-3);
  border-bottom: 1px solid var(--border); flex-shrink: 0; gap: 7px;
}

.file-list { flex: 1; overflow-y: auto; padding: 5px; }

.file-item {
  display: flex; align-items: center; gap: 7px;
  padding: 6px 9px; border-radius: var(--radius-sm);
  cursor: pointer; font-size: 12px; color: var(--text-2);
  border: 1px solid transparent; transition: background 0.1s, color 0.1s;
}
.file-item:hover  { background: var(--bg-hover); color: var(--text-1); }
.file-item.active { background: var(--accent-dim); color: var(--accent); border-color: rgba(79,142,247,0.2); }
.file-item i      { font-size: 11px; flex-shrink: 0; }
.file-item .unsaved-dot {
  width: 5px; height: 5px; border-radius: 50%;
  background: var(--amber); margin-left: auto; display: none; flex-shrink: 0;
}
.file-item.unsaved .unsaved-dot { display: block; }

.new-file-btn {
  margin: 5px; padding: 7px;
  border-radius: var(--radius-sm); background: transparent;
  border: 1px dashed var(--border-mid); color: var(--text-3);
  font-family: var(--font-ui); font-size: 12px; cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 6px;
  transition: color 0.12s, border-color 0.12s, background 0.12s;
}
.new-file-btn:hover { color: var(--accent); border-color: rgba(79,142,247,0.35); background: var(--accent-dim); }

/* ─────────────────────────────
   EDITOR AREA
───────────────────────────── */
.editor-area { flex: 1; min-width: 0; display: flex; flex-direction: column; overflow: hidden; }

/* TABS */
.tabs-row {
  display: flex; align-items: flex-end; gap: 2px;
  padding: 7px 7px 0; background: var(--bg-panel);
  border-bottom: 1px solid var(--border);
  overflow-x: auto; flex-shrink: 0; scrollbar-width: none;
}
.tabs-row::-webkit-scrollbar { display: none; }

.tab {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 5px 11px 6px; border-radius: var(--radius-sm) var(--radius-sm) 0 0;
  font-size: 11.5px; font-family: var(--font-code); color: var(--text-3);
  background: transparent; border: 1px solid transparent; border-bottom: none;
  cursor: pointer; white-space: nowrap; transition: color 0.1s, background 0.1s;
}
.tab:hover { color: var(--text-2); background: var(--bg-hover); }
.tab.active { color: var(--text-1); background: var(--bg-base); border-color: var(--border); border-bottom: 1px solid var(--bg-base); position: relative; top: 1px; }
.tab .tab-dot { width: 5px; height: 5px; border-radius: 50%; background: var(--amber); display: none; }
.tab.unsaved .tab-dot { display: block; }
.tab .tab-close { font-size: 9px; opacity: 0.4; cursor: pointer; padding: 1px 2px; transition: opacity 0.1s, color 0.1s; }
.tab .tab-close:hover { opacity: 1; color: var(--red); }

/* EDITOR TOOLBAR */
.editor-toolbar {
  display: flex; align-items: center; gap: 3px;
  padding: 5px 9px; background: var(--bg-panel);
  border-bottom: 1px solid var(--border); flex-shrink: 0;
}

.tool-btn {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 4px 8px; border-radius: var(--radius-sm);
  font-family: var(--font-ui); font-size: 11.5px; font-weight: 500;
  color: var(--text-3); background: transparent; border: 1px solid transparent;
  cursor: pointer; transition: color 0.1s, background 0.1s, border-color 0.1s;
}
.tool-btn:hover    { color: var(--text-1); background: var(--bg-raised); border-color: var(--border); }
.tool-btn.active   { color: var(--accent); background: var(--accent-dim); border-color: rgba(79,142,247,0.25); }
.tool-btn.error-on { color: var(--red); background: rgba(240,106,106,0.08); border-color: rgba(240,106,106,0.2); }
.tool-btn i        { font-size: 10px; }

.toolbar-stats {
  margin-left: auto; display: flex; align-items: center; gap: 11px;
  font-size: 10.5px; color: var(--text-3); font-family: var(--font-code);
}

/* SEARCH BAR */
.search-bar {
  display: none; align-items: center; gap: 5px;
  padding: 5px 9px; background: var(--bg-base);
  border-bottom: 1px solid var(--border); flex-shrink: 0;
}
.search-bar.visible { display: flex; }
.search-input {
  flex: 1; max-width: 210px; padding: 4px 9px; border-radius: var(--radius-sm);
  background: var(--bg-raised); border: 1px solid var(--border);
  color: var(--text-1); font-family: var(--font-code); font-size: 12px;
  outline: none; transition: border-color 0.12s;
}
.search-input:focus { border-color: var(--border-mid); }
.search-input::placeholder { color: var(--text-3); }
.search-action-btn {
  padding: 4px 8px; border-radius: var(--radius-sm); font-size: 11px;
  background: var(--bg-raised); border: 1px solid var(--border);
  color: var(--text-2); cursor: pointer; font-family: var(--font-ui);
  transition: all 0.1s;
}
.search-action-btn:hover { color: var(--text-1); border-color: var(--border-mid); }
.search-count { font-size: 10px; color: var(--text-3); font-family: var(--font-code); white-space: nowrap; }

/* EDITOR SPLIT CONTAINER */
.split-container {
  flex: 1; min-height: 0; display: flex; overflow: hidden;
}
.editor-box { flex: 1; min-width: 0; display: flex; flex-direction: column; overflow: hidden; position: relative; }
#editor { flex: 1; min-height: 0; }

/* Preview sync status bar */
.preview-status-bar {
  display: none; align-items: center; gap: 7px; padding: 3px 12px;
  background: var(--bg-panel); border-top: 1px solid var(--border);
  font-size: 10px; color: var(--text-3); font-family: var(--font-code); flex-shrink: 0;
}
.preview-status-bar.visible { display: flex; }
.status-ok  { color: var(--green); }
.status-err { color: var(--red); }

/* ─────────────────────────────
   PREVIEW PANEL
───────────────────────────── */
#preview-divider {
  width: 4px; cursor: col-resize; flex-shrink: 0;
  background: var(--border); display: none; transition: background 0.15s;
}
#preview-divider.visible  { display: block; }
#preview-divider:hover,
#preview-divider.dragging { background: var(--accent); }

.preview-box {
  width: 0; min-width: 0; flex-shrink: 0; display: flex; flex-direction: column;
  border-left: 1px solid transparent; overflow: hidden; opacity: 0;
  transition: width 0.28s ease, opacity 0.22s ease, border-color 0.28s;
}
.preview-box.open { width: 45%; min-width: 270px; border-color: var(--border); opacity: 1; }

.preview-header {
  display: flex; align-items: center; gap: 7px; padding: 0 13px; height: 36px;
  background: var(--bg-panel); border-bottom: 1px solid var(--border);
  font-size: 10px; font-weight: 600; letter-spacing: 0.08em;
  text-transform: uppercase; color: var(--text-3); flex-shrink: 0;
}
.preview-header .live-dot {
  width: 6px; height: 6px; border-radius: 50%; background: var(--green);
  box-shadow: 0 0 5px rgba(62,207,126,0.6); margin-left: auto;
  animation: dotBlink 2s ease-in-out infinite;
}
@keyframes dotBlink { 0%,100% { opacity:1; } 50% { opacity:0.3; } }

.preview-controls {
  display: flex; align-items: center; gap: 4px; padding: 5px 9px;
  border-bottom: 1px solid var(--border); background: var(--bg-panel); flex-shrink: 0;
}
.preview-tab-btn {
  padding: 3px 8px; border-radius: var(--radius-sm); font-size: 11px;
  background: transparent; border: 1px solid transparent; color: var(--text-3);
  cursor: pointer; font-family: var(--font-ui); transition: all 0.1s;
}
.preview-tab-btn:hover  { color: var(--text-1); background: var(--bg-raised); border-color: var(--border); }
.preview-tab-btn.active { color: var(--accent); background: var(--accent-dim); border-color: rgba(79,142,247,0.25); }
.preview-refresh {
  margin-left: auto; padding: 3px 8px; border-radius: var(--radius-sm); font-size: 11px;
  background: transparent; border: 1px solid var(--border); color: var(--text-2);
  cursor: pointer; font-family: var(--font-ui); transition: all 0.1s;
}
.preview-refresh:hover { color: var(--text-1); border-color: var(--border-mid); }

.preview-frame-wrap { flex: 1; min-height: 0; position: relative; background: white; overflow: hidden; }
#preview-iframe  { width: 100%; height: 100%; border: none; background: white; display: block; }
.react-preview-wrap { flex: 1; min-height: 0; overflow: hidden; background: white; }
#react-preview-frame { width: 100%; height: 100%; border: none; display: block; }

.preview-loading {
  position: absolute; inset: 0; background: rgba(15,17,23,0.9);
  display: flex; align-items: center; justify-content: center;
  flex-direction: column; gap: 8px; font-size: 12px; color: var(--text-3);
  transition: opacity 0.2s; pointer-events: none;
}
.preview-loading.hidden { opacity: 0; }
.preview-spinner-ring {
  width: 22px; height: 22px; border: 2px solid var(--border); border-top-color: var(--accent);
  border-radius: 50%; animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ─────────────────────────────
   MAIN RESIZE DIVIDER (editor ↔ AI panel)
───────────────────────────── */
#main-divider {
  width: 4px; cursor: col-resize; flex-shrink: 0;
  background: var(--border); transition: background 0.15s;
}
#main-divider:hover, #main-divider.dragging { background: var(--accent); }

/* ─────────────────────────────
   AI PANEL
───────────────────────────── */
.ai-panel {
  width: 290px; flex-shrink: 0;
  background: var(--bg-panel); border-left: 1px solid var(--border);
  display: flex; flex-direction: column; overflow: hidden;
}

.panel-section { flex: 1; min-height: 0; display: flex; flex-direction: column; overflow: hidden; }
.panel-section + .panel-section { border-top: 1px solid var(--border); max-height: 38%; }

.panel-header {
  display: flex; align-items: center; gap: 7px; padding: 9px 13px;
  font-size: 10px; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase;
  color: var(--text-3); background: var(--bg-base); border-bottom: 1px solid var(--border); flex-shrink: 0;
}
.panel-header i { font-size: 10px; }
.panel-dot {
  width: 6px; height: 6px; border-radius: 50%; background: var(--text-3); margin-left: auto;
  transition: background 0.2s, box-shadow 0.2s;
}
.panel-dot.active { background: var(--green); box-shadow: 0 0 5px rgba(62,207,126,0.6); animation: dotBlink 2s ease-in-out infinite; }

.panel-body {
  flex: 1; overflow-y: auto; padding: 12px;
  scrollbar-width: thin; scrollbar-color: var(--bg-hover) transparent;
}
.panel-body::-webkit-scrollbar { width: 4px; }
.panel-body::-webkit-scrollbar-thumb { background: var(--bg-hover); border-radius: 2px; }

pre {
  font-family: var(--font-code); font-size: 12px; line-height: 1.7;
  white-space: pre-wrap; word-break: break-all; color: var(--text-2);
}
pre.has-content    { color: var(--text-1); }
pre.output-content { color: var(--green); }
pre.output-content.error { color: var(--red); }
.empty-hint { font-size: 12px; color: var(--text-3); font-style: italic; }

/* Shimmer loading skeleton */
.shimmer { display: none; flex-direction: column; gap: 5px; padding: 3px 0; }
.shimmer.visible { display: flex; }
.shimmer-line {
  height: 9px; border-radius: 5px;
  background: linear-gradient(90deg, var(--bg-raised) 25%, var(--bg-hover) 50%, var(--bg-raised) 75%);
  background-size: 200% 100%; animation: shimmerAnim 1.3s infinite;
}
@keyframes shimmerAnim { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

/* ═══════════════════════════════════════════════════════
   AUTOCOMPLETE POPUP
═══════════════════════════════════════════════════════ */
#autocomplete-popup {
  position: absolute; z-index: 100; display: none; flex-direction: column;
  min-width: 270px; max-width: 440px;
  background: var(--bg-panel); border: 1px solid var(--border-mid);
  border-radius: var(--radius); box-shadow: 0 8px 28px rgba(0,0,0,0.5), var(--accent-glow);
  overflow: hidden; animation: popIn 0.12s ease;
}
@keyframes popIn { from { opacity:0; transform: translateY(-4px); } to { opacity:1; transform: translateY(0); } }

.popup-header {
  display: flex; align-items: center; gap: 6px; padding: 5px 10px;
  background: var(--bg-raised); border-bottom: 1px solid var(--border);
  font-size: 10px; color: var(--text-3); font-family: var(--font-ui);
}
.popup-spinner {
  width: 8px; height: 8px; border: 1.5px solid var(--border); border-top-color: var(--accent);
  border-radius: 50%; animation: spin 0.65s linear infinite; flex-shrink: 0;
}
.popup-items { max-height: 170px; overflow-y: auto; }
.popup-item {
  display: flex; align-items: flex-start; gap: 8px; padding: 6px 10px;
  cursor: pointer; border-bottom: 1px solid var(--border); transition: background 0.1s;
}
.popup-item:last-child   { border-bottom: none; }
.popup-item:hover,
.popup-item.selected     { background: var(--bg-hover); }
.popup-item-code { font-family: var(--font-code); font-size: 12px; color: var(--cyan); flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: pre; line-height: 1.5; }
.popup-item-icon { font-size: 10px; color: var(--accent); margin-top: 2px; flex-shrink: 0; }
.popup-footer {
  padding: 4px 10px; background: var(--bg-base); border-top: 1px solid var(--border);
  font-size: 9.5px; color: var(--text-3); font-family: var(--font-ui); display: flex; gap: 9px;
}
.popup-footer kbd {
  background: var(--bg-raised); border: 1px solid var(--border-mid);
  border-radius: 3px; padding: 1px 4px; font-size: 9px;
  color: var(--text-2); font-family: var(--font-code);
}

/* ═══════════════════════════════════════════════════════
   MODALS — shared structure
═══════════════════════════════════════════════════════ */
.modal-overlay {
  position: fixed; inset: 0; z-index: 500;
  background: rgba(0,0,0,0.6); backdrop-filter: blur(6px);
  display: none; align-items: center; justify-content: center;
}
.modal-overlay.open { display: flex; animation: fadeIn 0.15s ease; }
@keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

.modal {
  background: var(--bg-panel); border: 1px solid var(--border-mid);
  border-radius: 14px; width: 620px; max-width: 92vw; max-height: 78vh;
  display: flex; flex-direction: column; overflow: hidden;
  box-shadow: 0 24px 70px rgba(0,0,0,0.7);
  animation: slideUp 0.2s cubic-bezier(0.34,1.56,0.64,1);
}
@keyframes slideUp { from { opacity:0; transform: translateY(16px) scale(0.97); } to { opacity:1; transform: translateY(0) scale(1); } }

.modal-header { display: flex; align-items: center; gap: 8px; padding: 13px 17px; border-bottom: 1px solid var(--border); }
.modal-header h2 { font-size: 13.5px; font-weight: 600; flex: 1; color: var(--text-1); }
.modal-close {
  width: 24px; height: 24px; border-radius: var(--radius-sm);
  background: var(--bg-raised); border: 1px solid var(--border);
  color: var(--text-2); font-size: 10px; cursor: pointer;
  display: flex; align-items: center; justify-content: center; transition: background 0.1s, color 0.1s;
}
.modal-close:hover { background: rgba(240,106,106,0.12); color: var(--red); border-color: rgba(240,106,106,0.2); }
.modal-body { flex: 1; overflow-y: auto; padding: 16px; }

/* ─────────────────────────────
   STATS MODAL
───────────────────────────── */
.stats-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 9px; margin-bottom: 18px; }
.stat-card {
  background: var(--bg-raised); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 12px 14px;
}
.stat-val { font-size: 22px; font-weight: 700; font-family: var(--font-code); margin-bottom: 2px; }
.stat-label { font-size: 10px; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.07em; }
.stat-card.blue   .stat-val { color: var(--accent); }
.stat-card.cyan   .stat-val { color: var(--cyan); }
.stat-card.green  .stat-val { color: var(--green); }
.stat-card.amber  .stat-val { color: var(--amber); }
.stat-card.red    .stat-val { color: var(--red); }
.stat-card.violet .stat-val { color: #a78bfa; }

.section-title {
  font-size: 10px; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: var(--text-3);
  margin-bottom: 9px; display: flex; align-items: center; gap: 8px;
}
.section-title::after { content:''; flex:1; height:1px; background: var(--border); }

.lang-bar-list { display: flex; flex-direction: column; gap: 7px; }
.lang-bar-row  { display: flex; align-items: center; gap: 9px; font-size: 12px; }
.lang-bar-label { width: 65px; color: var(--text-2); font-family: var(--font-code); }
.lang-bar-track { flex:1; height: 5px; background: var(--bg-raised); border-radius: 3px; overflow: hidden; }
.lang-bar-fill  { height: 100%; border-radius: 3px; transition: width 0.5s ease; }
.lang-bar-count { width: 26px; text-align: right; color: var(--text-3); font-family: var(--font-code); font-size: 11px; }

/* ─────────────────────────────
   FOLDERS MODAL
───────────────────────────── */
.folder-tree { display: flex; flex-direction: column; gap: 2px; }
.folder-item {
  display: flex; align-items: center; gap: 8px; padding: 7px 10px; border-radius: var(--radius-sm);
  cursor: pointer; font-size: 13px; color: var(--text-2); border: 1px solid transparent; transition: all 0.1s;
}
.folder-item:hover { background: var(--bg-hover); color: var(--text-1); }
.folder-item i     { color: var(--amber); font-size: 13px; }
.folder-count      { margin-left: auto; font-size: 10px; color: var(--text-3); font-family: var(--font-code); }
.folder-files      { padding-left: 24px; display: flex; flex-direction: column; gap: 1px; margin-top: 1px; }
.folder-file-item {
  display: flex; align-items: center; gap: 7px; padding: 5px 8px;
  border-radius: var(--radius-sm); cursor: pointer; font-size: 12px; color: var(--text-3);
  transition: all 0.1s; border: 1px solid transparent;
}
.folder-file-item:hover { background: var(--bg-hover); color: var(--text-1); }
.folder-new-btn {
  margin-top: 9px; padding: 7px 13px; border-radius: var(--radius-sm); font-size: 12px;
  background: transparent; border: 1px dashed var(--border-mid); color: var(--text-3);
  cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 0.15s;
  width: 100%; justify-content: center; font-family: var(--font-ui);
}
.folder-new-btn:hover { color: var(--amber); border-color: rgba(245,166,35,0.4); background: rgba(245,166,35,0.06); }

/* ─────────────────────────────
   RUN HISTORY MODAL
───────────────────────────── */
.run-history-list { display: flex; flex-direction: column; gap: 7px; }
.run-history-item {
  background: var(--bg-raised); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 10px 12px;
  display: flex; flex-direction: column; gap: 6px;
  cursor: pointer; transition: border-color 0.1s;
}
.run-history-item:hover { border-color: var(--border-mid); }
.run-history-meta { display: flex; align-items: center; gap: 7px; font-size: 11px; color: var(--text-3); }
.rh-lang {
  padding: 1px 6px; border-radius: 4px;
  background: var(--accent-dim); border: 1px solid rgba(79,142,247,0.2);
  color: var(--accent); font-family: var(--font-code);
}
.rh-ok  { color: var(--green); }
.rh-err { color: var(--red); }
.run-history-code {
  font-family: var(--font-code); font-size: 11px; color: var(--text-2);
  white-space: pre; overflow: hidden; text-overflow: ellipsis; max-height: 36px;
  border-left: 2px solid var(--border-mid); padding-left: 7px;
}
.run-history-output { font-family: var(--font-code); font-size: 11px; white-space: pre-wrap; word-break: break-all; max-height: 46px; overflow: hidden; }
.run-history-output.ok  { color: var(--green); }
.run-history-output.err { color: var(--red); }
.rh-restore {
  align-self: flex-end; padding: 3px 8px; border-radius: 4px; font-size: 11px;
  background: var(--accent-dim); border: 1px solid rgba(79,142,247,0.2); color: var(--accent);
  cursor: pointer; transition: all 0.1s; font-family: var(--font-ui);
}
.rh-restore:hover { background: rgba(79,142,247,0.2); }
.rh-empty { color: var(--text-3); font-size: 13px; font-style: italic; text-align: center; padding: 24px 0; }

/* ─────────────────────────────
   AI EXPLAIN MODAL
───────────────────────────── */
.explain-content { font-size: 13px; line-height: 1.8; color: var(--text-1); white-space: pre-wrap; word-break: break-word; font-family: var(--font-ui); }
.explain-content.loading { color: var(--text-3); font-style: italic; }

/* ─────────────────────────────
   SHARE MODAL
───────────────────────────── */
.share-link-row {
  display: flex; gap: 8px; align-items: center;
  background: var(--bg-raised); border: 1px solid var(--border); border-radius: var(--radius);
  padding: 9px 12px; font-family: var(--font-code); font-size: 12px; color: var(--cyan); word-break: break-all;
}
.share-copy-btn {
  flex-shrink: 0; padding: 4px 11px; border-radius: var(--radius-sm); font-size: 12px;
  background: var(--accent-dim); border: 1px solid rgba(79,142,247,0.25); color: var(--accent);
  cursor: pointer; white-space: nowrap; font-family: var(--font-ui); transition: background 0.1s;
}
.share-copy-btn:hover { background: rgba(79,142,247,0.2); }
.share-info-row {
  display: flex; align-items: center; gap: 9px; padding: 9px 12px;
  background: var(--bg-raised); border: 1px solid var(--border); border-radius: var(--radius);
  font-size: 12px; color: var(--text-2); margin-top: 9px;
}
.share-info-row i { font-size: 13px; width: 16px; text-align: center; }

/* ═══════════════════════════════════════════════════════
   TOAST
═══════════════════════════════════════════════════════ */
#toast {
  position: fixed; bottom: 18px; right: 18px; z-index: 9999;
  padding: 8px 15px; border-radius: var(--radius);
  background: var(--bg-panel); border: 1px solid var(--border-mid);
  font-size: 12.5px; color: var(--text-1);
  display: flex; align-items: center; gap: 7px;
  box-shadow: 0 6px 22px rgba(0,0,0,0.5);
  transform: translateY(14px); opacity: 0; pointer-events: none;
  transition: transform 0.22s cubic-bezier(0.34,1.56,0.64,1), opacity 0.18s;
  backdrop-filter: blur(10px);
}
#toast.show { transform: translateY(0); opacity: 1; }
#toast.success i { color: var(--green); }
#toast.info    i { color: var(--accent); }
#toast.error   i { color: var(--red); }

/* Global scrollbar style */
* { scrollbar-width: thin; scrollbar-color: var(--bg-hover) transparent; }
a { text-decoration: none; }
</style>
</head>

<body>

<!-- ═══════════════════════════════════════════════════════
     NAVBAR
     Redesigned: 4 primary labeled buttons, rest are icon-only
═══════════════════════════════════════════════════════ -->
<div class="navbar">

  <!-- Brand -->
  <div class="logo">
    <div class="logo-icon"><i class="fa-solid fa-bolt"></i></div>
    AiEditor
    <span class="logo-beta">BETA</span>
  </div>

  <div class="nav-sep"></div>

  <!-- Language selector -->
  <div class="lang-wrap">
    <i class="fa-solid fa-code"></i>
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
      <option value="jsx">React (JSX)</option>
    </select>
  </div>

  <!-- Autosave status -->
  <div class="autosave-pill" id="autosavePill">
    <i class="fa-solid fa-floppy-disk"></i>
    <span id="autosaveText">Auto-save on</span>
  </div>

  <div class="nav-spacer"></div>

  <!-- PRIMARY ACTIONS (labeled) -->
  <button class="nav-btn" id="previewBtn" onclick="togglePreview()" title="Toggle live preview">
    <i class="fa-solid fa-eye" id="previewIcon"></i>
    <span id="previewBtnText">Preview</span>
  </button>

  <button class="nav-btn suggest" onclick="askAI()" title="Ask AI to suggest improvements">
    <i class="fa-solid fa-robot"></i> Suggest
  </button>

  <button class="nav-btn" onclick="applyFix()" title="Apply AI suggestion to editor">
    <i class="fa-solid fa-wand-magic-sparkles"></i> Apply
  </button>

  <button class="nav-btn primary" onclick="runCode()" title="Run code">
    <i class="fa-solid fa-play"></i> Run
  </button>

  <div class="nav-sep"></div>

  <!-- SECONDARY ACTIONS (icon-only — tooltips on hover) -->
  <button class="nav-btn icon-only" onclick="openShareModal()" title="Share code link">
    <i class="fa-solid fa-share-nodes"></i>
  </button>
  <button class="nav-btn icon-only" onclick="toggleTheme()" title="Toggle light/dark theme" id="themeBtn">
    <i class="fa-solid fa-moon" id="themeIcon"></i>
  </button>
  <button class="nav-btn icon-only" onclick="openRunHistoryModal()" title="View run history">
    <i class="fa-solid fa-clock-rotate-left"></i>
  </button>
  <button class="nav-btn icon-only" onclick="openFoldersModal()" title="Folder manager">
    <i class="fa-solid fa-folder-tree"></i>
  </button>
  <button class="nav-btn icon-only" onclick="openStatsModal()" title="Statistics">
    <i class="fa-solid fa-chart-bar"></i>
  </button>
  <a href="/history">
    <button class="nav-btn icon-only" title="Code history snapshots">
      <i class="fa-solid fa-code-branch"></i>
    </button>
  </a>

  <div class="nav-sep"></div>

  <form method="POST" action="/logout" style="margin:0">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <button class="nav-btn danger">
      <i class="fa-solid fa-right-from-bracket"></i> Logout
    </button>
  </form>

</div>


<!-- ═══════════════════════════════════════════════════════
     APP LAYOUT
═══════════════════════════════════════════════════════ -->
<div class="app-layout">

  <!-- SIDEBAR -->
  <div class="sidebar" id="mainSidebar">
    <div class="sidebar-header">
      <i class="fa-solid fa-folder-open"></i> Explorer
    </div>
    <div class="file-list" id="files"></div>
    <button class="new-file-btn" onclick="newFile()">
      <i class="fa-solid fa-plus"></i> New File
    </button>
  </div>

  <!-- EDITOR AREA -->
  <div class="editor-area">

    <!-- Open tabs -->
    <div id="tabs" class="tabs-row"></div>

    <!-- Editor toolbar -->
    <div class="editor-toolbar">
      <button class="tool-btn" id="searchToggleBtn" onclick="toggleSearchBar()">
        <i class="fa-solid fa-magnifying-glass"></i> Search
      </button>
      <button class="tool-btn" onclick="formatCode()">
        <i class="fa-solid fa-align-left"></i> Format
      </button>
      <button class="tool-btn" onclick="explainCode()">
        <i class="fa-solid fa-comment-dots"></i> Explain
      </button>
      <button class="tool-btn" onclick="downloadFile()">
        <i class="fa-solid fa-download"></i> Download
      </button>
      <button class="tool-btn" id="errHighlightBtn" onclick="toggleErrorHighlight()">
        <i class="fa-solid fa-triangle-exclamation"></i> Errors
      </button>
      <div class="toolbar-stats">
        <span><span id="statLines">1</span>L</span>
        <span><span id="statChars">0</span>C</span>
        <span><span id="statWords">0</span>W</span>
      </div>
    </div>

    <!-- Search & Replace (hidden until toggled) -->
    <div class="search-bar" id="searchBar">
      <input type="text" class="search-input" id="searchInput" placeholder="Find…" oninput="performSearch()" />
      <input type="text" class="search-input" id="replaceInput" placeholder="Replace…" />
      <button class="search-action-btn" onclick="performReplace()">Replace</button>
      <button class="search-action-btn" onclick="replaceAll()">All</button>
      <span class="search-count" id="searchCount"></span>
      <button class="search-action-btn" onclick="toggleSearchBar()"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <!-- Monaco + preview split -->
    <div class="split-container" id="splitContainer">

      <div class="editor-box" id="editorBox">
        <div id="editor"></div>

        <!-- Preview sync status -->
        <div class="preview-status-bar" id="previewStatusBar">
          <i class="fa-solid fa-circle-check status-ok" id="previewStatusIcon"></i>
          <span id="previewStatusText">Preview synced</span>
          <span style="margin-left:auto;opacity:0.5" id="previewTimestamp"></span>
        </div>

        <!-- AI autocomplete popup -->
        <div id="autocomplete-popup">
          <div class="popup-header">
            <div class="popup-spinner" id="popup-spinner"></div>
            <span id="popup-label">AI suggestions</span>
          </div>
          <div class="popup-items" id="popup-items"></div>
          <div class="popup-footer">
            <span><kbd>↑</kbd><kbd>↓</kbd> navigate</span>
            <span><kbd>↵</kbd>/<kbd>⇥</kbd> accept</span>
            <span><kbd>Esc</kbd> dismiss</span>
          </div>
        </div>
      </div>

      <!-- Preview resize divider -->
      <div id="preview-divider"></div>

      <!-- Live Preview Panel -->
      <div class="preview-box" id="previewBox">
        <div class="preview-header">
          <i class="fa-solid fa-display" style="color:var(--green);font-size:10px"></i>
          Live Preview
          <div class="live-dot"></div>
        </div>
        <div class="preview-controls">
          <button class="preview-tab-btn active" id="tabHtml" onclick="setPreviewTab('html')">
            <i class="fa-brands fa-html5" style="color:#fb923c;margin-right:3px"></i> HTML/CSS/JS
          </button>
          <button class="preview-tab-btn" id="tabReact" onclick="setPreviewTab('react')">
            <i class="fa-brands fa-react" style="color:#38bdf8;margin-right:3px"></i> React
          </button>
          <button class="preview-refresh" onclick="refreshPreview()">
            <i class="fa-solid fa-arrows-rotate"></i> Refresh
          </button>
        </div>
        <div class="preview-frame-wrap" id="htmlPreviewWrap">
          <div class="preview-loading" id="previewLoading">
            <div class="preview-spinner-ring"></div>
            <span>Rendering…</span>
          </div>
          <iframe id="preview-iframe" sandbox="allow-scripts allow-same-origin"></iframe>
        </div>
        <div class="react-preview-wrap" id="reactPreviewWrap" style="display:none">
          <iframe id="react-preview-frame" sandbox="allow-scripts"></iframe>
        </div>
      </div>
    </div>
  </div>

  <!-- Main resize divider -->
  <div id="main-divider"></div>

  <!-- AI PANEL -->
  <div class="ai-panel">
    <div class="panel-section" style="flex:1.4">
      <div class="panel-header">
        <i class="fa-solid fa-brain" style="color:#a78bfa"></i>
        AI Response
        <div class="panel-dot" id="aiDot"></div>
      </div>
      <div class="panel-body">
        <div class="shimmer" id="aiShimmer">
          <div class="shimmer-line" style="width:85%"></div>
          <div class="shimmer-line" style="width:65%"></div>
          <div class="shimmer-line" style="width:75%"></div>
          <div class="shimmer-line" style="width:50%"></div>
        </div>
        <pre id="result" class="empty-hint">— ask AI to suggest improvements —</pre>
      </div>
    </div>
    <div class="panel-section">
      <div class="panel-header">
        <i class="fa-solid fa-terminal" style="color:var(--cyan)"></i>
        Output
        <div class="panel-dot" id="runDot"></div>
      </div>
      <div class="panel-body">
        <div class="shimmer" id="runShimmer">
          <div class="shimmer-line" style="width:72%"></div>
          <div class="shimmer-line" style="width:52%"></div>
        </div>
        <pre id="output" contenteditable="true" spellcheck="false">type input here then press Run</pre>
      </div>
    </div>
  </div>

</div>


<!-- ═══════════════════════════════════════════════════════
     MODALS
═══════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="statsModal">
  <div class="modal" style="width:580px">
    <div class="modal-header">
      <i class="fa-solid fa-chart-bar" style="color:#a78bfa"></i>
      <h2>Statistics</h2>
      <button class="modal-close" onclick="closeModal('statsModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" id="statsModalBody"></div>
  </div>
</div>

<div class="modal-overlay" id="foldersModal">
  <div class="modal" style="width:440px">
    <div class="modal-header">
      <i class="fa-solid fa-folder-tree" style="color:var(--amber)"></i>
      <h2>Folder Manager</h2>
      <button class="modal-close" onclick="closeModal('foldersModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" id="foldersModalBody"></div>
  </div>
</div>

<div class="modal-overlay" id="runHistoryModal">
  <div class="modal" style="width:580px">
    <div class="modal-header">
      <i class="fa-solid fa-clock-rotate-left" style="color:var(--green)"></i>
      <h2>Run History</h2>
      <button class="modal-close" onclick="closeModal('runHistoryModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" id="runHistoryModalBody"></div>
  </div>
</div>

<div class="modal-overlay" id="explainModal">
  <div class="modal" style="width:620px">
    <div class="modal-header">
      <i class="fa-solid fa-comment-dots" style="color:#ec4899"></i>
      <h2>AI Code Explanation</h2>
      <button class="modal-close" onclick="closeModal('explainModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <pre id="explainContent" class="explain-content loading">Asking AI to explain your code…</pre>
    </div>
  </div>
</div>

<div class="modal-overlay" id="shareModal">
  <div class="modal" style="width:460px">
    <div class="modal-header">
      <i class="fa-solid fa-share-nodes" style="color:var(--cyan)"></i>
      <h2>Share Code</h2>
      <button class="modal-close" onclick="closeModal('shareModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <div class="share-link-row">
        <i class="fa-solid fa-link" style="color:var(--text-3)"></i>
        <span id="shareLinkText" style="flex:1">Generating link…</span>
        <button class="share-copy-btn" onclick="copyShareLink()"><i class="fa-solid fa-copy"></i> Copy</button>
      </div>
      <div class="share-info-row">
        <i class="fa-solid fa-lock" style="color:var(--green)"></i>
        Read-only — recipients can view but not edit.
      </div>
      <div class="share-info-row">
        <i class="fa-solid fa-code" style="color:var(--accent)"></i>
        File: <strong id="shareFileName" style="color:var(--cyan);margin-left:4px">—</strong>
        &nbsp;·&nbsp; Language: <strong id="shareLang" style="color:#a78bfa;margin-left:4px">—</strong>
      </div>
    </div>
  </div>
</div>

<!-- TOAST -->
<div id="toast">
  <i class="fa-solid fa-circle-check" id="toastIcon"></i>
  <span id="toastMsg">Done</span>
</div>

<!-- Monaco loader -->
<script src="https://unpkg.com/monaco-editor@latest/min/vs/loader.js"></script>

<script>
/* ═══════════════════════════════════════════════════════════════
   FILE SYSTEM STATE
   files:       filename → content map
   openTabs:    list of open tab filenames
   currentFile: active file
═══════════════════════════════════════════════════════════════ */
let files       = { "main.py": "print('hello world')" };
let openTabs    = ["main.py"];
let currentFile = "main.py";

/* ═══════════════════════════════════════════════════════════════
   FOLDERS — logical file groupings (not OS folders)
═══════════════════════════════════════════════════════════════ */
let folders = { "Project": ["main.py"] };

/* ═══════════════════════════════════════════════════════════════
   RUN HISTORY — capped at 50 entries, newest first in modal
═══════════════════════════════════════════════════════════════ */
let runHistory = [];
const RUN_HISTORY_MAX = 50;

/* ═══════════════════════════════════════════════════════════════
   AUTO-SAVE — debounced 1.5s after last keystroke
═══════════════════════════════════════════════════════════════ */
let autoSaveTimer = null;
let savedFiles    = JSON.parse(JSON.stringify(files));
const AUTO_SAVE_DELAY = 1500;

/* ═══════════════════════════════════════════════════════════════
   LIVE PREVIEW STATE
═══════════════════════════════════════════════════════════════ */
let previewOpen      = false;
let previewTab       = 'html';
let livePreviewTimer = null;
const LIVE_DELAY     = 600;

/* THEME — dark by default */
let isDarkMode = true;

/* ERROR HIGHLIGHT */
let errorHighlightEnabled = false;
let errorDecorations      = [];

/* SEARCH */
let searchOpen    = false;
let searchMatches = [];

/* ═══════════════════════════════════════════════════════════════
   TOAST — bottom-right notification
   type: 'success' | 'info' | 'error'
═══════════════════════════════════════════════════════════════ */
let toastTimer = null;
function showToast(msg, type = 'success') {
  const el = document.getElementById('toast');
  document.getElementById('toastIcon').className = {
    success: 'fa-solid fa-circle-check',
    info:    'fa-solid fa-circle-info',
    error:   'fa-solid fa-circle-xmark',
  }[type] || 'fa-solid fa-circle-check';
  document.getElementById('toastMsg').textContent = msg;
  el.className = 'show ' + type;
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => { el.className = ''; }, 3000);
}

/* ═══════════════════════════════════════════════════════════════
   MODAL HELPERS
═══════════════════════════════════════════════════════════════ */
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }

/* Close modal when clicking the dark backdrop */
document.querySelectorAll('.modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', (e) => { if (e.target === overlay) overlay.classList.remove('open'); });
});

/* ═══════════════════════════════════════════════════════════════
   FILE ICON HELPERS
═══════════════════════════════════════════════════════════════ */
function getIconClass(name) {
  const ext = name.split('.').pop().toLowerCase();
  const brands = { js:'fa-brands fa-js', py:'fa-brands fa-python', php:'fa-brands fa-php', html:'fa-brands fa-html5', css:'fa-brands fa-css3-alt', java:'fa-brands fa-java', jsx:'fa-brands fa-react', tsx:'fa-brands fa-react' };
  const solid  = { json:'fa-solid fa-code', ts:'fa-solid fa-code', rs:'fa-solid fa-code', go:'fa-solid fa-code', cpp:'fa-solid fa-file-code', cs:'fa-solid fa-file-code' };
  return brands[ext] || solid[ext] || 'fa-solid fa-file-code';
}
function getIconColor(name) {
  const ext = name.split('.').pop().toLowerCase();
  const map = { js:'#fbbf24', py:'#60a5fa', php:'#a78bfa', html:'#fb923c', css:'#38bdf8', json:'#34d399', ts:'#38bdf8', rs:'#fb923c', go:'#22d3ee', java:'#f87171', jsx:'#38bdf8', tsx:'#38bdf8' };
  return map[ext] || '#7a8ba8';
}

/* ═══════════════════════════════════════════════════════════════
   DETECT PREVIEW TYPE from current language / file extension
═══════════════════════════════════════════════════════════════ */
function getPreviewType() {
  const lang = document.getElementById('language').value;
  const ext  = currentFile.split('.').pop().toLowerCase();
  if (lang === 'jsx' || ext === 'jsx' || ext === 'tsx') return 'react';
  if (['html','css','javascript','js'].includes(lang) || ['html','css','js','ts'].includes(ext)) return 'web';
  return 'none';
}

/* ═══════════════════════════════════════════════════════════════
   RENDER SIDEBAR FILES
═══════════════════════════════════════════════════════════════ */
function renderFiles() {
  let html = '';
  for (let name in files) {
    const isUnsaved = savedFiles[name] !== files[name];
    html += `
      <div class="file-item ${name === currentFile ? 'active' : ''} ${isUnsaved ? 'unsaved' : ''}" onclick="openFile('${name}')">
        <i class="${getIconClass(name)}" style="color:${getIconColor(name)}"></i>
        <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${name}</span>
        <span class="unsaved-dot" title="Unsaved changes"></span>
      </div>`;
  }
  document.getElementById("files").innerHTML = html;
}

/* ═══════════════════════════════════════════════════════════════
   RENDER TABS
═══════════════════════════════════════════════════════════════ */
function renderTabs() {
  let html = '';
  openTabs.forEach(file => {
    const isUnsaved = savedFiles[file] !== files[file];
    html += `
      <div class="tab ${file === currentFile ? 'active' : ''} ${isUnsaved ? 'unsaved' : ''}" onclick="switchTab('${file}')">
        <i class="${getIconClass(file)}" style="color:${getIconColor(file)};font-size:10px"></i>
        <span>${file}</span>
        <span class="tab-dot" title="Unsaved"></span>
        <span class="tab-close" onclick="closeTab(event,'${file}')"><i class="fa-solid fa-xmark"></i></span>
      </div>`;
  });
  document.getElementById("tabs").innerHTML = html;
}

/* Open a file — add to tabs if not already open */
function openFile(name) {
  if (!openTabs.includes(name)) openTabs.push(name);
  currentFile = name;
  editor.setValue(files[name]);
  renderTabs(); renderFiles(); syncPreviewToLanguage(); updateStats();
}

/* Switch to a tab that's already open */
function switchTab(name) {
  currentFile = name;
  editor.setValue(files[name]);
  renderTabs(); renderFiles(); syncPreviewToLanguage(); updateStats();
}

/* Close a tab, falling back to another open tab */
function closeTab(e, name) {
  e.stopPropagation();
  openTabs = openTabs.filter(f => f !== name);
  if (!openTabs.length) openTabs = [Object.keys(files)[0]];
  if (currentFile === name) { currentFile = openTabs[0]; editor.setValue(files[currentFile] || ''); }
  renderTabs(); renderFiles();
}

/* Prompt for name and create a new empty file */
function newFile() {
  const name = prompt("File name (e.g. index.html):");
  if (name) {
    files[name] = ''; savedFiles[name] = '';
    openTabs.push(name); currentFile = name;
    editor.setValue('');
    renderFiles(); renderTabs();
    showToast('Created ' + name, 'info');
  }
}

/* ═══════════════════════════════════════════════════════════════
   LIVE STATS — lines / chars / words
═══════════════════════════════════════════════════════════════ */
function updateStats() {
  const code = window.editor ? editor.getValue() : '';
  document.getElementById('statLines').textContent = code.split('\n').length;
  document.getElementById('statChars').textContent = code.length;
  document.getElementById('statWords').textContent = code.trim() ? code.trim().split(/\s+/).length : 0;
}

/* ═══════════════════════════════════════════════════════════════
   AUTO-SAVE — 1.5s debounce after last keystroke
═══════════════════════════════════════════════════════════════ */
function triggerAutoSave() {
  const pill = document.getElementById('autosavePill');
  const txt  = document.getElementById('autosaveText');
  pill.className = 'autosave-pill saving'; txt.textContent = 'Saving…';
  clearTimeout(autoSaveTimer);
  autoSaveTimer = setTimeout(() => {
    savedFiles[currentFile] = files[currentFile];
    try { localStorage.setItem('aieditor_files', JSON.stringify(files)); } catch(e) {}
    pill.className = 'autosave-pill saved'; txt.textContent = 'Saved ✓';
    renderTabs(); renderFiles();
    setTimeout(() => { pill.className = 'autosave-pill'; txt.textContent = 'Auto-save on'; }, 2200);
  }, AUTO_SAVE_DELAY);
}

/* Restore files from localStorage on page load */
function restoreFromStorage() {
  try {
    const saved = localStorage.getItem('aieditor_files');
    if (saved) { const p = JSON.parse(saved); Object.assign(files, p); savedFiles = JSON.parse(JSON.stringify(files)); }
  } catch(e) {}
}

/* ═══════════════════════════════════════════════════════════════
   LIVE PREVIEW
═══════════════════════════════════════════════════════════════ */
function togglePreview() {
  previewOpen = !previewOpen;
  const box   = document.getElementById('previewBox');
  const divEl = document.getElementById('preview-divider');
  const btn   = document.getElementById('previewBtn');
  const icon  = document.getElementById('previewIcon');
  const txt   = document.getElementById('previewBtnText');
  const sb    = document.getElementById('previewStatusBar');

  if (previewOpen) {
    box.classList.add('open'); divEl.classList.add('visible');
    btn.classList.add('preview-on'); icon.className = 'fa-solid fa-eye-slash'; txt.textContent = 'Hide';
    sb.classList.add('visible');
    syncPreviewToLanguage(); updatePreview();
  } else {
    box.classList.remove('open'); divEl.classList.remove('visible');
    btn.classList.remove('preview-on'); icon.className = 'fa-solid fa-eye'; txt.textContent = 'Preview';
    sb.classList.remove('visible');
  }
}

/* Auto-switch sub-tab to React or HTML based on file type */
function syncPreviewToLanguage() { setPreviewTab(getPreviewType() === 'react' ? 'react' : 'html'); }

/* Toggle between HTML/CSS/JS and React preview panes */
function setPreviewTab(tab) {
  previewTab = tab;
  document.getElementById('tabHtml').classList.toggle('active', tab === 'html');
  document.getElementById('tabReact').classList.toggle('active', tab === 'react');
  document.getElementById('htmlPreviewWrap').style.display  = tab === 'html'  ? '' : 'none';
  document.getElementById('reactPreviewWrap').style.display = tab === 'react' ? '' : 'none';
  if (previewOpen) updatePreview();
}

/* Force-refresh the preview */
function refreshPreview() { updatePreview(true); }

/* Update preview content — dispatches to HTML or React renderer */
function updatePreview(force = false) {
  if (!previewOpen && !force) return;
  const code = window.editor ? editor.getValue() : '';
  const lang = document.getElementById('language').value;
  if (previewTab === 'react') updateReactPreview(code);
  else updateHtmlPreview(code, lang);
  const now = new Date();
  document.getElementById('previewTimestamp').textContent = now.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit',second:'2-digit'});
  document.getElementById('previewStatusIcon').className = 'fa-solid fa-circle-check status-ok';
  document.getElementById('previewStatusText').textContent = 'Preview synced';
}

/* Render HTML / CSS / JS in sandboxed iframe */
function updateHtmlPreview(code, lang) {
  const loading = document.getElementById('previewLoading');
  loading.classList.remove('hidden');
  let srcdoc = '';
  if (lang === 'html') {
    srcdoc = code;
  } else if (lang === 'css') {
    srcdoc = `<!DOCTYPE html><html><head><style>body{font-family:Arial,sans-serif;padding:30px;margin:0;}${code}</style></head><body>
      <h1 class="demo-heading">CSS Preview</h1>
      <p class="demo-text">This is a paragraph to demonstrate your styles.</p>
      <div class="box" style="width:120px;height:120px;background:steelblue;margin:20px 0;"></div>
      <button class="demo-btn">Sample Button</button></body></html>`;
  } else if (lang === 'javascript') {
    srcdoc = `<!DOCTYPE html><html><head><style>body{font-family:monospace;background:#1a1a2e;color:#e2eaff;padding:20px;font-size:13px;}</style></head><body>
      <div id="output"></div>
      <script>(function(){const out=document.getElementById('output');const ol=console.log;console.log=function(...a){ol(...a);const d=document.createElement('div');d.style.cssText='padding:3px 0;border-bottom:1px solid rgba(255,255,255,0.06)';d.textContent=a.map(x=>typeof x==='object'?JSON.stringify(x,null,2):String(x)).join(' ');out.appendChild(d);};try{${code}}catch(e){const d=document.createElement('div');d.style.color='#f06a6a';d.textContent='Error: '+e.message;out.appendChild(d);}})();<\/script></body></html>`;
  } else {
    srcdoc = `<html><body style="font-family:monospace;padding:20px;background:#0f1117;color:#7a8ba8;font-size:13px;"><p>Live preview is available for HTML, CSS, JavaScript, and React (JSX).</p></body></html>`;
  }
  const iframe = document.getElementById('preview-iframe');
  iframe.srcdoc = srcdoc;
  iframe.onload = () => loading.classList.add('hidden');
  setTimeout(() => loading.classList.add('hidden'), 1500);
}

/* Render React/JSX component using Babel + React CDN in iframe */
function updateReactPreview(code) {
  document.getElementById('react-preview-frame').srcdoc = `<!DOCTYPE html>
<html><head><meta charset="UTF-8">
<style>body{margin:0;padding:0;font-family:system-ui,sans-serif;}#root{min-height:100vh;}
.react-error{background:#1a0a0a;color:#f06a6a;padding:16px;font-family:monospace;font-size:12px;white-space:pre-wrap;border-left:3px solid #f06a6a;margin:12px;}</style>
</head><body><div id="root"></div>
<script src="https://unpkg.com/react@18/umd/react.development.js"><\/script>
<script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"><\/script>
<script src="https://unpkg.com/@babel/standalone/babel.min.js"><\/script>
<script type="text/babel">
try{${code}const r=ReactDOM.createRoot(document.getElementById('root'));
if(typeof App!=='undefined')r.render(React.createElement(App));
else document.getElementById('root').innerHTML='<div class="react-error">No App component found.</div>';
}catch(e){document.getElementById('root').innerHTML='<div class="react-error">React Error:\\n'+e.message+'<\/div>';}
<\/script></body></html>`;
}

/* ─────────────────────────────
   PREVIEW RESIZE DIVIDER (drag)
───────────────────────────── */
const prevDiv = document.getElementById('preview-divider');
let isPrevDrag = false;
prevDiv.addEventListener('mousedown', () => { isPrevDrag = true; prevDiv.classList.add('dragging'); });
document.addEventListener('mousemove', (e) => {
  if (!isPrevDrag) return;
  const split  = document.getElementById('splitContainer');
  const rect   = split.getBoundingClientRect();
  const leftW  = e.clientX - rect.left - 2;
  const rightW = rect.width - leftW - 4;
  const edBox  = document.getElementById('editorBox');
  const pvBox  = document.getElementById('previewBox');
  if (leftW > 240 && rightW > 190) {
    edBox.style.flex = 'none'; edBox.style.width = leftW + 'px';
    pvBox.style.width = rightW + 'px'; pvBox.style.minWidth = '0';
  }
});
document.addEventListener('mouseup', () => { isPrevDrag = false; prevDiv.classList.remove('dragging'); });

/* ═══════════════════════════════════════════════════════════════
   MONACO EDITOR
═══════════════════════════════════════════════════════════════ */
require.config({ paths: { vs: 'https://unpkg.com/monaco-editor@latest/min/vs' } });

require(['vs/editor/editor.main'], function () {

  restoreFromStorage(); /* load persisted files from localStorage */

  window.editor = monaco.editor.create(document.getElementById('editor'), {
    value:                      files[currentFile],
    language:                   'python',
    theme:                      'vs-dark',
    automaticLayout:            true,
    minimap:                    { enabled: false },
    fontSize:                   13,
    fontFamily:                 "'JetBrains Mono', monospace",
    fontLigatures:              true,
    lineHeight:                 22,
    padding:                    { top: 14, bottom: 14 },
    scrollbar:                  { verticalScrollbarSize: 4, horizontalScrollbarSize: 4 },
    renderLineHighlight:        'gutter',
    cursorBlinking:             'expand',
    cursorSmoothCaretAnimation: 'on',
    smoothScrolling:            true,
    bracketPairColorization:    { enabled: true },
    quickSuggestions:           false,
    suggestOnTriggerCharacters: false,
    wordBasedSuggestions:       'off',
  });

  /* ── AUTOCOMPLETE POPUP elements ── */
  const popup      = document.getElementById('autocomplete-popup');
  const popupItems = document.getElementById('popup-items');
  const popupLabel = document.getElementById('popup-label');
  const popupSpin  = document.getElementById('popup-spinner');
  let acTimer = null, acCtrl = null, acList = [], acIdx = -1, acPos = null;

  /* Position popup just below the cursor line */
  function positionPopup() {
    const pos = editor.getPosition();
    if (!pos) return;
    const coords = editor.getScrolledVisiblePosition(pos);
    if (!coords) return;
    const layout  = editor.getLayoutInfo();
    const tabsH   = document.getElementById('tabs').offsetHeight;
    const edW     = document.getElementById('editorBox').offsetWidth;
    const POP_W   = 270;
    let left = Math.min(Math.max(6, layout.contentLeft + coords.left), edW - POP_W - 8);
    let top  = tabsH + coords.top + 24;
    popup.style.left = left + 'px'; popup.style.top = top + 'px';
  }

  /* Re-render popup items with current selection highlighted */
  function renderPopupItems() {
    popupItems.innerHTML = '';
    acList.forEach((text, i) => {
      const row = document.createElement('div');
      row.className = 'popup-item' + (i === acIdx ? ' selected' : '');
      row.innerHTML = `<span class="popup-item-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></span>
        <span class="popup-item-code">${escHtml(text.split('\n')[0])}</span>`;
      row.addEventListener('mousedown', (e) => { e.preventDefault(); acceptSuggestion(i); });
      popupItems.appendChild(row);
    });
  }

  /* Show popup with given suggestions list */
  function showPopup(suggestions, position) {
    acList = suggestions; acIdx = 0; acPos = position;
    popupSpin.style.display = 'none';
    popupLabel.textContent = suggestions.length === 1 ? '1 suggestion' : `${suggestions.length} suggestions`;
    renderPopupItems(); positionPopup(); popup.style.display = 'flex';
  }

  /* Hide and reset the autocomplete popup */
  function hidePopup() { popup.style.display = 'none'; acList = []; acIdx = -1; }

  /* Insert chosen suggestion text at cursor position */
  function acceptSuggestion(index) {
    const text = acList[index];
    if (!text || !acPos) return;
    editor.executeEdits('ai-autocomplete', [{
      range: new monaco.Range(acPos.lineNumber, acPos.column, acPos.lineNumber, acPos.column), text,
    }]);
    const lines = text.split('\n');
    editor.setPosition({
      lineNumber: acPos.lineNumber + lines.length - 1,
      column: lines.length > 1 ? lines[lines.length-1].length + 1 : acPos.column + text.length,
    });
    editor.focus(); hidePopup();
  }

  /* Simple HTML entity escaper */
  function escHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  /* Fetch AI autocomplete suggestions from backend */
  async function fetchAutocomplete(position) {
    if (acCtrl) acCtrl.abort();
    acCtrl = new AbortController();
    const model    = editor.getModel();
    const language = document.getElementById('language').value;
    const context  = model.getValueInRange({startLineNumber:1,startColumn:1,endLineNumber:position.lineNumber,endColumn:position.column});
    const fullCode = model.getValue();
    if (fullCode.trim().length < 3) { hidePopup(); return; }

    popupSpin.style.display = 'block'; popupLabel.textContent = 'thinking…';
    popupItems.innerHTML = ''; positionPopup(); popup.style.display = 'flex';

    try {
      const res = await fetch('/autocomplete', {
        method: 'POST', signal: acCtrl.signal,
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ code: context, full_code: fullCode, language }),
      });
      if (!res.ok) { hidePopup(); return; }
      const data = await res.json();
      let suggestions = [];
      if (Array.isArray(data.suggestions) && data.suggestions.length) suggestions = data.suggestions.map(s=>s.trim()).filter(Boolean);
      else if (typeof data.suggestion === 'string' && data.suggestion.trim()) suggestions = [data.suggestion.trim()];
      if (!suggestions.length) { hidePopup(); return; }
      showPopup(suggestions, position);
    } catch(e) { if (e.name !== 'AbortError') hidePopup(); }
  }

  /* ── Content change: sync state, auto-save, autocomplete, preview ── */
  editor.onDidChangeModelContent(() => {
    files[currentFile] = editor.getValue();
    updateStats(); renderTabs(); triggerAutoSave();
    if (errorHighlightEnabled) runErrorHighlight();
    hidePopup(); clearTimeout(acTimer);
    const pos = editor.getPosition();
    acTimer = setTimeout(() => fetchAutocomplete(pos), 900);
    if (previewOpen) { clearTimeout(livePreviewTimer); livePreviewTimer = setTimeout(() => updatePreview(), LIVE_DELAY); }
  });

  /* ── Keyboard navigation for autocomplete popup ── */
  editor.onKeyDown((e) => {
    if (popup.style.display !== 'flex') return;
    if (e.keyCode === monaco.KeyCode.UpArrow) {
      e.preventDefault(); e.stopPropagation(); acIdx = Math.max(0, acIdx-1); renderPopupItems();
    } else if (e.keyCode === monaco.KeyCode.DownArrow) {
      e.preventDefault(); e.stopPropagation(); acIdx = Math.min(acList.length-1, acIdx+1); renderPopupItems();
    } else if (e.keyCode === monaco.KeyCode.Enter || e.keyCode === monaco.KeyCode.Tab) {
      e.preventDefault(); e.stopPropagation(); if (acIdx >= 0) acceptSuggestion(acIdx);
    } else if (e.keyCode === monaco.KeyCode.Escape) {
      e.preventDefault(); e.stopPropagation(); hidePopup();
    }
  });

  editor.onDidBlurEditorWidget(() => hidePopup());
  updateStats();
});

/* ═══════════════════════════════════════════════════════════════
   changeLanguage — update Monaco's syntax highlighting mode
═══════════════════════════════════════════════════════════════ */
function changeLanguage() {
  const lang = document.getElementById('language').value;
  if (window.editor) monaco.editor.setModelLanguage(editor.getModel(), lang === 'jsx' ? 'javascript' : lang);
  if (previewOpen) { syncPreviewToLanguage(); updatePreview(); }
}

/* ═══════════════════════════════════════════════════════════════
   toggleTheme — switch between Monaco dark and light themes
═══════════════════════════════════════════════════════════════ */
function toggleTheme() {
  isDarkMode = !isDarkMode;
  document.getElementById('themeIcon').className = isDarkMode ? 'fa-solid fa-moon' : 'fa-solid fa-sun';
  monaco.editor.setTheme(isDarkMode ? 'vs-dark' : 'vs');
  showToast(isDarkMode ? 'Dark mode' : 'Light mode', 'info');
}

/* ═══════════════════════════════════════════════════════════════
   toggleSearchBar — show/hide find & replace bar
═══════════════════════════════════════════════════════════════ */
function toggleSearchBar() {
  searchOpen = !searchOpen;
  document.getElementById('searchBar').classList.toggle('visible', searchOpen);
  document.getElementById('searchToggleBtn').classList.toggle('active', searchOpen);
  if (searchOpen) document.getElementById('searchInput').focus();
  else { if (window.editor) editor.deltaDecorations([], []); document.getElementById('searchCount').textContent = ''; searchMatches = []; }
}

/* Highlight all occurrences of the search query */
function performSearch() {
  if (!window.editor) return;
  const query = document.getElementById('searchInput').value;
  const model = editor.getModel();
  if (!query) { editor.deltaDecorations(searchMatches, []); searchMatches = []; document.getElementById('searchCount').textContent = ''; return; }
  const matches = model.findMatches(query, false, false, false, null, true);
  document.getElementById('searchCount').textContent = matches.length ? `${matches.length} match${matches.length>1?'es':''}` : 'No matches';
  searchMatches = editor.deltaDecorations(searchMatches, matches.map(m=>({range:m.range,options:{inlineClassName:'search-highlight'}})));
}

/* Replace first occurrence */
function performReplace() {
  if (!window.editor) return;
  const query   = document.getElementById('searchInput').value;
  const replace = document.getElementById('replaceInput').value;
  if (!query) return;
  const match = editor.getModel().findNextMatch(query, editor.getPosition(), false, false, null, true);
  if (match) { editor.executeEdits('replace',[{range:match.range,text:replace}]); performSearch(); showToast('Replaced 1 occurrence','info'); }
  else showToast('No match found','error');
}

/* Replace all occurrences at once */
function replaceAll() {
  if (!window.editor) return;
  const query   = document.getElementById('searchInput').value;
  const replace = document.getElementById('replaceInput').value;
  if (!query) return;
  const matches = editor.getModel().findMatches(query, false, false, false, null, true);
  if (!matches.length) { showToast('No matches found','error'); return; }
  editor.executeEdits('replace-all', matches.slice().reverse().map(m=>({range:m.range,text:replace})));
  performSearch(); showToast(`Replaced ${matches.length} occurrence(s)`,'success');
}

/* ═══════════════════════════════════════════════════════════════
   formatCode — backend formatter or Monaco fallback
═══════════════════════════════════════════════════════════════ */
async function formatCode() {
  if (!window.editor) return;
  showToast('Formatting…','info');
  try {
    const res = await fetch('/format', {
      method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
      body:JSON.stringify({code:editor.getValue(),language:document.getElementById('language').value}),
    });
    if (res.ok) { const d=await res.json(); if(d.formatted){editor.setValue(d.formatted);showToast('Code formatted','success');return;} }
  } catch(e) {}
  editor.getAction('editor.action.formatDocument').run();
  showToast('Code formatted','success');
}

/* ═══════════════════════════════════════════════════════════════
   explainCode — AI explanation of selected text or full code
═══════════════════════════════════════════════════════════════ */
async function explainCode() {
  const el = document.getElementById('explainContent');
  el.className = 'explain-content loading'; el.textContent = 'Asking AI to explain your code…';
  openModal('explainModal');
  const sel  = window.editor ? editor.getModel().getValueInRange(editor.getSelection()) : '';
  const code = sel.trim().length > 0 ? sel : (window.editor ? editor.getValue() : '');
  try {
    const res = await fetch('/explain', {
      method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
      body:JSON.stringify({code,language:document.getElementById('language').value}),
    });
    const data = await res.json();
    el.className = 'explain-content'; el.textContent = data.result || data.explanation || '— No explanation returned —';
  } catch(e) { el.className='explain-content'; el.textContent='— Error fetching explanation. —'; }
}

/* ═══════════════════════════════════════════════════════════════
   downloadFile — save current file content to user's device
═══════════════════════════════════════════════════════════════ */
function downloadFile() {
  const blob = new Blob([window.editor ? editor.getValue() : ''], {type:'text/plain'});
  const url  = URL.createObjectURL(blob);
  const a    = document.createElement('a');
  a.href = url; a.download = currentFile;
  document.body.appendChild(a); a.click(); document.body.removeChild(a); URL.revokeObjectURL(url);
  showToast(`Downloaded ${currentFile}`,'success');
}

/* ═══════════════════════════════════════════════════════════════
   toggleErrorHighlight — Monaco gutter decorations for errors
═══════════════════════════════════════════════════════════════ */
function toggleErrorHighlight() {
  errorHighlightEnabled = !errorHighlightEnabled;
  document.getElementById('errHighlightBtn').classList.toggle('error-on', errorHighlightEnabled);
  if (errorHighlightEnabled) { runErrorHighlight(); showToast('Error highlighting on','info'); }
  else { if (window.editor) errorDecorations = editor.deltaDecorations(errorDecorations,[]); showToast('Error highlighting off','info'); }
}

/* Scan Monaco markers and add red gutter highlight on error lines */
function runErrorHighlight() {
  if (!window.editor || !errorHighlightEnabled) return;
  const model   = editor.getModel();
  const markers = monaco.editor.getModelMarkers({resource:model.uri});
  errorDecorations = editor.deltaDecorations(errorDecorations,
    markers.filter(m=>m.severity>=monaco.MarkerSeverity.Warning).map(m=>({
      range: new monaco.Range(m.startLineNumber,1,m.endLineNumber,1),
      options: {
        isWholeLine:true, glyphMarginClassName:'error-highlight-gutter',
        glyphMarginHoverMessage:{value:`**${m.severity===monaco.MarkerSeverity.Error?'Error':'Warning'}**: ${m.message}`},
        overviewRuler:{color:'#f06a6a',position:monaco.editor.OverviewRulerLane.Right},
      }
    }))
  );
}

/* ═══════════════════════════════════════════════════════════════
   openStatsModal — compute live metrics and render dashboard
═══════════════════════════════════════════════════════════════ */
function openStatsModal() {
  let totalLines=0, totalChars=0, totalWords=0; const langCount={};
  for (const [name,content] of Object.entries(files)) {
    const ext = name.split('.').pop().toLowerCase();
    totalLines += content.split('\n').length; totalChars += content.length;
    totalWords += content.trim()?content.trim().split(/\s+/).length:0;
    langCount[ext]=(langCount[ext]||0)+1;
  }
  const fileCount = Object.keys(files).length;
  const successCt = runHistory.filter(r=>!r.isError).length;
  const maxLang   = Math.max(...Object.values(langCount),1);
  const langBars  = Object.entries(langCount).map(([ext,cnt])=>{
    const colors={py:'#60a5fa',js:'#fbbf24',html:'#fb923c',css:'#38bdf8',jsx:'#22d3ee',php:'#a78bfa',java:'#f87171'};
    return `<div class="lang-bar-row">
      <span class="lang-bar-label">.${ext}</span>
      <div class="lang-bar-track"><div class="lang-bar-fill" style="width:${(cnt/maxLang*100).toFixed(0)}%;background:${colors[ext]||'#4f8ef7'}"></div></div>
      <span class="lang-bar-count">${cnt}</span></div>`;
  }).join('');
  document.getElementById('statsModalBody').innerHTML=`
    <div class="stats-grid">
      <div class="stat-card blue"><div class="stat-val">${fileCount}</div><div class="stat-label">Files</div></div>
      <div class="stat-card cyan"><div class="stat-val">${totalLines.toLocaleString()}</div><div class="stat-label">Lines</div></div>
      <div class="stat-card violet"><div class="stat-val">${totalChars.toLocaleString()}</div><div class="stat-label">Characters</div></div>
      <div class="stat-card amber"><div class="stat-val">${totalWords.toLocaleString()}</div><div class="stat-label">Words</div></div>
      <div class="stat-card green"><div class="stat-val">${successCt}</div><div class="stat-label">Successful Runs</div></div>
      <div class="stat-card red"><div class="stat-val">${runHistory.length-successCt}</div><div class="stat-label">Failed Runs</div></div>
    </div>
    <div class="section-title">File types</div>
    <div class="lang-bar-list">${langBars||'<div style="color:var(--text-3);font-size:12px">No files yet</div>'}</div>`;
  openModal('statsModal');
}

/* ═══════════════════════════════════════════════════════════════
   openFoldersModal — show logical folder groupings
═══════════════════════════════════════════════════════════════ */
function openFoldersModal() {
  let html='<div class="folder-tree">';
  for (const [fName,fFiles] of Object.entries(folders)) {
    html+=`<div class="folder-item"><i class="fa-solid fa-folder-open"></i><span>${fName}</span>
      <span class="folder-count">${fFiles.length} file${fFiles.length!==1?'s':''}</span></div>
      <div class="folder-files">
        ${fFiles.map(f=>`<div class="folder-file-item" onclick="closeModal('foldersModal');openFile('${f}')">
          <i class="${getIconClass(f)}" style="color:${getIconColor(f)}"></i>${f}</div>`).join('')}
      </div>`;
  }
  const allFFs   = Object.values(folders).flat();
  const unfiled  = Object.keys(files).filter(f=>!allFFs.includes(f));
  if (unfiled.length) {
    html+=`<div class="folder-item"><i class="fa-solid fa-folder" style="color:var(--text-3)"></i>
      <span>Unfiled</span><span class="folder-count">${unfiled.length}</span></div>
      <div class="folder-files">
        ${unfiled.map(f=>`<div class="folder-file-item" onclick="closeModal('foldersModal');openFile('${f}')">
          <i class="${getIconClass(f)}" style="color:${getIconColor(f)}"></i>${f}</div>`).join('')}
      </div>`;
  }
  html+=`</div><button class="folder-new-btn" onclick="createNewFolder()"><i class="fa-solid fa-folder-plus"></i> New Folder</button>`;
  document.getElementById('foldersModalBody').innerHTML=html;
  openModal('foldersModal');
}

/* Prompt to create a new folder, optionally add current file to it */
function createNewFolder() {
  const name=prompt("Folder name:"); if(!name) return;
  folders[name]=[];
  if(confirm(`Add "${currentFile}" to "${name}"?`)){
    for(const k of Object.keys(folders)) folders[k]=folders[k].filter(f=>f!==currentFile);
    folders[name].push(currentFile);
  }
  showToast(`Folder "${name}" created`,'success'); openFoldersModal();
}

/* ═══════════════════════════════════════════════════════════════
   openRunHistoryModal — show past executions, newest first
═══════════════════════════════════════════════════════════════ */
function openRunHistoryModal() {
  let html='';
  if (!runHistory.length) {
    html='<div class="rh-empty"><i class="fa-solid fa-clock-rotate-left" style="font-size:22px;opacity:0.2;display:block;margin-bottom:7px"></i>No runs yet</div>';
  } else {
    html='<div class="run-history-list">';
    [...runHistory].reverse().forEach((entry,i)=>{
      const realIdx=runHistory.length-1-i;
      html+=`<div class="run-history-item">
        <div class="run-history-meta">
          <span class="rh-lang">${entry.lang}</span>
          <i class="fa-solid fa-circle${entry.isError?'-xmark rh-err':'-check rh-ok'}"></i>
          <span>${entry.isError?'Error':'Success'}</span>
          <span style="margin-left:auto">${entry.timestamp}</span>
        </div>
        <div class="run-history-code">${escHtmlBasic(entry.code.substring(0,120))}${entry.code.length>120?'…':''}</div>
        <div class="run-history-output ${entry.isError?'err':'ok'}">${escHtmlBasic(String(entry.output||'').substring(0,150))}</div>
        <button class="rh-restore" onclick="restoreFromHistory(${realIdx})"><i class="fa-solid fa-arrow-rotate-left"></i> Restore</button>
      </div>`;
    });
    html+='</div>';
  }
  document.getElementById('runHistoryModalBody').innerHTML=html;
  openModal('runHistoryModal');
}

/* Load a historical code snapshot back into the editor */
function restoreFromHistory(index) {
  const entry=runHistory[index]; if(!entry) return;
  editor.setValue(entry.code); files[currentFile]=entry.code;
  closeModal('runHistoryModal'); showToast('Code restored','info');
}

function escHtmlBasic(str) {
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/* ═══════════════════════════════════════════════════════════════
   openShareModal — generate base64-encoded share link
═══════════════════════════════════════════════════════════════ */
function openShareModal() {
  const code = window.editor ? editor.getValue() : '';
  const lang = document.getElementById('language').value;
  const link = `${location.origin}/share?lang=${lang}&code=${btoa(unescape(encodeURIComponent(code)))}`;
  document.getElementById('shareLinkText').textContent = link;
  document.getElementById('shareFileName').textContent = currentFile;
  document.getElementById('shareLang').textContent     = lang;
  openModal('shareModal');
}

/* Copy share link to clipboard */
function copyShareLink() {
  const text=document.getElementById('shareLinkText').textContent;
  navigator.clipboard.writeText(text).then(()=>showToast('Link copied!','success')).catch(()=>showToast('Copy failed','error'));
}

/* ═══════════════════════════════════════════════════════════════
   LOADING HELPERS — shimmer + status dot
   type: 'ai' | 'run'
═══════════════════════════════════════════════════════════════ */
function setLoading(type, on) {
  document.getElementById(type+'Shimmer').classList.toggle('visible',on);
  const dot=document.getElementById(type+'Dot');
  on ? dot.classList.add('active') : dot.classList.remove('active');
}

/* ═══════════════════════════════════════════════════════════════
   askAI — send code to /suggest endpoint and display result
═══════════════════════════════════════════════════════════════ */
async function askAI() {
  const resultEl=document.getElementById('result');
  resultEl.className=''; resultEl.textContent=''; setLoading('ai',true);
  try {
    const res=await fetch('/suggest',{method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
      body:JSON.stringify({code:editor.getValue(),language:document.getElementById('language').value}),
    });
    const data=await res.json();
    setLoading('ai',false); resultEl.className='has-content'; resultEl.textContent=data.result;
  } catch(e) { setLoading('ai',false); resultEl.className='empty-hint'; resultEl.textContent='— error fetching suggestion —'; }
}

/* ═══════════════════════════════════════════════════════════════
   applyFix — insert AI suggestion directly into editor
═══════════════════════════════════════════════════════════════ */
function applyFix() {
  const content=document.getElementById('result').textContent;
  if(content && !content.startsWith('—')){
    editor.setValue(content); files[currentFile]=content;
    if(previewOpen) updatePreview(true); showToast('AI fix applied','success');
  } else showToast('No AI suggestion to apply','error');
}

/* ═══════════════════════════════════════════════════════════════
   runCode
   HTML/CSS/JSX → open preview panel
   All others   → POST to /run backend, record in history
═══════════════════════════════════════════════════════════════ */
async function runCode() {

  const code     = editor.getValue();
  const language = document.getElementById('language').value;

  /* get input typed in output box BEFORE clearing it */
  const programInput =
  document.getElementById('output').innerText;


  /* Renderable — show in preview panel */

  if (language==='html') {

    if(!previewOpen)
    togglePreview();

    setPreviewTab('html');

    updatePreview(true);

    document.getElementById('output').textContent =
    '— HTML rendered in preview —';

    return;

  }


  if (language==='css') {

    if(!previewOpen)
    togglePreview();

    setPreviewTab('html');

    updatePreview(true);

    document.getElementById('output').textContent =
    '— CSS rendered in preview —';

    return;

  }


  if (language==='jsx') {

    if(!previewOpen)
    togglePreview();

    setPreviewTab('react');

    updatePreview(true);

    document.getElementById('output').textContent =
    '— React rendered in preview —';

    return;

  }


  /* Server-side execution */

  setLoading('run',true);


  const outputEl =
  document.getElementById('output');


  outputEl.innerHTML = 'running...';


  const res = await fetch('/run',{

    method:'POST',

    headers:{
      'Content-Type':'application/json',

      'X-CSRF-TOKEN':
      document
      .querySelector('meta[name="csrf-token"]')
      .content
    },


    body:JSON.stringify({

      code,
      language,

      /* send input to backend */
      input: programInput

    }),

  });


  const data =
  await res.json();


  const isError =
  !!data.error;


  setLoading('run',false);


  if(isError)

    outputEl.innerHTML =
    '<span style="color:var(--red)">'+
    data.error+
    '</span>';

  else

    outputEl.textContent =
    data.output;


  /* Record in run history */

  runHistory.push({

    lang:language,

    code,

    output:isError
      ? data.error
      : data.output,

    isError,

    timestamp:
      new Date()
      .toLocaleTimeString([],
      {
        hour:'2-digit',
        minute:'2-digit',
        second:'2-digit'
      })

  });


  if(runHistory.length>RUN_HISTORY_MAX)

    runHistory.shift();

}

/* ═══════════════════════════════════════════════════════════════
   MAIN DIVIDER — drag to resize editor area vs AI panel
═══════════════════════════════════════════════════════════════ */
const mainDiv    = document.getElementById('main-divider');
const editorArea = document.querySelector('.editor-area');
let isDragging   = false;

mainDiv.addEventListener('mousedown', () => { isDragging=true; mainDiv.classList.add('dragging'); });
document.addEventListener('mousemove', (e) => {
  if(!isDragging) return;
  const layout   = document.querySelector('.app-layout');
  const sidebar  = document.getElementById('mainSidebar');
  const left     = layout.getBoundingClientRect().left + sidebar.offsetWidth;
  editorArea.style.flex='none'; editorArea.style.width=Math.max(260,e.clientX-left)+'px';
});
document.addEventListener('mouseup', () => { isDragging=false; mainDiv.classList.remove('dragging'); });

/* ── Initial render ── */
renderFiles();
renderTabs();
</script>
</body>
</html>
