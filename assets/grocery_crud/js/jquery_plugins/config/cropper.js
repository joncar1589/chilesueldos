var croppers = [];
var crops = [];
$(document).ready(function(){
    for(i=0;i<croppers.length;i++){
        options = {
        uploadUrl:'cropper/save',
        cropUrl:'cropper/crop',
        deleteUrl:'cropper/delete_crop',
        modal:true,
        imgEyecandyOpacity:0.4,
        loaderHtml:'<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div> ' };
        c = new Croppic(croppers[i],options,$('#'+croppers[i]+" input"));
        crops.push(c);        
    }
});