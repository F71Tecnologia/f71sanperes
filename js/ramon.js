// JavaScript Document
// BY RAMON LIMA

//----- FUNÇÕES CONTIDAS NESSA FOLHA DE SCRIPTS ----//
// VERIFICA NOME (NAO PODE TER APOSTOFRO  " ' ")
// ABRIR (FUNCAO NOVA PARA ABRIR POPUP)
// MASCARA_DATA
// VERIFICA_DATA
// FORMATAR
// PULA
// CORFUNDO
// FORMATAVALOR
// TELEFONEFORMAT
// PORCENTAGEM
// DRAGME (MOVER OBJETOS DENTRO DA PAGINA)
// ALERT_E_REDIRECT
// AJAX_UPLOAD()
// AJAX_INSERT()
// LIMPA CACHE
// DROGA DE BANCO ( EXCLUSIVO PARA ALTERAR E CADASTRAR AUTONOMOS, COOPERADOS E CLTS )
// insertValueQuery() - PARA SELECIONAR ITENS EM UM SELECT MULTIPLO COM DUPLO CLIQUE NO SELECT (EXCLUSIVO DO ARQUIVO escala.php )
// VARIOS AJAX
// AJAX UF
// AJAX UPDATE NO CHECK BOX
//----- FUNÇÕES CONTIDAS NESSA FOLHA DE SCRIPTS ----//

// VERIFICA NOME (NAO PODE TER APOSTOFRO  " ' ") aki
function verificanome(campo,e){
	var aux = '';
	var whichCode = e.keyCode;
	
	if (whichCode == 39){
		alert ("Impossivel utilizar o APOSTROFO!");
		return false;  // Enter backspace ou FN qualquer um que não seja alfa numerico
	}else{
		aux += campo.value;
	}
	
	return aux;
}

//-FUNCAO PARA ABRIR NOVO POPUP
function abrir(URL,Nombre,w,h,a) {
	
	if(w == 0 && h == 0){
		var w = 780;
  		var h = 650;
	}
	
	var left = 99;
 	var top = 99;


  	window.open(URL, Nombre+'a', 'width='+w+', height='+h+', top='+top+', left='+left+', scrollbars=yes, status=no, toolbar=no, location=no, directories=no, menubar=no, resizable=yes, fullscreen=no');

}

//-FUNÇÃO PARA MASCARAR A DATA
//PRIMEIRA PARTE, FORMATANDO
function mascara_data(d){  
	var mydata = '';  
	data = d.value;  
	mydata = mydata + data;  
	if (mydata.length == 2){  
		mydata = mydata + '/';  
		d.value = mydata;  
	}  
	if (mydata.length == 5){  
		mydata = mydata + '/';  
		d.value = mydata;  
	}  
		if (mydata.length == 10){  
		verifica_data(d);  
	}  
} 
           
//- SEGUNDA PARTE, VERIFICANDO
function verifica_data (d) {  

	dia = (d.value.substring(0,2));  
	mes = (d.value.substring(3,5));  
	ano = (d.value.substring(6,10));  
             

	situacao = "";  
	// verifica o dia valido para cada mes  
	if ((dia < 01)||(dia < 01 || dia > 30) && (  mes == 04 || mes == 06 || mes == 09 || mes == 11 ) || dia > 31) {  
		situacao = "falsa";  
	}  

	// verifica se o mes e valido  
	if (mes < 01 || mes > 12 ) {  
		situacao = "falsa";  
	}  

	// verifica se e ano bissexto  
	if (mes == 2 && ( dia < 01 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))) {  
		situacao = "falsa";  
	}  
   
	if (d.value == "") {  
		situacao = "falsa";  
	}  

	if (situacao == "falsa") {  
		alert("Data digitada &eacute; inv&aacute;lida, digite novamente!"); 
		d.value = "";  
		d.focus();  
	}  
	
}


//- FUNÇÃO PARA PULAR
function pula(maxlength, id, proximo){ 
   if(document.getElementById(id).value.length >= maxlength){ 
     document.getElementById(proximo).focus();
 }
} 

//- FUNÇÃO PARA COLORIR O FUNDO
function CorFundo(campo,cor){
	var d = document;
	if(cor == 1){
		var color = "#F2F2E3";
	}else{
		var color = "#FFFFFF";
	}

	d.getElementById(campo).style.background=color;
	
}

//- FORMATAÇÃO DE VALOR
function FormataValor(objeto,teclapres,tammax,decimais){
	
	var tecla            = teclapres.keyCode;
	var tamanhoObjeto    = objeto.value.length;
	
	if ((tecla == 8) && (tamanhoObjeto == tammax)){
		tamanhoObjeto = tamanhoObjeto - 1 ;
	}
	
	if (( tecla == 8 || tecla == 88 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 ) && ((tamanhoObjeto+1) <= tammax)){
		vr    = objeto.value;
		vr    = vr.replace( "/", "" );
		vr    = vr.replace( "/", "" );
		vr    = vr.replace( ",", "" );
		vr    = vr.replace( ".", "" );
		vr    = vr.replace( ".", "" );
		vr    = vr.replace( ".", "" );
		vr    = vr.replace( ".", "" );
		tam    = vr.length;

		if (tam < tammax && tecla != 8){
			tam = vr.length + 1 ;
		}
		if ((tecla == 8) && (tam > 1)){
			tam = tam - 1 ;
			vr = objeto.value;
			vr = vr.replace( "/", "" );
			vr = vr.replace( "/", "" );
			vr = vr.replace( ",", "" );
			vr = vr.replace( ".", "" );
			vr = vr.replace( ".", "" );
			vr = vr.replace( ".", "" );
			vr = vr.replace( ".", "" );
		}
		
//- Cálculo para casas decimais setadas por parametro
if ( tecla == 8 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 ){
	if (decimais > 0){
		if ( (tam <= decimais) ){ 
			objeto.value = ("0," + vr) ;
		}
		if( (tam == (decimais + 1)) && (tecla == 8)){
			objeto.value = vr.substr( 0, (tam - decimais)) + ',' + vr.substr( tam - (decimais), tam );
		}
		if ( (tam > (decimais + 1)) && (tam <= (decimais + 3)) &&  ((vr.substr(0,1)) == "0")){
			objeto.value = vr.substr( 1, (tam - (decimais+1))) + ',' + vr.substr( tam - (decimais), tam ) ;
		}
		if ( (tam > (decimais + 1)) && (tam <= (decimais + 3)) &&  ((vr.substr(0,1)) != "0")){
			objeto.value = vr.substr( 0, tam - decimais ) + ',' + vr.substr( tam - decimais, tam ) ; 
		}
		if ( (tam >= (decimais + 4)) && (tam <= (decimais + 6)) ){
			objeto.value = vr.substr( 0, tam - (decimais + 3) ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
		}
		if ( (tam >= (decimais + 7)) && (tam <= (decimais + 9)) ){
			objeto.value = vr.substr( 0, tam - (decimais + 6) ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
		}
		if ( (tam >= (decimais + 10)) && (tam <= (decimais + 12)) ){
			objeto.value = vr.substr( 0, tam - (decimais + 9) ) + '.' + vr.substr( tam - (decimais + 9), 3 ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
		}
		if ( (tam >= (decimais + 13)) && (tam <= (decimais + 15)) ){
			objeto.value = vr.substr( 0, tam - (decimais + 12) ) + '.' + vr.substr( tam - (decimais + 12), 3 ) + '.' + vr.substr( tam - (decimais + 9), 3 ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
		}
	}else if(decimais == 0){
		if ( tam <= 3 ){
			objeto.value = vr ;
		}
		if ( (tam >= 4) && (tam <= 6) ){
			if(tecla == 8){
				objeto.value = vr.substr(0, tam);
				window.event.cancelBubble = true;
				window.event.returnValue = false;
			}
				objeto.value = vr.substr(0, tam - 3) + '.' + vr.substr( tam - 3, 3 ); 
		}
		if ( (tam >= 7) && (tam <= 9) ){
			if(tecla == 8){
				objeto.value = vr.substr(0, tam);
				window.event.cancelBubble = true;
				window.event.returnValue = false;
			}
				objeto.value = vr.substr( 0, tam - 6 ) + '.' + vr.substr( tam - 6, 3 ) + '.' + vr.substr( tam - 3, 3 ); 
		}
		if ( (tam >= 10) && (tam <= 12) ){
			if(tecla == 8){
				objeto.value = vr.substr(0, tam);
				window.event.cancelBubble = true;
				window.event.returnValue = false;
			}
			objeto.value = vr.substr( 0, tam - 9 ) + '.' + vr.substr( tam - 9, 3 ) + '.' + vr.substr( tam - 6, 3 ) + '.' + vr.substr( tam - 3, 3 ); 
		}
		if ( (tam >= 13) && (tam <= 15) ){
			if(tecla == 8){
				objeto.value = vr.substr(0, tam);
				window.event.cancelBubble = true;
				window.event.returnValue = false;
			}
			objeto.value = vr.substr( 0, tam - 12 ) + '.' + vr.substr( tam - 12, 3 ) + '.' + vr.substr( tam - 9, 3 ) + '.' + vr.substr( tam - 6, 3 ) + '.' + vr.substr( tam - 3, 3 ) ;
		}            
	}
	}
	}else if((window.event.keyCode != 8) && (window.event.keyCode != 9) && (window.event.keyCode != 13) && (window.event.keyCode != 35) && (window.event.keyCode != 36) && (window.event.keyCode != 46)){
		window.event.cancelBubble = true;
		window.event.returnValue = false;
	}
} 


//- FORMATANDO QUALQUER CAMPO, PASSANDO O ESTILO DE FORMATAÇÃO
function formatar(mascara, documento){ 
  var i = documento.value.length; 
  var saida = mascara.substring(0,1); 
  var texto = mascara.substring(i) 
   
  if (texto.substring(0,1) != saida){ 
            documento.value += texto.substring(0,1); 
  } 
   
} 

  
//- FORMATANDO TELEFONE   aki
function TelefoneFormat(Campo, e) {
	var key = '';
	var len = 0;
	var strCheck = '0123456789';
	var aux = '';
	var whichCode = e.keyCode;
	
	if (whichCode == 13 || whichCode == 8 || whichCode == 0){
		return true;  // Enter backspace ou FN qualquer um que não seja alfa numerico
	}
	
	
	key = String.fromCharCode(whichCode);
	if (strCheck.indexOf(key) == -1){
		return false;  //NÃO E VALIDO
	}
	
	aux =  Telefone_Remove_Format(Campo.value);
	len = aux.length;
	
	if(len>=10){
		return false;	//impede de digitar um telefone maior que 10
	}
	
	aux += key;
	Campo.value = Telefone_Mont_Format(aux);

	return false;
	
}

//-CONTINUANDO FORMATAÇÃO DE TELEFONE
function  Telefone_Mont_Format(Telefone){
	var aux = len = '';
	len = Telefone.length;
	
	if(len<=9){
		tmp = 5;
	}else{
		tmp = 6;
	}
	
	aux = '';
	for(i = 0; i < len; i++){
		if(i==0){
			aux = '(';
		}
		
		aux += Telefone.charAt(i);
		if(i+1==2){
			aux += ')';
		}
		
		if(i+1==tmp){
			aux += '-';
		}
	}
	
	return aux ;
}

//- TERCEIRA PARTE DA FORMATAÇÃO DO TELEFONE
function  Telefone_Remove_Format(Telefone){
	var strCheck = '0123456789';
	var len = i = aux = '';
	len = Telefone.length;

	for(i = 0; i < len; i++){
		if (strCheck.indexOf(Telefone.charAt(i))!=-1){
			aux += Telefone.charAt(i);
		}
	}
	
	return aux;
}



//- PASSA NUMERO INTEIRO PARA PERCENTUAL EX: 20 = 0.02
function porcentagem(d,a){
	
	var tecla=(window.event)?event.keyCode:e.which;
    
	var caracter=d.value;
	var len = '';
	var len = d.length;
	
	if((tecla > 47 && tecla < 58)){
		return true;
	}else if (tecla != 8 ){
		return false;
	}
		
	return true;
}

/*
//MOVER OBJETOS DENTRO DA PAGINA
//DRAGME
<!-- This script and many more are available free online at -->
<!-- Created by: elouai.com -->
<!-- Início

var ie=document.all;
var nn6=document.getElementById&&!document.all;
var isdrag=false;
var x,y;
var dobj;

function movemouse(e)
{
  if (isdrag)
  {
    dobj.style.left = nn6 ? tx + e.clientX - x : tx + event.clientX - x;
    dobj.style.top  = nn6 ? ty + e.clientY - y : ty + event.clientY - y;
    return false;
  }
}

function selectmouse(e)
{
  var fobj       = nn6 ? e.target : event.srcElement;
  var topelement = nn6 ? "HTML" : "BODY";
  while (fobj.tagName != topelement && fobj.className != "dragme")
  {
    fobj = nn6 ? fobj.parentNode : fobj.parentElement;
  }

  if (fobj.className=="dragme")
  {
    isdrag = true;
    dobj = fobj;
    tx = parseInt(dobj.style.left+0);
    ty = parseInt(dobj.style.top+0);
    x = nn6 ? e.clientX : event.clientX;
    y = nn6 ? e.clientY : event.clientY;
    document.onmousemove=movemouse;
    return false;
  }

}

document.onmousedown=selectmouse;
document.onmouseup=new Function("isdrag=false");
//  Fim  DRAGME -->
*/

// LAERT_REDIRECT
function alert_redirect(mensagem,url){
	alert(mensagem);
	location.href = url;
}


//AJAX UPLOAD
function ajaxUpload(tabela,valor,campo,nomeid,id,tipoaj,pasta){
	var xmlHttp;
	try{											  // Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
  	} catch (e) {										  // Internet Explorer
  		try {
    	xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
    	try {
      	xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    } catch (e) {
      	alert("Your browser does not support AJAX!");
      	return false;
		}
	}
	}
	
	//A VARIAVEL pasta ERA UTILIZADA PARA INFORMAR O PATH DAS IMAGENS E TAL
	//MAS AGORA MUDEI, VAI INFORMAR SE O AJAX É UPDATE DA FOLHA DE PG OU NAO
	
	var d = document.getElementById(campo);
	
	var url = "http://www.f71lagos.com/intranet/";
	
	//d.style.background = 'transparent url(imagens/red-status.gif) no-repeat scroll right center';		STYLE ERROR
	//alert(tabela + " | valor: " + valor + " | campo: " + campo + " | nomeid: " + nomeid + " | id: " + id + "   pppp  = " + tipoaj);
	//alert("UPDATE " + tabela + " SET " + campo + " = ' " + valor + " ' WHERE " + nomeid + " = ' " + id + "'");
	
	d.style.background = 'transparent url(' + url + 'imagens/carregando/CIRCLE_BALL_branco.gif) no-repeat scroll right center';
	
	xmlHttp.onreadystatechange=function() {
		d.style.background = 'transparent url(' + url + 'imagens/carregando/CIRCLE_BALL_branco.gif) no-repeat scroll right center';
		if(xmlHttp.readyState==4){
			var resposta = xmlHttp.responseText;
			if(resposta == "ERRO"){
				d.style.background = 'transparent url(' + url + 'carregando/yellow-status.gif) no-repeat scroll right center';
			}else{
				d.style.background = 'transparent url(' + url + 'carregando/green-status.gif) no-repeat scroll right center';
			}
			//alert(xmlHttp.responseText);
		}
	}

	xmlHttp.open("GET",url + 'classes/ajaxupdate.php?tabela=' + tabela + '&valor=' + valor + '&campo=' + campo + '&nomeid=' + nomeid + '&id=' + id + '&tipo=' + tipoaj,true);
	xmlHttp.send(null);
  
}


//AJAX INSERT
function ajaxInsert(tabela,valor,campo,nomeid,id,tipoaj){
	var xmlHttp;
	try{											  // Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
  	} catch (e) {										  // Internet Explorer
  		try {
    	xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
    	try {
      	xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    } catch (e) {
      	alert("Your browser does not support AJAX!");
      	return false;
		}
	}
	}

	var d = document.getElementById(campo);
	

	//d.style.background = 'transparent url(imagens/red-status.gif) no-repeat scroll right center';		STYLE ERROR
	
	//alert(tabela + " | valor: " + valor + " | campo: " + campo + " | nomeid: " + nomeid + " | id: " + id + "   pppp  = " + d);
	
	d.style.background = 'transparent url(imagens/carregando/CIRCLE_BALL_branco.gif) no-repeat scroll right center';
	
	xmlHttp.onreadystatechange=function() {
		d.style.background = 'transparent url(imagens/carregando/CIRCLE_BALL_branco.gif) no-repeat scroll right center';
		if(xmlHttp.readyState==4){
			d.style.background = 'transparent url(imagens/green-status.gif) no-repeat scroll right center';
			//alert(xmlHttp.responseText);
		}
	}
  
	var url = "";
	

	xmlHttp.open("GET",'classes/ajaxupdate.php?tabela=' + tabela + '&valor=' + valor + '&campo=' + campo + '&nomeid=' + nomeid + '&id=' + id + '&tipo=' + tipoaj,true);
	xmlHttp.send(null);
  
}


//LIMPA CACHE
function limpaCache(url){
	var aurl = '';
	if(url.indexOf("?")>=0){
		return aurl + "&rand=" + encodeURI(Math.random());
	}else{
		return aurl + "?randon=" + encodeURI(Math.random());
	}
}

// DROGA DE BANCO
function drogadebanco(){
	var tipoPG = document.getElementById("banco").value;
	var bancoselecionado = document.getElementById("banco").value;
	if(bancoselecionado == "0"){
		document.getElementById("agencia").style.display='none';
		document.getElementById("linhabanc2").style.display='none';
		/*document.getElementById("linhabanc3").style.display='none';			 */
	}else if(bancoselecionado != "0" & bancoselecionado != "9999"){
		document.getElementById("agencia").style.display='';
		document.getElementById("linhabanc2").style.display='';
		/*document.getElementById("linhabanc3").style.display='none';*/
	}else if(bancoselecionado == "9999"){
		document.getElementById("agencia").style.display='';
		document.getElementById("linhabanc2").style.display='';
		/*document.getElementById("linhabanc3").style.display='';*/
	}
}

//INSERTVALUEQUERY
function insertValueQuery(de,para,hidden) {
    var myQuery = document.getElementById(para);
	var myQueryHidden = document.getElementById(hidden);
    var myListBox = document.getElementById(de);
	
	document.all.id_projeto.style.display='none'; 

    if(myListBox.options.length > 0) {
        sql_box_locked = true;
        var chaineAj = ", ";
		var chaineAj2 = "";
        var NbSelect = 0;
        
		for(var i=0; i<myListBox.options.length; i++) {
            if (myListBox.options[i].selected){
                NbSelect++;
                if (NbSelect > 1)
                    chaineAj += ", ";
					chaineAj2 += "\n";
                chaineAj += myListBox.options[i].value;
				chaineAj2 += myListBox.options[i].text;
            }
        }

        //IE support
        if (document.selection && document.selection2) {
            myQuery.focus();
            sel = document.selection.createRange();
			sel2 = document.selection2.createRange();
            sel.text = chaineAj;
			sel.text = chaineAj2;
            document.all.insert.focus();
        }
		
        
		//MOZILLA/NETSCAPE support
        else if (document.all.sql_query.selectionStart || document.all.sql_query.selectionStart == "0") {
            var startPos = document.all.sql_query.selectionStart;
            var endPos = document.all.sql_query.selectionEnd;
            var chaineSql = document.all.sql_query.value;

            myQuery.value = chaineSql.substring(0, startPos) + chaineAj + chaineSql.substring(endPos, chaineSql.length);
        } else {
            myQuery.value += chaineAj;
			myQueryHidden.value += chaineAj2;
        }
        sql_box_locked = false;
    }
}


//VARIOS AJAX
function AjaxVarios(destino,campo,retorno,idajax){
    
    
	var xmlHttp;
	try{											  // Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
  	} catch (e) {										  // Internet Explorer
  		try {
    	xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
    	try {
      	xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    } catch (e) {
      	alert("Your browser does not support AJAX!");
      	return false;
		}
	}
	}

	var d = document.getElementById(campo);
	var enviar = d.value;
	var r = document.getElementById(retorno);
	
	var url = "http://www.f71idr.com/intranet/";
	d.style.background = 'transparent url(' + url + 'imagens/carregando/CIRCLE_BALL_branco.gif) no-repeat scroll right center';
	
	xmlHttp.onreadystatechange=function() {
		d.style.background = 'transparent url(' + url + 'imagens/carregando/CIRCLE_BALL_branco.gif) no-repeat scroll right center';
		if(xmlHttp.readyState==4){
			var resposta = xmlHttp.responseText;
			//alert(destino + '?id=' + idajax + '&ajax=' + enviar);
			if(resposta == "ERRO"){
				d.style.background = 'transparent url(' + url + 'imagens/yellow-status.gif) no-repeat scroll right center';
			}else{
				d.style.background = 'transparent url(' + url + 'imagens/green-status.gif) no-repeat scroll right center';
				r.innerHTML = resposta;
			}
			//alert(xmlHttp.responseText);
		}
	}
	
	if(enviar == ""){
		enviar = 1;
	}
	
	xmlHttp.open("GET",destino + '?id=' + idajax + '&ajax=' + enviar,true);
	xmlHttp.send(null);
  
}


//VARIOS UF
function ajaxuf(campo,retorno){
	var xmlHttp;
	try{											  // Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
  	} catch (e) {										  // Internet Explorer
  		try {
    	xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
    	try {
      	xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    } catch (e) {
      	alert("Your browser does not support AJAX!");
      	return false;
		}
	}
	}

	var d = document.getElementById(campo);
	var enviar = d.value;
	
	var r = document.getElementById(retorno);
	
	var url = "http://www.f71lagos.com/intranet/";
	var urlajax = "http://www.f71lagos.com/intranet/ajax/";
	
	d.style.background = 'transparent url(' + url + 'imagens/carregando/CIRCLE_BALL_branco.gif) no-repeat scroll right center';
	
	xmlHttp.onreadystatechange=function() {
		d.style.background = 'transparent url(' + url + 'imagens/carregando/CIRCLE_BALL_branco.gif) no-repeat scroll right center';
		if(xmlHttp.readyState==4){
			var resposta = xmlHttp.responseText;
			if(resposta == "ERRO"){
				d.style.background = 'transparent url(' + url + 'carregando/yellow-status.gif) no-repeat scroll right center';
			}else{
				d.style.background = 'transparent url(' + url + 'carregando/green-status.gif) no-repeat scroll right center';
				r.innerHTML = resposta;
			}
			//alert(xmlHttp.responseText);
		}
	}
	
	if(enviar == ""){
		enviar = 1;
	}
	
	xmlHttp.open("GET",urlajax + 'ajax_reg.php?uf=' + enviar,true);
	xmlHttp.send(null);
  
}

//AJAX UPLOAD
function ajaxupdatefolha(tabela,valor,campo,nomeid,id,tipoaj){
	var xmlHttp;
	try{											  // Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
  	} catch (e) {										  // Internet Explorer
  		try {
    	xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
    	try {
      	xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    } catch (e) {
      	alert("Your browser does not support AJAX!");
      	return false;
		}
	}
	}
	
	//A VARIAVEL pasta ERA UTILIZADA PARA INFORMAR O PATH DAS IMAGENS E TAL
	//MAS AGORA MUDEI, VAI INFORMAR SE O AJAX É UPDATE DA FOLHA DE PG OU NAO
	
	var d = document.getElementById(campo);
	var campo2 = campo.split("_");
	var url = "http://www.f71lagos.com/intranet/";
	
	//d.style.background = 'transparent url(imagens/red-status.gif) no-repeat scroll right center';		STYLE ERROR
	//alert(tabela + " | valor: " + valor + " | campo: " + campo + " | nomeid: " + nomeid + " | id: " + id + "   pppp  = " + tipoaj);
	//alert("UPDATE " + tabela + " SET " + campo2[0] + " = ' " + valor + " ' WHERE " + nomeid + " = ' " + id + "'");
	
	d.style.background = 'transparent url(' + url + 'imagens/carregando/CIRCLE_BALL_branco.gif) no-repeat scroll right center';
	
	xmlHttp.onreadystatechange=function() {
		d.style.background = 'transparent url(' + url + 'imagens/carregando/CIRCLE_BALL_branco.gif) no-repeat scroll right center';
		if(xmlHttp.readyState==4){
			var resposta = xmlHttp.responseText;
			if(resposta == "ERRO"){
				d.style.background = 'transparent url(' + url + 'carregando/yellow-status.gif) no-repeat scroll right center';
			}else{
				d.style.background = 'transparent url(' + url + 'carregando/green-status.gif) no-repeat scroll right center';
			}
			//alert(xmlHttp.responseText);
		}
	}

	xmlHttp.open("GET",url + 'classes/ajaxupdate.php?tabela=' + tabela + '&valor=' + valor + '&campo=' + campo2[0] + '&nomeid=' + nomeid + '&id=' + id + '&tipo=' + tipoaj,true);
	xmlHttp.send(null);
  
}

//AJAX UPDATE NO CHECK BOX
function ajupdatecheck(tabela,campo,nomeid,id,tipoaj){
        
    var xmlHttp;
    try{											  // Firefox, Opera 8.0+, Safari
        xmlHttp=new XMLHttpRequest();
    } catch (e) {										  // Internet Explorer
        try {
            xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
                alert("Your browser does not support AJAX!");
                return false;
            }
        }
    }
	
    //A VARIAVEL pasta ERA UTILIZADA PARA INFORMAR O PATH DAS IMAGENS E TAL
    //MAS AGORA MUDEI, VAI INFORMAR SE O AJAX É UPDATE DA FOLHA DE PG OU NAO

    var d = document.getElementById(campo);
    var campo2 = campo.split("_");
    var retorno = "retorno_" + campo2[1];
    var r = document.getElementById(retorno);

    if(d.checked == true){
            var valor = 2;
    }else{
            var valor = 1;
    }

    var url = "http://www.f71lagos.com/intranet/";

    //d.style.background = 'transparent url(imagens/red-status.gif) no-repeat scroll right center';		STYLE ERROR
    //alert(tabela + " | valor: " + valor + " | campo: " + campo + " | nomeid: " + nomeid + " | id: " + id + "   pppp  = " + tipoaj);
    //alert("UPDATE " + tabela + " SET " + campo2[0] + " = ' " + valor + " ' WHERE " + nomeid + " = ' " + id + "'");

    r.style.background = 'transparent url(' + url + 'imagens/carregando/CIRCLE_BALL_branco.gif) no-repeat scroll right center';

    xmlHttp.onreadystatechange=function() {
            r.style.background = 'transparent url(' + url + 'imagens/carregando/CIRCLE_BALL_branco.gif) no-repeat scroll right center';
            if(xmlHttp.readyState==4){
                    var resposta = xmlHttp.responseText;
                    if(resposta == "ERRO"){
                            r.style.background = 'transparent url(' + url + 'imagens/yellow-status.gif) no-repeat scroll right center';
                    }else{
                            r.style.background = 'transparent url(' + url + 'imagens/green-status.gif) no-repeat scroll right center';
                    }
                    //alert(xmlHttp.responseText);
            }
    }

    xmlHttp.open("GET",url + 'classes/ajaxupdate.php?tabela=' + tabela + '&valor=' + valor + '&campo=' + campo2[0] + '&nomeid=' + nomeid + '&id=' + id + '&tipo=' + tipoaj,true);
    xmlHttp.send(null);
        
}