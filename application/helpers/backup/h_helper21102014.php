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
        
        function funciones($callback,$user){
            switch($callback)
            {
                case '1':
                    calcular_liquidacion($user);
                break;
            }
        }
        
        function calcular_liquidacion($user)
        {
            $empresas = get_instance()->db->get_where('dbclientes112010',array('user'=>$user->id));
            if($empresas->num_rows>0){
                foreach($empresas->result() as $emp){
                    get_instance()->db->where('empresa',$emp->id);
                    get_instance()->db->where('fecha_egreso is null',null,FALSE);                    
                    foreach(get_instance()->db->get('trabajadores')->result() as $e){


			   /*Modificado por Victor Alarcon el 02-10-2014 para que el Script ejecute el calculo de las liquidaciones del mes anterior hasta el 13 de cada mes
			     y desde el 14 en adelante se realize el calculo del mes en curso*/
			  /*-----------------------------------------------------------------------------------------------------------------------------------------------------*/
						$fecha_emision = date("d")<=13?date("Y-m-d",strtotime(date("Y-m-t",strtotime("-1 month ".date("Y-m"))))):date("Y-m-d",strtotime(date("Y-m-t")));
                        $empleado = get_instance()->querys->get_salario($e->id);                        $iduf = date("d")<=13?((date("Y")-2000)*date("Y"))+date("m",strtotime("-1 month ".date("Y-m"))):((date("Y")-2000)*date("Y"))+date("m");
                        $dbuf = get_instance()->querys->getuf($iduf);
                        $salario = date("d")<=13?get_instance()->querys->get_salario_minimo(date("Y"),date("m",strtotime("-1 month ".date("Y-m")))):get_instance()->querys->get_salario_minimo(date("Y"),date("m"));
                        $dbfamiliares = get_instance()->querys->getfamiliares(date("Y"));
                        $dbimpuestos = date("d")<=13?get_instance()->querys->getimpuestos(date("Y"),date("m",strtotime("-1 month ".date("Y-m")))):get_instance()->querys->getimpuestos(date("Y"),date("m"));
                        $dbsca = date("d")<=13?get_instance()->querys->getsca(date("Y"),date("m",strtotime("-1 month ".date("Y-m"))),$emp->id):get_instance()->querys->getsca(date("Y"),date("m"),$emp->id);
                        $dbsca = $dbsca->num_rows==0?0.95:$dbsca->row()->factor;
						
						//Modificado por Victor el 07/10/2014 para fijar la tasa en 0.07 para todas las empresas y eliminar el dbsalud
                        //$dbsalud = date("d")<=13?get_instance()->querys->getretsalud(date("Y"),date("m",strtotime("-1 month ".date("Y-m"))),$emp->id):get_instance()->querys->getretsalud(date("Y"),date("m"),$emp->id);
                        //$dbsalud = $dbsalud->num_rows==0?0.07:$dbsalud->row()->factor;
						
						
						/*-----------------------------------------------------------------------------------------------------------------------------------------------------*/
                        if($empleado->num_rows>0 && $dbuf->num_rows>0 && $salario->num_rows>0 && $dbfamiliares->num_rows>0 && $dbimpuestos->num_rows>0 && !empty($dbsca)){
                        $empleado = $empleado->row();
                        $dbuf = $dbuf->row();
                        $salario = $salario->row();
                        $dbfamiliares = $dbfamiliares->row();
                        $dbimpuestos = $dbimpuestos->row();                                                

                        $bonos = 0;
                        $comisiones = 0;                                                        
                        $aguinaldos = 0;
                        $cantidad_horas = 0;
                        $factor_hora_extra = 0;
                        $indem_sustit = 0;
                        $indem_anos = 0;
                        $cta_ahorro_afp = 0;
                        $anticipos = 0;
                        $prestamos_ccaf = 0;
                        $otros_descuentos = 0;
                        $descuentos_prestamos = 0;
                        $dias_trabajados = 30;
                        $dias_mes = 30;
                        $sueldo_base = $empleado->sueldo_base;

						$tasa_salud = $empleado->id_caja !== '1' && $empleado->sistema_salud_afiliado == '5'?0.064:0.07;
                        $sueldo_proporcional = $dias_trabajados/$dias_mes*$sueldo_base;                            
                        $hora_ordinaria = (($sueldo_base/30)*28)/180;
                        $valor_hora = $hora_ordinaria*$factor_hora_extra;                            
                        $horas = $valor_hora*$cantidad_horas;        
                        //Calcular preimponible
                        $preimponible = $sueldo_proporcional+$comisiones+$bonos+$aguinaldos+$horas;        
                        //Calcular gratificaciones
                        $salario_minimo = $salario->monto;
                        $gratificaciones = $preimponible>0?(($empleado->gratificacion/100)*$preimponible>$salario_minimo*4.75/12)?$salario_minimo*4.75/12:$preimponible*($empleado->gratificacion/100):0;               
                        //Subtotal haberes
                        $subtotal_haberes = $preimponible+$gratificaciones;        
                        //Traer tope imponible
                        $tope_imponible = $dbuf->tope_afp*$dbuf->uf;
                        //Calcualr total imponible
                        $total_imponible = $tope_imponible<=$subtotal_haberes?$tope_imponible:$subtotal_haberes;            
                        //Calcular bono_locomocion
                        $locomocion = $empleado->bono_locomocion * $dias_trabajados/$dias_mes;
                        //Calcular Colacion
                        $colacion = $empleado->bono_colacion * $dias_trabajados/$dias_mes;
                        //Calcular otras asignaciones
                        $otras_asignaciones = $empleado->otras_asignaciones * $dias_trabajados/$dias_mes;
                        //Calcular cargas familiares
                        $familiares = 0;
                        if($total_imponible<$dbfamiliares->tramo_a2)
                        $familiares = $empleado->cargas_familiares*$dbfamiliares->monto_tramo_a;
                        else if($total_imponible<$dbfamiliares->tramo_b2)
                        $familiares = $empleado->cargas_familiares*$dbfamiliares->monto_tramo_b;
                        else if($total_imponible<$dbfamiliares->tramo_c2)
                        $familiares = $empleado->cargas_familiares*$dbfamiliares->monto_tramo_c;
                        else if($total_imponible<$dbfamiliares->tramo_d2)
                        $familiares = $empleado->cargas_familiares*$dbfamiliares->monto_tramo_d;
                        //Calcular total no imponible
                        $totalnoimponible = $locomocion + $colacion + $otras_asignaciones + $familiares + $indem_sustit + $indem_anos;        
                        //Calcular fondo de pensiones
                        $fondopensiones = $total_imponible*($empleado->por_afp/100);
                        //Calcular % de salud
                        $salud = $total_imponible <= $tope_imponible?$total_imponible*$tasa_salud:$tope_imponible*$tasa_salud;
                        //Calcular apv	
                        $apv = $empleado->apv==''?0:$empleado->apv;                            
                        //Calcular inp
                        $inp = $empleado->dependiente == 1?0:0;
                        //Calcular seguro cesantia
                        $tope_afc = $dbuf->tope_afc * $dbuf->uf;
                        $s = $empleado->tipo_contrato=='FIJO'?0:0.6;        
                        $segurocesantia = $empleado->empleado_casa=='1'?0:$subtotal_haberes<=$tope_afc?($s/100)*$subtotal_haberes:($s/100)*$tope_afc;
                        //Calcular adicional salud
                        $legal_7 = $total_imponible<=$tope_imponible?$total_imponible*$tasa_salud:$tope_imponible*$tasa_salud;
                        $plan_isapre = $empleado->plan_isapre*$dbuf->uf;
                        $adicional_salud = $plan_isapre>$legal_7?$plan_isapre-$legal_7:0;
						$caja_com_trabj = $empleado->sistema_salud_afiliado=='5'&& $empleado->id_caja!=='1'?$total_imponible*($empleado->por_caja/100):0;
						$caja_com_trabj = round($caja_com_trabj);
						
                        //Calcular total leyes sociales
                        $total_leyes_sociales = $fondopensiones+$salud+$apv+$inp+$segurocesantia+$adicional_salud+$caja_com_trabj;        
                        //Calcular renta_afecta
                        $renta_afecta = $subtotal_haberes-$total_leyes_sociales;
                        //Calcular imp2cat
                        $imp2cat = 1;
                        if($renta_afecta<$dbimpuestos->tramo_0b)                    
                        $imp2cat = $renta_afecta*$dbimpuestos->factor_0-$dbimpuestos->rebaja_0;
                        else if($renta_afecta<$dbimpuestos->tramo_5b)                    
                        $imp2cat = $renta_afecta*$dbimpuestos->factor_5-$dbimpuestos->rebaja_5;
                        else if($renta_afecta<$dbimpuestos->tramo_10b)                    
                        $imp2cat = $renta_afecta*$dbimpuestos->factor_10-$dbimpuestos->rebaja_10;
                        else if($renta_afecta<$dbimpuestos->tramo_15b)                    
                        $imp2cat = $renta_afecta*$dbimpuestos->factor_15-$dbimpuestos->rebaja_15;
                        else if($renta_afecta<$dbimpuestos->tramo_25b)                    
                        $imp2cat = $renta_afecta*$dbimpuestos->factor_25-$dbimpuestos->rebaja_25;
                        else if($renta_afecta<$dbimpuestos->tramo_32b)                    
                        $imp2cat = $renta_afecta*$dbimpuestos->factor_32-$dbimpuestos->rebaja_32;
                        else if($renta_afecta<$dbimpuestos->tramo_37b)      
                        $imp2cat = $renta_afecta*$dbimpuestos->factor_37-$dbimpuestos->rebaja_37;
                        else if($renta_afecta<$dbimpuestos->tramo_40b)                    
                        $imp2cat = $renta_afecta*$dbimpuestos->factor_40-$dbimpuestos->rebaja_40;        
                        //Otros descuentos
                        $otros_descuentos = $imp2cat+$cta_ahorro_afp + $anticipos + $prestamos_ccaf + $descuentos_prestamos + $otros_descuentos;        
                        //Totales
                        $total_haberes = $subtotal_haberes+$totalnoimponible;
                        $total_descuentos = $total_leyes_sociales+$otros_descuentos;                            
                        $alcance = $total_haberes-$total_descuentos;        
                        //Calcular seguro sobrevivencia sis
                        $tope_sis = $dbuf->tope_sis * $dbuf->uf;
                        $seguro_sis = $subtotal_haberes<=$tope_sis?$dbuf->tasa_sis/100*$subtotal_haberes:$dbuf->tasa_sis/100*$tope_sis;       
                        //Calculo de Aporte Indemnizacion 
                        $aporte = $empleado->empleado_casa=='1'?$total_imponible*(4.11/100):0;
                        //Cuenta Ahorro voluntario
                        $afp = $seguro_sis+$apv+$fondopensiones+$aporte;
                        $seguro_cesantia_trabajador = $empleado->empleado_casa=='1'?0:$segurocesantia;
                        $s = $empleado->tipo_contrato=='FIJO'?3:2.4;
                        $s /= 100;
                        $seguro_cesantia_empleador = $empleado->empleado_casa=='1'?0:$subtotal_haberes<$tope_afc?$subtotal_haberes*$s:$tope_afc*$s;
                        $afc = $seguro_cesantia_empleador+$seguro_cesantia_trabajador;
                        //Cotización accidente de trabajo
                        $tope_seguro = $dbuf->tope_seguro_accidente*$dbuf->uf;
                        $cot_accidente_trabajo = $subtotal_haberes<$tope_seguro?$dbsca/100*$subtotal_haberes:$dbsca/100*$tope_seguro;        
                        $ips = $empleado->id_caja !=='1'?$cot_accidente_trabajo:$cot_accidente_trabajo-$familiares;
						$caja_comp = $empleado->sistema_salud_afiliado=='5'&&$empleado->id_caja!=='1'?($total_imponible*($empleado->por_caja/100))-$familiares:0;
						$caja_comp = round($caja_comp);
                        //Cotización obligatoria            
                        //Adicional de salud        
                        $total_fonasa = $ips<0?$adicional_salud+$salud+$ips:$adicional_salud+$salud;
                        //Total leyes sociales
						
						$total_leyes_sociales;
						if($total_fonasa>=0&&$ips>=0&&$caja_comp>=0){
							$total_leyes_sociales = $afp+$afc+$ips+$total_fonasa+$caja_comp;
						}
						elseif($total_fonasa>=0&&$ips<0){
							$total_leyes_sociales = $afp+$afc+$total_fonasa;
						}
						elseif($total_fonasa<0){
							$total_leyes_sociales = $afp+$afc;
						}
						else{
							$total_leyes_sociales = $afp+$afc+$ips+$total_fonasa;
						}
						
                        //Costo total empleador
                        $costo_total_empleador = $total_leyes_sociales+$otros_descuentos+$alcance;                
                        
                        $data = array(
                            'user' =>$user->id,
                            "fecha_emision"=>$fecha_emision,
                            "empresa"=>$emp->id,
                            "empleado"=>$e->id,
                            "fecha"=>$fecha_emision,
                            "dias_mes"=>$dias_mes,
                            "dias_trabajados"=>$dias_trabajados,
                            "cantidad_horas"=>$cantidad_horas,
                            "factor_hora_extra"=>$factor_hora_extra,
                            "hrs_extras"=>$horas,
                            "bonos_ficha"=>0,
                            "comisiones"=>$comisiones,
                            "bonos"=>$bonos,
                            "aguinaldos"=>$aguinaldos,
                            "preimponible"=>$preimponible,
                            "gratificaciones"=>$gratificaciones,
                            "sueldo_base"=>$sueldo_base,
                            "sueldo_base_proporcional"=>$sueldo_proporcional,
                            "sub_totales_haberes"=>$subtotal_haberes,
                            "tope_imponible"=>$tope_imponible,
                            "total_imponible"=>$total_imponible,
                            "locomocion"=>$locomocion,
                            "colacion"=>$colacion,
                            "familiar"=>$familiares,
                            "otras_asignaciones"=>$otras_asignaciones,
                            "indem_sustit"=>0,
                            "indem_anos"=>0,
                            "total_no_imponible"=>$totalnoimponible,
                            "total_leyes_sociales"=>$total_leyes_sociales,
                            "total_impuestos"=>$imp2cat,
                            "afp"=>$afp,
                            "cotizacion_obligatoria"=>$fondopensiones,
                            "seguro_inv"=>$seguro_sis,
                            "cuenta_ahorro"=>$apv,
                            "indemnizacion"=>$aporte,
                            "afc"=>$afc,
                            "seguro_cesantia_trabajador"=>$seguro_cesantia_trabajador,
                            "seguro_cesantia_empleador"=>$seguro_cesantia_empleador,
                            "mutualidad"=>$ips,
                            "cot_accidente_trabajo"=>$cot_accidente_trabajo,
                            "total_ips"=>$ips,
                            "cotizacion_obligatoria_salud"=>$salud,
                            "cotizacion_adicional"=>$adicional_salud,
                            "total_fonasa"=>$total_fonasa,
                            "impuesto_trabajador"=>$imp2cat,
                            "costo_total_empleador"=>$costo_total_empleador,
                            "fondo_pensiones"=>$fondopensiones,
                            "cotiz_salud"=>$salud,
                            "ahorro_prev_voluntario"=>$apv,
							"caja_com_trabj"=>$caja_com_trabj,
                            "cotiz_seg_ces"=>$segurocesantia,
                            "adicional_salud"=>$adicional_salud,
                            "total_leyes_soc"=>$total_leyes_sociales,
                            "renta_afecta"=>$renta_afecta,
                            "imp_2cat"=>$imp2cat,
                            "cta_ahorro_afp"=>$cta_ahorro_afp,
                            "anticipos"=>$anticipos,
                            "prestamos_ccaf"=>$prestamos_ccaf,
                            "descuentos_prestamos"=>$descuentos_prestamos,
                            "otros_descuentos"=>$otros_descuentos,
                            "total_otros_descuentos"=>$otros_descuentos,
                            "total_haberes"=>$total_haberes,
                            "total_descuentos"=>$total_descuentos,
                            "alcance_liquido"=>$alcance,
							"caja_comp"=>$caja_comp
                        );                        
                        $where = array('EXTRACT(Month from fecha) = '=>date("m",strtotime($fecha_emision)),'user'=>$user->id,"empresa"=>$emp->id,'empleado'=>$e->id);
                        if(get_instance()->db->get_where('liquidaciones',$where)->num_rows==0)
                        get_instance()->db->insert('liquidaciones',$data);
                        else
                        get_instance()->db->update('liquidaciones',$data,$where);
                       
					   }
                    }
                }
            }
        }
?>