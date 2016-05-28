<form action="<?php echo io_get_current_url(); ?>" method="post">
	<?php 
		wp_nonce_field('io_newsletter', 'io_newsletter_nonce');
	?>
	<table border="0" cellpadding="5" cellspacing="0" width="100%">
		<tr>
			<td width="30%"><b>Deine Mail-Adresse:</b></td>
			<td width="70%"><input type="email" name="newsletter_email" size="40" value="<?php echo $vorlage; ?>"></td>
		</tr>
		<tr>
			<td width="30%"><b>Was willst du tun:</b></td>
			<td width="70%">
				<input type="radio" id="newsletter_art_l" name="newsletter_art" value="l">&nbsp;&nbsp;<label for="newsletter_art_l">L&ouml;schen</label><br>
				<input type="radio" id="newsletter_art_a" name="newsletter_art" value="a">&nbsp;&nbsp;<label for="newsletter_art_a">&Auml;ndern</label><br>
				<input type="radio" id="newsletter_art_e" name="newsletter_art" value="e">&nbsp;&nbsp;<label for="newsletter_art_e">Wieder eintragen (nur f&uuml;r Mitglieder)</label>
			</td>
		</tr>
		<tr>
			<td width="30%"><b>L&ouml;se bitte folgende Aufgabe:</b><br><br><?php echo $aufgabe; ?></td>
			<td width="70%"><input type="number" name="newsletter_aufgabe" size="10"></td>
		</tr>
		<tr>
			<td width="30%"></td>
			<td width="70%"><input type="submit" name="newsletter_submit" value="Abschicken"></td>
		</tr>
	</table>
</form>
	