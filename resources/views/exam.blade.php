@extends('layouts.base')

@section('title')
	{{ $exam->name }}
@endsection

@section('style')
.exams-container {
  	width: 60%;
  	background-color: #ffffff;
  	box-shadow: 0 0 9px 0 rgba(0, 0, 0, 0.3);
  	margin: 100px auto;
}
.exams-container h1 {
  	text-align: center;
  	color: #5b6574;
  	font-size: 24px;
  	padding: 20px 0 20px 0;
  	border-bottom: 1px solid #dee0e4;
}
.exams-container .exams {
	padding: 20px;
}
.exams-container .exams .exam {
	background-color: beige;
	padding: 20px;
}
.exams-container .pagination {
	padding: 20px;
}
.exams-container .pagination nav {
	width: 100%;
}
nav svg {
	max-width: 20px;
}

.badge-primary {
	background-color: #5cb85c;
}
@endsection

@section('content')
<div class="exams-container">
    <h1>
		{{ $exam->name }}
		@if ($registered)
		<span class="badge badge-primary">Registered</span>
		@endif
	</h1>
	
	<div class="exams">
		<h2>{{ $exam->description }}</h2>
		<p>Time: {{ $exam->start_time }} ~ {{ $exam->end_time }}</p>
		@if ($message)
		<p>{{ $message }}</p>
		@else
			@if ($registered)
			<a class="btn btn-primary" href="{{ $examURL }}">Enroll</a>
			@else
			<form action="{{ route('exams.register', ['examID' => $exam->id]) }}" method="POST">
				@csrf 
				<button type="submit" claas="btn btn-primary">Register</button>
			</form>
			@endif
		@endif
	</div>
</div>
@endsection