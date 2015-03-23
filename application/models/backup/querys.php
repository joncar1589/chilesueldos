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
    
    function set_where_propietarios($cell)
    {
        $this->db->where($cell,$_SESSION['user']);
        if(!empty($_SESSION['propietarios'])){
            foreach($_SESSION['propietarios'] as $p)
            $this->db->or_where($cell,$p);
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
}
?>
