<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('main.php');
class Paginas extends Main {
        
	public function __construct()
	{
		parent::__construct();
                if(empty($_SESSION['user']))
                header("Location:".base_url());
	}       
        public function index($url = 'main')
	{
            $p = $this->db->get_where('paginas',array('url'=>$url));
            if($p->num_rows>0)
            $this->loadView(array('view'=>'paginas','data'=>$p->row()->contenido));
            else
            $this->loadView('404');
	}                
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */