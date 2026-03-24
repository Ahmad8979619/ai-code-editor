<!DOCTYPE html>
<html>

<head>

<title>AI Code Editor</title>

<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

body{
font-family:Arial;
margin:30px;
background:#0f172a;
color:white;
transition:0.3s;
}

body.light{
background:#f1f5f9;
color:black;
}

.top-bar{
display:flex;
gap:10px;
margin-bottom:20px;
flex-wrap:wrap;
}

.container{
display:flex;
gap:20px;
}

.sidebar{
width:200px;
background:#020617;
padding:15px;
border-radius:10px;
}

body.light .sidebar{
background:white;
border:1px solid #ddd;
}

.editor-box{
width:55%;
}

.ai-box{
width:30%;
background:#020617;
padding:15px;
border-radius:10px;
}

body.light .ai-box{
background:white;
border:1px solid #ddd;
}

#editor{
height:400px;
border-radius:10px;
border:1px solid #1e293b;
}

button{
padding:8px 12px;
background:#22c55e;
border:none;
color:white;
cursor:pointer;
border-radius:6px;
display:flex;
align-items:center;
gap:5px;
}

.toggle{
background:#3b82f6;
}

.output{
margin-top:20px;
padding:10px;
background:#020617;
border-radius:10px;
}

.error{
color:red;
}

.success{
color:#22c55e;
}

</style>

</head>


<body>

<h2>

<i class="fa-solid fa-code"></i>

AI Code Editor

</h2>


<div class="top-bar">

<select id="language">

<option value="python">Python</option>

<option value="javascript">JavaScript</option>

<option value="php">PHP</option>

<option value="java">Java</option>

</select>


<button onclick="askAI()">

<i class="fa-solid fa-robot"></i>

Suggest

</button>


<button onclick="applyFix()">

<i class="fa-solid fa-wrench"></i>

Apply

</button>


<button onclick="runCode()">

<i class="fa-solid fa-play"></i>

Run

</button>


<button class="toggle"
onclick="toggleTheme()">

<i class="fa-solid fa-moon"></i>

Theme

</button>


<a href="/history">

<button>

<i class="fa-solid fa-clock"></i>

History

</button>

</a>

</div>


<div class="container">


<div class="sidebar">

<h3>

<i class="fa-solid fa-folder"></i>

Files

</h3>

<div id="files"></div>

<button onclick="newFile()">

<i class="fa-solid fa-plus"></i>

New File

</button>

</div>



<div class="editor-box">

<div id="editor"></div>

</div>



<div class="ai-box">

<pre id="result"></pre>

<div class="output">

<pre id="output"></pre>

</div>

</div>


</div>


<script src="https://unpkg.com/monaco-editor@latest/min/vs/loader.js"></script>

<script>

let files={

"main.py":"print('hello')"

};

let currentFile="main.py";

function renderFiles(){

let html="";

for(let name in files){

html+=`<div onclick="openFile('${name}')">

<i class="fa-solid fa-file"></i>

${name}

</div>`;

}

document.getElementById("files").innerHTML=html;

}


function openFile(name){

currentFile=name;

editor.setValue(files[name]);

}


function newFile(){

let name=prompt("file name");

if(name){

files[name]="";

renderFiles();

}

}


require.config({

paths:{vs:'https://unpkg.com/monaco-editor@latest/min/vs'}

});


require(['vs/editor/editor.main'],function(){

window.editor=monaco.editor.create(

document.getElementById('editor'),

{

value:files[currentFile],

language:'python',

theme:'vs-dark'

}

);

});


function toggleTheme(){

document.body.classList.toggle("light");

if(document.body.classList.contains("light")){

monaco.editor.setTheme("vs");

}else{

monaco.editor.setTheme("vs-dark");

}

}


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
