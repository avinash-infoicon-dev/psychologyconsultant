@php
    \App\Support\Locales::applyFromRequest(request(), true);
    $errorLocales = \App\Support\Locales::all();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

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

        .home{
            display:inline-block;
            margin-top:22px;
            color:#fff;
            text-decoration:underline;
            font-weight:600;
        }

        .langs{
            display:flex;
            justify-content:center;
            flex-wrap:wrap;
            gap:8px;
            margin-top:28px;
        }

        .lang{
            color:#fff;
            text-decoration:none;
            border:1px solid rgba(255, 255, 255, 0.55);
            border-radius:999px;
            padding:6px 14px;
            font-size:13px;
            font-weight:600;
            letter-spacing:.04em;
        }

        .lang.is-active{
            background:#fff;
            color:#4f46e5;
            border-color:#fff;
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

    <div class="icon">@yield('icon')</div>

    <h1>@yield('heading')</h1>

    @yield('body')

    <div class="status">
        @yield('status')
    </div>

    @unless ($hideHome ?? false)
        <div>
            <a class="home" href="{{ url('/') }}">{{ __('errors.home') }}</a>
        </div>
    @endunless

    @if (count($errorLocales) > 1)
        <div class="langs">
            @foreach ($errorLocales as $code => $meta)
                <a class="lang{{ app()->getLocale() === $code ? ' is-active' : '' }}"
                   href="{{ request()->fullUrlWithQuery(['locale' => $code]) }}"
                   hreflang="{{ str_replace('_', '-', $code) }}"
                   lang="{{ str_replace('_', '-', $code) }}">{{ $meta['short'] }}</a>
            @endforeach
        </div>
    @endif

    <div class="footer">
        {{ __('errors.footer') }}
    </div>

</div>

</body>
</html>
