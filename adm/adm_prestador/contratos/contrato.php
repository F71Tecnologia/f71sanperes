<?php
include('../../../conn.php');
include('../../../classes/global.php');
include('../../../wfunction.php');
include('../gerenciar/funcoes.php');
include('../../../classes/PrestadorServicoClass.php');

$objPrestador = new PrestadorServico();

$id_prestador = $_REQUEST['id'];

if(!empty($id_prestador)){
    $prestador = getPrestador($id_prestador);

    $tipo_servico = $prestador['id_cnae'];
}else{
    $tipo_servico = null;
}

$id = $_REQUEST['layout'];
$res = $objPrestador->getLayoutContratos($id, $tipo_servico);

$conteudo = $res['conteudo'];

if(!empty($id_prestador)){
    $valor_contrato = (!empty($prestador['valor'])) ? number_format($prestador['valor'], 2, ',', '.') : NULL; 
    $valor_contrato .= " (";
    $valor_contrato .= (!empty($prestador['valor'])) ? valor_extenso($prestador['valor']) : '0.00';
    $valor_contrato .= ")";
    
    $data_atual = date('d') . " de " . mesesArray(date('m')) . " de " . date('Y');
    
    $array_dados = array(
        "[[Contratante]]"                                   =>  $prestador['empresa_razao'],
        "[[CNPJ do Contratante]]"                           =>  $prestador['empresa_cnpj'],
        "[[Endereço do Contratante]]"                       =>  $prestador['empresa_endereco'],
        "[[Bairro do Contratante]]"                         =>  $prestador['empresa_bairro'],
        "[[Cidade do Contratante]]"                         =>  $prestador['empresa_cidade'],
        "[[Estado do Contratante]]"                         =>  $prestador['empresa_uf'],
        "[[Nome fantasia da contratada]]"                   =>  $prestador['nome_fantasia'],
        "[[CNPJ da contratada]]"                            =>  $prestador['cnpj'],
        "[[Endereço da contratada]]"                        =>  $prestador['endereco'],
        "[[Município onde será executado o serviço]]"       =>  $prestador['municipio'],
        "[[Valor do contrato]]"                             =>  $valor_contrato,
        "[[Banco da contratada]]"                           =>  $prestador['nome_banco'],
        "[[Agência da contratada]]"                         =>  $prestador['agencia'],
        "[[Conta da contratada]]"                           =>  $prestador['conta'],
        "[[Cidade]]"                                        =>  $prestador['cidade'],
        "[[Estado]]"                                        =>  $prestador['estado'],
        "[[Data inicial do contrato]]"                      =>  $prestador['contratado_em'],
        "[[Data atual]]"                                    =>  $data_atual
    );
    
    foreach($array_dados as $i => $dados){
        $conteudo = str_replace($i, $dados, $conteudo);
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <title>:: Intranet :: <?php echo $res['nome']; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link rel="shortcut icon" href="../../../favicon.ico">
        <style>
            * { 
                margin: 0; 
                padding: 0; 
            }
            .m-b-50{
                margin-bottom: 30px;
            }            
            .m-t-50{
                margin-top: 50px;
            }            
            @media print {
                #_logo{ 
                    width: 20%;
                }
                #_logoeduc{
                    width: 75%;
                }
            }
        </style>
        <link href="../../../resources/css/bootstrap.css" rel="stylesheet" type="text/css">
        <link href="../../../resources/css/bootstrap-theme.css" rel="stylesheet" type="text/css">
        <link href="../../../resources/css/font-awesome.min.css" rel="stylesheet">
        <link href="../../../resources/css/style-print.css" rel="stylesheet">
        <script src="../../../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../../../resources/js/print.js" type="text/javascript"></script>

    </head>
    <body>
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container-fluid">
                <div class="text-center"> 
                    <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                    <!--<a href="index.php" class="btn btn-info navbar-btn"><i class="fa fa-home"></i> Principal</a>-->
                    <a class="btn btn-default" href="javascript:history.back()"><i class="fa fa-reply"></i> Voltar</a>
                </div>
            </div>
        </nav>
        <div class="pagina">
            <div class="row m-t-50 m-b-50">
                <div class="col-lg-10">
                    <img class="pull-left" id="_logoeduc" src="../../../imagens/logo_educ.jpg">
                </div>
                <div class="col-lg-2">
                    <img class="pull-right" id="_logo" src="../../../imagens/logomaster1.gif" alt="logo">
                </div>
            </div>
            
            <?php echo $conteudo; ?>
        </div>
    </body>
</html>