<!DOCTYPE html>
<html>

<head>

<title>Create Account</title>

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
radial-gradient(circle at 20% 20%, #1e293b, transparent 40%),
radial-gradient(circle at 80% 80%, #020617, transparent 40%),
#020617;

color:white;

}


/* glow background */

.bg-glow{

position:absolute;

width:550px;
height:550px;

background:#22c55e;

filter:blur(150px);

opacity:0.12;

border-radius:50%;

z-index:-1;

}


/* card */

.card{

width:380px;

padding:40px;

border-radius:18px;

background:rgba(15,23,42,0.75);

backdrop-filter:blur(20px);

border:1px solid rgba(255,255,255,0.05);

box-shadow:0 20px 60px rgba(0,0,0,0.6);

}


/* logo */

.logo{

text-align:center;
font-size:42px;
color:#22c55e;

margin-bottom:10px;

}


/* title */

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

box-shadow:0 0 0 2px rgba(34,197,94,0.2);

}


/* icons */

.icon{

position:absolute;

top:50%;

left:15px;

transform:translateY(-50%);

opacity:0.5;

}


/* eye icon */

.eye{

position:absolute;

top:50%;

right:15px;

transform:translateY(-50%);

cursor:pointer;

opacity:0.5;

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

transition:0.2s;

}

button:hover{

transform:translateY(-1px);

box-shadow:0 5px 20px rgba(34,197,94,0.3);

}


/* links */

.link{

text-align:center;

margin-top:18px;

font-size:14px;

}


a{

color:#3b82f6;

text-decoration:none;

}


/* error */

.error{

background:#ef4444;

padding:10px;

border-radius:8px;

margin-bottom:15px;

font-size:13px;

text-align:center;

}

</style>

</head>


<body>

<div class="bg-glow"></div>


<div class="card">


<div class="logo">

<i class="fa-solid fa-user-plus"></i>

</div>


<h2>Create Account</h2>


@if ($errors->any())

<div class="error">

Please check your inputs

</div>

@endif



<form method="POST" action="/register">

@csrf


<div class="input-group">

<i class="fa-solid fa-user icon"></i>

<input
type="text"
name="name"
placeholder="Full Name"
required>

</div>



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
onclick="togglePassword('password')">

</i>

</div>



<div class="input-group">

<i class="fa-solid fa-lock icon"></i>

<input
type="password"
name="password_confirmation"
id="password2"
placeholder="Confirm Password"
required>

<i
class="fa-solid fa-eye eye"
onclick="togglePassword('password2')">

</i>

</div>



<button>

Create Account

</button>


</form>


<div class="link">

Already have account?

<a href="/login">

Login

</a>

</div>


</div>



<script>

function togglePassword(id){

let input=
document.getElementById(id);

input.type=
input.type==="password"
? "text"
: "password";

}

</script>


</body>

</html>
