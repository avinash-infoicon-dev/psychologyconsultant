{{-- Original maintenance page:
<!DOCTYPE html>
<html>
<head>
    <title>Maintenance</title>
</head>
<body>
    <h1>We'll be back soon!</h1>
    <p>Our website is currently undergoing scheduled maintenance.</p>
</body>
</html>
--}}

{{-- Previous English-only styled page:
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Under Maintenance</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(100deg,#4f46e5,#7c3aed);
            color:#fff;
            display:flex;
            align-items:center;
            justify-content:center;
            min-height:100vh;
            padding:20px;
        }

        .card{
            background:rgba(216, 147, 147, 0.12);
            backdrop-filter:blur(12px);
            border:1px solid rgba(255, 255, 255, 0.41);
            border-radius:18px;
            max-width:650px;
            width:100%;
            text-align:center;
            padding:50px 40px;
            box-shadow:0 20px 40px rgba(49, 45, 45, 0.25);
        }

        .icon{
            font-size:70px;
            margin-bottom:20px;
        }

        h1{
            font-size:38px;
            margin-bottom:15px;
            font-weight:700;
        }

        p{
            font-size:18px;
            line-height:1.7;
            opacity:.95;
        }

        .status{
            display:inline-block;
            margin-top:30px;
            background:#ffffff;
            color:#4f46e5;
            padding:10px 24px;
            border-radius:30px;
            font-weight:600;
        }

        .footer{
            margin-top:35px;
            font-size:14px;
            opacity:.8;
        }

        @media(max-width:600px){

            .card{
                padding:35px 25px;
            }

            h1{
                font-size:28px;
            }

            p{
                font-size:16px;
            }

            .icon{
                font-size:55px;
            }

        }
    </style>
</head>
<body>

<div class="card">

    <div class="icon">🛠️</div>

    <h1>We'll Be Back Soon</h1>

    <p>
        Our website is currently undergoing scheduled maintenance to improve
        performance and enhance your experience.
    </p>

    <p style="margin-top:15px;">
        We appreciate your patience and apologize for any inconvenience.
    </p>

    <div class="status">
        🚧 Maintenance in Progress
    </div>

    <div class="footer">
        Thank you for your understanding.
    </div>

</div>

</body>
</html>
--}}

@extends('errors.layout', ['hideHome' => true])

@section('title', __('errors.503.title'))
@section('icon', '🛠️')
@section('heading', __('errors.503.heading'))
@section('status', '🚧 '.__('errors.503.status'))

@section('body')
    <p>{{ __('errors.503.body') }}</p>
    <p style="margin-top:15px;">{{ __('errors.503.patience') }}</p>
@endsection
