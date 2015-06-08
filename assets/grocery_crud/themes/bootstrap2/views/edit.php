<?php

	$this->set_css($this->default_theme_path.'/bootstrap2/bootstrap/css/bootstrap.css');
        $this->set_js($this->default_theme_path.'/bootstrap2/bootstrap/js/bootstrap.js');
	$this->set_js_lib($this->default_theme_path.'/bootstrap2/js/jquery.form.js');
	$this->set_js_config($this->default_theme_path.'/bootstrap2/js/flexigrid-edit.js');

	$this->set_js_lib($this->default_javascript_path.'/jquery_plugins/jquery.noty.js');
	$this->set_js_lib($this->default_javascript_path.'/jquery_plugins/config/jquery.noty.config.js');
?>
<div class="panel panel-default flexigrid crud-form" style='width: 100%;' data-unique-hash="<?php echo $unique_hash; ?>">
    <div class='panel-heading'>
        <h4 class="panel-title"><?php echo $this->l('form_edit'); ?> <?php echo $subject?></h4>
    </div>
	
    <div class='panel-body' id='main-table-box'>
	<?php echo form_open( $update_url, 'method="post" id="crudForm" autocomplete="off" enctype="multipart/form-data"'); ?>
                <?php
                $counter = 0;
                        foreach($fields as $field)
                        {
                                $even_odd = $counter % 2 == 0 ? 'odd' : 'even';
                                $counter++;
                ?>
                <div class='form-group' id="<?php echo $field->field_name; ?>_field_box">
                        <label for='field-<?= $field->field_name ?>' id="<?php echo $field->field_name; ?>_display_as_box">
                                <?php echo $input_fields[$field->field_name]->display_as; ?><?php echo ($input_fields[$field->field_name]->required)? "<span class='required'>*</span> " : ""; ?> :
                        </label>
                        <?php echo $input_fields[$field->field_name]->input ?>
                </div>
                <?php }?>
                <!-- Start of hidden inputs -->
                        <?php
                                foreach($hidden_fields as $hidden_field){
                                        echo $hidden_field->input;
                                }
                        ?>
                <!-- End of hidden inputs -->
                <?php if ($is_ajax) { ?><input type="hidden" name="is_ajax" value="true" /><?php }?>

                <div id='report-error' style="display:none" class='alert alert-danger'></div>
                <div id='report-success' style="display:none" class='alert alert-success'></div>
                
		<div class="btn-group">			
                    <button id="form-button-save" type='submit' class="btn btn-success"><?php echo $this->l('form_save'); ?></button>
                    <?php if(!$this->unset_back_to_list) { ?>
                    <button type='button' id="save-and-go-back-button"  class="btn btn-default"><?php echo $this->l('form_save_and_go_back'); ?></button>                            
                    <button type='button' id="cancel-button"  class="btn btn-danger"><?php echo $this->l('form_cancel'); ?></button>
                    <?php } ?>		    
		</div>                
	<?php echo form_close(); ?>
    </div>
</div>
<script>
	var validation_url = '<?php echo $validation_url?>';
	var list_url = '<?php echo $list_url?>';

	var message_alert_edit_form = "<?php echo $this->l('alert_edit_form')?>";
	var message_update_error = "<?php echo $this->l('update_error')?>";
        $("input[type='text'],input[type='password']").addClass('form-control');
</script>
