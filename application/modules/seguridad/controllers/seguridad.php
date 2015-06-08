<?php 
    require_once APPPATH.'/controllers/panel.php';    
    class Seguridad extends Panel{
        function __construct() {
            parent::__construct();
        }
        
        function grupos($x = '',$y = ''){
            $crud = $this->crud_function($x,$y);            
            $crud->set_relation_n_n('funciones','funcion_grupo','funciones','grupo','funcion','{nombre}','priority');            
            $crud->set_relation_n_n('miembros','user_group','user','grupo','user','{nombre} {apellido}','priority');          
            $crud->field_type('escritura','true_false',array('0'=>'No','1'=>'Si'));
            $crud->field_type('lectura','true_false',array('0'=>'No','1'=>'Si'));
            $output = $crud->render();
            $this->loadView($output);
        }               
        
        function funciones($x = '',$y = ''){
            $crud = $this->crud_function($x,$y);            
            $output = $crud->render();
            $this->loadView($output);
        }
        
        function user($x = '',$y = ''){
            $crud = $this->crud_function($x,$y);  
            $crud->field_type('status','true_false',array('0'=>'No','1'=>'Si'));
            $crud->field_type('admin','true_false',array('0'=>'No','1'=>'Si'));
            $crud->unset_columns('password','');
            $crud->set_field_upload('foto','img/fotos');
            $output = $crud->render();
            $this->loadView($output);
        }
        
        function perfil($x = '',$y = ''){
            $this->as['perfil'] = 'user';
            $crud = $this->crud_function($x,$y);    
            $crud->where('id',$this->user->id);
            $crud->fields('nombre','apellido_paterno','apellido_materno','email','password','foto');
            $crud->field_type('password','password');
            $crud->field_type('foto','image',array('path'=>'img/fotos','width'=>'300px','height'=>'300px'));
            $crud->callback_before_update(function($post,$primary){
                if(!empty($primary) && $this->db->get_where('user',array('id'=>$primary))->row()->password!=$post['password'] || empty($primary)){
                    $post['password'] = md5($post['password']);                
                }
                return $post;
            });
            $crud->callback_after_update(function($post,$id){
                $this->user->login_short($id);
            });
            $output = $crud->render();
            $this->loadView($output);
        }
        
        function user_insertion($post,$id = ''){
            if(!empty($id)){
                $post['pass'] = $this->db->get_where('user',array('id'=>$id))->row()->password!=$post['password']?md5($post['password']):$post['password'];
            }
            else $post['pass'] = md5($post['pass']);
            return $post;
        }
        
        function ajustes($x = '',$y = '') {
            $crud = $this->crud_function($x,$y);
            $crud->unset_columns('id');
            $crud->unset_add()
                    ->unset_export()
                    ->unset_print()
                    ->unset_read()
                    ->unset_delete()
                    ->unset_fields('id');
            $crud->display_as('tiempo_gratuidad','Meses de gratuidad');
            //Fields                        
            //unsets            
            //Displays                      
            //Fields types                        
            //Validations                
            $crud->required_fields('tope_colacion', 'tope_locomocion', 'email_paypal', 'costo_preferencial', 'costo_premium', 'costo_empresa');
            $crud->set_rules('email_paypal', 'Email registrado en paypal', 'required|valid_email');
            $this->db->where('reportes.tipo', '1');
            $crud = $this->get_reportes($crud);
            $this->loadView($crud->render());
        }                
        
         function permisos($x = '', $y = '') {
            $crud = $this->crud_function($x, $y);
            $crud->set_relation('user', 'user', 'nombre');
            $crud->set_relation('empresa', 'dbclientes112010', 'nickname');
            $crud->set_primary_key('id2', 'dbclientes2010');
            $crud->fields('empresa', 'user');
            $crud->required_fields('empresa', 'user');
            $crud->unset_export()
                    ->unset_print()
                    ->unset_read()
                    ->unset_columns('priority', 'id')
                    ->field_type('id', 'invisible');
            $this->loadView($crud->render());
        }
    }
?>
