<?php include "tablcss.php"; ?>
<div class="css3-tabstrip">
    <ul>
        <li>
            <input type="radio" name="css3-tabstrip-0" checked="checked" id="css3-tabstrip-0-0" /><label for="css3-tabstrip-0-0"><?php _e("Start a new game");?></label>
            <div>
                <h3><?php _e("Create chess table");?></h3>
                <?php include 'create_game.php'; ?>
            </div>
        </li>
		<li>
            <input type="radio" name="css3-tabstrip-0" id="css3-tabstrip-0-1" /><label for="css3-tabstrip-0-1"><?php _e("Registered users");?></label>
            <div>
                <h3><?php _e("Regsitered Users Listing");?></h3>
				<?php include 'regsitered_users.php'; ?>
            </div>
        </li>
		<li>
            <input type="radio" name="css3-tabstrip-0" id="css3-tabstrip-0-2" /><label for="css3-tabstrip-0-2"><?php _e("Current active game");?></label>
            <div>
                <h3><?php _e("Active game");?></h3>
                <?php include 'activegame.php'; ?>
            </div>
        </li>
		<li>
            <input type="radio" name="css3-tabstrip-0" id="css3-tabstrip-0-3" /><label for="css3-tabstrip-0-3"><?php _e("Result");?></label>
            <div>
                <h3><?php _e("Result");?></h3>
                Reuslt of the game
            </div>
        </li>
    </ul>
</div>