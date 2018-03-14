<?php
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

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


$menu_administrativo ='<table width="97%" border="0" cellspacing="0" class="menu">
<tr>
<td height="17" align="center"> <div align="left"></div></td>
</tr>';





//CONSULTA BOTÕES
$qr_botoes = mysql_query("SELECT * FROM botoes WHERE 1");
	while($row_botoes = mysql_fetch_assoc($qr_botoes)):
	
	//configurando links	
	 $onclick = 'javascript:abrir("'.$row_botoes['botoes_link'].'","AVDesempenho","750","450","yes")';	 
	 $pagina = '<a href="'.$row_botoes['botoes_link'].'" target="_blank">';
	
	 
	 
	 
	switch ($row_botoes['botoes_id']){
		
		case 22: $pagina = '<a href="#" target="_blank" onClick="javascript:abrir("'.$row_botoes['botoes_link'].'id=1&regiao='.$regiao_usuario.'","AVDesempenho","750","450","yes")">';
		break;
		
		}
	
	
	
	
	
	$tipo_usuario = explode(',',$row_botoes['tipo_usuario']);
		$grupo = explode(',',$row_botoes['grupo']);
		
		
		///verifica o grupo de usuários
		if(in_array($grupo_usuario,$grupo)) 
			
			
			//VERIFICA 	SESSÃO DO MENU
			switch($row_botoes['botoes_menu']){
				
				case 1:  $menu_administrativo.='<tr>
							<td height="17" class="tr">
							'.$pagina.'
							<img src="'.$row_botoes['botoes_img'].'" border=0 align="absmiddle">
							'.$row_botoes['botoes_nome'].'
							</a>
							</td>
						</tr><tr>
												';
			}
		





endwhile;









/*$menu_rh_financeiro ='<table width="97%" border="0" cellspacing="0" class="menu">
<tr>
<td height="17" align="center"> <div align="left"></div></td>
</tr>';

$menu_cadastros ='<table width="97%" border="0" cellspacing="0" class="menu">
<tr>
<td height="17" align="center"> <div align="left"></div></td>
</tr>';

$menu_edicao_visualizacao ='<table width="97%" border="0" cellspacing="0" class="menu">
<tr>
<td height="17" align="center"> <div align="left"></div></td>
</tr>';
*/


$menu_sistema ='<table width="97%" border="0" cellspacing="0" class="menu">
<tr>
<td height="17" align="center"> <div align="left"></div></td>
</tr>';






/////////////// 	menu administrativo   //////////////////////////

//ADMINISTRAÇÃO
if($perfil_usuario == 0 or $perfil_usuario == 1) {
$menu_administrativo .=	'<tr>
							<td height="17" class="tr">
							<a href="'.$row_botoes['botao_link'].'" target="_blank">
							<img src="imagensmenu2/admgeral.gif" border=0 align="absmiddle">
							'.$row_botoes['botoes_nome'].'
							</a>
							</td>
						</tr>';
}



//RELATÓRIO DE GESTÃO
if($perfil_usuario == 1 or $perfil_usuario == 4) {
	
$menu_administrativo .='<tr>
						<td height="17" class="tr">
						<a href="#" class="link2" onClick=\"javascript:abrir("ver_tudo.php?id=15&id_reg=$regiao_usuario&id_user=$id_user","RelGestao","750","450","yes");\">
						<img src="imagensmenu2/gestao.gif" border=0 align="absmiddle">
						Relatórios de Gestão
						</a>
						</td>
					</tr>';
}


//PATRIMÔNIO
if($perfil_usuario == 0 or $perfil_usuario == 1 or $perfil_usuario == 2) {
	
$menu_administrativo .=	'<tr>
							<td height="17" class="tr">
							<a href="patrimonio.php?regiao=$regiao_usuario&id=1" onclick=\"return hs.htmlExpand(this, { objectType: "iframe" } )\" class="link2">
							<img src="imagensmenu2/patrimonio.gif" border=0 align="absmiddle">
							Patrimônio
							</a>
							</td>
						</tr>';
}

//CONTROLE DE FROTA
if($perfil_usuario == 0 or $perfil_usuario == 1 or $perfil_usuario == 2) {

$menu_administrativo .='<tr>
						<td height="17" class="tr">
						<a href="#" class="link2" onClick=\"javascript:abrir("frota/frota.php?regiao=$regiao_usuario","CFrota","790","450","yes");\">
						<img src="imagensmenu2/c1.gif" border=0 align="absmiddle">
						CONTROLE DE FROTA
						</a>
						</td>
					</tr>';
}

//REEMBOLSO
if($perfil_usuario == 1 or $perfil_usuario == 2) {
$menu_administrativo .='<tr>
		<td height="17" class="tr">
		<a href="#" class="link2" onClick=\"javascript:abrir("frota/reembolso.php","Reembolso","790","450","yes");\">
		<img src="imagensmenu2/reembolso.gif" border=0 align="absmiddle">
		REEMBOLSO
		</a>
		</td>
	</tr>';
}

//AVALIAÇÃO DE DESEMPENHO
if($perfil_usuario == 1 or $perfil_usuario == 2) {
$menu_administrativo .='<tr>
		<td height="17" class="tr">
		<a href="#" class="link2" onClick=\"javascript:abrir("bolsista_class.php?id=1&regiao=$regiao_usuario","AVDesempenho","750","450","yes");\">
		<img src="imagensmenu2/avaliacao.gif" border=0 align="absmiddle">
		Av. de Desempenho
		</a>
		</td>
	</tr>';
}

//MARCAR PONTO
if($perfil_usuario == 0 or $perfil_usuario == 1 or $perfil_usuario == 2 or $perfil_usuario == 4) {
$menu_administrativo .='<tr>
						<td height="17" class="tr">
						<a href="cadastro.php?id=10&id_reg=$regiao_usuario&id_user=$id_user" onclick=\"return hs.htmlExpand(this, { objectType: "iframe" } )\" class="link2">
						<img src="imagensmenu2/ponto.gif" border=0 align="absmiddle">
						Marcar Ponto
						</a></td>
					</tr>';
}


//ESCALA DE TRABALHO
if($perfil_usuario == 2 or $perfil_usuario == 4 or $perfil_usuario == 5) {
$menu_administrativo .='<tr>
							<td height="17" class="tr">
							<a href="#" onClick=\"javascript:abrir("escala/escala.php?id=1&id_reg=$regiao_usuario","EscalaTrab","780","550","yes");\" class="link2">
							<img src="imagensmenu2/escala.gif" border=0 align="absmiddle">
							Escala de Trabalho
							</a>
							</td>
						</tr>';
}


//CONTROLE CTPS
if($perfil_usuario == 0 or $perfil_usuario == 1 or $perfil_usuario == 2) {
$menu_administrativo .='<tr>
							<td height="17" class="tr">
							<a href="#" class="link2" onClick=\"javascript:abrir("ctps.php?regiao=$regiao_usuario&id=1","Controlectps","790","450","yes");\">
							<img src="imagensmenu2/ctps.gif" border=0 align="absmiddle">
							Controle CTPS
							</a></td>
						</tr>';
}



$menu_administrativo .='</table>';
/////// 	FIM  menu administrativo   //////////////////////////


switch ($perfil_usuario) {
	case 0:							//USUÁRIO BÁSICO


$menu = "

<table width='97%' border='0' cellspacing='0' class='menu'>
<tr>
<td height='17' align='center'> <div align='left'></div></td>
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
Visualizar Apólices
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
<a href='#' class='link2' onClick=\"javascript:abrir('tarefas.php?id=1&user=$id_user','EnTaref','750','450','yes');\">
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
Gestão de compras
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='cadastro.php?id=10&id_reg=$regiao_usuario&id_user=$id_user' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/ponto.gif' border=0 align='absmiddle'>
Marcar Ponto
</a></td>
</tr>


<tr>
<td height='17' class='tr'>
<a href='#' onClick=\"javascript:abrir('escala/escala.php?id=1&id_reg=$regiao_usuario','Escala','780','550','yes');\" class='link2'>
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
Patrimônio
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

	case 1:						//USUÁRIO DIRETOR
?>



<?php


	/*$menu_administrativo ='<table width="97%" border="0" cellspacing="0" class="menu">
	
	
	
	
	
	
	
	
	
	
	
	</table>

	';
*/

$menu_rh_financeiro = '<table width="97%" border="0" cellspacing="0" class="menu">
						<tr>
							<td height="17" class="tr">
							<a href="#" onClick=\"javascript:abrir("contabil/login3.php?mt=$row_user[id_master]","Gestão_Contábil","750","450","yes");\" class="link2">
							<img src="imagensmenu2/contabilp.gif" border=0 align="absmiddle">
							GESTÃO CONTÁBIL
							</a>
							</td>
						</tr>
						
						<tr>
							<td height="17" class="tr">
							<a href="#" class="link2" onClick=\"javascript:abrir("gestaocompras.php?id=1&regiao=$regiao_usuario","GCompras","780","550","yes");\">
							<img src="imagensmenu2/compras.gif" border=0 align="absmiddle">
							Gestão de compras
							</a></td>
						</tr>
						<tr>
							<td height="17" class="tr">
							<a href="folhaspg/folha.php?id=9&enc=$linkFolha" class="link2" target="_blank">
							<img src="imagensmenu2/cadastro.gif" border=0 align="absmiddle">
							Folha de Pagamento
							</a></td>
						</tr>
						
						<tr>
							<td height="17" class="tr">
							<a href="#" class="link2" onClick=\"javascript:abrir("login_adm.php","Financeiro","790","450","yes");\">
							<img src="imagensmenu2/financeiro.gif" border=0 align="absmiddle">
							ADM &amp; Financeiro
							</a></td>
						</tr>
						
						
						<tr>
							<td height="17" class="tr">
							<a href="#" class="link2" onClick=\"javascript:abrir("login_rh.php?","GestaoRH","790","450","yes");\">
							<img src="imagensmenu2/rh.gif" border=0 align="absmiddle">
							Gestão de RH
							</a></td>
						</tr>
						
						</table>
';


$menu_cadastros = '<table width="97%" border="0" cellspacing="0" class="menu">
				 <tr>
					<td height="17" class="tr">
					<a href="#" class="link2" onClick=\"javascript:abrir("cadastro.php?id=13&regiao=$regiao_usuario&user=$id_user","CadUnidade","750","450","yes");\">
					<img src="imagensmenu2/unidades.gif" border=0 align="absmiddle">
					Cadastrar Unidades
					</a></td>
				</tr>
				
				<tr>
					<td height="17" class="tr">
					<a href="#" class="link2" onClick=\"javascript:abrir("cadastro.php?id=6&regiao=$regiao_usuario","CadBancos","750","450","yes");\">
					<img src="imagensmenu2/bancos.gif" border=0 align="absmiddle">
					Cadastrar Bancos
					</a></td>
				</tr>
				
				<tr>
					<td height="17" class="tr">
					<a href="#" class="link2" onClick=\"javascript:abrir("cadastro.php?id=5&regiao=$regiao_usuario","CadApolice","750","450","yes");\">
					<img src="imagensmenu2/apolice.gif" border=0 align="absmiddle">
					Cadastrar Apólice
					</a></td>
				</tr>				 
						
				<tr>
					<td height="17" class="tr">
					<a href="#" class="link2" onClick=\"javascript:abrir("cadastro.php?id=7&regiao=$regiao_usuario","CadAtivi","750","450","yes");\">
					<img src="imagensmenu2/atividade.gif" border=0 align="absmiddle">
					Cadastrar Atividades
					</a></td>
				</tr>
					
				<tr>
					<td height="17" class="tr">
					<a href="#" class="link2" onClick=\"javascript:abrir("cadastro.php?id=14&regiao=$regiao_usuario","TipoPG","750","450","yes");\">
					<img src="imagensmenu2/dinheiro.gif" border=0 align="absmiddle">
					Tipo de Pagamento
					</a></td>
				</tr>
				
				
				<tr>
					<td height="17" class="tr">
					<a href="#" class="link2" onClick=\"javascript:abrir("cadfornecedores.php?regiao=$regiao_usuario","CadForne","800","500","yes");\">
					<img src="imagensfinanceiro/cadastrofornecedores.gif" border=0 align="absmiddle">
					Cadastrar Fornecedor
					</a></td>				
				</tr>
				
				<tr>
					<td height="17" class="tr">
					<a href="#" onClick=\"javascript:abrir("documentos_empresa/cadastro.php?mt=$row_user[id_master]","Cadastro_de_documentos","750","450","yes");\" class="link2">
					<img src="imagensmenu2/Folder-close_blue.png" border=0 align="absmiddle">
					DOCUMENTOS
					</a>
					</td>
				</tr>
				
				<tr>
					<td height="17" class="tr">
					<a href="#" class="link2" onClick=\"javascript:abrir("cooperativas/cooperativa.php?id=1&regiao=$regiao_usuario","Coopera","780","450","yes");\">
					<img src="imagensmenu2/cooperativa.gif" border=0 align="absmiddle">
					Cooperativas / PJ
					</a></td>
				</tr>
				
				
				 </table>';
				 
				 
$menu_edicao_visualizacao = '<table width="97%" border="0" cellspacing="0" class="menu">
							<tr>	
								<td height="17" class="tr">
								<a href="#" class="link2" onClick=\"javascript:abrir("ver.php?id=1&regiao=$regiao_usuario","VisuPro","780","450","yes");\">
								<img src="imagensmenu2/projeto.gif" border=0 align="absmiddle">
								Visualizar Projetos
								</a></td>
							</tr>
							
							
							
							<tr>
								<td height="17" class="tr">
								<a href="ver_tudo.php?id=7&regiao=$regiao_usuario" onclick=\"return hs.htmlExpand(this, { objectType: "iframe" } )\" class="link2">
								<img src="imagensmenu2/unidades.gif" border=0 align="absmiddle">
								Visualizar Unidades
								</a></td>
							</tr>
							
							
							<tr>
								<td height="17" class="tr">
								<a href="ver_tudo.php?id=4&regiao=$regiao_usuario" onclick=\"return hs.htmlExpand(this, { objectType: "iframe" } )\" class="link2">
								<img src="imagensmenu2/bancos.gif" border=0 align="absmiddle">
								Visualizar Bancos
								</a></td>
							</tr>
							
							
							
							<tr>
								<td height="17" class="tr">
								<a href="ver_tudo.php?id=5&regiao=$regiao_usuario" onclick=\"return hs.htmlExpand(this, { objectType: "iframe" } )\" class="link2">
								<img src="imagensmenu2/apolice.gif" border=0 align="absmiddle">
								Visualizar Apólices
								</a></td>
							</tr>
							
							
							<tr>
								<td height="17" class="tr">
								<a href="ver_tudo.php?id=1&regiao=$regiao_usuario" onclick=\"return hs.htmlExpand(this, { objectType: "iframe" } )\" class="link2">
								<img src="imagensmenu2/atividade.gif" border=0 align="absmiddle">
								Visualizar Atividades
								</a></td>
							</tr>
							
							<tr>
								<td height="17" class="tr">
								<div>
								<a href="ver_tudo.php?id=8&regiao=$regiao_usuario" onclick=\"return hs.htmlExpand(this, { objectType: "iframe" } )\" class="link2">
								<img src="imagensmenu2/dinheiro.gif" border=0 align="absmiddle">
								Ver Tipo de PG
								</a></div></td>
							</tr>
							
							<tr>
								<td height="17" class="tr">
								<a href="fornecedores.php?regiao=$regiao_usuario" onclick=\"return hs.htmlExpand(this, { objectType: "iframe" } )\" class="link2">
								<img src="imagensfinanceiro/cadastrofornecedores.gif" border=0 align="absmiddle">
								Visualizar Fornecedores
								</a>
								</td>
							</tr>

				
							</table> ';
							
							
$menu_sistema = '<table width="97%" border="0" cellspacing="0" class="menu">

				<tr>
                      <td align="center">
                      <a href="ver_tudo.php?id=19" target="_blank">
                      <img src="imagensmenu2/ver_user_master.gif" width="65" height="59" border="0"></a>
                      &nbsp;&nbsp;&nbsp;&nbsp;
                      <a href="suporte/admsuporte.php" target="_blank">
                      <img src="imagensmenu2/helpdesk.gif" alt="HELP DESK" width="50" height="50" border="0"></a>
                      </td>
                      </tr>

				<tr>
					<td height="17" class="tr">
					<a href="#" class="link2" onClick=\"javascript:abrir("cadastro.php?id=2","CadRegiao","650","250","yes");\">
					<img src="imagensmenu2/regiao.gif" border=0 align="absmiddle">
					Cadastrar Região
					</a></td>
				</tr>
				
				
				<tr>
					<td height="17" class="tr">
					<a href="#" class="link2" onClick=\"javascript:abrir("cadastro.php?id=3&user=$id_user","CadUser","750","450","yes");\">
					<img src="imagensmenu2/ca_user.gif" border=0 align="absmiddle">
					Cadastrar Usuário
					</a></td>
				</tr>
				
				<tr>
					<td height="17" class="tr">
					<a href="ver_tudo.php?id=6&regiao=$regiao_usuario" onclick=\"return hs.htmlExpand(this, { objectType: "iframe" } )\" class="link2">
					<img src="imagensmenu2/ca_user.gif" border=0 align="absmiddle">
					Visualizar Usuários
					</a></td>
				</tr>
				
				
				</table> ';							

break;

	case 2:							//USUÁRIO FINANCEIRO

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
Gestão de compras
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('cadastro.php?id=13&regiao=$regiao_usuario&user=$id_user','CadUni','750','450','yes');\">
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
Cadastrar Região
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
Cadastrar Apólice
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='ver_tudo.php?id=5&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/apolice.gif' border=0 align='absmiddle'>
Visualizar Apólices
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
Visualizar Usuários
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('tarefas.php?id=1&user=$id_user','EnviarTare','750','450','yes');\">
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
<a href='cadastro.php?id=10&id_reg=$regiao_usuario&id_user=$id_user' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/ponto.gif' border=0 align='absmiddle'>
Marcar Ponto
</a></td>
</tr>


<tr>
<td height='17' class='tr'>
<a href='#' onClick=\"javascript:abrir('escala/escala.php?id=1&id_reg=$regiao_usuario','EscalaTrab','780','550','yes');\" class='link2'>
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
<img src='	' border=0 align='absmiddle'>
Gestão de RH
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('patrimonio.php?regiao=$regiao_usuario&id=1','Patrimonio','790','450','yes');\">
<img src='imagensmenu2/patrimonio.gif' border=0 align='absmiddle'>
Patrimônio
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



	case 4:						//USUÁRIO PSCÓLOGO



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
Visualizar Apólices
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
<a href='#' class='link2' onClick=\"javascript:abrir('ver_tudo.php?id=15&id_reg=$regiao_usuario&id_user=$id_user','Rel','750','450','yes');\">
<img src='imagensmenu2/gestao.gif' border=0 align='absmiddle'>
Relatórios de Gestão
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='cadastro.php?id=10&id_reg=$regiao_usuario&id_user=$id_user' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'>
<img src='imagensmenu2/ponto.gif' border=0 align='absmiddle'>
Marcar Ponto
</a></td>
</tr>
<tr>


<tr>
<td height='17' class='tr'>
<a href='#' onClick=\"javascript:abrir('escala/escala.php?id=1&id_reg=$regiao_usuario','EscalaTrab','780','550','yes');\" class='link2'>
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



	case 5:						//USUÁRIO PESQUISADOR



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
<a href='#' class='link2' onClick=\"javascript:abrir('tarefas.php?id=1&user=$id_user','EnTar','750','450','yes');\">
<img src='imagensmenu2/ferramenta.gif' border=0 align='absmiddle'>
Enviar Tarefas
</a></td>
</tr>

<tr>
<td height='17' align='center' class='tr'> <div align='left'></div></td>
</tr>
</table>";

}

// usuário contador

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
<a href='#' class='link2' onClick=\"javascript:abrir('tarefas.php?id=1&user=$id_user','EnTaref','750','450','yes');\">
<img src='imagensmenu2/ferramenta.gif' border=0 align='absmiddle'>
Enviar Tarefas
</a></td>
</tr>

<tr>
<td height='17' class='tr'>
<a href='#' class='link2' onClick=\"javascript:abrir('login_rh.php','GestaoRH','790','450','yes');\">
<img src='imagensmenu2/rh.gif' border=0 align='absmiddle'>
Gestão de RH
</a></td>
</tr>

<tr>
<td height='17' class='tr'>&nbsp;</td>
</tr>

</table>";	
	

}
?>