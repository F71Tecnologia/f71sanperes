/*******************************************************
dmsAutoComplete v1.1
Copyright (C) 2006 Rafael Dohms.
**************************

Author:
	Rafael Dohms (rafael at rafaeldohms dot com dot br)
	http://www.rafaeldohms.com.br
Baseado em conceito por: (concept by) 
	joekepley at yahoo (dot) com

*******************************************************
en:
This is an AJAX implementation of a auto-complete/auto-
suggest script. It uses PHP to return a XML result list
and displays it in a div for selection.

pt_br:
Este script é uma implementação AJAX de um script de
auto-compleção/auto-sugestão, similar o utilizado pelo
Google. Ele usa um backend PHP que retorna um XML de 
resultados mostrados em um DIV para seleção
*******************************************************/
/******************************************************
Change Log:

v1.1
- Adaptação para remover limite de "nome do objeto"
- Alteração CSS para quebras de linha no FireFox
- FIX: apertar TAB/ENTER sem nada escolhido gerava erro

*******************************************************/
/******************************************************
USAGE:
----> example.htm
----> http://www.rafaeldohms.com.br/myprojects/autocomplete/example.htm

<script>
//Instanciar objeto
var AC = new dmsAutoComplete('string','acDiv');

//Definir função de retorno
AC.chooseFunc = function(id,label){
	alert(id+'-'+label);
}
</script>
*******************************************************/

function dmsAutoComplete(elem,divname){
	
	var me = this;
	this.elem = document.getElementById(elem);
	this.highlighted = -1;
	this.arrItens = new Array();
	this.ajaxTarget = 'dmsAC.php';
	this.chooseFunc = null; //Função para executar com obj selecionado
	this.div = document.getElementById(divname);

	//Keycodes que devem ser monitorados
	var TAB = 9;
	var ESC = 27;
	var KEYUP = 38;
	var KEYDN = 40;
	var ENTER = 13;
	
	//Tamanho do DIV = Tamanho do campo
	this.div.style.width = this.elem.style.width;
	
	//Desabilitar autocomplete IE
	me.elem.setAttribute("autocomplete","off");
	
	//Crate AJAX Request
	this.ajaxReq = createRequest();

	//Ação a ser executada no KEYDOWN (funções de navegação)
	me.elem.onkeydown = function(ev)
	{
		var key = me.getKeyCode(ev);

		switch(key)
		{
			case TAB:
			case ENTER:
				if (me.highlighted.id != undefined){
					me.acChoose(me.highlighted.id);
				}
				me.hideDiv();
				return false;
			break;

			case ESC:
				me.hideDiv();
				return false;
			break;

			case KEYUP:
				me.changeHighlight('up');
				return false;
			break;

			case KEYDN:
				me.changeHighlight('down');
				return false;
			break;
		}
		
	};
	
	this.setElemValue = function(){
		var a = me.highlighted.firstChild;
		me.elem.value = a.innerHTML;
	}
	
	this.highlightThis = function(obj,yn){
		if (yn = 'y'){
			me.highlighted.className = '';
			me.highlighted = obj;
			me.highlighted.className = 'selected';
			
			me.setElemValue(obj);
			
		}else{
			obj.className = '';
			me.highlighted = '';
		}
	}
	
	this.changeHighlight = function(way){
		
		if (me.highlighted != '' && me.highlighted != null ){
			me.highlighted.className = '';
			switch(way){
				case 'up':
					if(me.highlighted.parentNode.firstChild == me.highlighted){
						me.highlighted = me.highlighted.parentNode.lastChild;
					}else{
						me.highlighted = me.highlighted.previousSibling;
					}
				break;
				case 'down':
					if(me.highlighted.parentNode.lastChild == me.highlighted){
						me.highlighted = me.highlighted.parentNode.firstChild;
					}else{
						me.highlighted = me.highlighted.nextSibling;
					}
				break;
				
			}
			me.highlighted.className = 'selected';
			me.setElemValue();
		}else{
			switch(way){
				case 'up':
					me.highlighted = me.div.firstChild.lastChild;
				break;
				case 'down':
					me.highlighted = me.div.firstChild.firstChild;
				break;
				
			}
			me.highlighted.className = 'selected';
			me.setElemValue();
		}
		
	}
	
	//Rotina no KEYUP (pegar input)
	me.elem.onkeyup = function(ev) 
	{
		var key = me.getKeyCode(ev);
		switch(key)
		{
		//The control keys were already handled by onkeydown, so do nothing.
		case TAB:
		case ESC:
		case KEYUP:
		case KEYDN:
			return;
		case ENTER:
			return false;
			break;
		default:
			//Enviar query por AJAX
			if (me.ajaxReq != undefined){

				me.ajaxReq.open("POST", me.ajaxTarget, true);
				me.ajaxReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
				me.ajaxReq.onreadystatechange = me.acResult;
				
				var param = 'string=' + me.elem.value;
				
				me.ajaxReq.send(param);
				
			}
			
			//Remover elementos highlighted
			me.highlighted = '';
		}
	};
	
	//Sumir com autosuggest
	me.elem.onblur = function() {
		me.hideDiv();
	}
	
	//Ajax return function
	this.acResult = function(){
		
		if (me.ajaxReq.readyState == 4){
				
			//alert(linkReq.responseText); //DEBUG
			
			me.showDiv()
			
			//Pegar resposta do servidor
			var xmlRes = me.ajaxReq.responseXML;
		
			var itens = xmlRes.getElementsByTagName('item');
			var itCnt = itens.length;
	
			//Pegar primeiro fliho
			me.div.innerHTML = '';
			var ul = document.createElement('ul');
			me.div.appendChild(ul);
			
			if (itCnt > 0){
				for (i=0; i<itCnt; i++){
					
					//Popular array global
					me.arrItens[itens[i].getAttribute("id")] = new Array();
					me.arrItens[itens[i].getAttribute("id")]['label'] = itens[i].getAttribute("label");
					
					//Adicionar LI
					var li = document.createElement('li');
					li.id = itens[i].getAttribute("id");
					li.onmouseover = function(){ this.className = 'selected'; me.highlightThis(this,'y')}
					li.onmouseout  = function(){ this.className = '';  me.highlightThis(this,'n')}
					li.onmousedown = function() {
						me.acChoose(this.id);
						me.hideDiv();
						return false;
					}
					
					var a = document.createElement('a');
					a.href = '#';
					a.onclick = function() { return false; }
					a.innerHTML = unescape(itens[i].getAttribute("label"));

					li.appendChild(a);
					ul.appendChild(li);	
				}
			}else{
				me.hideDiv();	
			}
		}
	}
	
	this.acChoose = function (id){
		
		//Setar Vendedor e Cliente apra visu
		if (id != ''){
			//Função de retorno (Opcional)
			if (me.chooseFunc != null){
				me.chooseFunc(id,unescape(me.arrItens[id]['label']));
			}
		}
		
		//Esconder lista de clientes
		me.hideDiv();
		me.elem.value = '';
		
	}

	this.positionDiv = function()
	{
		var el = this.elem;
		var x = 0;
		var y = el.offsetHeight;

		//Walk up the DOM and add up all of the offset positions.
		while (el.offsetParent && el.tagName.toUpperCase() != 'BODY')
		{
			x += el.offsetLeft;
			y += el.offsetTop;
			el = el.offsetParent;
		}

		x += el.offsetLeft;
		y += el.offsetTop;

		this.div.style.left = x + 'px';
		this.div.style.top = y + 'px';
	};

	this.hideDiv = function(){
		
		me.highlighted = '';
		me.div.style.display = 'none';
	
	}

	this.showDiv = function(){
		
		me.highlighted = '';
		me.positionDiv();
		me.div.style.display = 'block';
	
	}
	
	//HELPER FUNCTIONS
	
	/********************************************************
	Helper function to determine the keycode pressed in a 
	browser-independent manner.
	********************************************************/
	this.getKeyCode = function(ev)
	{
		if(ev)			//Moz
		{
			return ev.keyCode;
		}
		if(window.event)	//IE
		{
			return window.event.keyCode;
		}
	};

	/********************************************************
	Helper function to determine the event source element in a 
	browser-independent manner.
	********************************************************/
	this.getEventSource = function(ev)
	{
		if(ev)			//Moz
		{
			return ev.target;
		}
	
		if(window.event)	//IE
		{
			return window.event.srcElement;
		}
	};

	/********************************************************
	Helper function to cancel an event in a 
	browser-independent manner.
	(Returning false helps too).
	********************************************************/
	this.cancelEvent = function(ev)
	{
		if(ev)			//Moz
		{
			ev.preventDefault();
			ev.stopPropagation();
		}
		if(window.event)	//IE
		{
			window.event.returnValue = false;
		}
	}
}


//Função que cria AJAX Request
function createRequest() {
  try {
    request = new XMLHttpRequest();
  } catch (trymicrosoft) {
    try {
      request = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (othermicrosoft) {
      try {
        request = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (failed) {
        request = false;
      }
    }
  }

  if (!request)
    alert("Error initializing XMLHttpRequest!");
  else
  	return request;
}