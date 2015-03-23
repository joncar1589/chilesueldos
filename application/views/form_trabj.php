<?php $this->load->view('includes/nav'); ?>
<link type="text/css" rel="stylesheet" href="<?= base_url('assets/grocery_crud/css/ui/simple/jquery-ui-1.10.1.custom.min.css') ?>" />
<link type="text/css" rel="stylesheet" href="<?= base_url('assets/grocery_crud/css/jquery_plugins/jquery-ui-timepicker-addon.css') ?>" />
<script src="<?= base_url('assets/grocery_crud/js/jquery_plugins/ui/jquery-ui-1.10.3.custom.min.js') ?>"></script>
<script src="<?= base_url('assets/grocery_crud/js/jquery_plugins/jquery-ui-timepicker-addon.js') ?>"></script>
<script src="<?= base_url('assets/grocery_crud/js/jquery_plugins/ui/i18n/datepicker/jquery.ui.datepicker-es.js') ?>"></script>

<link type="text/css" rel="stylesheet" href="<?= base_url('assets/chosen/chosen.css') ?>" />
<script src="<?= base_url('assets/chosen/chosen.jquery.js') ?>"></script>
<script src="<?= base_url('assets/chosen/chosen.proto.js') ?>"></script>
<section style="text-align:center; margin:0 auto 0 auto; width:410px;">
	<h3>Seleccione la empresa</h3>
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
				<script>
					function redireccion(){
                                            var empresa = $('#empresa').find(":selected").val();
                                            document.location.href="http://www.chilesueldos.cl/empresa/empleados/1/"+empresa
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