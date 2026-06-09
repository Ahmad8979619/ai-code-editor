@extends('layouts.app')

@section('title', 'History — Stackly')

@section('content')

{{-- ══════════════════════════════════════════════════════
     STACKLY — CODE HISTORY PAGE
     Improvements added:
     - Stackly logo in the page header
     - Search / filter bar by language
     - Stats bar (total saved, most used language)
     - Expand/collapse code preview per card
     - Better empty state with a call to action
     - Improved card layout with file name display
     ══════════════════════════════════════════════════════ --}}

<style>
/* ── DESIGN TOKENS (match dashboard) ── */
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

/* ── PAGE HEADER ── */
/* top row: Stackly logo mark + page title + action button */
.page-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 28px;
  flex-wrap: wrap;
  gap: 14px;
}

.page-brand {
  display: flex;
  align-items: center;
  gap: 14px;
}

/* inline SVG logo — same mark used across all pages */
.page-brand svg { height: 28px; width: auto; }

.page-title-block { display: flex; flex-direction: column; gap: 2px; }
.page-title {
  font-size: 20px;
  font-weight: 700;
  color: var(--t1);
  letter-spacing: -0.3px;
  display: flex;
  align-items: center;
  gap: 9px;
}
.page-title i { font-size: 16px; color: var(--cyan); }
.page-sub { font-size: 12.5px; color: var(--t3); }

/* ── STATS BAR ── */
/* summary pills shown under the header */
.stats-bar {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  margin-bottom: 22px;
}

.stat-pill {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border-radius: 10px;
  background: rgba(7,13,31,0.75);
  border: 1px solid var(--border);
  font-size: 12.5px;
  color: var(--t2);
  backdrop-filter: blur(10px);
}
.stat-pill i { font-size: 12px; }
.stat-pill strong { color: var(--t1); font-weight: 600; }
.sp-blue   i { color: var(--blue); }
.sp-purple i { color: var(--purple); }
.sp-green  i { color: var(--green); }

/* ── SEARCH + FILTER BAR ── */
.filter-bar {
  display: flex;
  gap: 10px;
  margin-bottom: 22px;
  flex-wrap: wrap;
  align-items: center;
}

/* search input */
.search-input {
  flex: 1;
  min-width: 200px;
  padding: 9px 14px 9px 38px;
  background: rgba(7,13,31,0.8);
  border: 1px solid var(--border);
  border-radius: 10px;
  color: var(--t1);
  font-family: var(--ui);
  font-size: 13.5px;
  outline: none;
  transition: border-color .15s;
  position: relative;
}
.search-input:focus { border-color: var(--bordm); }
.search-input::placeholder { color: var(--t3); }

/* wrapper adds the search icon inside the input */
.search-wrap {
  position: relative;
  flex: 1;
  min-width: 200px;
}
.search-wrap i {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 13px;
  color: var(--t3);
  pointer-events: none;
}

/* language filter pills */
.filter-pill {
  padding: 8px 14px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  border: 1px solid var(--border);
  background: rgba(7,13,31,0.75);
  color: var(--t3);
  transition: background .15s, border-color .15s, color .15s;
  user-select: none;
}
.filter-pill:hover           { color: var(--t2); border-color: var(--bordm); }
.filter-pill.active          { background: rgba(59,130,246,0.12); color: #60a5fa; border-color: rgba(59,130,246,0.3); }
.filter-pill.active.fp-py    { background: rgba(96,165,250,0.1);  color: #60a5fa; border-color: rgba(96,165,250,0.3); }
.filter-pill.active.fp-js    { background: rgba(251,191,36,0.1);  color: #fbbf24; border-color: rgba(251,191,36,0.3); }
.filter-pill.active.fp-php   { background: rgba(167,139,250,0.1); color: #a78bfa; border-color: rgba(167,139,250,0.3); }
.filter-pill.active.fp-html  { background: rgba(251,146,60,0.1);  color: #fb923c; border-color: rgba(251,146,60,0.3); }
.filter-pill.active.fp-css   { background: rgba(56,189,248,0.1);  color: #38bdf8; border-color: rgba(56,189,248,0.3); }
.filter-pill.active.fp-java  { background: rgba(248,113,113,0.1); color: #f87171; border-color: rgba(248,113,113,0.3); }

/* ── FLASH MESSAGE ── */
.alert-success {
  display: flex;
  align-items: center;
  gap: 10px;
  background: rgba(34,197,94,0.08);
  border: 1px solid rgba(34,197,94,0.2);
  color: var(--green);
  padding: 12px 16px;
  border-radius: 10px;
  font-size: 13.5px;
  margin-bottom: 20px;
}

/* ── EMPTY STATE ── */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 14px;
  text-align: center;
  padding: 60px 24px;
  background: rgba(7,13,31,0.6);
  border: 1px solid var(--border);
  border-radius: 16px;
  backdrop-filter: blur(12px);
}
.empty-icon {
  width: 60px;
  height: 60px;
  border-radius: 16px;
  background: rgba(59,130,246,0.1);
  border: 1px solid rgba(59,130,246,0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  color: var(--blue);
}
.empty-state h3 { font-size: 17px; font-weight: 600; color: var(--t1); }
.empty-state p  { font-size: 13.5px; color: var(--t3); max-width: 320px; line-height: 1.6; }

/* ── CODE CARDS ── */
/* grid: 2 columns on wide screens, 1 on small */
.cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(480px, 1fr));
  gap: 16px;
  margin-bottom: 28px;
}

.code-card {
  background: rgba(7,13,31,0.75);
  border: 1px solid var(--border);
  border-radius: 14px;
  overflow: hidden;
  backdrop-filter: blur(14px);
  transition: border-color .2s, box-shadow .2s;
  position: relative;
}

/* top accent line colour per language */
.code-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 2px;
}
.code-card.lc-python     ::before, .code-card.lc-python::before     { background: linear-gradient(90deg, #60a5fa, #3b82f6); }
.code-card.lc-javascript ::before, .code-card.lc-javascript::before { background: linear-gradient(90deg, #fbbf24, #f59e0b); }
.code-card.lc-php        ::before, .code-card.lc-php::before        { background: linear-gradient(90deg, #a78bfa, #7c3aed); }
.code-card.lc-java       ::before, .code-card.lc-java::before       { background: linear-gradient(90deg, #f87171, #ef4444); }
.code-card.lc-html       ::before, .code-card.lc-html::before       { background: linear-gradient(90deg, #fb923c, #ea580c); }
.code-card.lc-css        ::before, .code-card.lc-css::before        { background: linear-gradient(90deg, #38bdf8, #0284c7); }
.code-card.lc-cpp        ::before, .code-card.lc-cpp::before        { background: linear-gradient(90deg, #fb923c, #ea580c); }

.code-card:hover { border-color: var(--bordm); box-shadow: 0 8px 28px rgba(0,0,0,0.3); }

/* card header row */
.card-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 18px 12px;
  gap: 12px;
}

.card-left { display: flex; align-items: center; gap: 10px; }

/* language badge */
.lang-badge {
  padding: 4px 11px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: .04em;
  text-transform: uppercase;
}
.lb-python     { background: rgba(96,165,250,0.12);  color: #60a5fa; border: 1px solid rgba(96,165,250,0.25); }
.lb-javascript { background: rgba(251,191,36,0.12);  color: #fbbf24; border: 1px solid rgba(251,191,36,0.25); }
.lb-php        { background: rgba(167,139,250,0.12); color: #a78bfa; border: 1px solid rgba(167,139,250,0.25); }
.lb-java       { background: rgba(248,113,113,0.12); color: #f87171; border: 1px solid rgba(248,113,113,0.25); }
.lb-cpp        { background: rgba(251,146,60,0.12);  color: #fb923c; border: 1px solid rgba(251,146,60,0.25); }
.lb-html       { background: rgba(251,146,60,0.12);  color: #fb923c; border: 1px solid rgba(251,146,60,0.25); }
.lb-css        { background: rgba(56,189,248,0.12);  color: #38bdf8; border: 1px solid rgba(56,189,248,0.25); }

/* card timestamp + line count */
.card-meta { display: flex; align-items: center; gap: 12px; font-size: 11.5px; color: var(--t3); }
.card-meta i { font-size: 10px; }

/* collapsible code block */
.code-block {
  padding: 0 18px;
  overflow: hidden;
  max-height: 160px; /* collapsed */
  transition: max-height .3s ease;
  position: relative;
}
.code-block.expanded { max-height: 600px; }

/* fade-out gradient at the bottom when collapsed */
.code-fade {
  position: absolute;
  bottom: 0; left: 0; right: 0;
  height: 48px;
  background: linear-gradient(transparent, rgba(7,13,31,0.95));
  pointer-events: none;
  transition: opacity .3s;
}
.code-block.expanded .code-fade { opacity: 0; }

pre {
  background: rgba(2,6,23,0.9);
  padding: 14px 16px;
  border-radius: 10px;
  overflow-x: auto;
  font-family: var(--mono);
  font-size: 12.5px;
  border: 1px solid rgba(255,255,255,0.05);
  color: #94a3b8;
  line-height: 1.75;
  margin-bottom: 0;
}

/* expand / collapse toggle button */
.expand-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  width: 100%;
  padding: 7px;
  background: none;
  border: none;
  border-top: 1px solid var(--border);
  color: var(--t3);
  font-family: var(--ui);
  font-size: 11.5px;
  cursor: pointer;
  transition: color .15s, background .15s;
}
.expand-btn:hover { color: var(--t2); background: rgba(255,255,255,0.03); }
.expand-btn i { font-size: 10px; transition: transform .3s; }
.expand-btn.open i { transform: rotate(180deg); }

/* card action buttons row */
.card-actions {
  display: flex;
  gap: 8px;
  padding: 12px 18px;
  border-top: 1px solid var(--border);
}

.action-btn {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 8px 16px;
  border-radius: 8px;
  font-family: var(--ui);
  font-size: 12.5px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  color: #fff;
  transition: transform .15s, box-shadow .15s;
}
.action-btn:hover { transform: translateY(-1px); }

/* copy button */
.btn-copy {
  background: linear-gradient(135deg, var(--blue), #2563eb);
  box-shadow: 0 0 14px rgba(59,130,246,0.25);
}
.btn-copy:hover { box-shadow: 0 0 24px rgba(59,130,246,0.5); }
.btn-copy.copied {
  background: linear-gradient(135deg, var(--green), #16a34a);
  box-shadow: 0 0 24px rgba(34,197,94,0.5);
}

/* open in editor button */
.btn-edit {
  background: rgba(255,255,255,0.06);
  border: 1px solid var(--border) !important;
  color: var(--t2);
  border: none;
}
.btn-edit:hover { background: rgba(255,255,255,0.1); color: var(--t1); }

/* delete button */
.btn-delete {
  background: linear-gradient(135deg, var(--red), #dc2626);
  box-shadow: 0 0 14px rgba(239,68,68,0.2);
  margin-left: auto; /* push to the right */
}
.btn-delete:hover { box-shadow: 0 0 24px rgba(239,68,68,0.5); }

/* ── PAGINATION ── */
.pager {
  display: flex;
  gap: 6px;
  justify-content: center;
  list-style: none;
  padding: 0;
  margin-top: 8px;
}
.pager li a, .pager li span {
  display: block;
  padding: 7px 13px;
  border-radius: 8px;
  background: rgba(15,23,42,0.7);
  border: 1px solid rgba(255,255,255,0.06);
  color: var(--t3);
  font-size: 13px;
  text-decoration: none;
  transition: .15s;
}
.pager li a:hover { border-color: rgba(59,130,246,0.3); color: var(--t1); }
.pager li.active span { background: rgba(59,130,246,0.15); border-color: rgba(59,130,246,0.35); color: #60a5fa; }

/* override Laravel's default pagination classes */
nav[role="navigation"] ul { display: flex; gap: 6px; justify-content: center; list-style: none; padding: 0; }
nav[role="navigation"] ul li a,
nav[role="navigation"] ul li span {
  display: block; padding: 7px 13px; border-radius: 8px;
  background: rgba(15,23,42,0.7); border: 1px solid rgba(255,255,255,0.06);
  color: var(--t3); font-size: 13px; text-decoration: none; transition: .15s;
}
nav[role="navigation"] ul li a:hover { border-color: rgba(59,130,246,0.3); color: var(--t1); }
nav[role="navigation"] ul li.active span,
nav[role="navigation"] ul li span[aria-current] {
  background: rgba(59,130,246,0.15); border-color: rgba(59,130,246,0.35); color: #60a5fa;
}
</style>

{{-- ══ PAGE HEADER ══ --}}
<div class="page-top">
  <div class="page-brand">

    {{-- Stackly inline SVG logo --}}
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 180 40" height="28">
      <defs>
        <linearGradient id="hlg1" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#3b82f6"/>
          <stop offset="100%" stop-color="#22d3ee"/>
        </linearGradient>
        <linearGradient id="hlg2" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" stop-color="#e2eaff"/>
          <stop offset="100%" stop-color="#a5c0ff"/>
        </linearGradient>
      </defs>
      <rect x="0" y="4" width="32" height="32" rx="8" fill="url(#hlg1)" opacity="0.15"/>
      <rect x="0" y="4" width="32" height="32" rx="8" fill="none" stroke="url(#hlg1)" stroke-width="1.2" opacity="0.55"/>
      <rect x="7"  y="13"   width="18" height="3" rx="1.5" fill="url(#hlg1)" opacity="1"/>
      <rect x="9"  y="18.5" width="14" height="3" rx="1.5" fill="url(#hlg1)" opacity="0.75"/>
      <rect x="11" y="24"   width="10" height="3" rx="1.5" fill="url(#hlg1)" opacity="0.5"/>
      <text x="42" y="27" font-family="Outfit,Inter,sans-serif" font-size="19" font-weight="800" letter-spacing="1.5" fill="url(#hlg2)">STACKLY</text>
    </svg>

    <div class="page-title-block">
      <div class="page-title">
        <i class="fa-solid fa-clock-rotate-left"></i> Code History
      </div>
      <div class="page-sub">All your saved code snippets</div>
    </div>
  </div>

  {{-- open editor shortcut --}}
  <a href="/editor" style="display:inline-flex;align-items:center;gap:8px;padding:9px 20px;border-radius:10px;background:linear-gradient(135deg,#3b82f6,#6366f1);color:#fff;font-size:13.5px;font-weight:600;text-decoration:none;box-shadow:0 0 18px rgba(59,130,246,0.3);transition:transform .15s" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
    <i class="fa-solid fa-code"></i> Open Editor
  </a>
</div>

{{-- ══ STATS BAR ══ --}}
<div class="stats-bar">
  <div class="stat-pill sp-blue">
    <i class="fa-solid fa-file-code"></i>
    <span><strong>{{ $codes->total() }}</strong> snippets saved</span>
  </div>
  @php
    /* most used language across the paginated collection */
    $langCounts = $codes->getCollection()->groupBy('language')->map->count()->sortDesc();
    $topLang    = $langCounts->keys()->first() ?? '—';
  @endphp
  <div class="stat-pill sp-purple">
    <i class="fa-solid fa-layer-group"></i>
    <span>Most used: <strong>{{ $topLang }}</strong></span>
  </div>
  <div class="stat-pill sp-green">
    <i class="fa-solid fa-calendar-day"></i>
    <span>Page <strong>{{ $codes->currentPage() }}</strong> of <strong>{{ $codes->lastPage() }}</strong></span>
  </div>
</div>

{{-- ══ FLASH MESSAGE ══ --}}
@if(session('success'))
  <div class="alert-success">
    <i class="fa-solid fa-circle-check"></i>
    {{ session('success') }}
  </div>
@endif

{{-- ══ SEARCH + LANGUAGE FILTER BAR ══ --}}
<div class="filter-bar">
  <div class="search-wrap">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input
      class="search-input"
      type="text"
      id="searchInput"
      placeholder="Search your code snippets…"
      oninput="filterCards()"
    >
  </div>

  {{-- language filter pills — clicking filters the visible cards --}}
  <span class="filter-pill active" data-lang="all"        onclick="setLang(this)">All</span>
  <span class="filter-pill fp-py"  data-lang="python"     onclick="setLang(this)">Python</span>
  <span class="filter-pill fp-js"  data-lang="javascript" onclick="setLang(this)">JS</span>
  <span class="filter-pill fp-php" data-lang="php"        onclick="setLang(this)">PHP</span>
  <span class="filter-pill fp-html"data-lang="html"       onclick="setLang(this)">HTML</span>
  <span class="filter-pill fp-css" data-lang="css"        onclick="setLang(this)">CSS</span>
  <span class="filter-pill fp-java"data-lang="java"       onclick="setLang(this)">Java</span>
</div>

{{-- ══ EMPTY STATE ══ --}}
@if($codes->count() === 0)
  <div class="empty-state">
    <div class="empty-icon"><i class="fa-solid fa-code"></i></div>
    <h3>No saved code yet</h3>
    <p>Head to the editor, write some code, and save it — it will appear here.</p>
    <a href="/editor" style="display:inline-flex;align-items:center;gap:8px;padding:10px 22px;border-radius:10px;background:linear-gradient(135deg,#3b82f6,#6366f1);color:#fff;font-size:13.5px;font-weight:600;text-decoration:none;box-shadow:0 0 18px rgba(59,130,246,0.25)">
      <i class="fa-solid fa-rocket"></i> Start Coding
    </a>
  </div>
@endif

{{-- ══ CODE CARDS GRID ══ --}}
<div class="cards-grid" id="cardsGrid">
  @foreach($codes as $code)
  <div
    class="code-card lc-{{ strtolower($code->language) }}"
    data-lang="{{ strtolower($code->language) }}"
    data-code="{{ strtolower($code->code) }}"
  >
    {{-- card header: language badge + timestamp --}}
    <div class="card-head">
      <div class="card-left">
        <span class="lang-badge lb-{{ strtolower($code->language) }}">
          {{ strtoupper($code->language) }}
        </span>
        {{-- show snippet line count --}}
        <span style="font-size:11.5px;color:var(--t3)">
          {{ count(explode("\n", trim($code->code))) }} lines
        </span>
      </div>
      <div class="card-meta">
        <i class="fa-regular fa-clock"></i>
        {{ $code->created_at->diffForHumans() }}
      </div>
    </div>

    {{-- collapsible code preview --}}
    <div class="code-block" id="block-{{ $code->id }}">
      <pre>{{ $code->code }}</pre>
      <div class="code-fade" id="fade-{{ $code->id }}"></div>
    </div>

    {{-- expand / collapse toggle --}}
    <button
      class="expand-btn"
      id="expbtn-{{ $code->id }}"
      onclick="toggleExpand({{ $code->id }})"
    >
      <i class="fa-solid fa-chevron-down"></i>
      <span>Show more</span>
    </button>

    {{-- action buttons --}}
    <div class="card-actions">
      {{-- copy to clipboard --}}
      <button
        class="action-btn btn-copy"
        onclick="copyCode(this, {{ json_encode($code->code) }})"
      >
        <i class="fa-solid fa-copy"></i> Copy
      </button>

      {{-- open this code in the editor --}}
      <a
        href="/editor?code={{ urlencode($code->code) }}&lang={{ $code->language }}"
        class="action-btn btn-edit"
        style="text-decoration:none"
      >
        <i class="fa-solid fa-pen-to-square"></i> Edit
      </a>

      {{-- delete with confirmation --}}
      <form method="POST" action="{{ route('delete.code', $code->id) }}" style="margin-left:auto">
        @csrf
        @method('DELETE')
        <button
          class="action-btn btn-delete"
          onclick="return confirm('Delete this snippet permanently?')"
        >
          <i class="fa-solid fa-trash"></i> Delete
        </button>
      </form>
    </div>
  </div>
  @endforeach
</div>

{{-- ══ PAGINATION ══ --}}
<div style="margin-top:8px">
  {{ $codes->links() }}
</div>


<script>
/* ── COPY TO CLIPBOARD ──────────────────────────────────
   Copies the code string, shows a green "Copied" state
   for 1.5s then resets the button.
*/
function copyCode(btn, code) {
  navigator.clipboard.writeText(code);
  btn.classList.add('copied');
  btn.innerHTML = '<i class="fa-solid fa-check"></i> Copied!';
  setTimeout(() => {
    btn.classList.remove('copied');
    btn.innerHTML = '<i class="fa-solid fa-copy"></i> Copy';
  }, 1500);
}

/* ── EXPAND / COLLAPSE CODE BLOCK ───────────────────────
   Toggles between the collapsed (160px) and expanded
   state for a single card's code preview.
*/
function toggleExpand(id) {
  const block  = document.getElementById('block-'  + id);
  const btn    = document.getElementById('expbtn-' + id);
  const isOpen = block.classList.toggle('expanded');
  btn.classList.toggle('open', isOpen);
  btn.querySelector('span').textContent = isOpen ? 'Show less' : 'Show more';
}

/* ── SEARCH FILTER ──────────────────────────────────────
   Hides cards whose code content doesn't include the
   search query (case-insensitive).
*/
function filterCards() {
  const query = document.getElementById('searchInput').value.toLowerCase();
  document.querySelectorAll('.code-card').forEach(card => {
    const matches = card.dataset.code.includes(query);
    card.style.display = matches ? '' : 'none';
  });
}

/* ── LANGUAGE FILTER PILLS ──────────────────────────────
   Shows only cards matching the selected language.
   "All" removes the filter.
*/
let activeLang = 'all';

function setLang(pill) {
  // update active pill style
  document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
  pill.classList.add('active');
  activeLang = pill.dataset.lang;

  // show/hide cards by language
  document.querySelectorAll('.code-card').forEach(card => {
    const match = activeLang === 'all' || card.dataset.lang === activeLang;
    card.style.display = match ? '' : 'none';
  });

  // also re-apply search if active
  filterCards();
}
</script>

@endsection
