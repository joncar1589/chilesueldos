<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('main.php');
class Registro extends Main {
        
	public function __construct()
	{
            parent::__construct();
            if(!empty($_SESSION['user']))
                header("Location:".base_url('panel'));
	}
        
        public function index($url = 'main',$page = 0)
	{
            $crud = new ajax_grocery_CRUD();
            $crud->set_theme('registro');
            $crud->set_table('user');
            $crud->set_subject('Usuarios');
            $crud->set_primary_key('id','user');
            $crud->set_primary_key('id','paises');            
            $crud->set_primary_key('id','ciudades');            
            $crud->set_relation('tipodocumento','tipo_documento','denominacion');
            $crud->set_relation('estadocivil','estado_civil','nombre');
            $crud->set_relation('pais','paises','nombre');
            $crud->set_relation('ciudad','ciudades','nombre');
            $crud->set_relation_dependency('ciudad','pais','pais');
            
            //Fields
            $crud->field_type('sexo','enum',array('M','F'))
                 ->field_type('created','invisible')
                 ->field_type('modified','invisible')
                 ->field_type('status','invisible')
                 ->field_type('password','password')
                 ->field_type('password2','password')
                 ->field_type('cuenta','invisible')
                 ->field_type('liquidaciones_free','invisible')
                 ->field_type('fecha_registro','invisible')
                 ->field_type('feriados_free','invisible')
                 ->field_type('finiquitos_free','invisible')
                 ->field_type('saldo','invisible')
                 ->set_field_upload('fotoadjunto','files');
            
            //unsets
            $crud->unset_back_to_list()
                 ->unset_delete()
                 ->unset_read()
                 ->unset_edit()
                 ->unset_list()
                 ->unset_export()
                 ->unset_print()
                 ->unset_fields('premium','preferencial','id');
            //Displays
            $crud->display_as('tipodocumento','Tipo de documento')
                 ->display_as('fechanac','Fecha de Nacimiento')
                 ->display_as('lugarnac','Lugar de nacimiento')
                 ->display_as('estadocivil','Estado Civil')
                 ->display_as('pais','Pais donde habita')
                 ->display_as('ciudad','Ciudad donde habita')
                 ->display_as('codigomovil','Codigo del movil')
                 ->display_as('password','Contraseña')
                 ->display_as('password2','Repetir Contraseña')
                 ->display_as('nrocelular','Numero de celular')
                 ->display_as('fotoadjunto','Foto')
                 ->display_as('cedula','Documento');
            $crud->set_lang_string('insert_success_message','<script>document.location.href="'.base_url('panel').'"</script>');
            $crud->set_lang_string('form_save','Registrarse');
            //Fields types            
            //Validations  
            $crud->required_fields(
                    'nombre','apellido','email',
                    'password','password2',
                    'pais','ciudad','tipodocumento',
                    'lugarnac','sexo','nacionalidad',
                    'direccion','nrocelular','password','usuario','cedula','rut');  
            $crud->set_rules('email','Email','required|valid_email|is_unique[user.email]');
            $crud->set_rules('usuario','Usuario','required|is_unique[user.usuario]');
            $crud->set_rules('password','Contraseña','required|min_length[8]');        
            $crud->set_rules('password2','Repetir Contraseña','required|min_length[8]|matches[password]');        
            $crud->set_rules('rut','Rut','required|is_unique[user.rut]');        
            //Callbacks
            $crud->callback_field('direccion',array($this,'direccionField'));
            $crud->callback_before_insert(array($this,'binsertion'));
            $crud->callback_after_insert(array($this,'ainsertion'));
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'registro';
            $this->loadView($output);
	}
        
        function conectar()
        {
            $this->loadView('predesign/login');
        }
        /* Callbacks */
        function binsertion($post)
        {            
            $post['status'] = 1;
            $post['created'] = date("Y-m-d H:i:s");
            $post['password'] = md5($post['password']);
            $ajustes = $this->db->get('ajustes')->row();                                    
            $post['liquidaciones_free'] = $ajustes->liquidaciones_free;
            $post['feriados_free'] = $ajustes->feriados_free;
            $post['finiquitos_free'] = $ajustes->finiquitos_free;
            $post['fecha_registro'] = date("Y-m-d");
            return $post;
        }
        
        function ainsertion($post,$id)
        {    
            $this->user->login_short($id);            
            return true;
        }
        
        function forget($key = '')
        {
            if(empty($_POST) && empty($key))
            $this->loadView('forget');
            else
            {
                if(empty($key)){
                if(empty($_SESSION['key'])){
                $this->form_validation->set_rules('email','Email','required|valid_email');
                if($this->form_validation->run())
                {
                    $user = $this->db->get_where('user',array('email'=>$this->input->post('email')));
                    if($user->num_rows>0){
                        $_SESSION['key'] = md5(rand(0,2048));
                        $_SESSION['email'] = $this->input->post('email');
                        correo($this->input->post('email'),'reestablecimiento de contraseña',$this->load->view('email/forget',array('user'=>$user->row()),TRUE));
                        $_SESSION['msj'] = $this->success('Los pasos para la restauracion han sido enviados a su correo electronico');
                        header("Location:".base_url('registro/forget'));
                        //$this->loadView(array('view'=>'forget','msj'=>$this->success('Los pasos para la restauracion han sido enviados a su correo electronico')));
                    }
                    else
                    $this->loadView(array('view'=>'forget','msj'=>$this->error('El correo que desea restablecer no esta registrado.')));
                }
                else
                    $this->loadView(array('view'=>'forget','msj'=>$this->error($this->form_validation->error_string())));
                }
                else
                {
                    $this->form_validation->set_rules('email','Email','required|valid_email');
                    $this->form_validation->set_rules('pass','Password','required|min_length[8]');
                    $this->form_validation->set_rules('pass2','Password2','required|min_length[8]|matches[pass]');
                    $this->form_validation->set_rules('key','Llave','required');
                    if($this->form_validation->run())
                    {
                        if($this->input->post('key') == $_SESSION['key'])
                        {
                            $this->db->update('user',array('password'=>md5($this->input->post('pass'))),array('email'=>$_SESSION['email']));
                            session_unset();
                            $this->loadView(array('view'=>'forget','msj'=>$this->success('Se ha restablecido su contraseña')));
                        }
                        else
                            $this->loadView(array('view'=>'recover','msj'=>$this->error('Se ha vencido el plazo para el restablecimiento, solicitelo nuevamente.')));
                    }
                    else{
                        if(empty($_POST['key'])){
                        $this->loadView(array('view'=>'forget','msj'=>$this->error('Se ha vencido el plazo para el restablecimiento, solicitelo nuevamente.')));    
                        session_unset();
                        }
                        else
                        $this->loadView(array('view'=>'recover','key'=>$key,'msj'=>$this->error($this->form_validation->error_string())));
                    }
                }
                }
                else
                {
                    if(!empty($_SESSION['key']) && $key==$_SESSION['key'])
                    {
                        $this->loadView(array('view'=>'recover','key'=>$key));
                    }
                    else
                    $this->loadView(array('view'=>'forget','msj'=>$this->error('Se ha vencido el plazo para el restablecimiento, solicitelo nuevamente.')));
                }
            }
        }
        function loginface()
        {
           $this->form_validation->set_rules('nombre','Nombre','required');
           $this->form_validation->set_rules('apellido','Apellido','required');
           $this->form_validation->set_rules('email','Email','required|valid_email');
           if($this->form_validation->run()){
                $data = array('nombre'=>$_POST['nombre'],'apellido'=>$_POST['apellido']);                
                if($this->db->get_where('user',array('email'=>$_POST['email']))->num_rows>0)
                {
                    $this->db->where('email',$_POST['email']);
                    $this->db->update('user',$data);
                }
                else
                {     
                    $data['email'] = $_POST['email'];                    
                    $ajustes = $this->db->get('ajustes')->row();                                    
                    $data['liquidaciones_free'] = $ajustes->liquidaciones_free;
                    $data['feriados_free'] = $ajustes->feriados_free;
                    $data['finiquitos_free'] = $ajustes->finiquitos_free;
                    $this->db->insert('user',$data);                    
                }
                $this->user->login_short($this->db->get_where('user',array('email'=>$_POST['email']))->row()->id);                
                echo "success";
           }
           else
               echo $this->form_validation->error_string();
        }
        
        function direccionField()
        {
            return form_input('direccion','','id="field-direccion" placeholder="Calle/Avenida/Residencia/Casa"');
        }
		
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */