<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../../login.php'>Logar</a> ";
    exit;
}

include "../../conn.php";
include "../../wfunction.php";

$user = $_SESSION['id_user'];
$regiao = $_SESSION['id_regiao'];
$master = $_SESSION['id_master'];
$lista = false;

if(isset($_REQUEST['gerar']) && !empty($_REQUEST['gerar'])){
    if(count($_REQUEST['clt'])==0){
        echo "Volte e selecione algum CLT.<br><a href='importacao_usu.php'>voltar</a>";
        exit;
    }
    $projeto = $_REQUEST['projeto'];
    
    $rsEmpresa = montaQuery("rhempresa","*","id_projeto={$projeto} AND id_regiao = {$regiao}");
    if(count($rsEmpresa) == 0) {
        echo "O projeto selecionado não está vinculado a uma empresa";
        exit;
    }else{
        $empresa = current($rsEmpresa);
    }
    
    
    $clts = implode(",",$_REQUEST['clt']);
    
    $rsClt = montaQuery("rh_clt","*","id_projeto={$projeto} AND id_clt IN ({$clts})","nome"); //AND transporte=1
    $total = count($rsClt);
    $toArquivos = 1;
    if($total > 200){ //LIMITE DE REGISTROS POR ARQUIVO
        $toArquivos = ceil($total / 200);
    }
    
    $folder = dirname(__FILE__) . "/arquivos_import/";    
    if(!is_dir($folder)) mkdir ($folder, 0777);
    
    for($i=1; $i <= $toArquivos; $i++){
        $if = str_pad($i, 5, "0", STR_PAD_LEFT);
        
        $fname = "{$projeto}_{$if}_CADUSU_" . date("Ymd") . ".TXT";
        $filename = $folder . $fname;

        $handle = fopen($filename, "w+");
        /* ESCREVENDO NO ARQUIVO */
        /* HEADER */
        
        $cnpj = str_pad(str_replace(array("-",".","/"),"",$empresa['cnpj']), 14, "0", STR_PAD_LEFT);
        fwrite($handle, "0000101CADUSU04.02{$cnpj}\r\n");
        $numRegistro = (int)+$_REQUEST['matricula'];
        foreach($rsClt as $k => $clt){
            //NUMERO CADA REGISTRO 5
            $num = str_pad($k+1, 5, "0", STR_PAD_LEFT);
            
            //ID DO CLT 15
            $matricula = str_pad($numRegistro, 15, " ", STR_PAD_RIGHT);
            
            //NOME 40 CARACTERS
            if(strlen($clt['nome']) > 40)
                $nome = substr (trim($clt['nome']), 0, 40);
            else
                $nome = str_pad(trim($clt['nome']), 40, " ", STR_PAD_RIGHT);
            //CPF 11
            $cpf = str_pad(str_replace(array(".","-"),"",trim($clt['cpf'])), 11, "0", STR_PAD_LEFT);
            
            /*VALOR DA PASSAGEM*/
            if($_REQUEST['valorpad']==0){
                $result_vale = mysql_query("SELECT id_tarifa1,id_tarifa2,id_tarifa3,id_tarifa4 FROM rh_vale WHERE id_clt = '{$clt['id_clt']}'");
                if(mysql_num_rows($result_vale) > 0){
                    $row_vale = mysql_fetch_assoc($result_vale);

                    $valor1 = 0;
                    $valor2 = 0;
                    $valor3 = 0;
                    $valor4 = 0;

                    if($row_vale['id_tarifa1'])
                        $valor1 = current(montaQuery("rh_tarifas","CAST(REPLACE(valor, ',', '.') AS DECIMAL(10,2)) as valor","id_tarifas = {$row_vale['id_tarifa1']}"));

                    if($row_vale['id_tarifa2'])
                        $valor2 = current(montaQuery("rh_tarifas","CAST(REPLACE(valor, ',', '.') AS DECIMAL(10,2)) as valor","id_tarifas = {$row_vale['id_tarifa2']}"));

                    if($row_vale['id_tarifa3'])
                        $valor3 = current(montaQuery("rh_tarifas","CAST(REPLACE(valor, ',', '.') AS DECIMAL(10,2)) as valor","id_tarifas = {$row_vale['id_tarifa3']}"));

                    if($row_vale['id_tarifa4'])
                        $valor4 = current(montaQuery("rh_tarifas","CAST(REPLACE(valor, ',', '.') AS DECIMAL(10,2)) as valor","id_tarifas = {$row_vale['id_tarifa4']}"));

                }

                $passagemTotal = ($valor1['valor'] * 2) + ($valor2['valor'] * 2) + ($valor3['valor'] * 2) + ($valor4['valor'] * 2);
                $valDiario = str_pad(number_format($passagemTotal,2,"",""), 6, "0", STR_PAD_LEFT);
            }else{
                $valDiario = str_pad(str_replace(array(".",","), "", $_REQUEST['valor']), 6 , "0", STR_PAD_LEFT);
            }
            
            $tipoRegistro = $_REQUEST['tpregistro']; //02=NOVO CADASTRO, 03=EDIÇÃO
            $tipoCartao = $_REQUEST['tpcartao']; //04=BILHETEUNICO
            $redeRecarga = $_REQUEST['rederecarga']; //01=ONIBUS
            
            //$cidades = "02"; //RIO DE JANEIRO
            
            $cidades = array(
                "3302"=>"74",
                "3303"=>"74",
                "3304"=>"74",
                "3316"=>"02",
                "3317"=>"02",
                "3318"=>"02",
                "3315"=>"02",
                "3320"=>"06"
                );
            
            $cartao = str_pad(" ", 13, " ");
            $nasci = date("dmY", strtotime($clt['data_nasci']));
            $rg = str_pad(str_replace(array(".","-","/"),"",trim($clt['rg'])), 15, "0", STR_PAD_LEFT);
            $org = trim($clt['orgao']);
            if(strlen($org)>6) $org = substr($org,0,6);
            $orgao = str_pad(str_replace(array(".","-","/"),"",$org), 6, " ", STR_PAD_RIGHT);
            $tel = "0000000000000";
            $email = str_pad(" ",60," ");
            
            fwrite($handle, "{$num}{$tipoRegistro}{$matricula}{$nome}{$cpf}{$valDiario}{$cidades[$clt['id_projeto']]}{$redeRecarga}{$cartao}{$tipoCartao}{$nasci}{$clt['sexo']}{$rg}{$orgao}{$tel}{$email}\r\n");
            $numRegistro ++;
        }
        $numa = str_pad($k+2, 5, "0", STR_PAD_LEFT);
        fwrite($handle, "{$numa}99");
        fclose($handle);
    }
    
    
    if($toArquivos==1){
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload");
        header("Content-Length: " . filesize($filename));
        header("Content-Disposition: attachment; filename={$fname}");
        flush();

        readfile($filename);
    }else{
        //FOI GERADO VÁRIOS ARQUIVOS, MOSTRAR CADA UM PARA DOWNLOAD
        echo "Clique no arquivo para baixar.<br/>";
        for($i=1; $i <= $toArquivos; $i++){
            
        }
        echo "";
    }
    
    exit;
}


/*FILTRANDO OS USUÁRIOS DO PROJETO SELECIONADO*/
if(validate($_REQUEST['filtrar'])){
    $projeto = $_REQUEST['projeto'];
    $qrClt = "SELECT A.id_clt,A.nome,DATE_FORMAT(A.data_nasci,'%d/%m/%Y') AS data_nascibr,A.rg,A.orgao,A.cpf,B.nome AS funcao,IF(A.transporte=1,'Sim','Não') AS transporte  FROM rh_clt AS A 
                LEFT JOIN curso AS B ON (A.id_curso=B.id_curso)
                WHERE A.id_projeto = {$_REQUEST['projeto']} AND A.`status` = 10
                ORDER BY A.nome";
    $rsClt = mysql_query($qrClt);
    if(mysql_num_rows($rsClt) > 0)
        $lista=true;
    $cont=0;
}


$rsProjetos = montaQuery("projeto","id_projeto,nome","id_regiao={$regiao}","nome");
$projetos = array("-1"=>"« Selecione »");
foreach ($rsProjetos as $pro) {
    $projetos[$pro["id_projeto"]] = $pro["id_projeto"] . " - " . $pro["nome"];
}

$tpRegistro = array("02"=>"Inclusão de Usuário","03"=>"Alteração de Usuário");
$tpCartao = array(
    "01"=>"VT Rio Card ao Portador",
    "02"=>"VT Rio Card do Comprador",
    "03"=>"VT Rio Card do Comprador/Usuário",
    "04"=>"VT Rio Card Individual",
    "05"=>"Bilhete Único Inter. do Usuário",
    "06"=>"Bilhete Único Carioca do Usuário",
    "05"=>"VT Rio Card com Migração de Impressão/Personalizado"
        );
$redeRecarta = array(
    "01"=>"Ônibus",
    "02"=>"Metrô"
    );
?>

<html>
    <head>
        <title>:: Intranet :: RH - Arquivo para importação de Usuários Fetranspor</title>
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
        <script src="../../js/jquery.maskMoney.js" type="text/javascript"></script>

        <script src="../../js/global.js" type="text/javascript"></script>

        <script>
            $(function(){
                $("#form1").validationEngine();
                // Configuração para campos de Real.
                $("#valor").maskMoney({showSymbol:true, symbol:"R$ ", decimal:",", thousands:"."});

                
                $("#checkAll").click(function(){
                    if($("#checkAll:checked")){
                        $(".ck").attr("checked","checked");
                    }else{
                        console.log('remove');
                        $(".ck").removeAttr("checked");
                    }
                });
                
                $("#valorSim").click(function(){
                    $("#pvalor").removeClass("hidden");
                });
                $("#valorNao").click(function(){
                    $("#pvalor").addClass("hidden");
                });
            });
        </script>

    </head>
    <body id="importVT" class="novaintra">
        <div id="content" style="width: auto!important;">
            <form action="" method="post" name="form1" id="form1">
                <p><a href="javascript:history.go(-1);">« voltar</a></p>
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $master; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>RH - Arquivo para importação de Usuários Fetranspor</h2>
                    </div>
                    <div class="fright"> <?php include('../../reportar_erro.php'); ?></div> 
                </div>
                <br/>
                <fieldset>
                    <legend>Selecione</legend>
                    <p><label class="first">Projeto:</label> <?php echo montaSelect($projetos, $projeto, "id='projeto' name='projeto' class='validate[required,custom[select]]'") ?></p>
                    <?php if($lista){ ?>
                    <p><label class="first">Tipo Registro:</label> <?php echo montaSelect($tpRegistro, null, "id='tpregistro' name='tpregistro'") ?></p>
                    <p><label class="first">Tipo Cartão:</label> <?php echo montaSelect($tpCartao, null, "id='tpcartao' name='tpcartao'") ?></p>
                    <p><label class="first">Rede Recarga:</label> <?php echo montaSelect($redeRecarta, null, "id='rederecarga' name='rederecarga'") ?></p>
                    <p><label class="first">Matricula Inicio:</label> <input type="text" name="matricula" id="matricula" value="1" size="3" /> </p>
                    <p><label class="first">Valor Padrão?</label> <input type="radio" name="valorpad" id="valorSim" value="1"><label for="valorSim">SIM</label> <input type="radio" name="valorpad" id="valorNao" value="0"><label for="valorNao">NÃO</label></p>
                    <p id="pvalor" class="hidden"><label class="first">Valor:</label> <input type="text" name="valor" id="valor" value="" size="6" /> (valor diário) </p>
                    <?php } ?>
                    <p class="controls">
                        <input type="submit" class="button" value="Filtrar" name="filtrar" id="filtrar" />
                        <?php if($lista){ ?>
                        <input type="submit" class="button" value="Gerar Arquivo" name="gerar" id="gerar" />
                        <?php } ?>
                    </p>
                </fieldset>
                
                <?php if($lista){ ?>
                <br/><br/>
                <table class="grid" cellpadding="0" cellspacing="0" border="0" align="center">
                    <thead>
                        <tr>
                            <th><!--<input type="checkbox" name="checkAll" id="checkAll" value="0" />-->#</th>
                            <th>Código</th>
                            <th>Nome</th>
                            <th>Função</th>
                            <th>CPF</th>
                            <th>RG</th>
                            <th>Data Nascimento</th>
                            <th>Vale Transporte?</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysql_fetch_assoc($rsClt)){ ?>
                        <tr class="<?php if($cont++ % 2 == 0){echo 'even';}else{echo 'odd';}?>">
                            <td><input type="checkbox" name="clt[]" id="clt_<?php echo $row['id_clt']?>" value="<?php echo $row['id_clt']?>" class="ck" /></td>
                            <td><?php echo $row['id_clt']?></td>
                            <td><?php echo $row['nome']?></td>
                            <td><?php echo $row['funcao']?></td>
                            <td><?php echo $row['cpf']?></td>
                            <td><?php echo $row['rg']?></td>
                            <td><?php echo $row['data_nascibr']?></td>
                            <td><?php echo $row['transporte']?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } ?>
            </form>
        </div>
    </body>
</html>