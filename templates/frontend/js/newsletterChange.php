<form action="<?php echo io_get_current_url(); ?>" method="post">
	<?php 
		wp_nonce_field('io_newsletter', 'io_newsletter_nonce');
	?>
	<table border="0" cellpadding="5" cellspacing="0" width="100%">
		<tr>
			<td width="30%"><b>Deine alte Mail-Adresse:</b></td>
			<td width="70%"><input type="email" name="newsletter_email_alt" size="40"></td>
		</tr>
		<tr>
			<td width="30%"><b>Deine neue Mail-Adresse:</b></td>
			<td width="70%"><input type="email" name="newsletter_email_neu" size="40"></td>
		</tr>
		<tr>
			<td width="30%"></td>
			<td width="70%">
				<input type="hidden" name="newsletter_art" value="c">
				<input type="submit" name="newsletter_submit" value="Abschicken">
			</td>
		</tr>
	</table>
</form>
	