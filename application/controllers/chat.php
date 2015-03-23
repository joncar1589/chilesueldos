<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('panel.php');
class Chat extends Panel {
        
	public function __construct()
	{
		parent::__construct();                
	}
       
        public function index($id = '')
	{
		$this->loadView('chat');
	}	
        
        public function restartServer(){
            //Matar proceso            
            $str = '<p>pkill node</p>';            
            $str.= '<p>'.shell_exec('pkill node').'</p>';
            $str.= '<p>node /var/www/chilesueldos/server-chat-nodeJS/server.js</p>';
            $str.= '<p>'.system ('node /var/www/chilesueldos/server-chat-nodeJS/server.js').'</p>';
            echo $str.'<p><a href="javascript:document.location.reload()">Refrescar pagina</a></p>';
        }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */