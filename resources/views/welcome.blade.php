<!DOCTYPE html>
<html>
    <head>
        <title>Is Stuart Pooping?</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                /*display: table-cell;*/
                margin-top: 50px;
                /*vertical-align: middle;*/
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 96px;
                margin-bottom: 50px;
            }

            .previous {
                font-size: 30px;
                margin-bottom: 50px;
            }

            .world-record {
                font-size: 30px;
                margin-bottom: 50px;
            }

            .stats {
                font-size: 30px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">{{ $isPooping ? 'YES' : 'NO' }}</div>
                @if(isset($mostRecentPoop))
                <div class="previous">
                    Most recent poop: {{ $mostRecentPoop->end_at->diffForHumans() }} <br>
                    It took: {{ $mostRecentPoop->readableDuration() }}
                </div>
                <div class="world-record">
                    His all-time record is: {{ $recordPoop->readableDuration() }} <br>
                    It occurred on: {{ $recordPoop->end_at->format('F jS Y') }}
                </div>
                <div class="stats">
                    Lifetime Poops: {{ $lifetimePoops }} <br>
                    Average Poop Time: {{ $averagePoopTime }}

                </div>
                @else
                <div class="previous">
                    NO POOPS RECORDED YET
                </div>
                @endif
            </div>
        </div>
    </body>
</html>
