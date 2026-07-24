<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quiz Results – {{ $quiz->title }}</title>
<style>
  body { background:#111; font-family: Arial, sans-serif; color:#2C2C2C; margin:0; padding:0; }
  .wrapper { background:#111; padding:40px 16px; }
  .container { max-width:600px; margin:0 auto; background:#fff; border-radius:16px; overflow:hidden; }
  .header { background:linear-gradient(135deg,#1a1a1a,#2a1500); padding:28px 32px; text-align:center; border-bottom:3px solid #FF7518; }
  .header h1 { color:#fff; font-size:22px; margin:0; }
  .body { padding:32px; }
  .greeting { font-size:20px; font-weight:700; margin:0 0 8px; }
  .intro { color:#555; font-size:15px; line-height:1.6; margin:0 0 24px; }
  .score-box { text-align:center; background:#fdfbf9; border:1px solid #eaeaea; border-radius:12px; padding:24px; margin-bottom:24px; }
  .score { font-size:42px; font-weight:800; color:#FF7518; margin:0; }
  .meta { color:#555; font-size:14px; margin-top:8px; }
  .q-card { border:1px solid #eee; border-radius:10px; padding:14px 16px; margin-bottom:12px; }
  .q-title { font-size:13px; font-weight:700; color:#FF7518; text-transform:uppercase; margin:0 0 6px; }
  .q-prompt { font-size:15px; font-weight:600; color:#1a1a1a; margin:0 0 10px; }
  .row { font-size:13px; color:#444; line-height:1.5; margin:0 0 4px; }
  .label { color:#888; font-weight:600; }
  .ok { color:#15803d; font-weight:700; }
  .bad { color:#b91c1c; font-weight:700; }
  .cta { text-align:center; margin:28px 0 8px; }
  .cta a { display:inline-block; background:linear-gradient(135deg,#FF7518,#CC5500); color:#fff !important; text-decoration:none; font-weight:700; padding:12px 28px; border-radius:50px; }
  .footer { background:#1a1a1a; color:#777; text-align:center; padding:20px; font-size:12px; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="container">
    <div class="header"><h1>Animal IQ · Quiz Results</h1></div>
    <div class="body">
      <p class="greeting">Hi {{ $recipientName }},</p>
      <p class="intro">
        Here are your results for <strong>{{ $quiz->title }}</strong>
        @if($attempt->status === 'timed_out') (time ran out)@endif.
      </p>

      <div class="score-box">
        <p class="score">{{ number_format((float) $attempt->percentage, 0) }}%</p>
        <p class="meta">{{ $attempt->score }} / {{ $attempt->max_score }} points
          · {{ (float) $attempt->percentage >= (float) $quiz->pass_percentage ? 'Passed' : 'Below pass mark' }}
          ({{ $quiz->pass_percentage }}%+)
        </p>
      </div>

      @foreach($reviewRows as $row)
        <div class="q-card">
          <p class="q-title">Q{{ $row['number'] }} · <span class="{{ $row['status'] === 'Correct' ? 'ok' : 'bad' }}">{{ $row['status'] }}</span> · +{{ $row['points'] }} pts</p>
          <p class="q-prompt">{{ $row['prompt'] }}</p>
          <p class="row"><span class="label">Your answer:</span> {{ $row['your_answer'] }}</p>
          <p class="row"><span class="label">Correct answer:</span> {{ $row['correct_answer'] }}</p>
        </div>
      @endforeach

      <div class="cta">
        <a href="{{ route('quizzes.result', [$quiz, $attempt]) }}">View full results online</a>
      </div>
    </div>
    <div class="footer">&copy; {{ date('Y') }} Animal IQ</div>
  </div>
</div>
</body>
</html>
