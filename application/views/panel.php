<?php $this->load->view('includes/headerpanel') ?>
    <div class="main-container" id="main-container">
            <?php $this->load->view('includes/sidebar') ?>
            <div class="main-content">
                <div class="main-content-inner">
                    <?php $this->load->view('includes/breadcum') ?>
                    <div class="page-content">						
                        <div class="page-header">
                            <h1>
                                    Escritorio
                                    <small>
                                            <i class="ace-icon fa fa-angle-double-right"></i>
                                    </small>
                            </h1>
                        </div><!-- /.page-header -->
                        <div class="row">
                                <div class="col-xs-12">
                                       <?= empty($crud)?'':$this->load->view('cruds/'.$crud) ?>
                                </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div><!-- /.page-content -->
                </div>
            </div><!-- /.main-content -->			
    </div><!-- /.main-container -->
    <script src="<?= base_url("js/ace.min.js") ?>"></script>
    <script src="<?= base_url("js/jquery-ui.custom.min.js") ?>"></script>	
    <script src="<?= base_url("js/ace-elements.min.js") ?>"></script>
