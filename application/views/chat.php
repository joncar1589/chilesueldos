<link rel="stylesheet" type="text/css" href="<?= base_url('css/chat.css') ?>">
<script src="<?= base_url('js/chat.js') ?>"></script>
<script>
    var id = 'vacio';
    <?php if(!empty($id)): ?>
        id = '<?= $id ?>';
    <?php endif ?>
    $(document).on('ready',function(){
        WebSocketTest('<?= $_SESSION['user'] ?>','<?= $_SESSION['nombre'].' '.$_SESSION['apellido'] ?>','<?= $_SESSION['cuenta'] ?>');
    });
    
    <?php if($_SESSION['cuenta']==1): ?>
        function restart(){
            $.post('<?= base_url('chat/restartServer') ?>',{},function(data){emergente(data);});
        }
    <?php endif ?>
</script>
<div class='row' style="margin-top:30px;">    
    <div id="chatlist" class="col-xs-8 col-sm-9">
        <div id="personChat"></div>
        <div id="list">
            
        </div>
    </div>
    <div id="chatperson" class="col-xs-4 col-sm-2">
        <div><b>Administradores</b></div>
        <div id="admins"></div>
        <div><b>Clientes</b></div>
        <div id="clientes"></div>
    </div>
</div>        
<div class="row">
    <form action="" onsubmit="return sendMessage()">
        <input autocomplete="off" type="text" name="chatmessage" id="chatmessage" placeholder="Envia un mensaje" class="form-control">
        <button type="submit" class="btn btn-default">Enviar mensaje</button>
    </form>
</div>