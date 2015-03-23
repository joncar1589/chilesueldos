<?= $output ?>

<script>
$(document).ready(function(){
    if($("#field-papel").val()!='otro')
    $("#ancho_field_box,#alto_field_box").hide();
    $("#field-papel").change(function(){
        if($(this).val()=='otro')
            $("#ancho_field_box,#alto_field_box").show();
        else
            $("#ancho_field_box,#alto_field_box").hide();
    })
})
</script>