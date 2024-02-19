<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OVCRD Ticketing Report</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif;">
    <table class="w-full">
        <tr>
            <td class="w-half">
                <img src="https://up.edu.ph/wp-content/uploads/2020/01/UP-Seal.png" alt="report logo" width="60" />
            </td>
            <td class="w-half">
                <h4 style="margin-bottom: 0!important;">OVCRD Ticketing System</h4>
                <h2 style="margin-top: 0!important; color: #8D1436;">{{ $report->title }}</h2>
            </td>
        </tr>
    </table>
 
    <table style="width: 100%;">
        <tr>
            <th height="10" style="text-align: left !important; width: 100%;">
                <!-- Section -->
                <table style="width: 100%;">
                    <tr>
                        <td width="50">
                            <h4 style="display: inline-block; margin: 0 !important;">Section:</h4>
                        </td>
                        <td width="50">
                            <span style="font-weight: normal!important;">{{ $report->officeCode }}</span>
                        </td>
                    </tr>
                </table>
                <!-- From -->
                <table style="width: 100%;">
                    <tr>
                        <td width="50">
                            <h4 style="display: inline-block; margin: 0 !important;">Type:</h4>
                        </td>
                        <td width="50">
                            <span style="font-weight: normal!important;">{{ $report->processName }}</span>
                        </td>
                    </tr>
                </table>
            </th>
            <th height="10" style="text-align: left !important; width: 100%;">
                <!-- Section -->
                <table style="width: 100%;">
                    <tr>
                        <td width="50">
                            <h4 style="display: inline-block; margin: 0 !important;">From:</h4>
                        </td>
                        <td width="50">
                            <span style="font-weight: normal!important; text-align: right!important;">2024-01-31</span>
                        </td>
                    </tr>
                </table>
                <!-- From -->
                <table style="width: 100%;">
                    <tr>
                        <td width="50">
                            <h4 style="display: inline-block; margin: 0 !important;">To:</h4>
                        </td>
                        <td width="50">
                            <span style="font-weight: normal!important; text-align: right!important;">2024-01-31</span>
                        </td>
                    </tr>
                </table>
            </th>
        </tr>
    </table>
 
    <table style="width: 100%; margin-top: 25px;">
        <tr style="background-color: #646464; color: #ffffff;">
            <th width="2" style="padding: 5px; font-size: 13px; text-align: center !important;">#</th>
            <th width="3" style="padding: 5px; font-size: 13px; text-align: left !important;">Ticket #</th>
            <th width="65" style="padding: 5px; font-size: 13px; text-align: left !important;">Title</th>
            <th width="2" style="padding: 5px; font-size: 13px; text-align: left !important;">Status</th>
            <th width="28" style="padding: 5px; font-size: 13px; text-align: left !important;">Date</th>
        </tr>
        @if (count($items))
            @foreach($items as $key => $item)
                @if ($key % 2 == 0)
                    <tr style="background-color: #f3f3f3;">
                @else
                    <tr style="background-color: #dfdfdf;">
                @endif
                    <td width="2" style="padding: 5px; font-size: 11px; text-align: center;">
                        {{ ($key + 1) }}
                    </td>
                    <td width="3" style="padding: 5px; font-size: 11px;">
                        {{ $item['control_number'] }}
                    </td>
                    <td width="65" style="padding: 5px; font-size: 11px;">
                        {{ $item['title'] }}
                    </td>
                    <td width="2" style="padding: 5px; font-size: 11px;">
                        {{ $item['statusLabel'] }}
                    </td>
                    <td width="28" style="padding: 5px; font-size: 11px;">
                        {{ $item['createdDate'] }}
                    </td>
                </tr>
            @endforeach
            <tr style="background-color: #646464;">
                <td colspan="5" style="padding: 5px; font-size: 12px; color: #ffffff;">
                    {{ count($items) }} ticket/s found.
                </td>
            </tr>
        @else
            <tr style="background-color: #dfdfdf;">
                <td colspan="5" style="padding: 5px; font-size: 11px; text-align: center;">
                    No tickets found.
                </td>
            </tr>
        @endif
    </table>
 
    <div style="position: absolute; bottom: 0;">
        <p style="font-size: 10px; font-style: italic; font-weight: light; color: rgb(112, 112, 112); margin-bottom: 5px!important;">This report is created at 2024-01-31 10:51 am.</p>
        <p style="font-size: 10px; font-style: italic; font-weight: light; color: rgb(112, 112, 112); margin-top: 0 !important;">Generated by: Admin User</p>
    </div>
</body>
</html>