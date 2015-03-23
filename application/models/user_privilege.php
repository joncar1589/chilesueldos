<?php
class User_privilege  extends grocery_crud_model_Postgre  {

	function get_list()
        {   
            $this->querys->set_where_propietarios($this->table_name);
            return parent::get_list();
        }
        
        function db_update($post_array, $primary_key_value)
        {
            $primary_key_field = $this->get_primary_key();
            $update = $this->db->get_where($this->table_name,array($primary_key_field=>$primary_key_value))->row();
            $data = array('user'=>$_SESSION['user'],'accion'=>'Update','tabla'=>$this->table_name,'fecha'=>date("Y-m-d"));
            $data['datos'] = '';
            foreach($post_array as $x=>$y){
                $data['datos'].= ($update->{$x} != $y || $x=='empresa' || $x=='empleado')?$x.'= '.$y.', ':'';
            }
            $data['datos'] .= ', Primary_Key='.$primary_key_value;
            if(!empty($data['datos']))
            $this->db->insert('logs',$data);//Actaulizar Logs
            return $this->db->update($this->table_name,$post_array, array( $primary_key_field => $primary_key_value));
        }

        function db_insert($post_array)
        {
            $post_array['user'] = $_SESSION['user'];
            $insert = $this->db->insert($this->table_name,$post_array);            
            if($insert)
            {
                    $id = $this->db->insert_id();
                    $this->db->insert('logs',array('user'=>$_SESSION['user'],'accion'=>'Insert','tabla'=>$this->table_name,'fecha'=>date("Y-m-d"),'datos'=>print_r($post_array,TRUE)));//Actaulizar Logs
                    return $id;
            }
            return false;
        }

        function db_delete($primary_key_value)
        {
            $primary_key_field = $this->get_primary_key();

            if($primary_key_field === false)
                    return false;

            //$this->db->limit(1);
            $this->db->delete($this->table_name,array( $primary_key_field => $primary_key_value));
            $this->db->insert('logs',array('user'=>$_SESSION['user'],'accion'=>'Delete','tabla'=>$this->table_name,'fecha'=>date("Y-m-d"),'datos'=>'Primary_Key='.$primary_key_value));//Actaulizar Logs
            if( $this->db->affected_rows() != 1)
                    return false;
            else
                    return true;
        }
        
        function get_edit_values($primary_key_value)
        {
            //$this->querys->set_where_propietarios($this->table_name);
            $primary_key_field = $this->get_primary_key();
            $this->db->where($primary_key_field,$primary_key_value);
            $result = $this->db->get($this->table_name)->row();
            return $result;
        }
}
?>
