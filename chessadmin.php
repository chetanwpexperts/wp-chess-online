<?php 
// admin class
class chessadmin{
	public function __construct(){
		$this->init_actions();
	}
	// add action scripts
	public function init_actions() {
        add_action( 'admin_menu', array( $this, 'chess_menu' ) );
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
    }

	public function chess_menu(){
		add_menu_page("Chess", "Chess", 7, "chess", "create_chess_table");
	}

	// set pluign path
	public function plugin_path() {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

}
$chessadminsetting = new chessadmin();
?>