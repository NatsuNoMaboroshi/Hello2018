<?php
$locale = [
    'name' => 'Enter your name: ',
    'isFirst' => 'Do you want to start first? (y/n) ',
    'isNew' => 'Start a new game? (y/n) ',
    'position' => 'It is your turn, please enter target x,y position (e.g.: 2,3) ',
    'incorrectPosition' => 'Incorrect position! Please enter another coordinate. '
];

function main()
{
    global $locale, $race, $whosTurn, $raceCount, $isFirst, $name;
    $race = [
        [ ' ', ' ', ' ' ],
        [ ' ', ' ', ' ' ],
        [ ' ', ' ', ' ' ],
    ];
    $whosTurn = 'o';
    $raceCount = 1;

    // ask name
    setOutput($locale['name']);
    $name = getInput();
    setOutput("Hello, $name\n");
    // ask play order
    setOutput($locale['isFirst']);
    $isFirst = getInput() == 'y' ? true : false;
    // ask position
    playGame();

    // ask start a new game
    setOutput($locale['isNew']);
    $isNew = getInput() == 'y' ? true : false;
    return !$isNew or main();
}

function playGame()
{
    global $race, $locale, $whosTurn, $raceCount, $isComputer, $isFirst, $name;
    $isComputer = ($raceCount + $isFirst) & 1;
    // play with computer
    if ($isComputer) {
        $candidate = [];
        foreach ($race as $row => $columns) {
            foreach ($columns as $col => $value) {
                if ($value == ' ') {
                    $x = $row + 1;
                    $y = $col + 1;
                    $candidate[] = "$x,$y";
                }
            }
        }
        $index = array_rand($candidate);
        $position = $candidate[$index];
        setOutput("Computer ($whosTurn), " . $locale['position'] . "\n");
    } else {
        setOutput($name . " ($whosTurn), " . $locale['position']);
        $position = getInput();
    }
    @list($x, $y) = explode(',', $position);
    $x = filter_var($x, FILTER_VALIDATE_INT);
    $y = filter_var($y, FILTER_VALIDATE_INT);

    if (isValidPosition($x) && isValidPosition($y) && $race[$x-1][$y-1] == ' ') {
        $raceCount++;

        $race[$x-1][$y-1] = $whosTurn;
        $whosTurn = $whosTurn == 'o' ? 'x' : 'o';
        foreach ($race as $row => $columns) {
            $line = ' ' . implode(' | ', $columns) . "\n";
            setOutput($line);
            if ($row < 2) {
                setOutput('───┼───┼───' . "\n");
            }
        }
    } else {
        setOutput($locale['incorrectPosition'] . "\n");
    }
    // isGameOver?
    return isGameOver() or playGame();
}

function isValidPosition($val)
{
    return is_int($val) && $val > 0 && $val < 4;
}

function isGameOver()
{
    global $race;
    foreach ($race as $row => $columns) {
        // checkRow
        $string = implode($columns);
        if ($string == 'xxx' || $string == 'ooo') {
            return true;
        }
    }
    // checkColumn
    // write a matrix transpose is better but I am lazy
    for ($i = 0; $i < 3; $i++) {
        $string = '';
        foreach ($race as $row => $columns) {
            $string .= $race[$row][$i];
        }
        if ($string == 'xxx' || $string == 'ooo') {
            return true;
        }
    }
    // checkDiagonal
    // center is 1,1
    $string = $race[0][0] . $race[1][1] . $race[2][2];
    if ($string == 'xxx' || $string == 'ooo') {
        return true;
    }

    $string = $race[0][2] . $race[1][1] . $race[2][0];
    if ($string == 'xxx' || $string == 'ooo') {
        return true;
    }

    // is full
    $line = '';
    foreach ($race as $row => $columns) {
        $line .= implode($columns);
    }
    if (strpos(' ', $line) < 0) {
        return true;
    }

    return false;
}

function getInput()
{
    return trim(fgets(STDIN));
}

function setOutput($msg)
{
    fwrite(STDOUT, $msg);
}

main();
