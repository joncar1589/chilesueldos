<li>
    <a class="dropdown-toggle" href="#">
            <i class="menu-icon fa fa-lock"></i>
            <span class="menu-text">Admin</span>
            <b class="arrow fa fa-angle-down"></b>
    </a>
    <b class="arrow"></b>
    <ul class="submenu">
        <!--- Seguridad --->
        <li>
            <a class="dropdown-toggle" href="#">
                <i class="menu-icon fa fa-lock"></i>
                <span class="menu-text">Seguridad</span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
            <b class="arrow"></b>
            <ul class='submenu'>
                <li>
                    <a href="<?= base_url('seguridad/ajustes') ?>">
                        <i class="menu-icon fa fa-wrench"></i>
                        <span class="menu-text">Ajustes generales</span>                                    
                    </a>                                
                </li>                            
                <li>
                    <a href="<?= base_url('seguridad/grupos') ?>">
                        <i class="menu-icon fa fa-users"></i>
                        <span class="menu-text">Grupos</span>                                    
                    </a>                                
                </li>
                <li>
                    <a href="<?= base_url('seguridad/funciones') ?>">
                        <i class="menu-icon fa fa-arrows"></i>
                        <span class="menu-text">Funciones</span>                                    
                    </a>                                
                </li>
                <li>
                    <a href="<?= base_url('seguridad/user') ?>">
                        <i class="menu-icon fa fa-user"></i>
                        <span class="menu-text">Usuarios</span>                                    
                    </a>                                
                </li>                
                <li>
                    <a href="<?= base_url('seguridad/permisos') ?>">
                        <i class="menu-icon fa fa-key"></i>
                        <span class="menu-text">Permisos de empresa</span>                                    
                    </a>                                
                </li>
            </ul>
        </li>
        <!--- Fin seguridad ---->
         <!--- CMS --->
        <li>
            <a class="dropdown-toggle" href="#">
                <i class="menu-icon fa fa-file-o"></i>
                <span class="menu-text">CMS</span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
            <b class="arrow"></b>
            <ul class='submenu'>
                <li>
                    <a href="<?= base_url('cms/paginas_informativas') ?>">
                        <i class="menu-icon fa fa-file"></i>
                        <span class="menu-text">Paginas</span>                                    
                    </a>                                
                </li>
                <li>
                    <a href="<?= base_url('cms/banner') ?>">
                        <i class="menu-icon fa fa-picture-o"></i>
                        <span class="menu-text">Banner</span>                                    
                    </a>                                
                </li>
                <li>
                    <a href="<?= base_url('cms/notificaciones') ?>">
                        <i class="menu-icon fa fa-bell"></i>
                        <span class="menu-text">Notificaciones</span>                                    
                    </a>                                
                </li>                            
            </ul>
        </li>
        <!--- Fin CMS ---->
        <!--- Tabalas Empresa --->
        <li>
            <a class="dropdown-toggle" href="#">
                <i class="menu-icon fa fa-building-o"></i>
                <span class="menu-text">Empresas</span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
            <b class="arrow"></b>
            <ul class='submenu'>
                <li>
                    <a href="<?= base_url('empresa_admin/empresas') ?>">
                        <i class="menu-icon fa fa-building-o"></i>
                        <span class="menu-text">Empresas</span>                                    
                    </a>                                
                </li>
                <li>
                    <a href="<?= base_url('empresa_admin/trabajadores') ?>">
                        <i class="menu-icon fa fa-user"></i>
                        <span class="menu-text">Trabajadores</span>                                    
                    </a>                                
                </li>
                <li>
                    <a href="<?= base_url('empresa_admin/contratos') ?>">
                        <i class="menu-icon fa fa-files-o"></i>
                        <span class="menu-text">Contratos</span>                                    
                    </a>                                
                </li>
                <li>
                    <a href="<?= base_url('empresa_admin/dbsca') ?>">
                        <i class="menu-icon fa fa-files-o"></i>
                        <span class="menu-text">SCA</span>                                    
                    </a>                                
                </li>
                <li>
                    <a href="<?= base_url('empresa_admin/liquidaciones') ?>">
                        <i class="menu-icon fa fa-files-o"></i>
                        <span class="menu-text">Liquidaciones</span>                                    
                    </a>                                
                </li>
                <li>
                    <a href="<?= base_url('empresa_admin/finiquitos') ?>">
                        <i class="menu-icon fa fa-files-o"></i>
                        <span class="menu-text">Finiquitos</span>                                    
                    </a>                                
                </li>
                <li>
                    <a href="<?= base_url('empresa_admin/feriado') ?>">
                        <i class="menu-icon fa fa-files-o"></i>
                        <span class="menu-text">Feriados</span>                                    
                    </a>                                
                </li>
            </ul>
        </li>
        <!--- Fin tablas Empresa ---->
        <!--- Tabalas maestras --->
        <li>
            <a class="dropdown-toggle" href="#">
                <i class="menu-icon fa fa-lock"></i>
                <span class="menu-text">Tablas Maestras</span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
            <b class="arrow"></b>
            <ul class='submenu'>
                <li>
                    <a href="<?= base_url('tablasmaestras/paises') ?>">
                        <i class="menu-icon fa fa-globe"></i>
                        <span class="menu-text">Paises</span>                                    
                    </a>                                
                </li>  
                <li>
                    <a href="<?= base_url('tablasmaestras/ciudades') ?>">
                        <i class="menu-icon fa fa-globe"></i>
                        <span class="menu-text">Ciudades</span>                                    
                    </a>                                
                </li>
                <li>
                    <a href="<?= base_url('tablasmaestras/comunas') ?>">
                        <i class="menu-icon fa fa-globe"></i>
                        <span class="menu-text">Comunas</span>                                    
                    </a>                                
                </li>       
                <li>
                    <a href="<?= base_url('tablasmaestras/isapre') ?>">
                        <i class="menu-icon fa fa-file"></i>
                        <span class="menu-text">ISAPRE</span>                                    
                    </a>                                
                </li>    
                 <li>
                    <a href="<?= base_url('tablasmaestras/afp') ?>">
                        <i class="menu-icon fa fa-file"></i>
                        <span class="menu-text">AFP</span>                                    
                    </a>                                
                </li>  
                 <li>
                    <a href="<?= base_url('tablasmaestras/uf') ?>">
                        <i class="menu-icon fa fa-file"></i>
                        <span class="menu-text">UF</span>                                    
                    </a>                                
                </li>  
                <li>
                    <a href="<?= base_url('tablasmaestras/asignacionesfamiliares') ?>">
                        <i class="menu-icon fa fa-users"></i>
                        <span class="menu-text">Asignaciones Familiares</span>                                    
                    </a>                                
                </li>  
                <li>
                    <a href="<?= base_url('tablasmaestras/impuestos_trabajadores') ?>">
                        <i class="menu-icon fa fa-institution"></i>
                        <span class="menu-text">Impuestos Trabajadores</span>                                    
                    </a>                                
                </li>
                <li>
                    <a href="<?= base_url('tablasmaestras/feriados') ?>">
                        <i class="menu-icon fa fa-calendar"></i>
                        <span class="menu-text">Dias Feriados</span>                                    
                    </a>                                
                </li>
                <li>
                    <a href="<?= base_url('tablasmaestras/finiquitos_causales') ?>">
                        <i class="menu-icon fa fa-legal"></i>
                        <span class="menu-text">Finiquitos Causales</span>                                    
                    </a>                                
                </li>
                <li>
                    <a href="<?= base_url('tablasmaestras/salarios_minimos') ?>">
                        <i class="menu-icon fa fa-bell"></i>
                        <span class="menu-text">Salarios Minimos</span>                                    
                    </a>                                
                </li>
                 <li>
                    <a href="<?= base_url('tablasmaestras/reportes') ?>">
                        <i class="menu-icon fa fa-files-o"></i>
                        <span class="menu-text">Reportes</span>                                    
                    </a>                                
                </li>
            </ul>
        </li>
        <!--- Fin tablas Maestras ---->
        <!--- Operaciones --->
        <li>
            <a class="dropdown-toggle" href="#">
                <i class="menu-icon fa fa-lock"></i>
                <span class="menu-text">Operaciones</span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
            <b class="arrow"></b>
            <ul class='submenu'>
                <li>
                    <a href="<?= base_url('operaciones/transacciones') ?>">
                        <i class="menu-icon fa fa-globe"></i>
                        <span class="menu-text">Transacciones</span>                                    
                    </a>                                
                </li>
                 <li>
                    <a href="<?= base_url('operaciones/caja_compensacion') ?>">
                        <i class="menu-icon fa fa-globe"></i>
                        <span class="menu-text">Cajas de compensación</span>                                    
                    </a>                                
                </li>  
                <li>
                    <a href="<?= base_url('operaciones/logs') ?>">
                        <i class="menu-icon fa fa-globe"></i>
                        <span class="menu-text">Logs</span>                                    
                    </a>                                
                </li>
            </ul>
        </li>
        <!--- Fin Operaciones ---->
    </ul>
</li>
<!--- Fin Admin ---->
