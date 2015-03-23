<nav class="navbar navbar-default" role="navigation">
  <!-- Brand and toggle get grouped for better mobile display -->
  <?if(!empty($data['brandlabel'])): ?>
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="<?= empty($data['brandlink'])?'#':$data['brandlink'] ?>"><?= $data['brandlabel'] ?></a>
  </div>
 <? endif ?>
  <!-- Collect the nav links, forms, and other content for toggling -->
  <? if(!empty($data['collapse'])): ?>
  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    <? if(!empty($data['collapse']['nav'])): ?>
    <ul class="nav navbar-nav">
      <? foreach($data['collapse']['nav'] as $x): ?>
        <li><a href="<?= $x['link'] ?>"><?= $x['label'] ?></a></li>
      <? endforeach ?>
    </ul>
    <? endif ?>
    <? if(!empty($data['collapse']['form'])): ?>
    <form class="navbar-form navbar-left" id="<?= $data['collapse']['form']['id'] ?>" role="search" method="<?= $data['collapse']['form']['method'] ?>" action="<?= $data['collapse']['form']['action'] ?>">
      <div class="form-group">
        <? foreach($data['collapse']['form']['items'] as $x): ?>
          <?= $x ?>
        <? endforeach ?>
      </div>
      <button type="submit" class="btn btn-default"><?= $data['collapse']['form']['button'] ?></button>
    </form>
    <? endif ?>
  </div><!-- /.navbar-collapse -->
  <? endif ?>
</nav>

