<header>
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
                    <a class="navbar-brand" href="<?= site_url() ?>">Remuneraciones</a>
                  </div>

                  <!-- Collect the nav links, forms, and other content for toggling -->
                  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">                   
                    <ul class="nav navbar-nav navbar-right">                                            
                        <?php if(!empty($_SESSION['user'])): ?>
                            <li><a href="<?= site_url('empresa/recargar') ?>"><?= img('img/monedas.png') ?> <?= $_SESSION['saldo'] ?>$</a></li>
                            <li><a href="<?= site_url('panel') ?>"><?= $_SESSION['nombre'].' '.$_SESSION['apellido'] ?></a></li>
                            <li><a href="<?= site_url('main/unlog') ?>">Desconectar</a></li>
                        <?php else: ?>
                            <li><a href="<?= site_url('registro/index/add') ?>">Ingresar</a></li>
                        <?php endif ?>
                    </ul>
                  </div><!-- /.navbar-collapse -->
                </div><!-- /.container-fluid -->
              </nav>
</header>