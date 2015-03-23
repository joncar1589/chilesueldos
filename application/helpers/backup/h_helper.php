<?php 
	function  correo($email = 'joncar.c@gmail.com',$titulo = '',$msj = '',$from='info@softwopen.com')
        {            
            $sfrom=$from; //cuenta que envia
            $sdestinatario=$email; //cuenta destino
            $ssubject=$titulo; //subject
            $shtml=$msj; //mensaje
            $sheader="From:".$sfrom."\nReply-To:".$sfrom."\n";
            $sheader=$sheader."X-Mailer:PHP/".phpversion()."\n";
            $sheader=$sheader."Mime-Version: 1.0\n";
            $sheader=$sheader."Content-Type: text/html; charset:utf-8";
            mail($sdestinatario,$ssubject,$shtml,$sheader);
        }
        function meses($mes)
        {
            $meses = array('ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE');
            return $meses[$mes-1];
        }

        function contratos_tipo($val)
        {
            switch($val)
            {
                case 0: return 'ARRENDAMIENTO';
                case 1: return 'VENTA A PLAZO';
                case 2: return 'VENTA DE CONTADO';
                case 3: return 'VENTA A CRÉDITO';
            }
        }
        
        function rol($var)
        {
            switch($var)
            {
                case 0: return 'Remitente';
                case 1: return 'Destinatario';
                case 2: return 'Recepcionista';
                case 3: return 'Administrador';
            }
        }
        
        function status($var)
        {
            switch($var)
            {
                case 0: return 'Bloqueado';
                case 1: return 'Activo';
            }
        }
        
        function refresh_list()
        {
            return "<script>$('.filtering_form').trigger('submit')</script>";
        }
        
        function paypal_button($product,$price)
        {            
            return 
            img('img/paypal.jpg','width:100%').'
            <form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post" class="form-group well">
            <div class="row" style="padding:0 30px;">
            <div style="text-align:left;"><b>Indique el monto a recargar</b></div>
            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="business" value="'.  get_instance()->db->get('ajustes')->row()->email_paypal.'">
            <input type="hidden" name="currency_code" value="USD">
            <input name="custom" value="'.$_SESSION['user'].'" type="hidden">
            <input type="hidden" name="item_name" value="'.$product.'">
            <input type="text" name="amount" class="form-control" placeholder="Monto a recargar" value="">
            <input type="hidden" name="return" value="'.base_url('panel/thank').'">
            <input type="hidden" name="cancel_return" value="'.base_url('panel').'">
            <input type="hidden" name="notify_url" value="'.base_url('panel/ipnlistener').'">
            </div><div class="row" style="margin:30px 0">
            <button type="submit" class="btn btn-info">'.$product.'</button>
            </div>
            </form>';               
        }
        
        function fragmentar($TheStr, $sLeft, $sRight){             
            $d = array();
            foreach(explode($sLeft,$TheStr) as $n=>$c){
                if($n>0)
                array_push($d,substr($c,0,strpos($c,$sRight,0)));
            }                
            return $d; 
        }   
        
        function stringtosql($sentencia)
        {
            $se = explode("from",$sentencia);
            $se[0] = str_replace("select","",$se[0]);
            if(strpos($sentencia,'where')!==false){
            $s = explode("where",$se[1]);
            $se[1] = $s[0];
            $se[2] = $s[1];
            }
            return $se;
        }
        
        function sqltodata($query)
        {
            $data = '';            
            if($query->num_rows>0){
                foreach($query->result() as $s){
                    foreach($s as $x)
                        $data.= $x==null?'NULL':$x.' ';
                }                 
            }            
            return $data;
        }
        
        function sqltotable($query)
        {
            $data = '';                
            if($query->num_rows>0){
                foreach($query->result() as $n=>$s){                    
                    $data.='<tr>';
                    foreach($s as $x)
                        $data.='<td>'.$x.'</td>';
                    $data.='</tr>';
                }    
                $h='<tr>';
                    foreach($query->result_array() as $n=>$r){
                        if($n==0){
                        foreach($r as $p=>$z)
                        $h.='<td><b>'.$p.'</b></td>';
                        }
                    }
                $h.='</tr>';                
                return '<table border="1">'.$h.$data.'</table>';
            }                        
            else return 'Sin datos para mostrar';
        }                
?>