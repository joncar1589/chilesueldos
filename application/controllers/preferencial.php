<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('panel.php');
class Preferencial extends Panel {
        
	public function __construct()
	{
		parent::__construct(); 
                if($_SESSION['cuenta']!=1 && !$this->querys->has_access('preferencial'))
                    header("Location:".base_url('panel'));
	}
       
        public function index($url = 'main',$page = 0)
	{
		parent::index();
	}
                
        /*Cruds*/                       
        function carga($x = '',$y = '')
        {
            $crud = parent::importar_sueldos($x,$y);
            $crud->field_type('user','invisible'); 
            $crud->set_model('user_privilege');
            $crud->set_css('assets/grocery_crud/css/jquery_plugins/chosen/chosen.css');   
            $crud->callback_field('empresa',function($val){                
                get_instance()->querys->set_where_propietarios('user');
                $data = array('');
                foreach(get_instance()->db->get('dbclientes112010')->result() as $e)
                $data[$e->id2] = $e->nickname.' '.$e->rut;
                return form_dropdown('empresa',$data,$val,'id="field-empresa" data-placeholder="Seleccione una empresa" class="chosen-select"').
                '<script src="'.base_url('assets/grocery_crud/js/jquery_plugins/jquery.chosen.min.js').'"></script>'
                .'<script>$(".chosen-select,.chosen-multiple-select").chosen();</script>';;
            });
            $crud->unset_export()
                 ->unset_print()
                 ->unset_back_to_list();                                    
            $crud->unset_columns('user');            
            $crud->callback_after_insert(array($this,'carga_ainsertion'));
            $crud->set_lang_string('insert_success_message','Archivo subido, <a class="btn btn-success" target="_new" href="'.base_url('preferencial/procesar').'">click para procesarlo</a>');
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'sueldos';
            $this->loadView($output);
        }
        /* Callbacks */      
        function carga_ainsertion($post)
        {
            $_SESSION['archivo'] = $post['archivo'];
            $_SESSION['archivo_empresa'] = $post['empresa'];
            return $post;
        }
        
        function procesar($x = '',$y = '')
        {            
            $output = '<p>Abriendo archivo</p>';
            $this->load->view('procesar',array('output'=>$output));  
            $archivo = !empty($x)?$x:'';            
            if(empty($archivo))$archivo = !empty($_SESSION['archivo'])?$_SESSION['archivo']:'';
            $empresa = !empty($y)?$y:'';
            if(empty($empresa))$empresa = !empty($_SESSION['archivo_empresa'])?$_SESSION['archivo_empresa']:'';            
            if(!empty($archivo)){
            $file = fopen(base_url('files/'.$archivo), "r");            
            if(!$file)$this->load->view('procesar',array('output'=>'<p style="color:red">Archivo '.$archivo.' no encontrado</p>'));              
            else{
            $linea = fgets($file);
            while(!feof($file))
            {
                $linea = fgets($file);
                $output = '';
                $registro = explode(";",$linea);
                if(count($registro)==35){                    
                    $data = array();
                    $data['dependiente'] = ucwords(strtolower($registro[0]));
                    $data['rut'] = ucwords(strtolower($registro[1]));
                    $data['tipo_contrato'] = ucwords(strtolower($registro[2]));
                    $data['admin'] = ucwords(strtolower($registro[3]));
                    $data['apellido_paterno'] = ucwords(strtolower($registro[4]));
                    $data['apellido_materno'] = ucwords(strtolower($registro[5]));
                    $data['nombre'] = ucwords(strtolower($registro[6]));
                    $data['nacionalidad'] = ucwords(strtolower($registro[7]));
                    $data['domicilio'] = ucwords(strtolower($registro[8]));
                    $data['pais'] = ucwords(strtolower($registro[9]));
                    $data['ciudad'] = ucwords(strtolower($registro[10]));
                    $data['comuna'] = ucwords(strtolower($registro[11]));
                    $data['codigo_postal'] = ucwords(strtolower($registro[12]));
                    $data['fecha_nacimiento'] = ucwords(strtolower($registro[13]));
                    $data['estado_civil'] = ucwords(strtolower($registro[14]));
                    $data['cargo'] = ucwords(strtolower($registro[15]));
                    $data['sexo'] = ucwords(strtolower($registro[16]));
                    $data['sueldo_base'] = ucwords(strtolower($registro[17]));
                    $data['gratificacion'] = ucwords(strtolower($registro[18]));
                    $data['fecha_ingreso'] = ucwords(strtolower($registro[19]));
                    $data['cargas_familiares'] = ucwords(strtolower($registro[20]));
                    $data['afp_afiliado'] = ucwords(strtolower($registro[21]));
                    $data['sistema_salud_afiliado'] = ucwords(strtolower($registro[22]));
                    $data['plan_isapre'] = ucwords(strtolower($registro[23]));
                    $data['observaciones'] = ucwords(strtolower($registro[24]));
                    $data['bono_locomocion'] = ucwords(strtolower($registro[25]));
                    $data['bono_colacion'] = ucwords(strtolower($registro[26]));
                    $data['otras_asignaciones'] = ucwords(strtolower($registro[27]));
                    $data['apv'] = ucwords(strtolower($registro[28]));
                    $data['fecha_emision'] = $registro[29];
                    $data['cargo_descripcion'] = $registro[30];
                    $data['horas_semanales'] = $registro[31];
                    $data['dias_semana'] = $registro[32];
                    $data['hora_entrada'] = $registro[33];
                    $data['hora_salida'] = $registro[34];
                    $data['hora_salida'] = $registro[34];
                    $data['empresa'] = $empresa;
                    $data['user'] = $_SESSION['user'];                    
                    if($this->validar_data($data)){
                        if($this->db->get_where('trabajadores',array('rut'=>$registro[1],'empresa'=>$empresa))->num_rows==0){
                            $this->db->insert('trabajadores',$data);
                            $output = '<p style="color:blue">'.$registro[4].' '.$registro[6].' Incluido</p>';
                        }
                        else
                        {
                            $this->db->update('trabajadores',$data,array('rut'=>$registro[1],'empresa'=>$empresa));
                            $output = '<p style="color:blue">'.$registro[4].' '.$registro[6].' Actualizado</p>';
                        }
                    }
                    else
                        $output = '<p style="color:red">Linea inesperada</p>';
                }
                else
                    $output = '<p style="color:red">'.$output.'</p>';
                $this->load->view('procesar',array('output'=>$output));
                ob_flush();
            }   
            fclose($file);            
            }}
            else
                $this->load->view('procesar',array('output'=>'<p style="color:red">Archivo no encontrado</p>'));  
        }
        
        function validar_data($data)
        {
            $civil = array('Soltero(a)','Casado(a)','Viudo(a)','Divorciado(a)');
            $sexo = array('M','F');
            $err = true;
            $err = (!empty($data['dependiente']) && is_numeric($data['dependiente']) && $data['dependiente']<2 && $data['dependiente'] > -1)?true:false;
            $err = (!empty($data['rut']))?true:false;
            $err = (!empty($data['tipo_contrato']))?true:false;
            $err = (!empty($data['admin']) && is_numeric($data['admin']) && $data['admin']<2 && $data['admin'] > -1)?true:false;
            $err = (!empty($data['apellido_paterno']))?true:false;
            $err = (!empty($data['apellido_materno']))?true:false;
            $err = (!empty($data['nombre']))?true:false;
            $err = (!empty($data['nacionalidad']))?true:false;
            $err = (!empty($data['domicilio']))?true:false;
            $err = (!empty($data['pais']) && $this->db->get_where('paises',array('id'=>$data['pais']))->num_rows>0)?true:false;
            $err = (!empty($data['ciudad']) && $this->db->get_where('ciudades',array('id'=>$data['ciudad']))->num_rows>0)?true:false;
            $err = (!empty($data['comuna']) && $this->db->get_where('comunas',array('id'=>$data['comuna']))->num_rows>0)?true:false;
            $err = (!empty($data['codigo_postal']) && strlen($data['codigo_postal'])<7)?true:false;
            $err = (!empty($data['fecha_nacimiento']))?true:false;
            $err = (!empty($data['estado_civil']) && in_array($data['estado_civil'],$civil))?true:false;
            $err = (!empty($data['cargo']))?true:false;
            $err = (!empty($data['sexo']) && strlen($data['sexo'])==1 && in_array($data['sexo'],$sexo))?true:false;
            $err = (!empty($data['sueldo_base']) && is_numeric($data['sueldo_base']))?true:false;
            $err = (!empty($data['gratificacion']) && is_numeric($data['gratificacion']))?true:false;
            $err = (!empty($data['fecha_ingreso']))?true:false;
            $err = (!empty($data['cargas_familiares']) && is_numeric($data['cargas_familiares']) && $data['cargas_familiares']>=0)?true:false;
            $err = (!empty($data['afp_afiliado']) && $this->db->get_where('afp',array('id'=>$data['afp_afiliado']))->num_rows>0)?true:false;
            $err = (!empty($data['sistema_salud_afiliado']) && $this->db->get_where('isapre',array('id'=>$data['sistema_salud_afiliado']))->num_rows>0)?true:false;            
            
            return $err;
        }
        
        /*Cruds*/                       
        function carga_sueldos($x = '',$y = '')
        {
            $crud = parent::importar_liquidaciones($x,$y);
            /*if(!empty($_SESSION['archivo']))
            $crud->set_lang_string('insert_success_message','Archivo subido, <a class="btn btn-success" target="_new" href="'.base_url('preferencial/procesar/'.$_SESSION['archivo']).'">click para procesarlo</a>');*/
            $crud->callback_after_insert(function($post,$id){
                $_SESSION['archivo'] = $post['archivo'];
                return true;
            });
            $crud->columns('archivo');
            $crud->field_type('user','hidden',$_SESSION['user'])
                 ->field_type('empresa','invisible');
            $output = $crud->render();
            $output->view = 'panel';
            $output->crud = 'importar_liquidaciones';
            $output->title = 'Importar liquidaciones';
            $this->loadView($output);
        }
        
        function procesar_liquidacion($files){
            $output = '<p>Abriendo archivo</p>';
            $this->load->view('procesar',array('output'=>$output));  
            
            $file = fopen(base_url('files/'.$files), "r");            
            if(!$file)$this->load->view('procesar',array('output'=>'<p style="color:red">Archivo '.$files.' no encontrado</p>'));              
            else{
            $linea = fgets($file);
            while(!feof($file))
            {
             
                $linea = fgets($file);
                $output = '';
                $registro = explode(";",$linea);                
                if(count($registro)==14){                       
                    $data = array();
                    $data['rut_empresa'] = ucwords(strtolower($registro[0]));
                    $data['rut_empleado'] = ucwords(strtolower($registro[1]));
                    $data['fecha'] = ucwords(strtolower($registro[2]));
                    $data['dias_trabajados'] = ucwords(strtolower($registro[3]));
                    $data['hrs_extras'] = ucwords(strtolower($registro[4]));
                    $data['factor_hora_extra'] = str_replace(",",".",ucwords(strtolower($registro[5])));
                    $data['comisiones'] = ucwords(strtolower($registro[6]));
                    $data['bonos'] = ucwords(strtolower($registro[7]));
                    $data['aguinaldos'] = ucwords(strtolower($registro[8]));
                    $data['cuenta_ahorro'] = ucwords(strtolower($registro[9]));
                    $data['anticipos'] = ucwords(strtolower($registro[10]));
                    $data['prestamos_ccaf'] = ucwords(strtolower($registro[11]));
                    $data['descuentos_prestamos'] = ucwords(strtolower($registro[12]));
                    $data['otros_descuentos'] = ucwords(strtolower($registro[13]));
                    
                    
                    $empresa = $this->db->get_where('dbclientes112010',array('rut'=>$data['rut_empresa']));
                    
                    if($empresa->num_rows>0){
                        $this->db->where('fecha_egreso is null',null,FALSE);
                        $this->db->where('rut',$data['rut_empleado']);
                        $empleado = $this->db->get('trabajadores');
                        if($empleado->num_rows>0){
                            $data = procesar_calculo_liquidacion($empleado->row(),$empresa->row(),$_SESSION['user'],$data['fecha'],$data,TRUE);
                            $where = array('empresa'=>$data['empresa'],'empleado'=>$data['empleado'],'fecha'=>$data['fecha']);
                            if($this->db->get_where('liquidaciones',$where)->num_rows==0){
                                $this->db->insert('liquidaciones',$data);                                
                                $this->load->view('procesar',array('output'=>'<div style="color:blue">Se inserto la liquidación de '.$empleado->row()->nombre.' con fecha de '.$data['fecha'].'</div>'));
                                ob_flush();
                            }
                            else{
                                $this->db->update('liquidaciones',$data,$where);                                
                                $this->load->view('procesar',array('output'=>'<div style="color:green">Se modifico la liquidación de '.$empleado->row()->nombre.' con fecha de '.$data['fecha'].'</div>'));
                                ob_flush();
                            }
                        }
                        else $this->load->view('procesar',array('output'=>'<div style="color:red">Rut de empleado no encontrada, verifiquelo e intente nuevamente</div>'));
                    }
                    else $this->load->view('procesar',array('output'=>'<div style="color:red">Rut de empresa no encontrada, verifiquelo e intente nuevamente</div>'));;
                }
                else
                    $output = '<p style="color:red">'.$output.'</p>';
                $this->load->view('procesar',array('output'=>$output));
                ob_flush();
            }   
            fclose($file);            
            }            
        }        
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */