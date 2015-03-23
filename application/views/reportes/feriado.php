<?php 
/* Pagina 1 y 2*/
$pdf = $this->fpdf;
$pdf->AddPage('L');
/*Cuadro superior*/
$pdf->SetFont('Arial','B',15);
$pdf->SetFillColor(242,242,242);
$pdf->cell(0,7.5,'COMPROBANTE DE FERIADO',0,0,'C',true);
$pdf->SetFont('Arial','',12);
$pdf->Ln();
$pdf->Ln();
$pdf->cell(0,6,'En  cumplimiento   a  las  disposiciones  legales   vigentes   se  deja  constancia   que  a  contar  del');
$pdf->Ln();
$pdf->cell(110,6,date("d/m/Y",strtotime($feriado->fecha)),1,0,'C');
$pdf->cell(57,6,'y hasta el',0,0,'C');
$pdf->cell(110,6,date("d/m/Y",strtotime($feriado->fecha_final)),1,0,'C');
$pdf->Ln();
$pdf->cell(57,6,'el Trabajador Sr.(a): ',0,0,'L');
$pdf->cell(220,6,$feriado->empapellido.' '.$feriado->empnombre,1,0,'L');
$pdf->Ln();
$pdf->cell(57,6,utf8_decode('Cedula de identidad Nº'),0,0,'L');
$pdf->cell(60,6,$feriado->emprif,1,0,'L');
$pdf->cell(60,6,utf8_decode(', hará  uso  de  su  feriado  anual  que  corresponde  al'),0,0,'L');
$pdf->Ln();
$pdf->cell(57,6,utf8_decode('periodo del año'),0,0,'L');
$pdf->cell(60,6,utf8_decode($feriado->periodo),1,0,'L');
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(0,6,utf8_decode('Se  deja  constancia  que la  remuneración correspondiente  al periodo  de feriado  será incluida  en la respectiva liquidación del mes.'),0,0,'L');

$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->cell(57,6,utf8_decode('Resumen'),0,0,'L');
$pdf->cell(57,6,utf8_decode('Dias hábiles'),0,0,'L');
$pdf->Ln();
$pdf->cell(57,6,utf8_decode('Feriado legal normal'),0,0,'L');
$pdf->cell(6,6,utf8_decode("+"),0,0,'L');
$pdf->cell(10,6,utf8_decode($feriado->dias_legal),1,0,'C');

$pdf->Ln();
$pdf->cell(57,6,utf8_decode('Feriado progresivo'),0,0,'L');
$pdf->cell(6,6,utf8_decode("+"),0,0,'L');
$pdf->cell(10,6,utf8_decode($feriado->dias_progresivo),1,0,'C');

$pdf->Ln();
$pdf->cell(57,6,utf8_decode('Total feriado'),0,0,'L');
$pdf->cell(6,6,utf8_decode("+"),0,0,'L');
$pdf->cell(10,6,utf8_decode($feriado->total),1,0,'C');

$pdf->Ln();
$pdf->cell(57,6,utf8_decode('Dias otorgados'),0,0,'L');
$pdf->cell(6,6,utf8_decode("+"),0,0,'L');
$pdf->cell(10,6,utf8_decode($feriado->dias_otorgados),1,0,'C');

$pdf->Ln();
$pdf->Ln();
$pdf->cell(57,6,utf8_decode('Saldo Pendiente'),0,0,'L');
$pdf->cell(6,6,utf8_decode("+"),0,0,'L');
$pdf->cell(10,6,utf8_decode($feriado->pendientes),1,0,'C');
$pdf->cell(77,6,'',0,0,'L');
$pdf->SetFont('Arial','',12);
$pdf->cell(57,6,utf8_decode('En santiago a'),0,0,'L');
$pdf->cell(57,6,utf8_decode(date("d",strtotime($feriado->fecha_emision))." de ".meses(date("m",strtotime($feriado->fecha_emision)))." de ".date("Y",strtotime($feriado->fecha_emision))),0,0,'L');

$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->cell(120,6,utf8_decode('Firma y Timbre del Empleador'),0,0,'C');
$pdf->cell(120,6,utf8_decode('Firma del Trabajador'),0,0,'C');
$pdf->Ln();
$pdf->cell(120,6,utf8_decode($feriado->empresanombre),0,0,'C');
$pdf->cell(120,6,utf8_decode($feriado->empapellido.' '.$feriado->empnombre),0,0,'C');
$pdf->Ln();
$pdf->cell(120,6,utf8_decode($feriado->empresarut),0,0,'C');
$pdf->cell(120,6,utf8_decode($feriado->emprif),0,0,'C');
if(!empty($feriado->REPRESENTANTE_LEGAL) && !empty($feriado->rut_legal)){
$pdf->Ln();
$pdf->cell(120,6,utf8_decode($feriado->REPRESENTANTE_LEGAL),0,0,'C');
$pdf->Ln();
$pdf->cell(120,6,utf8_decode($feriado->rut_legal),0,0,'C');
}
$pdf->line(10,155,125,155);
$pdf->line(135,155,255,155);

//Segunda hoja
$pdf->AddPage('L');
$pdf->SetFont('Arial','B',15);
$pdf->SetFillColor(242,242,242);
$pdf->cell(0,7.5,'BASE DE CALCULOS SALDO FERIADOS '.date("d/m/Y",strtotime($feriado->fecha)).'',0,0,'C',true);
$pdf->SetFont('Arial','',12);
$pdf->Ln();
$pdf->Ln();
$pdf->cell(100,7.5,'Saldo Inicial: ');
$pdf->cell(50,7.5,$feriado->dias_antes_ingreso,1,0,'R');
$pdf->Ln();
$pdf->cell(100,7.5,'Dias Feriados desde el ingreso al dia '.date("d/m/Y",strtotime($feriado->fecha)).': ');
$pdf->cell(50,7.5,$feriado->dias_legal,1,0,'R');
$pdf->Ln();
$pdf->cell(100,7.5,'Vitacora de dias solicitados: ');

$this->db->select(
        'feriado.*, trabajadores.nombre as empnombre, trabajadores.dias_antes_ingreso, trabajadores.apellido_paterno as empapellido, trabajadores.rut as emprif,
     dbclientes112010.nickname as empresanombre, dbclientes112010.rut as empresarut, dbclientes112010.rut_legal, dbclientes112010.REPRESENTANTE_LEGAL');
$this->db->join('trabajadores', 'trabajadores.id = feriado.empleado');
$this->db->join('dbclientes112010', 'dbclientes112010.id2 = feriado.empresa');
$x = 0;
foreach($this->db->get_where('feriado', array('feriado.empleado' => $feriado->empleado))->result() as $v)
    $x+= $v->dias_otorgados;
$pdf->cell(50,7.5,$x,1,0,'R');
$pdf->Ln();
$pdf->cell(100,7.5,'Total saldo dias feriados al dia de '.date("d/m/Y",strtotime($feriado->fecha)).': ');
$pdf->cell(50,7.5,$feriado->dias_legal-$x,1,0,'R');
$this->fpdf->Output();
?>