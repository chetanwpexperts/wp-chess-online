<?php
global $wpdb;
$qry = "SELECT * FROM $creategame->chess_table";
$gamedata = $wpdb->get_results($qry);
?>
<div class="CSSTableGenerator" >
	<table>
		<tr>
			<td>Host</td>
			<td>Opponent</td>
			<td>Chess Color</td>
			<td>Started</td>
			<td>End On</td>
			<td>Action</td>
		</tr>
		<?php 
		foreach($gamedata as $activegame){
			if($activegame->color == 1){
				$color = 'White';
			}else{
				$color = 'Black';
			}
			
			$host = get_userdata($activegame->host);
			$opponent = get_userdata($activegame->username);
			
			$getcolor = ($activegame->color == 1) ? "white" : "black"; 
			
			$playURL = get_site_url()."/play-online/?act=join_chess_table&host={$host->user_login}&color={$getcolor}&time_stamp={$activegame->timestamp}";
			
		?>
		<tr>
			<td><?php echo $host->display_name; ?></td>
			<td><?php echo $opponent->display_name; ?></td>
			<td><?php echo $color; ?></td>
			<td><?php echo $activegame->started_date;?></td>
			<td><?php echo $activegame->end_date;?></td>
			<td><a href="<?php echo $playURL;?>">View</a></td>
		</tr>
		<?php } ?>
	</table>
</div>