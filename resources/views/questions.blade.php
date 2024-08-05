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
.exams-container .exams .questions {
	padding: 20px;
}
.exams-container .exams .questions .remain-time {
    color: red;
    margin-bottom: 20px;
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
    <h1 id="exam-name" data-exam-id="{{ $exam->id }}">
		{{ $exam->name }}
	</h1>
	
	<div class="exams">
        <div class="questions">
		@if ($canEnroll)
            <div class="remain-time">Remain time:<b id="countdown"></b></div>
            <form id="exam-form">
                @foreach($examQuestions as $question)
                    <h3>
                        {{ $question['question'] }}
                    </h3>

                    @foreach($question['choices'] as $choice)
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" 
                                name="question-{{ $question['question_id'] }}" 
                                id="question_{{ $question['question_id'] }}_option_{{ $choice['choice_id'] }}"
                                value="{{ $choice['choice_id'] }}">
                            <label class="form-check-label" for="question_{{ $question['question_id'] }}_option_{{ $choice['choice_id'] }}">
                                {{ $choice['choice_name'] }}
                            </label>
                        </div>
                    @endforeach
                @endforeach
                <button type="button" class="btn btn-primary" id="submit-exam">Submit</button>
            </form>
        @else
		<p>Time: {{ $exam->start_time }} ~ {{ $exam->end_time }}</p>
        <p>Exam is not available</p>
		@endif
        </div>
	</div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        var remainTime = {{ $remainTime }}; // Thời gian còn lại tính bằng giây

        function updateCountdown() {
            var hours = Math.floor(remainTime / 3600);
            var minutes = Math.floor((remainTime % 3600) / 60);
            var seconds = remainTime % 60;

            // Update UI
            $('#countdown').text(
                (hours < 10 ? '0' : '') + hours + ":" +
                (minutes < 10 ? '0' : '') + minutes + ":" +
                (seconds < 10 ? '0' : '') + seconds
            );

            // Update time every second
            if (remainTime > 0) {
                remainTime--;
            } else {
                clearInterval(timerInterval);
                $('#countdown').text("Time's up!");
                
                // submit automatically
                // Get form data
                var submission = getFormData('exam-form');
                var examID = $('#exam-name').data('exam-id');

                // store submission to redis
                $.ajax({
                    url: `/exam/${examID}/submit`,
                    type: 'POST',
                    data: {
                        examID: examID,
                        submission: submission
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        window.location.href = `/exam/${examID}`;
                    },
                    error: function(xhr) {
                        console.error(xhr);
                    }
                });
            }
        }

        var timerInterval = setInterval(updateCountdown, 1000);

        $('#submit-exam').click(function() {
            // Get form data
            var submission = getFormData('exam-form');
            var examID = $('#exam-name').data('exam-id');

            // store submission to redis
            $.ajax({
                url: `/exam/${examID}/submit`,
                type: 'POST',
                data: {
                    examID: examID,
                    submission: submission
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    window.location.href = `/exam/${examID}`;
                },
                error: function(xhr) {
                    console.error(xhr);
                }
            });
        });

        $('.form-check-input').change(function() {
            // Get form data
            var submission = getFormData('exam-form');
            var examID = $('#exam-name').data('exam-id');
            
            // store submission to redis
            $.ajax({
                url: `/exam/${examID}/store-submission`,
                type: 'POST',
                data: {
                    examID: examID,
                    submission: submission
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log(response);
                },
                error: function(xhr) {
                    console.error(xhr);
                }
            });
        });
        
        function getFormData(formID) {
            // Get form data
            var form = document.getElementById('exam-form');
            var formData = new FormData(form);

            // convert data
            var dataObject = {};
            formData.forEach((value, key) => {
                if (dataObject[key]) {
                    dataObject[key] = [].concat(dataObject[key], value);
                } else {
                    dataObject[key] = value;
                }
            });
            var submission = JSON.stringify(dataObject);
            return submission;
        }
    });
</script>
@endsection