<?php
$matrix = [
    [7, 4, 2, 0],
    [3, 0, 9, 0],
    [1, 2, 7, 0]
];
$startPoint = [0, 0];
$wayPoint = [0, 0];
$endPoint = [1, 2];
$previosPoint = null;
$previousNumber = null;
$allPoints = [$wayPoint];
$fakePoints = [];
$count = 0;

function horizontalMove($cord,$wayPoint, $matrix)
{
    if ($cord >= 0 and $cord <= (count($matrix[0])-1)){
        return [$wayPoint[0], $cord];
    }

    return 0;
}

function verticalMove($cord,$wayPoint, $matrix)
{
    if ($cord >= 0 and $cord <= (count($matrix)-1)){
        return [$cord, $wayPoint[1]];
    }

    return 0;
}

function choosePoint($nearestPoint, $endPoint, $matrix){
    global $allPoints, $previosPoint, $previousNumber, $wayPoint, $count, $fakePoints, $startPoint;
    $nearestPoint = array_filter($nearestPoint);
    $newPoints = [];

    foreach ($nearestPoint as $point){
        $len = abs($endPoint[1] - $point[1]);
        $necPoint = array('len' => $len, 'cord' => [$point[0],$point[1]], 'value' => $matrix[$point[0]][$point[1]], 'minValue' => $matrix[$point[0]][$point[1]]+$len);
        array_push($newPoints, $necPoint);
    }

    usort($newPoints, function($a, $b){
        return ($a['minValue'] - $b['minValue']);
    });

    $checkPoint = [];

    foreach ($newPoints as $point){
        if ($point['value'] != 0 and !in_array($point['cord'], array_merge($allPoints, $fakePoints))){
            array_push($checkPoint, $point);
        }
    }

    if (count($checkPoint) === 0){
        if(json_encode($wayPoint) == json_encode($startPoint)){
            return false;
        }
        array_pop($allPoints);
        array_push($fakePoints, $wayPoint);
        $wayPoint = $allPoints[count($allPoints)-1];
        $count -= $previousNumber;
        $nearestPoint = [horizontalMove($wayPoint[1]-1,$wayPoint, $matrix),
            horizontalMove($wayPoint[1]+1,$wayPoint, $matrix),
            verticalMove($wayPoint[0]-1,$wayPoint, $matrix),
            verticalMove($wayPoint[0]+1,$wayPoint, $matrix)];
        choosePoint($nearestPoint, $endPoint, $matrix);

    }else{
        $nextStep = $checkPoint[0];
        foreach ($checkPoint as $p){
            if ($p['minValue'] == $nextStep['minValue'] and abs($endPoint[0] - $p['cord'][0]) < abs($endPoint[0] - $p['cord'][0])){

                $nextStep = $point;
            }
        }
        array_push($allPoints, $nextStep['cord']);
        $previousNumber = $nextStep['value'];
        $wayPoint = $nextStep['cord'];
        $count += $nextStep['value'];
        echo "value: ",$nextStep['value'].", ";
        return $count;
    }
}

function checkNearestPoints(){
    global $wayPoint, $matrix, $endPoint, $allPoints;

    $nearestPoint = [horizontalMove($wayPoint[1]-1,$wayPoint, $matrix),
        horizontalMove($wayPoint[1]+1,$wayPoint, $matrix),
        verticalMove($wayPoint[0]-1,$wayPoint, $matrix),
        verticalMove($wayPoint[0]+1,$wayPoint, $matrix)];

    $cost = choosePoint($nearestPoint, $endPoint, $matrix);

    if (json_encode($wayPoint) == json_encode($endPoint)){
        echo 'Путь составляет:'.$cost." единиц. ";
        echo 'Путь: '.json_encode($allPoints);
        return;
    }

    if ($cost === false){
        echo "Путь отсутствует";
        return;
    }

    checkNearestPoints();
}

checkNearestPoints();
?>