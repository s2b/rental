<div id="black">
	<?php echo form_open($form_url); ?>
	<fieldset id="modal">
		<?php if ($mode == 'add'): ?>
		<legend>Inventar-Gegenstand hinzufügen</legend>
		<?php else: ?>
		<legend>Inventar-Gegenstand bearbeiten</legend>
		<?php endif; ?>

		<dl>
			<dt><label for="title">Titel:</label></dt>
			<dd>
				<?php echo form_error('title') ?>
				<input type="text" name="title" id="title" size="30" value="<?php echo set_value('title', $inv->title) ?>" />
			</dd>
		</dl>
		<dl>
			<dt><label for="desc">Beschreibung:</label></dt>
			<dd>
				<?php echo form_error('desc') ?>
				<input type="text" name="desc" id="desc" size="30" value="<?php echo set_value('desc', $inv->desc) ?>" />
			</dd>
		</dl>

		<div class="right">
			<input type="submit" name="cancel" value="Abbrechen" />
			<input type="submit" name="save" value="Speichern" />
		</div>
	</fieldset>
	</form>
</div>