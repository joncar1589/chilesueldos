<? if(empty($_SESSION['user'])): ?>
<? if(!empty($msj))echo $msj ?>
<? if(!empty($_SESSION['msj']))echo $_SESSION['msj'] ?>
<form role="form" class="form-horizontal well" action="<?= base_url('main/login') ?>" onsubmit="return validar(this)" method="post">
   <h1>Iniciar sessión</h1>
   <?= input('usuario','Email','text') ?>
   <?= input('pass','Contraseña','password') ?>
   <input type="hidden" name="redirect" value="<?= empty($_GET['redirect'])?base_url('panel'):base_url($_GET['redirect']) ?>">
   <div><input type="checkbox" name="remember" value="1" checked> Recordar contraseña</div>
   <div align="center"><button type="submit" class="btn btn-success">Ingresar</button>
   <a class="btn btn-link" href="<?= base_url('registro/index/add') ?>">Registrate</a><br/>
   <a class="btn btn-link" href="<?= base_url('registro/forget') ?>">¿Olvidó su contraseña?</a>
   <div align="center" style="margin-top:10px"><a href="javascript:loginface();"><?= img('img/face_connect.png','width:50%') ?></a></div>
   <div align="center" style="margin-top:10px"><a href="javascript:logingoogle();"><?= img('img/google.png','width:30%') ?></a></div>
   </div>
</form>
<? else: ?>
<div align="center"><a href="<?= base_url('panel') ?>" class="btn btn-success btn-large">Entrar al sistema</a></div>
<? endif; ?>
<?php $_SESSION['msj'] = null ?>