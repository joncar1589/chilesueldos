<?php 
    if(!$unset_delete || !$unset_edit || !$unset_read || !empty($actions)){               
        foreach($list as $n=>$l){
            $list[$n]->actions = '';
            if(!$unset_read){
                 $list[$n]->actions.= '<a href="'.$l->read_url.'" title="'.$this->l('list_view').$subject.'" class="edit_button"><i class="glyphicon glyphicon-list-alt"></i></a> ';
            }
            if(!$unset_edit){
                 $list[$n]->actions.= '<a style="color:blue" href="'.$l->edit_url.'" title="'.$this->l('list_edit').$subject.'" class="edit_button"><i class="glyphicon glyphicon-edit"></i></a> ';
            }
            if(!$unset_delete){
                 $list[$n]->actions.= '<a style=color:red href="'.$l->delete_url.'" title="'.$this->l('list_delete').$subject.'" class="delete-row" ><i class="glyphicon glyphicon-remove"></i></a> ';
            }
        }
    }
 ?>
<?php echo json_encode($list) ?>