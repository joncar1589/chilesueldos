<?php
function input($name = '',$label = '',$type='text')
{
    return '<div class="form-group">
              <label for="email" class="col-sm-4 control-label">'.$label.'</label>
               <div class="col-sm-8">
                <input type="'.$type.'" class="form-control" name="'.$name.'" id="field-'.$name.'" data-val="required" placeholder="'.$label.'">
                </div>
             </div>';
}

function dropdown_query($nombre,$query,$first='0',$style='')
{
    
    $data = array();
    foreach($query->result() as $q)
    $data[$q->id] = $q->nombre;
    
    return '<div class="form-group">
              <label for="email" class="col-sm-2 control-label">'.$nombre.'</label>
               <div class="col-sm-10">
                    '.form_dropdown($nombre,$data,$first,$style).'
                </div>
             </div>';
}

function img($src = '',$style = '',$url = TRUE,$extra = '')
{
    $path = $url?base_url():'';
    $src = empty($src)?'img/vacio.png':$src;
    return '<img src="'.$path.$src.'" style="'.$style.'" '.$extra.'>';
}
?>
