<?php
/* Includes */
//include 'misc.php';
//include 'io.php';

/* Load game */
$game = ioLoadGame($gid, $uid);

/* Get PGN result */
switch ($game['curstate']) {
    case 'w':
        $result = '1-0';
        break;
    case 'b':
        $result = '0-1';
        break;
    case '-':
        $result = '1/2-1/2';
        break;
    default:
        $result = '*';
        break;
}

/* Remove last move if OCC specific and remember whether mate */
$last = $game['mhistory'][count($game['mhistory']) - 1];
if ($last == 'mate' || $last == '---' || $last == 'resigned' || $last == 'draw' ||
    $last == 'stalemate') {
    if ($last == 'mate')
        $ismate = 1;
    unset($game['mhistory'][count($game['mhistory']) - 1]);
}

/* Get real move number */
$rounds = floor((count($game['mhistory']) + 1) / 2);

/* Show PGN header */
$pgnStrHeader = '[Event "bebogame turn based western chess"]' . "\n";
$pgnStrHeader .= '[Site "bebogame.com"]' . "\n";
$pgnStrHeader .= '[Date "' . date('Y.m.d', $game['ts_start']) . '"]' . "\n";
$pgnStrHeader .= '[Round "' . $rounds . '"]' . "\n";
$pgnStrHeader .= '[White "' . $game['white'] . '"]' . "\n";
$pgnStrHeader .= '[Black "' . $game['black'] . '"]' . "\n";
$pgnStrHeader .= '[Result "' . $result . '"]' . "\n\n";

/* Show PGN moves */
$num = count($game['mhistory']);
for ($j = 1, $i = 0; $i < $num; $i++) {
    /* Get check or checkmate sign */
    $move = $game['mhistory'][$i];
    if ($move[strlen($move) - 1] == '+') {
        if ($i == $num - 1 && $ismate)
            $check = '#';
         /* checkmate */
        else
            $check = '+';
        $move = substr($move, 0, strlen($move) - 1);
    } else
        $check = '';
    /* Castling is not Zeros but Os */
    if ($move == '0-0' || $move == '0-0-0')
        $move = str_replace('0', 'O', $move);
    /* Promotion has equation sign */
    $c = $move[strlen($move) - 1];
    if ($c == 'Q' || $c == 'R' || $c == 'B' || $c == 'N')
        $move = sprintf('%s=%s', substr($move, 0, strlen($move) - 1), $move[strlen($move) -
            1]);
    /* Show move and put newline after 5 rounds */
    if ($i % 2 == 0) {
        $pgnStr .= $j . '. ';
        $j++;
    }
    $pgnStr .= $move . $check;
    if (($i + 1) % 10 == 0)
        $pgnStr .= "\n";
    else
        $pgnStr .= ' ';
}
?>