@extends('layouts.base')

@section('title')
	Login
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
@endsection

@section('content')
<div class="exams-container">
    <h1>{{ $exam->name }}</h1>
	
	<div class="exams">
		<div class="exam">
			<h2>{{ $exam->description }}</h2>
		</div>
	</div>
</div>
@endsection