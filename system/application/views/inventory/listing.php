<h2>Inventar-Verwaltung</h2>
<p>Auf dieser Seite kann Studioinventar hinzugefügt, bearbeitet, deaktiviert und gelöscht werden.</p>

<?php if (empty($inventory)): ?>
<div class="message">Es wurde bislang kein Inventar vorhanden.</div>
<?php else: ?>
<table class="listing sp-t">
	<thead>
		<tr>
			<th style="width: 45%">Inventar</th>
			<th style="width: 20%">geändert von</th>
			<th style="width: 15%">geändert am</th>
			<th class="center" style="width: 10%">Status</th>
			<th class="center" style="width: 10%">Aktion</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($inventory as $inv): ?>
		<tr>
			<td class="line-l">
				<?php echo $inv->title ?><br />
				<span class="marginalia"><?php echo $inv->desc ?></span>
			</td>
			<td>
				<a href="mailto:<?php echo $inv->user->email ?>"><?php echo $inv->user->name ?></a>
				<span class="marginalia"><?php echo $this->user_model->semester($inv->user->semester_id) ?></span>
			</td>
			<td><?php echo date('d.m.Y G:i', strtotime($inv->time)) ?></td>
			<td class="middle center">
				<a href="<?php echo base_url() . 'inventory/status/' . $inv->id ?>" class="edit-link"><?php echo $inv->status_text ?></a>
			</td>
			<td class="middle center line-r">
				<a href="<?php echo base_url() . 'inventory/edit/' . $inv->id ?>" class="modal-link">bearbeiten</a>
				<a href="<?php echo base_url() . 'inventory/delete/' . $inv->id ?>" class="modal-link">löschen</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php endif ?>
<div class="listing-buttons">
	<a href="<?php echo base_url() ?>inventory/add" class="add modal-link"><span>Hinzufügen</span></a>
</div>