<?php

class ajax_grocery_CRUD extends grocery_CRUD {

	protected $unset_ajax_extension			= false;
	protected $state_code 			= null;
	private $slash_replacement	= "_agsl_";
	protected $relation_dependency		= array();

	function __construct()
	{
		parent::__construct();

		$this->states[101]='ajax_extension';
                $this->states[302]='cropper';
                
                
                $db_driver = get_instance()->db->platform();
                $model_name = 'grocery_crud/grocery_crud_model_'.$db_driver;
                $model_alias = 'm'.substr(md5(rand()), 0, rand(4,15) );                
                if (file_exists(APPPATH.'models/'.$model_name.'.php')){
                    unset(get_instance()->{$model_name});
                    get_instance()->load->model('grocery_crud_model');
                    get_instance()->load->model('grocery_crud/grocery_crud_generic_model');
                    get_instance()->load->model($model_name,$model_alias);
                    $this->basic_model = get_instance()->{$model_alias};
                }
	}

	public function inline_js($inline_js = '')
	{
		$this->_inline_js($inline_js);
	}


	public function set_relation_dependency($target_field, $source_field, $relation_field_on_source_table)
	{
		$this->relation_dependency[$target_field] = array($target_field, $source_field,$relation_field_on_source_table);
		return $this;
	}

	private function render_relation_dependencies()
	{

		foreach($this->relation_dependency as $dependency)
		{
			$this->render_relation_dependency($dependency[0],$dependency[1],$dependency[2]);
		}

	}

	private function render_relation_dependency($target_field, $source_field, $relation_field_on_source_table){

		$sourceElement = "'#field-$source_field'";
		$targetElement = "'#field-$target_field'";

		$js_text = "
			$(document).ready(function() {
				$($sourceElement).change(function() {
					var selectedValue = $($sourceElement).val();
					//alert('selectedValue'+selectedValue);
					//alert('post:'+'ajax_extension/$target_field/$relation_field_on_source_table/'+encodeURI(selectedValue.replace(/\//g,'$this->slash_replacement')));
					$.post('ajax_extension/$target_field/$relation_field_on_source_table/'+encodeURI(selectedValue.replace(/\//g,'$this->slash_replacement')), {}, function(data) {
					//alert('data'+data);
					var \$el = $($targetElement);
						  var newOptions = data;
						  \$el.empty(); // remove old options
						  \$el.append(\$('<option></option>').attr('value', '').text(''));
						  \$.each(newOptions, function(key, value) {
						    \$el.append(\$('<option></option>')
						       .attr('value', key).text(value.replace(/&(nbsp|amp|quot|lt|gt);/g,' ')));
						    });
						  //\$el.attr('selectedIndex', '-1');
						  \$el.chosen().trigger('liszt:updated');

    	  			},'json');
    	  			$($targetElement).change();
				});
			});
			";

		$this->inline_js($js_text);

	}

	public function render()
	{

		$this->pre_render();

		$this->state_code = $this->getStateCode();

		if( $this->state_code != 0 )
		{
			$this->state_info = $this->getStateInfo();
		}
		else
		{
			throw new Exception('The state is unknown , I don\'t know what I will do with your data!', 4);
			die();
		}

		switch ($this->state_code) {
			case 2://add
					$this->render_relation_dependencies();
					$output = parent::render();
			break;
			case 3://edit
					$this->render_relation_dependencies();
					$output = parent::render();
			break;
			case 6://update
				$this->render_relation_dependencies();
				$output = parent::render();
			break;

			case 101://ajax_extension

				$state_info = $this->getStateInfo();

				$ajax_extension_result = $this->ajax_extension($state_info);

				$ajax_extension_result[""] = "asd";

				echo json_encode($ajax_extension_result);
			die();

			break;
                        case 302: //Cropper
                            $state_info = $this->getStateInfo();

                            $ajax_extension_result = $this->cropper($state_info);

                            $ajax_extension_result[""] = "";

                            echo json_encode($ajax_extension_result);
                            die();
                        break;
			default:

				$output = parent::render();

			break;

		}

		if(empty($output)){
			$output = $this->get_layout();
		}else{
		}

		return $output;
	}



public function getStateInfo()
	{
		$state_code = $this->getStateCode();

		$segment_object = $this->get_state_info_from_url();

		$first_parameter = $segment_object->first_parameter;

		$second_parameter = $segment_object->second_parameter;

		$third_parameter = $segment_object->third_parameter;


		$state_info = (object)array();

		switch ($state_code) {
			case 101: //ajax_extension
				$state_info->target_field_name = $first_parameter;
				$state_info->relation_field_on_source_table = $second_parameter;
				$state_info->filter_value = $third_parameter;

			break;
                        case 302: //Cropper
                            $state_info->target_field_name = $first_parameter;                            
                        break;
			default:
				$state_info = parent::getStateInfo();

		}

		return $state_info;
	}



	protected function ajax_extension($state_info)
	{

		if(!isset($this->relation[$state_info->target_field_name]))
			return false;

		list($field_name, $related_table, $related_field_title, $where_clause, $order_by)  = $this->relation[$state_info->target_field_name];


		$target_field_name = $state_info->target_field_name;

		$relation_field_on_source_table = $state_info->relation_field_on_source_table;

		$filter_value = $state_info->filter_value;

		if(is_int($filter_value)){

			$final_filter_value = $filter_value;

		}else {

				$decoded_filter_value = urldecode($filter_value);

				$replaced_filter_value = str_replace($this->slash_replacement,'/',$decoded_filter_value);

				if(strpos($replaced_filter_value,'/') !== false) {
					$final_filter_value = $this->_convert_date_to_sql_date($replaced_filter_value);

				}else{
					$final_filter_value = $replaced_filter_value;
				}
		}

		$target_field_relation = $this->relation[$target_field_name];

		$result = $this->get_dependency_relation_array($target_field_relation, $relation_field_on_source_table, $final_filter_value);

		return $result;
	}

	protected function get_dependency_relation_array($relation_info, $relation_key_field, $relation_key_value, $limit = null)
	{
		list($field_name , $related_table , $related_field_title, $where_clause, $order_by)  = $relation_info;

		$where_clause = array($relation_key_field => $relation_key_value);
		if(empty($relation_key_value)){
			$relation_array = array();
		}else{
			$relation_array = $this->basic_model->get_relation_array($field_name , $related_table , $related_field_title, $where_clause, $order_by, $limit);
		}
		return $relation_array;
	}



	public function unset_ajax_extension()
	{
		$this->unset_ajax_extension = true;

		return $this;
	}


    //Overriden with the purpose of adding a third parameter, currently not calling parent. It should be changed in future if changes are made to parent.
	protected function get_state_info_from_url()
	{
		$ci = &get_instance();

		$segment_position = count($ci->uri->segments) + 1;
		$operation = 'list';

		$segements = $ci->uri->segments;
		foreach($segements as $num => $value)
		{
			if($value != 'unknown' && in_array($value, $this->states))
			{
				$segment_position = (int)$num;
				$operation = $value; //I don't have a "break" here because I want to ensure that is the LAST segment with name that is in the array.
			}
		}

		$function_name = $this->get_method_name();

		if($function_name == 'index' && !in_array('index',$ci->uri->segments))
			$segment_position++;

		$first_parameter = isset($segements[$segment_position+1]) ? $segements[$segment_position+1] : null;
		$second_parameter = isset($segements[$segment_position+2]) ? $segements[$segment_position+2] : null;
		$third_parameter = isset($segements[$segment_position+3]) ? $segements[$segment_position+3] : null;

		return (object)array('segment_position' => $segment_position, 'operation' => $operation, 'first_parameter' => $first_parameter, 'second_parameter' => $second_parameter, 'third_parameter' => $third_parameter);
	}
        
        protected function get_field_input($field_info, $value = null,$primary_key='')
	{
			$real_type = $field_info->crud_type;

			$types_array = array(
					'integer',
					'text',
					'true_false',
					'string',
					'date',
					'datetime',
					'enum',
					'set',
					'relation',
					'relation_readonly',
					'relation_n_n',
					'upload_file',
					'upload_file_readonly',
					'hidden',
					'password',
					'readonly',
					'dropdown',
					'multiselect',
                                        'tags',
                                        'map',
                                        'editor',
                                        'products',
                                        'image'
			);

			if (in_array($real_type,$types_array)) {
				/* A quick way to go to an internal method of type $this->get_{type}_input .
				 * For example if the real type is integer then we will use the method
				 * $this->get_integer_input
				 *  */
				$field_info->input = $this->{"get_".$real_type."_input"}($field_info,$value,$primary_key);
			}
			else
			{
				$field_info->input = $this->get_string_input($field_info,$value,$primary_key);
			}

		return $field_info;
	}
        
        protected function get_edit_input_fields($field_values = null)
	{
		$fields = $this->get_edit_fields();
		$types 	= $this->get_field_types();

		$input_fields = array();

		foreach($fields as $field_num => $field)
		{
			$field_info = $types[$field->field_name];

			$field_value = !empty($field_values) && isset($field_values->{$field->field_name}) ? $field_values->{$field->field_name} : null;
			if(!isset($this->callback_edit_field[$field->field_name]))
			{
				$field_input = $this->get_field_input($field_info, $field_value,$this->getStateInfo()->primary_key);
			}
			else
			{
				$primary_key = $this->getStateInfo()->primary_key;
				$field_input = $field_info;
				$field_input->input = call_user_func($this->callback_edit_field[$field->field_name], $field_value, $primary_key, $field_info, $field_values);
			}

			switch ($field_info->crud_type) {
				case 'invisible':
					unset($this->edit_fields[$field_num]);
					unset($fields[$field_num]);
					continue;
				break;
				case 'hidden':
					$this->edit_hidden_fields[] = $field_input;
					unset($this->edit_fields[$field_num]);
					unset($fields[$field_num]);
					continue;
				break;
			}

			$input_fields[$field->field_name] = $field_input;
		}

		return $input_fields;
	}
        
        protected function _initialize_variables()
	{
		$ci = &get_instance();
		$ci->load->config('grocery_crud');

		$this->config = (object)array();

		/** Initialize all the config variables into this object */
		$this->config->default_language 	= $ci->config->item('grocery_crud_default_language');
		$this->config->date_format 			= $ci->config->item('grocery_crud_date_format');
		$this->config->default_per_page		= $ci->config->item('grocery_crud_default_per_page');
		$this->config->file_upload_allow_file_types	= $ci->config->item('grocery_crud_file_upload_allow_file_types');
		$this->config->file_upload_max_file_size	= $ci->config->item('grocery_crud_file_upload_max_file_size');
		$this->config->default_text_editor	= $ci->config->item('grocery_crud_default_text_editor');
		$this->config->text_editor_type		= $ci->config->item('grocery_crud_text_editor_type');
		$this->config->character_limiter	= $ci->config->item('grocery_crud_character_limiter');
		$this->config->dialog_forms			= $ci->config->item('grocery_crud_dialog_forms');
		$this->config->paging_options		= $ci->config->item('grocery_crud_paging_options');
                $this->config->map_lib = $ci->config->item('map_lib');
                $this->config->map_lat = $ci->config->item('map_lat');
                $this->config->map_lon = $ci->config->item('map_lon');
		/** Initialize default paths */
		$this->default_javascript_path				= $this->default_assets_path.'/js';
		$this->default_css_path						= $this->default_assets_path.'/css';
		$this->default_texteditor_path 				= $this->default_assets_path.'/texteditor';
		$this->default_theme_path					= $this->default_assets_path.'/themes';

		$this->character_limiter = $this->config->character_limiter;

		if($this->character_limiter === 0 || $this->character_limiter === '0')
		{
			$this->character_limiter = 1000000; //a big number
		}
		elseif($this->character_limiter === null || $this->character_limiter === false)
		{
			$this->character_limiter = 30; //is better to have the number 30 rather than the 0 value
		}
	}
        
        public function set_js_lib($js_file,$url=false)
	{
		$this->js_lib_files[sha1($js_file)] = !$url?base_url().$js_file:$js_file;
		$this->js_files[sha1($js_file)] = !$url?base_url().$js_file:$js_file;
	}
        
        protected function get_tags_input($field_info,$value)
	{		
                    //$this->set_js_lib($this->default_javascript_path.'/jquery_plugins/tags.js');
                    //$this->set_js_config($this->default_javascript_path.'/jquery_plugins/config/tags.js');
                    $this->set_css($this->default_css_path.'/jquery_plugins/tags.css');
                    
                    $str = '';
                    if(!empty($field_info->extras)){
                    foreach($field_info->extras as $e)
                    $str.= '"'.$e.'",';
                    }
                    $input =
                            '<script src="'.base_url('js/tags.js').'"></script>'.                             
                            form_input($field_info->name,$value,'id="field-'.$field_info->name.'" class="tags"').
                            '<script>$("#field-'.$field_info->name.'").tagbox({url:['.$str.']});</script>';
                    return $input;
	}
        
        protected function get_map_input($field_info,$value)
	{		
            $this->set_js_lib($this->config->map_lib,true);
            $this->set_js_config($this->default_javascript_path.'/jquery_plugins/config/map.js');
            $extras = $field_info->extras;
            $width = !empty($extras['width'])?$extras['width']:'400px';
            $height = !empty($extras['height'])?$extras['height']:'400px';
            $lat = !empty($extras['lat'])?$extras['lat']:$this->config->map_lat;
            $lon = !empty($extras['lon'])?$extras['lon']:$this->config->map_lon;
            if(!empty($value)){
                $c = explode(',',$value);
                $lat = str_replace('(','',$c[0]);
                $lon = str_replace(')','',$c[1]);
            }
            return                    
                   '<input type="hidden" value="'.$value.'" name="'.$field_info->name.'" id="field-'.$field_info->name.'">'
                   .'<div id="map_'.$field_info->name.'" style="width:'.$width.'; height:'.$height.'"></div>'
                   .'<script>
                    var '.$field_info->name.' = new mapa(\'map_'.$field_info->name.'\',\''.$lat.'\',\''.$lon.'\');'.
                    $field_info->name.'.initialize();
                    google.maps.event.addListener('.$field_info->name.'.marker,\'dragend\',function(){$("#field-'.$field_info->name.'").val('.$field_info->name.'.marker.getPosition())});
                    </script>';
	}
        
        protected function get_editor_input($field_info,$value)
	{
            $extras = $field_info->extras;
           
		if($extras['type'] == 'text_editor')
		{
			$editor = empty($extras['editor'])?$this->config->default_text_editor:$extras['editor'];
			switch ($editor) {
				case 'ckeditor':
					$this->set_js_lib($this->default_texteditor_path.'/ckeditor/ckeditor.js');
					$this->set_js_lib($this->default_texteditor_path.'/ckeditor/adapters/jquery.js');
					$this->set_js_config($this->default_javascript_path.'/jquery_plugins/config/jquery.ckeditor.config.js');
				break;

				case 'tinymce':
					$this->set_js_lib($this->default_texteditor_path.'/tiny_mce/jquery.tinymce.js');
					$this->set_js_config($this->default_javascript_path.'/jquery_plugins/config/jquery.tine_mce.config.js');
				break;

				case 'markitup':
					$this->set_css($this->default_texteditor_path.'/markitup/skins/markitup/style.css');
					$this->set_css($this->default_texteditor_path.'/markitup/sets/default/style.css');

					$this->set_js_lib($this->default_texteditor_path.'/markitup/jquery.markitup.js');
					$this->set_js_config($this->default_javascript_path.'/jquery_plugins/config/jquery.markitup.config.js');
				break;
			}

			$class_name = $this->config->text_editor_type == 'minimal' ? 'mini-texteditor' : 'texteditor';

			$input = "<textarea id='field-{$field_info->name}' name='{$field_info->name}' class='$class_name' >$value</textarea>";
		}
		else
		{
			$input = "<textarea id='field-{$field_info->name}' name='{$field_info->name}'>$value</textarea>";
		}
		return $input;
	}
        
        
        //Products type
        protected function db_insert($state_info)
	{
		$validation_result = $this->db_insert_validation();
                
		if($validation_result->success)
		{
			$post_data = $state_info->unwrapped_data;

			$add_fields = $this->get_add_fields();

			if($this->callback_insert === null)
			{
				if($this->callback_before_insert !== null)
				{
					$callback_return = call_user_func($this->callback_before_insert, $post_data);

					if(!empty($callback_return) && is_array($callback_return))
						$post_data = $callback_return;
					elseif($callback_return === false)
						return false;
				}

				$insert_data = array();
				$types = $this->get_field_types();
				foreach($add_fields as $num_row => $field)
				{
					/* If the multiselect or the set is empty then the browser doesn't send an empty array. Instead it sends nothing */
					if(isset($types[$field->field_name]->crud_type) && ($types[$field->field_name]->crud_type == 'set' || $types[$field->field_name]->crud_type == 'multiselect') && !isset($post_data[$field->field_name]))
					{
						$post_data[$field->field_name] = array();
					}
                                        
                                        /* If the value it's products */
					

					if(isset($post_data[$field->field_name]) && !isset($this->relation_n_n[$field->field_name]))
					{
						if(isset($types[$field->field_name]->db_null) && $types[$field->field_name]->db_null && is_array($post_data[$field->field_name]) && empty($post_data[$field->field_name]))
						{
							$insert_data[$field->field_name] = null;
						}
						elseif(isset($types[$field->field_name]->db_null) && $types[$field->field_name]->db_null && $post_data[$field->field_name] === '')
						{
							$insert_data[$field->field_name] = null;
						}
						elseif(isset($types[$field->field_name]->crud_type) && $types[$field->field_name]->crud_type == 'date')
						{
							$insert_data[$field->field_name] = $this->_convert_date_to_sql_date($post_data[$field->field_name]);
						}
						elseif(isset($types[$field->field_name]->crud_type) && $types[$field->field_name]->crud_type == 'readonly')
						{
							//This empty if statement is to make sure that a readonly field will never inserted/updated
						}
						elseif(isset($types[$field->field_name]->crud_type) && ($types[$field->field_name]->crud_type == 'set' || $types[$field->field_name]->crud_type == 'multiselect'))
						{
							$insert_data[$field->field_name] = !empty($post_data[$field->field_name]) ? implode(',',$post_data[$field->field_name]) : '';
						}
						elseif(isset($types[$field->field_name]->crud_type) && $types[$field->field_name]->crud_type == 'datetime'){
							$insert_data[$field->field_name] = $this->_convert_date_to_sql_date(substr($post_data[$field->field_name],0,10)).
																		substr($post_data[$field->field_name],10);
						}
						else
						{
							$insert_data[$field->field_name] = $post_data[$field->field_name];
						}
					}
				}

				$insert_result =  $this->basic_model->db_insert($insert_data);

				if($insert_result !== false)
				{
					$insert_primary_key = $insert_result;
				}
				else
				{
					return false;
				}

				if(!empty($this->relation_n_n))
				{
					foreach($this->relation_n_n as $field_name => $field_info)
					{
						$relation_data = isset( $post_data[$field_name] ) ? $post_data[$field_name] : array() ;
						$this->db_relation_n_n_update($field_info, $relation_data  ,$insert_primary_key);
					}
				}
                                
                                foreach($add_fields as $num_row => $field)
				{					
                                    if(isset($types[$field->field_name]->crud_type) && ($types[$field->field_name]->crud_type == 'products') && !isset($post_data[$field->field_name]))
                                    {      
                                       $f = '';
                                       $extras = $types[$field->field_name]->extras;
                                       //$table = get_instance()->db->query("SHOW COLUMNS FROM ".$extras['table']);                                       
                                       $table = get_instance()->db->field_data($extras['table']);        
                                       foreach($_POST as $p=>$v)
                                       {
                                           $x = explode("_",$p);                                           
                                           if($x[0]==$field->field_name)
                                           {                                               
                                               if($f!=$x[1]){
                                                   $f = $x[1];
                                                   $data = array();                                                                                                                  
                                                   foreach($table as $t){
                                                   $t->Field = $t->name;
                                                   if(isset($_POST[$x[0].'_'.$x[1].'_'.$t->Field]))$data[$t->Field] = $_POST[$x[0].'_'.$x[1].'_'.$t->Field];
                                                   }
                                                   $data[$extras['relation_field']] = $insert_primary_key;
                                                   get_instance()->db->insert($extras['table'],$data);
                                               }
                                           }
                                       }
                                    }
                                }

				if($this->callback_after_insert !== null)
				{
					$callback_return = call_user_func($this->callback_after_insert, $post_data, $insert_primary_key);

					if($callback_return === false)
					{
						return false;
					}

				}
			}else
			{
					$callback_return = call_user_func($this->callback_insert, $post_data);

					if($callback_return === false)
					{
						return false;
					}
			}

			if(isset($insert_primary_key))
				return $insert_primary_key;
			else
				return true;
		}

		return false;

	}
        
        protected function db_update($state_info)
	{
		$validation_result = $this->db_update_validation();

		$edit_fields = $this->get_edit_fields();

		if($validation_result->success)
		{
			$post_data 		= $state_info->unwrapped_data;
			$primary_key 	= $state_info->primary_key;

			if($this->callback_update === null)
			{
				if($this->callback_before_update !== null)
				{
					$callback_return = call_user_func($this->callback_before_update, $post_data, $primary_key);

					if(!empty($callback_return) && is_array($callback_return))
					{
						$post_data = $callback_return;
					}
					elseif($callback_return === false)
					{
						return false;
					}

				}

				$update_data = array();
				$types = $this->get_field_types();
				foreach($edit_fields as $num_row => $field)
				{
					/* If the multiselect or the set is empty then the browser doesn't send an empty array. Instead it sends nothing */
					if(isset($types[$field->field_name]->crud_type) && ($types[$field->field_name]->crud_type == 'set' || $types[$field->field_name]->crud_type == 'multiselect') && !isset($post_data[$field->field_name]))
					{
						$post_data[$field->field_name] = array();
					}
                                        
                                        if(isset($types[$field->field_name]->crud_type) && ($types[$field->field_name]->crud_type == 'products'))
                                        {      
                                           $f = '';                                                   
                                           $extras = $types[$field->field_name]->extras;
                                           get_instance()->db->where($extras['relation_field'],$primary_key);
                                           get_instance()->db->delete($extras['table']);
                                           //$table = get_instance()->db->query("SHOW COLUMNS FROM ".$extras['table']);                                       
                                           $table = get_instance()->db->field_data($extras['table']);
                                           
                                           foreach($_POST as $p=>$v)
                                           {
                                               $x = explode("_",$p);                                           
                                               if($x[0]==$field->field_name)
                                               {                                               
                                                   if($f!=$x[1]){
                                                       $f = $x[1];
                                                       $data = array();                                                                                                                  
                                                       foreach($table as $t){
                                                       $t->Field = $t->name;
                                                       if(isset($_POST[$x[0].'_'.$x[1].'_'.$t->Field]))$data[$t->Field] = $_POST[$x[0].'_'.$x[1].'_'.$t->Field];
                                                       }
                                                       $data[$extras['relation_field']] = $primary_key;
                                                       get_instance()->db->insert($extras['table'],$data);
                                                   }
                                               }
                                           }
                                        }

					if(isset($post_data[$field->field_name]) && !isset($this->relation_n_n[$field->field_name]))
					{
						if(isset($types[$field->field_name]->db_null) && $types[$field->field_name]->db_null && is_array($post_data[$field->field_name]) && empty($post_data[$field->field_name]))
						{
							$update_data[$field->field_name] = null;
						}
						elseif(isset($types[$field->field_name]->db_null) && $types[$field->field_name]->db_null && $post_data[$field->field_name] === '')
						{
							$update_data[$field->field_name] = null;
						}
						elseif(isset($types[$field->field_name]->crud_type) && $types[$field->field_name]->crud_type == 'date')
						{
							$update_data[$field->field_name] = $this->_convert_date_to_sql_date($post_data[$field->field_name]);
						}
						elseif(isset($types[$field->field_name]->crud_type) && $types[$field->field_name]->crud_type == 'readonly')
						{
							//This empty if statement is to make sure that a readonly field will never inserted/updated
						}
						elseif(isset($types[$field->field_name]->crud_type) && ($types[$field->field_name]->crud_type == 'set' || $types[$field->field_name]->crud_type == 'multiselect'))
						{
							$update_data[$field->field_name] = !empty($post_data[$field->field_name]) ? implode(',',$post_data[$field->field_name]) : '';
						}
						elseif(isset($types[$field->field_name]->crud_type) && $types[$field->field_name]->crud_type == 'datetime'){
							$update_data[$field->field_name] = $this->_convert_date_to_sql_date(substr($post_data[$field->field_name],0,10)).
																		substr($post_data[$field->field_name],10);
						}
						else
						{
							$update_data[$field->field_name] = $post_data[$field->field_name];
						}
					}
				}

				if($this->basic_model->db_update($update_data, $primary_key) === false)
				{
					return false;
				}

				if(!empty($this->relation_n_n))
				{
					foreach($this->relation_n_n as $field_name => $field_info)
					{
						if (   $this->unset_edit_fields !== null
							&& is_array($this->unset_edit_fields)
							&& in_array($field_name,$this->unset_edit_fields)
						) {
								continue;
						}

						$relation_data = isset( $post_data[$field_name] ) ? $post_data[$field_name] : array() ;
						$this->db_relation_n_n_update($field_info, $relation_data ,$primary_key);
					}
				}

				if($this->callback_after_update !== null)
				{
					$callback_return = call_user_func($this->callback_after_update, $post_data, $primary_key);

					if($callback_return === false)
					{
						return false;
					}

				}
			}
			else
			{
				$callback_return = call_user_func($this->callback_update, $post_data, $primary_key);

				if($callback_return === false)
				{
					return false;
				}
			}

			return true;
		}
		else
		{
			return false;
		}
	}
        
        protected function db_delete($state_info)
	{
		$primary_key 	= $state_info->primary_key;

		if($this->callback_delete === null)
		{
			if($this->callback_before_delete !== null)
			{
				$callback_return = call_user_func($this->callback_before_delete, $primary_key);

				if($callback_return === false)
				{
					return false;
				}

			}

			if(!empty($this->relation_n_n))
			{
				foreach($this->relation_n_n as $field_name => $field_info)
				{
					$this->db_relation_n_n_delete( $field_info, $primary_key );
				}
			}
                        
                        $types = $this->get_field_types();
                        $add_fields = $this->get_add_fields();                        
                        foreach($add_fields as $num_row => $field)
                        {					
                            if(isset($types[$field->field_name]->crud_type) && ($types[$field->field_name]->crud_type == 'products') && !isset($post_data[$field->field_name]))
                            {      
                               $extras = $types[$field->field_name]->extras;
                               get_instance()->db->where($extras['relation_field'],$primary_key);
                               get_instance()->db->delete($extras['table']);
                            }
                            
                            if(isset($types[$field->field_name]->crud_type) && ($types[$field->field_name]->crud_type == 'image') && !isset($post_data[$field->field_name]))
                            {      
                                $extras = $types[$field->field_name]->extras;                                
                                $delete_result = $this->basic_model->db_delete_cropper($primary_key,$field->field_name,$extras['path']);
                            }
                        }

			$delete_result = $this->basic_model->db_delete($primary_key);

			if($delete_result === false)
			{
				return false;
			}

			if($this->callback_after_delete !== null)
			{
				$callback_return = call_user_func($this->callback_after_delete, $primary_key);

				if($callback_return === false)
				{
					return false;
				}

			}                        
		}
		else
		{
			$callback_return = call_user_func($this->callback_delete, $primary_key);

			if($callback_return === false)
			{
				return false;
			}
		}

		return true;
	}
        
        protected function get_products_input($field_info,$value,$primary_key)
	{
            
            //Para la parte de edicion, el campo primary_key va a traer el id del producto para hacer la consulta y renderizar todas las columnas
            $this->load_js_chosen();
            $this->set_js_config($this->default_javascript_path.'/jquery_plugins/config/jquery.chosen.config.js');
            $this->set_js_config($this->default_javascript_path.'/jquery_plugins/config/products.js');
            $extras = $field_info->extras;
                        
            if(!empty($extras['source'])){
                $data = array(''=>'Seleccione '.$extras['dropdown']);
                if(!is_array($extras['source'])){
                if(!empty($extras['or_where']))
                {
                    foreach($extras['or_where'] as $o){
                        foreach($o as $p=>$z)
                            get_instance()->db->or_where($p,$z);
                    }
                }
                
                $source = get_instance()->db->get($extras['source']);    //Se consulta el contenido del select                                            
                foreach($source->result() as $x)
                    $data[$x->id] = $x->{$extras['field_detalle']};
                }
                else
                {
                    foreach($extras['source'] as $x=>$y)
                    $data[$x] = $y;
                }
            }
            else $extras['dropdown'] = '';
            
            $inputs = '<div class="productitem" style="width:100%; text-align: left;">';      
            //$table = get_instance()->db->query("SHOW COLUMNS FROM `{$extras['table']}`");            
            $table = get_instance()->db->field_data($extras['table']);            
            foreach($table as $x){
                $x->Field = $x->name;
                $class = !empty($extras['class'][$x->Field])?'class="'.$extras['class'][$x->Field].'"':'';
                switch($x->Field){
                    case $extras['dropdown']: 
                        $select = '<select name="'.$field_info->name.'_x3x_'.$x->Field.'" id="field-'.$field_info->name.'_x3x_" data-placeholder="'.$field_info->name.'" '.$class.'>';                        
                        foreach($data as $x=>$y)
                            $select.='<option value="'.$x.'">'.$y.'</option>';
                        $select.= '</select>';
                        $inputs.= $select;
                    break;
                    case 'id':
                    case $extras['relation_field']:
                    case 'user': $inputs.=''; break;
                    default: 
                        $placeholder = !empty($extras['display_as'][$x->Field])?$extras['display_as'][$x->Field]:$x->Field;                        
                        $inputs.= form_input($field_info->name.'_x3x_'.$x->Field,'',$class.' id="field-'.$field_info->name.'_x3x_'.$x->Field.'" style="width:100px" placeholder="'.$placeholder.'"').' ';
                    break;
                }
            }                       
            $enlaces = '<a href="javascript:addFieldProduct(\\\'#'.$field_info->name.'_input_box\\\','.$field_info->name.'FieldProduct,\\\''.$field_info->name.'\\\')"><i class="fa fa-plus"></i></a> <a href="javascript:removeFieldProduct(\\\'#ax4x\\\')" id="ax4x" style="color:red"><i class="glyphicon glyphicon-remove"></i></a></div>';
            $inputs.= $enlaces;
            //Aqui viene el edit           
            if(!empty($primary_key))
            {
                $edits = get_instance()->db->get_where($extras['table'],array($extras['relation_field']=>$primary_key));
                $editfield = '';
                $y = 0;
                foreach($edits->result() as $e){
                $editfield.= '<div class="productitem" style="width:100%; text-align: left;">';
                foreach($table as $x){
                $x->Field = $x->name;
                $d = $edits->row($y);
                $class = !empty($extras['class'][$x->Field])?'class="'.$extras['class'][$x->Field].'"':'';
                switch($x->Field){
                    case $extras['dropdown']: 
                        $editfield.= form_dropdown($field_info->name.'_'.$y.'_'.$x->Field,$data,$d->{$x->Field},'id="field-'.$field_info->name.'_'.$y.'_" data-placeholder="'.$field_info->name.'" '.$class.'');                        
                    break;
                    case 'id':
                    case $extras['relation_field']:
                    case 'user': $inputs.=''; break;
                    $placeholder = !empty($extras['display_as'][$x->Field])?$extras['display_as'][$x->Field]:$x->Field;                    
                    default: $editfield.= form_input($field_info->name.'_'.$y.'_'.$x->Field,$d->{$x->Field},$class.' id="field-'.$field_info->name.'_'.$y.'_'.$x->Field.'" style="width:50px" placeholder="'.$placeholder.'"').' '; break;
                }                
                }
                $y++;
                $editfield.='<a href="javascript:addFieldProduct(\'#'.$field_info->name.'_input_box\','.$field_info->name.'FieldProduct,\''.$field_info->name.'\')"><i class="fa fa-plus"></i></a> <a href="javascript:removeFieldProduct(\'#aa'.$y.'\')" id="aa'.$y.'" style="color:red"><i class="glyphicon glyphicon-remove"></i></a></div>';
                }
            }
            
            //Salida
            $str =  '<script>'.$field_info->name.'FieldProduct = \''.$inputs.'\'; FieldProductId[\''.$field_info->name.'\'] = 0; console.log(FieldProductId); ';
            if(empty($primary_key) || $edits->num_rows==0)$str.= 'addFieldProduct(\'#'.$field_info->name.'_input_box\','.$field_info->name.'FieldProduct,\''.$field_info->name.'\')';
            $str.= '</script>';            
            if(!empty($primary_key))$str.= $editfield.'<script>FieldProductId[\''.$field_info->name.'\'] = '.$y.'</script>';            
            return $str;
	}
        
        function cropper($state_info)
        {
            switch($state_info->target_field_name)
            {
                case 'crop': 
                    if(!empty($_POST)){
                    $imgUrl = $_POST['imgUrl'];
                    $imgInitW = $_POST['imgInitW'];
                    $imgInitH = $_POST['imgInitH'];
                    $imgW = $_POST['imgW'];
                    $imgH = $_POST['imgH'];
                    $imgY1 = $_POST['imgY1'];
                    $imgX1 = $_POST['imgX1'];
                    $cropW = $_POST['cropW'];
                    $cropH = $_POST['cropH'];
                    $jpeg_quality = 100;
                    $name = explode("/",$imgUrl);
                    $name = $name[count($name)-1];
                    $output_filename = $_POST['url'].'/'.$name;
                    $output_filename = explode(".",$output_filename);
                    $output_filename = $output_filename[0].rand();
                    $what = getimagesize($imgUrl);
                    switch(strtolower($what['mime']))
                    {
                        case 'image/png':
                            $img_r = imagecreatefrompng($imgUrl);
                                    $source_image = imagecreatefrompng($imgUrl);
                                    $type = '.png';
                            break;
                        case 'image/jpeg':
                            $img_r = imagecreatefromjpeg($imgUrl);
                                    $source_image = imagecreatefromjpeg($imgUrl);
                                    $type = '.jpeg';
                            break;
                        case 'image/gif':
                            $img_r = imagecreatefromgif($imgUrl);
                                    $source_image = imagecreatefromgif($imgUrl);
                                    $type = '.gif';
                            break;
                        default: die('image type not supported');
                    }
                    $resizedImage = imagecreatetruecolor($imgW, $imgH);
                    imagecopyresampled($resizedImage, $source_image, 0, 0, 0, 0, $imgW,$imgH, $imgInitW, $imgInitH);	
                    $dest_image = imagecreatetruecolor($cropW, $cropH);
                    imagecopyresampled($dest_image, $resizedImage, 0, 0, $imgX1, $imgY1, $cropW,$cropH, $cropW, $cropH);	
                    imagejpeg($dest_image, $output_filename.$type, $jpeg_quality);
                    $response = array("status" => 'success',"url" => base_url($output_filename.$type ));
                    unlink($_POST['url'].'/'.$name);
                     return $response;
                    }
                    else
                    return array('status'=>'Fail, Faltan datos');
                break;
                case 'save': 
                    $imagePath = $_POST['url'].'/';
                    $allowedExts = array("gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG");
                    $temp = explode(".", $_FILES["img"]["name"]);
                    $extension = end($temp);
                    if ( in_array($extension, $allowedExts))
                      {
                      if ($_FILES["img"]["error"] > 0)
                            {
                                     $response = array(
                                            "status" => 'error',
                                            "message" => 'ERROR Return Code: '. $_FILES["img"]["error"],
                                    );
                                    echo "Return Code: " . $_FILES["img"]["error"] . "<br>";
                            }
                      else
                            {

                              $filename = $_FILES["img"]["tmp_name"];
                              list($width, $height) = getimagesize( $filename );

                              move_uploaded_file($filename,  $imagePath . $this->_unique_field_name($_FILES["img"]["name"]).'.'.$extension);

                              $response = array(
                                    "status" => 'success',
                                    "url" => base_url($imagePath.$this->_unique_field_name($_FILES["img"]["name"]).'.'.$extension),
                                    "width" => $width,
                                    "height" => $height
                              );

                            }
                      }
                    else
                      {
                       $response = array(
                                    "status" => 'error',
                                    "message" => 'something went wrong',
                            );
                      }

                      return $response;
                break;
                
                case 'delete_crop':
                    if(file_exists($_POST['path']."/".$_POST['url']))
                    {
                            if( unlink($_POST['path']."/".$_POST['url']) )
                            {
                                    $this->basic_model->db_file_delete($_POST['field'],$_POST['url']);

                                    return array('success'=>true);
                            }
                            else
                            {
                                    return array('success'=>false);
                            }
                    }
                    else
                    {
                            $this->basic_model->db_file_delete($_POST['field'],$_POST['url']);
                            return array('success'=>true);
                    }
                break;
            }
        }
        
        function get_image_input($field_info,$value,$primary_key)
        {
            $this->set_css($this->default_css_path.'/jquery_plugins/cropper/croppic.css');
            $this->set_js_lib($this->default_javascript_path.'/jquery_plugins/croppic.min.js');
            $this->set_js_config($this->default_javascript_path.'/jquery_plugins/config/cropper.js',true);
            $extras = $field_info->extras;
            $url = $extras['path'];
            $str = '<script>var url = \''.base_url().'\'; var crop_upload_url = \''.$url.'\'; croppers.push(\'crop-'.$field_info->name.'\');</script>';
            
            if(empty($primary_key))
            $input = '<input style="visibility:hidden" type="file" name="'.$field_info->name.'" id="'.'field-'.$field_info->name.'">';
            else
            $input = '<input style="visibility:hidden" type="input" value="'.$value.'" name="'.$field_info->name.'" id="'.'field-'.$field_info->name.'">';
            return '
                    <div class="crop" style="width:'.$extras['width'].'; height:'.$extras['height'].'; position:relative;" id="crop-'.$field_info->name.'">
                    '.$input.'
                    </div>
                '.$str;
        }

}
