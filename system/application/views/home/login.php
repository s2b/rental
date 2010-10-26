<div id="black">
	<?php echo form_open('home/login') ?>
		<fieldset id="modal">
			<legend>Login</legend>
			<div class="errors"><?php echo validation_errors(); ?></div>

			<dl>
				<dt><label for="email">E-Mail:</label></dt>
				<dd><input type="text" name="email" id="email" value="<?php echo set_value('email') ?>" /></dd>
			</dl>
			<dl>
				<dt><label for="password">Passwort:</label></dt>
				<dd><input type="password" name="password" id="password" value="" /></dd>
			</dl>

			<div class="right"><input type="submit" value="Einloggen" /></div>
		</fieldset>
	</form>
</div>