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

$opt = array("1"=>"ASSEGURADOS ATIVOS", "0"=>"DESATIVADOS E NÃO ASSEGURADO");

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $tipo_status = $_REQUEST['tipo'];

    $str_qr_relatorio = "SELECT A.nome,A.rg, A.cpf, date_format(A.data_nasci, '%d/%m/%Y') as data_nascibr,
        date_format(A.data_entrada, '%d/%m/%Y') AS data_entradabr, date_format(data_saida, '%d/%m/%Y') AS data_saidabr
        FROM autonomo AS A
        INNER JOIN apolice AS B
        ON B.id_apolice = A.apolice
        WHERE A.id_regiao = '$id_regiao' AND A.tipo_contratacao = '1' AND status = '{$tipo_status}' ";
    
    if(!isset($_REQUEST['todos_projetos'])) {
        $str_qr_relatorio .= "AND A.id_projeto = '$id_projeto' ";
    }
    
    $str_qr_relatorio .= "ORDER BY A.nome";
    
    $qr_relatorio = mysql_query($str_qr_relatorio) or die(mysql_error());
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$optSel = (isset($_REQUEST['tipo'])) ? $_REQUEST['tipo'] : null;

?>
<html>
    <head>
        <title>:: Intranet :: Relatório de Assegurados</title>
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
                        <h2>Relatório de Assegurados</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>


                <fieldset class="noprint">
                    <legend>Relatório</legend>
                    <div class="fleft">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        <input type="hidden" name="hide_tipo" id="hide_tipo" value="<?php echo $optSel ?>" />
                        <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?> </p>                        
                        <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?> </p>
                        <p><label class="first">Status:</label> <?php echo montaSelect($opt, $optSel, array('name' => "tipo", 'id' => 'tipo')); ?> </p>
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
                                <th>NOME</th>
                                <th>IDENTIDADE</th>
                                <th>CPF</th>
                                <th>DATA NASCIMENTO</th>
                                <th>DATA ENTRADA</th>
                                <th>DATA SAIDA</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"; ?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo $row_rel['nome'] ?></td>
                                <td> <?php echo $row_rel['rg']; ?></td>
                                <td> <?php echo $row_rel['cpf']; ?></td>
                                <td> <?php echo $row_rel['data_nascibr']; ?></td>
                                <td> <?php echo $row_rel['data_entradabr']; ?></td>
                                <td> <?php echo $row_rel['data_saidabr']; ?></td>
                            </tr>                                
                        <?php } ?>
                    </tbody>
                </table>
                <?php  } ?>
            </form>
        </div>
    </body>
</html>