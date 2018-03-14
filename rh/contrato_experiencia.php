
<TABLE WIDTH=794 BORDER=0 align="center" CELLPADDING=0 CELLSPACING=0 bgcolor="#FFFFFF" class="bordaescura1px">
<TR><TD HEIGHT=20></TD>
<TR VALIGN=TOP>
  <TD height="22" colspan="3" align="center" valign="middle"> <strong> 
		<img src="../imagens/logomaster<?php echo $row_master['id_master']; ?>.gif"/>
</TD>
  <TR VALIGN=TOP><TD WIDTH=296></TD><TD WIDTH=250 HEIGHT=22 ALIGN=CENTER STYLE="font-size: 14pt; font-family: Arial; color: #000000; font-weight: bold;">CONTRATO DE EXPERIÊNCIA</TD><TD WIDTH=300></TD>
</TABLE>
<TABLE WIDTH=794 BORDER=0 align="center" CELLPADDING=0 CELLSPACING=0 bgcolor="#FFFFFF" class="bordaescura1px">
  <TR><TD HEIGHT=25></TD>
  <TR VALIGN=TOP><TD WIDTH=62></TD><TD WIDTH=677 HEIGHT=876><DIV>
    <hr>
  </DIV><DIV ALIGN=LEFT class="linha"  STYLE=" font-family: Arial; color: #000000; font-size: 10pt;">
    <div align="justify">
      <p>
      <p><FONT STYLE=" font-family: Arial; color: #000000; font-size: 8pt;">
          
        Pelo presente instrumento particular e na melhor forma de direito,
        os abaixo assinados, <?=$row_master['razao'];?>,
        sediada na AVENIDA PAULISTA 2300<?="{$row_proj['endereco']}"?>, Bairro <?="{$row_proj['bairro']}"?>, Cidade <?="{$row_proj['cidade']}"?>,
        Estado <?="{$row_proj['estado']}"?> inscrita no CNPJ do MF sob Nº <?=$row_empresa['cnpj'];?> denominada Empregadora,
        e a ser (a).  DENIZE LAIS VOLCOV, domiciliado a 335831800108, BAIRRO: RUA DESDEMONA,0 CEP: 26, 33352 na cidade de SAO PAULO,
        no estado de SP, portador da CTPS Nº , Série 33352  - SP, doravante designado Empregado,
        celebram o presente Contrato Individual de Trabalho para fins de experiência,
        conforme legislação trabalhista em vigor,
        regido pelas cláusulas abaixo e demais disposições vigentes:  
   
<!--        Pelo presente instrumento particular e na melhor forma de direito, os abaixo assinados, 
        <?=$row_master['razao'];?>, sediada na  <?="{$row_proj['endereco']}, {$row_proj['bairro']},
            {$row_proj['cidade']}, {$row_proj['estado']}";?>, inscrito no CNPJ/MF sob o nº <?=$row_empresa['cnpj'];?>, 
            denominada Empregadora, e a ser (a). <?=$row_master['responsavel'];?>, <?=$row_master['nacionalidade'];?> ,
        <?=$row_master['civil'];?>, <?=$row_master['formacao'];?>, portador da Cédula de Identidade n.º <?=$row_master['rg'];?>,
        inscrito no CPF sob o n.º <?=$row_master['cpf'];?>
        , portador doravante designada, simplesmente EMPREGADORA e de outro lado <?=$row_clt['nome']; ?>, residente e domiciliado na <?=$row_clt['endereco'].", ".$row_clt['numero'].", ".$row_clt['complemento']." - ".$row_clt['bairro']." - ".$row_clt['cidade']." - ".$row_clt['uf'].", ".$row_clt['cep']; ?>, portador da CTPS n°  <?=$row_clt['campo1']." / ".$row_clt['serie_ctps']." - ".$row_clt['uf_ctps']?>, RG n° <?=$row_clt['rg']; ?> e CPF/MF <?=$row_clt['cpf']; ?> a seguir chamado apenas de EMPREGADO, é celebrado o presente CONTRATO DE EXPERI&Ecirc;NCIA, que terá vigência a partir da data de início da prestação de serviços abaixo apontada, de acordo com as condições a seguir especificadas:-->
        <BR>
        <BR>
        &nbsp;1 -  O Empregado trabalhará para a Empregadora na função de <?=$row_curso['nome']?> , assim como, realizando funções que vierem a ser objeto de ordens verbais, instruções, ordens de serviço, avisos e circulares, segundo as necessidades   da   empregadora, desde que compatíveis com as suas atribuições.
        <!--&nbsp;1 - Fica o EMPREGADO admitido no quadro de funcionários da EMPREGADORA para exercer as funções de <?=$row_curso['nome']?> mediante a remuneração de: R$ <?=$row_curso['salario']?> (<?php echo valor_extenso(number_format($row_curso['salario'],2,',',''));  ?>) por Mês.-->
        <BR>  <BR>
        2 - O local de trabalho é designado de acordo com a posição exercida, podendo a Empregadora, a qualquer tempo, transferir o Empregado a título temporário ou definitivo, tanto no âmbito da unidade para a qual foi admitido, como para outras, em conformidade com o parágrafo 1º do artigo 469 da Consolidação das Leis do Trabalho - CLT. 
         <BR>
        2.1. <?=$row_curso['nome']?>. 
        <!--2- O Horário de trabalho será aquele anotado na ficha de registro do EMPREGADO, sendo que eventual alteração na jornada de trabalho por mútuo consenso, não inovará esse ajuste, permanecendo sempre íntegra a obrigação do EMPREGADO de cumprir o horário contratualmente estabelecido, observando o limite legal.-->
        <BR>  <BR> 
        3 ? O horário de trabalho do empregado é definido conforme escala de serviços, podendo ocorrer alteração do mesmo, de acordo com as necessidades do serviço ou da unidade de trabalho.
         <BR>
        3.1. O horário de trabalho será: 10h AS 19h 2a a 6a. 40 horas semanais.
<!--3- Nos termos do que dispõe o parágrafo primeiro do artigo 469 da Consolidação das Leis de Trabalho (“CLT”), o EMPREGADO acatará determinação emanada da EMPREGADORA para a prestação de serviços tanto na localidade de celebração do CONTRATO DE TRABALHO, como em qualquer outra cidade, capital ou vila do território nacional, quando esta decorra de real necessidade de serviço,quer essa transferência seja transitória, quer seja definitiva.-->
        <BR>  <BR> 
        4- O Empregado perceberá a remuneração de R$ <?=$row_curso['salario']?> por MÊS.
<!--4- No ato da assinatura desse contrato, o EMPREGADO recebe o Regulamento Interno da Empresa cujas cláusulas fazem parte do contrato de trabalho, e a violação de qualquer uma delas implicará em sanção, cuja gradação dependerá da gravidade da mesma, podendo culminar com a rescisão do contrato.-->
        <BR>  <BR>
        5 -  O período de experiência é de <?php echo $prazoExp; ?> (<?php echo $prazoExpExt; ?>) com início em <?=$row_clt['data_entrada']?>, podendo ser prorrogado por mais <?php echo $prazoProrrogado; ?> dias.
         <BR>
        5.1 - Havendo continuidade na prestação de serviços, a Empregadora após o período de experiência de 90 (noventa) dias, o presente contrato passará a vigorar por prazo indeterminado, mantidas as demais condições ora estabelecidas.
<!--5- Em caso de dano causado pelo EMPREGADO fica a EMPREGADORA autorizada a efetivar o desconto da importância correspondente ao prejuízo, o qual fará, com fundamento no parágrafo primeiro do artigo 462 da Consolidação das Leis de Trabalho, já que expressamente prevista em contrato.-->
        <BR>  <BR>
        6 -  A rescisão do presente contrato por parte da Empregadora ou do Empregado, antes do término do período de experiência implicará em indenização, conforme art. 479 e 480 da CLT.
<!--6- O presente contrato vigerá por <?php echo $prazoExp; ?> (<?php echo $prazoExpExt; ?>) dias, podendo ser prorrogado por <?php echo $prazoProrrogado; ?>, com início em <?=$row_clt['data_entrada']?> e término em <?=$data_final?> e <?=$data_incial_pro?> à <?=$data_final_pro?>, sendo celebrado a título de experiência, para que as partes verifiquem, reciprocamente, a conveniência ou não de se vincularem em caráter definitivo a um contrato de trabalho por prazo indeterminado.--> 
        <BR>  <BR>
        7 -  Além dos descontos previstos em Lei, reserva-se a Empregadora o direito de descontar do Empregado as importâncias correspondentes aos danos por ele causados, com fundamento no parágrafo 1º do artigo 462 da CLT. 
<!--7- Fica estabelecido que, findo o prazo acima, este contrato será rescindido, independente de aviso prévio, e nos termos dos artigos 479 e 480 da CLT.-->
        <BR>  <BR>   
        8 -  O Empregado fica ciente dos Regulamentos Internos da Empresa e das Normas de Segurança que regulam suas atividades na Empregadora, sendo que sua não observância acarretará na aplicação de medidas administrativas disciplinares cabíveis a cada caso.
<!--5- Em caso de dano causado pelo EMPREGADO fica a EMPREGADORA autorizada a efetivar o desconto da importância correspondente ao prejuízo, o qual fará, com fundamento no parágrafo primeiro do artigo 462 da Consolidação das Leis de Trabalho, já que expressamente prevista em contrato.-->
        <BR>  <BR>
       9 -  O Empregado concorda que toda informação, documento, dados, nomes, papéis, receitas médicas ou qualquer informação relacionada à doença ou tratamento de pessoas vinculadas à sua atividade, não poderá em momento algum, quer durante a vigência do contrato de trabalho, quer após a sua rescisão, ser veiculada, comentada, discutida ou divulgada por qualquer meio de comunicação existente, devendo ser mantido em absoluto sigilo e confidencialidade.
<!--6- O presente contrato vigerá por <?php echo $prazoExp; ?> (<?php echo $prazoExpExt; ?>) dias, podendo ser prorrogado por <?php echo $prazoProrrogado; ?>, com início em <?=$row_clt['data_entrada']?> e término em <?=$data_final?> e <?=$data_incial_pro?> à <?=$data_final_pro?>, sendo celebrado a título de experiência, para que as partes verifiquem, reciprocamente, a conveniência ou não de se vincularem em caráter definitivo a um contrato de trabalho por prazo indeterminado.--> 
        <BR>  
        9.1 -  O Empregado somente poderá utilizar-se destas informações para cumprir com as obrigações derivadas do presente contrato e não poderá proporcionar a informação confidencial a qualquer pessoa física ou jurídica, sob pena de incorrer na extinção do contrato de trabalho por justa causa e/ou em indenização cabível. Tendo assim contratado, assinam o presente instrumento, em duas vias, na presença da testemunha abaixo.
<!--7- Fica estabelecido que, findo o prazo acima, este contrato será rescindido, independente de aviso prévio, e nos termos dos artigos 479 e 480 da CLT.-->
        <BR>  <BR>   
        
        </font></p>
      <p>&nbsp;</p>
      <p><FONT STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"><BR>
      </font></p>
      <FONT STYLE=" font-family: Arial; color: #000000; font-size: 8pt;">
      <p align="center"><?php list($dia_entrada,$mes_entrada,$ano_entrada) = explode('/',$row_clt['data_entrada']); print "$row_proj[nome], $dia_entrada de ".$meses_pt[(int)$mes_entrada]." de $ano_entrada."; //print "$row_reg[regiao], $dia de $mes de $ano."; ?></p>
      </font>
        <table width="100%" border="0" >
          <tr>
            <td align="center">____________________________________</td>
            <td align="center">____________________________________</td>
          </tr>
          <tr class="linha">
<!--            <td align="center" class="linha"><strong><?= $row_master['razao'];
?></strong></td>-->
            <td align="center" class="linha"><strong>DENIZE LAIS VOLCOV
</strong></td>
            <td align="center" class="linha"><strong>
              &nbsp;<?=$row_master['razao']?></strong></td>
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
            <td align="center"><strong>____________________________________</strong></td>
            <td align="center"><strong>____________________________________</strong></td>
          </tr>
          <tr>
            <td class="linha"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Testemunha<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RG:</strong></td>
            <td class="linha"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Testemunha<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RG:</strong></td>
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
  </DIV></TD><TD WIDTH=55 bgcolor="#FFFFFF"></TD>
</TABLE>

