//Libreria javascript Por ing jonathan Cardozo Version 1.0

var fileid = 1;
var files = [];
var obligatorios = [];
var path_lib = 'lib/';

$(document).ready(function(){
	def();
	mover_foot();

});

	function def()
	{
		$("#header_logo").click(function(){document.location.href="./"});
		$("#txtlook").keyup(function(e){bindkey(e)});
		$(".menu_horizontal_li_nactive, .menu_horizontal_li_active").click(function(){
		$(".menu_horizontal_li_active").attr("class","menu_horizontal_li_nactive");
		$(document).click(function(){$(".BusquedaAjax").remove();});
		$(this).attr("class","menu_horizontal_li_active");});

		$(".txtbus").focus(function(){if($(this).val()==$(this).attr('name')){$(this).attr('value',''); $(this).css('color','black');}});

		$(".txtbus").blur(function(){var str = $(this).val().replace(/^\s*|\s*$/g,"");if(str==''){$(this).attr('value',$(this).attr('name')); $(this).css('color','gray');}});

		$("body a").click(function(){if($(this).hasClass('not'))wait();});
		
		//$('.table').dataTable({"sDom": '<"top">rt<"bottom"><"clear">','iDisplayLength': 100});
		
		$("input[type='date']").click(function(){if($("#calendarDiv").css("display")!='block'){alert($("#calendarDiv").css("display")); displayCalendar(document.getElementById($(this).attr('id')),'yyyy/mm/dd',this)}});
		$("input[type='date']").focus(function(){if($("#calendarDiv").css("display")!='block'){displayCalendar(document.getElementById($(this).attr('id')),'yyyy/mm/dd',this)}});
		$("input[type='date']").attr('size','10');
		$("input[type='date']").keyup(function(){$(this).attr('value',$(this).attr('value').substring(0,$(this).attr('value').length-1))});
		//$("input[type='number']").limitkeypress();
	        //$(function() {$(".pestana").organicTabs();});
		$(".checkall").click(function(){
			if($(this).attr('id')=='all')
			{
				
				if($(this).attr('checked') == 'checked')
					$(".check").attr('checked',true);
					
				else
					$(".check").attr('checked',false);
			}
		});

		
	}

	function reloj()
	{
		var d = new Date();
		h = d.getHours()
		i = (d.getMinutes()<=9)?"0"+d.getMinutes():d.getMinutes();
		s = (d.getSeconds()<=9)?"0"+d.getSeconds():d.getSeconds();

		$(".reloj").html(h+":"+i+":"+s);
	
		setTimeout(reloj,1000);
	}

	function add_css()
	{
		$("head").append('<link href="'+path_lib+'css/emergente.css" rel="stylesheet" type="text/css">');
		$("head").append('<link href="'+path_lib+'css/bootstrap.css" rel="stylesheet" type="text/css">');
		$("head").append('<link href="style.css" rel="stylesheet" type="text/css">');
		$("head").append('<link rel="stylesheet" href="'+path_lib+'css/pestana.css">');
	}

	
	function suptxt(txt)
	{
		$("#txtlook").attr("value",txt);
	}

	function bindkey(e)
	{
		

		if(e.which==27)$(".BusquedaAjax").remove();
		else
		{
		var st = $("#txtlook").val();
		if(st.length>2)
		{
			var p = $("#txtlook").offset();
			
			$("header").append('<div class="BusquedaAjax"></div>');
			$(".BusquedaAjax").css({'top':(p.top+23)+"px",'left':p.left+"px"});
			var data = "type=buscar&palabra="+$("#txtlook").val();
			$(".BusquedaAjax").html('Cargando...');
			ajax(data,1,".BusquedaAjax");
		}
		}
	}

	function mover_foot()
	{
				
	}
	
	function ajax(data,x,label,callback,url)
	{
		
		if(x==null || x==undefined)x=true;
		else x = false;
		if(url===undefined)url = '';
		
		if(x)
			try{emergente('Procesando por favor espere...');}catch(e){alert(e.message);}
		else
			$(label).html('Procesando por favor espere...');

		$.ajax({
				url:url,
				type:"post",
				contentType:"application/x-www-form-urlencoded",
				data:data,
				success:function(data){
                                    if(callback!==undefined)callback(data);
                                    
                                    else{
					if(x)
					try{emergente(data);}catch(e){alert(e.message);}
					else
					$(label).html(data);
                                    
					if(data.search('success_message')>0){
						emergente('Los datos fueron introducidos con exito');
						$("#btn-close").click(function(){document.location.href=window.top.location;});
					}
                                    }
				}
		});
	}
	
	function getajax(url)
	{
		emergente('Procesando por favor espere...');
		$.ajax({
				url:url,
				type:"get",
				success:function(data){
					emergente(data,undefined,undefined,undefined,window.location.href);
				}
		});
	}

	function emergente(data,xs,ys,boton,header){


		var x = (xs==undefined)?(window.innerWidth/2)-325:xs;
		var y = (ys==undefined)?(window.innerHeight/2):ys;
		var b = (boton==undefined || boton)?true:false;
		var h = (header==undefined)?'Mensaje':header;
		if($(".modal").html()==undefined){
		$('body,html').animate({scrollTop: 0}, 800);
		var str = '';
                var str = '<!-- Modal -->'+
                '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">'+
                '<div class="modal-dialog">'+
                '<div class="modal-content">'+
                '<div class="modal-header">'+
                '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'+
                '<h4 class="modal-title" id="myModalLabel">'+h+'</h4>'+
                '</div>'+
                '<div class="modal-body">'+
                data+
                '</div>'+
                '<div class="modal-footer">'+
                '<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>'+
                '</div>'+
                '</div><!-- /.modal-content -->'+
                '</div><!-- /.modal-dialog -->'+
                '</div><!-- /.modal -->';
                $("body").append(str);
                $("#myModal").modal("toggle");
		}
		else
		{
			$(".modal-body").html(data);
                        if($("#myModal").css('display')=='none')
                            $("#myModal").modal("toggle");
		}
	}
	function val_email(email)
	{
		var re  = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/; 
		return !re.test(email)?false:true; 
	}
	
	var val = true;
	var msj = '';
	function validar(form)
	{
		val = true;
		$(form).find(':input').each(function(){
			if($(this).attr('data-val')=="required" && $(this).val()=='')
				{$(this).css({'border-color': '#B94A48'}); val=false;  msj = msj+'El campo <b>'+this.name+'</b> debe ser completado<br/>';}
			if($(this).attr('type')=='email' && !val_email($(this).val()))
				{$(this).css({'border-color': '#B94A48'}); val=false; msj = msj+'El campo <b>'+this.name+'</b> debe tener un email valido<br/>';}
			if($(this).attr('data-equal')!==undefined && $($(this).attr('data-equal')).val() != $(this).val())
				{$(this).css({'border-color': '#B94A48'}); $($(this).attr('data-equal')).css({'border-color': '#B94A48'}); val=false; msj = msj+'El campo <b>'+this.name+'</b> debe ser igual a '+$($(this).attr('data-equal')).attr('name')+"<br/>";}
		});
		if(!val)$(form).before('<p class="alert alert-danger">'+msj+'</p>');
		return !val?false:true;
	}
	var post = '';
	function send(form,url)
	{
		if(validar(form)){
		$(form).find(':input').each(function(){
			post = post+this.name+"="+$(this).val()+"&";
		});
		ajax(post,undefined,undefined,undefined,url);
		}
		return false;
	}
        
        function error(msj)
        {
            return '<div class="alert alert-danger">'+msj+'</div>';
        }
        function success(msj)
        {
            return '<div class="alert alert-success">'+msj+'</div>';
        }
        
        function JSONsend(form,urlValidation,url)
        {
            if(validar(form)){
                $(form).find(':input').each(function(){
			post = post+this.name+"="+$(this).val()+"&";
		});
                ajax(post,undefined,undefined,function(data){
                    data = data.replace('<textarea>','');
                    data = data.replace('</textarea>','');
                    var x = JSON.parse(data);
                    if(x.success)
                    {
                        ajax(post,undefined,undefined,function(data){
                        data = data.replace('<textarea>','');
                        data = data.replace('</textarea>','');
                        var x = JSON.parse(data);
                        if(x.success)
                        emergente(success(x.success_message));
                        else
                        emergente(error('ocurrio un error en la operacion'));
                        },url);
                    }
                    else
                        emergente(error(x.error_message));
                },urlValidation);
            }
            return false;
        }

	function limpiar(obligatorio)
	{
		
		for(var i=0;i<obligatorio.length;i++)
		{
			
			$("#"+obligatorio[i]).attr('value','')
		}
	}

	function procesardata(obligatorio)
	{
		var data = '';
		for(var i=0;i<obligatorio.length;i++)
		{
			data+= obligatorio[i]+"="+$("#"+obligatorio[i]).val()+'&';
		}

		var filedata = 'file=';
		for(var i=0;i<files.length;i++)
		{
			filedata+= $("#"+files[i].attr('id')+" img").attr('id')+";";
		}
		filedata += '&';		
		
		var p = 'datos=';
		for(var i=0;i<obligatorio.length-1;i++)
		{
			p+= obligatorio[i]+';';
		}
		p+= obligatorio[obligatorio.length-1];
		
		return data+filedata+p;
	}
	
	function ajaxupload(url,id)
	{
		 var button = $(id);

    		 new AjaxUpload(id, 
		 {
        	 action: 'controller/'+url,
        	 onSubmit : function(file , ext)
		 {
       		
		 	if (! (ext && /^(jpg|png|jpeg|gif|doc|docx|pdf|odt|rar|zip|tar.gz|tar.bz|tar.bz2)$/.test(ext)))
			{
            	 		emergente('Error: Archivo no soportado');
            			return false;
        	 	} 
			else 
		 	{
            	       		button.text('Cargando');
				//$("#guardar").attr("disabled", "disabled");
            	       		this.disable();
			
				fileid += 1;
				$("#files").append('<div id="file'+fileid+'" class="upfile">Adjuntar Archivo</div>');	
				ajaxupload('ajax.php?type=addfile','#file'+fileid);
				files.push(button);		
        	 	}
        	 },
        	onComplete: function(file, response){
			button.html(response);
        	}  
    		});
		
	}	

	function rvfile(file)
	{
		ajax('type=rvtmpfile&tmpfile='+file,false);

		for(var i=0; i<files.length; i++)
		{
			if($("#"+files[i].attr('id')+" img").attr('id') == file)
			files[i].remove();
			files.splice(i,1);
		}
	}

	function buscar()
	{
		document.location.href="?busqueda&sk="+$("#txtlook").val();
		$(".BusquedaAjax").remove();
		return false;
	}

	function frame(url)
	{
		emergente('<iframe width="500" height="700" border="0" src="'+url+'" frameborder="0" SCROLLING="no"></iframe>',undefined,800);
	}

	function enviar(type)
	{
		
		if(validar(obligatorios))
		{		
			var data = "type="+type+"&"+procesardata(obligatorios);
			ajax(data);
		}
	}

	function wait()
	{
		emergente('Procesando por favor espere...');
	}


	function isNumberKey(o)
	{
		
		var expresion = /^[+]?\d*$/ ;
		var re = new RegExp(expresion);
		
		
		if(re.test($(o).val()))
		return true;
 
		return false;
	}
