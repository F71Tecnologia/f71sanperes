<?php

if (isset($_GET['download']) && !empty($_GET['download'])) {
    $file = $_GET['download'];
    $dirFile = 'arquivos/grrf/' . $file;
    header("Content-Type: application/save");
    header("Content-Length:" . filesize($dirFile));
    header('Content-Disposition: attachment; filename="GRRF.re"');
    header("Content-Transfer-Encoding: binary");
    header('Expires: 0');
    header('Pragma: no-cache');
    $fp = fopen("$dirFile", "r");
    fpassthru($fp);
    fclose($fp);
    exit();
}

//
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

include '../../conn.php';
include '../../wfunction.php';
include '../../classes/construcaoTXT.php';
include 'classes/Regiao.class.php';
include 'classes/Clt.class.php';
include 'classes/Curso.class.php';
include 'classes/Projeto.class.php';
include 'classes/Grrf.class.php';
include 'classes/DaoMain.class.php';
include 'classes/DaoGrrf.class.php';
include 'classes/DaoClt.class.php';
include_once 'funcoes_grrf.php';
include "../../classes/LogClass.php";
$log = new Log();

$log->gravaLog('Relat�rio e Impostos', "GRRF Gerado");



$usuario = carregaUsuario();


$dataRecolhimento = isset($_REQUEST['data']) ? $_REQUEST['data'] : date('d/m/Y');
$mes = isset($_REQUEST['mes']) ? $_REQUEST['mes'] : NULL;
$ano = isset($_REQUEST['ano']) ? $_REQUEST['ano'] : NULL;
$regiao = isset($_REQUEST['regiao']) ? $_REQUEST['regiao'] : NULL;
$projeto = isset($_REQUEST['projeto']) ? $_REQUEST['projeto'] : NULL;
$idCltArray = isset($_REQUEST['clt']) ? $_REQUEST['clt'] : NULL;
$valorBaseInformado = isset($_REQUEST['valor_base_informado']) ? $_REQUEST['valor_base_informado'] : '0';
$post_cbo = isset($_REQUEST['cbo']) ? $_REQUEST['cbo'] : '';
$post_id_rescisao = isset($_REQUEST['id_rescisao']) ? $_REQUEST['id_rescisao'] : '';
$pegar_salario_anterior = ((isset($_REQUEST['mes_anterior'])) && ($_REQUEST['mes_anterior'] == 'on' )) ? TRUE :FALSE;




if (!is_array($idCltArray)) {
    $idCltArray = array($idCltArray => $idCltArray);
}
foreach ($idCltArray as $idClt) {

    $daoClt = new DaoClt();


    $clt = new Clt();

    $regiao = new Regiao();

    $clt->setIdClt($idClt);


    $row_clt = $daoClt->buscaClt($clt);

    $regiao->setIdRegiao($row_clt['id_regiao']);

    $clt->setRegiao($regiao);

    $projeto = new Projeto();
    $projeto->setIdProjeto($row_clt['id_projeto']);

    $clt->setProjeto($projeto);
    
    $curso = new Curso();
    $curso->setIdCurso($row_clt['id_curso']);

    $clt->setCurso($curso);

    if ($post_cbo == '') {
        $sobrescreveCodCbo = array('5425' => '5512');
//        $sobrescreveCodCbo = array();
        $row_cbo = $daoClt->getCbo($clt, $sobrescreveCodCbo);
    } else {
        $row_cbo['cod'] = $post_cbo;
    }

//    var_dump($row_cbo);
//    echo '<br>';

    $debugar = TRUE;
    $display = ' none ';
    if ($debugar) {
        $display = ' block ';
    }

//    echo '<h3 style="display: '.$display.';" >CBO: '.$row_cbo['cod'].'</h3>';


    
//    $curso = new Curso();
//    $curso->setIdCurso($row_clt['id_curso']);
    

    $row_empresa = $daoClt->buscaEmpresa($clt);

    $grrf = new Grrf();
    $grrf->setDataRecolhimento($dataRecolhimento)
            ->setMes($mes)
            ->setAno($ano)
            ->setRegiao($regiao)
            ->setClt($clt)
            ->setValorBaseInformado($valorBaseInformado);

    $dao = new DaoGrrf($grrf);
    $row_rescisao = $dao->buscaRescisao($post_id_rescisao);
    $row_rescisao = $row_rescisao[0]; // gambi ??
    
    $sqlMovimento = "SELECT * FROM rh_movimentos_rescisao WHERE status != 0, id_rescisao = $post_id_rescisao";
    $qryMovimento = mysql_query($sqlMovimento);
    while($rowMovimento = mysql_fetch_assoc($qryMovimento)){
        $arrayMovimentos[$rowMovimento['id_mov']] += $rowMovimento;
    }
    
    $obj = new txt();

    $row_empresa['cnpj_matriz_vasco'] = '07813739000161'; //PROVIS�RIO
    $row_empresa['cnpj_matriz'] = $row_empresa['cnpj']; //PROVIS�RIO
    
    $dados['empresa'] = array();
    $dados['empresa']['tipo_inscricao'] = '1'; // 1 - CNPJ
    $dados['empresa']['cnpj'] = $obj->completa($obj->limpar($row_empresa['cnpj_matriz']), 14);
    $dados['empresa']['razao'] = $obj->completa(RemoveCaracteres(RemoveAcentos($row_empresa['razao'])), 30);
    $dados['empresa']['responsavel'] = $obj->completa(RemoveCaracteres(RemoveAcentos($row_empresa['responsavel'])), 20);
    $row_empresa['endereco'] = preg_replace("/(  +)/i", " ", $row_empresa['endereco']);
    $dados['empresa']['endereco_empresa'] = $obj->completa(RemoveCaracteres(RemoveAcentos($row_empresa['endereco'])), 50);
    
    
    $dados['empresa']['bairro'] = $obj->completa(RemoveCaracteres(RemoveAcentos($row_empresa['bairro'])), 20);
    $dados['empresa']['cep'] = $obj->completa($obj->limpar($row_empresa['cep']), 8);
    $dados['empresa']['cidade'] = $obj->completa(RemoveCaracteres(RemoveAcentos($row_empresa['cidade'])), 20);
    $dados['empresa']['uf'] = $obj->completa($row_empresa['uf'], 2);
    $dados['empresa']['tel'] = $obj->completa($obj->limpar($row_empresa['tel']), 12, '0', 'antes');
    $dados['empresa']['email'] = $obj->completa($row_empresa['email'], 60);
    $dados['empresa']['data_recolhimento'] = $obj->completa($obj->limpar($dataRecolhimento), 8);
    $dados['empresa']['cnae2'] = $obj->completa($row_empresa['cnae2'], 7);
    $dados['empresa']['cnae2'] = $obj->completa($row_empresa['cnae2'], 7);
    $dados['empresa']['fpas'] = $obj->completa($row_empresa['fpas'], 3);
    $dados['empresa']['tomador_tipo'] = '0';
    $dados['empresa']['tomador_cnpj'] = '00000000000000';


// inicio da linha 1
    $obj->dados('00'); // TIPO DE REGISTRO
    $obj->filler(51); // BRANCOS
    $obj->dados('2'); // TIPO DE REMESSA (2 - GRRF)
    $obj->dados($dados['empresa']['tipo_inscricao']); // TIPO DE INSCRIÇÃO (1 - CNPJ)
    $obj->dados($row_empresa['cnpj_matriz_vasco']); // INSCRIÇÃO DO RESPONS�?VEL (1 - CNPJ)
    $obj->dados($dados['empresa']['razao']);
    $obj->dados($dados['empresa']['responsavel']); // NOME RESPONSAVEL 
    
    $dados['empresa']['endereco_empresa'] = $obj->completa(preg_replace("/(  +)/i", " ", RemoveCaracteres(RemoveAcentos( $obj->nome($row_empresa['endereco'])))), 50);
    
//    var_dump($dados['empresa']['endereco_empresa']);
    
    $obj->dados($dados['empresa']['endereco_empresa']); // RUA 
    $obj->dados($dados['empresa']['bairro']); // BAIRRO
    $obj->dados($dados['empresa']['cep']); // CEP
    $obj->dados($dados['empresa']['cidade']); // CIDADE
    $obj->dados($dados['empresa']['uf']); // UNIDADE DA FEDERAÇÃO
    $obj->dados($dados['empresa']['tel']); // TELEFONE
    $obj->dados($dados['empresa']['email']); // ENDEREÇO INTERNET CONTATO
    $obj->dados($dados['empresa']['data_recolhimento']); // ENDEREÇO INTERNET CONTATO
    $obj->filler(60); // BRANCOS
    $obj->fechalinha('*'); // FECHA LINHA
// inicio da linha 2

    $dados['empresa']['razao'] = $obj->completa(RemoveCaracteres(RemoveAcentos($row_empresa['razao'])), 40);


    $obj->dados('10'); // CAMPO OBRIGATORIO (SEMPRE 10)
    $obj->dados($dados['empresa']['tipo_inscricao']); // TIPO DE INSCRIÇÃO (1 - CNPJ)
    $obj->dados($dados['empresa']['cnpj']); // INSCRIÇÃO DO RESPONS�?VEL (1 - CNPJ)
    $obj->dados($obj->completa('', 36, '0')); // ZEROS
    $obj->dados($dados['empresa']['razao']); // NOME EMPRESA / RAZÃO
    
//    $dados['empresa']['endereco_empresa'] = preg_replace("/(  +)/i", " ",     $obj->dados($dados['empresa']['endereco_empresa']));
    
//    $obj->dados($obj->completa(preg_replace("/(  +)/i", " ",  $dados['empresa']['endereco_empresa']), 50, ' ')); // RUA , Nº
    $obj->dados($dados['empresa']['endereco_empresa']); // RUA , Nº
//    var_dump($dados['empresa']['endereco_empresa']);
    $obj->dados($dados['empresa']['bairro']); // BAIRRO
    $obj->dados($dados['empresa']['cep']); // CEP
    $obj->dados($dados['empresa']['cidade']); // CIDADE
    $obj->dados($dados['empresa']['uf']); // UNIDADE DA FEDERAÇÃO 
    $obj->dados($dados['empresa']['tel']); // TELEFONE
    $obj->dados($dados['empresa']['cnae2']); // CNAE DA EMPRESA
    $obj->dados('1'); // SIMPLES, NÃO OPTANTE
    $obj->dados($dados['empresa']['fpas']); // SIMPLES, NÃO OPTANTE
    $obj->filler(143); // BRANCOS
    $obj->fechalinha('*'); // FECHA LINHA


    $dados['trabalhador'] = array();
    $dados['trabalhador']['pis'] = $obj->completa($obj->limpar($row_clt['pis']), 11);
    $dados['trabalhador']['data_admissao'] = $obj->limpar(Data($row_clt['data_entrada']));




    $dados['trabalhador']['nome'] = $obj->completa($obj->nome($row_clt['nome']), 70);
    $dados['trabalhador']['numero_ctps'] = $obj->completa($obj->limpar($row_clt['numero_ctps']), 7, '0', 'antes');
    $dados['trabalhador']['serie_ctps'] = $obj->completa($obj->limpar($row_clt['serie_ctps']), 5, '0', 'antes');
    $dados['trabalhador']['sexo'] = get_cod_genero($row_clt);
    $dados['trabalhador']['escolaridade'] = $obj->completa($row_clt['escolaridade'], 2, '0', 'antes');
    $dados['trabalhador']['data_nasci'] = $obj->limpar(Data($row_clt['data_nasci']));
    $dados['trabalhador']['horas_trabalhadas'] = $obj->completa('40', 2, '0', 'antes');
    $dados['trabalhador']['cbo'] = $obj->completa($obj->limpar(substr($row_cbo['cod'], 0, 4)), 6, '0', 'antes');
    $dados['trabalhador']['codigo_movimentacao'] = $obj->completa($row_rescisao['cod_movimentacao'], 2);
    $dados['trabalhador']['data_demissao'] = $obj->limpar(Data($row_rescisao['data_demi']));
    $dados['trabalhador']['codigo_saque'] = $obj->completa($row_rescisao['codigo_saque'], 3, ' ');
    $dados['trabalhador']['aviso'] = trim($row_rescisao['aviso_codigo']);
    
    


    $pensao_alimenticia = get_pensao_alimenticia($row_clt, $row_rescisao);
    $banco = '0';
    $agencia = '0';
    $conta = '0';

    $dados['trabalhador']['data_aviso'] = $obj->limpar(Data(get_cod_data_aviso($row_rescisao)));
    $dados['trabalhador']['data_homologacao'] = $obj->completa('', 8);
    $dados['trabalhador']['valor_dissidio'] = $obj->completa('', 15, '0');

    
    
    if ($pegar_salario_anterior) {
        $remuneracao_mes_anterior = get_remuneracao_mes_anterior($row_rescisao);
    } else {
        $remuneracao_mes_anterior = '0';
    }
//    var_dump($remuneracao_mes_anterior);
//    echo '<br>';

    $dados['trabalhador']['remuneracao_mes_anterior'] = $obj->completa($obj->limpar($remuneracao_mes_anterior), 15, '0', 'antes');
    
//    echo '<pre>';
//    print_r($row_rescisao);
//    echo '</pre>';
//    
//    exit();
    
    $remuneracao_mes = get_remuneracao_mes($row_rescisao);
    
//    var_dump($remuneracao_mes['base_fgts']);
//    exit();

//    echo '<div style="display: '.$display.';" >';
//    echo $remuneracao_mes['calculo'];
//    echo '</div>';
//    echo '<pre>';
//    print_r($remuneracao_mes['incidencias']);
//    echo '</pre>';

    $dados['trabalhador']['remuneracao_mes_rescisao'] = $obj->completa($obj->limpar($remuneracao_mes['base_fgts']), 15, '0', 'antes');


    $aviso_previo_indenizado = get_aviso_previo_indenizado($row_rescisao);
    $aviso_previo_indenizado = ($aviso_previo_indenizado<=0) ? '13' : $aviso_previo_indenizado;
    
    $dados['trabalhador']['aviso_previo_indenizado'] = $obj->completa($obj->limpar($aviso_previo_indenizado), 15, '0', 'antes');
    $dados['trabalhador']['pensao_alimenticia'] = $pensao_alimenticia['flag'];
    $dados['trabalhador']['percentual_pensao_alimenticia'] = $obj->completa($pensao_alimenticia['percentual'], 5, '0');
    $dados['trabalhador']['valor_pensao_alimenticia'] = $obj->completa($pensao_alimenticia['valor'], 15, '0');
    $dados['trabalhador']['cpf'] = $obj->limpar($row_clt['cpf']);
    $dados['trabalhador']['banco'] = $obj->completa($banco, 3, '0');
    $dados['trabalhador']['agencia'] = $obj->completa($agencia, 4, '0');
    $dados['trabalhador']['conta'] = $obj->completa($conta, 13, '0');
    $dados['trabalhador']['valor_base_informado'] = $obj->completa($obj->limpar($valorBaseInformado), 15, '0', 'antes');
    $dados['trabalhador']['aviso_codigo'] = $row_rescisao['aviso_codigo'];

    
    $dados['trabalhador']['saldo_salario'] = $obj->completa($obj->limpar($row_rescisao['saldo_salario']), 15, '0', 'antes');
    $dados['trabalhador']['dt_salario'] = $obj->completa($obj->limpar($row_rescisao['dt_salario']), 15, '0', 'antes');
    $dados['trabalhador']['ferias_vencidas'] = $obj->completa($obj->limpar($row_rescisao['ferias_vencidas']), 15, '0', 'antes');
    $dados['trabalhador']['ferias_pr'] = $obj->completa($obj->limpar($row_rescisao['ferias_pr']), 15, '0', 'antes');
    $dados['trabalhador']['umterco_fp'] = $obj->completa($obj->limpar($row_rescisao['umterco_fp']), 15, '0', 'antes');
    $dados['trabalhador']['sal_familia'] = $obj->completa($obj->limpar($row_rescisao['sal_familia']), 15, '0', 'antes');
    $dados['trabalhador']['insalubridade'] = $obj->completa($obj->limpar($row_rescisao['insalubridade']), 15, '0', 'antes');
    $dados['trabalhador']['inss_ss'] = $obj->completa($obj->limpar($row_rescisao['inss_ss']), 15, '0', 'antes');
    $dados['trabalhador']['inss_dt'] = $obj->completa($obj->limpar($row_rescisao['inss_dt']), 15, '0', 'antes');
    $dados['trabalhador']['ir'] = $obj->completa($obj->limpar($row_rescisao['ir_ss'] + $row_rescisao['ir_dt']), 15, '0', 'antes');
    $dados['trabalhador']['avos_dt'] = $obj->completa($obj->limpar($row_rescisao['avos_dt']), 2, '0', 'antes');
    $dados['trabalhador']['avos_fp'] = $obj->completa($obj->limpar($row_rescisao['avos_fp']), 2, '0', 'antes');
    
if(FALSE){
// inicio da linha 3
    $obj->dados('40'); // tipo de registro 
    $obj->dados($dados['empresa']['tipo_inscricao']); // TIPO DE INSCRIÇÃO (1 - CNPJ)
    $obj->dados($dados['empresa']['cnpj']); // INSCRI��O DA EMPRESA
    $obj->dados($dados['empresa']['tomador_tipo']); // tipo de inscri��o - tomador obra const. civil (n�o informado)
    $obj->dados($dados['empresa']['tomador_cnpj']); // tipo de inscri��o - tomador obra const. civil (n�o informado)
    $obj->dados($dados['trabalhador']['pis']); // PIS
    $obj->dados($dados['trabalhador']['data_admissao']); // data admiss�o
    $obj->dados('01'); // categoria do empregador (01 - empregado)
    $obj->dados($dados['trabalhador']['nome']); // Nome do trabalhador
    $obj->dados($dados['trabalhador']['numero_ctps']); // CTPS
    $obj->dados($dados['trabalhador']['serie_ctps']); // SERIE CTPS
    $obj->dados($dados['trabalhador']['sexo']); // GENERO
    $obj->dados($dados['trabalhador']['escolaridade']); // ESCOLARIDADE
    $obj->dados($dados['trabalhador']['data_nasci']); // data nascimento
    $obj->dados($dados['trabalhador']['horas_trabalhadas']); // quantidade de horas trabalhadas por semana
    $obj->dados($dados['trabalhador']['cbo']); // CODIGO CBO
    $obj->dados($dados['trabalhador']['data_admissao']); // data de op��o REPETIDOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO *****************
    $obj->dados($dados['trabalhador']['codigo_movimentacao']); // Codigo de movimento
    $obj->dados($dados['trabalhador']['data_demissao']); // data de movimenta��o
    $obj->dados($dados['trabalhador']['codigo_saque']); // c�digo de saque
    $obj->dados($dados['trabalhador']['aviso_codigo']); // Aviso pr�vio (1 - trabalhado 2- Indenizado 3-Ausencia/Dispensa)
    $obj->dados($dados['trabalhador']['data_aviso']); // data in�cio do aviso previo
    $obj->dados('S'); // Reposi��o de Vaga
    $obj->dados($dados['trabalhador']['data_homologacao']); // data da HOmologa��o Diss�dio Coletivo
    $obj->dados($dados['trabalhador']['valor_dissidio']); // Valor Diss�dio
    $obj->dados($dados['trabalhador']['remuneracao_mes_anterior']); // Remunera��o mes anterior
    $obj->dados($dados['trabalhador']['remuneracao_mes_rescisao']); // Remunera��o mes da rescis�o        
    $obj->dados($dados['trabalhador']['aviso_previo_indenizado']); // Aviso Pr�vio Indenizado
    $obj->dados($dados['trabalhador']['pensao_alimenticia']); // Indicativo Pens�o aliminticia
    $obj->dados($dados['trabalhador']['percentual_pensao_alimenticia']); // Percentual da pens�o alimenticia
    $obj->dados($dados['trabalhador']['valor_pensao_alimenticia']); // Valor da P�ns�o alimenticia
    $obj->dados($dados['trabalhador']['cpf']); // CPF
    $obj->dados($dados['trabalhador']['banco']); //  banco da conta do trabalhador \(N�o informado porque existem N de agencias com mais de 4 digitos cadastradas)
    $obj->dados($dados['trabalhador']['agencia']);  // Agencia
    $obj->dados($dados['trabalhador']['conta']); // Conta
    $obj->dados($dados['trabalhador']['valor_base_informado']); // Saldo para Fins Rescis�rios
    $obj->filler(39); // brancos
    $obj->fechalinha('*'); // FECHA LINHA
// inicio de linha 4
}  
    
    // inicio da linha 3
    $obj->dados('41'); // tipo de registro 
    $obj->dados($dados['empresa']['tipo_inscricao']); // TIPO DE INSCRIÇÃO (1 - CNPJ)
    $obj->dados($dados['empresa']['cnpj']); // INSCRI��O DA EMPRESA
    $obj->dados($dados['empresa']['tomador_tipo']); // tipo de inscri��o - tomador obra const. civil (n�o informado)
    $obj->dados($dados['empresa']['tomador_cnpj']); // tipo de inscri��o - tomador obra const. civil (n�o informado)
    $obj->dados($dados['trabalhador']['pis']); // PIS
    $obj->dados($dados['trabalhador']['data_admissao']); // data admiss�o
    $obj->dados('01'); // categoria do empregador (01 - empregado)
    $obj->dados($obj->completa('', 2, '0', 'antes')); // Dias Trabalhados
    $obj->dados($obj->completa($dados['trabalhador']['saldo_salario'], 15,'0', 'antes')); // Saldo Salario 
    $obj->dados($obj->completa($dados['trabalhador']['avos_dt'], 2, '0', 'antes')); // 13 sal�rio 12 avos
    $obj->dados($obj->completa($dados['trabalhador']['dt_salario'], 15, '0', 'antes')); // 13 sal�rio 
    $obj->dados($obj->completa('', 2, '0', 'antes')); // 13 sal�rio indenizado 12 avos
    $obj->dados($obj->completa('', 15, '0', 'antes')); // 13 sal�rio indenizado
    $obj->dados($obj->completa($dados['trabalhador']['ferias_vencidas'], 15, '0', 'antes')); // F�rias Vencidas
    $obj->dados($obj->completa($dados['trabalhador']['avos_fp'], 2, '0', 'antes')); // F�rias Proporcionais
    $obj->dados($obj->completa($dados['trabalhador']['ferias_pr'], 15, '0', 'antes')); // F�rias Proporcionais
    $obj->dados($obj->completa($dados['trabalhador']['umterco_fp'], 15, '0', 'antes')); // 1/3 F�rias 
    $obj->dados($obj->completa('', 2, '0', 'antes')); // Sal�rio Fam�lia Dias
    $obj->dados($obj->completa($dados['trabalhador']['sal_familia'], 15, '0', 'antes')); // Sal�rio Fam�lia Valor
    $obj->dados($obj->completa($arrayMovimentos[66]['valor'], 15, '0', 'antes')); // Adicional Noturno
    $obj->dados($obj->completa('', 15, '0', 'antes')); // Comiss�es
    $obj->dados($obj->completa($arrayMovimentos[192]['valor'], 15, '0', 'antes')); // Gratifica��o
    $obj->dados($obj->completa('', 3, '0', 'antes')); // Horas extras quantidade
    $obj->dados($obj->completa($arrayMovimentos[287]['valor'] + $arrayMovimentos[364]['valor'], 15, '0', 'antes')); // Horas extras valor
    $obj->dados($obj->completa($dados['trabalhador']['insalubridade'], 15, '0', 'antes')); // Adicional insalubridade/periculosidade
    $obj->dados($obj->completa($dados['trabalhador']['inss_ss'], 15, '0', 'antes')); // Dedu��o Previd�ncia
    $obj->dados($obj->completa($dados['trabalhador']['inss_dt'], 15, '0', 'antes')); // Dedu��o 13� sal�rio
    $obj->dados($obj->completa('', 15, '0', 'antes')); // Dedu��o adiantamentos
    $obj->dados($obj->completa($dados['trabalhador']['ir'], 15, '0', 'antes')); // Dedu��o imposto de renda
    $obj->dados($obj->completa(' ', 53)); // Brancos
    $obj->fechalinha('*');
    
    $obj->dados('90');
    $obj->dados($obj->completa('', 51, '9'));
    $obj->filler(306);
    $obj->fechalinha('*');

    //GRAVANDO *********

//    $data_save['remuneracao_mes_anterior'] = $remuneracao_mes_anterior;
//    $data_save['remuneracao_mes'] = $remuneracao_mes;
//    $data_save['aviso_previo_indenizado'] = $aviso_previo_indenizado;
    $id_rescisao = $row_rescisao['id_recisao'];
    $cbo = $row_cbo['cod']; //IMPORTANTE CASO SEJA UM NOVO CBO (SOBREESCRITO)

    $valorBaseInformado = str_replace('.', '', $valorBaseInformado);
    $valorBaseInformado = str_replace(',', '.', $valorBaseInformado);
    $valorBaseInformado = str_replace(',', '', $valorBaseInformado);
    
//    echo '<br>'.$valorBaseInformado.'<br>';
    
    $incidencias = json_encode($remuneracao_mes['incidencias']);
    $base_fgts = json_encode($remuneracao_mes['base_fgts']);

    $sql_update = "UPDATE grrf SET `status`=0 WHERE id_clt='$idClt' AND mes='$mes' AND ano='$ano'";
//    echo $sql_update.'<br>';
    mysql_query($sql_update);
    $sql_grrf = "INSERT INTO grrf (id_clt, mes, ano, id_regiao, id_projeto, user, valor_informado_empresa, cbo,  incidencias, mes_anterior_rescisao, mes_rescisao, id_rescisao) VALUES ('$idClt','$mes','$ano','$regiao->idRegiao','$projeto->idProjeto','$_COOKIE[logado]', '$valorBaseInformado', '$cbo', '$incidencias','$remuneracao_mes_anterior','$base_fgts',$id_rescisao)";
//    echo $sql_grrf.'<br>';
    mysql_query($sql_grrf);
    $grrf_id = mysql_insert_id();
    $log->gravaLog('Relat�rio e Impostos', "GRRF Gerado: ID{$grrf_id}");
    
    // FIM DO INSERT
    
    
    
    

// Gera o arquivo
    $diretorio = 'arquivos/grrf/';

    if (!is_dir($diretorio)) {
        mkdir($diretorio);
    }
    $nome = $idClt.'_'. $grrf_id.'.re';
//    $nome = 'GRRF.re';
    $caminho = $diretorio . $nome;
    if (file_exists($caminho))
        unlink($caminho);
    $fp = fopen($caminho, "a");
    $escreve = fwrite($fp, $obj->arquivo);
    fclose($fp);
    ?>

    

    <?php //= $idClt . ' - ' . $row_clt['nome']; ?>
        <?php

        echo ' <a href="?download=' . $nome . '" style="font-size: 18px;" >Baixar arquivo</a><br>';

        $todos_clts['empresas'][$dados['empresa']['cnpj']] = $dados['empresa'];
//    $todos_clts[$dados['empresa']['cnpj']]['trabalhador'] = $dados['trabalhador'];
    }
//print_helper($todos_clts);
    exit();
//$main = new Main($grrf);