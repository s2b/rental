<h2>Übersicht</h2>
<p>Auf dieser Seite werden deine aktuellen <a href="#inventory">Inventarbuchungen</a> und <a href="#studio">Studio-Reservierungen</a> sowie deren Bearbeitungsstand aufgelistet.</p>

<h3 class="sp-t" id="inventory">Deine Inventarbuchungen</h3>
<?php if (empty($inventory_bookings)): ?>
<p>Aktuell gibt es keine offenen Inventarbuchungen.</p>
<?php else: ?>
<table class="listing">
	<thead>
		<tr>
			<th>Begründung</th>
			<th style="width: 20%">von</th>
			<th style="width: 20%">bis</th>
			<th style="width: 20%">beantragt am</th>
			<th class="center" style="width: 10%">Status</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($inventory_bookings as $booking): ?>
		<tr class="noline">
			<td class="line-l"><?php echo $booking->desc ?></td>
			<td><?php echo date('d.m.Y G:i', strtotime($booking->start)) ?></td>
			<td><?php echo date('d.m.Y G:i', strtotime($booking->end)) ?></td>
			<td><?php echo date('d.m.Y G:i', strtotime($booking->time)) ?></td>
			<td rowspan="<?php echo (!empty($booking->updates)) ? 3 : 2 ?>" class="middle center line-l line-r line-b"><?php echo $booking->status_text ?></td>
		</tr>
		<tr>
			<td colspan="4" class="line-l">
				<ul>
					<?php foreach ($booking->inventory as $inventory): ?>
					<li>
						<?php echo $inventory->title ?><br />
						<span class="marginalia"><?php echo $inventory->desc ?></span>
					</li>
					<?php endforeach; ?>
				</ul>
			</td>
		</tr>
		<?php if (!empty($booking->updates)): ?>
		<tr>
			<td colspan="4" class="line-l">
				<ul class="sem marginalia">
					<?php
					$string = '%1$s hat die Buchung am %2$s %3$s%4$s';
					foreach ($booking->updates as $update):
					?>
					<li>
						<?php
						echo sprintf($string,
							'<a href="mailto:' . $update->user->email . '">' . $update->user->name . '</a> (' . $this->user_model->semester($update->user->semester_id) . ')',
							date('d.m.Y G:i', strtotime($update->time)),
							$update->status_text,
							'.'
						);
						?>
					</li>
					<?php endforeach; ?>
				</ul>
			</td>
		</tr>
		<?php endif; ?>
		<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>

<h3 class="sp-t" id="studio">Deine Studio-Reservierungen</h3>
<?php if (empty($studio_bookings)): ?>
<p>Aktuell gibt es keine offenen Studio-Reservierungen.</p>
<?php else: ?>
<table class="listing">
	<thead>
		<tr>
			<th>Begründung</th>
			<th style="width: 20%">von</th>
			<th style="width: 20%">bis</th>
			<th style="width: 25%">beantragt am</th>
			<th class="center" style="width: 10%">Status</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($studio_bookings as $booking): ?>
		<tr class="noline">
			<td class="line-l"><?php echo $booking->desc ?></td>
			<td><?php echo date('d.m.Y G:i', strtotime($booking->start)) ?></td>
			<td><?php echo date('d.m.Y G:i', strtotime($booking->end)) ?></td>
			<td><?php echo date('d.m.Y G:i', strtotime($booking->time)) ?></td>
			<td rowspan="<?php echo (!empty($booking->updates)) ? 3 : 2 ?>" class="middle center line-l line-r line-b"><?php echo $booking->status_text ?></td>
		</tr>
		<tr>
			<td colspan="4" class="line-l">
				<ul>
					<?php foreach ($booking->inventory as $inventory): ?>
					<li>
						<?php echo $inventory->title ?><br />
						<span class="marginalia"><?php echo $inventory->desc ?></span>
					</li>
					<?php endforeach; ?>
				</ul>
			</td>
		</tr>
		<?php if (!empty($booking->updates)): ?>
		<tr>
			<td colspan="4" class="line-l">
				<ul class="sem marginalia">
					<?php
					$string = '%1$s hat die Reservierung am %2$s %3$s%4$s';
					foreach ($booking->updates as $update):
					?>
					<li>
						<?php
						echo sprintf($string,
							'<a href="mailto:' . $update->user->email . '">' . $update->user->name . '</a> (' . $this->user_model->semester($update->user->semester_id) . ')',
							date('d.m.Y G:i', strtotime($update->time)),
							$update->status_text,
							'.'
						);
						?>
					</li>
					<?php endforeach; ?>
				</ul>
			</td>
		</tr>
		<?php endif; ?>
		<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>