<div id="black">
	<?php echo form_open($form_url); ?>
	<fieldset id="modal">
		<legend>Inventar-Paket löschen</legend>

		<p>Soll das ausgewählte Inventar-Paket wirklich <strong>endgültig gelöscht</strong> werden? Dieser Vorgang kann nicht rückgängig gemacht werden.</p>
		
		<p>Hinweis: Die im Paket enthaltenen Inventar-Gegenstände werden <strong>nicht</strong> gelöscht.</p>

		<div class="right">
			<input type="submit" name="cancel" value="Abbrechen" />
			<input type="submit" name="delete" value="Löschen" />
		</div>
	</fieldset>
	</form>
</div>