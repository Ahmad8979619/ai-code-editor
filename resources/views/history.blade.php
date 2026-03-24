<!DOCTYPE html>

<html>

<head>

<title>History</title>

<style>

body{

background:#0f172a;

color:white;

font-family:Arial;

padding:40px;

}

.card{

background:#020617;

padding:20px;

margin-bottom:20px;

border-radius:10px;

}

button{

padding:8px;

background:#22c55e;

border:none;

color:white;

cursor:pointer;

}

</style>

</head>

<body>

<h2>Saved Codes</h2>

<a href="/editor">

<button>Back to Editor</button>

</a>

<br><br>

@foreach($codes as $code)

<div class="card">

<p>

Language:

{{ $code->language }}

</p>

<pre>

{{ $code->code }}

</pre>

<button
onclick="copyCode(

`{{ $code->code }}`

)">

Copy

</button>

</div>

@endforeach


<script>

function copyCode(code){

navigator.clipboard.writeText(code);

alert("Copied!");

}

</script>

</body>

</html>
