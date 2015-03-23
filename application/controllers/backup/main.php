<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
ob_start();
session_start();
class Main extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
        var $rules_admin = array();
	var $rules_user = array('main','facebook');
	var $rules_guest = array('cruds/usuarios');
	private $restricted = 'conectar';
	var $errorView = '404';
	var $pathPictures = 'assets/uploads/pictures';
        var $pathAvatars = 'assets/uploads/pictures';
        var $pathCurriculos = 'assets/uploads/pictures';
        
	public function __construct()
	{
		parent::__construct();
                $this->load->database();
		$this->load->helper('url');
                $this->load->helper('html');
                $this->load->helper('h');
                $this->load->model('user');
                $this->load->model('querys');
                $this->load->library('grocery_crud');                
                $this->load->library('ajax_grocery_crud');                                
                $this->load->library('fpdf/fpdf');
	}
        
        public function index()
	{
            $this->loadView('main');
	}
        
        public function success($msj)
	{
		return '<div class="alert alert-success">'.$msj.'</div>';
	}

	public function error($msj)
	{
		return '<div class="alert alert-danger">'.$msj.'</div>';
	}
        
        public function login()
	{
		if(!$this->user->log)
		{	
			if(!empty($_POST['usuario']) && !empty($_POST['pass']))
			{
				$this->db->where('email',$this->input->post('usuario'));
				$r = $this->db->get('user');
				if($this->user->login($this->input->post('usuario',TRUE),$this->input->post('pass',TRUE)))
				{
					if($r->num_rows>0 && $r->row()->status==1)
					{                                                                                        
                                            if(!empty($_POST['remember']))$_SESSION['remember'] = 1;
                                            if(empty($_POST['redirect']))
                                            echo $this->success('Usuario logueado correctamente por favor espere...! <script>document.location.href="'.site_url().'"</script>');
                                            else
                                            echo $this->success('Usuario logueado correctamente por favor espere...! <script>document.location.href="'.$_POST['redirect'].'"</script>');                                            
					}
					else $_SESSION['msj'] = $this->error('El usuario se encuentra bloqueado, comuniquese con un administrador para solucionar su problema');
				}
                                else $_SESSION['msj'] = $this->error('Usuario o contrasena incorrecta, intente de nuevo.');
			}
			else
                            $_SESSION['msj'] = $this->error('Debe completar todos los campos antes de continuar');
                        
                        if(!empty($_SESSION['msj']))
                            header("Location:".base_url('registro/index/add'));
		}
	}

	public function unlog()
	{
		$this->user->unlog();
                $_SESSION['user'] = 'clean';
                header("Location:".site_url());
	}
        
        public function loadView($param = array('view'=>'main'))
        {
            if(is_string($param))
            $param = array('view'=>$param);
            $this->load->view('template',$param);
        }
		
	public function loadViewAjax($view,$data = null)
        {
            $view = $this->valid_rules($view);
            $this->load->view($view,$data);
        }
        
        function notificar()
        {
            foreach($this->db->get_where('notificaciones',array('status'=>1))->result() as $n){
                foreach($this->db->get('user')->result() as $user){
                $texto = $n->text;
                $texto = str_replace('$_USER',$user->id,$texto);
                $sql = fragmentar($texto,'{sql="','}');
                foreach($sql as $s)
                {
                    list($sentencia,$type) = explode(";",$s);
                    $sentencia = str_replace('"','',$sentencia);
                    switch($type)
                    {
                        case 'data':$texto = str_replace('{sql="'.$s.'}',sqltodata($this->db->query(strip_tags($sentencia))),$texto);
                        case 'table':$texto = str_replace('{sql="'.$s.'}',sqltotable($this->db->query(strip_tags($sentencia))),$texto);
                    }
                }
                echo $n->remitente.'<br/>'.$n->destinatarios.'<div>'.utf8_decode($texto).'</div>';
	        $destinatarios = explode(",",$n->destinatarios);
		$des = '';
		foreach($destinatarios as $x){
		switch($x)
		{
			case 'Todos':
                                if(empty($des) || !strstr($user->email,$des))
                                    $des.= $user->email.',';											
			break;
			case 'Personas':				
				if($this->db->get_where('dbclientes112010',array('user'=>$user->id))->num_rows==0)
				{
					if(!strstr($user->email,$des))
					$des.= $user->email.',';				
				}
			break;
			case 'Empresas':				
				if($this->db->get_where('dbclientes112010',array('user'=>$user->id))->num_rows>0)
				{
					if(!strstr($user->email,$des))
					$des.= $user->email.',';				
				}
			break;
			case 'Preferenciales':				
				if($user->preferencial==1)
				{
					if(!strstr($user->email,$des))
					$des.= $user->email.',';				
				}
			break;
			case 'Premiums':
				if($user->premium==1)
				{
					if(!strstr($user->email,$des))
					$des.= $user->email.',';				
				}
			break;
		}
		}
		correo($des,$n->titulo,utf8_decode($texto),$n->remitente);
                }
            }
        }    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */