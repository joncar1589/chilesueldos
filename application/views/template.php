<!Doctype html>
<html lang="es">
	<head>
		<title><?= empty($title)?'Chilesueldos':$title ?></title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="google-site-verification" content="EEKa6wNE9pnCMkQu4ir09WLpavmc-yXVkDxDYdfDl2w" />
		<script src="http://code.jquery.com/jquery-1.9.0.js"></script>
		<script src="<?= base_url('assets/bootstrap/js/bootstrap.min.js') ?>"></script>
		<script src="<?= base_url('assets/frame.js') ?>"></script>
                <script src="<?= base_url('js/face.js') ?>"></script>
                <script src="<?= base_url('js/google.js') ?>"></script>
                <script>var url = "<?= base_url() ?>"</script>
		<link rel="stylesheet" type="text/css" href="<?= base_url('assets/bootstrap/css/bootstrap.min.css') ?>">
                <link rel="stylesheet" type="text/css" href="<?= base_url('css/font-awesome.css') ?>">
                <link rel="stylesheet" type="text/css" href="<?= base_url('css/style.css') ?>">
		<?php 
		if(!empty($css_files) && !empty($js_files)):
		foreach($css_files as $file): ?>
		<link type="text/css" rel="stylesheet" href="<?= $file ?>" />
		<?php endforeach; ?>
		<?php foreach($js_files as $file): ?>
		<script src="<?= $file ?>"></script>
		<?php endforeach; ?>                
                <?php endif; ?>                
                <!-- Respond.js proxy on external server -->       
                <?php
                    if(!empty($_SESSION['remember']))
                    setcookie("user",$_SESSION['user'],time()+60*60*24*30);                
                    elseif(isset($_SESSION['user']) && $_SESSION['user']=='clean'){
                        setcookie("user",'',time()-3600);                
                        $this->user->unlog();
                    }
                    elseif(!empty($_COOKIE['user']))
                    $this->user->login_short($_COOKIE['user']);
                ?>
                <link href="<?= base_url('css/zozo.tabs.min.css') ?>" rel="stylesheet">
                <link href="<?= base_url('css/zozo.tabs.flat.min.css') ?>" rel="stylesheet">                
                <script src="<?= base_url('js/zozo.tabs.min.js') ?>"></script>
	</head>
	<body>
            
            <?php $this->load->view('includes/header'); ?>            
            
            <?php $this->load->view($view); ?>
            
            <?php $this->load->view('includes/footer') ?>
            <?php if(!empty($_SESSION['user'])): ?>
            <div class="chatnav hidden-xs">
                <div><a href="<?= base_url('chat') ?>" target="_new"><i class="fa fa-comment-o"></i>¿Necesitas Soporte?</a></div>
            </div>
            <?php endif ?>
        </body>
</html>