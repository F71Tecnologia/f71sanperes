<?php
$clt = $this->funcionario;
$id_reg = $this->regiao;
$id_user = $_COOKIE['logado'];



$data = date('d/M/Y');

$qry_consulta = mysql_query("SELECT * FROM rh_doc_status WHERE id_clt='$clt' AND tipo='35'");
$cont_consulta = mysql_num_rows($qry_consulta);

if ($cont_consulta == 0 )
{
$insert_doc = mysql_query("INSERT INTO rh_doc_status (tipo, id_clt, data, id_user) VALUES ('35', '$clt', NOW(), '$_COOKIE[logado]')");
	
}

$result_clt = mysql_query("
SELECT A.*,date_format(A.data_entrada, '%d/%m/%Y')as data_entrada,B.nome AS nome_escolaridade, date_format(date_add(A.data_entrada, interval 2 year), '%d/%m/%Y') AS dt_entradaY
FROM rh_clt AS A
LEFT JOIN escolaridade as B on (A.escolaridade = B.id)
where id_clt = '$clt'");
$row_clt = mysql_fetch_array($result_clt);

$result_horarios = mysql_query("SELECT nome, horas_semanais FROM rh_horarios where id_horario = '{$row_clt['rh_horario']}' LIMIT 1");
$row_horarios = mysql_fetch_array($result_horarios);

if($row_clt['prazoexp'] == 1){
    $prazoExp = '30';
    $prazoPro = 60;
    $prazoExpExt = 'trinta';
    $prazoProrrogado = 'um preriodo de 60 (sessenta) dias';
}else if($row_clt['prazoexp'] == 2 OR $row_clt['prazoexp'] == ''){
    $prazoExp = '45';
    $prazoPro = 45;
    $prazoExpExt = 'quarenta e cinco';
    $prazoProrrogado = 'igual per�odo';
}else if($row_clt['prazoexp'] == 3){
    $prazoExp = '60';
    $prazoPro = 30;
    $prazoExpExt = 'sessenta';
    $prazoProrrogado = 'um preriodo de 30 (trinta) dias';
}

//PEGA O CURSO DO CONTRATADO
$sql_transf = mysql_fetch_assoc(mysql_query("SELECT id_curso_de FROM rh_transferencias WHERE id_clt = $row_clt[id_clt] ORDER BY data_proc ASC LIMIT 1"));
if(!empty($sql_transf['id_curso_de'])){
    $idCurso = $sql_transf['id_curso_de'];
}else{
    $idCurso = $row_clt['id_curso'];
}
//$idCurso = $row_clt['id_curso'];

$result_curso = mysql_query("Select * from  curso where id_curso = '$idCurso'");
$row_curso = mysql_fetch_array($result_curso);

$result_reg = mysql_query("Select * from  regioes where id_regiao = '$id_reg'");
$row_reg = mysql_fetch_array($result_reg);

$result_proj = mysql_query("SELECT * FROM projeto WHERE id_projeto='$row_clt[id_projeto]' ");
$row_proj = mysql_fetch_assoc($result_proj);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_reg[id_master]' ") or die(mysql_error());
$row_master = mysql_fetch_assoc($qr_master);


$result_empresa = mysql_query("Select * from  rhempresa where id_empresa = '$row_clt[rh_vinculo]'");
$row_empresa = mysql_fetch_array($result_empresa);

$meses_pt = array('Erro','Janeiro','Fevereiro','Mar�o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

$dia = date('d');
$mes = date('n');
$ano = date('Y');

switch ($mes) {
case 1:
$mes = "Janeiro";
break;
case 2:
$mes = "Fevereiro";
break;
case 3:
$mes = "Mar�o";
break;
case 4:
$mes = "Abril";
break;
case 5:
$mes = "Maio";
break;
case 6:
$mes = "Junho";
break;
case 7:
$mes = "Julho";
break;
case 8:
$mes = "Agosto";
break;
case 9:
$mes = "Setembro";
break;
case 10:
$mes = "Outubro";
break;
case 11:
$mes = "Novembro";
break;
case 12:
$mes = "Dezembro";
break;
}

$data_entrada = explode("/",$row_clt['data_entrada']);
$dia_entrada = $data_entrada[0];
$mes_entrada = $data_entrada[1];
$ano_entrada = $data_entrada[2];
$data_final = date("d/m/Y", mktime (0, 0, 0, $mes_entrada  , $dia_entrada + ($prazoExp - 1), $ano_entrada));
$data_final_ano = date("d/m/Y", mktime (0, 0, 0, $mes_entrada  , $dia_entrada + (365 - 1), $ano_entrada));
$data_incial_pro = date("d/m/Y", mktime (0, 0, 0, $mes_entrada  , $dia_entrada + ($prazoExp), $ano_entrada));
$data_final_pro = date("d/m/Y", mktime (0, 0, 0, $mes_entrada  , $dia_entrada + ($prazoExp+$prazoPro-1), $ano_entrada));
$data_final1 = explode("/",$data_final);
$dia_final = $data_final1[0];
$mes_final = $data_final1[1];
$ano_final = $data_final1[2];
$data_final2 = date("d/m/Y", mktime (0, 0, 0, $mes_final  , $dia_final + ($prazoExp - 1), $ano_final));

$id_curso = $row_curso['id_curso'];

$qrsalario = "select * from rh_salario where id_curso = '$id_curso' order by data desc limit 1";
$rssalario = mysql_query($qrsalario);
$salarioAntigo = mysql_fetch_array($rssalario);
$salario1 = $salarioAntigo['salario_novo'];
$totalHistorico = mysql_num_rows($rssalario);

if ($salarioAntigo['salario_antigo'] == '0' or $salarioAntigo['salario_antigo'] == '1'){
    $salario1 = $salarioAntigo['salario_novo'];
} else {
    $salario1 = $salarioAntigo['salario_antigo'];
}
if($totalHistorico == 0){
                            $salario1 = $row_curso['salario'];
                        }

if($_COOKIE['logado'] != 87 and $row_clt['status'] == 10){
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];

$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '3' and id_clt = '$clt'");
$num_row_verifica = mysql_num_rows($result_verifica);
if($num_row_verifica == "0"){
	mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('3','$clt','$data_cad', '$user_cad')");
}else{
	mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$clt' and tipo = '3'");
}
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
}


?>
<?php
function valor_extenso($valor=0, $maiusculas=false)
{
    // verifica se tem virgula decimal
    if (strpos($valor,",") > 0)
    {
      // retira o ponto de milhar, se tiver
      $valor = str_replace(".","",$valor);
 
      // troca a virgula decimal por ponto decimal
      $valor = str_replace(",",".",$valor);
    }
$singular = array("centavo", "real", "mil", "milh�o", "bilh�o", "trilh�o", "quatrilh�o");
$plural = array("centavos", "reais", "mil", "milh�es", "bilh�es", "trilh�es",
"quatrilh�es");
 
$c = array("", "cem", "duzentos", "trezentos", "quatrocentos",
"quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta",
"sessenta", "setenta", "oitenta", "noventa");
$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze",
"dezesseis", "dezesete", "dezoito", "dezenove");
$u = array("", "um", "dois", "tr�s", "quatro", "cinco", "seis",
"sete", "oito", "nove");
 
        $z=0;
 
        $valor = number_format($valor, 2, ".", ".");
        $inteiro = explode(".", $valor);
		$cont=count($inteiro);
		        for($i=0;$i<$cont;$i++)
                for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
                $inteiro[$i] = "0".$inteiro[$i];
 
        $fim = $cont - ($inteiro[$cont-1] > 0 ? 1 : 2);
        for ($i=0;$i<$cont;$i++) {
                $valor = $inteiro[$i];
                $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
                $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
                $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";
 
                $r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd &&
$ru) ? " e " : "").$ru;
                $t = $cont-1-$i;
                $r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
                if ($valor == "000")$z++; elseif ($z > 0) $z--;
                if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t];
                if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) &&
($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
        }
 
         if(!$maiusculas)
		 {
          return($rt ? $rt : "zero");
         } elseif($maiusculas == "2") {
          return (strtoupper($rt) ? strtoupper($rt) : "Zero");
         } else {
         return (ucwords($rt) ? ucwords($rt) : "Zero");
         }
 
}
?>
<div class="pagina" style="text-justify: inter-word!important;">
	<TABLE BORDER=0 align="center" CELLPADDING=0 CELLSPACING=0 bgcolor="#FFFFFF" class="bordaescura1px">
            <TR>
              <TD colspan="3" align="center" valign="middle" class="titulo_documento"> 
                  <img src="../imagens/logomaster6.gif">
                  <hr style="margin-top: 1px; margin-bottom: 11px;" />
              </TD>
            </tr>
            <tr VALIGN=TOP>
                <TD colspan="3" class="titulo_documento" STYLE="font-weight: bold;">
                    CONTRATO DE APRENDIZAGEM
                    <br/><br/>
                </TD>
            </tr>
            </TABLE>
    
    <div align="right" style="font-size: 12px;"><strong>CONTRATO DE APRENDIZAGEM QUE <br>
                    ENTRE SI FAZEM, DE UM LADO, O <?=$row_master['razao'];?>, <br>
                    E DE OUTRO LADO, NA QUALIDADE <br>
                    DE APRENDIZ, O <?php echo $row_clt['nome']; ?>. <br></strong>
                </div>
    
	<TABLE WIDTH=794 BORDER=0 align="center" CELLPADDING=0 CELLSPACING=0 bgcolor="#FFFFFF" class="bordaescura1px">
	  
	  <TR VALIGN=TOP>
              <TD WIDTH=677 HEIGHT=876><DIV>
		
	  </DIV><DIV ALIGN=LEFT class="linha"  STYLE=" font-family: Arial; color: #000000; font-size: 10pt;">
              <div align="justify" class="textoprint">
		  <p>
		  <p><FONT STYLE=" font-family: Arial; color: #000000; font-size: 8pt;">
		      
                      
                                    A <?=$row_master['razao'];?>, com sede  <?= $row_proj['endereco'] ?>, Bairro <?= $row_proj['bairro'] ?>, Cidade <?= $row_proj['cidade'] ?>,
                                  Estado <?= $row_proj['estado'] ?>, inscrito no CNPJ sob o n� <?= $row_empresa['cnpj'] ?>, neste ato Representada por 
                                  <?= $row_empresa['responsavel'] ?>,<?= $row_empresa['nacionalidade'] ?>, 
                                    (nome do preposto, nacionalidade, estado civil, profiss�o, Resid�ncia, n� da carteira de identidade e inscri��o do CPF n�<?= $row_empresa['cpf'] ?> 
                                    doravante denominada  CONTRATANTE, e de  outro lado, na qualidade de  EMPREGADO APRENDIZ,
                                    <?php echo $row_clt['nome']; ?>, <?php echo $row_clt['nacionalidade']; ?>, <?php echo $row_clt['civil']; ?>, <?php echo $row_clt['nome_escolaridade']; ?>,
                                        <?php echo $row_clt['endereco']; ?>, n� da carteira de identidade <?php echo $row_clt['cpf']; ?> ou <?php echo $row_clt['campo1']; ?>
                                    <!--(nome do Jovem, nacionalidade, estado civil, estudante, resid�ncia, n� da carteira de identidade  __ ou ____   CTPS-->
                                    doravante CONTRATADO, firmam o presente contrato mediante as seguintes cl�usulas e condi��es:
                                    
                                    
                                    <br><br> 
                                    
                                    <h3><strong>CL�USULA PRIMEIRA - DO OBJETO</strong></h3>
                                    <strong>O CONTRATADO</strong>, na qualidade de empregado aprendiz se compromete a freq�entar o curso de <strong>APRENDIZAGEM COMERCIAL EM SERVI�OS ADMINISTRATIVOS</strong>
                                    ministrado pelo Servi�o Nacional de Aprendizagem Comercial - Departamento Regional do Estado do Rio de Janeiro - SENAC/RJ,
                                    de acordo comercial com o programa previamente estabelecido pela Entidade.
                                    Par�grafo �nico - <strong>O CONTRATADO</strong> sujeitar-se-� quanto aos aspectos t�cnicos da Aprendizagem �s normas e metodologias adotadas pelo SENAC/RJ.
                                    
                                    <br><br>
                                    
                                    <strong>Par�grafo �nico</strong> -  <strong>O CONTRATADO</strong> sujeitar-se-� quanto aos aspectos t�cnicos da Aprendizagem �s normas e metodologias adotadas pelo SENAC/RJ.

                                    <br><br>
                                    
                                    <h3><strong>CL�USULA SEGUNDA - DAS OBRIGA��ES DO CONTRATANTE</strong></h3>

                                    I - O <strong>CONTRATANTE</strong> a seu exclusivo crit�rio, obriga-se a fornecer ao <strong>CONTRATADO</strong>
                                    todos os meios materiais, para que possa haver um perfeito desenvolvimento do objeto do presente <strong>CONTRATO</strong>,
                                    e via de conseq��ncia, da forma��o t�cnico-profissional met�dica do aprendiz, compat�vel com o seu desenvolvimento f�sico, moral e psicol�gico.
                                    
                                    <br><br>   
                                    
                                    II - Formalizar por escrito o contrato de aprendizagem, determinando o in�cio e o final de sua vig�ncia, por ser um contrato de trabalho especial.
                                    III - Conceder as f�rias ao empregado aprendiz coincidindo com o per�odo de f�rias escolares, vedado o parcelamento.
                                    IV - O CONTRATANTE dever� oferecer condi��es de seguran�a e sa�de, conforme o disposto no art. 405 da CLT, e nas Normas Regulamentadoras, aprovadas pela Portaria n� 3.214/78.

                                    <h3><strong>CL�USULA TERCEIRA - DAS OBRIGA��ES DO CONTRATADO</strong></h3>

                                    I - Cumprir fielmente as obriga��es assumidas na cl�usula primeira;
                                    II - Executar com zelo e dilig�ncia as tarefas necess�rias � forma��o objeto do contrato
                                    III - Cumprir as metas do aprendizado estabelecidas pelo SENAC/RJ.
                                    IV - Estar matriculado e freq�entar escola de ensino regular, caso o aprendiz n�o tenha conclu�do o ensino m�dio.
                                     
                                    <br><br>
                                    
                                    <h3><strong>CL�USULA QUARTA - DAS CONDI��ES DE VALIDADE DO CONTRATO</strong></h3>

                                    I - registro e anota��o na Carteira de Trabalho e Previd�ncia Social.
                                    II - matr�cula e freq��ncia do aprendiz � escola regular, caso n�o tenha conclu�do o ensino m�dio.
                                    III - inscri��o do aprendiz em curso de aprendizagem desenvolvido sob a orienta��o do SENAC/RJ nos moldes do art. 430 da CLT.
                                    IV - defini��o de programa de aprendizagem, desenvolvido atrav�s de atividades te�ricas e pr�ticas, contendo os objetivos do curso, conte�dos a serem ministrados e a carga hor�ria.
                                    V - Possuir o empregado aprendiz entre 18 e 24 anos de idade.

                                    <br><br>
                                    
                                    <h3><strong>CL�USULA QUINTA - DA REMUNERA��O</strong></h3>

                                    O CONTRATADO pelas atividades, objeto do Contrato, receber� a t�tulo de sal�rio, a quantia de R$ <?=$row_curso['salario']?>   (<?php echo valor_extenso(number_format($row_curso['salario'],2,',',''));  ?>) mensal.
                                    � 1� - A al�quota do dep�sito ao Fundo de Garantia por Tempo de Servi�o ? FGTS -ser� de 2 %(dois por cento) da remunera��o paga ou devida ao empregado aprendiz, de acordo com o � 7� do art. 15 da Lei n� 8.036/90.
                                    � 2� - O aprendiz ter� direito ao sal�rio m�nimo-hora, observando-se, caso exista, piso estadual e condi��o mais favor�vel. A exist�ncia de conven��o ou o acordo coletivo da categoria poder� garantir ao aprendiz sal�rio maior que o m�nimo (art. 428, � 2�, da CLT e art. 17, par�grafo �nico do Decreto n� 5.598/05). 
                                    � 3� - Al�m das horas destinadas �s atividades pr�ticas, ser�o computadas no sal�rio tamb�m as horas destinadas �s aulas te�ricas, o descanso semanal remunerado e feriados.

                                    <br><br>
                                    
                                    <h3><strong>CL�USULA SEXTA - DA JORNADA</strong></h3>

                                    A dura��o da jornada do empregado aprendiz ser� de <?= $row_horarios['nome'] ?>  horas di�rias, perfazendo <?= $row_horarios['horas_semanais'] ?>  horas semanais,
                                    nelas inclu�das as atividades te�ricas e/ou pr�ticas, vedadas a prorroga��o e a compensa��o da jornada, inclusive as hip�teses previstas nos incisos I e II do art. 413 da CLT.

                                    <br><br>
                                    
                                    <h3><strong>CL�USULA S�TIMA - DA VIG�NCIA (2 anos)<strong></h3>

                                   Este contrato ter� sua vig�ncia no per�odo de <?php echo $row_clt['data_entrada'] . ' � ' . $row_clt['dt_entradaY']?>  . 
                                   
                                   
                                   
                                   <h3><strong>CL�USULA OITAVA - DA RESCIS�O</strong></h3>

                                    O presente contrato extinguir-se-� no seu termo ou quando o aprendiz completar 24 (vinte e quatro) anos.  E, nas hip�teses de rescis�o antecipada do contrato de aprendizagem n�o se aplicam os artigos 479 e 480 da CLT, que tratam da indeniza��o, por metade, da remunera��o a que teria direito at� o termo do contrato.
                                    � 1� - S�o hip�teses de rescis�o antecipada do contrato de aprendizagem: 1 - desempenho insuficiente ou inadapta��o do aprendiz; 2 - falta disciplinar grave nos termos do art. 482 da CLT; 3 - aus�ncia injustificada � escola regular que implique perda do ano letivo; 4 - e, a pedido do aprendiz.
                                    � 2� - Na incid�ncia da primeira hip�tese caber� a entidade executora da aprendizagem prestar a declara��o e na ocorr�ncia da terceira hip�tese a declara��o dever� ser expedida pelo estabelecimento de ensino regular.

                                    
                                    <h3><strong>CL�USULA NONA - DO FORO</strong></h3>

                                    As partes elegem o foro da Comarca da cidade do Rio de Janeiro para dirimir quaisquer conflitos oriundos do presente contrato, com expressa ren�ncia de qualquer outro, por mais privilegiado que seja.
                                    E assim, por se acharem justas e contratadas, as partes firmam o presente instrumento em 03 (tr�s) vias de igual teor e forma, na presen�a das testemunhas abaixo nomeadas, para que produza seus efeitos jur�dicos e legais.

                                                                    		    
		  <p>&nbsp;</p>
		  <p><FONT STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"><BR>
		  </font></p>
		  <FONT STYLE=" font-family: Arial; color: #000000; font-size: 8pt;">
                                    <p align="center">
                                        <?php list($dia_entrada,$mes_entrada,$ano_entrada) = explode('/',$row_clt['data_entrada']); print "$row_proj[nome], $dia_entrada de ".$meses_pt[(int)$mes_entrada]." de $ano_entrada."; //print "$row_reg[regiao], $dia de $mes de $ano."; ?></p>
		  </font>
		    <table width="100%" border="0" >
		      <tr>
		        <td align="center">____________________________________</td>
		        <td align="center">____________________________________</td>
		      </tr>
		      <tr class="linha">
                                            <!--            <td align="center" class="linha"><strong><?= $row_master['razao'];
                                            ?></strong></td>-->
                                        <td align="center" class="linha"><strong>INSTITUTO LAGOS RIO</strong></td> <!-- &nbsp; row_master['razao'] = Substituido Direto Por Instituto Lagos Rio-->
                                        <td align="center" class="linha"><strong><?php echo $row_clt['nome']; ?></strong></td>
                                        </tr>
		      <tr>
		        <td align="center">&nbsp;</td>
		        <td align="center">&nbsp;</td>
		      </tr>
		      <tr>
		        <td align="center">&nbsp;</td>
		        <td align="center">&nbsp;</td>
		      </tr>
                                        <tr>
                                            <td align="center">&nbsp;</td>
                                            <td class="linha" style="font-size: 10px;"><hr style="border-top: 1px solid #333;width: 262px; margin: 0px auto 5px;"><p style="text-align: center"><strong>Respons�vel pelo Jovem Aprendiz  Menor de 18 anos<br></strong></p></td>
                                        </tr>
		      <tr>
                                        <td align="center"><strong>____________________________________</strong></td>
		        <td align="center"><strong>____________________________________</strong></td>
		      </tr>
		      <tr>
		        <td class="linha text-center" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Testemunha<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		        <td class="linha text-center"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Testemunha<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
		      </tr>
		    </table>
		    <p align="center" class="linha">&nbsp;</p>
	</DIV>
		  <p>&nbsp;</p>
		  <p><span class="linha"><BR>
		  </span></p>
		</div>
	  </DIV><DIV ALIGN=LEFT  STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"></DIV><DIV ALIGN=CENTER class="linha"  STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"><BR>
	  </DIV>
	  <DIV ALIGN=LEFT  STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"></DIV><DIV ALIGN=CENTER class="linha"  STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"><BR>
	  </DIV></TD>
	</TABLE>
</div>
