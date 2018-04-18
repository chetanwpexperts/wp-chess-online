<script src="//code.jquery.com/jquery-1.11.3.min.js"></script> 
<?php
require_once('../../../../wp-load.php' );
global $wpdb;

$current_user = wp_get_current_user();
$username = $current_user->user_login;

$uid = $username;
$gid = $_GET['gid'];
$host = $_GET['host'];
$opponent = $_GET['opponent'];

$whitePlayer = $_GET['wp'];
//find black player
$blackPlayer = ($whitePlayer == $username) ? $opponent : $wPlayer;

$currentTimeStamp = time();

/* Check game id */
/*if (isset($_POST['gid']))
$gid = $_POST['gid'];
else
if (isset($_GET['gid']))
$gid = $_GET['gid'];
if (preg_match('/[^\w\-\.]/', $gid))
$gid = null;*/

 $browse = "";
/* Browser variables */
if (isset($_GET['browse']))
    $browse = $_GET['browse'];
if (isset($_GET['rotate']) && $_GET['rotate'] == 0)
    $rotate = 1;
else
    $rotate = 0;

/* Includes */
include 'misc.php';
include 'io.php';
include 'render.php';
include 'chess.php';

/* Check posted command. The POST name is always 'cmd' in any formular.*/

if (!$browse && !empty($_POST['cmd'])) {

    //$gid = inputFilter($_POST['gid']);
    $currentTimeStamp = time();
    $cmd = $_POST['cmd'];
    $cmdres = '';

    //$sql = mysqli_query($conn,("SELECT gameid FROM chess_offer_draw WHERE gameid='$gid'") or die(mysqli_error($conn));
    //if (mysql_num_rows($sql) >= 1)exit('pending draw acceptance');

    ioLock();
    $cmdres = handleMove($gid, $uid, $cmd);
    ioUnlock();

    if (!strstr($cmdres, 'ERROR')) {
		
		$wpdb->update( 
			$wpdb->prefix . 'ongoing_games', 
			array( 
				'urturn' => '1',  // string
			), 
			array( 'gameid' =>  $gid)
		);

		$wpdb->update( 
			$wpdb->prefix . 'ongoing_games', 
			array( 
				'urturn' => '0',  // string
			), 
			array( 'username' => $username, 'gameid' =>  $gid)
		);
    }

    if (strstr($cmdres, 'CHECKMATE')) {
        //record PGN
        include ('pgnformat.php');
		
		$wpdb->insert( $wpdb->prefix .'pgn', 
			array( 
				'username' => $username,
				'opponents' => $opponent, 
				'hd' => $pgnStrHeader, 
				'mv' => $pgnStr, 
				'gameid' => $gid
			), 
			array( 
				'%s', 
				'%s', 
				'%s',
				'%s',
				'%s'
			) 
		);
		
		$wpdb->insert( $wpdb->prefix .'pgn', 
			array( 
				'username' => $opponent,
				'opponents' => $username, 
				'hd' => $pgnStrHeader, 
				'mv' => $pgnStr, 
				'gameid' => $gid
			), 
			array( 
				'%s', 
				'%s', 
				'%s',
				'%s',
				'%s'
			) 
		);
        //END record PGN
		
		$wpdb->insert( $wpdb->prefix .'stats', 
			array( 
				'username' => $username,
				'win' => 1, 
				'loss' => 0, 
				'draw' => 0
			), 
			array( 
				'%s', 
				'%d',
				'%d',
				'%d'
			) 
		);
		$wpdb->insert( $wpdb->prefix .'stats', 
			array( 
				'username' => $opponent,
				'win' => 0, 
				'loss' => 1, 
				'draw' => 0
			), 
			array( 
				'%s', 
				'%d',
				'%d',
				'%d'
			) 
		);
        
		$wpdb->delete( $wpdb->prefix . 'ongoing_games', array( 'gameid' => $gid ) );
    }
    if (strstr($cmdres, 'STALEMATE')) {
        //record PGN
        include ('pgnformat.php');
		
		$wpdb->insert( $wpdb->prefix .'pgn', 
			array( 
				'username' => $username,
				'opponents' => $opponent, 
				'hd' => $pgnStrHeader, 
				'mv' => $pgnStr, 
				'gameid' => $gid
			), 
			array( 
				'%s', 
				'%s', 
				'%s',
				'%s',
				'%s'
			) 
		);
		
		$wpdb->insert( $wpdb->prefix .'pgn', 
			array( 
				'username' => $opponent,
				'opponents' => $username, 
				'hd' => $pgnStrHeader, 
				'mv' => $pgnStr, 
				'gameid' => $gid
			), 
			array( 
				'%s', 
				'%s', 
				'%s',
				'%s',
				'%s'
			) 
		);

        //END record PGN
		
		$wpdb->insert( $wpdb->prefix .'stats', 
			array( 
				'username' => $username,
				'win' => 0, 
				'loss' => 0, 
				'draw' => 1
			), 
			array( 
				'%s', 
				'%d',
				'%d',
				'%d'
			) 
		);
		$wpdb->insert( $wpdb->prefix .'stats', 
			array( 
				'username' => $opponent,
				'win' => 0, 
				'loss' => 0, 
				'draw' => 1
			), 
			array( 
				'%s', 
				'%d',
				'%d',
				'%d'
			) 
		);
        
		$wpdb->delete( $wpdb->prefix . 'ongoing_games', array( 'gameid' => $gid ) );

    }
}

/* Load game */
$game = ioLoadGame($gid, $uid);

/* Force browsing mode for archived games.
if ($game['archived'] && !$browse) {
$browse = 1;
$rotate = 0;
}
*/
/* Get mode depending javascript */
if ($browse) {
    if ($uid == $game['white'])
        $pcolor = 'w';
    else
        $pcolor = 'b';
    if ($rotate) {
        if ($pcolor == 'w')
            $pcolor = 'b';
        else
            $pcolor = 'w';
    }
    include 'browser.php';
} else {
    include 'board.js';
}


/* Build page */
$links = array();
// $links['Overview'] = 'index.php';
if ($browse) {
    if (!$game['archived'])
        $links['Input Mode'] = 'board.php?gid=' . $gid;
    $links['Rotate Board'] = 'board.php?gid=' . $gid . '&browse=1&rotate=' . $rotate;
} else {
    if ($game['curmove'] > 0)
        $links['Browsing Mode'] = 'board.php?gid=' . $gid . '&browse=1&rotate=1';
}


renderPageBegin('boardPageTable');

echo '<TABLE width="345px" cellspacing=0 cellpadding=0><TR><TD valign="top">';
if ($browse)
    renderBoard(null, $pcolor, null);
else
    renderBoard($game['board'], $game['p_color'], $game['p_maymove'], 0);

echo '<IMG src="./chessset/spacer.gif" width=10></TD>';
//echo '<TD rowspan=2><IMG width=10 alt="" src="../../media/img/chessset/spacer.gif">SSS</TD>';
echo '<TR><TD width=0 valign="top">';
if ($browse) {
    renderBrowserForm($game);
    renderHistory($game['mhistory'], null, 1);
} else {
    renderCommandForm($game, $cmdres, $move);
    renderHistory($game['mhistory'], getCMDiff($game['board']), 0);
    /*if ((strstr($cmdres, 'CHECKMATE')) || (strstr($cmdres, 'STALEMATE'))){
    @unlink("../../games/chess/vasx9i0b2w/games/$gid");
    }*/
}
echo '</TD></TR>';

echo '</TABLE>';
echo '</BODY></HTML>';
if ($browse)
    echo '<script language="Javascript">gotoMove(0);gotoMove(move_count-1);renderBoard();</script>';


	 $qry = "select urturn, username from ".$wpdb->prefix."ongoing_games WHERE username = '".$username."' AND gameid='".$_GET['gid']."'";
			$rowdata = $wpdb->get_row($qry);
			
			?>
			<script>
			var reload_session ;
			var filepath = "<?php echo plugins_url();?>/wp_chess_online/turnrefresh.php";
			$(document).ready(function(){
				reload_session =  setInterval(function(){
					$.post(filepath, { gid:"<?php echo $_GET['gid'];?>" },function(res){
					 <?php  
						if($rowdata->urturn){
							echo "clearInterval(reload_session);";
							echo 'return false;';
						}  
					?>
					if(res == 1){
						clearInterval(reload_session);
						//location.reload();
						 window.parent.refresh_Game();
						//
					}
					  
					})
					
				}, 3000);
			});
			</script> 	

