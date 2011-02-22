<div id="black">
	<?php echo form_open($form_url); ?>
	<fieldset id="modal">
		<?php if ($mode == 'add'): ?>
		<legend>Inventar-Paket hinzufügen</legend>
		<?php else: ?>
		<legend>Inventar-Paket bearbeiten</legend>
		<?php endif; ?>

		<dl>
			<dt><label for="title">Titel:</label></dt>
			<dd>
				<?php echo form_error('title') ?>
				<input type="text" name="title" id="title" value="<?php echo set_value('title', $package->title) ?>" />
			</dd>
		</dl>
		<dl>
			<dt><label for="desc">Beschreibung:</label></dt>
			<dd>
				<?php echo form_error('desc') ?>
				<input type="text" name="desc" id="desc" value="<?php echo set_value('desc', $package->desc) ?>" />
			</dd>
		</dl>
		<dl>
			<dt><label for="inventory">Inventar:</label></dt>
			<dd>
				<select name="inventory[]" id="inventory" multiple="multiple" size="5">
					<?php foreach ($inventory as $item): ?>
					<option value="<?php echo $item->id ?>"<?php echo (in_array($item->id, $package->inventory)) ? ' selected="selected"' : '' ?>><?php echo $item->title ?></option>
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