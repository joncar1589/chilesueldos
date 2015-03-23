var FieldProductId = [];
function addFieldProduct(contenedor,field,id)
{
    var x = field.replace(/x3x/g,""+FieldProductId[id]+"");
     x = x.replace(/x4x/g,"a"+FieldProductId[id]);
    $(contenedor).append('<div class="products_items">'+x+'</div>');
    FieldProductId[id]+=1;
    $(window).trigger('addFieldProduct');
    datepicker();
}

function removeFieldProduct(contenedor)
{    
    
    $(contenedor).parent('div').remove();    
}
