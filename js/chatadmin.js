var dialog = $("#chatlist"),
    inputbox = $("#chatmessage"),
    mensajes = [];
    ws = null;
function WebSocketTest(userid,username,cuenta)
{                        
    if ("WebSocket" in window)
    {   
       $("#chatlist").append('<div>Conectando con el servidor...</div>');
       ws = new WebSocket("ws://www.chiletributa.cl:3000");
       ws.onopen = function()
       {
          $("#chatlist").html('');
          ws.send(JSON.stringify({cuenta:cuenta,username:username,userid:userid,event:'loginuser',data:''}));  
       };
       ws.onmessage = function (evt) 
       {        
           data = JSON.parse(evt.data);   
           console.log(data);
           if(data.event=='message' && data.from!='Server'){
               dato = data.from.split('-');
               if(mensajes.indexOf(dato[0])==-1){
               $(".chatnav").append('<div class="contact" id="a'+dato[0]+'"><a href="javascript:openchat(\''+dato[0]+'\')"><i class="fa fa-comment-o"></i>'+dato[1]+'</a></div>');
               mensajes.push(dato[0]);
               }
           }
       };                 
       ws.SendMsj = function(msj){
         console.log('Mensaje enviado');     
         ws.send(JSON.stringify({username:username,userid:userid,event:'message',data:msj}));  
       };
       ws.onclose = function()
       { 
           $("#chatlist").append('<div class="alert alert-danger">No se ha podido conectar con el servidor de chat...</div>');
          // websocket is closed.      
       };
    }
    else
    {
       // The browser doesn't support WebSocket
       console.log("WebSocket NOT supported by your Browser!");
    }
}

function sendMessage(){
  ws.SendMsj($("#chatmessage").val());
  $("#chatmessage").val('');
  return false;
}