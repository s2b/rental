<?php if ($is_inventory): ?>
<h2>Inventar-Buchungen</h2>
<p>Auf dieser Seite wird gebuchtes und beantragtes Inventar aufgelistet. Die Buchungen können außerdem bestätigt oder abgelehnt werden.</p>
<?php else: ?>
<h2>Studio-Reservierungen</h2>
<p>Auf dieser Seite werden Studio-Reservierungen aufgelistet. Diese können außerdem bestätigt oder abgelehnt werden.</p>
<?php endif; ?>

<?php if (empty($bookings)): ?>
<div class="message">Es sind keine <?php echo ($is_inventory) ? 'Buchungen' : 'Reservierungen' ?> vorhanden.</div>
<?php else: ?>
<?php echo form_open($form_url); ?>
<table class="listing sp-t">
	<thead>
		<tr>
			<th>Begründung</th>
			<th style="width: 15%">von</th>
			<th style="width: 15%">bis</th>
			<th style="width: 20%">beantragt von</th>
			<th style="width: 15%">beantragt am</th>
			<th class="center" style="width: 10%">Status</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($bookings as $booking): ?>
		<tr class="noline">
			<td><?php echo $booking->desc ?></td>
			<td><span class="date"><?php echo date('d.m.Y G:i', strtotime($booking->start)) ?></span></td>
			<td><span class="date"><?php echo date('d.m.Y G:i', strtotime($booking->end)) ?></span></td>
			<td>
				<a href="mailto:<?php echo $booking->user->email ?>?subject=Deine%20Ausleihe"><?php echo $booking->user->name ?></a>
				<span class="marginalia"><?php echo $this->user_model->semester($booking->user->semester_id) ?></span>
			</td>
			<td><?php echo date('d.m.Y G:i', strtotime($booking->time)) ?></td>
			<td rowspan="2" class="middle center line">
				<span><?php echo $booking->status_text ?></span>
				<select name="action[<?php echo $booking->id ?>]" class="autosubmit listing-action">
					<option value="">Aktion</option>
					<?php foreach ($actions as $value => $title): ?>
					<option value="<?php echo $value ?>"><?php echo $title ?></option>
					<?php endforeach; ?>
				</select>
				<input type="submit" class="optbutton" value="OK" />
			</td>
		</tr>
		<tr>
			<td colspan="5">
				<ul>
					<?php foreach ($booking->inventory as $inventory): ?>
					<li><?php echo $inventory->title ?><br /><span class="marginalia"><?php echo $inventory->desc ?></span></li>
					<?php endforeach; ?>
				</ul>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
</form>
<?php endif; ?>