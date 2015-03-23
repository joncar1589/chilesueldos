var dialog = $("#chatlist"),
    inputbox = $("#chatmessage"),
    ws = null;
function WebSocketTest(userid,username,cuenta)
{                        
    if ("WebSocket" in window)
    {   
       $("#chatlist #list").append('<div>Conectando con el servidor...</div>');
       ws = new WebSocket("ws://www.chiletributa.cl:3000");
       ws.id = 'vacio';
       ws.onopen = function()
       {
          $("#chatlist #list").html('');
          $("#chatlist #personChat").html('Chateando con: <span class="label label-info">Global</span>');
          console.log(id);
          ws.send(JSON.stringify({cuenta:cuenta,username:username,userid:userid,event:'loginuser',data:id}));  
       };
       ws.onmessage = function (evt) 
       {        
           data = JSON.parse(evt.data);           
           switch(data.event)
           {
               case 'message':                    
                   console.log(data);
                    if(data.to==ws.id){
                        $("#chatlist #list").append('<div>'+data.data+'</div>');                        
                    }
                    $("#chat_"+data.to).addClass('activechat');
               break;
               case 'refreshList':
                    $("#chatperson #admins").html('<ul></ul>');
                    $("#chatperson #clientes").html('<ul></ul>');
                    for(i in data.data)
                    {
                        datos = data.data[i];
                        datos = datos.split("-");                    
                        ubicar = datos[1]=='1'?'#admins ul':'#clientes ul'                        
                        $(ubicar).append('<li id="chat_'+datos[2]+'"><a href="javascript:seleccionar_chat(\''+datos[2]+'\',\''+datos[0]+'\')"><i class="glyphicon glyphicon-user"></i> '+datos[0]+'</a></li>');
                    }
               break;
               
               case 'setMessages':
                   ws.setMessages(data.data);                   
               break;
           }

       };                 
       ws.SendMsj = function(msj){          
          if(msj!='')
          ws.send(JSON.stringify({username:username,userid:userid,event:'message',data:msj,to:ws.id}));  
       };

       ws.setMessages = function(data){
           console.log(data);
           $("#chatlist #list").html('');
           if(data!=''){               
               for(i in data){  
                   console.log(data[i].from+"=="+ws.id)
                   if(data[i].from==ws.id){
                       data[i].message = data[i].message.replace('alert-success','alert-info');
                       $("#chatlist #list").append("<div>"+data[i].message+"</div>");
                   }                   
                   else{                       
                        $("#chatlist #list").append("<div>"+data[i].message+"</div>");
                   }
               }
           }
       }
       
       ws.getMessages = function(){
           ws.send(JSON.stringify({username:username,userid:userid,event:'getMessages',data:'',to:ws.id}));  
       }
       
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

function seleccionar_chat(id,person){
    ws.id = id;    
    $("#chatlist #list").html('');
    $("#chat_"+id).removeClass('activechat');
    if(id=='vacio')
        $("#chatlist #personChat span").html('Global');
    else{
        $("#chatlist #personChat span").html(person+" <a href='javascript:seleccionar_chat(\"vacio\")'><i class='glyphicon glyphicon-remove'></i></a>");
        ws.getMessages();
    }
}