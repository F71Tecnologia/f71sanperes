<?php
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

if (isset($_REQUEST['gerar'])) {

    if(isset($id_regiao) && isset($id_projeto))
    {
		$where = "where p.id_regiao = '{$id_regiao}' and p.id_projeto = '{$id_projeto}' ";
    }else
    {
		exit();
    }
    
    if(isset($data1) && isset($data2))
    {
	$where .= " and p.data between '$data1' and '$data2' ";
    }
    
    if($_REQUEST['contratacao'] == 'clt')
    {
	if($_REQUEST['func'] != '-1')
	{
	    $where .="and t.id_clt = {$_REQUEST['func']} ";
	}
	
	$sql = "select p.pis, t.nome, c.nome as curso, p.data, p.hora, p.imagem
		from rh_clt t inner join
		    terceiro_ponto p on t.pis = p.pis and t.pis is not null and t.pis <> '' inner join 
		    curso c on c.id_curso = t.id_curso 
		".$where."
		order by
			t.nome, p.data_completa desc";
	
    }elseif($_REQUEST['contratacao'] == 'terceiro')
    {
	if($_REQUEST['func'] != '-1')
	{
	    $where .="and t.id_terceirizado = {$_REQUEST['func']} ";
	}
	
	$sql = "select p.id_terceirizado, t.nome, c.nome as curso, p.data, p.hora, p.imagem
		from
			terceirizado t inner join 
			terceiro_ponto p on t.id_terceirizado = p.id_terceirizado inner join 
			curso c on c.id_curso = t.id_curso 
		".$where."
		order by
			t.nome, p.data_completa desc";
		
    }else
    {
	if($_REQUEST['func'] != '-1')
	{
	    $where .="and t.id_autonomo = {$_REQUEST['func']} ";
	}
	
	$sql = "select p.id_autonomo, t.nome, c.nome as curso, p.data, p.hora, p.imagem
			from
			    autonomo t inner join 
			    terceiro_ponto p on t.id_autonomo = p.id_autonomo inner join 
			    curso c on c.id_curso = t.id_curso
			".$where."
			order by
				t.nome, p.data_completa desc";
    }
    $result = mysql_query($sql);
    //echo "sql = [$sql]<br>\n";
    //exit();
}

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

?>
<html>
    <head>
        <title>:: Intranet :: Controle de Acesso</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
	<link href="../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="../js/jquery.ui.datepicker-pt-BR.js"></script>
        <script src="../js/global.js" type="text/javascript"></script>

        <script>
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
        </script>
    </head>
    <body class="novaintra" >        
        <div id="content">
            <form  name="form" action="" method="post" id="form">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Relatório de Controle de Acesso</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>

                <fieldset class="noprint">
                    <legend>Relatório</legend>
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
                        <p><label class="first" style='width: 200px'>Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?> </p>                        
                        <p><label class="first" style='width: 200px'>Projeto:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?> </p>
                        <p><label class="first" style='width: 200px'>Período:</label> 
			    <input name="data1" type="text" id="data1" size="11" maxlength="11" value="<?=(!is_null($_REQUEST['data1']))?($_REQUEST['data1']):''?>"> até 
			    <input name="data2" type="text" id="data2" size="11" maxlength="11" value="<?=(!is_null($_REQUEST['data2']))?($_REQUEST['data2']):''?>">
			</p>
                        <p><label class="first" style='width: 200px'>Funcionário:</label> <?php echo montaSelect($select_funcionarios, null, array('name' => "func", 'id' => 'func')); ?> </p>
                    </div>

                    <br class="clear"/>

                    <p class="controls" style="margin-top: 10px;">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>

                <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel"></p>    
                
<? if (isset($_REQUEST['gerar'])): ?>
		
	    <table id='tbRelatorio' cellpadding="8" cellspacing="0" style="border:0px; background-color:#f5f5f5; margin:0px auto; margin-top:1px; width:100%;">
		<tr>
		    <td class="show" colspan="9">
			<span style="color:#F90; font-size:32px;">></span>
			TERCEIRIZADOS
		    </td>
		</tr>
	       <tr class="novo_tr">
		   <td>NOME</td>
		   <td>FUNÇÃO</td>
		   <td style="text-align: center;">DATA REGISTRO</td>
		   <td style="text-align: center;">HORA REGISTRO</td>
		   <td style="text-align: center;">IMAGEM</td>
	       </tr>

		<?php 
		$tot = 0;
		$nome = "";
		$cor = "#FFFFFF";
		while ($row = mysql_fetch_array($result)):
		    if($row['nome']!=$nome)
		    {
			$cor = ($cor=="#FFFFFF"?"#B9E3A7":"#FFFFFF");
		    }		    
		?>

		<tr class="linha_<?php echo ($alternateColor++%2==0)?"um":"dois"; ?>" style="background-color:<?=$cor?>;font-size:12px;">
		    <td><?=($row['nome']==$nome?'':$row['nome'])?></td>
		    <td><?=($row['nome']==$nome?'':$row['curso'])?></td>
		    <td style="text-align: center;"><?=date("d/m/Y", strtotime($row['data']))?></td>
		    <td style="text-align: center;"><?=$row['hora']?></td>
		    <td style="text-align: center;"><a href="../fotos/<?=$row['imagem']?>" target="_blank"><img src="../fotos/<?=$row['imagem']?>" width="40" height="30"/></a></td>
		</tr>

		<?php 
		    $nome = $row['nome'];
		    $tot = $tot + $row['valor'];
		endwhile; ?>

		<tr class="linha_<?php echo ($alternateColor++%2==0)?"um":"dois"; ?>" style="font-size:12px;">
		    <td></td>
		    <td></td>
		    <td style="text-align: center;"></td>
		    <td style="text-align: center;"></td>
		    <td style="text-align: center;"></td>
		</tr>		
		
	    </table>
<? endif;?>
            </form>
        </div>
<script>	
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
</script>
    </body>
</html>