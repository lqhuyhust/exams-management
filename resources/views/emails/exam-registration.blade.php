<!DOCTYPE html>
<html>

<head>
    <title>Register Successfully</title>
</head>

<body>
    <p>Congratulations on successfully registering for the {{ $examName }} exam! Here are the specific details:</p>
    <ul>
        <li><b>Exam time:</b> {{ $examStartTime }} ~ {{ $examEndTime }} </li>
        <li><b>Exam URL:</b> <a href="{{ $examURL }}">Click here!</a></li>
    </ul>
    <p>Click to exam URL to verify your registration</p>
    <p>Please remember to pay attention to the exam time.</p>
</body>
</html>