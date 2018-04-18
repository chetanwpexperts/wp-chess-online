<?php 
include realpath(dirname(__FILE__) . '/..'). "/occ_mod/misc.php";
function ioSaveGame($game, $gameID){

	$hfile = fopen(realpath(dirname(__FILE__) . '/..') ."/occ_mod/data/games/$gameID", 'w');
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

function ioCreateGame($white, $black)

{

	$game = array();

	$game['ts_start'] = time();

	$game['white'] = $white;

	$game['black'] = $black;

	$game['curmove'] = 0;

	$game['curplyr'] = 'w';

	$game['curstate'] = '?';

	$game['wcs'] = 1;

	$game['wcl'] = 1;

	$game['bcs'] = 1;

	$game['bcl'] = 1;

	$game['w2spm'] = 'x';

	$game['b2spm'] = 'x';

	$game['lastmove'] = 'x';

	$game['lastkill'] = 'x';

	$game['oscf'] = 'x';

	$game['olcf'] = 'x';

	$game['board'] = array('wR', 'wN', 'wB', 'wQ', 'wK', 'wB', 'wN', 'wR', 'wP',

		'wP', 'wP', 'wP', 'wP', 'wP', 'wP', 'wP', '', '', '', '', '', '', '', '', '', '',

		'', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',

		'', '', 'bP', 'bP', 'bP', 'bP', 'bP', 'bP', 'bP', 'bP', 'bR', 'bN', 'bB', 'bQ',

		'bK', 'bB', 'bN', 'bR');



	$gameFileName = sprintf('%s-%s-%s-', date('YmdHi', $game['ts_start']), $white, $black);

	ioSaveGame($game, $gameFileName);

	return $gameFileName;

}


class chess_template{
	var $prefix;
	var $membership;
	var $chess_table;
	var $chess_stats;
	var $ongoing_games;
	var $offer_draw;
	var $pgn;
	var $blogid;
	
	public function __construct(){
		$this->prefix = "chess_";
		$this->membership = $this->prefix."membership";
		$this->chess_table = $this->prefix."table";
		$this->chess_stats = $this->prefix."stats";
		$this->ongoing_games = $this->prefix."ongoing_games";
		$this->offer_draw = $this->prefix."offer_draw";
		$this->pgn = $this->prefix."pgn";
		$this->blog_id = get_current_blog_id();
	}
	
	function getopponentemail($opponentid){
		$opponent = get_userdata($opponentid);
		return $opponent->user_email;
	}
	
	function hostemail($hostid){
		$host = get_userdata($hostid);
		return $host->user_email;
	}
	
	function sendemail($hostid, $opponentid, $arg){
		global $wpdb;
		$email_to = $this->hostemail($hostid);
		
		// Create the message body and subject
		$to = $email_to;  // give to email address
		$subject = 'Your game started Now';  //change subject of email
		
		$admin_email = get_option('admin_email');
		$from    = '< '.$admin_email.' >';     // give from email address

		// mandatory headers for email message, change if you need something different in your setting.
		$headers  = "From: " . $from . "\r\n";
		$headers .= "Reply-To: ". $from . "\r\n";
		$headers .= "CC: test@neowebsolution.com\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		$url = array();
		ob_start();
		$host_name = get_userdata($hostid);
		$opponent_name = get_userdata($opponentid);
		
		// new funciton 
		
			$username = $host_name->user_login;
				
			//create and start chess table here
			$host = $username;
			$hostColor = 'white';
			$timeStamp = $arg['timestamp'];
			//can't play game hosted by member themself
			$whitePlayer = $host;
			$blackPlayer = $opponent_name->user_login;
			
			$hostTurn = 1;
			$urTurn = 0;

			$gameID = ioCreateGame($whitePlayer, $blackPlayer);

			//used to show ongoing games
			
			$wpdb->insert( $wpdb->prefix .'ongoing_games', 
				array( 
					'username' => $username,
					'opponents' => $blackPlayer,
					'gameid' => $gameID, 
					'host' => $host, 
					'white' => $whitePlayer, 
					'urturn' => $hostTurn
				), 
				array( 
					'%s', 
					'%s', 
					'%s',
					'%s',
					'%s',
					'%d'
				) 
			);
			
			$wpdb->insert( $wpdb->prefix .'ongoing_games', 
				array( 
					'username' => $blackPlayer,
					'opponents' => $username,
					'gameid' => $gameID, 
					'host' => $host, 
					'white' => $blackPlayer, 
					'urturn' => $urTurn
				), 
				array( 
					'%s', 
					'%s', 
					'%s',
					'%s',
					'%s',
					'%d'
				) 
			);

			//delete chess table

			$wpdb->delete( $wpdb->prefix .'table', array( 'host' => $host,  'timestamp' => $timeStamp, 'type' => "host") );
			$wpdb->delete( $wpdb->prefix .'table', array( 'host' => $host,  'timestamp' => $timeStamp, 'type' => "opponent") );
			
			$location = get_site_url()."/play-online/?act=play_chess&{$hostColor}=".$host."&host=".$host."&gid=".$gameID;
	
		///end new funciton
		
		$url['url'] = get_site_url()."/play-online/?act=join_chess_table&host={$host_name->user_login}&color=white&time_stamp={$arg['timestamp']}";
		$url['url'] = get_site_url().'/wp-login.php?redirect_to='.urlencode($location);
		
		include( plugin_dir_path( __FILE__ ) . 'email.php');
		
		$content = ob_get_clean();
		$message = $content;
		
		// Sending mail
		$retrn = wp_mail( $to, $subject, $message, $headers);
		
		$to = 	$this->getopponentemail($opponentid);
		$url = array();
		ob_start();
		
		$url['url'] = get_site_url()."/play-online/?act=join_chess_table&host={$host_name->user_login}&color=black&time_stamp={$arg['timestamp']}";
		$url['url'] = get_site_url().'/wp-login.php?redirect_to='.urlencode($location);
		
		include( plugin_dir_path( __FILE__ ) . 'email.php');
		
		$content = ob_get_clean();
		$message = $content;
		
		// Sending mail
		$retrn = wp_mail( $to, $subject, $message, $headers);
		
		
		if($retrn){
			return true;
		}else{
			return false;
		}
	}
	
	function creategame($submit_action, $postdata){
		global $wpdb;
		if (isset($submit_action)) {
			$colorSide = sprintf('%d', $postdata['color']);
			//0 = random, 1 = white, 2 = black
			if ($colorSide == 0) {
				//if random is chosen, assign either white or black
				$colorSide = mt_rand(1, 2);
				// insert query 
				$arg = array( 
						'username' => $postdata['host'],
						'host' => $postdata['opponent'], 
						'opponent' => $postdata['opponent'], 
						'color' => '1',						
						'timestamp' => time(), 
						'started_date' => $postdata['start_date'], 
						'end_date' => $postdata['end_date'],
						'type' => 'host'
					);
				$wpdb->insert( 
					$this->chess_table, 
					$arg, 
					array( 
						'%d', 
						'%d', 
						'%d',
						'%d',
						'%d',
						'%s',
						'%s',
						'%s'
					) 
				);
				
				$arg2 = array( 
						'username' => $postdata['opponent'],
						'host' => $postdata['host'],
						'opponent' => $postdata['host'],
						'color' => '2',						
						'timestamp' => time(), 
						'started_date' => $postdata['start_date'], 
						'end_date' => $postdata['end_date'],
						'type' => 'opponent'
					);
				$wpdb->insert( 
					$this->chess_table, 
					$arg2, 
					array( 
						'%d', 
						'%d',
						'%d',
						'%d',
						'%d',
						'%s',
						'%s',
						'%s'
					) 
				);
				$mailsent = $this->sendemail($postdata['host'], $postdata['opponent'], $arg);
				if($mailsent){
					return true;
				}else{
					return false;
				}
			}
		}
	}
	
	function get_all_users(){
		$blogusers = get_users( 'blog_id='.$this->blog_id.'&orderby=nicename&role=subscriber' );
		ob_start();
		foreach($blogusers as $users){
		?>
		<option value="<?php echo $users->ID;?>"><?php echo $users->display_name;?></option>
		<?php 
		}
		$option = ob_get_clean();
		return $option;
	}
}
?>