<?php
#error_reporting(E_ALL);
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../wfunction.php');
include('PrestacaoContas.class.php');


//ARRAY DE FUNCIONARIOS PARA VISUALIZAR APENAS SERVIÇOS DE TERCEIROS
//178 => MILTON
$funcionario_contabilidade = array(178);

$usuario = carregaUsuario();
$objPrestacao = new PrestacaoContas();

if (isset($_POST['box']) && !empty($_POST['box']) && isset($_POST['desprocessar'])) {
    foreach ($_POST['box'] as $value) {
        $select = mysql_fetch_assoc(mysql_query("SELECT * FROM prestacoes_contas WHERE id_prestacao = $value LIMIT 1"));
        
        $insert = "INSERT INTO prestacoes_contas_desprocessada VALUES ('', NOW(), '$select[tipo]', '$select[data_referencia]', $select[valor_total], '$select[gerado_em]', $select[gerado_por], $select[id_projeto], $usuario[id_funcionario]);";
        mysql_query($insert);
        $delete = "DELETE FROM prestacoes_contas WHERE id_prestacao = $value LIMIT 1;";
        mysql_query($delete);
    }
    exit;
}

//----- CARREGA PROJETOS COM PRESTAÇÕES FINALIZADAS NO MES SELECIONADO
if (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) {
    /*-- AND A.id_regiao = '{$_REQUEST['regiao']}' 
        -- AND A.id_projeto = '{$_REQUEST['projeto']}' */
    
    $qr_proFinalizado = "
        SELECT 
            A.id_prestacao,A.id_projeto,DATE_FORMAT(A.data_referencia, '%d/%m/%Y') data_referencia,A.tipo,A.valor_total,DATE_FORMAT(A.gerado_em, '%d/%m/%Y %T') gerado_em,
            B.nome nomeProjeto,C.nome nomeFuncionario
        FROM prestacoes_contas AS A
        LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto),
        funcionario AS C
        WHERE   
            A.tipo = '{$_REQUEST['tipo']}' 
            AND Year(data_referencia) = '{$_REQUEST['ano']}' 
            AND Month(data_referencia) = '{$_REQUEST['mes']}'
            AND A.gerado_por = C.id_funcionario
        ORDER BY B.nome ASC";
//            print_r($qr_proFinalizado); exit;
    $qr_proFinalizado = mysql_query($qr_proFinalizado);
    $num_rows = mysql_num_rows($qr_proFinalizado);
    if ($num_rows > 0) {
        while ($row = mysql_fetch_assoc($qr_proFinalizado)) {
            
            $tipo = $objPrestacao->getTiposPrestacoes($row['tipo']);
            
            $tabela .= '
            <tr class="">
                <td style="text-align: center;"><input name="box[]" id="check" class="box" type="checkbox" value="'.$row['id_prestacao'].'"></td>
                <td>'.$row['nomeProjeto'].'</td>
                <td>'.$tipo.'</td>
                <td>'.substr($row['data_referencia'],3).'</td>
                <td>'.number_format($row['valor_total'],2,',','.').'</td>
                <td>'.$row['gerado_em'].'</td>
                <td>'.$row['nomeFuncionario'].'</td>
            </tr>';
        }
    }
}

$meses = mesesArray(null,'');
$anos = anosArray(null, null, array("" => "« Selecione »"));
$tipos = array_merge(array(''=>"« Selecione o Tipo »"), $objPrestacao->getTiposPrestacoes());

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : null;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$tipoR = (isset($_REQUEST['tipo'])) ? $_REQUEST['tipo'] : null;

?>
<html>
    <head>
        <title>:: Intranet :: DESPROCESSAR PRESTAÇÃO DE CONTAS</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>

        <script src="../js/global.js" type="text/javascript"></script>

        <style>
            @media print
            {
                fieldset{display: none;}
                .h2page{display: none;}
                .grAdm{display: none;}
            }
            @media screen
            {
                #headerPrint{display: none;}
            }
            body.novaintra table.grid tbody tr:nth-child(even) td{background: #ECECEC;}
            body.novaintra table.grid tbody tr:nth-child(odd) td{background: #FFFFFF;}
        </style>
        <script>
            $(function(){
                $('#filtrar').click(function(){
                    $('.box').removeClass('validate[minCheckbox[1]]');
                    $(".ui-dialog").remove();
                    $("#form1").validationEngine('attach',{
                        onValidationComplete: function(form, status){
                            return status;
                        }  
                    });
                });
                
                $('#desprocessar').click(function(){
                    $('.box').addClass('validate[minCheckbox[1]]');
                    $("#form1").validationEngine('attach',{
                        onValidationComplete: function(form, status){
                            if(status == true){
                                //if(confirm('Deseja realmente desprocessar as prestações selecionadas?') == false)return false;
                                thickBoxConfirm('Desprocessar','Deseja realmente desprocessar a prestação selecionada?','auto','auto',function(data){
                                    if(data == true){
                                        var ar = new Array();
                                        $(".box:checked").each(function(i) {
                                            ar.push($(this).val());
                                        });
                                        $.post("desprocessar_prestacao.php", {bugger:Math.random(), desprocessar:'desprocessar', box:ar}, function(resultado){
                                            //$('#teste').html(resultado);
                                            alert("Prestações desprocessadas!");
                                            window.location.reload();
                                        });
                                    }
                                });
                            }
                            return false;
                        }  
                    });
                });
            });
        </script>
    </head>
    <body id="page-despesas" class="novaintra">
        <div id="content">
            <form action="" method="post" name="form1" id="form1">
                <h2>DESPROCESSAR PRESTAÇÃO DE CONTAS</h2>
                <fieldset>
                    <legend>Dados</legend>
                    <p id="mensal" >
                        <label class="first">Mês:</label> 
                        <?php echo montaSelect($meses, $mesR, "id='mes' name='mes' class='validate[required]'") ?>  <?php echo montaSelect($anos, $anoR, "id='ano' name='ano' class='validate[required]'") ?> (mês da prestação finalizada) 
                    </p>
                    <p>
                        <label class="first">Tipo:</label> 
                        <?php if(in_array($_COOKIE['logado'], $funcionario_contabilidade)){ ?>
                            <select id='tipo' name='tipo' class='validate[required]'>
                                <option value="terceiro">Contrato de Terceiros</option>
                            </select>
                        <?php }else{ ?>
                            <?php echo montaSelect($tipos, $tipoR, "id='tipo' name='tipo' class='validate[required]'") ?>
                        <?php } ?>
                    </p>
                    <p class="controls">
                        <input type="submit" id="filtrar" class="button" style="cursor: pointer;" value="Filtrar" name="filtrar" />
                    </p>
                </fieldset>
            <?php if($num_rows > 0){ ?><br><br>
                <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
                <thead>
                    <tr>
                        <th></th>
                        <th>Projeto</th>
                        <th>Tipo</th>
                        <th>Data Referência</th>
                        <th>Valor</th>
                        <th>Gerado Em</th>
                        <th>Gerado Por</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $tabela; ?>
                </tbody>
                </table><br>
                <p class="controls">
                    <input type="submit" id="desprocessar" class="button" style="cursor: pointer;" value="DESPROCESSAR SELECIONADOS" style="float: right;">
                </p>
                <div style="clear: both;"></div>
            </form>
            <?php }elseif(isset($_REQUEST['filtrar'])){ echo '<br><p class="" style="padding: 10px; background-color: #fcf8e3;">Não existe prestação de contas finalizada para o filtro selecionado!</p>';} ?>
        </div>
        <div id="teste"></div>
    </body>
</html>