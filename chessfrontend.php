<?php 
// chess front end 
class chessfrontend{
	var $prefix;	
	var $membership;	
	var $chess_table;	
	var $chess_stats;	
	var $ongoing_games;
	var $offer_draw;	
	var $pgn;	var $blogid;		
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
	public function countdown($start_date, $end_date){
		ob_start();	?>	
		<div id="element"></div>	
		<script type="text/javascript">		
		jquery(document).ready(function($){	
			<?php 			
				$sd =  date("Y/m/d", $start_date);	
				$ed =  date("Y/m/d", $end_date);	
			?>			
			var startdate = "<?php echo $sd;?>",	
			enddate = "<?php echo $ed;?>";		
			if(new Date() >= new Date(startdate) && new Date() <= new Date(enddate)) {	
				$("#element").countdown(enddate, function(event) {	
					$(this).text(	event.strftime('%D days %H:%M:%S') );			
				});			
			}		
		});		
		</script>
		<?php 		
		$timer = ob_get_clean();	
		return $timer;	
	}
}

$chess_front = new chessfrontend();
?>