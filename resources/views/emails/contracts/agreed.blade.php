@php
    $isRtl = app()->getLocale() === 'ar';
    $dir = $isRtl ? 'rtl' : 'ltr';
    $align = $isRtl ? 'right' : 'left';
    $logoSrc = null;
    if (! empty($logoPath) && is_file($logoPath)) {
        $logoSrc = isset($message) ? $message->embed($logoPath) : asset('phoenix/assets/logo/softora.jpg');
    }
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Your agreed copy of :title', ['title' => $contract->title]) }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6fb;font-family:Tahoma,Arial,'Segoe UI',sans-serif;color:#1b1d21;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6fb;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 8px 24px rgba(27,45,94,0.08);">
                    <tr>
                        <td style="background-color:#3874ff;padding:28px 32px;text-align:center;">
                            @if($logoSrc)
                                <img src="{{ $logoSrc }}" alt="{{ $appName }}" width="140" style="display:block;margin:0 auto 12px auto;max-width:140px;height:auto;border:0;">
                            @endif
                            <p style="margin:0;color:#dbe7ff;font-size:12px;letter-spacing:1px;text-transform:uppercase;">{{ $appName }}</p>
                            <h1 style="margin:8px 0 0 0;color:#ffffff;font-size:22px;line-height:1.4;">{{ __('Contract agreement confirmed') }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;text-align:{{ $align }};">
                            <p style="margin:0 0 16px 0;font-size:16px;line-height:1.7;">
                                {{ __('Hello :name,', ['name' => $user->full_name]) }}
                            </p>
                            <p style="margin:0 0 24px 0;font-size:15px;line-height:1.8;color:#3d4450;">
                                {{ __('Thank you for agreeing to this contract. A full copy is included below for your records.') }}
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f8ff;border:1px solid #d6e4ff;border-radius:12px;margin-bottom:28px;">
                                <tr>
                                    <td style="padding:20px 22px;text-align:{{ $align }};">
                                        <p style="margin:0 0 6px 0;color:#3874ff;font-size:12px;font-weight:bold;letter-spacing:0.4px;text-transform:uppercase;">{{ __('Contract Title') }}</p>
                                        <p style="margin:0 0 16px 0;font-size:18px;font-weight:bold;color:#102a43;">{{ $contract->title }}</p>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:0 0 8px 0;font-size:13px;color:#52606d;text-align:{{ $align }};">
                                                    <strong>{{ __('Agreement reference') }}:</strong>
                                                    #{{ $agreement->id }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:0 0 8px 0;font-size:13px;color:#52606d;text-align:{{ $align }};">
                                                    <strong>{{ __('Agreement date') }}:</strong>
                                                    {{ $agreedAt->format('Y-m-d H:i') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:0;font-size:13px;color:#52606d;text-align:{{ $align }};">
                                                    <strong>{{ __('Name') }}:</strong>
                                                    {{ $user->full_name }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 16px 0;font-size:14px;font-weight:bold;color:#102a43;">{{ __('Contract Details') }}</p>

                            @forelse($pages as $index => $pageContent)
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:16px;border:1px solid #e4e7ec;border-radius:10px;">
                                    <tr>
                                        <td style="padding:10px 16px;background-color:#f8fafc;border-bottom:1px solid #e4e7ec;font-size:12px;font-weight:bold;color:#3874ff;text-align:{{ $align }};">
                                            {{ __('Page :number', ['number' => $index + 1]) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:18px 16px;font-size:14px;line-height:1.85;color:#1b1d21;white-space:pre-wrap;text-align:{{ $align }};">{{ $pageContent }}</td>
                                    </tr>
                                </table>
                            @empty
                                <p style="margin:0;font-size:14px;color:#52606d;">{{ __('Empty') }}</p>
                            @endforelse

                            <p style="margin:24px 0 0 0;font-size:13px;line-height:1.7;color:#52606d;">
                                {{ __('This is an official copy of the agreement recorded in your account.') }}
                                {{ __('Please keep this email for your records.') }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 32px 28px 32px;border-top:1px solid #eef0f4;text-align:center;color:#8b95a1;font-size:12px;line-height:1.6;">
                            {{ __('This email is a copy of the contract you agreed to.') }}<br>
                            &copy; {{ now()->year }} {{ $appName }}. {{ __('All rights reserved.') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
