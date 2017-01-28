<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>{{ array_get($data, 'hostname') }}</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!--<link rel="apple-touch-icon" href="apple-touch-icon.png">-->
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <style>
            .status-unknown {
                color: gray;
            }
            .status-up {
                color: green;
            }
            .status-down {
                color: red;
            }
            
            .status-history {
                display: inline-block;
                height: 12px;
                margin: 0;
                padding: 0;
                overflow: hiddem;
            }
            
            .status-history .unknown {
                background-color: #d9edf7;
            }
            
            .status-history .up {
                background-color: #dff0d8;
            }
            
            .status-history .down {
                background-color: #f2dede;
            }
            
            .status-history a {
                float: left;
                width: 12px;
                height: 12px;
                cursor: pointer;
            }
            
            .status-history a:hover {
                height: 11px;
                border: 1px solid #000;
            }
        </style>
    </head>
    <body>
        <!--[if lte IE 9]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
        <![endif]-->

        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <h1>{{ array_get($data, 'hostname') }}</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div id="loadStats" style="width: 100%; height: 500px;"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-lg-offset-4 col-sm-12 col-sm-offset-0">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>History</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (array_get($data, 'daemons', []) as $daemon)
                            <tr class="active">
                                <td>{{ $daemon['name'] }}</td>
                                @if ($daemon['isup'] === true)
                                <td class="status-up">Up</td>
                                @elseif ($daemon['isup'] === false)
                                <td class="status-down">Down</td>
                                @else
                                <td class="status-unknown">Unknown</td>
                                @endif
                                <td><div class="status-history">
                                @foreach ($daemon['history'] as $history)
                                    <a title="{{ $history['time'] }}" class="{{ (is_null($history['isup'])?'unknown':($history['isup']===true?'up':'down')) }}"></a>
                                @endforeach
                                </div></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
          google.charts.load('current', {'packages':['corechart']});
          google.charts.setOnLoadCallback(drawChart);

          function drawChart() {
            var data = google.visualization.arrayToDataTable({!! json_encode(array_get($data, 'load_stats', [])) !!});

            var options = {
              vAxis: {minValue: 0}
            };

            var chart = new google.visualization.AreaChart(document.getElementById('loadStats'));
            chart.draw(data, options);
          }
        </script>
    </body>
</html>
