<?php if(!empty($list)): ?>
<?php 
    $column_width = (int)(80/count($columns));    
?>
<div style="overflow-x: visible !important; overflow-y: visible !important;">
    <table class="table table-bordered table-condensed table-striped">                        
    <thead>
            <tr>                    
                    <?php foreach($columns as $column){?>
                    <th style='cursor:pointer;'>                        
                        <?php echo $column->display_as?>
                        <span id="th_<?= $column->field_name ?>"></span>
                    </th>
                    <?php }?>
                    <?php if(!$unset_delete || !$unset_edit || !$unset_read || !empty($actions)){?>
                    <th align="right" abbr="tools" axis="col1" class="" width='20%'>
                            <div class="text-right">
                                    <?php echo $this->l('list_actions'); ?>
                            </div>
                    </th>
                    <?php }?>
            </tr>
    </thead>		
    <tbody>
        <!--- Form Search ---->
        <tr>                    
                <?php foreach($columns as $column):?>
                <th>                        
                    <!--<?php echo $column->display_as?>
                    <span id="th_<?= $column->field_name ?>"></span>-->
                    <input type="hidden" name="search_field[]" value="<?= $column->field_name ?>">
                    <?= form_input('search_text[]','','class="form-control" placeholder="'.$column->display_as.'"') ?>
                </th>
                <?php endforeach?>                
        </tr>
        <?php foreach($list as $num_row => $row){ ?>        
            <tr <?php if($num_row % 2 == 1){?>class="erow"<?php }?>>
                <?php foreach($columns as $column){?>
                <td width='<?php echo $column_width?>%' class='<?php if(isset($order_by[0]) &&  $column->field_name == $order_by[0]){?>sorted<?php }?>'>
                        <div class='text-left'><?php echo $row->{$column->field_name} != '' ? $row->{$column->field_name} : '&nbsp;' ; ?></div>
                </td>
                <?php }?>
                <?php if(!$unset_delete || !$unset_edit || !$unset_read || !empty($actions)){ ?>
                <td align="left" width='20%'>  
                    
                    <div align="right" class="btn-group" style="white-space: nowrap; width:100%;">                        
                        <button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true" style="float: none; display: inline-block; margin-left: -4px; padding: 0 10px; font-size:12px;">Seleccione una acción<span class="caret" style="margin-top:0px;"></span></button>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" style="position: relative; margin-left: 18%;">
                          <?php if(!$unset_read){?>
                          <li role="presentation">
                              <a href='<?php echo $row->read_url?>' title='<?php echo $this->l('list_view')?> <?php echo $subject?>' class="edit_button">
                                    <i class="glyphicon glyphicon-search"></i> Ver
                              </a>
                          </li>
                          <?php }?>
                          <?php if(!$unset_edit){?>
                          <li role="presentation">
                              <a href='<?php echo $row->edit_url?>' title='<?php echo $this->l('list_edit')?> <?php echo $subject?>' class="edit_button">
                                    <i class="glyphicon glyphicon-edit"></i> Editar
                              </a>
                          </li>
                          <?php }?>       
                          <?php if(!$unset_delete){?>
                          <li role="presentation">
                              <a href='<?php echo $row->delete_url?>' title='<?php echo $this->l('list_delete')?> <?php echo $subject?>' class="delete-row" >
                                    <i class="glyphicon glyphicon-remove"></i> Borrar
                              </a>
                          </li>
                          <?php }?>
                          <?php 
                                if(!empty($row->action_urls)){
                                foreach($row->action_urls as $action_unique_id => $action_url){ 
                                        $action = $actions[$action_unique_id];
                            ?>
                          <li role="presentation">
                              <a href="<?php echo $action_url; ?>" class="<?php echo $action->css_class; ?> crud-action"><?php 
                                    ?><?= $action->label ?><?php 	
                                ?></a>
                          </li>
                          <?php }
                            }?>
                        </ul>
                  </div>
                    
                </td>
                <?php } ?>
            </tr>
        <?php } ?>  
    </tbody>
</table>
</div>
<?php else: ?>
Sin datos para mostrar
<?php endif; ?>
