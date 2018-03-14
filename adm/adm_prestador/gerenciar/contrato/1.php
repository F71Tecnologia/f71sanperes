<h2 class="titulo">CONTRATO DE PRESTA��O DE SERVI�OS</h2>            
<p>Pelo presente instrumento particular, de um lado o <strong><?= $prestador['contratante']; ?></strong>, pessoa jur�dica de direito privado, inscrito no CNPJ sob o n� <?= $prestador['cnpj_contratante']; ?>, localizado na <?= trim(strtoupper($prestador['endereco_contratante'] . ' ' . $prestador['bairro_contratante'] . ' ' . $prestador['cidade_contratante'] . ' ' . $prestador['estado_contratante'])); ?>, doravante denominado <strong>CONTRATANTE</strong>, e de outro lado, <strong><?= trim($prestador['nome_fantasia']); ?></strong>, pessoa jur�dica de direito privado, inscrito no CNPJ sob o n.� <?= trim($prestador['cnpj']); ?>, com sede na <?= trim(strtoupper($prestador['endereco'])); ?>, doravante denominada <strong>CONTRATADA;</strong></p>
<p>Firmam entre si, o presente contrato de presta��o de servi�os, mediante as seguintes cl�usulas e condi��es:</p>
<h4>Cl�usula Primeira - OBJETO</h4>
<p>1.1 - O objeto do presente Contrato refere-se � disponibiliza��o de m�o de obra especializada na �rea m�dica para presta��o de plant�es m�dicos na lota��o <?= $prestador['municipio']; ?>, em parceira com a Prefeitura de <?= $prestador['municipio']; ?>.</p>
<h4>Cl�usula Segunda - PRAZO</h4>
<p>2.1 - O presente Contrato ser� por prazo indeterminado, iniciando sua vig�ncia a partir do dia <?= $contratado_em['2']; ?> de <?= $meses[$contratado_em[1]]; ?> de <?= $contratado_em[0]; ?>, podendo ser rescindido por qualquer das Partes, a qualquer momento, sem justo motivo, desde que haja pr�via comunica��o expressa, com anteced�ncia m�nima de 30 (trinta) dias.</p>
<h4>Cl�usula Terceira - DA PRESTA��O DE SERVI�OS</h4>
<p>3.1 - A CONTRATADA prestar� os servi�os objeto do presente Contrato de forma aut�noma e sem qualquer v�nculo de natureza trabalhista, previdenci�ria e tribut�ria;</p>
<p>3.2 - A CONTRATADA dever� prestar os servi�os objeto da presente contrata��o, dentro das depend�ncias da CONTRATANTE, em car�ter n�o habitual.</p>
<h4>Cl�usula Quarta - REMUNERA��O</h4>
<p>4.1 - Em remunera��o pelos servi�os profissionais ora contratados, ser� devida a import�ncia fixa mensal de R$ <?= !empty($prestador['valor_limite']) ? number_format($prestador['valor_limite'], 2, ',', '.') : NULL; ?> (<?= valor_extenso(!empty($prestador['valor_limite']) ? $prestador['valor_limite'] : '0.00' ); ?>) a serem pagos via dep�sito em conta corrente de titularidade da CONTRATADA, cujos dados s�o os seguintes:</p>
<p>BANCO <?= $prestador['nome_banco'] ?></p>
<p>Ag�ncia <?= $prestador['agencia'] ?></p>
<p>Conta Corrente <?= $prestador['conta'] ?></p>
<p>Titular <?= $prestador['nome_fantasia']; ?></p>
<p>CNPJ  <?= $prestador['cnpj']; ?></p>
<p>Par�grafo Primeiro - Para que a CONTRATANTE possa proceder ao pagamento da remunera��o prevista na cl�usula 4.1, a CONTRATADA dever� encaminhar � CONTRATANTE a respectiva Nota Fiscal de Presta��o de Servi�os, acompanhada de relat�rio detalhado contendo a descri��o dos servi�os prestados, at� o dia 20 de cada m�s.</p>
<h4>Cl�usula Quinta - REAJUSTE ANUAL</h4>
<p>5.1 - Decorrido um prazo de 12 (doze) meses da presente contrata��o e, havendo interesse da CONTRATANTE na continuidade da presta��o dos servi�os da CONTRATADA, o valor da remunera��o mensalmente recebida dever� ser reajustado anualmente, tendo como �ndice indexador o IPCA, ou qualquer outro que vier oficialmente a substitu�-lo. A substitui��o do �ndice indexador do reajuste em quest�o dever� ser alvo da elabora��o de Aditivo contratual devidamente assinado pelas Partes.</p>
<h4>Cl�usula Sexta- TRIBUTOS</h4>
<p>6.1 - Todos os tributos federais, estaduais ou municipais que incidam sobre a presta��o dos servi�os objeto do presente Contrato, ser�o de exclusiva responsabilidade da CONTRATADA, cabendo-lhe apresentar os respectivos comprovantes de recolhimento sempre que solicitado pela CONTRATANTE.</p>
<h4>Cl�usula S�tima - RESPONSABILIDADE CIVIL</h4>
<p>7.1 - A CONTRATADA assume integral responsabilidade, independente de culpa, por todas e quaisquer perdas e danos que seus s�cios, empregados e prepostos e demais trabalhadores por ela contratados para a presta��o dos servi�os causarem, volunt�ria ou involuntariamente, � CONTRATANTE, bem como aos seus empregados e quaisquer terceiros lesados, at� o integral ressarcimento pelas perdas e danos causados.</p>
<h4>Cl�usula Oitava - CONFIDENCIALIDADE</h4>
<p>8.1 - A CONTRATADA, por si, por seus prepostos e empregados, obriga-se a manter absoluto sigilo, durante toda a vig�ncia do Contrato e pelo prazo de 5 (cinco) anos contados de seu encerramento, sobre todas as informa��es confidenciais, de uso exclusivo da CONTRATANTE, obtidas em raz�o do exerc�cio direto ou indireto de suas atividades.</p>
<p>8.2 - Para os fins do termo mencionado na cl�usula anterior, "Informa��o Confidencial" significa qualquer informa��o relacionada aos projetos e estudos da CONTRATANTE, incluindo, sem se limitar a: pesquisas, relat�rios, avalia��es e pareceres elaborados com base em qualquer Informa��o tida como confidencial pela CONTRATANTE, senhas, estrat�gias, segredos comerciais e propriedade intelectual, os quais a CONTRATADA possa ter acesso por e-mail, carta, correspond�ncia, telefone, conference call ou em reuni�es e encontros realizados em nome da CONTRATANTE.</p>
<p>8.3 - A CONTRATADA concorda que todos os segredos e informa��es confidenciais aos quais tenha tido acesso, em raz�o da presta��o dos servi�os ora contratados, s�o de propriedade da CONTRATANTE, obrigando-se a devolv�-las imediatamente � CONTRATANTE, quando da rescis�o do presente Contrato.</p>
<p>8.4 - Caso a CONTRATADA descumpra a obriga��o elencada na cl�usula oitava, arcar� com uma multa indenizat�ria em favor da CONTRATANTE, cujo valor ser� apurado pela CONTRATANTE, no momento do conhecimento da infra��o, a seu exclusivo crit�rio.</p>
<h4>Cl�usula Nona- RESCIS�O</h4>
<p>9.1 - A CONTRATANTE poder� rescindir o presente Contrato nas seguintes hip�teses:</p>
<p>9.1.1 - des�dia da CONTRATADA no cumprimento das obriga��es assumidas para com a CONTRATANTE e terceiros;</p>
<p>9.1.2 - caso a CONTRATADA pratique atos que atinjam a imagem comercial da CONTRATANTE perante terceiros;</p>
<p>9.1.3 - caso a CONTRATADA desrespeite as cl�usulas previstas no presente contrato;</p>
<p>9.1.4 - a qualquer tempo e por qualquer motivo, desde que comunique a CONTRATADA de tal inten��o, por escrito, com anteced�ncia m�nima de 30 (trinta) dias.</p>
<p>9.2 - A CONTRATADA poder� rescindir o presente Contrato nas seguintes circunst�ncias:</p>
<p>9.2.1 - quando a CONTRATANTE exigir da CONTRATADA atividade que exceda a presta��o dos servi�os objeto do presente contrato;</p>
<p>9.2.2 - caso a CONTRATANTE descumpra quaisquer das cl�usulas previstas no presente Contrato;</p>
<p>9.2.3 - caso haja decreta��o de fal�ncia, concordata, insolv�ncia ou recupera��o judicial da CONTRATANTE;</p>
<p>9.2.4 - por motivos de for�a maior que inviabilizem a continuidade da presta��o dos servi�os em quest�o;</p>
<p>9.2.5 - a qualquer tempo e por qualquer motivo, desde que comunique a CONTRATANTE de tal inten��o, por escrito, com anteced�ncia m�nima de 30 (trinta) dias.</p>
<p>9.3 - A rescis�o do presente Contrato n�o extingue os direitos e obriga��es que as Partes tenham entre si e perante terceiros, adquiridas anteriormente.</p>
<h4>Cl�usula D�cima - INDEPEND�NCIA ENTRE AS PARTES</h4>
<p>10.1 - A CONTRATADA � a �nica respons�vel pelas reclama��es trabalhistas, previdenci�rias, fiscais e securit�rias, incluindo-se aquelas decorrentes de modifica��es na legisla��o em vigor, relativamente aos seus empregados e prepostos, ou terceiros por ela contratados, envolvidos direta ou indiretamente na presta��o dos servi�os objeto do presente Contrato.</p>
<h4>Cl�usula D�cima Primeira - DISPOSI��ES GERAIS</h4>
<p>11.1 - Notifica��es: Todas as notifica��es e comunica��es relativas a este Contrato ser�o feitas atrav�s dos gestores das Partes e enviadas para os endere�os indicados no pre�mbulo do presente Contrato.</p>
<p>11.2 - Nova��o: O n�o exerc�cio, pelas Partes, de quaisquer dos direitos ou prerrogativas previstos neste Contrato, ou mesmo na legisla��o aplic�vel, ser� tido como ato de mera liberalidade, n�o constituindo altera��o ou nova��o das obriga��es ora estabelecidas, cujo cumprimento poder� ser exigido a qualquer tempo, independentemente de comunica��o pr�via � Parte.</p>
<p>11.3 - Caso Fortuito e For�a Maior: Nenhuma das Partes ser� respons�vel por descumprimento de suas obriga��es contratuais em conseq��ncia de caso fortuito ou for�a maior, nos termos da legisla��o em vigor, devendo, para tanto, comunicar a ocorr�ncia de tal fato de imediato � outra Parte e informar os efeitos danosos do evento.</p>
<p>Constatada a ocorr�ncia de caso fortuito ou de for�a maior, ficar�o suspensas, enquanto essa perdurar, as obriga��es que as Partes ficarem impedidas de cumprir.</p>
<p>11.4 - Subcontrata��o e Cess�o: � vedado � CONTRATADA a subcontrata��o ou cess�o, total ou parcial, dos direitos e obriga��es oriundos e/ou decorrentes deste Contrato, inclusive seus cr�ditos, sem a pr�via e expressa autoriza��o da CONTRATANTE.</p>
<p>11.5 - Aditivos: Este Contrato s� poder� ser alterado, em qualquer de suas disposi��es, mediante a celebra��o, por escrito, de termo aditivo contratual assinado por ambas as Partes.</p>
<h4>Cl�usula D�cima Segunda - FORO</h4>
<p>12.1 - Elegem as partes o Foro da Comarca do <?= $prestador['cidade'] ?>, Estado de  <?= $estados[strtoupper($prestador['estado'])] ?>, para dirimir quaisquer controv�rsias relacionadas ao presente Contrato, prevalecendo este sobre qualquer outro, por mais privilegiado que seja.</p>
<p>E por estarem assim justos e contratados, assinam o presente em duas vias de igual forma e teor, na presen�a de duas testemunhas, para que possa produzir todos os seus efeitos de direito.</p>
<p><?= $estados[strtoupper($prestador['estado'])] ?>, <?= date('d'); ?> de <?= $meses[str_pad(date('m'),2,'0')]; ?> de <?= date('Y'); ?>.</p>
<div class="f-left w-metade">
    <h4>Contratante</h4>
    <p>Testemunhas</p>
    <p>1.</p>
    <p>Nome: _____________________________</p>
    <p>RG:&nbsp;&nbsp;&nbsp; _____________________________</p>
</div>
<div class="f-left">
    <h4>Contratada</h4>
    <p>&nbsp;</p>
    <p>2.</p>
    <p>Nome: _____________________________</p>
    <p>RG:&nbsp;&nbsp;&nbsp; _____________________________</p>
</div>
<div class="clear"></div>