<!DOCTYPE html>
<html lang="en">
<head>

    <title>Clover coverage</title>
    <base href="{{URL::asset('/')}}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="/css/site.css" rel="stylesheet" media="screen">
    <link href="/css/clover.css" rel="stylesheet" media="screen">
    <meta charset="UTF-8" />
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <table class="table">
                <tr>
                    <th>File</th>
                </tr>
                @foreach($return as $line)
                @if($line['line'] != '')
                <tr>
                    <td
                    @if($line['covered'] === true)
                    class="success"
                    @endif
                    @if($line['covered'] === false)
                    class="danger"
                    @endif
                        ><pre>{{{$line['line']}}}</pre></td>
                </tr>
                @endif

                @endforeach
            </table>
        </div>
    </div>
</div>
</body>
</html>