<?php
function renderPageBegin($class)
{
    echo '<HTML><HEAD><LINK rel=stylesheet type="text/css" href="chess.css"></HEAD><BODY>';
    if (!empty($class))
        echo '<TABLE class="' . $class .
            '" border=0 cellspacing=0 cellpadding=0><TR><TD>';
    else
        echo '<TABLE border=0 cellspacing=0 cellpadding=0><TR><TD>';
}

function renderCommandForm($game, $cmdres, $move)
{
    $i_move = $game['curmove'];
    $i_plyr = 'Black';
    if ($game['curplyr'] == 'w') {
        $i_plyr = 'White';
        $i_move++;
    } else {
        if ($game['curstate'] == 'D')
            $i_move++;
    }

    if ($cmdres || ($game['archived'] == 0 && $game['lastmove'] != 'x' && ($game['curstate'] ==
        'D' || $game['curstate'] == '?'))) {
        if (!empty($cmdres))
            $info = $cmdres;
    }

    if ($game['p_maymove'] && $game['curstate'] == '?') {
        /* Normal move form */
        echo '<FORM name="commandForm" method="post">';
        echo '<input type="hidden" name="gid" value="' . $_GET['gid'] . '">';
        echo '<INPUT type="hidden" name="cmd" value="">';
        echo '<div style="text-align:center"><INPUT id="moveButton" type="button" value="Confirm Move" onClick="onClickMove()"></div>';
        echo '<INPUT type="hidden" name="move" value="' . $move . '">';
        echo '<script language="Javascript">checkMoveButton(); highlightMove(window.document.commandForm.move.value)</script>';
        echo '</FORM>';
    } else {
        if (strstr($cmdres, 'CHECKMATE')) {
            echo '<div style="text-align:center;color:#FFAA00;font-size:16px"><b>CHECKMATE! You WIN!!</b></div>';
            @unlink("./data/games/$gid");
        } elseif (strstr($cmdres, 'STALEMATE')) {
            echo '<div style="text-align:center;color:#FFAA00;font-size:16px"><b>STALEMATE! It\'s a DRAW!!</b></div>';
            @unlink("./data/games/$gid");
        } elseif (strstr($cmdres, 'CHECK')) {
            echo '<div style="text-align:center;color:#478D22"><b>CHECK! Now waiting opponent to defend.</b></div>';
        } else {
            echo '<div style="text-align:center;color:#478D22"><b>now waiting opponent to move...</b></div>';
        }
    }
}

/* Render browser form which contains title about who is playing and browsing
* buttons to move forward/backward in history. */
function renderBrowserForm($game)
{
    echo '<P align="center">';
    echo '<A href="first" onClick="return gotoMove(0);">';
    echo '<IMG alt="" src="./chessset/h_first.gif" border=0></A>';
    echo '<IMG width=2 height=2 alt="" src="./chessset/spacer.gif">';
    echo '<A href="prev" onClick="return gotoMove(cur_move-1);">';
    echo '<IMG alt="" src="./chessset/h_backward.gif" border=0></A>';
    echo '<IMG width=2 height=2 alt="" src="./chessset/spacer.gif">';
    echo '<IMG name="colorpin" alt="" src="./chessset/h_white.gif">';
    echo '<IMG name="digit1" alt="" src="./chessset/d0.gif">';
    echo '<IMG name="digit2" alt="" src="./chessset/d1.gif">';
    echo '<IMG alt="" src="./chessset/h_right.gif">';
    echo '<IMG width=2 height=2 alt="" src="./chessset/spacer.gif">';
    echo '<A href="next" onClick="return gotoMove(cur_move+1);">';
    echo '<IMG alt="" src="./chessset/h_forward.gif" border=0></A>';
    echo '<IMG width=2 height=2 alt="" src="./chessset/spacer.gif">';
    echo '<A href="last" onClick="return gotoMove(move_count-1);">';
    echo '<IMG alt="" src="./chessset/h_last.gif" border=0></A>';
    echo '</P>';
}

/* Render move history and chessmen difference.
* $list: w1,b1,w2,b2,... 
* If $browsing is set ignore $diff (create empty slots instead) and show full 
* history with javascript links. Otherwise show only few last moves. */
function renderHistory($list, $diff, $browsing)
{
    if (count($list) == 0)
        return;
    for ($i = 0; $i < 15; $i++)
        echo '<div style="display:none"><IMG name="tslot' . $i .
            '" src="./chessset/sempty.gif"></div>';
}

/* Render chess board.
* $board: 1dim chess board (a1=0,...,h8=63) with color/chessmen ('bQ','wP',...)
* $pc: playercolor ('w' or 'b' or empty)
* $active: may move (add javascript calls for command assembly)
* If $board is null create empty board for history browser.
*/

function renderBoard($board, $pc, $active)
{
    global $theme;

    /* show white at bottom if not playing */
    if (empty($pc))
        $pc = 'w';

    /* build chessboard */
    echo '<TABLE class="boardFrame"><TR><TD>';
    echo '<TABLE class="board" cellpadding="0" cellspacing="0">';
    if ($pc == 'w') {
        $index = 56;
        $pos_change = 1;
        $line_change = -16;
    } else {
        $index = 7;
        $pos_change = -1;
        $line_change = 16;
    }
    for ($y = 0; $y < 9; $y++) {
        echo '<TR>';
        for ($x = 0; $x < 9; $x++) {
            if ($y == 8) {
                /* number at bottom */
                if ($x > 0) {
                    if ($pc == 'w')
                        $c = chr(96 + $x);
                    else
                        $c = chr(96 + 9 - $x);
                    echo '<TD align="center"><IMG height=4 src="./chessset/spacer.gif"><BR><B class="boardCoord">' .
                        $c . '</B></TD>';
                } else
                    echo '<TD></TD><TD></TD>';
            } else
                if ($x == 0) {
                    /* number on the left */
                    if ($pc == 'w')
                        $i = 8 - $y;
                    else
                        $i = $y + 1;
                    echo '<TD><B class="boardCoord">' . $i .
                        '</B></TD><TD><IMG width=4 src="./chessset/spacer.gif"></TD>';
                } else {
                    /* normal tile */
                    if ($board) {
                        $entry = $board[$index];
						//echo $entry;
                        $color = substr($entry, 0, 1);
						if( !isset($entry[1]) ) $entry[1] = '' ;
						// echo $entry[1];
                        $name = strtolower(getCMName($entry[1]));
                    }
                    if ((($y + 1) + ($x)) % 2 == 0)
                        $class = 'boardTileWhite';
                    else
                        $class = 'boardTileBlack';
                    if ($board == null) {
                        echo '<TD class="' . $class . '"><IMG name="b' . $index .
                            '" src="./chessset/empty.gif"></TD>';
                    } else
                        if ($name != 'empty') {
                            if ($active) {
                                if ($pc != $color)
                                    $cmdpart = sprintf('x%s', i2bc($index));
                                else
                                    $cmdpart = sprintf('%s%s', $board[$index][1], i2bc($index));
                                echo '<TD id="btd' . $index . '" class="' . $class .
                                    '"><A href="" onClick="return assembleCmd(\'' . $cmdpart . '\');"><IMG border=0 src="./chessset/' .
                                    $color . $name . '.gif"></A></TD>';
                            } else
                                echo '<TD class="' . $class . '"><IMG src="./chessset/' . $color .
                                    $name . '.gif"></TD>';
                        } else {
                            if ($active) {
                                $cmdpart = sprintf('-%s', i2bc($index));
                                echo '<TD id="btd' . $index . '" class="' . $class .
                                    '"><A href="" onClick="return assembleCmd(\'' . $cmdpart . '\');"><IMG border=0 src="./chessset/empty.gif"></A></TD>';
                            } else
                                echo '<TD class="' . $class .
                                    '"><IMG src="./chessset/empty.gif"></TD>';
                        }
                        $index += $pos_change;
                }
        }
        $index += $line_change;
        echo "</TR>";
    }
    echo "</TABLE></TD></TR></TABLE>";
}
?>