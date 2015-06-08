<?php 
    require_once APPPATH.'/controllers/panel.php';    
    class Cms extends Panel{
        function __construct() {
            parent::__construct();
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
        
        function notificaciones($x = '', $y = '') {
            $crud = $this->crud_function($x, $y);
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
            $this->loadView($crud->render());
        }
    }
?>
