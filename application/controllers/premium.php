<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('panel.php');
class Premium extends Panel {
        
	public function __construct()
	{
		parent::__construct(); 
                if($_SESSION['cuenta']!=1 && !$this->querys->has_access('premium'))
                    header("Location:".base_url('panel'));
	}
       
        public function index($url = 'main',$page = 0)
	{
		parent::index();
	}                    
        /*Cruds*/  
        
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */