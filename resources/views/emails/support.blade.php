{{-- <h1>{{ translate('Ticket') }}</h1> --}}

{{-- {{ $content }}
<p><b>{{ translate('Sender') }}: </b>{{ $sender }}</p>
<p>
	<b>{{ translate('Details') }}:</b>
	<br>
	@php echo $details; @endphp
</p>
<a class="btn btn-primary btn-md" href="{{ $link }}">{{ translate('See ticket') }}</a> --}}


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header, .footer {
            text-align: center;
            color: #333;
        }
        .header img {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .content h1 {
            font-size: 24px;
            color: #333;
            text-align: center;
        }
        .content p, .content li {
            font-size: 16px;
            color: #555;
            line-height: 1.5;
        }
        .content ol {
            padding-left: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
        }
        .footer p {
            font-size: 14px;
            color: #777;
        }
        .footer a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
	@php
			$logo = get_setting('header_logo');
	@endphp
    <div class="container">
        <div class="header">
			@if($logo != null)
			<img loading="lazy"  src="{{ uploaded_asset($logo) }}" height="40" style="display:inline-block;">
		@else
			<img loading="lazy"  src="{{ static_asset('assets/img/logo.png') }}" height="40" style="display:inline-block;">
		@endif
        </div>
        <div class="content">
            {!! $content !!} 
            <hr>
        </div>
        <div class="footer">
            <p><strong>Shopeedo Headquarters: Lahore, Punjab, Pakistan.</strong></p>
            <p>Best regards,<br>The Shopeedo Team</p>
            <p><a href="https://dev.shopeedo.com">Visit Shopeedo</a> | <a href="mailto:info@shopeedo.com">Contact Support</a></p>
        </div>
    </div>
</body>
</html>


