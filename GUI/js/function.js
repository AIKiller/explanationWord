//���ù��ò���

var sid = $("#sid",parent.document).html();//��ȡsid

//�������ù��ú���
function toggleModalById(elementID){

	$("#"+elementID).modal('toggle');

}

//��������cookie
function setCookie(name,value) 
{ 
    var Days = 30; 
    var exp = new Date(); 
    exp.setTime(exp.getTime() + Days*24*60*60*3600);
    document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString(); 
}
//��ȡcookies 
function getCookie(name) 
{ 
    var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
 
    if(arr=document.cookie.match(reg))
 
        return unescape(arr[2]); 
    else 
        return null; 
} 

//ɾ��cookies 
function delCookie(name) 
{ 
    var exp = new Date(); 
    exp.setTime(exp.getTime() - 1); 
    var cval=getCookie(name); 
    if(cval!=null) 
    document.cookie= name + "="+cval+";expires="+exp.toGMTString(); 
}