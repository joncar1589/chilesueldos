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
    
    protected function crud_function($x,$y){
            $crud = new ajax_grocery_CRUD();
            $crud->set_theme('bootstrap2');
            $table = !empty($this->as[$this->router->fetch_method()])?$this->as[$this->router->fetch_method()]:$this->router->fetch_method();
            $crud->set_table($table);
            $crud->set_subject(ucfirst($this->router->fetch_method()));
            $crud->unset_fields('id');
            $requireds = array();
            foreach($this->db->field_data($table) as $row)
                array_push($requireds,$row->name);
            $crud->required_fields_array($requireds);
            
            if(method_exists('panel',$this->router->fetch_method()))
             $crud = call_user_func(array('panel',$this->router->fetch_method()),$crud,$x,$y);
            return $crud;
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

    public function loadView($data)
        {
            if(empty($_SESSION['user']))
            header("Location:".base_url('main?redirect='.$_SERVER['REQUEST_URI']));
            if(!$this->user->hasAccess()){
                throw  new Exception('<b>ERROR: 403<b/> Usted no posee permisos para realizar esta operación','403');
            }
            else{
                if(!empty($data->output)){
                    $data->view = empty($data->view)?'panel':$data->view;
                    $data->crud = empty($data->crud)?'user':$data->crud;
                    $data->title = empty($data->title)?ucfirst($this->router->fetch_method()):$data->title;
                }
                parent::loadView($data);            
            }
        }

    /* Cruds */             
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
