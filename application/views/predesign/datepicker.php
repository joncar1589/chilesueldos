<link type="text/css" rel="stylesheet" href="<?= base_url('assets/grocery_crud/css/ui/simple/jquery-ui-1.10.1.custom.min.css') ?>" />
<link type="text/css" rel="stylesheet" href="<?= base_url('assets/grocery_crud/css/jquery_plugins/jquery-ui-timepicker-addon.css') ?>" />
<script src="<?= base_url('assets/grocery_crud/js/jquery_plugins/ui/jquery-ui-1.10.3.custom.min.js') ?>"></script>
<script src="<?= base_url('assets/grocery_crud/js/jquery_plugins/jquery-ui-timepicker-addon.js') ?>"></script>
<script src="<?= base_url('assets/grocery_crud/js/jquery_plugins/ui/i18n/datepicker/jquery.ui.datepicker-es.js') ?>"></script>
<script src="<?= base_url('assets/grocery_crud/js/jquery_plugins/ui/i18n/timepicker/jquery-ui-timepicker-es.js') ?>"></script>
<script>
$(function(){
    $(".datetime-input").datetimepicker({
        timeFormat: "hh:mm:ss",
                dateFormat: "dd/mm/yy",
                showButtonPanel: true,
                changeMonth: true,
                changeYear: true
    });

        $(".datetime-input-clear").button();

        $(".datetime-input-clear").click(function(){
                $(this).parent().find(".datetime-input").val("");
                return false;
        });	

});
</script>