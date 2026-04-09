<!DOCTYPE html>
<html>

<head>

<title>Dashboard</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

:root{

--bg:#020617;
--panel:#020617cc;
--border:#1e293b;

--green:#22c55e;
--blue:#3b82f6;
--purple:#8b5cf6;
--cyan:#22d3ee;
--red:#ef4444;

}

/* layout */

body{

margin:0;
font-family:system-ui;
color:white;
height:100vh;

background:

radial-gradient(circle at 15% 20%, rgba(59,130,246,0.15), transparent 40%),

radial-gradient(circle at 80% 80%, rgba(34,197,94,0.12), transparent 40%),

#020617;

display:flex;

}


/* neon sidebar */

.sidebar{

width:220px;

background:rgba(2,6,23,0.85);

backdrop-filter:blur(20px);

border-right:1px solid var(--border);

padding:25px 18px;

display:flex;

flex-direction:column;

gap:12px;

box-shadow:

0 0 30px rgba(0,0,0,0.8),

0 0 60px rgba(59,130,246,0.05);

}


/* logo */

.logo{

font-size:18px;

display:flex;

align-items:center;

gap:10px;

margin-bottom:15px;

color:var(--cyan);

text-shadow:

0 0 20px rgba(34,211,238,0.6);

}


/* sidebar links */

.nav-item{

padding:12px 14px;

border-radius:10px;

cursor:pointer;

display:flex;

align-items:center;

gap:12px;

font-size:14px;

color:#94a3b8;

transition:0.25s;

position:relative;

}


/* glow line */

.nav-item::before{

content:"";

position:absolute;

left:0;

top:50%;

height:0%;

width:3px;

background:var(--cyan);

border-radius:5px;

transform:translateY(-50%);

transition:0.25s;

}


.nav-item:hover{

background:rgba(59,130,246,0.08);

color:white;

box-shadow:

0 0 15px rgba(59,130,246,0.25);

}


.nav-item:hover::before{

height:60%;

}


/* active */

.active{

background:rgba(34,211,238,0.08);

color:var(--cyan);

box-shadow:

0 0 20px rgba(34,211,238,0.3);

}


.active::before{

height:70%;

}


/* main content */

.main{

flex:1;

padding:35px;

overflow:auto;

}


/* cards */

.card{

background:rgba(15,23,42,0.6);

padding:22px;

border-radius:14px;

border:1px solid var(--border);

margin-bottom:20px;

transition:0.3s;

box-shadow:

0 0 0 1px rgba(255,255,255,0.02) inset,

0 0 30px rgba(59,130,246,0.05);

}


.card:hover{

transform:translateY(-4px);

box-shadow:

0 0 40px rgba(59,130,246,0.15),

0 0 60px rgba(34,197,94,0.05);

}


/* stat */

.stat{

font-size:34px;

margin-top:10px;

color:var(--green);

text-shadow:

0 0 20px rgba(34,197,94,0.6);

}


/* grid */

.grid{

display:grid;

grid-template-columns:1fr 1fr;

gap:20px;

margin-bottom:25px;

}


/* buttons */

button{

border:none;

padding:12px 20px;

border-radius:10px;

cursor:pointer;

color:white;

display:flex;

align-items:center;

gap:10px;

font-size:14px;

transition:0.25s;

background:

linear-gradient(135deg,#3b82f6,#2563eb);

box-shadow:

0 0 20px rgba(59,130,246,0.3);

}


button:hover{

transform:translateY(-2px);

box-shadow:

0 0 35px rgba(59,130,246,0.5);

}


/* last code */

pre{

background:#020617;

padding:18px;

border-radius:12px;

overflow:auto;

font-size:13px;

border:1px solid var(--border);

}


/* glow background */

.glow{

position:fixed;

width:500px;
height:500px;

border-radius:50%;

filter:blur(120px);

opacity:0.25;

z-index:-1;

animation:float 8s infinite alternate;

}


.glow1{

background:var(--cyan);

top:-120px;

left:-120px;

}


.glow2{

background:var(--green);

bottom:-150px;

right:-150px;

animation-delay:2s;

}


@keyframes float{

0%{transform:translateY(0);}

100%{transform:translateY(-40px);}

}

</style>

</head>


<body>


<div class="glow glow1"></div>
<div class="glow glow2"></div>


<!-- sidebar -->

<div class="sidebar">


<div class="logo">

<i class="fa-solid fa-bolt"></i>

AiEditor

</div>


<a href="/dashboard" class="nav-item active">

<i class="fa-solid fa-chart-line"></i>

Dashboard

</a>


<a href="/editor" class="nav-item">

<i class="fa-solid fa-code"></i>

Editor

</a>


<a href="/history" class="nav-item">

<i class="fa-solid fa-clock"></i>

History

</a>


<form method="POST" action="/logout">

@csrf

<button style="margin-top:20px;width:100%;background:linear-gradient(135deg,#ef4444,#dc2626)">

<i class="fa-solid fa-right-from-bracket"></i>

Logout

</button>

</form>


</div>


<!-- main -->

<div class="main">


<h2>

Welcome {{ $user->name }} 👋

</h2>



<div class="grid">


<div class="card">

<h3>

<i class="fa-solid fa-file-code"></i>

Saved Codes

</h3>

<div class="stat">

{{ $totalFiles }}

</div>

</div>



<div class="card">

<h3>

<i class="fa-solid fa-robot"></i>

AI Usage

</h3>

<div class="stat">

{{ $totalFiles }}

</div>

</div>


</div>



<a href="/editor">

<button>

<i class="fa-solid fa-code"></i>

Open Editor

</button>

</a>



@if($lastCode)

<div class="card">


<h3>

<i class="fa-solid fa-clock"></i>

Last Code

</h3>


<pre>

{{ $lastCode->code }}

</pre>


</div>

@endif


</div>


</body>

</html>
