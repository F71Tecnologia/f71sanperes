
<TABLE WIDTH=794 BORDER=0 align="center" CELLPADDING=0 CELLSPACING=0 bgcolor="#FFFFFF" class="bordaescura1px">
<TR><TD HEIGHT=20></TD>
<TR VALIGN=TOP>
  <TD height="22" colspan="3" align="center" valign="middle"> <strong> 
		<img src="../imagens/logomaster<?php echo $row_master['id_master']; ?>.gif"/>
</TD>
  <TR VALIGN=TOP><TD WIDTH=296></TD><TD WIDTH=250 HEIGHT=22 ALIGN=CENTER STYLE="font-size: 14pt; font-family: Arial; color: #000000; font-weight: bold;">CONTRATO DE EXPERI�NCIA</TD><TD WIDTH=300></TD>
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
        Estado <?="{$row_proj['estado']}"?> inscrita no CNPJ do MF sob N� <?=$row_empresa['cnpj'];?> denominada Empregadora,
        e a ser (a).  DENIZE LAIS VOLCOV, domiciliado a 335831800108, BAIRRO: RUA DESDEMONA,0 CEP: 26, 33352 na cidade de SAO PAULO,
        no estado de SP, portador da CTPS N� , S�rie 33352  - SP, doravante designado Empregado,
        celebram o presente Contrato Individual de Trabalho para fins de experi�ncia,
        conforme legisla��o trabalhista em vigor,
        regido pelas cl�usulas abaixo e demais disposi��es vigentes:  
   
<!--        Pelo presente instrumento particular e na melhor forma de direito, os abaixo assinados, 
        <?=$row_master['razao'];?>, sediada na  <?="{$row_proj['endereco']}, {$row_proj['bairro']},
            {$row_proj['cidade']}, {$row_proj['estado']}";?>, inscrito no CNPJ/MF sob o n� <?=$row_empresa['cnpj'];?>, 
            denominada Empregadora, e a ser (a). <?=$row_master['responsavel'];?>, <?=$row_master['nacionalidade'];?> ,
        <?=$row_master['civil'];?>, <?=$row_master['formacao'];?>, portador da C�dula de Identidade n.� <?=$row_master['rg'];?>,
        inscrito no CPF sob o n.� <?=$row_master['cpf'];?>
        , portador doravante designada, simplesmente EMPREGADORA e de outro lado <?=$row_clt['nome']; ?>, residente e domiciliado na <?=$row_clt['endereco'].", ".$row_clt['numero'].", ".$row_clt['complemento']." - ".$row_clt['bairro']." - ".$row_clt['cidade']." - ".$row_clt['uf'].", ".$row_clt['cep']; ?>, portador da CTPS n�  <?=$row_clt['campo1']." / ".$row_clt['serie_ctps']." - ".$row_clt['uf_ctps']?>, RG n� <?=$row_clt['rg']; ?> e CPF/MF <?=$row_clt['cpf']; ?> a seguir chamado apenas de EMPREGADO, � celebrado o presente CONTRATO DE EXPERI&Ecirc;NCIA, que ter� vig�ncia a partir da data de in�cio da presta��o de servi�os abaixo apontada, de acordo com as condi��es a seguir especificadas:-->
        <BR>
        <BR>
        &nbsp;1 -  O Empregado trabalhar� para a Empregadora na fun��o de <?=$row_curso['nome']?> , assim como, realizando fun��es que vierem a ser objeto de ordens verbais, instru��es, ordens de servi�o, avisos e circulares, segundo as necessidades   da   empregadora, desde que compat�veis com as suas atribui��es.
        <!--&nbsp;1 - Fica o EMPREGADO admitido no quadro de funcion�rios da EMPREGADORA para exercer as fun��es de <?=$row_curso['nome']?> mediante a remunera��o de: R$ <?=$row_curso['salario']?> (<?php echo valor_extenso(number_format($row_curso['salario'],2,',',''));  ?>) por M�s.-->
        <BR>  <BR>
        2 - O local de trabalho � designado de acordo com a posi��o exercida, podendo a Empregadora, a qualquer tempo, transferir o Empregado a t�tulo tempor�rio ou definitivo, tanto no �mbito da unidade para a qual foi admitido, como para outras, em conformidade com o par�grafo 1� do artigo 469 da Consolida��o das Leis do Trabalho - CLT. 
         <BR>
        2.1. <?=$row_curso['nome']?>. 
        <!--2- O Hor�rio de trabalho ser� aquele anotado na ficha de registro do EMPREGADO, sendo que eventual altera��o na jornada de trabalho por m�tuo consenso, n�o inovar� esse ajuste, permanecendo sempre �ntegra a obriga��o do EMPREGADO de cumprir o hor�rio contratualmente estabelecido, observando o limite legal.-->
        <BR>  <BR> 
        3 ? O hor�rio de trabalho do empregado � definido conforme escala de servi�os, podendo ocorrer altera��o do mesmo, de acordo com as necessidades do servi�o ou da unidade de trabalho.
         <BR>
        3.1. O hor�rio de trabalho ser�: 10h AS 19h 2a a 6a. 40 horas semanais.
<!--3- Nos termos do que disp�e o par�grafo primeiro do artigo 469 da Consolida��o das Leis de Trabalho (�CLT�), o EMPREGADO acatar� determina��o emanada da EMPREGADORA para a presta��o de servi�os tanto na localidade de celebra��o do CONTRATO DE TRABALHO, como em qualquer outra cidade, capital ou vila do territ�rio nacional, quando esta decorra de real necessidade de servi�o,quer essa transfer�ncia seja transit�ria, quer seja definitiva.-->
        <BR>  <BR> 
        4- O Empregado perceber� a remunera��o de R$ <?=$row_curso['salario']?> por M�S.
<!--4- No ato da assinatura desse contrato, o EMPREGADO recebe o Regulamento Interno da Empresa cujas cl�usulas fazem parte do contrato de trabalho, e a viola��o de qualquer uma delas implicar� em san��o, cuja grada��o depender� da gravidade da mesma, podendo culminar com a rescis�o do contrato.-->
        <BR>  <BR>
        5 -  O per�odo de experi�ncia � de <?php echo $prazoExp; ?> (<?php echo $prazoExpExt; ?>) com in�cio em <?=$row_clt['data_entrada']?>, podendo ser prorrogado por mais <?php echo $prazoProrrogado; ?> dias.
         <BR>
        5.1 - Havendo continuidade na presta��o de servi�os, a Empregadora ap�s o per�odo de experi�ncia de 90 (noventa) dias, o presente contrato passar� a vigorar por prazo indeterminado, mantidas as demais condi��es ora estabelecidas.
<!--5- Em caso de dano causado pelo EMPREGADO fica a EMPREGADORA autorizada a efetivar o desconto da import�ncia correspondente ao preju�zo, o qual far�, com fundamento no par�grafo primeiro do artigo 462 da Consolida��o das Leis de Trabalho, j� que expressamente prevista em contrato.-->
        <BR>  <BR>
        6 -  A rescis�o do presente contrato por parte da Empregadora ou do Empregado, antes do t�rmino do per�odo de experi�ncia implicar� em indeniza��o, conforme art. 479 e 480 da CLT.
<!--6- O presente contrato viger� por <?php echo $prazoExp; ?> (<?php echo $prazoExpExt; ?>) dias, podendo ser prorrogado por <?php echo $prazoProrrogado; ?>, com in�cio em <?=$row_clt['data_entrada']?> e t�rmino em <?=$data_final?> e <?=$data_incial_pro?> � <?=$data_final_pro?>, sendo celebrado a t�tulo de experi�ncia, para que as partes verifiquem, reciprocamente, a conveni�ncia ou n�o de se vincularem em car�ter definitivo a um contrato de trabalho por prazo indeterminado.--> 
        <BR>  <BR>
        7 -  Al�m dos descontos previstos em Lei, reserva-se a Empregadora o direito de descontar do Empregado as import�ncias correspondentes aos danos por ele causados, com fundamento no par�grafo 1� do artigo 462 da CLT. 
<!--7- Fica estabelecido que, findo o prazo acima, este contrato ser� rescindido, independente de aviso pr�vio, e nos termos dos artigos 479 e 480 da CLT.-->
        <BR>  <BR>   
        8 -  O Empregado fica ciente dos Regulamentos Internos da Empresa e das Normas de Seguran�a que regulam suas atividades na Empregadora, sendo que sua n�o observ�ncia acarretar� na aplica��o de medidas administrativas disciplinares cab�veis a cada caso.
<!--5- Em caso de dano causado pelo EMPREGADO fica a EMPREGADORA autorizada a efetivar o desconto da import�ncia correspondente ao preju�zo, o qual far�, com fundamento no par�grafo primeiro do artigo 462 da Consolida��o das Leis de Trabalho, j� que expressamente prevista em contrato.-->
        <BR>  <BR>
       9 -  O Empregado concorda que toda informa��o, documento, dados, nomes, pap�is, receitas m�dicas ou qualquer informa��o relacionada � doen�a ou tratamento de pessoas vinculadas � sua atividade, n�o poder� em momento algum, quer durante a vig�ncia do contrato de trabalho, quer ap�s a sua rescis�o, ser veiculada, comentada, discutida ou divulgada por qualquer meio de comunica��o existente, devendo ser mantido em absoluto sigilo e confidencialidade.
<!--6- O presente contrato viger� por <?php echo $prazoExp; ?> (<?php echo $prazoExpExt; ?>) dias, podendo ser prorrogado por <?php echo $prazoProrrogado; ?>, com in�cio em <?=$row_clt['data_entrada']?> e t�rmino em <?=$data_final?> e <?=$data_incial_pro?> � <?=$data_final_pro?>, sendo celebrado a t�tulo de experi�ncia, para que as partes verifiquem, reciprocamente, a conveni�ncia ou n�o de se vincularem em car�ter definitivo a um contrato de trabalho por prazo indeterminado.--> 
        <BR>  
        9.1 -  O Empregado somente poder� utilizar-se destas informa��es para cumprir com as obriga��es derivadas do presente contrato e n�o poder� proporcionar a informa��o confidencial a qualquer pessoa f�sica ou jur�dica, sob pena de incorrer na extin��o do contrato de trabalho por justa causa e/ou em indeniza��o cab�vel. Tendo assim contratado, assinam o presente instrumento, em duas vias, na presen�a da testemunha abaixo.
<!--7- Fica estabelecido que, findo o prazo acima, este contrato ser� rescindido, independente de aviso pr�vio, e nos termos dos artigos 479 e 480 da CLT.-->
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

