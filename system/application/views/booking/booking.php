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
	<div id="calendar"><?php echo $calendar ?></div>
</fieldset>

<?php if ($is_inventory): ?>
<fieldset class="tabs">
	<legend>Inventar</legend>
	<h3 data-tab="1">Pakete</h3>
	<?php if (!empty($packages)): ?>
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
	</ul>
	<?php else: ?>
	<p class="listing" data-tab="2">kein Inventar vorhanden</p>
	<?php endif ?>
</fieldset>
<?php else: ?>
<fieldset>
	<legend>Räume</legend>
	
</fieldset>
<?php endif ?>

<fieldset class="buttons">
	<input type="submit" name="submit" value="<?php echo ($is_inventory) ? 'Inventar anfragen' : 'Studio anfragen' ?>" />
</fieldset>
</form>

<script type="text/javascript">
jQuery(document).ready(function ($) {
	$('#calendar').Calendar({links: false});
});
</script>