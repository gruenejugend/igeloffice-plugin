<script type='text/javascript' src='https://code.jquery.com/jquery-1.11.3.min.js'></script>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function() { 
		var label = $("label[for='user_login']").html().split("<br>")[0];
		$("label[for='user_login']").html($("label[for='user_login']").html().replace(label, 'Nutzer_innenname<br><i>Wenn du dich als normale Nutzer_in einloggst folgendes Format:<br>[Vorname] [Nachname]</i>'));
	});
</script>