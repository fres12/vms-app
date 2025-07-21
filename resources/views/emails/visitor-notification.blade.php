<!DOCTYPE html>
<html>
<head>
    <title>New Visitor Registration</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2d3748;">New Visitor Registration</h2>
        
        <p>A new visitor has registered on {{ \Carbon\Carbon::parse($visitor['submit_date'])->format('d M Y H:i') }} with the following details:</p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Full Name:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ $visitor['name'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Company:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ $visitor['company'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Department:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ $visitor['department'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Visit Purpose:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ $visitor['visit_purpose'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Start Date:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ \Carbon\Carbon::parse($visitor['startdate'])->format('d M Y H:i') }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>End Date:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ \Carbon\Carbon::parse($visitor['enddate'])->format('d M Y H:i') }}</td>
            </tr>
        </table>

        <div style="margin-top: 30px;">
            <p>Please review this visitor registration before {{ \Carbon\Carbon::parse($visitor['startdate'])->subDay()->format('d M Y H:i') }} through <a href="http://localhost:8000/visitor-list" style="color: #4CAF50; text-decoration: underline;">this link</a>.</p>
        </div>

        <div style="margin-top: 40px; font-size: 12px; color: #666;">
            <p>This is an automated message from the VMS App. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html> 