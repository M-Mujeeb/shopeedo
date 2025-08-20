<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to Bank Alfalah</title>
</head>
<body>
    <form id="bank_alfalah_form" action="https://payments.bankalfalah.com/HS/HS/HS" method="POST">
        @foreach($post_data as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    </form>
    <script>
        document.getElementById('bank_alfalah_form').submit();
    </script>
</body>
</html>