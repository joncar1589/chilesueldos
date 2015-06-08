<?php 
    require_once APPPATH.'/controllers/panel.php';    
    class Tablasmaestras extends Panel{
        function __construct() {
            parent::__construct();
        }

        public function paises($x = '', $y = '') {            
            $crud = $this->crud_function($x,$y);
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
            $this->loadView($crud->render());
        }

        public function ciudades($x = '', $y = '') {            
                $crud = $this->crud_function($x,$y);
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
                    $this->loadView($crud->render());
         }

         public function comunas($x = '', $y = '') {
                $crud = $this->crud_function($x,$y);
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
         
        public function paginas_informativas($x = '', $y = '') {
             $this->as = array('paginas_informativas'=>'paginas');
             $crud = $this->crud_function($x,$y);
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
             $this->loadView($crud->render());
         }
         
         public function banner($x = '', $y = '') {
            $crud = $this->crud_function($x, $y);
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
            $this->loadView($crud->render());
        }
        
         function isapre($x = '', $y = '') {
            $crud = $this->crud_function($x, $y);
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
            $this->loadView($crud->render());
        }
        
            function afp($x = '', $y = '') {
                $crud = $this->crud_function($x, $y);
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
                $this->loadView($crud->render());
           }
           
           public function uf($x = '', $y = '') {
                $crud = $this->crud_function($x, $y);
                $crud->unset_columns('id')
                        ->unset_fields('id');
                //Fields            
                //unsets            
                //Displays   
                $crud->display_as('fecha', 'Mes/Año');
                $crud->field_type('iduf', 'invisible');
                $crud->callback_before_insert(function($post){
                        list($d, $m, $y) = explode("/", $post['fecha']);
                        $post['iduf'] = ($y - 2000) * $y + $m;
                        return $post;
                });
                //Fields types            
                //Validations                
                $crud->required_fields('fecha');
                $this->loadView($crud->render());
            }
            
            public function asignacionesfamiliares($x = '', $y = '') {
                $crud = $this->crud_function($x, $y);
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
                $this->loadView($crud->render());
            }
            
             public function impuestos_trabajadores($x = '', $y = '') {
                $crud = $this->crud_function($x, $y);
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
                $this->loadView($crud->render());
            }
            
               public function feriados($x = '', $y = '') {
                    $crud =$this->crud_function($x, $y);
                    $crud->required_fields('fecha');
                    $crud->unset_fields('id')
                            ->unset_columns('id');
                    $this->db->where('reportes.tipo', '1');
                    $crud = $this->get_reportes($crud);
                    $this->loadView($crud->render());
                }
                
                function finiquitos_causales($x = '', $y = '') {
                    $crud = $this->crud_function($x, $y);
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
                    $this->loadView($crud->render());
                }
                
                 function salarios_minimos($x = '', $y = '') {
                    $crud = $this->crud_function($x, $y);
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
                    $this->loadView($crud->render());
                }
                
                function reportes($x = '', $y = '')  {
                    $crud = $this->crud_function($x, $y);
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
                    $this->loadView($crud->render());
                }
            
            
            //*** Callbacks ****/
    }
?>
