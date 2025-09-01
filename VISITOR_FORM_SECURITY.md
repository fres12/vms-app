# Visitor Form Security Implementation

## Security Measures Implemented

### 1. SQL Injection Prevention
- ✅ **Parameterized Queries**: Using Laravel Eloquent ORM with prepared statements
- ✅ **Input Sanitization**: All inputs are sanitized before database insertion
- ✅ **Type Casting**: Integer values are properly cast to prevent injection
- ✅ **Validation**: Strict validation rules for all input fields

### 2. XSS (Cross-Site Scripting) Prevention
- ✅ **Input Sanitization**: HTML tags and special characters are filtered
- ✅ **Output Escaping**: Laravel's `{{ }}` syntax automatically escapes output
- ✅ **Client-side Filtering**: Real-time filtering of dangerous characters
- ✅ **HTML5 Validation**: Pattern attributes prevent invalid input

### 3. File Upload Security
- ✅ **File Type Validation**: Only JPG, JPEG, PNG files allowed
- ✅ **MIME Type Checking**: Server-side MIME type verification
- ✅ **File Size Limits**: Maximum 2MB per file
- ✅ **Secure Filenames**: Random filenames prevent path traversal
- ✅ **Image Validation**: `getimagesize()` verification

### 4. Input Validation & Sanitization
- ✅ **Full Name**: Only letters, spaces, dots, hyphens, apostrophes
- ✅ **Email**: Strict regex pattern for email format
- ✅ **NIK**: Only numbers, maximum 16 characters
- ✅ **Phone**: Only numbers, hyphens, parentheses, spaces
- ✅ **Company**: Alphanumeric, spaces, dots, hyphens, ampersands, commas
- ✅ **Visit Purpose**: Alphanumeric, spaces, dots, hyphens, commas, exclamation, question marks
- ✅ **Equipment/Brand**: Alphanumeric, spaces, dots, hyphens, ampersands, commas

### 5. Rate Limiting
- ✅ **Form Submission Limit**: Maximum 3 submissions per IP
- ✅ **Lockout Period**: 30-minute lockout after 3 failed attempts
- ✅ **IP-based Tracking**: Form attempts tracked by IP address

### 6. Client-Side Security
- ✅ **JavaScript Validation**: Real-time input validation
- ✅ **Character Filtering**: Dangerous characters blocked in real-time
- ✅ **Length Restrictions**: HTML5 maxlength attributes
- ✅ **Pattern Validation**: HTML5 pattern attributes

### 7. Error Handling
- ✅ **Generic Error Messages**: Specific error details not exposed
- ✅ **Error Logging**: All errors logged with IP and user agent
- ✅ **Input Preservation**: Form data preserved on validation errors

### 8. Data Protection
- ✅ **Input Length Limits**: Maximum lengths enforced
- ✅ **Null Byte Removal**: Null bytes stripped from input
- ✅ **Whitespace Trimming**: Leading/trailing whitespace removed
- ✅ **Special Character Encoding**: HTML entities encoded

## Validation Rules

### Server-Side Validation
```php
'full_name' => [
    'required',
    'string',
    'max:255',
    'regex:/^[a-zA-Z\s\.\-\']+$/'
],
'email' => [
    'required',
    'email',
    'max:255',
    'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
],
'nik' => [
    'required',
    'string',
    'max:16',
    'regex:/^[0-9]+$/'
],
'phone' => [
    'nullable',
    'string',
    'max:20',
    'regex:/^[0-9\-\+\(\)\s]+$/'
],
'visit_purpose' => [
    'required',
    'string',
    'max:500',
    'regex:/^[a-zA-Z0-9\s\.\-\,\!\?]+$/'
]
```

### Client-Side Validation
```javascript
// Full name validation
const nameRegex = /^[a-zA-Z\s\.\-\']+$/;

// Email validation
const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

// NIK validation
const nikRegex = /^[0-9]+$/;

// Phone validation
const phoneRegex = /^[0-9\-\+\(\)\s]+$/;
```

## File Upload Security

### Allowed File Types
- JPG/JPEG
- PNG

### File Size Limit
- Maximum 2MB per file

### Security Checks
1. File extension validation
2. MIME type verification
3. File size checking
4. Image content validation
5. Secure filename generation

## Rate Limiting Configuration

### Form Submission Limits
- Maximum 3 attempts per IP address
- 10-minute window for attempts
- 30-minute lockout after 3 failed attempts

### Cache Keys
- `visitor_form_{ip}` - Attempt counter
- `visitor_form_{ip}_lockout` - Lockout timestamp

## Security Headers

The form inherits security headers from the global SecurityHeaders middleware:
- X-Frame-Options: DENY
- X-Content-Type-Options: nosniff
- X-XSS-Protection: 1; mode=block
- Referrer-Policy: strict-origin-when-cross-origin

## Testing Security

To test the security measures:

1. **SQL Injection**: Try `' OR '1'='1` in any field
2. **XSS**: Try `<script>alert('xss')</script>` in any field
3. **File Upload**: Try uploading non-image files or oversized files
4. **Rate Limiting**: Try submitting the form multiple times quickly
5. **Input Validation**: Try entering invalid characters in each field

All attacks should be blocked and logged appropriately.

## Monitoring

### Logged Events
- Successful form submissions
- Failed form submissions
- File upload attempts
- Rate limiting triggers
- Validation errors

### Log Information
- IP address
- User agent
- Form data (sanitized)
- Error details (server-side only)
- Timestamp

## Best Practices

1. Always use HTTPS in production
2. Regularly monitor logs for suspicious activity
3. Keep Laravel and dependencies updated
4. Regularly backup form submissions
5. Monitor file upload directory for malicious files
6. Implement CAPTCHA for additional protection if needed 