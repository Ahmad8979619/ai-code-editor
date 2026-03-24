<!DOCTYPE html>
<html>

<head>

<title>AI Code Editor</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

body{

margin:0;
font-family:Arial;

background:linear-gradient(
135deg,
#0f172a,
#020617
);

color:white;

}

.container{

max-width:1100px;
margin:auto;
padding:80px 20px;

display:flex;
align-items:center;
justify-content:space-between;

}

.text{

max-width:500px;

}

h1{

font-size:40px;
margin-bottom:20px;

}

p{

font-size:18px;
opacity:0.8;
line-height:1.6;

}

.buttons{

margin-top:30px;

}

button{

padding:12px 20px;
margin-right:10px;

border:none;
border-radius:8px;

cursor:pointer;
font-size:16px;

}

.start{

background:#22c55e;
color:white;

}

.login{

background:#3b82f6;
color:white;

}

button:hover{

opacity:0.8;

}

.image img{

width:450px;

border-radius:12px;

box-shadow:0 20px 50px rgba(0,0,0,0.5);

}

.nav{

padding:20px;

display:flex;
justify-content:space-between;
align-items:center;

background:#020617;

}

.logo{

font-size:20px;
font-weight:bold;

}

.features{

display:flex;
gap:40px;

padding:60px 20px;
max-width:1000px;
margin:auto;

}

.feature{

text-align:center;

}

.feature i{

font-size:30px;
margin-bottom:10px;

color:#22c55e;

}

.footer{

text-align:center;

padding:40px;
opacity:0.6;

}

</style>

</head>

<body>


<div class="nav">

<div class="logo">

<i class="fa-solid fa-code"></i>
AI Code Editor

</div>

<div>

<a href="/login">

<button class="login">

Login

</button>

</a>

</div>

</div>



<div class="container">

<div class="text">

<h1>

Smart AI Code Editor

</h1>

<p>

Online code editor powered by artificial intelligence.

Get real-time code suggestions,
automatic error fixing,
and explanations that help you learn faster.

</p>


<div class="buttons">

<a href="/editor">

<button class="start">

Start Coding

</button>

</a>


<a href="/register">

<button class="login">

Create Account

</button>

</a>

</div>

</div>


<div class="image">

<img
src="https://images.unsplash.com/photo-1555066931-4365d14bab8c">

</div>


</div>



<div class="features">


<div class="feature">

<i class="fa-solid fa-robot"></i>

<h3>AI Suggestions</h3>

<p>

Smart code completion using AI

</p>

</div>



<div class="feature">

<i class="fa-solid fa-bug"></i>

<h3>Error Fixing</h3>

<p>

Automatic debugging and fixes

</p>

</div>



<div class="feature">

<i class="fa-solid fa-bolt"></i>

<h3>Fast Coding</h3>

<p>

Write code faster and smarter

</p>

</div>


</div>



<div class="footer">

AI Code Editor © 2026

</div>


</body>

</html>
