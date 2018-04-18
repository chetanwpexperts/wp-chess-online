<?php 
include "template_class.php"; 
$creategame = new chess_template();

$return_status = $creategame->creategame($_POST['actCreateChessTbl'], $_POST);

if($return_status == 1){
	$message = 'Chess Table created';
}
$option_array = array('random' => "random", 'white' => "white", 'black' => "black");
?>
<script type="text/javascript"> 
	jQuery(document).ready( function($) {	
		setTimeout('$("#return_status").fadeOut()',2500); 
	});
</script>
<div id="return_status"><?php echo $message; ?></div>
<form name="chess_table" method="post" action="">
	<p><label>Pich Color </label>
	<select name="color" style="width:100px">
		<?php 
		foreach($option_array as $key => $value){ ?>
			<option value="<?php echo $key;?>"><?php echo $value; ?></option>
		<?php }
		?>
	</select></p>
	<p><label>Choose Username( Host Of Game )</label>
	<select name="host">
		<?php echo $creategame->get_all_users(); ?>
	</select></p>
	<p><label>Choose Opponent</label>
	<select name="opponent">
		<?php echo $creategame->get_all_users(); ?>
	</select></p>
	
	<p><label>Start Date</label>
	<input type="text" id="Start" name="start_date" value=""/> &nbsp;
	<label>End Date</label>
	<input type="text" id="end" name="end_date" value=""/>
	</p>
	<input name="actCreateChessTbl" type="submit" class="button" value="Create A Game">
</form>
<script>
jQuery(document).ready(function() {
    jQuery('#Start').datepicker({
        dateFormat : 'dd-mm-yy'
    });
	jQuery('#end').datepicker({
        dateFormat : 'dd-mm-yy'
    });
});
</script>