<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}
 
include("../conn.php");
include("../classes/funcionario.php");
include("../wfunction.php");
include('../classes/global.php');
include '../classes_permissoes/regioes.class.php';
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$ACOES = new Acoes();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$opt = array("2" => "CLT", "1" => "Autônomo", "3" => "Cooperado", "4" => "Autônomo/PJ");

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;
    $arrayStatus = array(10, 40, 50, 51, 52);
    $status = implode(",", $arrayStatus);

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $tipo_contratacao = $_REQUEST['tipo'];
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));
    $contratacao = ($tipo_contratacao == "2") ? "clt" : "autonomo";
    $optSelStatus = $_REQUEST['status'];
    
    if($_COOKIE['debug'] == 666){
        echo $optSelStatus;
    }
    
    $auxStatus = ($optSelStatus == 1) ? " AND (A.status < 60 OR A.status = 200 OR A.status = 70) " : " AND (A.status >= 60 AND A.status < 70) ";
    
    if ($tipo_contratacao == 2) {

        $str_qr_relatorio = "SELECT *, A.nome, A.endereco enderecoClt, B.nome AS nome_curso, B.salario,
            A.cpf, A.rg, A.pis, C.nome AS nome_banco, A.agencia, A.conta,
            date_format(A.data_demi, '%d/%m/%Y')as data_saidabr,
            date_format(A.data_nasci, '%d/%m/%Y')as data_nascibr,
            date_format(A.data_escola, '%d/%m/%Y')as data_escolabr,
            D.nome as nome_etnia,
            date_format(A.data_ctps, '%d/%m/%Y')as data_ctpsbr,
            date_format(A.dada_pis, '%d/%m/%Y')as data_pisbr,
            date_format(A.data_rg, '%d/%m/%Y')as data_rgbr,
            date_format(A.data_entrada, '%d/%m/%Y')as data_entradabr,
            date_format(A.data_exame, '%d/%m/%Y')as data_examebr,
            A.campo1 as numero_ctps
            FROM rh_clt AS A
            LEFT JOIN curso AS B
            ON B.id_curso = A.id_curso
            LEFT JOIN bancos AS C
            ON C.id_banco = A.banco
            LEFT JOIN etnias AS D
            ON D.id = A.etnia
            WHERE A.id_regiao = '$id_regiao' AND A.tipo_contratacao = '$tipo_contratacao' $auxStatus";
    } else {
        $str_qr_relatorio = "SELECT *, A.nome, B.nome AS nome_curso, B.salario,
            A.cpf, A.rg, A.pis, C.nome AS nome_banco, A.agencia, A.conta,
            date_format(A.data_saida, '%d/%m/%Y')as data_saidabr,
            date_format(A.data_nasci, '%d/%m/%Y')as data_nascibr,
            date_format(A.data_escola, '%d/%m/%Y')as data_escolabr,
            D.nome as nome_etnia,
            date_format(A.data_ctps, '%d/%m/%Y')as data_ctpsbr,
            date_format(A.dada_pis, '%d/%m/%Y')as data_pisbr,
            date_format(A.data_rg, '%d/%m/%Y')as data_rgbr,
            date_format(A.data_entrada, '%d/%m/%Y')as data_entradabr,
            date_format(A.data_exame, '%d/%m/%Y')as data_examebr
            FROM autonomo AS A
            INNER JOIN curso AS B
            ON B.id_curso = A.id_curso 
            LEFT JOIN bancos AS C
            ON C.id_banco = A.banco
            LEFT JOIN etnias AS D
            ON D.id = A.etnia
            WHERE A.id_regiao = '$id_regiao' AND A.tipo_contratacao = '$tipo_contratacao' AND A.status != '1'";
    }
    if (!isset($_REQUEST['todos_projetos'])) {
        $str_qr_relatorio .= "AND A.id_projeto = '$id_projeto' ";
    }

    $str_qr_relatorio .= "ORDER BY A.nome";

    $qr_relatorio = mysql_query($str_qr_relatorio) or die(mysql_error());
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$optSel = (isset($_REQUEST['tipo'])) ? $_REQUEST['tipo'] : null;
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Participantes por Datas de Entrada e Saida</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">

    </head>
    <body>
<?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Participantes do Projeto em Ordem Alfabética </h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                        <div class="panel-body">
                            <div class="form-group" >

                                <label for="select" class="col-sm-2 control-label hidden-print" >Região</label>
                                <div class="col-sm-5">
                                    <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao' , 'class' => 'form-control')); ?><span class="loader"></span>
                                </div>

                                <label for="select" class="col-sm-1 control-label hidden-print" >Projeto</label>
                                <div class="col-sm-3">
                                    <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?> <span class="loader"></span> 
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="select" class="col-sm-2 control-label hidden-print" >Tipo de Contratação</label>
                                <div class="col-sm-2">
                                    <?php echo montaSelect($opt, $optSel, array('name' => "tipo", 'id' => 'tipo' , 'class' => 'form-control')); ?><span class="loader"></span>
                                </div>

                                <label for="select" class="col-sm-1 control-label hidden-print" >Status</label>
                                <div class="col-sm-2">
                                    <?php echo montaSelect(array('1'=> 'Ativo', '0' => 'Inativo'), $_REQUEST['status'], array('name' => "status", 'id' => 'status' , 'class' => 'form-control')); ?><span class="loader"></span>
                                </div>
                            </div>
                        </div>

                        <div class="panel-footer text-right hidden-print controls">
                            <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                            <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                                <button type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="btn btn-success" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <?php } ?>
                            <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                            if ($ACOES->verifica_permissoes(85)) { ?>
                                <button type="submit" name="todos_projetos" value="gerar de todos os projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>
                            <?php } ?>
                                <button type="submit" name="gerar" id="gerar" value="filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                        </div>
                    </div> 
               
                
                <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                        <table class="table table-striped table-hover text-sm valign-middle table-bordered" id="tbRelatorio">
                            <thead>
                                <tr class="titulo">
                                    <th>COD.</th>
                                    <th>NOME</th>
                                    <th>ATIVIDADE</th>
                                    <th>UNIDADE</th>
                                    <th>SALÁRIO</th>
                                    <th>DATA DE NASCIMENTO</th>
                                    <th>ESTADO CIVIL</th>
                                    <th>SEXO</th>
                                    <th>NACIONALIDADE</th>
                                    <th>ENDEREÇO</th>
                                    <th>Nº</th>
                                    <th>BAIRRO</th>
                                    <th>CIDADE</th>
                                    <th>UF</th>
                                    <th>CEP</th>
                                    <th>NATURALIDADE</th>
                                    <th>ESTUDANTE</th>
                                    <th>TERMINOU EM</th>
                                    <th>ESCOLARIDADE</th>
                                    <th>TIPO DE FORMAÇÃO (CURSO)</th>
                                    <th>INSTITUIÇÃO DE ENSINO</th>
                                    <th>TELEFONE FIXO</th>
                                    <th>CELULAR</th>
                                    <th>PAI</th>
                                    <th>NACIONALIDADE DO PAI</th>
                                    <th>MÃE</th>
                                    <th>NACIONALIDADE DA MÃE</th>
                                    <th>NÚMERO DE FILHOS</th>
                                    <th>CABELOS</th>
                                    <th>OLHOS</th>
                                    <th>PESO</th>
                                    <th>ALTURA</th>
                                    <th>ETNIA</th>
                                    <th>MARCAS OU CICATRIZ</th>
                                    <th>DEFICIÊNCIA</th>
                                    <th>CPF</th>
                                    <th>NÚMERO (CTPS)</th>
                                    <th>SÉRIE (CTPS)</th>
                                    <th>UF (CTPS)</th>
                                    <th>DATA DA CARTEIRA DE TRABALHO</th>
                                    <th>CERTIFICADO DE RESERVISTA</th>
                                    <th>PIS</th>
                                    <th>DATA DO PIS</th>
                                    <th>FGTS</th>
                                    <th>TÍTULO DE ELEITOR</th>
                                    <th>ZONA</th>
                                    <th>SEÇÃO</th>
                                    <th>RG</th>
                                    <th>ORGÃO EXPEDIDOR (RG)</th>
                                    <th>UF (RG)</th>
                                    <th>DATA DE EXPEDIÇÃO (RG)</th>
                                    <th>MATRÍCULA</th>
                                    <th>DATA DE ENTRADA</th>
                                    <th>DATA DO EXAME ADMISSIONAL</th>
                                    <th>LOCAL DE PAGAMENTO</th>
                                    <th>OBSERVAÇÕES</th>
                                    <th>BANCO</th>
                                    <th>AGÊNCIA</th>
                                    <th>C.C.</th>
                                </tr> 
                            </thead>
                           
                            <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                                $class = ($cont++ % 2 == 0) ? "even" : "odd"; ?>

                                <tbody>
                                    <tr class="<?php echo $class ?>">
                                        <td><?php echo $row_rel['campo3'] ?></td>
                                        <td><?php echo $row_rel['nome'] ?></td>
                                        <td> <?php echo $row_rel['nome_curso']; ?></td>
                                        <td><?php echo $row_rel['locacao'] ?></td>
                                        <td align="center"><?php echo number_format($row_rel['salario'], 2, ',', '.'); ?></td>
                                        <td> <?php echo $row_rel['data_nascibr']; ?></td>
                                        <td> <?php echo $row_rel['civil']; ?></td>
                                        <td> <?php echo $row_rel['sexo']; ?></td>
                                        <td> <?php echo $row_rel['nacionalidade']; ?></td>
                                        <td> <?php echo $row_rel['enderecoClt']; ?></td>
                                        <td> <?php echo $row_rel['numero']; ?></td>
                                        <td> <?php echo $row_rel['bairro']; ?></td>
                                        <td> <?php echo $row_rel['cidade']; ?></td>
                                        <td> <?php echo $row_rel['uf']; ?></td>
                                        <td> <?php echo $row_rel['cep']; ?></td>
                                        <td> <?php echo $row_rel['naturalidade']; ?></td>
                                        <td> <?php echo $row_rel['estuda']; ?></td>
                                        <td> <?php echo $row_rel['data_escolabr']; ?></td>
                                        <td> <?php echo $row_rel['escolaridade']; ?></td>
                                        <td> <?php echo $row_rel['curso']; ?></td>
                                        <td> <?php echo $row_rel['instituicao']; ?></td>
                                        <td> <?php echo $row_rel['tel_fixo']; ?></td>
                                        <td> <?php echo $row_rel['tel_cel']; ?></td>
                                        <td> <?php echo $row_rel['pai']; ?></td>
                                        <td> <?php echo $row_rel['nacionalidade_pai']; ?></td>
                                        <td> <?php echo $row_rel['mae']; ?></td>
                                        <td> <?php echo $row_rel['nacionalidade_mae']; ?></td>
                                        <td> <?php echo $row_rel['num_filhos']; ?></td>
                                        <td> <?php echo $row_rel['cabelos']; ?></td>
                                        <td> <?php echo $row_rel['olhos']; ?></td>
                                        <td> <?php echo $row_rel['peso']; ?></td>
                                        <td> <?php echo $row_rel['altura']; ?></td>
                                        <td> <?php echo $row_rel['nome_etnia']; ?></td>
                                        <td> <?php echo $row_rel['defeito']; ?></td>
                                        <td> <?php echo $row_rel['deficiencia']; ?></td>
                                        <td> <?php echo $row_rel['cpf']; ?></td>
                                        <td align="center"> <?php echo $row_rel['numero_ctps']; ?></td>
                                        <td align="center"> <?php echo $row_rel['serie_ctps']; ?></td>
                                        <td align="center"> <?php echo $row_rel['uf_ctps']; ?></td>
                                        <td> <?php echo $row_rel['data_ctpsbr']; ?></td>
                                        <td> <?php echo $row_rel['reservista']; ?></td>
                                        <td> <?php echo $row_rel['pis']; ?></td>
                                        <td> <?php echo $row_rel['data_pisbr']; ?></td>
                                        <td> <?php echo $row_rel['fgts']; ?></td>
                                        <td> <?php echo $row_rel['titulo']; ?></td>
                                        <td> <?php echo $row_rel['zona']; ?></td>
                                        <td> <?php echo $row_rel['secao']; ?></td>
                                        <td> <?php echo $row_rel['rg']; ?></td>
                                        <td> <?php echo $row_rel['orgao']; ?></td>
                                        <td> <?php echo $row_rel['uf_rg']; ?></td>
                                        <td> <?php echo $row_rel['data_rgbr']; ?></td>
                                        <td> <?php echo $row_rel['matricula']; ?></td>
                                        <td> <?php echo $row_rel['data_entradabr']; ?></td>
                                        <td> <?php echo $row_rel['data_examebr']; ?></td>
                                        <td> <?php echo $row_rel['localpagamento']; ?></td>
                                        <td> <?php echo $row_rel['observacao']; ?></td>
                                        <td> <?php echo $row_rel['nome_banco']; ?></td>
                                        <td> <?php echo $row_rel['agencia']; ?></td>
                                        <td> <?php echo $row_rel['conta']; ?></td>
                                    </tr>

                                <?php } ?>



                            </tbody>
                        </table>
                    <?php } ?>
            </form>
            <?php include('../template/footer.php'); ?>
            <div class="clear"></div>
        </div>

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script>
                                 $(function () {
                                     $(".bt-image").on("click", function () {
                                         var id = $(this).data("id");
                                         var contratacao = $(this).data("contratacao");
                                         var nome = $(this).parents("tr").find("td:first").html();
                                         thickBoxIframe(nome, "relatorio_documentos_new.php", {id: id, contratacao: contratacao, method: "getList"}, "625-not", "500");
                                     });
                                 });
                                 $(function () {
                                     $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
                                 });
        </script>

    </body>
</html>
<!-- A -->