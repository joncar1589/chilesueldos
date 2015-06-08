<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
ob_start();
session_start();

class Main extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -  
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     * Since this controller is set as the default controller in 
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    var $rules_admin = array();
    var $rules_user = array('main', 'facebook');
    var $rules_guest = array('cruds/usuarios');
    private $restricted = 'conectar';
    var $errorView = '404';
    var $pathPictures = 'assets/uploads/pictures';
    var $pathAvatars = 'assets/uploads/pictures';
    var $pathCurriculos = 'assets/uploads/pictures';

    public function __construct() {

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
        $this->db->dbnotprefix = array('dbclientes112010', 'j04299e21');
        ini_set('display_errors', true);
    }

    public function index() {
	$this->loadView('main');
    }

    public function success($msj) {
        return '<div class="alert alert-success">' . $msj . '</div>';
    }

    public function error($msj) {
        return '<div class="alert alert-danger">' . $msj . '</div>';
    }

    public function login() {
        if (!$this->user->log) {
            if (!empty($_POST['usuario']) && !empty($_POST['pass'])) {
                $this->db->where('email', $this->input->post('usuario'));
                $r = $this->db->get('user');
                if ($this->user->login($this->input->post('usuario', TRUE), $this->input->post('pass', TRUE))) {
                    if ($r->num_rows > 0 && $r->row()->status == 1) {
                        if (!empty($_POST['remember']))
                            $_SESSION['remember'] = 1;
                        if (empty($_POST['redirect']))
                            echo $this->success('Usuario logueado correctamente por favor espere...! <script>document.location.href="' . site_url() . '"</script>');
                        else
                            echo $this->success('Usuario logueado correctamente por favor espere...! <script>document.location.href="' . $_POST['redirect'] . '"</script>');
                    } else
                        $_SESSION['msj'] = $this->error('El usuario se encuentra bloqueado, comuniquese con un administrador para solucionar su problema');
                } else
                    $_SESSION['msj'] = $this->error('Usuario o contrasena incorrecta, intente de nuevo.');
            } else
                $_SESSION['msj'] = $this->error('Debe completar todos los campos antes de continuar');

            if (!empty($_SESSION['msj']))
                header("Location:" . base_url('registro/index/add'));
        }
    }

    public function unlog() {
        $this->user->unlog();        
        header("Location:" . site_url());
    }

    public function loadView($param = array('view' => 'main')) {
        if (is_string($param))
            $param = array('view' => $param);
        $this->load->view('template', $param);
    }

    public function loadViewAjax($view, $data = null) {
        $view = $this->valid_rules($view);
        $this->load->view($view, $data);
    }
    
    function error404(){
            $this->loadView(array('view'=>'errors/403'));
        }
        
     // se agrego las variables reenviar y empresa, esto es para que al momento de utilizar la opcion
    // Reenviar en la pagina de liquidaciones, se envie la notificacion a todos los usuarios que tienen
    // Asignada esa empresa
    function notificar($id = '', $reenviar = '', $empresa = '') {
        if (!empty($id))
            $this->db->where('id', $id);
        $this->db->where('status', 1);
        if (empty($reenviar)) {
            $this->db->where('siguiente_notificacion <=', date("Y-m-d"));
        }
        foreach ($this->db->get('notificaciones')->result() as $n) {

            //Calculamos la siguiente vez que se ejecute
            if ($n->repetir == 0) {
                $this->db->query('update sueldos_notificaciones set siguiente_notificacion=NULL where id = ' . $n->id);
            } else {
                $siguiente = date("Y-m-d", strtotime("+" . $n->ciclo . ' day ' . $n->siguiente_notificacion));
                $this->db->update('notificaciones', array('siguiente_notificacion' => $siguiente), array('id' => $n->id));
            }
            $destinatarios = explode(",", $n->destinatarios);
            $des = '';
            foreach ($destinatarios as $x) {
                switch ($x) {
                    case 'Todos':
                        $user = $this->db->get('user');
                        foreach ($user->result() as $u) {
                            $texto = $this->getcontentmail($n, $u);
                            if (empty($des) || !strstr($u->email, $des)) {
                                $des = $u->email . ',';
                            }
                            echo $n->remitente . '<br/>' . $n->destinatarios . $des . '<div>' . utf8_decode($texto) . '</div>';
                            correo($des.'info@chilesueldos.cl', $n->titulo, utf8_decode($texto), $n->remitente);
                            //correo('victor@chiletributa.cl', $n->titulo, utf8_decode($texto), $n->remitente);
                        }
                        break;
                    case 'Personas':
                        $this->db->where('dbclientes112010.user is NULL', FALSE, FALSE);
                        $this->db->join('dbclientes112010', 'dbclientes112010.user = user.id', 'left');
                        $this->db->select('user.*');
                        $user = $this->db->get('user');
                        foreach ($user->result() as $u) {
                            $texto = $this->getcontentmail($n, $u);
                            if (empty($des) || !strstr($u->email, $des)) {
                                $des.= $u->email . ',';
                            }
                            echo $n->remitente . '<br/>' . $n->destinatarios . $des . '<div>' . utf8_decode($texto) . '</div>';
                            correo($des.'info@chilesueldos.cl', $n->titulo, utf8_decode($texto), $n->remitente);
                            //correo('victor@chiletributa.cl', $n->titulo, utf8_decode($texto), $n->remitente);
                        }
                        break;
                    case 'Empresas':
                        $this->db->group_by('dbclientes112010.user');
                        $this->db->join('dbclientes112010', 'dbclientes112010.user = user.id', 'inner');
                        $this->db->select('dbclientes112010.user');
                        $user = $this->db->get('user');
                        foreach ($user->result() as $u) {
                            $p = $this->db->get_where('user', array('id' => $u->user))->row();
                            $texto = $this->getcontentmail($n, $p);
                            if (empty($des) || !strstr($p->email, $des)) {
                                $des = $p->email . ',';
                            }
                            echo $n->remitente . '<br/>' . $n->destinatarios . $des . '<div>' . utf8_decode($texto) . '</div>';
                            correo($des.'info@chilesueldos.cl', $n->titulo, utf8_decode($texto), $n->remitente);
                            //correo('victor@chiletributa.cl', $n->titulo, utf8_decode($texto), $n->remitente);
                        }
                        break;
                    case 'Preferenciales':
                        $user = $this->db->get_where('user', array('preferencial' => 1));
                        foreach ($user->result() as $u) {
                            $texto = $this->getcontentmail($n, $u);
                            if (empty($des) || !strstr($u->email, $des)) {
                                $des = $u->email . ',';
                            }
                            echo $n->remitente . '<br/>' . $n->destinatarios . $des . '<div>' . utf8_decode($texto) . '</div>';
                            correo($des.'info@chilesueldos.cl', $n->titulo, utf8_decode($texto), $n->remitente);
                            //correo('victor@chiletributa.cl', $n->titulo, utf8_decode($texto), $n->remitente);
                        }
                        break;
                    case 'Premiums':
                        $user = $this->db->get_where('user', array('premium' => 1));
                        foreach ($user->result() as $u) {
                            $texto = $this->getcontentmail($n, $u);
                            if (empty($des) || !strstr($u->email, $des)) {
                                $des = $u->email . ',';
                            }
                            echo $n->remitente . '<br/>' . $n->destinatarios . $des . '<div>' . utf8_decode($texto) . '</div>';
                            correo($des.'info@chilesueldos.cl', $n->titulo, utf8_decode($texto), $n->remitente);
                           // correo('victor@chiletributa.cl', $n->titulo, utf8_decode($texto), $n->remitente);
                        }
                        break;

                    case 'Otros':
                        $otros = explode(",", $n->otros);
                        foreach ($otros as $o) {
                            list($nombre, $email) = explode("-", $o);
                            $this->db->or_where('email', $email);
                        }
                        foreach ($this->db->get('user')->result() as $u) {
                            $texto = $this->getcontentmail($n, $u);
                            if (empty($des) || !strstr($u->email, $des))
                                $des = $u->email . ',';
                            echo $n->remitente . '<br/>' . $n->destinatarios . $des .', info@chilesueldos.cl'. '<div>' . utf8_decode($texto) . '</div>';
                            correo($des.'info@chilesueldos.cl', $n->titulo . ' ' . $u->nombre, utf8_decode($texto), $n->remitente);
                            //correo('victor@chiletributa.cl', $n->titulo . ' ' . $u->nombre, utf8_decode($texto), $n->remitente);
                            //correo('ignacio@chiletributa.cl', $n->titulo . ' ' . $u->nombre, utf8_decode($texto), $n->remitente);
                        }
                        break;
                    //opcion reenviar que se activa desde la pagina de liquidaciones    
                    case 'Reenviar' :
                        if ($reenviar == 1) {
                            
                            $this->db->select('user_permiso.*, user_creador.id as id_creador, user_creador.nombre as nombre_creador, user_creador.email as email_creador');
                            $this->db->join('sueldos_permisos', 'sueldos_permisos.empresa = dbclientes112010.id2', 'left');
                            $this->db->join('sueldos_user as user_permiso', 'user_permiso.id = sueldos_permisos.user', 'left');
                            $this->db->join('sueldos_user as user_creador', 'user_creador.id = dbclientes112010.user', 'inner');
                            $this->db->where('dbclientes112010.id2', $empresa);
                            $user = $this->db->get('dbclientes112010');

                            foreach ($user->result() as $u) {
                                $texto = $this->getcontentmail($n, $u);
                                if (empty($des) || !strstr($u->email, $des)) {
                                    $des = $u->email . ',';
                                }
                                echo $n->remitente . '<br/>' . $n->destinatarios . $des . '<div>' . utf8_decode($texto) . '</div>';
                                correo($des.'victor@chiletributa.cl', $n->titulo, utf8_decode($texto), $n->remitente);             
                                //correo('victor@chiletributa.cl, ignacio@chiletributa.cl', $n->titulo, utf8_decode($texto), $n->remitente);
                            }
                            $des = $user->row()->email_creador.',';
                            $user->row()->id = $user->row()->id_creador;
                            $texto = $this->getcontentmail($n,$user->row());
                            echo $n->remitente . '<br/>' . $n->destinatarios . $des . '<div>' . utf8_decode($texto) . '</div>';
                            correo($des.'victor@chiletributa.cl', $n->titulo, utf8_decode($texto), $n->remitente);
                        }
                    break;
                }
            }
            // 	echo $n->remitente.'<br/>'.$n->destinatarios.'<div>'.utf8_decode($texto).'</div>';
            //	correo($des,$n->titulo,utf8_decode($texto),$n->remitente);
            //	correo('victor@chiletributa.cl',$n->titulo,utf8_decode($texto),$n->remitente);
        }
    }

    function getcontentmail($n, $user) {

        $texto = $n->text;
        $texto = str_replace('$_USER', $user->id, $texto);
        funciones($n->funcion, $user);
        $texto = str_replace("&gt;", '>', $texto);
        $texto = str_replace("&lt;", '<', $texto);
        $sql = fragmentar($texto, '{sql="', '}');
        foreach ($sql as $s) {
            list($sentencia, $type) = explode(";", $s);
            $sentencia = str_replace('"', '', $sentencia);
            switch ($type) {
                case 'data':$texto = str_replace('{sql="' . $s . '}', sqltodata($this->db->query(strip_tags($sentencia))), $texto);
                    break;
                case 'table':$texto = str_replace('{sql="' . $s . '}', sqltotable($this->db->query(strip_tags($sentencia))), $texto);
                    break;
                case 'table_not': $datos = sqltotable($this->db->query(strip_tags($sentencia)));
                    $datos = str_replace('<table border="1">', '<table border="1" style="border-spacing: 0 0; border-collapse: collapse;">', $datos);
                    $datos = str_replace('<td><b>nickname</b></td>', '<th><b>Empresa</b></th>', $datos);
                    $datos = str_replace('<td><b>total_liquido</b></td>', '<th style="width:100px;"><b> Total Liquido </b></th>', $datos);
                    $datos = str_replace('<td><b>total_leyes</b></td>', '<th style="width:100px;"><b> Total Leyes </b></th>', $datos);
                    $datos = str_replace('</th><td><b>url</b></td></tr>', '</th></tr>', $datos);
                    $datos = str_replace('[url=', ' <a href="', $datos);
                    $datos = str_replace(']', '"><b>Liquidaciones</b></a> ', $datos);
                    $texto = str_replace('{sql="' . $s . '}', $datos, $texto);
                    break;
            }
        }

        return $texto;
    }

}

/* End of file welcome.php */
    /* Location: ./application/controllers/welcome.php */    
