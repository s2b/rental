<div id="black">
	<fieldset id="modal">
		<legend>Passwort vergessen</legend>
		<div class="errors"><?php echo validation_errors(); ?></div>
		
		<dl>
			<dt><label for="email">E-Mailadresse:</label></dt>
			<dd><input type="email" name="email" id="email" value="<?php echo set_value('email') ?>" /></dd>
		</dl>
		
		<div class="l" style="margin-top: 1em"><a href="<?php echo base_url() ?>home/login">zurück zum Login</a></div>
		<div class="right"><input type="submit" value="Passwort zuschicken" /></div>
	</fieldset>
</div>