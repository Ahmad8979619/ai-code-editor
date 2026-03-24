<!DOCTYPE html>
<html>

<head>

<title>AI Code Editor</title>

<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

body{

margin:0;
font-family:system-ui;

background:linear-gradient(
135deg,
#0f172a,
#020617
);

color:white;

}

/* navbar */

.navbar{

display:flex;
justify-content:space-between;
align-items:center;

padding:15px 30px;

background:#020617;

border-bottom:1px solid #1e293b;

}

.logo{

font-size:20px;
font-weight:bold;

display:flex;
align-items:center;
gap:8px;

}

.right{

display:flex;
gap:10px;

}

/* layout */

.container{

display:flex;
gap:20px;

padding:20px;

}

/* sidebar */

.sidebar{

width:200px;

background:#020617;

padding:15px;

border-radius:12px;

border:1px solid #1e293b;

}

.sidebar h3{

margin-top:0;

}

/* editor */

.editor-box{

flex:1;

background:#020617;

border-radius:12px;

padding:10px;

border:1px solid #1e293b;

}

#editor{

height:500px;

border-radius:8px;

}

/* AI box */

.ai-box{

width:320px;

background:#020617;

border-radius:12px;

padding:15px;

border:1px solid #1e293b;

}

/* buttons */

button{

border:none;

padding:8px 14px;

border-radius:8px;

cursor:pointer;

color:white;

display:flex;
align-items:center;
gap:6px;

font-size:14px;

}

.green{

background:#22c55e;

}

.blue{

background:#3b82f6;

}

.gray{

background:#334155;

}

.red{

background:#ef4444;

}

button:hover{

opacity:0.85;

}

/* code output */

pre{

white-space:pre-wrap;

background:#020617;

padding:10px;

border-radius:8px;

}

.output{

margin-top:15px;

}

.file{

padding:6px;

border-radius:6px;

cursor:pointer;

}

.file:hover{

background:#1e293b;

}

/* top controls */

.controls{

display:flex;

gap:10px;

margin-bottom:10px;

}

select{

padding:6px;

border-radius:6px;

}

</style>

</head>


<body>


<div class="navbar">

<div class="logo">

<i class="fa-solid fa-code"></i>

AI Code Editor

</div>


<div class="right">


<select id="language">

<option value="python">Python</option>

<option value="javascript">JavaScript</option>

<option value="php">PHP</option>

<option value="java">Java</option>

</select>


<button
class="green"
onclick="askAI()">

<i class="fa-solid fa-robot"></i>

Suggest

</button>


<button
class="green"
onclick="applyFix()">

<i class="fa-solid fa-wrench"></i>

Apply

</button>


<button
class="blue"
onclick="runCode()">

<i class="fa-solid fa-play"></i>

Run

</button>


<button
class="gray"
onclick="toggleTheme()">

<i class="fa-solid fa-moon"></i>

Theme

</button>


<a href="/history">

<button
class="green">

<i class="fa-solid fa-clock"></i>

History

</button>

</a>


<form method="POST" action="/logout">

@csrf

<button
class="red">

<i class="fa-solid fa-right-from-bracket"></i>

Logout

</button>

</form>


</div>


</div>



<div class="container">


<div class="sidebar">

<h3>

<i class="fa-solid fa-folder"></i>

Files

</h3>


<div id="files"></div>


<button
class="green"
onclick="newFile()">

<i class="fa-solid fa-plus"></i>

New File

</button>


</div>



<div class="editor-box">


<div class="controls">

</div>


<div id="editor"></div>


</div>



<div class="ai-box">


<h3>

<i class="fa-solid fa-brain"></i>

AI Response

</h3>


<pre id="result">

AI response here...

</pre>



<div class="output">


<h3>

<i class="fa-solid fa-terminal"></i>

Output

</h3>


<pre id="output"></pre>


</div>


</div>


</div>



<script src="https://unpkg.com/monaco-editor@latest/min/vs/loader.js"></script>


<script>

/* files */

let files={

"main.py":"print('hello')"

};

let currentFile="main.py";


function renderFiles(){

let html="";

for(let name in files){

html+=`

<div
class="file"
onclick="openFile('${name}')">

<i class="fa-solid fa-file"></i>

${name}

</div>

`;

}

document.getElementById("files").innerHTML=html;

}


function openFile(name){

currentFile=name;

editor.setValue(files[name]);

}


function newFile(){

let name=prompt("File name");

if(name){

files[name]="";

renderFiles();

}

}


/* editor */

require.config({

paths:{vs:'https://unpkg.com/monaco-editor@latest/min/vs'}

});


require(['vs/editor/editor.main'],function(){

window.editor=monaco.editor.create(

document.getElementById('editor'),

{

value:files[currentFile],

language:'python',

theme:'vs-dark',

automaticLayout:true

}

);

});


/* theme */

function toggleTheme(){

if(document.body.style.background.includes("f1")){

location.reload();

}
else{

document.body.style.background="#f1f5f9";

monaco.editor.setTheme("vs");

}

}


/* ai */

async function askAI(){

let res=await fetch(

'/suggest',

{

method:'POST',

headers:{

'Content-Type':'application/json',

'X-CSRF-TOKEN':

document.querySelector('meta[name="csrf-token"]').content

},

body:JSON.stringify({

code:editor.getValue(),

language:

document.getElementById("language").value

})

}

);

let data=await res.json();

document.getElementById("result").innerText=

data.result;

}


function applyFix(){

editor.setValue(

document.getElementById("result").innerText

);

}


/* run */

async function runCode(){

let res=await fetch(

'/run',

{

method:'POST',

headers:{

'Content-Type':'application/json',

'X-CSRF-TOKEN':

document.querySelector('meta[name="csrf-token"]').content

},

body:JSON.stringify({

code:editor.getValue(),

language:

document.getElementById("language").value

})

}

);

let data=await res.json();

document.getElementById("output").innerText=

data.output ?? data.error;

}


editor?.onDidChangeModelContent(()=>{

files[currentFile]=editor.getValue();

});


renderFiles();

</script>


</body>

</html>
