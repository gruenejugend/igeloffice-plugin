<script type='text/javascript' src='https://code.jquery.com/jquery-1.11.3.min.js'></script>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function() { 
		$("#user_art").prop('readonly', true);
		$("#landesverband").prop('readonly', true);
<?php if(!current_user_can('administrator')) {
?>
		$("#groups").prop('readonly', true);
		$("#permissions").prop('readonly', true);
<?php } 
?>
		
		$(".user-nickname-wrap").hide();
		$(".user-display-name-wrap").hide();
		$(".user-description-wrap").hide();
		
		if($("#user_art").val() !== 'User') {
			$(".user-first-name-wrap").hide();
			$(".user-last-name-wrap").hide();
		}
		
		$(".user-user-login-wrap").after($("#<?php echo User_Util::ATTRIBUT_ART; ?>"));
<?php if($user->art == 'basisgruppe') {
?>
		$("#<?php echo User_Util::ATTRIBUT_ART; ?>").after($("#io_landesverband"));
<?php }
?>
		$(".user-sessions-wrap.").after($("#io_groups"));
		$("#io_groups").after($("#io_permissions"));
	});
</script>