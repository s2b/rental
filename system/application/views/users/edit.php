<div id="black">
	<?php echo form_open($form_url); ?>
	<fieldset id="modal">
		<legend>Benutzer bearbeiten</legend>

		<dl>
			<dt><label for="name">Name:</label></dt>
			<dd>
				<?php echo form_error('name') ?>
				<input type="text" name="name" id="name" value="<?php echo set_value('name', $user->name) ?>" />
			</dd>
		</dl>
		<dl>
			<dt><label for="email">E-Mailadresse:</label></dt>
			<dd>
				<?php echo form_error('email') ?>
				<input type="email" name="email" id="email" value="<?php echo set_value('email', $user->email) ?>" />
			</dd>
		</dl>
		<dl>
			<dt><label for="student_id">Matrikelnummer:</label></dt>
			<dd>
				<?php echo form_error('student_id') ?>
				<input type="text" name="student_id" id="student_id" value="<?php echo set_value('student_id', $user->student_id) ?>" />
			</dd>
		</dl>
		<dl>
			<dt><label for="semester">Semester:</label></dt>
			<dd>
				<?php echo form_error('semester') ?>
				<select name="semester" id="semester">
					<?php foreach ($semesters as $id => $title): ?>
					<option value="<?php echo $id ?>" <?php echo set_select('semester', $id, ($id == $user->semester_id)) ?>><?php echo $title ?></option>
					<?php endforeach ?>
				</select>
			</dd>
		</dl>

		<div class="right">
			<input type="submit" name="cancel" value="Abbrechen" />
			<input type="submit" name="save" value="Speichern" />
		</div>
	</fieldset>
	</form>
</div>