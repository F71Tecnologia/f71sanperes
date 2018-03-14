<?php
	if (empty($_COOKIE['logado'])) {
		print "<script>location.href = '../login.php?entre=true';</script>";
		exit;
	}
	
	require_once('../conn.php');
	require_once('../classes/funcionario.php');
	require_once('../classes_permissoes/regioes.class.php');
	require_once('../wfunction.php');
	require_once('../classes_permissoes/acoes.class.php');
	require_once('../classes/global.php');
        include "../classes/LogClass.php";
        $log = new Log();
	// require_once('../../framework/app/controller/helpers/breadCrumbClass.php');
	
	// Classe do breadcrump
	
	
	
	$usuario = carregaUsuario();
	$optRegiao = getRegioes();
	$optMeses = mesesArray();
	
	$global = new GlobalClass();
	
	$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
	
	$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Lançar movimentos em lote");
	$breadcrumb_pages = array("Gestão de RH"=>"/intranet/rh/principalrh.php");
	
	// echo '<pre>';
	// var_dump($usuario);
	// var_dump($optRegiao);
	// var_dump($optMeses);
	// echo '</pre>';
	
	//ARRAY DE ANOS
	for ($i = 2010; $i <= date('Y'); $i++) {
		$optAnos[$i] = $i;
	}
	
	$query = "SELECT * FROM rh_movimentos";
	$sql = mysql_query($query) or die("Erro ao selecionar Movimentos");
	$movimentos = array();
	while($linhas_mov = mysql_fetch_assoc($sql)){
		$movimentos[$linhas_mov['cod']] = $linhas_mov['descicao'];
	}
	
	$historico_movimentos = "SELECT A.*,A.projeto AS id_projeto, B.nome as nome_projeto, C.nome as nome_funcionario, DATE_FORMAT( A.criado_em, '%d/%m/%Y') AS data_criacao
	FROM header_movimentos_lote AS A
	LEFT JOIN projeto AS B ON(A.projeto = B.id_projeto)
	LEFT JOIN funcionario AS C ON(A.criado_por = C.id_funcionario)";
	$sql_historico = mysql_query($historico_movimentos) or die("Erro so selecionar histórico de movimentos em lote");
	$dados_historico = array();
	while($rows_historico = mysql_fetch_assoc($sql_historico)){
		$dados_historico[] = array(
        "id" => $rows_historico['id_header'],
        "projeto" => $rows_historico['nome_projeto'],
        "id_projeto" => $rows_historico['id_projeto'],
        "mes" => $rows_historico['mes_mov'],
        "ano" => $rows_historico['ano_mov'],
        "valor" => $rows_historico['valor_mov'],
        "por" => $rows_historico['nome_funcionario'],
        "em" => $rows_historico['data_criacao']
		);
	}
	
	if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'lancar_movimento'){
		
		$return = array("status" => 0);
		
		$data_cad = date("Y-m-d");
		$valor = str_replace(".", "", $_REQUEST['valor_mov']);
		$valor = str_replace(",", ".", $valor);
		
		//CADASTRO DO CABEÇALHO
		$query_header = "INSERT INTO header_movimentos_lote (regiao,projeto,mes_mov,ano_mov,valor_mov,criado_por) VALUES (
		'{$_REQUEST['regiao']}','{$_REQUEST['projeto']}','{$_REQUEST['mes']}','{$_REQUEST['ano']}','{$valor}','{$_COOKIE['logado']}'
		)";
		$sql_header = mysql_query($query_header) or die("Erro ao cadastrar header");
		if($sql_header){
			$id_header = mysql_insert_id();
			$dados_mov = "SELECT * FROM rh_movimentos WHERE cod = '{$_REQUEST['movimento']}'";
			$sql_dados_mov = mysql_query($dados_mov) or die("Erro ao selecionar movimentos");
			$d_mov = array();
			while($rows_mov = mysql_fetch_assoc($sql_dados_mov)){
				if($rows_mov['categoria'] == "CREDITO" && $rows_mov['incidencia'] == "FOLHA"){
					$incidencia = "5020,5021,5023";
					}else{
					$incidencia = "";
				}
				$d_mov = array(
                "id_mov" => $rows_mov['id_mov'],
                "cod" => $rows_mov['cod'],
                "nome_movimento" => $rows_mov['descicao'],
                "categoria" => $rows_mov['categoria'],
                "incidencia" => $incidencia
				);
			}
			
			$query = "";
			if(count($_REQUEST['clts']) > 0){
				$query .= "INSERT INTO rh_movimentos_clt (id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento, nome_movimento,data_movimento,user_cad,valor_movimento,lancamento,incidencia,status,status_folha,status_ferias,status_reg,id_header_lote) VALUES ";
				foreach ($_REQUEST['clts'] as $clt){
					$query .= "( '{$clt}','{$_REQUEST['regiao']}','{$_REQUEST['projeto']}','{$_REQUEST['mes']}','{$_REQUEST['ano']}','{$d_mov['id_mov']}','{$d_mov['cod']}','{$d_mov['categoria']}','{$d_mov['nome_movimento']}','{$data_cad}','{$_COOKIE['logado']}','{$valor}','1','{$d_mov['incidencia']}','1','1','1','1','{$id_header}'),";
				}
				
				$query = substr($query, 0, -1);
				$sql_query = mysql_query($query) or die("Erro ao cadastrar movimentos em lote");
			}
		}
		
		if($sql_query){
			$return = array("status" => 1);
		}
		
		echo json_encode($return);
                $log->gravaLog('Movimentos em Lote', "Movimento em Lote Cadastrado: ÌD".  mysql_insert_id());
		exit();
		
	}
	
	if(isset($_REQUEST['method']) && $_REQUEST['method'] == "desprocessarMovimento"){
		$return = array("status" => 0);
		$query = "DELETE FROM header_movimentos_lote WHERE id_header = '{$_REQUEST['header']}'";
		$query_linhas = "DELETE FROM rh_movimentos_clt WHERE id_header_lote = '{$_REQUEST['header']}'";
		$sql_desprocessa = mysql_query($query) or die("Erro ao remover header");
		$sql_desprocessa_linhas = mysql_query($query_linhas) or die("Erro ao remover linhas de movimentos");
		if($sql_desprocessa){
			$return = array("status" => 1);
		}
		
		echo json_encode($return);
                $log->gravaLog('Movimentos em Lote', "Movimento Desprocessado: ID{$_REQUEST['header']}");
		exit();
	}
	
	if (isset($_REQUEST['method']) && $_REQUEST['method'] == "visualizarParticipantes") {
		$return = array("status" => 0);
		$sql = "SELECT A.id_clt, C.nome as nome_clt, B.nome, A.nome_movimento, A.valor_movimento
        FROM `rh_movimentos_clt` AS A
        LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
        LEFT JOIN rh_clt AS C ON(A.id_clt = C.id_clt)
        WHERE id_header_lote = '{$_REQUEST['header']}'";
		$visualiza_verifica = mysql_query($sql) or die("erro ao selecionar participantes");
		$dados = array();
		if($visualiza_verifica){
			while($linha = mysql_fetch_assoc($visualiza_verifica)){
				$dados[] = array("id_clt" => $linha['id_clt'], "clt" => utf8_encode($linha['nome_clt']), "projeto" => utf8_encode($linha['nome']) ,"movimento" => utf8_encode($linha['nome_movimento']),  "valor" => $linha['valor_movimento']);
			}
			$return = array("status" => 1, "dados" => $dados);
		}
		
		echo json_encode($return);
		exit();
	}
	
	if (isset($_REQUEST['filtrar'])) {
		$cont = 0;
		$arrayStatus = array(10,20,30,40,50,51,52);
		$status = implode(",", $arrayStatus);
		
		$id_regiao = $_REQUEST['regiao'];
		$id_projeto = $_REQUEST['projeto'];
		
		$cond_funcao = (isset($_REQUEST['funcao']) && !empty($_REQUEST['funcao']) && $_REQUEST['funcao'] != '-1')?" AND E.id_curso= '{$_REQUEST['funcao']}' ":"";
		
		$projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
		$sql = "SELECT D.nome as unidade, A.nome, A.id_clt, DATE_FORMAT(A.data_entrada, '%d/%m/%Y') as dt_admissao,  E.nome as funcao, F.especifica
		FROM rh_clt as A
		LEFT JOIN projeto as D ON (D.id_projeto = A.id_projeto)
		INNER JOIN curso as E ON (E.id_curso = A.id_curso)
		LEFT JOIN rhstatus AS F ON (F.codigo = A.status)
		WHERE A.status IN($status)
		AND A.id_regiao = '$id_regiao' $cond_funcao";
		if(!isset($_REQUEST['todos_projetos'])) {
			$sql .= "AND A.id_projeto = '$id_projeto' ";
		}
		$sql .= "ORDER BY A.nome";
		//$sql .= " LIMIT 20 ";
		// echo "<!-- {$sql} -->";
		$qr_relatorio = mysql_query($sql) or die(mysql_error());
		$num_rows = mysql_num_rows($qr_relatorio);
	}
	
	$regiaoSel = (isset($_REQUEST['reg'])) ? $_REQUEST['reg'] : $usuario['id_regiao'];
	$projetoSel = (isset($_REQUEST['pro'])) ? $_REQUEST['pro'] : null;
	$funcaoSel = (isset($_REQUEST['funcao'])) ? $_REQUEST['funcao'] : null;
	$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoSel;
	$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;
	$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : null;
	$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : null;
	
	// echo '<pre>';
	// var_dump($regiaoSel);
	// var_dump($projetoSel);
	// var_dump($optRegiao);
	// echo '</pre>';
	
?>
<!doctype html>
<html lang="pt-br">
	<head>
		<meta charset="ISO-8859-1">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<title>:: Intranet :: Lançar Movimentos Em Lote</title>
		<?php require_once('inc/chamadas_topo.php'); ?>
	</head>
	<body class="novaintra">
		<?php 
			// Introduzido por Erick - 20/06/2016
			require_once('../template/navbar_default.php'); 
		?>
		
		<div class="container">
			
			<div class="row">
				<div class="col-lg-12">
					<div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - LANÇAR MOVIMENTOS EM LOTE</small></h2></div>
				</div>
			</div>
			<!--resposta de algum metodo realizado-->
			<?php echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>
			
			<div class="row">
				<div class="col-lg-12">
					<!-- Tab panes -->
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="lista">
							
							<form class="form-horizontal" role="form" id="form" method="post" autocomplete="off" name="form">
								<input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
								<input type="hidden" name="hide_funcao" id="hide_funcao" value="<?php echo $funcaoSel ?>" />
								<div class="panel panel-default hidden-print">
									<div class="panel-body">
										
										<div class="form-group">
											<label for="categoria_lista" class="col-lg-2 control-label">Região:</label>
											<div class="col-lg-9">
												<?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?>
											</div>
										</div>
										
										<div class="form-group">
											<label for="categoria_lista" class="col-lg-2 control-label">Projeto:</label>
											<div class="col-lg-9">
												<?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?>
											</div>
										</div>
										
										<div class="form-group">
											<label for="categoria_lista" class="col-lg-2 control-label">Função:</label>
											<div class="col-lg-9">
												<?php echo montaSelect(array("-1" => "« Selecione o Projeto »"), $funcaoSel, array('name' => "funcao", 'id' => 'funcao', 'class' => 'form-control')); ?>
											</div>
										</div>
										<?php if (isset($_POST['filtrar'])) { ?>
											
											<div class="form-group">
												<label for="categoria_lista" class="col-lg-2 control-label">Mês:</label>
												<div class="col-lg-9">
													<?php echo montaSelect($optMeses, $mesSel, array('name' => "mes", 'id' => 'mes', 'class' => "validate[required, custom[select]] form-control")); ?>
												</div>
											</div>
											
											<div class="form-group">
												<label for="categoria_lista" class="col-lg-2 control-label">Ano:</label>
												<div class="col-lg-9">
													<?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano', 'class' => 'form-control')); ?>
												</div>
											</div>
											
											<div class="form-group">
												<label for="nome_centrocusto" class="col-lg-2 control-label">Selecione um Movimento:</label>
												<div class="col-lg-9"><?php echo montaSelect($movimentos, $movSelected, array('name' => "movimento", 'id' => 'movimento', 'class' => 'form-control')); ?></div>
											</div>
											
											<div class="form-group">
												<label for="nome_centrocusto" class="col-lg-2 control-label">Valor do Movimento:</label>
												<div class="col-lg-9"><input type="text" name="valor_mov" id="valor_mov" value="" class="form-control validate[required]" /></div>
											</div>
										<?php } ?>
										
									</div><!-- /.panel-body -->
									
									<div class="panel-footer text-right">
										<div class="alert alert-danger" role="alert"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></div>
										<?php if (!empty($qr_relatorio) && isset($_POST['filtrar'])) { ?>
											<button type="button" name="lancar_movimento" id="lancar_movimento" class="btn btn-warning"><span class="fa fa-plus"> Lançar Movimentos</button>
											<?php } ?>
											<button type="submit" name="filtrar" id="filtrar" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
											
											
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-lg-12">
							<div class="panel panel-default progresso">
								<div class="panel-heading">Progresso...</div>
								<div class="panel-body">
									<div class="progress">
										<div id="progressBar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
											<span class="sr-only">45% Complete</span>
										</div>
									</div>
								</div>
								
							</div><!-- /.panel -->
						</div>
					</div>
					<?php if(count($dados_historico) > 0){ ?>
						<div class="row">
							<div class="col-lg-12">
								<div class="panel panel-default">
									<div class="panel-body">
										<table class="table table-striped table-hover table-condensed table-bordered" style="font-size: 14px;">
											<thead>
												<tr class="bg-primary valign-middle">
													<th class="text-center">ID</th>
													<th class="text-center">PROJETO</th>
													<th class="text-center">MÊS</th>
													<th class="text-center">ANO</th>
													<th class="text-center">VALOR</th>
													<th class="text-center">CRIADO POR</th>
													<th class="text-center">DATA DE CRIAÇÃO</th>
													<th class="text-center" colspan="2">AÇÃO</th>
												</tr>
											</thead>
											<tbody>
												
												<?php foreach ($dados_historico as $dados){ ?>
													<tr class="tr_<?php echo $dados['id']; ?> valign-middle">
														<td class="text-center"><?php echo $dados['id']; ?></td>
														<td class="text-center"><?php echo $dados['projeto']; ?></td>
														<td class="text-center"><?php echo $optMeses[$dados['mes']]; ?></td>
														<td class="text-center"><?php echo $dados['ano']; ?></td>
														<td class="text-right"><?php echo number_format($dados['valor'],2,',','.'); ?></td>
														<td class="text-center"><?php echo $dados['por']; ?></td>
														<td class="text-center"><?php echo $dados['em']; ?></td>
														<!--<td class="text-center"><a href="javascript:;" data-key='<?php echo $dados['id']; ?>' data-projeto="<?php echo $dados['id_projeto']; ?>" class="visualizar" style="text-decoration: none; color: #0000CC; font-weight: bold; cursor: pointer;"><img src="../imagens/icones/icon-view-dis.gif" title="visualizar" /></a></td>-->
														<td class="text-center"><a class="btn btn-xs btn-primary visualizar" data-key='<?php echo $dados['id']; ?>' data-projeto="<?php echo $dados['id_projeto']; ?>"><i title="Visualizar" class="bt-image fa fa-search"></i></a></td>
														<!--<td class="text-center"><a href="javascript:;" data-key='<?php echo $dados['id']; ?>' class="desprocessar" style="text-decoration: none; color: #0000CC; font-weight: bold; cursor: pointer;"><img src="../imagens/icones/icon-delete.gif" title="desprocessar" /></a></td>-->
														<td class="text-center"><a class="btn btn-xs btn-danger desprocessar" data-key='<?php echo $dados['id']; ?>' title="Visualizar"><i class="bt-image fa fa-trash-o"></i></a></td>
													</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
									
								</div>
							</div>
							
							<!--<div class="row">
								<div class="col-lg-12">
								
								</div>
							</div>-->
						</div>
						<?php
							}else{
							echo $global->getResposta('danger', 'Nenhum cadastrado encontrado');
						}
					?>
					
					<?php if (!empty($qr_relatorio) && isset($_POST['filtrar'])) { ?>
						
						
						
						
						<div class="row">
							<div class="col-lg-12">
								<div class="panel panel-default">
									<div class="panel-body">
										<table id="tbRelatorio" class="table table-striped table-hover table-condensed table-bordered">
											<thead>
												<tr class="bg-primary">
													<th colspan="5"><?php echo $projeto['nome'] ?></th>
												</tr>
												<tr class="bg-primary valign-middle" style="text-align: left">
													<th><input type="checkbox" name="todos" id="todos" /></th>
													<th>NOME</th>
													<th>FUNÇÃO</th>
													<th>STATUS</th>
													<th>DATA DE ADMISSÃO</th>
												</tr>
											</thead>
											<tbody>
												<?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"?>
													<tr class="<?php echo $class ?> pull-left" style="text-align: left">
														<td><input type="checkbox" name="clts[]" class="clts clt_<?php echo $row_rel['id_clt'] ?> validate[minCheckbox[1]]" data-prompt-position='centerRight' value="<?php echo $row_rel['id_clt'] ?>"  /></td>
														<td><?php echo $row_rel['nome'] ?></td>
														<td> <?php echo $row_rel['funcao']; ?></td>
														<td> <?php echo $row_rel['especifica']; ?></td>
														<td><?php echo $row_rel['dt_admissao']; ?></td>
													</tr>
												<?php } ?>
											</tbody>
											<tfoot>
												<tr style="text-align: right;">
													<td colspan="4">Total:</td>
													<td style="text-align: left;"><?php echo $num_rows?></td>
												</tr>
											</tfoot>
										</table>
									</div>
									
									<div class="panel-footer">
										<div class="pull-right">
											<button type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
										</div>
										<div class="clearfix"></div>
									</div>
									
								</div>
								
								<div class="panel-footer">
								</div>
							</div>
						</div>
					<?php  } ?>
					
				</form>
				
				<div id="lista_funcionarios" style='margin-top:30px !important'></div>
				
				<?php include_once '../template/footer.php'; ?>
			</div><!-- /.container -->
		</body>
	</html>				