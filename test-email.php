<?php
/**
 * Simple Email Test Script
 * Run this file directly to test email functionality
 */

// Email configuration
$to = 'siregarfresnel@gmail.com';
$subject = 'Test Email from VMS App';
$message = '
<html>
<head>
    <title>Test Email</title>
</head>
<body>
    <h2>Test Email from VMS App</h2>
    <p>This is a test email to verify email functionality.</p>
    <p>If you receive this email, the email system is working correctly.</p>
    <p>Time sent: ' . date('Y-m-d H:i:s') . '</p>
</body>
</html>';

// Email headers
$headers = array(
    'MIME-Version: 1.0',
    'Content-type: text/html; charset=UTF-8',
    'From: VMS App <noreply@vmsapp.com>',
    'Reply-To: noreply@vmsapp.com',
    'X-Mailer: PHP/' . phpversion()
);

// Send email
$mailSent = mail($to, $subject, $message, implode("\r\n", $headers));

if ($mailSent) {
    echo "✅ Test email sent successfully to $to\n";
    echo "📧 Subject: $subject\n";
    echo "⏰ Time: " . date('Y-m-d H:i:s') . "\n";
} else {
    echo "❌ Failed to send test email\n";
    echo "🔧 Please check your server's mail configuration\n";
}

echo "\n📋 Next steps:\n";
echo "1. Check your email inbox at $to\n";
echo "2. If email received, Laravel email should work\n";
echo "3. If not received, check server mail logs\n";
?> 