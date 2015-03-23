<?php

class PDF extends PDF_Rotate {

    function Header() {
        //Put the watermark
        $this->SetFont('Arial', 'B', 50);
        $this->SetTextColor(255, 192, 203);
        $this->RotatedText(35, 190, 'Liquidacion de muestra', 45);
    }

    function RotatedText($x, $y, $txt, $angle) {
        //Text rotated around its origin
        $this->Rotate($angle, $x, $y);
        $this->Text($x, $y, $txt);
        $this->Rotate(0);
    }

}

/* Pagina 1 y 2 */
if ($liquidacion->pagado == 1)
    $this->fpdf = new PDF;
for ($i = 0; $i < 2; $i++) {
    $this->fpdf->AddPage();
    /* Cuadro superior */
    $this->fpdf->SetFont('Arial', 'B', 15);
    $this->fpdf->SetFillColor(242, 242, 242);
    $this->fpdf->cell(190, 12, 'LIQUIDACION DE SUELDO', 0, 0, 'C', true);
    $this->fpdf->Ln();
    $this->fpdf->cell(190, 3, '', 0, 0, 'C', false);
    $this->fpdf->Ln();
    $this->fpdf->Ln();
    
    $fecha = strtotime($liquidacion->fecha);

    /* Modificado por Victor el 29-09-2014 */

//Modifique la pocicion del Rut del trabajador y empleador.
//---------------------------------------------------------------------------------------------------------------------------------
    $this->fpdf->SetFont('Arial', '', 9.3);
    $this->fpdf->cell(40, 4, 'EMPLEADOR', 0, 0);
    $this->fpdf->cell(80, 4, $liquidacion->empresa, 0, 0);
    
    $this->fpdf->cell(52, 4, '', 0, 0, 'L');
    $this->fpdf->cell(40, 4, date("d/m/Y",$fecha), 0, 0);
    
    $this->fpdf->Ln();
    $this->fpdf->cell(13, 4, '', 0, 0, 'L');
    $this->fpdf->cell(13, 4, '', 0, 0, 'L');
    $this->fpdf->cell(14, 4, '', 0, 0, 'L');
    $this->fpdf->cell(80, 4, $liquidacion->empresarut, 0, 0);
    
    $this->fpdf->Ln();
    $this->fpdf->Ln();
    $this->fpdf->cell(40, 4, 'TRABAJADOR', 0, 0);
    $this->fpdf->cell(80, 4, strtoupper($liquidacion->apellido_paterno . ' ' . $liquidacion->apellido_materno . ' ' . $liquidacion->nombre), 0, 0);
    $this->fpdf->Ln();
    $this->fpdf->cell(13, 4, '', 0, 0, 'L');
    $this->fpdf->cell(13, 4, '', 0, 0, 'L');
    $this->fpdf->cell(14, 4, '', 0, 0, 'L');
    $this->fpdf->cell(80, 4, $liquidacion->rutempleado, 0, 0);
//--------------------------------------------------------------------------------------------------------------------------------

   
    $this->fpdf->Ln(8);
    $fecha = 'MES ' . date("m", $fecha) . ' DE ' . date("Y");
    $this->fpdf->cell(27, 4, $fecha, 0, 0, 'L');
    $this->fpdf->cell(13, 4, '', 0, 0, 'L');
    $this->fpdf->cell(55, 4, '', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
    $this->fpdf->cell(42, 4, '', 0, 0, 'L');
    $this->db->where('EXTRACT(MONTH from fecha) = \'' . date("m", strtotime($liquidacion->fecha)) . '\' AND EXTRACT(YEAR FROM Fecha) = \'' . date("Y", strtotime($liquidacion->fecha)) . '\'');
    $this->fpdf->cell(30.5, 4, 'UF. ' . number_format($this->db->get('uf')->row()->uf, 2, ',', '.'), 0, 0, 'L');
    $this->fpdf->Ln(10);
//Comienza los calculos
    $this->fpdf->cell(27, 4, 'Dias trabajados: ', 0, 0, 'L');
    $this->fpdf->cell(13, 4, $liquidacion->dias_trabajados, 0, 0, 'R');
    $this->fpdf->cell(55, 4, 'Sueldo Base', 0, 0, 'L');
    $this->fpdf->cell(24, 4, number_format($liquidacion->sueldo_base, 0, ',', '.'), 0, 0, 'R');
    $this->fpdf->cell(40.5, 4, 'Fondo de pensiones', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, number_format($liquidacion->fondo_pensiones, 0, ',', '.'), 0, 0, 'R');

    $this->fpdf->Ln();
    $this->fpdf->cell(27, 4, '', 0, 0, 'L');
    $this->fpdf->cell(13, 4, '', 0, 0, 'L');
    $this->fpdf->cell(55, 4, 'Sueldo Base Proporcional', 0, 0, 'L');
    $this->fpdf->cell(24, 4, number_format($liquidacion->sueldo_base_proporcional, 0, ',', '.'), 0, 0, 'R');
    $this->fpdf->cell(40.5, 4, 'Cotiz. Seg. Ces.', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, number_format($liquidacion->cotiz_seg_ces, 0, ',', '.'), 0, 0, 'R');


    $this->fpdf->Ln();
    $this->fpdf->cell(27, 4, 'Horas. Extras.', 0, 0, 'L');
    $this->fpdf->cell(13, 4, $liquidacion->cantidad_horas, 0, 0, 'R');
    $this->fpdf->cell(55, 4, 'Hrs. Ext', 0, 0, 'L');
    $this->fpdf->cell(24, 4, number_format($liquidacion->hrs_extras, 0, ',', '.'), 0, 0, 'R');
    $this->fpdf->cell(40.5, 4, 'Ahorro Prev. Voluntario', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, number_format($liquidacion->ahorro_prev_voluntario, 0, ',', '.'), 0, 0, 'R');


    $this->fpdf->Ln();
    $this->fpdf->cell(27, 4, '', 0, 0, 'L');
    $this->fpdf->cell(13, 4, '', 0, 0, 'L');
    $this->fpdf->cell(55, 4, 'Comisiones', 0, 0, 'L');
    $this->fpdf->cell(24, 4, number_format($liquidacion->comisiones, 0, ',', '.'), 0, 0, 'R');
    $this->fpdf->cell(40.5, 4, 'Cotiz. Salud', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, number_format($liquidacion->cotiz_salud, 0, ',', '.'), 0, 0, 'R');

    $this->fpdf->Ln();
    $this->fpdf->cell(27, 4, '', 0, 0, 'L');
    $this->fpdf->cell(13, 4, '', 0, 0, 'L');
    $this->fpdf->cell(55, 4, 'Bonos', 0, 0, 'L');
    $this->fpdf->cell(24, 4, number_format($liquidacion->bonos, 0, ',', '.'), 0, 0, 'R');
    $this->fpdf->cell(40.5, 4, 'Adicional de Salud', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, number_format($liquidacion->adicional_salud, 0, ',', '.'), 0, 0, 'R');

    $this->fpdf->Ln();
    $this->fpdf->cell(27, 4, '', 0, 0, 'L');
    $this->fpdf->cell(13, 4, '', 0, 0, 'L');
    $this->fpdf->cell(55, 4, 'Aguinaldo', 0, 0, 'L');
    $this->fpdf->cell(24, 4, number_format($liquidacion->aguinaldos, 0, ',', '.'), 0, 0, 'R');
    $this->fpdf->cell(40.5, 4, 'CCAF', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, number_format($liquidacion->caja_com_trabj, 0, ',', '.'), 0, 0, 'R');


    $this->fpdf->Ln();
    $this->fpdf->cell(27, 4, '', 0, 0, 'L');
    $this->fpdf->cell(13, 4, '', 0, 0, 'L');
    $this->fpdf->cell(55, 4, utf8_decode('Gratificación'), 0, 0, 'L');
    $this->fpdf->cell(24, 4, number_format($liquidacion->gratificaciones, 0, ',', '.'), 0, 0, 'R');
    $this->fpdf->cell(40.5, 4, 'Tot. Leyes Sociales', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, number_format($liquidacion->total_leyes_soc, 0, ',', '.'), 0, 0, 'R');

    $this->fpdf->Ln();
    $this->fpdf->cell(27, 4, '', 0, 0, 'L');
    $this->fpdf->cell(13, 4, '', 0, 0, 'L');
    $this->fpdf->cell(55, 4, '', 0, 0, 'L');
    $this->fpdf->cell(24, 4, '', 0, 0, 'L');
    $this->fpdf->cell(40.5, 4, 'RENTA AFECTA', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, number_format($liquidacion->renta_afecta, 0, ',', '.'), 0, 0, 'R');

    $this->fpdf->Ln(10);
    $this->fpdf->cell(27, 4, '', 0, 0, 'L');
    $this->fpdf->cell(13, 4, '', 0, 0, 'L');
    $this->fpdf->cell(55, 4, 'SUB TOTALES HABERES', 0, 0, 'L');
    $this->fpdf->cell(24, 4, number_format($liquidacion->sub_totales_haberes, 0, ',', '.'), 0, 0, 'R');
    $this->fpdf->cell(40.5, 4, 'Imp.2da Cat', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, number_format($liquidacion->imp_2cat, 0, ',', '.'), 0, 0, 'R');
    $this->fpdf->Ln();
    $this->fpdf->cell(27, 4, '', 0, 0, 'L');
    $this->fpdf->cell(13, 4, '', 0, 0, 'L');
    $this->fpdf->cell(55, 4, 'TOTAL IMPONIBLE', 0, 0, 'L');
    $this->fpdf->cell(24, 4, number_format($liquidacion->total_imponible, 0, ',', '.'), 0, 0, 'R');
    $this->fpdf->cell(40.5, 4, 'Cta.AhorroAFP', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, number_format($liquidacion->cta_ahorro_afp, 0, ',', '.'), 0, 0, 'R');
    $this->fpdf->Ln();
    $this->fpdf->cell(27, 4, '', 0, 0, 'L');
    $this->fpdf->cell(13, 4, '', 0, 0, 'L');
    $this->fpdf->cell(55, 4, '', 0, 0, 'L');
    $this->fpdf->cell(24, 4, '', 0, 0, 'L');
    $this->fpdf->cell(40.5, 4, 'Anticipos', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, number_format($liquidacion->anticipos, 0, ',', '.'), 0, 0, 'R');
    $this->fpdf->Ln();
    $this->fpdf->cell(27, 4, '', 0, 0, 'L');
    $this->fpdf->cell(13, 4, '', 0, 0, 'L');
    $this->fpdf->cell(55, 4, '', 0, 0, 'L');
    $this->fpdf->cell(24, 4, '', 0, 0, 'L');
    $this->fpdf->cell(40.5, 4, 'Prestamos. CCAF', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, number_format($liquidacion->prestamos_ccaf, 0, ',', '.'), 0, 0, 'R');
    $this->fpdf->Ln();
    $this->fpdf->cell(27, 4, '', 0, 0, 'L');
    $this->fpdf->cell(13, 4, '', 0, 0, 'L');
    $this->fpdf->cell(55, 4, utf8_decode('Asig. Locomoción'), 0, 0, 'L');
    $this->fpdf->cell(24, 4, number_format($liquidacion->locomocion, 0, ',', '.'), 0, 0, 'R');
    $this->fpdf->cell(40.5, 4, 'Desctos. Prestamos', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, number_format($liquidacion->descuentos_prestamos, 0, ',', '.'), 0, 0, 'R');
    $this->fpdf->Ln();
    $this->fpdf->cell(27, 4, '', 0, 0, 'L');
    $this->fpdf->cell(13, 4, '', 0, 0, 'L');
    $this->fpdf->cell(55, 4, utf8_decode('Asig. Colación'), 0, 0, 'L');
    $this->fpdf->cell(24, 4, number_format($liquidacion->colacion, 0, ',', '.'), 0, 0, 'R');
    $this->fpdf->cell(40.5, 4, 'Otros Desctos.', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, number_format($liquidacion->otros_descuentos, 0, ',', '.'), 0, 0, 'R');

    /* Modificado por victor el 29-09-2014 */

// No aparecia El campo Asignacion Familiar ni el monto correspondiente 
//----------------------------------------------------------------------------------------
    $this->fpdf->Ln();
    $this->fpdf->cell(27, 4, '', 0, 0, 'L');
    $this->fpdf->cell(13, 4, '', 0, 0, 'L');
    $this->fpdf->cell(55, 4, utf8_decode('Asignacion Familiar'), 0, 0, 'L');
    $this->fpdf->cell(24, 4, number_format($liquidacion->familiar, 0, ',', '.'), 0, 0, 'R');
    $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
//----------------------------------------------------------------------------------------

    $this->fpdf->Ln();
    $this->fpdf->cell(27, 4, '', 0, 0, 'L');
    $this->fpdf->cell(13, 4, '', 0, 0, 'L');
    $this->fpdf->cell(55, 4, utf8_decode('Otras Asignaciones'), 0, 0, 'L');
    $this->fpdf->cell(24, 4, number_format($liquidacion->otras_asignaciones, 0, ',', '.'), 0, 0, 'R');
    $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
    $this->fpdf->Ln();
    $this->fpdf->cell(27, 4, '', 0, 0, 'L');
    $this->fpdf->cell(13, 4, '', 0, 0, 'L');
    $this->fpdf->cell(55, 4, utf8_decode('Indem. Sustit.'), 0, 0, 'L');
    $this->fpdf->cell(24, 4, number_format($liquidacion->indem_sustit, 0, ',', '.'), 0, 0, 'R');
    $this->fpdf->SetFillColor(242, 242, 242);
    $this->fpdf->cell(40.5, 4, 'TOTAL HABERES', 0, 0, 'L', true);
    $this->fpdf->cell(30.5, 4, number_format($liquidacion->total_haberes, 0, ',', '.'), 0, 0, 'R', true);
    $this->fpdf->Ln();
    $this->fpdf->cell(27, 4, '', 0, 0, 'L');
    $this->fpdf->cell(13, 4, '', 0, 0, 'L');
    $this->fpdf->cell(55, 4, utf8_decode('Indem.Años Ser.'), 0, 0, 'L');
    $this->fpdf->cell(24, 4, number_format($liquidacion->indem_anos, 0, ',', '.'), 0, 0, 'R');
    $this->fpdf->SetFillColor(242, 242, 242);
    $this->fpdf->cell(40.5, 4, 'TOTAL DESCT.', 0, 0, 'L', true);
    $this->fpdf->cell(30.5, 4, number_format($liquidacion->total_descuentos, 0, ',', '.'), 0, 0, 'R', true);
    $this->fpdf->Ln();
    $this->fpdf->cell(27, 4, '', 0, 0, 'L');
    $this->fpdf->cell(13, 4, '', 0, 0, 'L');
    $this->fpdf->cell(55, 4, utf8_decode('TOTAL NO IMPON.'), 0, 0, 'L');
    $this->fpdf->cell(24, 4, number_format($liquidacion->total_no_imponible, 0, ',', '.'), 0, 0, 'R');
    $this->fpdf->SetFillColor(217, 217, 217);
    $this->fpdf->cell(40.5, 4, 'ALCANCE LIQUIDO', 0, 0, 'L', true);
    $this->fpdf->cell(30.5, 4, number_format($liquidacion->alcance_liquido, 0, ',', '.'), 0, 0, 'R', true);

    if ($i != 1) {
        $this->fpdf->Ln(127);
        $this->fpdf->cell(40, 4, utf8_decode(''), 0, 0, 'L');
        $this->fpdf->cell(105, 4, utf8_decode('Copia Trabajador'), 0, 0, 'L');
        $this->fpdf->cell(24, 4, utf8_decode('FIRMA TRABAJADOR'), 0, 0, 'L');

        $this->fpdf->Ln();
        $this->fpdf->cell(44, 4, utf8_decode(''), 0, 0, 'L');
        $this->fpdf->cell(108, 4, utf8_decode(''), 0, 0, 'L');
        $this->fpdf->cell(24, 4, $liquidacion->rutempleado, 0, 0, 'L');
    }

//Se imprimen los impuestos
    if ($i == 1) {
        $this->fpdf->Ln();
        $this->fpdf->Ln();
        $this->fpdf->cell(27, 4, '', 0, 0, 'L');
        $this->fpdf->cell(13, 4, '', 0, 0, 'L');
        $this->fpdf->SetFillColor(217, 217, 217);
        $this->fpdf->cell(55, 4, utf8_decode('TOTAL LEYES SOCIALES'), 0, 0, 'L', true);
        $this->fpdf->cell(24, 4, number_format($liquidacion->total_leyes_sociales, 0, ',', '.'), 0, 0, 'R', true);
        $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
        $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');

        $this->fpdf->Ln();
        $this->fpdf->cell(27, 4, '', 0, 0, 'L');
        $this->fpdf->cell(13, 4, '', 0, 0, 'L');
        $this->fpdf->SetFillColor(217, 217, 217);
        $this->fpdf->cell(55, 4, utf8_decode('TOTALES IMPUESTOS'), 0, 0, 'L', true);
        $this->fpdf->cell(24, 4, number_format($liquidacion->total_impuestos, 0, ',', '.'), 0, 0, 'R', true);
        $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
        $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
        $this->fpdf->SetFillColor(242, 242, 242);
        $this->fpdf->Ln();
        $this->fpdf->cell(27, 4, '', 0, 0, 'L');
        $this->fpdf->cell(13, 4, '', 0, 0, 'L');
        $this->fpdf->cell(55, 4, utf8_decode('AFP'), 0, 0, 'L', true);
        $this->fpdf->cell(24, 4, utf8_decode(''), 0, 0, 'R', true);
        $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
        $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');

        $this->fpdf->Ln();
        $this->fpdf->cell(27, 4, '', 0, 0, 'L');
        $this->fpdf->cell(13, 4, '', 0, 0, 'L');
        $this->fpdf->cell(55, 4, utf8_decode('Cotización Obligatoria'), 0, 0, 'L', true);
        $this->fpdf->cell(24, 4, number_format($liquidacion->cotizacion_obligatoria, 0, ',', '.'), 0, 0, 'R', true);
        $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
        $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');

        $this->fpdf->Ln();
        $this->fpdf->cell(27, 4, '', 0, 0, 'L');
        $this->fpdf->cell(13, 4, '', 0, 0, 'L');
        $this->fpdf->cell(55, 4, utf8_decode('Seguro Inv. Sobrevivencia (SIS)'), 0, 0, 'L', true);
        $this->fpdf->cell(24, 4, number_format($liquidacion->seguro_inv, 0, ',', '.'), 0, 0, 'R', true);
        $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
        $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');

        $this->fpdf->Ln();
        $this->fpdf->cell(27, 4, '', 0, 0, 'L');
        $this->fpdf->cell(13, 4, '', 0, 0, 'L');
        $this->fpdf->cell(55, 4, utf8_decode('Cuenta Ahorro Voluntario'), 0, 0, 'L', true);
        $this->fpdf->cell(24, 4, number_format($liquidacion->cuenta_ahorro, 0, ',', '.'), 0, 0, 'R', true);
        $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
        $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');

        $this->fpdf->Ln();
        $this->fpdf->cell(27, 4, '', 0, 0, 'L');
        $this->fpdf->cell(13, 4, '', 0, 0, 'L');
        $this->fpdf->cell(55, 4, utf8_decode('Aporte Indemnización (4,11%)'), 0, 0, 'L', true);
        $this->fpdf->cell(24, 4, number_format($liquidacion->indemnizacion, 0, ',', '.'), 0, 0, 'R', true);
        $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
        $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');

        $this->fpdf->Ln();
        $this->fpdf->cell(27, 4, '', 0, 0, 'L');
        $this->fpdf->cell(13, 4, '', 0, 0, 'L');
        $this->fpdf->cell(55, 4, utf8_decode('Total AFP'), 0, 0, 'L', true);
        $this->fpdf->cell(24, 4, number_format($liquidacion->afp, 0, ',', '.'), 0, 0, 'R', true);
        $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
        $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');

        $this->fpdf->Ln();
        $this->fpdf->cell(27, 4, '', 0, 0, 'L');
        $this->fpdf->cell(13, 4, '', 0, 0, 'L');
        $this->fpdf->cell(55, 4, utf8_decode('AFC'), 0, 0, 'L', true);
        $this->fpdf->cell(24, 4, utf8_decode(''), 0, 0, 'R', true);
        $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
        $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');

        $this->fpdf->Ln();
        $this->fpdf->cell(27, 4, '', 0, 0, 'L');
        $this->fpdf->cell(13, 4, '', 0, 0, 'L');
        $this->fpdf->cell(55, 4, utf8_decode('Seguro Cesantia Trabajador'), 0, 0, 'L', true);
        $this->fpdf->cell(24, 4, number_format($liquidacion->seguro_cesantia_trabajador, 0, ',', '.'), 0, 0, 'R', true);
        $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
        $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');

        $this->fpdf->Ln();
        $this->fpdf->cell(27, 4, '', 0, 0, 'L');
        $this->fpdf->cell(13, 4, '', 0, 0, 'L');
        $this->fpdf->cell(55, 4, utf8_decode('Seguro Cesantia Empleador'), 0, 0, 'L', true);
        $this->fpdf->cell(24, 4, number_format($liquidacion->seguro_cesantia_empleador, 0, ',', '.'), 0, 0, 'R', true);
        $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
        $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');

        $this->fpdf->Ln();
        $this->fpdf->cell(27, 4, '', 0, 0, 'L');
        $this->fpdf->cell(13, 4, '', 0, 0, 'L');
        $this->fpdf->cell(55, 4, utf8_decode('Total AFC'), 0, 0, 'L', true);
        $this->fpdf->cell(24, 4, number_format($liquidacion->afc, 0, ',', '.'), 0, 0, 'R', true);
        $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
        $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');

        $this->fpdf->Ln();
        $this->fpdf->cell(27, 4, '', 0, 0, 'L');
        $this->fpdf->cell(13, 4, '', 0, 0, 'L');
        $this->fpdf->cell(55, 4, utf8_decode('MUTUTALIDAD / INP'), 0, 0, 'L', true);
        $this->fpdf->cell(24, 4, utf8_decode(''), 0, 0, 'R', true);
        $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
        $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');

        $this->fpdf->Ln();
        $this->fpdf->cell(27, 4, '', 0, 0, 'L');
        $this->fpdf->cell(13, 4, '', 0, 0, 'L');
        $this->fpdf->cell(55, 4, utf8_decode('Cot. Accidente del Trabajo'), 0, 0, 'L', true);
        $this->fpdf->cell(24, 4, number_format($liquidacion->cot_accidente_trabajo, 0, ',', '.'), 0, 0, 'R', true);
        $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
        $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');

        $this->fpdf->Ln();
        $this->fpdf->cell(27, 4, '', 0, 0, 'L');
        $this->fpdf->cell(13, 4, '', 0, 0, 'L');
        $this->fpdf->cell(55, 4, utf8_decode('Total IPS / Mututalidad'), 0, 0, 'L', true);
        $this->fpdf->cell(24, 4, number_format($liquidacion->total_ips, 0, ',', '.'), 0, 0, 'R', true);
        $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
        $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');

        $this->fpdf->Ln();
        $this->fpdf->cell(27, 4, '', 0, 0, 'L');
        $this->fpdf->cell(13, 4, '', 0, 0, 'L');
        $this->fpdf->cell(55, 4, utf8_decode('SALUD'), 0, 0, 'L', true);
        $this->fpdf->cell(24, 4, utf8_decode(''), 0, 0, 'R', true);
        $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
        $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');

        $this->fpdf->Ln();
        $this->fpdf->cell(27, 4, '', 0, 0, 'L');
        $this->fpdf->cell(13, 4, '', 0, 0, 'L');
        $this->fpdf->cell(55, 4, utf8_decode('Cotización Obligatoria'), 0, 0, 'L', true);
        $this->fpdf->cell(24, 4, number_format($liquidacion->cotizacion_obligatoria_salud, 0, ',', '.'), 0, 0, 'R', true);
        $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
        $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');

        $this->fpdf->Ln();
        $this->fpdf->cell(27, 4, '', 0, 0, 'L');
        $this->fpdf->cell(13, 4, '', 0, 0, 'L');
        $this->fpdf->cell(55, 4, utf8_decode('Cotización Adicional'), 0, 0, 'L', true);
        $this->fpdf->cell(24, 4, number_format($liquidacion->cotizacion_adicional, 0, ',', '.'), 0, 0, 'R', true);
        $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
        $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');

        $this->fpdf->Ln();
        $this->fpdf->cell(27, 4, '', 0, 0, 'L');
        $this->fpdf->cell(13, 4, '', 0, 0, 'L');
        $this->fpdf->cell(55, 4, utf8_decode('Total Salud'), 0, 0, 'L', true);
        $this->fpdf->cell(24, 4, number_format($liquidacion->total_fonasa, 0, ',', '.'), 0, 0, 'R', true);
        $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
        $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');

        $this->fpdf->Ln();
        $this->fpdf->cell(27, 4, '', 0, 0, 'L');
        $this->fpdf->cell(13, 4, '', 0, 0, 'L');
        $this->fpdf->cell(55, 4, utf8_decode('Caja de Compensacion'), 0, 0, 'L', true);
        $this->fpdf->cell(24, 4, number_format($liquidacion->caja_comp, 0, ',', '.'), 0, 0, 'R', true);
        $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
        $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');

        $this->fpdf->Ln(9);
        $this->fpdf->cell(27, 4, '', 0, 0, 'L');
        $this->fpdf->cell(13, 4, '', 0, 0, 'L');
        $this->fpdf->SetFillColor(217, 217, 217);
        $this->fpdf->cell(55, 4, utf8_decode('COSTO TOTAL EMPLEADOR'), 0, 0, 'L', true);
        $this->fpdf->cell(24, 4, number_format($liquidacion->costo_total_empleador, 0, ',', '.'), 0, 0, 'R', true);
        $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
        $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
        
        $this->fpdf->Ln(34);
        $this->fpdf->cell(40, 4, utf8_decode(''), 0, 0, 'L');
        $this->fpdf->cell(104, 4, utf8_decode('Copia Empleador'), 0, 0, 'L');
        $this->fpdf->cell(24, 4, utf8_decode('FIRMA TRABAJADOR'), 0, 0, 'L');

        $this->fpdf->Ln();
        $this->fpdf->cell(44, 4, utf8_decode(''), 0, 0, 'L');
        $this->fpdf->cell(108, 4, utf8_decode(''), 0, 0, 'L');
        $this->fpdf->cell(24, 4, $liquidacion->rutempleado, 0, 0, 'L');
    }
}
$this->fpdf->Ln(90);
$this->fpdf->SetFont('Arial', 'B', 15);
$this->fpdf->SetFillColor(242, 242, 242);
$this->fpdf->cell(190, 12, 'RESUMEN PREVIRED', 0, 0, 'C', true);
$this->fpdf->Ln();
$this->fpdf->Ln();


$this->fpdf->SetFont('Arial', 'B', 12);
$this->fpdf->cell(55, 4, utf8_decode('Datos personales'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, '', 0, 0, 'R', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();

$this->fpdf->SetFont('Arial', '', 9);

$this->fpdf->cell(55, 4, utf8_decode('RUT'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, utf8_decode($liquidacion->rutempleado), 0, 0, 'L', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();

$this->fpdf->cell(55, 4, utf8_decode('Nombres'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, strtoupper($liquidacion->nombre), 0, 0, 'L', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();

$this->fpdf->cell(55, 4, utf8_decode('Apellido Paterno'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, strtoupper($liquidacion->apellido_paterno), 0, 0, 'L', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();

$this->fpdf->cell(55, 4, utf8_decode('Apellido Materno'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, strtoupper($liquidacion->apellido_materno), 0, 0, 'L', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();

$this->fpdf->cell(55, 4, utf8_decode('Sexo'), 0, 0, 'L', false);
if ($liquidacion->sexo == 'M') {
    $this->fpdf->cell(24, 4, utf8_decode('MASCULINO'), 0, 0, 'L', false);
} elseif ($liquidacion->sexo == 'F') {
    $this->fpdf->cell(24, 4, utf8_decode('FEMENINO'), 0, 0, 'L', false);
} else {
    $this->fpdf->cell(24, 4, strtoupper($liquidacion->sexo), 0, 0, 'L', false);
}
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();

$this->fpdf->cell(55, 4, utf8_decode('N° de Cargas Familiares'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, utf8_decode($liquidacion->cargas_familiares), 0, 0, 'L', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();

//$this->fpdf->cell(55,5,utf8_decode('Tramo Asignación Familiar'),0,0,'L',false);
//$this->fpdf->cell(24,5,'',0,0,'L',false);
//$this->fpdf->cell(40.5,5,'',0,0,'L');
//$this->fpdf->cell(30.5,5,'',0,0,'L');
//$this->fpdf->Ln();

$this->fpdf->cell(55, 4, utf8_decode('Tipo de Trabajador'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, strtoupper($liquidacion->tipo_trabajador), 0, 0, 'L', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();
$this->fpdf->Ln();

$this->fpdf->SetFont('Arial', 'B', 12);
$this->fpdf->cell(55, 4, utf8_decode('Datos Laborales'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, '', 0, 0, 'R', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();

$this->fpdf->SetFont('Arial', '', 9);

$this->fpdf->cell(55, 4, utf8_decode('Renta imponible'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, number_format($liquidacion->total_imponible, 0, ',', '.'), 0, 0, 'L', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();
$this->fpdf->Ln();

$this->fpdf->SetFont('Arial', 'B', 12);
$this->fpdf->cell(55, 4, utf8_decode('Datos AFP'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, '', 0, 0, 'R', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();

$this->fpdf->SetFont('Arial', '', 9);

$this->fpdf->cell(55, 4, utf8_decode('Nombre AFP'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, strtoupper($liquidacion->nombre_afp), 0, 0, 'L', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();

$this->fpdf->cell(55, 4, utf8_decode('Cotización Obligatoria'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, number_format($liquidacion->fondo_pensiones, 0, ',', '.'), 0, 0, 'L', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();

$this->fpdf->cell(55, 4, utf8_decode('Cotización SIS'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, number_format($liquidacion->seguro_inv, 0, ',', '.'), 0, 0, 'L', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();

//$this->fpdf->cell(55,5,utf8_decode('Depósito a Cuenta de Ahorro'),0,0,'L',false);
//$this->fpdf->cell(24,5,'',0,0,'L',false);
//$this->fpdf->cell(40.5,5,'',0,0,'L');
//$this->fpdf->cell(30.5,5,'',0,0,'L');
//$this->fpdf->Ln();

$this->fpdf->cell(55, 4, utf8_decode('Total AFP'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, number_format($liquidacion->afp, 0, ',', '.'), 0, 0, 'L', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();
$this->fpdf->Ln();

$this->fpdf->SetFont('Arial', 'B', 12);
$this->fpdf->cell(55, 4, utf8_decode('Datos Seguro de Cesantía, AFC'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, '', 0, 0, 'R', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();

$this->fpdf->SetFont('Arial', '', 9);

$this->fpdf->cell(55, 4, utf8_decode('Renta Imponible Seg. de Cesantía'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, '', 0, 0, 'L', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();

$this->fpdf->cell(55, 4, utf8_decode('Tipo de Contrato'), 0, 0, 'L', false);
if ($liquidacion->tipo_contrato == 'INDEFINIDO') {
    $this->fpdf->cell(24, 4, utf8_decode('INDEFINIDO'), 0, 0, 'L', false);
} else {
    $this->fpdf->cell(24, 4, utf8_decode('PLAZO FIJO'), 0, 0, 'L', false);
}
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();

$this->fpdf->cell(55, 4, utf8_decode('Aprt. Trabajador Seg. Cesantía'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, number_format($liquidacion->seguro_cesantia_trabajador, 0, ',', '.'), 0, 0, 'L', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();

$this->fpdf->cell(55, 4, utf8_decode('Aprt. Empleador Seg. Cesantía'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, number_format($liquidacion->seguro_cesantia_empleador, 0, ',', '.'), 0, 0, 'L', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();

$this->fpdf->cell(55, 4, utf8_decode('AFP Recaudadora'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, strtoupper($liquidacion->nombre_afp), 0, 0, 'L', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();

$this->fpdf->cell(55, 4, utf8_decode('Total AFC'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, number_format($liquidacion->afc, 0, ',', '.'), 0, 0, 'L', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();
$this->fpdf->Ln();

$this->fpdf->SetFont('Arial', 'B', 12);
$this->fpdf->cell(55, 4, utf8_decode('Datos MUTUAL/IPS'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, '', 0, 0, 'R', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();

$this->fpdf->SetFont('Arial', '', 9);

$this->fpdf->cell(55, 4, utf8_decode('Cotización Accidente del Trabajo'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, number_format($liquidacion->cot_accidente_trabajo, 0, ',', '.'), 0, 0, 'L', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();

$this->fpdf->cell(55, 4, utf8_decode('Total MUTUAL/IPS'), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, number_format($liquidacion->total_ips, 0, ',', '.'), 0, 0, 'L', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();

$this->fpdf->Ln();

$this->fpdf->SetFont('Arial', 'B', 12);
if ($liquidacion->nombre_isapre == 'FONASA')
    $salud = 'FONASA';
else
    $salud = 'ISAPRE';
$this->fpdf->cell(55, 4, utf8_decode('Datos ' . $salud), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, '', 0, 0, 'R', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();

$this->fpdf->SetFont('Arial', '', 9);

if ($liquidacion->nombre_isapre != 'FONASA') {
    $this->fpdf->cell(55, 4, utf8_decode('Nombre ISAPRE'), 0, 0, 'L', false);
    $this->fpdf->cell(24, 4, utf8_decode($liquidacion->nombre_isapre), 0, 0, 'L', false);
    $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
    $this->fpdf->Ln();

    $this->fpdf->cell(55, 4, utf8_decode('Cotizacion Obligatoria'), 0, 0, 'L', false);
    $this->fpdf->cell(24, 4, number_format($liquidacion->cotiz_salud, 0, ',', '.'), 0, 0, 'L', false);
    $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
    $this->fpdf->Ln();

    $this->fpdf->cell(55, 4, utf8_decode('Cotización Pactada Isapre'), 0, 0, 'L', false);
    $this->fpdf->cell(24, 4, utf8_decode(str_replace(".", ",", "$liquidacion->plan_isapre") . " UF"), 0, 0, 'L', false);
    $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
    $this->fpdf->Ln();

    $this->fpdf->cell(55, 4, utf8_decode('Cotización Adicional Voluntaria'), 0, 0, 'L', false);
    $this->fpdf->cell(24, 4, number_format($liquidacion->cotizacion_adicional, 0, ',', '.'), 0, 0, 'L', false);
    $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
    $this->fpdf->Ln();
} else {
    $this->fpdf->cell(55, 4, utf8_decode('Cotizacion Obligatoria'), 0, 0, 'L', false);
    $this->fpdf->cell(24, 4, number_format($liquidacion->cotiz_salud, 0, ',', '.'), 0, 0, 'L', false);
    $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
    $this->fpdf->Ln();
}
$this->fpdf->cell(55, 4, utf8_decode('Total ' . $salud), 0, 0, 'L', false);
$this->fpdf->cell(24, 4, number_format($liquidacion->total_fonasa, 0, ',', '.'), 0, 0, 'L', false);
$this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
$this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
$this->fpdf->Ln();
$this->fpdf->Ln();

if ($liquidacion->nombre_caja != 'Sin Caja de Compensacion') {
    $this->fpdf->SetFont('Arial', 'B', 12);

    $this->fpdf->cell(55, 4, utf8_decode('Datos Caja de Compensación'), 0, 0, 'L', false);
    $this->fpdf->cell(24, 4, '', 0, 0, 'R', false);
    $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
    $this->fpdf->Ln();

    $this->fpdf->SetFont('Arial', '', 9);

    $this->fpdf->cell(55, 4, utf8_decode('Nombre Caja'), 0, 0, 'L', false);
    $this->fpdf->cell(24, 4, utf8_decode(strtoupper($liquidacion->nombre_caja)), 0, 0, 'L', false);
    $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
    $this->fpdf->Ln();
    if ($liquidacion->familiar != 0) {
        $this->fpdf->cell(55, 4, utf8_decode('Monto Asignación Familiar CCAF'), 0, 0, 'L', false);
        $this->fpdf->cell(24, 4, number_format($liquidacion->familiar, 0, ',', '.'), 0, 0, 'L', false);
        $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
        $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
        $this->fpdf->Ln();
    }
    $this->fpdf->cell(55, 4, utf8_decode('Cotización de NO afiliado a Isapre'), 0, 0, 'L', false);
    $this->fpdf->cell(24, 4, number_format($liquidacion->caja_comp, 0, ',', '.'), 0, 0, 'L', false);
    $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
    $this->fpdf->Ln();

    $this->fpdf->cell(55, 4, utf8_decode('Total Caja de Compensación'), 0, 0, 'L', false);
    $this->fpdf->cell(24, 4, number_format($liquidacion->caja_comp, 0, ',', '.'), 0, 0, 'L', false);
    $this->fpdf->cell(40.5, 4, '', 0, 0, 'L');
    $this->fpdf->cell(30.5, 4, '', 0, 0, 'L');
    $this->fpdf->Ln();
    $this->fpdf->Ln();
}
$this->fpdf->Output();
?>