<html>
    <body>
        <div style="background-color: #ececec; padding: 20px; font-family:Arial, Helvetica, sans-serif;">
            <div style="background-color: white; border-radius: 5px; padding: 30px;">
                <h1 style="font-weight: bold; font-size: 20px; margin-bottom: 30px!important;">OVCRD Ticketing System</h1>
                <span style="font-size: 14px;">
                    <p>You have a new notification! Read the preview below.</p>
                    <p style="color: #00563F; margin-top: 30px!important; margin-bottom: 30px!important;">{{ $data['notification'] }}</p>
                    <p>Login now and check your other notifications.</p>
                    <a href="{{ $data['link'] }}" target="_blank" style="color: #73102C;">{{ $data['link'] }}</a>
                </span>
                <p style="font-size: 12px; color: #acacac; margin-top: 45px!important;">This message was generated automatically. Please do not reply to this email.</p>
            </div>
        </div>
    </body>
</html>