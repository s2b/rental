<?php if ($is_inventory): ?>
<h2>Inventar anfragen</h2>
<p>Auf dieser Seite kannst du eine neue Inventarbuchung anlegen. Diese wird anschließend von einem Administrator bestätigt oder abgelehnt.</p>
<?php else: ?>
<h2>Studio anfragen</h2>
<p>Auf dieser Seite kannst du eine neue Studio-Reservierung anlegen. Diese wird anschließend von einem Administrator bestätigt oder abgelehnt.</p>
<?php endif ?>

<?php echo form_open($form_url) ?>
<fieldset>
	<legend><label for="description">Verwendungszweck</label></legend>
	<input type="text" id="description" class="min" size="75" name="description" />
</fieldset>

<fieldset>
	<legend>Zeitraum</legend>
	<p>Zeitraum durch Klicken in den Kalender festlegen, anschließend gewünschte Uhrzeiten angeben:</p>
	<div id="calendar"><?php echo $calendar ?></div>
	<div id="calendar-result">
		<input type="hidden" name="start" />
		<input type="hidden" name="end" />
		<dl>
			<dt><label for="start_hour"><?php echo ($is_inventory) ? 'Gewünschter Verleihtermin:' : 'Gewünschter Beginn:' ?></label></dt>
			<dd id="calendar-start">
				<span></span>
				um
				<select name="start_hour" id="start_hour">
					<?php for ($i = 8; $i < 22; $i++): ?>
					<option><?php echo $i ?></option>
					<?php endfor ?>
				</select>
				<select name="start_min">
					<option>00</option>
					<option>15</option>
					<option>30</option>
					<option>45</option>
				</select>
				Uhr
			</dd>
		</dl>
		<dl>
			<dt><label for="end_hour"><?php echo ($is_inventory) ? 'Gewünschter Abgabetermin:' : 'Gewünschtes Ende:' ?></label></dt>
			<dd id="calendar-end">
				<span></span>
				um
				<select name="end_hour" id="end_hour">
					<?php for ($i = 8; $i < 22; $i++): ?>
					<option><?php echo $i ?></option>
					<?php endfor ?>
				</select>
				<select name="end_min">
					<option>00</option>
					<option>15</option>
					<option>30</option>
					<option>45</option>
				</select>
				Uhr
			</dd>
		</dl>
	</div>
</fieldset>

<?php if ($is_inventory): ?>
<fieldset class="tabs">
	<legend>Inventar</legend>
	<h3 data-tab="1">Pakete</h3>
	<?php if (!empty($packages)): ?>
	<p class="listing" data-tab="1">Pakete ausklappen, um deren Inhalt anzuzeigen</p>
	<ul class="listing" data-tab="1">
		<?php foreach ($packages as $package): ?>
		<li>
			<div class="listing-content">
				<?php echo $package->title ?><br />
				<span class="marginalia"><?php echo $package->desc ?></span>
			</div>
			<div class="clear"></div>
			
			<ul class="listing toggle">
				<?php foreach ($package->inventory as $item): ?>
				<li>
					<div class="listing-checkbox"><input type="checkbox" name="inventory[<?php echo $item->id ?>]" /></div>
					<div class="listing-content">
						<?php echo $item->title ?><br />
						<span class="marginalia"><?php echo $item->desc ?></span>
					</div>
					<div class="clear"></div>
				</li>
				<?php endforeach ?>
				<li class="marginalia"><a href="." class="select-all">alle auswählen</a> | <a href="." class="select-invert">Auswahl umkehren</a></li>
			</ul>
			
		</li>
		<?php endforeach ?>
	</ul>
	<?php else: ?>
	<p class="listing" data-tab="1">keine Inventar-Pakete vorhanden</p>
	<?php endif ?>
	
	<h3 data-tab="2">Inventar</h3>
	<?php if (!empty($inventory)): ?>
	<ul class="listing" data-tab="2">
		<?php foreach ($inventory as $item): ?>
		<li>
			<div class="listing-checkbox"><input type="checkbox" name="inventory[<?php echo $item->id ?>]" /></div>
			<div class="listing-content">
				<?php echo $item->title ?><br />
				<span class="marginalia"><?php echo $item->desc ?></span>
			</div>
			<div class="clear"></div>
		</li>
		<?php endforeach ?>
		<li class="marginalia"><a href="." class="select-all">alle auswählen</a> | <a href="." class="select-invert">Auswahl umkehren</a></li>
	</ul>
	<?php else: ?>
	<p class="listing" data-tab="2">kein Inventar vorhanden</p>
	<?php endif ?>
</fieldset>
<?php else: ?>
<fieldset>
	<legend>Räume</legend>
	<?php if (!empty($inventory)): ?>
	<ul class="listing">
		<?php foreach ($inventory as $item): ?>
		<li>
			<div class="listing-checkbox"><input type="checkbox" name="inventory[<?php echo $item->id ?>]" /></div>
			<div class="listing-content">
				<?php echo $item->title ?><br />
				<span class="marginalia"><?php echo $item->desc ?></span>
			</div>
			<div class="clear"></div>
		</li>
		<?php endforeach ?>
	</ul>
	<?php else: ?>
	<p class="listing" data-tab="2">keine Räume vorhanden</p>
	<?php endif ?>
</fieldset>
<?php endif ?>

<fieldset class="buttons">
	<input type="submit" value="<?php echo ($is_inventory) ? 'Inventar anfragen' : 'Studio anfragen' ?>" />
</fieldset>
</form>

<script type="text/javascript">
jQuery(document).ready(function ($) {
	$('#calendar').Calendar({bookingLinks: false, dateSelection: true});
});
</script>