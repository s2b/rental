<h2>Benutzer-Verwaltung</h2>
<p>Auf dieser Seite können vorhandene Benutzer bearbeitet oder gelöscht werden.</p>

<?php if (empty($users)): ?>
<div class="message">Es sind keine Benutzer vorhanden.</div>
<?php else: ?>
<table class="listing sp-t">
	<thead>
		<tr>
			<th style="width: 20%">Benutzer</th>
			<th style="width: 10%">Semester</th>
			<th style="width: 20%">registriert am</th>
			<th style="width: 20%">zuletzt eingeloggt am</th>
			<th class="center" style="width: 10%">Rolle</th>
			<th class="center" style="width: 10%">Status</th>
			<th class="center" style="width: 10%">Aktion</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($users as $user): ?>
		<tr>
			<td class="line-l">
				<a href="mailto:<?php echo $user->email ?>"><?php echo $user->name?></a><br />
				<span class="marginalia">Matrikelnummer: <?php echo $user->student_id ?></span>
			</td>
			<td><?php echo $this->user_model->semester($user->semester_id) ?></td>
			<td><?php echo date('d.m.Y G:i', strtotime($user->reg_date)) ?></td>
			<td><?php echo date('d.m.Y G:i', strtotime($user->last_visit)) ?></td>
			<td class="middle center">
				<a href="<?php echo base_url() . 'users/role/' . $user->id ?>" class="edit-link"><?php echo $user->role_text ?></a>
			</td>
			<td class="middle center">
				<a href="<?php echo base_url() . 'users/status/' . $user->id ?>" class="edit-link"><?php echo $user->status_text ?></a>
			</td>
			<td class="middle center line-r">
				<a href="<?php echo base_url() . 'users/edit/' . $user->id ?>" class="modal-link">bearbeiten</a>
				<a href="<?php echo base_url() . 'users/delete/' . $user->id ?>" class="modal-link">löschen</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php endif ?>