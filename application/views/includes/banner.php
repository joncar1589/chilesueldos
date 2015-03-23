<?php $banner = $this->db->get('banner'); ?>
<!--- Banner ---->
<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    <?php foreach($banner->result() as $n=>$b): ?>
    <li data-target="#carousel-example-generic" data-slide-to="<?= $n ?>" class="<?= $n==0?'active':'' ?>"></li>       
    <?php endforeach ?>
  </ol>

  <!-- Wrapper for slides -->
  <div class="carousel-inner">
   <?php foreach($banner->result() as $n=>$b): ?>
    <div class="item <?= $n==0?'active':'' ?>">
      <?= img('files/'.$b->url,'width:100%') ?>
      <div class="carousel-caption">
        Soluciones de remuneraciones al alcance de todos
      </div>
    </div>
   <?php endforeach ?>
  </div>

  <!-- Controls -->
  <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left"></span>
  </a>
  <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right"></span>
  </a>
</div>