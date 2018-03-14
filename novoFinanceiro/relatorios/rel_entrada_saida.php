<?php
include ("../include/restricoes.php");
include "../../conn.php";
include "../../funcoes.php";
include "../../wfunction.php";
include("../../classes_permissoes/regioes.class.php");

$obj_regiao = new Regioes();


//--VERIFICANDO MASTER -----------------
$id_user = $_COOKIE['logado'];
$REuser = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($REuser);
$tipo_user = $row_user['tipo_usuario'];
$REMaster = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($REMaster);
// ---- FINALIZANDO MASTER -----------------
$regiao = $_REQUEST['regiao'];
$mes2 = date('F');
$dia_h = date('d');
$mes_h = date('m');
$ano = date('Y');
$mes_q_vem = $mes_h + 1;
$meses = array('Erro', 'Janeiro', 'Fevereiro', 'MarÔøΩo', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
$MesInt = (int) $mes_h;
$mes = $meses[$MesInt];
$data_hoje = "$dia_h/$mes_h/$ano";
$anos = anosArray();

list($regiao) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));


//ENCRIPTOGRAFANDO
$linkEnc = encrypt($regiao);
$linkEnc = str_replace("+", "--", $linkEnc);

//EMBELEZAMENTO
$bord = "style='border-bottom:#000 solid 1px; font-size: 12px; font-face:Arial;'";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <title>:: Financeiro ::</title>

    <!-- highslide -->
    <link rel="stylesheet" type="text/css" href="../../js/highslide.css" />
    <script type="text/javascript" src="../../js/highslide-with-html.js"></script>
    <script type="text/javascript" >
        hs.graphicsDir = '../../images-box/graphics/';
        hs.outlineType = 'rounded-white';
        hs.showCredits = false;
        hs.wrapperClassName = 'draggable-header';
    </script>
    <!-- highslide -->

    <script>
        function MM_openBrWindow(theURL, winName, features) { //v2.0
            window.open(theURL, winName, features);
        }
        function MM_jumpMenu(targ, selObj, restore) { //v3.0
            eval(targ + ".location='" + selObj.options[selObj.selectedIndex].value + "'");
            if (restore)
                selObj.selectedIndex = 0;
        }
    </script>
    <link href="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script>

        <script type="text/javascript">
        $(function() {




            $('.date').datepicker({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true
            });

            $('.ano').parent().next().hide();
            $(".ano").click(function() {
                $(this).parent().next().slideToggle();
                $('.ano').parent().next().hide();
            });
            $('.dataautonomos').parent().next().hide();
            $('.dataautonomos').click(function() {
                $(this).parent().next().slideToggle();
                $('.dataautonomos').parent().next().hide();
            });
            $('a.recisao').click(function() {
                $(this).next().toggle();
            });
            
            $('#tipo_anual').change(function() {

                if ($(this).val() == 'entrada') {

                    $('#select_entrada').show();
                    $('#select_saida').hide();

                } else {

                    $('#select_entrada').hide();
                    $('#select_saida').show();

                }
            });



            ////SELECIONAR REGI√ÉO
            $('#select_regiao').change(function() {

                var valor = $(this).val();
                $.ajax({
                    url: 'encriptar.php?encriptar=' + valor,
                    success: function(link_encriptado) {

                        location.href = "rel_entrada_saida.php?enc=" + link_encriptado;

                    }
                });
            });


        });
        </script>
        <style type="text/css">
            body {
                font-family:Arial, Helvetica, sans-serif;
                font-size:12px;
                margin:0px;
                background:#F0F0F0;
            }
            #baseCentral{
                width:980px;
                margin:0px auto;
            }
            #topo{
                position:fixed;
                top:0px;
                background-color:#FFF;
                z-index:1000;
                width:978px;
                height:135px;
                border-top-width: 1px;
                border-right-width: 1px;
                border-left-width: 1px;
                border-top-style: solid;
                border-right-style: solid;
                border-left-style: solid;
                border-top-color: #666;
                border-right-color: #666;
                border-left-color: #666;
            }
            #conteudo {
                position:relative;	
                background-color:#FFF;
                border-right-width: 1px;
                border-bottom-width: 1px;
                border-left-width: 1px;
                border-right-style: solid;
                border-bottom-style: solid;
                border-left-style: solid;
                border-right-color: #666;
                border-bottom-color: #666;
                border-left-color: #666;

            }
            *html #conteudo {
                top:0px;
            }
            <!--
            .style2 {font-size: 12px}
            .style3 {
                color: #FF0000;
                font-weight: bold;
                text-align: center;
            }
            .style6 {
                font-size: 14px;
                font-weight: bold;
                color: #FFFFFF;
            }
            .style9 {color: #FF0000}
            .style12 {
                font-size: 12px;
                font-weight: bold;
                color: #003300;
            }
            .style29 {color: #000000}
            .style31 {	font-family: Arial, Helvetica, sans-serif;
                       font-weight: bold;
                       font-size: 14px;
                       color: #FF0000;
            }
            .style32 {font-size: 10px}
            .style33 {font-family: Verdana, Arial, Sans-Serif}
            .style27 {color:#FFF}
            -->

            #geral h1{
                font-size: 14px;
                color: #F00;
                font-variant: small-caps;
                text-decoration: none;
                margin: 0px;
                padding-top: 0px;
                padding-right: 0px;
                padding-bottom: 0px;
                padding-left: 30px;
            }
            .linha_um {
                background-color:#f5f5f5;
            }
            .linha_dois {
                background-color:#ebebeb;
            }
            .linha_um td, .linha_dois td {
                border-bottom:1px solid #ccc;
            }
        </style>
        <link href="../../novoFinanceiro/style/form.css" rel="stylesheet" type="text/css">
            </head>
            <body>
                <div id="baseCentral">
                    <div id="topo">
                        <?php
                        $query_master = mysql_query("SELECT id_master FROM regioes WHERE id_regiao = '$regiao'");
                        $query_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao'");
                        $id_master = @mysql_result($query_master, 0);
                        ?>
                        <table width="980">
                            <tr>
                                <td width="110" rowspan="3">
                                    <img src="../../imagens/logomaster<?= $id_master ?>.gif" width="110" height="79">
                                </td>
                                <td align="left" valign="top">
                                    <br />
                                    Data:&nbsp;<strong><?= date("d/m/Y"); ?></strong>&nbsp;<br />
                                    voc&ecirc; est&aacute; visualizando a Regi&atilde;o:&nbsp;<strong><?= @mysql_result($query_regiao, 0); ?></strong></td>
                                <td>
                                    <br />

                                    <!--Controle de regiao -->
                                    <div>
                                        <form name="formRegiao" id="formRegiao" method="get">
                                            <table>
                                                <tr>
                                                    <td><span style="color:#000">Regi&atilde;o</span></td>
                                                    <td>
                                                        <!------ Visualizando Regi√µes --------->
                                                        <select name='select_regiao' class='campotexto' id='select_regiao' >                                                                                
                                                            <?php
                                                            //$obj_regiao->Select_permissao_relatorio($regioes); 

                                                            $obj_regiao->Preenhe_select_por_master($id_master, $regiao);
                                                            ?>  
                                                        </select> 
                                                    </td>
                                                </tr>
                                            </table>
                                        </form>
                                    </div>
                                    <!--Controle de regiao -->

                                </td>
                            </tr>          
                        </table>

                        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0"  id="conteudo"> 


                            <tr>
                                <td>&nbsp;</td>
                            </tr>

                            <tr>
                                <td><a href="../relatorios.php?enc=<?php echo $linkEnc; ?>"> <img src="../../img_menu_principal/voltar.png" title="VOLTAR"/> </a></td>
                            </tr>
                            <tr bgcolor="#E8E8E8">
                                <td><div align="left">&nbsp;&nbsp;<span class="style9">&nbsp;<span class="style2"><img src="../../financeiro/imagensfinanceiro/entradas-up-32.png" alt="fornecedor" width="32" height="32" align="absmiddle" /><span class="style31"><img src="../../financeiro/imagensfinanceiro/saida-32.png" alt="fornecedor" width="32" height="32" align="absmiddle" /></span>&nbsp;</span></span><span class="style3"> ENTRADAS E SAI&#769;DAS</span></div></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <!--FOI TIRADO A PEDIDO DA DANIELA, POIS A MESMA INFORMOU QUE N√O USA-->
                                <td height="18" valign="top">
                                    <form action="../../financeiro/relfinanceiro2.php" method="post" name="for5">
                                        <p><span class="style12 style29">&nbsp;</span></p>
                                        <p><span class="style12 style29">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Selecione o Projeto:</span>
                                            <?php
                                            $result_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao' ");
                                            print "<select name='projeto' class='textarea2'>";
                                            while ($row_projeto = mysql_fetch_array($result_projeto)) {
                                                print "<option value=$row_projeto[0]>$row_projeto[0] - $row_projeto[nome] </option>";
                                            }
                                            print "</select>";
                                            ?>
                                            <br>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="relfinanceiro2.php?id=5&regiao=<?php // echo $regiao; ?>&select=1" class="style12 style29" style="TEXT-DECORATION: none;">
                                                    <label>
                                                        Visualizar lan&ccedil;amentos n&atilde;o pagos&nbsp;&nbsp;
                                                        <input type="radio" name="select" id="select" value="1">
                                                    </label>
                                                    <br>
                                                </a>
                                                <br>
                                                    <span class="style12 style29">
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <label>
                                                            Visualizar lan&ccedil;amentos futuros&nbsp;&nbsp;
                                                            <input type="radio" name="select" id="select" value='2'>
                                                        </label>
                                                    </span>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br />
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <input type="submit" class="submit-go" value="Gerar">
                                                        <input type="hidden" value="5" name="id">
                                                            <input type="hidden" value="<?php echo $regiao; ?>" name="regiao">
                                                                <br>
                                                                    &nbsp;&nbsp;&nbsp;</p>
                                                                    </form>
                                                                    </td>
                                                                    <td valign="top"><form action="../../financeiro/relfinanceiro2.php" method="post" name="for4" id="for4">
                                                                            &nbsp;<br />
                                                                            <br />
                                                                            &nbsp;&nbsp;&nbsp;<span class="style12 style29">&nbsp;Selecione a Conta:</span>
                                                                            <?php
                                                                            $result_banco = mysql_query("SELECT * FROM bancos where id_regiao = '$regiao' and interno ='1' AND status_reg = '1'");
                                                                            print "<select name='banco' class='textarea2'>";
                                                                            while ($row_banco = mysql_fetch_array($result_banco)) {

                                                                                //POG PARA BLOQUEAR VIAM√O   
                                                                                if (($_COOKIE['logado'] == 161 or $_COOKIE['logado'] == 180) and $row_banco['id_banco'] == 107)
                                                                                    continue;


                                                                                print "<option value=$row_banco[0]>$row_banco[0] - $row_banco[nome] - $row_banco[agencia] / $row_banco[conta]</option>";
                                                                            }
                                                                            print "</select>";
                                                                            ?>
                                                                            <br />
                                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style12 style29">Ou marque aqui para exibir todas as contas:</span>
                                                                            <input type="checkbox" name="todas_contas" id="todas_contas" value="1" />
                                                                            &nbsp;&nbsp;&nbsp;&nbsp;<br />
                                                                            &nbsp;&nbsp;&nbsp;&nbsp;<br />
                                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style12 style29">Selecione o m&ecirc;s e o ano de refer&ecirc;ncia:&nbsp;&nbsp;&nbsp;
                                                                                <select name="mes" id="mes" class='textarea2'>
                                                                                    <?php
                                                                                    $qr_meses = mysql_query("SELECT * FROM ano_meses");
                                                                                    while ($row_meses = mysql_fetch_assoc($qr_meses)) {
                                                                                        $selected = (date('m') == $row_meses['num_mes']) ? 'selected="selected"' : '';
                                                                                        echo '<option ' . $selected . ' value="' . $row_meses['num_mes'] . '">' . $row_meses['num_mes'] . ' - ' . $row_meses['nome_mes'] . '</option>';
                                                                                    }
                                                                                    ?>
                                                                                </select>
                                                                            </span> <span class="style12 style29"> &nbsp;&nbsp;&nbsp;&nbsp;
                                                                                <?php echo montaSelect($anos, date('Y'), "id='ano' name='ano' class='textarea2'") ?>
                                                                                <!--<select name="ano" id="ano" class='textarea2'>
                                                                                    <?php
                                                                                    for ($i = 2005; $i < 2016; $i++) {
                                                                                        $selected = (date('Y') == $i) ? 'selected="selected"' : '';
                                                                                        echo '<option ' . $selected . ' value="' . $i . '">' . $i . '</option>';
                                                                                    }
                                                                                    ?>
                                                                                </select>-->
                                                                            </span>
                                                                            <label></label>
                                                                            <span class="style12 style29"><br />
                                                                                &nbsp; </span> <span class="style12 style29">&nbsp;&nbsp;</span><span class="style12 style29">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                                                            <input type="submit" class="submit-go" value="Gerar" />
                                                                            <input type="hidden" value="4" name="id" />
                                                                            <input type="hidden" value="<?= $regiao; ?>" name="regiao" />
                                                                            <br />
                                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                        </form></td>
                                                                    </tr>
                                                                    </table>

                                                                    </div>

                                                                    </div>

                                                                    </body>
                                                                    </html>