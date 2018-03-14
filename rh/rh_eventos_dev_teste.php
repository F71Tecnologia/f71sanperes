<?php
if(empty($_COOKIE['logado'])){
	print 'Efetue o Login<br><a href="login.php">Logar</a>';
	exit;
}

include('../conn.php');
include('../funcoes.php');
include('../classes/abreviacao.php');

list($regiao,$evento) = explode('&',decrypt(str_replace('--','+',$_REQUEST['enc'])));
?>
<html>
<head>
<title>:: Intranet :: Eventos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="../favicon.ico">
<link href="folha/sintetica/folha.css" rel="stylesheet" type="text/css">
<link href="../js/highslide.css" rel="stylesheet" type="text/css" />
<script src="../js/highslide-with-html.js" type="text/javascript"></script>
<script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
<script type="text/javascript">
	hs.graphicsDir = '../images-box/graphics/'; 
	hs.outlineType = 'rounded-white';
	$().ready(function(){
		$('.botao').click(function() {
			$('.status').hide();
			var div = $(this).attr('href');
			$(div).slideDown();
			$(this).addClass('ativo');
			$('.botao').not(this).removeClass('ativo');
		});
	});
</script>
<style type="text/css">
.highslide-html-content { 
	width:100%; padding:0px;
}
.botao,.ativo {
	width:162px; height:32px; *height:35px; display:block; background:url(../imagens/fundo_botao_evento.jpg) no-repeat; text-align:center; padding:4px; text-decoration:none; color:#666; font-weight:bold; float:left; margin:0px 12px 12px 0px; font-size:12px;
}
.ativo {
	-moz-border-radius:5px; -webkit-border-radius:5px; background:none #FC9 !important; color:#222;
}
.botao:hover {
	background:url(../imagens/fundo_botao_evento_hover.jpg) no-repeat; color:#222;
}
tr.novo_tr {
	background-color:#dee3ed; color:#000; padding:8px; text-align:left; font-weight:bold; font-size:13px; border-top:1px solid #777;
}
tr.novo_tr td {
	font-weight:bold; border-top:1px solid #777;
}
td.show {
	font-weight:bold;  font-size:18px; font-family:'Trebuchet MS', Arial, Helvetica, sans-serif; background-color:#f5f5f5; border:1px solid #ddd;  margin-top:5px;
}
td.show .seta {
	 color:#F90; font-size:32px;
}
.status {
	display:none;
}
iframe {
    width: 700px;
}
</style>
</head>
<body>
<div id="corpo">

<span style="float:right;position:relative;"><?php include('../reportar_erro.php'); ?></span>
<span style="clear:right"></span>
<table cellspacing="4" cellpadding="0" id="topo">

  <tr>
    <td width="15%" valign="middle" style="font-size:11px; text-align:center;">
      <?php include "../empresa.php";
             $img= new empresa();
             $img -> imagemCNPJ(); ?><!--<img src="../imagens/logomaster1.gif" width="110" height="79">--><br>
      <h3 style="margin:0 0 10px 0;">Eventos em <?php echo mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao'"),0); ?></h3>
      <a href="../principalrh.php?regiao=<?=$regiao?>" class="voltar" style="float:none; width:40px; margin:10px auto; font-size:11px;">Voltar</a>
    </td>
    <td valign="middle" style="font-size:12px;">
        <?php $qr_status = mysql_query("SELECT * FROM rhstatus WHERE status_reg = '1' AND codigo NOT IN(90) ORDER BY especifica ASC");
              while($row_status = mysql_fetch_assoc($qr_status)) {
                  $id++;
                  $total = mysql_num_rows(mysql_query("SELECT * FROM rh_clt a LEFT JOIN projeto b ON a.id_projeto = b.id_projeto WHERE a.id_regiao = '$regiao' AND b.status_reg = '1' AND a.status = '$row_status[codigo]'"));
				  if(($evento == true and $row_status['codigo'] == $evento) or ($evento == false and $row_status['codigo'] == 10)) { 
				  	  $ativo = ' ativo';
				  } else {
					  $ativo = NULL;
				  }
                  echo '<a href="#'.$id.'" class="botao'.$ativo.'" title="Visualizar Participantes em '.$row_status['especifica'].'" onclick="return false">'.abreviacao($row_status['especifica'],5).' ('.$total.')</a>';
              } unset($id); ?>
    </td>
  </tr>
</table>

<?php $qr_status = mysql_query("SELECT * FROM rhstatus WHERE status_reg = '1' ORDER BY especifica ASC");
      while($row_status = mysql_fetch_assoc($qr_status)) {
          $id++; ?>
          
          <div class="status" id="<?=$id?>" <?php if(($evento == true and $row_status['codigo'] == $evento) or ($evento == false and $row_status['codigo'] == 10)) { echo 'style="display:block;"'; } ?>>
              <table cellpadding="0" cellspacing="1" id="folha">
          
       <?php $qr_participantes       = mysql_query("SELECT b.id_projeto, b.nome AS nome_projeto, a.id_curso, a.id_clt, a.status, a.nome
	   												  FROM rh_clt a
											     LEFT JOIN projeto b
												        ON a.id_projeto = b.id_projeto
												     WHERE a.id_regiao = '$regiao'
													   AND b.status_reg = '1'
													   AND a.status = '$row_status[codigo]'
												  ORDER BY b.id_projeto, a.nome ASC");
	   		 $total_participantes    = mysql_num_rows($qr_participantes);
			 while($row_participante = mysql_fetch_assoc($qr_participantes)) {
					
					if($ultimo_projeto != $row_participante['id_projeto']) { ?>

						  <tr>
							<td colspan="4" class="show">
							  &nbsp;<span class="seta">&#8250;</span> <?php echo $row_participante['nome_projeto']; ?> 
							</td>
						  </tr>
						  <tr class="novo_tr">
							<td width="5%" align="center">COD</td>
							<td width="35%">&nbsp;&nbsp;NOME</td>
							<td width="30%">&nbsp;&nbsp;CARGO</td>
							<td width="30%" align="center">DURA&Ccedil;&Atilde;O</td>
						  </tr>
						
				 <?php } $qr_curso  = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_participante[id_curso]'");
						 $row_curso = mysql_fetch_array($qr_curso);
						
						 $qr_evento  = mysql_query("SELECT date_format(data, '%d/%m/%Y') AS data, date_format(data_retorno, '%d/%m/%Y') AS data_retorno FROM rh_eventos WHERE id_clt = '$row_participante[id_clt]' AND cod_status = '$row_participante[status]' ORDER BY id_evento DESC");
						 $row_evento = mysql_fetch_array($qr_evento); ?>
							
                        <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
                            <td><?=$row_participante['id_clt']?></td>
                            <td align="left">
                                <a href="acao_evento_dev_testes.php?clt=<?=$row_participante['id_clt']?>&regiao=<?=$regiao?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )" class="participante" title="Inserir evento para <?=$row_participante['nome']?>">
                                <?=abreviacao($row_participante['nome'], 4, 1)?> <img src="folha/sintetica/seta_<?php if($seta++%2==0) { echo 'um'; } else { echo 'dois'; } ?>.gif">
                              </a>
                            </td>
                            <td align="left">&nbsp;&nbsp;<?=$row_curso['nome']?></td>
                            <td><?php if($row_participante['status'] != 10) {  
                                          echo $row_evento['data'].' - '.$row_evento['data_retorno'];
                                      } else {
                                          echo '-';
                                      } ?></td>
                        </tr>
                        
				 <?php $ultimo_projeto = $row_participante['id_projeto'];
			 
			 } if(empty($total_participantes)) { ?>
                        
                    <tr>
                     <td colspan="4">
                      <h3>Nenhum participante se encontra neste evento</h3>
                     </td>
                    </tr>
                        
         <?php } unset($ultimo_projeto);
		 		if($total_participantes > 10) { ?>
           	   <tr>
                  <td colspan="4"><a href="#corpo" class="ancora">Subir ao topo</a></td>
               </tr>
         <?php } ?>
           </table>	
           </div>
         
<?php } ?>
</div>
</body>
</html>