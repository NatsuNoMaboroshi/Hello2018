<?php
function getStarByNumber($value)
{
    $numberArray = str_split($value);

    $array = [
        0 => [
            ' *** ',
            '*   *',
            '*   *',
            '*   *',
            ' *** ',
        ],
        8 => [
            ' *** ',
            '*   *',
            '*****',
            '*   *',
            ' *** ',
        ],
        9 => [
            ' *** ',
            '*   *',
            '*****',
            '    *',
            ' *** ',
        ]
    ];

    for ($i = 1; $i <= 5; $i++) {
        $line = '';
        foreach ($numberArray as $value) {
            $line = $line . '  ' . $array[$value][$i-1];
        }
        echo $line . "\n";
    }
}

getStarByNumber(80988000999);
