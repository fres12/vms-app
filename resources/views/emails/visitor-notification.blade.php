<!DOCTYPE html>
<html>
<head>
    <title>Visitor Registration Notification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #003368;">Visitor Registration - {{ $data['status'] }}</h2>
        
        <p>Dear {{ isset($data['recipient_name']) ? $data['recipient_name'] : 'Admin' }},</p>

        <div style="margin: 20px 0;">
            <p><strong>{{ $data['message'] }}</strong></p>
            
            <h3 style="color: #003368;">Visitor Details:</h3>
            <table style="width: 100%; border-collapse: collapse; margin: 10px 0;">
                <tr>
                    <td style="padding: 8px 0;"><strong>Name:</strong></td>
                    <td>{{ $data['name'] }}</td>
                </tr>
                @if($data['company'])
                <tr>
                    <td style="padding: 8px 0;"><strong>Company:</strong></td>
                    <td>{{ $data['company'] }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 8px 0;"><strong>Visit Purpose:</strong></td>
                    <td>{{ $data['visit_purpose'] }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Department:</strong></td>
                    <td>{{ $data['department'] }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Start Date:</strong></td>
                    <td>{{ \Carbon\Carbon::parse($data['startdate'])->format('d M Y H:i') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>End Date:</strong></td>
                    <td>{{ \Carbon\Carbon::parse($data['enddate'])->format('d M Y H:i') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Status:</strong></td>
                    <td style="color: 
                        @if($data['status'] === 'Approved (2/2)' || $data['status'] === 'Approved')
                            #28a745
                        @elseif($data['status'] === 'For Review' || $data['status'] === 'Needs Final Approval')
                            #007bff
                        @elseif($data['status'] === 'Declined')
                            #dc3545
                        @endif
                    ">
                        {{ $data['status'] }}
                    </td>
                </tr>
            </table>
        </div>

        @if($data['status'] === 'For Review')
            <p>Please review this request in the visitor management system.</p>
            <p>You can approve or decline this request by logging into the system.</p>
        @elseif($data['status'] === 'Needs Final Approval')
            <p style="color: #dc3545;"><strong>Important:</strong> Please approve before the deadline: {{ $data['deadline'] ?? 'H-2 12:00' }}</p>
        @endif

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
            <p style="font-size: 12px; color: #666;">
                This is an automated message from the Visitor Management System.<br>
                Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html> 