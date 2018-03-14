<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/global.php');
include('../../classes/PrestadorServicoClass.php');

$usuario = carregaUsuario();

if(isset($_REQUEST['prestador']) && !empty($_REQUEST['prestador'])){
    $prestador = PrestadorServico::getPrestador($_REQUEST['prestador']);
    $id_prestador = $prestador['id_prestador'];
    $projeto = montaQueryFirst("projeto", "*" , "id_projeto={$prestador['id_projeto']}");
    $regiao = montaQueryFirst("regioes", "*" , "id_regiao={$prestador['id_regiao']}");
    $socios = montaQuery("prestador_socio","*","id_prestador = {$id_prestador}");
    $dependentes = montaQuery("prestador_dependente","*","prestador_id = {$id_prestador}");
    $saidas = montaQuery("saida","*","id_prestador = {$id_prestador}");
    //Contagem de todos os dependentes cadastrados
    $num_socios = (count($socios) > 0 )? count($socios) : 1;
    $num_dependentes = (count($dependentes) > 0 )? count($dependentes) : 1;
    $_SESSION['voltarPrestador']['id_regiao'] = $prestador['id_regiao'];
    $_SESSION['voltarPrestador']['id_projeto'] = $prestador['id_projeto'];
    //Verifica��o para ver se j� foi encerrado anteriormente
    //para que o update n�o mude o id de quem encerrou
    $encerrado = (!empty($prestador['encerrado_em']))? $prestador['encerrado_por'] : NULL;
    //MOVENDO OS S�CIOS DA TABELA prestadorservico PARA A TABELA prestador_socio
    if(!empty($prestador['co_responsavel_socio1'])) {
        $qr_inserir_socio = mysql_query("INSERT INTO prestador_socio(nome,tel,id_prestador) VALUES('{$prestador['co_responsavel_socio1']}','{$prestador['co_tel_socio1']}','{$id_prestador}')");
        $qr_deletar_socio = mysql_query("UPDATE prestadorservico
                SET co_responsavel_socio1 = NULL,
                co_tel_socio1 = NULL,
                co_fax_socio1 = NULL,
                co_civil_socio1 = NULL,
                co_nacionalidade_socio1 = NULL,
                co_email_socio1 = NULL,
                co_email_socio1 = NULL,
                co_municipio_socio1 = NULL,
                data_nasc_socio1 = NULL
                WHERE id_prestador = '$id_prestador'
                LIMIT 1
                ");
    }
    if(!empty($prestador['co_responsavel_socio2'])) {
        $qr_inserir_socio = mysql_query("INSERT INTO prestador_socio(nome,tel, id_prestador) 
                VALUES('{$prestador['co_responsavel_socio2']}','{$prestador['co_tel_socio2']}','{$id_prestador}')");
        $qr_deletar_socio = mysql_query("UPDATE prestadorservico
                SET co_responsavel_socio2 = NULL,
                co_tel_socio2 = NULL,
                co_fax_socio2 = NULL,
                co_civil_socio2 = NULL,
                co_nacionalidade_socio2 = NULL,
                co_email_socio2 = NULL,
                co_email_socio2 = NULL,
                co_municipio_socio2 = NULL,
                data_nasc_socio2 = NULL
                WHERE id_prestador = '$id_prestador'
                LIMIT 1
                ");
    }
}else{
    $saidas = array();
    $_SESSION['voltarPrestador']['id_regiao'] = $_REQUEST['regiao'];
    $_SESSION['voltarPrestador']['id_projeto'] = $_REQUEST['projeto'];
    $prestador = array();
    $socios = array();
    $dependentes = array();
    $num_socios = 1;
    $num_dependentes = 1;
}

if(isset($_REQUEST['cadastrar'])) {
    //C�digo para cadastrar
    $id_projeto = $_REQUEST['projeto'];
    $id_regiao = $_REQUEST['regiao'];
    $id_medida = $_REQUEST['id_medida'];
    $aberto_por = $_COOKIE['logado'];
    $contratado_em = implode("-",  array_reverse(explode("/",$_REQUEST['contratado_em'])));
    $contratante = $_REQUEST['contratante'];
    $endereco = $_REQUEST['endereco'];
    $cnpj = $_REQUEST['cnpj'];
    $responsavel = $_REQUEST['responsavel'];
    $civil = $_REQUEST['civil'];
    $nacionalidade = $_REQUEST['nacionalidade'];
    $formacao = $_REQUEST['formacao'];
    $rg = $_REQUEST['rg'];
    $cpf = $_REQUEST['cpf'];
    $c_fantasia = $_REQUEST['c_fantasia'];
    $c_razao = $_REQUEST['c_razao'];
    $c_endereco = $_REQUEST['c_endereco'];
    $c_cnpj = $_REQUEST['c_cnpj'];
    $c_ie = $_REQUEST['c_ie'];
    $c_im = $_REQUEST['c_im'];
    $c_tel = $_REQUEST['c_tel'];
    $c_fax = $_REQUEST['c_fax'];
    $c_responsavel = $_REQUEST['c_responsavel'];
    $c_civil = $_REQUEST['c_civil'];
    $c_nacionalidade = $_REQUEST['c_nacionalidade'];
    $c_formacao = $_REQUEST['c_formacao'];
    $c_rg = $_REQUEST['c_rg'];
    $c_cpf = $_REQUEST['c_cpf'];
    $c_email = $_REQUEST['c_email'];
    $c_site = $_REQUEST['c_site'];
    $co_responsavel = $_REQUEST['co_responsavel'];
    $co_tel = $_REQUEST['co_tel'];
    $co_fax = $_REQUEST['co_fax'];
    $co_civil = $_REQUEST['co_civil'];
    $co_nacionalidade = $_REQUEST['co_nacionalidade'];
    $co_email = $_REQUEST['co_email'];
    $objeto = $_REQUEST['objeto'];
    $valor = str_replace(".", "", $_REQUEST['valor']);
    $prestador_tipo = $_REQUEST['prestador_tipo'];
    $nome_banco = $_REQUEST['nome_banco'];
    $agencia = $_REQUEST['agencia'];
    $conta = $_REQUEST['conta'];
    $prestacao_contas = $_REQUEST['prestacao_contas'];       
    
    $insert_prestador = mysql_query("INSERT INTO prestadorservico(id_regiao, 
            id_projeto,
            id_medida,
            aberto_por,
            aberto_em,
            contratado_em,
            contratante,
            endereco, 
            cnpj, 
            responsavel, 
            civil,
            nacionalidade,
            formacao,
            rg,
            cpf,
            c_fantasia,
            c_razao,
            c_endereco,
            c_cnpj,
            c_ie,
            c_im,
            c_tel,
            c_fax,
            c_email,
            c_responsavel,
            c_civil,
            c_nacionalidade,
            c_formacao,
            c_rg,
            c_cpf,
            c_site,
            co_responsavel,
            co_tel,
            co_fax,
            co_civil,
            co_nacionalidade,
            co_email,
            objeto,
            valor,
            prestador_tipo,
            nome_banco,
            agencia,
            conta,
            prestacao_contas
            ) VALUES (
            '$id_regiao',
            '$id_projeto',
            '$id_medida',
            '$aberto_por',
            NOW(),
            '$contratado_em',
            '$contratante',
            '$endereco', 
            '$cnpj', 
            '$responsavel', 
            '$civil',
            '$nacionalidade',
            '$formacao',
            '$rg',
            '$cpf',
            '$c_fantasia',
            '$c_razao',
            '$c_endereco',
            '$c_cnpj',
            '$c_ie',
            '$c_im',
            '$c_tel',
            '$c_fax',
            '$c_email',
            '$c_responsavel',
            '$c_civil',
            '$c_nacionalidade',
            '$c_formacao',
            '$c_rg',
            '$c_cpf',
            '$c_site',
            '$co_responsavel',
            '$co_tel',
            '$co_fax',
            '$co_civil',
            '$co_nacionalidade',
            '$co_email',
            '$objeto',
            '$valor',
            '$prestador_tipo',
            '$nome_banco',
            '$agencia',
            '$conta',
            '$prestacao_contas'
            )");
    
    $id_prestador = mysql_insert_id();
    
    $nome_socios = $_REQUEST['nome_socio'];
    $tel_socios  = $_REQUEST['tel_socio'];
    $cpf_socios = $_REQUEST['cpf_socio'];
    
    //Para cada s�cio ser� realizado um cadastro
    for($cont = 0; !empty($nome_socios[$cont]); $cont++) {
        $insert_socio = mysql_query("INSERT INTO prestador_socio(
                nome,
                tel,
                cpf,
                id_prestador
                ) VALUES (
                '$nome_socios[$cont]',
                '$tel_socios[$cont]',
                '$cpf_socios[$cont]',
                '$id_prestador')");
    }
    
    $nome_dependentes = $_REQUEST['nome_dependente'];
    $tel_dependentes = $_REQUEST['tel_dependente'];
    $parentesco_dependentes = $_REQUEST['parentesco_dependente'];
    
    //Para cada dependente  ir� realizar um cadastro
    for($cont = 0; !empty($nome_dependentes[$cont]); $cont++) {
        $insert_dependente = mysql_query(" INSERT INTO prestador_dependente(prestador_id,
                prestador_dep_nome,
                prestador_dep_tel,
                prestador_dep_parentesco,
                prestador_dep_status
                ) VALUES (
                '$id_prestador',
                '$nome_dependentes[$cont]',
                '$tel_dependentes[$cont]',
                '$parentesco_dependentes[$cont]',
                '1'
                )");
    }
    //echo "<script>history.go(-2)</script>";
    header('Location: index.php');
}

if(isset($_REQUEST['editar'])) {
    //C�digo para editar
    $id_prestador = $_REQUEST['id_prestador'];
    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $id_medida = $_REQUEST['id_medida'];
    $contratado_em = implode("-",  array_reverse(explode("/",$_REQUEST['contratado_em'])));
    $encerrado_por = (!empty($_REQUEST['encerrado_por']))? $_REQUEST['encerrado_por'] : $_COOKIE['logado'];
    $encerrado_em = implode("-",  array_reverse(explode("/",$_REQUEST['encerrado_em'])));
    $contratante = $_REQUEST['contratante'];
    $endereco = $_REQUEST['endereco'];
    $cnpj = $_REQUEST['cnpj'];
    $responsavel = $_REQUEST['responsavel'];
    $civil = $_REQUEST['civil'];
    $nacionalidade = $_REQUEST['nacionalidade'];
    $formacao = $_REQUEST['formacao'];
    $rg = $_REQUEST['rg'];
    $cpf = $_REQUEST['cpf'];
    $c_fantasia = $_REQUEST['c_fantasia'];
    $c_razao = $_REQUEST['c_razao'];
    $c_endereco = $_REQUEST['c_endereco'];
    $c_cnpj = $_REQUEST['c_cnpj'];
    $c_ie = $_REQUEST['c_ie'];
    $c_im = $_REQUEST['c_im'];
    $c_tel = $_REQUEST['c_tel'];
    $c_fax = $_REQUEST['c_fax'];
    $c_responsavel = $_REQUEST['c_responsavel'];
    $c_civil = $_REQUEST['c_civil'];
    $c_nacionalidade = $_REQUEST['c_nacionalidade'];
    $c_formacao = $_REQUEST['c_formacao'];
    $c_rg = $_REQUEST['c_rg'];
    $c_cpf = $_REQUEST['c_cpf'];
    $c_email = $_REQUEST['c_email'];
    $c_site = $_REQUEST['c_site'];
    $co_responsavel = $_REQUEST['co_responsavel'];
    $co_tel = $_REQUEST['co_tel'];
    $co_fax = $_REQUEST['co_fax'];
    $co_civil = $_REQUEST['co_civil'];
    $co_nacionalidade = $_REQUEST['co_nacionalidade'];
    $co_email = $_REQUEST['co_email'];
    $objeto = $_REQUEST['objeto'];
    $valor = str_replace(".", "", $_REQUEST['valor']);
    $prestador_tipo = $_REQUEST['prestador_tipo'];
    $nome_banco = $_REQUEST['nome_banco'];
    $agencia = $_REQUEST['agencia'];
    $conta = $_REQUEST['conta'];
    $prestacao_contas = $_REQUEST['prestacao_contas'];
    
    $qr_update = mysql_query("UPDATE prestadorservico 
            SET id_medida = '$id_medida',
            contratado_em = '$contratado_em',
            encerrado_por = '$encerrado_por',
            encerrado_em = '$encerrado_em',
            contratante = '$contratante',
            endereco = '$endereco',
            cnpj = '$cnpj',
            responsavel = '$responsavel',
            civil = '$civil',
            nacionalidade = '$nacionalidade',
            formacao = '$formacao',
            rg = '$rg',
            cpf = '$cpf',
            c_fantasia = '$c_fantasia',
            c_razao = '$c_razao',
            c_endereco = '$c_endereco',
            c_cnpj = '$c_cnpj',
            c_ie = '$c_ie',
            c_im = '$c_im',
            c_tel = '$c_tel',
            c_fax = '$c_fax',
            c_email = '$c_email',
            c_responsavel = '$c_responsavel',
            c_civil = '$c_civil',
            c_nacionalidade = '$c_nacionalidade',
            c_formacao = '$c_formacao',
            c_rg = '$c_rg',
            c_cpf = '$c_cpf',
            c_site = '$c_site',
            co_responsavel = '$co_responsavel',
            co_tel = '$co_tel',
            co_fax = '$co_fax',
            co_civil = '$co_civil',
            co_nacionalidade = '$co_nacionalidade',
            co_email = '$co_email',
            objeto = '$objeto',
            valor = '$valor',
            prestador_tipo = '$prestador_tipo',
            nome_banco = '$nome_banco',
            agencia = '$agencia',
            conta = '$conta',
            prestacao_contas = '$prestacao_contas',
            id_regiao = '$id_regiao',
            id_projeto = '$id_projeto'
            WHERE id_prestador = '$id_prestador'
            LIMIT 1
            ");
    
    $nome_socios = $_REQUEST['nome_socio'];
    $tel_socios  = $_REQUEST['tel_socio'];
    $cpf_socios = $_REQUEST['cpf_socio'];
    $id_socios = $_REQUEST['id_socio'];
    $num_socios = count($id_socios);
    
    //Para cada s�cio ser� realizado um cadastro
    for($cont = 0; $cont < $num_socios; $cont++) {
        if(!empty($nome_socios[$cont]) && !empty($id_socios[$cont])) {
        $update_socio = mysql_query("UPDATE prestador_socio
                SET nome = '$nome_socios[$cont]',
                tel = '$tel_socios[$cont]',
                cpf = '$cpf_socios[$cont]'
                WHERE id_socio = '$id_socios[$cont]'
                LIMIT 1
                ");
        } else if(!empty($nome_socios[$cont]) && empty($id_socios[$cont])) {
            $insert_socio = mysql_query("INSERT INTO prestador_socio(
                nome,
                tel,
                cpf,
                id_prestador
                ) VALUES (
                '$nome_socios[$cont]',
                '$tel_socios[$cont]',
                '$cpf_socios[$cont]',
                '$id_prestador')");
        }
    }
    
    $nome_dependentes = $_REQUEST['nome_dependente'];
    $tel_dependentes = $_REQUEST['tel_dependente'];
    $parentesco_dependentes = $_REQUEST['parentesco_dependente'];
    $id_dependentes = $_REQUEST['id_dependente'];
    $num_dependentes = count($id_dependentes);
    
    //Para cada dependente  ir� realizar um cadastro
    for($cont = 0; $cont < $num_dependentes; $cont++) {
        if(!empty($nome_dependentes[$cont]) && !empty($id_dependentes[$cont])) {
            $update_dependente = mysql_query(" UPDATE prestador_dependente
                                                SET prestador_id = '$id_prestador',
                                                prestador_dep_nome = '$nome_dependentes[$cont]',
                                                prestador_dep_tel = '$tel_dependentes[$cont]',
                                                prestador_dep_parentesco = '$parentesco_dependentes[$cont]'
                                                WHERE prestador_dep_id = '$id_dependentes[$cont]'
                                                LIMIT 1
                                                ");
        } else if(!empty($nome_dependentes[$cont]) && empty($id_dependentes[$cont])) {
            $insert_dependente = mysql_query(" INSERT INTO prestador_dependente(prestador_id,
                                                prestador_dep_nome,
                                                prestador_dep_tel,
                                                prestador_dep_parentesco,
                                                prestador_dep_status
                                                ) VALUES (
                                                '$id_prestador',
                                                '$nome_dependentes[$cont]',
                                                '$tel_dependentes[$cont]',
                                                '$parentesco_dependentes[$cont]',
                                                '1'
                                                )");
        }
    }
    
    //echo "<script>history.go(-2)</script>";
    header('Location: index.php');
}

//Array com os tipos de contrato
$arrTipos = array(
    "1" => "Pessoa Jur�dica",
    "2" => "Pessoa Jur�dica - Cooperativa",
    "3" => "Pessoa F�sica",
    "4" => "Pessoa Jur�dica - Prestador de Servi�o",
    "5" => "Pessoa Jur�dica - Administradora",
    "6" => "Pessoa Jur�dica - Publicidade",
    "7" => "Pessoa Jur�dica Sem Reten��o");

$temContrato = array("1"=>"Sim","0"=>"N�o");

$medidas = PrestadorServico::listMedidasForSelect();

$grauParentesco = montaQuery("grau_parentesco");

$optParentesco = array(0 => "� Selecione o Grau de Parentesco �");

//Array com os poss�veis estados civis
$arrEstadoCivil = array(0 => "� Selecione um Estado Civil �", 1 => "Solteiro(a)", 2 => "Casado(a)", 3 => "Divorciado(a)", 4 => "Vi�vo(a)");

//Montar um array com os tipos de graus de parentesco possiveis,
//retornados da tabela grau_parentesco
foreach($grauParentesco as $value) {
    $optParentesco[$value['id_grau']] = $value['nome'];
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMUL�RIO SELECIONADO */
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$regiaoR = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : null;

$result_contratante = mysql_query("SELECT * FROM master where id_master = '{$usuario['id_master']}'");
$row_contratante = mysql_fetch_array($result_contratante); 
?>
<html>
    <head>
        <title>:: Intranet :: Prestador de Servi�o</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="prestador.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../../js/global.js" type="text/javascript"></script>
                
        <script>
            $(function() {
                $("#regiao").ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto");
                
                $(".data").mask("99/99/9999");
                
                $(".cpf").mask("999.999.999-99");
                
                $(".cnpj").mask("99.999.999/9999-99");
                
                $(".tel").mask("(99)9999-9999?9");
                
                $("#form1").validationEngine({promptPosition : "topRight"});                                                
                
                var conts = 20;
                
                var contd = 20;
                
                $("#adicionar_socio").click(function() {
                    var clone = $("#socio1").clone();
                    conts++;
                    clone.attr("id", "socio" + conts);
                    clone.find("input").val("");
                    $(clone).appendTo("#socios");
                    $(".tel").mask("(99)9999-9999?9");
                    $(".cpf").mask("999.999.999-99");
                });
                
                $("#adicionar_dependente").click(function() {
                    var clone = $("#dependente1").clone();
                    contd++;
                    clone.attr("id", "dependente" + contd);
                    clone.find("input").val("");
                    $(clone).appendTo("#dependentes");
                    $(".tel").mask("(99)9999-9999?9");
                });                                
                
                $("#cad").click(function(){
                    //aplica validate engine, caso tenha preenchido algo
                    if(($("#c_email").val() != '')){
                        $("#c_email").addClass("validate[custom[email]]");
                    }
                    if(($("#co_email").val() != '')){
                        $("#co_email").addClass("validate[custom[email]]");                        
                    }
                    if(($("#encerrado_em").val() != '')){
                        $("#encerrado_em").addClass("validate[custom[dateBr]]");
                    }
                    if(($("#agencia").val() != '')){
                        $("#agencia").addClass("validate[custom[onlyNumber]]");
                    }
                    if(($("#conta").val() != '')){
                        $("#conta").addClass("validate[custom[onlyNumber]]");
                    }
                    
                    var cnpj = validarCNPJ($("#cnpj").val());
                    var c_cnpj = validarCNPJ($("#c_cnpj").val());
                    var cpf = validaCpf($("#cpf").val());
                    var c_cpf = validaCpf($("#c_cpf").val());
                    var cpf_soc = validaCpf($("#cpf_socio1").val());                                        
                    
                    // VALIDA��O PROVIS�RIA DE CNPJ, CPF, ETC, O IDEAL � ADD A FUN��O JS NO VALIDATION ENGINE
                    if(!cnpj){
                        $(".contratante").css({display: 'block', margin: '20px 0 0 0'});
                        $(".contratante").html('CNPJ Inv�lido');
                        $("#cnpj").focus();
                        return false;
                    }else{
                        $(".contratante").css({display: 'none', margin: '0'});
                    }
                    
                    if(!c_cnpj){
                        $(".contratada").css({display: 'block', margin: '20px 0 0 0'});
                        $(".contratada").html('CNPJ Inv�lido');
                        $("#c_cnpj").focus();
                        return false;
                    }else{
                        $(".contratada").css({display: 'none', margin: '0'});
                    }
                    
                    if(!cpf){
                        $(".contratante").css({display: 'block', margin: '20px 0 0 0'});
                        $(".contratante").html('CPF Inv�lido');
                        $("#cpf").focus();
                        return false;
                    }else{
                        $(".contratante").css({display: 'none', margin: '0'});
                    }
                    
                    if(!c_cpf){
                        $(".contratada").css({display: 'block', margin: '20px 0 0 0'});
                        $(".contratada").html('CPF Inv�lido');
                        $("#cpf").focus();
                        return false;
                    }else{
                        $(".contratada").css({display: 'none', margin: '0'});
                    }
                    
                    if((!cpf_soc) && ($("#cpf_socio1").val() != '')){
                        $(".socios").css({display: 'block', margin: '20px 0 0 0'});
                        $(".socios").html('CPF Inv�lido');
                        $("#cpf_socio1").focus();
                        return false;
                    }else{
                        $(".socios").css({display: 'none', margin: '0'});                        
                    }
                    
                    return true;
                });
                
                $("#edit").click(function(){
                    //aplica validate engine, caso tenha preenchido algo
                    if(($("#c_email").val() != '')){
                        $("#c_email").addClass("validate[custom[email]]");
                    }
                    if(($("#co_email").val() != '')){
                        $("#co_email").addClass("validate[custom[email]]");                        
                    }
                    if(($("#encerrado_em").val() != '')){
                        $("#encerrado_em").addClass("validate[custom[dateBr]]");
                    } 
                    
                    var cnpj = validarCNPJ($("#cnpj").val());
                    var c_cnpj = validarCNPJ($("#c_cnpj").val());
                    var cpf = validaCpf($("#cpf").val());
                    var c_cpf = validaCpf($("#c_cpf").val());
                    var cpf_soc = validaCpf($("#cpf_socio1").val());
                    
                    // VALIDA��O PROVIS�RIA DE CNPJ, O IDEAL � ADD A FUN��O JS NO VALIDATION ENGINE
                    if(!cnpj){
                        $(".contratante").css({display: 'block', margin: '20px 0 0 0'});
                        $(".contratante").html('CNPJ Inv�lido');
                        $("#cnpj").focus();
                        return false;
                    }else{
                        $(".contratante").css({display: 'none', margin: '0'});
                    }
                    
                    if(!c_cnpj){
                        $(".contratada").css({display: 'block', margin: '20px 0 0 0'});
                        $(".contratada").html('CNPJ Inv�lido');
                        $("#c_cnpj").focus();
                        return false;
                    }else{
                        $(".contratada").css({display: 'none', margin: '0'});
                    }
                    
                    if(!cpf){
                        $(".contratante").css({display: 'block', margin: '20px 0 0 0'});
                        $(".contratante").html('CPF Inv�lido');
                        $("#cpf").focus();
                        return false;
                    }else{
                        $(".contratante").css({display: 'none', margin: '0'});
                    }
                    
                    if(!c_cpf){
                        $(".contratada").css({display: 'block', margin: '20px 0 0 0'});
                        $(".contratada").html('CPF Inv�lido');
                        $("#c_cpf").focus();
                        return false;
                    }else{
                        $(".contratada").css({display: 'none', margin: '0'});
                    }
                    
                    if((!cpf_soc) && ($("#cpf_socio1").val() != '')){
                        $(".socios").css({display: 'block', margin: '20px 0 0 0'});
                        $(".socios").html('CPF Inv�lido');
                        $("#cpf_socio1").focus();
                        return false;
                    }else{
                        $(".socios").css({display: 'none', margin: '0'});                        
                    }
                    
                    return true;
                });
                
            });
        </script>
        <style>
            .data{width: 80px;}
            .colEsq{
                float: left;
                width: 55%;
                margin-top: -10px;
                min-height: 0px;
                border-right: 0px;
                margin-right: 0px;
            }
            .colDir{
                width: auto;
                min-width: 0px;
                margin-left: 0px;
                min-height: 0px;
                border: 0px solid #ccc;
                padding: 0px;
            }
            fieldset{
                margin-top: 10px;
            }
            fieldset legend{
                font-family: 'Exo 2', sans-serif;
                font-size: 16px!important;
                font-weight: bold;
            }
            .first{
                vertical-align: 0!important;
            }
            .first-2{
                vertical-align: 0!important;
            }
            .contratante, .contratada, .socios{
                display: none;
            }
        </style>
    </head>
    <body class="novaintra">
        <div id="content" style="width: 850px;">
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Prestador de Servi�o</h2>
                    </div>
                </div>
                
                <fieldset>
                    <legend>Dados do Projeto</legend>
                    <?php if (count($saidas) == 0) {?>
                    <div class="colEsq" style="margin-top:0;">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />                                                
                        <p><label class="first">Regi�o:</label> <?php echo montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='validate[custom[select]]' style='width: 300px;'") ?></p>
                        <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1"=>"� Selecione a Regi�o �"), $projetoSel, "id='projeto' name='projeto' class='validate[custom[select]]'") ?></p>
                    </div>
                    <?php }else{ ?>
                    <input type="hidden" name="regiao" id="projeto_pre" value="<?php echo $prestador['id_regiao']; ?>" />
                    <input type="hidden" name="projeto" id="regiao_pre" value="<?php echo $prestador['id_projeto']; ?>" />
                    <?php } ?>
                    <div class="colDir">
                        <p><label class='first'>Data Inicio:</label><input type="text" name="contratado_em" id="contratado_em" value="<?php echo $prestador['contratado_embr']?>" class="data validate[required,custom[dateBr]]" /></p>
                        <p><label class='first'>Data Final:</label><input type="text" name="encerrado_em" id="encerrado_em" value="<?php echo $prestador['encerrado_embr']?>" class="data" /></p>
                    </div>
                </fieldset>
                <div id='message-box' class='message-red contratante'></div>
                <?php if(isset($_REQUEST['prestador']) && !empty($_REQUEST['prestador'])) { ?>                
                
                <fieldset>
                    <legend>Dados do Contratante</legend>
                    <p><label class='first'>Contratante:</label><input type="text" name="contratante" id="contratante" value="<?php echo $prestador['contratante']?>" size="108" class="validate[required]" /></p>
                    <p><label class='first'>Endere�o:</label><input type="text" name="endereco" id="endereco" value="<?php echo $prestador['endereco']?>" size="108" /></p>
                    <div class="colEsq">
                        <p><label class='first'>CNPJ:</label><input type="text" name="cnpj" id="cnpj" value="<?php echo $prestador['cnpj']?>" size="16" class="cnpj validate[required]" /></p>
                        <p><label class='first'>Responsavel:</label><input type="text" name="responsavel" id="responsavel" value="<?php echo $prestador['responsavel']?>" size="38" /></p>
                        <p><label class='first'>Nascionalidade:</label><input type="text" name="nacionalidade" id="nacionalidade" value="<?php echo $prestador['nacionalidade']?>" size="16" /></p>
                        <p><label class='first'>RG:</label><input type="text" name="rg" id="rg" value="<?php echo $prestador['rg']?>" size="16" class="validate[required]" /></p>
                    </div>
                    <div class="colDir">
                        <p><label class='first'>Estado Civil:</label><input type="text" name="nacionalidade" id="nacionalidade" value="<?php echo $prestador['civil']?>" size="16" /></p>
                        <p><label class='first'>Forma��o:</label><input type="text" name="formacao" id="formacao" value="<?php echo $prestador['formacao']?>" size="30" /></p>
                        <p><label class='first'>CPF:</label><input type="text" name="cpf" id="cpf" value="<?php echo $prestador['cpf']?>" size="30" class="cpf validate[required]" /></p>
                    </div>
                </fieldset>
                <?php } else { ?>
                <fieldset>
                    <legend>Dados do Contratante</legend>
                    <p><label class='first'>Contratante:</label><input type="text" name="contratante" id="contratante" value="<?php echo $row_contratante['razao']?>" size="108" class="validate[required]" /></p>
                    <p><label class='first'>Endere�o:</label><input type="text" name="endereco" id="endereco" value="<?php echo $row_contratante['endereco']?>" size="108" /></p>
                    <div class="colEsq">
                        <p><label class='first'>CNPJ:</label><input type="text" name="cnpj" id="cnpj" value="<?php echo $row_contratante['cnpj']?>" size="16" class="cnpj validate[required]" /></p>
                        <p><label class='first'>Responsavel:</label><input type="text" name="responsavel" id="responsavel" value="<?php echo $row_contratante['responsavel']?>" size="38" /></p>
                        <p><label class='first'>Nascionalidade:</label><input type="text" name="nacionalidade" id="nacionalidade" value="<?php echo $row_contratante['nacionalidade']?>" size="16" /></p>
                        <p><label class='first'>RG:</label><input type="text" name="rg" id="rg" value="<?php echo $row_contratante['rg']?>" size="16" class="validate[required]" /></p>
                    </div>
                    <div class="colDir">
                        <p><label class='first'>Estado Civil:</label><input type="text" name="nacionalidade" id="nacionalidade" value="<?php echo $row_contratante['civil']?>" size="16" /></p>
                        <p><label class='first'>Forma��o:</label><input type="text" name="formacao" id="formacao" value="<?php echo $row_contratante['formacao']?>" size="30" /></p>
                        <p><label class='first'>CPF:</label><input type="text" name="cpf" id="cpf" value="<?php echo $row_contratante['cpf']?>" size="30" class="cpf" /></p>
                    </div>
                </fieldset>
                <?php } ?>
                <div id='message-box' class='message-red contratada'></div>
                <fieldset>
                    <legend>Dados da Empresa Contratada</legend>
                    <p><label class='first'>Nome Fantasia:</label><input type="text" name="c_fantasia" id="c_fantasia" value="<?php echo $prestador['c_fantasia']?>" size="108" class="validate[required]" /></p>
                    <p><label class='first'>Raz�o Social:</label><input type="text" name="c_razao" id="c_razao" value="<?php echo $prestador['c_razao']?>" size="108" class="validate[required]" /></p>
                    <p><label class='first'>Endere�o:</label><input type="text" name="c_endereco" id="c_endereco" value="<?php echo $prestador['c_endereco']?>" size="108" /></p>
                    <p><label class='first'>Tipo de contrato:</label><?php echo montaSelect($arrTipos, $prestador['prestador_tipo'], "id='prestador_tipo' name='prestador_tipo'") ?></p>
                    <div class="colEsq">
                        <p><label class='first'>CNPJ:</label><input type="text" name="c_cnpj" id="c_cnpj" value="<?php echo $prestador['c_cnpj']?>" size="17" class="cnpj validate[required]" /></p>
                        <p><label class='first'>IM:</label><input type="text" name="c_im" id="c_im" value="<?php echo $prestador['c_im']?>" size="17" /></p>
                        <p><label class='first'>Fax:</label><input type="text" name="c_fax" id="c_fax" value="<?php echo $prestador['c_fax']?>" size="17" class="tel" /></p>
                    </div>
                    <div class="colDir">
                        <p><label class='first'>IE:</label><input type="text" name="c_ie" id="c_ie" value="<?php echo $prestador['c_ie']?>" size="12" /></p>
                        <p><label class='first'>Telefone:</label><input type="text" name="c_tel" id="c_tel" value="<?php echo $prestador['c_tel']?>" size="12" class="tel"/></p>
                    </div>
                    <p class="clear valid_email"><label class='first'>E-mail:</label><input type="text" name="c_email" id="c_email" value="<?php echo $prestador['c_email']?>" size="108" /></p>
                    
                    <div class="colEsq">
                        <p><label class='first'>Responsavel:</label><input type="text" name="c_responsavel" id="c_responsavel" value="<?php echo $prestador['c_responsavel']?>" size="35" class="validate[required]" /></p>
                        <p><label class='first'>Nascionalidade:</label><input type="text" name="c_nacionalidade" id="c_nacionalidade" value="<?php echo $prestador['c_nacionalidade']?>" size="35" /></p>
                        <p><label class='first'>RG:</label><input type="text" name="c_rg" id="c_rg" value="<?php echo $prestador['c_rg']?>" size="15" class="validate[required]" /></p>
                    </div>
                    <div class="colDir">
                        <p><label class='first'>Estado Civil:</label><?php echo montaSelect($arrEstadoCivil, $prestador['c_civil'], "id='c_civil' name='c_civil'") ?></p>
                        <p><label class='first'>Forma��o:</label><input type="text" name="c_formacao" id="c_formacao" value="<?php echo $prestador['c_formacao']?>" size="30" /></p>
                        <p><label class='first'>CPF:</label><input type="text" name="c_cpf" id="c_cpf" value="<?php echo $prestador['c_cpf']?>" size="15" class="cpf validate[required]" /></p>
                    </div>
                    
                    <p class="clear"><label class='first'>Site:</label><input type="text" name="c_site" id="c_site" value="<?php echo $prestador['c_site']?>" size="108" /></p>
                </fieldset>

                <fieldset>
                    <legend>Dados da pessoa de contato na contratada</legend>
                    <p><label class='first'>Nome Completo:</label><input type="text" name="co_responsavel" id="co_responsavel" value="<?php echo $prestador['co_responsavel']?>" size="108" class="validate[required]"/></p>
                    <p class="valid_email"><label class='first'>Email:</label><input type="text" name="co_email" id="co_email" value="<?php echo $prestador['co_email']?>" size="108" /></p>
                    <div class="colEsq">
                        <p><label class='first'>Telefone:</label><input type="text" name="co_tel" id="co_tel" value="<?php echo $prestador['co_tel']?>" size="12" class="tel" /></p>
                        <p><label class='first'>Estado Civil:</label><?php echo montaSelect($arrEstadoCivil, $prestador['co_civil'], "id='co_civil' name='co_civil'") ?></p>
                    </div>
                    <div class="colDir">
                        <p><label class='first'>Fax:</label><input type="text" name="co_fax" id="co_fax" value="<?php echo $prestador['co_fax']?>" size="12" class="tel" /></p>
                        <p><label class='first'>Nacionalidade:</label><input type="text" name="co_nacionalidade" id="co_nacionalidade" value="<?php echo $prestador['co_nacionalidade']?>" size="30" /></p>
                    </div>
                </fieldset>
                
                <fieldset>
                    <legend>Dados do contrato</legend>
                    <!--<p><label class='first-2'>Tem contrato?</label><?php // echo montaSelect($temContrato, $prestador['prestacao_contas'], "id='prestacao_contas' name='prestacao_contas'") ?></p>-->
                    
                    <p>
                        <label class='first-2'>Tem contrato?</label>
                        <select id="prestacao_contas" name="prestacao_contas">
                            <option value="1" <?php echo selected(1, $prestador['prestacao_contas']); ?>>Sim</option>
                            <option value="0" <?php echo selected(0, $prestador['prestacao_contas']); ?>>N�o</option>                            
                        </select>
                    </p>
                    
                    <!--<p><label class='first-2'>Assunto:</label><textarea name="assunto" id="assunto" rows="5" cols="72"><?php echo $prestador['assunto']?></textarea></p>-->
                    <p><label class='first-2' style="vertical-align:top!important;">Objeto:</label><textarea name="objeto" id="objeto" rows="5" cols="72"><?php echo $prestador['objeto']?> </textarea></p>
                    <!--<p><label class='first-2'>Especifica��o:</label><textarea name="especificacao" id="especificacao" rows="5" cols="72"><?php echo $prestador['especificacao']?></textarea></p>
                    <p><label class='first-2'>Munic�pio onde ser�<br>executado o servi�o:</label><input type="text" name="co_municipio" id="co_municipio" value="<?php echo $prestador['co_municipio']?>" size="40" /></p>-->
                    <div class="colEsq">
                        <p><label class='first-2'>Unidade de Medida:</label><?php echo montaSelect($medidas, $prestador['id_medida'], "id='id_medida' name='id_medida'")?></p>
                    </div>
                    <div class="colDir">
                        <p><label class='first'>Valor:</label><input type="text" name="valor" id="valor" value="<?php if($prestador['valor'] > 0) {echo number_format($prestador['valor'],2,",",".");}else{echo "0";}?>" size="20" class="validate[required]" /></p>
                    </div>
                </fieldset>
                
                <fieldset>
                    <legend>Dados Banc�rios</legend>
                    <p><label class='first'>Banco:</label><input type="text" name="nome_banco" id="nome_banco" value="<?php echo $prestador['nome_banco']?>" size="30" /></p>
                    <p><label class='first'>Ag�ncia:</label><input type="text" name="agencia" id="agencia" value="<?php echo $prestador['agencia']?>" size="30" /></p>
                    <p><label class='first'>Conta:</label><input type="text" name="conta" id="conta" value="<?php echo $prestador['conta']?>" size="30" /></p>
                </fieldset>
                
                <div id='message-box' class='message-red socios'></div>
                <fieldset>
                    <legend>S�cios</legend>
                    <input style="margin-left: 10px;" type="button" id="adicionar_socio" name="adicionar_socio" value="Adicionar S�cio"/>
                    <table cellpadding="0" cellspacing="0" border="0" class="grid" style="width: 98%; margin: 10px;">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Telefone</th>
                                <th>CPF</th>
                            </tr>
                        </thead>
                        <tbody id="socios">
                            <?php //Enquanto houver s[ocios no array retornado
                            //ir� criar e adicionar campos com as informa��es do dependente
                            for($cont = 1; $cont <= $num_socios; $cont++) {?>
                            <tr id="socio<?php echo $cont; ?>">
                                <td><input type="text" name="nome_socio[]" id="nome_socio1" value="<?php echo $socios[$cont]['nome'] ?>" size="38" /></td>
                                <td><input type="text" name="tel_socio[]" id="tel_socio1" value="<?php echo $socios[$cont]['tel'] ?>" size="38" class="tel" /></td>
                                <td><input type="text" name="cpf_socio[]" id="cpf_socio1" value="<?php echo $socios[$cont]['cpf'] ?>" size="38" class="cpf" /></td>
                                <?php if(isset($_REQUEST['prestador']) && !empty($_REQUEST['prestador'])){ ?>
                                <input type="hidden" name="id_socio[]" value="<?php echo $socios[$cont]['id_socio']; ?>"/>
                                <?php } ?>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </fieldset>
                
                <fieldset>
                    <legend>Dependentes</legend>
                    <input style="margin-left: 10px;" type="button" id="adicionar_dependente" name="adicionar_dependente" value="Adicionar Dependente"/>
                    <table cellpadding="0" cellspacing="0" border="0" class="grid" style="width: 98%; margin: 10px;">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Telefone</th>
                                <th>Grau Parentesco</th>
                            </tr>
                        </thead>
                        <tbody id="dependentes">
                            <?php //Enquanto houver dependentes no array retornado
                            //ir� criar e adicionar campos com as informa��es do dependente
                            for($cont = 1; $cont <= $num_dependentes; $cont++) {?>
                            <tr id="dependente<?php echo $cont; ?>">
                                <td><input type="text" id="nome_dependente" name="nome_dependente[]" value="<?php echo $dependentes[$cont]['prestador_dep_nome'] ?>" size="38" /></td>
                                <td><input type="text" id="tel_dependente" name="tel_dependente[]" value="<?php echo $dependentes[$cont]['prestador_dep_tel'] ?>" size="38" class="tel" /></td>
                                <td><?php echo montaSelect($optParentesco, $dependentes[$cont]['prestador_dep_parentesco'], "id='parentesco_dependente' name='parentesco_dependente[]' class='required[custom[select]]'") ?></td>
                                <?php if(isset($_REQUEST['prestador']) && !empty($_REQUEST['prestador'])){ ?>
                                <input type="hidden" name="id_dependente[]" value="<?php echo $dependentes[$cont]['prestador_dep_id']; ?>"/>
                                <?php } ?>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </fieldset>
                
                <p class="controls">
                    <?php //Verifica se foi selecionado um prestador na tela anterior
                        //Caso tenha sido selecionado o bot�o ser� de edi��o
                        if(isset($_REQUEST['prestador']) && !empty($_REQUEST['prestador'])) { ?>
                        <input type="hidden" name="id_prestador" value="<?php echo $id_prestador; ?>"/>
                        <?php //Verifica se o contrato j� foi encerrado anteriormente
                            //para nao substituir o usu�rio que o encerrou anteriormente
                            //no update da linha
                            if(!empty($encerrado)) { ?>
                        <input type="hidden" name="encerrado_por" value="<?php echo $encerrado; ?>"/>    
                        <?php } ?>
                        <input type="submit" name="editar" id="edit" value="Salvar" /> 
                    <?php } 
                            //Caso n�o tenha sido selecionado, ser� um novo cadastro
                            else { ?>
                        <input type="submit" name="cadastrar" id="cad" value="Cadastrar" /> 
                    <?php } ?>
                    <input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';" /> 
                </p>
            </form>
        </div>
    </body>
</html>