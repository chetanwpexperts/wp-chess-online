<?php
if(is_user_logged_in()){
	$current_user = wp_get_current_user();
	$username = $current_user->user_login;
	
	$player = explode('-',$_GET['gid']);

	$opponent = ($player[1] == $username) ? $player[2] : $player[1];
}else{
	wp_redirect(site_url());
}
?>
<div style="text-align:center"> You are <?php echo $username;?></div>
<br>

<table align="center">
 <tr><td width="406"><b>Chess Board</b></td><td width="50"></td><td width="406"><b>History Moves Replay</b></td></tr>
 <tr>
  <td valign="top">
  <iframe id="myiframe" style="width:415px;height:465px" scrolling="no" frameborder="0" src="<?php echo plugins_url();?>/wp_chess_online/occ_mod/board.php?member=<?php echo $username;?>&opponent=<?php echo $opponent;?>&wp=<?php echo $_GET['color'];?>&host=<?php echo $_GET['host'];?>&gid=<?php echo $_GET['gid'];?>"></iframe>
  </td>
  <td width="50"></td>
  <td valign="top">   
  <iframe id="myiframe2" style="width:415px;height:465px" scrolling="no" frameborder="0" src="<?php echo plugins_url();?>/wp_chess_online/occ_mod/board.php?member=<?php echo $username;?>&browse=1&rotate=1&gid=<?php echo $_GET['gid'];?>"></iframe>
    
  </td>
 </tr>
</table>

<br>

<table align="center" cellpadding="0" cellspacing="0"><tr><td valign="middle"><input type="button" style="width:100px;height:22px" value="Offer Draw" onclick="alert('implement offer draw here');"></td><td width="50"></td><td valign="middle"><input type="button" style="width:100px;height:22px" value="Surrender" onclick="alert('implement surrender here');"></td></tr></table>

<br>

<div style="text-align:center">Your Opponent: <?php echo $opponent;?>. Rating: Win: <?php echo $oppWin;?> | Loss: <?php echo $oppLoss;?> | Draw: <?php echo $oppDraw;?></div>

<script>
function refresh_Game(){
	document.getElementById('myiframe').src = document.getElementById('myiframe').src
	document.getElementById('myiframe2').src = document.getElementById('myiframe2').src
	
}
</script>