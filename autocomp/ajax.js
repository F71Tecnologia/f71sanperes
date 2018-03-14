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
searchReq.open("GET", 'usuarios.php?login=' + str, true);
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
ss.innerHTML += '<h3>Sugestões encontradas</h3>';
ss.innerHTML += '<small>'+str[0]+' resultados'+((str[0]==10)?' (<a href="ver_mais.php">veja mais</a>)':'')+'</small><ul>';
if (str[0] > 0) {
for(i=1; i < str.length - 1; i++) {
if (str[i] != str[0]) {	
ss.innerHTML += '<li><a href="#Usuario" onClick="javascript: seleciona_usuario(\'' + str[i] + '\');">' + str[i] + '</a></li>';
}
}
ss.innerHTML += '</ul>';		
}
else ss.innerHTML += '<small>Nenhum usuário encontrado. Digite mais letras para realizarmos uma nova busca</small><br/>';
ss.innerHTML +="<div id='info'>Veja a lista completa de usuários<br/>clicando aqui</div>";
}
}
function seleciona_usuario(usuario) {
document.getElementById('pesquisa_usuario').value = usuario;
document.getElementById('ajax').style.visibility='';
}