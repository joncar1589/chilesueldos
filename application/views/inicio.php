<? if(isset($_SESSION['cuenta'])): ?>   
<!-- Panel de administracion -->
    <div class="panel-group" id="accordion">
      <? if($_SESSION['cuenta']==3): ?>
        <div class="panel panel-default">
        <div class="panel-heading">
          <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                <b class="glyphicon glyphicon-plus-sign"></b> <b>Administración de tablas</b>
            </a>
          </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse">
          <div class="panel-body">
              <div class="row" align="center">
              <a href="<?= base_url('admin/user') ?>" class="col-lg-2 well"><i class="glyphicon glyphicon-user"></i><br/> Usuarios</a>
              <a href="<?= base_url('admin/paises') ?>" class="col-lg-2 well"><i class="glyphicon glyphicon-globe"></i><br/> Paises</a>
              <a href="<?= base_url('admin/ciudades') ?>" class="col-lg-2 well"><i class="glyphicon glyphicon-globe"></i><br/> Ciudades</a>
              <a href="<?= base_url('admin/remitentes') ?>" class="col-lg-2 well"><i class="glyphicon glyphicon-credit-card"></i><br/> Remitentes</a>
              <a href="<?= base_url('admin/destinatarios') ?>" class="col-lg-2 well"><i class="glyphicon glyphicon-credit-card"></i><br/> Destinatarios</a>
              <a href="<?= base_url('admin/dependencias') ?>" class="col-lg-2 well"><i class="glyphicon glyphicon-credit-card"></i><br/> Dependencias</a>
              <a href="<?= base_url('admin/cargos') ?>" class="col-lg-2 well"><i class="glyphicon glyphicon-credit-card"></i><br/> Cargos</a>
              <a href="<?= base_url('admin/tipos_destinatarios') ?>" class="col-lg-2 well"><i class="glyphicon glyphicon-credit-card"></i><br/> Tipos de destinatarios</a>
              <a href="<?= base_url('admin/tipos_remitentes') ?>" class="col-lg-2 well"><i class="glyphicon glyphicon-credit-card"></i><br/> Tipos de remitentes</a>
              <a href="<?= base_url('admin/providencias') ?>" class="col-lg-2 well"><i class="glyphicon glyphicon-credit-card"></i><br/> Providencias</a>
              <a href="<?= base_url('admin/tipos_documento') ?>" class="col-lg-2 well"><i class="glyphicon glyphicon-credit-card"></i><br/> Tipos de documentos</a>
              <a href="<?= base_url('admin/tipos_documentos_expedientes') ?>" class="col-lg-2 well"><i class="glyphicon glyphicon-credit-card"></i><br/> Tip/docs en expedientes</a>
              <a href="<?= base_url('admin/ajustes') ?>" class="col-lg-2 well"><i class="glyphicon glyphicon-credit-card"></i><br/> Ajustes del sistema</a>
              </div>
          </div>
        </div>
      </div>
    <? endif ?>
        
    <? if($_SESSION['cuenta']==0 || $_SESSION['cuenta']==3): ?>
    <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">
            <b class="glyphicon glyphicon-plus-sign"></b> <b>Remitentes</b>
        </a>
      </h4>
    </div>
    <div id="collapse2" class="panel-collapse collapse">
      <div class="panel-body">
          <div class="row" align="center">
          <a href="<?= base_url('remitentes/expedientes') ?>" class="col-lg-2 well"><i class="glyphicon glyphicon-user"></i><br/> Expedientes</a>
          </div>
      </div>
    </div>
  </div>
    <? endif ?>
        
   <? if($_SESSION['cuenta']==1 || $_SESSION['cuenta']==3): ?>
    <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">
            <b class="glyphicon glyphicon-plus-sign"></b> <b>Destinatarios</b>
        </a>
      </h4>
    </div>
    <div id="collapse3" class="panel-collapse collapse">
      <div class="panel-body">
          <div class="row" align="center">
          <a href="<?= base_url('destinatarios/expedientes') ?>" class="col-lg-2 well"><i class="glyphicon glyphicon-user"></i><br/> Expedientes</a>
          <a href="<?= base_url('destinatarios/expedientes_enviados') ?>" class="col-lg-2 well"><i class="glyphicon glyphicon-user"></i><br/> Expedientes Enviados</a>
          </div>
      </div>
    </div>
  </div>
    <? endif ?>
        
  <? if($_SESSION['cuenta']==2 || $_SESSION['cuenta']==3): ?>
    <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse4">
            <b class="glyphicon glyphicon-plus-sign"></b> <b>Recepcion</b>
        </a>
      </h4>
    </div>
    <div id="collapse4" class="panel-collapse collapse">
      <div class="panel-body">
          <div class="row" align="center">
          <a href="<?= base_url('recepcion/expedientes') ?>" class="col-lg-2 well"><i class="glyphicon glyphicon-user"></i><br/> Expedientes</a>
          <a href="<?= base_url('recepcion/usuarios') ?>" class="col-lg-2 well"><i class="glyphicon glyphicon-user"></i><br/> Usuarios</a>
          </div>
      </div>
    </div>
  </div>
    <? endif ?>
    </div>
<? endif ?>
      </div>
    </div>