@extends('mail.layouts.branded')

@section('content')
    @php $brand = $brandColor ?: '#8b2cf1'; @endphp

    {{-- Eyebrow + titre --}}
    <p style="margin:0 0 6px; font-size:12px; font-weight:bold; letter-spacing:1.5px; text-transform:uppercase; color:{{ $brand }};">
        {{ __('mail.collect.eyebrow') }}
    </p>
    <h1 style="margin:0 0 24px; font-size:24px; line-height:1.25; font-weight:bold; color:#111111;">
        {{ __('mail.collect.heading') }}
    </h1>

    <p style="margin:0 0 16px;">
        {{ $contactName ? __('mail.collect.greeting_name', ['name' => $contactName]) : __('mail.collect.greeting') }}
    </p>

    <p style="margin:0 0 28px;">
        {{ __('mail.collect.body', ['company' => $companyName]) }}
    </p>

    {{-- Récapitulatif de la collecte --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 28px;">
        <tr>
            <td style="padding:16px 20px; border:2px solid #111111; border-left:6px solid {{ $brand }}; background-color:#fafafa; font-size:14px; line-height:1.7;">
                <span style="color:#71717a; text-transform:uppercase; font-size:11px; letter-spacing:0.5px;">{{ __('mail.collect.recap_date') }}</span><br>
                <strong style="color:#111111;">{{ $dateLine }}</strong>
                @if ($placeLine)
                    <br><br>
                    <span style="color:#71717a; text-transform:uppercase; font-size:11px; letter-spacing:0.5px;">{{ __('mail.collect.recap_place') }}</span><br>
                    <strong style="color:#111111;">{{ $placeLine }}</strong>
                @endif
            </td>
        </tr>
    </table>

    {{-- CTA pixel : bordure noire + ombre décalée --}}
    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 28px;">
        <tr>
            <td style="background-color:{{ $brand }}; border:2px solid #111111; box-shadow:4px 4px 0 #111111;">
                <a href="{{ $cobrandUrl }}" style="display:inline-block; padding:14px 28px; color:#ffffff; font-size:14px; font-weight:bold; letter-spacing:0.5px; text-transform:uppercase; text-decoration:none;">
                    {{ __('mail.collect.cta') }} &rarr;
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 6px; font-size:12px; color:#a1a1aa;">
        {{ __('mail.collect.link_fallback') }}
    </p>
    <p style="margin:0; font-size:12px; word-break:break-all;">
        <a href="{{ $cobrandUrl }}" style="color:{{ $brand }};">{{ $cobrandUrl }}</a>
    </p>
@endsection
