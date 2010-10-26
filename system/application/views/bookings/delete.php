<div id="black">
	<?php echo form_open($form_url); ?>
	<fieldset id="modal">
		<legend>Buchung löschen</legend>

		<p>Soll die ausgewählte Buchung wirklich <strong>endgültig gelöscht</strong> werden? Dieser Vorgang kann nicht rückgängig gemacht werden.</p>

		<div class="right">
			<input type="submit" name="cancel" value="Abbrechen" />
			<input type="submit" name="delete" value="Löschen" />
			<?php echo $hidden_fields ?>
		</div>
	</fieldset>
	</form>
</div>