	<input type="submit" name="io_mailForward_submit" id="io_mailForward_submit" value="Abschicken">
	
</form>

<script type="text/javascript">
	$(".chb").each(function() {
		$(this).change(function()
		{
			$(".chb").prop('checked',false);
			$(this).prop('checked',true);
		});
	});
</script>