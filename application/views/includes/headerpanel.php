<header id="navbar" class="navbar navbar-default">
    <script type="text/javascript">
            try{ace.settings.check('navbar' , 'fixed')}catch(e){}
    </script>

    <div class="navbar-container" id="navbar-container">
            <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
                    <span class="sr-only">Toggle sidebar</span>

                    <span class="icon-bar"></span>

                    <span class="icon-bar"></span>

                    <span class="icon-bar"></span>
            </button>

            <div class="navbar-header pull-left">
                    <a href="<?= site_url() ?>" class="navbar-brand">
                            <small>                                    
                                    Chilesueldos
                            </small>
                    </a> 
            </div>
            <?php if($this->user->log): ?>
            <div class="navbar-buttons navbar-header pull-right" role="navigation">
                    <ul class="nav ace-nav">
                            <li class="grey">
                                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                            <i class="ace-icon fa fa-tasks"></i>
                                            <span class="badge badge-grey">0</span>
                                    </a>

                                    <ul class="dropdown-menu-right dropdown-navbar dropdown-menu dropdown-caret dropdown-close">
                                            <li class="dropdown-header">
                                                    Sin tareas pendientes
                                            </li>	
                                            <li class="dropdown-content">
                                                    <ul class="dropdown-menu dropdown-navbar navbar-pink">
                                                            <li>Sin tareas pendientes</li>
                                                    </ul>
                                            </li>
                                    </ul>
                            </li>

                            <li class="purple">
                                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                            <i class="ace-icon fa fa-bell"></i>
                                            <span class="badge badge-important">0</span>
                                    </a>

                                    <ul class="dropdown-menu-right dropdown-navbar navbar-pink dropdown-menu dropdown-caret dropdown-close">
                                            <li class="dropdown-header">
                                                    <i class="ace-icon fa fa-exclamation-triangle"></i>
                                                    0 Notificaciones
                                            </li>

                                            <li class="dropdown-content">
                                                    <ul class="dropdown-menu dropdown-navbar navbar-pink">
                                                            <li>Sin notificaciones pendientes</li>
                                                    </ul>
                                            </li>							
                                    </ul>
                            </li>

                            <li class="green">
                                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                            <i class="ace-icon fa fa-envelope"></i>
                                            <span class="badge badge-success">0</span>
                                    </a>

                                    <ul class="dropdown-menu-right dropdown-navbar dropdown-menu dropdown-caret dropdown-close">
                                            <li class="dropdown-header">
                                                    <i class="ace-icon fa fa-envelope-o"></i>
                                                    Sin mensajes
                                            </li>

                                            <li class="dropdown-content">
                                                    <ul class="dropdown-menu dropdown-navbar">
                                                        <li>Sin Mensajes</li>
                                                    </ul>
                                            </li>
                                    </ul>
                            </li>

                            <li class="light-blue">
                                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                                            <img class="nav-user-photo" src="<?= base_url(empty($this->user->foto)?'assets/grocery_crud/css/jquery_plugins/cropper/vacio.png':'img/fotos/'.$this->user->foto) ?>" alt="<?= $this->user->nombre ?>" />
                                            <span class="user-info">
                                                    <small>Bienvenido</small>
                                                    <?= $this->user->nombre ?>
                                            </span>
                                            <i class="ace-icon fa fa-caret-down"></i>
                                    </a>

                                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                                            <li>
                                                    <a href="#">
                                                            <i class="ace-icon fa fa-cog"></i>
                                                            Configuración
                                                    </a>
                                            </li>

                                            <li>
                                                    <a href="<?= base_url('seguridad/perfil/edit/'.$this->user->id) ?>">
                                                            <i class="ace-icon fa fa-user"></i>
                                                            Perfil
                                                    </a>
                                            </li>
                                            <li>
                                                    <a href="<?= base_url('main/unlog') ?>">
                                                            <i class="ace-icon fa fa-power-off"></i>
                                                            Salir
                                                    </a>
                                            </li>
                                    </ul>
                            </li>
                    </ul>
            </div>
            <?php endif ?>
    </div><!-- /.navbar-container -->
</header>
