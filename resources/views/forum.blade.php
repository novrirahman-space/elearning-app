<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forum {{ $course->name }}</title>
    @vite('resources/js/app.js')
    <style>
        body { font-family: system-ui; background: #f8fafc; padding: 2rem; }
        ul { list-style: none; padding: 0; }
        li { background: #fff; padding: 0.5rem 1rem; margin: 0.25rem 0; border-radius: 6px; }
        .reply { margin-left: 1rem; background: #f0f0f0; }
    </style>
</head>
<body data-course-id="{{ $course->id }}">
    <h1>Forum: {{ $course->name }}</h1>
    <ul id="discussion-list">
        @foreach ($discussions as $discussion)
            <li><strong>{{ $discussion->user->name }}</strong>: {{ $discussion->content }}</li>
            @foreach ($discussion->replies as $reply)
                <li class="reply">↳ <strong>{{ $reply->user->name }}</strong>: {{ $reply->content }}</li>
            @endforeach
        @endforeach
    </ul>
</body>
</html>
