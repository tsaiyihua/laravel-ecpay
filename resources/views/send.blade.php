<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Redirect to ECPay...</title>
</head>
<body>
<form action="{{ $apiUrl }}" id="pay-form" method="post">
@foreach($postData as $key => $val)
  <input type="hidden" name="{{ $key }}" value="{{ $val }}">
@endforeach
</form>
</body>
<script>
    document.getElementById('pay-form').submit();
</script>
</html>
