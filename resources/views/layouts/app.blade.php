<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name','AI Editor') }}</title>


<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


<style>

/* ========= GLOBAL THEME ========= */

:root{

--bg:#020617;
--panel:#020617cc;
--border:#1e293b;

--blue:#3b82f6;
--cyan:#22d3ee;
--green:#22c55e;
--purple:#8b5cf6;
--red:#ef4444;

}


/* base */

body{

margin:0;
font-family:system-ui;
color:white;

background:

radial-gradient(circle at 15% 20%, rgba(59,130,246,0.15), transparent 40%),
radial-gradient(circle at 80% 80%, rgba(34,197,94,0.12), transparent 40%),
#020617;

min-height:100vh;

display:flex;

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


/* sidebar */

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


/* nav links */

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

text-decoration:none;

}


/* glow bar */

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

0 0 30px rgba(59,130,246,0.05);

}


.card:hover{

transform:translateY(-4px);

box-shadow:

0 0 40px rgba(59,130,246,0.15);

}


/* button */

.btn{

border:none;

padding:12px 20px;

border-radius:10px;

cursor:pointer;

color:white;

display:inline-flex;

align-items:center;

gap:10px;

font-size:14px;

transition:0.25s;

background:

linear-gradient(135deg,var(--blue),#2563eb);

box-shadow:

0 0 20px rgba(59,130,246,0.3);

text-decoration:none;

}

.btn:hover{

transform:translateY(-2px);

box-shadow:

0 0 35px rgba(59,130,246,0.5);

}


/* stat */

.stat{

font-size:32px;

margin-top:10px;

color:var(--green);

text-shadow:

0 0 20px rgba(34,197,94,0.6);

}

</style>

</head>


<body>


<div class="glow glow1"></div>
<div class="glow glow2"></div>



@if(auth()->check())

<div class="sidebar">


<div class="logo">

<i class="fa-solid fa-bolt"></i>

AI Editor

</div>


<a href="{{ route('dashboard') }}"
class="nav-item {{ request()->routeIs('dashboard')?'active':'' }}">

<i class="fa-solid fa-chart-line"></i>

Dashboard

</a>


<a href="{{ route('editor') }}"
class="nav-item {{ request()->routeIs('editor')?'active':'' }}">

<i class="fa-solid fa-code"></i>

Editor

</a>


<a href="{{ route('history') }}"
class="nav-item {{ request()->routeIs('history')?'active':'' }}">

<i class="fa-solid fa-clock"></i>

History

</a>


<form method="POST" action="{{ route('logout') }}">

@csrf

<button class="btn"
style="margin-top:20px;width:100%;background:linear-gradient(135deg,#ef4444,#dc2626)">

<i class="fa-solid fa-right-from-bracket"></i>

Logout

</button>

</form>


</div>

@endif



<div class="main">

{{ $slot ?? '' }}

@yield('content')

</div>


</body>

</html>
