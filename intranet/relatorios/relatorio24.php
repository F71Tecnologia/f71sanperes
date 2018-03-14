<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include('../classes/global.php');
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$optProjeto = getProjetos($usuario['id_regiao']);
$ACOES = new Acoes();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

if (isset($_REQUEST['gerar']) || isset($_REQUEST['exportar'])) {
    
    $cont = 0;    
    $id_projeto = $_REQUEST['projeto'];
    $ano = date("Y");
    
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
        
        $str_qr_relatorio = "SELECT A.mes, J.nome as projeto, B.nome, B.cpf, C.matricula, 'clt' as regime_contratacao, B.inss,
            C.data_entrada, D.nome as cargo, 'especialidade', E.nome as centro_custo, F.horas_semanais, 
            G.especifica as status, 
            B.sallimpo_real as salario_bruto, 
            B.fgts, L.valor_movimento as dsr, B.a9000 as adicional_noturno, K.valor_movimento as insalubridade, 
            'valor total de vt' as valor_total_de_vt, B.a7001 as desconto_vt, 'auxilio', 'beneficio', M.valor_movimento as hora_extra, 
            N.valor_movimento as faltas, O.valor_movimento as atraso, H.umterco, H.total_liquido as ferias, 'decimo', I.total_liquido as recisao, 'outros' 

            FROM rh_folha AS A 
            LEFT JOIN rh_folha_proc AS B ON(A.id_folha = B.id_folha)
            LEFT JOIN rh_clt AS C ON(B.id_clt = C.id_clt)
            LEFT JOIN curso AS D ON(C.id_curso = D.id_curso)
            LEFT JOIN centro_custo AS E ON(C.id_centro_custo = E.id_centro_custo)
            LEFT JOIN rh_horarios AS F ON(C.rh_horario = F.id_horario)
            LEFT JOIN rhstatus AS G ON(C.`status` = G.codigo)
            LEFT JOIN rh_ferias AS H ON(C.id_clt = H.id_clt AND H.`status` = 1 AND H.mes = B.mes AND H.ano = B.ano)
            LEFT JOIN rh_recisao AS I ON(C.id_clt = I.id_clt AND I.`status` = 1 AND MONTH(I.data_demi) = B.mes AND YEAR(I.data_demi) = B.ano)
            LEFT JOIN projeto AS J ON(A.projeto = J.id_projeto)
            LEFT JOIN (SELECT valor_movimento,id_clt,cod_movimento,mes_mov,ano_mov,status FROM rh_movimentos_clt WHERE ano_mov = 2015 AND cod_movimento = 6006 AND `status` = 5) AS K ON(C.id_clt = K.id_clt AND K.mes_mov = B.mes AND K.ano_mov = B.ano )
            LEFT JOIN (SELECT valor_movimento,id_clt,cod_movimento,mes_mov,ano_mov,status FROM rh_movimentos_clt WHERE ano_mov = 2015 AND cod_movimento = 9997 AND `status` = 5) AS L ON(C.id_clt = L.id_clt AND L.mes_mov = B.mes AND L.ano_mov = B.ano)
            LEFT JOIN (SELECT valor_movimento,id_clt,cod_movimento,mes_mov,ano_mov,status FROM rh_movimentos_clt WHERE ano_mov = 2015 AND cod_movimento IN(80024,8080,80025) AND `status` = 5) AS M ON(C.id_clt = M.id_clt AND M.mes_mov = B.mes AND M.ano_mov = B.ano)
            LEFT JOIN (SELECT valor_movimento,id_clt,cod_movimento,mes_mov,ano_mov,status FROM rh_movimentos_clt WHERE ano_mov = 2015 AND cod_movimento IN(8000,50249,80032) AND `status` = 5) AS N ON(C.id_clt = N.id_clt AND N.mes_mov = B.mes AND N.ano_mov = B.ano)
            LEFT JOIN (SELECT valor_movimento,id_clt,cod_movimento,mes_mov,ano_mov,status FROM rh_movimentos_clt WHERE ano_mov = 2015 AND cod_movimento = 50252 AND `status` = 5) AS O ON(C.id_clt = O.id_clt AND O.mes_mov = B.mes AND O.ano_mov = B.ano)
            WHERE B.status = 3 AND A.projeto = '{$id_projeto}' AND A.ano = '{$ano}'
            GROUP BY A.mes, A.ano, C.id_clt
            ORDER BY A.mes, B.nome ASC";
    
    $qr_relatorio = mysql_query($str_qr_relatorio) or die(mysql_error());
}

$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;

if (isset($_REQUEST['data_xls'])) {
    
    $dados = $_REQUEST['data_xls'];
    
    ob_end_clean();
    header("Content-Encoding: UTF-8");
    header("Pragma: private");
    header("Cache-control: private, must-revalidate");
    header("Expires: 0");    
    header("Content-type: application/vnd.ms-excel");
//    header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
//    header("Content-type: application/xls");
    header("Content-Disposition: attachment; filename=relatorio_ses.xls");
    
    echo "\xEF\xBB\xBF";    
    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel/' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "  <head>";
    echo "  <title>RELATÓRIO SES</title>";
    echo "      <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
    echo "  </head>";
    echo "  <body>";
    echo "      $dados";
    echo "  </body>";
    echo "</html>";
    exit;
    
}

if (isset($_REQUEST['exp_'])) {
    //include_once 'xls_generator.php';
}

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
                /*$('#exportar').click(function(){
                   $('#form').attr('action','exporta_planilha_funcionarios.php');
                   $('#form').submit();
                });*/
                
                $('.gera').click(function(){
                    $('#form').attr('action','');
                    if($(this).val() === 'Gerar de Todos Projetos'){
                        $('#projeto').val('-1');
                    }
                });
                
                $("#exportarExcel").click(function () {
                    var html = $("#relatorio_exp").html();
                    $("#data_xls").val(html);
                    $("#exp_").click();
                });
            });
        </script>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Planilha de Funcionarios </h2></div>
            <div id="content">
                <form  name="form" action="" method="post" id="form" class="form-horizontal" >                                
                    <div class="panel panel-default">
                       <div class="panel-heading text-bold hidden-print">Relatório</div>
                        <div class="panel-body">
                            <div class="form-group" >
                                <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                                <label for="select" class="col-sm-5 control-label">Projeto:</label> 
                                <div class="col-sm-3">
                                    <?php echo montaSelect($optProjeto, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?> 
                                </div>
                            </div>
                            <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>                                                    
                        </div>
                    
                     

                        <div class="panel-footer text-right hidden-print">
                             <?php if (!empty($qr_relatorio) && (isset($_POST['gerar']))){ ?>    
                            <button type="button" id="exportarExcel" name="exportarExcel" value="Exportar para Excel" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar Para Excel</button>
                             <?php } ?>

                            <button type="submit" name="gerar" value="Gerar" id="gerar" class="btn btn-primary" ><span class="fa fa-filter"></span> Gerar</button>

                             <input type="hidden" id="data_xls" name="data_xls" value="">
                            <input type="submit" id="exp_" name="exp_" value="exp_" style="display: none">
                        </div>
                    </div>

                    
                     <?php if (!empty($qr_relatorio) && (isset($_POST['gerar']))){ ?>
                            <div id="relatorio_exp">
                                <table width="100%" class="table table-bordered table-condensed text-sm"> 
                                        <thead>
                                            <tr>
                                                <th>MES DE COMPETENCIA</th>
                                                <th>UNIDADE DE TRABALHO</th>
                                                <th>NOME DO FUNCIONARIO</th>
                                                <th>CPF</th>
                                                <th>MATRICULA</th>
                                                <th>REGIME DE CONTRATACAO</th>                                
                                                <th>DATA DE ADMISSAO</th>
                                                <th>CARGO</th>
                                                <th>ESPECIALIDADE</th>
                                                <th>CENTRO DE CUSTO</th>
                                                <th>CARGA HORARIA SEMANAL</th>
                                                <th>STATUS</th>
                                                <th>VALOR DE CUSTO TOTAL</th>
                                                <th>SALARIO BRUTO</th>
                                                <th>INSS</th>
                                                <th>FGTS</th>
                                                <th>DSR</th>
                                                <th>ADICIONAL NOTURNO</th>
                                                <th>INSALUBRIDADE</th>
                                                <th>VALOR TOTAL DE V.T.</th>
                                                <th>VALOR DESCONTADO DE V.T.</th>
                                                <th>AUXILIO</th>
                                                <th>BENEFICIOS</th>
                                                <th>HORA EXTRA</th>
                                                <th>FALTAS</th>
                                                <th>ATRASOS</th>
                                                <th>1/3 FERIAS</th>
                                                <th>FERIAS</th>
                                                <th>13º SALARIO</th>
                                                <th>RESCISAO</th>
                                                <th>OUTROS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                        while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                                            $class = ($cont++ % 2 == 0)?"even":"odd";      

                                            $custo_total_rend = $row_rel['salario_bruto'] + $row_rel['fgts'] + $row_rel['dsr'] + $row_rel['adicional_noturno'] + $row_rel['insalubridade'] + $row_rel['hora_extra'] + $row_rel['umterco'] + $row_rel['ferias'] + $row_rel['recisao'] + $row_rel['inss'];
                                            $custo_total_desc = $row_rel['desconto_vt'] + $row_rel['faltas'] + $row_rel['atraso'];
                                            $custo_total = $custo_total_rend - $custo_total_desc;
                                        ?>                            
                                            <tr class="<?php echo $class; ?>">
                                                <td><?php echo mesesArray($row_rel['mes']); ?></td>
                                                <td><?php echo RemoveAcentos($row_rel['projeto']); ?></td>
                                                <td><?php echo RemoveAcentos($row_rel['nome']); ?></td>
                                                <td><?php echo $row_rel['cpf']; ?></td>
                                                <td><?php echo $row_rel['matricula']; ?></td>
                                                <td><?php echo $row_rel['regime_contratacao']; ?></td>
                                                <td><?php echo converteData($row_rel['data_entrada'], "d/m/Y"); ?></td>                                
                                                <td><?php echo RemoveAcentos($row_rel['cargo']); ?></td>
                                                <td> - </td>
                                                <td><?php echo RemoveAcentos($row_rel['centro_custo']); ?></td>
                                                <td><?php echo $row_rel['horas_semanais']; ?></td>
                                                <td><?php echo RemoveAcentos($row_rel['status']); ?></td>
                                                <td><?php echo formataMoeda($custo_total); ?></td>
                                                <td><?php echo formataMoeda($row_rel['salario_bruto']); ?></td>
                                                <td><?php echo formataMoeda($row_rel['inss']); ?></td>
                                                <td><?php echo formataMoeda($row_rel['fgts']); ?></td>
                                                <td><?php echo formataMoeda($row_rel['dsr']); ?></td>
                                                <td><?php echo formataMoeda($row_rel['adicional_noturno']); ?></td>
                                                <td><?php echo formataMoeda($row_rel['insalubridade']); ?></td>
                                                <td> - </td>
                                                <td>- <?php echo formataMoeda($row_rel['desconto_vt']); ?></td>
                                                <td> - </td>
                                                <td> - </td>
                                                <td><?php echo formataMoeda($row_rel['hora_extra']); ?></td>
                                                <td>- <?php echo formataMoeda($row_rel['faltas']); ?></td>
                                                <td>- <?php echo formataMoeda($row_rel['atraso']); ?></td>
                                                <td><?php echo formataMoeda($row_rel['umterco']); ?></td>
                                                <td><?php echo formataMoeda($row_rel['ferias']); ?></td>
                                                <td> - </td>
                                                <td><?php echo formataMoeda($row_rel['recisao']); ?></td>
                                                <td> - </td>
                                            </tr>
                                        <?php
                                        } ?>
                                        </tbody>
                                    </table>
                                </div>
                          
                            <?php
                            } ?>
                           </div>
                  
                    
                      
            </form>
                <?php include('../template/footer.php'); ?>
       
    </div>    
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        
      </body>
</html>