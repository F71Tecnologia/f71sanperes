<?php
$clt = $this->funcionario;
$id_reg = $this->regiao;
$id_user = $_COOKIE['logado'];

$data = date('d/m/Y');

$result_clt = mysql_query("SELECT A.nome, A.rg, 
                            DATE_FORMAT(A.data_aviso, '%d/%m/%Y') AS data_avisoBR, 
                            DATE_FORMAT(A.data_entrada, '%d/%m/%Y') AS data_entradaBR, 
                            CONCAT(C.nome,' - ',C.razao) AS empresa, C.municipio
                            FROM rh_clt AS A
                            LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto)
                            LEFT JOIN master AS C ON (B.id_master = C.id_master)
                            where id_clt = '$clt'");
$row_clt = mysql_fetch_array($result_clt);

$meses_pt = array('Erro', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

$dia = date("d");
$mes = date("m");
$ano = date("Y");
?>

<div class="pagina">
    
    <div class="col-span-12 text-center">
        <img src="../imagens/logomaster1.gif">
        <hr style="margin-top: 1px; margin-bottom: 11px;" />
    </div>
    <br/>
    <br/>
    <div class="col-span-12 text-right"><p><?php echo $row_clt['municipio'].", ".$dia. " de ".$meses_pt[(int)$mes]." de ".$ano; ?></p></div>
    <br/>
    <div class="col-span-12 text-center">
        <h1>PEDIDO DE DEMISSÃO</h1>
    </div>
    <br/>
    <br/>
    <h3>Ao <?php echo $row_clt['empresa'] ?></h3>
    <br/>
    <p>Prezado(s) Senhor(es):</p>
    <br/>
    <div class="col-span-12 text-justify">
        <p>Por razões particulares, venho apresentar-lhes meu pedido de demissão do emprego 
            que ocupo nesta empresa desde <strong><?php echo $row_clt['data_entradaBR'] ?></strong>.</p>
        
    </div>
    <br/>
    <br/>
    <p>Informo ainda que irei cumprir o Aviso Prévio de 30 (trinta) dias a que estou sujeito por lei.</p>
    <br/>
    <br/>
    <p>Sem mais</p>
    <br/>
    <br/>    
    <br/>    
    <br/>
    <br/>
    
    <br/>    
    <br/>
    <p>_______________________________________________</p>
    <p><?php echo $row_clt['nome']; ?></p>
    
</div>
