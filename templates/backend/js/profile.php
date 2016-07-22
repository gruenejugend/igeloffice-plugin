<script type='text/javascript' src='https://code.jquery.com/jquery-1.11.3.min.js'></script>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function() { 
		$("#<?php echo User_Util::POST_ATTRIBUT_ART; ?>").prop('readonly', true);
		$("#<?php echo User_Util::POST_ATTRIBUT_LANDESVERBAND; ?>").prop('readonly', true);
<?php if(!current_user_can('administrator')) {
?>
		$("#<?php echo User_Util::POST_ATTRIBUT_GROUPS; ?>").prop('readonly', true);
		$("#<?php echo User_Util::POST_ATTRIBUT_PERMISSIONS; ?>").prop('readonly', true);
<?php } 
?>
		
		$(".user-nickname-wrap").hide();
		$(".user-display-name-wrap").hide();
		$(".user-description-wrap").hide();
		
		if($("#<?php echo User_Util::POST_ATTRIBUT_ART; ?>).val() !== 'User') {
			$(".user-first-name-wrap").hide();
			$(".user-last-name-wrap").hide();
		}
		
		$(".user-user-login-wrap").after($("#<?php echo User_Util::ATTRIBUT_ART; ?>"));
<?php if($user->art == 'basisgruppe') {
?>
		$("#<?php echo User_Util::ATTRIBUT_ART; ?>").after($("#io_<?php echo User_Util::POST_ATTRIBUT_LANDESVERBAND; ?>"));
<?php }
?>
		$(".user-sessions-wrap.").after($("#io_<?php echo User_Util::POST_ATTRIBUT_GROUPS; ?>"));
		$("#io_<?php echo User_Util::POST_ATTRIBUT_GROUPS; ?>").after($("#io_<?php echo User_Util::POST_ATTRIBUT_PERMISSIONS; ?>"));
	});
</script>