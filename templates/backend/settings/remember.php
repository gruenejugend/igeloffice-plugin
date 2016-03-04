		<div class="wrap">
	    <h1>Erinnerungseinstellungen</h1>
	    <form method="post" action="options.php">
	        <?php
				settings_fields("io_remember");
	            do_settings_sections("io_remember");      
	            submit_button(); 
	        ?>          
	    </form>
		</div>