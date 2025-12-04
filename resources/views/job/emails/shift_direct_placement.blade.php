<!DOCTYPE html>
<html>
<head>
    <title>{{ config('app.name') }} have added you to a new shift</title>
    <style>
        html,body { padding: 0; margin:0;  height: 100%}
    </style>
</head>
<body>


<div style="font-family:Arial,Helvetica,sans-serif; line-height: 1.5; font-weight: normal; font-size: 15px; color: #2F3044; min-height: 100%; margin:0; padding:0; width:100%; background-color:#edf2f7">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin:0 auto; padding:0; max-width:600px;">
        <tbody>
        <tr>
            <td align="center" valign="center" style="text-align:center; padding: 40px">
                <a href="javascript:;" rel="noopener" target="_blank">
                    <img alt="Logo" src="{{ asset('public/assets/media/logos/email-logo.png') }}" class="h-150px" style="width: 300px;"/>
                </a>
            </td>
        </tr>
        <tr>
            <td align="left" valign="center">
                <div style="text-align:left; margin: 0 20px; padding: 40px; background-color:#ffffff; border-radius: 6px">
                    <div style="padding-bottom: 30px; font-size: 17px;">
                        <strong>Dear {{ $invitation->worker_name }},</strong>
                    </div>
                    <div style="padding-bottom: 30px">{{ config('app.name') }} have added you to a shift on the following job {{ $invitation->job_name }} for {{ $invitation->client_name }}</div>
                    <div style="padding-bottom: 30px">At {{ $invitation->start_time }} on {{ $invitation->shift_date }} (expected duration {{ $invitation->default_shift_duration }}) </div>
                    <div style="padding-bottom: 30px">This shift is at {{ $invitation->site_name }} {{ $invitation->site_address }}</div>

                    @if($invitation->what_three_words_address)
                        <div style="padding-bottom: 30px">What3Words location: {{ $invitation->what_three_words_address }}</div>
                    @endif

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
