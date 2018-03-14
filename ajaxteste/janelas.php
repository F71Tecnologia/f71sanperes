
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Documento sem t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<style type="text/css">
<!--
.window {
    font-family: Arial, Verdana, sans serif, Helvetica;
    font-size: 12px;
    color: #000000;
    background-color: #CCCCCC;
    border-top: 2px solid #EEEEEE;
    border-left: 2px solid #EEEEEE;
    border-right: 2px solid #666666;
    border-bottom: 2px solid #666666;
}
.windowTopBarInactive {
    font-family: Arial, Verdana, sans serif, Helvetica;
    font-size: 12px;
    font-weight: bold;
    color: #FFFFFF;
    position:absolute;
    left:0px;
    top:1px;
    width:100%;
    height:18px;
    overflow:hidden;
    text-indent: 21px;
    background-image: url(icon.gif);
    background-repeat: no-repeat;
    background-position: left top;
    background-color: #999999;
    cursor: default;
}
.windowTopBarActive {
    font-family: Arial, Verdana, sans serif, Helvetica;
    font-size: 12px;
    font-weight: bold;
    color: #FFFFFF;
    position:absolute;
    left:0px;
    top:1px;
    width:100%;
    height:18px;
    overflow:hidden;
    text-indent: 21px;
    background-image: url(icon.gif);
    background-repeat: no-repeat;
    background-position: left top;
    background-color: #333399;
    cursor: default;
}
.windowButtonClose{
    background-image: url(close.gif);
    background-repeat: no-repeat;
    background-position: center center;
    position:absolute;
    top:2px;
    right:2px;
    width:16px;height:16px;
}
.windowButtonMax{
    background-image: url(max.gif);
    background-repeat: no-repeat;
    background-position: center center;
    position:absolute;
    top:2px;
    right:20px;
    width:16px;height:16px;
}
.windowButtonMin{
    background-image: url(min.gif);
    background-repeat: no-repeat;
    background-position: center center;
    position:absolute;
    top:2px;
    right:36px;
    width:16px;height:16px;
}
.windowRedim{
    background-image: url(redim.gif);
    background-repeat: no-repeat;
    background-position: top left;
    position:absolute;
    bottom:0px;
    right:0px;
    width:9px;
    height:9px;
    overflow:hidden;
    cursor: nw-resize;
}

-->
</style>
<script language="JavaScript" type="text/JavaScript">
var maior_index=500; // usado para passar os troço pra frente
/****************************************************
Drag-n-Drop 1.0
24/01/2004
by Micox-naironjcg@hotmail.com
****************************************************/
function move(e){
/* antibugs */
var targ;
if (!e) var e = window.event;
if (e.target) targ = e.target;
else if (e.srcElement) targ = e.srcElement;
if (targ.nodeType == 3) targ = targ.parentNode;
/* fim antibugs */

    var pos_alvo_y,pos_nova_y,pos_alvo_x,pos_nova_x,alvao;
    alvao = quem_movimenta_mico_editor //document.getElementById(quem_movimenta_mico_editor);
    pos_alvo_y = alvao.style.top
    pos_alvo_y = pos_alvo_y.substr(0,pos_alvo_y.indexOf("px"))*1
    pos_nova_y = pos_alvo_y + (e.clientY-pos_antiga_y)
    alvao.style.top = pos_nova_y + "px"
    pos_antiga_y = e.clientY
    
    pos_alvo_x = alvao.style.left
    pos_alvo_x = pos_alvo_x.substr(0,pos_alvo_x.indexOf("px"))*1
    pos_nova_x = pos_alvo_x + (e.clientX-pos_antiga_x)
    alvao.style.left = pos_nova_x + "px"
    pos_antiga_x = e.clientX
}
function resiza(e){
/* antibugs */
var targ;
if (!e) var e = window.event;
if (e.target) targ = e.target;
else if (e.srcElement) targ = e.srcElement;
if (targ.nodeType == 3) targ = targ.parentNode;
/* fim antibugs */

    var pos_alvo_y,pos_nova_y,pos_alvo_x,pos_nova_x,alvao;
    alvao = quem_resiza_mico_editor
    h_alvo_y = alvao.style.height
    h_alvo_y = h_alvo_y.substr(0,h_alvo_y.indexOf("px"))*1
    h_nova_y = h_alvo_y + (e.clientY-h_antiga_y)
    if(h_nova_y>32){
        alvao.style.height = h_nova_y + "px"
        h_antiga_y = e.clientY
    }

    
    w_alvo_x = alvao.style.width
    w_alvo_x = w_alvo_x.substr(0,w_alvo_x.indexOf("px"))*1
    w_nova_x = w_alvo_x + (e.clientX-w_antiga_x)
    if(w_nova_x>100){
        alvao.style.width = w_nova_x + "px"
        w_antiga_x = e.clientX
    }
}
function setMen(mensagem){
    document.getElementById("men").innerHTML=mensagem
}
function appendMen(mensagem){
    document.getElementById("men").innerHTML+= "<br>" + mensagem;
}
function startDrag(quem,e){
/* antibugs */
var targ,quem;
if (!e) var e = window.event;
if (e.target) targ = e.target;
else if (e.srcElement) targ = e.srcElement;
if (targ.nodeType == 3) targ = targ.parentNode;
/* fim antibugs */

    //variave global quem_movimenta_mico_editor; pos_inicial_mico_ed
    quem_movimenta_mico_editor = quem
    pos_antiga_x = e.clientX;
    pos_antiga_y = e.clientY;
    document.onmousemove = move
    document.onmouseup = endDrag
}
function naBorda(quem,e,extra){
/* antibugs */
var targ,quem;
if (!e) var e = window.event;
if (e.target) targ = e.target;
else if (e.srcElement) targ = e.srcElement;
if (targ.nodeType == 3) targ = targ.parentNode;
/* fim antibugs */

    var base = Number(quem.style.top.replace("px","")) + Number(quem.style.height.replace("px",""));

    if(e.clientY < (base + extra) && e.clientY > (base - extra)){
        return true;
    }else{  return false; }

}
function mouseovermove(quem,e){
/* antibugs */
var targ,quem;
if (!e) var e = window.event;
if (e.target) targ = e.target;
else if (e.srcElement) targ = e.srcElement;
if (targ.nodeType == 3) targ = targ.parentNode;
/* fim antibugs */
    setMen(e.clientY + " " + naBorda(quem,e,2));
}
function resizaStart(quem,e){
/* antibugs */
var targ,quem;
if (!e) var e = window.event;
if (e.target) targ = e.target;
else if (e.srcElement) targ = e.srcElement;
if (targ.nodeType == 3) targ = targ.parentNode;
/* fim antibugs */

    //variave global quem_movimenta_mico_editor;
    quem_resiza_mico_editor = quem
    w_antiga_x = e.clientX;
    h_antiga_y = e.clientY;
    document.onmousemove = resiza
    document.onmouseup = endResiza
}
function endDrag(){ 
    document.onmousemove=""
    document.onmouseup=""
    delete pos_antiga_x,pos_antiga_y;
}
function endResiza(){ 
    document.onmousemove="";
    document.onmouseup="";
    delete w_antiga_x,h_antiga_y;
}
function maximiza(quem){
    var quem_ = quem.style
    quem_.left="0px";quem_.top="0px";
    quem_.width="100%";
    quem_.height= "100%";
}
function restaura(quem,x,y,w,h){
    var quem_ = quem.style
    quem_.left= x;quem_.top=y;
    quem_.width= w;
    quem_.height= h;
}
function troca(quem){
    var quem_ = quem.style
    if(quem_.width=="100%" && quem_.height=="100%"){
        restaura(quem,"20px","50px","250px","150px")
    }else{
        maximiza(quem);
    }
}
function setaFocus(quem){
    var divs; //abaixo vou pegar as div window e tirar o foco delas
    divs = document.getElementsByTagName("div");
    for(var i=0;i<divs.length;i++){
        if(divs[i].className=="window"){
            getChildItem(divs[i],"topBar").className="windowTopBarInactive";
        }
    }
    quem.style.zIndex = maior_index++;
    getChildItem(quem,"topBar").className="windowTopBarActive";
}
function getChildItem(objeto,idFilho){
    for(var i=0;i<objeto.childNodes.length;i++){
        //appendMen(i);
        if(objeto.childNodes[i].id==idFilho){
            return objeto.childNodes[i];
            i = objeto.childNodes.length;
        }
    }
    return null;
}
//-->
</SCRIPT></script>
</head>

<body>
<div id="Layer1" style="position:absolute; left:300px; top:110px; width:288px; height:138px; z-index:8" class="window" onmousedown="setaFocus(this)"> 
  <div id="topBar" class="windowTopBarActive" onmousedown="startDrag(this.parentNode,event)">Janela 1 - viva blablablablab 
  </div>

  <div class="windowButtonClose"></div>
  <div class="windowButtonMin"></div>
  <div class="windowButtonMax" onmouseup="troca(this.parentNode)"></div> 
  <div class="windowRedim" onmousedown="resizaStart(this.parentNode,event)"></div>  
  <div id="left"></div>
  <div id="top"></div>
  <div id="width"></div>
  <div id="height"></div>
  <div id="conteudos" style="position:absolute;top:20px;border:1px solid black;width:100%;overflow:hidden;height:70%;"><iframe style="width:90%;border:0px;height:90%"src="file:///D:/Documents%20and%20Settings/N%E1iron/Desktop/framework/Untitled-1.htm"></iframe></div>

</div>

<div id="Layer2" style="position:absolute; left:10px; top:100px; width:200px; height:138px; z-index:7" class="window" onmousedown="setaFocus(this)"> 
  <div id="topBar" class="windowTopBarInactive" onmousedown="startDrag(this.parentNode,event)">Janela 2</div>
  asdfasdf 
   <div class="windowButtonClose"></div>
  <div class="windowButtonMin"></div>
  <div class="windowButtonMax" onmouseup="troca(this.parentNode)"></div>
  <div class="windowBorderTop"></div>
  <div class="windowBorderBottom"></div>
  <div class="windowBorderLeft"></div>

  <div class="windowBorderRight"></div>
</div>

<div id="men"></div>
<div id="men2"></div>
<div id="Layer3" style="position:absolute; left:144px; top:10px; width:286px; height:224px; z-index:3; background-color: #999999; border: 1px none #000000;"></div>
</body>
</html>