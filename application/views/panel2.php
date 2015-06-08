<div class="row">    
    <?php if(empty($crud)): ?>
        <div class="panel-group" id="accordion">
            <?php if($this->querys->has_access('admin')): ?>
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title" align="left">
                  <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
                    Admin
                  </a>
                </h4>
              </div>
              <div id="collapse1" class="panel-collapse collapse">
                <div class="panel-body" align="center">
                    <a href="<?= base_url('admin/ajustes') ?>"><div class="col-lg-2 well"><i class="glyphicon glyphicon-wrench"></i><br/> Ajustes</div></a>
                    <a href="<?= base_url('admin/usuarios') ?>"><div class="col-lg-2 well"><i class="glyphicon glyphicon-user"></i><br/> Usuarios</div></a>
                    <a href="<?= base_url('admin/paises') ?>"><div class="col-lg-2 well"><i class="glyphicon glyphicon-globe"></i><br/> Paises</div></a>
                    <a href="<?= base_url('admin/ciudades') ?>"><div class="col-lg-2 well"><i class="fa fa-globe"></i><br/> Ciudades</div></a>
                    <a href="<?= base_url('admin/comunas') ?>"><div class="col-lg-2 well"><i class="fa fa-globe"></i><br/> Comunas</div></a>
                    <a href="<?= base_url('admin/paginas') ?>"><div class="col-lg-2 well"><i class="fa fa-pagelines"></i><br/> Gestor de contenido</div></a>
                    <a href="<?= base_url('admin/banner') ?>"><div class="col-lg-2 well"><i class="fa fa-file-o"></i><br/> Banners</div></a>
                    <a href="<?= base_url('admin/isapre') ?>"><div class="col-lg-2 well"><i class="fa fa-file-o"></i><br/> ISAPRE</div></a>
                    <a href="<?= base_url('admin/afp') ?>"><div class="col-lg-2 well"><i class="fa fa-file-o"></i><br/> AFP</div></a>                    
                    <a href="<?= base_url('admin/uf') ?>"><div class="col-lg-2 well"><i class="fa fa-money"></i><br/> UF</div></a>
                    <a href="<?= base_url('admin/asignacionesfamiliares') ?>"><div class="col-lg-2 well"><i class="fa fa-money"></i><br/> Asignaciones familiares</div></a>
                    <a href="<?= base_url('admin/impuestostrabajadores') ?>"><div class="col-lg-2 well"><i class="fa fa-money"></i><br/> Impuestos Trabajadores</div></a>
                    <a href="<?= base_url('admin/liquidaciones') ?>"><div class="col-lg-2 well"><i class="fa fa-user"></i><i class="fa fa-money"></i><br/> Liquidaciones</div></a>
                    <a href="<?= base_url('admin/finiquitos') ?>"><div class="col-lg-2 well"><i class="fa fa-user"></i><i class="fa fa-hand-o-right"></i><br/> Finiquitos</div></a>
                    <a href="<?= base_url('admin/finiquitos_causales') ?>"><div class="col-lg-2 well"><i class="fa fa-archive"></i><br/> Causales de finiquitos</div></a>
                    <a href="<?= base_url('admin/empresas') ?>"><div class="col-lg-2 well"><i class="fa fa-building-o"></i><br/> Empresas</div></a>
                    <a href="<?= base_url('admin/notificaciones') ?>"><div class="col-lg-2 well"><i class="fa fa-bell-o"></i><br/> Notificaciones</div></a>
                    <a href="<?= base_url('admin/salarios_minimos') ?>"><div class="col-lg-2 well"><i class="fa fa-envelope-o"></i><br/> Salarios Minimos</div></a>
                    <a href="<?= base_url('admin/dias_feriados') ?>"><div class="col-lg-2 well"><i class="fa fa-calendar"></i><br/>Dias Feriados</div></a>
                    <a href="<?= base_url('admin/reportes') ?>"><div class="col-lg-2 well"><i class="fa fa-file-text-o"></i><br/>Reportes</div></a>
                    <a href="<?= base_url('admin/transacciones') ?>"><div class="col-lg-2 well"><i class="fa fa-hand-o-right"></i><i class="fa fa-money"></i><br/>Depositos</div></a>
                    <a href="<?= base_url('admin/permisos') ?>"><div class="col-lg-2 well"><i class="fa fa-lock"></i><br/>Permisos</div></a>
	  <a href="<?= base_url('admin/caja_comp') ?>"><div class="col-lg-2 well"><i class="fa fa-inbox"></i><br/>Caja de Compensacion</div></a>
                    <a href="<?= base_url('admin/logs') ?>"><div class="col-lg-2 well"><i class="fa fa-file-o"></i><br/>Logs</div></a>                    
                    <a class="visible-xs" href="javascript:openchat()"><div class="col-lg-2 well"><i class="fa fa-comments-o"></i><br/>Chat</div></a>                    
                </div>
              </div>
            </div>
            <?php endif ?>
            <?php if($this->querys->has_access('persona')): ?>
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title" align="left">
                  <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                    Persona
                  </a>
                </h4>
              </div>
              <div id="collapseOne" class="panel-collapse collapse">
                <div class="panel-body" align="center">
                    <a href="<?= base_url('persona/liquidaciones') ?>"><div class="col-lg-2 well"><i class="fa fa-money"></i><br/> Liquidaciones</div></a>
                    <a href="<?= base_url('persona/feriado') ?>"><div class="col-lg-2 well"><i class="fa fa-money"></i><br/> Comprobante de feriado</div></a>
                </div>
              </div>
            </div>
            <?php endif ?>
            <?php if($this->querys->has_access('empresa')): ?>
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title" align="left">
                  <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                    Empresa
                  </a>
                </h4>
              </div>
              <div id="collapseTwo" class="panel-collapse collapse">
                <div class="panel-body" align="center">
                  <a href="<?= base_url('empresa/emp') ?>"><div class="col-lg-2 well"><i class="fa fa-building-o"></i><br/> Empresa</div></a>
                  <a href="<?= base_url('empresa/empleados') ?>"><div class="col-lg-2 well"><i class="fa fa-group"></i><br/> Empleados</div></a>
                  <a href="<?= base_url('empresa/contratos') ?>"><div class="col-lg-2 well"><i class="fa fa-group"></i><br/> Contratos</div></a>                                                                        
                  <a href="<?= base_url('empresa/sca') ?>"><div class="col-lg-2 well"><i class="fa fa-money"></i><br/> DBSCA</div></a>                  
                  <a href="<?= base_url('empresa/liquidaciones') ?>"><div class="col-lg-2 well"><i class="fa fa-money"></i><br/> Liquidaciones</div></a>
                  <a href="<?= base_url('empresa/finiquitos') ?>"><div class="col-lg-2 well"><i class="fa fa-money"></i><br/> Finiquitos</div></a>
                  <a href="<?= base_url('empresa/feriados') ?>"><div class="col-lg-2 well"><i class="fa fa-money"></i><br/> Comprobante de feriado</div></a>
                  <a class="visible-xs" href="javascript:openchat()"><div class="col-lg-2 well"><i class="fa fa-comments-o"></i><br/>Chat</div></a>                    
                </div>
              </div>
            </div>
            <?php endif ?>
            <?php if($this->querys->has_access('preferencial')): ?>
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title" align="left">
                  <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                    Preferencial
                  </a>
                </h4>
              </div>
              <div id="collapseThree" class="panel-collapse collapse">
                <div class="panel-body" align="center">
                  <a href="<?= base_url('preferencial/carga') ?>"><div class="col-lg-2 well"><i class="fa fa-money"></i><br/> Carga masiva de empleados</div></a>
                  <a href="<?= base_url('preferencial/carga_sueldos') ?>"><div class="col-lg-2 well"><i class="fa fa-money"></i><br/> Carga masiva de sueldos</div></a>
                </div>
              </div>
            </div>
            <?php endif ?> 
            
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title" align="left">
                  <a data-toggle="collapse" data-parent="#accordion" href="#collapseFive">
                    Ajustes
                  </a>
                </h4>
              </div>
              <div id="collapseFive" class="panel-collapse collapse">
                <div class="panel-body" align="center">
                  <a href="<?= base_url('persona/datos') ?>"><div class="col-lg-2 well"><i class="fa fa-money"></i><br/> Actualizar datos</div></a>                  
                  <a href="<?= base_url('empresa/transacciones') ?>"><div class="col-lg-2 well"><i class="fa fa-money"></i><br/> Transacciones</div></a>                  
                </div>
              </div>
            </div>
          </div>
    <?php else: ?>
        <?php $this->load->view('includes/nav'); ?>
        <?php $this->load->view('includes/breadcrum'); ?>
        <?php $this->load->view('cruds/'.$crud); ?>
    <?php endif ?>
</div>
