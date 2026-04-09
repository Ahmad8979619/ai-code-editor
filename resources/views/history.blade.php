@extends('layouts.app')

@section('title','History')

@section('content')

<h2>

<i class="fa-solid fa-clock-rotate-left"></i>

Code History

</h2>


@if($codes->count()==0)

<div class="card" style="text-align:center;opacity:0.7">

<h2>No saved code yet</h2>

<p>Start using AI editor to generate code</p>

</div>

@endif



@foreach($codes as $code)

<div class="card">

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">

<div class="badge">

{{ strtoupper($code->language) }}

</div>


<small style="opacity:0.6">

{{ $code->created_at->diffForHumans() }}

</small>

</div>


<pre>

{{ $code->code }}

</pre>


<div style="display:flex;gap:10px;margin-top:12px">


<button
class="btn copy-btn"
onclick="copyCode(this,`{{ $code->code }}`)">

<i class="fa-solid fa-copy"></i>

Copy

</button>



<form
method="POST"
action="/delete-code/{{ $code->id }}">

@csrf

@method('DELETE')

<button
class="btn delete-btn">

<i class="fa-solid fa-trash"></i>

Delete

</button>

</form>


</div>


</div>

@endforeach



<style>

/* language badge */

.badge{

padding:5px 12px;

border-radius:20px;

font-size:12px;

font-weight:600;

background:

linear-gradient(135deg,#22c55e,#16a34a);

box-shadow:

0 0 12px rgba(34,197,94,0.35);

}


/* code block */

pre{

background:#020617;

padding:18px;

border-radius:12px;

overflow:auto;

font-size:13px;

border:1px solid var(--border);

box-shadow:

0 0 25px rgba(0,0,0,0.6) inset;

}


/* buttons */

.copy-btn{

background:

linear-gradient(135deg,#3b82f6,#2563eb);

}

.copy-btn:hover{

box-shadow:

0 0 25px rgba(59,130,246,0.5);

}


.delete-btn{

background:

linear-gradient(135deg,#ef4444,#dc2626);

}

.delete-btn:hover{

box-shadow:

0 0 25px rgba(239,68,68,0.5);

}


/* copy feedback animation */

.copy-success{

background:

linear-gradient(135deg,#22c55e,#16a34a) !important;

box-shadow:

0 0 25px rgba(34,197,94,0.5);

}

</style>



<script>

function copyCode(btn,code){

navigator.clipboard.writeText(code);


btn.classList.add("copy-success");

btn.innerHTML="✓ Copied";


setTimeout(()=>{

btn.classList.remove("copy-success");

btn.innerHTML='<i class="fa-solid fa-copy"></i> Copy';

},1500);

}

</script>


@endsection
