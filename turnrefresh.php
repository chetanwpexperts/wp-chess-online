<?php 
require_once('../../../wp-load.php' );
$current_user = wp_get_current_user();
$gmaid = $_POST['gid'];
if(function_exists('refreshiframeaftermove')){
	echo refreshiframeaftermove($gmaid, $current_user->user_login);
}
?>