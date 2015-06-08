<!Doctype html>
<html lang="es">
        <head>
                <title><?= empty($title)?'Chilesueldos':$title ?></title>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="google-site-verification" content="EEKa6wNE9pnCMkQu4ir09WLpavmc-yXVkDxDYdfDl2w" />                    
                <?php 
                if(!empty($css_files) && !empty($js_files)):
                foreach($css_files as $file): ?>
                <link type="text/css" rel="stylesheet" href="<?= $file ?>" />
                <?php endforeach; ?>
                <?php foreach($js_files as $file): ?>
                <script src="<?= $file ?>"></script>
                <?php endforeach; ?>                
                <?php endif; ?>                                                                
                <? if(empty($crud) || empty($css_files)): ?>
                    <script src="http://code.jquery.com/jquery-1.10.0.js"></script>		
                    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
                    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
                <? endif ?>
                <script src="<?= base_url('assets/frame.js') ?>"></script>
                <script src="<?= base_url('js/face.js') ?>"></script>
                <script src="<?= base_url('js/google.js') ?>"></script>
                <script>var url = "<?= base_url() ?>"</script>  
                <link href="<?= base_url('css/zozo.tabs.min.css') ?>" rel="stylesheet">
                <link href="<?= base_url('css/zozo.tabs.flat.min.css') ?>" rel="stylesheet">                
                <script src="<?= base_url('js/zozo.tabs.min.js') ?>"></script>
                <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">    
                <link rel="stylesheet" type="text/css" href="<?= base_url('css/ace.min.css') ?>">
                <script src="<?= base_url('js/ace-extra.min.js') ?>"></script>		                
                <link rel="stylesheet" type="text/css" href="<?= base_url('css/style.css') ?>">
        </head>
        <body>
            
            <?php $this->load->view($view); ?>            
            <?php if(!empty($_SESSION['user'])): ?>
            <div class="chatnav hidden-xs">
                <div><a href="<?= base_url('chat') ?>" target="_new"><i class="fa fa-comment-o"></i>¿Necesitas Soporte?</a></div>
            </div>
            <?php endif ?>
        </body>
</html>
