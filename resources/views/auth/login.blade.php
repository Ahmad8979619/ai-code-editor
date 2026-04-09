<!DOCTYPE html>
<html>

<head>

<title>Login</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

*{
box-sizing:border-box;
font-family:system-ui;
}

body{

margin:0;
height:100vh;

display:flex;
justify-content:center;
align-items:center;

background:
radial-gradient(circle at 20% 20%, rgba(34,197,94,0.15), transparent 40%),
radial-gradient(circle at 80% 80%, rgba(59,130,246,0.15), transparent 40%),
#020617;

overflow:hidden;
color:white;

}

/* animated glow circles */

.glow{

position:absolute;
border-radius:50%;
filter:blur(120px);
opacity:0.25;
animation:float 6s infinite alternate ease-in-out;

}

.glow1{

width:400px;
height:400px;
background:#22c55e;
top:-120px;
left:-120px;

}

.glow2{

width:500px;
height:500px;
background:#3b82f6;
bottom:-150px;
right:-150px;

animation-delay:2s;

}

@keyframes float{

0%{
transform:translateY(0);
}

100%{
transform:translateY(-40px);
}

}


/* login card */

.card{

width:370px;

padding:40px;

border-radius:18px;

background:rgba(15,23,42,0.7);

backdrop-filter:blur(25px);

border:1px solid rgba(255,255,255,0.05);

box-shadow:
0 25px 70px rgba(0,0,0,0.8),
0 0 30px rgba(34,197,94,0.05);

transition:0.3s;

}

.card:hover{

transform:translateY(-3px);

box-shadow:
0 30px 90px rgba(0,0,0,0.9),
0 0 40px rgba(34,197,94,0.2);

}


/* logo */

.logo{

text-align:center;

font-size:44px;

color:#22c55e;

margin-bottom:15px;

animation:pulse 2s infinite;

}

@keyframes pulse{

0%{text-shadow:0 0 10px rgba(34,197,94,0.3);}

50%{

text-shadow:
0 0 25px rgba(34,197,94,0.9),
0 0 45px rgba(34,197,94,0.3);

}

100%{text-shadow:0 0 10px rgba(34,197,94,0.3);}

}


h2{

text-align:center;

margin-bottom:25px;

font-weight:600;

}


/* inputs */

.input-group{

position:relative;

margin-bottom:18px;

}

input{

width:100%;

padding:13px 45px;

border-radius:10px;

border:1px solid #1e293b;

background:#020617;

color:white;

font-size:14px;

outline:none;

transition:0.2s;

}

input:focus{

border:1px solid #22c55e;

box-shadow:
0 0 0 2px rgba(34,197,94,0.2),
0 0 20px rgba(34,197,94,0.15);

transform:scale(1.01);

}


/* icons */

.icon{

position:absolute;

top:50%;

left:15px;

transform:translateY(-50%);

opacity:0.5;

}


/* eye */

.eye{

position:absolute;

top:50%;

right:15px;

transform:translateY(-50%);

cursor:pointer;

opacity:0.5;

}

.eye:hover{

color:#22c55e;

opacity:1;

}


/* button */

button{

width:100%;

padding:13px;

border:none;

border-radius:10px;

font-size:15px;

cursor:pointer;

background:
linear-gradient(135deg,#22c55e,#16a34a);

color:white;

margin-top:5px;

transition:0.25s;

position:relative;
overflow:hidden;

}

button::before{

content:"";

position:absolute;

width:140%;
height:140%;

top:-20%;
left:-20%;

background:linear-gradient(
120deg,
transparent,
rgba(255,255,255,0.4),
transparent
);

transform:translateX(-100%);
transition:0.6s;

}

button:hover::before{

transform:translateX(100%);

}

button:hover{

transform:translateY(-2px);

box-shadow:
0 8px 35px rgba(34,197,94,0.35);

}


/* error */

.error{

background:rgba(239,68,68,0.15);

border:1px solid rgba(239,68,68,0.4);

padding:10px;

border-radius:8px;

margin-bottom:15px;

font-size:13px;

text-align:center;

color:#fca5a5;

}


/* link */

.link{

text-align:center;

margin-top:18px;

font-size:14px;

}

a{

color:#3b82f6;

text-decoration:none;

}

</style>

</head>


<body>

<div class="glow glow1"></div>

<div class="glow glow2"></div>


<div class="card">


<div class="logo">

<i class="fa-solid fa-code"></i>

</div>


<h2>Welcome Back</h2>


@if ($errors->any())

<div class="error">

Invalid email or password

</div>

@endif



<form method="POST" action="/login">

@csrf


<div class="input-group">

<i class="fa-solid fa-envelope icon"></i>

<input
type="email"
name="email"
placeholder="Email address"
required>

</div>



<div class="input-group">

<i class="fa-solid fa-lock icon"></i>

<input
type="password"
name="password"
id="password"
placeholder="Password"
required>

<i
class="fa-solid fa-eye eye"
onclick="togglePassword()">

</i>

</div>


<button>

Login

</button>


</form>


<div class="link">

Don't have account?

<a href="/register">

Create account

</a>

</div>


</div>



<script>

function togglePassword(){

let input=
document.getElementById("password");

input.type=
input.type==="password"
? "text"
: "password";

}

</script>


</body>

</html>
