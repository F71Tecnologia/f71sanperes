<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/global.php');
include('../../classes/FuncoesClass.php');
include("../../classes_permissoes/acoes.class.php");

$acoes = new Acoes();

$usuario = carregaUsuario();
$master = $usuario['id_master'];
$id_regiao = $usuario['id_regiao'];
$id_usuario = $_COOKIE['logado'];

if(isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method']=="alteraSalario"){
    $id_curso = $_REQUEST['id_curso'];
    $data_cad = date('Y-m-d');
    $salario_antigo = $_REQUEST['salario_antigo'];
    $salario_novo = $_REQUEST['salario_new'];
    $diferenca = $_REQUEST['difere'];
    
    mysql_query("INSERT INTO rh_salario (id_curso,data,salario_antigo,salario_novo,diferenca,user_cad,status) VALUES 
    ('$id_curso','$data_cad','$salario_antigo','$salario_novo','$diferenca','$id_usuario','1')") or die (mysql_error());
    
    mysql_query("UPDATE curso SET salario = '$salario_novo', valor = '$salario_novo' WHERE id_curso = '$id_curso' LIMIT 1") or die (mysql_error());
    
    $return = array('status'=>1);
    //$return = $_REQUEST;
    $return['valor'] = "R$ ".number_format($_REQUEST['salario_new'],2,",",".");
    //"R$ ".number_format($_REQUEST['salario_new'],2,",",".");
    echo json_encode($return);
    exit;
}

$sql = FuncoesClass::getRhHorario($_REQUEST['curso']);
$total_horario = mysql_num_rows($sql);

$row = FuncoesClass::getCursosID($_REQUEST['curso']);

$altera_funcao = FuncoesClass::alteraFuncao($usuario, $id_regiao, $id_usuario);

//dados para voltar no index com select preenchido
$regiao_selecionada = $_REQUEST['hide_regiao'];
$projeto_selecionado = $_REQUEST['hide_projeto'];

$sql_departamento = "SELECT * FROM departamentos ORDER BY nome";
$sql_departamento = mysql_query($sql_departamento);
$arrayDepartamentos[0] = 'Selecione';
while($row_departamento = mysql_fetch_assoc($sql_departamento)){
    $arrayDepartamentos[$row_departamento['id_departamento']] = $row_departamento['nome'];
}

if($regiao_selecionada == ''){
    $_SESSION['regiao_select'];
    $_SESSION['projeto_select'];
    session_write_close();
}else{
    $_SESSION['regiao_select'] = $regiao_selecionada;
    $_SESSION['projeto_select'] = $projeto_selecionado;
    session_write_close();
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Editar Função ".$row['nome_funcao']);
$breadcrumb_pages = array(/*"Gestão de RH"=>"../../rh", */"Gestão de Funções"=>"index2.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Editar Função <?=$row['nome_funcao']?></title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <!--link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css"-->
        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="jquery.autocomplete.css" rel="stylesheet" type="text/css" />  
        <style>
            
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
            .bt-image{                
                cursor: pointer;
            }
            .some_insa, #hide_noturno, .hide_noturno{
                display: none;
            }
        </style>
    </head>
    <body>
    <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>Editar Função <?=$row['nome_funcao']?></small></h2></div>
                </div>
            </div>
            <div class="row">
                <form action="" class="form-horizontal" method="post" name="form1" id="form1" autocomplete="off">
                    <input type="hidden" name="id_curso" id="id_curso" value="<?php echo $row['id_curso']; ?>" />
                    <input type="hidden" name="regiao" id="regiao" value="<?php echo $row['id_regiao']; ?>" />
                    <input type="hidden" name="projeto" id="projeto" value="<?php echo $row['campo3']; ?>" />
                    <input type="hidden" name="id_cbo" id="id_cbo" value="<?php echo $row['cbo_codigo']; ?>" />
                    <input type="hidden" name="contratacao_curso" id="contratacao_curso" value="<?php echo $row['tipo']; ?>" />
                    <div class="col-lg-12 form_funcoes">
                        <div class="panel panel-default">
                            <div class="panel-heading">Dados da Função</div>
                            <div class="panel-body">
                                <fieldset id="func1">
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Nome da Função:</label>
                                        <div class="col-lg-10">
                                            <input type="text" name="nome_funcao" id="nome_funcao" value="<?=$row['nome_funcao']?>" class="form-control validate[required]" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Área:</label>
                                        <div class="col-lg-10">
                                            <input type="text" name="area" id="area" value="<?=$row['area']?>" class="form-control validate[required]" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Departamento:</label>
                                        <div class="col-lg-10">
                                            <?php echo montaSelect($arrayDepartamentos, $row['id_departamento'], 'name="departamento" id="departamento" class="form-control validate[required,custom[select]] departamento"'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Nome do CBO:</label>
                                        <div class="col-lg-10">
                                            <input type="text" name="cbo" id="cbo" value="<?=$row['nome_cbo']?>" class="form-control validate[required]" placeholder="Ex: Assistente administrativo  - 4110.10" />
                                            <span id="selection"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Local:</label>
                                        <div class="col-lg-10">
                                            <input type="text" name="local" id="local" value="<?=$row['local']?>" class="form-control validate[required]" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Salário:</label>
                                        <div class="col-lg-4 control-label text-left">
                                            <?php if($acoes->verifica_permissoes(84)){ ?>
                                                <span id='textVal'><?=formataMoeda($row['salario'])?></span>
                                                <img src="../../imagens/icones/icon-edit.gif" title="Editar Valor" class="edita_valor bt-image" data-type="salario" data-key="<?=$row['id_curso']?>" data-toggle="modal" data-target="#box_salario" />
                                            <?php }else{
                                                echo formataMoeda($row['valor']);
                                            } ?>
                                        </div>
                                        <label for="nome" class="col-lg-2 control-label">Parcelas:</label>
                                        <div class="col-lg-4">
                                            <input type="text" name="parcelas" id="parcelas" maxlength="4" class="form-control" value="<?=$row['parcelas']?>"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 text-right no-margin-b">Qtd. Máxima de Contratação:</label>
                                        <div class="col-lg-4">
                                            <input type="text" name="qtd_contratacao" id="qtd_contratacao" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]]" value="<?=$row['qnt_maxima']?>" />
                                        </div>
                                        <label for="nome" class="col-lg-2 control-label">Horas:</label>
                                        <div class="col-lg-4">
                                            <input type="text" name="horas" id="horas" maxlength="4" class="form-control" value="<?=$row['hora_mes']?>" />
                                        </div>
                                    </div>
                                    <?php if($row['tipo'] == 2){ ?>
                                        <div class="form-group">
                                            <label for="nome" class="col-lg-2 control-label"></label>
                                            <div class="col-lg-2">
                                                <input type="radio" name="periculosidade" value="" id="insal" <?php if($row['tipo_insalubridade'] != '0'){ echo "checked"; } ?> /> Insalubridade
                                            </div>
                                            <div class="col-lg-2">
                                                <input type="radio" name="periculosidade" value="1" id="peric" <?php if($row['periculosidade_30'] == '1'){ echo "checked"; } ?> /> Periculosidade 30%
                                            </div>
                                        </div>
                                        <div class="form-group some_insa">
                                            <label for="nome" class="col-lg-2 control-label">Insalubridade:</label>
                                            <div class="col-lg-10">
                                                <select name="insalubridade" id="insalubridade" class="form-control">
                                                    <option value="-1">« Selecione »</option>
                                                    <option value="1" <?php echo selected(1, $row['tipo_insalubridade']); ?>>Insalubridade 20%</option>
                                                    <option value="2" <?php echo selected(2, $row['tipo_insalubridade']); ?>>Insalubridade 40%</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group some_insa">
                                            <label for="nome" class="col-lg-2 control-label">Quantidade de Salários:</label>
                                            <div class="col-lg-10">
                                                <input type="text" name="qtd_salarios" id="qtd_salarios" class="form-control" maxlength="4" value="<?=$row['qnt_salminimo_insalu']?>" />
                                            </div>
                                        </div>
                                    <?php }else{ ?>
                                        <div class="form-group">
                                            <label for="nome" id="p_valor_hora" class="col-lg-2 control-label">Valor Hora:</label>
                                            <div class="col-lg-10" id="p_valor_hora">
                                                <input type="text" name="valor_hora_cooperado" id="valor_hora" class="form-control money" value="<?=$row['valor_hora']?>" />
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Descrição:</label>
                                        <div class="col-lg-10">
                                            <textarea name="descricao" id="descricao" class="form-control"><?=$row['descricao']?></textarea>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="panel-body">
                                <?php
                                if($row['tipo']==2){

                                    $posicao = 0;
                                    $numRows = mysql_num_rows($sql);
                                    if($numRows>0){ 
                                        while($rst = mysql_fetch_array($sql)){
                                            $folga = $rst['folga']; ?>
                                            <input type="hidden" name="id_horario[]" id="id_horario" value="<?=$rst['id_horario']?>" />
                                            <fieldset class="horario" data-position="<?=$posicao?>">
                                                <legend>Dados do Horário</legend>
                                                <div class="form-group">
                                                    <div class="">
                                                        <label class="col-lg-1 col-lg-offset-11 control-label pull-right pointer del_hor" id="del_hor"><img src="../../imagens/icones/icon-delete.gif" title="Deletar horário" /></label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="nome" class="col-lg-2 control-label">Nome do Horário:</label>
                                                    <div class="col-lg-10">
                                                        <input type="text" name="nome_horario[]" id="nome_horario" class="form-control validate[required] limpa nome_horario" value="<?=$rst['nome']?>" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="nome" class="col-lg-2 control-label">Observações:</label>
                                                    <div class="col-lg-10">
                                                        <input type="text" name="obs[]" id="obs" class="form-control limpa obs" value="<?=$rst['obs']?>" />
                                                    </div>
                                                </div>
                                                <div class="form-group remove">
                                                    <label for="nome" class="col-md-2 control-label">Preenchimento:</label>
                                                    <label for="nome" class="col-md-1 control-label no-padding-l">Entrada</label>
                                                    <div class="col-md-1 no-padding-l">
                                                        <input type="text" name="entrada[]" id="entrada" class="form-control preenchimento validate[required] limpa entrada" value="<?=$rst['entrada_1']?>" />
                                                    </div>
                                                    <label for="nome" class="col-md-2 control-label">Saída Almoço</label>
                                                    <div class="col-md-1 no-padding-l">
                                                        <input type="text" name="ida_almoco[]" id="ida_almoco" class="form-control preenchimento validate[required] limpa ida_almoco" value="<?=$rst['saida_1']?>" />
                                                    </div>
                                                    <label for="nome" class="col-md-2 control-label">Retorno Almoço</label>
                                                    <div class="col-md-1 no-padding-l">
                                                        <input type="text" name="volta_almoco[]" id="volta_almoco" class="form-control preenchimento validate[required] limpa volta_almoco" value="<?=$rst['entrada_2']?>" />
                                                    </div>
                                                    <label for="nome" class="col-md-1 control-label">Saída</label>
                                                    <div class="col-md-1 no-padding-l">
                                                        <input type="text" name="saida[]" id="saida" class="form-control preenchimento validate[required] limpa saida" value="<?=$rst['saida_2']?>" />
                                                    </div>
                                                </div>
                                                <div class="form-group esquerda" id="esquerda">
                                                    <label for="nome" class="col-lg-2 control-label">Horas Mês:</label>
                                                    <div class="col-lg-4">
                                                        <input type="text" name="horas_mes[]" id="horas_mes" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]] limpa horas_mes" value="<?=$rst['horas_mes']?>" />
                                                    </div>
                                                    <label for="nome" class="col-lg-2 control-label">Dias Mês:</label>
                                                    <div class="col-lg-4">
                                                        <input type="text" name="dias_mes[]" id="dias_mes" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]] limpa dias_mes" value="<?=$rst['dias_mes']?>" />
                                                    </div>
                                                </div>
                                                <div class="form-group direita" id="direita">
                                                    <label for="nome" class="col-lg-2 control-label">Horas Semanais:</label>
                                                    <div class="col-lg-4">
                                                        <input type="text" name="horas_semana[]" id="horas_semana" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]] limpa horas_semana" value="<?=$rst['horas_semanais']?>" />
                                                    </div>
                                                    <label for="nome" class="col-lg-2 control-label">Dias Semana:</label>
                                                    <div class="col-lg-4">
                                                        <input type="text" name="dias_semana[]" id="dias_semana" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]] limpa dias_semana" value="<?=$rst['dias_semana']?>" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="nome" class="col-lg-2 control-label">Adicional Noturno:</label>
                                                    <div class="col-lg-2">
                                                        <div class="radio">
                                                            <input type="radio" name="noturno[<?=$posicao?>]" value="1" id="n_sim" class="n_sim check_not" <?php if($rst['adicional_noturno'] == '1'){ echo "checked"; } ?> /> Sim
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="radio">
                                                            <input type="radio" name="noturno[<?=$posicao?>]" value="0" id="n_nao" class="n_nao check_not" <?php if($rst['adicional_noturno'] != '1'){ echo "checked"; } ?> /> Não
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="hide_noturno" class="hide_noturno">
                                                    <label for="nome" class="col-lg-2 control-label">Horas Noturno:</label>
                                                    <div class="col-lg-10">
                                                        <input type="text" name="horas_noturno[]" id="horas_noturno" maxlength="4" class="form-control horas_noturno" value="<?=$rst['horas_noturnas']?>" />
                                                    </div>
                                                </div>
                                            </fieldset>
                                        <?php $posicao++; 
                                        }
                                    } else { ?>
                                        <fieldset class="horario" data-position="0">
                                    <legend>Dados do Horário</legend>
                                    <div class="form-group">
                                        <div class="">
                                            <label class="col-lg-1 col-lg-offset-11 control-label pull-right pointer del_hor" id="del_hor"><img src="../../imagens/icones/icon-delete.gif" title="Deletar horário" /></label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Nome do Horário:</label>
                                        <div class="col-lg-10">
                                            <input type="text" name="nome_horario[]" id="nome_horario" class="form-control validate[required] limpa nome_horario" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Observações:</label>
                                        <div class="col-lg-10">
                                            <input type="text" name="obs[]" id="obs" class="form-control limpa obs" />
                                        </div>
                                    </div>
                                    <div class="form-group remove">
                                        <label for="nome" class="col-lg-2 control-label">Preenchimento:</label>
                                        <div class="col-lg-2">
                                            Entrada <input type="text" name="entrada[]" id="entrada_0" class="form-control preenchimento validate[required] limpa entrada" data-ordem="0" />
                                        </div>
                                        <div class="col-lg-2">
                                            Saída Almoço <input type="text" name="ida_almoco[]" id="ida_almoco_0" class="form-control preenchimento validate[required] limpa ida_almoco" />
                                        </div>
                                        <div class="col-lg-2">
                                            Retorno Almoço <input type="text" name="volta_almoco[]" id="volta_almoco_0" class="form-control preenchimento validate[required] limpa volta_almoco" />
                                        </div>
                                        <div class="col-lg-2">
                                            Saída <input type="text" name="saida[]" id="saida_0" class="form-control preenchimento validate[required] limpa saida" />
                                        </div>
                                    </div>
                                    <div class="form-group esquerda" id="esquerda">
                                        <label for="nome" class="col-lg-2 control-label">Horas Mês:</label>
                                        <div class="col-lg-4">
                                            <input type="text" name="horas_mes[]" id="horas_mes" size="30" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]] limpa horas_mes" />
                                        </div>
                                        <label for="nome" class="col-lg-2 control-label">Dias Mês:</label>
                                        <div class="col-lg-4">
                                            <input type="text" name="dias_mes[]" id="dias_mes" size="30" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]] limpa dias_mes" />
                                        </div>
                                    </div>
                                    <div class="form-group direita" id="direita">
                                        <label for="nome" class="col-lg-2 control-label">Horas Semanais:</label>
                                        <div class="col-lg-4">
                                            <input type="text" name="horas_semana[]" id="horas_semana" size="30" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]] limpa horas_semana" />
                                        </div>
                                        <label for="nome" class="col-lg-2 control-label">Dias Semana:</label>
                                        <div class="col-lg-4">
                                            <input type="text" name="dias_semana[]" id="dias_semana" size="30" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]] limpa dias_semana" />
                                        </div>
                                    </div>
                                    <div class="form-group direita" id="direita">
                                        <label for="nome" class="col-lg-2 control-label">Folgas:</label>
                                        <div class="col-lg-2">
                                            <div class="checkbox">
                                                <label><input class="check" type="checkbox" name="folga[][0]" value="1" /> Sábado</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="checkbox">
                                                <label><input class="check" type="checkbox" name="folga[][1]" value="2" /> Domingo</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="checkbox">
                                                <label><input class="check" type="checkbox" name="folga[][2]" value="5" /> Plantonista</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Adicional Noturno:</label>
                                        <div class="col-lg-2">
                                            <div class="radio">
                                                <label><input type="radio" name="noturno[]" value="1" id="n_sim" class="n_sim check_not" /> Sim</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="radio">
                                                <label><input type="radio" name="noturno[]" value="0" id="n_nao" class="n_nao check_not" /> Não</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="hide_noturno">
                                        <label for="nome" class="col-lg-2 control-label">Horas Noturno:</label>
                                        <div class="col-lg-10">
                                            <input type="text" name="horas_noturno[]" id="horas_noturno" maxlength="4" class="form-control horas_noturno" />
                                        </div>
                                    </div>
                                </fieldset>
<!--                                        <fieldset class="horario" data-position="0">
                                            <div id="del_hor" class="del_hor"><img src="../../imagens/icones/icon-delete.gif" title="Deletar horário" /></div>
                                            <legend>Dados do Horário</legend>
                                            <p>
                                                <label class='first'>Nome do Horário:</label>
                                                <input type="text" name="nome_horario[]" id="nome_horario" size="93" class="nome_horario validate[required] limpa" />
                                            </p>
                                            <p>
                                                <label class='first'>Observações:</label>
                                                <input type="text" name="obs[]" id="obs" class="obs limpa" size="93" />
                                            </p>
                                            <p class="remove">
                                                <label class='first'>Preenchimento:</label>
                                                Entrada <input type="text" name="entrada[]" id="entrada" size="10" class="entrada preenchimento validate[required] limpa" data-ordem="0" />
                                                Saída Almoço <input type="text" name="ida_almoco[]" id="ida_almoco" size="10" class="ida_almoco preenchimento validate[required] limpa" />
                                                Retorno Almoço <input type="text" name="volta_almoco[]" id="volta_almoco" size="10" class="volta_almoco preenchimento validate[required] limpa" />
                                                Saída <input type="text" name="saida[]" id="saida" size="10" class="saida preenchimento validate[required] limpa" />
                                            </p>

                                            <div id="esquerda" class="esquerda">
                                                <p>
                                                    <label class='first'>Horas Mês:</label>
                                                    <input type="text" name="horas_mes[]" id="horas_mes" size="30" maxlength="4" class="horas_mes validate[required,custom[onlyNumber]] limpa" />
                                                </p>
                                                <p>
                                                    <label class='first'>Dias Mês:</label>
                                                    <input type="text" name="dias_mes[]" id="dias_mes" size="30" maxlength="4" class="dias_mes validate[required,custom[onlyNumber]] limpa" />
                                                </p>
                                            </div>

                                            <div id="direita" class="direita">
                                                <p>
                                                    <label class='first'>Horas Semanais:</label>
                                                    <input type="text" name="horas_semana[]" id="horas_semana" size="30" maxlength="4" class="horas_semana validate[required,custom[onlyNumber]] limpa" />
                                                </p>
                                                <p>
                                                    <label class='first'>Dias Semana:</label>
                                                    <input type="text" name="dias_semana[]" id="dias_semana" size="30" maxlength="4" class="dias_semana validate[required,custom[onlyNumber]] limpa" />
                                                </p>
                                                <p>
                                                    <label class='first'>Folgas:</label>
                                                    <input class="check" type="checkbox" name="folga[][0]" value="1" /> Sábado
                                                    <input class="check" type="checkbox" name="folga[][1]" value="2" /> Domingo
                                                    <input class="check" type="checkbox" name="folga[][2]" value="5" /> Plantonista
                                                </p>
                                            </div>

                                            <p>
                                                <label class='first'>Adicional Noturno: </label>                     
                                                <input type="radio" name="noturno[0]" value="1" id="n_sim" class="n_sim check_not" /> Sim
                                                <input type="radio" name="noturno[0]" value="0" id="n_nao" class="n_nao check_not" /> Não
                                            </p>

                                            <p id="hide_noturno">
                                                <label class='first'>Horas Noturno:</label>
                                                <input type="text" name="horas_noturno[]" id="horas_noturno" class="horas_noturno" size="30" maxlength="4" />
                                            </p>
                                        </fieldset>-->
                                    <?php } ?>                                        
                                    <label class="col-lg-3 col-lg-offset-9 control-label pointer add_hor" id="add_hor">Adicionar outro Horário <img src="../../imagens/icones/icon-add.png" title="Adicionar outro horário" /></label>
                                <?php } ?>
                            </div>
                            <div class="panel-footer text-right">
                                <input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';" class="btn btn-default" />
                                <input type="submit" name="atualizar" id="atualizar" value="Atualizar" class="btn btn-primary" />
                            </div>
                        </div>
                    </div><!--form_funcoes-->
                </form>
            </div>
            <div class="modal fade" id="box_salario" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-md">
                    <form action="" method="post" name="form2" id="form2" autocomplete="off" class="form-horizontal">
                        <div class="modal-content">
                            <div class="modal-header bg-primary" id="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Alteração Salarial</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <h2 class="col-md-12" id="erro2"></h2>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3">Salário Antigo:</label>
                                    <div class="col-md-9">
                                        <?=formataMoeda($row['salario'])?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Salário Novo: R$ </label>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="salario_novo" id="salario_novo">
                                            <span class="input-group-addon pointer"><i class="fa fa-calculator"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3">Diferença:</label>
                                    <div class="col-md-9">
                                        R$: <strong id="diferenca"></strong>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="id_curso" id="id_curso" value="<?php echo $row['id_curso']; ?>" />
                                <input type="hidden" name="salario_antigo" id="salario_antigo" value="<?php echo $row['salario']; ?>" />
                                <input type="hidden" name="salario_new" id="salario_new" value="" />
                                <input type="hidden" name="difere" id="difere" value="" />

                                <input type="button" class="btn btn-primary" name="altera_salario" id="altera_salario" value="Atualizar" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        
        <script src="../../js/jquery.price_format.2.0.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <!--script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script-->
        <script src="../../js/jquery.autocomplete.js" type="text/javascript"></script>
        <script>
            $(function(){
                //mascara
                $("#data_ini").mask("99/99/9999");
                $("#data_fim").mask("99/99/9999");
                $("#salario, #valor, #quota, #salario_novo").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});
                $(".entrada, .ida_almoco, .volta_almoco, .saida").mask("99:99");
                
                //autocomplete
                $("#cbo").autocomplete("lista_cbo.php", {
                    width: 600,
                    matchContains: false,      
                    minChars: 3,
                    selectFirst: false                    
                });
                
                //validation engine
                $("#form1").validationEngine({promptPosition : "topRight"});
                
                //oculta/exibe dados do CLT
                window.func2 = $("#func2").clone();
                $('#contratacao').change(function(){                    
                    if(($(this).val() == "1") || ($(this).val() == "3")){
                        $("#func2").remove();
                    }else if($(this).val() == "2"){
                        if (!$("div.form_funcoes fieldset#func2").length) {
                           var fieldset =  $(document.createElement('fieldset')).append(window.func2.html()).prop('id','func2');
                           $("#func1").after(fieldset);
                        }
                    }
                });
                
                //chickbox
                $(".bt-image").on("click", function() {
                    var action = $(this).data("type");
                    
                    var txtVal = $("#textVal").html();
                    $("#salario_antigo").html("#salario_new");
                    $(".valorForm").html(txtVal);
                    $("#salario_novo").val("");
                    $("#diferenca").html("");
                    $("#erro2").html("");
//                    
//                    if (action === "salario") {
//                        //thickBoxIframe("Alteração Salarial", "altera_salario.php", {curso: key, method: "getDocs"}, "360-not", "240");
//                        thickBoxModal("Alteração Salarial", "#box_salario", "240", "360", null, null).css({display: "block"});
//                    }
                });
                
                //calculo de diferença salarial
                $(".fa-calculator").click(function() {
                    var antigo = $('#salario_antigo').val();
                    var novo = $('#salario_novo').val().replace('.', '');
                        novo = novo.replace(',', '.');
                    var total = (parseFloat(novo) - parseFloat(antigo)).toFixed(2);
                    
                    $("#diferenca").html(total);
                    $("#difere").val(total);
                    $("#salario_new").val(novo);
                    $("#salario").val(novo);
                });
                
                $("#altera_salario").click(function() {
                    var novo = $('#salario_novo').val().replace('.', '');
                        novo = novo.replace(',', '.');
                    var data = $("#form2").serialize();
                    
                    if((novo === 0) || (novo === '')){
                        $("#erro2").html('<strong>Preencha o Salário Novo</strong>').css({color: "#F00"});
                    }else if($("#difere").val() === ''){
                        $("#erro2").html('<strong>Calcule a diferença</strong>').css({color: "#F00"});
                    }else{
                        $.post('edit_curso2.php?method=alteraSalario&' + data, null, function(data){
                            if(data.status == 1){
                                $('#textVal').html(data.valor);
                                //$(".ui-icon-closethick").trigger("click");
                                $('#box_salario').modal('toggle');
                            }
                        },'json');
                    }
                });                               
                
                //clona o fieldset de horario
                $("#add_hor").click(function(){
                    var clone = $('.form_funcoes .horario:last').clone(false);
                    var next_position = parseFloat( clone.attr('data-position')) + 1;
                    clone.attr('data-position', next_position);                                       
                    clone.find("*[id]").andSelf().each(function() { 
                        $(this).attr("id", $(this).attr("id") + next_position); 
                    });
                    
                    clone.find(".check_not").each(function(){
                        $(this).attr({name:"noturno[" + next_position + "]"});
                    });
                    
                    $('.form_funcoes .horario:last').after(clone);
                    var p = $(this).prev().attr("data-position");
                    if(p == next_position){
                        $("fieldset[data-position = " + next_position + "] .check[value=1]").attr({name:"folga[" + next_position + "][0]"});
                        $("fieldset[data-position = " + next_position + "] .check[value=2]").attr({name:"folga[" + next_position + "][1]"});
                        $("fieldset[data-position = " + next_position + "] .check[value=5]").attr({name:"folga[" + next_position + "][2]"});
                    }
                                        
                    clone.find(".limpa").val("");
                    clone.find(".check").prop('checked', false);
                    clone.find(".horas_noturno").val("");
                    
                    $('.form_funcoes .horario:last').addClass("del");
                    $(".del .del_hor").css({display: 'block'});
                    
                    $(".entrada, .ida_almoco, .volta_almoco, .saida")
                        .unmask() //Desabilita a máscara. Se não fizer isso dá problema
                        .mask("99:99"); //Habilita novamente, pegando todos os campos criados 
                    
                    $(".check_not").on('click', function (){
                        var hide_noturno = $(this).parent().parent().parent().next();
                        var noturno = $(this).val();

                        if(noturno == 1){
                            hide_noturno.show();
                        }else{
                            hide_noturno.children().val('');
                            hide_noturno.hide();
                        }
                    });
                    
                    $(".del_hor").on('click', function (){
                        $(this).parents("fieldset").remove();
                    });
                });
                
                $(".del_hor").on('click', function (){
                    $(this).parents("fieldset").remove();
                });
                
                //trata insalubridade/periculosidade
                $("#insal").click(function(){
                    $(".some_insa").show();                    
                    $("#insalubridade").addClass("validate[custom[select]]");
                    $("#qtd_salarios").addClass("validate[required,custom[onlyNumber]]");
                });
                
                $("#peric").click(function(){
                    $(".some_insa").hide();
                    $("#insalubridade").removeClass("validate[custom[select]]");
                    $("#qtd_salarios").removeClass("validate[required,custom[onlyNumber]]");
                });
                
                if($("#insal").is(':checked')){
                    $(".some_insa").show();
                }
                
                $(".check_not").on('click', function (){
                    var hide_noturno = $(this).parent().parent().parent().next();
                    var noturno = $(this).val();
                    
                    if(noturno == 1){
                        hide_noturno.show();
                    }else{
                        hide_noturno.children().val('');
                        hide_noturno.hide();
                    }
                });
                
                $(".n_sim").each(function(){
                    if($(".n_sim").is(':checked')){
                        $(this).parent().parent().parent().next().show();
                    }
                });
                
            });
        </script>
    </body>
</html>