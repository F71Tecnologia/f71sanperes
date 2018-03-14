<?php
//
$clt = $this->funcionario;
$id_reg = $this->regiao;
$id_user = $_COOKIE['logado'];

$data = date('d/m/Y');

$result_clt = mysql_query("SELECT A.nome, CONCAT(C.nome,' - ',C.razao) AS empresa, C.municipio
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
        <h1>AVISO PRÉVIO TRABALHADO</h1>
    </div>
    <br/>
    <br/>
    <h3>Sr.(a) <strong><?php echo $row_clt['nome'] ?></strong></h3>
    <br/>
    <div class="col-span-12 text-justify">
        <p>Pelo presente o (a) notificamos que após <strong>30 (TRINTA)</strong> dias da entrega deste comunicado, não mais serão utilizados 
            os seus serviços pelo <?php echo $row_clt['empresa'] ?>, e, por isso,
            vimos avisá-lo nos termos e para os efeitos do disposto no art. 487, inciso II da CLT.</p>
    </div>
    <br/>
    <div class="col-span-12 text-center">
        <p>Pedimos a devolução do presente com o seu CIENTE abaixo: </p>
    </div>
    <br/>
    <br/>
    <p><strong>Opção:</strong></p>
    <p style="padding-left: 45px">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) Redução 07 (sete) dias.</p>
    <p style="padding-left: 45px">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) Redução 02 (duas) horas diárias.</p>
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
