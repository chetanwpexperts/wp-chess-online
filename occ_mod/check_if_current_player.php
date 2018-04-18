<?php
include_once ('../../settings/global_settings.inc.php');
include_once ('../../settings/db_config.inc.php');
include_once ('../../components/util/function.php');	

$sql = mysqli_query($conn, "SELECT * FROM {$tablePrefix}_viewongoingbg WHERE memberid='{$_SESSION['memberid']}' AND gid='{$_SESSION['gid']}' AND urturn=1", $conn) or q_die(__file__, __line__, $conn);

if(mysqli_num_rows($sql) == 0){
    $not_player_turn = true;
}else{
    $not_player_turn = false;
}
?>