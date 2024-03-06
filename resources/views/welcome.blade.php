<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
    </style>



</head>

<body>
    <?php
    //separa os dados das datas



    $date_start = substr($data_start, 0, 2);
    $month_start = substr($data_start, 3, 2);
    $year_start = substr($data_start, 6, 6);

    $date_end = substr($data_end, 0, 2);
    $month_end = substr($data_end, 3, 2);
    $year_end = substr($data_end, 6, 6);



    //pegar a quantidade de dias dos  meses
    $get_days_start = cal_days_in_month(CAL_GREGORIAN, $month_start, $year_start);

    $get_days_end = cal_days_in_month(CAL_GREGORIAN, $month_end, $year_end);

    $total_dates = 0;
    //se mes inicial for o mesmo que o mes final ele diminui a data inicial pela data final
    if ($month_start == $month_end) {
        $total_dates = $date_end - $date_start;
    } else {
        $total_dates += $get_days_start - $date_start;
        $total_dates += $date_end;
    }



    //vai armazenar todas datas 
    $period = [];
    $l = 0;
    for ($i = $date_start; $i <= $get_days_start; $i++) {
        if ($l <= 30 &&  sizeof($period) <= $total_dates) {
            $date = intval($i);

            if (strlen($date) == 1) {
                array_push($period, "0{$date}/{$month_start}");
                $l += 1;
            } else {
                array_push($period, "{$date}/{$month_start}");
                $l += 1;
            }
        }
    }

    for ($i = 1; $i <= $date_end; $i++) {
        if ($l <= 30 &&  sizeof($period) <= $total_dates) {
            $date = intval($i);

            if (strlen($date) == 1) {
                array_push($period, "0{$date}/{$month_end}");
                $l += 1;
            } else {
                array_push($period, "{$date}/{$month_end}");
                $l += 1;
            }
        }
    }


    //MOCK DATA
    $MOCK = [
        [
            'quarto' => 'A100',
            'reservas' => [
                [
                    'periodo_start' => '02/02/2024',
                    'periodo_end' => '11/02/2024',
                    'check-in' => true
                ],
                [
                    'periodo_start' => '11/02/2024',
                    'periodo_end' => '15/02/2024',
                    'check-in' => false
                ]
            ]

        ],
        [
            'quarto' => 'B200',
            'reservas' => [
                [
                    'periodo_start' => '20/01/2024',
                    'periodo_end' => '02/02/2024',
                    'check-in' => false
                ], [
                    'periodo_start' => '02/02/2024',
                    'periodo_end' => '15/02/2024',
                    'check-in' => true
                ]
            ]

        ]
    ];

    function getAllDates($array, $date)
    {
        $new_mock = array_filter(
            $array,
            function ($item) use ($date) {
                return $item['periodo_end'] == $date || $item['periodo_start'] == $date;
            }

        );
        return $new_mock;
    }



    ?>

    <h1>Reservas</h1>
    <p>Data start: <span>{{$data_start}}</span></p>
    <p>Data end: <span>{{$data_end}}</span></p>


    <div class="container-fluid  border">
        <table class="table">
            <thead>

                <tr>
                    <th class="">Quarto</th>

                    <?php
                    foreach ($period as $value) {
                        echo ("<th>{$value}</th>");
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $html = "";
                foreach ($MOCK as $item) {
                    $html = $html . "<td>{$item['quarto']}</td>";

                    foreach ($period as $date) {
                        $block = "";

                        $timeZone = new DateTimeZone('UTC');

                        //data do td para timerstamp
                        $new_date = $date . '/' . $year_start;
                        $data_td = DateTime::createFromFormat('d/m/Y', $new_date, $timeZone);

                        foreach ($item['reservas'] as $reserva) {

                            $d_inicio = DateTime::createFromFormat('d/m/Y', $reserva['periodo_start'], $timeZone);
                            $d_final = DateTime::createFromFormat('d/m/Y', $reserva['periodo_end'], $timeZone);

                            if ($data_td > $d_inicio && $data_td < $d_final) {
                                $hasCheck = $reserva['check-in'] == true ? "bg-success" : "bg-danger";
                                $block = "<div class='{$hasCheck}' style='width:100%;height:100%;'></div>";
                            }


                            if ($data_td == $d_final) {
                                $new_mock = getAllDates($item['reservas'], $reserva['periodo_end']);
                                if (count($new_mock) > 1) {
                                    $hasCheckExit = $new_mock[0]['check-in'] == true ? "bg-success" : "bg-danger";
                                    $hasCheckEnter = $new_mock[1]['check-in'] == true ? "bg-success" : "bg-danger";
                                    $block = "<div class='d-flex' style='width:100%;height:100%;'> <div class=' start {$hasCheckExit}' style='width:100%;height:100%;'></div><div class=' start {$hasCheckEnter}' style='width:100%;height:100%;'></div> </div>";
                                } else {
                                    $hasCheck = $reserva['check-in'] == true ? "bg-success" : "bg-danger";
                                    $block = "<div class='{$hasCheck}' style='width:100%;height:100%;'></div>";
                                }
                            }

                            if ($data_td == $d_inicio) {
                                $new_mock = getAllDates($item['reservas'], $reserva['periodo_start']);
                                if (count($new_mock) == 1) {
                                    $hasCheck = $reserva['check-in'] == true ? "bg-success" : "bg-danger";
                                    $block = "<div class='{$hasCheck}' style='width:100%;height:100%;'></div>";
                                }
                            }
                        }


                        $html =  $html . "<td style='width:50px;height:50px;' >{$block}</td>";
                    }
                    echo ("<tr>{$html}</tr>");
                    $html = "";
                }

                ?>
            </tbody>

        </table>

    </div>

    <script>

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>