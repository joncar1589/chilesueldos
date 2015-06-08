<?php

class User extends CI_Model{
        var $log = false;

        function __construct(){
                parent::__construct();
                if(!empty($_SESSION['user'])){
                        $this->log = TRUE;
                        $this->set_variables();
                }
        }

        function login($user,$pass)
        {
                $this->db->where('email',$user);
                $this->db->where('password',md5($pass));

                $r = $this->db->get('user');
                if($r->num_rows>0 && $r->row()->status==1)
                {
                    $this->getParameters($r);
                    return true;
                }
                else
                    return false;
        }

        function login_short($id)
        {
                $this->getParameters($this->db->get_where('user',array('id'=>$id)));
        }

        function getParameters($row)
        {
            $r = $row->row();
            $field_data = $this->db->field_data('user');                    
            foreach($field_data as $f){
                $_SESSION[$f->name] = $r->{$f->name};
            }                    
            $this->set_variables();
        }

        function set_variables(){
            foreach($_SESSION as $n=>$x){
                $this->$n = $x;
            }
            $_SESSION['user'] = $_SESSION['id'];
            $this->user = $_SESSION['user'];
        }
        
        function setVariable($nombre,$valor){
            $_SESSION[$nombre] = $valor;
            $this->set_variables();
        }

        function unlog()
        {
                session_unset();
        }
        
        function getOperations($operation){
            switch($operation){
                case 'list':
                case 'read':
                case 'export':
                case 'print':
                    return 'lectura';
                break;
                case 'add':
                case 'edit':
                case 'delete':
                    return 'escritura';
                break;
            }
        }
        
        
        function getAccess($select,$where = array(), $user = ''){
            $this->db->select($select);
            $this->db->join('user_group','user_group.user = user.id');
            $this->db->join('grupos','grupos.id = user_group.grupo');
            $this->db->join('funcion_grupo','funcion_grupo.grupo = grupos.id');
            $this->db->join('funciones','funciones.id = funcion_grupo.funcion');            
            $where['user'] = empty($user)?$this->user:$user;
            return $this->db->get_where('user',$where);            
        }
        function hasAccess(){
            $funcion = $this->router->fetch_method();
            $this->load->library('ajax_grocery_crud');
            $crud = new ajax_grocery_crud();            
            $fun = $this->getOperations($crud->getParameters());//Almacenar si es lectura o escritura  
            $permisos = $this->getAccess('grupos.*',array('funciones.nombre'=>$funcion,$fun=>1));
            return $permisos->num_rows>0 || $this->router->fetch_class()=='panel'?TRUE:FALSE;            
        }
}
?>
