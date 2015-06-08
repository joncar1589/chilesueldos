/*
 jQuery paging plugin v1.2.0 23/06/2014
 http://www.xarg.org/2011/09/jquery-pagination-revised/
 Copyright (c) 2011, Robert Eisele (robert@xarg.org)
 Dual licensed under the MIT or GPL Version 2 licenses.
*/
(function(n,u,r){n.fn.paging=function(y,z){var s=this,b={setOptions:function(a){b.a=n.extend(b.a||{lapping:0,perpage:10,page:1,refresh:{interval:10,url:null},format:"",lock:!1,onFormat:function(){},onSelect:function(){return!0},onRefresh:function(){}},a||{});b.a.lapping|=0;b.a.perpage|=0;null!==b.a.page&&(b.a.page|=0);1>b.a.perpage&&(b.a.perpage=10);b.interval&&u.clearInterval(b.interval);b.a.refresh.url&&(b.interval=u.setInterval(function(){n.ajax({url:b.a.refresh.url,success:function(a){if("string"===
typeof a)try{a=n.parseJSON(a)}catch(m){return}b.a.onRefresh(a)}})},1E3*b.a.refresh.interval));b.format=function(a){for(var b=0,g=0,h=1,e={d:[],g:0,f:0,b:5,current:3,i:0,j:0},c,p=/[*<>pq\[\]().-]|[nc]+!?/g,n={"[":"first","]":"last","<":"prev",">":"next",q:"left",p:"right","-":"fill",".":"leap"},f={};c=p.exec(a);)c=""+c,r===n[c]?"("===c?g=++b:")"===c?g=0:h&&("*"===c?(e.g=1,e.f=0):(e.g=0,e.f="!"===c.charAt(c.length-1),e.b=c.length-e.f,(e.current=1+c.indexOf("c"))||(e.current=1+e.b>>1)),e.d[e.d.length]=
{e:"block",h:0,c:0},h=0):(e.d[e.d.length]={e:n[c],h:g,c:r===f[c]?f[c]=1:++f[c]},"q"===c?++e.j:"p"===c&&++e.i);return e}(b.a.format);return b},setNumber:function(a){b.l=r===a||0>a?-1:a;return b},setPage:function(a){function v(a,b,c){c=""+a.onFormat.call(b,c);p=b.value?p+c.replace(/<a/i,'<a data-page="'+b.value+'"'):p+c}if(b.a.lock)return b.a.onSelect(0,s),b;if(r===a){if(a=b.a.page,null===a)return b}else if(b.a.page==a)return b;b.a.page=a|=0;var m=b.l,g=b.a,h,e,c,p,w=1,f=b.format,d,k,l,q,x=f.d.length,
t=x;g.perpage<=g.lapping&&(g.lapping=g.perpage-1);q=m<=g.lapping?0:g.lapping|0;0>m?(c=m=-1,h=Math.max(1,a-f.current+1-q),e=h+f.b):(c=1+Math.ceil((m-g.perpage)/(g.perpage-q)),a=Math.max(1,Math.min(0>a?1+c+a:a,c)),f.g?(h=1,e=1+c,f.current=a,f.b=c):(h=Math.max(1,Math.min(a-f.current,c-f.b)+1),e=f.f?h+f.b:Math.min(h+f.b,1+c)));for(;t--;){k=0;l=f.d[t];switch(l.e){case "left":k=l.c<h;break;case "right":k=e<=c-f.i+l.c;break;case "first":k=f.current<a;break;case "last":k=f.b<f.current+c-a;break;case "prev":k=
1<a;break;case "next":k=a<c}w|=k<<l.h}d={number:m,lapping:q,pages:c,perpage:g.perpage,page:a,slice:[(k=a*(g.perpage-q)+q)-g.perpage,Math.min(k,m)]};for(p="";++t<x;){l=f.d[t];k=w>>l.h&1;switch(l.e){case "block":for(;h<e;++h)d.value=h,d.pos=1+f.b-e+h,d.active=h<=c||0>m,d.first=1===h,d.last=h===c&&0<m,v(g,d,l.e);continue;case "left":d.value=l.c;d.active=l.c<h;break;case "right":d.value=c-f.i+l.c;d.active=e<=d.value;break;case "first":d.value=1;d.active=k&&1<a;break;case "prev":d.value=Math.max(1,a-1);
d.active=k&&1<a;break;case "last":(d.active=0>m)?d.value=1+a:(d.value=c,d.active=k&&a<c);break;case "next":(d.active=0>m)?d.value=1+a:(d.value=Math.min(1+a,c),d.active=k&&a<c);break;case "leap":case "fill":d.pos=l.c;d.active=k;v(g,d,l.e);continue}d.pos=l.c;d.last=d.first=r;v(g,d,l.e)}s.length&&(n("a",s.html(p)).click(function(a){a.preventDefault();a=this;do if("a"===a.nodeName.toLowerCase())break;while(a=a.parentNode);b.setPage(n(a).data("page"));b.k&&(u.location=a.href)}),b.k=g.onSelect.call({number:m,
lapping:q,pages:c,slice:d.slice},a,s));return b}};return b.setNumber(y).setOptions(z).setPage()}})(jQuery,this);
