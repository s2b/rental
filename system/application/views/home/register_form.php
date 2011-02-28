<div id="black">
	<?php echo form_open('home/register') ?>
		<fieldset id="modal">
			<legend>Registrierung</legend>
			<div class="errors"><?php echo validation_errors(); ?></div>

			<dl>
				<dt><label for="text">Name:</label></dt>
				<dd><input type="text" name="name" id="name" size="30" value="<?php echo set_value('name') ?>" /></dd>
			</dl>
			<dl>
				<dt><label for="email">E-Mail:</label></dt>
				<dd><input type="email" name="email" id="email" size="30" value="<?php echo set_value('email') ?>" /></dd>
			</dl>
			<dl>
				<dt><label for="student_id">Matrikelnummer:</label></dt>
				<dd><input type="text" name="student_id" id="student_id" size="10" value="<?php echo set_value('student_id') ?>" /></dd>
			</dl>
			<dl>
				<dt><label for="semester">Anfangssemester:</label></dt>
				<dd>
					<select name="semester" id="semester">
						<?php foreach ($semesters as $semester_id => $semester_title): ?>
						<option value="<?php echo $semester_id ?>"<?php echo set_select('semester', $semester_id) ?>><?php echo $semester_title ?></option>
						<?php endforeach ?>
					</select>
				</dd>
			</dl>
			<dl>
				<dt><label for="password">Passwort:</label></dt>
				<dd><input type="password" name="password" id="password" size="30" value="" /></dd>
			</dl>
			<dl>
				<dt><label for="password_confirm">Passwort (Bestätigung):</label></dt>
				<dd><input type="password" name="password_confirm" id="password_confirm" size="30" value="" /></dd>
			</dl>

			<div class="l" style="margin-top: 1em"><a href="<?php echo base_url() ?>home/login">zurück zum Login</a></div>
			<div class="right"><input type="submit" value="Registrieren" /></div>
		</fieldset>
	</form>
</div>