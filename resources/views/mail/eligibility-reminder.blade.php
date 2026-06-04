@extends('mail.layouts.branded')

@section('content')
    @php $brand = $brandColor ?: '#8b2cf1'; @endphp

    {{-- Eyebrow + titre --}}
    <p style="margin:0 0 6px; font-size:12px; font-weight:bold; letter-spacing:1.5px; text-transform:uppercase; color:{{ $brand }};">
        {{ __('mail.reminder.eyebrow') }}
    </p>
    <h1 style="margin:0 0 24px; font-size:24px; line-height:1.25; font-weight:bold; color:#111111;">
        {{ __('mail.reminder.heading') }}
    </h1>

    <p style="margin:0 0 16px;">{{ __('mail.reminder.greeting') }}</p>

    <p style="margin:0 0 28px;">
        {{ __('mail.reminder.body', ['date' => $eligibleDate]) }}
    </p>

    <p style="margin:0 0 24px;">{{ __('mail.reminder.cta_intro') }}</p>

    {{-- CTA pixel : toujours vers la page officielle HUG (lien stable, non co-brandé) --}}
    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 28px;">
        <tr>
            <td style="background-color:{{ $brand }}; border:2px solid #111111; box-shadow:4px 4px 0 #111111;">
                <a href="{{ $donateUrl }}" style="display:inline-block; padding:14px 28px; color:#ffffff; font-size:14px; font-weight:bold; letter-spacing:0.5px; text-transform:uppercase; text-decoration:none;">
                    {{ __('mail.reminder.cta') }} &rarr;
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0; font-size:13px; color:#71717a;">{{ __('mail.reminder.outro') }}</p>
@endsection
