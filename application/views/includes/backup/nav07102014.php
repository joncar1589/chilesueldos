<?php if(!empty($_SESSION['user'])): ?>
<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href='<?= site_url('panel') ?>'>Volver al panel</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">                
        <?php if($this->querys->has_access('admin')): ?>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Admin <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="<?= base_url('admin/ajustes'); ?>">Ajustes</a></li>
            <li><a href="<?= base_url('admin/usuarios'); ?>">Usuarios</a></li>
            <li><a href="<?= base_url('admin/paises'); ?>">Paises</a></li>
            <li><a href="<?= base_url('admin/ciudades'); ?>">Ciudades</a></li>            
            <li><a href="<?= base_url('admin/comunas'); ?>">Comunas</a></li>            
            <li><a href="<?= base_url('admin/paginas'); ?>">Gestor de contenido</a></li>            
            <li><a href="<?= base_url('admin/banner'); ?>">Banner</a></li>            
            <li><a href="<?= base_url('admin/afp'); ?>">AFP</a></li>   
            <li><a href="<?= base_url('admin/uf'); ?>">UF</a></li>            
            <li><a href="<?= base_url('admin/isapre'); ?>">ISAPRE</a></li>                        
            <li><a href="<?= base_url('admin/asignacionesfamiliares'); ?>">Asignaciones Familiares</a></li>            
            <li><a href="<?= base_url('admin/impuestostrabajadores'); ?>">Impuestos Trabajadores</a></li>            
            <li><a href="<?= base_url('admin/liquidaciones'); ?>">Liquidaciones</a></li>            
            <li><a href="<?= base_url('admin/notificaciones'); ?>">Notificaciones</a></li>            
            <li><a href="<?= base_url('admin/salarios_minimos'); ?>">Salarios Minimos</a></li>            
            <li><a href="<?= base_url('admin/dias_feriados'); ?>">Dias Feriados</a></li>            
          </ul>
        </li>
        <?php endif ?>
        <?php if($this->querys->has_access('persona')): ?>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Personas <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="<?= base_url('persona/liquidaciones'); ?>">Liquidaciones</a></li>            
            <li><a href="<?= base_url('persona/feriado'); ?>">Comprobante de feriado</a></li>            
          </ul>
        </li>
        <?php endif ?>
        <?php if($this->querys->has_access('empresa')): ?>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Empresa <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="<?= base_url('empresa/emp'); ?>">Empresas</a></li>            
            <li><a href="<?= base_url('empresa/empleados'); ?>">Empleados</a></li>                                    
            <li><a href="<?= base_url('empresa/sca'); ?>">SCA</a></li>                        
            <li><a href="<?= base_url('empresa/salud'); ?>">Salud</a></li>                        
            <li><a href="<?= base_url('empresa/liquidaciones'); ?>">Liquidaciones</a></li>            
            <li><a href="<?= base_url('empresa/finiquitos'); ?>">Finiquitos</a></li>  
            <li><a href="<?= base_url('empresa/feriados'); ?>">Comprobante de feriado</a></li>                        
          </ul>
        </li>
        <?php endif ?>
        <?php if($this->querys->has_access('preferencial')): ?>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Preferencial <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="<?= base_url('preferencial/carga'); ?>">Carga masiva de sueldos</a></li>                        
          </ul>
        </li>
        <?php endif ?>        
      </ul>      
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<?php endif ?>