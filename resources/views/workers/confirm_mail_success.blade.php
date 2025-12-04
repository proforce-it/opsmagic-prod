<!DOCTYPE html>
<html>
<head>
    <title>Confirmation mail</title>
    <style>
        html,body { padding: 0; margin:0; background-color:#edf2f7}
    </style>
</head>
<body>


<div style="font-family:Arial,Helvetica,sans-serif; line-height: 1.5; font-weight: normal; font-size: 15px; color: #2F3044; min-height: 100%; margin:0; padding:0; width:100%;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin:0 auto; padding:0; max-width:600px;">
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
                @if($message)
                    <div style="text-align:left; margin: 0 20px; padding: 40px; background-color:#ffffff; border-radius: 6px">
                        <div style="padding-bottom: 30px">{{ $message }}</div>
                    </div>
                @else
                    <div style="text-align:left; margin: 0 20px; padding: 40px; background-color:#ffffff; border-radius: 6px">
                        <div style="padding-bottom: 30px; font-size: 17px;">
                            <strong>Thank you for confirming your email address  {{ $worker->first_name }},</strong>
                        </div>
                        <div style="padding-bottom: 30px">Your registration process is now complete and your consultant can match you with suitable assignments. You can now close this page</div>
                    </div>
                @endif
            </td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
