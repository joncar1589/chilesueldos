<?php 
//print_r($finiquito->row());
class PDF extends FPDF
{
    // Cabecera de página
    function __construct($finiquito)
    {
        parent::__construct();
        $this->finiquito = $finiquito;
    }
    function Header()
    {
        $this->SetFont('Arial','',9);
        $this->cell(190,4.5,$this->finiquito->row()->empresa_nombre,0,2,'L');
        $this->cell(190,4.5,'RUT: '.$this->finiquito->row()->empresa_rut,0,0,'L');
    }
}
setlocale(LC_ALL,"es_ES");
$pdf = new pdf($finiquito);
$pdf->AddPage();
/*Cuadro superior*/
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',13);
$pdf->SetFillColor(242,242,242);
$pdf->cell(190,6.5,'FINIQUITO DE TRABAJO',0,0,'C');
$pdf->SetFont('Arial','',9);
$pdf->Ln();
$pdf->Ln();
$pdf->MultiCell(190,4.5,utf8_decode('En '.$finiquito->row()->ciudad_empresa.' a '.strftime("%d de %B del %Y",strtotime($finiquito->row()->fecha_emision)).' entre  '.$finiquito->row()->empresa_nombre.', R.U.T: '.$finiquito->row()->empresa_rut.', con domicilio en '.$finiquito->row()->empresa_direccion.', comuna de '.$finiquito->row()->empresa_comuna.', y Don(a) '.$finiquito->row()->empleado_nombre.' '.$finiquito->row()->empleado_apellido.', cédula nacional de identidad N° '.$finiquito->row()->empleado_rut.', se acuerda lo siguiente:'));
$pdf->Ln();
if($finiquito->row()->inciso != 0 || !empty($finiquito->row()->inciso)){
$pdf->MultiCell(190,4.5,utf8_decode('PRIMERO: Don(a) '.$finiquito->row()->empleado_nombre.' '.$finiquito->row()->empleado_apellido.', declara haber prestado servicios a '.$finiquito->row()->empresa_nombre.', desde el  '.strftime("%d de %B del %Y",strtotime($finiquito->row()->empleado_fechai)).'  al  '.strftime("%d de %B del %Y",strtotime($finiquito->row()->fecha)).', fecha esta última de término de sus servicios por la siguiente causa, conforme al artículo '.$finiquito->row()->articulo.', inciso N° '.$finiquito->row()->inciso.', D.F.L. N° 1 del Código del trabajo, esto es, "'.$finiquito->row()->causal.'". '));
}else{
    $pdf->MultiCell(190,4.5,utf8_decode('PRIMERO: Don(a) '.$finiquito->row()->empleado_nombre.' '.$finiquito->row()->empleado_apellido.', declara haber prestado servicios a '.$finiquito->row()->empresa_nombre.', desde el  '.strftime("%d de %B del %Y",strtotime($finiquito->row()->empleado_fechai)).'  al  '.strftime("%d de %B del %Y",strtotime($finiquito->row()->fecha)).', fecha esta última de término de sus servicios por la siguiente causa, conforme al artículo '.$finiquito->row()->articulo.', D.F.L. N° 1 del Código del trabajo, esto es, "'.$finiquito->row()->causal.'". '));
}
$pdf->Ln();
$pdf->MultiCell(190,4.5,utf8_decode('SEGUNDO: Don(a) '.$finiquito->row()->empleado_nombre.' '.$finiquito->row()->empleado_apellido.', declara recibir en este acto y a su entera satisfacción, de parte de '.$finiquito->row()->empresa_nombre.' ,las sumas que a continuación se indican, por los siguientes conceptos: '));

$pdf->Ln();
$pdf->Ln();
$x = 1;
$suma = 0;
foreach($finiquito->result() as $f){
    $pdf->cell(20,4.5,'',0,0);
    $format = '';
    switch($f->descripcion)
    {
        case 'REMUNERACION DEL MES':
            $format = 'de %B %Y';
        break;
        case 'VACACIONES PROPORCIONALES PERIODO':
            $format = '%Y';
        break;
    }
    $f->ano = strftime($format,strtotime(str_replace("/","-",$f->ano)));
    if($f->descripcion=='IMDEMNISACION POR AÑOS DE SERVICIOS' || $f->descripcion == 'INDEMNIZACION POR AÑOS DE SERVICIOS')
        $descripcion = utf8_decode ($x.'. '.$f->descripcion.' '.$f->ano.' ('.$f->dias.' años) ');
    elseif($f->descripcion=='IMDEMNIZACION VOLUNTARIA' || $f->descripcion=='IMDEMNIZACION POR MES DE AVISO')
        $descripcion = $x.'. '.$f->descripcion;
    elseif($f->descripcion=='REMUNERACION DEL MES' || $f->descripcion=='VACACIONES PROPORCIONALES PERIODO')
        $descripcion = $x.'. '.$f->descripcion.' '.$f->ano.' ('.$f->dias.' dias) ';
    else
        $descripcion = utf8_decode ($x.'. '.$f->descripcion);
    $pdf->cell(130,4.5,$descripcion,0,'L');
    $pdf->cell(20,4.5,'$ '.round($f->monto,0),0,0);
    $pdf->Ln();
    $suma+= $f->monto;
    $x++;
}
$pdf->Ln();
$pdf->SetFont('Arial','B',9);
$pdf->cell(20,4.5,'TOTAL',0,0);
$pdf->SetFont('Arial','',9);
$pdf->cell(130,4.5,'',0,'L');
$pdf->cell(20,4.5,'$ '.round($suma,0),0,0);
$pdf->Ln();
$pdf->cell(190,4.5,'Son: '.utf8_decode($this->enletras->ValorEnLetras($suma,'Pesos')),0,0,'L');
$pdf->Ln();
$pdf->Ln();
$pdf->MultiCell(190,4.5,utf8_decode('TERCERO: Don(a) '.$finiquito->row()->empleado_nombre.' '.$finiquito->row()->empleado_apellido.', deja constancia que durante el tiempo que prestó servicios a '.$finiquito->row()->empresa_nombre.', recibió de esta, correcta y oportunamente el total de las remuneraciones convenidas, de acuerdo con su contrato de trabajo, clase de trabajo ejecutado, y que nada se le adeuda por los conceptos antes indicados, ni por ningún otro, sea de origen legal o contractual derivado de la prestación de sus servicios y motivo por el cual, no teniendo reclamo alguno que formular en contra  '.$finiquito->row()->empresa_nombre.', le otorga el más amplio y total finiquito, declaración que formula libre y espontáneamente, en perfecto y cabal conocimiento de cada uno y de todos sus derechos. 
Para constancia firman las partes el presente finiquito en tres ejemplares, quedando uno de ellos en poder de cada parte y el tercero ante la inspección del trabajo. '));
$pdf->Ln();
$pdf->MultiCell(190,4.5,utf8_decode('CUARTO: A la fecha se encuentran todas las leyes sociales debidamente canceladas. '));

$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->cell(95,6,utf8_decode('_________________________________________________'),0,0,'C');
$pdf->cell(95,6,utf8_decode('_________________________________________________'),0,0,'C');
$pdf->Ln();
$pdf->cell(95,6,utf8_decode($finiquito->row()->empleado_nombre.' '.$finiquito->row()->empleado_apellido),0,0,'C');
$pdf->cell(95,6,utf8_decode($finiquito->row()->empresa_nombre),0,0,'C');
$pdf->Ln();
$pdf->cell(95,6,utf8_decode($finiquito->row()->empleado_rut),0,0,'C');
$pdf->cell(95,6,utf8_decode($finiquito->row()->empresa_rut),0,0,'C');
if(!empty($finiquito->row()->REPRESENTANTE_LEGAL) && !empty($finiquito->row()->rut_legal)){
$pdf->Ln();
$pdf->cell(290,6,utf8_decode($finiquito->row()->REPRESENTANTE_LEGAL),0,0,'C');
$pdf->Ln();
$pdf->cell(285,6,utf8_decode($finiquito->row()->rut_legal),0,0,'C');
}
$pdf->Output();
?>
