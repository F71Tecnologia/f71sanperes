<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}
// http://www.netsorrindo.com/intranet/financeiro/relfinanceiro2.php?banco=101&mes=01&ano=2014&id=4&regiao=45&logado=202&PHPSESSID=88e4a8fad0e663df1c9372f135eaf43a&roundcube_sessid=3039f7b2380c97c1502d62c83ce49339
include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include "../../funcoes.php";

$usuario = carregaUsuario();

$filtro = false;

if (isset($_POST['action']) && $_POST['action'] == 'busca') {
    $filtro = TRUE;
    $id_saida = isset($_POST['id_saida']) ? $_POST['id_saida'] : NULL;
    $grupo = isset($_POST['grupo']) ? $_POST['grupo'] : NULL;
    $subgrupo = isset($_POST['subgrupo']) ? $_POST['subgrupo'] : NULL;
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : NULL;
    $nome = isset($_POST['nome']) ? $_POST['nome'] : NULL;
    $master = isset($_POST['master']) ? $_POST['master'] : NULL;
    $regiao = isset($_POST['regiao']) ? $_POST['regiao'] : NULL;
    $projeto = isset($_POST['projeto']) ? $_POST['projeto'] : NULL;
    $mes = isset($_POST['mes']) ? $_POST['mes'] : NULL;
    $ano = isset($_POST['ano']) ? $_POST['ano'] : NULL;
    $tabela = isset($_POST['tabela']) ? $_POST['tabela'] : NULL;
    $limit = isset($_POST['limit']) ? $_POST['limit'] : '50';

    $condicao = "";
    
    if ($grupo != "") {
        $array_ids_entrada_saida = array();
        $sql_grupo = "SELECT * FROM entradaesaida WHERE grupo =" . $grupo;
        $query = mysql_query($sql_grupo);
        while ($row = mysql_fetch_array($query)) {
            $array_ids_entrada_saida[] = $row['id_entradasaida'];
        }
        if (!empty($array_ids_entrada_saida)) {
            $ids_entrada_saida = implode(', ', $array_ids_entrada_saida);
            $condicao .= " WHERE A.tipo IN ($ids_entrada_saida)";
        }
    }
    if($subgrupo!=""){
        $array_ids_entrada_saida = array();
        $sql = "SELECT * FROM entradaesaida WHERE cod LIKE '".$subgrupo."%'";
        $query = mysql_query($sql);
        while($row = mysql_fetch_array($query)){
            $array_ids_entrada_saida[] = $row['id_entradasaida'];
        }
        $ids_entrada_saida = implode(', ', $array_ids_entrada_saida);
        $condicao .= " WHERE A.tipo IN ($ids_entrada_saida)";
    }
    if($tipo!=""){
        $condicao .= " WHERE A.tipo  = '".$tipo."'";
    }
    if($mes!=""){
        $condicao .= ($condicao=="") ? ' WHERE MONTH(A.data_vencimento) = '.$mes : " AND  MONTH(A.data_vencimento) = ".$mes;
    }
    if($ano!="" && $mes!=""){
        $condicao .= ($condicao=="") ? ' WHERE YEAR(A.data_vencimento) ='.$ano." AND MONTH(A.data_vencimento) = $mes" : " AND  YEAR(A.data_vencimento) =".$ano." AND MONTH(A.data_vencimento) = $mes";
    }else{
       if($ano!="" && $mes==""){
           $condicao .= ($condicao=="") ? ' WHERE YEAR(A.data_vencimento) ='.$ano : " AND  YEAR(A.data_vencimento) =".$ano;
        } 
    }
    if($id_saida!=""){
        $condicao .=  ($condicao=="") ? ' WHERE A.id_saida= "'.$id_saida.'"' : ' AND A.id_saida= "'.$id_saida.'"';
    }
    if($nome!=""){
        $condicao .=  ($condicao=="") ? ' WHERE ( A.nome LIKE "%'.$nome.'%" OR A.especifica LIKE "%'.$nome.'%" )' : '  AND ( A.nome LIKE "%'.$nome.'%" OR A.especifica LIKE "%'.$nome.'%" ) ';
    }
    if($regiao!="" & $projeto!=""){
        $condicao .= ($condicao=="") ? ' WHERE A.id_regiao = '.$regiao.' AND A.id_projeto ='.$projeto : " AND  A.id_regiao = ".$regiao.' AND A.id_projeto='.$projeto;
    }else{
        if($regiao!="" & $projeto==""){
            $condicao .= ($condicao=="") ? ' WHERE A.id_regiao = '.$regiao : " AND  A.id_regiao = ".$regiao;
        }
    }
    if($projeto!=""){
        $condicao .= ($condicao=="") ? ' WHERE A.id_projeto = '.$projeto : " AND  A.id_projeto = ".$projeto;
    }
    
    $sql_q = "SELECT *,A.id_saida AS r_id_saida, A.nome AS r_nome, A.especifica AS r_especifica, B.nome as nome_projeto,D.nome as nome_banco, DATE_FORMAT(A.data_vencimento, '%d/%m/%Y') AS vencimento FROM saida A LEFT JOIN projeto B ON B.id_projeto = A.id_projeto
LEFT JOIN regioes C ON C.id_regiao = A.id_regiao  LEFT JOIN bancos D ON D.id_banco = A.id_banco LEFT JOIN saida_files E ON E.id_saida = A.id_saida ".$condicao.' AND  A.status = 2  GROUP BY A.id_saida  LIMIT '.$limit.' ;';
    
    echo $sql_q;
    $query = mysql_query($sql_q);    
}
?>
<html>
    <head>
        <title>:: Intranet :: Prestador de Serviço</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../../js/global.js" type="text/javascript"></script>


        <script>
            $(function() {
                $("#regiao").ajaxGetJson("../actions/action.saida.php", {action: "load_projeto", regiao: "<?= isset($regiao) ? $regiao : $usuario['id_regiao']; ?>", projeto: "<?= isset($projeto) ? $projeto :  $usuario['id_master']; ?>" }, null, "projeto");
                $("#select_grupo").ajaxGetJson("../actions/action.saida.php", {action: "load_subgrupo"}, null, "subgrupo");                
                $("#subgrupo").ajaxGetJson("../actions/action.saida.php", {action: "load_tipo"}, null, "tipo");
            });
        </script>
        <style>
            .bt-image{
                width: 18px;
                cursor: pointer;
            }
        </style>
    </head>
    <body class="novaintra">
        <div id="content">
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px; width: 120px;">
                    <div class="fleft">
                        <h2>Financeiro - BUSCAR LANÇAMENTOS</h2>
                        <p>Buscar lançamentos</p>
                    </div>
                </div>

                <fieldset>
                    <legend>Filtro</legend>
                    <p><label class="first">Código:</label> <input type="text" name="id_saida" id="hide_projeto" value="<?= isset($_REQUEST['id_saida']) ? $_REQUEST['id_saida'] : ''; ?>" /></p>
                    <p><label class="first">Nome:</label> <input type="text" name="nome" id="hide_projeto" value="<?= isset($_REQUEST['nome']) ? $_REQUEST['nome'] : ''; ?>" /></p>
                    <?php $selected_tabela = isset($_REQUEST['tabela']) ? $_REQUEST['tabela'] : ''; ?>
                    <?php
                    $grupo = montaQuery("entradaesaida_grupo", "*", "id_grupo >= 10");
                    $select_grupo[''] = 'Todos os Grupos';
                    foreach ($grupo as $valor):
                        $ids_grupo[] = $valor['id_grupo'];
                        $select_grupo[$valor['id_grupo']] = $valor['id_grupo'] . ' - ' . $valor['nome_grupo'];
                    endforeach;
                    ?>
                    <?php $selected_grupo = isset($_REQUEST['grupo']) ? $_REQUEST['grupo'] : ''; ?>
                    <p><label class="first">Grupo:</label> <?php echo montaSelect($select_grupo, $selected_grupo, " name='grupo' id='select_grupo' class='required[custom[select]]'") ?></p>
                    <?php
                    $subgrupo = array('' => 'Todos os Subgrupos');
                    ?>
                    <?php $selected_subgrupo = isset($_REQUEST['subgrupo']) ? $_REQUEST['subgrupo'] : ''; ?>
                    <p><label class="first">Subgrupo:</label> <?php echo montaSelect($subgrupo, $selected_subgrupo, "id='subgrupo' name='subgrupo' class='required[custom[select]]'") ?></p>
                    <?php
                    $array_tipos[''] = 'Todos os Tipos';
                    ?>
                    <?php $selected_tipo = isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : ''; ?>
                    <p><label class="first">Tipo:</label> <?php echo montaSelect($array_tipos, $selected_tipo, "id='tipo' name='tipo' class='required[custom[select]]'") ?></p>
                    <?php $meses = array('' => 'Todos os meses', '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'); ?>
                    <?php $selected_mes = isset($_REQUEST['mes']) ? $_REQUEST['mes'] : ''; ?>
                    <p><label class="first">Mês:</label> <?php echo montaSelect($meses, $selected_mes, "id='select_mes' name='mes' class='required[custom[select]]'") ?></p>
                    <?php $ano = array('' => 'Todos os anos', 2008 => 2008, 2009 => 2009, 2010 => 2010, 2011 => 2011, 2012 => 2012, 2013 => 2013, 2014 => 2014, 2015 => 2015, 2016 => 2016); ?>
                    <?php $selected_ano = isset($_REQUEST['ano']) ? $_REQUEST['ano'] : date('Y'); ?>
                    <p><label class="first">Ano:</label> <?php echo montaSelect($ano, $selected_ano, "id='select_ano' name='ano' class='required[custom[select]]'") ?></p>


                    <input type="hidden" name="action" value="busca" />
                    <?php $selected_regiao = ($filtro) ? $regiao : $usuario['id_regiao'] ; ?>
                    <p><label class="first">Região:</label> <?php echo montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $selected_regiao, "id='regiao' name='regiao' class='required[custom[select]]'") ?></p>
                    <?php $selected_projeto = ($filtro) ? $regiao : $usuario['id_regiao']; ?>
                    <p><label class="first">Projeto:</label> <?php echo montaSelect(array("" => "« Selecione a Região »"), $selected_projeto, "id='projeto' name='projeto' class='required[custom[select]]'") ?></p>
                    <?php $selected_limit = ($filtro) ? $limit : '50'; ?>
                    <p><label class="first">Números de registros:</label> <?php echo montaSelect(array("50" => "50", "100" => "100", "150"=> "150", "200" => "200"), $selected_limit, "id='limit' name='limit' class='required[custom[select]]'") ?></p>
                    <p class="controls"> 
                        <input type="hidden" name="tabela" value="saida" />
                        <input type="submit" class="button" />
                    </p>
                </fieldset>

                <?php
                $cont = mysql_num_rows($query);
                if($cont>0){ ?>
                        
                        <br/>
                        <p style="text-align: right;"><input type="button" onclick="tableToExcel('tabela', 'Relatório CNES')" value="Exportar para Excel" class="exportarExcel"></p>
                        
                        <table cellpadding="0" cellspacing="0" border="0" class="grid" width="100%" id="tabela_relatorio">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th>Banco</th>
                                    <th>Região</th>
                                    <th>Projeto</th>
                                    <th>Data de vencimento</th>
                                    <th>Valor</th>
                                    <th >Comprovante</th>
                                    <th >Comprovante de Pagamento</th>
                                    <th >Editar</th>
                                </tr>
                            </thead>
                            <tbody id="lista_lancamentos">                
                            <?php
                            while($row = mysql_fetch_array($query)){ ?>
                                <?php $valor = str_replace(",", ".", $row['valor']); ?>
                                <tr>
                                    <td><?= $row['r_id_saida']; ?></td>
                                    <td><?= $row['r_nome']; ?></td>
                                    <td><?= $row['r_especifica']; ?></td>
                                    <td><?= $row['id_banco'].' - '.$row['nome_banco']; ?></td>
                                    <td><?= $row['id_regiao'].' - '.$row['regiao']; ?></td>
                                    <td><?= $row['id_projeto'].' - '.$row['nome_projeto']; ?></td>
                                    <td><?= $row['vencimento']; ?></td>
                                    <td><?= number_format($valor, '2', ',', '.'); ?></td>
                                    <?php                                     
                                    $link_encryptado = encrypt('ID='.$row['id_saida'].'&tipo=0');
                                    ?>
                                    <td class="center">
                                        <?php if(!empty($row['tipo_saida_file'])){  ?>
                                        <a target="_blank" title="Comprovante" href="../view/comprovantes.php?<?= $link_encryptado; ?>"><img src="../../financeiro/imagensfinanceiro/attach-32.png"  /></a>
                                        <?php } ?>
                                    </td>
                                    <td class="center">
                                        <?php 
                                        $sql_file_pg = 'SELECT * FROM saida_files_pg WHERE id_saida = '.$row['id_saida'];
                                        $res_file_pg = mysql_query($sql_file_pg);
                                        $file_pg = mysql_fetch_assoc($res_file_pg);
                                        if(!empty($file_pg)){ 
                                            $link_encryptado_pg = encrypt('ID='.$row['id_saida'].'&tipo=1');                                    
                                        ?>
                                        <a target="_blank" title="Comprovante de pagamento" href="../view/comprovantes.php?<?= $link_encryptado_pg; ?>"><img src="../../financeiro/imagensfinanceiro/attach-32.png"  /></a>                                        
                                        <?php } ?>
                                    </td>
                                    <td class="center"><a href="http://www.netsorrindo.com/intranet/novoFinanceiro/cad_edit_saida.php?regiao=45&id=<?= $row['r_id_saida']; ?>&popup=1" title="Editar" ><img src="/intranet/imagens/icones/icon-edit-dis.gif" alt="Editar " /></a></td>
                                </tr>
                           <?php } ?>
                            </tbody>
                        </table>
                    
              <?php  }else{ ?>
                    <br/>
                    <div id='message-box' class='message-yellow'>
                        <p>Nenhum registro encontrado</p>
                    </div>
                <?php }?>
            </form>

        </div>
    </body>
</html>