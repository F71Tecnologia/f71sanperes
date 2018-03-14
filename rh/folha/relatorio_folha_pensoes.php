<?php

// Verificando se o usuário está logado
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../../../login.php">Logar</a>';
    exit;
}

// Incluindo Arquivos
require('../../conn.php');
include('../../funcoes.php');

// Buscando a Folha
list($regiao, $folha) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));

// Consulta da Data
$data = mysql_result(mysql_query("SELECT data_proc FROM rh_folha WHERE id_folha = '$folha'"), 0);

// Incluindo Arquivos
include('../../classes/calculos.php');
include('../../classes/abreviacao.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../classes/valor_proporcional.php');
include('../../classes/FolhaClass.php');
include('../../classes/regiao.php');

$Regi = new regiao();
$Trab = new proporcional();
$objFolha = new Folha();

$dados = array();
$qry_pensao = "SELECT *, if(valor_saida IS NULL,0,1) AS pago FROM (
	SELECT A.id_clt,C.id_saida,A.mes,A.ano,A.nome,B.cod_movimento,B.nome_movimento,D.nome AS favorecido,D.cpfcnpj,B.valor_movimento,REPLACE(C.valor,',','.') AS valor_saida
	FROM rh_folha_proc AS A
	LEFT JOIN rh_movimentos_clt AS B ON(A.id_clt = B.id_clt AND A.mes = B.mes_mov AND A.ano = B.ano_mov AND B.cod_movimento IN(6004,7009,50222,80026))
	LEFT JOIN saida AS C ON(A.mes = LPAD(C.mes_competencia, 2, '0') AND A.ano = C.ano_competencia AND REPLACE(C.valor,',','.') = B.valor_movimento AND C.tipo = 154 AND C.`status` = 2)	
	LEFT JOIN entradaesaida_nomes AS D ON(A.id_clt = D.id_clt)
        WHERE A.id_folha = '{$folha}' AND B.`status` IN(1,5)
) AS tmp";
$sql_pensao  = mysql_query($qry_pensao) or die("Erro ao verificar pensões");
while($rows = mysql_fetch_assoc($sql_pensao)){
    $dados[] = $rows;  
}

?>
 

<html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
            <title>:: Intranet :: Relatório de Pensão Alimentícia (<?= $folha ?>)</title>
            <link href="sintetica/folha.css" rel="stylesheet" type="text/css">
            <link href="../../favicon.ico" rel="shortcut icon">
            <link href="../../js/highslide.css" rel="stylesheet" type="text/css" />
            <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
            <script src="../../js/highslide-with-html.js" type="text/javascript"></script>
            <script src="../../resources/js/tooltip.js"></script>
            <script src="../../resources/js/main.js" type="text/javascript"></script>
            <script src="../../js/global.js" type="text/javascript"></script>
            <script type="text/javascript">
                hs.graphicsDir = '../../images-box/graphics/';
                hs.outlineType = 'rounded-white';

                $(function() {
                   
                });

            </script>
            <style type="text/css">
                .highslide-html-content { width:600px; padding:0px; }
                .rendimentos{
                    background-color:  #eee;	
                }
                #tabela tr{
                    font-size:10px;
                    text-align: left;
                    padding: 10px;
                    box-sizing: border-box;
                }	
                #tabela td{
                    height: 30px;
                    text-align: left;
                    padding: 10px;
                    box-sizing: border-box;
                }	
                .totalizador tr, .totalizador td {
                    border: 1px solid #ccc;
                }
                
            </style>
        </head>
        <body>
            <div id="corpo" style="padding: 10px 10px">
                
                <table cellpadding="0" cellspacing="1" id="tabela" width="100%">       
                    <?php $titulo = 1; ?>
                    <?php foreach ($dados as $dadosPensao){ ?>
                        <?php if($titulo == 1){ ?>
                            <?php $titulo++; ?>
                            <tr style="">
                                <td colspan="8" style="font-size: 16px; text-align: center; text-transform: uppercase; background: #ccc">Movimentos de Pensão Alimentícia Competência (<?php echo $dadosPensao['mes']."/".$dadosPensao['ano']; ?>)</td> 
                            </tr>
                            <tr style="">
                                <td colspan="8" style="font-size: 11px; text-align: center; text-transform: uppercase; background: #ccc; color: crimson">* Funcionário sem vinculo de favorecido, Por favor cadastre um favorecido em Editar Participante</td> 
                            </tr>
                            <tr class="secao" style="background: #ddd !important; color: #333;">
                                <td>ID</td>
                                <td>NOME</td>
                                <td>MOVIMENTO</td>
                                <td>VALOR</td>
                                <td>FAVORECIDO</td>
                                <td>CPF/CNPJ</td>
                                <td>PAGO</td>
                                <td>AÇÕES</td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td><?php echo $dadosPensao['id_clt']; ?></td>
                            <td><?php echo $dadosPensao['nome']; ?></td>
                            <td><?php echo $dadosPensao['nome_movimento']; ?></td>
                            <td><?php echo $dadosPensao['valor_movimento']; ?></td>
                            <td>
                                <?php
                                    if(isset($dadosPensao['favorecido'])){
                                        echo $dadosPensao['favorecido']; 
                                    }else{
                                        echo "<span style='color:red; '>Sem Favorecido</span>";
                                    }
                                ?>
                            </td>
                            <td>
                                <?php 
                                    if(isset($dadosPensao['cpfcnpj'])){
                                        echo $dadosPensao['cpfcnpj'];  
                                    }else{
                                        echo "<span style='color:red; text-align:center !important; '>--</span>";
                                    }
                                ?>
                            </td>
                            <td><?php echo ($dadosPensao['pago'] == 1)?"Sim":"Não"; ?></td>
                            <td>
                                <?php 
                                    if($dadosPensao['favorecido'] != null){
                                        echo ($dadosPensao['pago'] == 1)?"":"<a href='javascript:;'>Lançar</a>"; 
                                    }else{
                                        echo "s/n *";
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </table>
                <p style="text-align: right;"><input type="button" onclick="tableToExcel('tabela', 'Folha Analitica')" value="Exportar para Excel" class="exportarExcel"> <a name="pdf" data-title="Movimentos de Pensão Alimentícia Competência (<?php echo $dadosPensao['mes']."/".$dadosPensao['ano']; ?>)" data-id="tabela" id="pdf" value="Gerar PDF" style="cursor: pointer"><i class="fa fa-file-pdf-o"></i> Gerar PDF</a></p>
                <div class="clear"></div>
                <br />
            </div>
        </body>
    </html>
    