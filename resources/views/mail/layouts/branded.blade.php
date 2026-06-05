@php
    // Couleur de marque pilotée par l'entreprise (co-branding), fallback sur le token violet.
    $brand = $brandColor ?: '#8b2cf1';
    // Nom affiché dans le bandeau : on évite le "Laravel" par défaut de APP_NAME.
    $platformName = config('mail.from.name') ?: 'Mission Donneur';

    // Logos embarqués en CID : ils s'affichent dans tous les clients, sans dépendre
    // d'une URL publique (utile en dev où APP_URL = localhost).
    $hugLogoPath = resource_path('images/logos/hug.png');
    $hugLogoCid = (isset($message) && is_file($hugLogoPath)) ? $message->embed($hugLogoPath) : null;
    $companyLogoCid = (isset($message) && ! empty($companyLogoPath) && is_file($companyLogoPath))
        ? $message->embed($companyLogoPath)
        : null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>{{ $title ?? $platformName }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f5; font-family:Arial, Helvetica, sans-serif; color:#18181b; -webkit-font-smoothing:antialiased;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f5;">
        <tr>
            <td align="center" style="padding:40px 16px;">
                {{-- Carte principale : bordure nette + ombre pixel décalée --}}
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px; max-width:100%; background-color:#ffffff; border:2px solid #111111; box-shadow:8px 8px 0 #111111;">
                    {{-- Liseré de marque (accent pixel) --}}
                    <tr>
                        <td style="height:8px; background-color:{{ $brand }}; line-height:8px; font-size:0;">&nbsp;</td>
                    </tr>

                    {{-- En-tête co-brandé : HUG × entreprise --}}
                    <tr>
                        <td style="padding:24px 28px; border-bottom:2px solid #111111;">
                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    @if ($hugLogoCid)
                                        <td valign="middle">
                                            <img src="{{ $hugLogoCid }}" alt="HUG" height="34" style="height:34px; display:block;">
                                        </td>
                                    @else
                                        <td valign="middle" style="font-size:16px; font-weight:bold; color:#111111;">HUG</td>
                                    @endif

                                    @if ($companyLogoCid || ! empty($companyName))
                                        <td valign="middle" style="padding:0 14px; color:#a1a1aa; font-size:18px;">&times;</td>
                                        <td valign="middle">
                                            @if ($companyLogoCid)
                                                <img src="{{ $companyLogoCid }}" alt="{{ $companyName ?? '' }}" height="30" style="height:30px; display:block;">
                                            @else
                                                <span style="font-size:15px; font-weight:bold; color:#111111;">{{ $companyName }}</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Corps --}}
                    <tr>
                        <td style="padding:36px 28px; font-size:15px; line-height:1.65; color:#27272a;">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Pied --}}
                    <tr>
                        <td style="padding:22px 28px; border-top:2px solid #111111; background-color:#fafafa; font-size:12px; line-height:1.6; color:#71717a;">
                            <span style="display:inline-block; font-weight:bold; color:#111111; letter-spacing:0.5px;">{{ __('mail.footer.signature') }}</span><br>
                            {{ __('mail.footer.disclaimer') }}
                        </td>
                    </tr>
                </table>

                {{-- Mention bas de page hors carte --}}
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px; max-width:100%;">
                    <tr>
                        <td style="padding:16px 8px 0; font-size:11px; line-height:1.5; color:#a1a1aa; text-align:center;">
                            {{ $platformName }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
