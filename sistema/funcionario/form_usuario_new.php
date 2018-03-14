<?php
/*
 * SEM TENTAR ENTENDER, COU COLOCAR BOOTSTRAP
 * CORRENDO POR CAUSA DA IMPLANTAÇÃO DO NOVO CLIENTE
 * ADD OS CAMPOS DE HORA DE ACESSO E DIAS DE ACESSO
 * RAMON 22.03.2016
 */


if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../../login.php'>Logar</a> ";
    exit;
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/global.php");
include("../../classes/FuncionarioClass.php");
require_once('../../classes/CltClass.php');

$CltClass = new CltClass();
$global = new GlobalClass();
$usuario = carregaUsuario();
$clts = $CltClass->carregaClts();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "6", "area" => "Sistema", "id_form" => "form1", "ativo" => "Formulário de Funcionário");
$breadcrumb_pages = array("Gestão de Funcionários" => "index.php");

$ObjFunc = new FuncionarioClass();
$funcionarios = $ObjFunc->listFuncionariosAtivos();
if(isset($_POST['id_funcionario_busca']))
{
    $id_user = $_POST['id_funcionario_busca'];
    
}
else
{
    $id_user = $_COOKIE['logado'];
    $result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
    $row_user = mysql_fetch_array($result_user);

    // SELECIONANDO AS REGIÕES CADASTRADAS NO BANCO
    $sql = "SELECT * from regioes where id_master = '$row_user[id_master]'";
    $result = mysql_query($sql, $conn);

    //PEGANDO O ID DO CADASTRO
    $id = $_REQUEST['id'];


    $id_user = $_REQUEST['user'];
    $pag = $_REQUEST['pag'];
}
function acoes_checked($acoes_id, $id_funcionario) {

    $qr_acoes_assoc = mysql_query("SELECT * FROM funcionario_acoes_assoc  WHERE id_funcionario = '$id_funcionario'  AND acoes_id = '$acoes_id' ");
    if (mysql_num_rows($qr_acoes_assoc) != 0)
        return 'checked="checked"';
}

if (isset($_POST['ajax'])) {

    $id_master = $_POST['id_master'];

    $qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_master = '$id_master'");
    while ($row_regiao = mysql_fetch_assoc($qr_regiao)) {


        echo '<option value="' . $row_regiao['id_regiao'] . '">' . htmlentities(utf8_encode($row_regiao['regiao'])) . '</option>';
    }
    exit;
}
?>
<!--html>
    <head><title>:: Intranet ::</title>
        <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
        <script language="javascript" src="jquery-1.3.2.js"></script>
        <script src='../../ajax.js' type='text/javascript'></script>
        <script language="javascript" src='../../js/ramon.js' type='text/javascript'></script>
        <link href='../../autocomp/css.css' type='text/css' rel='stylesheet'>
        <link href="../../net1.css" rel="stylesheet" type="text/css">

        <script src="../../jquery/jquery-1.4.2.min.js"></script>
        <script src="../../jquery/base64.js"></script-->
<!DOCTYPE html>

<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Formulário de Usuários</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <!--<link rel="stylesheet" href="../../css/ui.css">-->
        <style>
            #ui-id-1{
                z-index: 1051;
                
            }
            .ui-menu-item :hover{
                background: #1d89cf;
                
            }
            .ui-menu-item-wrapper{
                padding:5px;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); 

        

        $result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'", $conn);
        
        $row_user = mysql_fetch_array($result_user);

        if (empty($_REQUEST['master'])) {
            $mostrar = "style='display:none'";
        } else {
            $mostrar = "";
        }

        $link_foto = $row_user['id_regiao'] . "funcionario" . $row_user['0'] . $row_user['foto'];

        if ($row_user['foto'] != "0") {
            $link = "<img src='fotos/$link_foto' border=1 width='100' height='130'>";
            $foto = "Deseja remover a foto? <label><input name='foto' type='checkbox' id='foto' value='3'/> Sim</label>";
        } else {
            $link = "<img src='fotos/semimagem.gif' border=1 width='100' height='130'>";
            $foto = "Foto: <input name='foto' type='checkbox' id='foto' value='1' onClick=\"document.all.tablearquivo.style.display = (document.all.tablearquivo.style.display == 'none') ? '' : 'none' ;\">";
        }
        ?>
        <div class="container">
            <div class="page-header box-sistema-header"><h2><span class="glyphicon glyphicon-phone"></span> - Sistema<small> - Gestão de Usuários</small></h2></div>

            <form action='../../cadastro2.php' method="post" id="form1" class="form-horizontal" enctype='multipart/form-data'>

                <div class="panel panel-default">
                    <div class="panel-heading text-bold">Dados do Usuário</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-sm-12 text-right">
                                <button id="copiar" name="copiar" type="button" class="btn btn-success">Copiar Perfil</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="master" class="col-sm-2 control-label">Master</label>
                            <div class="col-sm-4">
                                <?php
                                include "../../classes/regiao.php";

                                $SelMas = new regiao();
                                $SelMas->SelectMaster('master', 'class=\'form-control\'', $id_user);
                                ?>
                            </div>
                            <label for="regiao" class="col-sm-2 control-label">Regiões</label>
                            <div class="col-sm-4">
                                <select name='id_regiao' class='form-control' id='regiao'>

                                    <?php
                                    $REReg = mysql_query("SELECT * FROM regioes");
                                    while ($row = mysql_fetch_array($REReg)) {

                                        $regiao_atual = $row_user['id_regiao'];
                                        $regiao_atual2 = $row['id_regiao'];

                                        if ($regiao_atual == $regiao_atual2) {
                                            print "<option value='$row[id_regiao]' selected>$row[regiao] - $row[sigla]</option>";
                                        } else {
                                            print "<option value='$row[id_regiao]'>$row[regiao] - $row[sigla]</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="funcao" class="col-sm-2 control-label">Função</label>
                            <div class="col-sm-4"><input name='funcao' type='text' class='form-control' id='funcao' value=''></div>

                            <label for="locacao" class="col-sm-2 control-label">Lotação</label>
                            <div class="col-sm-4"><input name='locacao' type='text' class='form-control' id='locacao' value=''></div>
                        </div>

                        <div class="form-group">
                            <label for="nome" class="col-sm-2 control-label">Nome Completo</label>
                            <div class="col-sm-4"><input name='nome' type='text' class='form-control' id='nome' size='35' value=''></div>

                            <label for="nome1" class="col-sm-2 control-label">Nome Exibição</label>
                            <div class="col-sm-4"><input name='nome1' type='text' class='form-control' id='nome1' size='15' value=''></div>
                        </div>

                        <div class="form-group">
                            <label for="nasc_dia" class="col-sm-2 control-label">Data Nascimento</label>
                            <div class="col-sm-4"><input name='nasc_dia' type='text' class='form-control data' id="nasc_dia" size='10' maxlength=10 value=''></div>
                                                        
                            <label for="horario_inicio" class="col-sm-2 control-label">Horário de acesso</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <input name='horario_inicio' type='text' class='form-control' id="horario_inicio" value='<?= $row_user['horario_inicio'] ?>' />    
                                    <span class="input-group-addon">até</span>
                                    <input name='horario_fim' type='text' class='form-control' id="horario_fim" value='<?= $row_user['horario_fim'] ?>' />
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email2" class="col-sm-2 control-label">E-mail</label>
                            <div class="col-sm-4"><input name='email2' type='email' class='form-control' id='email2' value=''></div>

                            <label for="acesso_dias" class="col-sm-2 control-label">Dias de Acesso</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect(array("5" => "Dias de Semana","7" => "Todos os Dias"),$row_user['acesso_dias'], "id='acesso_dias' name='acesso_dias' class='form-control'");?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cltAssoc" class="col-sm-2 control-label">Clt Associado</label>
                            <div class="col-sm-4">
                                <?php 
                                
                                $disabled = ($usuario['oculto'] == 0 && $row_user['id_clt'] != 0) ? 'disabled' : '';
                                echo montaSelect($clts, $row_user['id_clt'], "id='cltAssoc', name='cltAssoc' class='form-control' $disabled"); 
                                
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="login" class="col-sm-2 control-label">Login</label>
                            <div class="col-sm-4"><input name="login"  type="text" class="form-control" id="login" value="" ></div>

                            <label for="s" class="col-sm-2 control-label">Senha inicial</label>
                            <div class="col-sm-4"><input name="s" id="s" type="text" class="form-control" value="123456" readonly ></div>
                        </div>
                        

                        <!-- AKI -->
                        
                        <table class='table text-sm valign-middle'>

                            <tr style="border: 1px solid #ddd;">
                                <td class="info text-center text-bold" colspan="9">  
                                    <label class="control-label">E-mail</label>
                                </td>
                            </tr>

                            <tr style="border: 1px solid #ddd;">
                                <td>MASTER:</td>
                                <td colspan="7" style="border-bottom:1px solid #ddd; border-right:1px solid #ddd"> 

                                    <?php
                                    $qr_master = mysql_query("SELECT * FROM master WHERE status = 1 AND email_servidor != ''");
                                    while ($row_master = mysql_fetch_assoc($qr_master)):

                                        $verifica_master_email = mysql_num_rows(mysql_query("SELECT * FROM funcionario_email_assoc WHERE id_master = '$row_master[id_master]' AND id_funcionario = '$id_user'"));
                                        $checked = ($verifica_master_email != 0 ) ? 'checked="checked"' : '';


                                        echo '<input type="checkbox" name="master_email[]" value="' . $row_master['id_master'] . '" class="master_email" ' . $checked . '/> ' . $row_master['nome'] . ' &nbsp;';

                                    endwhile;
                                    ?>
                                </td>
                                
                                
                            </tr>
                        
                            <tr>
                                <td colspan="9">
                                    <table  class='table text-sm valign-middle'>
                                        <?php
                                        $qr_master = mysql_query("SELECT * FROM master WHERE status = 1 AND email_servidor != ''");
                                        while ($row_master = mysql_fetch_assoc($qr_master)):

                                            $qr_email = mysql_query("SELECT * FROM funcionario_email_assoc  WHERE id_funcionario = '$id_user' AND id_master = '$row_master[id_master]'");
                                            $row_email = mysql_fetch_assoc($qr_email);

                                            $display = (mysql_num_rows($qr_email) == 0) ? 'display:none;' : 'display:block;';
                                            ?>	

                                            <tr class="master_<?php echo $row_master['id_master']; ?>" style="<?php echo $display; ?>">
                                                <td colspan="5" align="left" width="980" heigth="20" bgcolor="#F0F0F0"> 
                                                    <strong> <?php echo $nome_master; ?></strong>
                                                </td>
                                            </tr>
                                            <tr class="master_<?php echo $row_master['id_master']; ?>" style="<?php echo $display; ?>">
                                                <td width="100"><?php echo $row_master['nome'] ?></td>
                                                <td width="50" align="right"><strong>E-mail:</strong></td>
                                                <td width="200"><input type="text" name="email[<?php echo $row_master['id_master']; ?>]" class="email"  value="<?php echo $row_email['email']; ?>"/></td>
                                                <td width="150" align="right"><strong>Senha do e-mail:</strong></td>
                                                <td width="400"><input type="password" name="senha_email[<?php echo $row_master['id_master']; ?>]" rel="<?php echo $row_master['id_master']; ?>" class="senha_email" value="<?php echo $row_email['senha']; ?>"/> <span class="menssagem"></span>

                                                </td>
                                            </tr>

                                            <?php
                                        endwhile;
                                        unset($checked);
                                        ?>
                                    </table>
                                </td>
                            </tr>

                            <tr style="border: 1px solid #ddd;">
                                <td colspan="9" class="info text-center text-bold">Gerenciamento de Acesso a Intranet</td>
                            </tr>

                            
                            <tr  bgcolor="#EFEFEF" style="border: 1px solid #ddd;">
                                <td  colspan="9" align="center" style="border:1px solid #ddd; border-right:1px solid #ddd"><strong>Acesso as regiões</strong></td>
                            </tr>

                            <?php
////CONTROLE DE ACESSO DAS REGIõES
                            $array_status = array(1 => 'REGIÕES ATIVAS', 0 => 'REGIÕES INATIVAS');

                            foreach ($array_status as $status => $nome_status) {
                                ?>
                                <tr>
                                    <td bgcolor="#EFEFEF" align="center" valign="top"><?php echo $nome_status; ?></td>
                                    <td  colspan="6" style="border-bottom:1px solid #ddd; border-right:1px solid #ddd">
                                        <table class='table text-sm valign-middle'>
                                            <?php
                                            if ($status == 0) {
                                                $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = '$status' OR status_reg = '$status' ORDER BY id_master");
                                            } else {
                                                $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = '$status' AND status_reg = '$status' ORDER BY id_master");
                                            }

                                            while ($row_regioes = mysql_fetch_assoc($qr_regioes)):
                                                if ($row_regioes['id_regiao'] == 38 and $row_regioes['id_regiao'] == 16)
                                                    continue;

                                                $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regioes[id_master]' ");
                                                $row_master = mysql_fetch_assoc($qr_master);

                                                $verifica_reg_assoc = mysql_num_rows(mysql_query("SELECT * FROM funcionario_regiao_assoc WHERE id_regiao = '$row_regioes[id_regiao]' AND id_funcionario = '$id_user'"));
                                                $checked = ($verifica_reg_assoc != 0) ? 'checked="checked"' : '';


                                                if ($row_master['id_master'] != $master_anterior) {
                                                    echo '<tr  bgcolor="#C7E2E2">'
                                                    . '<td align="left">' . $row_master['nome'] . '</td>'
                                                    .'<td align="rigth">
                                                    <input name="todos_master"  type="checkbox" value="' . $row_regioes['id_master'] . '_' . $status . '" class="todos_master" id="todos_master' . $row_regioes['id_master'] . '_' . $status . '" />
                                                    <label class="control-label pointer" for="todos_master' . $row_regioes['id_master'] . '_' . $status . '">Marcar/Desmarcar todos</label>
                                        	  </td>
					  </tr>';
                                                }

                                                echo '<tr bgcolor="#D9ECFF">
						<td colspan="2">
							<input name="empresas[]"  type="hidden" value="' . $row_regioes['id_master'] . '"/>
							<input name="regioes_permitidas[' . $row_regioes['id_master'] . '][]" type="checkbox" value="' . $row_regioes['id_regiao'] . '"  ' . $checked . '  class="master_' . $row_regioes['id_master'] . '_' . $status . '"/>' . $row_regioes['id_regiao'] . ' - ' . ($row_regioes['regiao']) . '
						</td>
     				</tr>';

                                                $master_anterior = $row_master['id_master'];

                                            endwhile;

                                            echo '<tr><td style="border:1px solid transparent">&nbsp;</td></tr>';

                                            unset($master_anterior);
                                            ?>  
                                        </table>
                                    </td>
                                </tr>
                                <tr><td></td></tr>
                                <?php
                            } //fim foreach
///////////////////////////////////////
                            ?>




                            
                            <?php
                            $qr_botoes_pg = mysql_query("SELECT * FROM botoes_pagina WHERE 1");
                            while ($row_pagina = mysql_fetch_assoc($qr_botoes_pg)):
                                
                                echo '<tr bgcolor="#EFEFEF">
                                    
			<td colspan="8" align="center" style="border-bottom:1px solid #ddd; border-right:1px solid #ddd"><strong>' . $row_pagina['botoes_pg_nome'] . '</strong><br></td>
			</tr>';
                                ///PERMISSÔES PARA OS RELATÓRIOS DO FINANCEIRO
                                if ($row_pagina['botoes_pg_id'] == 3) {

                                    echo '<tr>
                                            <td style="background-color: #EFEFEF;" align="center">PÁGINA INICIAL</td>
                                            <td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd">';
                                            
                                    $qr_acoes = mysql_query("SELECT * FROM acoes WHERE botoes_pagina_id = '$row_pagina[botoes_pg_id]'");
                                    
                                    while ($row_acoes = mysql_fetch_assoc($qr_acoes)):

                                        $qr_acoes_assoc = mysql_query("SELECT * FROM funcionario_acoes_assoc  WHERE id_funcionario = '$id_user'  AND acoes_id = '$row_acoes[acoes_id]' ");
                                        $checked = (mysql_num_rows($qr_acoes_assoc) != 0) ? 'checked="checked"' : '';
                                        
                                        echo "<input type='checkbox' name='acoes[]' value='" . $row_acoes['acoes_id'] . "' " . $checked . " class='subgrupo".$row_acoes['acoes_id']."'/> " . $row_acoes['acoes_nome'] . "<br>";


                                    endwhile;
                                }
                                echo '</td></tr><tr><td></td></tr>';
                                ////////////////////////////////////////////


                                $qr_botoes_menu = mysql_query("SELECT * FROM botoes_menu WHERE botoes_pagina = '$row_pagina[botoes_pg_id]'");
                                while ($row_botoes_menu = mysql_fetch_assoc($qr_botoes_menu)) {
                                    $todos++;
                                    ?>
                                <tr  bgcolor="#EFEFEF">
                                    <td  colspan="8" align="center" style="border:1px solid #ddd; border-right:1px solid #ddd"><strong>ACESSO <?php echo $row_botoes_menu['botoes_menu_nome'] ?></strong></td>
                                </tr>
                                    <tr>
                                        <td height="30" bgcolor="FFF" align="center" valign="top" style="background-color: #F5F5F5" >

                                            <?php echo $row_botoes_menu['botoes_menu_nome'] ?><br><br><br>

                                            

                                        </td>
                                        
                                        <td colspan="7" style="border-bottom:1px solid #ddd; border-right:1px solid #ddd">         
                                            <table class="table" style="">
                                                <tr class="info" colspan="7" style="text-align:right;">
                                                    <td style="border:1px solid transparent">
                                                        <input type="checkbox" class="tipo_menu"  name="todos" value="<?php echo $todos; ?>" id="check<?php echo $todos; ?>" />&nbsp;<label class="control-label pointer" for="check<?php echo $todos; ?>">Marcar/Desmarcar todos</label>
                                                    </td>
                                                </tr>
                                            </table>
                                            <?php
                                            $qr_botoes = mysql_query("SELECT * FROM botoes WHERE   botoes_menu = '$row_botoes_menu[botoes_menu_id]'  ORDER BY  botoes_menu ASC");
                                            $contador_icone = 0;

                                            while ($row_botoes = mysql_fetch_assoc($qr_botoes)):



                                                $qr_botoes_assoc = mysql_query("SELECT * FROM botoes_assoc WHERE botoes_id = '$row_botoes[botoes_id]'  AND id_funcionario = '$id_user' ");
                                                $row_assoc = mysql_fetch_assoc($qr_botoes_assoc);


                                                ////permisões pra deletar, exluir e etc;.
                                                $qr_acoes = mysql_query("SELECT * FROM acoes WHERE   botoes_id = '$row_botoes[botoes_id]' ORDER BY tp_contratacao_id ASC") or die(mysql_error());

                                                /////GESTÃO DE COMPRAS
                                                if ($row_botoes['botoes_id'] == 8) {

                                                    echo '<table class="table text-sm valign-middle">';
                                                    ?>
                                            <tr bgcolor="#C7E2E2">

                                                <td colspan="2">
                                                    <input type="checkbox" name="botoes[]" value="<?php echo $row_botoes['botoes_id']; ?>"  <?php if ($row_assoc['botoes_id'] == $row_botoes['botoes_id']) echo 'checked'; ?> class="<?php echo $todos; ?>"> <?php echo $row_botoes['botoes_nome']; ?> -  ETAPAS DE COMPRA
                                                </td>  
                                            </tr>


                                            <?php
                                            $qr_acompanhamento = mysql_query("SELECT * FROM acompanhamento_compra WHERE status = 1") or die(mysql_error());
                                            while ($row_acomp = mysql_fetch_assoc($qr_acompanhamento)):

                                                $verifica_acomp = mysql_num_rows(mysql_query("SELECT * FROM func_acompanhamento_assoc WHERE id_funcionario = '$id_user' AND id_acompanhamento = '$row_acomp[acompanhamento_id]'"));

                                                $checked = ($verifica_acomp != 0) ? 'checked="checked"' : '';
                                                echo '<tr bgcolor="#D9ECFF">                                       
											 <td colspan="2">
											 	<input type="checkbox" name="acomp_compra[]" value="' . $row_acomp['acompanhamento_id'] . '" ' . $checked . '/> ' . $row_acomp['acompanhamento_nome'] . '
											 </td>
										</tr>';


                                            endwhile;


                                            echo '</table>';
                                            unset($checked);
                                        }   ////////////FIM BOTÃO 8



                                        if (mysql_num_rows($qr_acoes) != 0) {
                                            ?>

                                            <table class="table text-sm valign-middle">
                                                <tr bgcolor="#C7E2E2">

                                                    <td colspan="2">
                                                        <input type="checkbox" name="botoes[]" value="<?php echo $row_botoes['botoes_id']; ?>"  <?php if ($row_assoc['botoes_id'] == $row_botoes['botoes_id']) echo 'checked'; ?> class="<?php echo $todos; ?>"> <?php echo $row_botoes['botoes_nome']; ?> -  Ações
                                                    </td>  
                                                </tr>


                                                <?php
                                                ////acoes	
                                                while ($row_acoes = mysql_fetch_assoc($qr_acoes)):

                                                    $checked = acoes_checked($row_acoes['acoes_id'], $id_user);

                                                    echo '<tr bgcolor="#D9ECFF"><td width="30">&nbsp;</td> <td><input type="checkbox" name="acoes[]" value="' . $row_acoes['acoes_id'] . '" ' . $checked . ' class="master_acoes_' . $row_acoes['tp_contratacao_id'] . ' subgrupo'.$todos.'"/> ' . '(' . $row_acoes['acoes_id'] . ') ' . $row_acoes['acoes_nome'] . '</td></tr>';

                                                endwhile;




                                                ///BOTÕES VISUALIZAR OBRIGAÇÃO E EXCLUIR OBRIGAÇÃO			
                                                if ($row_botoes['botoes_id'] == 82) {

                                                    while ($row_acoes = mysql_fetch_assoc($qr_acoes)):

                                                        $checked = acoes_checked($row_acoes['acoes_id'], $id_user);

                                                        echo '<tr bgcolor="#D9ECFF"><td width="30">&nbsp;</td> <td><input type="checkbox" name="acoes[]" value="' . $row_acoes['acoes_id'] . '" ' . $checked . ' class="subgrupo'.$todos.'"/> ' . '(' . $row_acoes['acoes_id'] . ')' . $row_acoes['acoes_nome'] . '</td></tr>';

                                                    endwhile;
                                                } else
                                                //CONDIÇÃO PARA EXIBIR AS REGIÕES PERMITIDAS PARA VISUALIZAÇÃO DA FOLHA 
                                                if ($row_botoes['botoes_id'] == 33 or $row_botoes['botoes_id'] == 60) {


                                                    while ($row_acoes = mysql_fetch_assoc($qr_acoes)):


                                                        $checked = acoes_checked($row_acoes['acoes_id'], $id_user);

                                                        //ações
                                                        echo '<tr bgcolor="#D9ECFF"><td width="30">&nbsp;</td> <td><input type="checkbox" name="acoes[]" value="' . $row_acoes['acoes_id'] . '" ' . $checked . ' class="subgrupo'.$todos.'"/> ' . '(' . $row_acoes['acoes_id'] . ')' . $row_acoes['acoes_nome'] . '</td></tr>';

                                                    endwhile;


                                                    echo'<tr  bgcolor="#D9ECFF"><td colspan="2">';

                                                    foreach ($array_status as $status => $nome_status) {



                                                        if ($status == 0) {
                                                            $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = '$status' OR status_reg = '$status' ORDER BY id_master");
                                                            echo '<tr  bgcolor="#D9ECFF"><td colspan="2" >&nbsp;</td></tr><tr  bgcolor="#74BABA" height="25"><td colspan="2" align="center">REGIÕES INATIVAS</td></tr>';
                                                        } else {
                                                            $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = '$status' AND status_reg = '$status' ORDER BY id_master");
                                                            echo '<tr  bgcolor="#D9ECFF"><td colspan="2" >&nbsp;</td></tr><tr  bgcolor="#74BABA" height="25"><td colspan="2" align="center">REGIÕES ATIVAS</td></tr>';
                                                        }

                                                        while ($row_regioes = mysql_fetch_assoc($qr_regioes)):

                                                            $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regioes[id_master]' ");
                                                            $row_master = mysql_fetch_assoc($qr_master);

                                                            if ($row_master['status'] == 0)
                                                                continue;

                                                            $verifica_reg_assoc = mysql_num_rows(mysql_query("SELECT * FROM funcionario_acoes_assoc WHERE id_regiao = '$row_regioes[id_regiao]' AND id_funcionario = '$id_user' AND botoes_id = '$row_botoes[botoes_id]'"));
                                                            $checked = ($verifica_reg_assoc != 0) ? 'checked="checked"' : '';



                                                            if ($row_master['id_master'] != $master_anterior) {
                                                                echo '<tr  bgcolor="#C7E2E2"><td align="left" colspan="2">' . $row_master['nome'] . ' 
									<span style="float:right;"> 
                                                                            <input name=""  type="checkbox" value="' . $row_botoes['botoes_id'] . $row_regioes['id_master'] . '_' . $status . '"   class="todos_master" id="' . $row_botoes['botoes_id'] . $row_regioes['id_master'] . '_' . $status . '" />'
                                                                            . '<label class="control-label pointer" for="' . $row_botoes['botoes_id'] . $row_regioes['id_master'] . '_' . $status . '">Marcar/Desmarcar todos</label>'
                                                                        . '</span> </td></tr>';
                                                            }

                                                            echo '<tr bgcolor="#D9ECFF">
																		<td colspan="2">
																			<input name="acoes_folhas[' . $row_botoes['botoes_id'] . ']" type="hidden" value="' . $row_acoes['acoes_id'] . '"/>
																			<input name="regiao_folhas[' . $row_botoes['botoes_id'] . '][]" type="checkbox" value="' . $row_regioes['id_regiao'] . '"  ' . $checked . '  class="master_' . $row_botoes['botoes_id'] . $row_regioes['id_master'] . '_' . $status . '"/>' . $row_regioes['id_regiao'] . ' - ' . ($row_regioes['regiao']) . '
																		</td>
																	</tr>';

                                                            $master_anterior = $row_master['id_master'];

                                                        endwhile;
                                                    }
                                                } else if ($row_botoes['botoes_id'] == 6) {





                                                    while ($row_acoes = mysql_fetch_assoc($qr_acoes)):

                                                        $checked = acoes_checked($row_acoes['acoes_id'], $id_user);

                                                        if ($row_acoes['tp_contratacao_id'] != $tipo_contratacao_anterior) {

                                                            $nome_tipo = mysql_result(mysql_query("SELECT tipo_contratacao_nome FROM tipo_contratacao WHERE tipo_contratacao_id = '$row_acoes[tp_contratacao_id]'"), 0);


                                                            echo '<tr  bgcolor="#D9ECFF"><td colspan="2" >&nbsp;</td></tr><tr  bgcolor="#74BABA" height="25"><td colspan="2" >' . $nome_tipo . ' <span style="float:right;"> <input name=""  type="checkbox" value="acoes_' . $row_acoes['tp_contratacao_id'] . '"   class="todos_master"  />Marcar/Desmarcar todos </span> </td>
												
												</tr>';
                                                        }


                                                        //ações
                                                        echo '<tr bgcolor="#D9ECFF"><td width="30">&nbsp;</td> <td><input type="checkbox" name="acoes[]" value="' . $row_acoes['acoes_id'] . '" ' . $checked . ' class="master_acoes_' . $row_acoes['tp_contratacao_id'] . ' subgrupo'.$todos.'"/> ' . '(' . $row_acoes['acoes_id'] . ') ' . $row_acoes['acoes_nome'] . '</td></tr>';

                                                        $tipo_contratacao_anterior = $row_acoes['tp_contratacao_id'];
                                                    endwhile;
                                                }


                                                echo '</td></tr>';




                                                echo '</table>';
                                            } else if ($row_botoes['botoes_id'] == 100) {
                                                ?> 
                                                <table class="table text-sm valign-middle">
                                                    <tr><td style="border:1px solid transparent">
                                                <input type="checkbox" name="botoes[]" value="<?php echo $row_botoes['botoes_id']; ?>"  <?php if ($row_assoc['botoes_id'] == $row_botoes['botoes_id']) echo 'checked'; ?> class="<?php echo $todos; ?>"> <?php echo '(' . $row_botoes['botoes_id'] . ') ' . $row_botoes['botoes_nome']; ?><br/>
                                                        </td></tr>
                                                </table>
                                                <table class="table text-sm valign-middle">
                                                    <tr>
                                                        <td>
                                                            <?php
                                                            foreach ($array_status as $status => $nome_status) {



                                                                if ($status == 0) {
                                                                    $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = '$status' OR status_reg = '$status' ORDER BY id_master");
                                                                    echo '<tr  bgcolor="#D9ECFF"><td colspan="2" >&nbsp;</td></tr><tr  bgcolor="#74BABA" height="25"><td colspan="2" align="center">REGIÕES INATIVAS</td></tr>';
                                                                } else {
                                                                    $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = '$status' AND status_reg = '$status' ORDER BY id_master");
                                                                    echo '<tr  bgcolor="#D9ECFF"><td colspan="2" >&nbsp;</td></tr><tr  bgcolor="#74BABA" height="25"><td colspan="2" align="center">REGIÕES ATIVAS</td></tr>';
                                                                }

                                                                while ($row_regioes = mysql_fetch_assoc($qr_regioes)):

                                                                    $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regioes[id_master]' ");
                                                                    $row_master = mysql_fetch_assoc($qr_master);

                                                                    if ($row_master['status'] == 0)
                                                                        continue;
                                                                    $qr_reg_relatorio = mysql_query("");



                                                                    $verifica_reg_assoc = mysql_num_rows(mysql_query("SELECT * FROM regioes_relatorios_assoc WHERE id_funcionario = '$_COOKIE[logado]' AND id_regiao = '$row_regioes[id_regiao]'"));
                                                                    $checked = ($verifica_reg_assoc != 0) ? 'checked="checked"' : '';



                                                                    if ($row_master['id_master'] != $master_anterior) {
                                                                        echo '<tr  bgcolor="#C7E2E2"><td align="left">' . $row_master['nome'] . ' 
									<td style="text-align:right;"> <input name=""  type="checkbox" value="' . $row_botoes['botoes_id'] . $row_regioes['id_master'] . '_' . $status . '"   class="todos_master"  id="master_'.$row_regioes['id_master'] . '_' . $status.'"/>   <label class="control-label pointer" for="master_'.$row_regioes['id_master'] . '_' . $status.'">Marcar/Desmarcar todos</label> </td></tr>';
                                                                    }

                                                                    echo '<tr bgcolor="#D9ECFF">
																		<td colspan="2">
																			<input name="regiao_relatorios[]" type="checkbox" value="' . $row_regioes['id_regiao'] . '"  ' . $checked . '  class="master_' . $row_botoes['botoes_id'] . $row_regioes['id_master'] . '_' . $status . '"/>' . $row_regioes['id_regiao'] . ' - ' . ($row_regioes['regiao']) . '
																		</td>
																	</tr>';

                                                                    $master_anterior = $row_master['id_master'];

                                                                endwhile;
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                </table>


                                            <?php } else { ?>
                                                    <table class="table text-sm valign-middle">
                                                        <tr>
                                                            <td style="border:1px solid transparent">
                                                <input type="checkbox" name="botoes[]" value="<?php echo $row_botoes['botoes_id']; ?>"  <?php if ($row_assoc['botoes_id'] == $row_botoes['botoes_id']) echo 'checked'; ?> class="<?php echo $todos; ?>"> <?php echo '(' . $row_botoes['botoes_id'] . ') ' . $row_botoes['botoes_nome']; ?><br>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                <?php
                                            }
                                        endwhile;

                                        echo '</td>
		</tr>
		
		<tr><td>&nbsp; </td</tr>';

                                        unset($checked);
                                    };

                                endwhile;
                                ?>

                                <tr>
                                    <td height="56" colspan='4' align='center' bgcolor="#FFFFFF">

                                        <input type='button' name='Submit9' value='CANCELAR' class="btn btn-danger" id="cancelar_form">
                                        <input type='submit' name='Submit9' value='CADASTRAR' class="btn btn-success">

                                        <input type='hidden' name='pag' value='<?= $pag ?>'>
                                        <input type='hidden' name='id_cadastro' value='3'>
                                        <input type='hidden' name='id_funcionario' value='<?= $row_user['id_funcionario'] ?>'>  </td>
                                </tr>
                            </table>
                            </form>
                            <form name="formbusca" id="formbusca" action="" method="POST">
                                <input id="id_funcionario_busca" name="id_funcionario_busca" type="hidden" class="form-control input-md" value="">
                            </form>
                
                            <br>
                            <!-- MOdal de BUSCA de copiar o perfil -->
                            <!-- Modal -->
                             <div class="modal fade" id="modalbuscar" role="dialog">
                               <div class="modal-dialog modal-lg">
                                   <div class="modal-content">
                                   <div class="modal-header btn-primary">
                                     <button type="button" class="close" data-dismiss="modal">&times;</button>
                                     <h4 class="modal-title">Lista</h4>
                                   </div>
                                   <div class="modal-body">
                                     <!-- Text input-->
                                        <div class="form-group">
                                          <label class="col-md-4 control-label" for="regiao">Nome do Perfil</label>  
                                          <div class="col-md-4">
                                            <input id="buscar" name="buscar" type="text" placeholder="nome do perfil" class="form-control input-md">
                                          </div>
                                        </div>
                                   </div>
                                   <div class="modal-footer">
                                     <button type="button" class="btn btn-success" data-dismiss="modal" id="copiar_perfil">Copiar</button>
                                   </div>
                                 </div>
                               </div>
                             </div>
                            
                            <div class="clear"></div>
            
                        <?php include("../../template/footer.php"); ?>                       
                            
                        </div>
                            <script src="../../js/jquery-1.10.2.min.js"></script>
                            <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
                            <script src="../../resources/js/bootstrap.min.js"></script>
                            <script src="../../resources/js/bootstrap-dialog.min.js"></script>
                            <script src="../../resources/js/tooltip.js"></script>
                            <script src="../../resources/js/main.js"></script>
                            <script src="../../js/global.js"></script>
                            <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
                            <script src="../../js/jquery.validationEngine-2.6.js"></script>
                            <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
                            <script>
                                function validaForm() {
                                    d = document.form1;
                                    if (d.funcao.value == "") {
                                        alert("O campo Função deve ser preenchido!");
                                        d.funcao.focus();
                                        return false;
                                    }
                                    if (d.locacao.value == "") {
                                        alert("O campo Lotação deve ser preenchido!");
                                        d.locacao.focus();
                                        return false;
                                    }

                                    if (d.nome.value == "") {
                                        alert("O campo Nome deve ser preenchido!");
                                        d.nome.focus();
                                        return false;
                                    }
                                    if (d.nome1.value == "") {
                                        alert("O campo Nome para Exibição deve ser preenchido!");
                                        d.nome1.focus();
                                        return false;
                                    }
                                    if (d.login.value == "") {
                                        alert("O campo Login deve ser preenchido!");
                                        d.login.focus();
                                        return false;
                                    }
                                    if (d.nome.value == "") {
                                        alert("O campo Nome deve ser preenchido!");
                                        d.nome.focus();
                                        return false;
                                    }
                                    return true;
                                }

                                $(function () {

                                    $('input[name=todos]').click(function () {

                                        var verifica = $(this).attr('checked');
                                        var numero = $(this).val();


                                        if (verifica == true) {

                                            $('.' + numero).attr('checked', 'checked');

                                        } else {

                                            $('.' + numero).attr('checked', false)
                                        }
                                    });


                                    $('.todos_master').click(function () {

                                        var valor = $(this).val();
                                        var verifica = $(this).attr('checked');


                                        if (verifica == true) {

                                            $('.master_' + valor).attr('checked', 'checked');

                                        } else {

                                            $('.master_' + valor).attr('checked', false);
                                        }

                                    });
                                    
                                    $('.data').datepicker();

                                    $('input[name=todos]').click(function () {

                                        var numero = $(this).val();
                                        var verifica = $(this).is(":checked");

                                        if (verifica) {

                                            $('.' + numero).prop("checked", true);
                                            $('.subgrupo' + numero).prop("checked", true);

                                        } else {

                                            $('.' + numero).attr('checked', false);
                                            $('.subgrupo' + numero).attr("checked", false);
                                        }
                                    });
                                    var availableTags = [
                                    <?php 
                                            $listafunc = "";
                                            foreach($funcionarios as $row) { 
                                            $listafunc .= "'{$row['id_funcionario']} - {$row['nome']}' ,";
                                        }  
                                        echo substr($listafunc, 0, -1);
                                        ?>
                                        ];
                                    $( "#buscar" ).autocomplete({
                                                source: availableTags
                                              });
                                    
                                    $("#buscar").change(function(){
                                        var val = $(this).val();
                                        var res = val.split(" - ");
                                        $("#id_funcionario_busca").val(res[0]);
                                    });
                                    
                                    $('#copiar_perfil').click(function () {
                                       $("#formbusca").attr("action","form_usuario_new.php");
                                        $("#formbusca").submit();
                                    });
                                    
                                    $('#cancelar_form').click(function () {
                                        location.href="index.php";
                                    });
                                    
                                    $('.todos_master').click(function () {

                                        var valor = $(this).val();
                                        var verifica = $(this).is(":checked");
                                        if (verifica) {
                                            $('.master_' + valor).prop("checked", true);
                                        } else {
                                            $('.master_' + valor).attr('checked', false);
                                        }
                                    });


                                    $('.master_email').change(function () {
                                        var master_id = $(this).val();
                                        var senha = $(this).parent().parent().parent().find('.senha');
                                        var email = $(this).parent().parent().parent().find('.email');

                                        if ($(this).attr('checked')) {
                                            $('.master_' + master_id).css('display', 'block');
                                        } else {
                                            $('.master_' + master_id).hide();
                                        }
                                    });
                                    $("#copiar").click(function() {
                                        $("#modalbuscar").modal("show");
                                    });
                                    
                                    $("#horario_inicio").mask("99:99:99");
                                    $("#horario_fim").mask("99:99:99");
                                    
                                    $('.senha_email').live('blur', function () {

                                        var master = $(this).attr('rel');
                                        var senha = Base64.encode($(this).val());
                                        var email = $(this).parent().parent().find('.email').val();
                                        var menssagem = $(this).parent().parent().find('.menssagem');

                                        $.ajax({
                                            url: 'action.verifica_email.php?master=' + master + '&senha=' + senha + '&email=' + email,
                                            success: function (resposta) {


                                                if (resposta == 1) {
                                                    
                                                    menssagem.html('<span class="ok"> OK</span>')

                                                } else {
                                                    menssagem.html('<span class="email_incorreto"> E-mail ou senha incorreto!</span>');

                                                }

                                            }

                                        });

                                    });
                                });
                            </script>

                            </body>
                            </html>
