<?php
#teste
if (empty($_COOKIE['logado']))
{
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

$id_regiao = $usuario['id_regiao'];

//echo "<br>\n";
//var_dump($usuario);
//echo "<br>\n";
//var_dump($_SESSION);
//echo "<br>\n";
//echo "<br>\n";
//var_dump($_SESSION);
//echo "<br>\n";

$id_curso = trim($_REQUEST['curso']) == '' ? -1 : $_REQUEST['curso'];
$id_projeto = $_REQUEST['projeto'];
$regiao_in = inRegiao($optRegiao);

// #########################################################################################################
// Para alterar a tela com bootstrap, foi criado o campo máquina. Verificar onde existe a variável $maquina.
//echo "maquina = [{$_REQUEST['maquina']}]<br/>\n";

if (trim($_REQUEST['maquina']) == "")
{
    $maquina = 1;
    $nome_maquina = "HOSPITAL";
} else
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

$hora1 = (strlen($_REQUEST['hora1']) > 5 ? $_REQUEST['hora1'] : $_REQUEST['hora1'] . ":00");
$hora2 = (strlen($_REQUEST['hora2']) > 5 ? $_REQUEST['hora2'] : $_REQUEST['hora2'] . ":00");

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
    return $datas[2] . "/" . $datas[1] . "/" . $datas[0];
}

function inRegiao($regiao)
{
    foreach ($regiao as $id => $lin)
    {
        if ($id >= 0)
        {
            $in .= "{$id},";
        }
    }
    if (trim($in) != "")
    {
        return substr($in, 0, strlen($in) - 1);
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

if ($_POST['acao'] == 'del')
{
    $sql = "delete from acesso_assoc where acesso_plantao_id = {$_REQUEST['id']}";
    mysql_query($sql);

    $sql = "delete from acesso_plantao where acesso_plantao_id = {$_REQUEST['id']}";
    mysql_query($sql);

    $sql = "insert acesso_delete(acesso_plantao_id, maquina_id) values({$_REQUEST['id']}, {$maquina})";
    mysql_query($sql);

    //echo "id = [{$_REQUEST['id']}]<br>\n";
}

if ($_REQUEST['acao'] == 'pesquisa_func')
{
    $funcs = '';
    $func = array();
    $dif = array();

    foreach ($_REQUEST['func_add'] as $lin)
    {
        $funcs .= "{$lin}, ";
    }
    $funcs = substr($funcs, 0, strlen($funcs) - 2);

    if ($_REQUEST['contratacao'] == 'clt')
    {
        $sql = "select 
		    concat(a.colaborador_id, ';', '1') as id
		from 
		    acesso_assoc a inner join acesso_plantao p on a.acesso_plantao_id = p.acesso_plantao_id
		where 
		    p.data_inicio = '{$data1}' and p.data_fim = '{$data2}'
		    and a.colaborador_id in ({$funcs})";
    } elseif ($_REQUEST['contratacao'] == 'terceiro')
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
    for ($i = 0; $i < sizeof($dif); $i++)
    {
        $dif = array_diff_key($_REQUEST['func_add'], $func);
    }

    echo json_encode($dif);
    exit();
}

//Confirmando inclusão.
if ($_REQUEST['acao'] == 'conf_incluir')
{
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

    foreach ($_REQUEST['func_add'] as $lin)
    {
        if (trim($lin) !== '')
        {
            list($id, $tipo) = explode(";", $lin);
            $sql = "insert into acesso_assoc(acesso_plantao_id, colaborador_id, tipo_colaborador) values({$id_plantao}, {$id}, {$tipo})";
            mysql_query($sql);
        }
    }
}

if ($_REQUEST['acao'] == 'deletar')
{
    $sql = "delete from acesso_assoc where acesso_plantao_id = {$_REQUEST['id']}";
    mysql_query($sql);

    $sql = "delete from acesso_plantao where acesso_plantao_id = {$_REQUEST['id']}";
    mysql_query($sql);
}

if ($_REQUEST['acao'] == 'conf_editar')
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

    $sql = "update acesso_plantao set data_inicio = '" . $data1 . " " . $hora1 . "', data_fim = '" . $data2 . " " . $hora2 . "', hora_entrada = '{$hora1}', hora_saida='{$hora2}', `update`='S' where acesso_plantao_id = {$_REQUEST['id']}";
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
    foreach ($_REQUEST['func_add'] as $lin)
    {
        if (trim($lin) !== '')
        {
            list($id, $tipo) = explode(";", $lin);
            $sql = "insert into acesso_assoc(acesso_plantao_id, colaborador_id, tipo_colaborador) values({$_REQUEST['id']}, {$id}, {$tipo})";
            mysql_query($sql);
        }
    }
}

if ($_REQUEST['acao'] == 'editar')
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
        if (trim($row['nome']) == '')
        {
            $nome = '';
        } else
        {
            $nome = utf8_encode($row['nome'] . ' - (' . $row['curso'] . ')');
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
    if ($_REQUEST['contratacao'] == 'clt')
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
        while ($row = mysql_fetch_array($result_clt))
        {
            $select_funcionarios[$i]['id'] = $row['id'];
            $select_funcionarios[$i]['nome'] = utf8_encode($row['nome']);
            $i++;
        }

        echo json_encode($select_funcionarios);
        exit();
    } elseif ($_REQUEST['contratacao'] == 'terceiro')
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
        $i = 0;
        while ($row = mysql_fetch_array($result_ter))
        {
            $select_funcionarios[$i]['id'] = $row['id'];
            $select_funcionarios[$i]['nome'] = utf8_encode($row['nome']);
            $i++;
        }

        echo json_encode($select_funcionarios);
        exit();
    } else
    {
        $select_aut = "
	SELECT 
		concat(t.id_autonomo, ';', '2') as id, concat(t.nome, ' - ', '(',lower(c.nome),')') as nome
	FROM 
		autonomo t inner join curso c on t.id_curso = c.id_curso
	WHERE 
		id_projeto = '{$_REQUEST['pro']}' 
	ORDER BY nome";
        $result_aut = mysql_query($select_aut);
        $i = 0;
        while ($row = mysql_fetch_array($result_aut))
        {
            $select_funcionarios[$i]['id'] = $row['id'];
            $select_funcionarios[$i]['nome'] = utf8_encode($row['nome']);
            $i++;
        }

        echo json_encode($select_funcionarios);
        exit();
    }
} else
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



    $data1 = "01/" . $_REQUEST['data_uso'];
    //$data1 = '01/02/2014';

    if (trim($_REQUEST['data_uso']) != '')
    {
        $datas = explode('/', $data1);
        $ano = $datas[2];
        $mes = $datas[1];

        if (isset($_REQUEST['inc']))
        {
            $mes = $mes + $_REQUEST['inc'];
            if ($mes > 12)
            {
                $ano++;
                $mes = 1;
            }
            if ($mes < 1)
            {
                $ano = $ano - 1;
                $mes = 12;
            }
        }

        $data_t = mktime(0, 0, 0, $mes, '01', $ano);
    } else
    {
        $ano = date('Y');
        $mes = date('m');
        $data_t = mktime(0, 0, 0, date('m'), '01', date('Y'));
    }

    $data_uso = $mes . "/" . $ano;

    if ($mes == 1)
    {
        $mes_a = 12;
        $ano_a = $ano - 1;
    } else
    {
        $mes_a = $ano;
        $mes_a = $mes - 1;
    }

    $data_a = mktime(0, 0, 0, ($mes_a), '01', $ano_a);
    $ultimo_dia_a = date("t", $data_a);

    $data = date('D', $data_t);
    $dia = date('d', $data_t);
    $primeiro_dia_semana = date('w', $data_t);
    $dia_corrente = ($ultimo_dia_a - ($primeiro_dia_semana - 1));

    $ultimo_dia = date("t", $data_t) + ($primeiro_dia_semana == 0 ? 7 : $primeiro_dia_semana);

    $con = 0;
    $eventos = array();

    $mes_ant = $mes - 1;
    if ($mes_ant < 1)
    {
        $mes_ant = 12;
        $ano_ant = $ano - 1;
    } else
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
		(a.data_inicio BETWEEN '{$ano}-{$mes}-01' and '{$ano}-{$mes}-" . date("t", $data_t) . "'
		or a.data_fim BETWEEN '{$ano}-{$mes}-01' and '{$ano}-{$mes}-" . date("t", $data_t) . "')
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

    for ($i = 0; $i < 42; $i++)
    {

        $calendario[$lin][$col] = "";

        if ($primeiro_dia_semana <= $i && $ultimo_dia > $i && (($primeiro_dia_semana == '0' && $lin > 0) ? TRUE : (($primeiro_dia_semana != '0') ? TRUE : FALSE)))
        {
            $calendario[$lin][$col] = date('Y-m-d', mktime(0, 0, 0, $mes, $dia, $ano));
            $dia++;
        }
        $col++;

        if ($col === 7)
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
    return mktime(0, 0, 0, $args[1], $args[2], $args[0]);
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
            .cantos2{
                -moz-border-radius:3px;
                -webkit-border-radius:3px;
                border-radius:3px;
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
                this.css("position", "fixed");
                //console.log(Math.max(0, ((($(window).height() - $(this).outerHeight()) / 2))) + "px");
                //this.css("top", Math.max(0, ((($(window).height() - $(this).outerHeight()) / 2))) + "px");
                this.css("top", "100px");
                this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft()) + "px");
                return this;
            }

            function enviar_curso()
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

                // Leva em consideração data e hora.
                // data_st = new Date(start_d[2], start_d[1]-1, start_d[0], start_h[0], start_h[1], 0, 0);
                // data_en = new Date(end_d[2], end_d[1]-1, end_d[0], end_h[0], end_h[1], 0, 0);

                // Leva em consideração apenas a data.
                data_st = new Date(start_d[2], start_d[1] - 1, start_d[0], start_h[0], start_h[1], 0, 0);
                data_en = new Date(end_d[2], end_d[1] - 1, end_d[0], start_h[0], start_h[1], 0, 0);

                console.log("data_st = " + data_st);
                console.log("data_en = " + data_en);

                if (data_st <= data_en)
                {
                    console.log('true');
                    return true;
                } else
                {
                    console.log('false');
                    return false;
                }
            }

            function enviar()
            {
                if (compDate($("#data1").val() + " " + $("#hora1").val(), $("#data2").val() + " " + $("#hora2").val()))
                {
                    $('#func_add').each(function ()
                    {
                        $('#func_add option').attr("selected", "selected");
                    });

                    if ($('#func_add option:selected').length > 0)
                    {
                        $("#form").submit();
                    } else
                    {
                        alert('Nenhum colaborador foi selecionado!');
                    }
                } else
                {
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

            function showWaiting(par)
            {
                if (par == 1)
                {
                    $(".container").css('visibility', 'visible');
                    $(".container").css('display', 'block');
                } else
                {
                    $(".container").css('visibility', 'hidden');
                    $(".container").css('display', 'none');
                }
            }
            function exibe_cad_escala(acao, id, data, id_curso)
            {
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
                if (acao == 'editar')
                {
                    $("#apagar").css('display', 'block');
                    $('#tit_pop').html('Editar');
                    $('#acao').val('conf_editar');
                    $('#id').val(id);
                    $.ajax({
                        url: "acesso_escala.php",
                        data: '&acao=' + acao + '&id=' + id + '&data=' + data + '&curso=' + id_curso + '&maquina=' + $("#maquina").val(),
                        dataType: "json",
                        type: "POST",
                        beforeSend: function () {
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
                                $('#projeto').append($("<option></option>").attr("value", func_add[0].projeto_id).text(func_add[0].projeto_id + ' - ' + data.projeto));
                                $('#regiao').val(func_add[0].regiao_id);

                                $('#projeto').prop('disabled', 'disabled');
                                $('#regiao').prop('disabled', 'disabled');

                                for (var i = 0; i < func_add.length; ++i)
                                {
                                    lin = func_add[i];
                                    $('#func_add').append($("<option></option>").attr("value", lin.colaborador_id + ';' + lin.tipo_colaborador).text(lin.nome));
                                }
                                showWaiting(2);
                            });
                } else
                {
                    $("#apagar").css('display', 'none');
                    $('#tit_pop').html('Incluir');
                    $("#data1").val(dataRevert(data));
                    $('#acao').val('conf_incluir');
                    $('#projeto').prop('disabled', false);
                    $('#regiao').prop('disabled', false);
                    $('#regiao').val('-1');
                    $('#projeto').empty();
                    $('#projeto').append($("<option></option>").attr("value", -1).text('« Selecione »'));
                }

                $("#cad_escala").css('display', 'block');
            }

            function dataRevert(data)
            {
                datas = data.split("-", 3);
                ano = datas[0];
                mes = datas[1];
                dias = datas[2].split(" ");
                dia = dias[0];
                hora = dias[1];

                return dia + '/' + mes + '/' + ano;
            }

            function oculta_cad_escala()
            {
                $("#cad_escala").css('display', 'none');
            }


            function addFunc()
            {
                $('#func option:selected').remove().appendTo('#func_add');
            }
            function rmFunc()
            {
                $('#func_add option:selected').remove().appendTo('#func');
            }

            $(function () {
                var id_destination = "projeto";
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function (data) {
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");

                $('#projeto').change(function () {
                    $('#func').empty();
                    var pro = $(this).val();
                    $.post("<?= $_SERVER['PHP_SELF'] ?>", {pro: pro, method: 'funcionarios', contratacao: $('#contratacao').val()}, function (data) {
                        var selected = "";

                        //var unid = "<option value='-1'>« Selecione »</option>\n";
                        var unid = "";
                        $.each(data, function (k, v) {
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

                $('#contratacao').change(function () {
                    $('#func').empty();
                    var pro = $('#projeto').val();
                    $.post("<?= $_SERVER['PHP_SELF'] ?>", {pro: pro, method: 'funcionarios', contratacao: $('#contratacao').val()}, function (data) {
                        var selected = "";

                        //var unid = "<option value='-1'>« Selecione »</option>\n";
                        var unid = "";
                        $.each(data, function (k, v) {
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
            function formata_data(obj, prox) {
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
        <form  name="form" action="" method="post" id="form">
            <div class="container" style="z-index: 10000; position:fixed; top:1px">
                <center>
                    <div id="exampleModal" class="reveal-modal" style="z-index: 10001">
                        <img src="./img/loading.gif" id="l_cerveja" />
                    </div>
                </center>
            </div>
            <!-- ################################################################################### -->
            <!-- Popup cadastro escala style="z-index: 1000; padding:0px;display: none;position:fixed; width:850px; height:540px; top:68px; right:20px;"> -->
            <div id="cad_escala" class="sombra cantos" style="z-index: 100; padding:0px;display: none;position:fixed; width:800px; height:525px; top:68px; right:20px; background-color:#FFF">
                <div style="text-align: right;padding:10px 10px 0 10px;">
                    <img src="./img/close_button.gif" onclick="oculta_cad_escala();"/>
                </div>
                <div id="page" style="margin: 5px; height: 445px">
                    <input type="hidden" name="id" id="id" />
                    <input type="hidden" name="acao" id="acao" />
                    <fieldset class="noprint">
                        <legend id="tit_pop"></legend>
                        <div class="fleft">
                            <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                            <input type="hidden" name="hide_funcao" id="hide_funcao" value="<?php echo $funcaoSel ?>" />
                            <p>
                                <label class="first" style='width: 200px'>Máquina:</label><?= $nome_maquina; ?>
                            </p>
                            <p><label class="first" style='width: 200px'>Forma Contratação:</label>
                                <select name='contratacao' id='contratacao'>
                                    <option value='clt' <?= ($_REQUEST['contratacao']) == 'clt' ? 'selected="selected"' : '' ?>>CLT</option>
                                    <option value='terceiro' <?= ($_REQUEST['contratacao']) == 'terceiro' ? 'selected="selected"' : '' ?>>Terceirizado</option>
                                    <option value='cooperado' <?= ($_REQUEST['contratacao']) == 'cooperado' ? 'selected="selected"' : '' ?>>Cooperado</option>
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
                                <input name="data1" type="text" id="data1" size="11" maxlength="11" value="<?= (!is_null($_REQUEST['data1'])) ? ($_REQUEST['data1']) : '' ?>"> até 
                                <input name="data2" type="text" id="data2" size="11" maxlength="11" value="<?= (!is_null($_REQUEST['data2'])) ? ($_REQUEST['data2']) : '' ?>">
                            </p>
                            <p><label class="first" style='width: 200px'>Horário:</label> 
                                <input name="hora1" class="hora" type="text" id="hora1" size="7" maxlength="5" value="<?= (!is_null($_REQUEST['hora1'])) ? ($_REQUEST['hora1']) : '' ?>"> até 
                                <input name="hora2" class="hora" type="text" id="hora2" size="7" maxlength="5" value="<?= (!is_null($_REQUEST['hora2'])) ? ($_REQUEST['hora2']) : '' ?>">
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
<?php echo montaSelect($select_funcionarios, null, array('name' => "func", 'id' => 'func', "multiple" => "multiple", "size" => "10", "style" => "width: 340px; font-size:10px;")); ?>
                                            </td>
                                            <td style="text-align: center">
                                                <input type="button" value="->" style="width: 50px" onclick="addFunc();" /><br>
                                                <input type="button" value="<-" style="width: 50px" onclick="rmFunc();" />
                                            </td>
                                            <td style="text-align: center">
<?php echo montaSelect($select_funcionarios, null, array('name' => "func_add[]", 'id' => 'func_add', "multiple" => "multiple", "size" => "10", "style" => "width: 340px; font-size:10px;")); ?>
                                            </td>
                                        </tr>
                                    </table>
                                </center>
                            </div>
                        </div>

                        <br class="clear"/>&nbsp;


                    </fieldset>
                    <p style="margin-top: 10px; text-align: right; padding: 10px">
                    <table border="0" style="width: 100%">
                        <tr>
                            <td style="text-align: left"><input style="background-color: #F5634A" type="button" name="apagar" value="Apagar Escala" id="apagar" onclick="apaga();"/></td>
                            <td style="text-align: right"><input type="button" name="gerar" value="Confirmar" id="gerar" onclick="enviar();"/></td>
                        </tr>
                    </table>

                    </p>
                </div>        
            </div>	
            <!-- Fim popup cadastro escala -->
            <!-- ################################################################################### ##-->

            <div id="content" style="padding: 0 15px 0 15px">

                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Cadastro de Escala 2</h2>
                    </div>
                </div>
            </div>
<?
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
while ($row = mysql_fetch_array($result))
{
    $opt.="<option value='{$row['id_curso']}' " . ($row['id_curso'] == $id_curso ? "selected='selected'" : "") . ">{$row['curso']}</option>\n";
}
?>
            <div style="background-color:#fff; text-align: center">
                <center>
                    <div class="cantos2" style="background-color: #ECF4FC; width: 500px; padding: 4px">
                        Máquina:
                        <select name="maquina" id="maquina" onchange="enviar_curso();" style="width: 400px">
                            <option value='-1'><< Selecione >></option>
<?= $maq ?>
                        </select>
                    </div>
                    <div class="cantos2" style="background-color: #ECF4FC; width: 500px; padding: 4px">
                        Função:
                        <select name="curso" id="curso" onchange="enviar_curso();" style="width: 400px">
                            <option value='-1'><< Selecione >></option>
<?= $opt ?>
                        </select>
                    </div>
                    <div style="padding: 2px"></div>
                    <div class="cantos2" style="background-color: #ECF4FC; width: 500px; padding: 4px">
                        <a href="acesso_escala.php?data_uso=<?= $data_uso ?>&inc=-1&curso=<?= $id_curso ?>">Mês Anterior</a> <?= $data_uso ?> <a href="acesso_escala.php?data_uso=<?= $data_uso ?>&inc=1&curso=<?= $id_curso ?>">Próximo Mês</a>
                    </div>
                </center>
            </div>

            <div class="mv-container" style="padding: 5px 5px 5px 5px;">
                <table style="height: 100%; margin-bottom: 5px" border="0" cellspacing="0" cellpadding="0">
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
$vezes = 0;
$mes = $mes - 1;
if ($mes < 1)
{
    $ano = $ano - 1;
    $mes = 12;
}
foreach ($calendario as $lin):
    ?>
                        <tr>
                            <td>
                                <table class='mv-daynames-table' style="border-right: #9E9FA0 solid 1px; border-bottom: #9E9FA0 solid 1px; width: 100%; height: 100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
    <?
    for ($i = 0; $i < 7; $i++):

        if ($dia_corrente == $ultimo_dia_a + 1)
        {
            $mes = $mes + 1;
            if ($mes > 12)
            {
                $ano = $ano + 1;
                $mes = 1;
            }
            $dia_corrente = 1;
            $ultimo_dia_a = date("t", $data_t);
        }
        $data_ex = $ano . '-' . ($mes < 10 ? '0' . $mes : $mes) . '-' . ($dia_corrente < 10 ? '0' . $dia_corrente : $dia_corrente);

        $rec = ret_func($data_ex, $id_curso, $maquina);
        ?>
            <td> 
                <table style="border-left: #9E9FA0 solid 1px; width: 100%; height: 100%" border="0" cellspacing="0" cellpadding="0">
                    <tr style="vertical-align: top">
                        <td onclick="exibe_cad_escala('incluir', '0', '<?= $ano . '-' . ($mes < 10 ? '0' . $mes : $mes) . '-' . ($dia_corrente < 10 ? '0' . $dia_corrente : $dia_corrente) ?>', '<?= $id_curso ?>')" style="cursor:pointer; border-left: #9E9FA0 solid 1px; font-weight: bold">
                            &nbsp;<?= $dia_corrente ?>
                        </td>
                    </tr>
        <?
        $id = 0;
        $j = -1;
        foreach ($rec as $row):
            ?>
            <?
            if ($row['id'] != $id):
                $j++;
                ?>
                <tr><td onclick="exibe_cad_escala('editar', '<?= $row['id'] ?>', '<?= $ano . '-' . ($mes < 10 ? '0' . $mes : $mes) . '-' . ($dia_corrente < 10 ? '0' . $dia_corrente : $dia_corrente) ?>', '<?= $id_curso ?>')" style="cursor:pointer; text-align: center; background-color: <?= $cores[$j] ?>">
                <?= $row['entrada'] ?>/<?= $row['saida'] ?>
                    </td></tr>
                    <? endif; ?>				    
            <tr>
                <td onclick="exibe_cad_escala('editar', '<?= $row['id'] ?>', '<?= $ano . '-' . ($mes < 10 ? '0' . $mes : $mes) . '-' . ($dia_corrente < 10 ? '0' . $dia_corrente : $dia_corrente) ?>', '<?= $id_curso ?>')" style="cursor:pointer; border-left: #9E9FA0 solid 1px; background-color: <?= $cores[$j] ?>">
            <?= $row['nome'] ?></ br>
                </td>
            </tr>
                    <?
                    $id = $row['id'];
                endforeach;
                ?>

                                                </table>
                                            </td>
        <? $dia_corrente++; ?>
                                        <? endfor ?>
                                    </tr>
                                </table>		
                            </td>
                        </tr>
<? endforeach; ?>
                </table>

            </div>	

            <script>
                $(document).ready(function () {
                    $('#cad_escala').center();
                });
                $(window).resize(function () {
                    $('#cad_escala').center();
                });

                $(function () {
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
                    $('.hora').mask('00:00', {reverse: false});
                });


            </script>
        </form>
    </body>
</html>