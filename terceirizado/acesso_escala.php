<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit; 
}
// teste
include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();

$id_regiao = $usuario['id_regiao'];

//echo "<br>\n" ;
//var_dump($usuario);
//echo "<br>\n";

//var_dump($_SESSION);
//echo "<br>\n";

//echo "<br>\n";
//var_dump($_SESSION);
//echo "<br>\n";

$id_curso = trim($_REQUEST['curso'])==''?-1:$_REQUEST['curso'];
$id_projeto = $_REQUEST['projeto'];
$regiao_in = inRegiao($optRegiao);

// #########################################################################################################
// Para alterar a tela com bootstrap, foi criado o campo máquina. Verificar onde existe a variável $maquina.

//echo "maquina = [{$_REQUEST['maquina']}]<br/>\n";

if(trim($_REQUEST['maquina'])=="")
{
    $maquina = 1;
    $nome_maquina = "HOSPITAL";
}else
{
    $maquina = $_REQUEST['maquina'];
    
    $sql = "select maquina from acesso_maquina where maquina_id = {$maquina}";
    $res = mysql_query($sql);
    $dados = mysql_fetch_assoc($res);
    
    $nome_maquina = $dados['maquina'];
}

//echo "maquina = [{$maquina}]<br/>\n";
//echo "<br>\n";
//echo $id_regiao."<br>\n";
//echo $optRegiao."<br>\n";

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

function data_br($data) {
    $datas = explode("-", $data);
    return $datas[2]."/".$datas[1]."/".$datas[0];
}

function inRegiao($regiao) {
    foreach($regiao as $id => $lin) {
	if($id >= 0)
	{
	    $in .= "{$id},";
	}
    }
    if(trim($in) != "") {
	return substr($in, 0, strlen($in) -1);
    }
}

function ret_func($data, $id_curso, $maquina)
{
    global $id_regiao;
    //echo "id_regiao = [{$id_regiao}]<br>\n";
    $sql = "		
	select * from
		(select p.acesso_plantao_id as id_escala, t.id_clt as id, t.nome, c.id_curso, c.nome as curso, 1 as tipo, p.hora_entrada, p.hora_saida
		from 
			acesso_plantao p inner join 
			acesso_assoc a on p.acesso_plantao_id = a.acesso_plantao_id inner join
			rh_clt t on t.id_clt = a.colaborador_id inner join 
			curso c on c.id_curso = t.id_curso
		where 
			'{$data}' BETWEEN DATE_FORMAT(p.data_inicio,'%Y-%m-%d') and DATE_FORMAT(p.data_fim,'%Y-%m-%d') 
			and a.tipo_colaborador = 1 and t.id_curso = '{$id_curso}' and p.regiao_id = {$id_regiao} and p.maquina_id = {$maquina}
		union
		select p.acesso_plantao_id as id_escala, t.id_autonomo as id, t.nome, c.id_curso, c.nome as curso, 2 as tipo, p.hora_entrada, p.hora_saida
		from 
			acesso_plantao p inner join 
			acesso_assoc a on p.acesso_plantao_id = a.acesso_plantao_id inner join
			autonomo t on t.id_autonomo = a.colaborador_id inner join 
			curso c on c.id_curso = t.id_curso
		where 
			'{$data}' BETWEEN DATE_FORMAT(p.data_inicio,'%Y-%m-%d') and DATE_FORMAT(p.data_fim,'%Y-%m-%d')
			and a.tipo_colaborador = 2 and t.id_curso = '{$id_curso}' and p.regiao_id = {$id_regiao} and p.maquina_id = {$maquina}
		union
		select p.acesso_plantao_id as id_escala, t.id_terceirizado as id, t.nome, c.id_curso, c.nome as curso, 3 as tipo, p.hora_entrada, p.hora_saida
		from 
			acesso_plantao p inner join 
			acesso_assoc a on p.acesso_plantao_id = a.acesso_plantao_id inner join
			terceirizado t on t.id_terceirizado = a.colaborador_id inner join 
			curso c on c.id_curso = t.id_curso
		where 
			'{$data}' BETWEEN DATE_FORMAT(p.data_inicio,'%Y-%m-%d') and DATE_FORMAT(p.data_fim,'%Y-%m-%d')
			and a.tipo_colaborador = 3 and t.id_curso = '{$id_curso}' and p.regiao_id = {$id_regiao} and p.maquina_id = {$maquina}
		union
		select p.acesso_plantao_id as id_escala, 0 as id, '' as nome, 0 as id_curso, '' as curso, 0 as tipo, p.hora_entrada, p.hora_saida
		from 
			acesso_plantao p
		where 
			'{$data}' BETWEEN DATE_FORMAT(p.data_inicio,'%Y-%m-%d') and DATE_FORMAT(p.data_fim,'%Y-%m-%d') and p.regiao_id = {$id_regiao} and p.maquina_id = {$maquina}
		group by p.acesso_plantao_id			    
	    ) tab
	    order by id_escala, nome
	";
    
    
    $result = mysql_query($sql);
    
    $func = array();
    $i = 0;
    while ($row = mysql_fetch_array($result)) 
	{
	$func[$i]['nome'] = $row['nome'];
	$func[$i]['id'] = $row['id_escala'];
	$func[$i]['entrada'] = $row['hora_entrada'];
	$func[$i]['saida'] = $row['hora_saida'];
	$i++;
    }
    
    return $func;
}

if($_POST['acao']=='del')
{
    $sql = "delete from acesso_assoc where acesso_plantao_id = {$_REQUEST['id']}";
    mysql_query($sql);
    
    $sql = "delete from acesso_plantao where acesso_plantao_id = {$_REQUEST['id']}";
    mysql_query($sql);
    
    $sql = "insert acesso_delete(acesso_plantao_id, maquina_id) values({$_REQUEST['id']}, {$maquina})";
    mysql_query($sql);
    //echo "id = [{$_REQUEST['id']}]<br>\n";
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
            concat(a.colaborador_id, ';', '1') as id
        from 
            acesso_assoc a inner join acesso_plantao p on a.acesso_plantao_id = p.acesso_plantao_id
        where 
            p.data_inicio = '{$data1}' and p.data_fim = '{$data2}'
            and a.colaborador_id in ({$funcs})";
		    
    } elseif($_REQUEST['contratacao'] == 'terceiro') 
	{
	$sql = "select 
            concat(a.colaborador_id, ';', '3') as id, t.nome
        from 
            acesso_assoc a inner join acesso_plantao p on a.acesso_plantao_id = p.acesso_plantao_id inner join
            terceirizado t on t.id_terceirizado = a.colaborador_id
        where 
            p.data_inicio = '{$data1}' and p.data_fim = '{$data2}'
            and a.colaborador_id in ({$funcs})";

    } else 
	{
	$sql = "select 
            concat(a.colaborador_id, ';', '2') as id, u.nome
        from 
            acesso_assoc a inner join acesso_plantao p on a.acesso_plantao_id = p.acesso_plantao_id inner join
            autonomo u on u.id_autonomo = a.colaborador_id
        where 
            p.data_inicio = '{$data1}' and p.data_fim = '{$data2}'
            and a.colaborador_id in ({$funcs})";
    }
    
	//echo "sql = [{$sql}]\n\n";
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
if ($_REQUEST['acao']=='conf_incluir') {
    $data_inc1 = $data1 . ' ' . $hora1;
    $data_inc2 = $data2 . ' ' . $hora2;
    
    $sql = "insert into acesso_plantao(data_inicio, data_fim, hora_entrada, hora_saida, regiao_id, projeto_id, maquina_id) values('{$data_inc1}', '{$data_inc2}', '{$hora1}', '{$hora2}', {$_REQUEST['regiao']}, {$_REQUEST['projeto']}, {$maquina})";
    mysql_query($sql);
    $id_plantao = mysql_insert_id();
    
    // Trava para não sobrepor as escalas
    /*
    foreach($_REQUEST['func_add'] as $lin)
    {
	list($id, $tipo) = explode(";", $lin);
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
	    a.colaborador_id = {$id} and
	    a.tipo_colaborador = {$tipo};";
	
	//echo "sql = [$sql]<br>\n<br>\n";
	    
	$result = mysql_query($sql);
	$row = mysql_fetch_row($result);

	//var_dump($row);
	
	if($row[0] == '0')
	{
	    $sql = "insert into acesso_assoc(acesso_plantao_id, colaborador_id, tipo_colaborador) values({$id_plantao}, {$id}, {$tipo})";
	    mysql_query($sql);
	}
    }
     * 
     */

    foreach($_REQUEST['func_add'] as $lin)
	{
	if(trim($lin) !== '')
	{
	    list($id, $tipo) = explode(";", $lin);
	    $sql = "insert into acesso_assoc(acesso_plantao_id, colaborador_id, tipo_colaborador) values({$id_plantao}, {$id}, {$tipo})";
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
    /*
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
    */
    
    $sql = "update acesso_plantao set data_inicio = '".$data1." ".$hora1."', data_fim = '".$data2." ".$hora2."', hora_entrada = '{$hora1}', hora_saida='{$hora2}', `update`='S' where acesso_plantao_id = {$_REQUEST['id']}";
    //echo "sql = [$sql]<br>\n";
    mysql_query($sql);
    
    $sql = "delete from acesso_assoc where acesso_plantao_id = {$_REQUEST['id']}";
    //echo "sql = [$sql]<br>\n";
    mysql_query($sql);

    // Trava para não sobrepor as escalas
    /*
    foreach($_REQUEST['func_add'] as $lin)
    {
	list($id, $tipo) = explode(";", $lin);
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
	    a.colaborador_id = {$id} and
	    a.tipo_colaborador = {$tipo};";
	    
	//echo "sql = [$sql]<br>\n<br>\n";
	$result = mysql_query($sql);
	$row = mysql_fetch_row($result);
	
	//var_dump($row);
	
	if($row[0] == '0')
	{
	    if(trim($lin) !== '')
	    {
		$sql = "insert into acesso_assoc(acesso_plantao_id, colaborador_id, tipo_colaborador) values({$_REQUEST['id']}, {$id}, {$tipo})";
		mysql_query($sql);
	    }
	}
    }
     * 
     */
    foreach($_REQUEST['func_add'] as $lin) 
	{
	if(trim($lin) !== '') 
	{
	    list($id, $tipo) = explode(";", $lin);
	    $sql = "insert into acesso_assoc(acesso_plantao_id, colaborador_id, tipo_colaborador) values({$_REQUEST['id']}, {$id}, {$tipo})";
	    mysql_query($sql);
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
		p.acesso_plantao_id as id, p.data_inicio, p.data_fim, p.hora_entrada, p.hora_saida, p.projeto_id, p.regiao_id, a.tipo_colaborador, a.colaborador_id, r.nome, lower(c.nome) as curso
	from 
		acesso_plantao p inner join 
		acesso_assoc a on p.acesso_plantao_id = a.acesso_plantao_id inner join
		rh_clt r on a.colaborador_id = r.id_clt inner join
		curso c on r.id_curso = c.id_curso
	where
		a.tipo_colaborador = 1 and p.acesso_plantao_id = {$id} and p.regiao_id = {$id_regiao} and p.maquina_id = {$maquina}
	union
	select 
		p.acesso_plantao_id as id, p.data_inicio, p.data_fim, p.hora_entrada, p.hora_saida, p.projeto_id, p.regiao_id, a.tipo_colaborador, a.colaborador_id, t.nome, lower(c.nome) as curso
	from 
		acesso_plantao p inner join 
		acesso_assoc a on p.acesso_plantao_id = a.acesso_plantao_id inner join
		terceirizado t on a.colaborador_id = t.id_terceirizado inner join
		curso c on t.id_curso = c.id_curso
	where
		a.tipo_colaborador = 3 and p.acesso_plantao_id = {$id} and p.regiao_id = {$id_regiao} and p.maquina_id = {$maquina}
	union
	select 
		p.acesso_plantao_id as id, p.data_inicio, p.data_fim, p.hora_entrada, p.hora_saida, p.projeto_id, p.regiao_id, a.tipo_colaborador, a.colaborador_id, u.nome, lower(c.nome) as curso
	from 
		acesso_plantao p inner join 
		acesso_assoc a on p.acesso_plantao_id = a.acesso_plantao_id inner join
		autonomo u on a.colaborador_id = u.id_autonomo inner join
		curso c on u.id_curso = c.id_curso
	where
		a.tipo_colaborador = 2 and p.acesso_plantao_id = {$id} and p.regiao_id = {$id_regiao} and p.maquina_id = {$maquina}
	union

	select 
		p.acesso_plantao_id as id, p.data_inicio, p.data_fim, p.hora_entrada, p.hora_saida, p.projeto_id, 
		p.regiao_id, '' as tipo_colaborador, '' as colaborador_id, '' as nome, '' as curso
	from 
		acesso_plantao p 
	where p.acesso_plantao_id = {$id};";
		
    //echo "sql = [$sql]\n";
    
    $result = mysql_query($sql);

    while ($row = mysql_fetch_array($result))
    {
	if(trim($row['nome'])=='')
	{
	    $nome = '';
	}else
	{
	    $nome = utf8_encode($row['nome'].' - ('.$row['curso'].')');
	}
	$func['func_add'][$con]['id'] = $row['id'];
	$func['func_add'][$con]['data_inicio'] = $row['data_inicio'];
	$func['func_add'][$con]['data_fim'] = $row['data_fim'];
	$func['func_add'][$con]['hora_entrada'] = $row['hora_entrada'];
	$func['func_add'][$con]['hora_saida'] = $row['hora_saida'];
	$func['func_add'][$con]['projeto_id'] = $row['projeto_id'];
	$func['func_add'][$con]['regiao_id'] = $row['regiao_id'];
	$func['func_add'][$con]['tipo_colaborador'] = $row['tipo_colaborador'];
	$func['func_add'][$con]['colaborador_id'] = $row['colaborador_id'];
	$func['func_add'][$con]['nome'] = $nome;
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
	$select_clt = "
	SELECT 
		concat(t.id_clt, ';', '1') as id, concat(t.nome, ' - ', '(',lower(c.nome),')') as nome
	FROM 
		rh_clt t inner join curso c on t.id_curso = c.id_curso
	WHERE 
		id_projeto = '{$_REQUEST['pro']}' 
	ORDER BY nome";
		
	$result_clt = mysql_query($select_clt);
	$i = 0;
	while ($row = mysql_fetch_array($result_clt)) {
	    $select_funcionarios[$i]['id']=$row['id'];
	    $select_funcionarios[$i]['nome']=utf8_encode($row['nome']);
	    $i++;
	}

	echo json_encode($select_funcionarios);
	exit();
    } elseif($_REQUEST['contratacao'] == 'terceiro') 
	{
	$select_ter = "
	SELECT 
		concat(t.id_terceirizado, ';', '3') as id, concat(t.nome, ' - ', '(',lower(c.nome),')') as nome
	FROM 
		terceirizado t inner join curso c on t.id_curso = c.id_curso
	WHERE 
		id_projeto = '{$_REQUEST['pro']}' 
	ORDER BY nome";

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
	$select_aut = "
	SELECT 
		concat(t.id_autonomo, ';', '2') as id, concat(t.nome, ' - ', '(',lower(c.nome),')') as nome
	FROM 
		autonomo t inner join curso c on t.id_curso = c.id_curso
	WHERE 
		id_projeto = '{$_REQUEST['pro']}' 
	ORDER BY nome";	
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

    $cores = array(
    '#7FC7AF', '#EDC951', '#00DFFC', '#F5634A', '#BFB35A', '#C6E5D9', 
    '#F1BBBA', '#00B4FF', '#FA2A00', '#FFF7BD', '#60B99A', '#EEDD99', 
    '#4DBCE9', '#F77825', '#99B2B7', '#FF9F80', '#EFFAB4', '#F2F2F2', 
    '#FF6B6B', '#F2E9E1');
//    $cores = array(
//    'bg-info', 'bg-danger', 'bg-success', 'bg-warning', 'bg-defaut', 'bg-primary', 
//    'tr-bg-info', 'tr-bg-danger', 'tr-bg-success', 'tr-bg-warning', 'tr-bg-defaut', 'tr-bg-primary');
    
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
	    if($mes > 12) {
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
		a.regiao_id = {$id_regiao} and a.maquina_id = {$maquina} and
		(a.data_inicio BETWEEN '{$ano}-{$mes}-01' and '{$ano}-{$mes}-".date("t", $data_t)."'
		or a.data_fim BETWEEN '{$ano}-{$mes}-01' and '{$ano}-{$mes}-".date("t", $data_t)."')
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

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Cadastro de Escala");
$breadcrumb_pages = array("Escalas" => "escalas2.php");

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Escalas</title>
        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/add-ons.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <form name="form" action="" method="post" id="form" class="form-horizontal">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>Cadastro de Escala</small></h2></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body text-center">
                            <div class="form-group">
                                <?php
$sql = "select * from acesso_maquina where regiao_id in ({$regiao_in})";
$result_maq = mysql_query($sql);

$maq = "";
while ($row = mysql_fetch_array($result_maq))
{
    $maq.="<option value='{$row['maquina_id']}' " . ($row['maquina_id'] == $maquina ? "selected='selected'" : "") . ">{$row['maquina']}</option>\n";
}
                                
                                $sql = "
	    select * from
	    (		select c.id_curso, c.nome as curso
			    from 
				    acesso_plantao p inner join 
				    acesso_assoc a on p.acesso_plantao_id = a.acesso_plantao_id inner join
				    rh_clt t on t.id_clt = a.colaborador_id inner join 
				    curso c on c.id_curso = t.id_curso
			    where a.tipo_colaborador = 1 and p.regiao_id in ({$regiao_in}) and p.maquina_id = {$maquina}
			    union
			    select c.id_curso, c.nome as curso
			    from 
				    acesso_plantao p inner join 
				    acesso_assoc a on p.acesso_plantao_id = a.acesso_plantao_id inner join
				    autonomo t on t.id_autonomo = a.colaborador_id inner join 
				    curso c on c.id_curso = t.id_curso
			    where a.tipo_colaborador = 2 and p.regiao_id in ({$regiao_in}) and p.maquina_id = {$maquina}
			    union
			    select c.id_curso, c.nome as curso
			    from 
				    acesso_plantao p inner join 
				    acesso_assoc a on p.acesso_plantao_id = a.acesso_plantao_id inner join
				    terceirizado t on t.id_terceirizado = a.colaborador_id inner join 
				    curso c on c.id_curso = t.id_curso
			    where a.tipo_colaborador = 3 and p.regiao_id in ({$regiao_in}) and p.maquina_id = {$maquina}) tab
	    group by id_curso, curso
	    order by curso
	    ";
                            
                                $result = mysql_query($sql);
                                $opt = "";
                                while ($row = mysql_fetch_array($result)) {
                                    $opt.="<option value='{$row['id_curso']}' ".($row['id_curso']==$id_curso?"selected='selected'":"").">{$row['curso']}</option>\n";
                                } ?>
                                <label class="control-label col-md-offset-3 col-md-1">Máquina: </label>
                                <div class="col-md-5" style="margin-bottom: 5px">
                                    <select name="maquina" class="form-control" id="maquina" onchange="enviar_cusro();">
                                        <option value='-1'><< Selecione >></option>
                                        <?=$maq?>
                                    </select>
                                </div>                                
                                <label class="control-label col-md-offset-3 col-md-1">Função: </label>
                                <div class="col-md-5">
                                    <select name="curso" class="form-control" id="curso" onchange="enviar_cusro();">
                                        <option value='-1'><< Selecione >></option>
                                        <?=$opt?>
                                    </select>
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                        </div>
                    </div><!-- panel -->
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 text-left">
                    <a class="btn btn-info" href="acesso_escala.php?data_uso=<?=$data_uso?>&inc=-1&curso=<?=$id_curso?>"><i class="fa fa-angle-double-left"></i> Mês Anterior</a>
                </div>
                <div class="col-md-8 text-center">
                    <label class="control-label"><?=$data_uso?></label>
                </div>
                <div class="col-md-2 text-right">
                    <a class="btn btn-info" href="acesso_escala.php?data_uso=<?=$data_uso?>&inc=1&curso=<?=$id_curso?>">Próximo Mês <i class="fa fa-angle-double-right"></i></a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="mv-container2" style="">
                        <table class='mv-daynames-table table table-condensed table-bordered'>
                            <thead>
                                <tr class="bg-default">
                                    <th class="center">Domingo</th>
                                    <th class="center">Segunda</th>
                                    <th class="center">Terça</th>
                                    <th class="center">Quarta</th>
                                    <th class="center">Quinta</th>
                                    <th class="center">Sexta</th>
                                    <th class="center">Sábado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $vezes = 0;
                                $mes = $mes -1;
                                if($mes < 1) {
                                    $ano = $ano - 1;
                                    $mes = 12;
                                }
                                foreach($calendario as $lin): ?>
                                    <tr>
                                        <?php
                                        for($i =0;$i<7;$i++):
                                            if($dia_corrente==$ultimo_dia_a+1) {
                                                $mes = $mes + 1;
                                                if($mes > 12) {
                                                    $ano = $ano + 1;
                                                    $mes = 1;
                                                }
                                                $dia_corrente = 1;
                                                $ultimo_dia_a = date("t", $data_t);
                                            }
                                            $data_ex = $ano.'-'.($mes < 10?'0'.$mes:$mes).'-'.($dia_corrente < 10?'0'.$dia_corrente:$dia_corrente);

                                            $rec = ret_func($data_ex, $id_curso, $maquina); ?>
                                            <td class="center text-bold pointer valign-middle">
                                                <div onclick="exibe_cad_escala('incluir', '0', '<?=$ano.'-'.($mes < 10?'0'.$mes:$mes).'-'.($dia_corrente < 10?'0'.$dia_corrente:$dia_corrente)?>', '<?=$id_curso?>')" data-toggle="modal" data-target="#cad_escala">
                                                    &nbsp;<?=$dia_corrente?>
                                                </div>
                                                <?php
                                                $id = 0;
                                                $j = -1;
                                                foreach($rec as $row): 
                                                    if($row['id'] != $id): $j++; ?>
                                                        <div class="center text-bold pointer" onclick="exibe_cad_escala('editar', '<?=$row['id']?>', '<?=$ano.'-'.($mes < 10?'0'.$mes:$mes).'-'.($dia_corrente < 10?'0'.$dia_corrente:$dia_corrente)?>', '<?=$id_curso?>')" style="background-color: <?=$cores[$j]?>" data-toggle="modal" data-target="#cad_escala">
                                                        <!--div class="center text-bold pointer <?=$cores[$j]?>" onclick="exibe_cad_escala('editar', '<?=$row['id']?>', '<?=$ano.'-'.($mes < 10?'0'.$mes:$mes).'-'.($dia_corrente < 10?'0'.$dia_corrente:$dia_corrente)?>', '<?=$id_curso?>')" data-toggle="modal" data-target="#cad_escala"-->
                                                            <?=$row['entrada']?>/<?=$row['saida']?>
                                                        </div>
                                                    <?php endif; ?>
                                                        <div class="center text-bold pointer" onclick="exibe_cad_escala('editar', '<?=$row['id']?>', '<?=$ano.'-'.($mes < 10?'0'.$mes:$mes).'-'.($dia_corrente < 10?'0'.$dia_corrente:$dia_corrente)?>', '<?=$id_curso?>')" style="background-color: <?=$cores[$j]?>" data-toggle="modal" data-target="#cad_escala">
                                                        <!--div class="center text-bold pointer <?=$cores[$j]?>" onclick="exibe_cad_escala('editar', '<?=$row['id']?>', '<?=$ano.'-'.($mes < 10?'0'.$mes:$mes).'-'.($dia_corrente < 10?'0'.$dia_corrente:$dia_corrente)?>', '<?=$id_curso?>')" data-toggle="modal" data-target="#cad_escala"-->
                                                            <?=$row['nome']?>
                                                        </div>
                                                    <?php $id = $row['id'];
                                                endforeach; ?>
                                            </td>
                                            <?php $dia_corrente++;
                                        endfor; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="cad_escala" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header" id="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="tit_pop"></h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="col-md-2 control-label"><!--Forma -->Máquina:</label>
                                <div class="col-md-10" style="margin-top: 8px">
                                    <?= $nome_maquina; ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label"><!--Forma -->Contratação:</label>
                                <div class="col-md-10">
                                    <select class="form-control" name="contratacao" id="contratacao">
                                        <option value="clt" <?=($_REQUEST['contratacao'])=='clt'?'selected="selected"':''?>>CLT</option>
                                        <option value="terceiro" <?=($_REQUEST['contratacao'])=='terceiro'?'selected="selected"':''?>>Terceirizado</option>
                                        <option value="cooperado" <?=($_REQUEST['contratacao'])=='cooperado'?'selected="selected"':''?>>Cooperado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Região:</label>
                                <div class="col-md-10">
                                    <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control', "style" => "font-size:10px;")); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Projeto:</label>
                                <div class="col-md-10">
                                    <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control', "style" => "font-size:10px;")); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Período:</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input name="data1" type="text" id="data1" class="form-control data" maxlength="11" value="<?=(!is_null($_REQUEST['data1']))?($_REQUEST['data1']):''?>">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                    </div>
                                </div>
                                <label class="col-md-2 margin_t10 text-center">até</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input name="data2" type="text" id="data2" class="form-control data" maxlength="11" value="<?=(!is_null($_REQUEST['data2']))?($_REQUEST['data2']):''?>">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Horário:</label>
                                <div class="col-md-4">
                                    <input name="hora1" class="form-control hora" type="text" id="hora1" maxlength="5" value="<?=(!is_null($_REQUEST['hora1']))?($_REQUEST['hora1']):''?>">
                                </div>
                                <label class="col-md-2 margin_t10 text-center">até</label>
                                <div class="col-md-4">
                                    <input name="hora2" class="form-control hora" type="text" id="hora2" maxlength="5" value="<?=(!is_null($_REQUEST['hora2']))?($_REQUEST['hora2']):''?>">
                                </div>
                            </div>
                            <h4 class="text-center">Funcionários</h4>
                            <div class="form-group">
                                <div class="col-md-5">
                                    <h5 class="text-center">Existentes</h5>
                                </div>
                                <div class="col-md-2"></div>
                                <div class="col-md-5">
                                    <h5 class="text-center">Adicionados</h5>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-5">
                                    <?php echo montaSelect($select_funcionarios, null, array('name' => "func", 'id' => 'func', "multiple" => "multiple", "class"=>"form-control"));?>
                                </div>
                                <div class="col-md-2 valign-middle">
                                    <button type="button" class="col-md-12 btn btn-default" onclick="addFunc();"><i class="fa fa-angle-double-right"></i></button>
                                    <button type="button" class="col-md-12 btn btn-default" onclick="rmFunc();"><i class="fa fa-angle-double-left"></i></button>
                                </div>
                                <div class="col-md-5">
                                    <?php echo montaSelect($select_funcionarios, null, array('name' => "func_add[]", 'id' => 'func_add', "multiple" => "multiple", "class"=>"form-control"));?>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="id" id="id" />
                            <input type="hidden" name="acao" id="acao" />
                            <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                            <input type="hidden" name="hide_funcao" id="hide_funcao" value="<?php echo $funcaoSel ?>" />

                            <div class="col-md-6 text-left">
                                <button type="button" class="btn btn-danger" name="apagar" value="Apagar Escala" id="apagar" onclick="apaga();">Apagar Escala</button>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" name="gerar" value="Confirmar" class="btn btn-primary" id="gerar" onclick="enviar();">Confirmar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once '../template/footer.php'; ?>
            </form>
        </div>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.mask.min.js" type="text/javascript"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
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
		this.css("position","fixed");
		//console.log(Math.max(0, ((($(window).height() - $(this).outerHeight()) / 2))) + "px");
		//this.css("top", Math.max(0, ((($(window).height() - $(this).outerHeight()) / 2))) + "px");
		this.css("top", "100px");
		this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft()) + "px");
		return this;
	    }
	    
	    function enviar_cusro() 
		{
		$("#acao").val('atualizar');
		$("#form").submit();
	    }
	    
	    function compDate(start, end) 
		{
		a = start.split(" ");
		b = a[0];
		c = a[1];

		start_d = b.split("/");
		start_h = c.split(":");

		a = end.split(" ");
		b = a[0];
		c = a[1];

		end_d = b.split("/");
		end_h = c.split(":");

		data_st = new Date(start_d[2], start_d[1]-1, start_d[0], start_h[0], start_h[1], 0, 0);
		data_en = new Date(end_d[2], end_d[1]-1, end_d[0], end_h[0], end_h[1], 0, 0);

		if(data_st < data_en) {
		    return true;
		} else {
		    return false;
		}
	    }
	   
	    function enviar() 
		{
		if(compDate($("#data1").val()+" "+$("#hora1").val(), $("#data2").val()+" "+$("#hora2").val()))
		{
		    $('#func_add').each(function() 
			{
			$('#func_add option').attr("selected","selected");
		    });

		    if($('#func_add option:selected').length > 0) 
			{
			$("#form").submit();
		    } else {
			alert('Nenhum colaborador foi selecionado!');
		    }
		} else {
		    alert('Data e hora inicial não podem ser maiores que data e hora final!');
		}
	    }
	    
	    function apaga()
	    {
		var r = confirm("A escala será permanentemente eliminada. Confirmar?");
		if (r == true) {
		    //console.log('Apagou');
		    $('#acao').val('del');
		    $("#form").submit();
		}		
	    }
	    
	    function showWaiting(par) {
		if(par == 1) {
		    $(".container2").css('visibility', 'visible');
		    $(".container2").css('display', 'block');
		} else {
		    $(".container2").css('visibility', 'hidden');
		    $(".container2").css('display', 'none');
		}
	    }
	    function exibe_cad_escala(acao, id, data, id_curso) {
		$('#cad_escala').center();
		$("#data1").val('');
		$("#data2").val('');
		$("#hora1").val('');
		$("#hora2").val('');
		$('#txt_projeto').val('');
		$('#txt_regiao').val('');
		$('#func_add').empty();
		$('#func').empty();
                
                $('#modal-header').removeClass('modal-header bg-warning');
                $('#modal-header').removeClass('modal-header bg-primary');
		//var dados = $('#frm_login').serialize();
		if(acao == 'editar') {
		    $("#apagar").show();
		    $('#tit_pop').html('Editar');
                    $('#modal-header').addClass('modal-header bg-warning');
		    $('#acao').val('conf_editar');
		    $('#id').val(id);
		    $.ajax({
			url: "acesso_escala.php",
			data: '&acao='+acao+'&id='+id+'&data='+data+'&curso='+id_curso+'&maquina='+$("#maquina").val(),
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
			    $('#func_add').append($("<option></option>").attr("value",lin.colaborador_id+';'+lin.tipo_colaborador).text(lin.nome));
			}
			showWaiting(2);
		    });
		} else {
		    $("#apagar").css('display', 'none');
                    $('#tit_pop').html('Incluir');
                    $('#modal-header').addClass('modal-header bg-primary');
                    
		    $("#data1").val(dataRevert(data));
		    $('#acao').val('conf_incluir');
		    $('#projeto').prop('disabled', false);
		    $('#regiao').prop('disabled', false);
		    $('#regiao').val('-1');
		    $('#projeto').empty();
		    $('#projeto').append($("<option></option>").attr("value",-1).text('« Selecione »'));
		}
	    }

	    function dataRevert(data) {
		datas = data.split("-",3);
		ano = datas[0];
		mes = datas[1];
		dias = datas[2].split(" ");
		dia = dias[0];
		hora = dias[1];
		
		return dia+'/'+mes+'/'+ano;
	    }
	    
	    function oculta_cad_escala() {
		$("#cad_escala").css('display', 'none');
	    }
	    
	    function addFunc() {
		$('#func option:selected').remove().appendTo('#func_add');
	    }
	    function rmFunc() {
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
                    if(pro != null){
                        $.post("<?= $_SERVER['PHP_SELF'] ?>", {pro: pro, method: 'funcionarios', contratacao: $('#contratacao').val()}, function(data) {
                            var selected = "";
                            if(data != null){
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
                            }
                            $("#func").html(unid);
                        }, 'json');
                    }
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
        <script>
        $(document).ready(function(){
            $('#cad_escala').center();
        });
        $(window).resize(function() {
            $('#cad_escala').center();
        });

        $(function() {
            $('.data').datepicker({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '2005:c+1'
            });

            $('.hora').mask('00:00', {reverse: false});
        });
        </script>
    </body>
</html>