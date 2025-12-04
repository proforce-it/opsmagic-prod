<!DOCTYPE html>
<html>
<head>
    <title>Password generating</title>
    <style>
        html,body { padding: 0; margin:0; }
    </style>
</head>
<body>


<div style="font-family:Arial,Helvetica,sans-serif; line-height: 1.5; font-weight: normal; font-size: 15px; color: #2F3044; min-height: 100%; margin:0; padding:0; width:100%; background-color:#edf2f7">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin:0 auto; padding:0; max-width:600px">
        <tbody>
        <tr>
            <td align="center" valign="center" style="text-align:center; padding: 40px">
                <a href="javascript:;" rel="noopener" target="_blank">
                    <img alt="Logo" src="{{ asset('assets/media/logos/email-logo.png') }}" class="h-150px" style="width: 300px;"/>
                </a>
            </td>
        </tr>
        <tr>
            <td align="left" valign="center">
                <div style="text-align:left; margin: 0 20px; padding: 40px; background-color:#ffffff; border-radius: 6px">
                    <div style="padding-bottom: 30px; font-size: 17px;">
                        <strong>Dear {{ $worker_name }},</strong>
                    </div>
                    <div style="padding-bottom: 30px">You recently requested a password generating for your {{ config('app.name') }} worker account. Please click the link to generate your password</div>
                    <div style="padding-bottom: 40px; text-align:center;">
                        <a href="{{ $url }}" rel="noopener" style="text-decoration:none;display:inline-block;text-align:center;padding:0.75575rem 1.3rem;font-size:0.925rem;line-height:1.5;border-radius:0.35rem;color:#ffffff;background-color:#009EF7;border:0px;margin-right:0.75rem!important;font-weight:600!important;outline:none!important;vertical-align:middle" target="_blank">{{ $url }}</a>
                    </div>
                    <div style="padding-bottom: 30px">If you did not request a password generate you can safely ignore this email</div>

                    <div style="border-bottom: 1px solid #eeeeee; margin: 15px 0"></div>
                    <div style="padding-bottom: 10px">Best wishes,
                        <br> The {{ config('app.name') }} Team
                    </div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
