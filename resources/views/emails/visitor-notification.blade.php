<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Visitor Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8fafc;
            padding: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 0 0 8px 8px;
        }
        .visitor-info {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #2563eb;
        }
        .label {
            font-weight: bold;
            color: #374151;
        }
        .value {
            color: #6b7280;
            margin-bottom: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Visitor Registration</h1>
        <p>Department A has received a new visitor registration</p>
    </div>
    
    <div class="content">
        <h2>Visitor Details:</h2>
        
        <div class="visitor-info">
            <div class="label">Full Name:</div>
            <div class="value">{{ $visitorData['full_name'] }}</div>
            
            <div class="label">NIK (National ID):</div>
            <div class="value">{{ $visitorData['nik'] }}</div>
            
            <div class="label">Company:</div>
            <div class="value">{{ $visitorData['company'] ?? 'Not provided' }}</div>
            
            <div class="label">Phone:</div>
            <div class="value">{{ $visitorData['phone'] ?? 'Not provided' }}</div>
            
            <div class="label">Department Purpose:</div>
            <div class="value">{{ $visitorData['department_purpose'] }}</div>
            
            <div class="label">Section Purpose:</div>
            <div class="value">{{ $visitorData['section_purpose'] ?? 'Not provided' }}</div>
            
            <div class="label">Visit Date & Time:</div>
            <div class="value">{{ $visitorData['visit_datetime'] }}</div>
            
            <div class="label">Registration Date:</div>
            <div class="value">{{ $visitorData['created_at'] }}</div>
        </div>
        
        <p><strong>Note:</strong> This visitor has uploaded their ID card photo and self photo. Please review the registration in the admin panel.</p>
    </div>
    
    <div class="footer">
        <p>This is an automated notification from VMS App</p>
        <p>Please do not reply to this email</p>
    </div>
</body>
</html> 