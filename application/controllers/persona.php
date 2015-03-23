<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('panel.php');
class Persona extends Panel {
        
	public function __construct()
	{
		parent::__construct();                
	}
       
        public function index($url = 'main',$page = 0)
	{
		parent::index();
	}
                
        /*Cruds*/                       
        function liquidaciones($x = '',$y = '')
        {
            $crud = parent::liquidaciones($x,$y);
            $crud->field_type('user','invisible');                          
            $crud->where("liquidaciones.empleado = '".$this->querys->get_empleado_id($_SESSION['rut'])."'");
            $crud->columns('empresa','fecha','alcance_liquido');
            $crud->unset_columns('user')
                 ->unset_add()
                 ->unset_edit()
                 ->unset_delete()
                 ->unset_export()
                 ->unset_print();            
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'liquidacion';
            $this->loadView($output);
        }
        
        
        function datos($x = '',$y = '')
        {
            $crud = parent::usuarios($x,$y);
            $crud->field_type('user','invisible');                          
            $crud->where("user.id",$_SESSION['user']);    
            //$crud->set_model('user_privilege');
            
            $crud->unset_fields('preferencial','premium','cuenta','status','password2');
            $crud->unset_columns('user','password2','cuenta','preferencial','premium','status','password')
                 ->unset_add()                 
                 ->unset_delete()
                 ->unset_export()
                 ->unset_print();            
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $this->loadView($output);
        }
        
        function feriado($x = '',$y = '')
        {            
            $crud = parent::feriados($x,$y);
            $crud->field_type('user','invisible');                          
            $crud->where("feriado.empleado = '".$this->querys->get_empleado_id($_SESSION['rut'])."'");
            $crud->columns('empresa','fecha','fecha_final','dias_otorgados');
            $crud->set_relation('empresa','empresas','nombre');
            $crud->add_action('Exportar en pdf',base_url('img/pdf.jpg'),base_url('panel/imprimir_feriado').'/');
            $crud->unset_columns('user')
                 ->unset_add()
                 ->unset_edit()
                 ->unset_delete()
                 ->unset_export()
                 ->unset_print()
                 ->unset_read();;            
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $this->loadView($output);
        }
        /* Callbacks */      
        
        
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */