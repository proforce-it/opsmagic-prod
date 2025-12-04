<!DOCTYPE html>
<html>
<head>
    <title>Confirmation mail</title>
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
                    <!--begin:Email content-->
                    <div style="padding-bottom: 30px; font-size: 17px;">
                        <strong>Dear {{ $confirmData->first_name }},</strong>
                    </div>
                    <div style="padding-bottom: 30px">You recently registered with {{ config('app.name') }}. To complete the process we need  to confirm your email address.
                        <br> Please click the link below to confirm.</div>
                    <div style="padding-bottom: 40px; text-align:center;">
                        <a href="{{ $confirmData->confirm_link }}" rel="noopener" style="text-decoration:none;display:inline-block;text-align:center;padding:0.75575rem 1.3rem;font-size:0.925rem;line-height:1.5;border-radius:0.35rem;color:#ffffff;background-color:#009EF7;border:0px;margin-right:0.75rem!important;font-weight:600!important;outline:none!important;vertical-align:middle" target="_blank">Confirm my email address</a>
                    </div>
                    <div style="border-bottom: 1px solid #eeeeee; margin: 15px 0"></div>
                    <div style="padding-bottom: 50px; word-wrap: break-all;">
                        <p style="margin-bottom: 10px;">If you cannot see the link above, you can also copy the following URL and paste it in a web browser:</p>
                        <a href="{{ $confirmData->confirm_link }}" rel="noopener" target="_blank" style="text-decoration:none;color: #009EF7">{{ $confirmData->confirm_link }}</a>
                    </div>
                    <!--end:Email content-->
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
