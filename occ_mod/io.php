<?php
/* Input/output functions to access backend data. */

/* XXX Abstraction is not too good right now. E.g., the p_* variables are
* not loaded but set by interpreting game data related to user id. Same 
* with timestamp in saving. If finally a database should be allowed as
* backend separation must be more accurate, e.g., via raw_io_* functions
* that really just convert backend data to abstract format without 
* interpreting anything. */

include_once ('misc.php');

/* Data locations */
$res_games = 'data/games';

/* Sender email address for notifications */
$mail_from = '';

/* Lock/unlock IO access via locking file. Locking is tried for two seconds
* then the locking file is replaced. This is to prevent deadlocks through
* broken sessions and should be safe since no action will take that long. 
* Locking will only be done for write access. Thus there is an infinitesimal
* chance that a read will result corrupted data (when coincidending with a
* write access). */
$ioref = 0;
/* Reset IO lock reference counter on page reload. */
function ioLock()
{
    global $ioref;

    if ($ioref++ == 0) {
        $attempts = 0;
        while (($hfile = fopen('tmp/iolock', 'x')) === false) {
            usleep(100000);
            if (++$attempts == 20)
                break;
        }
        if ($hfile)
            fclose($hfile);
    }
}
function ioUnlock()
{
    global $ioref;

    if ($ioref == 0)
        return;
    if (--$ioref == 0)
        unlink('tmp/iolock');
}

/* Load a game (try active games first, then archived games) and set various
* user-based variables, too. Return game as array or NULL on error.
* Static (loaded) entries:
* archived: resides in archive
* ts_start: timestamp of starting date (secs)
* ts_last: timestamp of last move (secs)
* white: name of white player
* black: name of black player
* curmove: number of current move (start at 0)
* curplyr: color of current player (w or b)
* curstate: state of game (w/b=white/black won,-=draw,D=draw offered,?=open)
* wcs: white may castle short
* wcl: white may castle long
* bcs, bcl: dito for black
* w2spm: 2-step pawn move of white (x or a-h)
* b2spm: dito for black
* lastmove: last move in full notation (e.g. Pd2-d4 or x)
* lastkill: chessman captured in last move with board index (e.g. wP08 or x)
* oscf: old short castling flag (only set by king/rook move)
* olcf: dito for long castling
* board: chess board array (0=a1,63=h8) with e.g. 'bP', 'wQ' or ''
* mhistory: move history list (w1,b1,w2,b2,...)
* chatter: list of chatter lines (first is newest)
* Dynamic (based on user id) entries:
* p_maymove: whether it's player's turn (always 0 if user is not playing)
* p_mayundo: player may undo last move
* p_mayabort: player may abort game (first move or opponent took too long) 
* p_mayarchive: player may move game to archive
* p_color: player color (w=white,b=black or empty if not playing)
* p_opponent: name of opponent (based on player color, empty if not playing)
*/
function ioLoadGame($gid, $uid)
{
    global $res_games, $res_archive;

    $game = array();

    /* Load raw game data */
    if (file_exists("$res_games/$gid")) {
        $raw = file("$res_games/$gid");
        $game['archived'] = 0;
    } else
        if (file_exists("$res_archive/$gid")) {
            $raw = file("$res_archive/$gid");
            $game['archived'] = 1;
        } else
            return null;

    /* Build time stamps */
    $aux = explode(' ', trim($raw[0]));
    $game['ts_start'] = mktime($aux[3], $aux[4], 0, $aux[1], $aux[2], $aux[0]);
    $game['ts_last'] = mktime($aux[8], $aux[9], 0, $aux[6], $aux[7], $aux[5]);

    /* Parse header */
    $hdr = explode(' ', trim($raw[1]));
    $game['white'] = $hdr[0];
    $game['black'] = $hdr[1];
    $game['curmove'] = $hdr[2];
    $game['curplyr'] = $hdr[3];
    $game['curstate'] = $hdr[4];
    $game['wcs'] = $hdr[5];
    $game['wcl'] = $hdr[6];
    $game['bcs'] = $hdr[7];
    $game['bcl'] = $hdr[8];
    $game['w2spm'] = $hdr[9];
    $game['b2spm'] = $hdr[10];
    $game['lastmove'] = $hdr[11];
    $game['lastkill'] = $hdr[12];
    $game['oscf'] = $hdr[13];
    $game['olcf'] = $hdr[14];

    /* Fill chess board */
    $game['board'] = array('', '', '', '', '', '', '', '', '', '', '', '', '', '',
        '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
        '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
        '', '', '', '', '', '', '', '', '', '');
    $wcm = explode(' ', trim($raw[2]));
    foreach ($wcm as $cm)
        $game['board'][bc2i($cm[1] . $cm[2])] = 'w' . $cm[0];
    $bcm = explode(' ', trim($raw[3]));
    foreach ($bcm as $cm)
        $game['board'][bc2i($cm[1] . $cm[2])] = 'b' . $cm[0];

    /* Get move history */
    $list = array();
    for ($i = 1, $j = 0; $i <= $game['curmove']; $i++, $j += 2) {
        $moves = explode(' ', trim($raw[3 + $i]));
        if (count($moves) > 1)
            $list[$j] = $moves[1];
        else
            $list[$j] = '???';
        if (count($moves) > 2)
            $list[1 + $j] = $moves[2];
        else
            if ($i < $game['curmove'])
                $list[1 + $j] = '???';
        /* Note: Possibly missing move of black in current move
        * is not set '' or something to easily allow undo of move
        * via unset(). '' or even null would be counted as existing
        * list item. */
    }
    $game['mhistory'] = $list;

    /* Get chatter */
    $game['chatter'] = array();
    for ($i = 4 + $game['curmove'], $j = 0; $i < count($raw); $i++, $j++)
        $game['chatter'][$j] = trim($raw[$i]);

    /* Determine color and opponent */
    if ($uid == $game['black']) {
        $game['p_color'] = 'b';
        $game['p_opponent'] = $game['white'];
    } else
        if ($uid == $game['white']) {
            $game['p_color'] = 'w';
            $game['p_opponent'] = $game['black'];
        } else {
            $game['p_color'] = '';
            $game['p_opponent'] = '';
        }

        /* Check whether player may move/archive */
        $game['p_maymove'] = 0;
    $game['p_mayarchive'] = 0;
    if (($game['curplyr'] == 'w' && $uid == $game['white']) || ($game['curplyr'] ==
        'b' && $uid == $game['black'])) {
        if ($game['curstate'] == '?' || $game['curstate'] == 'D')
            $game['p_maymove'] = 1;
        else
            if ($game['archived'] == 0)
                $game['p_mayarchive'] = 1;
    }

    /* Check whether player may abort */
    $game['p_mayabort'] = 0;
    if (!empty($game['p_color']) && $game['p_maymove'] == 0 && ($game['curstate'] ==
        'D' || $game['curstate'] == '?')
        /*&& time() - $game['ts_last'] > 2419200 four weeks*/ && $game['archived'] == 0)
        $game['p_mayabort'] = 1;
    else
        if (!empty($game['p_color']) && $game['archived'] == 0 && $game['p_maymove'] ==
            1 && (($game['p_color'] == 'w' && $game['curmove'] == 0) || ($game['p_color'] ==
            'b' && $game['curmove'] == 1)))
            $game['p_mayabort'] = 1;

    $game['p_mayundo'] = 0;

    return $game;
}

/* Load game info and return it as array. If $location is not null it is either
* 'opengames' or 'archive'. If set only that one location is checked. Return 
* null if error occured.
* archived: resides in archive
* ts_start: timestamp of starting date (secs)
* ts_last: timestamp of last move (secs)
* white: name of white player
* black: name of black player
* curmove: number of current move (start at 0)
* curplyr: color of current player (w or b)
* curstate: state of game (w/b=white/black won,-=draw,D=draw offered,?=open) */
function ioLoadGameInfo($location, $gid)
{
    global $res_games, $res_archive;

    $game = array();

    /* Load raw game data */
    if (($location == null || $location == 'opengames') && file_exists("$res_games/$gid")) {
        $raw = file("$res_games/$gid");
        $game['archived'] = 0;
    } else
        if (($location == null || $location == 'archive') && file_exists("$res_archive/$gid")) {
            $raw = file("$res_archive/$gid");
            $game['archived'] = 1;
        } else
            return null;
    /* NB: this is required since usort in loadInfos will destroy the index
    * key. Therefore each entry needs to know the game id. */
    $game['gid'] = $gid;

    /* Build time stamps */
    $aux = explode(' ', trim($raw[0]));
    $game['ts_start'] = mktime($aux[3], $aux[4], 0, $aux[1], $aux[2], $aux[0]);
    $game['ts_last'] = mktime($aux[8], $aux[9], 0, $aux[6], $aux[7], $aux[5]);

    /* Parse header */
    $hdr = explode(' ', trim($raw[1]));
    $game['white'] = $hdr[0];
    $game['black'] = $hdr[1];
    $game['curmove'] = $hdr[2];
    $game['curplyr'] = $hdr[3];
    $game['curstate'] = $hdr[4];

    return $game;
}

/* Load array of game infos according to the given filter criteria. null for a
* criteria means any value. $location must be set either 'opengames' or 
* 'archive'.
* Always returns an array but it may be empty if no matching games were found 
* and entries are sorted most recent first. */
function compareTimestamp($a, $b)
{
    if ($a['ts_last'] == $b['ts_last'])
        return 0;
    else
        if ($a['ts_last'] < $b['ts_last'])
            return 1;
        else
            return - 1;
}
function ioLoadGameInfoList($location, $player, $pcolor, $opponent)
{
    global $res_games, $res_archive;

    if ($location == 'archive')
        $dirname = $res_archive;
    else
        $dirname = $res_games;
    $infos = array();

    $hdir = opendir($dirname);
    while ($entry = readdir($hdir)) {
        if ($entry == '.' || $entry == '..')
            continue;
        if ($player != null || $opponent != null) {
            if ($opponent != null && strpos($entry, '-' . $opponent . '-') === false)
                continue;
            if ($player != null) {
                if (strpos($entry, '-' . $player . '-') === false)
                    continue;
                if ($pcolor != null) {
                    $fields = explode('-', $entry);
                    if ($pcolor == 'w' && $fields[1] != $player)
                        continue;
                    else
                        if ($pcolor == 'b' && $fields[2] != $player)
                            continue;
                }
            }
        }
        $infos[$entry] = ioLoadGameInfo($location, $entry);
    }
    closedir($hdir);
    /* NB: sort will destroy index key, therefore $infos['gid'] is used
    * later instead. */
    if (count($infos) > 0)
        usort($infos, 'compareTimestamp');
    return $infos;
}

/* Save an open game and update last move time stamp to current time. Chatter
* must have been updated already (newest is first in list). */
function ioSaveGame($game, $gid)
{
    global $res_games;

    $hfile = fopen("$res_games/$gid", 'w');

    fwrite($hfile, date('Y m d H i ', $game['ts_start']));
    fwrite($hfile, date('Y m d H i', time()));
    fwrite($hfile, "\n");

    fwrite($hfile, $game['white'] . ' ' . $game['black'] . ' ');
    fwrite($hfile, $game['curmove'] . ' ' . $game['curplyr'] . ' ');
    fwrite($hfile, $game['curstate'] . ' ' . $game['wcs'] . ' ' . $game['wcl'] . ' ');
    fwrite($hfile, $game['bcs'] . ' ' . $game['bcl'] . ' ' . $game['w2spm'] . ' ');
    fwrite($hfile, $game['b2spm'] . ' ' . $game['lastmove'] . ' ');
    fwrite($hfile, $game['lastkill'] . ' ' . $game['oscf'] . ' ');
    fwrite($hfile, $game['olcf'] . "\n");

    for ($i = 0; $i < 64; $i++)
        if ($game['board'][$i] != '' && $game['board'][$i][0] == 'w') {
            $c = i2bc($i);
            fwrite($hfile, $game['board'][$i][1] . $c . ' ');
        }
    fwrite($hfile, "\n");
    for ($i = 0; $i < 64; $i++)
        if ($game['board'][$i] != '' && $game['board'][$i][0] == 'b') {
            $c = i2bc($i);
            fwrite($hfile, $game['board'][$i][1] . $c . ' ');
        }
    fwrite($hfile, "\n");

    for ($i = 0, $j = 1; $i < count($game['mhistory']); $i += 2, $j++)
        fwrite($hfile, $j . ' ' . $game['mhistory'][$i] . ' ' . $game['mhistory'][$i + 1] .
            "\n");
    fclose($hfile);


}

/* Archive game if user is allowed to and return a status message.
* XXX no locking since only one user may archive and won't do it twice
* the same time... */
function ioArchiveGame($gid, $uid)
{
    /*
    global $res_games, $res_archive;

    $game = ioLoadGame($gid, $uid);
    if ($game == null)
    return 'ERROR: Game not found!';
    if (!$game['p_mayarchive'])
    return 'ERROR: You cannot archive the game!';
    rename("$res_games/$gid", "$res_archive/$gid");
    clearstatcache();
    return 'Game has been archived.';
    */
}

/* Abort an open game. This is only possible if your opponent did not move
* at all yet or did not move for more than four weeks. Aborting a game will
* have NO influence on the game statistics. Return a status message. */
function ioAbortGame($gid, $uid)
{
    /*
    global $res_games;

    ioLock();

    $game = ioLoadGame($gid, $uid);
    if ($game == null)
    return 'ERROR: Game does not exist!';
    if (!$game['p_mayabort'])
    return 'ERROR: You cannot abort the game!';
    unlink("$res_games/$gid");

    ioUnlock();

    return 'Game deleted.';
    */
}
function ioLoadUserTheme($uid)
{
    //return 'default';
    /* XXX always default for now */
}
?>