<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include("../../conn.php");
include("../../classes/funcionario.php");
include("../../wfunction.php");
include "../../classes/LogClass.php";
//include 'C:\xampp\htdocs\iabas_sp\intranet\include\ChromePhp.php';
$container_full = 1;
$usuario = carregaUsuario();
$log = new Log();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$usuarioW = carregaUsuario();
$idMaster = $usuarioW['id_master'];
$html = "";

if (validate($_REQUEST['method']) && $_REQUEST['method'] == "getProjetos") {
    $cnpj = $_REQUEST['cnpj'];
    $qr = "SELECT A.cnpj, B.nome
            FROM rhempresa AS A
            LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto)
            WHERE A.cnpj = '$cnpj'
            ORDER BY B.nome";
    $rs = mysql_query($qr);

    $html = "<div id='modal_moldura'><div id='modal_geral'><div id='modal_lado1'>";

    $html .= "<table cellpadding='0' cellspacing='0' border='0' class='grid' width='100%' id='tbgerada'>";
    $html .= "<thead><tr><th>Projetos</th></thead><tbody>";
    $cnt = 0;
    while ($row = mysql_fetch_assoc($rs)) {
        $class = ($cnt++ % 2 == 0) ? "odd" : "even";
        $html .= "<tr class='{$class}'><td>" . $row['nome'] . "</td></tr>";
    }
    $html .="</tbody></table>";
    
    echo utf8_encode($html);
    exit;
}


$rowMaster = montaQueryFirst("master", "nome,cod_os", "id_master = {$usuarioW['id_master']}");

$rs_regioes = montaQuery("regioes", "id_regiao", "id_master = '{$idMaster}'");
$idsRegioes = null;
foreach ($rs_regioes as $val) {
    $idsRegioes[] = current($val);
}

$rs_empresas = montaQuery("rhempresa", "id_empresa,id_regiao,nome,cnpj,id_projeto", "id_regiao IN (" . implode(",", $idsRegioes) . ")", "nome", null, "array", false, "cnpj");
$numEmpresas = count($rs_empresas);
$alternateColor = 0;

//foi alterado, pq a Maria está gerando de 2013
$anoBase = date('Y') - 1;
//$anoBase = 2013;
$anos = anosArray("2010", $anoBase);
$anosSel = (validate($_REQUEST['ano'])) ? $_REQUEST['ano'] : $anoBase;

if (validate($_REQUEST['geratxt'])) {
    include("montaRais.class.php");
    $txt = new montaRais($anosSel);

    $empresas = $txt->consultaEmpresas($_REQUEST['cnpj']);

    foreach ($empresas as $empresa) {        
        $nomeFile = normalizaNometoFile($empresa['id_empresa'] . "_" . $empresa['nome'] . "_" . $anoBase) . ".txt";
        $arquvios[] = $nomeFile;
        $arquivo = fopen("arquivos/" . $nomeFile, "w+");
        
        $id_projetos = $txt->getProjetosByCNPJ($empresa['cnpj']);
        
        $rsdadosEmpregado = $txt->consultaEmpregado($id_projetos);
        
        
        $array_est = $txt->getEstatisticas($id_projetos,$anoBase);        
        
        
        $arr_hora_extra = $txt->incluiHoraExtraMatriz($id_projetos, $array_est);        
        
        $arr_contribuicao = $txt->incluiContribuicao($id_projetos, $array_est);
        $arr_contribuicaoAss = $txt->incluiContribuicaoAss($id_projetos, $array_est);
        
        
        while ($rowDados = mysql_fetch_assoc($rsdadosEmpregado)) {
            $txt->montaMatriz($rowDados);
        }   
        
        $txt->verificaValoresMes();
        $txt->setPAT();
        $txt->montaCabecalho($arquivo, $empresa);
        $txt->montaDetalhe($arquivo, $arr_hora_extra, $arr_contribuicao, $arr_contribuicaoAss);
        $txt->montaRodape($arquivo);
        fclose($arquivo);
        $txt->unsetMatriz();
    }

    $html = "<div class='listaRais'>";
    $html .= "<p style='font-weight: bold;'>Selecione um item na lista para download</p>";
    foreach ($arquvios as $file) {
        $html .= "<p><a href='arquivos/download.php?file={$file}' target='_blank'><i class='fa fa-download' aria-hidden='true'></i> - {$file}</a></p>";
    }
    $html .= "</div>";
    $log->gravaLog('Relatório e Impostos', "Relatório RAIS gerado - {$empresa['nome']}.");
}

$abashow = 1;
?>
<html>
    <head>
        <title>Gerar RAIS</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskMOney_2.1.2.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskedinput.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>

        <script src="../../js/global.js" type="text/javascript"></script>
        <script>
            $(function() {
                $("#form").validationEngine();
                $(".bt-image").css('cursor', 'pointer');

                $(".bt-image").on("click", function() {
                    var cnpj = $(this).data("key");
                    thickBoxIframe("Projetos do CNPJ " + cnpj, "", {cnpj: cnpj, method: "getProjetos"}, "625-not", "500");
                });

            });
        </script>
        <style type="text/css">
            
            body{
                background-color: white ;
            }
            
            /* As hex codes */

            .color-primary-0 { color: #2C65CE }	/* Main Primary color */
            .color-primary-1 { color: #1B53B9 }
            .color-primary-2 { color: #3564B9 }
            .color-primary-3 { color: #4980E6 }
            .color-primary-4 { color: #6A92DD }
            /* As RGBa codes */

            .rgba-primary-0 { color: rgba( 44,101,206,1) }	/* Main Primary color */
            .rgba-primary-1 { color: rgba( 27, 83,185,1) }
            .rgba-primary-2 { color: rgba( 53,100,185,1) }
            .rgba-primary-3 { color: rgba( 73,128,230,1) }
            .rgba-primary-4 { color: rgba(106,146,221,1) }

            .listaRais{
                border: 0px solid #ccc; 
                padding: 10px;
                width: auto; 
            }
            .listaRais p a{ font-family: arial; text-decoration: none; color: #000; background: url(../../imagens/icones/icon-doc.gif) no-repeat; padding-left: 22px;}
            .listaRais h3{ font-family: arial; font-size: 14px; text-transform: uppercase; color: #000; display: block; border-bottom: 1px solid #ccc; padding-bottom: 5px;}
            .raisColor{
                color:#2C66CE;
            }
            .raisColor>h1{
                margin-top:10px;
                font-weight: bolder;
            }
            .breadcrumb{
                background-color: white;                
            }
            .breadcrumb>li>a{
                color:#6A92DD;
            }
            .breadcrumb>li.active{
                color:#999;
            }
            hr{
                color:#2C65CE;
            }
            
            #anoBaseh2{
                color: black;                
                border-bottom: 8px solid #F58634;
            }
            .container{
                min-height: 100vh;
            }
             
        </style>    
    </head>
    <body  data-type="adm">
       <?php include("../../template/navbar_default.php"); ?>
        <div class="container" style="background-color:white;">
            <form method="post" name="form" id="form" action="">            
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <ol class="breadcrumb">
                        <li><a href="../../">Início</a></li>
                        <li><a href="../">Recursos Humanos</a></li>
                        <li class="active">RAIS</li>
                    </ol>
                </div>                
            </div>
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <h2 id="anoBaseh2"><span class="fa fa-users"></span> - RAIS <small>- Ano Base <span class="destaque"><?php echo $anoBase; ?></span></small></h2>
                   
                    <p style="font-weight: bold;">Selecione as empresas para gerar o arquivo</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <table  cellpadding="0" cellspacing="0" class="table table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Empresa</th>
                                <th>CNPJ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rs_empresas as $row) { ?>                                
                                <tr class="<?php echo ($alternateColor++ % 2 == 0) ? "even" : "odd"; ?>">
                                    <td class="txcenter"><input type="checkbox" name="cnpj[]" id="cnpj[<?php echo $row['id_empresa']; ?>]" value="<?php echo $row['id_empresa']; ?>" class="validate[required]" /> </td>
                                    <td><?php echo $row['nome']; ?></td>
                                    <td><?php echo $row['cnpj']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <button type="submit" name="geratxt" value="GERAR RAIS" class="btn btn-primary exportarTxt">
                        <span class="fa fa-file-text-o"></span> Gerar Arquivo RAIS
                    </button>
                    <?php
                        if (!empty($html)) {
                            echo $html;
                        }
                    ?>
                </div>
            </div>
            </form>    
            <?php include_once '../../template/footer.php'; ?>
</div><!-- container-->
    </body>
</html>