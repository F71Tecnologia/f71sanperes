<?php
include('../conn.php');
require('../rh/fpdf/fpdf.php');
require('../classes/InformeRendimentoClass.php');
include('../wfunction.php');

$usuarioW = carregaUsuario();
$msg = null;

if (validate($_REQUEST['gerar'])) {
    $inf = new InformeRendimentoClass($_REQUEST['cooperativa'],"cooperativa"); 
    $inf->setAnoBase($_REQUEST['ano']);
    $inf->setTipo($_REQUEST['tipo']);
    $inf->iniciaFpdf();
    $qr_participante = $inf->getParticipante($_REQUEST['id']);
    
    while ($participante = mysql_fetch_assoc($qr_participante)) {
        $meses = array();
        $valorMes = array();
        $id = $participante['id_clt'];
        $nome = $participante['nome'];
        $nomeFile = "Informe_".normalizaNometoFile($nome);
        $inf->setParticipante($participante);
        
        //RODA A QUERY E PEGA OS VALORES E COLOCA NAS VARIAVEIS PUBLICAS
        $inf->getDadosFolhas($id);
        
        //NORMALIZA VALORES, REMOVENDO VALORES NEGATIVOS
        $inf->normalizaValores();
        
        //VALIDA GERAÇÃO DO PDF, CASO TODOS OS VALORES SEJAM VAZIOS, NÃO GERA PDF PARA TAL PESSOA
        $geraPDF = $inf->validaValores();

        if ($geraPDF) {
            $inf->setFileName($nomeFile.".pdf");
            $inf->geraPdf();
            $inf->finalizaPdf();
        }

        $inf->limpaVariaveis();
        
    }
    
    if ($geraPDF) {
        $inf->downloadFile();
        exit;
    }else{
        $msg = "Não foi encontrado valores para o Funcionário ({$nome}) no ano escolhido ({$_REQUEST['ano']})";
    }
    
//    
//    $id_cooperativa = $_REQUEST['cooperativa'];
//    
//    // Consultando a Folha
//    // Consultando a Folha Individual
//    $qr_folha_individual = mysql_query("SELECT * FROM folha_cooperado as A 
//        INNER JOIN  folhas as B
//        ON A.id_folha = B.id_folha
//        WHERE  A.id_autonomo = $id AND B.coop = $empregado[id_cooperativa] AND A.ano = $ano_base AND A.status = 3 AND B.status = 3;");
//    
//    print_r($inf);
//    exit();
//    
//    while ($folha_individual = mysql_fetch_assoc($qr_folha_individual)) {
//
//        if ($folha['terceiro'] == 1) {
//            $salario13 += $folha_individual['salario_liq'];
//        } else {
//            $salario += $folha_individual['salario'] + $folha_individual['adicional'];
//        }
//
//        $inss += $folha_individual['inss'];
//        $ir += $folha_individual['irrf'];
//        $ajuda_custo += $folha_individual['ajuda_custo'];
//    }
}


//DADOS PARA A PRIMEIRA TELA, ANTES DO POST
$optAnos = anosArray();
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date("Y") - 1;

$regiao = $_GET['id_reg'];
$projeto = $_GET['pro'];
$tipo = "";
$id_colaborador = "";
if (validate($_REQUEST['bol'])) {
    $tipo = 1;
    $id_colaborador = $_REQUEST['bol'];
} elseif (validate($_REQUEST['clt'])) {
    $tipo = 2;
    $id_colaborador = $_REQUEST['clt'];
} else {
    $tipo = 3;
    $id_colaborador = $_REQUEST['coo'];
}

$roMaster = montaQueryFirst("master", "nome", "id_master = {$usuarioW['id_master']}");
?>
<html>
    <head>
        <title>:: Intranet :: Informe de Rendimentos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../js/jquery.maskMOney_2.1.2.js" type="text/javascript"></script>
        <script src="../js/jquery.maskedinput.js" type="text/javascript"></script>

        <script src="../js/global.js" type="text/javascript"></script>
    </head>

    <body class="novaintra">
        <div id="content">
            <form action="" method="post" name="form1" id="form1">
                <div id="headerPrint">
                    <img src="../imagens/logomaster<?php echo $usuarioW['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <h2><?php echo $roMaster['nome'] ?></h2>
                    <h3>Informe de Rendimentos</h3>
                    <p class="clear"></p>
                </div>

                <br/>

                <fieldset>
                    <legend>Dados</legend>
                    <input type="hidden" name="tipo" id="tipo" value="<?php echo $tipo ?>" />
                    <input type="hidden" name="cooperativa" id="cooperativa" value="<?php echo $_REQUEST['cooperativa'] ?>" />
                    <input type="hidden" name="id" id="id" value="<?php echo $id_colaborador ?>" />
                    <p><label class="first">Ano:</label> <?php echo montaSelect($optAnos, $anoSel, "id='ano' name='ano'") ?></p>
                    <p class="controls"> <input type="submit" class="button" value="Gerar" name="gerar" /> </p>
                </fieldset>
                <br/>
                
                <?php 
                if($msg!==null){
                    echo "<div id='message-box' class='message-yellow'><p>{$msg}</p></div>";
                }?>
            </form>
        </div>
    </body>
</html>