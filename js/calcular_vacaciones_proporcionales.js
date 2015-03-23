function calcular_vacaciones_proporcionales()
{        
    if(empleado!=undefined && vacaciones!=undefined && liquidaciones!=undefined && $("#field-fecha").val()!='')
    {                      
        //Calcular vacaciones prolongadas.
        var valor_dia = 15/12;
        var fecha_egreso = $("#field-fecha").val();
        fecha_egreso = fecha_egreso.split("/");
        //fecha_egreso = new Date(fecha_egreso[2]+"-"+fecha_egreso[1]+"-"+fecha_egreso[0])                                
        
        //dias_antes = $("#field-dias_antes_ingreso").val();
        dias_antes = empleado['dias_antes_ingreso'];
        
        var fecha_inicio = dias_antes==undefined || dias_antes=='' || parseInt(dias_antes)<=0?empleado['fecha_ingreso']:empleado['fecha_creacion'];                        
        fecha_inicio = fecha_inicio.split("-");
        periodo = fecha_inicio[0];
        
        /*var periodo = vacaciones['periodo']==''?fecha_inicio[0]:vacaciones['periodo'];        
        if(vacaciones['periodo']!='')
        {
           fecha_inicio = periodo+"-"+fecha_inicio[1]+"-"+fecha_inicio[2];        
           fecha_inicio = fecha_inicio.split("-");
        } */            
        numMeses = parseInt(fecha_egreso[2])*12 + parseInt(fecha_egreso[1]) - (parseInt(periodo)*12 + parseInt(fecha_inicio[1]));
            if (fecha_egreso[2]<periodo){
            numMeses = numMeses - 1;
        }
        dias_sobrantes = 0;
        if(fecha_egreso[0]>fecha_inicio[2]){//Se verifica si tiene dias que no suman un mes
            mes_sobrante = new Date(fecha_inicio[0]+"-"+fecha_inicio[1]+"-"+fecha_inicio[2]);
            //mes_sobrante.setTime(mes_sobrante.getTime()+numMeses+2*30*24*60*60*1000);
            ini = fecha_inicio[2]+"/"+fecha_egreso[1]+"/"+fecha_egreso[2];            
            fin = fecha_egreso[0]+"/"+fecha_egreso[1]+"/"+fecha_egreso[2];
            dias_sobrantes = restaFechas(ini,fin);
            //numMeses-=1;
        }              
        if(numMeses>0){
            var dias_habiles = (numMeses*valor_dia);
            //Si tenemos dias sobrantes los sumamos
            dias_habiles += dias_sobrantes==0?0:dias_sobrantes*0.04167;
            dias_habiles += dias_antes==undefined || dias_antes=='' || parseInt(dias_antes)<=0?0:parseInt(dias_antes);            
            dias_habiles -= parseInt(vacaciones['suma_otorgados'])>0?parseInt(vacaciones['suma_otorgados']):0;
            //dias_habiles += parseInt(empleado['dias_antes_ingreso'])>0?parseInt(empleado['dias_antes_ingreso']):0;
            return dias_habiles.toFixed(2);
        }

        else
            return dias_antes==undefined || dias_antes==''?0:dias_antes;        
    }
    else return 0;
}

restaFechas = function(aFecha1,aFecha2,array)
{    
    if(!array || array==undefined){
        aFecha1 = aFecha1.split('/'); 
        aFecha2 = aFecha2.split('/'); 
    }        
    var fFecha1 = Date.UTC(aFecha1[2],aFecha1[1]-1,aFecha1[0]); 
    var fFecha2 = Date.UTC(aFecha2[2],aFecha2[1]-1,aFecha2[0]); 
    var dif = fFecha2 - fFecha1;
    var dias = Math.floor(dif / (1000 * 60 * 60 * 24)); 
    
    return dias;
}