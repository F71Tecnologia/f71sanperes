<h2 class="titulo">CONTRATO DE PRESTAÇÃO DE SERVIÇOS</h2>            
<p>Pelo presente instrumento particular, de um lado o <strong><?= $prestador['contratante']; ?></strong>, pessoa jurídica de direito privado, inscrito no CNPJ sob o nº <?= $prestador['cnpj_contratante']; ?>, localizado na <?= trim(strtoupper($prestador['endereco_contratante'] . ' ' . $prestador['bairro_contratante'] . ' ' . $prestador['cidade_contratante'] . ' ' . $prestador['estado_contratante'])); ?>, doravante denominado <strong>CONTRATANTE</strong>, e de outro lado, <strong><?= trim($prestador['nome_fantasia']); ?></strong>, pessoa jurídica de direito privado, inscrito no CNPJ sob o n.º <?= trim($prestador['cnpj']); ?>, com sede na <?= trim(strtoupper($prestador['endereco'])); ?>, doravante denominada <strong>CONTRATADA;</strong></p>
<p>Firmam entre si, o presente contrato de prestação de serviços, mediante as seguintes cláusulas e condições:</p>
<h4>Cláusula Primeira - OBJETO</h4>
<p>1.1 - O objeto do presente Contrato refere-se à disponibilização de mão de obra especializada na área médica para prestação de plantões médicos na lotação <?= $prestador['municipio']; ?>, em parceira com a Prefeitura de <?= $prestador['municipio']; ?>.</p>
<h4>Cláusula Segunda - PRAZO</h4>
<p>2.1 - O presente Contrato será por prazo indeterminado, iniciando sua vigência a partir do dia <?= $contratado_em['2']; ?> de <?= $meses[$contratado_em[1]]; ?> de <?= $contratado_em[0]; ?>, podendo ser rescindido por qualquer das Partes, a qualquer momento, sem justo motivo, desde que haja prévia comunicação expressa, com antecedência mínima de 30 (trinta) dias.</p>
<h4>Cláusula Terceira - DA PRESTAÇÃO DE SERVIÇOS</h4>
<p>3.1 - A CONTRATADA prestará os serviços objeto do presente Contrato de forma autônoma e sem qualquer vínculo de natureza trabalhista, previdenciária e tributária;</p>
<p>3.2 - A CONTRATADA deverá prestar os serviços objeto da presente contratação, dentro das dependências da CONTRATANTE, em caráter não habitual.</p>
<h4>Cláusula Quarta - REMUNERAÇÃO</h4>
<p>4.1 - Em remuneração pelos serviços profissionais ora contratados, será devida a importância fixa mensal de R$ <?= !empty($prestador['valor_limite']) ? number_format($prestador['valor_limite'], 2, ',', '.') : NULL; ?> (<?= valor_extenso(!empty($prestador['valor_limite']) ? $prestador['valor_limite'] : '0.00' ); ?>) a serem pagos via depósito em conta corrente de titularidade da CONTRATADA, cujos dados são os seguintes:</p>
<p>BANCO <?= $prestador['nome_banco'] ?></p>
<p>Agência <?= $prestador['agencia'] ?></p>
<p>Conta Corrente <?= $prestador['conta'] ?></p>
<p>Titular <?= $prestador['nome_fantasia']; ?></p>
<p>CNPJ  <?= $prestador['cnpj']; ?></p>
<p>Parágrafo Primeiro - Para que a CONTRATANTE possa proceder ao pagamento da remuneração prevista na cláusula 4.1, a CONTRATADA deverá encaminhar à CONTRATANTE a respectiva Nota Fiscal de Prestação de Serviços, acompanhada de relatório detalhado contendo a descrição dos serviços prestados, até o dia 20 de cada mês.</p>
<h4>Cláusula Quinta - REAJUSTE ANUAL</h4>
<p>5.1 - Decorrido um prazo de 12 (doze) meses da presente contratação e, havendo interesse da CONTRATANTE na continuidade da prestação dos serviços da CONTRATADA, o valor da remuneração mensalmente recebida deverá ser reajustado anualmente, tendo como índice indexador o IPCA, ou qualquer outro que vier oficialmente a substituí-lo. A substituição do índice indexador do reajuste em questão deverá ser alvo da elaboração de Aditivo contratual devidamente assinado pelas Partes.</p>
<h4>Cláusula Sexta- TRIBUTOS</h4>
<p>6.1 - Todos os tributos federais, estaduais ou municipais que incidam sobre a prestação dos serviços objeto do presente Contrato, serão de exclusiva responsabilidade da CONTRATADA, cabendo-lhe apresentar os respectivos comprovantes de recolhimento sempre que solicitado pela CONTRATANTE.</p>
<h4>Cláusula Sétima - RESPONSABILIDADE CIVIL</h4>
<p>7.1 - A CONTRATADA assume integral responsabilidade, independente de culpa, por todas e quaisquer perdas e danos que seus sócios, empregados e prepostos e demais trabalhadores por ela contratados para a prestação dos serviços causarem, voluntária ou involuntariamente, à CONTRATANTE, bem como aos seus empregados e quaisquer terceiros lesados, até o integral ressarcimento pelas perdas e danos causados.</p>
<h4>Cláusula Oitava - CONFIDENCIALIDADE</h4>
<p>8.1 - A CONTRATADA, por si, por seus prepostos e empregados, obriga-se a manter absoluto sigilo, durante toda a vigência do Contrato e pelo prazo de 5 (cinco) anos contados de seu encerramento, sobre todas as informações confidenciais, de uso exclusivo da CONTRATANTE, obtidas em razão do exercício direto ou indireto de suas atividades.</p>
<p>8.2 - Para os fins do termo mencionado na cláusula anterior, "Informação Confidencial" significa qualquer informação relacionada aos projetos e estudos da CONTRATANTE, incluindo, sem se limitar a: pesquisas, relatórios, avaliações e pareceres elaborados com base em qualquer Informação tida como confidencial pela CONTRATANTE, senhas, estratégias, segredos comerciais e propriedade intelectual, os quais a CONTRATADA possa ter acesso por e-mail, carta, correspondência, telefone, conference call ou em reuniões e encontros realizados em nome da CONTRATANTE.</p>
<p>8.3 - A CONTRATADA concorda que todos os segredos e informações confidenciais aos quais tenha tido acesso, em razão da prestação dos serviços ora contratados, são de propriedade da CONTRATANTE, obrigando-se a devolvê-las imediatamente à CONTRATANTE, quando da rescisão do presente Contrato.</p>
<p>8.4 - Caso a CONTRATADA descumpra a obrigação elencada na cláusula oitava, arcará com uma multa indenizatória em favor da CONTRATANTE, cujo valor será apurado pela CONTRATANTE, no momento do conhecimento da infração, a seu exclusivo critério.</p>
<h4>Cláusula Nona- RESCISÃO</h4>
<p>9.1 - A CONTRATANTE poderá rescindir o presente Contrato nas seguintes hipóteses:</p>
<p>9.1.1 - desídia da CONTRATADA no cumprimento das obrigações assumidas para com a CONTRATANTE e terceiros;</p>
<p>9.1.2 - caso a CONTRATADA pratique atos que atinjam a imagem comercial da CONTRATANTE perante terceiros;</p>
<p>9.1.3 - caso a CONTRATADA desrespeite as cláusulas previstas no presente contrato;</p>
<p>9.1.4 - a qualquer tempo e por qualquer motivo, desde que comunique a CONTRATADA de tal intenção, por escrito, com antecedência mínima de 30 (trinta) dias.</p>
<p>9.2 - A CONTRATADA poderá rescindir o presente Contrato nas seguintes circunstâncias:</p>
<p>9.2.1 - quando a CONTRATANTE exigir da CONTRATADA atividade que exceda a prestação dos serviços objeto do presente contrato;</p>
<p>9.2.2 - caso a CONTRATANTE descumpra quaisquer das cláusulas previstas no presente Contrato;</p>
<p>9.2.3 - caso haja decretação de falência, concordata, insolvência ou recuperação judicial da CONTRATANTE;</p>
<p>9.2.4 - por motivos de força maior que inviabilizem a continuidade da prestação dos serviços em questão;</p>
<p>9.2.5 - a qualquer tempo e por qualquer motivo, desde que comunique a CONTRATANTE de tal intenção, por escrito, com antecedência mínima de 30 (trinta) dias.</p>
<p>9.3 - A rescisão do presente Contrato não extingue os direitos e obrigações que as Partes tenham entre si e perante terceiros, adquiridas anteriormente.</p>
<h4>Cláusula Décima - INDEPENDÊNCIA ENTRE AS PARTES</h4>
<p>10.1 - A CONTRATADA é a única responsável pelas reclamações trabalhistas, previdenciárias, fiscais e securitárias, incluindo-se aquelas decorrentes de modificações na legislação em vigor, relativamente aos seus empregados e prepostos, ou terceiros por ela contratados, envolvidos direta ou indiretamente na prestação dos serviços objeto do presente Contrato.</p>
<h4>Cláusula Décima Primeira - DISPOSIÇÕES GERAIS</h4>
<p>11.1 - Notificações: Todas as notificações e comunicações relativas a este Contrato serão feitas através dos gestores das Partes e enviadas para os endereços indicados no preâmbulo do presente Contrato.</p>
<p>11.2 - Novação: O não exercício, pelas Partes, de quaisquer dos direitos ou prerrogativas previstos neste Contrato, ou mesmo na legislação aplicável, será tido como ato de mera liberalidade, não constituindo alteração ou novação das obrigações ora estabelecidas, cujo cumprimento poderá ser exigido a qualquer tempo, independentemente de comunicação prévia à Parte.</p>
<p>11.3 - Caso Fortuito e Força Maior: Nenhuma das Partes será responsável por descumprimento de suas obrigações contratuais em conseqüência de caso fortuito ou força maior, nos termos da legislação em vigor, devendo, para tanto, comunicar a ocorrência de tal fato de imediato à outra Parte e informar os efeitos danosos do evento.</p>
<p>Constatada a ocorrência de caso fortuito ou de força maior, ficarão suspensas, enquanto essa perdurar, as obrigações que as Partes ficarem impedidas de cumprir.</p>
<p>11.4 - Subcontratação e Cessão: É vedado à CONTRATADA a subcontratação ou cessão, total ou parcial, dos direitos e obrigações oriundos e/ou decorrentes deste Contrato, inclusive seus créditos, sem a prévia e expressa autorização da CONTRATANTE.</p>
<p>11.5 - Aditivos: Este Contrato só poderá ser alterado, em qualquer de suas disposições, mediante a celebração, por escrito, de termo aditivo contratual assinado por ambas as Partes.</p>
<h4>Cláusula Décima Segunda - FORO</h4>
<p>12.1 - Elegem as partes o Foro da Comarca do <?= $prestador['cidade'] ?>, Estado de  <?= $estados[strtoupper($prestador['estado'])] ?>, para dirimir quaisquer controvérsias relacionadas ao presente Contrato, prevalecendo este sobre qualquer outro, por mais privilegiado que seja.</p>
<p>E por estarem assim justos e contratados, assinam o presente em duas vias de igual forma e teor, na presença de duas testemunhas, para que possa produzir todos os seus efeitos de direito.</p>
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