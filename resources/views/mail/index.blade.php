<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Invitation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
        }

        .header h1 {
            color: #333333;
        }

        .content h2 p{
            margin-top: 20px;
            color: #555555;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
        }

        @media screen and (max-width: 600px) {
            .container {
                width: 100%;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Invitation</h1>
        </div>
        <div class="content">
            <h2>Hello, this is the email from CRM Project!</h2>
            <p>We are inviting you join us as company employee.</p>
            <p>Click the link below and get registred</p>
            <a href="https://765d-103-82-121-241.ngrok-free.app/accept-invitation/{{$token}}" class="button">Register</a>
        </div>
    </div>
</body>
</html>