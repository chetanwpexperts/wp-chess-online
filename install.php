<?php
/**
 * Play Chess Online installer function
 *
 * @author webexe
 */
Class Installation {
	
	function activate() {
		
		$new_page_title = 'Play Online';
        $new_page_content = '';
        $new_page_template = 'onlinechess-template.php'; //ex. template-custom.php. Leave blank if you don't want a custom page template.

        //don't change the code bellow, unless you know what you're doing

        $page_check = get_page_by_title($new_page_title);
        $new_page = array(
                'post_type' => 'page',
                'post_title' => $new_page_title,
                'post_content' => $new_page_content,
                'post_status' => 'publish',
                'post_author' => 1,
        );
        if(!isset($page_check->ID)){
                $new_page_id = wp_insert_post($new_page);
                if(!empty($new_page_template)){
                        update_post_meta($new_page_id, '_wp_page_template', $new_page_template);
                }
        }
		
		global $wpdb;
		$membership = $wpdb->prefix."membership";
		$chess_table = $wpdb->prefix."table";
		$chess_stats = $wpdb->prefix."stats";
		$ongoing_games = $wpdb->prefix."ongoing_games";
		$offer_draw = $wpdb->prefix."offer_draw";
		$pgn = $wpdb->prefix."pgn";
		
		$tbA = "CREATE TABLE ".$membership." (
		  id INT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		  username varchar(255) NOT NULL UNIQUE default '0',
		  pwrd varchar(255) NOT NULL,
		  PRIMARY KEY(id))ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
		$wpdb->query($tbA);
		
		$tbB = "CREATE TABLE ".$chess_table." (
		  id INT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		  username varchar(255) NOT NULL default '',
		  host varchar(100) NOT NULL default '0',
		  opponent varchar(100) NOT NULL default '0',
		  color TINYINT(1) UNSIGNED NOT NULL default 0,
		  timestamp INT(20) UNSIGNED NOT NULL default 0,
		  started_date VARCHAR(100) NOT NULL default '',
		  end_date VARCHAR(100) NOT NULL default '',
		  type VARCHAR(100) NOT NULL default '',
		  PRIMARY KEY(id),
		  INDEX(username,host,opponent))ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		  ";
		$wpdb->query($tbB);
		
		$tbC = "CREATE TABLE ".$chess_stats." (
		  id INT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		  username varchar(255) NOT NULL default '',
		  win INT(20) UNSIGNED NOT NULL default 0,
		  loss INT(20) UNSIGNED NOT NULL default 0,
		  draw INT(20) UNSIGNED NOT NULL default 0,
		  PRIMARY KEY(id))ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
		$wpdb->query($tbC);
		
		$tbD = "CREATE TABLE ".$ongoing_games." (
		  id INT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		  username varchar(255) NOT NULL default '',
		  opponents varchar(24) DEFAULT NULL,
		  gameid varchar(255) NOT NULL DEFAULT '0',
		  host varchar(24) DEFAULT NULL,
		  white varchar(24) DEFAULT NULL,
		  urturn TINYINT(1) UNSIGNED DEFAULT NULL,
		  PRIMARY KEY(id),
		  INDEX(gameid))ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
		$wpdb->query($tbD);
		
		$tbE = "CREATE TABLE ".$offer_draw." (
		  id INT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		  offered_by varchar(24) NOT NULL default '0',
		  gameid varchar(255) NOT NULL default '0',
		  PRIMARY KEY(id),
		  INDEX(gameid))ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
		$wpdb->query($tbE);
		
		$tbF = "CREATE TABLE ".$pgn." (
		  id INT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		  username varchar(255) DEFAULT NULL,
		  opponents varchar(255) DEFAULT NULL,
		  hd text default NULL,
		  mv longtext default NULL,
		  gameid varchar(255) NOT NULL default '',
		  PRIMARY KEY(id),
		  INDEX(gameid))ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
		$wpdb->query($tbF);

	}	
	/**Drop tables from this plugin on deactivaion of plugin*/
	Static function deactivate() {
		global $wpdb;		
		// tables
		$membership = $wpdb->prefix."membership";
		$chess_table = $wpdb->prefix."table";
		$chess_stats = $wpdb->prefix."stats";
		$ongoing_games = $wpdb->prefix."ongoing_games";
		$offer_draw = $wpdb->prefix."offer_draw";
		$pgn = $wpdb->prefix."pgn";
		
		// queries
		$tba = "DROP TABLE IF EXISTS $membership";
		$tbb = "DROP TABLE IF EXISTS $chess_table";
		$tbc = "DROP TABLE IF EXISTS $chess_stats";
		$tbd = "DROP TABLE IF EXISTS $ongoing_games";
		$tbe = "DROP TABLE IF EXISTS $offer_draw";
		$tbf = "DROP TABLE IF EXISTS $pgn";
		
		//execute the all queries
		$wpdb->query($tba);
		$wpdb->query($tbb);
		$wpdb->query($tbc);
		$wpdb->query($tbd);
		$wpdb->query($tbe);
		$wpdb->query($tbf);
	}
}
