<?php
<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

include "funcoes.php";

$id_user = $_COOKIE['logado'];

$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

$grupo_usuario = $row_user['grupo_usuario'];
$regiao_usuario = $row_user['id_regiao'];
$apelido_usuario = $row_user['nome1'];
$perfil_usuario = $row_user['tipo_usuario'];

$regiao = $regiao_usuario;

//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkFolha = encrypt("$regiao"); 
$linkFolha = str_replace("+","--",$linkFolha);
// -----------------------------



switch ($perfil_usuario) {
	case 0:							//USU�RIO B�SICO


$menu = "
<table width='97%' border='0' cellspacing='0' class='menu'>
<tr>
<td height='17' align='center'> <div align='left'></div></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='suporte/suporte.php?regiao=$regiao_usuario' class='link2' target='_blank'>
<img src='imagensmenu2/suporte.gif' border=0 align='absmiddle'>
SUPORTE ON-LINE
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='javascript:window.location.reload()' class='link2' >
<img src='imagensmenu2/atualizar.gif' border=0 align='absmiddle'>
ATUALIZAR
</a>
</td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('ver.php?id=1&regiao=$regiao_usuario','VisPro','780','450','yes');\">
<img src='imagensmenu2/projeto.gif' border=0 align='absmiddle'>
Visualizar Projetos
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='ver_tudo.php?id=7&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/unidades.gif' border=0 align='absmiddle'>
Visualizar Unidades
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='ver_tudo.php?id=4&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/bancos.gif' border=0 align='absmiddle'>
Visualizar Bancos
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='ver_tudo.php?id=5&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/apolice.gif' border=0 align='absmiddle'>
Visualizar Ap�lices
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='ver_tudo.php?id=1&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/atividade.gif' border=0 align='absmiddle'>
Visualizar Atividades
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('tarefas.php?id=1&id_user=$id_user','EnTaref','750','450','yes');\">
<img src='imagensmenu2/ferramenta.gif' border=0 align='absmiddle'>
Enviar Tarefas
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='http://webmail.sorrindo.org' target='_blank' class='link2'>
<img src='imagensmenu2/email.gif' border=0 align='absmiddle'>
Consultar E-mail
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('gestaocompras.php?id=1&regiao=$regiao_usuario','GestCompras','780','550','yes');\">
<img src='imagensmenu2/compras.gif' border=0 align='absmiddle'>
Gest�o de compras
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='cadastro.php?id=10&regiao=$regiao_usuario&id_user=$id_user' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/ponto.gif' border=0 align='absmiddle'>
Marcar Ponto
</a></td>
</tr>


<tr>
<td height='17' class='tr'>
<a href='#' onClick=\"javascript:abrir('escala/escala.php?id=1&regiao=$regiao_usuario','Escala','780','550','yes');\" class='link2'>
<img src='imagensmenu2/escala.gif' border=0 align='absmiddle'>
Escala de Trabalho
</a></td>
</tr>


<tr>
<td height='17' class='tr'>
<a href='fornecedores.php?regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensfinanceiro/cadastrofornecedores.gif' border=0 align='absmiddle'>
Visualizar Fornecedores
</a></tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('cadfornecedores.php?regiao=$regiao_usuario','CadForne','800','500','yes');\">
<img src='imagensfinanceiro/cadastrofornecedores.gif' border=0 align='absmiddle'>
Cadastrar Fornecedor
</a></tr>

<tr>
<td height='17' class='tr'>
<a href='patrimonio.php?regiao=$regiao_usuario&id=1' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/patrimonio.gif' border=0 align='absmiddle'>
Patrim�nio
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('ctps.php?regiao=$regiao_usuario&id=1','CTPS','790','450','yes');\">
<img src='imagensmenu2/ctps.gif' border=0 align='absmiddle'>
Controle CTPS
</a></td>
</tr>


<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('frota/frota.php?regiao=$regiao_usuario','ConFrota','790','450','yes');\">
<img src='imagensmenu2/c1.gif' border=0 align='absmiddle'>
Controle de Frota
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='folhaspg/folha.php?id=9&enc=$linkFolha' class='link2' target='_blank'>
<img src='imagensmenu2/cadastro.gif' border='0' align='absmiddle'>
Folha de Pagamento
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('frota/reembolso.php','Reembolso','790','450','yes');\">
<img src='imagensmenu2/reembolso.gif' border=0 align='absmiddle'>
Reembolso
</a></td>
</tr>

<tr>
<td height='17' class='tr'>&nbsp;</td>
</tr>

<tr>
<td height='17' align='center' class='tr'> <div align='left'></div></td>
</tr>

</table>";

break;

	case 1:						//USU�RIO DIRETOR

$menu = "
<table width='97%' border='0' cellspacing='0' class='menu'>
<tr>
<td height='17' align='center'> <div align='left'></div></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='adm/login.php' target='_blank' class='link2'>
<img src='imagensmenu2/admgeral.gif' border=0 align='absmiddle'>
ADMINISTRA��O
</a>
</td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' onClick=\"javascript:abrir('contabil/login3.php?mt=$row_user[id_master]','Gest�o_Cont�bil','750','450','yes');\" class='link2'>
<img src='imagensmenu2/contabilp.gif' border=0 align='absmiddle'>
GEST�O CONT�BIL
</a>
</td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' onClick=\"javascript:abrir('documentos_empresa/cadastro.php?mt=$row_user[id_master]','Cadastro_de_documentos','750','450','yes');\" class='link2'>
<img src='imagensmenu2/Folder-close_blue.png' border=0 align='absmiddle'>
DOCUMENTOS
</a>
</td>
</tr>



<tr>
<td height='17' class='tr'>
<a href='suporte/suporte.php?regiao=$regiao_usuario' class='link2' target='_blank'>
<img src='imagensmenu2/suporte.gif' border=0 align='absmiddle'>
SUPORTE ON-LINE
</a>
</td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='javascript:window.location.reload()' class='link2' >
<img src='imagensmenu2/atualizar.gif' border=0 align='absmiddle'>
ATUALIZAR
</a></td>
</tr>


<tr>	
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('ver.php?id=1&regiao=$regiao_usuario','VisuPro','780','450','yes');\">
<img src='imagensmenu2/projeto.gif' border=0 align='absmiddle'>
Visualizar Projetos
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('cooperativas/cooperativa.php?id=1&regiao=$regiao_usuario','Coopera','780','450','yes');\">
<img src='imagensmenu2/cooperativa.gif' border=0 align='absmiddle'>
Cooperativas / PJ
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('gestaocompras.php?id=1&regiao=$regiao_usuario','GCompras','780','550','yes');\">
<img src='imagensmenu2/compras.gif' border=0 align='absmiddle'>
Gest�o de compras
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('cadastro.php?id=13&regiao=$regiao_usuario','CadUnidade','750','450','yes');\">
<img src='imagensmenu2/unidades.gif' border=0 align='absmiddle'>
Cadastrar Unidades
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='ver_tudo.php?id=7&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/unidades.gif' border=0 align='absmiddle'>
Visualizar Unidades
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('cadastro.php?id=2','CadRegiao','650','250','yes');\">
<img src='imagensmenu2/regiao.gif' border=0 align='absmiddle'>
Cadastrar Regi�o
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('cadastro.php?id=6&regiao=$regiao_usuario','CadBancos','750','450','yes');\">
<img src='imagensmenu2/bancos.gif' border=0 align='absmiddle'>
Cadastrar Bancos
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='ver_tudo.php?id=4&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/bancos.gif' border=0 align='absmiddle'>
Visualizar Bancos
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('cadastro.php?id=5&regiao=$regiao_usuario','CadApolice','750','450','yes');\">
<img src='imagensmenu2/apolice.gif' border=0 align='absmiddle'>
Cadastrar Ap�lice
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='ver_tudo.php?id=5&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/apolice.gif' border=0 align='absmiddle'>
Visualizar Ap�lices
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('cadastro.php?id=7&regiao=$regiao_usuario','CadAtivi','750','450','yes');\">
<img src='imagensmenu2/atividade.gif' border=0 align='absmiddle'>
Cadastrar Atividades
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='ver_tudo.php?id=1&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/atividade.gif' border=0 align='absmiddle'>
Visualizar Atividades
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('cadastro.php?id=3','CadUser','750','450','yes');\">
<img src='imagensmenu2/ca_user.gif' border=0 align='absmiddle'>
Cadastrar Usu�rio
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='ver_tudo.php?id=6&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/ca_user.gif' border=0 align='absmiddle'>
Visualizar Usu�rios
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('tarefas.php?id=1&id_user=$id_user','EnviTarefa','750','450','yes');\">
<img src='imagensmenu2/ferramenta.gif' border=0 align='absmiddle'>
Enviar Tarefas
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='http://webmail.sorrindo.org' target='_blank' class='link2'>
<img src='imagensmenu2/email.gif' border=0 align='absmiddle'>
Consultar E-mail
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('bolsista_class.php?id=1&regiao=$regiao_usuario','AVDesempenho','750','450','yes');\">
<img src='imagensmenu2/avaliacao.gif' border=0 align='absmiddle'>
Av. de Desempenho
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('ver_tudo.php?id=15&regiao=$regiao_usuario&id_user=$id_user','RelGestao','750','450','yes');\">
<img src='imagensmenu2/gestao.gif' border=0 align='absmiddle'>
Relat�rios de Gest�o
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='cadastro.php?id=10&regiao=$regiao_usuario&id_user=$id_user' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/ponto.gif' border=0 align='absmiddle'>
Marcar Ponto
</a></td>
</tr>


<tr>
<td height='17' class='tr'>
<a href='#' onClick=\"javascript:abrir('escala/escala.php?id=1&regiao=$regiao_usuario','EscalaTrab','780','550','yes');\" class='link2'>
<img src='imagensmenu2/escala.gif' border=0 align='absmiddle'>
Escala de Trabalho
</a></td>
</tr>


<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('cadastro.php?id=14&regiao=$regiao_usuario','TipoPG','750','450','yes');\">
<img src='imagensmenu2/dinheiro.gif' border=0 align='absmiddle'>
Tipo de Pagamento
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<div>
<a href='ver_tudo.php?id=8&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/dinheiro.gif' border=0 align='absmiddle'>
Ver Tipo de PG
</a></div></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('cadfornecedores.php?regiao=$regiao_usuario','CadForne','800','500','yes');\">
<img src='imagensfinanceiro/cadastrofornecedores.gif' border=0 align='absmiddle'>
Cadastrar Fornecedor
</a></tr>

<tr>
<td height='17' class='tr'>
<a href='fornecedores.php?regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensfinanceiro/cadastrofornecedores.gif' border=0 align='absmiddle'>
Visualizar Fornecedores
</a></tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('ctps.php?regiao=$regiao_usuario&id=1','Controlectps','790','450','yes');\">
<img src='imagensmenu2/ctps.gif' border=0 align='absmiddle'>
Controle CTPS
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('frota/frota.php?regiao=$regiao_usuario','CFrota','790','450','yes');\">
<img src='imagensmenu2/c1.gif' border=0 align='absmiddle'>
Controle de Frota
</a></td>
</tr>


<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('frota/reembolso.php','Reembolso','790','450','yes');\">
<img src='imagensmenu2/reembolso.gif' border=0 align='absmiddle'>
Reembolso
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='folhaspg/folha.php?id=9&enc=$linkFolha' class='link2' target='_blank'>
<img src='imagensmenu2/cadastro.gif' border=0 align='absmiddle'>
Folha de Pagamento
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('login_adm.php','Financeiro','790','450','yes');\">
<img src='imagensmenu2/financeiro.gif' border=0 align='absmiddle'>
ADM &amp; Financeiro
</a></td>
</tr>


<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('login_rh.php?','GestaoRH','790','450','yes');\">
<img src='imagensmenu2/rh.gif' border=0 align='absmiddle'>
Gest�o de RH
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='patrimonio.php?regiao=$regiao_usuario&id=1' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/patrimonio.gif' border=0 align='absmiddle'>
Patrim�nio
</a></td>
</tr>

<tr>
<td height='17' class='tr'>&nbsp;</td>
</tr>

<tr>
<td height='17' align='center' class='tr'> <div align='left'></div></td>
</tr>
</table>";

break;

	case 2:							//USU�RIO FINANCEIRO

$menu = "

<table width='97%' border='0' cellspacing='0' class='menu'>

<tr>
<td height='17' align='center'> <div align='left'></div></td>
</tr>
</td>

<tr>
<td height='17' class='tr'>
<a href='suporte/suporte.php?regiao=$regiao_usuario' class='link2' target='_blank'>
<img src='imagensmenu2/suporte.gif' border=0 align='absmiddle'>
SUPORTE ON-LINE
</a>
</td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='javascript:window.location.reload()' class='link2' >
<img src='imagensmenu2/atualizar.gif' border=0 align='absmiddle'>
ATUALIZAR
</a></td>
</tr>
<tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('ver.php?id=1&regiao=$regiao_usuario','VisuPro','780','450','yes');\">
<img src='imagensmenu2/projeto.gif' border=0 align='absmiddle'>
Visualizar Projetos
</a>
</td>
</tr>
<tr>

<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('gestaocompras.php?id=1&regiao=$regiao_usuario','Gestaocompras','780','550','yes');\">
<img src='imagensmenu2/compras.gif' border=0 align='absmiddle'>
Gest�o de compras
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('cadastro.php?id=13&regiao=$regiao_usuario','CadUni','750','450','yes');\">
<img src='imagensmenu2/unidades.gif' border=0 align='absmiddle'>
Cadastrar Unidades
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='ver_tudo.php?id=7&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/unidades.gif' border=0 align='absmiddle'>
Visualizar Unidades
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('cadastro.php?id=2','CadRegiao','650','250','yes');\">
<img src='imagensmenu2/regiao.gif' border=0 align='absmiddle'>
Cadastrar Regi�o
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('cadastro.php?id=6&regiao=$regiao_usuario','CadBancos','750','450','yes');\">
<img src='imagensmenu2/bancos.gif' border=0 align='absmiddle'>
Cadastrar Bancos
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='ver_tudo.php?id=4&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/bancos.gif' border=0 align='absmiddle'>
Visualizar Bancos
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('cadastro.php?id=5&regiao=$regiao_usuario','CadApo','750','450','yes');\">
<img src='imagensmenu2/apolice.gif' border=0 align='absmiddle'>
Cadastrar Ap�lice
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='ver_tudo.php?id=5&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/apolice.gif' border=0 align='absmiddle'>
Visualizar Ap�lices
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('cadastro.php?id=7&regiao=$regiao_usuario','CadAtiv','750','450','yes');\">
<img src='imagensmenu2/atividade.gif' border=0 align='absmiddle'>
Cadastrar Atividades
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='ver_tudo.php?id=1&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/atividade.gif' border=0 align='absmiddle'>
Visualizar Atividades
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='ver_tudo.php?id=6&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/ca_user.gif' border=0 align='absmiddle'>
Visualizar Usu�rios
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('tarefas.php?id=1&id_user=$id_user','EnviarTare','750','450','yes');\">
<img src='imagensmenu2/ferramenta.gif' border=0 align='absmiddle'>
Enviar Tarefas
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='http://webmail.sorrindo.org' target='_blank' class='link2'>
<img src='imagensmenu2/email.gif' border=0 align='absmiddle'>
Consultar E-mail
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('bolsista_class.php?id=1&regiao=$regiao_usuario','Desempenho','750','450','yes');\">
<img src='imagensmenu2/avaliacao.gif' border=0 align='absmiddle'>
Av. de Desempenho
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='cadastro.php?id=10&regiao=$regiao_usuario&id_user=$id_user' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/ponto.gif' border=0 align='absmiddle'>
Marcar Ponto
</a></td>
</tr>


<tr>
<td height='17' class='tr'>
<a href='#' onClick=\"javascript:abrir('escala/escala.php?id=1&regiao=$regiao_usuario','EscalaTrab','780','550','yes');\" class='link2'>
<img src='imagensmenu2/escala.gif' border=0 align='absmiddle'>
Escala de Trabalho
</a></td>
</tr>


<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('cadastro.php?id=14&regiao=$regiao_usuario','TipoPG','750','450','yes');\">
<img src='imagensmenu2/dinheiro.gif' border=0 align='absmiddle'>
Tipo de Pagamento
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='ver_tudo.php?id=8&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/dinheiro.gif' border=0 align='absmiddle'>
Ver Tipo de PG
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='fornecedores.php?regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensfinanceiro/cadastrofornecedores.gif' border=0 align='absmiddle'>
Visualizar Fornecedores
</a></tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('cadfornecedores.php?regiao=$regiao_usuario','CadForne','800','500','yes');\">
<img src='imagensfinanceiro/cadastrofornecedores.gif' border=0 align='absmiddle'>
Cadastrar Fornecedor
</a></tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('ctps.php?regiao=$regiao_usuario&id=1','ControleCTPS','790','450','yes');\">
<img src='imagensmenu2/ctps.gif' border=0 align='absmiddle'>
Controle CTPS
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('frota/frota.php?regiao=$regiao_usuario','ControleFrota','790','450','yes');\">
<img src='imagensmenu2/c1.gif' border=0 align='absmiddle'>
Controle de Frota
</a></td>
</tr>


<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('frota/reembolso.php','Reembolso','790','450','yes');\">
<img src='imagensmenu2/reembolso.gif' border=0 align='absmiddle'>
Reembolso
</a></td>
</tr>

<td height='17' class='tr'>
<a href='folhaspg/folha.php?id=9&enc=$linkFolha' class='link2' target='_blank'>
<img src='imagensmenu2/cadastro.gif' border=0 align='absmiddle'>
Folha de Pagamento
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('login_adm.php','Financeiro','790','450','yes');\">
<img src='imagensmenu2/financeiro.gif' border=0 align='absmiddle'>
ADM &amp; Financeiro
</a></td>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('login_rh.php','GestaoRH','790','450','yes');\">
<img src='imagensmenu2/rh.gif' border=0 align='absmiddle'>
Gest�o de RH
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('patrimonio.php?regiao=$regiao_usuario&id=1','Patrimonio','790','450','yes');\">
<img src='imagensmenu2/patrimonio.gif' border=0 align='absmiddle'>
Patrim�nio
</a></td>
</tr>

<tr>
<td height='17' class='tr'>&nbsp;</td>
</tr>

<tr>
<td height='17' align='center' class='tr'> <div align='left'></div></td>
</tr>

</table>";



break;



	case 4:						//USU�RIO PSC�LOGO



$menu = "

<table width='97%' border='0' cellspacing='0' class='menu'>

<tr>

<td height='17' align='center'> <div align='left'></div></td>

</tr>
<tr>
<td height='17' class='tr'>
<a href='suporte/suporte.php?regiao=$regiao_usuario' class='link2' target='_blank'>
<img src='imagensmenu2/suporte.gif' border=0 align='absmiddle'>
SUPORTE ON-LINE
</a>
</td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='javascript:window.location.reload()' class='link2' >
<img src='imagensmenu2/atualizar.gif' border=0 align='absmiddle'>
ATUALIZAR
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('ver.php?id=1&regiao=$regiao_usuario','VisuPro','780','450','yes');\">
<img src='imagensmenu2/projeto.gif' border=0 align='absmiddle'>
Visualizar Projetos
</a>
</td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='ver_tudo.php?id=7&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/unidades.gif' border=0 align='absmiddle'>
Visualizar Unidades
</a></td>
</tr>
<tr>

<td height='17' class='tr'>
<a href='ver_tudo.php?id=4&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/bancos.gif' border=0 align='absmiddle'>
Visualizar Bancos
</a></td>
</tr>

<tr>

<td height='17' class='tr'>
<a href='ver_tudo.php?id=5&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/apolice.gif' border=0 align='absmiddle'>
Visualizar Ap�lices
</a></td>

</tr>

<tr>

<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('ver_tudo.php?id=1&regiao=$regiao_usuario','Vatividade','750','450','yes');\">
<img src='imagensmenu2/atividade.gif' border=0 align='absmiddle'>
Visualizar Atividades
</a></td>

</tr>

<tr>

<td height='17' class='tr'>
<a href='ver_tudo.php?id=1&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/ferramenta.gif' border=0 align='absmiddle'>
Enviar Tarefas
</a></td>
</tr>

<tr>

<td height='17' class='tr'>
<a href='http://webmail.sorrindo.org' target='_blank' class='link2'>
<img src='imagensmenu2/email.gif' border=0 align='absmiddle'>
Consultar E-mail
</a></td>
</tr>
<tr>

<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('bolsista_class.php?id=1&regiao=$regiao_usuario','Desempenho','750','450','yes');\">
<img src='imagensmenu2/avaliacao.gif' border=0 align='absmiddle'>
Av. de Desempenho
</a></td>
</tr>
<tr>

<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('ver_tudo.php?id=15&regiao=$regiao_usuario&id_user=$id_user','Rel','750','450','yes');\">
<img src='imagensmenu2/gestao.gif' border=0 align='absmiddle'>
Relat�rios de Gest�o
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='cadastro.php?id=10&regiao=$regiao_usuario&id_user=$id_user' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/ponto.gif' border=0 align='absmiddle'>
Marcar Ponto
</a></td>
</tr>
<tr>


<tr>
<td height='17' class='tr'>
<a href='#' onClick=\"javascript:abrir('escala/escala.php?id=1&regiao=$regiao_usuario','EscalaTrab','780','550','yes');\" class='link2'>
<img src='imagensmenu2/escala.gif' border=0 align='absmiddle'>
Escala de Trabalho
</a></td>
</tr>


<td height='17' class='tr'>
<a href='fornecedores.php?regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensfinanceiro/cadastrofornecedores.gif' border=0 align='absmiddle'>
Visualizar Fornecedores
</a></tr>

<tr>
<td height='17' class='tr'>&nbsp;</td>
</tr>

<tr>
<td height='17' align='center' class='tr'> <div align='left'></div></td>
</tr>

</table>";


break;


	case 5:						//USU�RIO PESQUISADOR


$menu = "

<table width='97%' border='0' cellspacing='0' class='menu'>
<tr>
<td height='17' align='center'> <div align='left'></div></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='suporte/suporte.php?regiao=$regiao_usuario' class='link2' target='_blank'>
<img src='imagensmenu2/suporte.gif' border=0 align='absmiddle'>
SUPORTE ON-LINE
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='javascript:window.location.reload()' class='link2' >
<img src='imagensmenu2/atualizar.gif' border=0 align='absmiddle'>
ATUALIZAR
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('ver.php?id=1&regiao=$regiao_usuario','VisuPro','780','450','yes');\">
<img src='imagensmenu2/projeto.gif' border=0 align='absmiddle'>
Visualizar Projetos
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='ver_tudo.php?id=7&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/unidades.gif' border=0 align='absmiddle'>
Visualizar Unidades
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='ver_tudo.php?id=1&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/atividade.gif' border=0 align='absmiddle'>
Visualizar Atividades
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('tarefas.php?id=1&id_user=$id_user','EnTar','750','450','yes');\">
<img src='imagensmenu2/ferramenta.gif' border=0 align='absmiddle'>
Enviar Tarefas
</a></td>
</tr>

<tr>
<td height='17' align='center' class='tr'> <div align='left'></div></td>
</tr>
</table>";


break;


	case 6:						//USU�RIO CADASTRADOR


$menu = "
<table width='97%' border='0' cellspacing='0' class='menu'>
<tr>
<td height='17' align='center'> <div align='left'></div></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='suporte/suporte.php?regiao=$regiao_usuario' class='link2' target='_blank'>
<img src='imagensmenu2/suporte.gif' border=0 align='absmiddle'>
SUPORTE ON-LINE
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('cooperativas/cooperativa.php?id=1&regiao=$regiao_usuario','Coopera','780','450','yes');\">
<img src='imagensmenu2/cooperativa.gif' border=0 align='absmiddle'>
Cooperativas / PJ
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='javascript:window.location.reload()' class='link2' >
<img src='imagensmenu2/atualizar.gif' border=0 align='absmiddle'>
ATUALIZAR
</a>
</td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('ver.php?id=1&regiao=$regiao_usuario','VisPro','780','450','yes');\">
<img src='imagensmenu2/projeto.gif' border=0 align='absmiddle'>
Visualizar Projetos
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('cadastro.php?id=7&regiao=$regiao_usuario','CadAtivi','750','450','yes');\">
<img src='imagensmenu2/atividade.gif' border=0 align='absmiddle'>
Cadastrar Atividades
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='ver_tudo.php?id=1&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/atividade.gif' border=0 align='absmiddle'>
Visualizar Atividades
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('tarefas.php?id=1&id_user=$id_user','EnTaref','750','450','yes');\">
<img src='imagensmenu2/ferramenta.gif' border=0 align='absmiddle'>
Enviar Tarefas
</a></td>
</tr>



<tr>
<td height='17' class='tr'>&nbsp;</td>
</tr>

</table>";	
	
break;


if($id_user == "73"){
	
$menu = "
<table width='97%' border='0' cellspacing='0' class='menu'>
<tr>
<td height='17' align='center'> <div align='left'></div></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='suporte/suporte.php?regiao=$regiao_usuario' class='link2' target='_blank'>
<img src='imagensmenu2/suporte.gif' border=0 align='absmiddle'>
SUPORTE ON-LINE
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='javascript:window.location.reload()' class='link2' >
<img src='imagensmenu2/atualizar.gif' border=0 align='absmiddle'>
ATUALIZAR
</a>
</td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('login_adm.php','Financeiro','790','450','yes');\">
<img src='imagensmenu2/financeiro.gif' border=0 align='absmiddle'>
ADM &amp; Financeiro
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('tarefas.php?id=1&id_user=$id_user','EnTaref','750','450','yes');\">
<img src='imagensmenu2/ferramenta.gif' border=0 align='absmiddle'>
Enviar Tarefas
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('login_rh.php','GestaoRH','790','450','yes');\">
<img src='imagensmenu2/rh.gif' border=0 align='absmiddle'>
Gest�o de RH
</a></td>
</tr>

<tr>
<td height='17' class='tr'>&nbsp;</td>
</tr>

</table>";	
	
}
}
?>