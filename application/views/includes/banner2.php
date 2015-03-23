<?php $banner = $this->db->get('banner'); ?>
<div style="background:url(<?= base_url('files/27267-banner.png') ?>); background-size:cover; width:100%; height:80%">
    <?php if($banner->num_rows>0): ?>
        <div class="container" style="padding:40px;">
            <div id="tabbed-nav">
              <ul>
                  <?php foreach($banner->result() as $b): ?>
                    <li><a><?= $b->titulo ?></a></li>
                  <?php endforeach ?>
              </ul>
              <div>
                  <?php foreach($banner->result() as $b): ?>
                    <div><?= $b->contenido ?></div>
                  <?php endforeach ?>                                        
              </div>
            </div>
        </div>
    <?php endif ?>
</div>

<script>
     $("#tabbed-nav").zozoTabs({
        theme: "silver",      
        animation: {
            duration: 800,
            effects: "slideV",            
        },
        orientation:'vertical',
        autoplay: {        
            interval: 10000,
            smart: true        
        }        
    })
</script>