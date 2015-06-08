<?php 
    require_once APPPATH.'/controllers/panel.php';    
    class Empresa_admin extends Panel{
        function __construct() {
            parent::__construct();
        }
        
        public function empresas($x = '', $y = '') {
            $this->as = array('empresas'=>'dbclientes112010');
            $crud = $this->crud_function($x, $y);
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
            $this->loadView($crud->render());
        }

        public function trabajadores($x = '', $y = '') {

            $crud = $this->crud_function($x, $y);
            //$crud->set_relation('sistema_salud_afiliado', 'isapre', 'nombre');
            //$crud->set_relation('afp_afiliado', 'afp', 'nombre');
            $crud->set_relation('pais', 'paises', 'nombre');
            $crud->set_relation('ciudad', 'ciudades', 'nombre');
            $crud->set_relation('comuna', 'comunas', 'nombre');
            $crud->set_relation_dependency('ciudad', 'pais', 'pais');
            $crud->set_relation_dependency('comuna', 'ciudad', 'ciudad');
            //$crud->set_relation('id_caja', 'caja_compensacion', 'nombre_caja');


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
            $this->loadView($crud->render());
        }

            public function contratos($x = '', $y = '') {

            $crud = $this->crud_function($x, $y);
            $crud->set_relation('sistema_salud_afiliado', 'isapre', 'nombre');
            $crud->set_relation('afp_afiliado', 'afp', 'nombre');
            //$crud->set_relation('pais', 'paises', 'nombre');
            //$crud->set_relation('ciudad', 'ciudades', 'nombre');
            //$crud->set_relation('comuna', 'comunas', 'nombre');
            //$crud->set_relation_dependency('ciudad', 'pais', 'pais');
            //$crud->set_relation_dependency('comuna', 'ciudad', 'ciudad');
            //$crud->set_relation('id_caja', 'caja_compensacion', 'nombre_caja');


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
            $crud->field_type('tipo_contrato', 'enum', array('FIJO', 'INDEFINIDO', 'POR OBRA'));
            $crud->field_type('sexo', 'enum', array('M', 'F'));
            $crud->field_type('estado_civil', 'enum', array('Soltero(a)', 'Casado(a)', 'Divorciado(a)', 'Viudo(a)'));
            $crud->field_type('tipo_horario', 'enum', array(0=>'Sin horario fijo',1=>'Normal'));
            $crud->field_type('tipo_trabajador', 'enum', array('Activo', 'Pensionado'));
            $crud->field_type('fecha_creacion','invisible');            
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
            
            if (!empty($_POST['tipo_horario']) && $_POST['tipo_horario'] == 'Normal'){
                $crud->set_rules('horas_semanales','dias_semana','required');
            }

            $crud->field_type('obligaciones', 'products', array('table' => 'obligaciones', 'relation_field' => 'trabajador'));
            $crud->field_type('prohibiciones', 'products', array('table' => 'prohibiciones', 'relation_field' => 'trabajador'));
            $crud->field_type('horariosespeciales', 'products', array('table' => 'horarios_especiales', 'relation_field' => 'trabajador'));
            $crud->display_as('horariosespeciales', 'Horarios Especiales');
            $crud->add_action('Exportar contrato en pdf', base_url('img/pdf.jpg'), base_url('panel/imprimir_contrato') . '/');
            $crud->unset_fields('id','admin', 'observaciones','acuerdo_extra')
                 ->unset_columns('id','admin', 'observaciones', 'acuerdo_extra');
            $this->db->where('reportes.tipo', '1');
            $crud = $this->get_reportes($crud);
            $this->loadView($crud->render());
        }
        
        function dbsca($x = '',$y = '') {
            $crud = $this->crud_function($x, $y);
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
            $this->loadView($crud->render());
        }
        
        public function liquidaciones($x = '', $y = '') {
            $crud = $this->crud_function($x, $y);

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
            $this->loadView($crud->render());
        }
        
        function finiquitos($x = '',$y = '') {
            $crud = $this->crud_function($x, $y);
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
            $this->loadView($crud->render());
        }
        
         public function feriado($x = '', $y = '') {
            $crud = $this->crud_function($x, $y);
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
             $this->loadView($crud->render());
        }
    }
?>
