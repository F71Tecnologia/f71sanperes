<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$ACOES = new Acoes();

$opt = array("2"=>"CLT","1"=>"Autônomo","3"=>"Cooperado","4"=>"Autônomo/PJ");
$optBanco = array("1"=>"COM BANCO", "2"=>"SEM BANCO", "3"=>"OUTRO BANCO");

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $tipo_contratacao = $_REQUEST['tipo'];
    $tipo_banco = $_REQUEST['banco'];
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
    $contratacao = ($tipo_contratacao == "2")? "clt" : "autonomo";
    
    if($tipo_contratacao == 2) {
        $str_qr_relatorio = "SELECT A.id_curso, A.nome, A.campo3, A.locacao, A.cpf, A.rg,
                            A.tipo_conta, A.agencia, A.conta, B.tipopg, B.campo1,
                            C.id_banco, C.nome AS nome_banco, C.id_nacional, C.agencia AS banco_agencia, C.conta AS banco_conta
                            FROM rh_clt AS A
                            LEFT JOIN tipopg AS B
                            ON B.id_tipopg = A.tipo_pagamento
                            LEFT JOIN bancos AS C
                            ON C.id_banco = A.banco
                            WHERE A.status < '60'
                            AND A.id_regiao = '$id_regiao' ";
    }
    else {
        $str_qr_relatorio = "SELECT A.id_curso, A.nome, A.campo3, A.locacao, A.cpf, A.rg,
                            A.tipo_conta, A.agencia, A.conta, B.tipopg, B.campo1,
                            C.id_banco, C.nome AS nome_banco, C.id_nacional, C.agencia AS banco_agencia, C.conta AS banco_conta
                            FROM autonomo AS A
                            LEFT JOIN tipopg AS B
                            ON B.id_tipopg = A.tipo_pagamento
                            LEFT JOIN bancos AS C
                            ON C.id_banco = A.banco
                            WHERE A.status = '1'
                            AND A.id_regiao = '$id_regiao'
                            AND A.tipo_contratacao = '$tipo_contratacao' ";
    }
    if(!isset($_REQUEST['todos_projetos'])) {
        $str_qr_relatorio .= "AND A.id_projeto = '$id_projeto' ";
    }
    if($tipo_banco == "2") {
        $str_qr_relatorio .= "AND (A.banco = '' OR A.banco = '0') ";
    } else if($tipo_banco == "3") {
        $str_qr_relatorio .= "AND A.banco = '9999' ";
    }
    
    $str_qr_relatorio .= "ORDER BY A.nome";
    
    $qr_relatorio = mysql_query($str_qr_relatorio) or die(mysql_error());
    
    $row_banco = mysql_fetch_assoc($qr_relatorio);
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$optSel = (isset($_REQUEST['tipo'])) ? $_REQUEST['tipo'] : null;
$bancoSel = (isset($_REQUEST['banco'])) ? $_REQUEST['banco'] : null;
?>
<html>
    <head>
        <title>:: Intranet :: Relatório de Pagamentos por Banco</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>

        <script>
            $(function() {
                $(".bt-image").on("click", function() {
                    var id = $(this).data("id");
                    var contratacao = $(this).data("contratacao");
                    var nome = $(this).parents("tr").find("td:first").html();
                    thickBoxIframe(nome, "relatorio_documentos_new.php", {id: id, contratacao: contratacao, method: "getList"}, "625-not", "500");
                });
            });
            $(function() {
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
            });
        </script>
        <style>
            .colEsq{
                width: auto;
                min-height: 0px;
                border-right: 0px;
                margin-right: 0px;
                float: none;
            }
            .bt-image{
                width: 18px;
                cursor: pointer;
            }
            h3 {text-align: center;}
        </style>
    </head>
    <body class="novaintra" >        
        <div id="content">
            <form  name="form" action="" method="post" id="form">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Relatório de Pagamentos por Banco</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>


                <fieldset class="noprint">
                    <legend>Relatório</legend>
                    <div class="fleft">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?> </p>                        
                        <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?> </p>
                        <p><label class="first">Tipo Contratação:</label> <?php echo montaSelect($opt, $optSel, array('name' => "tipo", 'id' => 'tipo')); ?> </p>
                        <p><label class="first">Tipo Contratação:</label> <?php echo montaSelect($optBanco, $bancoSel, array('name' => "banco", 'id' => 'banco')); ?> </p>
                    </div>

                    <br class="clear"/>

                    <p class="controls" style="margin-top: 10px;">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                       if($ACOES->verifica_permissoes(85)) { ?>
                            <input type="submit" name="todos_projetos" value="Gerar de Todos Projetos" id="todos_projetos"/>
                      <?php } ?>
                            
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>

                <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])){ ?>
                <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel"></p>    
                <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
                        <thead>
                            <tr>
                                <th>COD.</th>
                                <th>NOME</th>
                                <th>LOCAÇÃO</th>
                                <th>CPF</th>
                                <th>RG</th>
                                <th>TIPO DE CONTA</th>
                                <th>AGÊNCIA</th>
                                <th>CONTA</th>
                                <th>SALÁRIO</th>
                                <th>FORMA PAGAMENTO</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"; ?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo $row_rel['campo3'] ?></td>
                                <td> <?php echo $row_rel['nome']; ?></td>
                                <td> <?php echo $row_rel['locacao']; ?></td>
                                <td> <?php echo $row_rel['cpf']; ?></td>
                                <td> <?php echo $row_rel['rg']; ?></td>
                                <td> <?php echo $row_rel['tipo_conta']; ?></td>
                                <td> <?php echo $row_rel['agencia']; ?></td>
                                <td> <?php echo $row_rel['conta']; ?></td>
                                <td align="center"><?php echo number_format($row_rel['salario'],2,',','.'); ?></td>
                                <td> <?php echo $row_rel['tipopg']; ?></td>
                            </tr>                                
                        <?php } ?>
                    </tbody>
                </table>
                <?php  } ?>
            </form>
        </div>
    </body>
</html>