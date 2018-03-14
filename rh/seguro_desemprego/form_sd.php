<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include "../../wfunction.php";

$usuario = carregaUsuario();


function normaliza_nome($str, $comprimento='40', $completar=' ', $alinhado=''){
    if(isset($str) && !empty($str)){
        $limpo = str_replace('.', '', $str);
        $limpo = str_replace('-', '', $limpo);
        $limpo = str_replace(')', '', $limpo);
        $limpo = str_replace('(', '', $limpo);
        $limpo = str_replace(',', '', $limpo);
        $limpo = str_replace('/', '', $limpo);
        $limpo = preg_replace("/( +)/i", ' ', $limpo);
        $limpo = str_replace('\\', '', $limpo);    

        $str_pad = ($alinhado=='RIGHT') ? STR_PAD_LEFT : STR_PAD_RIGHT;

        $limpo = substr ( $limpo , 0, $comprimento );
        $str = str_pad($limpo, $comprimento, $completar, $str_pad);
    }else{
        $str = '';
    }
    return $str;
}
//marcelo da costa pereira da silva = 

if(isset($_POST['acao'])){
    
    
    $dados['id_clt'] = isset($_POST['clt']) ? $_POST['clt'] : NULL;
    
    $dados['nome'] = normaliza_nome($_POST['nome'],40);
    $dados['nome_mae'] = normaliza_nome($_POST['mae'],40);
    
    $dados['endereco'] = normaliza_nome($_POST['endereco'],40);
    $dados['complemento'] = normaliza_nome($_POST['complemento'],16);
    $dados['cep'] =normaliza_nome($_POST['cep'],8);
    $dados['uf'] = normaliza_nome($_POST['uf'],2);
    $dados['tel'] = normaliza_nome($_POST['tel'],10);
    $dados['pis'] = normaliza_nome($_POST['pis'],11);
    $dados['ctps_numero'] = normaliza_nome($_POST['ctps_numero'],7);
    $dados['ctps_serie'] = normaliza_nome($_POST['ctps_serie'],3);
    $dados['ctps_uf'] = normaliza_nome($_POST['ctps_uf'],2);
    $dados['cpf'] = normaliza_nome($_POST['cpf'],11);
    $dados['tipo_inscricao'] = normaliza_nome($_POST['tipo_inscricao'],1);
    $dados['cnpj'] = normaliza_nome($_POST['cnpj'],14);
    
    $dados['cbo'] = normaliza_nome($_POST['cbo'],6);
    $dados['ocupacao'] = normaliza_nome($_POST['ocupacao'],22);
    
    if(isset($_POST['data_admissao'])) {
        $arr_data_admissao = explode('/', $_POST['data_admissao']);
        $dados['data_admissao'] = $arr_data_admissao[2].'-'.$arr_data_admissao[1].'-'.$arr_data_admissao[0];
    }
    if(isset($_POST['data_dispensa'])) {
        $arr_data_dispensa = explode('/', $_POST['data_dispensa']);
        $dados['data_dispensa'] = $arr_data_dispensa[2].'-'.$arr_data_dispensa[1].'-'.$arr_data_dispensa[0];
    }
    
    $dados['sexo'] = isset($_POST['sexo']) ? $_POST['sexo'] : NULL;
    $dados['grau_instrucao'] = isset($_POST['grau_instrucao']) ? $_POST['grau_instrucao'] : NULL;
    
    if(isset($_POST['data_nascimento'])) {
        $arr_data_nascimento = explode('/', $_POST['data_nascimento']);
        $dados['data_nascimento'] = $arr_data_nascimento[2].'-'.$arr_data_nascimento[1].'-'.$arr_data_nascimento[0];
    }
    
    
    $dados['hora_semana'] = normaliza_nome($_POST['hora_semana'],2);
    $dados['banco'] = normaliza_nome($_POST['banco'],3);

    $dados['antepenultimo_mes'] = normaliza_nome($_POST['antepenultimo_mes'],2,'0','RIGHT');
    $dados['antepenultimo_salario'] = isset($_POST['antepenultimo_salario']) ? str_replace('R$ ', '', str_replace(',', '.', str_replace('.', '', $_POST['antepenultimo_salario']))) : NULL;
    

    $dados['penultimo_mes'] = normaliza_nome($_POST['penultimo_mes'],2,'0','RIGHT');
    $dados['penultimo_salario'] = isset($_POST['penultimo_salario']) ? str_replace('R$ ', '', str_replace(',', '.',  str_replace('.', '', $_POST['penultimo_salario']))) : NULL;

    $dados['ultimo_mes'] = normaliza_nome($_POST['ultimo_mes'],2,'0','RIGHT');
    $dados['ultimo_salario'] = isset($_POST['ultimo_salario']) ? str_replace('R$ ', '', str_replace(',', '.',  str_replace('.', '', $_POST['ultimo_salario']))) : NULL;

    $dados['recebeu_ultimos_meses'] = isset($_POST['recebeu_ultimos_meses']) ? $_POST['recebeu_ultimos_meses'] : NULL;
    $dados['aviso_indenizado'] = isset($_POST['aviso_indenizado']) ? $_POST['aviso_indenizado'] : NULL;
    
    $dados['criado_por'] = $usuario['id_funcionario'];
    $dados['criado_em'] = date('Y-m-d');
    $dados['status_doc'] = 1;
    
    
    
    $keys = '';
    $val = '';
    
    foreach($dados as $k=>$v){
        $keys .= '`'.$k.'` ,';
        $values .= ' "'.$v.'" ,';
    }
    
    
    
    // status "0" para todos os outros criados para o clt
    $sql_update = 'UPDATE seguro_desemprego_doc SET `status_doc`="0" WHERE id_clt='.$dados['id_clt'].';';
//    echo '<br>'.$sql_update.'<br>';
    mysql_query($sql_update);
    
    $sql = 'INSERT INTO seguro_desemprego_doc('.substr($keys,0, -1).') VALUES ('.substr($values,0, -1).');';
    
//    echo '<br>'.$sql.'<br>';
    mysql_query($sql);
    
    header('Location: requerimento.php?id_clt='.$dados['id_clt']);
    exit();
    
}


$meses = array ('01'=> "Janeiro", '02'=> "Fevereiro", '03'=> "Março", '04'=> "Abril", '05'=> "Maio", '06'=> "Junho", '07'=> "Julho", '08'=> "Agosto", '09'=> "Setembro", '10'=> "Outubro", '11'=> "Novembro", '12'=> "Dezembro");

$arr_escolha = array('1'=>'SIM','2'=>'NÃO');
$arr_sexo = array('1'=>'MASCULINO','2'=>'FEMININO');
$arr_banco = array('104'=>'104 - CAIXA');
$arr_grau_instrucao = array('1'=>'ANALFABETO','2'=>'ATÉ A QUARTA SÉRIE INCOMPLETA','3'=>'4 SÉRIE COMPLETA(1 GRAU)','4'=>'5 À 8 SÉRIE INCOMPLETA',
        '5'=>'1 GRAU','6'=>'2 GRAU INCOMPLETO','7'=>'2 GRAU','8'=>'SUPERIOR INCOMPLETO','9'=>'SUPERIOR COMPLETO');
$arr_tipo_inscricao = array('1'=>'CNPJ','2'=>'CEI');

function diffDate($d1, $d2, $type = '', $sep = '-') {
    $d1 = explode($sep, $d1);
    $d2 = explode($sep, $d2);
    switch ($type) {
        case 'A':
            $X = 31536000;
            break;
        case 'M':
            $X = 2592000;
            break;
        case 'D':
            $X = 86400;
            break;
        case 'H':
            $X = 3600;
            break;
        case 'MI':
            $X = 60;
            break;
        default:
            $X = 1;
    }
    $res_1 = mktime(0, 0, 0, $d2[1], $d2[2], $d2[0]);
    $res_2 = mktime(0, 0, 0, $d1[1], $d1[2], $d1[0]);
    return floor(( ( $res_1 - $res_2 ) / $X));
}

$id_clt = isset($_REQUEST['id']) ? $_REQUEST['id'] : FALSE;

if ($id_clt) {

    $sql = "SELECT A.nome, A.mae AS nome_mae, A.endereco ,  A.numero , A.bairro, A.complemento, A.cep,  A.uf, A.tel_fixo,
            A.pis, A.campo1 AS numero_ctps, A.serie_ctps,A.uf_ctps, A.cpf, C.cnpj, B.nome AS nome_projeto, A.data_entrada,
            DATE_FORMAT(A.data_entrada,'%d/%m/%y')  AS data_admissao, DATE_FORMAT(A.data_saida,'%d/%m/%y')  AS data_dispensa, A.data_saida,
            IF(A.sexo='F',2,1) AS sexo, DATE_FORMAT(A.data_nasci,'%d/%m/%y') AS data_nascimento, D.hora_semana,
            A.escolaridade AS grau_instrucao, C.cnae, G.cod AS cbo, G.nome AS ocupacao, A.banco, A.agencia , E.id_nacional AS id_banco,
            REPLACE(F.sal_base,'.','') AS ultimo_salario, A.data_demi, DATE_FORMAT(A.data_demi, '%m') AS mes_rescisao, DATE_FORMAT(A.data_demi, '%Y') AS ano_rescisao
            FROM rh_clt AS A
            INNER JOIN projeto AS B ON B.id_projeto=A.id_projeto 
            INNER JOIN master AS C ON C.id_master=B.id_master
            INNER JOIN curso AS D ON D.id_curso=A.id_curso
            LEFT JOIN bancos AS E ON A.banco=E.id_banco
            LEFT JOIN rh_recisao AS F ON(A.data_demi=F.data_demi AND A.id_clt=F.id_clt)
            LEFT JOIN rh_cbo AS G ON(D.cbo_codigo=G.id_cbo)
            
            WHERE A.id_clt={$id_clt} 
            LIMIT 1";

    //folha status 3
//            echo '<br><br><br>'.$sql.'<br>';
    $result = mysql_query($sql);
    $funcionario = mysql_fetch_array($result);
    
//    echo '<pre>';
//    print_r($funcionario);
//    echo '</pre>';


    $mes_atual = date('m');
    $ano_atual = date('Y');
    $id_clt = isset($_REQUEST['id']) ? $_REQUEST['id'] : NULL;

    $sql = "SELECT A.id_clt, REPLACE(A.salbase,'.','') AS salbase, A.nome, A.status, A.mes, A.ano, B.terceiro FROM rh_folha_proc AS A
                    LEFT JOIN rh_folha AS B ON(A.id_folha=B.id_folha)
                    WHERE A.id_clt='$id_clt' AND A.status=3 AND B.terceiro=2 AND A.mes<$funcionario[mes_rescisao] ORDER BY A.id_folha_proc DESC LIMIT 6;";
//            
//            echo $sql.'<br>';
    $result_sal = mysql_query($sql);
    $salarios = array();
    while ($resp = mysql_fetch_array($result_sal)) {
        $salarios[] = $resp;
    }


    $d_entrada = date_create($funcionario['data_entrada']);
    $d_saida = date_create($funcionario['data_saida']);
    $d_entrada = date_format($d_entrada, 'Y-m');
    $d_saida = date_format($d_saida, 'Y-m');



    $funcionario['meses_trabalhados'] = diffDate($d_entrada . '-01', $d_saida . '-01', 'M');

    $sql_rescisao = "SELECT  IF(A.aviso='indenizado','1' ,IF(A.aviso='trabalhado','1','2')) AS aviso_codigo FROM rh_recisao AS A WHERE id_clt='" . $id_clt . "' AND status = '1'";

    $query_rescisao = mysql_query($sql_rescisao);
    $row_rescisao = mysql_fetch_array($query_rescisao);
}
        

$funcionario['cbo'] = str_replace('.', '', $funcionario['cbo']);
$funcionario['cbo'] = str_replace('-', '', $funcionario['cbo']);
$funcionario['cbo'] = preg_replace("/( +)/i", '', $funcionario['cbo']);
  

$ultimos_2_salarios = array();
for ($x = 0; $x <= 1; $x++) {
    $ultimos_2_salarios[$x]['mes'] = $salarios[$x]['mes'];
    $ultimos_2_salarios[$x]['salbase'] = $salarios[$x]['salbase'];
}
$ultimos_2_salarios = array_reverse($ultimos_2_salarios);
$soma_2_salarios = 0;

for ($x = 0; $x <= 1; $x++) {
    $soma_2_salarios += $ultimos_2_salarios[$x]['salbase'];
}

//$soma_2_salarios + str_replace('.', '', $funcionario['ultimo_salario']);


$recebeu_ultimos_meses = 1;
for ($x = 0; $x <= 5; $x++) {
    if ($recebeu_ultimos_meses == 1) {
        $recebeu_ultimos_meses = ($salarios[$x]['salbase'] <= 0) ? '2' : '1';
    }
}


?>
<html>
    <head>
        <title>:: Intranet :: Formulário de Desemprego</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script type="text/javascript" src="../../js/jquery.price_format.2.0.min.js"></script>
        <style>            
            .dados input[type=text]{
                width: 500px;
            }
            .dados input.size_1 {
                width: 30px;
            }
            .dados input.size_2 {
                width: 60px;
            }
            .dados input.size_3 {
                width: 120px;
            }
        </style>
    </head>
    <body class="novaintra" >        
        <div id="content">
            <form  name="form" action="" method="post">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Formulário de Seguro Desemprego</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>
                <fieldset class="noprint" style="width: 60%;">
                    <legend>Dados</legend>
                    <div class="fleft dados">
                        <p><label class="first">Nome:</label> <input type="text" value="<?= normaliza_nome($funcionario['nome'],40); ?>" name="nome" maxlength="40" /></p>
                        <p><label class="first">Nome da mãe:</label> <input type="text" value="<?= normaliza_nome($funcionario['nome_mae'],40); ?>" name="mae" maxlength="40"  /></p>
                        <p><label class="first">Endereço:</label> <input type="text" value="<?= normaliza_nome($funcionario['endereco'].' '. $funcionario['numero'].' '. $funcionario['bairro'],40); ?>"  name="endereco" maxlength="40"  /></p>
                        <p><label class="first">Complemento:</label> <input type="text" value="<?= normaliza_nome($funcionario['complemento'],16); ?>"  name="complemento" maxlength="16"  /></p>
                        <p><label class="first">Cep:</label> <input type="text" value="<?= $funcionario['cep']; ?>"  name="cep" maxlength="8" class="size_3"   /></p>
                        <p><label class="first">UF:</label> <input type="text" value="<?= normaliza_nome($funcionario['uf'],2); ?>" class="size_1" name="uf" maxlength="2"  /></p>
                        <p><label class="first">TEL:</label> <input type="text" value="<?= normaliza_nome($funcionario['tel_fixo'],10); ?>" name="tel" maxlength="10" /></p>
                        <p><label class="first">PIS:</label> <input type="text" value="<?= normaliza_nome($funcionario['pis'],11); ?>" name="pis" maxlength="11" class="size_3"  /></p>
                        <p><label class="first">NÚMERO CTPS:</label> <input type="text" value="<?= normaliza_nome($funcionario['numero_ctps'],7); ?>" name="ctps_numero" maxlength="7" class="size_3"  /></p>
                        <p><label class="first">SÉRIE CTPS:</label> <input type="text" value="<?= normaliza_nome($funcionario['serie_ctps'],3); ?>" name="serie" maxlength="3" class="size_3"  /></p>
                        <p><label class="first">UF CTPS:</label> <input type="text" value="<?= normaliza_nome($funcionario['uf_ctps'],2); ?>" name="ctps_uf" maxlength="2" class="size_2"  /></p>
                        <p><label class="first">CPF:</label> <input type="text" value="<?= normaliza_nome($funcionario['cpf'],11); ?>" name="cpf" maxlength="11" class="size_3"  /></p>
                        <p><label class="first">TIPO INSCRIÇÃO:</label> <?php echo montaSelect($arr_tipo_inscricao, '1', array('name' => "tipo_inscricao", 'class'=>'size_3')); ?></p>
                        
                        
                        <p><label class="first">CNPJ/CEI:</label> <input type="text" value="<?= normaliza_nome($funcionario['cnpj'],14); ?>" name="cnpj" maxlength="14" class="size_3"  /></p>
                        <!--<p><label class="first">CENAE:</label> <input type="text" value="<?php //= $funcionario['cnae']; ?>" name="cnae" /></p>-->
                        <p><label class="first">CBO:</label> <input type="text" value="<?= $funcionario['cbo']; ?>" name="cbo" maxlength="6" class="size_2" /></p>
                        <p><label class="first">OCUPAÇÃO:</label> <input type="text" value="<?= $funcionario['ocupacao']; ?>" name="ocupacao" maxlength="22" /></p>
                        <p><label class="first">DATA ADMISSSÂO:</label> <input type="text" value="<?= $funcionario['data_admissao']; ?>" name="data_admissao" maxlength="8"  class="size_2" /> dd/mm/aa</p>
                        <p><label class="first">DATA DISPENSA:</label> <input type="text" value="<?= $funcionario['data_dispensa']; ?>" name="data_dispensa" maxlength="8" class="size_2" /> dd/mm/aa</p>
                        <p><label class="first">SEXO:</label> <?php echo montaSelect($arr_sexo, $funcionario['sexo'], array('name' => "sexo", 'class'=>'size_2')); ?></p>
                        <p><label class="first">GRAU INSTRUÇAO:</label> <?php echo montaSelect($arr_grau_instrucao, $funcionario['grau_instrucao'], array('name' => "grau_instrucao", 'class'=>'size_2')); ?></p>
                        <p><label class="first">DATA NASCIMENTO:</label> <input type="text" value="<?= $funcionario['data_nascimento']; ?>" name="data_nascimento" maxlength="8" class="size_2"  /></p>
                        <p><label class="first">HORA SEMANA:</label> <input type="text" value="<?= $funcionario['hora_semana']; ?>" name="hora_semana" maxlength="2" class="size_1"  /></p>
                        
                        <p><label class="first">BANCO:</label> <?php echo montaSelect($arr_banco, '104', array('name' => "banco", 'class'=>'size_3')); ?></p>
                                              
                        <p><label class="first">ANTEPENÚLTIMO SALÁRIO:</label> <?php echo montaSelect($meses, $ultimos_2_salarios[0]['mes'], array('name' => "antepenultimo_mes", 'class'=>'size_2')); ?> <input type="text" value="<?= $ultimos_2_salarios[0]['salbase']; ?>"  class="size_3 money" name="antepenultimo_salario" /></p>
                        <p><label class="first">PENÚLTIMO SALÁRIO:</label> <?php echo montaSelect($meses, $ultimos_2_salarios[1]['mes'], array('name' => "penultimo_mes", 'class'=>'size_2')); ?> <input type="text" value="<?= $ultimos_2_salarios[1]['salbase']; ?>"  class="size_3 money" name="penultimo_salario" /></p>
                        <p><label class="first">ÚLTIMO SALÁRIO:</label> <?php echo montaSelect($meses, $funcionario['mes_rescisao'], array('name' => "ultimo_mes", 'class'=>'size_2')); ?> <input type="text" value="<?= $funcionario['ultimo_salario']; ?>"  class="size_3 money" name="ultimo_salario" /></p>
                       
                        <p><label class="first">RECEBEU ÚLTIMOS 6 MÊSES:</label> <?php echo montaSelect($arr_escolha, $recebeu_ultimos_meses, array('name' => "recebeu_ultimos_meses", 'class'=>'size_2')); ?></p>
                        <p><label class="first">AVISO INDENIZADO:</label> <?php echo montaSelect($arr_escolha, $row_rescisao['aviso_codigo'], array('name' => "aviso_indenizado", 'class'=>'size_2')); ?></p>
                        
                    </div>
                    <br class="clear"/>
                    <p class="controls" style="margin-top: 10px;">
                        <input type="hidden" name="acao" value="gravar_doc" />
                        <input type="hidden" name="clt" value="<?= $id_clt; ?>" />
                        <input type="submit" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>
            </form>
        </div>
    </body>
</html>