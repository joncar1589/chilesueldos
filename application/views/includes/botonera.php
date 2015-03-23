<div class="row botonera" align="center" style="height:10%;">
    <div align="center" class="col-xs-6 col-sm-2 well"><a href="<?= site_url() ?>"><i style="font-size:29px" class="fa fa-home"></i> <br> Inicio</a></div>
  <?php foreach($this->db->get_where('paginas',array('visible'=>1))->result() as $n=>$p): ?>    
    <div align="center" class="col-xs-6 col-sm-2 well"><a href="<?= site_url('paginas/'.$p->url) ?>"><i style="font-size:29px" class="fa fa-<?= $p->icono ?>"></i> <br> <?= str_replace("-"," ",str_replace("_"," ",ucwords($p->url))) ?></a></div>
  <?php endforeach ?>
</div>