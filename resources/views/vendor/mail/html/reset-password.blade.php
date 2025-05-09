<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
    {{ $head ?? '' }}
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6;">
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    {!! $header ?? '' !!}
                    <!-- Email Body -->
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0" style="border: hidden !important;">
                            <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                <!-- Body content -->
                                <tr>
                                    <td class="content-cell" style="padding: 20px; background-color: white; border-radius: 8px;">
                                        <h3 style="text-align: center; font-size: 15px; font-weight: bold;">Kode Verifikasi</h3>
                                        <p style="text-align: center; font-size: 24px; font-weight: bold; color: #333;">
                                            {{ $resetCode }}
                                        </p>
                                        <p style="text-align: center; font-size: 16px; color: #555;">
                                            If you did not request a password reset, no further action is required.
                                        </p>
                                        <br/>
                                        <p style="font-size: 16px; color: #333;">Regards,<br>{{ config('app.name') }}</p>
                                        {{ $subcopy ?? '' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {!! $footer ?? '' !!}
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
