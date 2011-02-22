<h2>Inventar-Pakete</h2>
<p>Auf dieser Seite können Inventar-Pakete hinzugefügt, bearbeitet, deaktiviert und gelöscht werden.</p>

<?php if (empty($packages)): ?>
<div class="message">Es sind bislang keine Inventar-Pakete vorhanden.</div>
<?php else: ?>
<div class="listing-buttons sp-t" style="margin-bottom: 0">
	<a href="<?php echo base_url() ?>packages/add" class="add modal-link"><span></span>Hinzufügen</a>
</div>
<table class="listing">
	<thead>
		<tr>
			<th style="width: 45%">Paket</th>
			<th style="width: 20%">geändert von</th>
			<th style="width: 15%">geändert am</th>
			<th class="center" style="width: 10%">Status</th>
			<th class="center" style="width: 10%">Aktion</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($packages as $package): ?>
		<tr class="noline">
			<td class="line-l">
				<?php echo $package->title ?><br />
				<span class="marginalia"><?php echo $package->desc ?></span>
			</td>
			<td>
				<a href="mailto:<?php echo $package->user->email ?>"><?php echo $package->user->name ?></a>
				<span class="marginalia"><?php echo $this->user_model->semester($package->user->semester_id) ?></span>
			</td>
			<td><?php echo date('d.m.Y G:i', strtotime($package->time)) ?></td>
			<td class="middle center line-b line-l" rowspan="2">
				<a href="<?php echo base_url() . 'packages/status/' . $package->id ?>" class="edit-link"><?php echo $package->status_text ?></a>
			</td>
			<td class="middle center line-b line-r" rowspan="2">
				<a href="<?php echo base_url() . 'packages/edit/' . $package->id ?>" class="modal-link">bearbeiten</a>
				<a href="<?php echo base_url() . 'packages/delete/' . $package->id ?>" class="modal-link">löschen</a>
			</td>
		</tr>
		<tr>
			<td class="line-l" colspan="3">
				<?php if (!empty($package->inventory)): ?>
				<ul>
					<?php foreach ($package->inventory as $item): ?>
					<li>
						<?php echo $item->title ?><br />
						<span class="marginalia"><?php echo $item->desc ?></span>
					</li>
					<?php endforeach ?>
				</ul>
				<?php endif ?>
			</td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>
<?php endif ?>
<div class="listing-buttons">
	<a href="<?php echo base_url() ?>packages/add" class="add modal-link"><span></span>Hinzufügen</a>
</div>