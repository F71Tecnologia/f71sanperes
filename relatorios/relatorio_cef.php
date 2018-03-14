<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
    }

include "../conn.php";
include "../classes/funcionario.php"; 
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$optRegiao = getRegioes();
$ACOES = new Acoes();

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos']) || isset($_REQUEST['exportar'])) {
    
    $cont = 0;
    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
    
        //tipo de documento == 1
        $str_qr_relatorio = "SELECT A.nome, A.cpf, A.data_nasci,A.uf_nasc, A.cidade, A.uf, A.mae,A.sexo,A.rg,A.orgao,A.uf_rg,A.data_rg,A.id_curso,A.data_entrada,A.tipo_endereco,A.endereco,A.numero,A.bairro,A.cep,A.tel_cel,A.email,A.escolaridade,A.banco,A.agencia,A.conta,A.naturalidade,A.conta_dv, A.tipo_conta, C.salario, C.nome as ocupacao
                             FROM rh_clt A
                             LEFT JOIN curso C on (C.id_curso = A.id_curso)
                             WHERE A.id_regiao = '$id_regiao' AND A.id_projeto = '$id_projeto' AND (A.status < 60 AND (A.status <> 200 AND A.status <> 67 AND A.status <> 68 AND A.status <> 69))";
         
    if(!isset($_REQUEST['todos_projetos'])) {
        $str_qr_relatorio .= "AND A.id_projeto = '$id_projeto' ";
    }
    
    $str_qr_relatorio .= "ORDER BY A.id_projeto,A.nome";

    $qr_relatorio = mysql_query($str_qr_relatorio) or die(mysql_error());
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;


?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Relatório de Importação CEF</title>
        
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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relatórios de Importação CEF</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group" >
                            
                            <label for="select" class="col-sm-2 control-label hidden-print" >Região</label>
                            <div class="col-sm-4">
                              <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                            
                            <label for="select" class="col-sm-1 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-4">
                              <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?> <span class="loader"></span> 
                            </div>
                            <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel;?>">
                             <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        </div>
                    </div>
                        
                        <div class="panel-footer text-right hidden-print controls">
                            <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])){ ?>
                                <button type="button" onclick="tableToExcel('tbRelatorio', 'Planilha Funcionarios')" value="Exportar para Excel" class="btn btn-success" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <?php } ?>
                            <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                                if($ACOES->verifica_permissoes(85)) { ?>
                            <!--<button type="submit" name="todos_projetos" value="gerar de todos os projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>-->
                            <?php } ?>
                            <button type="submit" name="gerar" id="gerar" value="gerrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                        </div>
                    </div>
                
                <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover text-sm valign-middle" id="tbRelatorio">
                
                        <thead>
                            <tr> 
                                <th>NOME COMPLETO</th>
                                <th>CPF</th>
                                <th>DATA DE NASCIMENTO</th>
                                <th>LOCAL DE NASCIMENTO</th>                                
                                <th>UF NASCIMENTO</th>
                                <th>ESTADO CIVIL</th>
                                <th>NOME DA MÃE</th>
                                <th>SEXO</th>
                                <th>DOC - TIPO DE DOCUMENTO</th>
                                <th>DOC - NUMERO</th>
                                <th>DOC - ORGÇÃO EMISSOR</th>
                                <th>OCUPAÇÃO</th>
                                <th>DATA DE ADMISSÃO</th>
                                <th>TIPO DE LOGRADOURO</th>
                                <th>ENDEREÇO</th>
                                <th>NÚMERO</th>
                                <th>BAIRRO</th>
                                <th>CIDADE</th>
                                <th>UF</th>
                                <th>CEP</th>
                                <th>DDD</th>
                                <th>TELEFONE</th>
                                <th>E-MAIL</th>
                                <th>GRAU DE INSTRUÇÃO</th>
                                <th>RENDA</th>
                                <th>BANCO - DESTINO</th>
                                <th>AGÊNCIA - DESTINO</th>
                                <th>OPERAÇÃO - DESTINO</th>
                                <th>CONTA - DESTINO</th>
                                <th>CONTA DV - DESTINO</th>
                            </tr>
                        </thead>
                        <!--<tbody>-->
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { 
                        $class = ($cont++ % 2 == 0)?"even":"odd"; 
                        $ddd = substr($row_rel['tel_cel'], 0, 4);
                        $ddd ="0".substr(trim($ddd), 1, 2);
                        $ddd = substr($row_rel['tel_cel'], 0, 4);
                        $tel = substr(trim($row_rel['tel_cel']), 5,strlen($row_rel['tel_cel']));
                        switch($row_rel['tel_cel'])
                        {
                            case "salario":
                                            $tipo_conta = 23;
                                            break;
                            case "corrente":
                                            $tipo_conta = 001;
                                            break;    
                            case "poupanca":
                                            $tipo_conta = 012;
                                            break;    
                        }
                        ?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo RemoveCaracteresGeral($row_rel['nome']); ?></td>
                                <td><?php echo RemoveCaracteresGeral($row_rel['cpf']); ?></td>
                                <td><?php echo implode("/", array_reverse(explode("-", $row_rel['data_nasci']))); ?></td>
                                <td><?php echo RemoveCaracteresGeral(RemoveAcentos($row_rel['naturalidade'])) ?></td>
                                <td><?php echo RemoveCaracteresGeral($row_rel['uf_nasc']); ?></td>
                                <td>1</td>
                                <td><?php echo $row_rel['mae']; ?></td>
                                <td><?php echo $row_rel['sexo']; ?></td>
                                <td>1</td>
                                <td><?php echo $row_rel['rg']; ?></td>
                                <td><?php echo $row_rel['orgao']; ?></td>
                                <td><?php echo $row_rel['ocupacao']; ?></td>
                                <td><?php echo implode("/", array_reverse(explode("-", $row_rel['data_entrada']))); ?></td>
                                <td><?php echo $row_rel['tipo_entrada']; ?></td>
                                <td><?php echo $row_rel['endereco']; ?></td>
                                <td><?php echo $row_rel['numero']; ?></td>
                                <td><?php echo $row_rel['bairro']; ?></td>
                                <td><?php echo $row_rel['cidade']; ?></td>
                                <td><?php echo $row_rel['uf']; ?></td>
                                <td><?php echo $row_rel['cep']; ?></td>
                                <td><?php echo $ddd; ?></td>
                                <td><?php echo $tel; ?></td>
                                <td><?php echo $row_rel['email']; ?></td>
                                <td><?php echo $row_rel['escolaridade']; ?></td>
                                <td><?php echo formataMoeda($row_rel['salario']); ?></td>
                                <td><?php echo $row_rel['banco']; ?></td>
                                <td><?php echo $row_rel['agencia']; ?></td>
                                <td><?php echo $tipo_conta; ?></td>
                                <td><?php echo $row_rel['conta']; ?></td>
                                <td><?php echo $row_rel['dv_conta']; ?></td>
                            </tr>                                
                        <?php } ?>
                    <!--</tbody>-->
                </table>
                </div>
                <?php  } ?>
             
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
           $(function() {
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");     
                
                $('#exportar').click(function(){
                   $('#form').attr('action','exporta_planilha_funcionarios.php');
                   $('#form').submit();
                });
                
                 $('.gera').click(function(){
                      $('#form').attr('action','');
                      if($(this).val() === 'Gerar de Todos Projetos'){
                          $('#projeto').val('-1');
                      }
                 });
            });
        </script>
        
    </body>
</html>
