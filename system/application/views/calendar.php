<?php if ($show_next_prev): ?>
<div class="calendar-top">
	<div class="prev"><a href="<?php echo $prev_url ?>"><span class="reader">vorheriger</span></a></div>
	<h3 class="month"><?php echo $month . ' ' . $year ?></h3>
	<div class="next"><a href="<?php echo $next_url ?>"><span class="reader">nächster</span></a></div>
	<div class="clear"></div>
</div>
<?php endif ?>

<div class="calendar">
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
			<td<?php echo ($day['today']) ? ' class="today"' : '' ?> data-stamp="<?php echo $day['stamp'] ?>" data-full="<?php echo $day['full'] ?>" data-human="<?php echo $day['human'] ?>">
				<div class="right"><strong><?php echo $day['day'] ?></strong></div>
				<?php if (!empty($day['dates'])): ?>
				<ul class="sem marginalia">
					<?php foreach ($day['dates'] as $date): ?>
					<li class="booking-box booking-record-<?php echo $date['data']['id'] ?>">
						<span class="booking-title" data-id="<?php echo $date['data']['id'] ?>" data-tab="<?php echo $date['data']['status'] ?>">
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
</div>