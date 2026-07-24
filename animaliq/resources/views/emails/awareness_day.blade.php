@extends('emails.layout')

@section('email-content')
    @php $day = $day ?? null; $firstName = $firstName ?? 'there'; @endphp
    <div style="text-align:center;margin-bottom:24px;">
        @if($day && $day->image)
        <img src="{{ asset('storage/' . $day->image) }}" alt="{{ $day->title }}" style="width:100%;max-height:240px;object-fit:cover;border-radius:12px;margin-bottom:16px;">
        @endif
        <p style="font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:2px;color:#f97316;margin-bottom:8px;">Animal Awareness Day</p>
        <h1 style="font-size:26px;font-weight:800;color:#111;margin:0 0 8px;">🌍 {{ $day->title ?? '' }}</h1>
        <p style="font-size:15px;color:#555;margin:0;">{{ $day?->celebration_date?->format('F j') }}</p>
    </div>

    <p style="font-size:16px;color:#333;margin-bottom:12px;">Hi {{ $firstName }},</p>
    <p style="font-size:15px;color:#555;line-height:1.7;margin-bottom:20px;">Today we celebrate <strong style="color:#111">{{ $day->title ?? '' }}</strong>!</p>

    @if($day && $day->body)
    <div style="background:#fff8f3;border-left:4px solid #f97316;border-radius:0 8px 8px 0;padding:16px 20px;margin-bottom:24px;">
        <p style="font-size:15px;color:#444;line-height:1.7;margin:0;">{{ $day->body }}</p>
    </div>
    @endif

    <div style="text-align:center;margin-top:24px;">
        <a href="{{ route('awareness-days.index') }}" style="display:inline-block;background:#f97316;color:#fff;font-weight:700;font-size:15px;padding:12px 28px;border-radius:8px;text-decoration:none;">View Our Awareness Days Calendar</a>
    </div>
@endsection
