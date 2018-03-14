<?php
include('../conn.php');
require('../rh/fpdf/fpdf.php');
require('../classes/InformeRendimentoClass.php');
include('../wfunction.php');

$usuarioW = carregaUsuario();
$msg = null;
$inf = new InformeRendimentoClass($usuarioW['id_master']);

if (validate($_REQUEST['gerar'])) {
    $inf->setAnoBase($_REQUEST['ano']);
    $nomeFile = "Informes_{$_REQUEST['pro']}_".date("d-m-Y");
    $inf->setFileName($nomeFile.".pdf");
    $inf->iniciaFpdf();
    $qr_participante = $inf->getParticipantes($_REQUEST['pro'], $_REQUEST['id_reg']);
    
    while ($participante = mysql_fetch_assoc($qr_participante)) {
        $inf->setTipo($participante['tipo_contratacao']);
        $meses = array();
        $valorMes = array();
        $multVinculos['total'] = null;
        $nome = $participante['nome'];
        $multVinculos = $inf->verificaDuplicidade(str_replace(array(".","-"),"",$participante['cpf']),$participante['tipo_contratacao']);
        
        if($participante['tipo_contratacao'] == 2){
            $campoID = "id_clt";
        }else{
            $campoID = "id_autonomo";
        }
        
        if($multVinculos['total'] > 1){
            $ARidClt = null;
            foreach($multVinculos['rs'] as $multClt){
                $ARidClt[] = $multClt[$campoID];
            }
            $id = implode(",",$ARidClt);
        }else{
            $id = $participante['id_clt'];
        }
        
        $inf->setParticipante($participante);
        
        //RODA A QUERY E PEGA OS VALORES E COLOCA NAS VARIAVEIS PUBLICAS
        $inf->getDadosFolhas($id);
        $inf->getDadosDecimoTerceiro($id);
        
        //SO PROCURA RESCISÃO E FERIAS SE FOR DO TIPO CLT
        if ($participante['tipo_contratacao'] == '2') {
            $inf->getDadosFerias($id);      //PROCURA FÉRIAS PARA POPULAR AS VARIAVEIS PUBLICAS
            $inf->getDadosExtra($id);
            //$inf->getDadosPlanoSaude($id);
            $inf->getDadosPensaoAlimenticia($id);
            
            if($inf->anoBase >= 2015 || $id == 6938){
                $inf->getDadosRescisao2015($id);
            }else{
                $inf->getDadosRescisao($id);    //PROCURA RESCISAO PARA POPULAR AS VARIAVEIS PUBLICAS
            }
            
        }
        
        //NORMALIZA VALORES, REMOVENDO VALORES NEGATIVOS
        $inf->normalizaValores();
        
        //VALIDA GERAÇÃO DO PDF, CASO TODOS OS VALORES SEJAM VAZIOS, NÃO GERA PDF PARA TAL PESSOA
        $geraPDF = $inf->validaValores();

        if ($geraPDF) {
            $inf->geraPdf();
        }

        $inf->limpaVariaveis();
    }
    
    $inf->finalizaPdf();
    $inf->downloadFile();
    
    exit;
}
?>
