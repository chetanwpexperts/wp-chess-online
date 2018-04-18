<?php
global $wpdb;

$current_user = wp_get_current_user();

$username = $current_user->user_login;
$qry = "select * from ".$wpdb->prefix."ongoing_games where opponents='".$_GET['host']."' order by id desc limit 0,1";
$rowdata = $wpdb->get_row($qry);
print_r($rowdata);
?>