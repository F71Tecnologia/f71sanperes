<?php
include('conn.php');
include('funcoes.php');
include("classes_permissoes/regioes.class.php");
include('wfunction.php');

/*
$array_funcionarios = array(82,87);

if (in_array($_COOKIE['logado'], $array_funcionarios)) {

    $total = 0;
    $qr_clt = mysql_query("SELECT id_clt, nome FROM rh_clt WHERE status IN (40,50,51,20,52)");
    while ($row_clt = mysql_fetch_assoc($qr_clt)):

        $qr_eventos = mysql_query("SELECT * FROM rh_eventos WHERE  cod_status IN(40,50,51,20,52) AND  id_clt = '$row_clt[id_clt]' ORDER BY data_retorno DESC");
        $row_eventos = mysql_fetch_assoc($qr_eventos);

        $dt_retorno = explode('-', $row_eventos['data_retorno']);
        $data_retorno = mktime(0, 0, 0, $dt_retorno[1], $dt_retorno[2], $dt_retorno[0]);
        $data_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

        if ($data_retorno <= $data_atual) {

            $total++;
        }

    endwhile;


    if ($total != 0) {

        echo '<script type="text/javascript">
	       
		  alert("EXISTEM TRABALHADORES QUE DEVEM VOLTAR DE FÉRIAS OU LICENÇA.");
		  location.href = "rh/ferias_normal.php?regiao=' . $_GET['regiao'] . '";
		  
		 </script>';
        exit();
    }
}
*/

$usuario = carregaUsuario();
$regiao_usuario = $usuario['id_regiao'];

$obj_regiao = new Regioes();

$id_user = $_COOKIE['logado'];
$regiao = $_GET['regiao'];
$qr_regiao = mysql_query("SELECT id_regiao,regiao FROM regioes WHERE id_regiao = '$regiao'");
$rw_regiao = mysql_fetch_array($qr_regiao);
$query_funcionario = mysql_query("SELECT id_funcionario, nome, tipo_usuario FROM funcionario WHERE id_funcionario = '$id_user'");
$row_funcionario = mysql_fetch_array($query_funcionario);
$tipo_user = $row_funcionario['tipo_usuario'];
$query_master = mysql_query("SELECT master.id_master, master.razao FROM regioes 
                            INNER JOIN master 
                            ON regioes.id_master = master.id_master
                            WHERE regioes.id_regiao = '$regiao'") or die(mysql_error());

$row_master = mysql_fetch_assoc($query_master);

$id_master = $row_master['id_master'];
$razao = $row_master['razao'];

//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkfo = encrypt("$regiao&1");
$linkfo = str_replace("+", "--", $linkfo);
// -----------------------------
//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkevento = encrypt("$regiao");
$linkevento = str_replace("+", "--", $linkevento);

//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkferias = encrypt("$regiao&1");
$linkferias = str_replace("+", "--", $linkferias);
// -----------------------------

/* Resumo */
$result_cont_total_geral = mysql_query("SELECT id_clt FROM rh_clt where id_regiao = '$regiao'");
$row_cont_total_geral = mysql_num_rows($result_cont_total_geral);

$result_sexo_m = mysql_query("SELECT * FROM rh_clt where sexo = 'M' and id_regiao = '$regiao' and status != '62'");
$row_cont_sexo_m = mysql_num_rows($result_sexo_m);

$result_sexo_f = mysql_query("SELECT * FROM rh_clt where sexo = 'F' and id_regiao = '$regiao' and status != '62'");
$row_cont_sexo_f = mysql_num_rows($result_sexo_f);

$dia = date('d');
$mes = date('m');
$ano = date('Y');
$data_antiga = date("Y-m-d", mktime(0, 0, 0, $mes, $dia - 90, $ano));
/* Resumo */

// Bloqueio Administração
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        <title>Gest&atilde;o de RH</title> 
        <script type="text/javascript" src="jquery/jquery-1.4.2.min.js" ></script>
        <script type="text/javascript" src="js/highslide-with-html.js"></script> 
        <link href="js/highslide.css" rel="stylesheet" type="text/css"  /> 
        <script src="jquery/jquery.tools.min.js" type="text/javascript"></script>

        <link href="css_principal.css" rel="stylesheet" type="text/css" />

        <script>

            $(function() {
                $("ul.tabs").tabs("div.panes > div");
            });

            hs.graphicsDir = 'images-box/graphics/';
            hs.outlineType = 'rounded-white';

            $(function() {
                $('#botoes ul li img').fadeTo('fast', 0.7).hover(function() {
                    $(this).fadeTo('fast', 1.0)
                }, function() {
                    $(this).fadeTo('fast', 0.7)
                });
                
                $("#resumo table tr:odd").addClass('linha_dois');
                $("#resumo table tr:even").addClass('linha_um');
                $('#resumo').find('table').find('tr:first').addClass('titulo_table');
                
                $('#select_regiao').change(function() {                    
                    var regiao = $(this).val();
                    var regiao_de = $("#regiao_logado").val();
                    var user = $("#user").val();                                        
                    $.ajax({
                        url: 'cadastro2.php?regiao='+regiao+'&regiao_de='+regiao_de+'&user='+user+'&id_cadastro=13',
                        success: function(){
                            location.href = 'principalrh.php?id=1&regiao='+ regiao;
                        }
                    });
                    $("#regiao_selecionada").val(regiao);
                });
            });
            
            function MM_jumpMenu(targ, selObj, restore) { //v3.0
                eval(targ + ".location='" + selObj.options[selObj.selectedIndex].value + "'");
                if (restore)
                    selObj.selectedIndex = 0;
            }
            
            function MM_openBrWindow(theURL, winName, features) { //v2.0
                window.open(theURL, winName, features);
            }                        
        </script>
    </head>
    <body style="background-color: #E8FFF3;">

        <div id="corpo" >
            <div id="conteudo">
                <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                                        
                    <input type="hidden" name="user"  id="user"  value="<?= $id_user ?>"/>
                    <input type="hidden" name="regiao_logado" id="regiao_logado" value="<?php echo $regiao_usuario; ?>" />
                    <input type="hidden" name="regiao_selecionada" id="regiao_selecionada" value="" />

                <div id="topo">	
                    <table width="100%">
                        <tr>
                            <td width="11%" height="81" align="center"> <img src="imagens/logomaster<?= $id_master ?>.gif" width="110" height="79"></td>
                            <td width="36%" align="left" valign="top">
                                <br />
                                <span>Gest&atilde;o de Recursos Humanos</span><br />
                                <span class="nome"><?php echo $row_funcionario[1] ?></span><br />
                                <strong>Data:</strong> <?php echo date('d/m/Y'); ?><br />
                                <strong>Regiao:</strong> <?php echo $rw_regiao[1]; ?>
                            </td>
                            <td width="53%" align="right">
                                <table width="0" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <?php $pagina = $_SERVER['PHP_SELF']; ?>
                                        <td>
                                            <a href="box_suporte.php?&regiao=<?php echo $regiao; ?>&pagina=<?php echo $pagina; ?>" onClick="return hs.htmlExpand(this, {objectType: 'iframe'})" >
                                                <img src="imagens/suporte.gif" style="margin-left:10px;"/>
                                            </a>
                                        </td>
                                        <td></td>
                                        <td>&nbsp;</td>                 
                                    </tr>
                                </table>                                    
                            </td>
                        </tr>

                        <tr class="barra">
                            <td colspan="3">
                                TROCAR REGIÃO:
                                <!------ Visualizando Regiões --------->
                                <select name='select_regiao' class='campotexto' id='select_regiao'>
                                <?php $obj_regiao->Preenhe_select_por_master($id_master, $regiao); ?>
                                </select>
                            </td>
                        </tr>
                    </table>

                </div>

                <div id="menu_principal">
                    <ul class="tabs">
                        <li>
                            <a href="#">
                                <div class="sombra1"> PRINCIPAL                                               
                                    <div class="texto"> PRINCIPAL</div>              
                                </div>
                            </a>
                        </li>
                        <?php
                        $qr_botoes_pg = mysql_query("SELECT * FROM botoes_pagina WHERE botoes_pg_id = 2");
                        while ($row_pagina = mysql_fetch_assoc($qr_botoes_pg)):

                            $qr_botoes_menu = mysql_query("SELECT * FROM botoes_menu WHERE botoes_pagina ='$row_pagina[botoes_pg_id]'  ORDER BY botoes_menu_id ");
                            while ($row_btn_menu = mysql_fetch_assoc($qr_botoes_menu)):

                                $qr_botoes = mysql_query("SELECT * FROM botoes 
                                                        INNER JOIN botoes_assoc 
                                                        ON botoes.botoes_id = botoes_assoc.botoes_id
                                                        WHERE botoes.botoes_menu = '$row_btn_menu[botoes_menu_id]'  AND botoes_assoc.id_funcionario = '$_COOKIE[logado]'  ORDER BY botoes.botoes_menu ASC");
                                ?>

                                <li <?php if (mysql_num_rows($qr_botoes) == 0) echo 'style="display:none;"'; ?>>
                                    <a href="#">
                                        <div class="sombra1"> 
                                            <?php echo $row_btn_menu['botoes_menu_nome']; ?>
                                            <div class="texto"><?php echo $row_btn_menu['botoes_menu_nome']; ?></div>
                                        </div>
                                    </a>
                                </li>

                        <?php
                            endwhile;
                        endwhile;
                        ?>
                    </ul>
                </div>

                <div id="submenu" class="panes" >

                    <div class="conteudo_aba" style="display:none;"> 
                        <table border="0" width="100%" class="tabela2">
                            <tr>
                                <td colspan="2" class="titulo_tabela">
                                    <div class="sombra1"> 
                                        CONTROLE DE PARTICIPANTES NA REGIÃO ATÉ A DATA ATUAL 
                                        <div class="texto">  
                                            CONTROLE DE PARTICIPANTES NA REGIÃO ATÉ A DATA ATUAL 
                                        </div>      
                                    </div>
                                </td>
                            </tr>                            
                            <tr>
                                <td width="90%">Total de participantes</td>
                                <td width="10%" align="center"><?= $row_cont_total_geral ?></td>
                            </tr>                            
                            <tr>
                            <td colspan="2" class="titulo_tabela">
                                <div class="sombra1"> CONTROLE DE FUNCIONÁRIOS POR SITUAÇÃO ATUAL <div class="texto">
                                        CONTROLE DE FUNCIONÁRIOS POR SITUAÇÃO ATUAL </div>      </div>
                            </td>
                            </tr>                            
                            <?php
                            $result_rhstatus = mysql_query("SELECT * FROM rhstatus where status_reg = '1'");
                            while ($row_rhstatus = mysql_fetch_array($result_rhstatus)):
                                $result_cont_status = mysql_query("SELECT id_clt FROM rh_clt where status = '$row_rhstatus[codigo]' and id_regiao = '$regiao'");
                                $row_cont_status = mysql_num_rows($result_cont_status);

                                $linha++;
                                ?>
                                <tr class="<?php if ($linha % 2 == 0) {
                                    echo 'linha_um';
                                } else {
                                    echo 'linha_dois';
                                } ?>">
                                    <td width="90%"><?php echo "($row_rhstatus[codigo]) $row_rhstatus[especifica]"; ?></td>
                                    <td width="10%" align="center"><?php echo $row_cont_status; ?></td>
                                </tr>
                            <?php 
                            endwhile; 
                            ?>
                            <tr>
                                <td colspan="2" class="titulo_tabela">
                                    <div class="sombra1">  
                                        CONTROLE DE FUNCIONÁRIOS ATIVOS POR SEXO 
                                        <div class="texto">
                                            CONTROLE DE FUNCIONÁRIOS ATIVOS POR SEXO 
                                        </div>      
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td width="90%">Homens</td>
                                <td width="10%" align="center"><?= $row_cont_sexo_m ?></td>
                            </tr>
                            <tr>
                                <td width="90%">Mulheres</td>
                                <td width="10%" align="center"><?= $row_cont_sexo_f ?></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="titulo_tabela">
                                    <div class="sombra1">  
                                        CONTROLE DE FUNCIONÁRIOS EM EXPERIÊNCIA 
                                        <div class="texto">
                                            CONTROLE DE FUNCIONÁRIOS EM EXPERIÊNCIA 
                                        </div>      
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td width="90%">Funcionário em experiência</td>
                                <td width="10%" align="center">
                                    <?php
                                    $result_data_entrada = mysql_query("SELECT id_clt FROM rh_clt WHERE data_entrada > '$data_antiga' AND id_regiao = '$regiao'");
                                    $row_datas = mysql_num_rows($result_data_entrada);
                                    print $row_datas;
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <?php
                    $qr_botoes_pg = mysql_query("SELECT * FROM botoes_pagina WHERE botoes_pg_id = 2");
                    while ($row_pagina = mysql_fetch_assoc($qr_botoes_pg)):

                        $qr_botoes_menu = mysql_query("SELECT * FROM botoes_menu WHERE botoes_pagina ='$row_pagina[botoes_pg_id]'    ORDER BY botoes_menu_id ");
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
                                                            WHERE botoes.botoes_menu = '$row_btn_menu[botoes_menu_id]'  AND botoes_assoc.id_funcionario = '$_COOKIE[logado]'  ORDER BY botoes.botoes_menu ASC");
                                    
                                    while ($row_botoes = mysql_fetch_assoc($qr_botoes)) {
                                        if ($row_botoes['botoes_id'] == 60 or $row_botoes['botoes_id'] == 55) {
                                            echo '<li><a href="' . $row_botoes['botoes_link'] . $linkfo . '"  target="_blank"  title="' . $row_botoes['botoes_descricao'] . '"><img src="' . $row_botoes['botoes_img'] . '" title="' . $row_botoes['botoes_descricao'] . '"/><br /> ' . $row_botoes['botoes_nome'] . '</a> </li>';
                                        } elseif ($row_botoes['botoes_id'] == 53) {
                                            echo '<li><a href="' . $row_botoes['botoes_link'] . $linkevento . '"  title="' . $row_botoes['botoes_descricao'] . '"><img src="' . $row_botoes['botoes_img'] . '" /><br /> ' . $row_botoes['botoes_nome'] . '</a> </li>';
                                        
                                        //link antigo: rh/rh_sindicatos.php?id=1&regiao=
                                        } elseif ($row_botoes['botoes_id'] == 48) {
                                        echo '<li><a href="' . $row_botoes['botoes_link'] . '"  title="' . $row_botoes['botoes_descricao'] . '" target="_blank"><img src="' . $row_botoes['botoes_img'] . '" /><br /> ' . $row_botoes['botoes_nome'] . '</a> </li>';
                                        
                                        //link antigo: rh/rh_feriados.php?id=1&amp;regiao=
                                        } elseif ($row_botoes['botoes_id'] == 46) {
                                        echo '<li><a href="' . $row_botoes['botoes_link'] . '"  title="' . $row_botoes['botoes_descricao'] . '" target="_blank"><img src="' . $row_botoes['botoes_img'] . '" /><br /> ' . $row_botoes['botoes_nome'] . '</a> </li>';
                                        
                                        } elseif ($row_botoes['botoes_id'] == 66) {
                                            echo '<li> <a href="' . $row_botoes['botoes_link'] . '"  target="_blank"><img src="' . $row_botoes['botoes_img'] . '" title="' . $row_botoes['botoes_descricao'] . '"/><br /> ' . $row_botoes['botoes_nome'] . '</a> </li>';
                                        } elseif ($row_botoes['botoes_id'] == 57) {
                                            echo '<li><a href="' . $row_botoes['botoes_link'] . $linkferias . '"  title="' . $row_botoes['botoes_descricao'] . '"><img src="' . $row_botoes['botoes_img'] . '" /><br /> ' . $row_botoes['botoes_nome'] . '</a> </li>';
                                        } elseif ($row_botoes['botoes_id'] == 58) {
                                            echo '<li> <a href="' . $row_botoes['botoes_link'] . $regiao . '"  target="_blank"><img src="' . $row_botoes['botoes_img'] . '" title="' . $row_botoes['botoes_descricao'] . '"/><br /> ' . $row_botoes['botoes_nome'] . '</a> </li>';
                                        } elseif ($row_botoes['botoes_id'] == 50) { ?>
                                            <li> 
                                                <a href="<?= $row_botoes['botoes_link']; ?>" title="<?= $row_botoes['botoes_descricao'] ?>" target="_blank">
                                                    <img src="<?= $row_botoes['botoes_img'] ?>" /><br />
                                                    <?php echo $row_botoes['botoes_nome']; ?>
                                                </a>
                                            </li>
                                        <?php } elseif ($row_botoes['botoes_id'] == 194) { 
                                            
//                                              if($_COOKIE['logado']==202){
                                                  // habilitar botões a partir de A.`status` = para vale 1 = refeição/ 2 = alimentação, B.`status` para tipos dentro ex.(1 = SODEXO, 2 = ALELO...) 
                                                    $sql_va = "SELECT A.*,B.*, A.`status` AS status_tipo, B.`status` AS status_categoria
                                                            FROM rh_va_tipos AS A
                                                            LEFT JOIN rh_va_categorias AS B ON(A.id_va_categoria=B.id_va_categoria)
                                                            WHERE A.`status`=1 AND B.`status`=1";

                                                    $result_va = mysql_query($sql_va);

                                                    $arr_va = array();

                                                    while($row_va = mysql_fetch_array($result_va)){
                                                        $arr_va[$row_va['id_va_categoria']] = $row_va['nome_categoria'];
                                                        $arr_va_campo[$row_va['id_va_categoria']] = $row_va['campo_clt'];
                                                        $arr_va_tipos[$row_va['id_va_categoria']][$row_va['id_va_tipos']] = $row_va['nome_tipo'];
                                                    }
                                                    foreach($arr_va as $k=>$row_va){
                                                    ?>
                                                        <?php foreach($arr_va_tipos[$k] as $i=>$row_tipo){ ?>
                                                                    <li style="text-transform: uppercase;"> 
                                                                        <a href="<?= $row_botoes['botoes_link'].'?tipo='.$i; ?>" title="<?= $row_botoes['botoes_descricao'] ?>" >
                                                                            <img src="<?= $row_botoes['botoes_img'] ?>" /><br />
                                                                            <?php echo $row_botoes['botoes_nome'].' '.$row_va.' '.$row_tipo; ?>
                                                                        </a>
                                                                    </li>
                                                        <?php } 
                                                      } 
//                                             } ?>
                                     <?php } else { ?>
                                    <li> 
                                        <a href="#" onClick="MM_openBrWindow('<?= $row_botoes['botoes_link'] . $regiao ?>', '', 'scrollbars=yes,resizable=yes,width=760,height=600,toolbars=no')" title="<?= $row_botoes['botoes_descricao'] ?>">
                                            <img src="<?= $row_botoes['botoes_img'] ?>" /><br />
                                            <?php echo $row_botoes['botoes_nome']; ?>
                                        </a>
                                    </li>
                                        <?php } ?>
                                    <?php }///fim loop ?>
                                </ul>
                            </div> 
                    <?php
                        endwhile;
                    endwhile;
                    ?>
                </div>
                <div style="clear:left;"></div>
                </form>
            </div>
            <span class="rodape2"><?= $razao ?> - Acesso Restrito a Funcion&aacute;rios</span>
        </div>
    </body>
</html>