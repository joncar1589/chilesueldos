<?= $output ?>
<script src="<?= base_url('js/calcular_vacaciones_proporcionales.js') ?>"></script>
<script>
<?php
    $str = 'var feriados = [';
    foreach($this->db->get('feriados')->result() as $e)
        $str.="'".$e->fecha.'\',';
    echo str_replace(']','];',str_replace(',]','];',$str.']'));
?>
var empleado, vacaciones, liquidaciones;

$(document).ready(function()
{    
    $("#field-dias_legal,#field-dias_anterior,#field-total,#field-dias_progresivo,#field-pendientes").attr('readonly',false);
    if($("#field-periodo").val()==''){
    $("#field-dias_otorgados,#field-dias_progresivo").val(0);
    $("#field-pendientes").val(0);
    sumar();
    }
    $("#field-empleado").change(function(){        
        if($(this).val()!=''){
            empleado = undefined;
            vacaciones = undefined;
            liquidaciones = undefined;

            $.post('<?php echo base_url("empresa/get_salario") ?>',{id:$(this).val()},function(data){
                empleado = JSON.parse(data);
                $("#field-fecha").trigger('change');
            });
            $.post('<?php echo base_url("empresa/get_liquidaciones") ?>',{id:$(this).val()},function(data){
                liquidaciones = JSON.parse(data);
                if(liquidaciones['id']=='')
                {
                    alert("El trabajador no tiene vacaciones vencidas o no cumple con los requisitos para optarlas");
                    liquidaciones = undefined;
                }
                $("#field-fecha").trigger('change');
            });
            <?php $id = !empty($id)?$id:''; ?>
            $.post('<?php echo base_url("empresa/get_feriados/".$id) ?>',{id:$(this).val()},function(data){
                vacaciones = JSON.parse(data);
                $("#field-fecha").trigger('change');
            });
            
        }
    });    
    $("#field-dias_otorgados,#field-dias_legal,#field-dias_progresivo,#field-pendientes").change(function(){$("#field-fecha").trigger('change');});
    $("#field-fecha").change(function(){
        feriado = parseFloat(calcular_vacaciones_proporcionales());
        $("#field-dias_legal").val(feriado.toFixed(0)); get_date_return(); sumar();
    });
    $("#field-dias_antes_ingreso").change(function(){        
        $("#field-fecha").trigger('change');
    });
    
    if($("#field-empleado").val()!=''){
        $("#field-empleado").trigger('change');
    }
});

function sumar()
{
    var total = parseInt($("#field-dias_legal").val()) + parseInt($("#field-dias_progresivo").val());
    $("#field-total").val(total); 
    var pendientes = parseInt($("#field-total").val()) - parseInt($("#field-dias_otorgados").val())
    $("#field-pendientes").val(pendientes);
}

function get_date_return()
{    
    var inicio = $("#field-fecha").val();
    inicio = inicio.split("/");      
    hoy = new Date(inicio[2]+"-"+inicio[1]+"-"+inicio[0]);
    i=0;
    while (i<parseInt($("#field-dias_otorgados").val())) {
      hoy.setTime(hoy.getTime()+24*60*60*1000); // añadimos 1 día
      var m = hoy.getMonth()<10?'0'+(hoy.getMonth()+1):hoy.getMonth()+1;
      var d = hoy.getDate()<10?'0'+hoy.getDate():hoy.getDate();      
      var str = hoy.getFullYear()+"-"+m+"-"+d;
      str = feriados.indexOf(str);
      if (hoy.getDay() != 6 && hoy.getDay() != 0 && str==-1)
        i++;  
    }
    var m = hoy.getMonth()+1;
    fecha = hoy.getDate()+ '/' + m + '/' + hoy.getFullYear();
    $("#field-fecha_final").val(fecha);
}
</script>