	<p id="new_user_login">
	
	</p>

<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function() { 
		$("#user_login").prop('readonly', 'true');
		$("#new_user_login").append($("label[for='user_login']"));
		$("label[for='user_login']").html($("label[for='user_login']").html().replace('Benutzername', 'Benutzer*innenname (wird generiert)'));
		<?php
			if($_GET['erweitert'] != 1) {
		?>
		$("label[for='user_email']").html($("label[for='user_email']").html().replace('E-Mail', 'E-Mail<br>(Keine Adresse mit @gruene-jugend.de)'));
		
		var userMailChange = function() {
			if($("#user_email").val().search("@gruene-jugend.de") !== -1) {
				$("#wp-submit").attr("disabled", true);
			} else {
				$("#wp-submit").attr("disabled", false);
			}
		};
		
		$("#user_email").keyup(function() {
			userMailChange();
		});
		userMailChange();
		<?php
			}
		?>
	});
			
	var userLoginUmlauts = function(str) {
		value =   str.split('ä').join('ae');
		value = value.split('ü').join('ue');
		value = value.split('ö').join('oe');
		value = value.split('Ä').join('Ae');
		value = value.split('Ü').join('Ue');
		value = value.split('Ö').join('Oe');
		value = value.split('ß').join('ss');

		return value;
	};
</script>