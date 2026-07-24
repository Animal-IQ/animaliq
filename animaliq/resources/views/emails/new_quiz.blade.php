@php
    $badgeLabel    = 'New Quiz';
    $introText     = 'A new wildlife quiz is live on Animal IQ. Log in, take the challenge, and earn points on the leaderboard!';
    $contentType   = 'Quiz';
    $contentTitle  = $quiz->title;
    $metaItems     = array_values(array_filter([
        'Difficulty: ' . ucfirst($quiz->difficulty),
        $quiz->duration_minutes ? 'Time limit: ' . $quiz->duration_minutes . ' minutes' : 'No time limit',
        $quiz->available_until ? 'Available until: ' . $quiz->available_until->format('M j, Y g:i A') : null,
        'Login required to play',
    ]));
    $excerpt       = $quiz->description ? \Illuminate\Support\Str::limit(strip_tags($quiz->description), 200) : null;
    $ctaUrl        = route('quizzes.show', $quiz);
    $ctaLabel      = 'Take the Quiz';
@endphp
@include('emails.layout')
