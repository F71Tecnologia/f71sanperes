<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include_once("../../conn.php");
include_once("../../funcoes.php");
include_once("../../wfunction.php");
include_once("../../classes_permissoes/regioes.class.php");
include_once("../../classes/NotaFiscalClass.php");

$usuarioW = carregaUsuario();
$regiao = $usuarioW['id_regiao'];
$master = $usuarioW['id_master'];
$usuario = $usuarioW['id_funcionario'];

//ARRAY DE PERMISSÃO
$array_permissao = array(182,179);

//OBJETO REGIAO
$regioes = new Regioes;

//CARREGA OBJETOS
$notas = new NotaFiscal();

//CARREGA LISTAGEM DE NOTAS
/**********************************LISTAGEM************************************/
$dados = $notas->getNotas();

if($_REQUEST['filtrar']){
    $regiao = (isset($_REQUEST['regiaoFiltro']) && $_REQUEST['regiaoFiltro'] != "-1") ? $_REQUEST['regiaoFiltro'] : "";
    $projeto = (isset($_REQUEST['projeto']) && $_REQUEST['projeto'] != "-1") ? $_REQUEST['projeto'] : "";
    $prestador = (isset($_REQUEST['prestador']) && $_REQUEST['prestador'] != "-1") ? $_REQUEST['prestador'] : "";
    $mes = (isset($_REQUEST['mes']) && $_REQUEST['mes'] != "-1") ? $_REQUEST['mes'] : "";
    $ano = (isset($_REQUEST['ano']) && $_REQUEST['ano'] != "-1") ? $_REQUEST['ano'] : "";
    $dados = $notas->getNotas(null,$regiao,$projeto,$prestador,$mes,$ano);
}


$meses = mesesArray(null);
$anos = anosArray(null, null);
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$regiaoSel = (isset($_REQUEST['regiaoFiltro'])) ? $_REQUEST['regiaoFiltro'] : null;
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m') - 1;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');

//CARREGA PRESTADOR
if($_REQUEST['method'] == "carregaPrestador"){
    $retorno = array("status" => 0);
    $dados = array();
    $query_prestador = "SELECT id_prestador,c_razao FROM  prestadorservico WHERE id_regiao = '{$_REQUEST['regiao']}' AND id_projeto = '{$_REQUEST['projeto']}' AND status = '{$_REQUEST['status']}'";
    try{
        $sql_prestador = mysql_query($query_prestador);
        if(mysql_num_rows($sql_prestador) > 0){
            while($rows_prestador = mysql_fetch_assoc($sql_prestador)){
                $dados[] = array("id_prestador" => $rows_prestador['id_prestador'], "nome" => utf8_encode($rows_prestador['c_razao']));
            }
        }else{
            $dados[] = array("id_prestador" => "-1", "nome" => "Nenhum resultado encontrado");
        }
        $retorno = array("status" => 1, "dados" => $dados);
                
    }catch(Exception $e){
        echo $e->getMessage("Erro ao selecionar prestador de serviço");
    }

    echo json_encode($retorno);
    exit();
}

?>
<html>
    <head>
        <title>:: Intranet :: NOTAS FICAIS</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript"></script>
        <script src="js/notas_fiscais.js" type="text/javascript"></script>
    </head>
    <body id="page-despesas" class="novaintra">
        <div id="content">
            <form action="" method="post" name="form1" id="form1">
                <input type="hidden" name="bancSel" id="bancSel" value="<?php echo $bancoR ?>" />
                <div id="head">
                    <img src="../../imagens/logomaster6.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>GERENCIADOR DE NOTAS FISCAIS</h2>
                    </div>
                </div>
                
                <!--- TELA DOS FILTROS --->
                
                <fieldset class="box-filtro">
                    <legend class="titulo">FILTRO</legend>
                    <div class="padding_50">
                        <div class="col-esq">
                            <p class="first_old">
                                <label>Região:</label> 
                                <select name="regiaoFiltro" id="regiaoFiltro" >
                                    <?php echo $regioes->Preenhe_select_por_master($master,$regiaoSel); ?>
                                </select>
                            </p>
                            <p class="first_old">
                                <label>Projeto:</label> 
                                <select name="projeto" id="projeto">
                                    <option value="-1">« Aguardando região »</option>
                                </select>
                            </p>
                            <p class="first_old">
                                <label>Prestador/Fornecedor:</label> 
                                <select name="prestador" id="prestador">
                                    <option value="-1">« Aguardando projeto »</option>
                                </select>
                            </p>
                        </div>
                        <div class="col-dir">
                            <p class="first_old"><label>Mês Competência:</label> <?php echo montaSelect($meses, $mesR, "id='mes' name='mes' ") ?></p>
                            <p class="first_old"><label>Ano Competência:</label> <?php echo montaSelect($anos, $anoR, "id='ano' name='ano'") ?></p>
                        </div>
                    </div>
                    <p class="controls"> 
                        <input type="submit" class="button" value="Filtrar" name="filtrar" /> 
                        <?php if(in_array($_COOKIE['logado'], $array_permissao)){ ?><input type="button" class="button" value="Novo" name="novo" id="novo" /><?php  } ?>
                        <p class="first_old"><input type="hidden" name="acao" id="acao" value="" /></p>
                        <p class="first_old"><input type="hidden" name="nota" id="nota" value="" /></p>
                        <p class="first_old"><input type="hidden" name="projeto_filtro" id="projeto_filtro" value="<?php echo $_REQUEST['projeto']; ?>" /></p>
                        <p class="first_old"><input type="hidden" name="prestador_filtro" id="prestador_filtro" value="<?php echo $_REQUEST['prestador']; ?>" /></p>
                    </p>
                </fieldset>
                
                <!--LISTAGEM DE NOTAS FISCSAIS :) -->
                <?php if(count($dados) > 0){ ?>
                    <fieldset class="box-listagem">
                        <legend class="titulo">LISTAGEM DE NOTAS FISCAIS</legend>
                        <div class="padding_50" style="margin-bottom:30px; overflow: hidden;">
                            <table id="tbNotas" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto; margin-top: 16px;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>PROJETO</th>
                                        <th>GRUPO</th>
                                        <th>SUBGRUPO</th>
                                        <th>PRESTADOR</th>
                                        <th>N° DOCUMENTO</th>
                                        <th>DATA EMISSÃO</th>
                                        <th>COMPETÊNCIA</th>
                                        <th>VALOR BRUTO</th>
                                        <?php if(in_array($_COOKIE['logado'], $array_permissao)){ ?><th colspan="2">AÇÃO</th><?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $total = 0; ?>
                                    <?php foreach ($dados as $notas){ ?>
                                        <?php $total += $notas['valor_bruto']; ?>
                                        <tr data-key="<?php echo $notas['nota']; ?>">
                                            <td><?php echo $notas['nota']; ?></td>
                                            <td><?php echo $notas['projeto']; ?></td>
                                            <td><?php echo $notas['grupo']; ?></td>
                                            <td><?php echo $notas['subgrupo']; ?></td>
                                            <td><?php echo $notas['nome_prestador']; ?></td>
                                            <td><?php echo $notas['num_doc']; ?></td>
                                            <td><?php echo $notas['data_emissao']; ?></td>
                                            <td><?php echo $meses[$notas['mes_competencia']] . "/" . $notas['ano_competencia']; ?></td>
                                            <td align="right"><?php echo "R$ " . $notas['valor_bruto']; ?></td>
                                            <?php if(in_array($_COOKIE['logado'], $array_permissao)){ ?>
                                                <?php $estiloEspecial = " style = 'border-right: 0px !important' "; ?>
                                                <td><img src="../../imagens/icones/icon-edit.gif" data-key="<?php echo $notas['nota'] ?>" title="Editar" class="edit_nota"/></td>
                                                <td><img src="../../imagens/icones/icon-delete.gif" data-key="<?php echo $notas['nota'] ?>" title="Excluir" class="remove_nota"/></td>
                                            <?php } ?>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    
                                    <tr>
                                        <td colspan="8" align="right" style="font-weight: bold; ">Total</td>
                                        <td align="right" <?php echo $estiloEspecial; ?>><?php echo "R$ " . number_format($total,2,",","."); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </fieldset>    
                <?php }else{ ?>
                    <p class="vermelha">Nenhum resultado encontrado</p>
                <?php } ?>
             </form>
        </div>
    </body>
</html>