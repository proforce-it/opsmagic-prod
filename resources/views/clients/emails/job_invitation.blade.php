<!DOCTYPE html>
<html>
<head>
    <title>{{ config('app.name') }} would like to add you to the worker pool for a job</title>
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
                    <div style="padding-bottom: 30px">{{ config('app.name') }} would like to add you to the pool of workers for the following job for {{ $invitation->client_name }}</div>
                    <div style="padding-bottom: 30px"> {{ $invitation->job_name }} at {{ $invitation->site_name }} {{ $invitation->site_address }}</div>

                    @if($invitation->what_three_words_address)
                        <div style="padding-bottom: 30px">What3Words location: {{ $invitation->what_three_words_address }}</div>
                    @endif

                    <div style="padding-bottom: 30px">Please review the attached assignment schedule and let us know if you are happy to accept the assignment under these terms.</div>
                    <div style="padding-bottom: 30px"><a style="color: #76B929" href="{{ $invitation->accept_link }}">Yes, I accept the assignment under the attached terms</a></div>
                    <div style="padding-bottom: 30px"><a style="color: #E60A7E" href="{{ $invitation->declined_link }}">No, I do not accept the assignment under the attached terms</a></div>
                    <div style="padding-bottom: 30px">Please note: Being assigned as a pool worker does not guarantee you will work any shifts for this client. If you accept this job you will receive separate notification for each and every shift when you are assigned to them.</div>

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
