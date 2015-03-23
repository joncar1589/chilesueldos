<?php $this->load->view('includes/nav'); ?>
<link type="text/css" rel="stylesheet" href="<?= base_url('assets/grocery_crud/css/ui/simple/jquery-ui-1.10.1.custom.min.css') ?>" />
<link type="text/css" rel="stylesheet" href="<?= base_url('assets/grocery_crud/css/jquery_plugins/jquery-ui-timepicker-addon.css') ?>" />
<script src="<?= base_url('assets/grocery_crud/js/jquery_plugins/ui/jquery-ui-1.10.3.custom.min.js') ?>"></script>
<script src="<?= base_url('assets/grocery_crud/js/jquery_plugins/jquery-ui-timepicker-addon.js') ?>"></script>
<script src="<?= base_url('assets/grocery_crud/js/jquery_plugins/ui/i18n/datepicker/jquery.ui.datepicker-es.js') ?>"></script>

<link type="text/css" rel="stylesheet" href="<?= base_url('assets/chosen/chosen.css') ?>" />
<script src="<?= base_url('assets/chosen/chosen.jquery.js') ?>"></script>
<script src="<?= base_url('assets/chosen/chosen.proto.js') ?>"></script>
<script>
$(function(){
    $(".datetime-input").datetimepicker({
        timeFormat:"",
                dateFormat: "mm/yy",
                showButtonPanel: false,
				showTime:false,
                changeMonth: true,
                changeYear: true,
    });

    $(".datetime-input-clear").button();

    $(".datetime-input-clear").click(function(){
            $(this).parent().find(".datetime-input").val("");
            return false;
    });	

});
</script>
<section style="text-align:center; margin:0 auto 0 auto; width:410px;">
	<h3>Seleccione la empresa y el mes</h3>
	<br>
	<br>
<div class="container" style="text-align:center;">
	<div class="row">
		<div class='col-sm-4'>
			<div class="form-group">
				<div>
					<select data-placeholder="Seleccione una Empresa" style="width:350px;" class="chosen-select" id="empresa" name="empr">
                                            <?php
                                            foreach ($empresas as $i => $empresa){
                                               echo '<option value="',$i,'">',$empresa,'</option>';					   
                                            }
                                            ?>
					</select>
					<script>$(".chosen-select").chosen()</script>
				</div>
				<br>
				<br>
				<div class='input-group date' id='datetime' >
					<input type="text" class="form-control datetime-input" id="fecha">
					<span class="input-group-addon">
						<span class="glyphicon glyphicon-calendar"></span>
					</span>
				</div>
				<script>
					function redireccion(){
                                            var fecha = $("#fecha").val().replace(" ","");
                                            var empresa = $('#empresa').find(":selected").val();
                                            fecha = fecha.replace("/","-");
                                            document.location.href="http://www.chilesueldos.cl/empresa/liquidaciones/"+fecha+"/"+empresa
					}
				</script>
				<br>
				<br>
				<div>
					<input type="submit" class="btn btn-primary btn-lg" value="Mostrar" onClick="return redireccion()">
				</div>
			</div>
		</div>
	</div>
</div>
</form>
</section>