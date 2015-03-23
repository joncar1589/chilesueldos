<div class="row col-xs-4" align="center">
<form action="<?= base_url('registro/forget') ?>" method="post" onsubmit="return validar(this)" role="form" class="form-horizontal">
    <?= !empty($_SESSION['msj'])?$_SESSION['msj']:'' ?>
    <?= !empty($msj)?$msj:'' ?>
    <?= input('email','Email','email') ?>
    <button type="submit" class="btn btn-success">Recuperar contraseña</button>
</form>
</div>