<div id="black">
	<?php echo form_open('home/resetpassword', '', $hidden) ?>
		<fieldset id="modal">
			<legend>Password zurücksetzen</legend>
			<div class="errors"><?php echo validation_errors(); ?></div>

			<dl>
				<dt><label for="email">E-Mail:</label></dt>
				<dd><?php echo $email ?></dd>
			</dl>
			<dl>
				<dt><label for="password">Passwort:</label></dt>
				<dd><input type="password" name="password" id="password" value="" /></dd>
			</dl>
			<dl>
				<dt><label for="password_confirm">Passwort (Bestätigung):</label></dt>
				<dd><input type="password" name="password_confirm" id="password_confirm" value="" /></dd>
			</dl>

			<div class="l" style="margin-top: 1em"><a href="<?php echo base_url() ?>home/login">zurück zum Login</a></div>
			<div class="right"><input type="submit" value="Zurücksetzen" /></div>
		</fieldset>
	</form>
</div>