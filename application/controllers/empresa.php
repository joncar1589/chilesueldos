<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('panel.php');
class Empresa extends Panel {
        
	public function __construct()
	{
		parent::__construct();                
	}
       
        public function index($url = 'main',$page = 0)
	{
		parent::index();
	}              
        
        function loadView($crud){
            if(!empty($crud->title))
                $crud->title='Empresa / '.$crud->title;
            
            parent::loadView($crud);
			
        }
        /*Cruds*/  
        function emp($x = '', $y = '')
        {            
            $crud = parent::empresa($x,$y);            
            $crud->set_model('user_privilege2');
            $crud->field_type('user','invisible');  
            $crud->required_fields('nickname','pais_id','ciudad_id','comuna_id','tipo','numero','calle','rut');
            $crud->columns('nickname','pais_id','ciudad_id','comuna_id','tipo','rut_legal','REPRESENTANTE_LEGAL','numero','calle','rut');            
            $crud->unset_columns('user');
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'empresa';
            $output->title = 'Empresas';
            $this->loadView($output);
        }           
        function empleados($x = '', $y = '')
        {            
            
            if(!empty($x) && !empty($y)){
            
            $crud = parent::empleados($x,$y);
            $crud->set_model('user_privilege2');
            $crud->field_type('user','invisible')
                 ->field_type('hora_salida','invisible');            
            $crud->columns('empresa','apellido_paterno','nombre','rut','estado_trabajador');
            $crud->callback_field('hora_entrada',function($val,$id){
            $value = !empty($id)?get_instance()->db->get_where('trabajadores',array('id'=>$id))->row()->hora_salida:'';
                return 'De '.form_input('hora_entrada',$val,'id="field-hora_entrada" style="width:100px"').' Hasta. '.form_input('hora_salida',$value,'id="field-hora_salida"  style="width:100px"');
            });
            $crud->callback_column('estado_trabajador', function($val,$row){return $row->fecha_egreso==null?'Activo':'Inactivo';});//Esto es para que aparesca una columna en la que muestre si el trabajador esta activo o no. Mod. por Victor el 08/10/2014 
            //$crud->callback_column('anexos',function($val,$row){return '<a href="'.base_url('empresa/anexos/'.$row->id).'">'.  get_instance()->db->get_where('anexos',array('trabajador'=>$row->id))->num_rows.'</a>';});
            $crud->display_as('hora_entrada','Horario Normal');
            $crud->display_as('estado_trabajador','Estado Trabajador');
            $crud->callback_field('empresa',function($val){                
                get_instance()->querys->set_where_propietarios('dbclientes112010');
                $data = array();
                foreach(get_instance()->db->get('dbclientes112010')->result() as $e)
                $data[$e->id2] = $e->nickname;
                return form_dropdown('empresa',$data,$val,'id="field-empresa" class="chosen-select"');
            });
            $crud->callback_column('empresa',function($val){return get_instance()->db->get_where('dbclientes112010',array('id'=>$val))->row()->nickname;});
            
            //Filtro para que en base a una pagina previa en la que se escoja la empresa
            //para que solo aparezcan los trabajadores de la empresa seleccionada.
            $empresa = $y;	
            $crud->where("sueldos_trabajadores.empresa =", $empresa, false);
            //$crud->order_by('apellido_paterno','acd');
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'empleados';
            $output->title = 'Empleados';
            $this->loadView($output);
            
            }else{
                $output['empresas'] = $this->querys->get_empresas();
                $output['view'] = 'form_trabj';
                $output['title'] = 'Trabajadores';
                $this->loadView($output);
            }
            
        }
        
        function contratos($x = '', $y = ''){

        }
        
        //Funcion no utilizada
        function clientes($x = '', $y = '')
        {            
            $crud = parent::clientes($x,$y);
            $crud->set_model('user_privilege');
            $crud->field_type('user','invisible');
            $crud->unset_columns('user');            
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'Clientes';
            $this->loadView($output);
        }
        //Funcion no utilizada
        function socios($x = '', $y = '')
        {            
            $crud = parent::socios($x,$y);
            $crud->set_model('user_privilege');
            $crud->field_type('user','invisible');
            $crud->unset_columns('user');            
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'Socios';
            $this->loadView($output);
        }
        //Funcion no utilizada
        function representantes($x = '', $y = '')
        {            
            $crud = parent::representantes($x,$y);
            $crud->set_model('user_privilege');
            $crud->field_type('user','invisible');
            $crud->unset_columns('user');            
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'Representantes';
            $this->loadView($output);
        }
        //Funcion no utilizada
        function honorarios($x = '', $y = '')
        {            
            $crud = parent::honorarios($x,$y);
            $crud->set_model('user_privilege');
            $crud->field_type('user','invisible');
            $crud->unset_columns('user');            
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'Honorarios';
            $this->loadView($output);
        }
        //Funcion no utilizada
        function retenciones($x = '', $y = '')
        {            
            $crud = parent::retenciones($x,$y);
            $crud->set_model('user_privilege');
            $crud->field_type('user','invisible');
            $crud->unset_columns('user');            
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'Retenciones';
            $this->loadView($output);
        }
        //Funcion no utilizada
        function otrosservicios($x = '', $y = '')
        {            
            $crud = parent::otrosservicios($x,$y);
            $crud->set_model('user_privilege');
            $crud->field_type('user','invisible');
            $crud->unset_columns('user');            
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'Otros Servicios';
            $this->loadView($output);
        }
        //Funcion no utilizada
        function nma($x = '', $y = '')
        {            
            $crud = parent::nma($x,$y);
            $crud->set_model('user_privilege');
            $crud->field_type('user','invisible');
            $crud->unset_columns('user');            
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'NMA';
            $this->loadView($output);
        }
        //Funcion no utilizada
        function sm($x = '', $y = '')
        {            
            $crud = parent::sm($x,$y);
            $crud->set_model('user_privilege');
            $crud->field_type('user','invisible');
            $crud->unset_columns('user');            
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'SM';
            $this->loadView($output);
        }
		       
        function asignacionesfamiliares($x = '', $y = '')
        {            
            $crud = parent::asignacionesfamiliares($x,$y);
            $crud->set_model('user_privilege2');
            $crud->field_type('user','invisible');
            $crud->unset_columns('user');            
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'Asignaciones Familiares';
            $this->loadView($output);
        }
		
        function liquidaciones($x = '', $y = '',$z = '')
        {            
            if(!empty($x) && !empty($y)){
  
            $crud = parent::liquidaciones($x,$y);            
            $crud->set_relation('empresa','dbclientes112010','nickname');
            $crud->set_model('user_privilege2');
            $crud->field_type('user','invisible');
            $crud->columns('empresa','empleado','fecha','alcance_liquido','total_leyes_sociales', 'dias_trabajados','total_bonos_imp','total_otr_desc_2');
            $crud->unset_columns('user');
            if($z=='add' || $z=='insert_validation' || $z=='insert')
            $crud->set_rules('empresa','Empresa','required|callback_can_do_liquidacion');
            $crud->callback_after_insert(array($this,'liquidaciones_ainsertion'));	
            //Filtro para que en base a una pagina previa en la que se escoja la empresa, el mes y el año de emisión de la liquidación
            //solo aparezcan las liquidaciones seleccionadas.
            $empresa = $y;
            $fecha = $x;
            list($month, $year) = explode('-', $fecha);		
            $crud->where("extract(month from fecha) =", $month,false);
            $crud->where("extract(year from fecha) =", $year,false);
            $crud->where("sueldos_liquidaciones.empresa =", $empresa,false);
            $crud->order_by('empleado','acd');
            $output = $crud->render();
            
            if($x=='edit' && is_numeric($y)){
                $output->y = $y;
               
            }
            $output->view = 'panel';
            $output->crud = 'liquidacion';
            $output->title = 'Liquidaciones';
            $this->loadView($output);
            }else{
                
                $output['empresas'] = $this->querys->get_empresas();
                $output['view'] = 'form_liq';
                $output['title'] = 'Liquidaciones';
                $this->loadView($output);
            }
        }
		
		
        function uf($x = '', $y = '')
        {            
            $crud = parent::uf($x,$y);                        
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'UF';
            $this->loadView($output);
        }
        
        function impuestostrabajadores($x = '', $y = '')
        {            
            $crud = parent::impuestostrabajadores($x,$y);       
            $crud->set_model('user_privilege2');
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'Impuestos Trabajadores';
            $this->loadView($output);
        }
        
        function sca($x = '', $y = '')
        {            
            $crud = parent::sca($x,$y);       
            $crud->set_model('user_privilege2');
            $crud->set_css('assets/grocery_crud/css/jquery_plugins/chosen/chosen.css');
			$crud->columns('empresa','fecha','factor');
            $crud->callback_field('empresa',function($val){                
                get_instance()->querys->set_where_propietarios('dbclientes112010');
                $data = array('');
                foreach(get_instance()->db->get('dbclientes112010')->result() as $e)
                $data[$e->id2] = $e->nickname.' '.$e->rut;
                return                 
                form_dropdown('empresa',$data,$val,'id="field-empresa" data-placeholder="Seleccione una empresa" class="chosen-select"')
                .'<script src="'.base_url('assets/grocery_crud/js/jquery_plugins/jquery.chosen.min.js').'"></script>'
                .'<script>$(".chosen-select,.chosen-multiple-select").chosen();</script>';
            });
			
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $output->title = 'SCA';
            $this->loadView($output);
        }
        
        /*
        Se quito esta opcion porque no se usara la tabla dbsalud ya que se le dio una tasa fija del 7% a todas las empresas -- Modificado por victor el 07/10/2014
        function salud($x = '', $y = '')
        {            
            $crud = parent::salud($x,$y);       
            $crud->callback_field('empresa',function($val){                
                get_instance()->querys->set_where_propietarios('user');
                $data = array('');
                foreach(get_instance()->db->get('dbclientes112010')->result() as $e)
                $data[$e->id2] = $e->nickname.' '.$e->rut;
                return form_dropdown('empresa',$data,$val,'id="field-empresa" data-placeholder="Seleccione una empresa" class="chosen-select"');
            });
            $crud->set_model('user_privilege');
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'user';
            $this->loadView($output);
        }*/
        
        function finiquitos($x = '', $y = '')
        {            
            $crud = parent::finiquitos($x,$y);       
            $crud->set_model('user_privilege2');
            $crud->required_fields('fecha_emision','empresa','empleado','causal','fecha','fecha_emision');
            $crud->callback_field('empresa',function($val){                
                get_instance()->querys->set_where_propietarios('dbclientes112010'); 
                $data = array('');
                foreach(get_instance()->db->get('dbclientes112010')->result() as $e)
                $data[$e->id2] = $e->nickname;
                return form_dropdown('empresa',$data,!empty($val)?$val:'','id="field-empresa" class="chosen-select" data-placeholder="Elige una empresa"');
            });
            /*if(empty($x) || $x=='ajax_list' || $x=='ajax_list_info' || $x=='success')
                $crud->set_relation('empresa','dbclientes112010','nickname');*/
            $crud->set_primary_key('id2','dbclientes112010');
            $crud->unset_columns('user','descripciones');
            $crud->set_relation('empleado','trabajadores','{rut} - {nombre} - {apellido_paterno}',array('fecha_egreso IS '=>'NULL'));
            $crud->set_relation('causal','finiquitos_causales','articulo {articulo} nro {inciso} - {causal}');
            $crud->set_relation_dependency('empleado','empresa','empresa');
            $crud->fields('fecha_emision','empresa','empleado','causal','fecha','user','descripciones');
            $crud->field_type('descripciones','products',array('class'=>array('ano'=>'datepicker-input'),'table'=>'descripcion_finiquito','dropdown'=>'descripcion','relation_field'=>'finiquito','source'=>'finiquito_detalle','field_detalle'=>'nombre','display_as'=>array('ano'=>'Mes/Año','dias'=>'Dias o Años')));
            $crud->field_type('user','invisible');
            if($x=='add' || $x=='insert_validation' || $x=='insert')
            $crud->set_rules('empresa','Empresa','required|callback_can_do_finiquito');
            $crud->callback_after_insert(array($this,'finiquitos_ainsertion'));
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'finiquitos';
            $output->title = 'Finiquitos';
            $this->loadView($output);
        }
        
        function feriados($x = '', $y = '')
        {            
            $crud = parent::feriados($x,$y);
            $crud->set_model('user_privilege2');
            $crud->field_type('user','invisible');            
            $crud->callback_field('empresa',function($val){                
                get_instance()->querys->set_where_propietarios('dbclientes112010');
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
            $crud->required_fields('empresa','periodo','empleado','fecha','fecha_emision','fecha_final','dias_legal','dias_progresivo','total','dias_otorgados','pendientes');                        
            $crud->callback_add_field('dias_legal',function($val){return form_input('dias_legal',15,'id="field-dias_legal"');});
            $crud->callback_add_field('dias_anterior',array($this,'carga_dias_anterior'));
            $crud->callback_after_insert(array($this,'feriados_ainsertion'));            
            $crud->unset_columns('user','pendientes','dias_anterior');   
            if($x=='add' || $x=='insert_validation' || $x=='insert')
            $crud->set_rules('empresa','Empresa','required|callback_can_do_feriado');
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'feriado';
            $output->title = 'Feriados';
            if(!empty($y))$output->id = $y;
            $this->loadView($output);
        }
        
        function anexos($x = '', $y = '',$z = '')
        {   
            if(empty($x) || !is_numeric($x))
                header("Location:".base_url('panel'));
            else{
            $crud = parent::anexos($x,$y,$z);
            $crud->set_model('user_privilege2');
            $crud->field_type('user','invisible');                        
            $crud->unset_export()
                 ->unset_print();      
            $crud->required_fields('fecha_emision','articulo','contenido');
            $crud->set_lang_string('insert_success_message','Anexo agregado con exito, Se recomienda hacer los ajustes señalados aca en el contrato de trabajo. <a href="'.base_url('empresa/empleados').'">Contratos</a>');
            $crud->unset_columns('user','trabajador');
            $crud->field_type('trabajador','hidden',$x);            
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'feriado';
            $output->title = 'Anexos';
            $this->loadView($output);
            }
        }
        
        
        function recargar()
        {
            $this->loadView((object)array('view'=>'recargar','title'=>'Recargar Saldo'));
        }
        
        function carga_dias_anterior($val)
        {
            return form_input('dias_anterior',0,'id="field-dias_anterior"');
        }      
        
        function transacciones($x = '',$y = '')
        {
            $crud = parent::transacciones($x,$y);
            $crud->set_model('user_privilege2');
            $crud->where('user',$_SESSION['user']);
            $crud->field_type('user','invisible');                        
            $crud->unset_columns('id','email','user','transaccion_id');
            $crud->unset_export()
                 ->unset_print();                                                      
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'finanza';
            $output->title = 'Finanzas';
            $this->loadView($output);            
        }
        /* Callbacks */     
        function finiquitos_ainsertion($post){
            $free = $this->db->get_where('user',array('id'=>$_SESSION['user']))->row()->finiquitos_free;
            $costo = $this->db->get('ajustes')->row()->costo_finiquito;
            $this->do_pay($free,'finiquitos', $costo);
            return true;
        }
        function feriados_ainsertion($post){
            $free = $this->db->get_where('user',array('id'=>$_SESSION['user']))->row()->feriados_free;
            $costo = $this->db->get('ajustes')->row()->costo_feriado;
            $this->do_pay($free,'feriados', $costo);
            return true;
        }
        function liquidaciones_ainsertion($post){
            $free = $this->db->get_where('user',array('id'=>$_SESSION['user']))->row()->liquidaciones_free;
            $costo = $this->db->get('ajustes')->row()->costo_liquidacion;
            $this->do_pay($free,'liquidaciones', $costo);
            return true;
        }
        /* Ajax */
        function get_salario()
        {
            $this->form_validation->set_rules('id','Id','required|integer');
            if($this->form_validation->run())
            {
                $emp = $this->querys->get_salario($this->input->post('id'));
                echo $emp->num_rows>0?json_encode($emp->row()):'';
            }
        }
        
        function get_liquidaciones()
        {
            $this->form_validation->set_rules('id','Id','required|integer');
            if($this->form_validation->run())
            {
                $emp = $this->querys->get_liquidaciones($this->input->post('id'));
                echo $emp->num_rows>0?json_encode($emp->result_array()):json_encode(array('id'=>''));
            }
        }
        
        function get_feriados($id = '')
        {
            $this->form_validation->set_rules('id','Id','required|integer');
            if($this->form_validation->run())
            {                
                if(!empty($id))$this->db->where('id != ',$id);
                $emp = $this->querys->get_feriados($this->input->post('id'));
                echo $emp->num_rows>0?json_encode($emp->row()):json_encode(array('periodo'=>''));
            }
        }
        
        function getuf()
        {
            $this->form_validation->set_rules('id','Id','required|integer');            
            if($this->form_validation->run())
            {                
                $emp = $this->querys->getuf($this->input->post('id'));
                echo $emp->num_rows>0?json_encode($emp->row()):json_encode(array('id'=>0));
            }
        }
        
        function getfamiliares()
        {
            $this->form_validation->set_rules('ano','Ano','required|integer');            
            if($this->form_validation->run())
            {                   
                $f = $this->querys->getfamiliares($this->input->post('ano'));
                echo $f->num_rows==0?json_encode(array('id'=>0)):json_encode($f->row());
            }
        }
        
        function getimpuestos()
        {
            $this->form_validation->set_rules('ano','Ano','required|integer');            
            $this->form_validation->set_rules('mes','Mes','required|integer');            
            if($this->form_validation->run())
            {                                   
                $f = $this->querys->getimpuestos($this->input->post('ano'),$this->input->post('mes'));
                echo $f->num_rows==0?json_encode(array('id'=>0)):json_encode($f->row());
            }            
        }
        
        function getRutEmpresa()
        {
            $this->form_validation->set_rules('id','Id','required|integer|greather_than[0]');
            if($this->form_validation->run())
            {
                $empresa = $this->db->get_where('dbclientes112010',array('id'=>$this->input->post('id')));
                if($empresa->num_rows>0)
                {
                    echo $empresa->row()->rut;
                }
                else
                    echo 'Ocurrio un error en la consulta';
            }
        }
        
        function getsca()
        {
            $this->form_validation->set_rules('ano','Ano','required|integer');            
            $this->form_validation->set_rules('mes','Mes','required|integer');            
            $this->form_validation->set_rules('empresa','Empresa','required|integer');            
            if($this->form_validation->run())
            {                                   
                $f = $this->querys->getsca($this->input->post('ano'),$this->input->post('mes'),$this->input->post('empresa'));
                echo $f->num_rows==0?json_encode(array('id'=>0)):json_encode($f->row());
            }    
        }
        
        /* Se quito esta opcion porque no se usara la tabla dbsalud ya que se le dio una tasa fija del 7% a todas las empresas -- Modificado por victor el 07/10/2014
        function getretsalud()
        {
            $this->form_validation->set_rules('ano','Ano','required|integer');            
            $this->form_validation->set_rules('mes','Mes','required|integer');            
            $this->form_validation->set_rules('empresa','Empresa','required|integer');            
            if($this->form_validation->run())
            {                                   
                $f = $this->querys->getretsalud($this->input->post('ano'),$this->input->post('mes'),$this->input->post('empresa'));
                echo $f->num_rows==0?json_encode(array('id'=>0)):json_encode($f->row());
            }    
        }
       */
        
        function getAnterior()
        {
            $this->form_validation->set_rules('empleado','Empleado','required|integer');
            $this->form_validation->set_rules('empresa','Empresa','required|integer');
            if($this->form_validation->run())
            {
                $this->db->order_by('id','DESC');
                $q = $this->db->get_where('feriado',array('empleado'=>$this->input->post('empleado'),'empresa'=>$this->input->post('empresa')));
                if($q->num_rows>0)
                {
                    echo $q->row()->pendientes;
                }
                else echo 0;
            }
            else echo 0;
        }     
        
        function get_salario_minimo()
        {
            $this->form_validation->set_rules('ano','Ano','required|integer');            
            $this->form_validation->set_rules('mes','Mes','required|integer');            
            if($this->form_validation->run())
            {                                   
                $f = $this->querys->get_salario_minimo($this->input->post('ano'),$this->input->post('mes'));
                echo $f->num_rows==0?json_encode(array('id'=>0)):json_encode($f->row());
            }    
        }
        
        function can_do_liquidacion($id)
        {            
            $tiempo_gratuidad = $this->db->get('ajustes')->row()->tiempo_gratuidad;
            
            $this->db->join('dbclientes112010','dbclientes112010.user = user.id');
            $registro = $this->db->get_where('user',array('dbclientes112010.id2'=>$_POST['empresa']))->row()->fecha_registro;
            $antiguedad = strtotime('+3 months '.$registro);
            
            $free = $this->db->get_where('user',array('id'=>$_SESSION['user']))->row()->liquidaciones_free;
            $costo = $this->db->get('ajustes')->row()->costo_liquidacion;
            
            if($free==0 && $costo>$_SESSION['saldo']){
                $this->form_validation->set_message('can_do_liquidacion','No posee saldo disponible para realizar este documento, <a href="'.base_url('empresa/recargar').'">¿Desea recargar?</a>');
                return false;
            }
            //tiene el tiempo
            elseif(strtotime(date("Y-m-d")>$antiguedad))            
            return false;
            
            else return true;
        }
        
        function can_do_feriado($id)
        {
            $costo = $this->db->get('ajustes')->row()->costo_feriado;
            $free = $this->db->get_where('user',array('id'=>$_SESSION['user']))->row()->feriados_free;
            if($free==0 && $costo>$_SESSION['saldo']){
            $this->form_validation->set_message('can_do_feriado','No posee saldo disponible para realizar este documento, <a href="'.base_url('empresa/recargar').'">¿Desea recargar?</a>');
            return false;
            }            
            return true;
        }
        
        function can_do_finiquito($id)
        {
            $costo = $this->db->get('ajustes')->row()->costo_finiquito;
            $free = $this->db->get_where('user',array('id'=>$_SESSION['user']))->row()->finiquitos_free;
            if($free==0 && $costo>$_SESSION['saldo']){
            $this->form_validation->set_message('can_do_finiquito','No posee saldo disponible para realizar este documento, <a href="'.base_url('empresa/recargar').'">¿Desea recargar?</a>');
            return false;
            }                        
            return true;
        }
        
        function do_pay($free,$field,$costo)
        {            
            if($free>0)
            $this->db->update('user',array($field.'_free'=>($free-1)),array('id'=>$_SESSION['user']));
            else{
            $this->querys->update_saldo($costo,'-',$_SESSION['user']);
            //$this->db->insert('transacciones',array('saldo'=>$saldo),array('id'=>$user));
            }
        }
		    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */