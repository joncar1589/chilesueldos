<?php 
/* Pagina 1 y 2*/
for($i=0;$i<2;$i++){
$this->fpdf->AddPage();
/*Cuadro superior*/
$this->fpdf->SetFont('Arial','B',15);
$this->fpdf->SetFillColor(242,242,242);
$this->fpdf->cell(190,7.5,'LIQUIDACION DE SUELDO',0,0,'C',true);
$this->fpdf->Ln();
$this->fpdf->Ln();

/*Modificado por Victor el 29-09-2014*/

//Modifique la pocicion del Rut del trabajador y empleador.
//---------------------------------------------------------------------------------------------------------------------------------
$this->fpdf->SetFont('Arial','',10);														
$this->fpdf->cell(40,5,'EMPLEADOR',0,0);
$this->fpdf->cell(80,5,$liquidacion->empresa,0,0);
$this->fpdf->Ln();						
$this->fpdf->cell(13,5,'',0,0,'L');  			
$this->fpdf->cell(13,5,'',0,0,'L');  			
$this->fpdf->cell(14,5,'',0,0,'L');  			
$this->fpdf->cell(80,5,$liquidacion->empresarut,0,0);
$this->fpdf->Ln();
$this->fpdf->cell(40,5,'TRABAJADOR',0,0);
$this->fpdf->cell(80,5,strtoupper($liquidacion->apellido_paterno.' '.$liquidacion->apellido_materno.' '.$liquidacion->nombre),0,0);
$this->fpdf->Ln();						
$this->fpdf->cell(13,5,'',0,0,'L');  			
$this->fpdf->cell(13,5,'',0,0,'L');  			
$this->fpdf->cell(14,5,'',0,0,'L');
$this->fpdf->cell(80,5,$liquidacion->rutempleado,0,0);
$this->fpdf->Ln();
//--------------------------------------------------------------------------------------------------------------------------------

$fecha = strtotime($liquidacion->fecha);
$this->fpdf->Ln();
$fecha = 'MES '.date("m",$fecha).' DE '.date("Y");
$this->fpdf->cell(27,5,$fecha,0,0,'L');
$this->fpdf->cell(13,5,'',0,0,'L');
$this->fpdf->cell(55,5,'',0,0,'L');
$this->fpdf->cell(30.5,5,'',0,0,'L');
$this->fpdf->cell(40.5,5,'',0,0,'L');
$this->db->where('EXTRACT(MONTH from fecha) = \''.date("m",strtotime($liquidacion->fecha)).'\' AND EXTRACT(YEAR FROM Fecha) = \''.date("Y",strtotime($liquidacion->fecha)).'\'');
$this->fpdf->cell(30.5,5,'UF. '.number_format($this->db->get('uf')->row()->uf,2,',','.'),0,0,'L');
$this->fpdf->Ln();
$this->fpdf->Ln();
//Comienza los calculos
$this->fpdf->cell(27,5,'Dias trabajados: ',0,0,'L');
$this->fpdf->cell(13,5,$liquidacion->dias_trabajados,0,0,'R');
$this->fpdf->cell(55,5,'Sueldo Base',0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->sueldo_base,0,',','.'),0,0,'R');
$this->fpdf->cell(40.5,5,'Fondo de pensiones',0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->fondo_pensiones,0,',','.'),0,0,'R');

$this->fpdf->Ln();
$this->fpdf->cell(27,5,'',0,0,'L');
$this->fpdf->cell(13,5,'',0,0,'L');
$this->fpdf->cell(55,5,'Sueldo Base Proporcional',0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->sueldo_base_proporcional,0,',','.'),0,0,'R');
$this->fpdf->cell(40.5,5,'Cotiz. Seg. Ces.',0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->cotiz_seg_ces,0,',','.'),0,0,'R');


$this->fpdf->Ln();
$this->fpdf->cell(27,5,'Horas. Extras.',0,0,'L');
$this->fpdf->cell(13,5,$liquidacion->cantidad_horas,0,0,'R');
$this->fpdf->cell(55,5,'Hrs. Ext',0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->hrs_extras,0,',','.'),0,0,'R');
$this->fpdf->cell(40.5,5,'Ahorro Prev. Voluntario',0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->ahorro_prev_voluntario,0,',','.'),0,0,'R');


$this->fpdf->Ln();
$this->fpdf->cell(27,5,'',0,0,'L');
$this->fpdf->cell(13,5,'',0,0,'L');
$this->fpdf->cell(55,5,'Comisiones',0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->comisiones,0,',','.'),0,0,'R');
$this->fpdf->cell(40.5,5,'Cotiz. Salud',0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->cotiz_salud,0,',','.'),0,0,'R');

$this->fpdf->Ln();
$this->fpdf->cell(27,5,'',0,0,'L');
$this->fpdf->cell(13,5,'',0,0,'L');
$this->fpdf->cell(55,5,'Bonos',0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->bonos,0,',','.'),0,0,'R');
$this->fpdf->cell(40.5,5,'Adicional de Salud',0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->adicional_salud,0,',','.'),0,0,'R');

$this->fpdf->Ln();
$this->fpdf->cell(27,5,'',0,0,'L');
$this->fpdf->cell(13,5,'',0,0,'L');
$this->fpdf->cell(55,5,'Aguinaldo',0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->aguinaldos,0,',','.'),0,0,'R');
$this->fpdf->cell(40.5,5,'Caja Compensacion',0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->caja_comp,0,',','.'),0,0,'R');


$this->fpdf->Ln();
$this->fpdf->cell(27,5,'',0,0,'L');
$this->fpdf->cell(13,5,'',0,0,'L');
$this->fpdf->cell(55,5,utf8_decode('Gratificación'),0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->gratificaciones,0,',','.'),0,0,'R');
$this->fpdf->cell(40.5,5,'Tot. Leyes Sociales',0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->total_leyes_soc,0,',','.'),0,0,'R');

$this->fpdf->Ln();
$this->fpdf->cell(27,5,'',0,0,'L');
$this->fpdf->cell(13,5,'',0,0,'L');
$this->fpdf->cell(55,5,'',0,0,'L');
$this->fpdf->cell(30.5,5,'',0,0,'L');
$this->fpdf->cell(40.5,5,'RENTA AFECTA',0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->renta_afecta,0,',','.'),0,0,'R');

$this->fpdf->Ln();
$this->fpdf->Ln();
$this->fpdf->cell(27,5,'',0,0,'L');
$this->fpdf->cell(13,5,'',0,0,'L');
$this->fpdf->cell(55,5,'SUB TOTALES HABERES',0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->sub_totales_haberes,0,',','.'),0,0,'R');
$this->fpdf->cell(40.5,5,'Imp.2da Cat',0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->imp_2cat,0,',','.'),0,0,'R');
$this->fpdf->Ln();
$this->fpdf->cell(27,5,'',0,0,'L');
$this->fpdf->cell(13,5,'',0,0,'L');
$this->fpdf->cell(55,5,'TOTAL IMPONIBLE',0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->total_imponible,0,',','.'),0,0,'R');
$this->fpdf->cell(40.5,5,'Cta.AhorroAFP',0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->cta_ahorro_afp,0,',','.'),0,0,'R');
$this->fpdf->Ln();
$this->fpdf->cell(27,5,'',0,0,'L');
$this->fpdf->cell(13,5,'',0,0,'L');
$this->fpdf->cell(55,5,'',0,0,'L');
$this->fpdf->cell(30.5,5,'',0,0,'L');
$this->fpdf->cell(40.5,5,'Anticipos',0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->anticipos,0,',','.'),0,0,'R');
$this->fpdf->Ln();
$this->fpdf->cell(27,5,'',0,0,'L');
$this->fpdf->cell(13,5,'',0,0,'L');
$this->fpdf->cell(55,5,'',0,0,'L');
$this->fpdf->cell(30.5,5,'',0,0,'L');
$this->fpdf->cell(40.5,5,'Prestamos. CCAF',0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->prestamos_ccaf,0,',','.'),0,0,'R');
$this->fpdf->Ln();
$this->fpdf->cell(27,5,'',0,0,'L');
$this->fpdf->cell(13,5,'',0,0,'L');
$this->fpdf->cell(55,5,utf8_decode('Asig. Locomoción'),0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->locomocion,0,',','.'),0,0,'R');
$this->fpdf->cell(40.5,5,'Desctos. Prestamos',0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->descuentos_prestamos,0,',','.'),0,0,'R');
$this->fpdf->Ln();
$this->fpdf->cell(27,5,'',0,0,'L');
$this->fpdf->cell(13,5,'',0,0,'L');
$this->fpdf->cell(55,5,utf8_decode('Asig. Colación'),0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->colacion,0,',','.'),0,0,'R');
$this->fpdf->cell(40.5,5,'Otros Desctos.',0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->otros_descuentos,0,',','.'),0,0,'R');

/* Modificado por victor el 29-09-2014*/

// No aparecia El campo Asignacion Familiar ni el monto correspondiente 
//----------------------------------------------------------------------------------------
$this->fpdf->Ln();
$this->fpdf->cell(27,5,'',0,0,'L');
$this->fpdf->cell(13,5,'',0,0,'L');
$this->fpdf->cell(55,5,utf8_decode('Asignacion Familiar'),0,0,'L');                  
$this->fpdf->cell(30.5,5,number_format($liquidacion->familiar,0,',','.'),0,0,'R');
$this->fpdf->cell(40.5,5,'',0,0,'L');
$this->fpdf->cell(30.5,5,'',0,0,'L');
//----------------------------------------------------------------------------------------

$this->fpdf->Ln();
$this->fpdf->cell(27,5,'',0,0,'L');
$this->fpdf->cell(13,5,'',0,0,'L');
$this->fpdf->cell(55,5,utf8_decode('Otras Asignaciones'),0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->otras_asignaciones,0,',','.'),0,0,'R');
$this->fpdf->cell(40.5,5,'',0,0,'L');
$this->fpdf->cell(30.5,5,'',0,0,'L');
$this->fpdf->Ln();
$this->fpdf->cell(27,5,'',0,0,'L');
$this->fpdf->cell(13,5,'',0,0,'L');
$this->fpdf->cell(55,5,utf8_decode('Indem. Sustit.'),0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->indem_sustit,0,',','.'),0,0,'R');
$this->fpdf->SetFillColor(242,242,242);
$this->fpdf->cell(40.5,5,'TOTAL HABERES',0,0,'L',true);
$this->fpdf->cell(30.5,5,number_format($liquidacion->total_haberes,0,',','.'),0,0,'R',true);
$this->fpdf->Ln();
$this->fpdf->cell(27,5,'',0,0,'L');
$this->fpdf->cell(13,5,'',0,0,'L');
$this->fpdf->cell(55,5,utf8_decode('Indem.Años Ser.'),0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->indem_anos,0,',','.'),0,0,'R');
$this->fpdf->SetFillColor(242,242,242);
$this->fpdf->cell(40.5,5,'TOTAL DESCT.',0,0,'L',true);
$this->fpdf->cell(30.5,5,number_format($liquidacion->total_descuentos,0,',','.'),0,0,'R',true);
$this->fpdf->Ln();
$this->fpdf->cell(27,5,'',0,0,'L');
$this->fpdf->cell(13,5,'',0,0,'L');
$this->fpdf->cell(55,5,utf8_decode('TOTAL NO IMPON.'),0,0,'L');
$this->fpdf->cell(30.5,5,number_format($liquidacion->total_no_imponible,0,',','.'),0,0,'R');
$this->fpdf->SetFillColor(217,217,217);
$this->fpdf->cell(40.5,5,'ALCANCE LIQUIDO',0,0,'L',true);
$this->fpdf->cell(30.5,5,number_format($liquidacion->alcance_liquido,0,',','.'),0,0,'R',true);

//Se imprimen los impuestos
    if($i==1)
    {
        $this->fpdf->Ln();
        $this->fpdf->Ln();
        $this->fpdf->cell(27,5,'',0,0,'L');
        $this->fpdf->cell(13,5,'',0,0,'L');
        $this->fpdf->SetFillColor(217,217,217);
        $this->fpdf->cell(55,5,utf8_decode('TOTAL LEYES SOCIALES'),0,0,'L',true);
        $this->fpdf->cell(30.5,5,number_format($liquidacion->total_leyes_sociales,0,',','.'),0,0,'R',true);
        $this->fpdf->cell(40.5,5,'',0,0,'L');
        $this->fpdf->cell(30.5,5,'',0,0,'L');
        
        $this->fpdf->Ln();
        $this->fpdf->cell(27,5,'',0,0,'L');
        $this->fpdf->cell(13,5,'',0,0,'L');
        $this->fpdf->SetFillColor(217,217,217);
        $this->fpdf->cell(55,5,utf8_decode('TOTALES IMPUESTOS'),0,0,'L',true);
        $this->fpdf->cell(30.5,5,number_format($liquidacion->total_impuestos,0,',','.'),0,0,'R',true);
        $this->fpdf->cell(40.5,5,'',0,0,'L');
        $this->fpdf->cell(30.5,5,'',0,0,'L');
        $this->fpdf->SetFillColor(242,242,242);
        $this->fpdf->Ln();
        $this->fpdf->cell(27,5,'',0,0,'L');
        $this->fpdf->cell(13,5,'',0,0,'L');
        $this->fpdf->cell(55,5,utf8_decode('AFP'),0,0,'L',true);
        $this->fpdf->cell(30.5,5,number_format(0,0,',','.'),0,0,'R',true);
        $this->fpdf->cell(40.5,5,'',0,0,'L');
        $this->fpdf->cell(30.5,5,'',0,0,'L');
        
        $this->fpdf->Ln();
        $this->fpdf->cell(27,5,'',0,0,'L');
        $this->fpdf->cell(13,5,'',0,0,'L');
        $this->fpdf->cell(55,5,utf8_decode('AFP'),0,0,'L',true);
        $this->fpdf->cell(30.5,5,number_format($liquidacion->afp,0,',','.'),0,0,'R',true);
        $this->fpdf->cell(40.5,5,'',0,0,'L');
        $this->fpdf->cell(30.5,5,'',0,0,'L');
        
        $this->fpdf->Ln();
        $this->fpdf->cell(27,5,'',0,0,'L');
        $this->fpdf->cell(13,5,'',0,0,'L');
        $this->fpdf->cell(55,5,utf8_decode('Cotización Obligatoria'),0,0,'L',true);
        $this->fpdf->cell(30.5,5,number_format($liquidacion->cotizacion_obligatoria,0,',','.'),0,0,'R',true);
        $this->fpdf->cell(40.5,5,'',0,0,'L');
        $this->fpdf->cell(30.5,5,'',0,0,'L');
        
        $this->fpdf->Ln();
        $this->fpdf->cell(27,5,'',0,0,'L');
        $this->fpdf->cell(13,5,'',0,0,'L');
        $this->fpdf->cell(55,5,utf8_decode('Seguro Inv. Sobrevivencia (SIS)'),0,0,'L',true);
        $this->fpdf->cell(30.5,5,number_format($liquidacion->seguro_inv,0,',','.'),0,0,'R',true);
        $this->fpdf->cell(40.5,5,'',0,0,'L');
        $this->fpdf->cell(30.5,5,'',0,0,'L');
        
        $this->fpdf->Ln();
        $this->fpdf->cell(27,5,'',0,0,'L');
        $this->fpdf->cell(13,5,'',0,0,'L');
        $this->fpdf->cell(55,5,utf8_decode('Cuenta Ahorro Voluntario'),0,0,'L',true);
        $this->fpdf->cell(30.5,5,number_format($liquidacion->cuenta_ahorro,0,',','.'),0,0,'R',true);
        $this->fpdf->cell(40.5,5,'',0,0,'L');
        $this->fpdf->cell(30.5,5,'',0,0,'L');
        
        $this->fpdf->Ln();
        $this->fpdf->cell(27,5,'',0,0,'L');
        $this->fpdf->cell(13,5,'',0,0,'L');
        $this->fpdf->cell(55,5,utf8_decode('Aporte Indemnización'),0,0,'L',true);
        $this->fpdf->cell(30.5,5,number_format($liquidacion->indemnizacion,0,',','.'),0,0,'R',true);
        $this->fpdf->cell(40.5,5,'',0,0,'L');
        $this->fpdf->cell(30.5,5,'',0,0,'L');
        
        $this->fpdf->Ln();
        $this->fpdf->cell(27,5,'',0,0,'L');
        $this->fpdf->cell(13,5,'',0,0,'L');
        $this->fpdf->cell(55,5,utf8_decode('AFC'),0,0,'L',true);
        $this->fpdf->cell(30.5,5,number_format($liquidacion->afc,0,',','.'),0,0,'R',true);
        $this->fpdf->cell(40.5,5,'',0,0,'L');
        $this->fpdf->cell(30.5,5,'',0,0,'L');
        
        $this->fpdf->Ln();
        $this->fpdf->cell(27,5,'',0,0,'L');
        $this->fpdf->cell(13,5,'',0,0,'L');
        $this->fpdf->cell(55,5,utf8_decode('Seguro Cesantia Trabajador'),0,0,'L',true);
        $this->fpdf->cell(30.5,5,number_format($liquidacion->seguro_cesantia_trabajador,0,',','.'),0,0,'R',true);
        $this->fpdf->cell(40.5,5,'',0,0,'L');
        $this->fpdf->cell(30.5,5,'',0,0,'L');
        
        $this->fpdf->Ln();
        $this->fpdf->cell(27,5,'',0,0,'L');
        $this->fpdf->cell(13,5,'',0,0,'L');
        $this->fpdf->cell(55,5,utf8_decode('Seguro Cesantia Empleador'),0,0,'L',true);
        $this->fpdf->cell(30.5,5,number_format($liquidacion->seguro_cesantia_empleador,0,',','.'),0,0,'R',true);
        $this->fpdf->cell(40.5,5,'',0,0,'L');
        $this->fpdf->cell(30.5,5,'',0,0,'L');
        
        $this->fpdf->Ln();
        $this->fpdf->cell(27,5,'',0,0,'L');
        $this->fpdf->cell(13,5,'',0,0,'L');
        $this->fpdf->cell(55,5,utf8_decode('Mutualidad / INP'),0,0,'L',true);
        $this->fpdf->cell(30.5,5,utf8_decode(''),0,0,'R',true);
        $this->fpdf->cell(40.5,5,'',0,0,'L');
        $this->fpdf->cell(30.5,5,'',0,0,'L');
        
        $this->fpdf->Ln();
        $this->fpdf->cell(27,5,'',0,0,'L');
        $this->fpdf->cell(13,5,'',0,0,'L');
        $this->fpdf->cell(55,5,utf8_decode('Cot. Accidente del Trabajo'),0,0,'L',true);
        $this->fpdf->cell(30.5,5,number_format($liquidacion->cot_accidente_trabajo,0,',','.'),0,0,'R',true);
        $this->fpdf->cell(40.5,5,'',0,0,'L');
        $this->fpdf->cell(30.5,5,'',0,0,'L');
        
        $this->fpdf->Ln();
        $this->fpdf->cell(27,5,'',0,0,'L');
        $this->fpdf->cell(13,5,'',0,0,'L');
        $this->fpdf->cell(55,5,utf8_decode('Total IPS (ex INP)'),0,0,'L',true);
        $this->fpdf->cell(30.5,5,number_format($liquidacion->total_ips,0,',','.'),0,0,'R',true);
        $this->fpdf->cell(40.5,5,'',0,0,'L');
        $this->fpdf->cell(30.5,5,'',0,0,'L');
        
        $this->fpdf->Ln();
        $this->fpdf->cell(27,5,'',0,0,'L');
        $this->fpdf->cell(13,5,'',0,0,'L');
        $this->fpdf->cell(55,5,utf8_decode('SALUD'),0,0,'L',true);
        $this->fpdf->cell(30.5,5,utf8_decode(''),0,0,'R',true);
        $this->fpdf->cell(40.5,5,'',0,0,'L');
        $this->fpdf->cell(30.5,5,'',0,0,'L');
        
        $this->fpdf->Ln();
        $this->fpdf->cell(27,5,'',0,0,'L');
        $this->fpdf->cell(13,5,'',0,0,'L');
        $this->fpdf->cell(55,5,utf8_decode('Cotización Obligatoria'),0,0,'L',true);
        $this->fpdf->cell(30.5,5,number_format($liquidacion->cotizacion_obligatoria_salud ,0,',','.'),0,0,'R',true);
        $this->fpdf->cell(40.5,5,'',0,0,'L');
        $this->fpdf->cell(30.5,5,'',0,0,'L');
        
        $this->fpdf->Ln();
        $this->fpdf->cell(27,5,'',0,0,'L');
        $this->fpdf->cell(13,5,'',0,0,'L');
        $this->fpdf->cell(55,5,utf8_decode('Cotización Adicional'),0,0,'L',true);
        $this->fpdf->cell(30.5,5,number_format($liquidacion->cotizacion_adicional,0,',','.'),0,0,'R',true);
        $this->fpdf->cell(40.5,5,'',0,0,'L');
        $this->fpdf->cell(30.5,5,'',0,0,'L');
        
        $this->fpdf->Ln();
        $this->fpdf->cell(27,5,'',0,0,'L');
        $this->fpdf->cell(13,5,'',0,0,'L');
        $this->fpdf->cell(55,5,utf8_decode('Total FONASA'),0,0,'L',true);
        $this->fpdf->cell(30.5,5,number_format($liquidacion->total_fonasa,0,',','.'),0,0,'R',true);
        $this->fpdf->cell(40.5,5,'',0,0,'L');
        $this->fpdf->cell(30.5,5,'',0,0,'L');
		
		$this->fpdf->Ln();
        $this->fpdf->cell(27,5,'',0,0,'L');
        $this->fpdf->cell(13,5,'',0,0,'L');
        $this->fpdf->cell(55,5,utf8_decode('Caja de Compensacion'),0,0,'L',true);
        $this->fpdf->cell(30.5,5,number_format($liquidacion->caja_comp,0,',','.'),0,0,'R',true);
        $this->fpdf->cell(40.5,5,'',0,0,'L');
        $this->fpdf->cell(30.5,5,'',0,0,'L');
        
        $this->fpdf->Ln();
        $this->fpdf->cell(27,5,'',0,0,'L');
        $this->fpdf->cell(13,5,'',0,0,'L');
        $this->fpdf->cell(55,5,utf8_decode('IMPUESTO TRABAJADOR'),0,0,'L',true);
        $this->fpdf->cell(30.5,5,number_format($liquidacion->impuesto_trabajador,0,',','.'),0,0,'R',true);
        $this->fpdf->cell(40.5,5,'',0,0,'L');
        $this->fpdf->cell(30.5,5,'',0,0,'L');
        
        $this->fpdf->Ln();
        $this->fpdf->Ln();
        $this->fpdf->cell(27,5,'',0,0,'L');
        $this->fpdf->cell(13,5,'',0,0,'L');
        $this->fpdf->SetFillColor(217,217,217);
        $this->fpdf->cell(55,5,utf8_decode('COSTO TOTAL EMPLEADOR'),0,0,'L',true);
        $this->fpdf->cell(30.5,5,number_format($liquidacion->costo_total_empleador,0,',','.'),0,0,'R',true);
        $this->fpdf->cell(40.5,5,'',0,0,'L');
        $this->fpdf->cell(30.5,5,'',0,0,'L');
   	
	
	
 }
	
}

$this->fpdf->Output();
?>