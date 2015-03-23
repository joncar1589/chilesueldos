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
setlocale(LC_ALL,"es_ES");
$pdf = new pdf($trabajador);
$pdf->AddPage();
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
if($t->empresa_tipo==0)
$pdf->Write( 4.5,utf8_decode('En '.$t->ciudad.', a '.utf8_encode(strftime("%d de %B del %Y",strtotime($t->fecha_emision_anexo))).', entre Don(a) '.$t->empresa_nombre.' , R.U.T. '.$t->empresa_rut.', con domicilio en '.$t->empresa_direccion.', comuna de '.$t->empresa_comuna.', en adelante el EMPLEADOR, y Don(a) '.$t->empleado_nombre.' '.$t->empleado_apellido.' '.$t->empleado_apellido2.', de nacionalidad '.$t->empleado_nacionalidad.', cédula de identidad No '.$t->empleado_rut.', nacido el '.strftime("%d de %B del %Y",strtotime($t->empleado_fecha_nacimiento)).', con domicilio en calle '.$t->empleado_direccion.', comuna de '.$t->empleado_comuna.', en adelante el TRABAJADOR, se deja constancia de la siguiente actualización de contrato laboral:'));
else
$pdf->Write( 4.5,utf8_decode('En '.$t->ciudad.', a '.utf8_encode(strftime("%d de %B del %Y",strtotime($t->fecha_emision_anexo))).', entre la empresa '.$t->empresa_nombre.' , R.U.T. '.$t->empresa_rut.', representada por Don(ña) '.$t->representante_legal.', cédula nacional de identidad No '.$t->rut_legal.', ambos con domicilio en '.$t->empresa_direccion.', comuna de '.$t->empresa_comuna.', en adelante el EMPLEADOR, y Don(ña) '.$t->empleado_nombre.' '.$t->empleado_apellido.', de nacionalidad '.$t->empleado_nacionalidad.', cédula de identidad No '.$t->empleado_rut.', nacido el '.strftime(" %d de %B del %Y",strtotime($t->empleado_fecha_nacimiento)).', con domicilio en calle '.$t->empleado_direccion.', comuna de '.$t->empleado_comuna.', en adelante el TRABAJADOR, se deja constancia de la siguiente actualización de contrato laboral:'));
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',9);
$pdf->write(4.5,utf8_decode('Modificándose de la siguiente manera:'));
$pdf->SetFont('Arial','',9);
$pdf->Ln();
$pdf->Ln();
$pdf->Write( 4.5,utf8_decode($t->articulo.": ".$t->contenido));
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
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
    $pdf->cell(95,6,utf8_decode($t->representante_legal),0,0,'C');
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