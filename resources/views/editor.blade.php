<!DOCTYPE html>
<html>
<head>
<title>Stackly</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* ── DESIGN TOKENS ───────────────────────────────────────── */
:root {
  --bg:#080c14; --panel:#0e1422; --raised:#151d2e; --hover:#1c2840;
  --border:rgba(255,255,255,0.08); --bordm:rgba(255,255,255,0.15);
  --accent:#5b9cf6; --accent2:#7c6cf8; --adim:rgba(91,156,246,0.13);
  --aglow:0 0 22px rgba(91,156,246,0.3);
  --green:#4ade80; --red:#f87171; --amber:#fbbf24; --cyan:#38bdf8; --pink:#f472b6;
  --t1:#eef2fa; --t2:#8ba0c4; --t3:#45587a; --icon:#c8d8f0;
  --ui:'Inter',sans-serif; --mono:'JetBrains Mono',monospace;
  --nav:54px; --side:216px; --r:10px; --rs:6px;
}
*,*::before,*::after { box-sizing:border-box; margin:0; padding:0; }
body { font-family:var(--ui); background:var(--bg); color:var(--t1); height:100vh; overflow:hidden; font-size:14px; }
body::before {
  content:''; position:fixed; inset:0; pointer-events:none; z-index:0;
  background:radial-gradient(ellipse 80% 55% at 10% -5%,rgba(91,156,246,0.07) 0%,transparent 55%),
             radial-gradient(ellipse 55% 40% at 90% 105%,rgba(124,108,248,0.05) 0%,transparent 50%);
}

/* ── NAVBAR ──────────────────────────────────────────────── */
/* Glass navbar: semi-transparent dark bg + blur + inner highlight */
.navbar {
  position:relative; z-index:50; height:var(--nav);
  display:flex; align-items:center; gap:4px; padding:0 12px;
  background:rgba(14,20,34,0.75);
  backdrop-filter:blur(18px);
  -webkit-backdrop-filter:blur(18px);
  border-bottom:1px solid rgba(255,255,255,0.07);
  box-shadow:0 4px 24px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.06);
}
.logo { display:flex; align-items:center; gap:9px; font-size:14px; font-weight:700; margin-right:6px; color:var(--t1); }
.logo-icon { width:28px; height:28px; border-radius:8px; background:linear-gradient(135deg,var(--accent),var(--accent2)); display:flex; align-items:center; justify-content:center; font-size:12px; color:#fff; box-shadow:0 0 14px rgba(91,156,246,0.45); }
.sep { width:1px; height:22px; background:rgba(255,255,255,0.08); margin:0 5px; flex-shrink:0; }
.spacer { flex:1; }
.lang-wrap { position:relative; display:flex; align-items:center; }
.lang-wrap i { position:absolute; left:9px; font-size:11px; color:var(--icon); pointer-events:none; z-index:1; }
/* glass select — frosted look matching the navbar */
select {
  appearance:none; padding:5px 10px 5px 27px; border-radius:var(--rs);
  background:rgba(255,255,255,0.05); color:var(--t1);
  border:1px solid rgba(255,255,255,0.1);
  font-family:var(--ui); font-size:12.5px; cursor:pointer; outline:none; transition:border-color .15s;
}
select:hover { border-color:rgba(255,255,255,0.18); }
select option { background:var(--raised); }

/* save status pill */
.save-pill { display:flex; align-items:center; gap:6px; padding:4px 11px; border-radius:20px; font-size:11px; color:var(--t3); background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); white-space:nowrap; transition:color .2s,border-color .2s; }
.save-pill.saving { color:var(--amber); border-color:rgba(251,191,36,0.3); }
.save-pill.saving i { color:var(--amber); }
.save-pill.saved   { color:var(--green); border-color:rgba(74,222,128,0.3); }
.save-pill.saved i { color:var(--green); }

/* ── BUTTONS ─────────────────────────────────────────────── */
/* Standard glass button — frosted transparent base */
.btn { display:inline-flex; align-items:center; gap:6px; padding:5px 12px; border-radius:var(--rs); font-family:var(--ui); font-size:12.5px; font-weight:500; cursor:pointer; border:1px solid rgba(255,255,255,0.08); white-space:nowrap; color:var(--t2); background:rgba(255,255,255,0.04); transition:background .12s,color .12s,transform .1s,border-color .12s; }
.btn i { font-size:12px; color:var(--icon); transition:color .12s; }
.btn:hover { color:var(--t1); background:rgba(255,255,255,0.09); border-color:rgba(255,255,255,0.16); transform:translateY(-1px); }
.btn:hover i { color:#fff; }
.btn:active { transform:translateY(0); }

/* Run button — blue glass with glow */
.btn.primary { background:rgba(91,156,246,0.2); color:#a8c8fa; border:1px solid rgba(91,156,246,0.4); padding:5px 18px; font-weight:600; box-shadow:0 0 16px rgba(91,156,246,0.2),inset 0 1px 0 rgba(255,255,255,0.1); }
.btn.primary i { color:#a8c8fa; }
.btn.primary:hover { background:rgba(91,156,246,0.3); color:#fff; border-color:rgba(91,156,246,0.55); }
.btn.primary:hover i { color:#fff; }

/* Suggest button — purple glass */
.btn.suggest { color:#c4b8ff; border-color:rgba(124,108,248,0.35); background:rgba(124,108,248,0.15); }
.btn.suggest i { color:#c4b8ff; }
.btn.suggest:hover { background:rgba(124,108,248,0.25); color:#fff; }
.btn.suggest:hover i { color:#fff; }

/* Preview active state */
.btn.prev-on { color:var(--green); border-color:rgba(74,222,128,0.35); background:rgba(74,222,128,0.1); }
.btn.prev-on i { color:var(--green); }

/* Danger (logout) */
.btn.danger { color:var(--red); background:rgba(248,113,113,0.07); border-color:rgba(248,113,113,0.2); }
.btn.danger i { color:var(--red); }
.btn.danger:hover { background:rgba(248,113,113,0.15); border-color:rgba(248,113,113,0.35); color:var(--red); }

/* ── ICON BUTTON GROUP ───────────────────────────────────── */
/* All 6 icon buttons sit inside one glass pill container */
.ico-group { display:flex; align-items:center; gap:2px; padding:3px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:10px; }

/* individual icon button — 30×30 square, each has its own colour */
.btn.ico { padding:0; width:30px; height:30px; border-radius:7px; border:1px solid transparent; background:transparent; display:flex; align-items:center; justify-content:center; }
.btn.ico i { font-size:14px; transition:color .12s; }

/* unique colour per icon — makes each one instantly recognisable */
.btn.ico.i-share  i { color:#5b9cf6; }  /* blue   — share      */
.btn.ico.i-theme  i { color:#fbbf24; }  /* amber  — theme      */
.btn.ico.i-hist   i { color:#4ade80; }  /* green  — history    */
.btn.ico.i-folder i { color:#fbbf24; }  /* amber  — folders    */
.btn.ico.i-stats  i { color:#a78bfa; }  /* purple — statistics */
.btn.ico.i-git    i { color:#38bdf8; }  /* cyan   — git        */

/* hover: coloured glass tint matching the icon */
.btn.ico.i-share:hover  { background:rgba(91,156,246,0.15);  border-color:rgba(91,156,246,0.3);  }
.btn.ico.i-theme:hover  { background:rgba(251,191,36,0.12);  border-color:rgba(251,191,36,0.28); }
.btn.ico.i-hist:hover   { background:rgba(74,222,128,0.1);   border-color:rgba(74,222,128,0.25); }
.btn.ico.i-folder:hover { background:rgba(251,191,36,0.12);  border-color:rgba(251,191,36,0.28); }
.btn.ico.i-stats:hover  { background:rgba(167,139,250,0.12); border-color:rgba(167,139,250,0.28);}
.btn.ico.i-git:hover    { background:rgba(56,189,248,0.12);  border-color:rgba(56,189,248,0.28); }

/* ── LAYOUT ──────────────────────────────────────────────── */
.app { position:relative; z-index:1; display:flex; height:calc(100vh - var(--nav)); overflow:hidden; }

/* ── SIDEBAR ─────────────────────────────────────────────── */
.sidebar { width:var(--side); flex-shrink:0; background:var(--panel); border-right:1px solid var(--border); display:flex; flex-direction:column; overflow:hidden; }
.sidebar-hdr { display:flex; align-items:center; padding:11px 14px 9px; gap:8px; font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--t2); border-bottom:1px solid var(--border); flex-shrink:0; }
.sidebar-hdr i { font-size:12px; color:var(--amber); }
.file-list { flex:1; overflow-y:auto; padding:5px; }

/* file row — rename/delete buttons shown on hover via CSS */
.file-item { display:flex; align-items:center; gap:8px; padding:6px 10px; border-radius:var(--rs); cursor:pointer; font-size:12px; color:var(--t2); border:1px solid transparent; position:relative; transition:background .1s,color .1s; }
.file-item:hover  { background:var(--hover); color:var(--t1); }
.file-item.active { background:var(--adim); color:var(--accent); border-color:rgba(91,156,246,0.25); }
.file-item i { font-size:12px; flex-shrink:0; }
.file-item .dot { width:5px; height:5px; border-radius:50%; background:var(--amber); margin-left:auto; display:none; }
.file-item.unsaved .dot { display:block; }

/* context action buttons: pencil + trash — hidden until row is hovered */
.fctx { display:none; position:absolute; right:8px; gap:3px; align-items:center; }
.file-item:hover .fctx { display:flex; }
.fctx-btn { background:none; border:none; cursor:pointer; padding:3px 5px; border-radius:3px; font-size:10px; color:var(--t3); transition:color .1s,background .1s; }
.fctx-btn:hover     { color:var(--t1); background:var(--raised); }
.fctx-btn.del:hover { color:var(--red); }
.fctx-btn.ren:hover { color:var(--accent); }

.new-file-btn { margin:6px; padding:8px; border-radius:var(--rs); background:transparent; border:1px dashed var(--bordm); color:var(--t3); font-family:var(--ui); font-size:12px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:7px; transition:color .12s,border-color .12s,background .12s; }
.new-file-btn i { font-size:12px; color:var(--t3); transition:color .12s; }
.new-file-btn:hover { color:var(--accent); border-color:rgba(91,156,246,0.4); background:var(--adim); }
.new-file-btn:hover i { color:var(--accent); }

/* ── EDITOR AREA ─────────────────────────────────────────── */
.editor-area { flex:1; min-width:0; display:flex; flex-direction:column; overflow:hidden; }
.tabs-row { display:flex; align-items:flex-end; gap:2px; padding:7px 7px 0; background:var(--panel); border-bottom:1px solid var(--border); overflow-x:auto; flex-shrink:0; scrollbar-width:none; }
.tabs-row::-webkit-scrollbar { display:none; }
.tab { display:inline-flex; align-items:center; gap:6px; padding:5px 12px 6px; border-radius:var(--rs) var(--rs) 0 0; font-size:11.5px; font-family:var(--mono); color:var(--t3); background:transparent; border:1px solid transparent; border-bottom:none; cursor:pointer; white-space:nowrap; transition:color .1s,background .1s; }
.tab i { font-size:11px; }
.tab:hover { color:var(--t2); background:var(--hover); }
.tab.active { color:var(--t1); background:var(--bg); border-color:var(--border); border-bottom:1px solid var(--bg); position:relative; top:1px; }
.tab .tdot { width:5px; height:5px; border-radius:50%; background:var(--amber); display:none; }
.tab.unsaved .tdot { display:block; }
.tab .tx { font-size:9px; opacity:.4; cursor:pointer; padding:1px 2px; }
.tab .tx:hover { opacity:1; color:var(--red); }
.toolbar { display:flex; align-items:center; gap:3px; padding:5px 10px; background:var(--panel); border-bottom:1px solid var(--border); flex-shrink:0; }
.tbtn { display:inline-flex; align-items:center; gap:6px; padding:4px 9px; border-radius:var(--rs); font-family:var(--ui); font-size:11.5px; font-weight:500; color:var(--t2); background:transparent; border:1px solid transparent; cursor:pointer; transition:color .1s,background .1s; }
.tbtn i { font-size:12px; color:var(--icon); transition:color .1s; }
.tbtn:hover  { color:var(--t1); background:var(--raised); border-color:var(--border); }
.tbtn:hover i { color:#fff; }
.tbtn.active { color:var(--accent); background:var(--adim); border-color:rgba(91,156,246,0.3); }
.tbtn.active i { color:var(--accent); }
.tbtn.err-on { color:var(--red); background:rgba(248,113,113,0.08); border-color:rgba(248,113,113,0.25); }
.tbtn.err-on i { color:var(--red); }
.tstats { margin-left:auto; display:flex; gap:12px; font-size:10.5px; color:var(--t3); font-family:var(--mono); }
.search-bar { display:none; align-items:center; gap:6px; padding:5px 10px; background:var(--bg); border-bottom:1px solid var(--border); flex-shrink:0; }
.search-bar.on { display:flex; }
.si { flex:1; max-width:215px; padding:4px 10px; border-radius:var(--rs); background:var(--raised); border:1px solid var(--border); color:var(--t1); font-family:var(--mono); font-size:12px; outline:none; transition:border-color .15s; }
.si:focus { border-color:var(--accent); }
.si::placeholder { color:var(--t3); }
.sab { padding:4px 9px; border-radius:var(--rs); font-size:11px; background:var(--raised); border:1px solid var(--border); color:var(--t2); cursor:pointer; font-family:var(--ui); transition:color .1s; }
.sab:hover { color:var(--t1); border-color:var(--bordm); }
.scnt { font-size:10px; color:var(--t3); font-family:var(--mono); white-space:nowrap; }
.search-highlight { background:rgba(251,191,36,0.28); border-radius:2px; }
.split { flex:1; min-height:0; display:flex; overflow:hidden; }
.editor-box { flex:1; min-width:0; display:flex; flex-direction:column; overflow:hidden; position:relative; }
#editor { flex:1; min-height:0; }
.prev-status { display:none; align-items:center; gap:8px; padding:3px 13px; background:var(--panel); border-top:1px solid var(--border); font-size:10px; color:var(--t3); font-family:var(--mono); flex-shrink:0; }
.prev-status.on { display:flex; }
.sok { color:var(--green); }
#pvdiv { width:4px; cursor:col-resize; flex-shrink:0; background:var(--border); display:none; }
#pvdiv.on { display:block; }
#pvdiv:hover, #pvdiv.drag { background:var(--accent); }
.prev-box { width:0; min-width:0; flex-shrink:0; display:flex; flex-direction:column; border-left:1px solid transparent; overflow:hidden; opacity:0; transition:width .28s,opacity .22s,border-color .28s; }
.prev-box.open { width:45%; min-width:270px; border-color:var(--border); opacity:1; }
.prev-hdr { display:flex; align-items:center; gap:8px; padding:0 14px; height:36px; background:var(--panel); border-bottom:1px solid var(--border); font-size:10px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--t2); flex-shrink:0; }
.prev-hdr i { color:var(--green); }
.live-dot { width:7px; height:7px; border-radius:50%; background:var(--green); box-shadow:0 0 6px rgba(74,222,128,0.7); margin-left:auto; animation:blink 2s ease-in-out infinite; }
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.25} }
.prev-ctrl { display:flex; align-items:center; gap:4px; padding:5px 10px; border-bottom:1px solid var(--border); background:var(--panel); flex-shrink:0; }
.ptab { padding:3px 9px; border-radius:var(--rs); font-size:11px; background:transparent; border:1px solid transparent; color:var(--t3); cursor:pointer; font-family:var(--ui); display:flex; align-items:center; gap:5px; transition:color .1s,background .1s; }
.ptab:hover  { color:var(--t1); background:var(--raised); border-color:var(--border); }
.ptab.active { color:var(--accent); background:var(--adim); border-color:rgba(91,156,246,0.25); }
.pref { margin-left:auto; padding:3px 9px; border-radius:var(--rs); font-size:11px; background:transparent; border:1px solid var(--border); color:var(--t2); cursor:pointer; font-family:var(--ui); display:flex; align-items:center; gap:5px; transition:color .1s; }
.pref:hover { color:var(--t1); border-color:var(--bordm); }
.prev-frame { flex:1; min-height:0; position:relative; background:#fff; overflow:hidden; }
#piframe, #riframe { width:100%; height:100%; border:none; display:block; }
.prev-load { position:absolute; inset:0; background:rgba(8,12,20,.92); display:flex; align-items:center; justify-content:center; flex-direction:column; gap:9px; font-size:12px; color:var(--t3); transition:opacity .2s; pointer-events:none; }
.prev-load.hide { opacity:0; }
.spin-ring { width:22px; height:22px; border:2px solid var(--border); border-top-color:var(--accent); border-radius:50%; animation:spin .7s linear infinite; }
@keyframes spin { to{transform:rotate(360deg)} }
#mdiv { width:4px; cursor:col-resize; flex-shrink:0; background:var(--border); }
#mdiv:hover, #mdiv.drag { background:var(--accent); }

/* ── AI PANEL ────────────────────────────────────────────── */
/* Wider (380px) so full AI code suggestions are visible without scrolling */
.ai-panel { width:380px; flex-shrink:0; background:var(--panel); border-left:1px solid var(--border); display:flex; flex-direction:column; overflow:hidden; }

/* tab bar at the top: "Response" | "Chat" */
.ai-tabs { display:flex; border-bottom:1px solid var(--border); flex-shrink:0; }
.ai-tab { flex:1; padding:9px 0; font-size:11px; font-weight:600; letter-spacing:.05em; text-transform:uppercase; text-align:center; color:var(--t3); cursor:pointer; border-bottom:2px solid transparent; background:var(--bg); transition:color .15s,border-color .15s; }
.ai-tab:hover { color:var(--t2); }
.ai-tab.active { color:var(--accent); border-bottom-color:var(--accent); }
.ai-tab i { margin-right:5px; }

/* response tab body */
.psec { flex:1; min-height:0; display:flex; flex-direction:column; overflow:hidden; }
.phdr { display:flex; align-items:center; gap:8px; padding:9px 14px; flex-shrink:0; font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--t2); background:var(--bg); border-bottom:1px solid var(--border); }
.phdr i { font-size:13px; }
.pdot { width:7px; height:7px; border-radius:50%; background:var(--t3); margin-left:auto; transition:background .2s,box-shadow .2s; }
.pdot.on { background:var(--green); box-shadow:0 0 6px rgba(74,222,128,0.7); animation:blink 2s infinite; }
/* pbody scrolls so AI can return full code without clipping */
.pbody { flex:1; overflow-y:auto; padding:14px; scrollbar-width:thin; scrollbar-color:var(--hover) transparent; }
pre { font-family:var(--mono); font-size:12px; line-height:1.75; white-space:pre-wrap; word-break:break-word; color:var(--t2); }
pre.has { color:var(--t1); background:var(--raised); border:1px solid var(--border); border-radius:var(--rs); padding:12px; }
.hint { font-size:12px; color:var(--t3); font-style:italic; }
.shimmer { display:none; flex-direction:column; gap:6px; padding:4px 0; }
.shimmer.on { display:flex; }
.shl { height:9px; border-radius:5px; background:linear-gradient(90deg,var(--raised) 25%,var(--hover) 50%,var(--raised) 75%); background-size:200% 100%; animation:sh 1.3s infinite; }
@keyframes sh { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* chat tab body */
.chat-panel { flex:1; min-height:0; display:none; flex-direction:column; overflow:hidden; }

/* small pill showing which file is being sent as context */
.chat-ctx { display:flex; align-items:center; gap:6px; padding:5px 12px; font-size:10px; color:var(--t3); font-family:var(--mono); border-bottom:1px solid var(--border); background:var(--panel); flex-shrink:0; }
.chat-ctx i { color:var(--accent); }
.chat-ctx-file { color:var(--accent); font-weight:600; }

/* scrollable message history */
.chat-msgs { flex:1; overflow-y:auto; padding:12px; display:flex; flex-direction:column; gap:10px; scrollbar-width:thin; scrollbar-color:var(--hover) transparent; }
.chat-empty { flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px; color:var(--t3); font-size:12px; text-align:center; padding:20px; }
.chat-empty i { font-size:28px; opacity:.2; }

/* message bubbles */
.chat-msg { display:flex; flex-direction:column; gap:4px; max-width:95%; }
.chat-msg.user { align-self:flex-end; align-items:flex-end; }
.chat-msg.ai   { align-self:flex-start; align-items:flex-start; }
.chat-bubble { padding:8px 12px; border-radius:10px; font-size:12px; line-height:1.6; word-break:break-word; }
.chat-msg.user .chat-bubble { background:var(--adim); border:1px solid rgba(91,156,246,0.25); color:var(--t1); border-radius:10px 10px 2px 10px; }
.chat-msg.ai   .chat-bubble { background:var(--raised); border:1px solid var(--border); color:var(--t1); border-radius:10px 10px 10px 2px; font-family:var(--mono); white-space:pre-wrap; }
.chat-msg.thinking .chat-bubble { color:var(--t3); font-style:italic; font-family:var(--ui); }
.chat-meta { font-size:9.5px; color:var(--t3); font-family:var(--mono); }

/* input row at the bottom of chat */
.chat-input-row { display:flex; align-items:flex-end; gap:8px; padding:10px 12px; border-top:1px solid var(--border); background:var(--bg); flex-shrink:0; }
#chatInput { flex:1; background:var(--raised); border:1px solid var(--border); color:var(--t1); font-family:var(--ui); font-size:12.5px; border-radius:var(--rs); padding:7px 11px; outline:none; resize:none; line-height:1.5; max-height:100px; overflow-y:auto; transition:border-color .15s; scrollbar-width:thin; }
#chatInput:focus { border-color:var(--accent); }
#chatInput::placeholder { color:var(--t3); }
.chat-send { flex-shrink:0; width:32px; height:32px; border-radius:var(--rs); background:var(--accent); border:none; color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:12px; box-shadow:var(--aglow); transition:background .12s; }
.chat-send:hover    { background:#74aff8; }
.chat-send:disabled { background:var(--raised); color:var(--t3); box-shadow:none; cursor:default; }

/* ── TERMINAL ────────────────────────────────────────────── */
.term-section { display:flex; flex-direction:column; overflow:hidden; border-top:1px solid var(--border); flex:1.8; min-height:220px; }
.term-hdr { display:flex; align-items:center; gap:8px; padding:8px 14px; flex-shrink:0; font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--t2); background:var(--bg); border-bottom:1px solid var(--border); }
.term-hdr i { font-size:13px; color:var(--cyan); }
.term-actions { display:flex; align-items:center; gap:3px; margin-left:auto; }
.ta { background:none; border:none; color:var(--t2); cursor:pointer; padding:4px 7px; border-radius:4px; font-size:11px; transition:color .1s,background .1s; }
.ta:hover      { color:var(--t1); background:var(--hover); }
.ta.del:hover  { color:var(--red); background:rgba(248,113,113,0.1); }
.ta.cp:hover   { color:var(--accent); }
.exit-badge { display:none; padding:2px 8px; border-radius:10px; font-size:9px; font-family:var(--mono); font-weight:700; flex-shrink:0; }
.exit-badge.ok  { background:rgba(74,222,128,0.12);  color:var(--green); border:1px solid rgba(74,222,128,0.3); }
.exit-badge.err { background:rgba(248,113,113,0.12); color:var(--red);   border:1px solid rgba(248,113,113,0.3); }
.term-bar { display:flex; align-items:center; gap:5px; padding:4px 11px; flex-shrink:0; background:var(--panel); border-bottom:1px solid var(--border); }
.tfbtn { background:none; border:none; color:var(--t2); cursor:pointer; padding:2px 6px; border-radius:3px; font-size:9px; font-family:var(--mono); font-weight:600; transition:color .1s,background .1s; }
.tfbtn:hover { color:var(--t1); background:var(--raised); }
.ttime { margin-left:auto; font-size:9.5px; color:var(--t3); font-family:var(--mono); }
.term-body { flex:1; min-height:0; display:flex; flex-direction:column; overflow:hidden; background:#05080e; }
.term-out-wrap { flex:1; min-height:0; overflow-y:auto; overflow-x:auto; scrollbar-width:thin; scrollbar-color:#1a2535 transparent; }
.term-out-wrap::-webkit-scrollbar { width:5px; height:5px; }
.term-out-wrap::-webkit-scrollbar-thumb { background:#1a2535; border-radius:3px; }
#termOut { font-family:var(--mono); font-size:12.5px; line-height:1.65; padding:13px 15px; color:#c9d1d9; white-space:pre-wrap; word-break:break-word; min-height:100%; }
#termOut .tp  { color:var(--t3); user-select:none; }
#termOut .ti  { color:var(--cyan); }
#termOut .tw  { color:var(--amber); }
#termOut .tok { color:var(--green); }
#termOut .ter { color:var(--red); }

/* ── AUTOCOMPLETE POPUP ──────────────────────────────────── */
#ac-popup { position:absolute; z-index:100; display:none; flex-direction:column; min-width:275px; max-width:450px; background:var(--panel); border:1px solid var(--bordm); border-radius:var(--r); box-shadow:0 10px 32px rgba(0,0,0,.6),var(--aglow); overflow:hidden; animation:pop .12s ease; }
@keyframes pop { from{opacity:0;transform:translateY(-5px)} to{opacity:1;transform:none} }
.ac-hdr { display:flex; align-items:center; gap:7px; padding:5px 11px; background:var(--raised); border-bottom:1px solid var(--border); font-size:10px; color:var(--t2); }
.ac-spin { width:8px; height:8px; border:1.5px solid var(--border); border-top-color:var(--accent); border-radius:50%; animation:spin .65s linear infinite; flex-shrink:0; }
.ac-list { max-height:170px; overflow-y:auto; }
.ac-item { display:flex; align-items:flex-start; gap:9px; padding:7px 11px; cursor:pointer; border-bottom:1px solid var(--border); transition:background .1s; }
.ac-item:last-child { border-bottom:none; }
.ac-item:hover, .ac-item.sel { background:var(--hover); }
.ac-code { font-family:var(--mono); font-size:12px; color:var(--cyan); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:pre; }
.ac-icon { font-size:11px; color:var(--accent); margin-top:1px; flex-shrink:0; }
.ac-foot { padding:4px 11px; background:var(--bg); border-top:1px solid var(--border); font-size:9.5px; color:var(--t3); display:flex; gap:10px; }
.ac-foot kbd { background:var(--raised); border:1px solid var(--bordm); border-radius:3px; padding:1px 5px; font-size:9px; color:var(--t2); font-family:var(--mono); }

/* ── MODALS ──────────────────────────────────────────────── */
.modal-ov { position:fixed; inset:0; z-index:500; background:rgba(0,0,0,.65); backdrop-filter:blur(7px); display:none; align-items:center; justify-content:center; }
.modal-ov.open { display:flex; animation:fin .15s ease; }
@keyframes fin { from{opacity:0} to{opacity:1} }
.modal { background:var(--panel); border:1px solid var(--bordm); border-radius:14px; width:580px; max-width:92vw; max-height:78vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 28px 75px rgba(0,0,0,.75); animation:sup .2s cubic-bezier(.34,1.56,.64,1); }
@keyframes sup { from{opacity:0;transform:translateY(16px) scale(.97)} to{opacity:1;transform:none} }
.modal-hdr { display:flex; align-items:center; gap:9px; padding:14px 18px; border-bottom:1px solid var(--border); }
.modal-hdr h2 { font-size:13.5px; font-weight:600; flex:1; }
.mcls { width:25px; height:25px; border-radius:var(--rs); background:var(--raised); border:1px solid var(--border); color:var(--t2); font-size:10px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .12s,color .12s; }
.mcls:hover { background:rgba(248,113,113,0.14); color:var(--red); border-color:rgba(248,113,113,0.25); }
.modal-body { flex:1; overflow-y:auto; padding:17px; }
.stats-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:20px; }
.sc { background:var(--raised); border:1px solid var(--border); border-radius:var(--r); padding:13px 15px; }
.sv { font-size:22px; font-weight:700; font-family:var(--mono); margin-bottom:3px; }
.sl { font-size:10px; color:var(--t3); text-transform:uppercase; letter-spacing:.07em; }
.sc.bl .sv{color:var(--accent)} .sc.cy .sv{color:var(--cyan)} .sc.gr .sv{color:var(--green)} .sc.am .sv{color:var(--amber)} .sc.rd .sv{color:var(--red)} .sc.vi .sv{color:var(--accent2)}
.sec-title { font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--t3); margin-bottom:10px; display:flex; align-items:center; gap:9px; }
.sec-title::after { content:''; flex:1; height:1px; background:var(--border); }
.lb-list { display:flex; flex-direction:column; gap:8px; }
.lb-row  { display:flex; align-items:center; gap:10px; font-size:12px; }
.lb-lbl  { width:65px; color:var(--t2); font-family:var(--mono); }
.lb-trk  { flex:1; height:5px; background:var(--raised); border-radius:3px; overflow:hidden; }
.lb-fill { height:100%; border-radius:3px; }
.lb-cnt  { width:28px; text-align:right; color:var(--t3); font-family:var(--mono); font-size:11px; }
.folder-tree { display:flex; flex-direction:column; gap:3px; }
.fi  { display:flex; align-items:center; gap:9px; padding:7px 11px; border-radius:var(--rs); cursor:pointer; font-size:13px; color:var(--t2); transition:background .1s; }
.fi:hover { background:var(--hover); color:var(--t1); }
.fi i { color:var(--amber); }
.fc  { margin-left:auto; font-size:10px; color:var(--t3); font-family:var(--mono); }
.ff  { padding-left:26px; display:flex; flex-direction:column; gap:1px; margin-top:1px; }
.ffi { display:flex; align-items:center; gap:8px; padding:5px 9px; border-radius:var(--rs); cursor:pointer; font-size:12px; color:var(--t3); transition:background .1s,color .1s; }
.ffi:hover { background:var(--hover); color:var(--t1); }
.fnew { margin-top:10px; padding:8px 14px; border-radius:var(--rs); font-size:12px; background:transparent; border:1px dashed var(--bordm); color:var(--t3); cursor:pointer; display:flex; align-items:center; gap:7px; width:100%; justify-content:center; font-family:var(--ui); transition:color .12s,border-color .12s,background .12s; }
.fnew:hover { color:var(--amber); border-color:rgba(251,191,36,.4); background:rgba(251,191,36,.06); }
.rhl { display:flex; flex-direction:column; gap:8px; }
.rhi { background:var(--raised); border:1px solid var(--border); border-radius:var(--r); padding:11px 13px; display:flex; flex-direction:column; gap:7px; transition:border-color .12s; }
.rhi:hover { border-color:var(--bordm); }
.rhm { display:flex; align-items:center; gap:8px; font-size:11px; color:var(--t3); }
.rhlg { padding:1px 7px; border-radius:4px; background:var(--adim); border:1px solid rgba(91,156,246,.2); color:var(--accent); font-family:var(--mono); }
.rhok{color:var(--green)} .rher{color:var(--red)}
.rhc  { font-family:var(--mono); font-size:11px; color:var(--t2); white-space:pre; overflow:hidden; text-overflow:ellipsis; max-height:36px; border-left:2px solid var(--bordm); padding-left:8px; }
.rho  { font-family:var(--mono); font-size:11px; white-space:pre-wrap; word-break:break-all; max-height:46px; overflow:hidden; }
.rho.ok{color:var(--green)} .rho.er{color:var(--red)}
.rhr  { align-self:flex-end; padding:3px 9px; border-radius:4px; font-size:11px; background:var(--adim); border:1px solid rgba(91,156,246,.2); color:var(--accent); cursor:pointer; font-family:var(--ui); transition:background .12s; }
.rhr:hover { background:rgba(91,156,246,.2); }
.rhe  { color:var(--t3); font-size:13px; font-style:italic; text-align:center; padding:26px 0; }
.expl { font-size:13px; line-height:1.8; white-space:pre-wrap; word-break:break-word; font-family:var(--ui); }
.expl.ld { color:var(--t3); font-style:italic; }
.shr-row  { display:flex; gap:9px; align-items:center; background:var(--raised); border:1px solid var(--border); border-radius:var(--r); padding:10px 13px; font-family:var(--mono); font-size:12px; color:var(--cyan); word-break:break-all; }
.shr-cp   { flex-shrink:0; padding:5px 12px; border-radius:var(--rs); font-size:12px; background:var(--adim); border:1px solid rgba(91,156,246,.25); color:var(--accent); cursor:pointer; white-space:nowrap; font-family:var(--ui); transition:background .12s; }
.shr-cp:hover { background:rgba(91,156,246,.22); }
.shr-info { display:flex; align-items:center; gap:10px; padding:10px 13px; background:var(--raised); border:1px solid var(--border); border-radius:var(--r); font-size:12px; color:var(--t2); margin-top:10px; }

/* ── TOAST ───────────────────────────────────────────────── */
#toast { position:fixed; bottom:20px; right:20px; z-index:9999; padding:9px 16px; border-radius:var(--r); background:var(--panel); border:1px solid var(--bordm); font-size:12.5px; color:var(--t1); display:flex; align-items:center; gap:8px; box-shadow:0 8px 26px rgba(0,0,0,.6); transform:translateY(16px); opacity:0; pointer-events:none; backdrop-filter:blur(12px); transition:transform .22s cubic-bezier(.34,1.56,.64,1),opacity .18s; }
#toast.show { transform:translateY(0); opacity:1; }
#toast i { font-size:14px; }
#toast.success i { color:var(--green); }
#toast.info    i { color:var(--accent); }
#toast.error   i { color:var(--red); }

/* ── AI PANEL RESIZE ─────────────────────────────────────── */
/* Horizontal drag handle — left edge of the AI panel        */
#aidiv { width:4px; cursor:col-resize; flex-shrink:0; background:var(--border); }
#aidiv:hover, #aidiv.drag { background:var(--accent2); }

/* Vertical drag handle — between AI response/chat area and terminal */
#ai-vdiv { height:4px; cursor:row-resize; flex-shrink:0; background:var(--border); width:100%; }
#ai-vdiv:hover, #ai-vdiv.drag { background:var(--accent2); }

* { scrollbar-width:thin; scrollbar-color:var(--hover) transparent; }
a { text-decoration:none; }
</style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
  <!-- STACKLY inline SVG logo — same mark used on the landing page -->
  <a href="/" style="text-decoration:none;display:flex;align-items:center">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 180 40" height="28">
      <defs>
        <linearGradient id="elg1" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#3b82f6"/>
          <stop offset="100%" stop-color="#22d3ee"/>
        </linearGradient>
        <linearGradient id="elg2" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" stop-color="#e2eaff"/>
          <stop offset="100%" stop-color="#a5c0ff"/>
        </linearGradient>
      </defs>
      <!-- icon bg -->
      <rect x="0" y="4" width="32" height="32" rx="8" fill="url(#elg1)" opacity="0.15"/>
      <rect x="0" y="4" width="32" height="32" rx="8" fill="none" stroke="url(#elg1)" stroke-width="1.2" opacity="0.55"/>
      <!-- stacked layers -->
      <rect x="7"  y="13"   width="18" height="3" rx="1.5" fill="url(#elg1)" opacity="1"/>
      <rect x="9"  y="18.5" width="14" height="3" rx="1.5" fill="url(#elg1)" opacity="0.75"/>
      <rect x="11" y="24"   width="10" height="3" rx="1.5" fill="url(#elg1)" opacity="0.5"/>
      <!-- wordmark -->
      <text x="42" y="27" font-family="Outfit,Inter,sans-serif" font-size="19" font-weight="800" letter-spacing="1.5" fill="url(#elg2)">STACKLY</text>
    </svg>
  </a>
  <div class="sep"></div>
  <div class="lang-wrap">
    <i class="fa-solid fa-code"></i>
    <select id="language" onchange="changeLang()">
      <option value="python">Python</option><option value="javascript">JavaScript</option>
      <option value="php">PHP</option><option value="java">Java</option>
      <option value="cpp">C++</option><option value="csharp">C#</option>
      <option value="go">Go</option><option value="rust">Rust</option>
      <option value="html">HTML</option><option value="css">CSS</option>
      <option value="json">JSON</option><option value="jsx">React (JSX)</option>
    </select>
  </div>
  <div class="save-pill" id="savePill"><i class="fa-solid fa-floppy-disk"></i><span id="saveText">Auto-save on</span></div>
  <div class="spacer"></div>
  <button class="btn" id="prevBtn" onclick="togglePreview()"><i class="fa-solid fa-eye" id="prevIcon"></i><span id="prevTxt">Preview</span></button>
  <button class="btn suggest" onclick="askAI()"><i class="fa-solid fa-robot"></i> Suggest</button>
  <button class="btn" onclick="applyFix()"><i class="fa-solid fa-wand-magic-sparkles"></i> Apply</button>
  <button class="btn primary" onclick="runCode()"><i class="fa-solid fa-play"></i> Run</button>
  <div class="sep"></div>
  <!-- Glass icon group — all 6 icons in one frosted pill, each with its own colour -->
  <div class="ico-group">
    <button class="btn ico i-share"  title="Share"        onclick="openShareModal()">      <i class="fa-solid fa-share-nodes"></i></button>
    <button class="btn ico i-theme"  title="Theme"        onclick="toggleTheme()" id="themeBtn"><i class="fa-solid fa-moon" id="themeIcon"></i></button>
    <button class="btn ico i-hist"   title="Run history"  onclick="openRunHistoryModal()"> <i class="fa-solid fa-clock-rotate-left"></i></button>
    <button class="btn ico i-folder" title="Folders"      onclick="openFoldersModal()">    <i class="fa-solid fa-folder-tree"></i></button>
    <button class="btn ico i-stats"  title="Statistics"   onclick="openStatsModal()">      <i class="fa-solid fa-chart-bar"></i></button>
    <a href="/history"><button class="btn ico i-git" title="Code history"><i class="fa-solid fa-code-branch"></i></button></a>
  </div>
  <div class="sep"></div>
  <form method="POST" action="/logout" style="margin:0">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <button class="btn danger"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
  </form>
</div>

<!-- APP -->
<div class="app">

  <!-- SIDEBAR -->
  <div class="sidebar">
    <div class="sidebar-hdr"><i class="fa-solid fa-folder-open"></i> Explorer</div>
    <!-- File list rebuilt by renderFiles() — each row has hover rename/delete buttons -->
    <div class="file-list" id="fileList"></div>
    <button class="new-file-btn" onclick="newFile()"><i class="fa-solid fa-plus"></i> New File</button>
  </div>

  <!-- EDITOR AREA -->
  <div class="editor-area">
    <div id="tabs" class="tabs-row"></div>
    <div class="toolbar">
      <button class="tbtn" id="srchBtn" onclick="toggleSearch()"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
      <button class="tbtn" onclick="fmtCode()"><i class="fa-solid fa-align-left"></i> Format</button>
      <button class="tbtn" onclick="explainCode()"><i class="fa-solid fa-comment-dots"></i> Explain</button>
      <button class="tbtn" onclick="dlFile()"><i class="fa-solid fa-download"></i> Download</button>
      <button class="tbtn" id="errBtn" onclick="toggleErrHL()"><i class="fa-solid fa-triangle-exclamation"></i> Errors</button>
      <div class="tstats"><span><span id="stL">1</span>L</span><span><span id="stC">0</span>C</span><span><span id="stW">0</span>W</span></div>
    </div>
    <div class="search-bar" id="searchBar">
      <input class="si" id="srchIn" placeholder="Find…" oninput="doSearch()">
      <input class="si" id="replIn" placeholder="Replace…">
      <button class="sab" onclick="doReplace()">Replace</button>
      <button class="sab" onclick="doReplaceAll()">All</button>
      <span class="scnt" id="srchCnt"></span>
      <button class="sab" onclick="toggleSearch()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="split" id="split">
      <div class="editor-box" id="edBox">
        <div id="editor"></div>
        <div class="prev-status" id="pvStatus">
          <i class="fa-solid fa-circle-check sok" id="pvIcon"></i>
          <span id="pvText">Preview synced</span>
          <span style="margin-left:auto;opacity:.5" id="pvTime"></span>
        </div>
        <div id="ac-popup">
          <div class="ac-hdr"><div class="ac-spin" id="acSpin"></div><span id="acLabel">AI suggestions</span></div>
          <div class="ac-list" id="acList"></div>
          <div class="ac-foot"><span><kbd>↑</kbd><kbd>↓</kbd> navigate</span><span><kbd>↵</kbd>/<kbd>⇥</kbd> accept</span><span><kbd>Esc</kbd> dismiss</span></div>
        </div>
      </div>
      <div id="pvdiv"></div>
      <div class="prev-box" id="pvBox">
        <div class="prev-hdr"><i class="fa-solid fa-display"></i> Live Preview<div class="live-dot"></div></div>
        <div class="prev-ctrl">
          <button class="ptab active" id="tabHtml" onclick="setPrevTab('html')"><i class="fa-brands fa-html5" style="color:#fb923c"></i> HTML/CSS/JS</button>
          <button class="ptab" id="tabReact" onclick="setPrevTab('react')"><i class="fa-brands fa-react" style="color:#38bdf8"></i> React</button>
          <button class="pref" onclick="refreshPrev()"><i class="fa-solid fa-arrows-rotate"></i> Refresh</button>
        </div>
        <div class="prev-frame" id="htmlWrap">
          <div class="prev-load" id="pvLoad"><div class="spin-ring"></div><span>Rendering…</span></div>
          <iframe id="piframe" sandbox="allow-scripts allow-same-origin"></iframe>
        </div>
        <div class="prev-frame" id="reactWrap" style="display:none">
          <iframe id="riframe" sandbox="allow-scripts"></iframe>
        </div>
      </div>
    </div>
  </div>

  <div id="mdiv"></div>

  <!-- Horizontal drag handle — resize the AI panel width by dragging this bar -->
  <div id="aidiv"></div>

  <!-- AI PANEL: tabbed — Response | Chat -->
  <div class="ai-panel">

    <!-- Tab switcher -->
    <div class="ai-tabs">
      <div class="ai-tab active" id="tabResp" onclick="switchAITab('response')">
        <i class="fa-solid fa-brain"></i> Response
      </div>
      <div class="ai-tab" id="tabChat" onclick="switchAITab('chat')">
        <i class="fa-solid fa-comments"></i> Chat
      </div>
    </div>

    <!-- RESPONSE TAB -->
    <div class="psec" id="aiRespPanel">
      <div class="phdr"><i class="fa-solid fa-brain" style="color:var(--accent2)"></i> AI Response<div class="pdot" id="aiDot"></div></div>
      <div class="pbody">
        <div class="shimmer" id="aiShim"><div class="shl" style="width:85%"></div><div class="shl" style="width:65%"></div><div class="shl" style="width:75%"></div><div class="shl" style="width:50%"></div></div>
        <pre id="result" class="hint">— ask AI to suggest improvements —</pre>
      </div>
    </div>

    <!-- CHAT TAB -->
    <div class="chat-panel" id="aiChatPanel">
      <!-- shows which file's code is being sent as context -->
      <div class="chat-ctx">
        <i class="fa-solid fa-paperclip"></i>
        Context: <span class="chat-ctx-file" id="chatCtxFile">main.py</span>
        <span style="margin-left:auto;font-size:9px">sent with each message</span>
      </div>
      <!-- message history (populated by appendMsg()) -->
      <div class="chat-msgs" id="chatMsgs">
        <div class="chat-empty" id="chatEmpty">
          <i class="fa-solid fa-comments"></i>
          <div>Ask anything about your code</div>
          <div style="font-size:11px;color:var(--t3)">"Why is this slow?" · "Add error handling"</div>
        </div>
      </div>
      <!-- message input: Enter to send, Shift+Enter for newline -->
      <div class="chat-input-row">
        <textarea id="chatInput" rows="1" placeholder="Ask about your code…"
          oninput="chatAutosize(this)" onkeydown="chatKeydown(event)"></textarea>
        <button class="chat-send" id="chatSendBtn" onclick="sendChat()">
          <i class="fa-solid fa-paper-plane"></i>
        </button>
      </div>
    </div>

    <!-- Vertical drag handle — resize between AI response/chat and terminal -->
    <div id="ai-vdiv"></div>

    <!-- TERMINAL (shared between both tabs) -->
    <div class="term-section">
      <div class="term-hdr">
        <i class="fa-solid fa-terminal"></i> Terminal
        <div class="term-actions">
          <span class="exit-badge" id="exitBadge"></span>
          <button class="ta cp"  title="Copy"  onclick="termCopy(event)"><i class="fa-solid fa-copy"></i></button>
          <button class="ta del" title="Clear" onclick="termClear(event)"><i class="fa-solid fa-trash-can"></i></button>
          <div class="pdot" id="runDot" style="margin-left:4px"></div>
        </div>
      </div>
      <div class="term-bar">
        <i class="fa-solid fa-circle" style="color:#f87171;font-size:7px"></i>
        <i class="fa-solid fa-circle" style="color:#fbbf24;font-size:7px;margin-left:3px"></i>
        <i class="fa-solid fa-circle" style="color:#4ade80;font-size:7px;margin-left:3px"></i>
        <div style="margin-left:auto;display:flex;align-items:center;gap:5px">
          <button class="tfbtn" onclick="termFS(-1)">A−</button>
          <button class="tfbtn" onclick="termFS(1)">A+</button>
          <span id="termTime" class="ttime"></span>
        </div>
      </div>
      <div class="term-body">
        <!-- output view: run results -->
        <div id="termViewOutput" style="display:flex;flex-direction:column;flex:1;min-height:0;overflow:hidden">
          <div class="term-out-wrap" id="termWrap">
            <div id="termOut"><span class="tp">~/project $</span> <span class="ti">ready — press Run to execute</span></div>
          </div>
        </div>
        <!-- stdin view: collect input before running -->
        <div id="termViewStdin" style="display:none;flex-direction:column;flex:1;min-height:0;overflow:hidden">
          <div style="padding:8px 13px;font-size:10px;color:var(--cyan);font-family:var(--mono);border-bottom:1px solid rgba(56,189,248,0.15);flex-shrink:0;display:flex;align-items:center;gap:9px">
            <i class="fa-solid fa-keyboard"></i> Enter program input (one value per line), then click Run
            <button onclick="termCancelStdin()" style="margin-left:auto;background:none;border:none;color:var(--t3);cursor:pointer;font-size:10px;font-family:var(--mono)">cancel</button>
          </div>
          <textarea id="termStdin" spellcheck="false" placeholder="e.g.&#10;Ahmad&#10;25&#10;Amman"
            style="flex:1;min-height:0;background:#05080e;color:#e2eaff;border:none;outline:none;font-family:var(--mono);font-size:12.5px;padding:13px 15px;resize:none;line-height:1.65;scrollbar-width:thin;scrollbar-color:#1a2535 transparent;"></textarea>
          <div style="padding:8px 13px;background:#0a0f18;border-top:1px solid rgba(56,189,248,0.15);flex-shrink:0;display:flex;gap:9px;align-items:center">
            <span style="font-size:10px;color:var(--t3);font-family:var(--mono)">Each line = one input() call</span>
            <button onclick="termRunWithStdin()" style="margin-left:auto;padding:5px 15px;border-radius:var(--rs);font-size:11px;font-weight:600;background:var(--accent);border:none;color:#fff;cursor:pointer"><i class="fa-solid fa-play" style="margin-right:5px"></i>Run</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- MODALS -->
<div class="modal-ov" id="statsModal"><div class="modal"><div class="modal-hdr"><i class="fa-solid fa-chart-bar" style="color:var(--accent2)"></i><h2>Statistics</h2><button class="mcls" onclick="closeModal('statsModal')"><i class="fa-solid fa-xmark"></i></button></div><div class="modal-body" id="statsBody"></div></div></div>
<div class="modal-ov" id="foldersModal"><div class="modal" style="width:440px"><div class="modal-hdr"><i class="fa-solid fa-folder-tree" style="color:var(--amber)"></i><h2>Folder Manager</h2><button class="mcls" onclick="closeModal('foldersModal')"><i class="fa-solid fa-xmark"></i></button></div><div class="modal-body" id="foldersBody"></div></div></div>
<div class="modal-ov" id="runHistoryModal"><div class="modal"><div class="modal-hdr"><i class="fa-solid fa-clock-rotate-left" style="color:var(--green)"></i><h2>Run History</h2><button class="mcls" onclick="closeModal('runHistoryModal')"><i class="fa-solid fa-xmark"></i></button></div><div class="modal-body" id="rhBody"></div></div></div>
<div class="modal-ov" id="explainModal"><div class="modal" style="width:620px"><div class="modal-hdr"><i class="fa-solid fa-comment-dots" style="color:var(--pink)"></i><h2>AI Code Explanation</h2><button class="mcls" onclick="closeModal('explainModal')"><i class="fa-solid fa-xmark"></i></button></div><div class="modal-body"><pre id="explainOut" class="expl ld">Asking AI to explain your code…</pre></div></div></div>
<div class="modal-ov" id="shareModal">
  <div class="modal" style="width:460px">
    <div class="modal-hdr"><i class="fa-solid fa-share-nodes" style="color:var(--cyan)"></i><h2>Share Code</h2><button class="mcls" onclick="closeModal('shareModal')"><i class="fa-solid fa-xmark"></i></button></div>
    <div class="modal-body">
      <div class="shr-row"><i class="fa-solid fa-link" style="color:var(--t3)"></i><span id="shrLink" style="flex:1">Generating…</span><button class="shr-cp" onclick="copyShrLink()"><i class="fa-solid fa-copy"></i> Copy</button></div>
      <div class="shr-info"><i class="fa-solid fa-lock" style="color:var(--green)"></i> Read-only — recipients can view but not edit.</div>
      <div class="shr-info"><i class="fa-solid fa-code" style="color:var(--accent)"></i> File: <strong id="shrFile" style="color:var(--cyan);margin-left:4px">—</strong> &nbsp;·&nbsp; Language: <strong id="shrLang" style="color:var(--accent2);margin-left:4px">—</strong></div>
    </div>
  </div>
</div>

<div id="toast"><i id="toastIcon" class="fa-solid fa-circle-check"></i><span id="toastMsg">Done</span></div>

<!-- Monaco Editor — pinned version for stability, never use @latest -->
<script src="https://unpkg.com/monaco-editor@0.44.0/min/vs/loader.js"></script>
<script>

/* ── STATE ───────────────────────────────────────────────── */
let files      = { 'main.py': "print('hello world')" }; // filename → content
let openTabs   = ['main.py'], curFile = 'main.py';
let folders    = { 'Project': ['main.py'] };
let runHistory = [], RH_MAX = 50;
let savedFiles = JSON.parse(JSON.stringify(files));      // snapshot for dirty tracking
let saveTimer  = null, SAVE_DELAY = 1500;
let prevOpen   = false, prevTab = 'html';
let prevTimer  = null,  PREV_DELAY = 600;
let darkMode   = true, errHL = false;
let errDecs    = [], srchOpen = false, srchMatches = [];
let lastAI     = null;
let termFontSz = 12.5;
let _runCode   = null, _runLang = null; // stored while stdin view is open
let chatBusy   = false;                 // prevents double-sending in chat


/* ── HELPERS ─────────────────────────────────────────────── */

/** H — HTML-escape a string so it's safe to inject into innerHTML */
function H(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/** csrf — reads the Laravel CSRF meta tag */
function csrf() { return document.querySelector('meta[name="csrf-token"]').content; }


/* ── TOAST ───────────────────────────────────────────────── */
let toastT = null;

/** toast — show a bottom-right notification for 3 seconds
 *  @param {string} msg   message text
 *  @param {string} type  'success' | 'info' | 'error'
 */
function toast(msg, type = 'success') {
  const icons = { success:'fa-solid fa-circle-check', info:'fa-solid fa-circle-info', error:'fa-solid fa-circle-xmark' };
  document.getElementById('toastIcon').className = icons[type];
  document.getElementById('toastMsg').textContent = msg;
  const el = document.getElementById('toast');
  el.className = 'show ' + type;
  clearTimeout(toastT);
  toastT = setTimeout(() => { el.className = ''; }, 3000);
}


/* ── MODALS ──────────────────────────────────────────────── */

/** openModal / closeModal — show or hide a modal by element id */
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }

// close any modal when clicking the dark overlay behind it
document.querySelectorAll('.modal-ov').forEach(ov => {
  ov.addEventListener('click', e => { if (e.target === ov) ov.classList.remove('open'); });
});


/* ── FILE ICONS ──────────────────────────────────────────── */

/** iconCls — returns the FA icon class for a file by its extension */
function iconCls(n) {
  const ext = n.split('.').pop().toLowerCase();
  const b = { js:'fa-brands fa-js', py:'fa-brands fa-python', php:'fa-brands fa-php', html:'fa-brands fa-html5', css:'fa-brands fa-css3-alt', java:'fa-brands fa-java', jsx:'fa-brands fa-react', tsx:'fa-brands fa-react' };
  const s = { json:'fa-solid fa-code', ts:'fa-solid fa-code', rs:'fa-solid fa-code', go:'fa-solid fa-code', cpp:'fa-solid fa-file-code', cs:'fa-solid fa-file-code' };
  return b[ext] || s[ext] || 'fa-solid fa-file-code';
}

/** iconCol — returns a colour matched to the file's language */
function iconCol(n) {
  const ext = n.split('.').pop().toLowerCase();
  const m = { js:'#fbbf24', py:'#60a5fa', php:'#a78bfa', html:'#fb923c', css:'#38bdf8', json:'#34d399', ts:'#38bdf8', rs:'#fb923c', go:'#22d3ee', java:'#f87171', jsx:'#38bdf8', tsx:'#38bdf8' };
  return m[ext] || '#7a8ba8';
}

/** prevType — which preview mode to use for the current file: 'react' | 'web' | 'none' */
function prevType() {
  const l = document.getElementById('language').value;
  const e = curFile.split('.').pop().toLowerCase();
  if (l === 'jsx' || e === 'jsx' || e === 'tsx') return 'react';
  if (['html','css','javascript','js'].includes(l) || ['html','css','js','ts'].includes(e)) return 'web';
  return 'none';
}


/* ── RENDER ──────────────────────────────────────────────── */

/** renderFiles — rebuild the sidebar file list.
 *  Each row includes hidden rename (pencil) and delete (trash) buttons
 *  that appear on hover via the .fctx CSS rule.
 */
function renderFiles() {
  let h = '';
  for (const n in files) {
    const u = savedFiles[n] !== files[n]; // true = unsaved changes
    h += `<div class="file-item ${n===curFile?'active':''} ${u?'unsaved':''}" onclick="openFile('${n}')">
            <i class="${iconCls(n)}" style="color:${iconCol(n)}"></i>
            <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${H(n)}</span>
            <span class="dot"></span>
            <!-- rename/delete buttons, visible on hover -->
            <span class="fctx" onclick="event.stopPropagation()">
              <button class="fctx-btn ren" title="Rename" onclick="renameFile('${n}')"><i class="fa-solid fa-pencil"></i></button>
              <button class="fctx-btn del" title="Delete" onclick="deleteFile('${n}')"><i class="fa-solid fa-trash"></i></button>
            </span>
          </div>`;
  }
  document.getElementById('fileList').innerHTML = h;
}

/** renderTabs — rebuild the open-file tab bar */
function renderTabs() {
  let h = '';
  for (const f of openTabs) {
    const u = savedFiles[f] !== files[f];
    h += `<div class="tab ${f===curFile?'active':''} ${u?'unsaved':''}" onclick="switchTab('${f}')">
            <i class="${iconCls(f)}" style="color:${iconCol(f)};font-size:10px"></i>
            <span>${H(f)}</span><span class="tdot"></span>
            <span class="tx" onclick="closeTab(event,'${f}')"><i class="fa-solid fa-xmark"></i></span>
          </div>`;
  }
  document.getElementById('tabs').innerHTML = h;
}

/** openFile — open a file (adds a tab if not already open) */
function openFile(n) {
  if (!openTabs.includes(n)) openTabs.push(n);
  curFile = n;
  if (window.editor) editor.setValue(files[n]);
  renderTabs(); renderFiles(); syncPrevLang(); updateStats();
  document.getElementById('chatCtxFile').textContent = n; // keep chat context label in sync
}

/** switchTab — activate an already-open tab */
function switchTab(n) {
  curFile = n;
  if (window.editor) editor.setValue(files[n]);
  renderTabs(); renderFiles(); syncPrevLang(); updateStats();
  document.getElementById('chatCtxFile').textContent = n;
}

/** closeTab — close a tab, fall back to first remaining */
function closeTab(e, n) {
  e.stopPropagation();
  openTabs = openTabs.filter(f => f !== n);
  if (!openTabs.length) openTabs = [Object.keys(files)[0]];
  if (curFile === n) { curFile = openTabs[0]; if (window.editor) editor.setValue(files[curFile] || ''); }
  renderTabs(); renderFiles();
}

/** newFile — prompt for a filename, validate, create an empty file */
function newFile() {
  const n = prompt('File name (e.g. index.html):');
  if (!n || !n.trim()) return;
  const name = n.trim();
  if (files[name]) { toast('File already exists', 'error'); return; }
  files[name] = ''; savedFiles[name] = '';
  openTabs.push(name); curFile = name;
  if (window.editor) editor.setValue('');
  renderFiles(); renderTabs();
  toast('Created ' + name, 'info');
}

/** renameFile — prompt for new name, update everywhere: files, tabs, folders, localStorage */
function renameFile(oldName) {
  const newName = prompt('Rename to:', oldName);
  if (!newName || !newName.trim() || newName.trim() === oldName) return;
  const n = newName.trim();
  if (files[n]) { toast('File already exists', 'error'); return; }
  // move content to new key
  files[n] = files[oldName];
  savedFiles[n] = savedFiles[oldName] || files[oldName];
  delete files[oldName];
  delete savedFiles[oldName];
  // update open tabs
  openTabs = openTabs.map(f => f === oldName ? n : f);
  if (curFile === oldName) curFile = n;
  // update folder membership
  for (const k in folders) folders[k] = folders[k].map(f => f === oldName ? n : f);
  // persist and refresh
  try { localStorage.setItem('aieditor_files', JSON.stringify(files)); } catch(e) {}
  if (window.editor && curFile === n) editor.setValue(files[n]);
  renderFiles(); renderTabs();
  document.getElementById('chatCtxFile').textContent = curFile;
  toast('Renamed to ' + n, 'success');
}

/** deleteFile — confirm, then remove the file everywhere.
 *  Always keeps at least one file alive.
 */
function deleteFile(name) {
  if (!confirm(`Delete "${name}"? This cannot be undone.`)) return;
  delete files[name];
  delete savedFiles[name];
  openTabs = openTabs.filter(f => f !== name);
  for (const k in folders) folders[k] = folders[k].filter(f => f !== name);
  // safety net: never let the editor be empty
  if (!Object.keys(files).length) {
    files['main.py'] = "print('hello world')";
    savedFiles['main.py'] = files['main.py'];
    openTabs = ['main.py'];
  }
  if (!openTabs.length) openTabs = [Object.keys(files)[0]];
  curFile = openTabs[0];
  if (window.editor) editor.setValue(files[curFile] || '');
  try { localStorage.setItem('aieditor_files', JSON.stringify(files)); } catch(e) {}
  renderFiles(); renderTabs();
  document.getElementById('chatCtxFile').textContent = curFile;
  toast('Deleted ' + name, 'info');
}

/** updateStats — refresh the L / C / W counters in the toolbar */
function updateStats() {
  const c = window.editor ? editor.getValue() : '';
  document.getElementById('stL').textContent = c.split('\n').length;
  document.getElementById('stC').textContent = c.length;
  document.getElementById('stW').textContent = c.trim() ? c.trim().split(/\s+/).length : 0;
}


/* ── AUTO-SAVE ───────────────────────────────────────────── */

/** triggerSave — debounced write to localStorage; updates the save pill */
function triggerSave() {
  const pill = document.getElementById('savePill');
  const txt  = document.getElementById('saveText');
  pill.className = 'save-pill saving'; txt.textContent = 'Saving…';
  clearTimeout(saveTimer);
  saveTimer = setTimeout(() => {
    savedFiles[curFile] = files[curFile];
    try { localStorage.setItem('aieditor_files', JSON.stringify(files)); } catch(e) {}
    pill.className = 'save-pill saved'; txt.textContent = 'Saved ✓';
    renderTabs(); renderFiles();
    setTimeout(() => { pill.className = 'save-pill'; txt.textContent = 'Auto-save on'; }, 2200);
  }, SAVE_DELAY);
}

/** loadSaved — restore files from localStorage on page load */
function loadSaved() {
  try {
    const s = localStorage.getItem('aieditor_files');
    if (s) { Object.assign(files, JSON.parse(s)); savedFiles = JSON.parse(JSON.stringify(files)); }
  } catch(e) {}
}


/* ── AI PANEL TAB SWITCHER ───────────────────────────────── */

/** switchAITab — toggle between the Response tab and the Chat tab
 *  @param {'response'|'chat'} tab
 */
function switchAITab(tab) {
  document.getElementById('tabResp').classList.toggle('active', tab === 'response');
  document.getElementById('tabChat').classList.toggle('active', tab === 'chat');
  document.getElementById('aiRespPanel').style.display  = tab === 'response' ? 'flex' : 'none';
  document.getElementById('aiChatPanel').style.display  = tab === 'chat'     ? 'flex' : 'none';
  if (tab === 'chat') document.getElementById('chatInput').focus();
}


/* ── AI CHAT ─────────────────────────────────────────────── */

/** chatAutosize — grow the textarea height as the user types (max ~5 lines) */
function chatAutosize(el) {
  el.style.height = 'auto';
  el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

/** chatKeydown — send on Enter; Shift+Enter inserts a newline instead */
function chatKeydown(e) {
  if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendChat(); }
}

/** appendMsg — add a chat bubble to the message list and return the bubble element
 *  @param {'user'|'ai'} role
 *  @param {string}  text
 *  @param {boolean} thinking  — if true, renders in muted italic style
 *  @returns {HTMLElement}  the bubble div (so the caller can update it later)
 */
function appendMsg(role, text, thinking = false) {
  // hide the "ask anything" empty state on first message
  const empty = document.getElementById('chatEmpty');
  if (empty) empty.style.display = 'none';

  const msgs = document.getElementById('chatMsgs');
  const time = new Date().toLocaleTimeString([], { hour:'2-digit', minute:'2-digit' });

  const wrap = document.createElement('div');
  wrap.className = `chat-msg ${role} ${thinking ? 'thinking' : ''}`;
  wrap.innerHTML = `<div class="chat-bubble">${H(text)}</div><div class="chat-meta">${time}</div>`;
  msgs.appendChild(wrap);
  msgs.scrollTop = msgs.scrollHeight;
  return wrap.querySelector('.chat-bubble');
}

/** sendChat — POST user message + current file code to /chat, stream reply into chat */
async function sendChat() {
  const inp = document.getElementById('chatInput');
  const msg = inp.value.trim();
  if (!msg || chatBusy) return;

  chatBusy = true;
  document.getElementById('chatSendBtn').disabled = true;
  inp.value = ''; inp.style.height = 'auto';

  appendMsg('user', msg); // show user bubble immediately

  // show a "Thinking…" bubble while waiting for the server
  const aiBubble = appendMsg('ai', 'Thinking…', true);

  try {
    const res = await fetch('/chat', {
      method: 'POST',
      headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':csrf() },
      body: JSON.stringify({
        message:  msg,
        code:     window.editor ? editor.getValue() : '',
        language: document.getElementById('language').value,
      })
    });
    const data  = await res.json();
    const reply = data.result || data.reply || data.message || data.error || 'No response';

    // replace the "Thinking…" bubble with the real reply
    aiBubble.closest('.chat-msg').classList.remove('thinking');
    aiBubble.textContent = reply;
    document.getElementById('chatMsgs').scrollTop = document.getElementById('chatMsgs').scrollHeight;

  } catch (err) {
    aiBubble.closest('.chat-msg').classList.remove('thinking');
    aiBubble.textContent = 'Error: ' + err.message;
  } finally {
    chatBusy = false;
    document.getElementById('chatSendBtn').disabled = false;
    document.getElementById('chatInput').focus();
  }
}


/* ── LIVE PREVIEW ────────────────────────────────────────── */

/** togglePreview — open or close the live preview panel */
function togglePreview() {
  prevOpen = !prevOpen;
  const box = document.getElementById('pvBox'), dv  = document.getElementById('pvdiv');
  const btn = document.getElementById('prevBtn'), ic  = document.getElementById('prevIcon');
  const tx  = document.getElementById('prevTxt'),  sb  = document.getElementById('pvStatus');
  if (prevOpen) {
    box.classList.add('open'); dv.classList.add('on');
    btn.classList.add('prev-on'); ic.className = 'fa-solid fa-eye-slash'; tx.textContent = 'Hide';
    sb.classList.add('on'); syncPrevLang(); updatePrev();
  } else {
    box.classList.remove('open'); dv.classList.remove('on');
    btn.classList.remove('prev-on'); ic.className = 'fa-solid fa-eye'; tx.textContent = 'Preview';
    sb.classList.remove('on');
  }
}

/** syncPrevLang — auto-select React or HTML preview tab to match the current file */
function syncPrevLang() { setPrevTab(prevType() === 'react' ? 'react' : 'html'); }

/** setPrevTab — activate 'html' or 'react' sub-tab and refresh */
function setPrevTab(t) {
  prevTab = t;
  document.getElementById('tabHtml').classList.toggle('active',  t === 'html');
  document.getElementById('tabReact').classList.toggle('active', t === 'react');
  document.getElementById('htmlWrap').style.display  = t === 'html'  ? '' : 'none';
  document.getElementById('reactWrap').style.display = t === 'react' ? '' : 'none';
  if (prevOpen) updatePrev();
}

/** refreshPrev — force-refresh the preview now */
function refreshPrev() { updatePrev(true); }

/** updatePrev — re-render the preview iframe from current editor content */
function updatePrev(force = false) {
  if (!prevOpen && !force) return;
  const code = window.editor ? editor.getValue() : '';
  const lang = document.getElementById('language').value;
  if (prevTab === 'react') updateReactPrev(code); else updateHtmlPrev(code, lang);
  const now = new Date();
  document.getElementById('pvTime').textContent = now.toLocaleTimeString([], {hour:'2-digit',minute:'2-digit',second:'2-digit'});
  document.getElementById('pvIcon').className = 'fa-solid fa-circle-check sok';
  document.getElementById('pvText').textContent = 'Preview synced';
}

/** updateHtmlPrev — wrap HTML/CSS/JS in a full document and inject via srcdoc */
function updateHtmlPrev(code, lang) {
  const ld = document.getElementById('pvLoad'); ld.classList.remove('hide');
  let src = '';
  if (lang === 'html') { src = code; }
  else if (lang === 'css') { src = `<!DOCTYPE html><html><head><style>body{font-family:Arial,sans-serif;padding:30px;margin:0}${code}</style></head><body><h1>CSS Preview</h1><p>Sample paragraph.</p><div style="width:120px;height:120px;background:steelblue;margin:20px 0"></div><button>Button</button></body></html>`; }
  else if (lang === 'javascript') { src = `<!DOCTYPE html><html><head><style>body{font-family:monospace;background:#1a1a2e;color:#e2eaff;padding:20px;font-size:13px}</style></head><body><div id="o"></div><script>(function(){var o=document.getElementById("o");var ol=console.log;console.log=function(){var a=Array.from(arguments);ol.apply(console,a);var d=document.createElement("div");d.textContent=a.map(x=>typeof x==="object"?JSON.stringify(x):String(x)).join(" ");o.appendChild(d)};try{${code}}catch(e){var d=document.createElement("div");d.style.color="#f87171";d.textContent="Error: "+e.message;o.appendChild(d)}})();<\/script></body></html>`; }
  else { src = '<html><body style="font-family:monospace;padding:20px;background:#080c14;color:#45587a"><p>Preview available for HTML, CSS, JavaScript, and React.</p></body></html>'; }
  const iframe = document.getElementById('piframe');
  iframe.srcdoc = src;
  iframe.onload = () => ld.classList.add('hide');
  setTimeout(() => ld.classList.add('hide'), 1500);
}

/** updateReactPrev — compile and render a React/JSX component in a sandboxed iframe */
function updateReactPrev(code) {
  document.getElementById('riframe').srcdoc = `<!DOCTYPE html><html><head><meta charset="UTF-8"><style>body{margin:0;font-family:system-ui,sans-serif}#root{min-height:100vh}.re{background:#1a0a0a;color:#f87171;padding:16px;font-family:monospace;font-size:12px;white-space:pre-wrap;border-left:3px solid #f87171;margin:12px}</style></head><body><div id="root"></div><script src="https://unpkg.com/react@18/umd/react.development.js"><\/script><script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"><\/script><script src="https://unpkg.com/@babel/standalone/babel.min.js"><\/script><script type="text/babel">try{${code}var r=ReactDOM.createRoot(document.getElementById("root"));if(typeof App!=="undefined")r.render(React.createElement(App));else document.getElementById("root").innerHTML="<div class='re'>No App component found.</div>";}catch(e){document.getElementById("root").innerHTML="<div class='re'>"+e.message+"<\/div>"}<\/script></body></html>`;
}

// drag-to-resize the preview panel
let pvDrag = false;
document.getElementById('pvdiv').addEventListener('mousedown', () => { pvDrag = true; document.getElementById('pvdiv').classList.add('drag'); });
document.addEventListener('mousemove', e => {
  if (!pvDrag) return;
  const r = document.getElementById('split').getBoundingClientRect();
  const lw = e.clientX - r.left - 2, rw = r.width - lw - 4;
  if (lw > 240 && rw > 190) {
    document.getElementById('edBox').style.cssText = `flex:none;width:${lw}px`;
    document.getElementById('pvBox').style.cssText = `width:${rw}px;min-width:0`;
  }
});
document.addEventListener('mouseup', () => { pvDrag = false; document.getElementById('pvdiv').classList.remove('drag'); });


/* ── MONACO SETUP ────────────────────────────────────────── */
require.config({ paths: { vs: 'https://unpkg.com/monaco-editor@0.44.0/min/vs' } });
require(['vs/editor/editor.main'], function () {
  loadSaved();

  window.editor = monaco.editor.create(document.getElementById('editor'), {
    value: files[curFile], language:'python', theme:'vs-dark', automaticLayout:true,
    minimap:{enabled:false}, fontSize:13, fontFamily:"'JetBrains Mono',monospace",
    fontLigatures:true, lineHeight:22, padding:{top:14,bottom:14},
    scrollbar:{verticalScrollbarSize:4,horizontalScrollbarSize:4},
    renderLineHighlight:'gutter', cursorBlinking:'expand',
    cursorSmoothCaretAnimation:'on', smoothScrolling:true,
    bracketPairColorization:{enabled:true},
    quickSuggestions:false, suggestOnTriggerCharacters:false, wordBasedSuggestions:'off',
  });

  /* AI AUTOCOMPLETE ──────────────────────────────────────── */
  const popup  = document.getElementById('ac-popup');
  const acList = document.getElementById('acList');
  let acT = null, acCtrl = null, acItems = [], acIdx = -1, acPos = null;

  /** acPosition — move popup below the current cursor position */
  function acPosition() {
    const p = editor.getPosition(); if (!p) return;
    const c = editor.getScrolledVisiblePosition(p); if (!c) return;
    const li = editor.getLayoutInfo(), tw = document.getElementById('tabs').offsetHeight;
    popup.style.left = Math.min(Math.max(6, li.contentLeft + c.left), document.getElementById('edBox').offsetWidth - 280) + 'px';
    popup.style.top  = (tw + c.top + 24) + 'px';
  }

  /** acRender — rebuild the suggestion list */
  function acRender() {
    acList.innerHTML = '';
    acItems.forEach((t, i) => {
      const row = document.createElement('div');
      row.className = 'ac-item' + (i === acIdx ? ' sel' : '');
      row.innerHTML = `<span class="ac-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></span><span class="ac-code">${H(t.split('\n')[0])}</span>`;
      row.addEventListener('mousedown', e => { e.preventDefault(); acAccept(i); });
      acList.appendChild(row);
    });
  }

  function acShow(sug, pos) {
    acItems = sug; acIdx = 0; acPos = pos;
    document.getElementById('acSpin').style.display = 'none';
    document.getElementById('acLabel').textContent  = sug.length === 1 ? '1 suggestion' : sug.length + ' suggestions';
    acRender(); acPosition(); popup.style.display = 'flex';
  }
  function acHide() { popup.style.display = 'none'; acItems = []; acIdx = -1; }

  /** acAccept — insert the chosen suggestion at the stored cursor position */
  function acAccept(i) {
    const t = acItems[i]; if (!t || !acPos) return;
    editor.executeEdits('ai-ac', [{ range: new monaco.Range(acPos.lineNumber, acPos.column, acPos.lineNumber, acPos.column), text: t }]);
    const ls = t.split('\n');
    editor.setPosition({ lineNumber: acPos.lineNumber + ls.length - 1, column: ls.length > 1 ? ls[ls.length-1].length + 1 : acPos.column + t.length });
    editor.focus(); acHide();
  }

  /** acFetch — POST code context to /autocomplete and show results */
  async function acFetch(pos) {
    if (acCtrl) acCtrl.abort(); acCtrl = new AbortController();
    const model = editor.getModel(), lang = document.getElementById('language').value;
    const ctx   = model.getValueInRange({ startLineNumber:1, startColumn:1, endLineNumber:pos.lineNumber, endColumn:pos.column });
    const full  = model.getValue(); if (full.trim().length < 3) { acHide(); return; }
    document.getElementById('acSpin').style.display = 'block';
    document.getElementById('acLabel').textContent  = 'thinking…';
    acList.innerHTML = ''; acPosition(); popup.style.display = 'flex';
    try {
      const r = await fetch('/autocomplete', { method:'POST', signal:acCtrl.signal, headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()}, body:JSON.stringify({ code:ctx, full_code:full, language:lang }) });
      if (!r.ok) { acHide(); return; }
      const d = await r.json();
      let s = [];
      if (Array.isArray(d.suggestions) && d.suggestions.length) s = d.suggestions.map(x => x.trim()).filter(Boolean);
      else if (typeof d.suggestion === 'string' && d.suggestion.trim()) s = [d.suggestion.trim()];
      if (!s.length) { acHide(); return; }
      acShow(s, pos);
    } catch(e) { if (e.name !== 'AbortError') acHide(); }
  }

  // sync content, save, refresh preview, and debounce autocomplete on every keystroke
  editor.onDidChangeModelContent(() => {
    files[curFile] = editor.getValue(); updateStats(); renderTabs(); triggerSave();
    if (errHL) runErrHL(); acHide(); clearTimeout(acT);
    const p = editor.getPosition(); acT = setTimeout(() => acFetch(p), 900);
    if (prevOpen) { clearTimeout(prevTimer); prevTimer = setTimeout(() => updatePrev(), PREV_DELAY); }
  });

  // keyboard nav inside the autocomplete popup
  editor.onKeyDown(e => {
    if (popup.style.display !== 'flex') return;
    if (e.keyCode === monaco.KeyCode.UpArrow)   { e.preventDefault(); e.stopPropagation(); acIdx = Math.max(0, acIdx-1); acRender(); }
    if (e.keyCode === monaco.KeyCode.DownArrow) { e.preventDefault(); e.stopPropagation(); acIdx = Math.min(acItems.length-1, acIdx+1); acRender(); }
    if (e.keyCode === monaco.KeyCode.Enter || e.keyCode === monaco.KeyCode.Tab) { e.preventDefault(); e.stopPropagation(); if (acIdx >= 0) acAccept(acIdx); }
    if (e.keyCode === monaco.KeyCode.Escape)    { e.preventDefault(); e.stopPropagation(); acHide(); }
  });
  editor.onDidBlurEditorWidget(acHide);

  // initial render and set up AI panel default state
  renderFiles(); renderTabs(); updateStats();
  document.getElementById('aiRespPanel').style.display  = 'flex';
  document.getElementById('aiChatPanel').style.display  = 'none';
});


/* ── LANGUAGE / THEME / SEARCH ───────────────────────────── */

/** changeLang — update Monaco syntax highlighting when the user picks a new language */
function changeLang() {
  const l = document.getElementById('language').value;
  if (window.editor) monaco.editor.setModelLanguage(editor.getModel(), l === 'jsx' ? 'javascript' : l);
  if (prevOpen) { syncPrevLang(); updatePrev(); }
}

/** toggleTheme — switch between dark and light Monaco themes */
function toggleTheme() {
  darkMode = !darkMode;
  document.getElementById('themeIcon').className = darkMode ? 'fa-solid fa-moon' : 'fa-solid fa-sun';
  if (window.editor) monaco.editor.setTheme(darkMode ? 'vs-dark' : 'vs');
  toast(darkMode ? 'Dark mode' : 'Light mode', 'info');
}

/** toggleSearch — show or hide the find-and-replace bar */
function toggleSearch() {
  srchOpen = !srchOpen;
  document.getElementById('searchBar').classList.toggle('on', srchOpen);
  document.getElementById('srchBtn').classList.toggle('active', srchOpen);
  if (srchOpen) document.getElementById('srchIn').focus();
  else { if (window.editor) editor.deltaDecorations([], []); document.getElementById('srchCnt').textContent = ''; srchMatches = []; }
}

/** doSearch — highlight all matches and update the match count label */
function doSearch() {
  if (!window.editor) return;
  const q = document.getElementById('srchIn').value;
  if (!q) { editor.deltaDecorations(srchMatches, []); srchMatches = []; document.getElementById('srchCnt').textContent = ''; return; }
  const ms = editor.getModel().findMatches(q, false, false, false, null, true);
  document.getElementById('srchCnt').textContent = ms.length ? ms.length + ' match' + (ms.length > 1 ? 'es' : '') : 'No matches';
  srchMatches = editor.deltaDecorations(srchMatches, ms.map(m => ({ range:m.range, options:{ inlineClassName:'search-highlight' } })));
}

/** doReplace — replace the next occurrence */
function doReplace() {
  if (!window.editor) return;
  const q = document.getElementById('srchIn').value, r = document.getElementById('replIn').value; if (!q) return;
  const m = editor.getModel().findNextMatch(q, editor.getPosition(), false, false, null, true);
  if (m) { editor.executeEdits('rep', [{ range:m.range, text:r }]); doSearch(); toast('Replaced 1','info'); } else toast('No match','error');
}

/** doReplaceAll — replace all occurrences (reversed so indices stay valid) */
function doReplaceAll() {
  if (!window.editor) return;
  const q = document.getElementById('srchIn').value, r = document.getElementById('replIn').value; if (!q) return;
  const ms = editor.getModel().findMatches(q, false, false, false, null, true);
  if (!ms.length) { toast('No matches','error'); return; }
  editor.executeEdits('rep-all', [...ms].reverse().map(m => ({ range:m.range, text:r })));
  doSearch(); toast('Replaced ' + ms.length, 'success');
}


/* ── FORMAT / EXPLAIN / DOWNLOAD / ERRORS ───────────────── */

/** fmtCode — format via /format endpoint; falls back to Monaco's built-in formatter */
async function fmtCode() {
  if (!window.editor) return; toast('Formatting…','info');
  try {
    const r = await fetch('/format', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()}, body:JSON.stringify({ code:editor.getValue(), language:document.getElementById('language').value }) });
    if (r.ok) { const d = await r.json(); if (d.formatted) { editor.setValue(d.formatted); toast('Formatted','success'); return; } }
  } catch(e) {}
  editor.getAction('editor.action.formatDocument').run(); toast('Formatted','success');
}

/** explainCode — send selected code (or whole file) to /explain and show in modal */
async function explainCode() {
  const el = document.getElementById('explainOut'); el.className = 'expl ld'; el.textContent = 'Asking AI…'; openModal('explainModal');
  const sel  = window.editor ? editor.getModel().getValueInRange(editor.getSelection()) : '';
  const code = sel.trim() ? sel : (window.editor ? editor.getValue() : '');
  try {
    const r = await fetch('/explain', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()}, body:JSON.stringify({ code, language:document.getElementById('language').value }) });
    const d = await r.json(); el.className = 'expl'; el.textContent = d.result || d.explanation || '— No explanation returned —';
  } catch(e) { el.className = 'expl'; el.textContent = '— Error —'; }
}

/** dlFile — trigger a browser download of the current file */
function dlFile() {
  const url = URL.createObjectURL(new Blob([window.editor ? editor.getValue() : '']));
  const a   = Object.assign(document.createElement('a'), { href:url, download:curFile });
  document.body.appendChild(a); a.click(); document.body.removeChild(a); URL.revokeObjectURL(url);
  toast('Downloaded ' + curFile, 'success');
}

/** toggleErrHL — turn gutter error/warning highlights on or off */
function toggleErrHL() {
  errHL = !errHL;
  document.getElementById('errBtn').classList.toggle('err-on', errHL);
  if (errHL) { runErrHL(); toast('Error highlighting on','info'); }
  else { if (window.editor) errDecs = editor.deltaDecorations(errDecs, []); toast('Error highlighting off','info'); }
}

/** runErrHL — apply coloured gutter decorations from Monaco model markers */
function runErrHL() {
  if (!window.editor || !errHL) return;
  const mk = monaco.editor.getModelMarkers({ resource: editor.getModel().uri });
  errDecs = editor.deltaDecorations(errDecs, mk.filter(x => x.severity >= monaco.MarkerSeverity.Warning).map(x => ({
    range: new monaco.Range(x.startLineNumber, 1, x.endLineNumber, 1),
    options: { isWholeLine:true, glyphMarginHoverMessage:{ value:`**${x.severity===monaco.MarkerSeverity.Error?'Error':'Warning'}**: ${x.message}` }, overviewRuler:{ color:'#f87171', position:monaco.editor.OverviewRulerLane.Right } }
  })));
}


/* ── TERMINAL ────────────────────────────────────────────── */

/** termSet — replace terminal output HTML and scroll to bottom */
function termSet(html) {
  document.getElementById('termOut').innerHTML = html;
  setTimeout(() => { document.getElementById('termWrap').scrollTop = document.getElementById('termWrap').scrollHeight; }, 0);
}

/** termClear — clear output, hide exit badge, return to output view */
function termClear(e) {
  if (e) { e.preventDefault(); e.stopPropagation(); }
  showView('output');
  termSet('<span class="tp">~/project $</span> <span class="ti">terminal cleared</span>');
  document.getElementById('exitBadge').style.display = 'none';
  document.getElementById('termTime').textContent    = '';
  _runCode = null; _runLang = null;
}

/** termCopy — copy plain terminal text to the clipboard */
function termCopy(e) {
  if (e) { e.preventDefault(); e.stopPropagation(); }
  navigator.clipboard.writeText(document.getElementById('termOut').innerText)
    .then(() => toast('Copied!','success')).catch(() => toast('Copy failed','error'));
}

/** termFS — adjust terminal font size by d px (clamped 9–20) */
function termFS(d) {
  termFontSz = Math.min(20, Math.max(9, termFontSz + d));
  document.getElementById('termOut').style.fontSize = termFontSz + 'px';
}

/** showView — switch terminal between 'output' and 'stdin' views */
function showView(v) {
  document.getElementById('termViewOutput').style.display = v === 'output' ? 'flex' : 'none';
  document.getElementById('termViewStdin').style.display  = v === 'stdin'  ? 'flex' : 'none';
}

/** termCancelStdin — cancel the pending stdin run and go back to output */
function termCancelStdin() { showView('output'); _runCode = null; _runLang = null; }

/** termRunWithStdin — run the stored code using the stdin textarea content */
async function termRunWithStdin() {
  showView('output');
  await execCode(_runCode, _runLang, document.getElementById('termStdin').value);
}

/** setBadge — show EXIT 0 (green) or EXIT 1 (red) with a timestamp */
function setBadge(isErr) {
  const b = document.getElementById('exitBadge');
  b.style.display = 'inline-block'; b.textContent = isErr ? 'EXIT 1' : 'EXIT 0';
  b.className = 'exit-badge ' + (isErr ? 'err' : 'ok');
  document.getElementById('termTime').textContent = new Date().toLocaleTimeString([], {hour:'2-digit',minute:'2-digit',second:'2-digit'});
}

/** setRunDot — pulse the green dot while code is running */
function setRunDot(on) { document.getElementById('runDot').classList.toggle('on', on); }


/* ── CODE EXECUTION ──────────────────────────────────────── */

/** execCode — POST code + stdin to /run, render coloured output in terminal */
async function execCode(code, lang, stdin) {
  const time = new Date().toLocaleTimeString([], {hour:'2-digit',minute:'2-digit',second:'2-digit'});
  termSet(`<span class="tp">~/project $</span> <span class="ti">run ${H(lang)} · ${time}</span>\n<span class="tw">⏳ executing…</span>`);
  document.getElementById('exitBadge').style.display = 'none'; setRunDot(true);
  try {
    const r = await fetch('/run', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()}, body:JSON.stringify({ code, language:lang, input:stdin||'' }) });
    const d = await r.json();
    const out = d.output || d.error || 'No output', isErr = !!d.error || r.status >= 400;
    setBadge(isErr);
    const html = `<span class="tp">~/project $</span> <span class="ti">run ${H(lang)} · ${time}</span>\n`
               + out.split('\n').map(l => isErr ? `<span class="ter">${H(l)}</span>` : `<span class="tok">${H(l)}</span>`).join('\n');
    termSet(html);
    runHistory.push({ lang, code, output:out, isError:isErr, timestamp:new Date().toLocaleTimeString() });
    if (runHistory.length > RH_MAX) runHistory.shift();
  } catch(e) { termSet(`<span class="tp">~/project $</span> <span class="ter">ERROR: ${H(e.message)}</span>`); setBadge(true); }
  finally { setRunDot(false); }
}

/** runCode — Run button handler.
 *  Web languages → preview panel.
 *  Code with input() / cin / scanf → show stdin view first.
 *  Everything else → execute directly.
 */
async function runCode() {
  const code = window.editor ? editor.getValue() : '';
  const lang = document.getElementById('language').value;
  if (['html','css','javascript','jsx'].includes(lang)) {
    if (!prevOpen) togglePreview(); else updatePrev(true);
    termSet('<span class="tp">~/project $</span> <span class="ti">rendered in preview panel</span>');
    return;
  }
  if (/\binput\s*\(|std::cin|scanf\s*\(|readline\s*\(|gets\s*\(|Scanner/.test(code)) {
    _runCode = code; _runLang = lang;
    document.getElementById('termStdin').value = '';
    showView('stdin'); document.getElementById('termStdin').focus();
  } else {
    showView('output'); await execCode(code, lang, '');
  }
}


/* ── AI SUGGEST & APPLY ──────────────────────────────────── */

/** askAI — POST code to /suggest and display the response in the AI panel */
async function askAI() {
  const el = document.getElementById('result'); el.textContent = 'Thinking…'; lastAI = null;
  document.getElementById('aiShim').classList.add('on'); document.getElementById('aiDot').classList.add('on');
  try {
    const r = await fetch('/suggest', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()}, body:JSON.stringify({ code:window.editor?editor.getValue():'', language:document.getElementById('language').value }) });
    const d = await r.json(); lastAI = d.result || null; el.textContent = d.result || d.error || JSON.stringify(d); el.className = 'has';
  } catch(e) { lastAI = null; el.textContent = 'AI ERROR: ' + e.message; el.className = ''; }
  finally { document.getElementById('aiShim').classList.remove('on'); document.getElementById('aiDot').classList.remove('on'); }
}

/** applyFix — replace editor content with the last AI suggestion (asks for confirmation) */
function applyFix() {
  if (!lastAI || !lastAI.trim()) { toast('No AI suggestion to apply','error'); return; }
  if (!confirm('Replace entire file with AI suggestion?')) return;
  if (window.editor) editor.setValue(lastAI);
  files[curFile] = lastAI;
  if (prevOpen) updatePrev(true);
  toast('AI fix applied', 'success');
}


/* ── STATS MODAL ─────────────────────────────────────────── */

/** openStatsModal — compute totals across all files and render in modal */
function openStatsModal() {
  let tL = 0, tC = 0, tW = 0; const lc = {};
  for (const n in files) { const c = files[n], e = n.split('.').pop().toLowerCase(); tL += c.split('\n').length; tC += c.length; tW += c.trim() ? c.trim().split(/\s+/).length : 0; lc[e] = (lc[e]||0) + 1; }
  const fc = Object.keys(files).length, sc = runHistory.filter(r => !r.isError).length;
  const mx = Math.max(...Object.values(lc), 1);
  const clr = {py:'#60a5fa',js:'#fbbf24',html:'#fb923c',css:'#38bdf8',jsx:'#22d3ee',php:'#a78bfa',java:'#f87171'};
  const bars = Object.entries(lc).map(([e,c]) => `<div class="lb-row"><span class="lb-lbl">.${H(e)}</span><div class="lb-trk"><div class="lb-fill" style="width:${Math.round(c/mx*100)}%;background:${clr[e]||'#5b9cf6'}"></div></div><span class="lb-cnt">${c}</span></div>`).join('');
  document.getElementById('statsBody').innerHTML = `<div class="stats-grid"><div class="sc bl"><div class="sv">${fc}</div><div class="sl">Files</div></div><div class="sc cy"><div class="sv">${tL.toLocaleString()}</div><div class="sl">Lines</div></div><div class="sc vi"><div class="sv">${tC.toLocaleString()}</div><div class="sl">Chars</div></div><div class="sc am"><div class="sv">${tW.toLocaleString()}</div><div class="sl">Words</div></div><div class="sc gr"><div class="sv">${sc}</div><div class="sl">Successful Runs</div></div><div class="sc rd"><div class="sv">${runHistory.length-sc}</div><div class="sl">Failed Runs</div></div></div><div class="sec-title">File types</div><div class="lb-list">${bars||'<div style="color:var(--t3);font-size:12px">No files</div>'}</div>`;
  openModal('statsModal');
}


/* ── FOLDERS MODAL ───────────────────────────────────────── */

/** openFoldersModal — show all folders plus an "Unfiled" group */
function openFoldersModal() {
  let h = '<div class="folder-tree">';
  for (const fn in folders) {
    const ff = folders[fn];
    h += `<div class="fi"><i class="fa-solid fa-folder-open"></i><span>${H(fn)}</span><span class="fc">${ff.length} file${ff.length!==1?'s':''}</span></div><div class="ff">${ff.map(f=>`<div class="ffi" onclick="closeModal('foldersModal');openFile('${f}')"><i class="${iconCls(f)}" style="color:${iconCol(f)}"></i>${H(f)}</div>`).join('')}</div>`;
  }
  const filed = Object.values(folders).flat();
  const unfiled = Object.keys(files).filter(f => !filed.includes(f));
  if (unfiled.length) h += `<div class="fi"><i class="fa-solid fa-folder" style="color:var(--t3)"></i><span>Unfiled</span><span class="fc">${unfiled.length}</span></div><div class="ff">${unfiled.map(f=>`<div class="ffi" onclick="closeModal('foldersModal');openFile('${f}')"><i class="${iconCls(f)}" style="color:${iconCol(f)}"></i>${H(f)}</div>`).join('')}</div>`;
  h += '</div><button class="fnew" onclick="mkFolder()"><i class="fa-solid fa-folder-plus"></i> New Folder</button>';
  document.getElementById('foldersBody').innerHTML = h; openModal('foldersModal');
}

/** mkFolder — create a new folder, optionally move the current file into it */
function mkFolder() {
  const n = prompt('Folder name:'); if (!n) return;
  folders[n] = [];
  if (confirm(`Add "${curFile}" to "${n}"?`)) { for (const k in folders) folders[k] = folders[k].filter(f => f !== curFile); folders[n].push(curFile); }
  toast(`Folder "${n}" created`, 'success'); openFoldersModal();
}


/* ── RUN HISTORY MODAL ───────────────────────────────────── */

/** openRunHistoryModal — list all past runs in reverse chronological order */
function openRunHistoryModal() {
  let h = '';
  if (!runHistory.length) { h = '<div class="rhe"><i class="fa-solid fa-clock-rotate-left" style="font-size:22px;opacity:.2;display:block;margin-bottom:8px"></i>No runs yet</div>'; }
  else {
    h = '<div class="rhl">';
    [...runHistory].reverse().forEach((e, i) => {
      const ri = runHistory.length - 1 - i;
      h += `<div class="rhi"><div class="rhm"><span class="rhlg">${H(e.lang)}</span><i class="fa-solid fa-circle${e.isError?'-xmark rher':'-check rhok'}"></i><span>${e.isError?'Error':'Success'}</span><span style="margin-left:auto">${H(e.timestamp)}</span></div><div class="rhc">${H(e.code.substring(0,120))}${e.code.length>120?'…':''}</div><div class="rho ${e.isError?'er':'ok'}">${H(String(e.output||'').substring(0,150))}</div><button class="rhr" onclick="rhRestore(${ri})"><i class="fa-solid fa-arrow-rotate-left"></i> Restore</button></div>`;
    });
    h += '</div>';
  }
  document.getElementById('rhBody').innerHTML = h; openModal('runHistoryModal');
}

/** rhRestore — load a past run's code back into the editor */
function rhRestore(i) {
  const e = runHistory[i]; if (!e) return;
  if (window.editor) editor.setValue(e.code); files[curFile] = e.code;
  closeModal('runHistoryModal'); toast('Code restored','info');
}


/* ── SHARE MODAL ─────────────────────────────────────────── */

/** openShareModal — generate a shareable URL with base64-encoded code */
function openShareModal() {
  const code = window.editor ? editor.getValue() : '', lang = document.getElementById('language').value;
  const link = location.origin + '/share?lang=' + lang + '&code=' + btoa(unescape(encodeURIComponent(code)));
  document.getElementById('shrLink').textContent = link;
  document.getElementById('shrFile').textContent = curFile;
  document.getElementById('shrLang').textContent = lang;
  openModal('shareModal');
}

/** copyShrLink — copy the share URL to the clipboard */
function copyShrLink() {
  navigator.clipboard.writeText(document.getElementById('shrLink').textContent)
    .then(() => toast('Link copied!','success')).catch(() => toast('Copy failed','error'));
}


/* ── AI PANEL HORIZONTAL RESIZE ─────────────────────────── */
/* Drag the left edge of the AI panel to resize it wider/narrower */
let aiDrag = false;
document.getElementById('aidiv').addEventListener('mousedown', () => {
  aiDrag = true;
  document.getElementById('aidiv').classList.add('drag');
});
document.addEventListener('mousemove', e => {
  if (!aiDrag) return;
  const rect = document.querySelector('.app').getBoundingClientRect();
  const newW = Math.max(260, Math.min(620, rect.right - e.clientX));
  document.querySelector('.ai-panel').style.width = newW + 'px';
});
document.addEventListener('mouseup', () => {
  if (!aiDrag) return;
  aiDrag = false;
  document.getElementById('aidiv').classList.remove('drag');
});


/* ── AI PANEL VERTICAL RESIZE ────────────────────────────── */
/* Drag the bar between the AI response/chat area and the terminal */
let aiVDrag = false;
document.getElementById('ai-vdiv').addEventListener('mousedown', () => {
  aiVDrag = true;
  document.getElementById('ai-vdiv').classList.add('drag');
});
document.addEventListener('mousemove', e => {
  if (!aiVDrag) return;
  const panel = document.querySelector('.ai-panel');
  const rect  = panel.getBoundingClientRect();
  const topH  = Math.max(80, Math.min(rect.height - 100, e.clientY - rect.top));
  const botH  = rect.height - topH - 4; // 4px = divider height
  // resize whichever AI section is currently visible
  const resp  = document.getElementById('aiRespPanel');
  const chat  = document.getElementById('aiChatPanel');
  const term  = document.querySelector('.term-section');
  if (resp.style.display !== 'none') {
    resp.style.flex   = 'none';
    resp.style.height = topH + 'px';
  } else {
    chat.style.flex   = 'none';
    chat.style.height = topH + 'px';
  }
  term.style.flex      = 'none';
  term.style.height    = botH + 'px';
  term.style.minHeight = '0';
});
document.addEventListener('mouseup', () => {
  if (!aiVDrag) return;
  aiVDrag = false;
  document.getElementById('ai-vdiv').classList.remove('drag');
});

</script>
</body>
</html>
