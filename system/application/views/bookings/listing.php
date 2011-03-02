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

<div id="calendar"><?php echo $calendar ?></div>

<?php echo form_open($form_url) ?>
<div class="tabs">

	<?php foreach ($bookings as $status => $category): ?>
	<h3 class="sp-t" data-tab="<?php echo $status ?>"><?php echo $category->title ?></h3>
	<table class="listing" data-tab="<?php echo $status ?>">
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
			<?php foreach ($category->bookings as $booking): ?>
			<?php $booked_class = ($status == BOOKING_NEW) ? ' ' . (($booking->booked) ? 'error' : 'ok') : '' ?>
			<tr class="noline booking-record-<?php echo $booking->id ?>">
				<td class="line-l">
					<span class="booking-title" data-id="<?php echo $booking->id ?>" data-tab="<?php echo $status ?>">
						<?php echo $booking->desc ?>
					</span>
				</td>
				<td><?php echo date('d.m.Y G:i', strtotime($booking->start)) ?></td>
				<td><?php echo date('d.m.Y G:i', strtotime($booking->end)) ?></td>
				<td>
					<a href="mailto:<?php echo $booking->user->email ?>?subject=Deine%20Ausleihe"><?php echo $booking->user->name ?></a>
					<span class="marginalia"><?php echo $this->user_model->semester($booking->user->semester_id) ?></span>
				</td>
				<td><?php echo date('d.m.Y G:i', strtotime($booking->time)) ?></td>
				<td rowspan="<?php echo (!empty($booking->updates)) ? 3 : 2 ?>" class="middle center line-l line-r line-b<?php echo $booked_class ?>">
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
			<tr class="booking-record-<?php echo $booking->id ?>">
				<td colspan="5" class="line-l">
					<ul>
						<?php foreach ($booking->inventory as $inventory): ?>
						<li>
							<?php echo $inventory->title ?><br />
							<span class="marginalia"><?php echo $inventory->desc ?></span>
							<?php if (isset($inventory->booked)): ?>
							<ul class="marginalia" style="margin: 0.5em 0">
								<?php foreach ($inventory->booked as $booked): ?>
								<li>
									bereits gebucht von
									<a href="mailto:<?php echo $booked->user->email ?>?subject=Deine%20Ausleihe"><?php echo $booked->user->name ?></a>
									<span class="marginalia"><?php echo $this->user_model->semester($booked->user->semester_id) ?></span>
									vom <?php echo date('d.m.Y G:i', strtotime($booked->start)) ?>
									bis <?php echo date('d.m.Y G:i', strtotime($booked->end)) ?>:
									<span class="booking-title" data-id="<?php echo $booked->id ?>" data-tab="<?php echo $booked->status ?>">
										<?php echo $booked->desc ?>
									</span>
								</li>
								<?php endforeach ?>
							</ul>
							<?php endif ?>
						</li>
						<?php endforeach; ?>
					</ul>
				</td>
			</tr>
			<?php if (!empty($booking->updates)): ?>
			<tr class="booking-record-<?php echo $booking->id ?>">
				<td colspan="5" class="line-l">
					<ul class="sem marginalia">
						<?php
						$string = '%1$s hat die ' . (($is_inventory) ? 'Buchung' : 'Reservierung') . ' am %2$s %3$s%4$s';
						foreach ($booking->updates as $update):
						?>
						<li>
							<?php
							echo sprintf($string,
								'<a href="mailto:' . $update->user->email . '">' . $update->user->name . '</a> (' . $this->user_model->semester($update->user->semester_id) . ')',
								date('d.m.Y G:i', strtotime($update->time)),
								$update->status_text,
								(($update->desc) ? ': ' . $update->desc : '.')
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
	<?php endforeach; ?>

</div>
</form>
<?php endif; ?>

<script type="text/javascript">
jQuery(document).ready(function ($) {
	$('#calendar').Calendar();
});
</script>