<?php
if(isset($_REQUEST['m'])){
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
        header('Location: m/');
    }
}

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = 'login.php?entre=true';</script>";
}


include('conn.php');
include "funcoes.php";
include('classes_permissoes/regioes.class.php');
include('classes_permissoes/master.class.php');
include "wfunction.php";

$id_user = $_COOKIE['logado'];
$sql = "SELECT * FROM funcionario where id_funcionario = '$id_user'";
$result_user = mysql_query($sql, $conn);

$row_user = mysql_fetch_array($result_user);

$result_regi = mysql_query("SELECT * FROM regioes where id_regiao = '$row_user[id_regiao]'", $conn);
$row_regi = mysql_fetch_array($result_regi);
$regiao = $row_regi['id_regiao'];

$grupo_usuario = $row_user['grupo_usuario'];
$regiao_usuario = $row_user['id_regiao'];
$apelido_usuario = $row_user['nome1'];
$tipo_user = $row_user['tipo_usuario'];
$id_master = $row_user['tipo_usuario'];

//CODIGO ESPECIFICO PARA O E-MAIL IFRAME
$intraUsu = carregaUsuario();
$qrEmail = "SELECT A.email,A.senha,B.email_servidor,B.dominio_email,B.ip_servidor FROM funcionario_email_assoc AS A
            LEFT JOIN master AS B ON (A.id_master=B.id_master)
            WHERE A.id_funcionario = '{$intraUsu['id_funcionario']}' AND A.id_master = {$intraUsu['id_master']}";
$rsemail = mysql_query($qrEmail);
$rowEmail = mysql_fetch_array($rsemail);
$numEmail = mysql_num_rows($rsemail);


if ($id_user == '5' or $id_user == '32') {
//-------------VERIVICANDO AS CONTAS PARA HOJE------------------
    $result_jr = mysql_query("SELECT * FROM saida where id_regiao = '$regiao_usuario' and status = '1'
and data_vencimento = '$ano-$mes_h-$dia_h' ORDER BY data_vencimento");
    $result_banco_jr = mysql_query("SELECT * FROM bancos where id_regiao='$regiao_usuario' and saldo LIKE '-%'");
    $linha_jr = mysql_num_rows($result_jr);
    $linha_banco_jr = mysql_num_rows($result_banco_jr);
    if ($linha_jr > "0") {
        print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\nVOCÊ POSSUI $linha_jr CONTA(S) A PAGAR HOJE');</script>";
    } else {

    }
    if ($linha_banco_jr > "0") {
        print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\nVOCÊ POSSUI $linha_banco_jr SALDO(S) NEGATIVO(S)');</script>";
    }
}
if ($id_user == '3' or $id_user == '27') {
//-------------VERIVICANDO SE EXISTEM PEDIDOS DE COMPRAS------------------
    $result_jr2 = mysql_query("SELECT * FROM compra where acompanhamento = '1' and status_reg = '1'");
    $linha_jr2 = mysql_num_rows($result_jr2);
    if ($linha_jr2 > "0") {
        print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\nVOCÊ POSSUI $linha_jr2 SOLICITAÇÕES DE COMPRA');</script>";
    }
}
//-----------VERIFICANDO SE EXISTE ALGUM CHAMADO RESPONDIDO OU COMBUSTIVEL ACEITO-------------------
$result_chamado = mysql_query("SELECT id_suporte FROM suporte where user_cad = '$id_user' and status = '2'");
$cont_chamado = mysql_num_rows($result_chamado);
if ($cont_chamado > "0") {
    print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\nVOCÊ POSSUI $cont_chamado CHAMADOS RESPONDIDOS NO SUPORTE ON-LINE');</script>";
}
/*
  $RECom1 = mysql_query("SELECT id_combustivel FROM fr_combustivel WHERE status_reg = '2' and id_user = '$id_user'");
  $ContCom1 = mysql_num_rows($RECom1);
  if($ContCom1 > "0"){
  print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\ $ContCom1 PEDIDOS DE COMBUSTIVEL LIBERADOS');</script>";
  }
 */
if ($id_user == '9' or $id_user == '1') {
//-----------VERIFICANDO SE EXISTE ALGUM CHAMADO RESPONDIDO-------------------
    $result_chamado = mysql_query("SELECT id_suporte FROM suporte where status = '1' or status = '3'");
    $cont_chamado = mysql_num_rows($result_chamado);
    if ($cont_chamado > "0") {
        print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\n$cont_chamado CHAMADOS ABERTOS NO SUPORTE ON-LINE');</script>";
    }
}
//-----------VERIFICANDO SE EXISTE ALGUM PEDIDO DE REEMBOLSO-------------------
if ($id_user == '32' or $id_user == '27') {
    $REReem = mysql_query("SELECT id_reembolso FROM fr_reembolso WHERE status = '1'");
    $ContReem = mysql_num_rows($REReem);
    if ($ContReem > "0") {
        print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\ $ContReem PEDIDOS DE REEMBOLSO EM ABERTO');</script>";
    }
}
//-----------VERIFICANDO SE EXISTE ALGUM PEDIDO DE COMBUSTIVEL-------------------
if ($id_user == '32' or $id_user == '27') {
    $RECom = mysql_query("SELECT id_combustivel FROM fr_combustivel WHERE status_reg = '1'");
    $ContCom = mysql_num_rows($RECom);
    if ($ContCom > "0") {
        print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\ $ContCom PEDIDOS DE COMBUSTIVEL EM ABERTO');</script>";
    }
}


$data = date('d/m/Y');
$mes = date('m');

$q_master = mysql_query("SELECT * FROM master where id_master = '$row_regi[id_master]'", $conn);

$row_master_1 = mysql_fetch_array($q_master);

$cont_result = mysql_query("SELECT COUNT(*) FROM tarefa where usuario = '$apelido_usuario' and id_regiao = '$regiao_usuario' and status_tarefa = '1'  and status_reg = '1'", $conn);
$row_cont = mysql_fetch_array($cont_result);


//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkFolha = encrypt("$regiao");
$linkFolha = str_replace("+", "--", $linkFolha);
// -----------------------------

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: Intranet ::</title>
        <link rel="shortcut icon" href="favicon.ico" />
        <link href="css_principal.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="js/lightbox.css" type="text/css" media="screen"/>
        <link rel="stylesheet" type="text/css" href="js/highslide.css" />

        <script type="text/javascript" src="js/highslide-with-html.js"></script>
        <script type="text/javascript" src="js/ramon.js"></script>
        <script src="jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
        <script src="jquery/jquery.tools.min.js" type="text/javascript"></script>

        <script language="JavaScript" type="text/JavaScript">
            ///HIGHSLIDE
            hs.graphicsDir = 'images-box/graphics/';
            hs.outlineType = 'rounded-white';
            ////

            function MM_jumpMenu(targ,selObj,restore){ //v3.0
                eval(targ+".location=	'"+selObj.options[selObj.selectedIndex].value+"'");
                if (restore) selObj.selectedIndex=0;
            }

            function popupfinanceiro(caminho,nome,largura,altura,rolagem) {
                var esquerda = (screen.width - largura) / 2;
                var cima = (screen.height - altura) / 2 -50;
                window.open(caminho,nome,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=' + rolagem + ',resizable=yes,copyhistory=no,top=' + cima + ',left=' + esquerda + ',width=' + largura + ',height=' + altura);
            }

            //////////////////  JQUERY
            $(function(){
                $.ajax({
                    url: 'roundcube/index.php',
                    successs: function(resposta){
                        $('#email').html(resposta);

                    }
                });

                $('tr.normal')
                .mouseover(function(){
                    $(this).addClass('over');
                })
                .mouseout(function(){
                    $(this).removeClass('over');
                });

                // setup ul.tabs to work as tabs for each div directly under div.panes
                $("ul.tabs").tabs("div.panes > div");

                $('#escolha_regiao').change(function(){
                    var regiao = $(this).val();
                    var regiao_de = $("#regiao_de").val();
                    var user = $("#user").val();
                    $.ajax({
                        url: 'cadastro2.php?regiao='+regiao+'&regiao_de='+regiao_de+'&user='+user+'&id_cadastro=13',
                        success: function(){
                            location.href = 'index.php';
                        }
                    });                    
                    $("#regiao_selecionada").val(regiao);
                });

                $('#escolha_master').change(function(){
                    var master = $(this).val();
                    var master_de = $("#master_de").val();
                    var user = $("#user").val();
                    $.ajax({
                        url: 'cadastro2.php?master='+master+'&master_de='+master_de+'&user='+user+'&id_cadastro=26',
                        success: function(){
                            location.href = 'index.php';
                        }
                    });
                });                                
            });
        </script>
        <style type="text/css">
            div#webmail_cadastro * {
                font-family: Tahoma, Geneva, sans-serif;
                margin: 10px;
            }

            div#webmail_cadastro input {
                border: 1px solid #808080;
            }
        </style>
    </head>
    <body>
        <div id="corpo">
            <input type="hidden" name="regiao_selecionada" id="regiao_selecionada" value="" />
            <div id="conteudo">
                <div id="topo">
                    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" valign="top">
                                <img src="imagens/logomaster<?= $row_user['id_master'] ?>.gif" width="110" height="79">
                            </td>

                            <td  valign="top">
                                <div style="color:#333;font-size:12px;">
                                    <br />
                                    <br />
                                    Ol&aacute;,
                                    <?php
                                    print "<br><span class='red'><b>$row_user[nome]</b></span>  <br>Data: <b>$data</b> <br>";
                                    if ($tipo_user == "1" or $tipo_user == "4") {
                                        print "você está visualizando a Região: <b>$row_regi[regiao]</b>";
                                    }
                                    ?>
                                    </b><br>
                                </div>
                            </td>
                            <td width="132" align="center" valign="middle" style="display:none" >

                                <span class="quadro_tarefas">
                                    <?php echo $row_cont['0']; ?>
                                </span>
                            </td>
                        </tr>
                        <tr class="barra">
                            <td align="center">


                            <?php // Visualizando Regiões NOVO    ?>
                                <span style="font-size:10px"> <strong>REGIÕES:</strong> </span>
                                <select name="regiao" class="campotexto" id="escolha_regiao" >
                                    <?php
                                    $a = new Regioes();
                                    $a->Preenhe_select_por_master($row_user['id_master'], $regiao_usuario);
                                    ?>
                                </select>  <br />
                                <input type="hidden" name="regiao_de" id="regiao_de" value="<?= $regiao_usuario ?>"/>
                                <input type="hidden" name="user"  id="user"  value="<?= $id_user ?>"/>
                                <input type="hidden" name="id_cadastro" value="13"/>

                            </td>

                            <td>

                            <?php // TROCA MASTER NOVO  ?>
                                <span style="font-size:10px"><strong> EMPRESAS:</strong> </span>
                                <select name="master" class="campotexto" id="escolha_master" >
                                    <?php
                                    $obj_master = new Master();
                                    $obj_master->Preenhe_master($row_user['id_master']);
                                    ?>
                                </select>
                                <input type="hidden" name="master_de" id="master_de" value="<?= $row_user['id_master'] ?>"/>
                                <input type="hidden" name="id_cadastro" value="26"/>

                            </td>
                            <td align="center"> <span class="sair"> <a href="logof.php" target="_parent" >SAIR  </a> </span></td>
                        </tr>
                        <tr><td colspan="3">&nbsp;</td></tr>
                    </table>


                    <?php
                    /* Liberando o resultado */
                    mysql_free_result($result_user);
                    mysql_free_result($result_regi);
                    mysql_free_result($cont_result);
                    ?>

                    <!-- ### FIM TOPO ### -->
                </div>
                <div id="menu_principal">
                    <!-- ### MENU PRINCIPAL (ABAS) ### -->
                    <ul class="tabs">

                        <?php
                        $qr_botoes_pg = mysql_query("SELECT * FROM botoes_pagina WHERE botoes_pg_id = 1 ");
                        while ($row_pagina = mysql_fetch_assoc($qr_botoes_pg)):

                            $qr_botoes_menu = mysql_query("SELECT * FROM botoes_menu WHERE botoes_pagina ='$row_pagina[botoes_pg_id]' ORDER BY botoes_menu_id ");
                            while ($row_btn_menu = mysql_fetch_assoc($qr_botoes_menu)):

                                $qr_botoes = mysql_query("SELECT * FROM botoes

                                                        INNER JOIN botoes_assoc
                                                        ON botoes.botoes_id = botoes_assoc.botoes_id
                                                        WHERE botoes.botoes_menu = '$row_btn_menu[botoes_menu_id]'  AND botoes_assoc.id_funcionario = '$_COOKIE[logado]'  ORDER BY botoes.botoes_menu ASC");
                                ?>

                                <li <?php if (mysql_num_rows($qr_botoes) == 0) echo 'style="display:none;"'; ?>>
                                    <a href="#">
                                        <div class="sombra1"> <?php echo $row_btn_menu['botoes_menu_nome']; ?>   <div class="texto">  <?php echo $row_btn_menu['botoes_menu_nome']; ?> </div>      </div>
                                    </a>
                                </li>
                                <?php
                            endwhile;

                        endwhile;
                        ?>
                    </ul>

                <!-- ### FIM  MENU_ PRINCIPAL ### -->
                </div>

                <div id="submenu" class="panes" >
                <!-- ### SUBMENU (CONTEUDO DAS ABAS) ### -->

                    <div class="conteudo_aba" style="display:none;">
                        <?php include 'index_parte_todos.php'; ?>
                    </div>
                        <?php
                        $qr_botoes_pg = mysql_query("SELECT * FROM botoes_pagina WHERE botoes_pg_id = 1 ");
                        while ($row_pagina = mysql_fetch_assoc($qr_botoes_pg)):

                            $qr_botoes_menu = mysql_query("SELECT * FROM botoes_menu WHERE botoes_pagina ='$row_pagina[botoes_pg_id]'   AND botoes_menu_id != 1 ORDER BY botoes_menu_id ");
                            while ($row_btn_menu = mysql_fetch_assoc($qr_botoes_menu)):
                                ?>
                            <div class="conteudo_aba" style="display:none;">
                                <table width="100%" >
                                    <tr>
                                        <td class="titulo_tabela">
                                            <div class="sombra1"> <?php echo $row_btn_menu['botoes_menu_nome']; ?>
                                                <div class="texto"> <?php echo $row_btn_menu['botoes_menu_nome']; ?></div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <ul>

                                <?php
                                $qr_botoes = mysql_query("SELECT * FROM botoes
                                                            INNER JOIN botoes_assoc
                                                            ON botoes.botoes_id = botoes_assoc.botoes_id
                                                            WHERE botoes.botoes_menu = '$row_btn_menu[botoes_menu_id]'
                                                            AND botoes_assoc.id_funcionario = '$_COOKIE[logado]'
                                                            ORDER BY botoes.botoes_menu ASC");


                                while ($row_botoes = mysql_fetch_assoc($qr_botoes)):
                                    if ($row_botoes['botoes_id'] == 33) {  //BOTÃO FOLHA DE PAGAMENTO COM A REGIAO CRIPTOGRAFADA
                                ?>
                                    <li>
                                        <a href="#"  onClick="window.open('<?= $row_botoes['botoes_link'] . '&enc=' . $linkFolha ?>','<?= $row_botoes['botoes_nome']; ?>','width=800,height=600,scrollbars=yes,resizable=yes')"  class="link"  title="<?php $row_botoes['botoes_descricao'] ?>">
                                            <img src="<?= $row_botoes['botoes_img'] ?>" border="0" align="absmiddle"><br />
                                            <?= $row_botoes['botoes_nome'] ?>
                                        </a>
                                    </li>
                                <?php } elseif($row_botoes['botoes_id'] == 17) {  //BOTÃO GESTAO DE UNIDADES
                                ?>
                                
                                <li>                                       
                                    <a href="<?=$row_botoes['botoes_link'];?>" class="link" target="_blank" title="<?=$row_botoes['botoes_descricao'];?>">
                                    <img src="<?=$row_botoes['botoes_img'];?>" border="0" align="absmiddle"><br />
                                       <?=$row_botoes['botoes_nome'];?>
                                    </a>
                                </li>							
										
                                <?php	}elseif ($row_botoes['botoes_onclick'] == 2) { ?>
                                    <li>
                                        <a href="<?= $row_botoes['botoes_link'] . $regiao_usuario; ?>"  onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )"   class="link"  title="<?php echo $row_botoes['botoes_descricao'] ?>">
                                            <img src="<?= $row_botoes['botoes_img'] ?>" border="0"	><br />
                                    <?= $row_botoes['botoes_nome'] ?>
                                        </a>
                                    </li>
                                <?php } else { ?>
                                    <li>
                                        <a href="#" onClick="window.open('<?= $row_botoes['botoes_link'] . $regiao_usuario; ?>&id_user=<?= $_COOKIE['logado'] ?>','<?= $palavra ?>','width=800,height=600,scrollbars=yes,resizable=yes')" class="link" title="<?php echo $row_botoes['botoes_descricao'] ?>" >
                                            <img src="<?= $row_botoes['botoes_img'] ?>" border="0" align="absmiddle"><br />
                                            <?= $row_botoes['botoes_nome'] ?>
                                        </a>
                                    </li>
                                    <?php
                                    }
                                endwhile;
                                ?>
                                </ul>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <?php endwhile; ?>
                <!-- ### FIM SUBMENU ### -->
                <div style="clear:left;"></div>

                <?php                                            print_r('Chegou aqui');
                                            exit;

				// pega o master
				$master_id = mysql_fetch_array(mysql_query("SELECT id_master FROM funcionario WHERE id_funcionario = {$id_user}"));
				$master_id = $master_id['id_master'];

				$email_user = mysql_fetch_array(mysql_query("SELECT email, senha, webmail_new_version FROM funcionario_email_assoc WHERE id_funcionario = {$id_user} AND id_master = {$master_id}"));

				//Pega o host do servidor de email dependendo do usuário que estiver acessando
				//Exemplo: suporte@institutolagosrio.com.br = mail.institutolagosrio.com.br etc.. etc..
				$master_data = mysql_fetch_array(mysql_query("SELECT * FROM master WHERE id_master = {$master_id}"));

				if ($master_data)  $_SESSION['webmail_host'] = $master_data['email_servidor'];

				////
				if ( ( $_COOKIE['logado'] == 5 && $master_id == 8)
                       || ($_COOKIE['logado'] == 88)
                       || ($_COOKIE['logado'] == 133 && $master_id == 8)
                       || ($_COOKIE['logado'] == 158 && $master_id == 6) ) {

                    /////
					if (!isset($_GET['webmail']) || $_GET['webmail'] != 'create') {
						$id_user    = $_COOKIE['logado'];

                        /////
						if ($email_user && $email_user['webmail_new_version']) { //webmail_new_version = 1 logo 1 = true
							$_SESSION['email']    = $email_user['email'];
							$_SESSION['password'] = $email_user['senha'];

							?>
							<div>
                                <iframe id="iframemail" src="webmail/index.php?box=Inbox&boxfull=INBOX" width="100%" height="700" frameborder="0"></iframe>
							</div>
							<?php
						} else if ($email_user && !$email_user['webmail_new_version']) { //webmail_new_version = 0 logo !0 = true obs: 0 é o padrão
							$_SESSION['email']    = $email_user['email'];
							$_SESSION['password'] = $email_user['senha'];
							?>
								<div>
								    <iframe id="iframemail" src="webmailt/cobaia.php"width="100%" height="700" frameborder="0"></iframe>
								</div>
							<?php
						} else {
							?>
							<center>
								<div id="webmail_cadastro">
									<h3>É preciso cadastrar seu email para ter acesso ao novo webmail</h3>
									<form action="?webmail=create" method="post">
										<table>
											<tr>
												<td>Email </td>
												<td><input type="text" name="webmail_email" size="31" /></td>
											</tr>
											<tr>
												<td>Senha </td>
												<td><input type="password" name="webmail_password" size="31" /></td>
											</tr>
											<tr>
												<td></td>
												<td><input type="submit" value="Cadastrar" style="width: 75px; height: 22px;" /></td>
											</tr>
										</table>
									</form>
								</div>
							</center>
							<?php
						}
                        /////

					} else {
                                            
						// gravando email cadastrado
						if ($_GET['webmail'] == 'create') {
							$email   = $_POST['webmail_email'];
							$senha   = $_POST['webmail_password'];
							$id_user = $_COOKIE['logado'];


                            //////
							if (filter_var($email, FILTER_VALIDATE_EMAIL) && $senha) {
								// pega o master
								$master_id = mysql_fetch_array(mysql_query("SELECT id_master FROM funcionario WHERE id_funcionario = {$id_user}"));
								$master_id = $master_id['id_master'];

								// pega dados de conexão do master
								$master_data = mysql_fetch_array(mysql_query("SELECT * FROM master WHERE id = {$master_id}"));

								//$register = mysql_query("INSERT INTO funcionario_email_assoc(id_funcionario, email, senha) VALUES({$id_user}, '{$email}', '{$senha}')");

								if ($register && $master_data['email_servidor']) {
									$_SESSION['webmail_host'] = $master_data['email_servidor'];

									header('Location: index.php');
								}
							}else {
								?>
								<style type="text/css">
									div#webmail_cadastro * {
										font-family: Tahoma, Geneva, sans-serif;
										margin: 10px;
									}

									div#webmail_cadastro input {
										border: 1px solid #808080;
									}
								</style>
								<center>
									<div id="webmail_cadastro">
										<h3>É preciso cadastrar seu email para ter acesso ao novo webmail</h3>
										<h4 style="color: #ff0000;">Email ou senha inválidos</h4>
										<form action="?webmail=create" method="post">
											<table>
												<tr>
													<td>Email </td>
													<td><input type="text" name="webmail_email" size="31" value="<?php echo $email; ?>" /></td>
												</tr>
												<tr>
													<td>Senha </td>
													<td><input type="password" name="webmail_password" size="31" value="<?php echo $senha; ?>" /></td>
												</tr>
												<tr>
													<td></td>
													<td><input type="submit" value="Cadastrar" style="width: 75px; height: 22px;" /></td>
												</tr>
											</table>
										</form>
									</div>
								</center>
								<?php
							}
                            ///

						}
					}
                    ////

				} else {

					if ($email_user && $email_user['webmail_new_version']) { //webmail_new_version = 1 logo 1 = true
                            $_SESSION['email']    = $email_user['email'];
                            $_SESSION['password'] = $email_user['senha'];

                            ?>
                            <div>
                                <iframe id="iframemail" src="webmail/index.php?box=Inbox&boxfull=INBOX" width="100%" height="700" frameborder="0"></iframe>
                            </div>
                            <?php
                        } else if ($email_user && !$email_user['webmail_new_version']) { //webmail_new_version = 0 logo !0 = true obs: 0 é o padrão
                            $_SESSION['email']    = $email_user['email'];
                            $_SESSION['password'] = $email_user['senha'];
                            ?>
                                <div>
                                    <iframe id="iframemail" src="webmailt/cobaia.php" width="100%" height="700" frameborder="0"></iframe>
                                </div>
                            <?php
                        } else {
                            ?>
                            <center>
                                <div id="webmail_cadastro">
                                    <h3>É preciso cadastrar seu email para ter acesso ao novo webmail</h3>
                                    <form action="?webmail=create" method="post">
                                        <table>
                                            <tr>
                                                <td>Email </td>
                                                <td><input type="text" name="webmail_email" size="31" /></td>
                                            </tr>
                                            <tr>
                                                <td>Senha </td>
                                                <td><input type="password" name="webmail_password" size="31" /></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td><input type="submit" value="Cadastrar" style="width: 75px; height: 22px;" /></td>
                                            </tr>
                                        </table>
                                    </form>
                                </div>
                            </center>
                            <?php
                        }
				}
                ///
                ?>
                <span class="rodape2"><?= $row_master_1['razao'] ?> - Acesso Restrito a Funcion&aacute;rios</span>
            </div>
        </div>
    </body>
</html>
