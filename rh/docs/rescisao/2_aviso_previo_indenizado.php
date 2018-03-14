<?php
$clt = $this->funcionario;
$id_reg = $this->regiao;
$id_user = $_COOKIE['logado'];

$data = date('d/m/Y');

$result_clt = mysql_query("SELECT A.nome, DATE_FORMAT(A.data_aviso, '%d/%m/%Y') AS data_avisoBR, CONCAT(C.nome,' - ',C.razao) AS empresa, C.municipio
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
        <h1>AVISO PRÉVIO INDENIZADO</h1>
    </div>
    <br/>
    <br/>
    <h3>Sr.(a) <strong><?php echo $row_clt['nome'] ?></strong></h3>
    <br/>
    <div class="col-span-12 text-justify">
        <p>Vimos pela presente notificá-lo(a) que, de acordo com o artigo 487 da CLT, seu contrato de trabalho 
            será rescindido a partir de <strong><?php echo $row_clt['data_avisoBR'] ?></strong> e que o aviso 
            prévio de acordo com a legislação vigente será indenizado em rescisão.</p>
    </div>
    <br/>
    <div class="col-span-12 text-center">
        <p>Pedimos a devolução do presente com o seu CIENTE abaixo: </p>
    </div>
    <br/>
    <br/>
     
    <br/>    
    
    <p>Atenciosamente,</p>
    <br/>    
    <br/>    
    <br/>
    <br/>
    <p>_______________________________________________</p>
    <p><?php echo $row_clt['empresa']; ?></p>
        
    <br/>    
    <br/>
    <p>_______________________________________________</p>
    <p><?php echo $row_clt['nome']; ?></p>
    
</div>
