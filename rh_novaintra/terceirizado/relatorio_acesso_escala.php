<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include "../../wfunction.php";
include "../../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();

$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];

$data1 = ConverteData($_REQUEST['data1']);
$data2 = ConverteData($_REQUEST['data2']);

function sum_time($time, $time_add)
{
    list($h, $m, $s) = explode(":", $time);
    list($ha, $ma, $sa) = explode(":", $time_add);
    
    $s = $s + $sa;
    if($s > 59)
    {
	$s = $s - 60;
	$m = $m + 1;
    }
    
    $m = $m + $ma;
    if($m > 59)
    {
	$m = $m - 60;
	$h = $h + 1;
    }
    
    $h = $h + $ha;
    
    return($h.":".$m.":".$s);
}

if($_REQUEST['acao']==='change_escala')
{
    $ids = explode('-', $_REQUEST['id_colaborador']);
    $id_colaborador = $ids[0];
    $id_assoc_ant = $ids[1];
    $tipo_colaborador = $ids[2];
    
    $id_horario = $_REQUEST['horario'];
    
    $sql = "delete from acesso_assoc where acesso_assoc_id = {$id_assoc_ant}";
    //echo "sql1 = [{$sql}]\n";
    mysql_query($sql);
    
    $sql = "insert into acesso_assoc(acesso_plantao_id, colaborador_id, tipo_colaborador) values({$id_horario}, {$id_colaborador}, {$tipo_colaborador})";
    //echo "sql2 = [{$sql}]\n";
    mysql_query($sql);
    
    exit();
}

if (isset($_REQUEST['gerar'])) 
{
    $sql = "
	    select 
	       p.acesso_plantao_id as id, 
	       DATE_FORMAT(p.data_inicio,'%d/%m/%Y') as inicio,
	       DATE_FORMAT(p.data_fim,'%d/%m/%Y') as fim, 
	       p.hora_entrada, p.hora_saida
	    from 
	       acesso_plantao p inner join acesso_assoc a on p.acesso_plantao_id = a.acesso_plantao_id
	    where
		    p.regiao_id = {$id_regiao} and
		    ((p.data_inicio BETWEEN '{$data1}' and '{$data2}') or
		    (p.data_fim BETWEEN '{$data1}' and '{$data2}'))
	    group by id, inicio, fim, p.hora_entrada, p.hora_saida
	    order by p.acesso_plantao_id desc;";
    
    //echo "sql = [{$sql}]<br>\n<br>\n";
    $tmp = mysql_query($sql);
}

function get_escala($id_escala)
{
    $data1 = ConverteData($_REQUEST['data1']);
    $data2 = ConverteData($_REQUEST['data2']);
    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];       
    
    if(isset($id_regiao) && isset($id_projeto))
    {
	$where = "where l.acesso_plantao_id = {$id_escala} and p.id_regiao = '{$id_regiao}' and p.id_projeto = '{$id_projeto}' ";
    }else
    {
	echo "exit<br>\n";
	exit();
    }
    
    if(isset($data1) && isset($data2))
    {
	$where .= " and p.data between '$data1' and '$data2' and p.data BETWEEN DATE_FORMAT(l.data_inicio,'%Y-%m-%d') and DATE_FORMAT(l.data_fim,'%Y-%m-%d') ";
    }

    if($_REQUEST['contratacao'] == 'clt')
    {
	if($_REQUEST['func'] != '-1')
	{
	    $where .="and t.id_clt = {$_REQUEST['func']} ";
	}
	$sql = "select colaborador_id, grupo, pis, nome, curso, acesso_assoc_id, tipo_colaborador, acesso_plantao_id,
			DATE_FORMAT(min(data_completa), '%Y-%m-%d') as data_entrada,
			DATE_FORMAT(max(data_completa), '%Y-%m-%d') as data_saida,
			DATE_FORMAT(min(data_completa), '%H:%i:%s') as entrada,
			IF(MAX(data_completa) = MIN(data_completa), '---', DATE_FORMAT(max(data_completa), '%H:%i:%s')) AS saida,
			timediff(max(data_completa), min(data_completa)) as total, 
			min(imagem) as img1, max(imagem) as img2,
			(if(valor = 0, valor_hora, valor/hora_mes)) as valor_hora, 
			(if(valor = 0, valor_hora, valor/hora_mes) * (timediff(max(data_completa), min(data_completa))))/10000 as valor 	
		from
		(
			select 
				@num := @num + 1 as num, 
				if(horario = 'entrada' and (@var='' or @var='saida'), @row := @row + 1, if(@var = 'saida', @row := @row + 1, @row := @row)) as grupo,
				@var := horario as var,
				ta.* 
			from
			(
				select 
					a.*,
					p.pis, t.nome, c.nome as curso, p.data_completa, 
					c.valor_hora, c.hora_mes, c.valor,
					p.imagem,
					if((addtime(l.hora_entrada, '-04:00:00') < p.hora) and (addtime(l.hora_entrada, '04:00:00') > p.hora), 'entrada', 
					if((addtime(l.hora_saida, '-04:00:00') < p.hora) and (addtime(l.hora_saida, '04:00:00') > p.hora), 'saida', 'erro')) as horario
				from 
					rh_clt t inner join 
					terceiro_ponto p on t.pis = p.pis and t.pis is not null and t.pis <> '' inner join 
					curso c on c.id_curso = t.id_curso inner join
					acesso_assoc a on a.colaborador_id = t.id_clt inner join
					acesso_plantao l on l.acesso_plantao_id = a.acesso_plantao_id
				{$where}
			) ta, (SELECT @row := 0) r, (SELECT @num := 0) n, (select @var := '') v
		) su
		group by grupo, pis, nome, curso
		order by data_completa desc, num desc";
    }elseif($_REQUEST['contratacao'] == 'terceiro')
    {
	if($_REQUEST['func'] != '-1')
	{
	    $where .="and t.id_terceirizado = {$_REQUEST['func']} ";
	}
	$sql = "select colaborador_id, grupo, id_terceirizado, nome, curso, acesso_assoc_id, tipo_colaborador, acesso_plantao_id,
			DATE_FORMAT(min(data_completa), '%Y-%m-%d') as data_entrada,
			DATE_FORMAT(max(data_completa), '%Y-%m-%d') as data_saida,
			DATE_FORMAT(min(data_completa), '%H:%i:%s') as entrada,
			IF(MAX(data_completa) = MIN(data_completa), '---', DATE_FORMAT(max(data_completa), '%H:%i:%s')) AS saida,
			timediff(max(data_completa), min(data_completa)) as total, 
			min(imagem) as img1, max(imagem) as img2,
			(if(valor = 0, valor_hora, valor/hora_mes)) as valor_hora, 
			(if(valor = 0, valor_hora, valor/hora_mes) * (timediff(max(data_completa), min(data_completa))))/10000 as valor 	
		from
		(
			select 
				@num := @num + 1 as num, 
				if(horario = 'entrada' and (@var='' or @var='saida'), @row := @row + 1, if(@var = 'saida', @row := @row + 1, @row := @row)) as grupo,
				@var := horario as var,
				ta.* 
			from
			(
				select
					a.*,
					p.id_terceirizado, t.nome, c.nome as curso, p.data_completa, 
					c.valor_hora, c.hora_mes, c.valor,
					p.imagem,
					if((addtime(l.hora_entrada, '-04:00:00') < p.hora) and (addtime(l.hora_entrada, '04:00:00') > p.hora), 'entrada', 
					if((addtime(l.hora_saida, '-04:00:00') < p.hora) and (addtime(l.hora_saida, '04:00:00') > p.hora), 'saida', 'erro')) as horario
				from 
					terceirizado t inner join 
					terceiro_ponto p on t.id_terceirizado = p.id_terceirizado inner join
					curso c on c.id_curso = t.id_curso inner join
					acesso_assoc a on a.colaborador_id = t.id_terceirizado inner join
					acesso_plantao l on l.acesso_plantao_id = a.acesso_plantao_id
				{$where}
				order by p.id_terceirizado, p.id_terceiro_ponto
			) ta, (SELECT @row := 0) r, (SELECT @num := 0) n, (select @var := '') v
		) su
		group by grupo, id_terceirizado, nome, curso
		order by data_completa desc, num desc";
		
    }else
    {
	if($_REQUEST['func'] != '-1')
	{
	    $where .="and t.id_autonomo = {$_REQUEST['func']} ";
	}
	$sql = "select colaborador_id, grupo, id_autonomo, nome, curso, acesso_assoc_id, tipo_colaborador, acesso_plantao_id,
			DATE_FORMAT(min(data_completa), '%Y-%m-%d') as data_entrada,
			DATE_FORMAT(max(data_completa), '%Y-%m-%d') as data_saida,
			DATE_FORMAT(min(data_completa), '%H:%i:%s') as entrada,
			IF(MAX(data_completa) = MIN(data_completa), '---', DATE_FORMAT(max(data_completa), '%H:%i:%s')) AS saida,
			timediff(max(data_completa), min(data_completa)) as total, 
			min(imagem) as img1, max(imagem) as img2,
			(if(valor = 0, valor_hora, valor/hora_mes)) as valor_hora, 
			(if(valor = 0, valor_hora, valor/hora_mes) * (timediff(max(data_completa), min(data_completa))))/10000 as valor 	
		from
		(
			select 
				@num := @num + 1 as num, 
				if(horario = 'entrada' and (@var='' or @var='saida'), @row := @row + 1, if(@var = 'saida', @row := @row + 1, @row := @row)) as grupo,
				@var := horario as var,
				ta.* 
			from
			(
				select 
					a.*,
					p.id_autonomo, t.nome, c.nome as curso, p.data_completa, 
					c.valor_hora, c.hora_mes, c.valor,
					p.imagem,
					if((addtime(l.hora_entrada, '-04:00:00') < p.hora) and (addtime(l.hora_entrada, '04:00:00') > p.hora), 'entrada', 
					if((addtime(l.hora_saida, '-04:00:00') < p.hora) and (addtime(l.hora_saida, '04:00:00') > p.hora), 'saida', 'erro')) as horario
				from 
					autonomo t inner join 
					terceiro_ponto p on t.id_autonomo = p.id_autonomo inner join
					curso c on c.id_curso = t.id_curso inner join
					acesso_assoc a on a.colaborador_id = t.id_autonomo inner join
					acesso_plantao l on l.acesso_plantao_id = a.acesso_plantao_id
				{$where}
				order by p.id_autonomo, p.id_terceiro_ponto
			) ta, (SELECT @row := 0) r, (SELECT @num := 0) n, (select @var := '') v
		) su
		group by grupo, id_autonomo, nome, curso
		order by data_completa desc, num desc";
    }
    $result = mysql_query($sql);
    
    return $result;
    //echo "sql = [$sql]<br>\n";
    //exit();
}

// ######################################################################################################################################################
// Crachá temporário.
$sql = "select c.id_cracha_temporario as id, concat('9000', c.id_cracha_temporario) as numero from cracha_temporario c order by c.id_cracha_temporario;";
$result_cracha = mysql_query($sql);
$select_cracha[-1]='---';
while ($row = mysql_fetch_array($result_cracha)) 
{
    $select_cracha[$row['id']]=$row['numero'];
}
// Fim crachá temporário.

$regiaoSel = (isset($_REQUEST['reg'])) ? $_REQUEST['reg'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['pro'])) ? $_REQUEST['pro'] : null;

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoSel;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;
$unidadeSel = (isset($_REQUEST['unidade'])) ? $_REQUEST['unidade'] : null;
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');

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
		while ($row = mysql_fetch_array($result_aut)) {
		    $select_funcionarios[$i]['id']=$row['id'];
		    $select_funcionarios[$i]['nome']=utf8_encode($row['nome']);
		    $i++;
		 }

		echo json_encode($select_funcionarios);
		exit();
    }
}

/*
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'funcionarios') {
    
    $select_clt = "SELECT id_clt,nome FROM rh_clt WHERE id_projeto = '{$_REQUEST['pro']}' ORDER BY nome";
    //echo "select_clt = [$select_clt]<br>\n";
    $select_ter = "SELECT id_terceirizado,nome FROM terceirizado WHERE id_projeto = '{$_REQUEST['pro']}' ORDER BY nome";
    //echo "select_ter = [$select_ter]<br>\n";
    $select_aut = "SELECT id_autonomo,nome FROM autonomo WHERE id_projeto = '{$_REQUEST['pro']}' ORDER BY nome";
    //echo "select_aut = [$select_aut]<br>\n";
    
    $result_clt = mysql_query($select_clt);
    $result_ter = mysql_query($select_ter);
    $result_aut = mysql_query($select_aut);

    $select_funcionarios = array();
    while ($row = mysql_fetch_array($result_clt)) {
	    $ind = $row['id_clt'];
	    $select_funcionarios[$ind] = utf8_encode($row['nome']);
	}
    while ($row = mysql_fetch_array($result_ter)) {
	    $ind = $row['id_terceirizado'];
	    $select_funcionarios[$ind] = utf8_encode($row['nome']);
	}	
    while ($row = mysql_fetch_array($result_aut)) {
	    $ind = $row['id_autonomo'];
	    $select_funcionarios[$ind] = utf8_encode($row['nome']);
	}
    
    echo json_encode(array('func' => $select_funcionarios));
    exit();
}
*/
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Relatório Controle de Acesso por Escala");
$breadcrumb_pages = array("Escalas" => "escalas.php");

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório Controle de Acesso por Escala</title>
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
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relatório Controle de Acesso por Escala</small></h2></div>
                </div>
            </div>
            <!-- ################################################################################### -->
            <!-- Popup cadastro escala -->
            <div id="cad_escala" class="sombra cantos" style="z-index: 100; padding:0px;display: none;position:fixed; width:550px; height:110px; top:68px; right:20px; background-color:#FFF">
                <div style="text-align: right;padding:10px 10px 0 10px;">
                    <img src="./img/close_button.gif" onclick="oculta_cad_escala();"/>
                </div>
                <div style="text-align: center;padding:0px 10px 0 10px;">
                    <a id='nome_funcionario'></a>
                </div>
                <div id="page" style="margin: 5px; height: 55px">
                    <form  name="popup" action="" method="post" id="popup">
                        <input type="hidden" name='id_colaborador' id='id_colaborador' />
                        <input type='hidden' name='acao' id='acao' value='change_escala' />
                        <div class="fleft">
                            <p>
                                <label class="first" style='margin-left: 40px; width: 200px'>Mudar horário:</label> 

                                <input type="button" name="gerar" value="Confirmar" id="gerar" onclick="enviar();"/>
                            </p>
                        </div>
                    </form>	 
                </div>        
            </div>	
            <!-- Fim popup cadastro escala -->	
            <!-- ################################################################################### -->
            <form name="form" action="" method="post" id="form" class="form-horizontal">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        <input type="hidden" name="hide_funcao" id="hide_funcao" value="<?php echo $funcaoSel ?>" />
                        <div class="form-group">
                            <label class="col-md-1 control-label">Contratação:</label>
                            <div class="col-md-11">
                                <select name='contratacao' id='contratacao' class="form-control">
                                    <option value='clt' <?=($_REQUEST['contratacao'])=='clt'?'selected="selected"':''?>>CLT</option>
                                    <option value='terceiro' <?=($_REQUEST['contratacao'])=='terceiro'?'selected="selected"':''?>>Terceirizado</option>
                                    <option value='cooperado' <?=($_REQUEST['contratacao'])=='cooperado'?'selected="selected"':''?>>Cooperado</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-1 control-label">Região:</label>
                            <div class="col-md-5">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?>
                            </div>
                            <label class="col-md-1 control-label">Projeto:</label>
                            <div class="col-md-5">
                                <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-1 control-label">Período:</label>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input class="form-control" name="data1" type="text" id="data1" maxlength="11" value="<?=(!is_null($_REQUEST['data1']))?($_REQUEST['data1']):''?>">
                                    <div class="input-group-addon text-bold">até</div>
                                    <input class="form-control" name="data2" type="text" id="data2" maxlength="11" value="<?=(!is_null($_REQUEST['data2']))?($_REQUEST['data2']):''?>">
                                </div>
                            </div>
                            <label class="col-md-1 control-label">Funcionário:</label>
                            <div class="col-md-5">
                                <?php echo montaSelect($select_funcionarios, null, array('name' => "func", 'id' => 'func', 'class' => 'form-control')); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-1">Crachá Temporário:</label>
                            <div class="col-md-5">
                                <?php echo montaSelect($select_cracha, null, array('name' => "cracha", 'id' => 'cracha', 'onclick' => 'sel_cracha();', 'class' => 'form-control')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <input type="submit" class="btn btn-primary" name="gerar" value="Gerar" id="gerar"/>
                    </div>
                </div>
                

                
                
<?php if (isset($_REQUEST['gerar'])){ ?>
            <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="btn btn-success"></p>
            <table id='tbRelatorio' class="table table-condensed table-hover">
                <thead>
                    <tr class="bg-primary">
                        <th class="valign-middle">NOME</th>
                        <th class="valign-middle">FUNÇÃO</th>
                        <th class="center valign-middle">FOTO REGISTRO INICIAL</th>
                        <th class="center valign-middle">DATA INICIAL</th>
                        <th class="center valign-middle">REGISTRO INICIAL</th>
                        <th class="center valign-middle">FOTO REGISTRO FINAL</th>
                        <th class="center valign-middle">DATA FINAL</th>
                        <th class="center valign-middle">REGISTRO FINAL</th>
                        <th class="center valign-middle">TOTAL DE REGISTROS. (HORAS)</th>
                        <!-- <th class="center valign-middle">VALOR HORA</th> -->
                        <!-- <th class="center valign-middle">VALOR ESTIMADO</th> -->
                    </tr>
                </thead>
                <?php 
                $tot = 0;
                $tot_horas = "00:00:00";
                $flag_e = "";
                $flag_d = "";
                while ($row_e = mysql_fetch_array($tmp)){
                    //echo "id = {$row['id']}<br>\n";
                    $result = get_escala($row_e['id']);
                    $flag_e = $row_e['inicio']." ".$row_e['hora_entrada']." - ".$row_e['fim']." ".$row_e['hora_saida'];
                    //var_dump($result);
	       
                    if((!is_null($result))&&($_REQUEST['cracha'] == '-1')){

                        while ($row = mysql_fetch_array($result)){


                            $p_inicio = date_create($row['data_entrada'].' '.$row['entrada']);
                            $p_final = date_create($row['data_saida'].' '.$row['saida']);
                            $intervalo = date_diff($p_inicio, $p_final);
                            $t_horas = explode(':', $row['total']);
                            $t_hora = $t_horas[0];

                            $trava = ($t_hora > 26)?false:true;
                            if($flag_e != $flag_d): ?>
                            <tr class="text-bold success">
                                    <td colspan="9">Escala <?=$flag_e?></td>
                                </tr>
                            <?php endif;
                            $flag_d = $flag_e; ?>
                            <tr class="linha_<?php echo ($alternateColor++%2==0)?"um":"dois"; ?>" style="font-size:12px;">
                                <td><a style="cursor: pointer;" id='<?=$row['colaborador_id'].'-'.$row['acesso_assoc_id'].'-'.$row['tipo_colaborador']?>' ><?=$row['nome']?></a></td>
                                <td><?=$row['curso']?></td>
                                <td style="text-align: center;"><a href="../../fotos/<?=$row['img1']?>" target="_blank"><img src="../../fotos/<?=$row['img1']?>" width="40" height="30"/></a></td>
                                <td style="text-align: center;"><?=date("d/m/Y", strtotime($row['data_entrada']))?></td>
                                <td style="text-align: center;"><?=$row['entrada']?></td>
                                <td style="text-align: center;">
                                    <?php if($row['img2']==$row['img1']): ?>
                                        ---
                                    <?php else: ?>
                                        <a href="../../fotos/<?=$row['img2']?>" target="_blank"><img src="../../fotos/<?=$row['img2']?>" width="40" height="30"/></a>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;"><?=date("d/m/Y", strtotime($row['data_saida']))?></td>
                                <td style="text-align: center;"><?=$row['saida']?></td>

                                <?php if($trava):?>
                                    <td style="text-align: center;"><?=$row['total']?></td>
                                    <!-- <td style="text-align: center;"><?="R$ ".number_format($row['valor_hora'], 2, ',', '.')?></td> -->
                                    <!-- <td style="text-align: center;"><?="R$ ".number_format($row['valor'], 2, ',', '.')?></td> -->
                                <?php else:?>
                                    <td style="text-align: center;color: #C00">Erro no registro</td>
                                <?php endif;?>
                            </tr>

                            <?php
                            if($trava) {
                                $tot_horas = sum_time($tot_horas, $row['total']);
                                $tot = $tot + $row['valor'];
                            }
                        }
                    }
                } ?>

		<tr class="linha_<?php echo ($alternateColor++%2==0)?"um":"dois"; ?>" style="font-size:12px;">
		    <td></td>
		    <td></td>
		    <td style="text-align: center;"></td>
		    <td style="text-align: center;"></td>
		    <td style="text-align: center;"></td>
		    <td style="text-align: center;"></td>
		    <td style="text-align: center;"></td>
		    <td style="text-align: center;"></td>
		    <td style="text-align: center;"><?=$tot_horas?></td>
		    <!-- <td style="text-align: center;"><?="R$ ".number_format($tot, 2, ',', '.')?></td> -->
		</tr>		
		
<?php
if($_REQUEST['func'] == '-1'){
    $data1 = ConverteData($_REQUEST['data1']);
    $data2 = ConverteData($_REQUEST['data2']);
    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];  

    $where = "";
    
    if($_REQUEST['cracha'] != '-1') {
	$where = "and t.id_cracha_temporario = {$_REQUEST['cracha']}";
    }
    
    $sql =  "
	select 
		CONCAT('9000', t.id_cracha_temporario) as id, 
		p.data, min(hora) as entrada, if(MAX(hora) = MIN(hora), '---', MAX(hora)) AS saida, timediff(max(hora), min(hora)) as total,
		min(p.imagem) as img1, max(p.imagem) as img2
	from 
		cracha_temporario t inner join 
		terceiro_ponto p on t.id_cracha_temporario = p.id_cracha_temporario
	where 
		p.id_regiao = '{$id_regiao}' and p.id_projeto = '{$id_projeto}'
		and p.data between '{$data1}' and '{$data2}' {$where} 
	group 
		by CONCAT('9000', t.id_cracha_temporario), p.data
	order by
		p.data DESC, saida desc, entrada desc;";

    $result = mysql_query($sql);

    ?>

	<tr class="text-bold success">
	    <td colspan="9">
		Crachás Temporários
	    </td>
	</tr>
    <?php
    while ($row = mysql_fetch_array($result)){


	$p_inicio = date_create($row['data_entrada'].' '.$row['entrada']);
	$p_final = date_create($row['data_saida'].' '.$row['saida']);
	$intervalo = date_diff($p_inicio, $p_final);
	$t_horas = explode(':', $row['total']);
	$t_hora = $t_horas[0];
    ?>

	<tr class="linha_<?php echo ($alternateColor++%2==0)?"um":"dois"; ?>" style="font-size:12px;">
		<td><?=$row['id']?></td>
		<td></td>
		<td style="text-align: center;"><a href="../../fotos/<?=$row['img1']?>" target="_blank"><img src="../../fotos/<?=$row['img1']?>" width="40" height="30"/></a></td>
		<td style="text-align: center;"><?=date("d/m/Y", strtotime($row['data']))?></td>
		<td style="text-align: center;"><?=$row['entrada']?></td>
		<td style="text-align: center;"><a href="../../fotos/<?=$row['img2']?>" target="_blank"><img src="../../fotos/<?=$row['img2']?>" width="40" height="30"/></a></td>
		<td style="text-align: center;"><?=date("d/m/Y", strtotime($row['data']))?></td>
		<td style="text-align: center;"><?=$row['saida']?></td>
		<td style="text-align: center;"><?=$row['total']?></td>
	</tr>

    <?php
    }
}
?>
		
		
	    </table>
<?php } ?>
            <?php include_once '../../template/footer.php'; ?>
            </form>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.mask.min.js" type="text/javascript"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(document).ready(function(){
                $('#cad_escala').center();
            });
            $(window).resize(function() {
                $('#cad_escala').center();
            });

            $('.exibe').on('click', function() {
                var offset = $(this).offset();

                $('#nome_funcionario').html($(this).html());
                $('#id_colaborador').val($(this).attr('id'));
                $("#cad_escala").css("position","absolute");
                $("#cad_escala").css("top", offset.top - 40 + "px");
                $("#cad_escala").css("left", offset.left + 200 + "px");

                $('#func').empty();
                $("#cad_escala").css('display', 'block');    
            });

            $(function() {
                $('#data1').datepicker({
                        changeMonth: true,
                    changeYear: true
                });
                $('#data2').datepicker({
                        changeMonth: true,
                    changeYear: true
                });    
            });
            
            jQuery.fn.center = function () {
		this.css("position","absolute");
		this.css("top", Math.max(0, ((($(window).height() - $(this).outerHeight()) / 2) + $(window).scrollTop())/2) + "px");
		this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft()) + "px");
		return this;
	    }
            $(function() {
                var id_destination = "projeto";
                $('#regiao').ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, function(data) {
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");
                $('#projeto').change(function() {
                    var pro = $(this).val();
                    $.post("<?= $_SERVER['PHP_SELF'] ?>", {pro: pro, method: 'funcionarios', contratacao: $('#contratacao').val()}, function(data) {
                        var selected = "";
                        var unid = "<option value='-1'>« TODOS »</option>\n";
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
                    var pro = $('#projeto').val();
                    $.post("<?= $_SERVER['PHP_SELF'] ?>", {pro: pro, method: 'funcionarios', contratacao: $('#contratacao').val()}, function(data) {
                        var selected = "";
                        var unid = "<option value='-1'>« TODOS »</option>\n";
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
	    
	    function oculta_cad_escala() {
		$("#cad_escala").css('display', 'none');
	    }
	    
	    function enviar() {
		var dados = $('#popup').serialize();

		$.ajax({
		    url: "relatorio_acesso_escala.php",
		    data: dados,
		    dataType: "json",
		    type: "POST"
		});
		oculta_cad_escala();
	    }
	    function sel_cracha() {
		$("#func").val('-1');
	    }
	    
	    function sel_func() {
		$("#cracha").val('-1');
	    }
        </script>
    </body>
</html>