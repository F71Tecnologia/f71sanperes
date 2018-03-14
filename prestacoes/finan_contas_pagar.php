<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../funcoes.php');
include('../wfunction.php');

$usuarioW = carregaUsuario();

$regiao = $usuarioW['id_regiao'];
$master = $usuarioW['id_master'];
$usuario = $usuarioW['id_funcionario'];
$id_regiao = 0;

$result = null;

if(isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])){
    $projeto = $_REQUEST['projeto'];
    $ano = $_REQUEST['ano'];
    $ano_maisum = $ano + 1;
    $sql = "SELECT A.id_saida,A.nome,A.especifica,A.valor,A.data_vencimento,A.data_pg,A.status,A.id_prestador,B.c_razao,
                   DATE_FORMAT(A.data_vencimento, '%d/%m/%Y') as data_vencimentoBR,
                   DATE_FORMAT(A.data_pg, '%d/%m/%Y') as data_pgBR 
                   FROM saida AS A
                   LEFT JOIN prestadorservico AS B ON (A.id_prestador = B.id_prestador)
            WHERE 
                YEAR(A.data_vencimento) = {$ano} AND 
                (YEAR(A.data_pg) = {$ano_maisum} OR A.status = 1) 
                AND A.id_projeto = {$projeto}
                AND A.id_banco > 0 AND A.status IN (1,2)";
    
    $result = mysql_query($sql);
}

$arrStatusSaida = array('1'=>"Aguardando pagamento", '2'=>"Paga");
$arProjetos = getProjetos($regiao);
//$meses = mesesArray(null);
$anos = anosArray(null, null);

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$attrPro = array("id" => "projeto", "name" => "projeto", "class" => "validate[custom[select]]");
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
//$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m') - 1;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$erros = 0;
$idsErros = array();
?>
<html>
    <head>
        <title>:: Intranet :: Contas a Pagar</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>

        <script src="../js/global.js" type="text/javascript"></script>

        <script>
            $(function() {
                $("#form1").validationEngine();
            });
        </script>

        <style>
            @media print
            {
                fieldset{display: none;}
                .h2page{display: none;}
                .grAdm{display: none;}
                #message-box{display: none;}
                input{display: none;}
            }
            @media screen
            {
                #headerPrint{display: none;}
            }
        </style>
    </head>
    <body id="page-despesas" class="novaintra">
        <div id="content">
            <form action="" method="post" name="form1" id="form1">
                <input type="hidden" name="bancSel" id="bancSel" value="<?php echo $bancoR ?>" />
                <h2>CONTAS A PAGAR</h2>

                <fieldset>
                    <legend>Dados</legend>
                    <p><label class="first">Projeto:</label> <?php echo montaSelect($arProjetos, $projetoR, $attrPro) ?></p>
                    <!--p><label class="first">Mês:</label> <?php /*echo montaSelect($meses, $mesR, "id='mes' name='mes' class='validate[custom[select]]'")*/ ?></p-->
                    <p><label class="first">Ano:</label> <?php echo montaSelect($anos, $anoR, "id='ano' name='ano' class='validate[custom[select]]'") ?></p>

                    <p class="controls"> <input type="submit" class="button" value="Filtrar" name="filtrar" /> </p>
                </fieldset>

                    <?php if (!empty($result) && mysql_num_rows($result) > 0) { ?>
                    <br/>                    
                    <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Equipe')" value="Exportar para Excel" class="exportarExcel"></p>    
                    <br/>
                    <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" width="100%" class="grid">
                        <thead>
                            <tr>
                                <th>CÓDIGO</th>
                                <th>ESPECIFICAÇÃO</th>
                                <th>PRESTADOR</th>
                                <th>VALOR</th>
                                <th>DATA DE VENCIMENTO</th>
                                <th>DATA DE PAGAMENTO</th>
                                <th>STATUS</th> 
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysql_fetch_assoc($result)) { ?> 
                                <tr>
                                    <td><?php echo $row['id_saida']; ?></td>
                                    <td><?php echo ($row['especifica'] == "")? $row['nome'] : $row['especifica']; ?></td>
                                    <td><?php echo ($row['c_razao'] == "")? "-" : $row['c_razao']; ?></td>
                                    <td><?php echo $row['valor']; ?></td>
                                    
                                    <td><?php echo $row['data_vencimentoBR']; ?></td>
                                    <td><?php echo $row['data_pgBR']; ?></td>
                                    <td><?php echo $arrStatusSaida[$row['status']]; ?></td>
                                </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php } else { ?>
                        <?php if ($projetoR !== null) { ?>
                        <br/>
                        <div id='message-box' class='message-green'>
                            <p>Nenhum registro encontrado</p>
                        </div>
                    <?php } ?>
                <?php } ?>
            </form>
        </div>
    </body>
</html>