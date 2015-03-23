<? $rows = count($data) ?>
<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
     <? $c = 'active' ?>
     <? for($i=0;$i<$rows;$i++): ?>
        <li data-target="#carousel-example-generic" data-slide-to="<?= $i ?>" class="<?= $c ?>"></li>
     <? $c = '' ?>
     <? endfor ?>
  </ol>

  <!-- Wrapper for slides -->
  <div class="carousel-inner">
     <? $c = 'active' ?>
     <? for($i=0;$i<$rows;$i++): ?>
        <div class="item <?= $c ?>"><?= img('files/'.$data[$i],'width:100%;') ?></div>
     <? $c = '' ?>
     <? endfor ?>
  </div>

  <!-- Controls -->
  <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left"></span>
  </a>
  <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right"></span>
  </a>
</div>

