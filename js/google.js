/* 
 Archivo para iniciar sesion y traer datos desde google-
 */   
(function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/client:plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();

function logingoogle()
{
    gapi.auth.signIn({callback:'signinCallback',clientid:'706224918497-pvs53mp83h778h8con59q1182fodqj20.apps.googleusercontent.com',cookiepolicy:"single_host_origin",requestvisibleactions:"http://schemas.google.com/AddActivity",scope:"https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email"});
}

function signinCallback(authResult) {  
  if (authResult['access_token']) {
      gapi.auth.setToken(authResult);
      gapi.client.load('oauth2', 'v2', function() {
          var request = gapi.client.oauth2.userinfo.get();
          request.execute(function(obj){
              $.post(url+"registro/loginface",{nombre:obj['given_name'],apellido:obj['family_name'],email:obj['email']},function(data){
                if(data=='success')
                    document.location.href=url+"panel";
            });
          })          
      });
  }        
}
