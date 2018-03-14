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
var str2 = escape(document.getElementById('reg').value);
if (str.length < 1) return false;
searchReq.open("POST", 'usuarios2.php?login=' + str + '&reg=' + str2, true);
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
ss.innerHTML += '<table><td><h3>Sugestões encontradas</h3></td><td align="right"><h3><a href="#" onClick=\"javascript: document.getElementById(\'ajax\').style.visibility=\'\'\"><img src=\'../imagens/bot_fechar.gif\' border=0></a></h3></td></rh></table>';
ss.innerHTML += '<small>'+str[0]+' resultados'+((str[0]==10)?' (<a href="ver_mais.php">veja mais</a>)':'')+'</small><ul>';
if (str[0] > 0) {
for(i=1; i < str.length - 1; i++) {
if (str[i] != str[0]) {	
ss.innerHTML += '<li>' + str[i] + '</li>';
}
}
ss.innerHTML += '</ul>';		
}
else ss.innerHTML += '<small>Nenhum registro encontrado. Digite mais letras para realizarmos uma nova busca</small><br/>';
ss.innerHTML +="<div id='info'> </div>";
}
}
function seleciona_usuario(usuario) {
document.getElementById('pesquisa_usuario').value = usuario;
var str_num = usuario.split("-");
document.getElementById('id_cbo').value = str_num[0];
document.getElementById('ajax').style.visibility='';
}
