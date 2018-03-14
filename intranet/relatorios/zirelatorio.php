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

function formataCbo($cbo) {
    $cbo = RemoveEspacos(RemoveCaracteresGeral($cbo));
    return sprintf("%07s", substr($cbo, 0, 3) . '-' . substr($cbo, 4, 5));
}

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;
    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];

    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
    
        
        $str_qr_relatorio = "SELECT A.matricula, A.locacao, A.nome, A.cpf, G.area_nome, B.nome AS nome_curso, 
                            DATE_FORMAT(A.data_nasci, '%d/%m/%Y') AS data_nascibr, 
                            DATE_FORMAT(A.data_entrada, '%d/%m/%Y') AS data_entradabr, 
                            DATE_FORMAT(A.data_exame, '%d/%m/%Y') AS data_examebr,
                            A.sexo, C.cod AS cbo, 
                            if(A.`status` = 10, 'N','S') AS afastado, D.especifica,
                            A.pis, A.campo1 AS numero_ctps, A.serie_ctps, A.uf_ctps, 
                            if(A.deficiencia = '', 'NA', if(A.deficiencia = 6, 'BR', 'PDH')) AS brPdh, 
                            if(F.folga >= 5,F.nome,'NA') AS regRevezamento, F.folga,
                            A.rg, A.email, E.nome AS nomeDef
                            FROM rh_clt AS A
                            LEFT JOIN curso AS B ON (B.id_curso = A.id_curso)
                            LEFT JOIN rh_cbo AS C ON (C.id_cbo = B.cbo_codigo)
                            LEFT JOIN rhstatus AS D ON (D.codigo = A.`status`)
                            LEFT JOIN deficiencias AS E ON (E.id = A.deficiencia)
                            LEFT JOIN rh_horarios AS F ON (F.funcao = A.id_curso)
                            LEFT JOIN areas AS G ON (G.area_id = B.area_funcao)
                            WHERE A.id_regiao = '$id_regiao' AND A.tipo_contratacao = '2' AND (D.codigo < '60' OR (D.codigo > '66' AND D.codigo <> '81' AND D.codigo <> '101')) ";
         
    if(!isset($_REQUEST['todos_projetos'])) {
        $str_qr_relatorio .= "AND A.id_projeto = '$id_projeto' ";
    }
    
    $str_qr_relatorio .= "ORDER BY A.nome";
    
    $qr_relatorio = mysql_query($str_qr_relatorio) or die(mysql_error());
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;


?>
<html>
    <head>
        <title>:: Intranet :: Planilha de Funcionários</title>
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
                        <h2>Planilha de Funcionarios</h2>
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
                                <th>MATRICULA</th>
                                <th>EMPRESA/FILIAL</th>
                                <th>FUNCIOÁRIO</th>
                                <th>CPF</th>
                                <th>SETOR</th>
                                <th>FUNÇÃO</th>
                                <th>DATA DE NASCIMENTO</th>
                                <th>DATA DE ADMISSÃO</th>
                                <th>DATA DO ÚLTIMO EXAME</th>
                                <th>SEXO</th>
                                <th>CBO</th>
                                <th>AFASTADO?</th>
                                <th>NIT(PIS/PASEP)</th>
                                <th>CTPS/SÉRIE</th>
                                <th>BR/PDH</th>
                                <th>REGIME DE REVEZAMENTO</th>
                                <th>IDENTIDADE</th>
                                <th>EMAIL</th>
                                <th>DEFICIÊNCIA?</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"; ?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo RemoveCaracteresGeral($row_rel['matricula']); ?></td>
                                <td><?php echo $row_rel['locacao'] ?></td>
                                <td><?php echo RemoveCaracteresGeral(RemoveAcentos($row_rel['nome'])) ?></td>
                                <td><?php echo RemoveCaracteresGeral($row_rel['cpf']); ?></td>
                                <td><?php echo $row_rel['area_nome']; ?></td>
                                <td><?php echo $row_rel['nome_curso']; ?></td>
                                <td><?php echo $row_rel['data_nascibr']; ?></td>
                                <td><?php echo $row_rel['data_entradabr']; ?></td>
                                <td><?php echo $row_rel['data_examebr']; ?></td>
                                <td><?php echo $row_rel['sexo']; ?></td>
                                <td><?php echo formataCbo($row_rel['cbo']); ?></td>
                                <td><?php echo $row_rel['afastado']; ?></td>  
                                <td><?php echo $row_rel['pis']; ?></td>
                                <td><?php echo $row_rel['numero_ctps'].'/'.$row_rel['serie_ctps'].'-'.$row_rel['uf_ctps']; ?></td>
                                <td><?php echo $row_rel['brPdh']; ?></td>
                                <td><?php echo $row_rel['regRevezamento']; ?></td>
                                <td><?php echo $row_rel['rg']; ?></td>
                                <td><?php echo $row_rel['email']; ?></td>
                                <td><?php echo $row_rel['nomeDef']; ?></td>
                            </tr>                                
                        <?php } ?>
                    </tbody>
                </table>
                <?php  } ?>
            </form>
        </div>
    </body>
</html>