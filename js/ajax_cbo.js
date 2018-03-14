function getXmlHttpRequestObject() {
if (window.XMLHttpRequest) {
return new XMLHttpRequest();
} else if(window.ActiveXObject) {
return new ActiveXObject("Microsoft.XMLHTTP");
} else {
}
}
var searchReq = getXmlHttpRequestObject();
function searchSuggest() {
if (searchReq.readyState == 4 || searchReq.readyState == 0) {
var str = escape(document.getElementById('pesquisa_usuario').value);
if (str.length < 1) return false;
searchReq.open("POST", '../usuarios.php?login=' + str, true);
searchReq.setRequestHeader('Content-Type','text/html'); 
searchReq.setRequestHeader('encoding','ISO-8859-1'); 
searchReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); 
searchReq.onreadystatechange = handleSearchSuggest; 
searchReq.send(null);
}		
}
function handleSearchSuggest() {
document.getElementById('ajax').style.visibility='visible';
if (searchReq.readyState == 4) {
var ss = document.getElementById('ajax')
ss.innerHTML = '';
var str = searchReq.responseText.split("\n");
ss.innerHTML += '<table><td><h3>Sugest&otilde;es encontradas</h3></td><td align="right"><h3><a href="#" onClick=\"javascript: document.getElementById(\'ajax\').style.visibility=\'\'\"><img src=\'../imagens/bot_fechar.gif\' border=0></a></h3></td></rh></table>';
ss.innerHTML += '<small>'+str[0]+' resultados'+((str[0]==10)?' (..)':'')+'</small><ul>';
if (str[0] > 0) {
for(i=1; i < str.length - 1; i++) {
if (str[i] != str[0]) {	
ss.innerHTML += '<li><a href="#Usuario" onClick="javascript: seleciona_usuario(\'' + str[i] + '\');">' + str[i] + '</a></li>';
}
}
ss.innerHTML += '</ul>';		
}
else ss.innerHTML += '<small>Nenhum registro encontrado. Digite mais letras para realizarmos uma nova busca</small><br/>';
ss.innerHTML +="<div id='info'>...<br/>..</div>";
}
}
function seleciona_usuario(usuario) {
document.getElementById('pesquisa_usuario').value = usuario;
var str_num = usuario.split("-");
document.getElementById('id_cbo').value = str_num[0];
document.getElementById('ajax').style.visibility='';
}