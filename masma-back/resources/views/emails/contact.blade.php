<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contact Form Submission</title>
</head>
<body>
    <h2>New Contact Inquiry</h2>
    
    <p><strong>Name:</strong> {{ $name }}</p>
    <p><strong>Company:</strong> {{ $company }}</p>
    <p><strong>Email:</strong> {{ $email }}</p>
    <p><strong>Phone:</strong> {{ $phone }}</p>
    <p><strong>Interest:</strong> {{ $interest }}</p>
    <p><strong>Category:</strong> {{ $category }}</p>
    <p><strong>Message:</strong><br>{{ $message }}</p>
    
    <hr>
    <p><em>Sent from MASMA website contact form</em></p>
</body>
</html>