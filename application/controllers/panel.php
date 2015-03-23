<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once('main.php');

class Panel extends Main {

    public function __construct() {
        parent::__construct();
        if (empty($_SESSION['user']))
            header("Location:" . base_url('registro/index/add?redirect=' . $_SERVER['REQUEST_URI']));
        else {
            $s = $this->db->get_where('user', array('id' => $_SESSION['user']))->row();
            $_SESSION['saldo'] = empty($s->saldo) ? 0 : $s->saldo;
        }
        $this->load->library('enletras');
        $this->load->library('ajax_grocery_CRUD');
        $this->load->library('html2pdf/html2pdf');

        if (empty($_SESSION['rut']) && $this->router->fetch_method() != 'addRut') {
            header("Location:" . base_url('panel/addRut'));
        }
        ini_set('display_errors', true);

        $this->db->select('permisos.user, permisos.empresa, dbclientes112010.user as useremp');
        $this->db->join('dbclientes112010', 'dbclientes112010.id2 = permisos.empresa');
        $_SESSION['permisos'] = $this->db->get_where('permisos', array('permisos.user' => $_SESSION['user']));
    }

    public function index($url = 'main', $page = 0) {
        $this->loadView('panel');
    }

    public function addRut() {
        if (empty($_POST['rut'])) {
            $output = '<h1>Lo sentimos pero no hemos podido encontrar su RUT. Debe Indicarnos su rut.</h1>
            <form method="post" action="' . base_url('panel/addRut') . '" onsubmit="return validar(this)">' . form_input('rut', '', 'id="field-rut" placeholder="Rut de usuario" class="form-control" data-val="required"') . '<div align="center"><button class="btn btn-success" type="submit">Guardar</button></div></form>';
            $this->loadView(array('view' => 'panel', 'crud' => 'user', 'output' => $output));
        } else {
            $this->db->update('user', array('rut' => $_POST['rut']), array('id' => $_SESSION['user']));
            $this->user->login_short($_SESSION['user']);
            header("Location:" . base_url('panel'));
        }
    }

    public function loadView($crud) {
        if (empty($_SESSION['user']))
            header("Location:" . base_url('registro/index/add?redirect=' . $_SERVER['REQUEST_URI']));
        else
            parent::loadView($crud);
    }

    /* Cruds */

    public function dias_feriados($x = '', $y = '') {
        $crud = new ajax_grocery_crud();
        $crud->set_theme('registro');
        $crud->set_table('feriados');
        $crud->set_subject('Dia Feriado');
        $crud->required_fields('fecha');
        $crud->unset_fields('id')
                ->unset_columns('id');
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }
    
    public function logs($x = '', $y = '') {
        $crud = new ajax_grocery_crud();
        $crud->set_theme('flexigrid');
        $crud->set_table('logs');
        $crud->set_subject('Log');        
        $crud->unset_fields('id')
                ->unset_columns('id');
        $crud->unset_add()
             ->unset_edit()
             ->unset_delete();
        $crud->set_relation('user','user','{nombre} {apellido}');
        return $crud;
    }

    public function usuarios($x = '', $y = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('registro');
        $crud->set_table('user');
        $crud->set_subject('Usuarios');
        $crud->set_relation('pais', 'paises', 'nombre');
        $crud->set_relation('ciudad', 'ciudades', 'nombre');
        $crud->set_relation_dependency('ciudad', 'pais', 'pais');
        $crud->unset_fields('id');
        $crud->unset_columns('id');
        $crud->field_type('liquidaciones_free', 'invisible')
                ->field_type('feriados_free', 'invisible')
                ->field_type('finiquitos_free', 'invisible')
                ->field_type('saldo', 'invisible');
        //Fields
        $crud->field_type('password', 'password');
        //unsets            
        //Displays
        $crud->display_as('pais', 'Pais donde habita')
                ->display_as('ciudad', 'Ciudad donde habita')
                ->display_as('password', 'Contraseña');
        //Fields types            
        //Validations  
        $crud->required_fields('nombre', 'apellido', 'email', 'password', 'pais', 'ciudad', 'direccion', 'password2', 'rut');
        if (!empty($_POST) && !empty($y) && $this->db->get_where('user', array('id' => $y))->row()->email != $_POST['email'])
            $crud->set_rules('email', 'Email', 'required|valid_email|is_unique[user.email]');
        $crud->set_rules('password', 'Contraseña', 'required|min_length[8]');
        //Callbacks
        $crud->callback_field('direccion', array($this, 'direccionField'));
        $crud->callback_before_insert(array($this, 'binsertion'));
        $crud->callback_before_update(array($this, 'bupdate'));
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    public function paises($x = '', $y = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('paises');
        $crud->set_subject('Pais');
        $crud->unset_columns('id');
        $crud->unset_fields('id');
        //Fields                        
        //unsets            
        //Displays 
        //Fields types
        $crud->field_type('created', 'invisible');
        $crud->field_type('modified', 'invisible');
        //Validations                
        $crud->required_fields('nombre');
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    public function ciudades($x = '', $y = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('ciudades');
        $crud->set_subject('Ciudad');
        $crud->set_relation('pais', 'paises', 'nombre');
        $crud->unset_columns('id');
        $crud->unset_fields('id');
        //Fields                        
        //unsets            
        //Displays 
        //Fields types
        $crud->field_type('created', 'invisible');
        $crud->field_type('modified', 'invisible');
        //Validations                
        $crud->required_fields('nombre', 'pais');
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    public function comunas($x = '', $y = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('comunas');
        $crud->set_subject('Comuna');
        $crud->set_relation('pais', 'paises', 'nombre');
        $crud->set_relation('ciudad', 'ciudades', 'nombre');
        $crud->set_relation_dependency('ciudad', 'pais', 'pais');
        $crud->unset_columns('id');
        $crud->unset_fields('id');
        //Fields                        
        //unsets            
        //Displays 
        //Fields types
        $crud->field_type('created', 'invisible');
        $crud->field_type('modified', 'invisible');
        //Validations                
        $crud->required_fields('nombre', 'pais', 'ciudad');
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    public function empresa($x = '', $y = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('dbclientes112010');
        $crud->set_subject('Empresa');       
        $crud->set_primary_key('id2', 'dbclientes112010');
        
        //Fields
        $crud->fields('nickname', 'rut', 'calle', 'numero', 'pais_id', 'ciudad_id', 'comuna_id', 'tipo', 'rut_legal', 'REPRESENTANTE_LEGAL')
             ->unset_columns('id');

        //unsets        
        //Displays   
        //Fields types
        $crud->field_type('nickname', 'input')
             ->field_type('rut', 'input')
             ->field_type('calle', 'input');

        $crud->set_rules('rut', 'rut', 'required|regex_match[/^([0-9]+)-[k,K,0-9]$/]');
        $crud->field_type('tipo', 'true_false', array('0' => 'Natural', '1' => 'Juridica'));
        $crud->set_relation('pais_id', 'paises', 'nombre');
        $crud->set_relation('ciudad_id', 'ciudades', 'nombre');
        $crud->set_relation('comuna_id', 'comunas', 'nombre');
        $crud->set_relation_dependency('ciudad', 'pais', 'pais');
        $crud->set_relation_dependency('comuna', 'comuna', 'comuna');

        $crud->display_as('pais_id', 'Pais')
                ->display_as('ciudad_id', 'Ciudad')
                ->display_as('comuna_id', 'Comuna')
                ->display_as('nickname', 'Nombre');


        //Validations                
        $crud->required_fields('nickname', 'pais_id', 'ciudad_id', 'comuna_id', 'tipo', 'user', 'numero', 'calle', 'rut');
        $crud->display_as('dias_semana', 'Horario Laboral');
        if (!empty($_POST['tipo']) && $_POST['tipo'] == 1) {
            $crud->set_rules('REPRESENTANTE_LEGAL', 'Representante Legal', 'required');
            $crud->set_rules('rut_legal', 'RUT de Representante Legal', 'required');
        }
        $crud->callback_after_insert(array($this, 'empresa_ainsertion'));
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    public function empleados($x = '', $y = '') {
        
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('trabajadores');
        $crud->set_subject('Empleado');
        $crud->set_relation('sistema_salud_afiliado', 'isapre', 'nombre');
        $crud->set_relation('afp_afiliado', 'afp', 'nombre');
        $crud->set_relation('pais', 'paises', 'nombre');
        $crud->set_relation('ciudad', 'ciudades', 'nombre');
        $crud->set_relation('comuna', 'comunas', 'nombre');
        $crud->set_relation_dependency('ciudad', 'pais', 'pais');
        $crud->set_relation_dependency('comuna', 'ciudad', 'ciudad');
        $crud->set_relation('id_caja', 'caja_compensacion', 'nombre_caja');
        
        
        //Fields                       
        //unsets
        //Displays 
        $crud->display_as('por_afp', '%AFP');
        $crud->display_as('por_salud', '%SALUD');
        $crud->display_as('afp_afiliado', 'AFP');
        $crud->display_as('plan_isapre', 'Plan Isapre (UF)');
        $crud->display_as('termino_contrato', 'Fecha que termina el contrato*');
        $crud->display_as('empleado_casa', 'Empleado de casa particular');
        $crud->display_as('id_caja', 'Caja de Compensacion');
        $crud->display_as('tipo_horario', 'Tipo de Jornada');
        $crud->display_as('seguro_cesantia', 'Seguro Cesantia trabajador');
        $crud->display_as('otros_acuerdos_horarios', 'Otros acuerdos de horario');
        $crud->display_as('tipo_trabajador', 'Tipo de trabajador');
        $crud->display_as('check_afc', '¿Cotiza AFC?');
        $crud->display_as('check_afp', '¿Cotiza AFP?');
        //Fields types            
        $crud->field_type('tipo_contrato', 'enum', array('FIJO', 'INDEFINIDO', 'POR OBRA'));
        $crud->field_type('sexo', 'enum', array('M', 'F'));
        $crud->field_type('estado_civil', 'enum', array('Soltero(a)', 'Casado(a)', 'Divorciado(a)', 'Viudo(a)'));
        
        //Combobox para seleccionar si el trabajador posee horario fijo o no
        //Modificado por: Victor Alarcón fecha: 03/12/2014
        $crud->field_type('tipo_horario', 'enum', array(0=>'Sin horario fijo',1=>'Normal'));
        $crud->field_type('tipo_trabajador', 'enum', array('Activo', 'Pensionado'));
        $crud->field_type('fecha_creacion','invisible');
        //Validations                
        $crud->required_fields('nombre', 'apellido_paterno', 'apellido_materno', 'empresa', 'domicilio', 'rut', 'fecha_nacimiento', 'estado_civil', 'cargo', 'sexo', 'sueldo_base', 'gratificacion', 'fecha_ingreso', 'cargas_familiares', 'afp_afiliado', 'sistema_salud_afiliado', 'tipo_contrato', 'dependiente', 'pais', 'ciudad', 'comuna', 'codigo_postal', 'nacionalidad', 'tipo_horario', 'contrato', 'id_caja', 'tipo_trabajador');
        $crud->set_rules('codigo_postal', 'required|alpha_numeric|less_than[7]');
        $crud->set_field_upload('comprobante_pdf', 'files');
        $crud->callback_add_field('gratificacion', function($val) {
            return form_radio('gratificacion', 25, TRUE) . ' SI ' . form_radio('gratificacion', 0, FALSE) . ' No';
        });
        $crud->callback_edit_field('gratificacion', function($val) {
            return form_radio('gratificacion', 25, $val == 25 ? TRUE : FALSE) . ' SI ' . form_radio('gratificacion', 0, $val == 25 ? FALSE : TRUE) . ' No';
        });
        $crud->callback_field('dependiente', function($val) {
            return form_radio('dependiente', 1, $val == 1 ? TRUE : FALSE) . ' SI ' . form_radio('dependiente', 0, $val == 1 ? FALSE : TRUE) . ' No';
        });
        
//        Campo No Utilizado
//        $crud->callback_field('admin', function($val) {
//            return form_radio('admin', 1, $val == 1 ? TRUE : FALSE) . ' SI ' . form_radio('admin', 0, $val == 0 ? TRUE : FALSE) . ' NO';
//        });
//        $crud->callback_column('admin', function($val) {
//            return $val == 1 ? 'SI' : 'NO';
//        });
        
        $crud->callback_before_insert('admin',function($post){
            $post['fecha_creacion'] = date("Y-m-d");
            return $post;
        });
        if (!empty($_POST) && $x != 'ajax_list' && $x != 'ajax_list_info' && !empty($_POST['cargas_familiares']) && is_numeric($_POST['cargas_familiares']) && $_POST['cargas_familiares'] > 0)
            $crud->set_rules('comprobante_pdf', 'Comprobante PDF', 'required');
        
        //Esto es para los trabajadores contratados antes del 2002 que no cotizan AFC
        $crud->callback_field('check_afc', function($val) {
            
            return form_radio('check_afc', 1, $val == 1 ? TRUE : FALSE) . ' SI ' . form_radio('check_afc', 0, $val == 1 ? FALSE : TRUE) . ' No';
        });
        //Esto es para los trabajadores pensionados que decean cotizar en el AFP
        $crud->callback_field('check_afp', function($val) {
            
            return form_radio('check_afp', 1, $val == 1 ? TRUE : FALSE) . ' SI ' . form_radio('check_afp', 0, $val == 1 ? FALSE : TRUE) . ' No';
        });
        
        $crud->unset_add_fields('comprobante_pdf_isapre', 'comprobante_pdf_apv');
        $crud->set_field_upload('comprobante_pdf_isapre', 'files');
        $crud->set_field_upload('comprobante_pdf_apv', 'files');
        $crud->field_type('empleado_casa', 'true_false', array('0' => 'No', '1' => 'Si'));

        if ((!empty($y) && !empty($_POST['apv']) && $this->db->get_where('trabajadores', array('id' => $y))->row()->apv != $_POST['apv']))
            $crud->set_rules('comprobante_pdf_apv', 'Comprobante PDF APV', 'required');
        if ((!empty($y) && !empty($_POST['plan_isapre']) && $this->db->get_where('trabajadores', array('id' => $y))->row()->plan_isapre != $_POST['plan_isapre']))
            $crud->set_rules('comprobante_pdf_isapre', 'Comprobante PDF ISAPRE', 'required');


        if (!empty($_POST) && !empty($_POST['sistema_salud_afiliado']) && $this->db->get_where('isapre', array('id' => $_POST['sistema_salud_afiliado']))->row()->plan_requerido == 1)
            $crud->set_rules('plan_isapre', 'Plan ISAPRE', 'required|numeric|greather_than[0]');

        if (!empty($_POST['tipo_contrato']) && $_POST['tipo_contrato'] == 'FIJO')
            $crud->set_rules('termino_contrato', 'Fecha que termina el contrato', 'required');
        
        //Condicion que valida si los campos de horario del trabajador poseen datos cuando esta seleccionada la opcion "Normal" del combobox
        //"Seleccionar Tipo Jornada". Solo funciona con el campo horas semanales, debo haberiguar porque no funciona con los demas campos.
        //Por: Victor Alarcón Fecha: 03/12/2014
        if (!empty($_POST['tipo_horario']) && $_POST['tipo_horario'] == 'Normal'){
            $crud->set_rules('horas_semanales','dias_semana','required');
        }
        
        $crud->field_type('obligaciones', 'products', array('table' => 'obligaciones', 'relation_field' => 'trabajador'));
        $crud->field_type('prohibiciones', 'products', array('table' => 'prohibiciones', 'relation_field' => 'trabajador'));
        $crud->field_type('horariosespeciales', 'products', array('table' => 'horarios_especiales', 'relation_field' => 'trabajador'));
        $crud->display_as('horariosespeciales', 'Horarios Especiales');
        $crud->add_action('Exportar contrato en pdf', base_url('img/pdf.jpg'), base_url('panel/imprimir_contrato') . '/');
        
         //$crud->fields('empresa'); 
        
        $crud->unset_fields('id','admin', 'observaciones','acuerdo_extra')
             ->unset_columns('id','admin', 'observaciones', 'acuerdo_extra');
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    public function clientes($x = '', $y = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('clientes');
        $crud->set_subject('Cliente');
        $crud->set_relation('pais', 'paises', 'nombre');
        $crud->set_relation('ciudad', 'ciudades', 'nombre');
        $crud->set_relation_dependency('ciudad', 'pais', 'pais');
        //Fields                        
        //unsets            
        //Displays   
        //Fields types            
        //Validations                
        $crud->required_fields(
                'nombre', 'inicio_actividades', 'telefono', 'celular', 'rut', 'calle', 'numero', 'dpto', 'comuna', 'ciudad', 'region', 'pais', 'actividad_negocio', 'actividad_economica', 'email_empresa', 'email_persona', 'certificado_electronico', 'tipo_contribuyente', 'total_acciones', 'cliente_activo', 'banco', 'cuenta', 'domicilio', 'estados_clientes');
        $crud->set_rules('email_empresa', 'Email empresa', 'required|valid_email');
        $crud->set_rules('email_persona', 'Email persona', 'required|valid_email');
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    public function feriados($x = '', $y = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('feriado');
        $crud->set_subject('Comprobante');
        //Fields                        
        //unsets            
        //Displays      
        $crud->display_as('dias_legal', 'Feriado legal normal');        
        $crud->display_as('dias_progresivo', 'Feriado progresivo');
        $crud->display_as('pendientes', 'Dias pendientes');
        $crud->unset_fields('id','dias_anterior')
                ->unset_columns('id');

        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    public function socios($x = '', $y = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('socios');
        $crud->set_subject('Socio');
        $crud->set_relation('cliente', 'clientes', 'nombre');
        //Fields                        
        //unsets            
        //Displays   
        //Fields types            
        //Validations                
        $crud->required_fields(
                'nombre', 'apellido', 'participacion', 'rut', 'cliente');
        return $crud;
    }

    public function representantes($x = '', $y = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('representantes_legales');
        $crud->set_subject('Representante');
        $crud->set_relation('cliente', 'clientes', 'nombre');
        //Fields                        
        //unsets            
        //Displays   
        //Fields types            
        //Validations                
        $crud->required_fields(
                'nombre', 'cliente');
        return $crud;
    }

    public function honorarios($x = '', $y = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('honorarios');
        $crud->set_subject('Honorarios');
        $crud->set_relation('cliente', 'clientes', 'nombre');
        //Fields            
        //unsets            
        //Displays   
        $crud->display_as('ano', 'Año');
        //Fields types            
        //Validations                
        $crud->required_fields(
                'nombre', 'cliente', 'ano', 'monto');
        return $crud;
    }

    public function retenciones($x = '', $y = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('retenciones');
        $crud->set_subject('Retenciones');
        $crud->set_relation('cliente', 'clientes', 'nombre');
        //Fields            
        //unsets            
        //Displays   
        $crud->display_as('ano', 'Año');
        //Fields types            
        //Validations                
        $crud->required_fields(
                'nombre', 'cliente', 'ano', 'monto');
        return $crud;
    }

    public function otrosservicios($x = '', $y = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('otros_servicios');
        $crud->set_subject('Servicio');
        $crud->set_relation('cliente', 'clientes', 'nombre');
        //Fields            
        //unsets            
        //Displays   
        $crud->display_as('ano', 'Año');
        //Fields types            
        //Validations                
        $crud->required_fields(
                'nombre', 'cliente', 'mes', 'monto');
        return $crud;
    }

    public function nma($x = '', $y = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('nma');
        $crud->set_subject('NMA');
        $crud->set_relation('cliente', 'clientes', 'nombre');
        //Fields            
        //unsets            
        //Displays   
        $crud->display_as('ano', 'Año');
        //Fields types            
        //Validations                
        $crud->required_fields(
                'nombre', 'cliente', 'mes', 'monto');
        return $crud;
    }

    public function sm($x = '', $y = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('sm');
        $crud->set_subject('SM');
        $crud->set_relation('cliente', 'clientes', 'nombre');
        //Fields            
        //unsets            
        //Displays   
        $crud->display_as('ano', 'Año');
        //Fields types            
        //Validations                
        $crud->required_fields(
                'nombre', 'cliente', 'ano', 'monto');
        return $crud;
    }

    public function uf($x = '', $y = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('uf');
        $crud->set_subject('UF');
        $crud->unset_columns('id')
                ->unset_fields('id');
        //Fields            
        //unsets            
        //Displays   
        $crud->display_as('fecha', 'Mes/Año');
        $crud->field_type('iduf', 'invisible');
        $crud->callback_before_insert(array($this, 'uf_binsertion'));
        //Fields types            
        //Validations                
        $crud->required_fields('fecha');
        return $crud;
    }

    public function asignacionesfamiliares($x = '', $y = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('asignacionesfamiliares');
        $crud->set_subject('Asignacion Familiar');
        $crud->unset_columns('id')
                ->unset_fields('id');
        //Fields            
        //unsets            
        //Displays  
        $crud->display_as('ano', 'Año');
        $crud->field_type('user', 'invisible');
        //Fields types            
        //Validations                
        $crud->required_fields('ano', 'sueldo_minimo', 'tramo_a1', 'tramo_b1', 'tramo_c1', 'tramo_d1', 'tramo_a2', 'tramo_b2', 'tramo_c2', 'tramo_d2', 'monto_tramo_a', 'monto_tramo_b', 'monto_tramo_c', 'monto_tramo_d');
        return $crud;
    }

    public function impuestostrabajadores($x = '', $y = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('impuestos_trabajadores');
        $crud->set_subject('Impuesto');
        //Fields            
        $crud->unset_fields('id')
                ->unset_columns('id');
        //unsets            
        //Displays  
        $crud->display_as('fecha', 'Mes/Año')
             ->display_as('por_0', '%_0')
             ->display_as('por_5', '%_5')
             ->display_as('por_10', '%_10')
             ->display_as('por_15', '%_15')
             ->display_as('por_25', '%_25')
             ->display_as('por_32', '%_32')
             ->display_as('por_37', '%_37')
             ->display_as('por_40', '%_40');
        $crud->field_type('user', 'invisible');
        //Fields types            
        //Validations                
        $crud->required_fields('fecha');
        return $crud;
    }

    public function liquidaciones($x = '', $y = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('liquidaciones');
        $crud->set_table('liquidaciones');
        $crud->set_subject('Liquidacion');
        //$crud->set_relation('empresa','empresas','nombre');

        $crud->callback_column('empresa', function($val) {
            return get_instance()->db->get_where('dbclientes112010', array('id' => $val))->row()->nickname;
        });
        $crud->set_relation('empleado', 'trabajadores', '{apellido_paterno} {apellido_materno} {nombre} {rut}');
        $crud->set_relation_dependency('empleado', 'empresa', 'empresa');
        $crud->fields(
                'empresa', 'empleado', 'fecha', 'dias_mes', 'dias_trabajados', 'sueldo_base', 'sueldo_base_proporcional', 'check_horas_extras', 
                'cantidad_horas', 'factor_hora_extra', 'hrs_extras','check_bonos','bonos_ficha', 'comisiones', 'bonos', 'aguinaldos', 
                'preimponible', 'gratificaciones', 'sub_totales_haberes', 'tope_imponible', 'total_imponible', 'locomocion', 
                'colacion', 'familiar', 'otras_asignaciones', 'indem_sustit', 'indem_anos', 'total_no_imponible', 'fondo_pensiones', 
                'cotiz_salud', 'ahorro_prev_voluntario', 'caja_com_trabj', 'cotiz_seg_ces', 'adicional_salud', 'total_leyes_soc', 
                'renta_afecta', 'imp_2cat', 'check_descuentos', 'cta_ahorro_afp', 'anticipos', 'prestamos_ccaf', 'descuentos_prestamos', 'otros_descuentos', //'tramo_asig_familiar', 
                'total_otros_descuentos', 'total_haberes', 'total_descuentos', 'alcance_liquido', 'total_leyes_sociales', 'total_impuestos', 
                'afp', 'cotizacion_obligatoria', 'seguro_inv', 'cuenta_ahorro', 'indemnizacion', 'afc', 'seguro_cesantia_trabajador', 
                'seguro_cesantia_empleador', 'mutualidad', 'cot_accidente_trabajo', 'total_ips', 'cotizacion_obligatoria_salud', 
                'cotizacion_adicional', 'total_fonasa', 'caja_comp', 'impuesto_trabajador', 'costo_total_empleador', 'total_otr_desc_2', 'total_bonos_imp');
        //Fields                      
        //unsets        
        //Displays
        $crud->display_as('fecha', 'Fecha de emision')
                ->display_as('dias_mes', 'Dias del mes')
                ->display_as('cantidad_horas', 'Cantidad de horas extras')
                ->display_as('indem_anos', 'Indem.Años Ser.')
                ->display_as('fondo_pensiones', 'Fondo de pensiones')
                ->display_as('ahorro_prev_voluntario', 'Ahorro Prev.Voluntario')
                ->display_as('caja_com_trabj', 'CCAF')
                ->display_as('cotiz_seg_ces', 'Cotiz Seg. Ces.')
                ->display_as('adicional_salud', 'Adicional de Salud')
                ->display_as('imp_2cat', 'imp.2da Cat')
                ->display_as('cta_ahorro_afp', 'Cta. Ahorro AFP')
                ->display_as('prestamos_ccaf', 'Prestamos CCAF')
                ->display_as('afp', 'AFP')
                ->display_as('afc', 'AFC')
                ->display_as('mutualidad', 'MUTUALIDAD/INP')
                ->display_as('total_ips', 'Total IPS (ex INP)')
                ->display_as('cuenta_ahorro', 'Cuenta Ahorro Voluntario')
                ->display_as('indemnizacion', 'Aporte Indemnización 4.11%')
                ->display_as('seguro_inv', 'Seguro Inv. Sobrevivencia (SIS)')
                ->display_as('cot_accidente_trabajo', 'Cot.Accidente del Trabajo')
                ->display_as('caja_comp', 'Caja de Compensación')
                ->display_as('total_otr_desc_2', 'Descuentos')
                ->display_as('total_bonos_imp', 'Bonos y Comisiones') 
                ->display_as('total_otros_descuentos', 'Total descuentos')
                ->display_as('check_horas_extras', 'Horas Extras')
                ->display_as('hrs_extras', 'Total Horas Extras')
                ->display_as('check_bonos', 'Bonos imponibles')
                ->display_as('bonos', 'Bonos e incentivos')
                ->display_as('descuentos_prestamos', 'Prestamos Empleador')
                ->display_as('check_descuentos', 'Descuentos')
                ->display_as('tramo_asig_familiar', 'Tramo Asignacion Familiar');
        //Fields types
        //Esto es para los trabajadores pensionados que decean cotizar en el AFP
        $crud->callback_field('check_horas_extras', function($val) {
            return form_radio('check_horas_extras', 1, $val == 1 ? TRUE : FALSE) . ' SI ' . form_radio('check_horas_extras', 0, $val == 0 ? TRUE : FALSE) . ' No';
        });
        
         $crud->callback_field('check_bonos', function($val) {
            return form_radio('check_bonos', 1, $val == 1 ? TRUE : FALSE) . ' SI ' . form_radio('check_bonos', 0, $val == 0 ? TRUE : FALSE) . ' No';
        });
        $crud->callback_field('check_descuentos', function($val) {
            return form_radio('check_descuentos', 1, $val == 1 ? TRUE : FALSE) . ' SI ' . form_radio('check_descuentos', 0, $val == 0 ? TRUE : FALSE) . ' No';
        });
        //$crud->field_type('tramo_asig_familiar', 'enum', array('Primer tramo','Segundo tramo','Tercer tramo','Cuarto tramo'));           
        //Validations
           
    

        $crud->unset_fields('id')
             ->unset_columns('id');
        
        if (!empty($y)) {
            $crud->field_type('dias_mes', 'hidden');
            $crud->field_type('sueldo_base', 'hidden');
            $crud->field_type('bonos_ficha', 'hidden');
            $crud->field_type('gratificaciones', 'hidden');
            $crud->field_type('preimponible', 'hidden');
            $crud->field_type('sub_totales_haberes', 'hidden');
            $crud->field_type('total_imponible', 'hidden');
            $crud->field_type('familiar', 'hidden');
            $crud->field_type('otras_asignaciones', 'hidden');
            $crud->field_type('indem_sustit', 'hidden');
            $crud->field_type('indem_anos', 'hidden');
            $crud->field_type('total_no_imponible', 'hidden');
            $crud->field_type('fondo_pensiones', 'hidden');
            $crud->field_type('cotiz_salud', 'hidden');
            $crud->field_type('tope_imponible', 'hidden');
            $crud->field_type('ahorro_prev_voluntario', 'hidden');
            $crud->field_type('caja_com_trabj', 'hidden');
            $crud->field_type('cotiz_seg_ces', 'hidden');
            $crud->field_type('adicional_salud', 'hidden');
            $crud->field_type('renta_afecta', 'hidden');
            $crud->field_type('imp_2cat', 'hidden');
            $crud->field_type('total_otros_descuentos', 'hidden');
            $crud->field_type('total_descuentos', 'hidden');
            $crud->field_type('total_impuestos', 'hidden');
            $crud->field_type('afp', 'hidden');
            $crud->field_type('total_leyes_soc', 'hidden');
            $crud->field_type('cotizacion_obligatoria', 'hidden');
            $crud->field_type('seguro_inv', 'hidden');
            $crud->field_type('cuenta_ahorro', 'hidden');
            $crud->field_type('indemnizacion', 'hidden');
            $crud->field_type('afc', 'hidden');
            $crud->field_type('seguro_cesantia_trabajador', 'hidden');
            $crud->field_type('seguro_cesantia_empleador', 'hidden');
            $crud->field_type('mutualidad', 'hidden');
            $crud->field_type('cot_accidente_trabajo', 'hidden');
            $crud->field_type('total_ips', 'hidden');
            $crud->field_type('cotizacion_obligatoria_salud', 'hidden');
            $crud->field_type('cotizacion_adicional', 'hidden');
            $crud->field_type('total_fonasa', 'hidden');
            $crud->field_type('impuesto_trabajador', 'hidden');
            $crud->field_type('caja_comp', 'hidden');
            $crud->field_type('total_otr_desc_2', 'hidden');
            $crud->field_type('total_bonos_imp', 'hidden');
            $crud->field_type('total_haberes', 'hidden');
            $crud->field_type('locomocion', 'hidden');
            $crud->field_type('colacion', 'hidden');
                
        }
        
        $crud->add_action('Exportar en pdf', base_url('img/pdf.jpg'), base_url('panel/imprimir_liquidacion') . '/');
        $crud->required_fields('empresa', 'empleado', 'fecha', 'dias_trabajados');
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        
        return $crud;
    }

    public function paginas($x = '', $y = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('paginas');
        $crud->set_subject('Pagina');
        //Fields                        
        //unsets            
        //Displays   
        $crud->display_as('visible', '¿Visible en el menu?');
        $crud->unset_columns('id');
        $crud->unset_fields('id');
        //Fields types            
        //Validations                
        $crud->required_fields('url', 'visible');
        $crud->callback_field('visible', function($val) {
            return form_dropdown('visible', array(0 => 'NO', 1 => 'SI'), $val, 'id="field-visible"');
        });
        $crud->callback_column('visible', function($val) {
            return $val == 0 ? 'NO' : 'SI';
        });
        $crud->callback_field('icono', function($val) {
            return form_input('icono', $val, 'id="field-icono"') . ' <a href="http://fortawesome.github.io/Font-Awesome/" target="_new">Iconos</a>';
        });
        if ((!empty($y) && !empty($_POST) && $this->db->get_where('paginas', array('id' => $y))->row()->url != $_POST['url']) || empty($y))
            $crud->set_rules('url', 'URL', 'required|alpha_numeric|is_unique[paginas.url]');
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    public function banner($x = '', $y = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('banner');
        $crud->set_subject('Banner');
        //Fields                        
        //unsets            
        //Displays          
        $crud->display_as('url', 'imagen');
        //Fields types            
        //Validations                
        $crud->required_fields('url');
        $crud->set_field_upload('url', 'files');
        $crud->callback_before_upload(array($this, 'banner_bupload'));
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    function afp() {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('afp');
        $crud->set_subject('Afp');
        $crud->unset_columns('id');
        $crud->unset_fields('id');
        //Fields                        
        //unsets            
        //Displays          
        $crud->display_as('fecha', 'MES/AÑO');
        //Fields types            
        //Validations                
        $crud->required_fields('nombre', 'tasa_dependientes', 'tasa_independientes', 'sis_dependientes', 'fecha');
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    function importar_sueldos() {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('archivo_sueldo');
        $crud->set_subject('Archivo');
        //$crud->set_relation('empresa','dbclientes112010','nickname');
        //Fields      
        $crud->field_type('fecha', 'invisible');
        //unsets            
        //Displays          
        $crud->set_field_upload('archivo', 'files');
        $crud->callback_column('archivo', function($val, $row) {
            return '<a target="_new" href="' . base_url(get_instance()->router->fetch_class() . '/procesar/' . $val . '/' . $row->empresa) . '">' . $val . '</a>';
        });
        //Fields types            
        //Validations                
        $crud->unset_fields('id')
                ->unset_columns('id');
        $crud->required_fields('empresa', 'archivo');
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    function importar_liquidaciones() {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('archivo_liquidaciones');
        $crud->set_subject('Archivo');
        //Fields      
        $crud->field_type('fecha', 'invisible');
        //unsets            
        //Displays          
        $crud->set_field_upload('archivo', 'files');
        $crud->callback_column('archivo', function($val, $row) {
            return '<a target="_new" href="' . base_url(get_instance()->router->fetch_class() . '/procesar_liquidacion/' . $val . '/' . $row->empresa) . '">' . $val . '</a>';
        });
        //Fields types            
        //Validations                
        $crud->unset_fields('id')
                ->unset_columns('id');
        $crud->required_fields('archivo');
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    function isapre() {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('isapre');
        $crud->set_subject('isapre');
        $crud->callback_field('plan_requerido', function($val) {
            return form_dropdown('plan_requerido', array(1 => 'SI', 0 => 'NO'), $val, 'id="field-plan_requerido"');
        });
        $crud->callback_column('plan_requerido', function($val) {
            return $val == 0 ? 'NO' : 'SI';
        });
        $crud->unset_columns('id')
                ->unset_fields('id');
        //Fields                        
        //unsets            
        //Displays          
        $crud->display_as('fecha', 'MES/AÑO');
        //Fields types            
        //Validations                
        $crud->required_fields('nombre', 'comision', 'fecha');
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    function caja_comp() {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('caja_compensacion');
        $crud->set_subject('Caja');
        $crud->unset_columns('id')
                ->unset_fields('id');
        //Fields                        
        //unsets            
        //Displays          
        $crud->display_as('fecha', 'MES/AÑO');
        $crud->display_as('nombre_caja', 'Nombre caja Compensacion');
        $crud->display_as('porcentaje_cotz', 'Porcentaje');
        //Fields types            
        //Validations                
        $crud->required_fields('nombre', 'Porcentaje', 'fecha');
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    function ajustes() {
               
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('ajustes');
        $crud->set_subject('Ajuste');
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
        return $crud;
    }

    function sca() {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('dbsca');
        $crud->set_subject('SCA');
        //$crud->set_relation('empresa','dbclientes112010','nickname',array('user'=>$_SESSION['user']));
        $crud->set_primary_key('id2', 'dbclientes112010');
        $crud->unset_export()
                ->unset_print()
                ->unset_read();
        //Fields                        
        //unsets            
        //Displays          
        $crud->callback_column('empresa', function($val) {
            return get_instance()->db->get_where('dbclientes112010', array('id2' => $val))->row()->nickname;
        });
        $crud->display_as('fecha', 'MES/AÑO');
        $crud->field_type('user', 'invisible');
        $crud->unset_columns('user', 'id');
        $crud->unset_fields('id');
        //Fields types                        
        //Validations                
        $crud->required_fields('factor', 'fecha', 'empresa');
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    function salud() {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('dbsalud');
        $crud->set_subject('Salud');
        //$crud->set_relation('empresa','dbclientes112010','nickname',array('user'=>$_SESSION['user']));
        $crud->unset_export()
                ->unset_print()
                ->unset_read();
        //Fields                        
        //unsets            
        //Displays          
        $crud->display_as('fecha', 'MES/AÑO');
        $crud->field_type('user', 'invisible');
        $crud->unset_columns('user');
        $crud->unset_columns('user', 'id');
        $crud->unset_fields('id');
        //Fields types                        
        //Validations                
        $crud->required_fields('factor', 'fecha', 'empresa');
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    function finiquitos_causales() {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('finiquitos_causales');
        $crud->set_subject('Finiquitos');
        $crud->unset_export()
                ->unset_print()
                ->unset_read();
        $crud->required_fields('articulo', 'inciso', 'causal');
        //Fields                        
        //unsets            
        //Displays    
        $crud->add_action('Exportar en pdf', base_url('img/pdf.jpg'), base_url('panel/imprimir_finiquito') . '/');
        $crud->display_as('articulo', 'Nro Articulo')
                ->display_as('causal', 'Causal de despido')
                ->display_as('fecha', 'Ultimo mes trabajado');
        //Fields types                        
        //Validations     
        $crud->unset_fields('id')
                ->unset_columns('id');
        $crud->callback_after_insert(function($post, $primary) {
            get_instance()->db->update('trabajadores', array('fecha_egreso' => date("Y-m-d", strtotime(str_replace("/", "-", $post['fecha'])))), array('id' => $post['empleado']));
        });
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    function finiquitos() {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('finiquitos');
        $crud->set_subject('Finiquitos');
        $crud->unset_export()
                ->unset_print()
                ->unset_read();
        $crud->required_fields('empleado', 'fecha', 'fecha_emision');
        //Fields                        
        //unsets            
        //Displays    
        $crud->add_action('Exportar en pdf', base_url('img/pdf.jpg'), base_url('panel/imprimir_finiquito') . '/');
        $crud->display_as('articulo', 'Nro Articulo')
                ->display_as('causal', 'Causal de despido')
                ->display_as('fecha', 'Ultimo mes trabajado');
        //Fields types                        
        //Validations     
        $crud->callback_column('empresa',function($val,$row){
            return get_instance()->db->get_where('dbclientes112010',array('id2'=>$val))->row()->nickname;
        });
        $crud->unset_fields('id')
                ->unset_columns('id');
        $crud->callback_after_insert(function($post, $primary) {
            get_instance()->db->update('trabajadores', array('fecha_egreso' => date("Y-m-d", strtotime(str_replace("/", "-", $post['fecha'])))), array('id' => $post['empleado']));
        });
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    function notificaciones($x = '', $y = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('notificaciones');
        $crud->set_subject('Notificaciones');
        $crud->unset_export()
                ->unset_print()
                ->unset_read();
        //Fields                        
        //unsets            
        //Displays        
        $crud->display_as('fecha', 'Comenzar a partir de: ');
        $crud->display_as('remitente', 'Email de remitente ');
        $crud->display_as('otros', 'Otros destinatarios');
        //Fields types                        
        $crud->field_type('repetir', 'dropdown', array('0' => 'No', '1' => 'Si'));
        $crud->field_type('ciclo', 'dropdown', array('1' => 'Diario', '30' => 'Mensual', '365' => 'Anual'));
        $crud->field_type('status', 'dropdown', array('1' => 'Activo', '0' => 'Inactivo'));
        $crud->field_type('destinatarios', 'set', array('Todos', 'Personas', 'Empresas', 'Preferenciales', 'Premiums', 'Otros'));

        $data = array();
        foreach ($this->db->get('user')->result() as $u)
            $data[$u->id] = $u->nombre . ' ' . $u->apellido . '-' . $u->email;
        $crud->field_type('otros', 'set', $data);
        $crud->field_type('funcion', 'dropdown', array('1' => 'Calculo de Liquidaciones'));
        $crud->add_action('Probar Script', '', base_url('main/notificar') . '/');
        if ($x != 'edit')
            $crud->field_type('siguiente_notificacion', 'invisible');
        $crud->unset_fields('id')
                ->unset_columns('id');

        $crud->fields('remitente', 'destinatarios', 'otros', 'fecha', 'repetir', 'ciclo', 'titulo', 'text', 'status', 'ultima_ejecucion', 'siguiente_notificacion', 'funcion');
        //Validations  
        $crud->required_fields('destinatarios', 'remitente', 'texto', 'titulo', 'fecha', 'repetir', 'status');
        if (!empty($_POST['repetir']) && $_POST['repetir'] == '1')
            $crud->set_rules('ciclo', 'Ciclo', 'required');

        if (!empty($_POST['destinatarios'])) {
            if (in_array('Otros', $_POST['destinatarios']))
                $crud->set_rules('otros', 'Otros destinatarios', 'required');
        }
        $crud->set_rules('remitente', '$emitente', 'required|valid_email');
        $crud->callback_before_insert(function($post) {
            $post['siguiente_notificacion'] = $post['fecha'];
        });
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    function anexos($x = '', $y = '', $z = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->where('trabajador', $x);
        $crud->set_table('anexos');
        $crud->set_subject('Anexo');
        $crud->unset_export()
                ->unset_print()
                ->unset_read();
        //Fields                        
        //unsets            
        //Displays                    
        //Fields types             
        $crud->field_type('articulo', 'enum', array('Primero', 'Segundo', 'Tercero', 'Cuarto', 'Quinto', 'Sexto', 'Septimo', 'Octavo', 'Noveno', 'Decimo', 'Decimo primero', 'Decimo Segundo', 'Decimo Tercero'));
        $crud->add_action('Exportar en pdf', base_url('img/pdf.jpg'), base_url('panel/imprimir_anexo') . '/');
        //Validations  
        $crud->required_fields('articulo', 'contenido');
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    function salarios_minimos($x = '', $y = '') {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('salarios_minimos');
        $crud->set_subject('Salarios');
        $crud->unset_export()
                ->unset_print()
                ->unset_read();
        //Fields                        
        //unsets            
        //Displays                    
        //Fields types                         
        //Validations  
        $crud->required_fields('fecha', 'monto');
        $crud->unset_fields('id')
                ->unset_columns('id');
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    function reportes($x, $y) {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('reportes');
        $crud->set_subject('Reportes');
        $crud->field_type('tipo', 'true_false', array('0' => 'General', '1' => 'Especifico'));
        $crud->field_type('orientacion', 'dropdown', array('P' => 'Vertical', 'L' => 'Horizontal'));
        $crud->field_type('papel', 'dropdown', array('Letter' => 'Carta', 'A4' => 'A4', 'otro' => 'Medida'));
        $crud->field_type('variables', 'products', array(
            'table' => 'reportes_variables',
            'dropdown' => 'tipo_dato',
            'relation_field' => 'reporte',
            'source' => array('input' => 'input', 'date' => 'date'),
        ));
        $crud->unset_fields('id', 'funcion');
        $crud->add_action('<i title="Imprimir reporte" class="fa fa-print"></i>', '', base_url('panel/imprimir_reporte') . '/');
        $crud->callback_add_field('margen_izquierdo', function($val) {
            return form_input('margen_izquierdo', 5, 'id="field-margen_izquierdo"');
        });
        $crud->callback_add_field('margen_derecho', function($val) {
            return form_input('margen_derecho', 5, 'id="field-margen_derecho"');
        });
        $crud->callback_add_field('margen_superior', function($val) {
            return form_input('margen_superior', 5, 'id="field-margen_superior"');
        });
        $crud->callback_add_field('margen_inferior', function($val) {
            return form_input('margen_inferior', 8, 'id="field-margen_inferior"');
        });
        $crud->required_fields('controlador', 'nombre', 'contenido', 'tipo', 'orientacion', 'papel', 'margen_izquierdo', 'margen_derecho', 'margen_superior', 'margen_inferior');
        $crud->columns('tipo', 'nombre', 'controlador');
        if (!empty($_POST) && !empty($_POST['papel']) && $_POST['papel'] == 'otro') {
            $crud->set_rules('ancho', 'Ancho', 'required');
            $crud->set_rules('alto', 'Alto', 'required');
        }

        $crud->callback_before_insert(function($post) {
            $post['contenido'] = base64_encode($post['contenido']);
            return $post;
        });
        $crud->callback_before_update(function($post) {
            $post['contenido'] = base64_encode($post['contenido']);
            return $post;
        });
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    function permisos($x, $y) {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('permisos');
        $crud->set_subject('Permiso');
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
        return $crud;
    }

    function transacciones($x, $y) {
        $crud = new ajax_grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('transacciones');
        $crud->set_subject('Transacciones');
        $crud->set_relation('user', 'user', 'nombre');
        $crud->unset_edit()
                ->unset_delete();
        $crud->unset_columns('id');
        $crud->callback_column('operacion', function($val) {
            return $val == '+' ? '<span style="color:blue">' . $val . '</span>' : '<span style="color:red">' . $val . '</span>';
        });
        $this->db->where('reportes.tipo', '1');
        $crud = $this->get_reportes($crud);
        return $crud;
    }

    /* Callbacks */

    public function imprimir_reporte($id, $idtable = '') {
        $idtable = empty($idtable) ? $id : $idtable;
        $reporte = $this->db->get_where('reportes', array('id' => $id));
        if ($reporte->num_rows > 0) {
            //Es un reporte con variables? Pues se recogen antes de procesar                
            $variables = $this->db->get_where('reportes_variables', array('reporte' => $id));
            if ($variables->num_rows > 0 && empty($_POST)) {
                $output = '<form action="' . base_url('panel/imprimir_reporte/' . $id . '/' . $idtable) . '" method="post" class="form-group" role="form" onsubmit="return validar(this)">';
                $output.= '<h3>Por favor complete los datos para generar el reporte</h3>';
                //Cargar librerias
                $date = 0;
                foreach ($variables->result() as $v) {
                    switch ($v->tipo_dato) {
                        case 'date': $date = 1;
                            break;
                    }
                }

                if ($date == 1)
                    $output.= $this->load->view('predesign/datepicker', null, TRUE);
                //dibujar forms
                foreach ($variables->result() as $v) {
                    switch ($v->tipo_dato) {
                        case 'date': $class = 'datetime-input';
                            break;
                    }
                    $output.= '<div class="form-group">' . form_input($v->variable, '', 'id="field-' . $v->variable . '" class="form-control ' . $class . '" placeholder="' . ucfirst($v->variable) . '"') . '</div>';
                }
                $output.= '<div align="center"><button type="submit" class="btn btn-success">Procesar</button></div>';
                $output.= '</form>';
                $this->loadView(array('crud' => 'user', 'view' => 'panel', 'output' => $output));
            } else {
                $reporte = $reporte->row();
                $texto = base64_decode($reporte->contenido);
                $texto = str_replace('$_USER', $_SESSION['user'], $texto);
                $texto = str_replace('$_ID', $idtable, $texto);
                //Reemplazar variables recogidas en el formulario
                if ($variables->num_rows > 0) {
                    foreach ($variables->result() as $v) {
                        if (!empty($_POST[$v->variable])) {
                            if ($v->tipo_dato == 'date')
                                $_POST[$v->variable] = date("Y-m-d", strtotime(str_replace("/", "-", $_POST[$v->variable])));
                            $texto = str_replace('$_' . $v->variable, $_POST[$v->variable], $texto);
                        }
                    }
                }
                $texto = str_replace("&gt;", '>', $texto);
                $texto = str_replace("&lt;", '<', $texto);
                $sql = fragmentar($texto, '{sql="', '}');

                foreach ($sql as $s) {
                    list($sentencia, $type) = explode(";", $s);
                    $sentencia = str_replace('"', '', $sentencia);
                    switch ($type) {
                        case 'data':$texto = str_replace('{sql="' . $s . '}', sqltodata($this->db->query(strip_tags($sentencia))), $texto);
                        case 'table':$texto = str_replace('{sql="' . $s . '}', sqltotable($this->db->query(strip_tags($sentencia))), $texto);
                    }
                }
                $papel = $reporte->papel == 'otro' ? array($reporte->ancho, $reporte->alto) : $reporte->papel;
                $html2pdf = new HTML2PDF($reporte->orientacion, $papel, 'fr', false, 'ISO-8859-15', array($reporte->margen_izquierdo, $reporte->margen_superior, $reporte->margen_derecho, $reporte->margen_inferior));
                $html2pdf->writeHTML(utf8_decode($texto));
                $html2pdf->Output($reporte->nombre . '.pdf');
            }
        } else
            $this->loadView('404');
    }

    function exist_liquidacion($id) {
        return $this->db->get_where('liquidaciones', array('id' => $id))->num_rows > 0 ? TRUE : FALSE;
    }

    function imprimir_liquidacion($id) {
        $_POST['id'] = $id;
        $this->form_validation->set_rules('id', 'id', 'required|integer|greather_than[0]|callback_exist_liquidacion');
        if ($this->form_validation->run()) {
            $this->db->select(
                    'liquidaciones.*,
                dbclientes112010.nickname as empresa,dbclientes112010.rut as empresarut, 
                trabajadores.nombre,trabajadores.apellido_paterno,trabajadores.apellido_materno,trabajadores.rut as rutempleado, 
                trabajadores.sexo, trabajadores.cargas_familiares, trabajadores.tipo_trabajador, 
                trabajadores.tipo_contrato,trabajadores.plan_isapre,afp.nombre as nombre_afp,isapre.nombre as nombre_isapre, caja_compensacion.nombre_caja,
                ');
            $this->db->join('dbclientes112010', 'dbclientes112010.id2 = liquidaciones.empresa');
            $this->db->join('trabajadores', 'trabajadores.id = liquidaciones.empleado');
            $this->db->join('afp', 'trabajadores.afp_afiliado = afp.id');
            $this->db->join('caja_compensacion', 'trabajadores.id_caja = caja_compensacion.id');
            $this->db->join('isapre','trabajadores.sistema_salud_afiliado = isapre.id');
            $liquidacion = $this->db->get_where('liquidaciones', array('liquidaciones.id' => $id));
            $this->load->view('reportes/liquidaciones', array('liquidacion' => $liquidacion->row()));
        } else
            $this->loadView('404');
    }

    function imprimir_feriado($id) {
        $_POST['id'] = $id;
        $this->form_validation->set_rules('id', 'id', 'required|integer|greather_than[0]');
        if ($this->form_validation->run()) {
            $this->db->select(
                    'feriado.*,trabajadores.nombre as empnombre, trabajadores.dias_antes_ingreso, trabajadores.apellido_paterno as empapellido, trabajadores.rut as emprif,
                 dbclientes112010.nickname as empresanombre, dbclientes112010.rut as empresarut, dbclientes112010.rut_legal, dbclientes112010.REPRESENTANTE_LEGAL');
            $this->db->join('trabajadores', 'trabajadores.id = feriado.empleado');
            $this->db->join('dbclientes112010', 'dbclientes112010.id2 = feriado.empresa');
            $feriado = $this->db->get_where('feriado', array('feriado.id' => $id));
            $this->load->view('reportes/feriado', array('feriado' => $feriado->row()));
        } else
            $this->loadView('404');
    }

    function imprimir_finiquito($id) {
        $_POST['id'] = $id;
        $this->form_validation->set_rules('id', 'id', 'required|integer|greather_than[0]');
        if ($this->form_validation->run()) {
            $this->db->select('
                finiquitos.fecha, 
                dbclientes112010.nickname as empresa_nombre, dbclientes112010.rut as empresa_rut, dbclientes112010.rut_legal, dbclientes112010.REPRESENTANTE_LEGAL,
                trabajadores.fecha_ingreso as empleado_fechai,trabajadores.fecha_egreso as empleado_fechae, trabajadores.nombre as empleado_nombre,trabajadores.apellido_paterno as empleado_apellido, trabajadores.rut as empleado_rut,
                finiquito_detalle.nombre as descripcion,descripcion_finiquito.ano,descripcion_finiquito.dias,descripcion_finiquito.monto,
                ciudades.nombre as empleado_ciudad,
                dbclientes112010.calle as empresa_direccion,dbclientes112010.numero as empresa_numero,dbclientes112010."of_depto" as empresa_depto, comunas.nombre as empresa_comuna,ci.nombre as ciudad_empresa,
                finiquitos_causales.articulo,finiquitos_causales.causal,finiquitos_causales.inciso,finiquitos.fecha_emision');
            $this->db->join('dbclientes112010', 'dbclientes112010.id2 = finiquitos.empresa', 'left');
            $this->db->join('trabajadores', 'trabajadores.id = finiquitos.empleado', 'left');
            $this->db->join('descripcion_finiquito', 'descripcion_finiquito.finiquito = finiquitos.id', 'left');
            $this->db->join('finiquito_detalle', 'finiquito_detalle.id = descripcion_finiquito.descripcion', 'left');
            $this->db->join('finiquitos_causales', 'finiquitos_causales.id = finiquitos.causal', 'left');
            $this->db->join('ciudades', 'ciudades.id = trabajadores.ciudad', 'left');
            $this->db->join('ciudades ci', 'ci.id = dbclientes112010.ciudad_id', 'left');
            $this->db->join('comunas', 'comunas.id = dbclientes112010.comuna_id', 'left');
            $f = $this->db->get_where('finiquitos', array('finiquitos.id' => $this->input->post('id')));
            $this->load->view('reportes/finiquito', array('finiquito' => $f));
        } else
            $this->loadView('404');
    }

    function imprimir_contrato($id) {
        $_POST['id'] = $id;
        $this->form_validation->set_rules('id', 'id', 'required|integer|greather_than[0]');
        if ($this->form_validation->run()) {
            $this->db->select('
                ciudades.nombre as ciudad,

                dbclientes112010.nickname as empresa_nombre, dbclientes112010.rut as empresa_rut, dbclientes112010.calle as empresa_direccion, dbclientes112010.numero as casa,
                emp.nombre as empresa_comuna, dbclientes112010.tipo as empresa_tipo, dbclientes112010.REPRESENTANTE_LEGAL, dbclientes112010.rut_legal,

                trabajadores.nombre as empleado_nombre, trabajadores.apellido_paterno as empleado_apellido, trabajadores.apellido_materno as empleado_apellido2, 
                trabajadores.nacionalidad as empleado_nacionalidad, trabajadores.rut as empleado_rut, 
                trabajadores.fecha_nacimiento as empleado_fecha_nacimiento, trabajadores.domicilio as empleado_direccion, 
                tra.nombre as empleado_comuna, trabajadores.cargo as empleado_cargo,trabajadores.cargo_descripcion as empleado_cargo_descripcion, trabajadores.horas_semanales, 
                trabajadores.dias_semana, trabajadores.hora_entrada, trabajadores.hora_salida, trabajadores.sueldo_base, 
                trabajadores.bono_locomocion, trabajadores.bono_colacion, afp.nombre as afp_afiliado, trabajadores.plan_isapre, 
                trabajadores.tipo_contrato, trabajadores.termino_contrato, trabajadores.fecha_ingreso, trabajadores.gratificacion, trabajadores.acuerdo_extra, trabajadores.descripcion_tipo_contrato,
                trabajadores.fecha_emision,trabajadores.empleado_casa,
                isapre.nombre as isapre, trabajadores.tipo_horario, trabajadores.otros_acuerdos_horarios');
            $this->db->join('dbclientes112010', 'dbclientes112010.id2 = trabajadores.empresa', 'left');
            $this->db->join('ciudades', 'ciudades.id = dbclientes112010.ciudad_id', 'left');
            $this->db->join('comunas emp', 'emp.id = dbclientes112010.comuna_id', 'left');
            $this->db->join('comunas tra', 'tra.id = trabajadores.comuna', 'left');
            $this->db->join('afp', 'afp.id = trabajadores.afp_afiliado', 'left');
            $this->db->join('isapre', 'isapre.id = trabajadores.sistema_salud_afiliado', 'left');

            $trabajador = $this->db->get_where('trabajadores', array('trabajadores.id' => $this->input->post('id')));
            if ($trabajador->num_rows > 0)
                $this->load->view('reportes/contratos', array('trabajador' => $trabajador));
            else
                $this->loadView('404');
        } else
            $this->loadView('404');
    }

    function imprimir_anexo($id) {
        $_POST['id'] = $id;
        $this->form_validation->set_rules('id', 'id', 'required|integer|greather_than[0]');
        if ($this->form_validation->run()) {
            $this->db->select('
                ciudades.nombre as ciudad,

                dbclientes112010.nickname as empresa_nombre, dbclientes112010.rut as empresa_rut, dbclientes112010.calle as empresa_direccion, 
                emp.nombre as empresa_comuna, dbclientes112010.tipo as empresa_tipo, dbclientes112010.REPRESENTANTE_LEGAL, dbclientes112010.rut_legal,

                trabajadores.nombre as empleado_nombre, trabajadores.apellido_paterno as empleado_apellido, trabajadores.apellido_materno as empleado_apellido2, 
                trabajadores.nacionalidad as empleado_nacionalidad, trabajadores.rut as empleado_rut, 
                trabajadores.fecha_nacimiento as empleado_fecha_nacimiento, trabajadores.domicilio as empleado_direccion, 
                tra.nombre as empleado_comuna, trabajadores.cargo as empleado_cargo,trabajadores.cargo_descripcion as empleado_cargo_descripcion, trabajadores.horas_semanales, 
                trabajadores.dias_semana, trabajadores.hora_entrada, trabajadores.hora_salida, trabajadores.sueldo_base, 
                trabajadores.bono_locomocion, trabajadores.bono_colacion, afp.nombre as afp_afiliado, trabajadores.plan_isapre, 
                trabajadores.tipo_contrato, trabajadores.termino_contrato, trabajadores.fecha_ingreso, trabajadores.gratificacion, trabajadores.acuerdo_extra, trabajadores.descripcion_tipo_contrato,
                
                anexos.articulo,anexos.contenido,anexos.fecha_emision as fecha_emision_anexo,
                
                isapre.nombre as isapre');
            $this->db->join('dbclientes112010', 'dbclientes112010.id2 = trabajadores.empresa');
            $this->db->join('ciudades', 'ciudades.id = dbclientes112010.ciudad_id');
            $this->db->join('comunas emp', 'emp.id = dbclientes112010.comuna_id');
            $this->db->join('comunas tra', 'tra.id = trabajadores.comuna');
            $this->db->join('anexos', 'anexos.trabajador = trabajadores.id');
            $this->db->join('afp', 'afp.id = trabajadores.afp_afiliado');
            $this->db->join('isapre', 'isapre.id = trabajadores.sistema_salud_afiliado');

            $trabajador = $this->db->get_where('trabajadores', array('anexos.id' => $this->input->post('id')));
            if ($trabajador->num_rows > 0)
                $this->load->view('reportes/anexos', array('trabajador' => $trabajador));
            else
                $this->loadView('404');
        } else
            $this->loadView('404');
    }

    function binsertion($post) {
        $post['password'] = md5($post['password']);
        return $post;
    }

    function bupdate($post, $primary) {
        if ($this->db->get_where('user', array('id' => $primary))->row()->password != $post['password'])
            $post['password'] = md5($post['password']);
        return $post;
    }

    function direccionField($val) {
        return form_input('direccion', $val, 'id="field-direccion" placeholder="Calle/Avenida/Residencia/Casa"');
    }

    function banner_bupload($files_to_upload, $field_info) {
        $type = $files_to_upload[$field_info->encrypted_field_name]['type'];
        $image_info = getimagesize($files_to_upload[$field_info->encrypted_field_name]["tmp_name"]);
        $image_width = $image_info[0];
        $image_height = $image_info[1];

        if ($type != 'image/png' && $type != 'image/jpg' && $type != 'image/jpeg') {
            return 'Extension no permitida';
        } else {
            if ($image_width != 1122 || $image_height != 480)
                return 'Dimensiones de imagen no soportadas, La imagen debe ser de 1122x480';
            else
                return true;
        }
    }

    function uf_binsertion($post) {
        list($d, $m, $y) = explode("/", $post['fecha']);
        $post['iduf'] = ($y - 2000) * $y + $m;
        return $post;
    }

    function empresa_ainsertion($post, $id) {
        $this->db->order_by('id', 'DESC');
        $sca = $this->db->get_where('dbsca', array('user' => $_SESSION['user']));
        if ($sca->num_rows > 0)
            $this->db->insert('dbsca', array('empresa' => $id, 'fecha' => $sca->row()->fecha, 'factor' => $sca->row()->factor, 'user' => $_SESSION['user']));
        return true;
    }

    protected function get_reportes($crud) {
        $this->db->where('controlador', $this->router->fetch_method());
        $this->db->select('reportes.*');
        foreach ($this->db->get('reportes')->result() as $r) {
            if ($r->icono != '')
                $crud->add_action('<i title="' . $r->nombre . '" class="fa fa-' . $r->icono . '"></i>', '', base_url('panel/imprimir_reporte/' . $r->id) . '/');
            else
                $crud->add_action($r->nombre, '', base_url('panel/imprimir_reporte/' . $r->id) . '/');
        }
        return $crud;
    }

    function ipnlistener() {
        $data = array(
            'email' => $_POST['payer_email'],
            'fecha' => date("Y-m-d H:i:s"),
            'monto' => $_POST['payment_gross'],
            'motivo' => $_POST['item_name'],
            'user' => $_POST['custom'],
            'status' => $_POST['payment_status'],
            'transaccion_id' => $_POST['txn_id'],
            'operacion' => '+'
        );

        $this->db->insert('transacciones', $data);
        $this->querys->update_saldo($_POST['payment_gross'], '+', $_POST['custom']);

        $str = '<h1>Se ha recibido una recarga de saldo</h1>';
        $str.= '<p>' . $_POST['payer_email'] . ' Ha recargado saldo en chilesueldos por un valor de: ' . $_POST['payment_gross'] . '</p>';
        $str.= '<h2>Mas detalles</h2>';
        foreach ($_POST as $p => $v)
            $str.= '<div>' . $p . ': ' . $v . '</div>';

        correo('joncar.c@gmail.com', 'Pago recibido', $str);
        correo('ignacio@chiletributa.cl', 'Pago recibido', $str);
    }
    
    
    function validar_liquidacion($url){
        
        
        $datos = str_replace("-empresa-liquidaciones-", "", $url);              
        $datos = explode("-", $datos);
        $val = array(
               'verificado' => '1',
            );  
        $data = array();
        $data['extract(month from fecha) ='] = $datos[0];
        $data['extract(year from fecha) ='] = $datos[1];
        $data['liquidaciones.empresa ='] = $datos[2];
        $liq = $this->db->get_where('liquidaciones',$data);
        if($liq->num_rows>0){
            //Verificar si las liquidaciones fueron pagadas        
            $monto = 0;
            foreach($liq->result() as $l){
                if($l->pagado==1)
                    $monto+= $this->db->get('ajustes')->row()->costo_liquidacion;
            }
            
            if($monto==0 || $monto<$_SESSION['saldo']){
                $val['pagado'] = 0;
                $this->db->update('liquidaciones',$val,$data);
                $this->db->update('user',array('saldo'=>($_SESSION['saldo']-$monto)),array('id'=>$_SESSION['user']));
                echo $this->success('Liquidaciones validadas');
            }
            else echo $this->success('No posee saldo suficiente para validar estas liquidaciones');
        }
    }
}

/* End of file panel.php */
/* Location: ./application/controllers/panel.php */
