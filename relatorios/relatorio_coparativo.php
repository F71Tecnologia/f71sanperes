<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}
function printArr($arr){
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../funcoes.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();

if (isset($_REQUEST['gerar'])) {

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
    
    
    //printArr($_POST);
    $sql = "SELECT $select 
            FROM $from 
            WHERE $where
            LIMIT 10";
    echo $sql;
    $qr_relatorio = mysql_query($sql)or die(mysql_error());
    $qtd_relatorio = mysql_num_rows($qr_relatorio);
    
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
?>
<html>
    <head>
        <title>:: Intranet :: Relatório Saídas</title>
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
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
    
                $('.date').datepicker({
                        dateFormat: 'dd/mm/yy',
                        changeMonth: true,
                        changeYear: true
                });
            });
        </script>
        <style>
        .check { width: 12.5%; }
        </style>
    </head>
    <body class="novaintra" >
        <div id="content">
            <form  name="form" action="" method="post" id="form">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Relatório Comparativo</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>


                <fieldset class="noprint">
                    <legend>Relatório Comparativo</legend>
                    <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                    <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?> </p>                        
                    <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?> </p>
                    <p><label class="first">Ver Saídas de</label> <input name="data_ini" id="data_ini" type="text" size="10" maxlength="10" class="date" value="<?php echo $_REQUEST['data_ini']; ?>"> <label style="font-weight: bold;">até</label> <input name="data_fim" id="data_fim" type="text" size="10" maxlength="10" class="date" value="<?php echo $_REQUEST['data_fim']; ?>"></p>
                </fieldset>
                <fieldset class="noprint" style="">
                    <legend>Informação CLT</legend>
                    <table style="width: 100%;">
                    <tr>
                        <td colspan="8" class="">
                            <input class="cltTodos" type="checkbox" name="" value=""> Todos
                            <input class="cltF" style="display: none;" type="checkbox" name="from[]" value="rh_clt-rc">
                            <input class="cltF" style="display: none;" type="checkbox" name="info[rc]" value="CLT">
                        </td>
                    </tr>
                    <tr>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.nome"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Nome"> Nome</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.endereco"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Endereço"> Endereço</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.numero"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Número"> Número</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.complemento"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Complemento"> Complemento</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.bairro"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Bairro"> Bairro</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.cidade"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Cidade"> Cidade</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.tel_fixo"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Tel. Fixo"> Tel. Fixo</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.tel_cel"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Tel. Cel"> Tel. Cel</td>
                    </tr>
                    <tr>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.nacionalidade"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Nacionalidade"> Nacionalidade</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.naturalidade"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Naturalidade"> Naturalidade</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.civil"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Estado Civil"> Estado Civil</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.rg"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="RG"> RG</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.orgao"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Orgão"> Orgão</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.cpf"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="CPF"> CPF</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.titulo"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Titulo"> Titulo</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.zona"><input classcltThclt" style="display: none;" type="checkbox" name="selectTh[]" value="Zona"> Zona</td>
                    </tr>
                    <tr>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.secao"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Sessão"> Sessão</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.pai"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Nome Pai"> Nome Pai</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.nacionalidade_pai"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Nacionalidade Pai"> Nacionalidade Pai</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.mae"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Nome Mãe"> Nome Mãe</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.nacionalidade_mae"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Nacionalidade Mãe"> Nacionalidade Mãe</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.escolaridade"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Escolaridade"> Escolaridade</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.instituicao"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Instituição"> Instituição</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.curso"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Curso"> Curso</td>
                    </tr>
                    <tr>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.banco"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Banco"> Banco</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.agencia"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Agência"> Agência</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.conta"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Conta"> Conta</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.obs"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="OBS"> OBS</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.data_entrada"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Data Entrada"> Data Entrada</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.data_saida"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Data Saída"> Data Saída</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.pis"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="PIS"> PIS</td>
                        <td class="check"><input class="clt" type="checkbox" name="select[]" value="rc.salario"><input class="cltTh" style="display: none;" type="checkbox" name="selectTh[]" value="Salário"> Salário</td>
                    </tr>
                    </table>
                </fieldset>
                <fieldset class="noprint" style="">
                    <legend>Informação Saida</legend>
                    <table style="width: 100%;">
                    <tr>
                        <td colspan="8" class="">
                            <input class="saidaTodos" type="checkbox" name="" value=""> Todos
                            <input class="saidaF" style="display: none;" type="checkbox" name="from[]" value="saida-s">
                            <input class="saidaF" style="display: none;" type="checkbox" name="info[s]" value="Saída">
                        </td>
                    </tr>
                    <tr>
                        <td class="check"><input class="saida" type="checkbox" name="select[]" value="s.id_banco"><input class="saidaTh" style="display: none;" type="checkbox" name="selectTh[]" value="Banco"> Banco</td>
                        <td class="check"><input class="saida" type="checkbox" name="select[]" value="s.id_user"><input class="saidaTh" style="display: none;" type="checkbox" name="selectTh[]" value="Usuário"> Usuário</td>
                        <td class="check"><input class="saida" type="checkbox" name="select[]" value="s.nome"><input class="saidaTh" style="display: none;" type="checkbox" name="selectTh[]" value="Nome"> Nome</td>
                        <td class="check"><input class="saida" type="checkbox" name="select[]" value="s.especifica"><input class="saidaTh" style="display: none;" type="checkbox" name="selectTh[]" value="Especifica"> Especifica</td>
                        <td class="check"><input class="saida" type="checkbox" name="select[]" value="s.valor"><input class="saidaTh" style="display: none;" type="checkbox" name="selectTh[]" value="Valor"> Valor</td>
                        <td class="check"><input class="saida" type="checkbox" name="select[]" value="s.data_vencimento"><input class="saidaTh" style="display: none;" type="checkbox" name="selectTh[]" value="Data Vencimento"> Data Vencimento</td>
                        <td class="check"><input class="saida" type="checkbox" name="select[]" value="s.data_pg"><input class="saidaTh" style="display: none;" type="checkbox" name="selectTh[]" value="Data Pagamento"> Data Pagamento</td>
                        <td class="check"><input class="saida" type="checkbox" name="select[]" value="s.id_userpg"><input class="saidaTh" style="display: none;" type="checkbox" name="selectTh[]" value="Usuário Pg"> Usuário Pg</td>
                    </tr>
                    </table>
                </fieldset>
                <fieldset class="noprint">
                <p class="controls" >
                    <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                </p>
                </fieldset>
                <?php if (!empty($qr_relatorio) && isset($_POST['gerar'])) { ?>
                <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel"></p>    
                <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
                        <thead>
                            
                            <tr>
                                <?php 
                                foreach ($arrColspan as $key => $value) {
                                    echo '<th colspan="'.$value.'">'.$infoArr[$key].'</th>';
                                }
                                ?>
                            </tr>
                            <tr>
                                <?php 
                                foreach($_POST['selectTh'] as $value){
                                    echo '<th>'.$value.'</th>';
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                            //printArr($row_rel);                            
                            echo '<tr>';
                            foreach($row_rel as $key => $value){
                                if(strripos($key, 'valor')){
                                    echo '<td>'.number_format($value,2,',','.').'</td>';
                                }else{
                                    echo '<td>'.$value.'</td>';
                                }
                            }
                            echo '</tr>';  
                        } ?>
                    </tbody>
                </table>
                <?php  } ?>
            </form>
        </div>
    </body>
</html>