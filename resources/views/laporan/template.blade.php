<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link type="text/css" rel="stylesheet" href="{{asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link href="/vendor/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <style>
    tbody tr td
    {
        vertical-align: middle !important;
    }

    #logo-obbi
    {
        width: 100px;
        margin: 20px;
        position: absolute;
    }

    #logo-company
    {
        width: 100px;
        margin: 20px;
        right: 0px;
        position: absolute;
    }

    @media print
    {
        tfoot
        {
            display: table-row-group !important;
        }

        thead
        {
            display: table-row-group !important;
        }

        .trbr
        {
            background-color: transparent !important; 
            border: none !important;
        }
        
        a[href]:after {
            content: none;
          }
    }
    </style>
</head>
<body id='body'>
    <div id='print'>
        <img src='/img/logo.jpg' id='logo-obbi'>
        {{--  @if(!empty($image))
        <img src='data:image/jpg;base64,{!! $image !!}' id='logo-company'>
        @endif  --}}

        
        {!! $body !!}

    </div>
</body>
</html>
<script>
window.print();
</script>