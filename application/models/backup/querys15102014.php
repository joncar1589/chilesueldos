<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Querys extends CI_Model{
    
    function __construct()
    {
        parent::__construct();
        $this->ajustes = $this->db->get('ajustes')->row();
    }
    
    function fecha($val)
    {
        return date($this->ajustes->formato_fecha,strtotime($val));
    }
    
    function moneda($val)
    {
        return $val.' '.$ajustes->moneda;
    }
    
    function get_propietarios_ids($admin = 1)
    {
        $tr = $this->db->get_where('trabajadores',array('rut'=>$_SESSION['rut'],'admin'=>$admin));
        if($tr->num_rows>0){
           $users = array();
           foreach($tr->result() as $t)
           {
               array_push($users,$t->user);
           }
           
           return $users;
        }
        else return null;
    }
    
    function get_empleado_id($rut = '')
    {
        $rut = empty($rut)?$_SESSION['rut']:$rut;                
        $user = $this->db->get_where('trabajadores',array('rut'=>trim($rut)));
        if($user->num_rows>0)
            return $user->row()->id;
        else
            return 0;
    }
    
    function update_saldo($val,$ope,$user)
    {
        $saldo = $this->db->get_where('user',array('id'=>$user))->row()->saldo;
        switch($ope)
        {
            case '+':
                $saldo+=$val;                
            break;
            case '-':
                $saldo-=$val;
            break;
        }
        $this->db->update('user',array('saldo'=>$saldo),array('id'=>$user));        
    }
    
//    function set_where_propietarios($cell)
//    {
//        $this->db->where($cell,$_SESSION['user']);
//        if(!empty($_SESSION['propietarios'])){
//            foreach($_SESSION['propietarios'] as $p)
//            $this->db->or_where($cell,$p);
//        }
//    }
    
    function set_where_propietarios($table,$user = ''){
        $user = empty($user)?$_SESSION['user']:$user;
        $this->db->where($table.'.user',$user);
        foreach($_SESSION['permisos']->result() as $s){
            if($table!='dbclientes112010')
            $this->db->or_where($table.'.user = '.$s->useremp.' AND sueldos_'.$table.'.empresa='.$s->empresa,null,FALSE);
            else
            $this->db->or_where($table.'.user = '.$s->useremp.' AND id2='.$s->empresa,null,FALSE);
        }
    }
    
    function has_access($controlador)
    {
        switch($controlador)
        {
            case 'persona':
            case 'empresa': return true;
            break;
            case 'admin':
                if($_SESSION['cuenta'] == 1)return true;
                else return false;
            break;
            case 'preferencial':
                if($_SESSION['preferencial'] == 1 || $_SESSION['cuenta'] == 1)return true;
                else return false;
            break;
            case 'premium':
                if($_SESSION['premium'] == 1 || $_SESSION['cuenta'] == 1)return true;
                else return false;
            break;
            default: return false;
        }
    }
    
    
    //****** calculo de liquidaciones
    function get_salario($id)
    {        
        $this->db->select('trabajadores.*, isapre.comision as por_salud, afp.tasa_dependientes as por_afp, afp.sis_dependientes as sis, ');
        $this->db->join('afp','trabajadores.afp_afiliado = afp.id');
        $this->db->join('isapre','trabajadores.sistema_salud_afiliado = isapre.id');
        $emp = $this->db->get_where('trabajadores',array('trabajadores.id'=>$id));
        return $emp;
    }
    
    function get_liquidaciones($id)
    {
        $this->db->select('liquidaciones.*');    
        $this->db->order_by('id','DESC');
        $emp = $this->db->get_where('liquidaciones',array('liquidaciones.empleado'=>$id));
        $emp->result();
        return $emp;
    }
    
    function get_feriados($id)
    {
        $this->db->select('feriado.*'); 
        $this->db->order_by('id','DESC');
        $emp = $this->db->get_where('feriado',array('feriado.empleado'=>$id));
        return $emp;
    }
    
    function getuf($id)
    {
        return $this->db->get_where('uf',array('iduf'=>$id));
    }
    
    function get_salario_minimo($ano,$mes)
    {
        $this->db->where('fecha <= ',$ano.'-'.$mes.'-01');                
        $f = $this->db->get('salarios_minimos');
        return $f;        
    }
    
    function getfamiliares($ano)
    {        
        return $this->db->get_where('asignacionesfamiliares',array('ano'=>$ano));        
    }
    
    function getimpuestos($ano,$mes)
    {
        $this->db->where('EXTRACT(Month from fecha) = ',$mes);
        $this->db->where('EXTRACT(Year from fecha) = ',$ano);
        return $this->db->get('impuestos_trabajadores');
    }
    
    function getsca($ano,$mes,$empresa)
    {
        $this->db->where('EXTRACT(Month from fecha) <= ',$mes);
        $this->db->where('EXTRACT(Year from fecha) <= ',$ano);
        $this->db->where('empresa',$empresa);
        $this->db->order_by('id','DESC');
        return $this->db->get('dbsca');       
    }
    
    function getretsalud($ano,$mes,$empresa)
    {
        $this->db->where('EXTRACT(Month FROM fecha) = ',$ano);
        $this->db->where('EXTRACT(Year FROM fecha) = ',$mes);
        $this->db->where('empresa',$empresa);
        return $this->db->get('dbsalud');
    }
}
?>
