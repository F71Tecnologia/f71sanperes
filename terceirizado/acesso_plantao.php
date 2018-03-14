<?php
#teste
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();

$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];

$data1 = ConverteData($_REQUEST['data1']);
$data2 = ConverteData($_REQUEST['data2']);

$hora1 = (strlen($_REQUEST['hora1']) > 5?$_REQUEST['hora1']:$_REQUEST['hora1']. ":00");
$hora2 = (strlen($_REQUEST['hora2']) > 5?$_REQUEST['hora2']:$_REQUEST['hora2']. ":00");

$regiaoSel = (isset($_REQUEST['reg'])) ? $_REQUEST['reg'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['pro'])) ? $_REQUEST['pro'] : null;

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoSel;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;
$unidadeSel = (isset($_REQUEST['unidade'])) ? $_REQUEST['unidade'] : null;
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');

function data_br($data)
{
    $datas = explode("-", $data);
    return $datas[2]."/".$datas[1]."/".$datas[0];
}

if ($_REQUEST['acao']=='pesquisa_func')
{
    $funcs = '';
    $func = array();
    $dif = array();
    
    foreach($_REQUEST['func_add'] as $lin)
    {
	$funcs .= "{$lin}, ";
    }
    $funcs = substr($funcs, 0, strlen($funcs) -2);
    
    if($_REQUEST['contratacao'] == 'clt')
    {
	$sql = "select 
		    a.colaborador_id as id
		from 
		    acesso_assoc a inner join acesso_plantao p on a.acesso_plantao_id = p.acesso_plantao_id
		where 
		    p.data_inicio = '{$data1}' and p.data_fim = '{$data2}'
		    and a.colaborador_id in ({$funcs})";
		    
    }elseif($_REQUEST['contratacao'] == 'terceiro')
    {
	$sql = "select 
			a.colaborador_id, t.nome
		from 
			acesso_assoc a inner join acesso_plantao p on a.acesso_plantao_id = p.acesso_plantao_id inner join
			terceirizado t on t.id_terceirizado = a.colaborador_id
		where 
			p.data_inicio = '{$data1}' and p.data_fim = '{$data2}'
			and a.colaborador_id in ({$funcs})";

    }else
    {
	$sql = "select 
			a.colaborador_id, u.nome
		from 
			acesso_assoc a inner join acesso_plantao p on a.acesso_plantao_id = p.acesso_plantao_id inner join
			autonomo u on u.id_autonomo = a.colaborador_id
		where 
			p.data_inicio = '{$data1}' and p.data_fim = '{$data2}'
			and a.colaborador_id in ({$funcs})";
    }
    
    echo "sql = [{$sql}]\n\n";
    
    $result = mysql_query($sql);
    
    $con = 0;
    while ($row = mysql_fetch_array($result))
    {
	$func[$con]['id'] = $row['id'];
	$con++;
    }    

    $dif = array_diff_key($_REQUEST['func_add'], $func);
    for($i = 0; $i < sizeof($dif);$i++)
    {
	$dif = array_diff_key($_REQUEST['func_add'], $func);
    }

    echo json_encode($dif);
    exit();
}

//Confirmando inclusão.
if ($_REQUEST['acao']=='conf_incluir') 
{
    
    if(strtolower($_REQUEST['contratacao']) == 'clt')
    {
	$tipo = 1;
    }elseif(strtolower($_REQUEST['contratacao']) == 'terceiro')
    {
	$tipo = 3;
    }else
    {
	$tipo = 2;
    }

    $data_inc1 = $data1 . ' ' . $hora1;
    $data_inc2 = $data2 . ' ' . $hora2;
    
    $sql = "insert into acesso_plantao(data_inicio, data_fim, hora_entrada, hora_saida, regiao_id, projeto_id) values('{$data_inc1}', '{$data_inc2}', '{$hora1}', '{$hora2}', {$_REQUEST['regiao']}, {$_REQUEST['projeto']})";
    mysql_query($sql);
    $id = mysql_insert_id();
    
    foreach($_REQUEST['func_add'] as $lin)
    {
	// Crítica, não permite q o mesmo funcionário pertença a dois horário sobrepostos.
	$sql = "select count(a.colaborador_id) as total
	    from 
		    acesso_assoc a inner join 
		    acesso_plantao p on a.acesso_plantao_id = p.acesso_plantao_id left join
		    terceirizado t on t.id_terceirizado = a.colaborador_id and a.tipo_colaborador = 3 left join
		    autonomo u on u.id_autonomo = a.colaborador_id and a.tipo_colaborador = 2 left join
		    rh_clt r on r.id_clt = a.colaborador_id and a.tipo_colaborador = 1
	    where
		    (('{$data_inc1}' BETWEEN p.data_inicio and p.data_fim) or
		    ('{$data_inc2}' BETWEEN p.data_inicio and p.data_fim)) and
	    a.colaborador_id = {$lin};";
	
	//echo "sql = [$sql]<br>\n<br>\n";
	    
	$result = mysql_query($sql);
	$row = mysql_fetch_row($result);

	//var_dump($row);
	
	if($row[0] == '0')
	{
	    $sql = "insert into acesso_assoc(acesso_plantao_id, colaborador_id, tipo_colaborador) values({$id}, {$lin}, $tipo)";
	    mysql_query($sql);
	}
    }
}

if($_REQUEST['acao'] == 'deletar')
{
    $sql = "delete from acesso_assoc where acesso_plantao_id = {$_REQUEST['id']}";
    mysql_query($sql);
    
    $sql = "delete from acesso_plantao where acesso_plantao_id = {$_REQUEST['id']}";
    mysql_query($sql);
}

if($_REQUEST['acao'] == 'conf_editar')
{
    if(strtolower($_REQUEST['contratacao']) == 'clt')
    {
	$tipo = 1;
    }elseif(strtolower($_REQUEST['contratacao']) == 'terceiro')
    {
	$tipo = 3;
    }else
    {
	$tipo = 2;
    }
    
    $sql = "update acesso_plantao set data_inicio = '{$data1}', data_fim = '{$data2}', hora_entrada = '{$hora1}', hora_saida='{$hora2}' where acesso_plantao_id = {$_REQUEST['id']}";
    //echo "sql = [$sql]<br>\n";
    mysql_query($sql);
    
    $sql = "delete from acesso_assoc where acesso_plantao_id = {$_REQUEST['id']}";
    //echo "sql = [$sql]<br>\n";
    mysql_query($sql);
    
    foreach($_REQUEST['func_add'] as $lin)
    {
	$sql = "select count(a.colaborador_id) as total
	    from 
		    acesso_assoc a inner join 
		    acesso_plantao p on a.acesso_plantao_id = p.acesso_plantao_id left join
		    terceirizado t on t.id_terceirizado = a.colaborador_id and a.tipo_colaborador = 3 left join
		    autonomo u on u.id_autonomo = a.colaborador_id and a.tipo_colaborador = 2 left join
		    rh_clt r on r.id_clt = a.colaborador_id and a.tipo_colaborador = 1
	    where
		    (('{$data1}' BETWEEN p.data_inicio and p.data_fim) or
		    ('{$data2}' BETWEEN p.data_inicio and p.data_fim)) and
	    a.colaborador_id = {$lin};";
	    
	//echo "sql = [$sql]<br>\n<br>\n";
	$result = mysql_query($sql);
	$row = mysql_fetch_row($result);
	
	//var_dump($row);
	
	if($row[0] == '0')
	{
	    if(trim($lin) !== '')
	    {
		$sql = "insert into acesso_assoc(acesso_plantao_id, colaborador_id, tipo_colaborador) values({$_REQUEST['id']}, {$lin}, $tipo)";
		mysql_query($sql);
	    }
	}
    }
}

if($_REQUEST['acao'] == 'editar')
{
    $id = $_REQUEST['id'];
    $con = 0;
    $func = array();
    
    $sql = "
	select 
		p.acesso_plantao_id as id, p.data_inicio, p.data_fim, p.hora_entrada, p.hora_saida, p.projeto_id, p.regiao_id, a.tipo_colaborador, a.colaborador_id, nome
	from 
		acesso_plantao p inner join 
		acesso_assoc a on p.acesso_plantao_id = a.acesso_plantao_id inner join
		rh_clt r on a.colaborador_id = r.id_clt
	where
		a.tipo_colaborador = 1 and p.acesso_plantao_id = {$id}
	union
	select 
		p.acesso_plantao_id as id, p.data_inicio, p.data_fim, p.hora_entrada, p.hora_saida, p.projeto_id, p.regiao_id, a.tipo_colaborador, a.colaborador_id, nome
	from 
		acesso_plantao p inner join 
		acesso_assoc a on p.acesso_plantao_id = a.acesso_plantao_id inner join
		terceirizado t on a.colaborador_id = t.id_terceirizado
	where
		a.tipo_colaborador = 3 and p.acesso_plantao_id = {$id}
	union
	select 
		p.acesso_plantao_id as id, p.data_inicio, p.data_fim, p.hora_entrada, p.hora_saida, p.projeto_id, p.regiao_id, a.tipo_colaborador, a.colaborador_id, nome
	from 
		acesso_plantao p inner join 
		acesso_assoc a on p.acesso_plantao_id = a.acesso_plantao_id inner join
		autonomo u on a.colaborador_id = u.id_autonomo
	where
		a.tipo_colaborador = 2 and p.acesso_plantao_id = {$id}
	union

	select 
		p.acesso_plantao_id as id, p.data_inicio, p.data_fim, p.hora_entrada, p.hora_saida, p.projeto_id, 
		p.regiao_id, '' as tipo_colaborador, '' as colaborador_id, '' as nome 
	from 
		acesso_plantao p 
	where p.acesso_plantao_id = {$id};";
		
    //echo "sql = [$sql]\n";
    
    $result = mysql_query($sql);

    while ($row = mysql_fetch_array($result))
    {
	$func['func_add'][$con]['id'] = $row['id'];
	$func['func_add'][$con]['data_inicio'] = $row['data_inicio'];
	$func['func_add'][$con]['data_fim'] = $row['data_fim'];
	$func['func_add'][$con]['hora_entrada'] = $row['hora_entrada'];
	$func['func_add'][$con]['hora_saida'] = $row['hora_saida'];
	$func['func_add'][$con]['projeto_id'] = $row['projeto_id'];
	$func['func_add'][$con]['regiao_id'] = $row['regiao_id'];
	$func['func_add'][$con]['tipo_colaborador'] = $row['tipo_colaborador'];
	$func['func_add'][$con]['colaborador_id'] = $row['colaborador_id'];
	$func['func_add'][$con]['nome'] = utf8_encode($row['nome']);
	$con++;
    }
    
    $sql = "select UPPER(nome) as nome from projeto where id_projeto = {$func['func_add'][0]['projeto_id']};";
    $result = mysql_query($sql);
    while ($row = mysql_fetch_array($result))
    {
	$func['projeto'] = utf8_encode($row['nome']);
    }
    
    $sql = "select upper(regiao) as nome from regioes where id_regiao = {$func['func_add'][0]['regiao_id']};";
    $result = mysql_query($sql);
    while ($row = mysql_fetch_array($result))
    {
	$func['regiao'] = utf8_encode($row['nome']);
    }
    
    exit(json_encode($func));
}

// monta select com nomes dos funcionarios
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'funcionarios')
{
    if($_REQUEST['contratacao'] == 'clt')
    {
	$select_clt = "SELECT id_clt as id, nome FROM rh_clt WHERE id_projeto = '{$_REQUEST['pro']}' ORDER BY nome";
	
	$result_clt = mysql_query($select_clt);
	$i = 0;
	while ($row = mysql_fetch_array($result_clt)) 
	{
	    $select_funcionarios[$i]['id']=$row['id'];
	    $select_funcionarios[$i]['nome']=utf8_encode($row['nome']);
	    $i++;
	}

	echo json_encode($select_funcionarios);
	exit();
    }elseif($_REQUEST['contratacao'] == 'terceiro')
    {
	$select_ter = "SELECT id_terceirizado as id, nome FROM terceirizado WHERE id_projeto = '{$_REQUEST['pro']}' ORDER BY nome";
	$result_ter = mysql_query($select_ter);
	$i=0;
	while ($row = mysql_fetch_array($result_ter))
	{
	    $select_funcionarios[$i]['id']=$row['id'];
	    $select_funcionarios[$i]['nome']=utf8_encode($row['nome']);
	    $i++;
	}

	echo json_encode($select_funcionarios);
	exit();
    }else {
	$select_aut = "SELECT id_autonomo as id, nome FROM autonomo WHERE id_projeto = '{$_REQUEST['pro']}' ORDER BY nome";
	$result_aut = mysql_query($select_aut);
	$i=0;
	while ($row = mysql_fetch_array($result_aut)) 
	{
	    $select_funcionarios[$i]['id']=$row['id'];
	    $select_funcionarios[$i]['nome']=utf8_encode($row['nome']);
	    $i++;
	 }

	echo json_encode($select_funcionarios);
	exit();
    }
}else
{
// #########################################################################
// #########################################################################
// #########################################################################
// Montando o calendário
// #########################################################################
// #########################################################################
// #########################################################################

    $cores = array('#7FC7AF', '#EDC951', '#00DFFC', '#F5634A', '#BFB35A', '#C6E5D9', 
		    '#F1BBBA', '#00B4FF', '#FA2A00', '#FFF7BD', '#60B99A', '#EEDD99', 
		    '#4DBCE9', '#F77825', '#99B2B7', '#FF9F80', '#EFFAB4', '#F2F2F2', 
		    '#FF6B6B', '#F2E9E1');
    
    
    
    $data1 = "01/".$_REQUEST['data_uso'];
    
    
    
    //$data1 = '01/02/2014';

    if(trim($_REQUEST['data_uso']) != '')
    {
	$datas = explode('/', $data1);
	$ano = $datas[2];
	$mes = $datas[1];
	
	if(isset($_REQUEST['inc']))
	{
	    $mes = $mes + $_REQUEST['inc'];
	    if($mes > 12)
	    {
		$ano++;
		$mes = 1;
	    }
	    if($mes < 1 )
	    {
		$ano=$ano - 1;
		$mes = 12;
	    }
	}
	
	$data_t = mktime(0,0,0,$mes,'01',$ano);
    }else
    {
	$ano = date('Y');
	$mes = date('m');
	$data_t = mktime(0,0,0,date('m'),'01',date('Y'));
    }
    
    $data_uso = $mes."/".$ano;
    
    if($mes == 1)
    {
	$mes_a = 12;
	$ano_a = $ano - 1;
    }else
    {
	$mes_a = $ano;
	$mes_a = $mes - 1;
    }
    
    $data_a = mktime(0,0,0,($mes_a),'01',$ano_a);
    $ultimo_dia_a = date("t", $data_a);

    $data = date('D', $data_t);
    $dia = date('d', $data_t);
    $primeiro_dia_semana = date('w', $data_t);
    $dia_corrente = ($ultimo_dia_a - ($primeiro_dia_semana-1));

    $ultimo_dia = date("t", $data_t)+($primeiro_dia_semana==0?7:$primeiro_dia_semana);

    $con = 0;
    $eventos = array();
    
    $mes_ant = $mes -1;
    if($mes_ant < 1)
    {
	$mes_ant = 12;
	$ano_ant = $ano -1;
    }else
    {
	$ano_ant = $ano;
    }
    
    $sql = "
	select 
		a.acesso_plantao_id as id, 
		DATE_FORMAT(a.data_inicio, '%Y-%m-%d') as inicio,  
		DATE_FORMAT(a.data_fim, '%Y-%m-%d') as fim, 
		hora_entrada,
		hora_saida
	from 
		acesso_plantao a 
	where 
		a.data_inicio BETWEEN '{$ano}-{$mes}-01' and '{$ano}-{$mes}-".date("t", $data_t)."'
		or a.data_fim BETWEEN '{$ano}-{$mes}-01' and '{$ano}-{$mes}-".date("t", $data_t)."'
	order by 
		a.data_inicio, a.data_fim
	";       
    
    //echo "sql = [$sql]<br>\n";		
		
    $result = mysql_query($sql);
    while ($row = mysql_fetch_array($result))
    {
	$eventos[$con]['inicio'] = $row['inicio'];
	$eventos[$con]['fim'] = $row['fim'];
	$eventos[$con]['id'] = $row['id'];
	$eventos[$con]['hora_entrada'] = $row['hora_entrada'];
	$eventos[$con]['hora_saida'] = $row['hora_saida'];
	$con++;
    }
    
    $dia = 1;
    $lin = 0;
    $col = 0;
    $calendario = array();
    
    for($i=0; $i < 42; $i++)
    {

	$calendario[$lin][$col] = "";
	
	if($primeiro_dia_semana <= $i && $ultimo_dia > $i && (($primeiro_dia_semana == '0' && $lin > 0)?TRUE:(($primeiro_dia_semana != '0')?TRUE:FALSE)))
	{
	    $calendario[$lin][$col] = date('Y-m-d',mktime(0,0,0,$mes,$dia,$ano));
	    $dia++;
	}
	$col++;

	if($col===7)
	{
	    $lin++;
	    $col = 0;
	}
    }
}

// #########################################################################
// #########################################################################
// #########################################################################
// Fim Montando o calendário
// #########################################################################
// #########################################################################
// #########################################################################

function mk_data($arg)
{
    $args = explode("-", $arg);
    return mktime(0,0,0,$args[1], $args[2], $args[0]);
}

?>
<html>
    <head>
        <title>:: Intranet :: Controle de Acesso</title>
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
	<link href="../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
	
        <script src="../js/jquery-1.9.0.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="../js/jquery.ui.datepicker-pt-BR.js"></script>
	<script src="../js/jquery.mask.min.js" type="text/javascript"></script>
	<script src="../js/global.js" type="text/javascript"></script>

<style>
    .mv-container {
	background: none repeat scroll 0 0 #fff;
	margin-right: 1px;
    }
    .mv-container {
	height: 100%;
	position: relative;
	white-space: nowrap;
    }    
    .month-row {
	left: 0;
	overflow: hidden;
	position: absolute;
	width: 100%;
    }

    .mv-daynames-table {
	background: none repeat scroll 0 0 #fff;
	color: #555;
	table-layout: fixed;
	width: 100%;
	padding: 0;
	margin: 0;
    }

    .mv-event-container {
	border-right: 1px solid #ddd;
    }
    .mv-event-container {
	background: none repeat scroll 0 0 white;
	bottom: 0;
	left: 0;
	overflow: hidden;
	position: absolute;
	top: 20px;
	width: 100%;
    }
    .corCab{
	background-color: #ECF4FC;
    }
    .fontCab{
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size:14px;
	font-style:normal;
	font-weight:bold;		
    }
    .backDes{
	background-color: #EAEAEA;
    }
    .sombra{
	    -webkit-box-shadow: 0px 1px 5px 0px rgba(50, 50, 50, 0.75);
	    -moz-box-shadow:    0px 1px 5px 0px rgba(50, 50, 50, 0.75);
	    box-shadow:         0px 1px 5px 0px rgba(50, 50, 50, 0.75);
    }
    .cantos{
	    -moz-border-radius:7px;
	    -webkit-border-radius:7px;
	    border-radius:7px;
    }
    
    /* ####################################################### */
    /* Popup centralizado */
    .container {
	width: 100%;
	height: 100%;
	top: 0;
	position: absolute;
	visibility: hidden;
	display: none;
    }

    .reveal-modal {
	position: relative;
	margin: 0 auto;
	top: 200px;
    }    
</style>	
	
        <script>
	    /*
	    jQuery.fn.center = function () {
		this.css("position","absolute");
		this.css("top", Math.max(0, ((($(window).height() - $(this).outerHeight()) / 2) + $(window).scrollTop())/2) + "px");
		this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft()) + "px");
		return this;
	    }
	    */
	    
	    jQuery.fn.center = function () {
		console.log('center');
		this.css("position","fixed");
		this.css("top", Math.max(0, ((($(window).height() - $(this).outerHeight()) / 2))) + "px");
		this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft()) + "px");
		return this;
	    }
	    
	   
	    function enviar()
	    {
		$('#func_add').each(function()
		{
		    $('#func_add option').attr("selected","selected");
		});

		if($('#func_add option:selected').length > 0)
		{
		    $("#form").submit();
		}else
		{
		    alert('Nenhum colaborador foi selecionado!');
		}
	    }
	    function showWaiting(par)
	    {
		if(par == 1)
		{
		    $(".container").css('visibility', 'visible');
		    $(".container").css('display', 'block');
		}else
		{
		    $(".container").css('visibility', 'hidden');
		    $(".container").css('display', 'none');
		}
	    }
	    function exibe_cad_escala(acao, id, data)
	    {
		console.log('exibe_cad_escala');
		
		$('#cad_escala').center();
		$("#data1").val('');
		$("#data2").val('');
		$("#hora1").val('');
		$("#hora2").val('');
		$('#txt_projeto').val('');
		$('#txt_regiao').val('');
		$('#func_add').empty();
		$('#func').empty();

		//var dados = $('#frm_login').serialize();
		if(acao == 'editar')
		{
		    $('#tit_pop').html('Editar');
		    $('#acao').val('conf_editar');
		    $('#id').val(id);
		    $.ajax({
			url: "acesso_plantao.php",
			data: '&acao='+acao+'&id='+id+'&data='+data,
			dataType: "json",
			type: "POST",
		        beforeSend: function() {
			    showWaiting(1);
			}})
		    .done(function (data) {
			
			func_add = data.func_add;
			
			$("#data1").val(dataRevert(func_add[0].data_inicio));
			$("#data2").val(dataRevert(func_add[0].data_fim));
			$("#hora1").val(func_add[0].hora_entrada);
			$("#hora2").val(func_add[0].hora_saida);
			$('#func_add').empty();
			
			$('#projeto').empty();
			$('#projeto').append($("<option></option>").attr("value",func_add[0].projeto_id).text(func_add[0].projeto_id+' - '+data.projeto));
			$('#regiao').val(func_add[0].regiao_id);
			
			$('#projeto').prop('disabled', 'disabled');
			$('#regiao').prop('disabled', 'disabled');
			
			for (var i = 0; i < func_add.length; ++i)
			{
			    lin = func_add[i];
			    $('#func_add').append($("<option></option>").attr("value",lin.colaborador_id).text(lin.nome));
			}
			showWaiting(2);
		    });
		}else
		{
		    $('#tit_pop').html('Incluir');
		    $("#data1").val(dataRevert(data));
		    $('#acao').val('conf_incluir');
		    $('#projeto').prop('disabled', false);
		    $('#regiao').prop('disabled', false);
		    $('#regiao').val('-1');
		    $('#projeto').empty();
		    $('#projeto').append($("<option></option>").attr("value",-1).text('« Selecione »'));
		}

		$("#cad_escala").css('display', 'block');
	    }

	    function dataRevert(data)
	    {
		datas = data.split("-",3);
		ano = datas[0];
		mes = datas[1];
		dias = datas[2].split(" ");
		dia = dias[0];
		hora = dias[1];
		
		return dia+'/'+mes+'/'+ano;
	    }
	    
	    function oculta_cad_escala()
	    {
		$("#cad_escala").css('display', 'none');
	    }
	    
	    
	    
	    /*
	    Verificar necessidade da validação abaixo!
    
	    function addFunc()
	    {
		$('#func option:selected').remove().appendTo('#func_add');
		$('#acao').val('pesquisa_func');
		
		$('#func_add').each(function()
		{
		    $('#func_add option').attr("selected","selected");
		});
		var dados = $('#form').serialize();

		$.ajax({
		    url: "acesso_plantao.php",
		    data: dados,
		    dataType: "json",
		    type: "POST",
		    success: function(data)
		    {
			$('#func_add option:selected').remove();
			var unid = "";
			$.each(data, function(k, v){
                        //for (var i in data.func) {
                            selected = "";
                            if (v.id == "<?= $unidadeSel ?>") {
                                selected = "selected=\"selected\" ";
                            }
			    unid += "<option value='" + v.id + "' " + selected + ">" + v.nome + "</option>\n";
                        });
                        $("#func_add").html(unid);
		    }
		});
	    }
	    */
	    function addFunc()
	    {
		$('#func option:selected').remove().appendTo('#func_add');
	    }
	    function rmFunc()
	    {
		$('#func_add option:selected').remove().appendTo('#func');
	    }

            $(function() {
                var id_destination = "projeto";
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function(data) {
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");
		
                $('#projeto').change(function() {
		    $('#func').empty();
                    var pro = $(this).val();
                    $.post("<?= $_SERVER['PHP_SELF'] ?>", {pro: pro, method: 'funcionarios', contratacao: $('#contratacao').val()}, function(data) {
                        var selected = "";
			
                        //var unid = "<option value='-1'>« Selecione »</option>\n";
			var unid = "";
			$.each(data, function(k, v){
                        //for (var i in data.func) {
                            selected = "";
                            if (v.id == "<?= $unidadeSel ?>") {
                                selected = "selected=\"selected\" ";
                            }
                            //unid += "<option value='" + i + "' " + selected + ">" + data.func[i] + "</option>\n";
			    unid += "<option value='" + v.id + "' " + selected + ">" + v.nome + "</option>\n";
                        });
                        $("#func").html(unid);
                    }, 'json');
                });
		
		$('#contratacao').change(function() {
		    $('#func').empty();
                    var pro = $('#projeto').val();
                    $.post("<?= $_SERVER['PHP_SELF'] ?>", {pro: pro, method: 'funcionarios', contratacao: $('#contratacao').val()}, function(data) {
                        var selected = "";
			
                        //var unid = "<option value='-1'>« Selecione »</option>\n";
			var unid = "";
			$.each(data, function(k, v){
                        //for (var i in data.func) {
                            selected = "";
                            if (v.id == "<?= $unidadeSel ?>") {
                                selected = "selected=\"selected\" ";
                            }
                            unid += "<option value='" + v.id + "' " + selected + ">" + v.nome + "</option>\n";
                        });
                        $("#func").html(unid);
                    }, 'json');
		});
            });
	    function formata_data(obj,prox) {
		switch (obj.value.length) {
			case 2:
				obj.value = obj.value + "/";
				break;
			case 5:
				obj.value = obj.value + "/";
				break;
			case 9:
				prox.focus();
				break;
		}
	    }
        </script>
    </head>
    <body class="novaintra" style="width: 99%">
    <div class="container" style="z-index: 10000">
	<center>
	    <div id="exampleModal" class="reveal-modal" style="z-index: 10001">
		<img src="./img/loading.gif" id="l_cerveja" />
	    </div>
	</center>
    </div>
    <!-- ################################################################################### -->
    <!-- Popup cadastro escala style="z-index: 1000; padding:0px;display: none;position:fixed; width:850px; height:540px; top:68px; right:20px;"> -->
    <div id="cad_escala" class="sombra cantos" style="z-index: 100; padding:0px;display: none;position:fixed; width:800px; height:545px; top:68px; right:20px; background-color:#FFF">
	<div style="text-align: right;padding:10px 10px 0 10px;">
	    <img src="./img/close_button.gif" onclick="oculta_cad_escala();"/>
	</div>
        <div id="page" style="margin: 5px; height: 465px">
	    <form  name="form" action="" method="post" id="form">
		<input type="hidden" name="id" id="id" />
		<input type="hidden" name="acao" id="acao" />
		<fieldset class="noprint">
		    <legend id="tit_pop"></legend>
		    <div class="fleft">
			<input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
			<input type="hidden" name="hide_funcao" id="hide_funcao" value="<?php echo $funcaoSel ?>" />
			<p><label class="first" style='width: 200px'>Forma Contratação:</label>
			    <select name='contratacao' id='contratacao'>
				<option value='clt' <?=($_REQUEST['contratacao'])=='clt'?'selected="selected"':''?>>CLT</option>
				<option value='terceiro' <?=($_REQUEST['contratacao'])=='terceiro'?'selected="selected"':''?>>Terceirizado</option>
				<option value='cooperado' <?=($_REQUEST['contratacao'])=='cooperado'?'selected="selected"':''?>>Cooperado</option>
			    </select>
			</p>
			<p>
			    <label class="first" style='width: 200px'>Região:</label>
			    <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?>
			</p>
			<p>
			    <label class="first" style='width: 200px'>Projeto:</label>
			    <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?>
			</p>
			<p><label class="first" style='width: 200px'>Período:</label> 
			    <input name="data1" type="text" id="data1" size="11" maxlength="11" value="<?=(!is_null($_REQUEST['data1']))?($_REQUEST['data1']):''?>"> até 
			    <input name="data2" type="text" id="data2" size="11" maxlength="11" value="<?=(!is_null($_REQUEST['data2']))?($_REQUEST['data2']):''?>">
			</p>
			<p><label class="first" style='width: 200px'>Horário:</label> 
			    <input name="hora1" class="hora" type="text" id="hora1" size="7" maxlength="5" value="<?=(!is_null($_REQUEST['hora1']))?($_REQUEST['hora1']):''?>"> até 
			    <input name="hora2" class="hora" type="text" id="hora2" size="7" maxlength="5" value="<?=(!is_null($_REQUEST['hora2']))?($_REQUEST['hora2']):''?>">
			</p>
			<div style="text-align: center; width:790px">
			    <center>
			    Funcionários<br>
			    <table border="0" width="90%">
				<tr>
				    <td style="text-align: center">Existentes</td>
				    <td style="text-align: center"></td>
				    <td style="text-align: center">Adicionados</td>
				</tr>
				<tr>
				    <td style="text-align: center">
					<?php echo montaSelect($select_funcionarios, null, array('name' => "func", 'id' => 'func', "multiple" => "multiple", "size"=>"10", "style"=>"width: 340px"));?>
				    </td>
		    		    <td style="text-align: center">
					<input type="button" value="->" style="width: 50px" onclick="addFunc();" /><br>
					<input type="button" value="<-" style="width: 50px" onclick="rmFunc();" />
				    </td>
				    <td style="text-align: center">
					<?php echo montaSelect($select_funcionarios, null, array('name' => "func_add[]", 'id' => 'func_add', "multiple" => "multiple", "size"=>"10", "style"=>"width: 340px"));?>
				    </td>
				</tr>
			    </table>
			    </center>
			</div>
		    </div>

		    <br class="clear"/>

		    <p style="margin-top: 10px; text-align: right; padding: 10px">
			<input type="button" name="gerar" value="Confirmar" id="gerar" onclick="enviar();"/>
		    </p>
		</fieldset>
	    </form>	    
	</div>        
    </div>	
    <!-- Fim popup cadastro escala -->	
    <!-- ################################################################################### -->
    
        <div id="content" style="padding: 0 15px 0 15px">
            
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Cadastro de Escala</h2>
                    </div>
                </div>
        </div>
    
    <div style="background-color:#fff; text-align: center">
	<a href="acesso_plantao.php?data_uso=<?=$data_uso?>&inc=-1">Mês Anterior</a> <?=$data_uso?> <a href="acesso_plantao.php?data_uso=<?=$data_uso?>&inc=1">Próximo Mês</a>
    </div>
	
<div class="mv-container" style="padding: 5px 5px 5px 5px;">
    <table style="height: 100%; border: #9E9FA0 solid 1px; margin-bottom: 5px" border="0" cellspacing="0" cellpadding="0">
	<tr>
	    <td style="height: 30px; text-align: center">
		<table class='mv-daynames-table' style="width: 100%; height: 30px" border="0" cellspacing="0" cellpadding="0">
		    <tr class="corCab fontCab">
			<td style="border: #9E9FA0 solid 1px; text-align: center">Domingo
			</td>
			<td style="border: #9E9FA0 solid 1px; text-align: center">Segunda
			</td>
			<td style="border: #9E9FA0 solid 1px; text-align: center">Terça
			</td>
			<td style="border: #9E9FA0 solid 1px; text-align: center">Quarta
			</td>
			<td style="border: #9E9FA0 solid 1px; text-align: center">Quinta
			</td>
			<td style="border: #9E9FA0 solid 1px; text-align: center">Sexta
			</td>
			<td style="border: #9E9FA0 solid 1px; text-align: center">Sábado
			</td>
		    </tr>
		</table>
	    </td>
	</tr>
	
	<?

	foreach($calendario as $lin):?>
	    <tr>
		<td>
		    <table class='mv-daynames-table' style="border-bottom: #9E9FA0 solid 1px; width: 100%; height: 100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<?
			for($i =0;$i<7;$i++):

			    if($dia_corrente==$ultimo_dia_a+1)
			    {
				$dia_corrente = 1;
				$ultimo_dia_a = date("t", $data_t);
			    }
			    ?>
			    <td onclick="exibe_cad_escala('incluir', '0', '<?=$ano.'-'.($mes < 10?'0'.$mes:$mes).'-'.($dia_corrente < 10?'0'.$dia_corrente:$dia_corrente)?>')" style="cursor:pointer; border-left: #9E9FA0 solid 1px;">
				<?=$dia_corrente?>
			    </td>
			    <? $dia_corrente++;?>
			<?endfor?>
			</tr>
			<?
			$cor = 0;
			foreach($eventos as $evento):
			    ?>
			    <tr>
				<?
				$dia_cont = 1;
				foreach($lin as $reg):
				    $fundo = '';
				    $title = "";
				    $conteudo = '&nbsp;';
				    $acao = "incluir";
				    if($evento['inicio']==$reg):
					//$conteudo = "Comp cor={$cor}";
					$acao = "editar";
					$conteudo = data_br($evento['inicio']) . " - " . data_br($evento['fim']);
					if($evento['fim']!=$reg && $dia_cont == 7)
					{
					    $conteudo.= " ...";
					    $fundo .= "font-weight:bold; ";
					}
					$fundo .= "background-color: ".$cores[$cor]."; ";
					$title = "title = '".data_br($evento['inicio']) . " - " . data_br($evento['fim'])."  ".$evento['hora_entrada']." - ".$evento['hora_saida']."'";
				    endif;
				    if($evento['fim']==$reg):
					//$conteudo = "Fim Comp cor={$cor}";
					$acao = "editar";
					$fundo .= "background-color: ".$cores[$cor]."; ";
					$title = "title = '".data_br($evento['inicio']) . " - " . data_br($evento['fim'])."  ".$evento['hora_entrada']." - ".$evento['hora_saida']."'";
				    endif;
				    if((mk_data($reg) > mk_data($evento['inicio'])) && (mk_data($reg) < mk_data($evento['fim']))):
					//$conteudo = "Entre cor={$cor}";
					$acao = "editar";
					$fundo = "background-color: ".$cores[$cor]."; ";
					$title = "title = '".data_br($evento['inicio']) . " - " . data_br($evento['fim'])."  ".$evento['hora_entrada']." - ".$evento['hora_saida']."'";
					if($dia_cont == 7)
					{
					    $conteudo.= " ...";
					    $fundo .= "font-weight:bold; ";
					}
					if($dia_cont == 1)
					{
					    $conteudo = data_br($evento['inicio']) . " - " . data_br($evento['fim']);
					}
				    endif;
				    ?>
				    <td onclick="exibe_cad_escala('<?=$acao?>', '<?=$evento['id']?>', '<?=$reg?>')" <?=$title?> style="cursor:pointer; text-align: right; border-left: #9E9FA0 solid 1px;<?=$fundo?>">
					<?=$conteudo?>
				    </td>
				<?
				    $dia_cont++;
				endforeach;?>
			    </tr>
			    <?
			    $cor++;
			endforeach?>
		    </table>		
		</td>
	    </tr>
	<?endforeach;?>
    </table>

</div>	
	
<script>
$(document).ready(function(){
    $('#cad_escala').center();
});
$(window).resize(function() {
    $('#cad_escala').center();
});

$(function() {
    $('#data1').datepicker({
	    //minDate: 'today',
	    maxDate: "+90D",
	    changeMonth: true,
	    changeYear: true
    });
    $('#data2').datepicker({
	    //minDate: 'today',
	    maxDate: "+90D",	
	    changeMonth: true,
	    changeYear: true
    });
    $('.hora').mask('00:00', {reverse: true});
});


</script>
    </body>
</html>