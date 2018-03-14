<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
} else {

    header("Content-Type:text/html; charset=ISO-8859-1", true);
    include('conn.php');
    include('classes/funcionario.php');
    include('wfunction.php');
    
    $usuario = carregaUsuario();

    ////////////////////////////////////////////////////////////////////////////
    /////////////////////// gravando log de relatorios /////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    if (isset($_REQUEST['par']) && $_REQUEST['par'] == TRUE) {
        $url = explode("?", $_REQUEST['url']);
        $date = date("Y-m-d H:i:s");
        $idUsuario = $_REQUEST['id'];
        $query = "INSERT INTO relatorios_log (nome_arquivo,data_acesso,id_usuario) VALUES ('$url[0]','$date','$idUsuario');";
        echo $query;
        $result = mysql_query($query);
        echo ($result) ? TRUE : FALSE;
    } else {

        $Fun = new funcionario();
        $Fun->MostraUser(0);
        $Master = $Fun->id_master;
        $Id = $Fun->id_funcionario;

        $qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$Id'");
        $funcionario = mysql_fetch_array($qr_funcionario);

        $projeto = $_REQUEST['projeto'];
        $regiao = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
        
        if($regiao == ''){
            $regiao = $usuario['id_regiao'];
        }
        ?>
        <html>
            <head>
                <title>:: Intranet ::</title>
                <link rel='shortcut icon' href='favicon.ico'>
                <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                <link href="rh/css/estrutura_projeto.css" rel="stylesheet" type="text/css">
                <script src="js/jquery-1.10.2.min.js" type="text/javascript"></script>
                <script type="text/javascript">
                    // jquery para os relatórios
                    $(document).ready(function() {
                        $(".link-relatorio").click(function() {
                            var url = $(this).attr('href');
                            var relatorio = $(this).data("id");
                            $.post('methods.php', {url: url, id_relatorio: relatorio, id_usuario:<?= $usuario['id_funcionario'] ?>, method: 'logRelatorio'}, function(data) {
                                if (data === true) {
                                    windows.open(url);
                                }
                            });
                        });
                    });
                </script>
                <style>
                    /* css para relatorios */
                    .listRelatorios{
                        list-style: none;
                    }
                    .listRelatorios li{
                        display: inline-block;
                        padding: 5px;
                    }
                    .tb-relatorios{
                        font-size: 10px;
                        width: 100%;
                        border-collapse: collapse;
                    }
                    .rel-novo{
                        display: inline-block;
                        background-color: #0099ff;
                        color: white;
                        padding: 2px 3px;
                        margin: 2px;
                        border: 1px solid #0088ee;
                        border-radius: 3px;
                        -webkit-border-radius: 3px;
                        -moz-border-radius: 3px;
                    }
                    /* fim css para relatorios */
                </style>
            </head>
            <body>
                <div id="corpo">
                    <script type="text/javascript">
                        function exibe() {
                            if (document.getElementById("localizacao").style.display == "none") {
                                document.getElementById("localizacao").style.display = "block";
                            }
                        }

                        function oculta() {
                            if (document.getElementById("localizacao").style.display == "block") {
                                document.getElementById("localizacao").style.display = "none";
                            }
                        }

                        function getPosicaoElemento() {
                            elemID = "username";
                            var offsetTrail = document.getElementById(elemID);
                            var offsetLeft = 0;
                            var offsetTop = 0;
                            while (offsetTrail) {
                                offsetLeft += offsetTrail.offsetLeft;
                                offsetTop += offsetTrail.offsetTop;
                                offsetTrail = offsetTrail.offsetParent;
                            }
                            if (navigator.userAgent.indexOf("Mac") != -1 &&
                                    typeof document.body.leftMargin != "undefined") {
                                offsetLeft += document.body.leftMargin;
                                offsetTop += document.body.topMargin;
                            }
                            offsetTop = offsetTop + 22;
                            document.all.Flutuante.style.left = offsetLeft + "px";
                            document.all.Flutuante.style.top = offsetTop + "px";
                        }

                        function ajaxFunction() {
                            var xmlHttp;
                            try
                            {
                                xmlHttp = new XMLHttpRequest();
                            }
                            catch (e)
                            {
                                try
                                {
                                    xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
                                }
                                catch (e)
                                {
                                    try
                                    {
                                        xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
                                    }
                                    catch (e)
                                    {
                                        alert("Your browser does not support AJAX!");
                                        return false;
                                    }
                                }
                            }
                            xmlHttp.onreadystatechange = function() {
                                if (document.getElementById('username').value == '') {
                                    document.all.ttdiv.style.display = "none";
                                } else {
                                    document.all.ttdiv.style.display = "";
                                    if (xmlHttp.readyState == 3) {
                                        document.all.spantt.innerHTML = "<div align='center' style='background-color:#5C7E59'><img src='imagens/carregando/CIRCLE_BALL.gif' align='absmiddle'>Aguarde</div>";
                                    } else if (xmlHttp.readyState == 4) {
                                        document.all.spantt.innerHTML = xmlHttp.responseText;
                                    }
                                }
                            }

                            var enviando = escape(document.getElementById('username').value);
                            xmlHttp.open("GET", 'ver.php?procura=' + enviando + '&id=1&projeto=<?= $projeto ?>&regiao=<?= $regiao ?>', true);
                            xmlHttp.send(null);

                        }
                    </script>
                    <?php
                    $qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
                    $row_regiao = mysql_fetch_assoc($qr_regiao);


                    // Tela 1
                    if (empty($projeto)) {
                        ?>

                        <div id="conteudo">
                            <table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
                                <tr>
                                    <td align="right"><?php include('reportar_erro.php'); ?></td>
                                </tr>

                                <tr>
                                    <td>
                                        <div style="border-bottom:2px solid #F3F3F3; margin-top:10px;">
                                            <h2 style="float:left; font-size:18px;text-transform:uppercase;">LOCAIS CADASTRADOS - <span style="color:#008080">(<?php echo $row_regiao['regiao']; ?>)</span> </h2>
                                            <p style="float:right;"><a href="javascript:window.close()">x Fechar</a></p>
                                            <div class="clear"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>

                                        <?php
                                        $qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
                                        $row_func = mysql_fetch_assoc($qr_funcionario);


                                        $status = array(1 => 'Local ativo ', 0 => 'Local inativo');
                                        foreach ($status as $status_reg => $tipo) {


                                            if ($row_func['tipo_usuario'] == 6) { //BLOQUEIO USUÁRIO CADASTRADOR ITABORAÍ
                                                $qr_projetos = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' and status_reg = '$status_reg' AND id_projeto = '3295' ORDER BY status_reg DESC");
                                            } else {

                                                $qr_projetos = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' and status_reg = '$status_reg' ORDER BY status_reg DESC");
                                            }
                                            $verifica_projetos = mysql_num_rows($qr_projetos);

                                            if (!empty($verifica_projetos)) {
                                                ?>


                                                <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="margin:0px auto;">
                                                    <tr>
                                                        <td>
                                                            <?php if ($status_reg == 1) { ?>

                                                                <h1 style="background-color:#C99; font-size:13px;font-weight:bold;color:#FFF;padding:4px 8px;width:180px;margin:20px auto; border:0; text-align:center;"> ATIVO(S)</h1>	

                                                            <?php } else { ?>

                                                                <h1 style="background-color:#C99; font-size:13px;font-weight:bold;color:#FFF;padding:4px 8px;width:180px;margin:20px auto; border:0; text-align:center;"> INATIVO(S)</h1>	

                                                            <?php } ?>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            <?php while ($row = mysql_fetch_array($qr_projetos)) { ?>         

                                                                <a title="Abrir: <?= $row['nome'] ?>" href="ver.php?projeto=<?= $row['id_projeto'] ?>&regiao=<?= $regiao ?>" class="projeto"><div style="color:#F90; float:left; font-size:32px;">&#8250;</div><div style="float:left; margin-left:10px;"><?= $row['id_projeto'] ?> - <?= $row['nome'] ?><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:normal; font-size:13px;"><?= $row['tema'] ?></span></div><div class="clear"></div></a>
                                                            <?php }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                </table>

                                            <?php } else { ?>

                                                <p style="text-align:center;">
                                                    Nenhum <?php echo $tipo; ?> encontrado.
                                                </p>
                                                <?php
                                            }
                                        }//foreach
                                        ?>

                                    </td>
                                </tr>
                            </table>
                        </div>

                        <?php
                        // Tela 2
                    } else {
                        ?>

                        <?php
                        $qr_projetos = mysql_query("SELECT *, date_format(inicio, '%d/%m/%Y') AS data_ini, date_format(termino, '%d/%m/%Y') AS data_fim FROM projeto WHERE id_projeto = '$projeto' AND status_reg IN ('1','0')");
                        $row = mysql_fetch_assoc($qr_projetos);

                        $qr_cooperativas = mysql_query("SELECT * FROM cooperativas WHERE id_regiao = '$regiao' AND status_reg = '1'");
                        $numero_cooperativas = mysql_num_rows($qr_cooperativas);

                        // Participantes Ativos
                        $qr_clt_ativos = mysql_query("SELECT * FROM rh_clt WHERE (status < '60' OR status = '200') AND id_regiao ='$regiao' AND tipo_contratacao = '2' AND id_projeto = '$projeto'");
                        $num_clt_ativos = mysql_num_rows($qr_clt_ativos);
                        $qr_cooperado_ativos = mysql_query("SELECT * FROM autonomo WHERE status = '1' AND tipo_contratacao = '3' AND id_regiao ='$regiao' AND id_projeto = '$projeto'");
                        $num_cooperado_ativos = mysql_num_rows($qr_cooperado_ativos);
                        $qr_autonomo_ativos = mysql_query("SELECT * FROM autonomo WHERE status = '1' AND tipo_contratacao = '1' AND id_regiao ='$regiao' AND id_projeto = '$projeto'");
                        $num_autonomo_ativos = mysql_num_rows($qr_autonomo_ativos);
                        $qr_autonomo_pj_ativos = mysql_query("SELECT * FROM autonomo WHERE status = '1' AND tipo_contratacao = '4' AND id_regiao ='$regiao' AND id_projeto = '$projeto'");
                        $num_autonomo_pj_ativos = mysql_num_rows($qr_autonomo_pj_ativos);

                        // Participantes Inativos
                        $qr_clt_inativos = mysql_query("SELECT * FROM rh_clt WHERE (status >= '60' AND status != '200') AND tipo_contratacao = '2' AND id_regiao = '$regiao' AND id_projeto = '$projeto'");
                        $num_clt_inativos = mysql_num_rows($qr_clt_inativos);
                        $qr_cooperado_inativos = mysql_query("SELECT * FROM autonomo WHERE status != '1' AND tipo_contratacao = '3' AND id_regiao = '$regiao' AND id_projeto = '$projeto'");
                        $num_cooperado_inativos = mysql_num_rows($qr_cooperado_inativos);
                        $qr_autonomo_inativos = mysql_query("SELECT * FROM autonomo WHERE status != '1' AND tipo_contratacao = '1' AND id_regiao = '$regiao' AND id_projeto = '$projeto'");
                        $num_autonomo_inativos = mysql_num_rows($qr_autonomo_inativos);
                        $qr_autonomo_pj_inativos = mysql_query("SELECT * FROM autonomo WHERE status != '1' AND tipo_contratacao ='4' AND id_regiao = '$regiao' AND id_projeto = '$projeto'");
                        $num_autonomo_pj_inativos = mysql_num_rows($qr_autonomo_pj_inativos);

                        // Total de Participantes
                        $total_ativos = $num_clt_ativos + $num_cooperado_ativos + $num_autonomo_ativos + $num_autonomo_pj_ativos;
                        $total_inativos = $num_clt_inativos + $num_cooperado_inativos + $num_autonomo_inativos + $num_autonomo_pj_inativos;
                        $total = $total_ativos + $total_inativos;

                        // Tela para Busca AvanÃ§ada
                        if (empty($_REQUEST['id'])) {
                            ?>
                            <div id="conteudo">   
                                <table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
                                    <tr>
                                        <td align="right"><?php include('reportar_erro.php'); ?></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div style="border-bottom:2px solid #F3F3F3; margin-top:10px;">
                                                <h2 style="float:left; font-size:18px;"><?= $row['id_projeto'] ?> - <?= $row['nome'] ?></h2>
                                                <p style="float:right;"><a href="ver.php?regiao=<?= $regiao ?>">&laquo; Voltar</a></p>
                                                <div class="clear"></div>
                                            </div></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#F3F3F3" style="padding-left:20px;">
                                            <b>Nome:</b> <?= $row['nome'] ?><br>
                                            <b>Tema:</b> <?= $row['tema'] ?><br>
                                            <b>Regi&atilde;o:</b> <?= $row['id_regiao'] . ' - ' . @mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row[id_regiao]'"), 0) ?><br>
                                            <b>&Aacute;rea:</b> <?= $row['area'] ?><br>
                                            <b>Descricao:</b> <?= $row['descricao'] ?><br>
                                            <b>Data de In&iacute;cio:</b> <?= $row['data_ini'] ?><br>
                                            <b>Previs&atilde;o de T&eacute;rmino:</b> <?= $row['data_fim'] ?><br>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><h1><span>EDI&Ccedil;&Atilde;O DE PARTICIPANTES</span></h1></td>
                                    </tr>
                                    <tr>
                                        <td class="menu">
                                            <p><a href="bolsista.php?regiao=<?= $regiao ?>&projeto=<?= $projeto ?>" class="botao" style="font-size:12px;">Visualizar Participantes</a>
                                                <a href="#" class="botao" style="font-size:12px;" onClick="exibe()">Localizar Participante</a>
                                                <a href="folha_ponto.php?regiao=<?= $regiao ?>&pro=<?= $projeto ?>&id=1" class="botao" target="_blank" style="font-size:12px;">Gerar Apontamento</a></p>

                                            <p><?php if ($row['status_reg'] == 1) : ?>
                                                    <a href="cadastro_bolsista.php?regiao=<?= $regiao ?>&pro=<?= $projeto ?>" class="botao">Cadastrar <span class="aut">Aut&oacute;nomo</span></a>		
                                                    <a href="rh/cadastroclt.php?regiao=<?= $regiao ?>&projeto=<?= $projeto ?>" class="botao">Cadastrar <span class="clt">CLT</span></a>
                                                    <a href="cooperativas/cadcooperado.php?regiao=<?= $regiao ?>&pro=<?= $projeto ?>&tipo=3" class="botao">Cadastrar <span class="coo">Cooperado</span></a>
                                                    <a href="cooperativas/cadcooperado.php?regiao=<?= $regiao ?>&pro=<?= $projeto ?>&tipo=4" class="botao" style="font-size:12px;">Cadastrar <span class="aut">Aut&oacute;nomo / PJ</span></a>
                                                    <a href="terceirizado/cadterceiro.php?regiao=<?= $regiao ?>&pro=<?= $projeto ?>&tipo=4" class="botao" style="font-size:12px;">Cadastrar <span class="aut">Terceirizado</span></a>
                                                </p>
                                            <?php endif; ?>   

                                            <div id="localizacao" class="localizacao" style="display:none;">
                                                <a onClick="oculta()" style="float:right; cursor:pointer;">x fechar</a>
                                                <form>
                                                    <input type="text" name="username" value="Insira o nome do participante" onBlur="if (this.value == '') {
                                                                                this.value = 'Insira o nome do participante';
                                                                            }" onFocus="if (this.value == 'Insira o nome do participante') {
                                                                                        this.value = '';
                                                                                    }
                                                                                    ;
                                                                                    getPosicaoElemento();" onKeyUp="ajaxFunction();
                                                                                            getPosicaoElemento();" id="username" style="color:#999; font-style:italic; width:550px; float:left;" /><img src='imagens/carregando/CIRCLE_BALL.gif' style="display:none;">
                                                </form>
                                                <div class="clear"></div>
                                                <div id="Flutuante" style="float:left">
                                                    <table border="0" cellpadding="0" cellspacing="0" id="ttdiv" style="border:solid 1px #000; display:none;" 
                                                           background="imagens/trans.png">
                                                        <tr>
                                                            <td><span style='font-size:13px;' id="spantt"></span></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><h1 style="margin-top:0px;"><span>ESTAT&Iacute;STICA DOS PARTICIPANTES DO LOCAL</span></h1></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            CLTs Ativos: <b><?= $num_clt_ativos ?></b><br>
                                            Aut&oacute;nomos Ativos: <b><?= $num_autonomo_ativos ?></b><br>
                                            Cooperados Ativos: <b><?= $num_cooperado_ativos ?></b><br>
                                            Aut&oacute;nomos / PJ Ativos: <b><?= $num_autonomo_pj_ativos ?></b><br>   
                                            <hr class="clear">
                                            CLTs Inativos: <b><?= $num_clt_inativos ?></b><br>
                                            Aut&oacute;nomos Inativos: <b><?= $num_autonomo_inativos ?></b><br>
                                            Cooperados Inativos: <b><?= $num_cooperado_inativos ?></b><br>
                                            Aut&oacute;nomos / PJ Inativos: <b><?= $num_autonomo_pj_inativos ?></b><br>
                                            <hr>
                                            <div class="left" style="width:50%;">
                                                Total Ativos: <b><?= $total_ativos ?></b><br>
                                                Total Inativos: <b><?= $total_inativos ?></b><br>
                                                Total: <b><?= $total ?></b><br>
                                            </div>
                                            <?php /* <div class="right" style="width:50%;">
                                              <?php if(!empty($total)) { ?>
                                              <div class="left">

                                              </div>
                                              <div class="right" style="margin-right:100px;">
                                              <p>&nbsp;</p>
                                              <p>&nbsp;</p>
                                              <span style="color:#9C3; font-weight:bold;">Total Ativos</span><br>
                                              <span style="color:#C30; font-weight:bold;">Total Inativos</span>
                                              </div>
                                              <div class="clear"></div>
                                              <?php } ?>
                                              </div> */ ?>
                                            <div class="clear"></div>   
                                            <?php
                                            if (!empty($numero_cooperativas)) {
                                                echo "<hr>";
                                                while ($cooperativa = mysql_fetch_assoc($qr_cooperativas)) {
                                                    $qr_coop_ativos = mysql_query("SELECT * FROM autonomo WHERE tipo_contratacao IN ('3','4') AND id_cooperativa = '$cooperativa[id_coop]' AND id_projeto = '$projeto' AND id_regiao = '$regiao' AND status = '1'");
                                                    $numero_coop_ativos = mysql_num_rows($qr_coop_ativos);
                                                    $qr_coop_inativos = mysql_query("SELECT * FROM autonomo WHERE tipo_contratacao IN ('3','4') AND id_cooperativa = '$cooperativa[id_coop]' AND id_projeto = '$projeto' AND id_regiao = '$regiao' AND status != '1'");
                                                    $numero_coop_inativos = mysql_num_rows($qr_coop_inativos);
                                                    $total_coop = $numero_coop_ativos + $numero_coop_inativos;
                                                    if (!empty($total_coop)) {
                                                        echo "$cooperativa[fantasia]: <b>$total_coop</b> <i>($numero_coop_ativos ativos / $numero_coop_inativos inativos)</i><br>";
                                                    }
                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><h1 style="margin-top:0px;"><span>RELAT&Oacute;RIOS DO LOCAL</span></h1></td>
                                    </tr>
                                    <tr>
                                        <td>

                                           <?php require ('relatorios.php'); ?>

                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div id="rodape">
                                <?php
                                $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$Master'");
                                $master = mysql_fetch_assoc($qr_master);
                                ?>
                                <p class="left"><img style="position:relative; top:7px;" src="imagens/logomaster<?= $Master ?>.gif" width="66" height="46"> <b><?= $master['razao'] ?></b>&nbsp;&nbsp;Acesso Restrito &aacute; Funcion&aacute;rios</p>
                                <p class="right"><br><br><a href="#corpo">Subir ao topo</a></p>
                                <div class="clear"></div>
                            </div>


                            <?php
                        } else {

                            $id = $_REQUEST['id'];

                            switch ($id) {
                                case 1:

                                    $recebi = $_REQUEST['procura'];
                                    $pro = $_REQUEST['projeto'];
                                    $reg = $_REQUEST['regiao'];

                                    $qr_busca = mysql_query("SELECT id_autonomo, nome, id_regiao, id_projeto, tipo_contratacao FROM autonomo WHERE status = '1' AND id_regiao = '$reg' AND id_projeto = '$pro' AND nome LIKE '%$recebi%' UNION SELECT id_clt, nome, id_regiao, id_projeto, tipo_contratacao FROM rh_clt WHERE status < '60' AND id_regiao = '$reg' AND id_projeto = '$pro' AND nome LIKE '%$recebi%'");
                                    $total_busca = mysql_num_rows($qr_busca);

                                    if (empty($total_busca)) {
                                        $Devolver = '<a href="#" style="color:#C30; text-decoration:none; display:block; padding:3px; padding-left:5px;">Sua busca n&atilde;o retornou resultado</a>';
                                    } else {
                                        while ($busca = mysql_fetch_array($qr_busca)) {
                                            if ($busca['tipo_contratacao'] == "2") {
                                                $li = "<a class=\"busca\"   
			       href='rh/ver_clt.php?reg=$busca[id_regiao]&clt=$busca[0]&pro=$busca[id_projeto]&pagina=bol'
			       onCLick=\"document.all.ttdiv.style.display='none'; 
		           document.all.username.value='" . $busca['nome'] . "' \">";
                                            } else {
                                                $li = "<a class=\"busca\" 
			       href='ver_bolsista.php?reg=$busca[id_regiao]&bol=$busca[0]&pro=$busca[id_projeto]'
		           onCLick=\"document.all.ttdiv.style.display='none';
		           document.all.username.value='" . $busca['nome'] . "' \">";
                                            }
                                            $Devolver .= "$li" . $busca['nome'] . "</a>";
                                        }
                                    }

                                    echo $Devolver;

                                    break;
                                case 2:
                            }
                        }
                        ?>

                    <?php } ?>
                </div>
            </body>
        </html>
        <?php
    }
}
?>