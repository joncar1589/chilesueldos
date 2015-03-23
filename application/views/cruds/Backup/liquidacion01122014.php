<?= $output ?>
<script>
    var empleado = undefined;
    var dbuf = undefined;
    var dbfamiliares = undefined;
    var dbimpuestos = undefined;
    var dbsca = undefined;
    var dbsalud = undefined;
    var salario = undefined;
    var caja = undefined;

    $(document).ready(function () {
        $("#field-factor_hora_extra").change(function () {
            if (parseFloat($(this).val()) < 1)
            {
                alert("Este valor debe ser un numero mayor o igual que 1")
                $(this).focus();
            }
        });
        //Traer el sueldo base
        $("#field-empleado").change(function () {
            if ($(this).val() != '')
            {
                consultar_sueldo($(this).val())
            }
        });
        $("#field-fecha").change(function () {
            consultas($(this).val());
        });
        //Ajustes de interfaz
        if ($("#field-fecha").val().length == 0) {
            $("input[type='text']").val(0);
            $("#field-dias_trabajados").val(30);
            $(".form-field-box").hide();
        }
        else {
            consultar_sueldo($("#field-empleado").val())
            consultas($("#field-fecha").val());

        }
        $(".even").removeClass("even");
        $("input").change(function () {
            calculos()
        });
<?php if (empty($y)): ?>
            $("input").attr('readonly', false);
            $("input[type='text']").css({"background": "#C3C3C3", 'color': 'white'});
<?php endif ?>
        $("#field-comisiones,#field-bonos,#field-aguinaldos,#field-otras_asignaciones,#field-fecha,#field-dias_trabajados,.chzn-search input,#field-cantidad_horas,#field-factor_hora_extra").css({"background": "transparent", 'color': 'black'});
        $(".chzn-search input").val('');
        $(".chzn-search input,#field-comisiones,#field-bonos,#field-aguinaldos,#field-otras_asignaciones,#field-dias_trabajados,#field-cantidad_horas,#field-factor_hora_extra").attr('readonly', false);
        $("#empresa_field_box,#empleado_field_box,#fecha_field_box,#fecha_emision_field_box").show();
        $("#costo_total_empleador_field_box,#total_leyes_sociales_field_box,#total_impuestos_field_box,#afp_field_box,#afc_field_box,#mutualidad_field_box,#total_ips_field_box,#total_fonasa_field_box,#impuesto_trabajador_field_box,#total_imponible_field_box,#renta_afecta_field_box,#total_otros_descuentos_field_box,#alcance_liquido_field_box").css('font-weight', 'bold');
        $("#costo_total_empleador_field_box,#total_leyes_sociales_field_box,#total_impuestos_field_box,#afp_field_box,#afc_field_box,#mutualidad_field_box,#total_ips_field_box,#total_fonasa_field_box,#impuesto_trabajador_field_box,#total_imponible_field_box,#renta_afecta_field_box,#total_otros_descuentos_field_box,#alcance_liquido_field_box").addClass('even');


    });
    function consultar_sueldo(e)
    {
        $.post('<?php echo base_url("empresa/get_salario") ?>', {id: e}, function (data) {
            empleado = JSON.parse(data);
            $("#field-sueldo_base").val(empleado['sueldo_base']);
            $("#cotizacion_obligatoria_display_as_box").html('Cotizacion Obligatoria (' + empleado['por_afp'] + ')%');
            var s = empleado['tipo_contrato'] == 'FIJO' ? '0' : '0.6';
            $("#seguro_cesantia_trabajador_display_as_box").html('Seguro Cesantia Trabajador (' + s + ')%');
            s = empleado['tipo_contrato'] == 'FIJO' ? '3' : '2.4';
            $("#seguro_cesantia_empleador_display_as_box").html('Seguro Cesantia Empleador (' + s + ')%');
            $("#cotizacion_obligatoria_salud_display_as_box").html('Cotizacion obligatoria salud (' + empleado['por_salud'] + ')%');
            calculos();
        });
    }


    function consultas(f)
    {
        f = f.split('/');
//           var dias = new Date(parseInt(f[2]) || new Date().getFullYear(),parseInt(f[1]), 0).getDate();
        var dias = 30;
        $("#field-dias_mes").val(dias);
        //Traer dbuf
        var iduf = (parseInt(f[2]) - 2000) * parseInt(f[2]) + parseInt(f[1]);
        $.post('<?php echo base_url('empresa/getuf') ?>', {id: iduf}, function (data) {
            dbuf = JSON.parse(data);
            $("#seguro_inv_display_as_box").html('Seguro Inv. Sobrevivencia (SIS) (' + dbuf['tasa_sis'] + ')%');
            if (parseInt(dbuf['id']) > 0) {
                calculos();
            }
            else
                alert("No se consiguieron registros de UF para el mes seleccionado");

        });
        //Traer el salario minimo
        $.post('<?php echo base_url('empresa/get_salario_minimo') ?>', {mes: f[1], ano: f[2]}, function (data) {
            salario = JSON.parse(data);
            if (parseInt(salario['id']) > 0) {
                calculos();
            }
            else
                alert("No se consiguieron Salarios Minimos para el mes seleccionado");
        });
        //Traer asignaciones familiares
        $.post('<?php echo base_url('empresa/getfamiliares') ?>', {ano: f[2]}, function (data) {
            dbfamiliares = JSON.parse(data);
            if (parseInt(dbfamiliares['id']) > 0) {
                calculos();
            }
            else
                alert("No se consiguieron registros de familiares para el mes seleccionado");
        });
        //Traer impuestos
        $.post('<?php echo base_url('empresa/getimpuestos') ?>', {mes: f[1], ano: f[2]}, function (data) {
            dbimpuestos = JSON.parse(data);
            if (parseInt(dbimpuestos['id']) > 0) {
                calculos();
            }
            else
                alert("No se consiguieron registros de impuestos para el mes seleccionado");
        });
        //Traer factor accidente
        $.post('<?php echo base_url('empresa/getsca') ?>', {mes: f[1], ano: f[2], empresa: $("#field-empresa").val()}, function (data) {
            dbsca = JSON.parse(data);
            $("#cot_accidente_trabajo_display_as_box").html('Cot.Accidente del Trabajo (' + dbsca['factor'] + ')%');
            if (parseInt(dbsca['id']) > 0) {
                calculos();
            }
            else
                alert("No se consiguieron registros de factor accidente para el mes seleccionado");
        });


        //Traer retencion salud
        /*   $.post('<?php// echo base_url('empresa/getretsalud') ?>',{mes:f[1],ano:f[2],empresa:$("#field-empresa").val()},function(data){                
         dbsalud = JSON.parse(data);
         if(parseInt(dbsalud['id'])>0){                                        
         calculos();
         }
         else alert("No se consiguieron registros de retención salud para el mes seleccionado");
         });*/

    }
    function check() {

        if ($("#hrs-ex-check").is(':checked')) {
            $("#cantidad_horas_field_box,#factor_hora_extra_field_box,#hrs_extras_field_box").show();
            $("#hrs-ex-check-field-box").hide();
            
        }
        if ($("#com-check").is(':checked')) {
            $("#comisiones_field_box").show();
            $("#com-check-field-box").hide();
        }
        

    }
    function calculos()
    {
        if (empleado != undefined && dbsca != undefined && dbuf != undefined && dbfamiliares != undefined && dbimpuestos != undefined && salario != undefined)
        {
            $(".form-field-box").show();
            $("#tope_imponible_field_box,#preimponible_field_box").hide();
            $("#dias_mes_field_box,#sueldo_base_field_box,#bonos_ficha_field_box,#cantidad_horas_field_box,#factor_hora_extra_field_box,#hrs_extras_field_box,#comisiones_field_box").hide();
            
            var bonos = parseInt($("#field-bonos").val());
            bonos = !isNaN(bonos) ? bonos : 0;
            var comisiones = parseInt($("#field-comisiones").val());
            comisiones = !isNaN(comisiones) ? comisiones : 0;
            var aguinaldos = parseInt($("#field-aguinaldos").val());
            aguinaldos = !isNaN(aguinaldos) ? aguinaldos : 0;
            var dias_trabajados = parseFloat($("#field-dias_trabajados").val()); //Modificado de "parseInt" a "parseFloat" por Víctor Alarcón el 29/09/2014 ya que el calculo no consideraba dias trabajados con decimales. 
            var dias_mes = parseInt($("#field-dias_mes").val());
            var sueldo_base = parseInt($("#field-sueldo_base").val());
            //Calcular sueldo proporcional
            var sueldo_proporcional = (sueldo_base / dias_mes) * dias_trabajados;
            $("#field-sueldo_base_proporcional").val(!isNaN(sueldo_proporcional) ? sueldo_proporcional.toFixed(0) : 0);
            //Calcular horas extras
            var hora_ordinaria = ((sueldo_base / 30) * 28) / 180;
            var valor_hora = hora_ordinaria * parseFloat($("#field-factor_hora_extra").val());
            var horas = valor_hora * parseInt($("#field-cantidad_horas").val());
            $("#field-hrs_extras").val(!isNaN(horas) ? horas.toFixed(0) : 0);
            //Calcular pre-imponible
            var preimponible = sueldo_proporcional + comisiones + bonos + aguinaldos + horas;
            $("#field-preimponible").val(!isNaN(preimponible) ? preimponible.toFixed(0) : 0);
            //Calcular gratificaciones
            var salario_minimo = parseInt(salario['monto']);
            var gratificaciones = preimponible > 0 ? ((parseFloat(empleado['gratificacion']) / 100) * preimponible > salario_minimo * 4.75 / 12) ? salario_minimo * 4.75 / 12 : preimponible * (empleado['gratificacion'] / 100) : 0;
            $("#field-gratificaciones").val(!isNaN(gratificaciones) ? gratificaciones.toFixed(0) : 0);
            //Subtotal haberes
            var subtotal_haberes = preimponible + gratificaciones;
            $("#field-sub_totales_haberes").val(!isNaN(subtotal_haberes) ? subtotal_haberes.toFixed(0) : 0);
            //Traer tope imponible
            var tope_imponible = parseFloat(dbuf['tope_afp']) * parseFloat(dbuf['uf']);
            $("#field-tope_imponible").val(!isNaN(tope_imponible) ? tope_imponible.toFixed(0) : 0);
            //Calcular total imponible
            var total_imponible = tope_imponible <= subtotal_haberes ? tope_imponible : subtotal_haberes;
            $("#field-total_imponible").val(!isNaN(total_imponible) ? total_imponible.toFixed(0) : 0);
            //Calcular bono_locomocion
            var locomocion = empleado['bono_locomocion'] * dias_trabajados / dias_mes;
            $("#field-locomocion").val(!isNaN(locomocion) ? locomocion.toFixed(0) : 0);
            //Calcular Colación
            var colacion = empleado['bono_colacion'] * dias_trabajados / dias_mes;
            $("#field-colacion").val(!isNaN(colacion) ? colacion.toFixed(0) : 0);
            //Calcular otras asignaciones
            var otras_asignaciones = empleado['otras_asignaciones'] * dias_trabajados / dias_mes;
            $("#field-otras_asignaciones").val(!isNaN(otras_asignaciones) ? otras_asignaciones.toFixed(0) : 0);
            //Calcular cargas familiares
            var familiares = 0;
            if (total_imponible < dbfamiliares['tramo_a2'])
                familiares = parseInt(empleado['cargas_familiares']) * parseInt(dbfamiliares['monto_tramo_a']);
            else if (total_imponible < dbfamiliares['tramo_b2'])
                familiares = parseInt(empleado['cargas_familiares']) * parseInt(dbfamiliares['monto_tramo_b']);
            else if (total_imponible < dbfamiliares['tramo_c2'])
                familiares = parseInt(empleado['cargas_familiares']) * parseInt(dbfamiliares['monto_tramo_c']);
            else if (total_imponible < dbfamiliares['tramo_d2'])
                familiares = parseInt(empleado['cargas_familiares']) * parseInt(dbfamiliares['monto_tramo_d']);
            $("#field-familiar").val(!isNaN(familiares) ? familiares : 0);
            //Calcular total no imponible

            /* Modificado por Víctor Alarcón el 01-10-2014 para resolver problema con total no imponible con decimales que no dejaba guardar la liquidación en la DB */
            /*------------------------------------------------------------------------------------------------------------------------------------------------------------*/
            var totalnoimponible = parseInt(Math.round(locomocion)) + parseInt(Math.round(colacion)) + parseInt(Math.round(otras_asignaciones)) + parseInt(Math.round(familiares)) + parseInt(Math.round($("#field-indem_sustit").val())) + parseInt(Math.round($("#field-indem_anos").val()));
            $("#field-total_no_imponible").val(!isNaN(totalnoimponible) ? totalnoimponible : 0);
            /*------------------------------------------------------------------------------------------------------------------------------------------------------------*/

            //Calcular fondo de pensiones
            var fondopensiones = total_imponible * (parseFloat(empleado['por_afp']) / 100);
            $("#field-fondo_pensiones").val(!isNaN(fondopensiones) ? fondopensiones.toFixed(0) : 0);
            //Calcular % de salud
            var por_salud = empleado['id_caja'] !== '1' && empleado['sistema_salud_afiliado'] == '5' ? 6.4 : 7;
            var salud = total_imponible <= tope_imponible ? total_imponible * (parseFloat(por_salud) / 100) : tope_imponible * (parseFloat(por_salud) / 100);
            $("#field-cotiz_salud").val(!isNaN(salud) ? salud.toFixed(0) : 0);
            //Calcular apv
            var apv = empleado['apv'] == undefined ? 0 : parseFloat(empleado['apv']);
            $("#field-ahorro_prev_voluntario").val(!isNaN(apv) ? apv.toFixed(0) : 0);
            //Calcular inp
            var inp = parseInt(empleado['dependiente']) == 1 ? 0 : 0;
            $("#field-inp_mutual").val(!isNaN(inp) ? inp.toFixed(0) : 0);
            //Calcular seguro cesantia
            var tope_afc = parseFloat(dbuf['tope_afc']) * parseFloat(dbuf['uf']);
            var s = empleado['tipo_contrato'] == 'FIJO' || empleado['tipo_contrato'] == 'POR OBRA' ? 0 : 0.6;

            var segurocesantia = empleado['empleado_casa'] == '1' ? 0 : subtotal_haberes <= tope_afc ? (s / 100) * subtotal_haberes : (s / 100) * tope_afc;
            $("#field-cotiz_seg_ces").val(!isNaN(segurocesantia) ? segurocesantia.toFixed(0) : 0);
            //Calcular adicional salud
            var legal_7 = total_imponible <= tope_imponible ? total_imponible * (parseFloat(empleado['por_salud']) / 100) : tope_imponible * (parseFloat(empleado['por_salud']) / 100);
            var plan_isapre = parseFloat(empleado['plan_isapre']) * parseFloat(dbuf['uf']);
            var adicional_salud = plan_isapre > legal_7 ? plan_isapre - legal_7 : 0;
            $("#field-adicional_salud").val(!isNaN(adicional_salud) ? adicional_salud.toFixed(0) : 0);
            //Calcular total leyes sociales
            var caja_com_trabj = empleado['sistema_salud_afiliado'] == '5' && empleado['id_caja'] !== '1' ? (total_imponible * (parseFloat(empleado['por_caja']) / 100)) : 0;//Esto es porque el trabajador no percibe el descuento por asignación familiar 
            $("#field-caja_com_trabj").val(!isNaN(caja_com_trabj) ? caja_com_trabj.toFixed(0) : 0);
            var total_leyes_sociales = fondopensiones + salud + apv + inp + segurocesantia + adicional_salud + caja_com_trabj;
            $("#field-total_leyes_soc").val(!isNaN(total_leyes_sociales) ? total_leyes_sociales.toFixed(0) : 0);
            //Calcular renta_afecta
            var renta_afecta = subtotal_haberes - total_leyes_sociales;
            $("#field-renta_afecta").val(!isNaN(renta_afecta) ? renta_afecta.toFixed(0) : 0);
            //Calcular imp2cat
            var imp2cat = 1;
            if (renta_afecta < parseFloat(dbimpuestos['tramo_0b']))
                imp2cat = renta_afecta * parseFloat(dbimpuestos['factor_0']) - parseFloat(dbimpuestos['rebaja_0']);
            else if (renta_afecta < parseFloat(dbimpuestos['tramo_5b']))
                imp2cat = renta_afecta * parseFloat(dbimpuestos['factor_5']) - parseFloat(dbimpuestos['rebaja_5']);
            else if (renta_afecta < parseFloat(dbimpuestos['tramo_10b']))
                imp2cat = renta_afecta * parseFloat(dbimpuestos['factor_10']) - parseFloat(dbimpuestos['rebaja_10']);
            else if (renta_afecta < parseFloat(dbimpuestos['tramo_15b']))
                imp2cat = renta_afecta * parseFloat(dbimpuestos['factor_15']) - parseFloat(dbimpuestos['rebaja_15']);
            else if (renta_afecta < parseFloat(dbimpuestos['tramo_25b']))
                imp2cat = renta_afecta * parseFloat(dbimpuestos['factor_25']) - parseFloat(dbimpuestos['rebaja_25']);
            else if (renta_afecta < parseFloat(dbimpuestos['tramo_32b']))
                imp2cat = renta_afecta * parseFloat(dbimpuestos['factor_32']) - parseFloat(dbimpuestos['rebaja_32']);
            else if (renta_afecta < parseFloat(dbimpuestos['tramo_37b']))
                imp2cat = renta_afecta * parseFloat(dbimpuestos['factor_37']) - parseFloat(dbimpuestos['rebaja_37']);
            else if (renta_afecta < parseFloat(dbimpuestos['tramo_40b']))
                imp2cat = renta_afecta * parseFloat(dbimpuestos['factor_40']) - parseFloat(dbimpuestos['rebaja_40']);
            $("#field-imp_2cat").val(!isNaN(imp2cat) ? imp2cat.toFixed(0) : 0);
            //Otros descuentos
            var otros_descuentos = imp2cat + parseFloat($("#field-cta_ahorro_afp").val()) + parseFloat($("#field-anticipos").val()) + parseFloat($("#field-prestamos_ccaf").val()) + parseFloat($("#field-descuentos_prestamos").val()) + parseFloat($("#field-otros_descuentos").val());
            $("#field-total_otros_descuentos").val(!isNaN(otros_descuentos) ? otros_descuentos.toFixed(0) : 0);
            //Totales
            var total_haberes = subtotal_haberes + totalnoimponible;
            $("#field-total_haberes").val(!isNaN(total_haberes) ? total_haberes.toFixed(0) : 0);
            var total_descuentos = total_leyes_sociales + otros_descuentos;
            $("#field-total_descuentos").val(!isNaN(total_descuentos) ? total_descuentos.toFixed(0) : 0);
            total_descuentos = !isNaN(total_descuentos) ? total_descuentos.toFixed(0) : 0;
            var alcance = total_haberes - total_descuentos;
            $("#field-alcance_liquido").val(!isNaN(alcance) ? alcance.toFixed(0) : 0);
            $("#field-cotizacion_obligatoria").val(!isNaN(fondopensiones) ? fondopensiones.toFixed(0) : 0);
            //Calcular seguro sobrevivencia sis
            var tope_sis = parseFloat(dbuf['tope_sis']) * parseFloat(dbuf['uf']);
            var seguro_sis = subtotal_haberes <= tope_sis ? (parseFloat(dbuf['tasa_sis']) / 100) * subtotal_haberes : (parseFloat(dbuf['tasa_sis']) / 100) * tope_sis;
            $("#field-seguro_inv").val(!isNaN(seguro_sis) ? seguro_sis.toFixed(0) : 0);
            //Calculo de Aporte Indemnizacion 
            var aporte = empleado['empleado_casa'] == '1' ? total_imponible * (4.11 / 100) : 0;
            $("#field-indemnizacion").val(!isNaN(aporte) ? aporte.toFixed(0) : 0);
            //Cuenta Ahorro voluntario
            $("#field-cuenta_ahorro").val(!isNaN(apv) ? apv.toFixed(0) : 0);
            var afp = seguro_sis + apv + fondopensiones + aporte;
            $("#field-afp").val(!isNaN(afp) ? afp.toFixed(0) : 0);


            var seguro_cesantia_trabajador = empleado['empleado_casa'] == '1' ? 0 : segurocesantia;
            $("#field-seguro_cesantia_trabajador").val(!isNaN(seguro_cesantia_trabajador) ? seguro_cesantia_trabajador.toFixed(0) : 0);
            //Seguro cesantia trabajador
            s = empleado['tipo_contrato'] == 'FIJO' || empleado['tipo_contrato'] == 'POR OBRA' ? 3 : 2.4;
            s /= 100;
            var seguro_cesantia_empleador = empleado['empleado_casa'] == '1' ? 0 : subtotal_haberes < tope_afc ? subtotal_haberes * s : tope_afc * s;
            $("#field-seguro_cesantia_empleador").val(!isNaN(seguro_cesantia_empleador) ? seguro_cesantia_empleador.toFixed(0) : 0);
            var afc = seguro_cesantia_empleador + seguro_cesantia_trabajador;
            $("#field-afc").val(!isNaN(afc) ? afc.toFixed(0) : 0);


            //Cotización accidente de trabajo
            var tope_seguro = parseFloat(dbuf['tope_seguro_accidente']) * parseFloat(dbuf['uf']);
            var cot_accidente_trabajo = subtotal_haberes < tope_seguro ? (parseFloat(dbsca['factor']) / 100) * subtotal_haberes : (parseFloat(dbsca['factor']) / 100) * tope_seguro;
            $("#field-cot_accidente_trabajo").val(!isNaN(cot_accidente_trabajo) ? cot_accidente_trabajo.toFixed(0) : 0);
            var ips = empleado['id_caja'] !== '1' ? cot_accidente_trabajo : cot_accidente_trabajo - familiares;
            $("#field-total_ips,#field-mutualidad").val(!isNaN(ips) ? ips.toFixed(0) : 0);
            //Cotización obligatoria
            $("#field-cotizacion_obligatoria_salud").val(!isNaN(salud) ? salud.toFixed(0) : 0);
            //Adicional de salud
            $("#field-cotizacion_adicional").val(!isNaN(adicional_salud) ? adicional_salud.toFixed(0) : 0);
            //Total fonasa
            var total_fonasa = ips < 0 ? adicional_salud + salud + ips : adicional_salud + salud;     // Modificado el 30-09-2014 para resolver problema Asignación familiar mayor que cotización SCA
            $("#field-total_fonasa").val(!isNaN(total_fonasa) ? total_fonasa.toFixed(0) : 0);
            $("#field-impuesto_trabajador").val(!isNaN(imp2cat) ? imp2cat.toFixed(0) : 0);
            //Caja de compensación
            var caja_comp = empleado['sistema_salud_afiliado'] == '5' && empleado['id_caja'] !== '1' ? (total_imponible * (parseFloat(empleado['por_caja']) / 100)) - familiares : 0; //Esto es porque solo los adheridos a Fonasa cotizan en la caja;
            $("#field-caja_comp").val(!isNaN(caja_comp) ? caja_comp.toFixed(0) : 0);
            //Total leyes sociales
            // Modificado el 15-10-2014 para resolver problema total Fonasa en negativo (asignación familiar mayor que cotización de salud y SCA)
            var total_leyes_sociales;
            if (total_fonasa >= 0 && ips >= 0 && caja_comp >= 0) {
                total_leyes_sociales = afp + afc + ips + total_fonasa + caja_comp;
            }
            else if (total_fonasa >= 0 && ips < 0) {
                total_leyes_sociales = afp + afc + total_fonasa;
            }
            else if (total_fonasa < 0) {
                total_leyes_sociales = afp + afc;
            }
            else {
                total_leyes_sociales = afp + afc + ips + total_fonasa;
            }
            $("#field-total_leyes_sociales").val(!isNaN(total_leyes_sociales) ? total_leyes_sociales.toFixed(0) : 0);

            //Costo total empleador
            var costo_total_empleador = total_leyes_sociales + otros_descuentos + alcance;
            $("#field-costo_total_empleador").val(!isNaN(costo_total_empleador) ? costo_total_empleador.toFixed(0) : 0);
            $("#field-total_impuestos").val(!isNaN(imp2cat) ? imp2cat.toFixed(0) : 0);

        }
    }
</script>
