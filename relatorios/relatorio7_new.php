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

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;
    $arrayStatus = array(10, 40, 50, 51, 52);
    $status = implode(",", $arrayStatus);

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $tipo_contratacao = $_REQUEST['tipo'];
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
    $contratacao = ($tipo_contratacao == "2")? "clt" : "autonomo";
    
    if($tipo_contratacao == 2) {
        $str_qr_relatorio = "SELECT *, A.nome, B.nome AS nome_curso, B.salario,
            A.cpf, A.rg, A.pis, C.nome AS nome_banco, A.agencia, A.conta,
            date_format(A.data_demi, '%d/%m/%Y')as data_saidabr,
            date_format(A.data_nasci, '%d/%m/%Y')as data_nascibr,
            date_format(A.data_escola, '%d/%m/%Y')as data_escolabr,
            D.nome as nome_etnia,
            date_format(A.data_ctps, '%d/%m/%Y')as data_ctpsbr,
            date_format(A.dada_pis, '%d/%m/%Y')as data_pisbr,
            date_format(A.data_rg, '%d/%m/%Y')as data_rgbr,
            date_format(A.data_entrada, '%d/%m/%Y')as data_entradabr,
            date_format(A.data_exame, '%d/%m/%Y')as data_examebr
            FROM rh_clt AS A
            LEFT JOIN curso AS B
            ON B.id_curso = A.id_curso
            LEFT JOIN bancos AS C
            ON C.id_banco = A.banco
            LEFT JOIN etnias AS D
            ON D.id = A.etnia
            WHERE A.id_regiao = '$id_regiao' AND A.tipo_contratacao = '$tipo_contratacao' AND A.status >= '60' ";
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
        <title>:: Intranet :: Relatório de Função e Salário</title>
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
                        <h2>Relatório de Função e Salário</h2>
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
                                <th>ATIVIDADE</th>
                                <th>UNIDADE</th>
                                <th>SALÁRIO</th>
                                <th>DATA DE NASCIMENTO</th>
                                <th>ESTADO CIVIL</th>
                                <th>SEXO</th>
                                <th>NACIONALIDADE</th>
                                <th>ENDEREÇO</th>
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
                                <th>DATA DE ENTRADA</th>
                                <th>DATA DO EXAME ADMISSIONAL</th>
                                <th>LOCAL DE PAGAMENTO</th>
                                <th>OBSERVAÇÕES</th>
                                <th>BANCO</th>
                                <th>AGÊNCIA</th>
                                <th>C.C.</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"; ?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo $row_rel['campo3'] ?></td>
                                <td><?php echo $row_rel['nome'] ?></td>
                                <td> <?php echo $row_rel['nome_curso']; ?></td>
                                <td><?php echo $row_rel['locacao'] ?></td>
                                <td align="center"><?php echo number_format($row_rel['salario'],2,',','.'); ?></td>
                                <td> <?php echo $row_rel['data_nascibr']; ?></td>
                                <td> <?php echo $row_rel['civil']; ?></td>
                                <td> <?php echo $row_rel['sexo']; ?></td>
                                <td> <?php echo $row_rel['nacionalidade']; ?></td>
                                <td> <?php echo $row_rel['endereco']; ?></td>
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
                                <td> <?php echo $row_rel['serie_ctps']; ?></td>
                                <td> <?php echo $row_rel['uf_ctps']; ?></td>
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
                <?php  } ?>
            </form>
        </div>
    </body>
</html>