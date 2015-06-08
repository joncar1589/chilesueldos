<?php if($this->user->log): ?>
<div id="sidebar" class="sidebar responsive">
        <ul class="nav nav-list">
            <li class="active highlight">
                <a href="<?= site_url('panel') ?>">
                        <i class="menu-icon fa fa-tachometer"></i>
                        <span class="menu-text">Escritorio</span>
                </a>
                <b class="arrow"></b>
            </li>     
            <li class="active highlight">
                <a href="<?= site_url('panel') ?>">
                        <i class="menu-icon fa fa-user"></i>
                        <span class="menu-text">Persona</span>
                </a>
                <b class="arrow"></b>
            </li>
            <li class="active highlight">
                <a href="<?= site_url('panel') ?>">
                        <i class="menu-icon fa fa-building-o"></i>
                        <span class="menu-text">Empresa</span>
                </a>
                <b class="arrow"></b>
            </li>
            <?php if($this->user->cuenta==1): ?>
            <?php $this->load->view('includes/menu_admin'); ?>
            <?php endif ?>
        </ul>
       <div id="sidebar-collapse" class="sidebar-toggle sidebar-collapse">
            <i data-icon2="ace-icon fa fa-angle-double-right" data-icon1="ace-icon fa fa-angle-double-left" class="ace-icon fa fa-angle-double-left"></i>
        </div>

        <script type="text/javascript">
                try{ace.settings.check('sidebar' , 'collapsed')
                ace.settings.sidebar_collapsed(true, true);
                }catch(e){}
        </script>
</div>
<?php endif ?>
