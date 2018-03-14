<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include "../../conn.php";
include "../../wfunction.php";

function checkPIS($pis) {
//    if('00000000000000'==$pis){
    if ($pis > 0) {
        return TRUE;
    } else {
        return FALSE;
    }
//    $pis = str_pad(ereg_replace('[^0-9]', '', $pis), 11, '0', STR_PAD_LEFT);
//
//    if (strlen($pis) != 11 || intval($pis) == 0) {
//        return false;
//    } else {
//        for ($d = 0, $p = 3, $c = 0; $c < 10; $c++) {
//            $d += $pis{$c} * $p;
//            $p  = ($p < 3) ? 9 : --$p;
//        }
//
//        $d = ((10 * $d) % 11) % 10;
//
//        return ($pis{$c} == $d) ? true : false;
//    }
}

$usuario = carregaUsuario();

$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]' ") or die(mysql_error());
$row_user = mysql_fetch_assoc($qr_user);


$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);




////SELECT regiao
$regiao = montaQuery('regioes', "id_regiao, regiao", "id_master = '$usuario[id_master]' ");
$optRegiao = array();
//$optProjeto[] = 'TODOS';
foreach ($regiao as $valor) {
    $optRegiao[$valor['id_regiao']] = $valor['regiao'];
}
$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : '';


////SELECT mês

$regiao_select = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projeto = montaQuery('projeto', "id_projeto, nome", "id_regiao = $regiao_select");
$optProjeto = array();
//$optProjeto[] = 'TODOS';
foreach ($projeto as $valor) {
    $optProjeto[$valor['id_projeto']] = $valor['nome'];
}
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : '';


////SELECT mês
$meses = montaQuery('ano_meses', "num_mes,nome_mes");
$optMeses = array();
foreach ($meses as $valor) {
    $optMeses[$valor['num_mes']] = $valor['nome_mes'];
}
$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');


//SELECT ANO
$optAnos = array();
for ($i = 2009; $i <= date('Y'); $i++) {
    $optAnos[$i] = $i;
}
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');



//FILTRO
if (isset($_REQUEST['filtrar']) && $_REQUEST['filtrar'] == "Filtrar") {

    $mes = $_POST['mes'];
    $ano = $_POST['ano'];
    $data_referencia = $ano . '-' . $mes . '-01';


    $qr_verifica_caged = mysql_query("SELECT * FROM caged 
                        WHERE   mes_caged = '$mes' AND ano_caged = '$ano' AND status_caged = 1");

    $verifica_cageg = mysql_num_rows($qr_verifica_caged);
    $row_caged = mysql_fetch_assoc($qr_verifica_caged);

    $qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_master = '$usuario[id_master]'");
    while ($row_regiao = mysql_fetch_assoc($qr_regiao)) {

        $ids_regioes[] = $row_regiao['id_regiao'];
    }

    $ids_regioes = implode(',', $ids_regioes);

    $qr_trabalhadores = mysql_query(" 
                                    /**CONSULTA DOS ADMITIDOS**/
                                    SELECT qr_admitidos.*, C.nome as nome_funcao, C.salario,D.id_regiao as id_regiao_transferencia, D.regiao as nome_regiao, E.id_projeto as  id_projeto_transferencia,E.nome as nome_projeto 
                                    FROM
                                                  (SELECT A.id_curso,A.id_clt, A.id_regiao, A.id_projeto, A.pis, 
                                                         REPLACE( REPLACE(A.pis,'.',''),'-','') as pis_limpo,
                                                         REPLACE( REPLACE(A.cpf,'.',''),'-','') as cpf_limpo,
                                                         E.cnpj,
                                                         D.cbo_codigo,
                                                         DATE_FORMAT(data_entrada,'%d/%m/%Y') as data,
                                                         IF( MONTH(A.data_entrada) = '$mes' AND YEAR(A.data_entrada) = '$ano','ADMITIDO(S)','') as movimento,										
                                                         A.nome as nome_clt, A.sexo,A.data_nasci, A.escolaridade, A.data_entrada,A.status_demi,A.data_demi, A.campo1,A.serie_ctps,A.uf_ctps,A.etnia,A.status,A.status_admi,
                                                         REPLACE(A.cep,'-','') as cep_limpo,
                                                         (SELECT id_curso_de 	  FROM rh_transferencias WHERE id_clt = A.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS funcao_de,
                                                         (SELECT id_curso_para  FROM rh_transferencias WHERE id_clt = A.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS funcao_para,    
                                                         (SELECT id_regiao_de   FROM rh_transferencias WHERE id_clt = A.id_clt AND id_regiao_de <> id_regiao_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS regiao_de,
                                                         (SELECT id_regiao_para FROM rh_transferencias WHERE id_clt = A.id_clt AND id_regiao_de <> id_regiao_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS regiao_para,
                                                         (SELECT id_projeto_de   FROM rh_transferencias WHERE id_clt = A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS projeto_de,
                                                         (SELECT id_projeto_para FROM rh_transferencias WHERE id_clt = A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS projeto_para

                                                        FROM rh_clt as A	                             
                                                                 INNER JOIN curso as D 
                                                                ON D.id_curso = A.id_curso       
                                                                INNER JOIN rhempresa as E 
                                                                ON E.id_projeto = A.id_projeto
                                                                WHERE YEAR(A.data_entrada) = '$ano'
							AND MONTH(A.data_entrada) = '$mes') as qr_admitidos
										  	
                                LEFT JOIN curso AS C ON (IF(qr_admitidos.funcao_para IS NOT NULL,C.id_curso   = qr_admitidos.funcao_para, IF(qr_admitidos.funcao_de IS NOT NULL,C.id_curso = qr_admitidos.funcao_de,C.id_curso = qr_admitidos.id_curso)))
                                LEFT JOIN regioes AS D ON (IF(qr_admitidos.regiao_para IS NOT NULL,D.id_regiao = qr_admitidos.regiao_para, IF(qr_admitidos.regiao_de IS NOT NULL,D.id_regiao = qr_admitidos.regiao_de,D.id_regiao = qr_admitidos.id_regiao)))    
                                LEFT JOIN projeto AS E ON (IF(qr_admitidos.projeto_para IS NOT NULL,E.id_projeto = qr_admitidos.projeto_para, IF(qr_admitidos.projeto_de IS NOT NULL,E.id_projeto = qr_admitidos.projeto_de,E.id_projeto = qr_admitidos.id_projeto)))              

                                UNION
  
                                /**CONSULTA DOS DEMITIDOS**/
                                SELECT qr_demitidos.*, C.nome as nome_funcao, C.salario,D.id_regiao as id_regiao_transferencia, D.regiao as nome_regiao, E.id_projeto as  id_projeto_transferencia,E.nome as nome_projeto 
                                FROM
                                                  (SELECT A.id_curso,A.id_clt, A.id_regiao, A.id_projeto, A.pis, 
                                                                  REPLACE( REPLACE(A.pis,'.',''),'-','') as pis_limpo,
                                                                  REPLACE( REPLACE(A.cpf,'.',''),'-','') as cpf_limpo,
                                                                  E.cnpj,	 
                                                                  D.cbo_codigo,
                                                                  DATE_FORMAT(data_demi,'%d/%m/%Y') as data, 
                                                                  IF( MONTH(A.data_demi) = '$mes' AND YEAR(A.data_demi) ='$ano','DEMITIDO(S)','') as movimento,										
                                                                  A.nome as nome_clt, A.sexo,A.data_nasci, A.escolaridade, A.data_entrada,A.status_demi,A.data_demi, A.campo1,A.serie_ctps,A.uf_ctps,A.etnia,A.status,A.status_admi,
                                                                  REPLACE(A.cep,'-','') as cep_limpo,
                                                                  (SELECT id_curso_de 	  FROM rh_transferencias WHERE id_clt = A.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS funcao_de,
                                                                  (SELECT id_curso_para  FROM rh_transferencias WHERE id_clt = A.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS funcao_para,    
                                                                  (SELECT id_regiao_de   FROM rh_transferencias WHERE id_clt = A.id_clt AND id_regiao_de <> id_regiao_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS regiao_de,
                                                                  (SELECT id_regiao_para FROM rh_transferencias WHERE id_clt = A.id_clt AND id_regiao_de <> id_regiao_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS regiao_para,
                                                                  (SELECT id_projeto_de   FROM rh_transferencias WHERE id_clt = A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc >= '$data_referencia' ORDER BY id_transferencia ASC LIMIT 1) AS projeto_de,
                                                                  (SELECT id_projeto_para FROM rh_transferencias WHERE id_clt = A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc <= '$data_referencia' ORDER BY id_transferencia DESC LIMIT 1) AS projeto_para
                                                            
                                                FROM rh_clt as A                                
                                                INNER JOIN curso as D 
                                                ON D.id_curso = A.id_curso
                                                INNER JOIN rhempresa as E 
                                                ON E.id_projeto = A.id_projeto
                                                WHERE YEAR(A.data_demi) = '$ano' AND MONTH(A.data_demi) = '$mes' AND A.status IN(60,61,62,81,63,101,64,65,66)) as qr_demitidos

                                LEFT JOIN curso AS C ON (IF(qr_demitidos.funcao_para IS NOT NULL,C.id_curso      = qr_demitidos.funcao_para,  IF(qr_demitidos.funcao_de IS NOT NULL,C.id_curso    = qr_demitidos.funcao_de,C.id_curso = qr_demitidos.id_curso)))
                                LEFT JOIN regioes AS D ON (IF(qr_demitidos.regiao_para IS NOT NULL,D.id_regiao   = qr_demitidos.regiao_para,  IF(qr_demitidos.regiao_de IS NOT NULL,D.id_regiao   = qr_demitidos.regiao_de,D.id_regiao = qr_demitidos.id_regiao)))    
                                LEFT JOIN projeto AS E ON (IF(qr_demitidos.projeto_para IS NOT NULL,E.id_projeto = qr_demitidos.projeto_para, IF(qr_demitidos.projeto_de IS NOT NULL,E.id_projeto = qr_demitidos.projeto_de,E.id_projeto = qr_demitidos.id_projeto)))  
                              
                                ORDER BY id_projeto_transferencia,movimento,nome_clt") or die(mysql_error());

    $total = mysql_num_rows($qr_trabalhadores);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>CAGED</title>
        <link href="../../net1.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
        <script>
            $(function() {
                $('#regiao').change(function() {

                    var id_regiao = $(this).val();

                    $('#projeto').next().html('<img src="../img_menu_principal/loader16.gif"/>');
                    $.ajax({
                        url: '../../action.global.php?regiao=' + id_regiao,
                        success: function(resposta) {
                            $('#projeto').html(resposta);
                            $('#projeto').next().html('');
                        }
                    });
                })

                $('.excluir').click(function() {

                    var id_caged = $(this).attr('rel');
                    var elemento = $(this);

                    if (confirm("Deseja excluir este arquivo?")) {
                        $.post("actions/excluir_caged.php", {id_caged: id_caged}, function(data) {

                            elemento.parent().parent().parent().fadeOut();
                            alert("Arquivo deletado!");
                        })
                    }
                    return false;
                })


            });
        </script>

        <style media="print" >
            .tabela_ramon{
                visibility: visible;
                margin-top:-350px;
            }


            #head, fieldset{
                visibility:  hidden;
            }


        </style>
        <style>
            .baixar{
                text-align: center;
                text-decoration: none;
                width: 60px;
                height: 35px;
                display: block;
                border: 1px solid #FFF;
            }
            .baixar:hover{
                text-decoration: underline;
                border: 1px solid #000;
            }
            .excluir{

            }
        </style>

    </head>

    <body class="novaintra">
        <form action="" method="post" name="form1">
            <div id="content">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $row_master['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>RH - CAGED</h2>                      
                    </div>
                    <div class="fright"> <?php include('../../reportar_erro.php'); ?></div> 
                </div>
                <br class="clear"/>
                <br/>
                <fieldset>
                    <legend>CAGED</legend>
                    <div class="fleft">
                        <p><label class="first">Mês:</label> <?php echo montaSelect($optMeses, $mesSel, array('name' => 'mes', 'id' => 'mes')); ?></p>
                        <p><label class="first">Ano:</label> <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano')); ?></p>
                    </div>

                    <br class="clear"/>
                    <p class="controls" style="margin-top: 10px;"><input type="submit" name="filtrar" value="Filtrar" id="filtrar"/></p>

                    <?php if ($verifica_cageg != 0) { ?>

                        <p class="controls" style="margin-top: 10px; text-align: left; color: #ff3333;">O arquivo desta competência já foi gerado!</p>                   
                    <?php } ?>
                </fieldset>  
        </form>
        <br/>
        <br/>

        <?php
        if (isset($_REQUEST['filtrar']) and $verifica_cageg == 0) {


            if ($total == 0) {
                echo '<h3>Nenhuma movimentação nesta competência!</h3>';
            } else {
                ?>
                <form action="actions/cadastro.caged_1.php" method="post" name="form1">

                    <table class="tabela_ramon" width="100%">
                        <thead >
                            <tr class="titulo">
                                <td colspan="7">
                                    <?php
                                    echo htmlentities(mesesArray($mes)) . ' / ' . $ano;
                                    ?>
                                </td>
                            </tr>
                        </thead>                   
                        <?php
                        while ($row_trab = mysql_fetch_assoc($qr_trabalhadores)) {

                            if ($row_trab['id_projeto_transferencia'] != $projetoAnt) {
                                echo '<tr><td colspan="7"><h3>' . $row_trab['nome_projeto'] . '<br> CNPJ: ' . $row_trab['cnpj'] . '</h3></td></tr>';
                                unset($movAnt);
                            }

                            $class = ($i++ % 2 == 0) ? 'class="corfundo_um"' : 'class="corfundo_dois"';
                            $tipo_admissao = array(10 => "Primeiro emprego", 20 => "Reemprego", 25 => "Contrato por prazo determinado", 35 => "Reintegra&ccedil;&atilde;o", 70 => "Transferência da entrada");

                            if ($row_trab['movimento'] == 'ADMITIDO(S)') {

                                $tipo = $tipo_admissao[$row_trab['status_admi']];
                            } else {

                                $qr_tipodemi = mysql_query("SELECT especifica FROM rhstatus WHERE codigo = '$row_trab[status]';");
                                $tipo = mysql_result($qr_tipodemi, 0);
                            }



                            ///?VERIFICAÇÔES
                            $valores_escolaridade = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11);
                            $valores_status_admissao = array(10, 20, 25, 35, 70, 31, 32, 40, 43, 45, 50, 60, 80);



                            if (empty($row_trab['pis']) or strlen($row_trab['pis_limpo']) <> 11) {
                                $erro['pis'] = 'PIS não informado.';
                            }
                            if (empty($row_trab['cbo_codigo'])) {
                                $erro['cbo'] = 'CBO não informado.';
                            }
                            if (empty($row_trab['sexo'])) {
                                $erro['sexo'] = 'O campo Sexo não pode estar vazio.';
                            }
                            if (empty($row_trab['data_nasci'])) {
                                $erro['nascimento'] = "Data de nascimento inválida!";
                            }
                            if (empty($row_trab['escolaridade']) or !in_array($row_trab['escolaridade'], $valores_escolaridade)) {
                                $erro['escolaridade'] = "Grau de instrução invalido.";
                            }
                            if (empty($row_trab['data_entrada'])) {
                                $erro['admissao'] = "Data de admissão não expecificada";
                            }
                            if (empty($row_trab['status_admi']) or !in_array($row_trab['status_admi'], $valores_status_admissao)) {
                                $erro['status_admin'] = "Movimento não expecificado.";
                            }
                            if (empty($row_trab['nome_clt'])) {
                                $erro['nome'] = "Nome inválido";
                            }
                            if (empty($row_trab['campo1'])) {
                                $erro['ctps'] = "Numero da carteira de trabalho inválido!";
                            }
                            if (empty($row_trab['serie_ctps'])) {
                                $erro['serie'] = "Serie da carteira de trabalho inválido.";
                            }
                            if (empty($row_trab['salario'])) {
                                $erro['salario'] = "Salario não expecificado.";
                            }
                            if (empty($row_trab['cbo_codigo'])) {
                                $erro['cbo'] = "CBO inválido";
                            }
                            if (empty($row_trab['uf_ctps'])) {
                                $erro['uf'] = "UF da carteira de trabalho invalido.";
                            }
                            if (empty($row_trab['cpf_limpo']) or strlen($row_trab['cpf_limpo']) <> 11) {
                                $erro['cpf'] = "CPF inválido.";
                            }
                            if (empty($row_trab['cep_limpo']) or strlen(($row_trab['cep_limpo'])) <> 8) {
                                $erro['cep'] = "CEP inválido.";
                             //  $erro['cep'] = $row_trab['cep_limpo'];
                            }
                            if (empty($row_trab['etnia'])) {
                                $erro['raca'] = "Etnia inválida.";
                            }


                            if ($row_trab['movimento'] != $movAnt) {

                                $movAnt = $row_trab['movimento'];
                                if ($row_trab['movimento'] == 'ADMITIDO(S)') {

                                    $titulo_tipo = 'TIPO DE ADMISSÃO';
                                    $titulo_data = 'DATA DE ADMISSÃO';
                                } else {

                                    $titulo_tipo = 'TIPO DE DEMISSÃO';
                                    $titulo_data = 'DATA DE DEMISSÃO';
                                }
                                ?>   
                                <tr class="subtitulo">
                                    <td colspan="7"><?php echo $row_trab['movimento']; ?></td>
                                </tr>
                                <tr class="subtitulo">
                                    <td width="10"></td>
                                    <td>COD.</td>
                                    <td>NOME</td>
                                    <td>REGIÃO</td>
                                    <td>PROJETO</td>
                                    <td><?php echo $titulo_tipo ?></td>
                                    <td><?php echo $titulo_data ?></td>
                                </tr>     

            <?php }
            ?>
                            <tr>
                                <td colspan="7" style="color: red;">
                                        <?php
                                        $check_registro = TRUE;
//                                echo '<pre>';
//                                print_r($row_trab);  
//                                echo '</pre>'; 
                                        if (checkPIS($row_trab['pis'])) {
//                                    echo '<br> O pis '.$row_trab['pis'].' passou';
                                        } else {
                                            $check_registro = FALSE;
                                            echo '<br>Erro no registro: ' . $row_trab['nome_clt'] . '. Erro: Pis ' . $row_trab['pis'] . ' inválido!';
                                        }
                                        echo strlen($row_trab['serie_ctps']);
                                        if ((strlen($row_trab['serie_ctps']) > 4)) {
//                                    echo '<br> O pis '.$row_trab['pis'].' passou';
                                            echo '<br>Erro no registro: ' . $row_trab['nome_clt'] . '. Erro: Série CLT ' . $row_trab['serie_ctps'] . ' com mais de 4 dígitos!';
                                            $check_registro = FALSE;
                                        }
                                        ?>
                                </td>
                            </tr>

                            <tr <?php echo $class; ?>>
                                <td align="center">
                                    <input type="checkbox" name="ids_clt[]" value="<?php echo $row_trab['id_clt'] ?>" <?= ($check_registro) ? ' checked="checked" ' : ''; ?>/>
                                </td>
                                <td><?php echo $row_trab['id_clt']; ?></td>
                                <td>
            <?php if (!empty($erro)) { ?>
                                        <a class="erros" title="<?= implode("<br>", $erro) ?>" href="Edita.php?ID=<?= $row_trab['id_clt'] ?>&<?= implode('&', array_keys($erro)) ?>" onclick="return hs.htmlExpand(this, {objectType: 'iframe'})">
                <?= $row_trab['nome_clt'] ?>
                                        </a>
                <?php
            } else {
                echo $row_trab['nome_clt'];
            }
            ?>
                                </td>
                                <td><?php echo $row_trab['nome_regiao']; ?></td>
                                <td align="center"><?php echo $row_trab['nome_projeto']; ?></td>
                                <td><?php echo $tipo; ?></td>
                                <td align="center"><?php echo $row_trab['data']; ?></td>
                            </tr>                       

            <?php
            $projetoAnt = $row_trab['id_projeto_transferencia'];
            unset($erro);
        }
        ?>

                        <tr>
                            <td colspan="7" align="center">

                                <input type="hidden" name="mes" value="<?= $mes ?>" />
                                <input type="hidden" name="ano" value="<?= $ano ?>" />
                                <input type="hidden" name="projeto" value="<?= $_REQUEST['projeto'] ?>" />
                                <input type="hidden" name="regiao" value="<?= $_REQUEST['regiao'] ?>" />

                                <input type="submit"  name="gerar_arquivo" value="Gerar arquivo do caged"/>
                            </td>
                        </tr>
                    </table>
                </form>
        <?php
    }
} elseif (isset($_REQUEST['filtrar']) and $verifica_cageg != 0) {
    ?>
            <table class="grid" width="30%">
                <tr class="titulo">                        
                    <td>Download</td>
                    <td>Excluir</td>
                </tr>
                <tr>
                    <td align="center">
                        <a href='Arquivos/download.php?file=CGD_<?php echo $_REQUEST['regiao']; ?>_<?php echo $_REQUEST['projeto']; ?>_<?php echo $mes; ?>_<?php echo $ano; ?>.txt' class='baixar' title="Download">
                            <img src='../../imagens/baixar_arquivo.png' width="30" height="30"/>
                        </a>
                    </td>
                    <td align="center"><a href="#" class="excluir" rel="<?php echo $row_caged['id_caged']; ?>" title="Excluir"><img src="../../imagens/deletado.gif"/></a></td>
                </tr>
            </table>
    <?php
}
?>
        </div>       
    </body>
</html>