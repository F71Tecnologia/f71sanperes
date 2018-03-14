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

$log->gravaLog('Relatório e Impostos', "GRRF Gerado");

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


    $row_clt = $daoClt->buscaClt($clt, $post_id_rescisao);

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
        //$row_cbo = $daoClt->getCbo($clt, $sobrescreveCodCbo);
        
        $sql = "
        SELECT C.cod FROM rh_clt A
        INNER JOIN curso B ON (A.id_curso = B.id_curso)
        INNER JOIN rh_cbo C ON (B.cbo_codigo = C.id_cbo)
        WHERE A.id_clt = $idClt
        LIMIT 1;";
        $qry = mysql_query($sql);
        $row_cbo = mysql_fetch_assoc($qry);
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
    
    if($_COOKIE['debug']==666){
        print_array('$row_rescisao');
        print_array($row_rescisao);
        //exit();
        
    }
    
    
    if ($row_rescisao['id_recisao'] == 435) { 
        $row_clt['cod_movimentacao'] = 'I1'; 
        $row_clt['codigo_saque'] = '01'; 
        $row_rescisao['aviso_codigo'] = 2;
    }
    
    $obj = new txt();

//    $row_empresa['cnpj_matriz_vasco'] = '07813739000161'; //PROVISÓRIO
    $row_empresa['cnpj_matriz'] = $row_empresa['cnpj']; //PROVISÓRIO
    
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
    $obj->dados($dados['empresa']['tipo_inscricao']); // TIPO DE INSCRIÃ‡ÃƒO (1 - CNPJ)
    $obj->dados($dados['empresa']['cnpj']); // INSCRIÃ‡ÃƒO DO RESPONSÃ?VEL (1 - CNPJ)
    $obj->dados($dados['empresa']['razao']);
    $obj->dados($dados['empresa']['responsavel']); // NOME RESPONSAVEL 
    
    $dados['empresa']['endereco_empresa'] = $obj->completa(preg_replace("/(  +)/i", " ", RemoveCaracteres(RemoveAcentos( $obj->nome($row_empresa['endereco'])))), 50);
    
//    var_dump($dados['empresa']['endereco_empresa']);
    
    $obj->dados($dados['empresa']['endereco_empresa']); // RUA 
    $obj->dados($dados['empresa']['bairro']); // BAIRRO
    $obj->dados($dados['empresa']['cep']); // CEP
    $obj->dados($dados['empresa']['cidade']); // CIDADE
    $obj->dados($dados['empresa']['uf']); // UNIDADE DA FEDERAÃ‡ÃƒO
    $obj->dados($dados['empresa']['tel']); // TELEFONE
    $obj->dados($dados['empresa']['email']); // ENDEREÃ‡O INTERNET CONTATO
    $obj->dados($dados['empresa']['data_recolhimento']); // ENDEREÃ‡O INTERNET CONTATO
    $obj->filler(60); // BRANCOS
    $obj->fechalinha('*'); // FECHA LINHA
// inicio da linha 2

    $dados['empresa']['razao'] = $obj->completa(RemoveCaracteres(RemoveAcentos($row_empresa['razao'])), 40);


    $obj->dados('10'); // CAMPO OBRIGATORIO (SEMPRE 10)
    $obj->dados($dados['empresa']['tipo_inscricao']); // TIPO DE INSCRIÃ‡ÃƒO (1 - CNPJ)
    $obj->dados($dados['empresa']['cnpj']); // INSCRIÃ‡ÃƒO DO RESPONSÃ?VEL (1 - CNPJ)
    $obj->dados($obj->completa('', 36, '0')); // ZEROS
    $obj->dados($dados['empresa']['razao']); // NOME EMPRESA / RAZÃƒO
    
//    $dados['empresa']['endereco_empresa'] = preg_replace("/(  +)/i", " ",     $obj->dados($dados['empresa']['endereco_empresa']));
    
//    $obj->dados($obj->completa(preg_replace("/(  +)/i", " ",  $dados['empresa']['endereco_empresa']), 50, ' ')); // RUA , NÂº
    $obj->dados($dados['empresa']['endereco_empresa']); // RUA , NÂº
//    var_dump($dados['empresa']['endereco_empresa']);
    $obj->dados($dados['empresa']['bairro']); // BAIRRO
    $obj->dados($dados['empresa']['cep']); // CEP
    $obj->dados($dados['empresa']['cidade']); // CIDADE
    $obj->dados($dados['empresa']['uf']); // UNIDADE DA FEDERAÃ‡ÃƒO 
    $obj->dados($dados['empresa']['tel']); // TELEFONE
    $obj->dados($dados['empresa']['cnae2']); // CNAE DA EMPRESA
    $obj->dados('1'); // SIMPLES, NÃƒO OPTANTE
    $obj->dados($dados['empresa']['fpas']); // SIMPLES, NÃƒO OPTANTE
    $obj->filler(143); // BRANCOS
    $obj->fechalinha('*'); // FECHA LINHA


    $dados['trabalhador'] = array();
    $dados['trabalhador']['pis'] = $obj->completa($obj->limpar($row_clt['pis']), 11);
    $dados['trabalhador']['data_admissao'] = $obj->limpar(Data($row_clt['data_entrada']));




    $dados['trabalhador']['nome'] = $obj->completa($obj->nome($row_clt['nome']), 70);
    $dados['trabalhador']['numero_ctps'] = $obj->completa($obj->limpar($row_clt['numero_ctps']), 7, '0', 'antes');
    $dados['trabalhador']['serie_ctps'] = $obj->completa($obj->limpar(preg_replace("/[^0-9]/", "", $row_clt['serie_ctps'])), 5, '0', 'antes');
    $dados['trabalhador']['sexo'] = get_cod_genero($row_clt);
    $dados['trabalhador']['escolaridade'] = $obj->completa($row_clt['escolaridade'], 2, '0', 'antes');
    $dados['trabalhador']['data_nasci'] = $obj->limpar(Data($row_clt['data_nasci']));
    $dados['trabalhador']['horas_trabalhadas'] = $obj->completa('40', 2, '0', 'antes');
    $dados['trabalhador']['cbo'] = $obj->completa($obj->limpar(substr($row_cbo['cod'], 0, 4)), 6, '0', 'antes');
    $dados['trabalhador']['codigo_movimentacao'] = $obj->completa($row_clt['cod_movimentacao'], 2);
    $dados['trabalhador']['data_demissao'] = $obj->limpar(Data($row_rescisao['data_demi']));
    $dados['trabalhador']['codigo_saque'] = $obj->completa($row_clt['codigo_saque'], 3, ' ');
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
    
    if(($row_rescisao['motivo'] == 61) && ($row_rescisao['aviso'] == 'trabalhado')){
        $remuneracao_mes['base_fgts'] += $row_rescisao['lei_12_506'];
    }
    
    if($_COOKIE['debug'] == 666){
        echo "LEI_12506: {$row_rescisao['lei_12_506']}";
        print_array("AAAAAAAAAAAAAAAAAAAAAAAAAA");
        print_array($remuneracao_mes);
    }
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
//    $aviso_previo_indenizado = ($aviso_previo_indenizado<=0) ? '13' : $aviso_previo_indenizado;
        if($_COOKIE['debug'] == 666){
            echo "AVISO_PREV: " . $aviso_previo_indenizado;
            print_array($row_rescisao);
        }
    if ($row_rescisao['motivo'] == 64) { $row_rescisao['aviso_codigo'] = 3; }
    
    
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


// inicio da linha 3
    $obj->dados('40'); // tipo de registro  
    $obj->dados($dados['empresa']['tipo_inscricao']); // TIPO DE INSCRIÃ‡ÃƒO (1 - CNPJ)
    $obj->dados($dados['empresa']['cnpj']); // INSCRIÇÃO DA EMPRESA
    $obj->dados($dados['empresa']['tomador_tipo']); // tipo de inscrição - tomador obra const. civil (não informado)
    $obj->dados($dados['empresa']['tomador_cnpj']); // tipo de inscrição - tomador obra const. civil (não informado)
    $obj->dados($dados['trabalhador']['pis']); // PIS
    $obj->dados($dados['trabalhador']['data_admissao']); // data admissão
    $obj->dados('01'); // categoria do empregador (01 - empregado)
    $obj->dados($dados['trabalhador']['nome']); // Nome do trabalhador
    $obj->dados($dados['trabalhador']['numero_ctps']); // CTPS
    $obj->dados($dados['trabalhador']['serie_ctps']); // SERIE CTPS
    $obj->dados($dados['trabalhador']['sexo']); // GENERO
    $obj->dados($dados['trabalhador']['escolaridade']); // ESCOLARIDADE
    $obj->dados($dados['trabalhador']['data_nasci']); // data nascimento
    $obj->dados($dados['trabalhador']['horas_trabalhadas']); // quantidade de horas trabalhadas por semana
    $obj->dados($dados['trabalhador']['cbo']); // CODIGO CBO
    $obj->dados($dados['trabalhador']['data_admissao']); // data de opção REPETIDOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO *****************
    $obj->dados($dados['trabalhador']['codigo_movimentacao']); // Codigo de movimento
    $obj->dados($dados['trabalhador']['data_demissao']); // data de movimentação
    $obj->dados($dados['trabalhador']['codigo_saque']); // código de saque
    $arrCLT_GAMBI = array(928,712);
    if(in_array($idClt, $arrCLT_GAMBI)){
        $dados['trabalhador']['aviso_codigo'] = 3;
    }
    $obj->dados($dados['trabalhador']['aviso_codigo']); // Aviso prévio (1 - trabalhado 2- Indenizado 3-Ausencia/Dispensa)
    $obj->dados($dados['trabalhador']['data_aviso']); // data início do aviso previo
    $obj->dados('S'); // Reposição de Vaga
    $obj->dados($dados['trabalhador']['data_homologacao']); // data da HOmologação Dissídio Coletivo
    $obj->dados($dados['trabalhador']['valor_dissidio']); // Valor Dissídio
    $obj->dados($dados['trabalhador']['remuneracao_mes_anterior']); // Remuneração mes anterior
    $obj->dados($dados['trabalhador']['remuneracao_mes_rescisao']); // Remuneração mes da rescisão        
    $obj->dados($dados['trabalhador']['aviso_previo_indenizado']); // Aviso Prévio Indenizado
    $obj->dados($dados['trabalhador']['pensao_alimenticia']); // Indicativo Pensão aliminticia
    $obj->dados($dados['trabalhador']['percentual_pensao_alimenticia']); // Percentual da pensão alimenticia
    $obj->dados($dados['trabalhador']['valor_pensao_alimenticia']); // Valor da Pénsão alimenticia
    $obj->dados($dados['trabalhador']['cpf']); // CPF
    $obj->dados($dados['trabalhador']['banco']); //  banco da conta do trabalhador \(Não informado porque existem N de agencias com mais de 4 digitos cadastradas)
    $obj->dados($dados['trabalhador']['agencia']);  // Agencia
    $obj->dados($dados['trabalhador']['conta']); // Conta
    $obj->dados($dados['trabalhador']['valor_base_informado']); // Saldo para Fins Rescisórios
    $obj->filler(39); // brancos
    $obj->fechalinha('*'); // FECHA LINHA
// inicio de linha 4
    
    
    // inicio da linha 3
//    $obj->dados('41'); // tipo de registro 
//    $obj->dados($dados['empresa']['tipo_inscricao']); // TIPO DE INSCRIÃ‡ÃƒO (1 - CNPJ)
//    $obj->dados($dados['empresa']['cnpj']); // INSCRIÇÃO DA EMPRESA
//    $obj->dados($dados['empresa']['tomador_tipo']); // tipo de inscrição - tomador obra const. civil (não informado)
//    $obj->dados($dados['empresa']['tomador_cnpj']); // tipo de inscrição - tomador obra const. civil (não informado)
//    $obj->dados($dados['trabalhador']['pis']); // PIS
//    $obj->dados($dados['trabalhador']['data_admissao']); // data admissão
//    $obj->dados('01'); // categoria do empregador (01 - empregado)
//    $obj->dados($obj->completa(' ', 2)); // Dias Trabalhados
//    $obj->dados($obj->completa(' ', 15)); // Saldo Salario
//    $obj->dados($obj->completa(' ', 2)); // 13 salário 12 avos
//    $obj->dados($obj->completa(' ', 15)); // 13 salário 
//    $obj->dados($obj->completa(' ', 2)); // 13 salário indenizado 12 avos
//    $obj->dados($obj->completa(' ', 15)); // 13 salário indenizado
//    $obj->dados($obj->completa(' ', 15)); // Férias Vencidas
//    $obj->dados($obj->completa(' ', 2)); // Férias Proporcionais
//    $obj->dados($obj->completa(' ', 15)); // Férias Proporcionais
//    $obj->dados($obj->completa(' ', 15)); // 1/3 Férias
//    $obj->dados($obj->completa(' ', 2)); // Salário Família Dias
//    $obj->dados($obj->completa(' ', 15)); // Salário Família Valor
//    $obj->dados($obj->completa(' ', 15)); // Adicional Noturno
//    $obj->dados($obj->completa(' ', 15)); // Comissões
//    $obj->dados($obj->completa(' ', 15)); // Gratificação
//    $obj->dados($obj->completa(' ', 3)); // Horas extras quantidade
//    $obj->dados($obj->completa(' ', 15)); // Horas extras valor
//    $obj->dados($obj->completa(' ', 15)); // Adicional insalubridade/periculosidade
//    $obj->dados($obj->completa(' ', 15)); // Dedução Previdência
//    $obj->dados($obj->completa(' ', 15)); // Dedução 13º salário
//    $obj->dados($obj->completa(' ', 15)); // Dedução adiantamentos
//    $obj->dados($obj->completa(' ', 15)); // Dedução imposto de renda
//    $obj->dados($obj->completa(' ', 53)); // Brancos
//    $obj->fechalinha('*');
    
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