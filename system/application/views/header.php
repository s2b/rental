<?php echo doctype('html5') ?>

<html>
	<head>
		<title>Ausleihtool &bull; media.fh-aachen.de</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>style/ausleihe2.css" />
	</head>
	<body>
	<div id="no-js">
		Diese Seite funktioniert teilweise nur mit eingeschaltetem JavaScript!
	</div>
	<div id="navigation">
		<div id="logo">
			<h1><?php echo anchor('/home', 'Ausleihe') ?></h1>
			media.fh-aachen.de
		</div>

		<?php if ($this->session->logged_in) : ?>

		<h2><?php echo $this->session->userdata('user_name') . ' ' . anchor('/home/logout', 'abmelden') ?></h2>
		<ul>
			<?php foreach ($this->menu_model->user_menu() as $url => $info) : ?>
			<li<?php echo ($this->uri->uri_part($info[1]) == $url) ? ' class="active"' : '' ?>>
				<?php echo anchor($url, $info[0]) ?>
			</li>
			<?php endforeach; ?>
		</ul>

		<?php if ($this->session->userdata('user_role') == USER_ROLE_ADMIN) : ?>
		<h2>Verwaltung</h2>
		<ul>
			<?php foreach ($this->menu_model->admin_menu() as $url => $info) : ?>
			<li<?php echo ($this->uri->uri_part($info[1]) == $url) ? ' class="active"' : '' ?>>
				<?php echo anchor($url, $info[0]) ?>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>

		<?php endif; ?>
	</div>

	<div id="content">
		<script type="text/javascript">var base_url = '<?php echo stripslashes(base_url()) ?>';</script>
		<script type="text/javascript" src="<?php echo base_url() ?>style/jquery-1.5.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url() ?>style/jquery-plugins.js"></script>
		<script type="text/javascript" src="<?php echo base_url() ?>style/ausleihe2.js"></script>