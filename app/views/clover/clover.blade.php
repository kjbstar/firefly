<!DOCTYPE html>
<html lang="en">
<head>

    <title>Clover coverage</title>
    <base href="{{URL::asset('/')}}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="/css/site.css" rel="stylesheet" media="screen">
    <meta charset="UTF-8" />
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered">
            <tr>
                <th>File</th>
                <th>Covered methods</th>
                <th>Covered statements</th>
                <th>Covered elements</th>
            </tr>
                @foreach($info as $file => $row)
                <tr>
                    <td><a href="{{URL::Route('cloverclass',$file)}}">{{$file}}</a></td>
                    @if($row['methods'] < 50)
                    <td class="danger">{{$row['methods']}}%</td>
                    @elseif($row['methods'] >= 50 && $row['methods'] < 90)
                    <td class="warning">{{$row['methods']}}%</td>
                    @elseif(is_null($row['methods']))
                    <td>&nbsp;</td>
                    @else
                    <td class="success">{{$row['methods']}}%</td>
                    @endif


                    @if($row['statements'] < 50)
                    <td class="danger">{{$row['statements']}}%</td>
                    @elseif($row['statements'] >= 50 && $row['statements'] < 90)
                    <td class="warning">{{$row['statements']}}%</td>
                    @elseif(is_null($row['statements']))
                    <td>&nbsp;</td>
                    @else
                    <td class="success">{{$row['statements']}}%</td>
                    @endif


                    @if($row['elements'] < 50)
                    <td class="danger">{{$row['elements']}}%</td>
                    @elseif($row['elements'] >= 50 && $row['elements'] < 90)
                    <td class="warning">{{$row['elements']}}%</td>
                    @elseif(is_null($row['elements']))
                    <td>&nbsp;</td>
                    @else
                    <td class="success">{{$row['elements']}}%</td>
                    @endif
                </tr>

                @endforeach
            </table>
        </div>
    </div>
</div>
</body>
</html>