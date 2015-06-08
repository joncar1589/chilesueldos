<?php
	$this->set_css($this->default_theme_path.'/bootstrap/bootstrap/css/bootstrap.css');
        $this->set_js($this->default_theme_path.'/bootstrap/bootstrap/js/bootstrap.js');	
?>
<div class="panel panel-default flexigrid crud-form" style='width: 100%;' data-unique-hash="<?php echo $unique_hash; ?>">
    <div class='panel-heading'>
        <h4 class="panel-title"><?php echo $this->l('list_record'); ?> <?php echo $subject?></h4>
    </div>	
    <div class='panel-body' id='main-table-box'>
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
                            <div class="form-control"><?php echo $input_fields[$field->field_name]->input ?></div>
                    </div>                            
            <?php }?>
            <div id='report-error' class='report-div error'></div>
            <div id='report-success' class='report-div success'></div>
            <div class="btn-group">			
                <a href='<?= $list_url ?>' type='button' class="btn btn-default"><?php echo $this->l('form_back_to_list'); ?></a>
            </div>
    </div>
</div>