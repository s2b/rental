<?php if ($show_next_prev): ?>
<div class="calendar_top">
	<div class="prev"><a href="<?php echo $prev_url ?>"><span class="reader">vorheriger</a></div>
	<h3 class="month"><?php echo $month . ' ' . $year ?></h3>
	<div class="next"><a href="<?php echo $next_url ?>"><span class="reader">nächster</span></a></div>
	<div class="clear"></div>
</div>
<?php endif ?>

<table class="calendar">
	<tr>
		<?php foreach ($days as $day): ?>
		<th><?php echo $day ?></th>
		<?php endforeach ?>
	</tr>
	<?php foreach ($calendar as $week): ?>
	<tr>
		<?php foreach ($week as $day): ?>
		<?php if (!$day): ?>
		<td class="empty"></td>
		<?php else: ?>
		<?php
		$classes = ($day['today']) ? 'today' : '';
		if (!empty($day['dates']))
		{
			foreach ($day['dates'] as $date)
			{
				$classes .= ' booking-record-' . $date['data']['id'];
			}
		}
		?>
		<td class="<?php echo $classes ?>">
			<div class="right"><strong><?php echo $day['day'] ?></strong></div>
			<?php if (!empty($day['dates'])): ?>
			<ul class="sem marginalia">
				<?php foreach ($day['dates'] as $date): ?>
				<li>
					<span class="booking-title" data-id="<?php echo $date['data']['id'] ?>">
						<?php echo (($date['timeframe']) ? $date['timeframe'] . ': ' : '') . $date['desc'] ?>
					</span>
				</li>
				<?php endforeach ?>
			</ul>
			<?php endif ?>
		</td>
		<?php endif ?>
		<?php endforeach ?>
	</tr>
	<?php endforeach ?>
</table>