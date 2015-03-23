<?= $output ?>
<script>
$(document).ready(function()
{
    
    var tope_colacion = <?php echo $this->querys->ajustes->tope_colacion ?>;
    var tope_locomocion = <?php echo $this->querys->ajustes->tope_locomocion ?>;
    $( "#field-fecha_nacimiento" ).datepicker( "option", "yearRange", "-99:+0" );
    $( "#field-fecha_nacimiento" ).datepicker( "option", "maxDate", "+0m +0d" );
    $("#dependiente_input_box input[type='radio']").click(function(){
        if($(this).val()=='1' && $("#field-empresa").val()!='')
        {
            $("#field-rut").val('Consultando Rut');
            $.post('<?php echo base_url('empresa/getRutEmpresa') ?>',{id:$("#field-empresa").val()},function(data){$("#field-rut").val(data)});            
        }
    });
    
    $("#field-bono_colacion, #field-bono_locomocion, #field-sueldo_base").change(function(){
        var sueldo = parseFloat($("#field-sueldo_base").val());
        var colacion = parseFloat($("#field-bono_colacion").val());
        var locomocion = parseFloat($("#field-bono_locomocion").val());
        if(!isNaN(sueldo))
        {
            var tcol = (tope_colacion*sueldo)/100;
            var tloc = (tope_locomocion*sueldo)/100;
            
            if(!isNaN(colacion) && colacion>tcol)
                alert("UD esta asignando una cantidad NO prudente a el bono de colacion, asumiendo ud la responsabilidad  frente a futuras revisiones de la Dirección de Trabajo");
            if(!isNaN(locomocion) && locomocion>tloc)
                alert("UD esta asignando una cantidad NO prudente a el bono de locomocion, asumiendo ud la responsabilidad  frente a futuras revisiones de la Dirección de Trabajo");
        }
    })
    if($("#field-tipo_contrato").val()!='POR OBRA')$("#descripcion_tipo_contrato_field_box").hide();
    if($("#field-tipo_contrato").val()!='FIJO')$("#termino_contrato_field_box").hide();
    $("#field-tipo_contrato").change(function(){
        if($(this).val()=='FIJO')$("#termino_contrato_field_box").show();
        else if($(this).val()=='POR OBRA')$("#descripcion_tipo_contrato_field_box").show();
        else $("#termino_contrato_field_box,#descripcion_tipo_contrato_field_box").hide();
    });
    //Lo siguiente es para ocultar los campos relacionados con los horarios cuando no este seleccionada la opcion "Normal" 
    //del combobox Seleccionar tipo Jornada.
    //Fecha modificacion:03/12/2014 Por: Victor Alarcón
    if($("#field-tipo_horario").val()!='Normal')$("#horas_semanales_field_box, #dias_semana_field_box, #hora_entrada_field_box, #horariosespeciales_field_box,#otros_acuerdos_horarios_field_box").hide();
    $("#field-tipo_horario").change(function(){
        if($(this).val()=='Normal')$("#horas_semanales_field_box, #dias_semana_field_box, #hora_entrada_field_box, #horariosespeciales_field_box,#otros_acuerdos_horarios_field_box").show();
        else $("#horas_semanales_field_box, #dias_semana_field_box, #hora_entrada_field_box, #horariosespeciales_field_box, #otros_acuerdos_horarios_field_box").hide();
    });
    //Lo siguiente es para ocultar los campos subir comprobante pdf y plan de isapre cuando este seleccionada la opcion "Fonasa" 
    //o no se haya seleccionado nada en el combobox Seleccionar sistema de salud.
    //Fecha modificacion:03/12/2014 Por: Victor Alarcón
    if($("#field-sistema_salud_afiliado").val()=='')$("#plan_isapre_field_box, #comprobante_pdf_isapre_field_box").hide();
    if($("#field-sistema_salud_afiliado").val()=='5')$("#plan_isapre_field_box, #comprobante_pdf_isapre_field_box").hide();
    $("#field-sistema_salud_afiliado").change(function(){
        if($(this).val()!=='5' && $(this).val()!=='')$("#plan_isapre_field_box, #comprobante_pdf_isapre_field_box").show();
        else $("#plan_isapre_field_box, #comprobante_pdf_isapre_field_box").hide();
    });
    
    if($("#field-afp_afiliado").val() == '')$("#check_afc_field_box").hide();
    $("#field-afp_afiliado").change(function(){
        if($(this).val()!=='' && $(this).val()!== '')$("#check_afc_field_box").show();
        else $("#check_afc_field_box").hide();
    });
    if($("#field-tipo_trabajador").val()!=='Pensionado')$("#check_afp_field_box").hide();
    $("#field-tipo_trabajador").change(function(){
        if($(this).val() == 'Pensionado' && $(this).val()!=='')$("#check_afp_field_box").show();
       else $("#check_afp_field_box").hide();
    });
    
    $("#field-tipo_trabajador").change(function(){
        if($(this).val() == 'Pensionado')$('input:radio[name=check_afp]')[1].checked = true;
        else $('input:radio[name=check_afp]')[0].checked = true;
    });
    
    $("#field-tipo_trabajador").change(function(){
        if($(this).val() == 'Activo' )$('input:radio[name=check_afp]')[0].checked = true;
    });
    
});
</script>