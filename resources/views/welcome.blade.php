<!DOCTYPE html>
<html>
<head>
<title>AI Code Editor</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
  :root {
    --bg-deep:    #02040f;
    --bg-mid:     #070d1f;
    --bg-surface: #0c1428;
    --bg-raised:  #111c35;
    --border:     rgba(99,160,255,0.12);
    --border-bright: rgba(99,160,255,0.28);
    --cyan:   #22d3ee;
    --blue:   #3b82f6;
    --indigo: #818cf8;
    --violet: #a78bfa;
    --green:  #4ade80;
    --text-1: #e2eaff;
    --text-2: #8ba0c8;
    --text-3: #4a6080;
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  html { scroll-behavior: smooth; }

  body {
    font-family: 'Outfit', sans-serif;
    color: var(--text-1);
    background: var(--bg-deep);
    overflow-x: hidden;
    position: relative;
  }

  /* ── animated mesh bg ── */
  .bg-mesh {
    position: fixed;
    inset: 0;
    z-index: 0;
    pointer-events: none;
    background:
      radial-gradient(ellipse 70% 55% at 5% 5%,  rgba(59,130,246,0.14) 0%, transparent 60%),
      radial-gradient(ellipse 55% 45% at 95% 15%, rgba(129,140,248,0.11) 0%, transparent 55%),
      radial-gradient(ellipse 60% 50% at 50% 95%, rgba(34,211,238,0.08) 0%, transparent 55%);
    animation: meshDrift 18s ease-in-out infinite alternate;
  }

  @keyframes meshDrift {
    0%   { transform: scale(1)    translateY(0);    opacity: 1; }
    100% { transform: scale(1.06) translateY(-20px); opacity: 0.75; }
  }

  /* scanlines */
  .bg-scan {
    position: fixed;
    inset: 0;
    z-index: 0;
    pointer-events: none;
    background: repeating-linear-gradient(
      0deg, transparent, transparent 2px,
      rgba(0,0,0,0.07) 2px, rgba(0,0,0,0.07) 4px
    );
  }

  /* grid lines */
  .bg-grid {
    position: fixed;
    inset: 0;
    z-index: 0;
    pointer-events: none;
    background-image:
      linear-gradient(rgba(99,160,255,0.03) 1px, transparent 1px),
      linear-gradient(90deg, rgba(99,160,255,0.03) 1px, transparent 1px);
    background-size: 60px 60px;
  }

  /* ── all content above layers ── */
  nav, main, footer { position: relative; z-index: 1; }

  /* ── navbar ── */
  nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 40px;
    height: 62px;
    background: rgba(7,13,31,0.8);
    backdrop-filter: blur(20px) saturate(180%);
    border-bottom: 1px solid var(--border);
    box-shadow: 0 1px 0 rgba(99,160,255,0.07), 0 6px 30px rgba(0,0,0,0.35);
    position: sticky;
    top: 0;
    z-index: 100;
  }

  .logo {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--text-1);
    text-decoration: none;
  }

  .logo-icon {
    width: 34px;
    height: 34px;
    border-radius: 9px;
    background: linear-gradient(135deg, var(--blue), var(--cyan));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    box-shadow: 0 0 14px rgba(59,130,246,0.45), 0 0 36px rgba(59,130,246,0.2);
    animation: logoPulse 3s ease-in-out infinite;
    flex-shrink: 0;
  }

  @keyframes logoPulse {
    0%, 100% { box-shadow: 0 0 14px rgba(59,130,246,0.45), 0 0 36px rgba(59,130,246,0.2); }
    50%       { box-shadow: 0 0 22px rgba(34,211,238,0.6),  0 0 55px rgba(34,211,238,0.25); }
  }

  .logo-badge {
    font-size: 9px;
    font-weight: 600;
    letter-spacing: 0.14em;
    color: var(--cyan);
    background: rgba(34,211,238,0.08);
    border: 1px solid rgba(34,211,238,0.22);
    padding: 2px 7px;
    border-radius: 20px;
  }

  .nav-right { display: flex; align-items: center; gap: 10px; }

  /* ── buttons ── */
  a { text-decoration: none; }

  .btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 20px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    font-family: 'Outfit', sans-serif;
    font-size: 14px;
    font-weight: 500;
    color: white;
    transition: transform 0.18s, box-shadow 0.18s, filter 0.18s;
    position: relative;
    overflow: hidden;
    letter-spacing: 0.01em;
  }

  .btn::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(160deg, rgba(255,255,255,0.18) 0%, transparent 60%);
    opacity: 0;
    transition: opacity 0.2s;
  }

  .btn:hover::after { opacity: 1; }
  .btn:hover { transform: translateY(-2px); filter: brightness(1.1); }
  .btn:active { transform: translateY(0); }

  .btn-ghost {
    background: rgba(11,18,40,0.7);
    border: 1px solid var(--border-bright);
    color: var(--text-2);
  }
  .btn-ghost:hover { color: var(--text-1); border-color: rgba(99,160,255,0.45); box-shadow: 0 0 14px rgba(59,130,246,0.15); }

  .btn-green {
    background: linear-gradient(135deg, #22c55e, #0d9488);
    box-shadow: 0 0 12px rgba(34,197,94,0.3);
  }
  .btn-green:hover { box-shadow: 0 0 22px rgba(74,222,128,0.55), 0 0 44px rgba(74,222,128,0.2); }

  .btn-blue {
    background: linear-gradient(135deg, var(--blue), var(--indigo));
    box-shadow: 0 0 12px rgba(59,130,246,0.3);
  }
  .btn-blue:hover { box-shadow: 0 0 22px rgba(59,130,246,0.6), 0 0 44px rgba(59,130,246,0.2); }

  .btn-lg {
    padding: 13px 28px;
    font-size: 15px;
    border-radius: 12px;
  }

  /* ── hero ── */
  .hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 48px;
    max-width: 1160px;
    margin: 0 auto;
    padding: 100px 40px 80px;
  }

  .hero-text { max-width: 520px; }

  .hero-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--cyan);
    background: rgba(34,211,238,0.07);
    border: 1px solid rgba(34,211,238,0.2);
    padding: 5px 14px;
    border-radius: 20px;
    margin-bottom: 24px;
  }

  .hero-tag .dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: var(--cyan);
    box-shadow: 0 0 8px rgba(34,211,238,0.9);
    animation: dotBlink 2s ease-in-out infinite;
  }

  @keyframes dotBlink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
  }

  h1 {
    font-size: clamp(36px, 5vw, 52px);
    font-weight: 800;
    line-height: 1.1;
    letter-spacing: -0.02em;
    margin-bottom: 22px;
    color: var(--text-1);
  }

  h1 .accent {
    background: linear-gradient(90deg, var(--blue), var(--cyan));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .hero p {
    font-size: 17px;
    line-height: 1.7;
    color: var(--text-2);
    margin-bottom: 36px;
  }

  .hero-btns { display: flex; gap: 12px; flex-wrap: wrap; }

  /* ── hero image / code preview ── */
  .hero-visual {
    flex-shrink: 0;
    width: 480px;
    max-width: 100%;
  }

  .code-window {
    background: rgba(7,13,31,0.85);
    border: 1px solid var(--border-bright);
    border-radius: 16px;
    overflow: hidden;
    box-shadow:
      0 0 0 1px rgba(255,255,255,0.03) inset,
      0 0 50px rgba(59,130,246,0.15),
      0 0 100px rgba(34,211,238,0.07),
      0 30px 80px rgba(0,0,0,0.5);
    backdrop-filter: blur(20px);
    animation: floatWindow 6s ease-in-out infinite;
  }

  @keyframes floatWindow {
    0%, 100% { transform: translateY(0) rotate(-0.5deg); }
    50%       { transform: translateY(-12px) rotate(0.5deg); }
  }

  .window-bar {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 12px 16px;
    background: rgba(2,4,15,0.5);
    border-bottom: 1px solid var(--border);
  }

  .window-dot { width: 11px; height: 11px; border-radius: 50%; }
  .wd-red   { background: #f87171; box-shadow: 0 0 6px rgba(248,113,113,0.6); }
  .wd-amber { background: #fbbf24; box-shadow: 0 0 6px rgba(251,191,36,0.6); }
  .wd-green { background: #4ade80; box-shadow: 0 0 6px rgba(74,222,128,0.6); }

  .window-title {
    margin-left: 8px;
    font-size: 12px;
    font-family: 'JetBrains Mono', monospace;
    color: var(--text-3);
  }

  .code-body {
    padding: 20px 22px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 12.5px;
    line-height: 1.8;
  }

  .ln { color: var(--text-3); user-select: none; margin-right: 18px; }
  .kw  { color: #c084fc; }
  .fn  { color: #60a5fa; }
  .str { color: #86efac; }
  .cm  { color: var(--text-3); font-style: italic; }
  .num { color: #fb923c; }
  .op  { color: var(--cyan); }

  .ai-suggestion {
    margin: 6px 0;
    background: rgba(34,211,238,0.05);
    border-left: 2px solid var(--cyan);
    border-radius: 0 6px 6px 0;
    padding: 4px 10px;
  }

  .ai-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 10px;
    color: var(--cyan);
    background: rgba(34,211,238,0.1);
    border: 1px solid rgba(34,211,238,0.2);
    padding: 2px 8px;
    border-radius: 20px;
    margin-bottom: 4px;
  }

  /* ── features ── */
  .features-section {
    max-width: 1000px;
    margin: 0 auto;
    padding: 60px 40px 100px;
  }

  .section-label {
    text-align: center;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--text-3);
    margin-bottom: 14px;
  }

  .section-title {
    text-align: center;
    font-size: clamp(24px, 3vw, 32px);
    font-weight: 700;
    margin-bottom: 50px;
    color: var(--text-1);
  }

  .features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
  }

  .feature-card {
    background: rgba(7,13,31,0.7);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 28px 24px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    backdrop-filter: blur(16px);
    transition: border-color 0.25s, box-shadow 0.25s, transform 0.25s;
    box-shadow: 0 0 0 1px rgba(255,255,255,0.02) inset;
    position: relative;
    overflow: hidden;
  }

  .feature-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(99,160,255,0.35), transparent);
    opacity: 0;
    transition: opacity 0.3s;
  }

  .feature-card:hover {
    border-color: var(--border-bright);
    transform: translateY(-5px);
  }

  .feature-card:hover::before { opacity: 1; }

  .feature-card:nth-child(1):hover { box-shadow: 0 0 30px rgba(74,222,128,0.12), 0 20px 40px rgba(0,0,0,0.3); }
  .feature-card:nth-child(2):hover { box-shadow: 0 0 30px rgba(59,130,246,0.12), 0 20px 40px rgba(0,0,0,0.3); }
  .feature-card:nth-child(3):hover { box-shadow: 0 0 30px rgba(34,211,238,0.12), 0 20px 40px rgba(0,0,0,0.3); }

  .feature-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
  }

  .fi-green  { background: rgba(74,222,128,0.1);  color: var(--green);  box-shadow: 0 0 16px rgba(74,222,128,0.15); }
  .fi-blue   { background: rgba(59,130,246,0.1);  color: var(--blue);   box-shadow: 0 0 16px rgba(59,130,246,0.15); }
  .fi-cyan   { background: rgba(34,211,238,0.1);  color: var(--cyan);   box-shadow: 0 0 16px rgba(34,211,238,0.15); }

  .feature-card h3 {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-1);
  }

  .feature-card p {
    font-size: 14px;
    line-height: 1.65;
    color: var(--text-2);
  }

  /* ── footer ── */
  footer {
    border-top: 1px solid var(--border);
    text-align: center;
    padding: 30px 20px;
    font-size: 13px;
    color: var(--text-3);
    background: rgba(2,4,15,0.4);
  }

  footer span { color: var(--text-2); }

  @media (max-width: 900px) {
    .hero { flex-direction: column; padding: 60px 24px 50px; text-align: center; }
    .hero-btns { justify-content: center; }
    .hero-visual { width: 100%; }
    .features-grid { grid-template-columns: 1fr; }
    nav { padding: 0 20px; }
  }
</style>
</head>

<body>

<div class="bg-mesh"></div>
<div class="bg-scan"></div>
<div class="bg-grid"></div>

<!-- NAV -->
<nav>
  <a href="/" class="logo">
    <div class="logo-icon"><i class="fa-solid fa-bolt"></i></div>
    AiEditor
    <span class="logo-badge">BETA</span>
  </a>

  <div class="nav-right">
    <a href="/login"><button class="btn btn-ghost">Login</button></a>
  </div>
</nav>

<main>

  <!-- HERO -->
  <section class="hero">
    <div class="hero-text">
      <div class="hero-tag">
        <span class="dot"></span>
        AI-Powered Development
      </div>

      <h1>Code Smarter<br>with <span class="accent">AI Assistance</span></h1>

      <p>
        An online code editor supercharged by artificial intelligence.
        Get real-time suggestions, automatic error fixes, and
        explanations that accelerate your workflow.
      </p>

      <div class="hero-btns">
        <a href="/editor"><button class="btn btn-green btn-lg"><i class="fa-solid fa-rocket"></i> Start Coding</button></a>
        <a href="/register"><button class="btn btn-blue btn-lg"><i class="fa-solid fa-user-plus"></i> Create Account</button></a>
      </div>
    </div>

    <div class="hero-visual">
      <div class="code-window">
        <div class="window-bar">
          <span class="window-dot wd-red"></span>
          <span class="window-dot wd-amber"></span>
          <span class="window-dot wd-green"></span>
          <span class="window-title">main.py — AI Code Editor</span>
        </div>
        <div class="code-body">
          <div><span class="ln">1</span><span class="kw">import</span> <span class="fn">requests</span></div>
          <div><span class="ln">2</span></div>
          <div><span class="ln">3</span><span class="kw">def</span> <span class="fn">fetch_data</span><span class="op">(</span>url<span class="op">):</span></div>
          <div><span class="ln">4</span>&nbsp;&nbsp;<span class="cm"># Fetch JSON from endpoint</span></div>
          <div><span class="ln">5</span>&nbsp;&nbsp;response <span class="op">=</span> requests<span class="op">.</span><span class="fn">get</span><span class="op">(</span>url<span class="op">)</span></div>
          <div><span class="ln">6</span>&nbsp;&nbsp;<span class="kw">return</span> response<span class="op">.</span><span class="fn">json</span><span class="op">()</span></div>
          <div><span class="ln">7</span></div>
          <div class="ai-suggestion">
            <div class="ai-badge"><i class="fa-solid fa-robot"></i> AI Suggestion</div>
            <div><span class="ln">8</span>&nbsp;&nbsp;<span class="kw">if</span> response<span class="op">.</span>status_code <span class="op">!=</span> <span class="num">200</span><span class="op">:</span></div>
            <div><span class="ln">9</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="kw">raise</span> <span class="fn">Exception</span><span class="op">(</span><span class="str">"Request failed"</span><span class="op">)</span></div>
          </div>
          <div><span class="ln">10</span></div>
          <div><span class="ln">11</span>result <span class="op">=</span> <span class="fn">fetch_data</span><span class="op">(</span><span class="str">"https://api.example.com"</span><span class="op">)</span></div>
          <div><span class="ln">12</span><span class="fn">print</span><span class="op">(</span>result<span class="op">)</span></div>
        </div>
      </div>
    </div>
  </section>

  <!-- FEATURES -->
  <section class="features-section">
    <div class="section-label">Everything you need</div>
    <div class="section-title">Built for Modern Developers</div>

    <div class="features-grid">

      <div class="feature-card">
        <div class="feature-icon fi-green">
          <i class="fa-solid fa-robot"></i>
        </div>
        <h3>AI Suggestions</h3>
        <p>Smart, context-aware code completion powered by AI. Get completions that understand your entire codebase.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon fi-blue">
          <i class="fa-solid fa-bug-slash"></i>
        </div>
        <h3>Error Fixing</h3>
        <p>Automatic debugging and one-click fixes. Catch bugs before they reach production with intelligent analysis.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon fi-cyan">
          <i class="fa-solid fa-bolt"></i>
        </div>
        <h3>Fast Coding</h3>
        <p>Write code faster and smarter. Save hours every session with AI-assisted scaffolding and refactoring.</p>
      </div>

    </div>
  </section>

</main>

<footer>
  <span>AiEditor</span> &nbsp;·&nbsp; © 2026 &nbsp;·&nbsp; Built with intelligence
</footer>

</body>
</html>
