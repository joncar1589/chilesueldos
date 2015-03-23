<?php 

class PDF extends FPDF
{
    // Cabecera de página
    function __construct($trabajador)
    {
        parent::__construct();
        $this->finiquito = $trabajador;
    }
    function Header()
    {
        $this->SetFont('Arial','',9);
        $this->cell(190,4.5,$this->finiquito->row()->empresa_nombre,0,2,'L');
        $this->cell(190,4.5,'RUT: '.$this->finiquito->row()->empresa_rut,0,0,'L');
        $this->Ln();
        $this->Ln();
    }
}
$this->db->dbprefix = 'sueldos_';
setlocale(LC_ALL,"es_ES");
$pdf = new pdf($trabajador);
$pdf->AddPage();
$lineas = false;
/*Cuadro superior*/
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',13);
$pdf->SetFillColor(242,242,242);
$pdf->cell(190,6.5,'CONTRATO DE TRABAJO',0,0,'C');
$pdf->SetFont('Arial','',9);
$pdf->Ln();
$pdf->Ln();
$t = $trabajador->row();
$numero = !empty($t->casa)?' Numero '.$t->casa:'';
if ($t->empresa_tipo == 0) {
    $pdf->Write(4.5, utf8_decode('En ' . $t->ciudad . ', a ' . utf8_encode(strftime("%d de %B del %Y", strtotime($t->fecha_emision))) . ', entre Don(a) ' . $t->empresa_nombre . ' , R.U.T. ' . $t->empresa_rut . ', con domicilio en ' . $t->empresa_direccion . $numero . ', comuna de ' . $t->empresa_comuna . ', en adelante el EMPLEADOR, y Don(a) ' . $t->empleado_nombre . ' ' . $t->empleado_apellido . ' ' . $t->empleado_apellido2 . ', de nacionalidad ' . $t->empleado_nacionalidad . ', cédula de identidad No ' . $t->empleado_rut . ', nacido el ' . strftime("%d de %B del %Y", strtotime($t->empleado_fecha_nacimiento)) . ', con domicilio en ' . $t->empleado_direccion . ', comuna de ' . $t->empleado_comuna . ', en adelante el TRABAJADOR, se ha convenido el siguiente Contrato de Trabajo:'));
} else {
    $pdf->Write(4.5, utf8_decode('En ' . $t->ciudad . ', a ' . utf8_encode(strftime("%d de %B del %Y", strtotime($t->fecha_emision))) . ', entre la empresa ' . $t->empresa_nombre . ' , R.U.T. ' . $t->empresa_rut . ', representada por Don(a) ' . $t->REPRESENTANTE_LEGAL . ', cédula nacional de identidad No ' . $t->rut_legal . ', ambos con domicilio en ' . $t->empresa_direccion .$numero. ', comuna de ' . $t->empresa_comuna . ', en adelante el EMPLEADOR, y Don(a) ' . $t->empleado_nombre . ' ' . $t->empleado_apellido . ', de nacionalidad ' . $t->empleado_nacionalidad . ', cédula de identidad No ' . $t->empleado_rut . ', nacido el ' . strftime(" %d de %B del %Y", strtotime($t->empleado_fecha_nacimiento)) . ', con domicilio en ' . $t->empleado_direccion . ', comuna de ' . $t->empleado_comuna . ', en adelante el TRABAJADOR, se ha convenido el siguiente Contrato de Trabajo:'));
}

$pdf->Ln(2);
$extra1= $t->bono_locomocion>0?$t->bono_locomocion.' ('.$this->enletras->ValorEnLetras($t->bono_locomocion,'Pesos').')':'';
$extra2= $t->bono_colacion>0?$t->bono_colacion.' ('.$this->enletras->ValorEnLetras($t->bono_colacion,'Pesos').')':'';
$extra3= $t->gratificacion>0?$t->gratificacion.' ('.$this->enletras->ValorEnLetras($t->gratificacion,'Pesos').')':'';
$extra4= !empty($t->acuerdo_extra)?$t->acuerdo_extra:'';

$extra = !empty($extra3)?', más un gratificación del 25% de su remuneración con tope de 4,75 ingresos mínimos mensuales según artículo 50 del código del trabajo. ':'';    
$extra.= !empty($extra1) || !empty($extra2)?'Además de una asignación por locomoción de ':'';
$extra.= !empty($extra1)?$extra1:'';
$extra.= !empty($extra) && !empty($extra2)?'y asignación por colación de ':'';
$extra.= !empty($extra2)?$extra2:'';


//$extra.= !empty($extra3)?$extra3:'';

$extra.= !empty($extra) && !empty($extra4)?', ':'';
$extra.= !empty($extra4)?$extra4:'';


$horario_especial = '';
$h = $this->db->get_where('horarios_especiales',array('trabajador'=>$this->input->post('id')));
foreach($h->result() as $ho)
    $horario_especial.= $ho->dia.' de '.$ho->de.' hasta '.$ho->hasta.' Horas, ';
if($h->num_rows>0)$horario_especial = 'y '.$horario_especial;


$pdf->Ln();
$pdf->Write( 4.5,utf8_decode('PRIMERO: El Trabajador se compromete y obliga a desempeñar las labores de '.$t->empleado_cargo.' que se le encomienda. '.$t->empleado_cargo_descripcion.'.

SEGUNDO: Los servicios se prestarán en '.$t->empresa_direccion.$numero.', comuna de '.$t->empresa_comuna.', sin perjuicio de la facultad del empleador de alterar, por causa justificada, la naturaleza de los servicios, o el sitio o recinto en que ellos han de prestarse, con la sola limitación de que se trate de labores similares y que el nuevo sitio o recinto quede dentro de la misma localidad o ciudad, conforme a lo señalado en el artículo 12 del Código del Trabajo.

'));

if($t->tipo_horario == "Normal"){
$pdf->Write( 4.5,utf8_decode('TERCERO: La jornada de trabajo ordinaria no excederá de las '.$t->horas_semanales.' horas semanales, y se efectuará de '.$t->dias_semana.' de '.$t->hora_entrada.' a '.$t->hora_salida.' horas. '.$horario_especial.$t->otros_acuerdos_horarios.''));
}else if($t->tipo_horario == "Sin horario fijo"){
   $pdf->Write( 4.5,utf8_decode('TERCERO: Por la naturaleza de sus funciones EL TRABAJADOR estará exento de cumplimiento de horario en base al artículo 22 del Código del Trabajo.

')); 
}else{
    $pdf->Write( 4.5,utf8_decode('TERCERO: La jornada de trabajo ordinaria no excederá de las '.$t->horas_semanales.' horas semanales, y se efectuará de '.$t->dias_semana.' de '.$t->hora_entrada.' a '.$t->hora_salida.' horas. '.$horario_especial.$t->otros_acuerdos_horarios.''));
}
$pdf->Ln();
if($t->empleado_casa==1){
if($t->gratificacion>0)$extra = 'más una gratificación del 25% de su remuneración con tope de 4,75 ingresos mínimos mensuales según artículo 50 del código del trabajo. '.$extra;
$pdf->Write( 4.5,utf8_decode('CUARTO: El empleador se compromete a remunerar al trabajador con un sueldo base equivalente a '.$t->sueldo_base.' ('.$this->enletras->ValorEnLetras($t->sueldo_base,'Pesos').'), más una gratificación del 25% de su remuneración con tope de 4,75 ingresos mínimos mensuales según artículo 50 del código del trabajo. '.$extra.'. 

Las deducciones que la Empleadora deberá según los casos practicar a las remuneraciones, son todas aquéllas que dispone el artículo 58 del Código del Trabajo.

El empleador se compromete, a depositar mensualmente, en la A.F.P. que el trabajador elija un 4,11% de la remuneración mensual imponible de éste, por el tiempo de duración del contrato, plazo que no podrá exceder de 11 años, a contar de la fecha de inicio de la relación laboral, para el financiamiento de la indemnización a todo evento, por término de contrato dispuesto en el art. 163 del Código del Trabajo.

'));    
}
else{
$pdf->Write( 4.5,utf8_decode('
CUARTO: El empleador se compromete a remunerar al trabajador con un sueldo base equivalente a '.$t->sueldo_base.' ('.$this->enletras->ValorEnLetras($t->sueldo_base,'Pesos').')'.$extra.'. 

El empleador efectuará los pagos previsionales autorizados por la ley.

'));       
}

if($t->empleado_casa==1)
$pdf->Write( 4.5,utf8_decode('QUINTO: El trabajador se compromete y obliga expresamente a cumplir las instrucciones que le sean impartidas por su jefe inmediato o su grupo familiar, en relacion a su trabajo. 
'));
else
$pdf->Write( 4.5,utf8_decode('QUINTO: El trabajador se compromete y obliga expresamente a cumplir las instrucciones que le sean impartidas por su jefe inmediato o por la gerencia de la empresa, en relación a su trabajo. 
'));

$prohibiciones = $this->db->get_where('prohibiciones',array('trabajador'=>$this->input->post('id')));
if($prohibiciones->num_rows>0 && !empty($prohibiciones->row()->nombre)){
$pdf->Ln();
$pdf->Write( 4.5,utf8_decode('Serán prohibiciones específicas del Trabajador las siguientes:'));
$pdf->Ln();
foreach($prohibiciones->result() as $p){
    $pdf->Write(3,utf8_decode('-. '));
    $pdf->MultiCell(0,3,utf8_decode($p->nombre),0,'L', FALSE);
    $pdf->Ln();
}
$lineas = true;
}

$obligaciones = $this->db->get_where('obligaciones',array('trabajador'=>$this->input->post('id')));

if($obligaciones->num_rows>0 && !empty($obligaciones->row()->nombre)){
$pdf->Ln();
$pdf->Cell(0,4.5,utf8_decode('Serán obligaciones específicas del Trabajador las siguientes:'),0,0,'L');
$pdf->Ln();
foreach($obligaciones->result() as $p){
    $pdf->Write(3,utf8_decode('-. '));
    $pdf->MultiCell(0,3,utf8_decode($p->nombre),0,'L', FALSE);
    $pdf->Ln();
    
 }
$pdf->Ln(4.5);
$lineas = true;
}
$exp = $t->tipo_contrato=='FIJO'?strftime(" %d de %B del %Y",strtotime($t->termino_contrato)):'INDEFINIDO';
$pdf->Ln();
$pdf->Write( 4.5,utf8_decode('SEXTO: Los atrasos reiterados, la no concurrencia del trabajador a sus labores sin causa justificada durante dos días seguidos, dos lunes en el mes o un total de tres días durante igual periodo de tiempo; asimismo, la falta injustificada, o sin aviso previo de parte del trabajador, constituyen un incumplimiento grave a las obligaciones que impone el contrato.'));
$pdf->Ln();
$pdf->Ln();
$pdf->Write( 4.5,utf8_decode('SEPTIMO: Se prohíbe al trabajador ejecutar servicios dentro del giro del empleador en el horario estipulado en el contrato.'));
$pdf->Ln();
$pdf->Ln();
$pdf->Write( 4.5,utf8_decode('OCTAVO: El trabajo en horario extraordinario se efectuará solo a petición del empleador y constará por autorización escrita de este.'));
$pdf->Ln();
$pdf->Ln();
$pdf->Write( 4.5,utf8_decode('NOVENO: El trabajador declara cotizar en '.$t->afp_afiliado.' y que el aporte para salud se deposite en '.$t->isapre));

if($t->tipo_contrato=='POR OBRA'){
$pdf->Ln();
$pdf->Ln();
$pdf->Write( 4.5,utf8_decode('DECIMO: El presente contrato será por término de Obra. '.$t->descripcion_tipo_contrato));
}
else{
$pdf->Ln();
$pdf->Ln();
$pdf->Write( 4.5,utf8_decode('DECIMO: El presente contrato será a plazo '.$exp.', sin embargo podrá ponérsele término cuando concurran para ello causas justificadas en conformidad a las leyes vigentes sobre la materia.'));    
}
$pdf->Ln();
$pdf->Ln();
$pdf->Write( 4.5,utf8_decode('DECIMO PRIMERO: Se deja constancia que el Trabajador, Don(a) '.$t->empleado_nombre.' '.$t->empleado_apellido.' '.$t->empleado_apellido2.', ingresó al servicio el día '.strftime(" %d de %B del %Y",strtotime($t->fecha_ingreso)).'.'));
$pdf->Ln();
$pdf->Ln();
$pdf->Write( 4.5,utf8_decode('DECIMO SEGUNDO: Para todos los efectos derivados del presente contrato las partes fijan domicilio en la ciudad de '.$t->ciudad.', y se someten a la Jurisdicción de sus Tribunales.'));
if($lineas == true){
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
    
}else{
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
}
$pdf->Write( 4.5,utf8_decode('El presente contrato se firma en dos ejemplares del mismo tenor dejando expresa constancia que en este acto el trabajador recibe uno de ellos. Se entienden incorporadas al presente contrato todas las disposiciones legales que se dicten con posterioridad a la fecha de suscripción a que tenga relación con él.'));
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
if($t->empresa_tipo==0)
{
    $pdf->cell(95,6,utf8_decode('_________________________________________________'),0,0,'C');
    $pdf->cell(95,6,utf8_decode('_________________________________________________'),0,0,'C');
    $pdf->Ln();
    $pdf->cell(95,6,utf8_decode($trabajador->row()->empresa_nombre),0,0,'C');
    $pdf->cell(95,6,utf8_decode($trabajador->row()->empleado_nombre.' '.$trabajador->row()->empleado_apellido.' '.$t->empleado_apellido2),0,0,'C');
    $pdf->Ln();
    $pdf->cell(95,6,utf8_decode($trabajador->row()->empresa_rut),0,0,'C');
    $pdf->cell(95,6,utf8_decode($trabajador->row()->empleado_rut),0,0,'C');
    $pdf->Ln();
    $pdf->cell(95,6,utf8_decode('EMPLEADOR'),0,0,'C');
    $pdf->cell(95,6,utf8_decode('TRABAJADOR'),0,0,'C');
}
else
{
    $pdf->cell(95,6,utf8_decode('_________________________________________________'),0,0,'C');
    $pdf->cell(95,6,utf8_decode('_________________________________________________'),0,0,'C');
    $pdf->Ln();
    $pdf->cell(95,6,utf8_decode($trabajador->row()->empresa_nombre),0,0,'C');
    $pdf->cell(95,6,utf8_decode($trabajador->row()->empleado_nombre.' '.$trabajador->row()->empleado_apellido.' '.$t->empleado_apellido2),0,0,'C');
    $pdf->Ln();
    $pdf->cell(95,6,utf8_decode($trabajador->row()->empresa_rut),0,0,'C');
    $pdf->cell(95,6,utf8_decode($trabajador->row()->empleado_rut),0,0,'C');
    $pdf->Ln();
    $pdf->cell(95,6,utf8_decode('Representante Legal'),0,0,'C');
    $pdf->cell(95,6,utf8_decode('TRABAJADOR'),0,0,'C');
    $pdf->Ln();
    $pdf->cell(95,6,utf8_decode($t->REPRESENTANTE_LEGAL),0,0,'C');
    $pdf->cell(95,6,utf8_decode(''),0,0,'C');
    $pdf->Ln();
    $pdf->cell(95,6,utf8_decode($t->rut_legal),0,0,'C');
    $pdf->cell(95,6,utf8_decode(''),0,0,'C');
    $pdf->Ln();
    $pdf->cell(95,6,utf8_decode('EMPLEADOR'),0,0,'C');
    $pdf->cell(95,6,utf8_decode(''),0,0,'C');
}
$pdf->Output();
?>