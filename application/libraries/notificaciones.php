<?php
class Notificaciones{
    
  function __construct()
  {
    $this->ci = get_instance();
  }
  
  function notificar_registro($id,$pass)
  {
      $user = $this->ci->db->get_where('user',array('id'=>$id));
      if($user->num_rows>0){
          $this->registro_mail($user->row(),$pass);
          $this->send_sms($user->row()->celular,'Ingresa en www.fderecho.net/mesaentrada con usuario:'.$user->row()->username.' y contraseña: '.$pass.' para ver las providencias de tus expedientes en mesa de entrada.');
      }
  }
  
  function notificar_expediente($id)
  {
      $this->ci->db->select('expedientes.*, remitentes.user as rem, destinatarios.user as des, user.celular, user.nombre as destino, user.email as desemail');
      $this->ci->db->join('remitentes','remitentes.id = expedientes.remitente');
      $this->ci->db->join('destinatarios','destinatarios.id = expedientes.destinatario');
      $this->ci->db->join('user','user.id = destinatarios.user');
      $exp = $this->ci->db->get_where('expedientes',array('expedientes.id'=>$id));
      if($exp->num_rows > 0)
      {
          $exp = $exp->row();
          $rem = $this->ci->db->get_where('user',array('id'=>$exp->rem))->row();
          $exp->remdata = $rem->nombre.' '.$rem->apellido;
          $this->expediente_email_rem($exp);
          $this->send_sms($rem->celular,'Tu expediente  Nro.'.$exp->id.' fue enviado exitosamente.');
          $this->expediente_email_des($exp);
          $this->send_sms($exp->celular,'Has recibido el expediente  Nro. '.$exp->id.' para providenciar.');
      }
  }
  
  function notificar_providencia($id)
  {
      $this->ci->db->select('expedientes.*, providencias.destinatario as providente, providencias.contestacion as providencia, providencias.fecha as provfecha, remitentes.user as rem, destinatarios.user as des, user.nombre as destino, user.email as desemail');
      $this->ci->db->join('remitentes','remitentes.id = expedientes.remitente');
      $this->ci->db->join('destinatarios','destinatarios.id = expedientes.destinatario');
      $this->ci->db->join('user','user.id = destinatarios.user');
      $this->ci->db->join('providencias','providencias.expediente = expedientes.id');
      $exp = $this->ci->db->get_where('expedientes',array('providencias.id'=>$id));
      if($exp->num_rows > 0)
      {
          $exp = $exp->row();
          $rem = $this->ci->db->get_where('user',array('id'=>$exp->rem))->row();
          $exp->remdata = $rem->nombre.' '.$rem->apellido;
          if($exp->providente!=0){
          $prov = $this->ci->db->get_where('user',array('id'=>$this->ci->db->get_where('destinatarios',array('id'=>$exp->providente))->row()->user))->row();
          $exp->providente = $prov->nombre;
          $exp->providente_email = $prov->email; 
          $exp->providente_cell = $prov->celular; 
          $this->providencia_email_des($exp);
          $this->send_sms($exp->providente_cell,'Te han derivado el expediente nro. '.$exp->id.' para providenciar');
          }
          $this->providencia_email_rem($exp);
          $this->send_sms($rem->celular,'Tu expediente nro. '.$exp->id.'fue providenciado: '.substr(strip_tags($exp->providencia),0,10));
      }
  }
  
  function registro_mail($user,$pass)
  {
      echo $pass;
      mail($user->email,'Registro de usuario',$this->ci->load->view('email/registro',array('user'=>$user,'pass'=>$pass),TRUE));
      return true;
  }
  
  function expediente_email_rem($exp)
  {
      $this->ci->mailer->mail($this->ci->db->get_where('user',array('id'=>$exp->rem))->row()->email,'Hemos enviado tu expediente',$this->ci->load->view('email/expediente_remitente',array('exp'=>$exp,'user'=>$exp->remdata),TRUE));
      return true;
  }
  
  function expediente_email_des($exp)
  {
      $this->ci->mailer->mail($exp->desemail,'Haz recibido un expediente',$this->ci->load->view('email/expediente_destinatario',array('exp'=>$exp,'user'=>$exp->destino),TRUE));
      return true;
  }
  
  function providencia_email_des($exp)
  {
      $this->ci->mailer->mail($exp->providente_email,'Te han derivado un expediente',$this->ci->load->view('email/providencia_destinatario',array('exp'=>$exp,'user'=>$exp->providente),TRUE));
      return true;
  }
  
  function providencia_email_rem($exp)
  {
      $this->ci->mailer->mail($this->ci->db->get_where('user',array('id'=>$exp->rem))->row()->email,'Tu expediente ha sido providenciado',$this->ci->load->view('email/providencia_remitente',array('exp'=>$exp,'user'=>$exp->remdata),TRUE));
      return true;
  }
  
  function send_sms($to,$message)
  {
      if($this->ci->db->get('ajustes')->row()->sms==1){
      $response = $this->ci->nexmo->send_message('fderecho',$to, array('text'=>$message));
      $this->ci->nexmo->d_print($response);
      }
  }
  
  function test_sms()
  {
      $from = 'Joncar';
      $to = '584169677564';
      $message = array('text' => 'test message');
      $response = $this->ci->nexmo->send_message($from, $to, $message);
      $this->ci->nexmo->d_print($response);
      return $this->ci->nexmo->get_http_status();
  }
}
?>