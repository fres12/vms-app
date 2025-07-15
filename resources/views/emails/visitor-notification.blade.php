<!DOCTYPE html>
<html>
<head>
    <title>New Visitor Registration</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2d3748;">New Visitor Registration</h2>
        
        <p>A new visitor has registered with the following details:</p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Full Name:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ $visitor->full_name }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Company:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ $visitor->company }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Department:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ $visitor->department_purpose }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Section:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ $visitor->section_purpose }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Visit Date:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ \Carbon\Carbon::parse($visitor->visit_datetime)->format('d M Y') }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Visit Time:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ \Carbon\Carbon::parse($visitor->visit_datetime)->format('H:i') }}</td>
            </tr>
        </table>

        <div style="margin-top: 30px;">
            <p>Please review and approve/reject this registration.</p>
        </div>

        <div style="margin-top: 40px; font-size: 12px; color: #666;">
            <p>This is an automated message from the VMS App. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html> 