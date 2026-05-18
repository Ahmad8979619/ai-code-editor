<!DOCTYPE html>
<html>
<head>
<title>AiEditor</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Geist:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root {
  --bg:      #0f1117;
  --panel:   #161b27;
  --raised:  #1c2333;
  --hover:   #222d42;
  --border:  rgba(255,255,255,0.07);
  --bordm:   rgba(255,255,255,0.12);
  --accent:  #4f8ef7;
  --adim:    rgba(79,142,247,0.12);
  --aglow:   0 0 18px rgba(79,142,247,0.25);
  --green:   #3ecf7e;
  --red:     #f06a6a;
  --amber:   #f5a623;
  --cyan:    #38bdf8;
  --t1: #e8edf5; --t2: #7a8ba8; --t3: #3f4f68;
  --ui: 'Geist',sans-serif;
  --mono: 'JetBrains Mono',monospace;
  --nav: 52px; --side: 210px; --r: 10px; --rs: 6px;
}
*,*::before,*::after { box-sizing:border-box; margin:0; padding:0; }
body { font-family:var(--ui); background:var(--bg); color:var(--t1); height:100vh; overflow:hidden; font-size:14px; }
body::before {
  content:''; position:fixed; inset:0; pointer-events:none; z-index:0;
  background: radial-gradient(ellipse 70% 60% at 15% 0%,rgba(79,142,247,0.05) 0%,transparent 55%),
              radial-gradient(ellipse 50% 40% at 85% 100%,rgba(56,189,248,0.03) 0%,transparent 50%);
}

/* ── NAVBAR ─────────────────────────────── */
.navbar {
  position:relative; z-index:50; height:var(--nav);
  display:flex; align-items:center; gap:6px; padding:0 14px;
  background:var(--panel); border-bottom:1px solid var(--border);
}
.logo { display:flex; align-items:center; gap:8px; font-size:14px; font-weight:600; margin-right:4px; }
.logo-icon {
  width:26px; height:26px; border-radius:7px; background:var(--accent);
  display:flex; align-items:center; justify-content:center; font-size:11px; color:#fff;
  box-shadow:0 0 12px rgba(79,142,247,0.4);
}
.sep { width:1px; height:20px; background:var(--border); margin:0 4px; flex-shrink:0; }
.spacer { flex:1; }

.lang-wrap { position:relative; display:flex; align-items:center; }
.lang-wrap i { position:absolute; left:9px; font-size:10px; color:var(--t3); pointer-events:none; z-index:1; }
select {
  appearance:none; padding:5px 10px 5px 26px; border-radius:var(--rs);
  background:var(--raised); color:var(--t1); border:1px solid var(--border);
  font-family:var(--ui); font-size:12.5px; cursor:pointer; outline:none;
}
select:hover { border-color:var(--bordm); }
select option { background:var(--raised); }

.save-pill {
  display:flex; align-items:center; gap:5px; padding:4px 10px;
  border-radius:20px; font-size:11px; color:var(--t3);
  background:var(--raised); border:1px solid var(--border); white-space:nowrap;
}
.save-pill.saving { color:var(--amber); border-color:rgba(245,166,35,0.25); }
.save-pill.saved  { color:var(--green); border-color:rgba(62,207,126,0.25); }

.btn {
  display:inline-flex; align-items:center; gap:5px; padding:5px 11px;
  border-radius:var(--rs); font-family:var(--ui); font-size:12.5px; font-weight:500;
  cursor:pointer; border:1px solid transparent; white-space:nowrap;
  color:var(--t2); background:transparent; transition:background .12s,transform .1s;
}
.btn:hover { color:var(--t1); background:var(--raised); border-color:var(--border); transform:translateY(-1px); }
.btn:active { transform:translateY(0); }
.btn.ico { padding:5px 7px; color:var(--t3); }
.btn.ico:hover { color:var(--t1); }
.btn.primary { background:var(--accent); color:#fff; border:none; padding:5px 16px; box-shadow:var(--aglow); }
.btn.primary:hover { background:#6a9ef9; border:none; color:#fff; }
.btn.suggest { color:var(--accent); border-color:rgba(79,142,247,0.2); background:var(--adim); }
.btn.suggest:hover { background:rgba(79,142,247,0.2); }
.btn.prev-on { color:var(--green); border-color:rgba(62,207,126,0.25); background:rgba(62,207,126,0.08); }
.btn.danger { color:var(--red); }
.btn.danger:hover { background:rgba(240,106,106,0.1); border-color:rgba(240,106,106,0.2); color:var(--red); }

/* ── LAYOUT ─────────────────────────────── */
.app { position:relative; z-index:1; display:flex; height:calc(100vh - var(--nav)); overflow:hidden; }

/* ── SIDEBAR ────────────────────────────── */
.sidebar {
  width:var(--side); flex-shrink:0; background:var(--panel);
  border-right:1px solid var(--border); display:flex; flex-direction:column; overflow:hidden;
}
.sidebar-hdr {
  display:flex; align-items:center; padding:11px 13px 9px; gap:7px;
  font-size:10px; font-weight:600; letter-spacing:.1em; text-transform:uppercase;
  color:var(--t3); border-bottom:1px solid var(--border); flex-shrink:0;
}
.file-list { flex:1; overflow-y:auto; padding:5px; }
.file-item {
  display:flex; align-items:center; gap:7px; padding:6px 9px;
  border-radius:var(--rs); cursor:pointer; font-size:12px; color:var(--t2);
  border:1px solid transparent; transition:background .1s,color .1s;
}
.file-item:hover  { background:var(--hover); color:var(--t1); }
.file-item.active { background:var(--adim); color:var(--accent); border-color:rgba(79,142,247,0.2); }
.file-item .dot   { width:5px; height:5px; border-radius:50%; background:var(--amber); margin-left:auto; display:none; }
.file-item.unsaved .dot { display:block; }
.new-file-btn {
  margin:5px; padding:7px; border-radius:var(--rs); background:transparent;
  border:1px dashed var(--bordm); color:var(--t3); font-family:var(--ui); font-size:12px;
  cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px;
  transition:color .12s,border-color .12s,background .12s;
}
.new-file-btn:hover { color:var(--accent); border-color:rgba(79,142,247,0.35); background:var(--adim); }

/* ── EDITOR AREA ────────────────────────── */
.editor-area { flex:1; min-width:0; display:flex; flex-direction:column; overflow:hidden; }

.tabs-row {
  display:flex; align-items:flex-end; gap:2px; padding:7px 7px 0;
  background:var(--panel); border-bottom:1px solid var(--border);
  overflow-x:auto; flex-shrink:0; scrollbar-width:none;
}
.tabs-row::-webkit-scrollbar { display:none; }
.tab {
  display:inline-flex; align-items:center; gap:5px; padding:5px 11px 6px;
  border-radius:var(--rs) var(--rs) 0 0; font-size:11.5px; font-family:var(--mono);
  color:var(--t3); background:transparent; border:1px solid transparent; border-bottom:none;
  cursor:pointer; white-space:nowrap;
}
.tab:hover { color:var(--t2); background:var(--hover); }
.tab.active { color:var(--t1); background:var(--bg); border-color:var(--border); border-bottom:1px solid var(--bg); position:relative; top:1px; }
.tab .tdot { width:5px; height:5px; border-radius:50%; background:var(--amber); display:none; }
.tab.unsaved .tdot { display:block; }
.tab .tx { font-size:9px; opacity:.4; cursor:pointer; padding:1px 2px; }
.tab .tx:hover { opacity:1; color:var(--red); }

.toolbar {
  display:flex; align-items:center; gap:3px; padding:5px 9px;
  background:var(--panel); border-bottom:1px solid var(--border); flex-shrink:0;
}
.tbtn {
  display:inline-flex; align-items:center; gap:5px; padding:4px 8px;
  border-radius:var(--rs); font-family:var(--ui); font-size:11.5px; font-weight:500;
  color:var(--t3); background:transparent; border:1px solid transparent; cursor:pointer;
  transition:color .1s,background .1s;
}
.tbtn:hover  { color:var(--t1); background:var(--raised); border-color:var(--border); }
.tbtn.active { color:var(--accent); background:var(--adim); border-color:rgba(79,142,247,0.25); }
.tbtn.err-on { color:var(--red); background:rgba(240,106,106,0.08); border-color:rgba(240,106,106,0.2); }
.tbtn i { font-size:10px; }
.tstats { margin-left:auto; display:flex; gap:11px; font-size:10.5px; color:var(--t3); font-family:var(--mono); }

.search-bar {
  display:none; align-items:center; gap:5px; padding:5px 9px;
  background:var(--bg); border-bottom:1px solid var(--border); flex-shrink:0;
}
.search-bar.on { display:flex; }
.si {
  flex:1; max-width:210px; padding:4px 9px; border-radius:var(--rs);
  background:var(--raised); border:1px solid var(--border);
  color:var(--t1); font-family:var(--mono); font-size:12px; outline:none;
}
.si:focus { border-color:var(--bordm); }
.si::placeholder { color:var(--t3); }
.sab {
  padding:4px 8px; border-radius:var(--rs); font-size:11px;
  background:var(--raised); border:1px solid var(--border);
  color:var(--t2); cursor:pointer; font-family:var(--ui);
}
.sab:hover { color:var(--t1); border-color:var(--bordm); }
.scnt { font-size:10px; color:var(--t3); font-family:var(--mono); white-space:nowrap; }

.split { flex:1; min-height:0; display:flex; overflow:hidden; }
.editor-box { flex:1; min-width:0; display:flex; flex-direction:column; overflow:hidden; position:relative; }
#editor { flex:1; min-height:0; }

/* ── PREVIEW ────────────────────────────── */
.prev-status {
  display:none; align-items:center; gap:7px; padding:3px 12px;
  background:var(--panel); border-top:1px solid var(--border);
  font-size:10px; color:var(--t3); font-family:var(--mono); flex-shrink:0;
}
.prev-status.on { display:flex; }
.sok { color:var(--green); } .serr { color:var(--red); }

#pvdiv { width:4px; cursor:col-resize; flex-shrink:0; background:var(--border); display:none; }
#pvdiv.on { display:block; }
#pvdiv:hover, #pvdiv.drag { background:var(--accent); }

.prev-box {
  width:0; min-width:0; flex-shrink:0; display:flex; flex-direction:column;
  border-left:1px solid transparent; overflow:hidden; opacity:0;
  transition:width .28s,opacity .22s,border-color .28s;
}
.prev-box.open { width:45%; min-width:270px; border-color:var(--border); opacity:1; }
.prev-hdr {
  display:flex; align-items:center; gap:7px; padding:0 13px; height:36px;
  background:var(--panel); border-bottom:1px solid var(--border);
  font-size:10px; font-weight:600; letter-spacing:.08em; text-transform:uppercase;
  color:var(--t3); flex-shrink:0;
}
.live-dot {
  width:6px; height:6px; border-radius:50%; background:var(--green);
  box-shadow:0 0 5px rgba(62,207,126,0.6); margin-left:auto;
  animation:blink 2s ease-in-out infinite;
}
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }
.prev-ctrl {
  display:flex; align-items:center; gap:4px; padding:5px 9px;
  border-bottom:1px solid var(--border); background:var(--panel); flex-shrink:0;
}
.ptab {
  padding:3px 8px; border-radius:var(--rs); font-size:11px; background:transparent;
  border:1px solid transparent; color:var(--t3); cursor:pointer; font-family:var(--ui);
}
.ptab:hover  { color:var(--t1); background:var(--raised); border-color:var(--border); }
.ptab.active { color:var(--accent); background:var(--adim); border-color:rgba(79,142,247,0.25); }
.pref {
  margin-left:auto; padding:3px 8px; border-radius:var(--rs); font-size:11px;
  background:transparent; border:1px solid var(--border); color:var(--t2);
  cursor:pointer; font-family:var(--ui);
}
.pref:hover { color:var(--t1); border-color:var(--bordm); }
.prev-frame { flex:1; min-height:0; position:relative; background:#fff; overflow:hidden; }
#piframe, #riframe { width:100%; height:100%; border:none; display:block; }
.prev-load {
  position:absolute; inset:0; background:rgba(15,17,23,.9);
  display:flex; align-items:center; justify-content:center;
  flex-direction:column; gap:8px; font-size:12px; color:var(--t3);
  transition:opacity .2s; pointer-events:none;
}
.prev-load.hide { opacity:0; }
.spin-ring {
  width:22px; height:22px; border:2px solid var(--border); border-top-color:var(--accent);
  border-radius:50%; animation:spin .7s linear infinite;
}
@keyframes spin { to{transform:rotate(360deg)} }

/* ── MAIN DIVIDER ───────────────────────── */
#mdiv { width:4px; cursor:col-resize; flex-shrink:0; background:var(--border); }
#mdiv:hover, #mdiv.drag { background:var(--accent); }

/* ── AI PANEL ───────────────────────────── */
.ai-panel {
  width:310px; flex-shrink:0; background:var(--panel);
  border-left:1px solid var(--border); display:flex; flex-direction:column; overflow:hidden;
}
.psec { flex:1; min-height:0; display:flex; flex-direction:column; overflow:hidden; }
.phdr {
  display:flex; align-items:center; gap:7px; padding:9px 13px; flex-shrink:0;
  font-size:10px; font-weight:600; letter-spacing:.1em; text-transform:uppercase;
  color:var(--t3); background:var(--bg); border-bottom:1px solid var(--border);
}
.pdot {
  width:6px; height:6px; border-radius:50%; background:var(--t3); margin-left:auto;
  transition:background .2s,box-shadow .2s;
}
.pdot.on { background:var(--green); box-shadow:0 0 5px rgba(62,207,126,0.6); animation:blink 2s infinite; }
.pbody {
  flex:1; overflow-y:auto; padding:12px;
  scrollbar-width:thin; scrollbar-color:var(--hover) transparent;
}
pre { font-family:var(--mono); font-size:12px; line-height:1.7; white-space:pre-wrap; word-break:break-all; color:var(--t2); }
pre.has { color:var(--t1); }
.hint { font-size:12px; color:var(--t3); font-style:italic; }
.shimmer { display:none; flex-direction:column; gap:5px; padding:3px 0; }
.shimmer.on { display:flex; }
.shl {
  height:9px; border-radius:5px;
  background:linear-gradient(90deg,var(--raised) 25%,var(--hover) 50%,var(--raised) 75%);
  background-size:200% 100%; animation:sh 1.3s infinite;
}
@keyframes sh { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* ── TERMINAL ───────────────────────────── */
.term-section {
  display:flex; flex-direction:column; overflow:hidden;
  border-top:1px solid var(--border); flex:1.8; min-height:220px;
}
.term-hdr {
  display:flex; align-items:center; gap:7px; padding:8px 13px; flex-shrink:0;
  font-size:10px; font-weight:600; letter-spacing:.1em; text-transform:uppercase;
  color:var(--t3); background:var(--bg); border-bottom:1px solid var(--border);
}
.term-actions { display:flex; align-items:center; gap:2px; margin-left:auto; }
.ta {
  background:none; border:none; color:var(--t3); cursor:pointer;
  padding:3px 6px; border-radius:4px; font-size:10px; line-height:1;
  transition:color .1s,background .1s;
}
.ta:hover        { color:var(--t1); background:var(--hover); }
.ta.del:hover    { color:var(--red); }
.ta.cp:hover     { color:var(--accent); }
.exit-badge { display:none; padding:1px 7px; border-radius:10px; font-size:9px; font-family:var(--mono); font-weight:700; flex-shrink:0; }
.exit-badge.ok  { background:rgba(62,207,126,0.12); color:var(--green); border:1px solid rgba(62,207,126,0.25); }
.exit-badge.err { background:rgba(240,106,106,0.12); color:var(--red);   border:1px solid rgba(240,106,106,0.25); }

.term-bar {
  display:flex; align-items:center; gap:4px; padding:4px 10px; flex-shrink:0;
  background:var(--panel); border-bottom:1px solid var(--border);
}
.tfbtn {
  background:none; border:none; color:var(--t3); cursor:pointer;
  padding:2px 5px; border-radius:3px; font-size:9px; font-family:var(--mono); font-weight:600;
}
.tfbtn:hover { color:var(--t1); background:var(--raised); }
.ttime { margin-left:auto; font-size:9.5px; color:var(--t3); font-family:var(--mono); }

.term-body { flex:1; min-height:0; display:flex; flex-direction:column; overflow:hidden; background:#080b10; }

.term-out-wrap {
  flex:1; min-height:0; overflow-y:auto; overflow-x:auto;
  scrollbar-width:thin; scrollbar-color:#1e2d3d transparent;
}
.term-out-wrap::-webkit-scrollbar { width:5px; height:5px; }
.term-out-wrap::-webkit-scrollbar-thumb { background:#1e2d3d; border-radius:3px; }

#termOut {
  font-family:var(--mono); font-size:12.5px; line-height:1.65;
  padding:12px 14px; color:#c9d1d9;
  white-space:pre-wrap; word-break:break-word; min-height:100%;
}
/* coloured spans */
#termOut .tp  { color:var(--t3); user-select:none; }
#termOut .ti  { color:var(--cyan); }
#termOut .tw  { color:var(--amber); }
#termOut .tok { color:var(--green); }
#termOut .ter { color:var(--red); }
#termOut .tin { color:#e2eaff; opacity:.7; }   /* echoed input */

/* inline input row */
.term-input-row {
  display:flex; align-items:center; flex-shrink:0;
  background:#0d1117; border-top:1px solid rgba(56,189,248,0.18);
  animation:irow .15s ease;
}
.term-input-row.hide { display:none; }
@keyframes irow { from{opacity:0;transform:translateY(4px)} to{opacity:1;transform:none} }
.tip-label {
  padding:0 10px; font-family:var(--mono); font-size:13px;
  color:var(--cyan); flex-shrink:0; user-select:none; line-height:36px;
}
#termIn {
  flex:1; background:transparent; border:none; outline:none;
  font-family:var(--mono); font-size:12.5px; color:#e2eaff;
  caret-color:var(--cyan); padding:6px 4px;
}
#termIn::placeholder { color:var(--t3); }
.tip-send {
  flex-shrink:0; padding:4px 10px; margin-right:8px;
  border-radius:var(--rs); font-size:10px;
  background:rgba(56,189,248,0.1); border:1px solid rgba(56,189,248,0.2);
  color:var(--cyan); cursor:pointer;
}
.tip-send:hover { background:rgba(56,189,248,0.2); }

/* ── AUTOCOMPLETE ───────────────────────── */
#ac-popup {
  position:absolute; z-index:100; display:none; flex-direction:column;
  min-width:270px; max-width:440px; background:var(--panel);
  border:1px solid var(--bordm); border-radius:var(--r);
  box-shadow:0 8px 28px rgba(0,0,0,.5),var(--aglow);
  overflow:hidden; animation:pop .12s ease;
}
@keyframes pop { from{opacity:0;transform:translateY(-4px)} to{opacity:1;transform:none} }
.ac-hdr {
  display:flex; align-items:center; gap:6px; padding:5px 10px;
  background:var(--raised); border-bottom:1px solid var(--border);
  font-size:10px; color:var(--t3); font-family:var(--ui);
}
.ac-spin {
  width:8px; height:8px; border:1.5px solid var(--border); border-top-color:var(--accent);
  border-radius:50%; animation:spin .65s linear infinite; flex-shrink:0;
}
.ac-list { max-height:170px; overflow-y:auto; }
.ac-item {
  display:flex; align-items:flex-start; gap:8px; padding:6px 10px;
  cursor:pointer; border-bottom:1px solid var(--border);
}
.ac-item:last-child { border-bottom:none; }
.ac-item:hover, .ac-item.sel { background:var(--hover); }
.ac-code { font-family:var(--mono); font-size:12px; color:var(--cyan); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:pre; }
.ac-icon { font-size:10px; color:var(--accent); margin-top:2px; flex-shrink:0; }
.ac-foot {
  padding:4px 10px; background:var(--bg); border-top:1px solid var(--border);
  font-size:9.5px; color:var(--t3); display:flex; gap:9px;
}
.ac-foot kbd {
  background:var(--raised); border:1px solid var(--bordm);
  border-radius:3px; padding:1px 4px; font-size:9px; color:var(--t2); font-family:var(--mono);
}

/* ── MODALS ─────────────────────────────── */
.modal-ov {
  position:fixed; inset:0; z-index:500; background:rgba(0,0,0,.6);
  backdrop-filter:blur(6px); display:none; align-items:center; justify-content:center;
}
.modal-ov.open { display:flex; animation:fin .15s ease; }
@keyframes fin { from{opacity:0} to{opacity:1} }
.modal {
  background:var(--panel); border:1px solid var(--bordm); border-radius:14px;
  width:580px; max-width:92vw; max-height:78vh; display:flex; flex-direction:column;
  overflow:hidden; box-shadow:0 24px 70px rgba(0,0,0,.7);
  animation:sup .2s cubic-bezier(.34,1.56,.64,1);
}
@keyframes sup { from{opacity:0;transform:translateY(16px) scale(.97)} to{opacity:1;transform:none} }
.modal-hdr { display:flex; align-items:center; gap:8px; padding:13px 17px; border-bottom:1px solid var(--border); }
.modal-hdr h2 { font-size:13.5px; font-weight:600; flex:1; }
.mcls {
  width:24px; height:24px; border-radius:var(--rs); background:var(--raised);
  border:1px solid var(--border); color:var(--t2); font-size:10px; cursor:pointer;
  display:flex; align-items:center; justify-content:center;
}
.mcls:hover { background:rgba(240,106,106,0.12); color:var(--red); border-color:rgba(240,106,106,0.2); }
.modal-body { flex:1; overflow-y:auto; padding:16px; }

.stats-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:9px; margin-bottom:18px; }
.sc { background:var(--raised); border:1px solid var(--border); border-radius:var(--r); padding:12px 14px; }
.sv { font-size:22px; font-weight:700; font-family:var(--mono); margin-bottom:2px; }
.sl { font-size:10px; color:var(--t3); text-transform:uppercase; letter-spacing:.07em; }
.sc.bl .sv{color:var(--accent)} .sc.cy .sv{color:var(--cyan)} .sc.gr .sv{color:var(--green)}
.sc.am .sv{color:var(--amber)} .sc.rd .sv{color:var(--red)} .sc.vi .sv{color:#a78bfa}
.sec-title {
  font-size:10px; font-weight:600; letter-spacing:.1em; text-transform:uppercase; color:var(--t3);
  margin-bottom:9px; display:flex; align-items:center; gap:8px;
}
.sec-title::after { content:''; flex:1; height:1px; background:var(--border); }
.lb-list { display:flex; flex-direction:column; gap:7px; }
.lb-row  { display:flex; align-items:center; gap:9px; font-size:12px; }
.lb-lbl  { width:65px; color:var(--t2); font-family:var(--mono); }
.lb-trk  { flex:1; height:5px; background:var(--raised); border-radius:3px; overflow:hidden; }
.lb-fill { height:100%; border-radius:3px; }
.lb-cnt  { width:26px; text-align:right; color:var(--t3); font-family:var(--mono); font-size:11px; }

.folder-tree { display:flex; flex-direction:column; gap:2px; }
.fi { display:flex; align-items:center; gap:8px; padding:7px 10px; border-radius:var(--rs); cursor:pointer; font-size:13px; color:var(--t2); border:1px solid transparent; }
.fi:hover { background:var(--hover); color:var(--t1); }
.fi i { color:var(--amber); }
.fc { margin-left:auto; font-size:10px; color:var(--t3); font-family:var(--mono); }
.ff { padding-left:24px; display:flex; flex-direction:column; gap:1px; margin-top:1px; }
.ffi { display:flex; align-items:center; gap:7px; padding:5px 8px; border-radius:var(--rs); cursor:pointer; font-size:12px; color:var(--t3); }
.ffi:hover { background:var(--hover); color:var(--t1); }
.fnew {
  margin-top:9px; padding:7px 13px; border-radius:var(--rs); font-size:12px;
  background:transparent; border:1px dashed var(--bordm); color:var(--t3);
  cursor:pointer; display:flex; align-items:center; gap:6px; width:100%; justify-content:center; font-family:var(--ui);
}
.fnew:hover { color:var(--amber); border-color:rgba(245,166,35,.4); background:rgba(245,166,35,.06); }

.rhl { display:flex; flex-direction:column; gap:7px; }
.rhi { background:var(--raised); border:1px solid var(--border); border-radius:var(--r); padding:10px 12px; display:flex; flex-direction:column; gap:6px; cursor:pointer; }
.rhi:hover { border-color:var(--bordm); }
.rhm { display:flex; align-items:center; gap:7px; font-size:11px; color:var(--t3); }
.rhlg { padding:1px 6px; border-radius:4px; background:var(--adim); border:1px solid rgba(79,142,247,.2); color:var(--accent); font-family:var(--mono); }
.rhok { color:var(--green); } .rher { color:var(--red); }
.rhc { font-family:var(--mono); font-size:11px; color:var(--t2); white-space:pre; overflow:hidden; text-overflow:ellipsis; max-height:36px; border-left:2px solid var(--bordm); padding-left:7px; }
.rho { font-family:var(--mono); font-size:11px; white-space:pre-wrap; word-break:break-all; max-height:46px; overflow:hidden; }
.rho.ok{color:var(--green)} .rho.er{color:var(--red)}
.rhr { align-self:flex-end; padding:3px 8px; border-radius:4px; font-size:11px; background:var(--adim); border:1px solid rgba(79,142,247,.2); color:var(--accent); cursor:pointer; font-family:var(--ui); }
.rhr:hover { background:rgba(79,142,247,.2); }
.rhe { color:var(--t3); font-size:13px; font-style:italic; text-align:center; padding:24px 0; }

.expl { font-size:13px; line-height:1.8; white-space:pre-wrap; word-break:break-word; font-family:var(--ui); }
.expl.ld { color:var(--t3); font-style:italic; }

.shr-row { display:flex; gap:8px; align-items:center; background:var(--raised); border:1px solid var(--border); border-radius:var(--r); padding:9px 12px; font-family:var(--mono); font-size:12px; color:var(--cyan); word-break:break-all; }
.shr-cp { flex-shrink:0; padding:4px 11px; border-radius:var(--rs); font-size:12px; background:var(--adim); border:1px solid rgba(79,142,247,.25); color:var(--accent); cursor:pointer; white-space:nowrap; font-family:var(--ui); }
.shr-cp:hover { background:rgba(79,142,247,.2); }
.shr-info { display:flex; align-items:center; gap:9px; padding:9px 12px; background:var(--raised); border:1px solid var(--border); border-radius:var(--r); font-size:12px; color:var(--t2); margin-top:9px; }

/* ── TOAST ──────────────────────────────── */
#toast {
  position:fixed; bottom:18px; right:18px; z-index:9999;
  padding:8px 15px; border-radius:var(--r); background:var(--panel);
  border:1px solid var(--bordm); font-size:12.5px; color:var(--t1);
  display:flex; align-items:center; gap:7px; box-shadow:0 6px 22px rgba(0,0,0,.5);
  transform:translateY(14px); opacity:0; pointer-events:none; backdrop-filter:blur(10px);
  transition:transform .22s cubic-bezier(.34,1.56,.64,1),opacity .18s;
}
#toast.show { transform:translateY(0); opacity:1; }
#toast.success i{color:var(--green)} #toast.info i{color:var(--accent)} #toast.error i{color:var(--red)}
* { scrollbar-width:thin; scrollbar-color:var(--hover) transparent; }
a { text-decoration:none; }
</style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
  <div class="logo">
    <div class="logo-icon"><i class="fa-solid fa-bolt"></i></div>
    AiEditor
  </div>
  <div class="sep"></div>
  <div class="lang-wrap">
    <i class="fa-solid fa-code"></i>
    <select id="language" onchange="changeLang()">
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
  <div class="save-pill" id="savePill"><i class="fa-solid fa-floppy-disk"></i><span id="saveText">Auto-save on</span></div>
  <div class="spacer"></div>
  <button class="btn" id="prevBtn" onclick="togglePreview()"><i class="fa-solid fa-eye" id="prevIcon"></i><span id="prevTxt">Preview</span></button>
  <button class="btn suggest" onclick="askAI()"><i class="fa-solid fa-robot"></i> Suggest</button>
  <button class="btn" onclick="applyFix()"><i class="fa-solid fa-wand-magic-sparkles"></i> Apply</button>
  <button class="btn primary" onclick="runCode()"><i class="fa-solid fa-play"></i> Run</button>
  <div class="sep"></div>
  <button class="btn ico" onclick="openShareModal()"><i class="fa-solid fa-share-nodes"></i></button>
  <button class="btn ico" onclick="toggleTheme()" id="themeBtn"><i class="fa-solid fa-moon" id="themeIcon"></i></button>
  <button class="btn ico" onclick="openRunHistoryModal()"><i class="fa-solid fa-clock-rotate-left"></i></button>
  <button class="btn ico" onclick="openFoldersModal()"><i class="fa-solid fa-folder-tree"></i></button>
  <button class="btn ico" onclick="openStatsModal()"><i class="fa-solid fa-chart-bar"></i></button>
  <a href="/history"><button class="btn ico"><i class="fa-solid fa-code-branch"></i></button></a>
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
    <div class="file-list" id="fileList"></div>
    <button class="new-file-btn" onclick="newFile()"><i class="fa-solid fa-plus"></i> New File</button>
  </div>

  <!-- EDITOR -->
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
        <div class="prev-hdr"><i class="fa-solid fa-display" style="color:var(--green);font-size:10px"></i> Live Preview<div class="live-dot"></div></div>
        <div class="prev-ctrl">
          <button class="ptab active" id="tabHtml" onclick="setPrevTab('html')"><i class="fa-brands fa-html5" style="color:#fb923c;margin-right:3px"></i> HTML/CSS/JS</button>
          <button class="ptab" id="tabReact" onclick="setPrevTab('react')"><i class="fa-brands fa-react" style="color:#38bdf8;margin-right:3px"></i> React</button>
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

  <!-- AI PANEL -->
  <div class="ai-panel">
    <div class="psec" style="flex:1">
      <div class="phdr"><i class="fa-solid fa-brain" style="color:#a78bfa"></i> AI Response<div class="pdot" id="aiDot"></div></div>
      <div class="pbody">
        <div class="shimmer" id="aiShim"><div class="shl" style="width:85%"></div><div class="shl" style="width:65%"></div><div class="shl" style="width:75%"></div><div class="shl" style="width:50%"></div></div>
        <pre id="result" class="hint">— ask AI to suggest improvements —</pre>
      </div>
    </div>

    <!-- TERMINAL -->
    <div class="term-section">
      <div class="term-hdr">
        <i class="fa-solid fa-terminal" style="color:var(--cyan)"></i> Terminal
        <div class="term-actions">
          <span class="exit-badge" id="exitBadge"></span>
          <button class="ta cp" title="Copy" onclick="termCopy(event)"><i class="fa-solid fa-copy"></i></button>
          <button class="ta del" title="Clear" onclick="termClear(event)"><i class="fa-solid fa-trash-can"></i></button>
          <div class="pdot" id="runDot" style="margin-left:4px"></div>
        </div>
      </div>
      <div class="term-bar">
        <div style="display:flex;align-items:center;gap:5px">
          <i class="fa-solid fa-circle" style="color:#f06a6a;font-size:7px"></i>
          <i class="fa-solid fa-circle" style="color:#f5a623;font-size:7px"></i>
          <i class="fa-solid fa-circle" style="color:#3ecf7e;font-size:7px"></i>
        </div>
        <div style="margin-left:auto;display:flex;align-items:center;gap:4px">
          <button class="tfbtn" onclick="termFS(-1)">A&minus;</button>
          <button class="tfbtn" onclick="termFS(1)">A+</button>
          <span id="termTime" class="ttime"></span>
        </div>
      </div>
      <div class="term-body">
        <!-- Output view -->
        <div id="termViewOutput" style="display:flex;flex-direction:column;flex:1;min-height:0;overflow:hidden">
          <div class="term-out-wrap" id="termWrap">
            <div id="termOut"><span class="tp">~/project $</span> <span class="ti">ready — press Run to execute</span></div>
          </div>
        </div>
        <!-- Stdin view: shown before running when code needs input -->
        <div id="termViewStdin" style="display:none;flex-direction:column;flex:1;min-height:0;overflow:hidden">
          <div style="padding:8px 12px;font-size:10px;color:var(--cyan);font-family:var(--mono);border-bottom:1px solid rgba(56,189,248,0.15);flex-shrink:0;display:flex;align-items:center;gap:8px">
            <i class="fa-solid fa-keyboard" style="font-size:10px"></i>
            Enter program input (one value per line), then click Run
            <button onclick="termCancelStdin()" style="margin-left:auto;background:none;border:none;color:var(--t3);cursor:pointer;font-size:10px;font-family:var(--mono)">cancel</button>
          </div>
          <textarea id="termStdin" spellcheck="false"
            placeholder="e.g.&#10;Ahmad&#10;25&#10;Amman"
            style="flex:1;min-height:0;background:#080b10;color:#e2eaff;border:none;outline:none;
                   font-family:var(--mono);font-size:12.5px;padding:12px 14px;resize:none;line-height:1.65;
                   scrollbar-width:thin;scrollbar-color:#1e2d3d transparent;"></textarea>
          <div style="padding:8px 12px;background:#0d1117;border-top:1px solid rgba(56,189,248,0.15);flex-shrink:0;display:flex;gap:8px;align-items:center">
            <span style="font-size:10px;color:var(--t3);font-family:var(--mono)">Each line = one input() call</span>
            <button onclick="termRunWithStdin()" style="margin-left:auto;padding:5px 14px;border-radius:var(--rs);font-size:11px;font-weight:600;background:var(--accent);border:none;color:#fff;cursor:pointer;font-family:var(--ui)"><i class="fa-solid fa-play" style="margin-right:5px"></i>Run</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- MODALS -->
<div class="modal-ov" id="statsModal">
  <div class="modal"><div class="modal-hdr"><i class="fa-solid fa-chart-bar" style="color:#a78bfa"></i><h2>Statistics</h2><button class="mcls" onclick="closeModal('statsModal')"><i class="fa-solid fa-xmark"></i></button></div><div class="modal-body" id="statsBody"></div></div>
</div>
<div class="modal-ov" id="foldersModal">
  <div class="modal" style="width:440px"><div class="modal-hdr"><i class="fa-solid fa-folder-tree" style="color:var(--amber)"></i><h2>Folder Manager</h2><button class="mcls" onclick="closeModal('foldersModal')"><i class="fa-solid fa-xmark"></i></button></div><div class="modal-body" id="foldersBody"></div></div>
</div>
<div class="modal-ov" id="runHistoryModal">
  <div class="modal"><div class="modal-hdr"><i class="fa-solid fa-clock-rotate-left" style="color:var(--green)"></i><h2>Run History</h2><button class="mcls" onclick="closeModal('runHistoryModal')"><i class="fa-solid fa-xmark"></i></button></div><div class="modal-body" id="rhBody"></div></div>
</div>
<div class="modal-ov" id="explainModal">
  <div class="modal" style="width:620px"><div class="modal-hdr"><i class="fa-solid fa-comment-dots" style="color:#ec4899"></i><h2>AI Code Explanation</h2><button class="mcls" onclick="closeModal('explainModal')"><i class="fa-solid fa-xmark"></i></button></div><div class="modal-body"><pre id="explainOut" class="expl ld">Asking AI to explain your code…</pre></div></div>
</div>
<div class="modal-ov" id="shareModal">
  <div class="modal" style="width:460px">
    <div class="modal-hdr"><i class="fa-solid fa-share-nodes" style="color:var(--cyan)"></i><h2>Share Code</h2><button class="mcls" onclick="closeModal('shareModal')"><i class="fa-solid fa-xmark"></i></button></div>
    <div class="modal-body">
      <div class="shr-row"><i class="fa-solid fa-link" style="color:var(--t3)"></i><span id="shrLink" style="flex:1">Generating…</span><button class="shr-cp" onclick="copyShrLink()"><i class="fa-solid fa-copy"></i> Copy</button></div>
      <div class="shr-info"><i class="fa-solid fa-lock" style="color:var(--green)"></i> Read-only — recipients can view but not edit.</div>
      <div class="shr-info"><i class="fa-solid fa-code" style="color:var(--accent)"></i> File: <strong id="shrFile" style="color:var(--cyan);margin-left:4px">—</strong> &nbsp;·&nbsp; Language: <strong id="shrLang" style="color:#a78bfa;margin-left:4px">—</strong></div>
    </div>
  </div>
</div>

<div id="toast"><i id="toastIcon" class="fa-solid fa-circle-check"></i><span id="toastMsg">Done</span></div>

<script src="https://unpkg.com/monaco-editor@latest/min/vs/loader.js"></script>
<script>
/* ── STATE ──────────────────────────────── */
let files = { 'main.py': "print('hello world')" };
let openTabs = ['main.py'], curFile = 'main.py';
let folders = { 'Project': ['main.py'] };
let runHistory = [], RH_MAX = 50;
let savedFiles = JSON.parse(JSON.stringify(files));
let saveTimer = null, SAVE_DELAY = 1500;
let prevOpen = false, prevTab = 'html', prevTimer = null, PREV_DELAY = 600;
let darkMode = true, errHL = false, errDecs = [], srchOpen = false, srchMatches = [];
let lastAI = null;

/* ── TERMINAL STATE ─────────────────────── */
let termFontSz = 12.5;
let _runCode = null, _runLang = null;  /* stored for stdin-first flow */

/* ── HELPERS ────────────────────────────── */
function H(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function csrf() { return document.querySelector('meta[name="csrf-token"]').content; }

/* ── TOAST ──────────────────────────────── */
let toastT = null;
function toast(msg, type) {
  type = type || 'success';
  var icons = { success:'fa-solid fa-circle-check', info:'fa-solid fa-circle-info', error:'fa-solid fa-circle-xmark' };
  document.getElementById('toastIcon').className = icons[type];
  document.getElementById('toastMsg').textContent = msg;
  var el = document.getElementById('toast');
  el.className = 'show ' + type;
  clearTimeout(toastT);
  toastT = setTimeout(function(){ el.className = ''; }, 3000);
}

/* ── MODALS ─────────────────────────────── */
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-ov').forEach(function(o){
  o.addEventListener('click', function(e){ if(e.target===o) o.classList.remove('open'); });
});

/* ── FILE ICONS ─────────────────────────── */
function iconCls(n) {
  var e = n.split('.').pop().toLowerCase();
  var b = {js:'fa-brands fa-js',py:'fa-brands fa-python',php:'fa-brands fa-php',html:'fa-brands fa-html5',css:'fa-brands fa-css3-alt',java:'fa-brands fa-java',jsx:'fa-brands fa-react',tsx:'fa-brands fa-react'};
  var s = {json:'fa-solid fa-code',ts:'fa-solid fa-code',rs:'fa-solid fa-code',go:'fa-solid fa-code',cpp:'fa-solid fa-file-code',cs:'fa-solid fa-file-code'};
  return b[e]||s[e]||'fa-solid fa-file-code';
}
function iconCol(n) {
  var e = n.split('.').pop().toLowerCase();
  var m = {js:'#fbbf24',py:'#60a5fa',php:'#a78bfa',html:'#fb923c',css:'#38bdf8',json:'#34d399',ts:'#38bdf8',rs:'#fb923c',go:'#22d3ee',java:'#f87171',jsx:'#38bdf8',tsx:'#38bdf8'};
  return m[e]||'#7a8ba8';
}
function prevType() {
  var l = document.getElementById('language').value;
  var e = curFile.split('.').pop().toLowerCase();
  if (l==='jsx'||e==='jsx'||e==='tsx') return 'react';
  if (['html','css','javascript','js'].includes(l)||['html','css','js','ts'].includes(e)) return 'web';
  return 'none';
}

/* ── RENDER ─────────────────────────────── */
function renderFiles() {
  var h = '';
  for (var n in files) {
    var u = savedFiles[n] !== files[n];
    h += '<div class="file-item'+(n===curFile?' active':'')+(u?' unsaved':'')+'" onclick="openFile(\''+n+'\')"><i class="'+iconCls(n)+'" style="color:'+iconCol(n)+'"></i><span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">'+H(n)+'</span><span class="dot"></span></div>';
  }
  document.getElementById('fileList').innerHTML = h;
}
function renderTabs() {
  var h = '';
  openTabs.forEach(function(f){
    var u = savedFiles[f] !== files[f];
    h += '<div class="tab'+(f===curFile?' active':'')+(u?' unsaved':'')+'" onclick="switchTab(\''+f+'\')"><i class="'+iconCls(f)+'" style="color:'+iconCol(f)+';font-size:10px"></i><span>'+H(f)+'</span><span class="tdot"></span><span class="tx" onclick="closeTab(event,\''+f+'\')"><i class="fa-solid fa-xmark"></i></span></div>';
  });
  document.getElementById('tabs').innerHTML = h;
}
function openFile(n) {
  if (!openTabs.includes(n)) openTabs.push(n);
  curFile = n;
  if (window.editor) editor.setValue(files[n]);
  renderTabs(); renderFiles(); syncPrevLang(); updateStats();
}
function switchTab(n) { curFile=n; if(window.editor) editor.setValue(files[n]); renderTabs(); renderFiles(); syncPrevLang(); updateStats(); }
function closeTab(e, n) {
  e.stopPropagation();
  openTabs = openTabs.filter(function(f){return f!==n;});
  if (!openTabs.length) openTabs = [Object.keys(files)[0]];
  if (curFile===n) { curFile=openTabs[0]; if(window.editor) editor.setValue(files[curFile]||''); }
  renderTabs(); renderFiles();
}
function newFile() {
  var n = prompt('File name (e.g. index.html):');
  if (n) { files[n]=''; savedFiles[n]=''; openTabs.push(n); curFile=n; if(window.editor) editor.setValue(''); renderFiles(); renderTabs(); toast('Created '+n,'info'); }
}
function updateStats() {
  var c = window.editor ? editor.getValue() : '';
  document.getElementById('stL').textContent = c.split('\n').length;
  document.getElementById('stC').textContent = c.length;
  document.getElementById('stW').textContent = c.trim() ? c.trim().split(/\s+/).length : 0;
}

/* ── AUTO-SAVE ──────────────────────────── */
function triggerSave() {
  var pill=document.getElementById('savePill'), txt=document.getElementById('saveText');
  pill.className='save-pill saving'; txt.textContent='Saving\u2026';
  clearTimeout(saveTimer);
  saveTimer = setTimeout(function(){
    savedFiles[curFile] = files[curFile];
    try { localStorage.setItem('aieditor_files', JSON.stringify(files)); } catch(e){}
    pill.className='save-pill saved'; txt.textContent='Saved \u2713';
    renderTabs(); renderFiles();
    setTimeout(function(){ pill.className='save-pill'; txt.textContent='Auto-save on'; }, 2200);
  }, SAVE_DELAY);
}
function loadSaved() {
  try { var s=localStorage.getItem('aieditor_files'); if(s){Object.assign(files,JSON.parse(s)); savedFiles=JSON.parse(JSON.stringify(files));} } catch(e){}
}

/* ── PREVIEW ────────────────────────────── */
function togglePreview() {
  prevOpen = !prevOpen;
  var box=document.getElementById('pvBox'), dv=document.getElementById('pvdiv');
  var btn=document.getElementById('prevBtn'), ic=document.getElementById('prevIcon'), tx=document.getElementById('prevTxt');
  var sb=document.getElementById('pvStatus');
  if (prevOpen) {
    box.classList.add('open'); dv.classList.add('on');
    btn.classList.add('prev-on'); ic.className='fa-solid fa-eye-slash'; tx.textContent='Hide';
    sb.classList.add('on'); syncPrevLang(); updatePrev();
  } else {
    box.classList.remove('open'); dv.classList.remove('on');
    btn.classList.remove('prev-on'); ic.className='fa-solid fa-eye'; tx.textContent='Preview';
    sb.classList.remove('on');
  }
}
function syncPrevLang() { setPrevTab(prevType()==='react'?'react':'html'); }
function setPrevTab(t) {
  prevTab=t;
  document.getElementById('tabHtml').classList.toggle('active',t==='html');
  document.getElementById('tabReact').classList.toggle('active',t==='react');
  document.getElementById('htmlWrap').style.display  = t==='html'  ? '' : 'none';
  document.getElementById('reactWrap').style.display = t==='react' ? '' : 'none';
  if (prevOpen) updatePrev();
}
function refreshPrev() { updatePrev(true); }
function updatePrev(force) {
  if (!prevOpen && !force) return;
  var code = window.editor ? editor.getValue() : '';
  var lang = document.getElementById('language').value;
  if (prevTab==='react') updateReactPrev(code); else updateHtmlPrev(code,lang);
  var now = new Date();
  document.getElementById('pvTime').textContent = now.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit',second:'2-digit'});
  document.getElementById('pvIcon').className = 'fa-solid fa-circle-check sok';
  document.getElementById('pvText').textContent = 'Preview synced';
}
function updateHtmlPrev(code, lang) {
  var ld = document.getElementById('pvLoad'); ld.classList.remove('hide');
  var src = '';
  if (lang==='html') { src=code; }
  else if (lang==='css') { src='<!DOCTYPE html><html><head><style>body{font-family:Arial,sans-serif;padding:30px;margin:0;}'+code+'</style></head><body><h1>CSS Preview</h1><p>Sample paragraph.</p><div style="width:120px;height:120px;background:steelblue;margin:20px 0;"></div><button>Sample Button</button></body></html>'; }
  else if (lang==='javascript') { src='<!DOCTYPE html><html><head><style>body{font-family:monospace;background:#1a1a2e;color:#e2eaff;padding:20px;font-size:13px;}</style></head><body><div id="o"></div><script>(function(){var o=document.getElementById("o");var ol=console.log;console.log=function(){var a=Array.from(arguments);ol.apply(console,a);var d=document.createElement("div");d.textContent=a.map(function(x){return typeof x==="object"?JSON.stringify(x):String(x);}).join(" ");o.appendChild(d);};try{'+code+'}catch(e){var d=document.createElement("div");d.style.color="#f06a6a";d.textContent="Error: "+e.message;o.appendChild(d);}})();<\/script></body></html>'; }
  else { src='<html><body style="font-family:monospace;padding:20px;background:#0f1117;color:#7a8ba8;font-size:13px;"><p>Preview available for HTML, CSS, JavaScript, and React.</p></body></html>'; }
  var iframe=document.getElementById('piframe');
  iframe.srcdoc=src;
  iframe.onload=function(){ ld.classList.add('hide'); };
  setTimeout(function(){ ld.classList.add('hide'); }, 1500);
}
function updateReactPrev(code) {
  document.getElementById('riframe').srcdoc='<!DOCTYPE html><html><head><meta charset="UTF-8"><style>body{margin:0;font-family:system-ui,sans-serif;}#root{min-height:100vh;}.re{background:#1a0a0a;color:#f06a6a;padding:16px;font-family:monospace;font-size:12px;white-space:pre-wrap;border-left:3px solid #f06a6a;margin:12px;}</style></head><body><div id="root"></div><script src="https://unpkg.com/react@18/umd/react.development.js"><\/script><script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"><\/script><script src="https://unpkg.com/@babel/standalone/babel.min.js"><\/script><script type="text/babel">try{'+code+'var r=ReactDOM.createRoot(document.getElementById("root"));if(typeof App!=="undefined")r.render(React.createElement(App));else document.getElementById("root").innerHTML="<div class=\'re\'>No App component found.</div>";}catch(e){document.getElementById("root").innerHTML="<div class=\'re\'>"+e.message+"<\/div>";}<\/script></body></html>';
}

/* Preview drag resize */
var pvDiv=document.getElementById('pvdiv'), pvDrag=false;
pvDiv.addEventListener('mousedown',function(){ pvDrag=true; pvDiv.classList.add('drag'); });
document.addEventListener('mousemove',function(e){
  if (!pvDrag) return;
  var sp=document.getElementById('split'), r=sp.getBoundingClientRect();
  var lw=e.clientX-r.left-2, rw=r.width-lw-4;
  if (lw>240&&rw>190) {
    var eb=document.getElementById('edBox'), pb=document.getElementById('pvBox');
    eb.style.flex='none'; eb.style.width=lw+'px'; pb.style.width=rw+'px'; pb.style.minWidth='0';
  }
});
document.addEventListener('mouseup',function(){ pvDrag=false; pvDiv.classList.remove('drag'); });

/* ── MONACO ─────────────────────────────── */
require.config({ paths: { vs: 'https://unpkg.com/monaco-editor@latest/min/vs' } });
require(['vs/editor/editor.main'], function(){
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

  var popup=document.getElementById('ac-popup'), acList=document.getElementById('acList');
  var acLabel=document.getElementById('acLabel'), acSpin=document.getElementById('acSpin');
  var acT=null, acCtrl=null, acItems=[], acIdx=-1, acPos=null;

  function acPos2() {
    var p=editor.getPosition(); if(!p) return;
    var c=editor.getScrolledVisiblePosition(p); if(!c) return;
    var li=editor.getLayoutInfo(), tw=document.getElementById('tabs').offsetHeight, ew=document.getElementById('edBox').offsetWidth, PW=270;
    popup.style.left=Math.min(Math.max(6,li.contentLeft+c.left),ew-PW-8)+'px';
    popup.style.top=(tw+c.top+24)+'px';
  }
  function acRender() {
    acList.innerHTML='';
    acItems.forEach(function(t,i){
      var row=document.createElement('div'); row.className='ac-item'+(i===acIdx?' sel':'');
      row.innerHTML='<span class="ac-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></span><span class="ac-code">'+H(t.split('\n')[0])+'</span>';
      row.addEventListener('mousedown',function(e){e.preventDefault();acAccept(i);});
      acList.appendChild(row);
    });
  }
  function acShow(sug, pos) { acItems=sug; acIdx=0; acPos=pos; acSpin.style.display='none'; acLabel.textContent=sug.length===1?'1 suggestion':sug.length+' suggestions'; acRender(); acPos2(); popup.style.display='flex'; }
  function acHide() { popup.style.display='none'; acItems=[]; acIdx=-1; }
  function acAccept(i) {
    var t=acItems[i]; if(!t||!acPos) return;
    editor.executeEdits('ai-ac',[{range:new monaco.Range(acPos.lineNumber,acPos.column,acPos.lineNumber,acPos.column),text:t}]);
    var ls=t.split('\n');
    editor.setPosition({lineNumber:acPos.lineNumber+ls.length-1,column:ls.length>1?ls[ls.length-1].length+1:acPos.column+t.length});
    editor.focus(); acHide();
  }
  async function acFetch(pos) {
    if (acCtrl) acCtrl.abort(); acCtrl=new AbortController();
    var m=editor.getModel(), lang=document.getElementById('language').value;
    var ctx=m.getValueInRange({startLineNumber:1,startColumn:1,endLineNumber:pos.lineNumber,endColumn:pos.column});
    var full=m.getValue(); if(full.trim().length<3){acHide();return;}
    acSpin.style.display='block'; acLabel.textContent='thinking\u2026'; acList.innerHTML=''; acPos2(); popup.style.display='flex';
    try {
      var r=await fetch('/autocomplete',{method:'POST',signal:acCtrl.signal,headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify({code:ctx,full_code:full,language:lang})});
      if (!r.ok){acHide();return;}
      var d=await r.json(); var s=[];
      if (Array.isArray(d.suggestions)&&d.suggestions.length) s=d.suggestions.map(function(x){return x.trim();}).filter(Boolean);
      else if (typeof d.suggestion==='string'&&d.suggestion.trim()) s=[d.suggestion.trim()];
      if (!s.length){acHide();return;}
      acShow(s,pos);
    } catch(e){if(e.name!=='AbortError')acHide();}
  }

  editor.onDidChangeModelContent(function(){
    files[curFile]=editor.getValue(); updateStats(); renderTabs(); triggerSave();
    if (errHL) runErrHL(); acHide(); clearTimeout(acT);
    var p=editor.getPosition(); acT=setTimeout(function(){acFetch(p);},900);
    if (prevOpen){clearTimeout(prevTimer);prevTimer=setTimeout(function(){updatePrev();},PREV_DELAY);}
  });
  editor.onKeyDown(function(e){
    if (popup.style.display!=='flex') return;
    if (e.keyCode===monaco.KeyCode.UpArrow){e.preventDefault();e.stopPropagation();acIdx=Math.max(0,acIdx-1);acRender();}
    else if (e.keyCode===monaco.KeyCode.DownArrow){e.preventDefault();e.stopPropagation();acIdx=Math.min(acItems.length-1,acIdx+1);acRender();}
    else if (e.keyCode===monaco.KeyCode.Enter||e.keyCode===monaco.KeyCode.Tab){e.preventDefault();e.stopPropagation();if(acIdx>=0)acAccept(acIdx);}
    else if (e.keyCode===monaco.KeyCode.Escape){e.preventDefault();e.stopPropagation();acHide();}
  });
  editor.onDidBlurEditorWidget(acHide);
  renderFiles(); renderTabs(); updateStats();
});

/* ── LANG / THEME / SEARCH ──────────────── */
function changeLang() {
  var l=document.getElementById('language').value;
  if (window.editor) monaco.editor.setModelLanguage(editor.getModel(),l==='jsx'?'javascript':l);
  if (prevOpen){syncPrevLang();updatePrev();}
}
function toggleTheme() {
  darkMode=!darkMode;
  document.getElementById('themeIcon').className=darkMode?'fa-solid fa-moon':'fa-solid fa-sun';
  if (window.editor) monaco.editor.setTheme(darkMode?'vs-dark':'vs');
  toast(darkMode?'Dark mode':'Light mode','info');
}
function toggleSearch() {
  srchOpen=!srchOpen;
  document.getElementById('searchBar').classList.toggle('on',srchOpen);
  document.getElementById('srchBtn').classList.toggle('active',srchOpen);
  if (srchOpen) document.getElementById('srchIn').focus();
  else { if(window.editor) editor.deltaDecorations([],[]); document.getElementById('srchCnt').textContent=''; srchMatches=[]; }
}
function doSearch() {
  if (!window.editor) return;
  var q=document.getElementById('srchIn').value, m=editor.getModel();
  if (!q){editor.deltaDecorations(srchMatches,[]);srchMatches=[];document.getElementById('srchCnt').textContent='';return;}
  var ms=m.findMatches(q,false,false,false,null,true);
  document.getElementById('srchCnt').textContent=ms.length?ms.length+' match'+(ms.length>1?'es':''):'No matches';
  srchMatches=editor.deltaDecorations(srchMatches,ms.map(function(m){return{range:m.range,options:{inlineClassName:'search-highlight'}};}));
}
function doReplace() {
  if (!window.editor) return;
  var q=document.getElementById('srchIn').value, r=document.getElementById('replIn').value; if(!q) return;
  var m=editor.getModel().findNextMatch(q,editor.getPosition(),false,false,null,true);
  if (m){editor.executeEdits('rep',[{range:m.range,text:r}]);doSearch();toast('Replaced 1','info');}
  else toast('No match','error');
}
function doReplaceAll() {
  if (!window.editor) return;
  var q=document.getElementById('srchIn').value, r=document.getElementById('replIn').value; if(!q) return;
  var ms=editor.getModel().findMatches(q,false,false,false,null,true);
  if (!ms.length){toast('No matches','error');return;}
  editor.executeEdits('rep-all',ms.slice().reverse().map(function(m){return{range:m.range,text:r};}));
  doSearch(); toast('Replaced '+ms.length,'success');
}

/* ── FORMAT / EXPLAIN / DOWNLOAD / ERRORS ─ */
async function fmtCode() {
  if (!window.editor) return; toast('Formatting\u2026','info');
  try {
    var r=await fetch('/format',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify({code:editor.getValue(),language:document.getElementById('language').value})});
    if (r.ok){var d=await r.json();if(d.formatted){editor.setValue(d.formatted);toast('Formatted','success');return;}}
  } catch(e){}
  editor.getAction('editor.action.formatDocument').run(); toast('Formatted','success');
}
async function explainCode() {
  var el=document.getElementById('explainOut'); el.className='expl ld'; el.textContent='Asking AI\u2026'; openModal('explainModal');
  var sel=window.editor?editor.getModel().getValueInRange(editor.getSelection()):'';
  var code=sel.trim().length>0?sel:(window.editor?editor.getValue():'');
  try {
    var r=await fetch('/explain',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify({code:code,language:document.getElementById('language').value})});
    var d=await r.json(); el.className='expl'; el.textContent=d.result||d.explanation||'\u2014 No explanation returned \u2014';
  } catch(e){el.className='expl';el.textContent='\u2014 Error \u2014';}
}
function dlFile() {
  var b=new Blob([window.editor?editor.getValue():'']),u=URL.createObjectURL(b),a=document.createElement('a');
  a.href=u;a.download=curFile;document.body.appendChild(a);a.click();document.body.removeChild(a);URL.revokeObjectURL(u);
  toast('Downloaded '+curFile,'success');
}
function toggleErrHL() {
  errHL=!errHL;
  document.getElementById('errBtn').classList.toggle('err-on',errHL);
  if (errHL){runErrHL();toast('Error highlighting on','info');}
  else{if(window.editor)errDecs=editor.deltaDecorations(errDecs,[]);toast('Error highlighting off','info');}
}
function runErrHL() {
  if (!window.editor||!errHL) return;
  var m=editor.getModel(), mk=monaco.editor.getModelMarkers({resource:m.uri});
  errDecs=editor.deltaDecorations(errDecs,mk.filter(function(x){return x.severity>=monaco.MarkerSeverity.Warning;}).map(function(x){return{
    range:new monaco.Range(x.startLineNumber,1,x.endLineNumber,1),
    options:{isWholeLine:true,glyphMarginClassName:'error-highlight-gutter',
    glyphMarginHoverMessage:{value:'**'+(x.severity===monaco.MarkerSeverity.Error?'Error':'Warning')+'**: '+x.message},
    overviewRuler:{color:'#f06a6a',position:monaco.editor.OverviewRulerLane.Right}}
  };}));
}

/* ── TERMINAL ───────────────────────────── */
/* ── TERMINAL FUNCTIONS ─────────────────── */
function termSet(html) {
  document.getElementById('termOut').innerHTML = html;
  var w = document.getElementById('termWrap');
  setTimeout(function(){ w.scrollTop = w.scrollHeight; }, 0);
}
function termClear(e) {
  if (e) { e.preventDefault(); e.stopPropagation(); }
  showView('output');
  termSet('<span class="tp">~/project $</span> <span class="ti">terminal cleared</span>');
  document.getElementById('exitBadge').style.display = 'none';
  document.getElementById('termTime').textContent = '';
  _runCode = null; _runLang = null;
}
function termCopy(e) {
  if (e) { e.preventDefault(); e.stopPropagation(); }
  navigator.clipboard.writeText(document.getElementById('termOut').innerText)
    .then(function(){ toast('Copied!','success'); })
    .catch(function(){ toast('Copy failed','error'); });
}
function termFS(d) {
  termFontSz = Math.min(20, Math.max(9, termFontSz + d));
  document.getElementById('termOut').style.fontSize = termFontSz + 'px';
}
function showView(v) {
  document.getElementById('termViewOutput').style.display = v==='output' ? 'flex' : 'none';
  document.getElementById('termViewStdin').style.display  = v==='stdin'  ? 'flex' : 'none';
}
function termCancelStdin() {
  showView('output');
  _runCode = null; _runLang = null;
}
/* Called when user clicks Run inside the stdin view */
async function termRunWithStdin() {
  var stdin = document.getElementById('termStdin').value;
  showView('output');
  await execCode(_runCode, _runLang, stdin);
}
function setBadge(isError) {
  var b = document.getElementById('exitBadge');
  b.style.display = 'inline-block';
  b.textContent   = isError ? 'EXIT 1' : 'EXIT 0';
  b.className     = 'exit-badge ' + (isError ? 'err' : 'ok');
  document.getElementById('termTime').textContent = new Date().toLocaleTimeString([],{hour:'2-digit',minute:'2-digit',second:'2-digit'});
}
function setRunDot(on) { document.getElementById('runDot').classList.toggle('on', on); }

/* Single execution — runs code with given stdin string, shows result */
async function execCode(code, lang, stdin) {
  var time = new Date().toLocaleTimeString([],{hour:'2-digit',minute:'2-digit',second:'2-digit'});
  termSet('<span class="tp">~/project $</span> <span class="ti">run ' + H(lang) + ' \u00b7 ' + time + '</span>\n<span class="tw">\u23f3 executing\u2026</span>');
  document.getElementById('exitBadge').style.display = 'none';
  setRunDot(true);
  try {
    var r = await fetch('/run', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
      body: JSON.stringify({ code: code, language: lang, input: stdin || '' })
    });
    var d = await r.json();
    var out = d.output || d.error || 'No output';
    var isErr = !!d.error || r.status >= 400;
    setBadge(isErr);

    /* Render output — color error lines red, normal lines green */
    var lines = out.split('\n');
    var html = '<span class="tp">~/project $</span> <span class="ti">run ' + H(lang) + ' \u00b7 ' + time + '</span>\n';
    html += lines.map(function(line) {
      return isErr
        ? '<span class="ter">' + H(line) + '</span>'
        : '<span class="tok">' + H(line) + '</span>';
    }).join('\n');
    termSet(html);

    runHistory.push({ lang:lang, code:code, output:out, isError:isErr, timestamp:new Date().toLocaleTimeString() });
    if (runHistory.length > RH_MAX) runHistory.shift();
  } catch(e) {
    termSet('<span class="tp">~/project $</span> <span class="ter">ERROR: ' + H(e.message) + '</span>');
    setBadge(true);
  } finally {
    setRunDot(false);
  }
}

/* ── RUN CODE ───────────────────────────── */
async function runCode() {
  var code = window.editor ? editor.getValue() : '';
  var lang = document.getElementById('language').value;

  /* Web languages — use preview panel */
  if (['html','css','javascript','jsx'].includes(lang)) {
    if (!prevOpen) togglePreview(); else updatePrev(true);
    termSet('<span class="tp">~/project $</span> <span class="ti">rendered in preview panel</span>');
    return;
  }

  /* Check if code has any input() / cin / scanf / readline calls */
  var needsStdin = /\binput\s*\(|std::cin|scanf\s*\(|readline\s*\(|gets\s*\(|Scanner/.test(code);

  if (needsStdin) {
    /* Show stdin view so user can type inputs before running */
    _runCode = code;
    _runLang = lang;
    document.getElementById('termStdin').value = '';
    showView('stdin');
    document.getElementById('termStdin').focus();
  } else {
    /* No input needed — run directly */
    showView('output');
    await execCode(code, lang, '');
  }
}

/* ── AI ─────────────────────────────────── */
async function askAI() {
  var el=document.getElementById('result'); el.textContent='Thinking\u2026'; lastAI=null;
  document.getElementById('aiShim').classList.add('on'); document.getElementById('aiDot').classList.add('on');
  try {
    var r=await fetch('/suggest',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify({code:window.editor?editor.getValue():'',language:document.getElementById('language').value})});
    var d=await r.json(); lastAI=d.result||null; el.textContent=d.result||d.error||JSON.stringify(d); el.className='has';
  } catch(e){lastAI=null;el.textContent='AI ERROR: '+e.message;el.className='';}
  finally{document.getElementById('aiShim').classList.remove('on');document.getElementById('aiDot').classList.remove('on');}
}
function applyFix() {
  if (lastAI&&lastAI.trim()){
    if(window.editor)editor.setValue(lastAI); files[curFile]=lastAI;
    if(prevOpen)updatePrev(true); toast('AI fix applied','success');
  } else toast('No AI suggestion to apply','error');
}

/* ── STATS MODAL ────────────────────────── */
function openStatsModal() {
  var tL=0,tC=0,tW=0,lc={};
  for(var n in files){var c=files[n],e=n.split('.').pop().toLowerCase();tL+=c.split('\n').length;tC+=c.length;tW+=c.trim()?c.trim().split(/\s+/).length:0;lc[e]=(lc[e]||0)+1;}
  var fc=Object.keys(files).length, sc=runHistory.filter(function(r){return !r.isError;}).length;
  var mx=Math.max.apply(null,Object.values(lc).concat([1]));
  var bars=Object.entries(lc).map(function(p){var e=p[0],c=p[1];var cols={py:'#60a5fa',js:'#fbbf24',html:'#fb923c',css:'#38bdf8',jsx:'#22d3ee',php:'#a78bfa',java:'#f87171'};return '<div class="lb-row"><span class="lb-lbl">.'+H(e)+'</span><div class="lb-trk"><div class="lb-fill" style="width:'+Math.round(c/mx*100)+'%;background:'+(cols[e]||'#4f8ef7')+'"></div></div><span class="lb-cnt">'+c+'</span></div>';}).join('');
  document.getElementById('statsBody').innerHTML='<div class="stats-grid"><div class="sc bl"><div class="sv">'+fc+'</div><div class="sl">Files</div></div><div class="sc cy"><div class="sv">'+tL.toLocaleString()+'</div><div class="sl">Lines</div></div><div class="sc vi"><div class="sv">'+tC.toLocaleString()+'</div><div class="sl">Chars</div></div><div class="sc am"><div class="sv">'+tW.toLocaleString()+'</div><div class="sl">Words</div></div><div class="sc gr"><div class="sv">'+sc+'</div><div class="sl">Successful Runs</div></div><div class="sc rd"><div class="sv">'+(runHistory.length-sc)+'</div><div class="sl">Failed Runs</div></div></div><div class="sec-title">File types</div><div class="lb-list">'+(bars||'<div style="color:var(--t3);font-size:12px">No files</div>')+'</div>';
  openModal('statsModal');
}

/* ── FOLDERS MODAL ──────────────────────── */
function openFoldersModal() {
  var h='<div class="folder-tree">';
  for(var fn in folders){var ff=folders[fn];h+='<div class="fi"><i class="fa-solid fa-folder-open"></i><span>'+H(fn)+'</span><span class="fc">'+ff.length+' file'+(ff.length!==1?'s':'')+'</span></div><div class="ff">'+ff.map(function(f){return'<div class="ffi" onclick="closeModal(\'foldersModal\');openFile(\''+f+'\')"><i class="'+iconCls(f)+'" style="color:'+iconCol(f)+'"></i>'+H(f)+'</div>';}).join('')+'</div>';}
  var all=Object.values(folders).reduce(function(a,b){return a.concat(b);},[]);
  var un=Object.keys(files).filter(function(f){return !all.includes(f);});
  if(un.length)h+='<div class="fi"><i class="fa-solid fa-folder" style="color:var(--t3)"></i><span>Unfiled</span><span class="fc">'+un.length+'</span></div><div class="ff">'+un.map(function(f){return'<div class="ffi" onclick="closeModal(\'foldersModal\');openFile(\''+f+'\')"><i class="'+iconCls(f)+'" style="color:'+iconCol(f)+'"></i>'+H(f)+'</div>';}).join('')+'</div>';
  h+='</div><button class="fnew" onclick="mkFolder()"><i class="fa-solid fa-folder-plus"></i> New Folder</button>';
  document.getElementById('foldersBody').innerHTML=h; openModal('foldersModal');
}
function mkFolder(){var n=prompt('Folder name:');if(!n)return;folders[n]=[];if(confirm('Add "'+curFile+'" to "'+n+'"?')){for(var k in folders)folders[k]=folders[k].filter(function(f){return f!==curFile;});folders[n].push(curFile);}toast('Folder "'+n+'" created','success');openFoldersModal();}

/* ── RUN HISTORY ────────────────────────── */
function openRunHistoryModal(){
  var h='';
  if(!runHistory.length){h='<div class="rhe"><i class="fa-solid fa-clock-rotate-left" style="font-size:22px;opacity:.2;display:block;margin-bottom:7px"></i>No runs yet</div>';}
  else{h='<div class="rhl">';[...runHistory].reverse().forEach(function(e,i){var ri=runHistory.length-1-i;h+='<div class="rhi"><div class="rhm"><span class="rhlg">'+H(e.lang)+'</span><i class="fa-solid fa-circle'+(e.isError?'-xmark rher':'-check rhok')+'"></i><span>'+(e.isError?'Error':'Success')+'</span><span style="margin-left:auto">'+H(e.timestamp)+'</span></div><div class="rhc">'+H(e.code.substring(0,120))+(e.code.length>120?'\u2026':'')+'</div><div class="rho '+(e.isError?'er':'ok')+'">'+H(String(e.output||'').substring(0,150))+'</div><button class="rhr" onclick="rhRestore('+ri+')"><i class="fa-solid fa-arrow-rotate-left"></i> Restore</button></div>';});h+='</div>';}
  document.getElementById('rhBody').innerHTML=h; openModal('runHistoryModal');
}
function rhRestore(i){var e=runHistory[i];if(!e)return;if(window.editor)editor.setValue(e.code);files[curFile]=e.code;closeModal('runHistoryModal');toast('Code restored','info');}

/* ── SHARE MODAL ────────────────────────── */
function openShareModal(){
  var code=window.editor?editor.getValue():'',lang=document.getElementById('language').value;
  var link=location.origin+'/share?lang='+lang+'&code='+btoa(unescape(encodeURIComponent(code)));
  document.getElementById('shrLink').textContent=link;
  document.getElementById('shrFile').textContent=curFile;
  document.getElementById('shrLang').textContent=lang;
  openModal('shareModal');
}
function copyShrLink(){var t=document.getElementById('shrLink').textContent;navigator.clipboard.writeText(t).then(function(){toast('Link copied!','success');}).catch(function(){toast('Copy failed','error');});}
</script>
</body>
</html>
