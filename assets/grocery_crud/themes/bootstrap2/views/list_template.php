<?php
	$this->set_css($this->default_theme_path.'/bootstrap2/bootstrap/css/bootstrap.css');
                  $this->set_css($this->default_theme_path.'/bootstrap2/css/flexigrid.css');
	$this->set_js_lib($this->default_javascript_path.'/'.grocery_CRUD::JQUERY);
	$this->set_js_lib($this->default_javascript_path.'/jquery_plugins/jquery.noty.js');
	$this->set_js_lib($this->default_javascript_path.'/jquery_plugins/config/jquery.noty.config.js');

	if (!$this->is_IE7()) {
		$this->set_js_lib($this->default_javascript_path.'/common/list.js');
	}
                  $this->set_js($this->default_theme_path.'/bootstrap2/bootstrap/js/bootstrap.js');
	$this->set_js($this->default_theme_path.'/bootstrap2/js/cookies.js');
	$this->set_js($this->default_theme_path.'/bootstrap2/js/flexigrid.js');
	$this->set_js($this->default_theme_path.'/bootstrap2/js/jquery.form.js');
	$this->set_js($this->default_javascript_path.'/jquery_plugins/jquery.numeric.min.js');
	$this->set_js($this->default_theme_path.'/bootstrap2/js/jquery.printElement.min.js');                
                  $this->set_js($this->default_theme_path.'/bootstrap2/js/pagination.js');                
	/** Fancybox */
	$this->set_css($this->default_css_path.'/jquery_plugins/fancybox/jquery.fancybox.css');
	$this->set_js($this->default_javascript_path.'/jquery_plugins/jquery.fancybox-1.3.4.js');
	$this->set_js($this->default_javascript_path.'/jquery_plugins/jquery.easing-1.3.pack.js');

	/** Jquery UI */
	$this->load_js_jqueryui();

?>
<script type='text/javascript'>
    var base_url = '<?php echo base_url();?>';
    var subject = '<?php echo $subject?>';
    var ajax_list_info_url = '<?php echo $ajax_list_info_url; ?>';
    var ajax_list = '<?php echo $ajax_list_url; ?>';
    var unique_hash = '<?php echo $unique_hash; ?>';
    var message_alert_delete = "<?php echo $this->l('alert_delete'); ?>";
    var crud_pagin = 1;
    var fragmentos = 1;
    var total_results = <?= $total_results ?>;
</script>


<div id='list-report-error' class='alert alert-danger' style="display:none;"></div>
<div id='list-report-success' class='alert alert-success' <?php if($success_message !== null){?>style="display:block"<?php }else{ ?> style="display:none" <?php }?>><?php
if($success_message !== null){?>
	<p><?php echo $success_message; ?></p>
<?php }
?></div>

<?php echo form_open( $ajax_list_url, 'method="post" id="filtering_form" class="filtering_form" autocomplete = "off" data-ajax-list-info-url="'.$ajax_list_info_url.'"'); ?>
<div class="panel panel-default flexigrid" style='width: 100%;' data-unique-hash="<?php echo $unique_hash; ?>">
    <div class="panel-heading" style='padding:0 15px'>
        <div class="row">
        <div class="col-xs-8">
            <?php if(!$unset_add || !$unset_export || !$unset_print){?>
            <div class='row'>
                    <?php if(!$unset_add){?>
                    <ul class="nav nav-pills pull-left">
                        <li role="presentation"><a style='color:black' href='<?php echo $add_url?>' title='<?php echo $this->l('list_add'); ?> <?php echo $subject?>'><i class='glyphicon glyphicon-plus-sign' style='color:green'></i> <?php echo $this->l('list_add'); ?> <?php echo $subject?></a></li>
                    </ul>
                    <?php }?>
                    <ul class="nav nav-pills pull-right">
                        <?php if(!$unset_export) { ?><li role="presentation"><a style='color:black' class="export-anchor" href="<?php echo $export_url; ?>" target="_blank"><img src='<?= base_url($this->default_theme_path.'/bootstrap2/images/export.png') ?>'> <?php echo $this->l('list_export');?></a></li><?php } ?>
                        <?php if(!$unset_print) { ?><li role="presentation"><a style='color:black' class="print-anchor" href="<?php echo $print_url; ?>"><i class="glyphicon glyphicon-print"></i> <?php echo $this->l('list_print');?></a></li><?php }?>
                    </ul>
            </div>
            <?php } else { echo '<h4>'.$subject.'</h4>'; }?>        
        </div>
        <div align="right" class="col-xs-4">
            Mostrando
            <select name="per_page" id='per_page' class="per_page">
                <?php foreach($paging_options as $option){?>
                        <option value="<?php echo $option; ?>" <?php if($option == $default_per_page){?>selected="selected"<?php }?>><?php echo $option; ?>&nbsp;&nbsp;</option>
                <?php }?>
            </select> por pagina
        </div>
        </div>
    </div>        
        <div id="hidden-operations" class="hidden-operations"></div>    
        <div id='ajax_list' class="ajax_list table-responsive">
                <?php echo $list_view?>
        </div>    
        <div class="panel-footer" style="padding: 0px;">
            <div class="row">
                <div class="col-xs-6" style="line-height: 37px; padding-left:30px;">
                    <i class="glyphicon glyphicon-cd ajax_refresh_and_loading" style="cursor:pointer"></i>
                    <?php $paging_starts_from = "<span id='page-starts-from' class='page-starts-from'>1</span>"; ?>
                    <?php $paging_ends_to = "<span id='page-ends-to' class='page-ends-to'>". ($total_results < $default_per_page ? $total_results : $default_per_page) ."</span>"; ?>
                    <?php $paging_total_results = "<span id='total_items' class='total_items'>$total_results</span>"?>
                    <?php echo str_replace( array('{start}','{end}','{results}'),
                            array($paging_starts_from, $paging_ends_to, $paging_total_results),
                            $this->l('list_displaying')
                       ); ?>
                </div>
                <div class="col-xs-6" align="right">
                    <ul class="pagination"  style="margin:0px;">
                        
                    </ul>
                </div>
            </div>    
        </div>
</div>
<?php echo form_close() ?>
