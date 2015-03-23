<?php

	class User extends CI_Model{
		var $log = false;

		function __construct(){
			parent::__construct();
			if(!empty($_SESSION['user']))
				$this->log = $_SESSION['user'];
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
                    $_SESSION['user']=$r->id;                    
                    $_SESSION['nombre']=$r->nombre;
                    $_SESSION['apellido']=$r->apellido;
                    $_SESSION['email'] = $r->email;
                    $_SESSION['rut'] = $r->rut;
                    $_SESSION['preferencial'] = $r->preferencial;
                    $_SESSION['premium'] = $r->premium;
                    $_SESSION['cuenta'] = $r->cuenta;                                        
                    $_SESSION['saldo'] = empty($r->saldo)?0:$r->saldo;
                }

		function unlog()
		{
			session_unset();
		}
		
		function edit($data)
		{
			$this->db->where('id',$_SESSION['user']);
			$this->db->update('user',$data);
		}

	}

?>
