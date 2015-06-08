<?php 
    require_once APPPATH.'/controllers/panel.php';    
    class Operaciones extends Panel{
        function __construct() {
            parent::__construct();
        }

        function transacciones($x = '', $y = '') {
            $crud = $this->crud_function($x, $y);
            $crud->set_relation('user', 'user', 'nombre');
            $crud->unset_edit()
                    ->unset_delete();
            $crud->unset_columns('id');
            $crud->callback_column('operacion', function($val) {
                return $val == '+' ? '<span style="color:blue">' . $val . '</span>' : '<span style="color:red">' . $val . '</span>';
            });
            $this->db->where('reportes.tipo', '1');
            $crud = $this->get_reportes($crud);
            $this->loadView($crud->render());
        }        
        
         function caja_compensacion($x = '', $y = '') {
            $crud = $this->crud_function($x, $y);
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
            $this->loadView($crud->render());
        }   
        
        public function logs($x = '', $y = '') {
            $crud = $this->crud_function($x, $y);
            $crud->unset_fields('id')
                    ->unset_columns('id');
            $crud->unset_add()
                 ->unset_edit()
                 ->unset_delete();
            $crud->set_relation('user','user','{nombre} {apellido}');
            $this->loadView($crud->render());
         }  
    }
?>
