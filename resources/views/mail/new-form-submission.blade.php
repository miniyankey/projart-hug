@extends('mail.layouts.branded')

@section('content')
    @php $brand = $brandColor ?: '#8b2cf1'; @endphp

    {{-- Eyebrow + titre --}}
    <p style="margin:0 0 6px; font-size:12px; font-weight:bold; letter-spacing:1.5px; text-transform:uppercase; color:{{ $brand }};">
        {{ __('mail.form_submission.eyebrow') }}
    </p>
    <h1 style="margin:0 0 24px; font-size:24px; line-height:1.25; font-weight:bold; color:#111111;">
        {{ $isCollectRequest ? __('mail.form_submission.heading_collect') : __('mail.form_submission.heading_contact') }}
    </h1>

    <p style="margin:0 0 28px;">
        {{ __('mail.form_submission.intro') }}
    </p>

    {{-- Récapitulatif de la soumission --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 28px;">
        <tr>
            <td style="padding:16px 20px; border:2px solid #111111; border-left:6px solid {{ $brand }}; background-color:#fafafa; font-size:14px; line-height:1.7;">
                @php
                    $rows = [
                        __('mail.form_submission.field_company') => $submission->company_name,
                        __('mail.form_submission.field_city') => $submission->city,
                        __('mail.form_submission.field_name') => $submission->name,
                        __('mail.form_submission.field_email') => $submission->contact_email,
                    ];
                @endphp

                @foreach ($rows as $label => $value)
                    <span style="color:#71717a; text-transform:uppercase; font-size:11px; letter-spacing:0.5px;">{{ $label }}</span><br>
                    <strong style="color:#111111;">{{ $value }}</strong>
                    @if (! $loop->last)<br><br>@endif
                @endforeach

                @if ($submission->trophy_participation)
                    <br><br>
                    <span style="color:#71717a; text-transform:uppercase; font-size:11px; letter-spacing:0.5px;">{{ __('mail.form_submission.field_trophy') }}</span><br>
                    <strong style="color:#111111;">{{ __('mail.form_submission.trophy_yes') }}</strong>
                @endif

                @if ($isCollectRequest && ! empty($submission->preferred_dates))
                    <br><br>
                    <span style="color:#71717a; text-transform:uppercase; font-size:11px; letter-spacing:0.5px;">{{ __('mail.form_submission.field_dates') }}</span><br>
                    <strong style="color:#111111;">
                        {{ collect($submission->preferred_dates)->map(fn ($d) => \Illuminate\Support\Carbon::parse($d)->format('d.m.Y'))->implode(', ') }}
                    </strong>
                @endif

                @if (! $isCollectRequest && filled($submission->message))
                    <br><br>
                    <span style="color:#71717a; text-transform:uppercase; font-size:11px; letter-spacing:0.5px;">{{ __('mail.form_submission.field_message') }}</span><br>
                    <strong style="color:#111111; font-weight:normal;">{{ $submission->message }}</strong>
                @endif
            </td>
        </tr>
    </table>

    {{-- CTA pixel vers le dashboard admin --}}
    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 28px;">
        <tr>
            <td style="background-color:{{ $brand }}; border:2px solid #111111; box-shadow:4px 4px 0 #111111;">
                <a href="{{ $dashboardUrl }}" style="display:inline-block; padding:14px 28px; color:#ffffff; font-size:14px; font-weight:bold; letter-spacing:0.5px; text-transform:uppercase; text-decoration:none;">
                    {{ __('mail.form_submission.cta') }} &rarr;
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 6px; font-size:12px; color:#a1a1aa;">
        {{ __('mail.form_submission.reply_hint', ['email' => $submission->contact_email]) }}
    </p>
    <p style="margin:0; font-size:12px; word-break:break-all;">
        <a href="{{ $dashboardUrl }}" style="color:{{ $brand }};">{{ $dashboardUrl }}</a>
    </p>
@endsection
