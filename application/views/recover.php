<div class="row col-xs-4" align="center">
<form action="<?= base_url('registro/forget') ?>" method="post" onsubmit="return validar(this)" role="form" class="form-horizontal">
    <?= !empty($msj)?$msj:'' ?>
    <input type="email" name="email" id="email" data-val="required" class="form-control" value="<?= $_SESSION['email'] ?>" readonly><br/>
    <input type="password" class="form-control" name="pass" id="pass" placeholder="Nuevo Password"><br/>
    <input type="password" class="form-control" name="pass2" id="pass2" placeholder="Repetir Password"><br/>
    <input type="hidden" name="key" value="<?= $key ?>">
    <button type="submit" class="btn btn-success">Recuperar contraseña</button>
</form>
    </div>