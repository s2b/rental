<div id="black">
	<?php echo form_open($form_url); ?>
	<fieldset id="modal">
		<legend>Inventar-Gegenstand löschen</legend>

		<p>Soll der ausgewählte Inventar-Gegenstand wirklich <strong>endgültig gelöscht</strong> werden? Dieser Vorgang kann nicht rückgängig gemacht werden.</p>

		<div class="right">
			<input type="submit" name="cancel" value="Abbrechen" />
			<input type="submit" name="delete" value="Löschen" />
		</div>
	</fieldset>
	</form>
</div>