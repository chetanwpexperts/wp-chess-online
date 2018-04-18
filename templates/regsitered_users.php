<?php 
$blogusers = get_users( 'blog_id='.$creategame->blog_id.'&orderby=nicename&role=subscriber' );
?>
<div class="CSSTableGenerator" >
	<table >
		<tr>
			<td>Id</td>
			<td>Username</td>
			<td>Full Name</td>
			<td>User Email</td>
		</tr>
		<?php 
		foreach($blogusers as $users){
		?>
		<tr>
			<td><?php echo $users->ID; ?></td>
			<td><?php echo $users->user_nicename; ?></td>
			<td><?php echo $users->display_name; ?></td>
			<td><?php echo $users->user_email; ?></td>
		</tr>
		<?php } ?>
	</table>
</div>
