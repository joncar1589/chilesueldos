<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('panel.php');
class Admin extends Panel {
        
	public function __construct()
	{
		parent::__construct();  
                if($_SESSION['cuenta']!=1)
                    header("Location:".base_url('panel'));
	}
       
        public function index($url = 'main',$page = 0)
	{
		parent::index();
	}                    
        /*Cruds*/  
        function loadView($crud){
            $crud->title='Admin / '.$crud->title;
            parent::loadView($crud);
        }
        function logs($x = '', $y = '')
        {            
            $crud = parent::logs($x,$y);                                    
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'empresa';
            $output->title = 'Empresas';
            $this->loadView($output);
        }     
        function empresas($x = '', $y = '')
        {            
            $crud = parent::empresa($x,$y);                        
            $crud->field_type('user','invisible');  
            $crud->required_fields('nickname','pais_id','ciudad_id','comuna_id','tipo','numero','calle','rut');
            $crud->columns('id2','nickname','pais_id','ciudad_id','comuna_id','tipo','rut_legal','REPRESENTANTE_LEGAL','numero','calle','rut','user');                        
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'empresa';
            $output->title = 'Empresas';
            $this->loadView($output);
        }       
        function empleados($x = '', $y = '')
        {            
            $crud = parent::empleados($x,$y);            
            $crud->set_relation('user','user','nombre');
            $crud->columns('empresa','apellido_paterno','nombre','rut','anexos');
            $crud->callback_field('hora_entrada',function($val,$id){
            $value = !empty($id)?get_instance()->db->get_where('trabajadores',array('id'=>$id))->row()->hora_salida:'';
                return 'De '.form_input('hora_entrada',$val,'id="field-hora_entrada" style="width:100px"').' Hasta. '.form_input('hora_salida',$value,'id="field-hora_salida"  style="width:100px"');
            });
            $crud->callback_column('anexos',function($val,$row){return '<a href="'.base_url('empresa/anexos/'.$row->id).'">'.  get_instance()->db->get_where('anexos',array('trabajador'=>$row->id))->num_rows.'</a>';});
            $crud->display_as('hora_entrada','Horario Normal');
            $crud->callback_field('empresa',function($val){                            
                $data = array();
                foreach(get_instance()->db->get('dbclientes112010')->result() as $e)
                $data[$e->id2] = $e->nickname;
                return form_dropdown('empresa',$data,$val,'id="field-empresa" class="chosen-select"');
            });
            $crud->callback_column('empresa',function($val){return get_instance()->db->get_where('dbclientes112010',array('id'=>$val))->row()->nickname;});
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'empleados';
            $output->title = 'Empleados';
            $this->loadView($output);
        }  
        
        function usuarios($x = '', $y = '')
        {            
            $crud = parent::usuarios($x,$y);    
            $crud->columns('nombre','apellido','email','cuenta','preferencial','premium','status','rut');
            $crud->field_type('cuenta','dropdown',array(0=>'Usuario','1'=>'Administrador'));
            $crud->field_type('status','dropdown',array(0=>'Bloqueado','1'=>'Activo'));
            $crud->field_type('preferencial','dropdown',array(0=>'NO','1'=>'SI'));
            $crud->field_type('premium','dropdown',array(0=>'NO','1'=>'SI'));
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'Usuarios';
            $this->loadView($output);
        }  
        function asignacionesfamiliares($x = '', $y = '')
        {            
            $crud = parent::asignacionesfamiliares($x,$y);                        
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'Asignaciones Familiares';
            $this->loadView($output);
        }    
        
        function impuestostrabajadores($x = '', $y = '')
        {            
            $crud = parent::impuestostrabajadores($x,$y);                   
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'Impuestos a trabajadores';
            $this->loadView($output);
        }
        
        function uf($x = '', $y = '')
        {            
            $crud = parent::uf();        
            $crud->required_fields('uf','utm','tope_afp','tope_ips','tope_afc','tope_sis','tope_seguro_accidente','tasa_sis',
            'tope_mes_apv','tope_ano_apv','tope_isapres','ccaf','fecha');
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'empleados';
            $output->title = 'UF';
            $this->loadView($output);
        }  
        
        function liquidaciones($x = '', $y = '')
        {            
            $crud = parent::liquidaciones($x,$y);
            $crud->callback_field('empresa',function($val){                
                //get_instance()->querys->set_where_propietarios('user');
                $data = array('');
                foreach(get_instance()->db->get('dbclientes112010')->result() as $e)
                $data[$e->id2] = $e->nickname.' '.$e->rut;
                return form_dropdown('empresa',$data,$val,'id="field-empresa" data-placeholder="Seleccione una empresa" class="chosen-select"');
            });
            $crud->set_model('user_privilege');
            $crud->field_type('user','invisible');                        
            $crud->columns('empresa','empleado','fecha','alcance_liquido','pagado');
            $crud->field_type('pagado','true_false',array('0'=>'Si','1'=>'No'));
            $crud->unset_columns('user');            
            $output = $crud->render();
            if($x=='edit' && is_numeric($y))
                $output->y = $y;
            $output->view = 'panel';
            $output->crud = 'liquidacion';
            $output->title = 'Liquidaciones';
            $this->loadView($output);
        }
        
        function sca($x = '', $y = '')
        {            
            $crud = parent::sca($x,$y);       
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'SCA';
            $this->loadView($output);
        }
        
        function salud($x = '', $y = '')
        {            
            $crud = parent::salud($x,$y);                   
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'Salud';
            $this->loadView($output);
        }
		
		function caja_comp($x = '', $y = '')
        {            
            $crud = parent::caja_comp($x,$y);                   
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'Caja Compenzacion';
            $this->loadView($output);
        }
        
        public function paises($x = '',$y = '')
	{
            $crud = parent::paises($x,$y);
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'Paises';
            $this->loadView($output);          
	}
        
         public function ciudades($x = '',$y = '')
	{
            $crud = parent::ciudades($x,$y);
            $output = $crud->render();
            $output->view = 'panel';
            $output->title = 'Ciudades';
            $output->crud = 'user';
            $this->loadView($output);          
	} 
        
         public function comunas($x = '',$y = '')
	{
            $crud = parent::comunas($x,$y);
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'Comunas';
            $this->loadView($output);          
	}
        
         public function paginas($x = '',$y = '')
	{
            $crud = parent::paginas($x,$y);            
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'Paginas';
            $this->loadView($output);          
	}
         public function banner($x = '',$y = '')
	{
            $crud = parent::banner($x,$y);
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'Banner';
            $this->loadView($output);          
	}  
        
        function afp($x = '',$y = '')
        {
            $crud = parent::afp($x,$y);
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'AFP';
            $this->loadView($output);  
        }
        
        function isapre($x = '',$y = '')
        {
            $crud = parent::isapre($x,$y);
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'ISAPRE';
            $this->loadView($output);  
        }
        
        function ajustes($x = '',$y = '')
        {
            $crud = parent::ajustes($x,$y);            
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'Ajustes';
            $this->loadView($output);  
        }
        
        function finiquitos_causales($x = '',$y = '')
        {
            $crud = parent::finiquitos_causales($x,$y);
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'Finiquitos Causales';
            $this->loadView($output);  
        }
        
        function finiquitos($x = '', $y = '')
        {            
            $crud = parent::finiquitos($x,$y);                   
            $crud->required_fields('empresa','empleado','causal','fecha','user');                        
            $crud->unset_columns('id','descripciones');
            $crud->set_relation('user','user','nombre');
            $crud->callback_field('empresa',function($val){                                
                $data = array('');
                foreach(get_instance()->db->get('dbclientes112010')->result() as $e)
                $data[$e->id2] = $e->nickname.' '.$e->rut;
                return form_dropdown('empresa',$data,$val,'id="field-empresa" data-placeholder="Seleccione una empresa" class="chosen-select"');
            });
            $crud->set_relation('empleado','trabajadores','{rut} - {nombre} - {apellido_paterno}',array('fecha_egreso IS '=>'NULL'));
            $crud->set_relation('causal','finiquitos_causales','articulo {articulo} nro {inciso} - {causal}');
            $crud->set_relation_dependency('empleado','empresa','empresa');
            $crud->fields('fecha_emision','empresa','empleado','causal','fecha','user','descripciones');
            $crud->field_type('descripciones','products',array('table'=>'descripcion_finiquito','dropdown'=>'descripcion','relation_field'=>'finiquito','source'=>'finiquito_detalle','field_detalle'=>'nombre','display_as'=>array('ano'=>'Mes o Año')));            
            $output = $crud->render();
            $output->view = 'panel';
            $output->title = 'Finiquitos';
            $output->crud = 'finiquitos';
            $this->loadView($output);
        }
        
        function feriados($x = '', $y = '')
        {            
            $crud = parent::feriados($x,$y);                        
            $crud->set_relation('empresa','empresas','nombre');
            $crud->set_relation('user','user','nombre');
            $crud->callback_field('empresa',function($val){                                
                $data = array('');
                foreach(get_instance()->db->get('dbclientes112010')->result() as $e)
                $data[$e->id2] = $e->nickname.' '.$e->rut;
                return form_dropdown('empresa',$data,$val,'id="field-empresa" data-placeholder="Seleccione una empresa" class="chosen-select"');
            });
            $crud->unset_export()
                 ->unset_print();
            $crud->add_action('Exportar en pdf',base_url('img/pdf.jpg'),base_url('panel/imprimir_feriado').'/');
            $crud->set_relation('empleado','trabajadores','{rut} - {nombre} - {apellido_paterno}',array('fecha_egreso IS '=>'NULL'));
            $crud->set_relation_dependency('empleado','empresa','empresa');
            $crud->required_fields('empresa','periodo','empleado','fecha','fecha_final','dias_legal','dias_progresivo','total','dias_otorgados','pendientes');                        
            $crud->callback_add_field('dias_legal',function($val){return form_input('dias_legal',15,'id="field-dias_legal"');});
            $crud->callback_add_field('dias_anterior',array($this,'carga_dias_anterior'));
            
            $crud->unset_columns('user','pendientes','dias_anterior');
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'feriado';
            $output->title = 'Feriados';
            $this->loadView($output);
        }
        
        function dias_feriados($x = '', $y = '')
        {            
            $crud = parent::dias_feriados($x,$y);                                    
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'Dias de feriado';
            $this->loadView($output);
        }
        
        function notificaciones($x = '', $y = '')
        {            
            $crud = parent::notificaciones($x,$y);                        
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'notificaciones';
            $output->title = 'Notificaciones';
            $this->loadView($output);
        }
        
        function salarios_minimos($x = '', $y = '')
        {            
            $crud = parent::salarios_minimos($x,$y);                        
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'Salarios Minimos';
            $this->loadView($output);
        }
        
        function reportes($x = '', $y = '')
        {            
            $crud = parent::reportes($x,$y);                        
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'reportes';
            $output->title = 'Reportes';
            $this->loadView($output);
        }
        
        function transacciones($x = '', $y = '')
        {            
            $crud = parent::transacciones($x,$y);                        
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'finanza';
            $output->title = 'Finanzas';
            $this->loadView($output);
        }
        
        function permisos($x = '', $y = '')
        {            
            $crud = parent::permisos($x,$y);                        
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'Permisos';
            $this->loadView($output);
        }
        
        function carga_dias_anterior($val)
        {
            return form_input('dias_anterior',0,'id="field-dias_anterior"');
        }  
        /* Callbacks */        
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */