<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email</title>
</head>
<body>
<p>Take Action Name: {{ isset($name) ? $name : ''}}</p>

  <img src="{!! $message->embedData($qr_code, 'QrCode.png', 'image/png')!!}"  width="200" height="200" style="display:block; border:none; outline:none; text-decoration:none;">

</body>
</html>
