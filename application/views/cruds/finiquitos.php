<?= $output ?>
<script src="<?= base_url('js/calcular_vacaciones_proporcionales.js') ?>"></script>
<script>
    var empleado,vacaciones,liquidaciones;
    <?php
        $str = 'var feriados = [';
        foreach($this->db->get('feriados')->result() as $e)
            $str.="'".$e->fecha.'\',';
        echo str_replace(']','];',str_replace(',]','];',$str.']'));
    ?>
    $(document).ready(function(){
        $("#field-empleado").change(function(){
            $("#field-descripciones").val(0);
            $("#field-descripciones_0_ano").val('');
            $("#field-descripciones_0_dias").val('');
            $("#field-descripciones_0_monto").val('');
            if($(this).val()!=''){
                empleado = undefined;
                vacaciones = undefined;
                liquidaciones = undefined;
                
                $.post('<?php echo base_url("empresa/get_salario") ?>',{id:$(this).val()},function(data){
                    empleado = JSON.parse(data);
                    if(empleado['fecha_ingreso2']!=undefined)empleado['fecha_ingreso'] = empleado['fecha_ingreso2'];
                    calcular_vacaciones_proporcionales();
                });
                $.post('<?php echo base_url("empresa/get_liquidaciones") ?>',{id:$(this).val()},function(data){
                    liquidaciones = JSON.parse(data);
                    if(liquidaciones['id']=='')
                    {
                        alert("El trabajador no tiene vacaciones vencidas o no cumple con los requisitos para optarlas");
                        liquidaciones = undefined;
                    }
                    calcular_vacaciones_proporcionales();
                });
                $.post('<?php echo base_url("empresa/get_feriados") ?>',{id:$(this).val()},function(data){
                    vacaciones = JSON.parse(data);
                    calcular_vacaciones_proporcionales();
                });
            }
        });        
        $("#field-fecha").change(function(){calcular_vacaciones()});
    });
    
    function calcular_vacaciones(){
            var fecha_inicio = empleado['fecha_ingreso'];
            fecha_inicio = fecha_inicio.split("-");
            var valor_dia = 15/12;
            var fecha_egreso = $("#field-fecha").val();
            fecha_egreso = fecha_egreso.split("/");
            
            dias_habiles = parseFloat(calcular_vacaciones_proporcionales());            
            console.log(dias_habiles);
            //Calculamos los dias inabiles                
            dias_habiles += sumar_dias(dias_habiles);            
            //Validamos si tenia dias en el mes ya trabajados
            dias_habiles += fecha_egreso[0]>fecha_inicio[2]?((valor_dia/30)*(parseInt(fecha_egreso[0])-parseInt(fecha_inicio[2]))):0;
            dias_habiles += vacaciones['periodo']=='' || parseInt(vacaciones['pendientes'])>0?0:parseInt(vacaciones['pendientes']);                            
            decimales = dias_habiles.toString();                
            decimales = decimales.split(".");                
            decimales = decimales[0]+".5";                                
            dias_habiles = dias_habiles<parseFloat(decimales)?dias_habiles.toFixed(1):dias_habiles.toFixed(0);
            //Se calcula el monto
            var y = 0;
            var sueldo = 0;
            var bonos = 0;//Se toman las ultimas 3 liquidaciones agregadas
            for(var i=0;i<liquidaciones.length;i++)
            {
                if(y<3)
                {
                    y++;
                    sueldo+= parseInt(liquidaciones[i]['sueldo_base']);                        
                    bonos+= parseInt(liquidaciones[i]['comisiones'])+parseInt(liquidaciones[i]['bonos']);
                }
            }
            //Si hubo bonos se calcula en funcion del promedio de resto se toma solo el sueldo base
            if(bonos > 0)
                monto = (((sueldo+bonos)/y)/30)*dias_habiles;
            else{
                monto = (parseInt(empleado['sueldo_base'])/30)*dias_habiles;
            }
            if(!isNaN(monto)){
                //Se le agrega el valor al finiquito
                $("#field-descripciones_0_").val(2);
                $("#field-descripciones_0_ano").val(fecha_egreso[2]);
                $("#field-descripciones_0_dias").val(dias_habiles);
                $("#field-descripciones_0_monto").val(monto.toFixed(0));

                if($("#field-causal").val()=='14')
                    sumar_anos_servicio(1);
            }
            
            if(dias_habiles==0 && $("#field-causal").val()=='14')                
                    sumar_anos_servicio(0)
    }
    
    

    function sumar_dias(dias)
    {    
    var inicio = $("#field-fecha").val();
    inicio = inicio.split("/");      
    hoy = new Date(inicio[2]+"-"+inicio[1]+"-"+inicio[0]);
    i=0;
    var y=0;    
    hoy.setTime(hoy.getTime()+24*60*60*1000); // añadimos 1 día      
    hoy.setTime(hoy.getTime()+24*60*60*1000); // añadimos 1 día      
    while (i<parseInt(dias)) {            
      var m = hoy.getMonth()<10?'0'+(hoy.getMonth()+1):hoy.getMonth()+1;
      var d = hoy.getDate()<10?'0'+hoy.getDate():hoy.getDate();      
      var str = hoy.getFullYear()+"-"+m+"-"+d;            
      str = feriados.indexOf(str);
      if (hoy.getDay() != 6 && hoy.getDay() != 0 && str==-1)
        i++;  
      else y++;      
      hoy.setTime(hoy.getTime()+24*60*60*1000); // añadimos 1 día      
    }
    return y;
    }
    
    function sumar_anos_servicio(id){                    
        fecha_egreso = $("#field-fecha").val();
        fecha_egreso = fecha_egreso.split("/");
        fecha_inicio = empleado['fecha_ingreso'];
        fecha_inicio = fecha_inicio.split("-");        
        aux = fecha_inicio[0];
        fecha_inicio[0] = fecha_inicio[2];
        fecha_inicio[2] = aux;
        field = "#field-descripciones_"+id+"_";
        
        anos = restaFechas(fecha_inicio,fecha_egreso,true)/30/12; //calcula la cantidad de años transcurridos
        anos = anos.toFixed(1);//se redondean los decimales
        if(anos>=1){
            addFieldProduct('#descripciones_input_box',descripcionesFieldProduct,'descripciones');
            $(field).val(4);
            anos = anos>11?11:anos;
            ano = anos;
            anos = anos.toString();
            anos = anos.split('.');
            if(anos.length>1){
            anos[0] = parseInt(anos[0]);
            anos[1] = parseInt(anos[1]);
            }
            else anos[1] = 0;            
            if(anos[1]>=6)
                anos[0]+=1;                        
            //Calculo del salario promedio de los ultimos 3 meses por lo que se toma de la liquidacion guardada.
            if(liquidaciones.length>0){
                salario = 0;
                for(i in liquidaciones){
                    salario+=parseInt(liquidaciones[i]['total_haberes']);
                }
                salario = salario/liquidaciones.length;
                salario = salario.toFixed(0);
                salario = parseInt(salario);
            }//Si no tiene liquidaciones se toma de el contrato
            else salario = parseInt(empleado['sueldo_base']);
            
            $(field+"ano").val(ano);
            $(field+"dias").val(anos[0]);
            $(field+"monto").val(salario*anos[0]);
        }
    }        
</script>
