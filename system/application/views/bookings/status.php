<div id="black">
	<?php echo form_open($form_url); ?>
	<fieldset id="modal">
		<legend>Buchungsstatus ändern</legend>

		<dl>
			<dt>Neuer Status:</dt>
			<dd><strong><?php echo $new_status ?></strong></dd>
		</dl>
		<dl>
			<dt><label for="desc">Begründung (intern):</label></dt>
			<dd><textarea name="desc" id="desc" class="max" cols="50" rows="4"></textarea></dd>
		</dl>

		<div class="right">
			<input type="submit" name="cancel" value="Abbrechen" />
			<input type="submit" name="save" value="Speichern" />
			<?php echo $hidden_fields ?>
		</div>
	</fieldset>
	</form>
</div>