<?php $this->load->view('includes/header'); ?>            
<?php $this->load->view('includes/banner'); ?>
<!--- Botonera ---->
<?php $this->load->view('includes/botonera'); ?>

<!---- Contenido ------>
<div class="row">
    <div class="col-xs-12">        
        <?= $data ?>
    </div>
</div>
<?php $this->load->view('includes/footer') ?>
