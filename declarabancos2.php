<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a>";
    exit;
} else {

    include "conn.php";
    include "classes/regiao.php";
    include('wfunction.php');

    $usuario = carregaUsuario();

    $id_user = $_COOKIE['logado'];
    $result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
    $row_user = mysql_fetch_array($result_user);
    $result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
    $row_master = mysql_fetch_array($result_master);

    $tela = (isset($_REQUEST['tela'])) ? $_REQUEST['tela'] : 0;
    $regiao = (isset($_REQUEST['reg'])) ? $_REQUEST['reg'] : $usuario['id_regiao'];
    $projeto = (isset($_REQUEST['pro'])) ? $_REQUEST['pro'] : null;
    $tipo = $_REQUEST['tipo'];
    $banco = $_REQUEST['banco'];

    
//------------------------cria array com projetos-------------------------------
    $projetosOp = array("-1" => "« Selecione »");
    $query = "SELECT id_projeto,nome FROM projeto WHERE id_regiao = '$regiao'";
    $result = mysql_query($query) or die(mysql_error());
    while ($row = mysql_fetch_assoc($result)) {
        $i = $_SERVER['PHP_SELF'] . "?pro=" . $row['id_projeto'] . "&tela=1";
        $projetosOp[$i] = $row['id_projeto'] . " - " . $row['nome'];
    }
//------------------------------------------------------------------------------
// Consulta de Bancos
    switch ($tela) {
        case 2:

            $qr_bancos = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$regiao' AND id_projeto = '$projeto' ORDER BY nome ASC");
            $verifica_banco = mysql_num_rows($qr_bancos);

            break;
        case 3:

            $qr_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$banco'");
            $row_banco = mysql_fetch_array($qr_banco);

            break;
    }

// Consulta de Participantes
    if ($tipo != "2") {
        $qr_participantes = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada FROM autonomo WHERE id_projeto = '$projeto' AND id_regiao = '$regiao' AND status = '1' AND tipo_contratacao = '$tipo' ORDER BY nome");
    } else {
        $qr_participantes = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada FROM rh_clt WHERE id_projeto = '$projeto' AND id_regiao = '$regiao' AND status < '60' AND tipo_contratacao = '$tipo' ORDER BY nome");
    }
    $verifica = mysql_num_rows($qr_participantes);
    ?>
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
            <title>:: Intranet ::</title>
            <link href="relatorios/css/estrutura.css" rel="stylesheet" type="text/css">
        </head>
        <?php
        switch ($tela) {
            case 0:
                ?>
                <body>
                    <div id="corpo">
                        <div id="topo">
                            <img src='imagens/logomaster<?= $row_user['id_master'] ?>.gif' alt="" width='120' height='86' />
                            <br><br><?= $row_master['razao'] ?>
                            <br><span style="color:#363;">CNPJ:</span> <?= $row_master['cnpj'] ?>
                        </div>
                        <div id="conteudo">
                            <h1 style="margin:70px;"><span>RELATÓRIOS</span> ENCAMINHAMENTO DE CONTA</h1>
                            <form style="margin-bottom:190px;">
                                <p><label class="first">Projeto:</label> <?php echo montaSelect($projetosOp, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'onChange' => "location.href = this.value;")); ?> </p>
                            </form>
                        </div>
                        <div id="rodape"></div>
                    </div>
                    <?php
                    break;
                case 1:
                    ?>
                <body>
                    <div id="corpo">
                        <div id="topo">
                            <img src='imagens/logomaster<?= $row_user['id_master'] ?>.gif' alt="" width='120' height='86' />
                            <br><br><?= $row_master['razao'] ?>
                            <br><span style="color:#363;">CNPJ:</span> <?= $row_master['cnpj'] ?>
                        </div>
                        <div id="conteudo">
                            <h1 style="margin:70px;"><span>RELATÓRIOS</span> ENCAMINHAMENTO DE CONTA</h1>
                            <form style="margin-bottom:190px;">
                                Selecione o Tipo de Contratação:
                                <select onChange="location.href = this.value;" class="campotexto">
                                    <option disabled="disabled" selected>Escolha um tipo abaixo</option>
                                    <option value="<?= $_SERVER['PHP_SELF'] ?>?reg=<?= $regiao ?>&pro=<?= $projeto ?>&tipo=1&tela=2">Autônomo</option>
                                    <option value="<?= $_SERVER['PHP_SELF'] ?>?reg=<?= $regiao ?>&pro=<?= $projeto ?>&tipo=2&tela=2">CLT</option>
                                    <option value="<?= $_SERVER['PHP_SELF'] ?>?reg=<?= $regiao ?>&pro=<?= $projeto ?>&tipo=3&tela=2">Cooperativado</option>
                                    <option value="<?= $_SERVER['PHP_SELF'] ?>?reg=<?= $regiao ?>&pro=<?= $projeto ?>&tipo=4&tela=2">Autônomo / PJ</option>  
                                </select>
                            </form>
                        </div>
                        <div id="rodape"></div>
                    </div>   

                    <?php
                    break;
                case 2:
                    ?>
                <body>
                    <div id="corpo">
                        <div id="topo">
                            <img src='imagens/logomaster<?= $row_user['id_master'] ?>.gif' alt="" width='120' height='86' />
                            <br><br><?= $row_master['razao'] ?>
                            <br><span style="color:#363;">CNPJ:</span> <?= $row_master['cnpj'] ?>
                        </div>
                        <div id="conteudo">
                            <h1 style="margin:70px;"><span>RELATÓRIOS</span> ENCAMINHAMENTO DE CONTA</h1>
                            <?php if (empty($verifica)) { ?>
                                <META HTTP-EQUIV=Refresh CONTENT="1; URL=<?= $_SERVER['PHP_SELF'] ?>?reg=<?= $regiao ?>&pro=<?= $projeto ?>&tela=1">
                                <span style="color:#C30; font-size:13px; font-weight:bold;">Nenhum Participante</span>
                            <?php } elseif (empty($verifica_banco)) { ?>
                                <META HTTP-EQUIV=Refresh CONTENT="1; URL=<?= $_SERVER['PHP_SELF'] ?>?reg=<?= $regiao ?>&pro=<?= $projeto ?>&tela=1">
                                <span style="color:#C30; font-size:13px; font-weight:bold;">Nenhum Banco</span>
                            <?php } else { ?>
                                <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" name="form" style="margin-bottom:200px;">
                                    Selecione o Banco:
                                    <select name="banco" class="campotexto">
                                        <option disabled="disabled" selected>Escolha um banco abaixo</option>
                                        <?php while ($row_banco = mysql_fetch_assoc($qr_bancos)) { ?>
                                            <option value="<?= $row_banco['id_banco'] ?>"><?= $row_banco['nome'] ?></option> 
                                        <?php } ?>
                                    </select>
                                    <input type="submit" class="botao" value="Gerar Encaminhamento">
                                    <input type="hidden" name="pro" value="<?= $projeto ?>">
                                    <input type="hidden" name="reg" value="<?= $regiao ?>">
                                    <input type="hidden" name="tipo" value="<?= $tipo ?>">
                                    <input type="hidden" name="tela" value="3">
                                </form>
                            <?php } ?>
                        </div>
                        <div id="rodape"></div>
                    </div>

                    <?php
                    break;
                case 3:
                    ?>   

                <body>
                    <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0" style="font-family:Arial; font-size:13px;">
                        <tr>
                            <td align="center">
                                <br>
                                <?php
                                while ($participante = mysql_fetch_array($qr_participantes)) {
                                    $qr_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$participante[id_curso]'");
                                    $row_curso = mysql_fetch_array($qr_curso);
                                    ?>
                                    <table width="750" border="0" cellpadding="0" cellspacing="0" style="page-break-after:always; background-color:#FFF;">
                                        <tr align="center">
                                            <td width="20" rowspan="2">&nbsp;</td>
                                            <td align="left">
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td align="center">
                                                            <div style="font-size:12px; text-align:center;">

                                                                <?php
                                                                if ($participante['tipo_contratacao'] == "3") {
                                                                    $qr_coop = mysql_query("SELECT * FROM cooperativas WHERE id_coop = '$participante[id_cooperativa]'");
                                                                    $row_coop = mysql_fetch_array($qr_coop);
                                                                    ?>
                                                                    <img src='cooperativas/logos/coop_<?= $row_coop['id_coop'] ?>.jpg' width='110' height='79'/><br><?= $row_coop['nome'] ?>
                                                                <?php } else { ?>
                                                                    <img src='imagens/logomaster<?= $row_user['id_master'] ?>.gif' alt="" width='120' height='86' />
                                                                    <br><br><?= $row_master['razao'] ?>				
                                                                <?php } ?>                  
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <p>&nbsp;</p>
                                                <blockquote style="font-size:15px; line-height:22px;">
                                                    <h1>AO
                                                        <p>&nbsp;</p>
                                                        <b><i><?= $row_banco['razao'] ?></i></b>
                                                    </h1>
                                                    <p>&nbsp;</p>
                                                    <h1>DECLARA&Ccedil;&Atilde;O&nbsp;</h1>
                                                    <p>Pela  presente, relacionamos os nossos colaboradores, para abertura de conta sal&aacute;rio nesse estabelecimento banc&aacute;rio, a saber:</p>
                                                    <p>&nbsp;</p>
                                                    <p><b>Nome:</b> <span style="color:#C30;"><?php echo $participante['nome']; ?></span></p>
                                                    <p><b>CPF:</b> <?php echo $participante['cpf']; ?></p>
                                                    <p><b>Endereço:</b> <?php echo $participante['endereco']; ?>, <?php echo $participante['bairro']; ?> - <?php echo $participante['cidade']; ?></p>
                                                    <p><b>Renda Mensal:</b> <span style="color:#C30;">R$ <?php echo number_format($row_curso['salario'], 2, ",", "."); ?></span></p>
                                                    <p><b>Curso:</b> <?php echo $row_curso['nome']; ?></p>
                                                    <p><b>Data In&iacute;cio:</b> <?php echo $participante['data_entrada']; ?></p>
                                                </blockquote>
                                                <div align="center">
                                                    <p style="margin-bottom:70px;">&nbsp;</p>
                                                    <?php
                                                    $data = date('d/m/Y');
                                                    $ClassReg = new regiao();

                                                    echo $ClassReg->RegiaoLogado();
                                                    echo " ";
                                                    echo $ClassReg->MostraDataCompleta($data);
                                                    ?>
                                                    <p>&nbsp;</p>
                                                    <p>_____________________________________<br>
                                                        <span style="font-size:12px;">
                                                            <?php
                                                            if ($participante['tipo_contratacao'] == "3") {
                                                                echo "$row_coop[nome]";
                                                            } else {
                                                                echo "$row_master[razao]";
                                                            }
                                                            ?>
                                                        </span></p>
                                                    <p style="margin-bottom:100px;">&nbsp;</p>
                                                    <span style="font-size:12px;">
                                                        <?php if ($participante['tipo_contratacao'] == "3") { ?>
                                                            <?= $row_coop['nome'] ?> <br> <?= $row_coop['endereco'] ?> - <?= $row_coop['tel'] ?>
                                                            <br><span style="color:#363;">CNPJ:</span> <?= $row_coop['cnpj'] ?>
                                                        <?php } else { ?>   
                                                            <?= $row_master['razao'] ?> <br> <?= $row_master['endereco'] ?> - <?= $row_master['telefone'] ?>
                                                            <br>CNPJ: <?= $row_master['cnpj'] ?>				
                                                        <?php } ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td width="20" rowspan="2">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </table>
                                    <h1>&nbsp;</h1>
                                <?php } ?>
                                <div id="navegacao">
                                    <table border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="printButtons">
                                        <tr>
                                            <td width="258" align="center">Mostrando de <?= $ini ?> a <?= $fim ?> de <?= $num_total ?></td>
                                        </tr>
                                        <tr>
                                            <td align="center">
                                                <?php
                                                if ($fim == $num_total) {
                                                    $botao = "<a href='declarabancos2.php?ini=$voltar'>[Voltar]</a>";
                                                } elseif ($ini == 0) {
                                                    $botao = "<a href='declarabancos2.php?ini=$proxima'>[Proxima]</a>";
                                                } else {
                                                    $botao = "<a href='declarabancos2.php?ini=$voltar'>[Voltar]</a>&nbsp;&nbsp;&nbsp;&nbsp;
		<a href='declarabancos2.php?ini=$proxima'>[Proxima]</a>";
                                                }
                                                echo $botao;
                                                ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <?php
                    break;
            }
        }
        ?>
    </body>
</html>