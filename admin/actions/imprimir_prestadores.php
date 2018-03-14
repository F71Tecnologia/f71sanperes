<?php
error_reporting(E_ALL);

include "../../conn.php";
include("../../funcoes.php");
include("../../wfunction.php");
include("../../classes/LogClass.php");
include("../../classes/EmpresaClass.php");
include("../prestadores/PrestadorServicoClass.php");
include("../prestadores/ImpostoAssocClass.php");

$charset = mysql_set_charset('utf8');

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : NULL;
$usuario = carregaUsuario();

$log = new Log();
$objPrestador = new PrestadorServicoClass();
$objEmpresa = (object) getEmpresaID($usuario['id_master']);
//print_array($objEmpresa);
if (!empty($_REQUEST['id_prestador'])) {
    $objPrestador->setId_prestador($_REQUEST['id_prestador']);
    if ($objPrestador->getPrestador()) {
        $objPrestador->getRowPrestador();
    }
}

switch ($action) {

    case 'encerramento' :
        //echo $objPrestador->getImprimir();exit;
        if($objPrestador->getImprimir() < 1){
            print "
            <script>
            alert(\"Você não pode imprimir este FECHAMENTO DE PROCESSO sem ter impresso a ABERTURA DE PROCESSO\");
            //window.close();
            </script>";
        }else{
            
            $result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_prestador[id_projeto]'");
            $row_projeto = mysql_fetch_array($result_projeto);

            $data = date("d/m/Y");
            
            $arrayImpressao['titulo'] = array(
                2 => array("TERMO DE ENCERRAMENTO DE  PROCESSO"),
                4 => array("Processo N&uacute;mero: {$objPrestador->getC_numero()}")
            );
            if($objPrestador->getAcompanhamento() == 2 || $objPrestador->getAcompanhamento() == 5){
                $arrayImpressao['titulo'][6][] = '<strong class="text-danger">NÃO APROVADO</strong>';
            } 
            
            $arrayImpressao['corpo'] = array(
                "<strong>ASSUNTO: {$objPrestador->getAssunto()}</strong>",
                "&nbsp",
                "<strong>CONTRATADA: </strong>{$objPrestador->getC_fantasia()} / {$objPrestador->getC_cnpj()}",
                "&nbsp",
                "<strong> ENCERRAMENTO: </strong>AP&Oacute;S  ASSINATURA DO CONTRATO E REGISTRO DO MESMO SER&Aacute; ABERTO O PROCESSO FINANCEIRO  PODENDO O PRESENTE SER ARQUIVADO",
                "&nbsp",
                "<strong>DATA: $data</strong>",
                "&nbsp",
                "&nbsp"
            );
            $arrayImpressao['assinatura'] = array(
                //"ASSINATURA E NOME DO EMPREGADO" => array('EU',true),
                "ASSINATURA E NOME DO RESPONSÁVEL" =>  array($objEmpresa->razao,false)
            );
            $arrayImpressao['espacamento'] = 100;
            $arrayImpressao['rodape'] = array(
                4 => array($objEmpresa->razao),
                6 => array(
                    "<small>$objEmpresa->endereco</small>",
                    "<small>$objEmpresa->cnpj</small>",
                    "<small>$objEmpresa->tel</small>"
                )
            );
            
            $data_b = date("Y-m-d");
            if($objPrestador->getImprimir() != 5){//AKI ELE CANCELA O PROCESSO SEM TER CONCLUÍDO OS PASSOS
                $objPrestador->setEncerrado_por($usuario['id_funcionario']);
                $objPrestador->setEncerrado_em($data_b);
                $objPrestador->setAcompanhamento(5);
            } else { // AKI O PROCESSO É TERMINADO DE FORMA CORRETA, ONDE TODOS OS PASSOS FORAM SEGUIDOS
                $objPrestador->setEncerrado_por($usuario['id_funcionario']);
                $objPrestador->setEncerrado_em($data_b);
                $objPrestador->setAcompanhamento(4);
                $objPrestador->setImprimir(6);
            }
            $objPrestador->updatePrestador();
        }
    break;
    
    case 'abertura' :
        $data = date("d/m/Y");

        $arrayImpressao['titulo'] = array(
            2 => array("$objEmpresa->razao"),
            4 => array("UF RESPONSÁVEL<br><b>ILR-RJ</b>")
        );
        $arrayImpressao['corpo'] = array(
            "<table>
                <tr>
                    <td class='text-left'>
                        <p>TITULO: </p>
                        <p class='text-bold'>ABERTURA DE PROCESSO</p>
                    </td>
                    <td class='text-center'>
                        <p>CODIFICAÇÃO: </p>
                        <p class='text-bold'>NOR-2000-001</p>
                    </td>
                    <td  class='text-center'>
                        <p>VERSÃO: </p>
                        <p class='text-bold'>01</p>
                    </td>
                    <td  class='text-right'>
                        <p>PÁGINA: </p>
                        <p class='text-bold'>1 / 1</p>
                    </td>
                </tr>
            </table>",
            "&nbsp",
            "ASSUNTO: {$objPrestador->getAssunto()}",
            "&nbsp",
            "&nbsp",
            "<strong>DATA: ___/___/______</strong>",
            "&nbsp",
            "PROCESSO N&ordm;: {$objPrestador->getC_numero()}",
            "&nbsp"
        );
        $arrayImpressao['assinatura'] = array(
            //"ASSINATURA E NOME DO EMPREGADO" => array('EU',true),
            "" =>  array($usuario['nome'],false)
        );
        $arrayImpressao['espacamento'] = 70;
        $arrayImpressao['rodape'] = array(
            4 => array("<strong>EXEMPLAR Nº 00 - Vigência $data</strong>", "<strong>PROIBIDA A REPRODUÇÃO</strong>"),
            6 => array(
                "<small>$objEmpresa->razao</small>",
                "<small>$objEmpresa->endereco</small>",
                "<small>$objEmpresa->cnpj</small>",
                "<small>$objEmpresa->tel</small>"
            )
        );
        $data_b = date("Y-m-d");
        if ($objPrestador->getImprimir() == 0) {
            $objPrestador->setImprimir(1);
            $objPrestador->updatePrestador();
        }
    break;
    
    case 'contrato' :
        
        $sql = "SELECT A.id_prestador, A.numero, A.id_regiao,  A.especialidade, D.nome AS nome_medico, D.crm,A.prestador_tipo, C.cep AS cep_contratante, C.logradouro AS logradouro_contratante, C.bairro AS bairro_contratante, C.municipio AS municipio_contratante, C.uf AS uf_contratante, 
                A.c_responsavel AS prestador_responsavel, A.c_rg AS prestador_rg, A.c_cpf AS prestador_cpf, A.contratante,A.imprimir, A.cnpj AS cnpj_contratante, A.endereco AS endereco_contratante, A.c_fantasia AS nome_fantasia, A.c_cnpj AS cnpj, A.c_endereco AS endereco, A.co_municipio AS municipio, A.contratado_em, A.valor_limite, A.nome_banco, A.agencia, A.conta,
                 B.cidade, B.estado, B.nome AS nome_projeto, DAY(A.contratado_em) AS dia_contratado, MONTH(A.contratado_em) AS mes_contratado, YEAR(A.contratado_em) AS ano_contratado
                FROM prestadorservico AS A
                LEFT JOIN projeto AS B ON(A.id_projeto=B.id_projeto)
                LEFT JOIN master AS C ON(B.id_master=C.id_master)
                LEFT JOIN prestador_medico AS D ON(D.id_prestador = A.id_prestador AND D.principal=1)
                WHERE A.id_prestador={$objPrestador->getId_prestador()} LIMIT 1";
        $prestador = mysql_fetch_assoc(mysql_query($sql));

        //define o tipo de contrato
        if($prestador['prestador_tipo'] != 9){
            header('Location: /intranet/processo/contrato.php?regiao='.$objPrestador->getId_regiao().'&prestador='.$objPrestador->getId_prestador());
            exit;
        }
        
        $contratado_em = explode('-', $prestador['contratado_em']);
        $meses = array("", '01' => "janeiro", '02' => "fevereiro", '03' => "março", '04' => "abril", '05' => "maio", '06' => "junho", '07' => "julho", '08' => "agosto", '09' => "setembro", '10' => "outubro", '11' => "novembro", '12' => "dezembro");

        $medicos_pj = array();
        //var_dump($prestador['prestador_tipo']);
        
        $sql = "SELECT A.*, B.id_curso, B.nome AS nome_curso, B.valor_hora, B.salario salario  FROM terceirizado AS A LEFT JOIN curso AS B ON(A.id_curso=B.id_curso) WHERE A.id_prestador = '{$objPrestador->getId_prestador()}' ";    //AND A.contrato_medico=1
        $query = mysql_query($sql);
        $medicos_pj = array();
        while ($row = mysql_fetch_array($query)) {

            $medicos_pj[$row['id_terceirizado']]['id_terceirizado'] = $row['id_terceirizado'];
            $medicos_pj[$row['id_terceirizado']]['nome'] = $row['nome'];
            $medicos_pj[$row['id_terceirizado']]['tel'] = $row['tel_fixo'];
            $medicos_pj[$row['id_terceirizado']]['cpf'] = $row['cpf'];
            $medicos_pj[$row['id_terceirizado']]['crm'] = $row['carteira_conselho'];
            $medicos_pj[$row['id_terceirizado']]['id_curso'] = $row['id_curso'];
            $medicos_pj[$row['id_terceirizado']]['valor_hora'] = $row['valor_hora'];
            $medicos_pj[$row['id_terceirizado']]['salario'] = $row['salario'];
            $medicos_pj[$row['id_terceirizado']]['nome_curso'] = $row['nome_curso'];
        }

        $medicos_funcoes = array();
        foreach($medicos_pj as $medico){
            $medicos_funcoes[$medico['id_curso']]['salario'] = $medico['salario'];
            $medicos_funcoes[$medico['id_curso']]['nome_curso'] = $medico['nome_curso'];
            $medicos_funcoes[$medico['id_curso']]['valor_hora'] = $medico['valor_hora'];
        }

        $sql = mysql_query("SELECT * FROM prestador_medico;");
        $result = mysql_query($sql);
        $medicos = array();
        while($row = mysql_fetch_array($result)){
            $medicos[] = $row;
        }
        
        $total_cont = count($medicos_funcoes);
        $final_especialidade = ($total_cont > 1) ? 's' : '';
        
        $cont = 1;
        foreach ($medicos_funcoes as $funcao) {
            $final = ($total_cont == $cont) ? '. ' : (($total_cont == ($cont + 1)) ? ' e ' : ', ');
            $funcoes .= strtoupper($funcao['nome_curso']) . $final;
            $cont++;
        }
        
        $cont = 1;
        $total_cont = count($medicos_pj);
        $nomes = (count($medicos_pj) > 1) ? "os médicos " : "o médico ";
        foreach ($medicos_pj as $medico) {
            $final = ($total_cont == $cont) ? '. ' : (($total_cont == ($cont + 1)) ? ' e ' : '; ');
            $nomes .= ' Dr.' . strtoupper($medico['nome']) . ', portador do CRM n° ' . $medico['crm'] . $final;
            $cont++;
        }
        
        if (isset($_REQUEST['horista']) && $_REQUEST['horista'] == 1) {
            $valoresHorista = "por hora de ";
            $valor = (!empty($_REQUEST['valor'])) ? $_REQUEST['valor'] : $medico['valor_hora'];
        } else {
            $valoresHorista = "mensal de ";
            $valor = (!empty($_REQUEST['valor'])) ? $_REQUEST['valor'] : $medico['salario'];
        }
        foreach ($medicos_funcoes as $medico) {
            $valoresHorista .= 'R$ ' . number_format($valor, 2, ',', '.') . ' (' . trim(valor_extenso(!empty($valor) ? $valor : '0.00' )) . ') na especialidade de ' . $medico['nome_curso'] . '; ';
        }
        
        $arrayImpressao['titulo'] = array(
            2 => array("CONTRATO DE PRESTAÇÃO DE SERVIÇOS MÉDICOS"),
        );
        $arrayImpressao['corpo'] = array(
            "Pelo presente instrumento particular, de um lado, <strong>".trim($prestador['contratante'])."</strong>, 
            pessoa jurídica de direito privado, devidamente inscrita no CNPJ sob o nº ".$prestador['cnpj_contratante'].", 
            com sede na cidade de ".trim($prestador['municipio_contratante'])." - ".trim($prestador['uf_contratante']).", 
            localizada na ".trim($prestador['logradouro_contratante']).", CEP ".trim($prestador['cep_contratante']).", 
            doravante denominada <strong>CONTRATANTE</strong>, e de outro, <strong>".trim($prestador['nome_fantasia'])."</strong>, 
            pessoa jurídica de direito privado, CNPJ ".trim($prestador['cnpj']).", com sede na Rua ".trim(strtoupper($prestador['endereco'])).", 
            neste ato representada, na forma de seu Contrato Social, por ".trim(strtoupper($prestador['prestador_responsavel'])).", 
            portador da Cédula de Identidade RG nº ".trim(strtoupper($prestador['prestador_rg']))." e inscrito no CPF/MF sob o nº 
            ".trim(strtoupper($prestador['prestador_cpf'])).",  doravante denominada <strong>CONTRATADA</strong>;",
            "Firmam entre si, o presente contrato de prestação de serviços médicos, mediante as seguintes cláusulas e condições:",
            "<strong>Cláusula Primeira - DO OBJETO</strong>",
            "1.1 - O objeto do presente Contrato refere-se à disponibilização de mão de obra especializada na área médica para prestação de serviços, em parceira com a Prefeitura Municipal de {$prestador['municipio']}.",
            "1.2 - A CONTRATANTE mediante Contrato de Gestão celebrado com a Prefeitura Municipal de {$prestador['municipio']}, pelo Departamento Municipal de Saúde, devendo em virtude disto, atender aos padrões fixados pela política de atendimento público à saúde, comprometendo-se a CONTRATADA a colaborar para atingir tais objetivos, com os serviços descritos na cláusula 1.1.",
            "Parágrafo único - Os serviços mencionados no caput, prestados pela CONTRATADA, serão realizados na$final_especialidade área$final_especialidade de especialidade$final_especialidade de $funcoes",
            "<strong>Cláusula Segunda - DA PRESTAÇÃO DE SERVIÇOS</strong>",
            "2.1 - Os serviços contratados poderão ser executados pelos profissionais médicos, sócios quotistas da empresa CONTRATADA ou por outro sócio ou empregado, desde que de reconhecida idoneidade e qualificação técnica especializada.",
            "2.2 - A CONTRATADA indica para a realização dos serviços contratados, $nomes",
            "2.3 - Na hipótese da CONTRATADA substituir o profissional indicado no item 2.2, deverá por escrito comunicar o nome do novo profissional à CONTRATANTE, com uma antecedência de 30 (trinta) dias antes da substituição. O prazo poderá ser alterado desde que de comum acordo entre as partes.",
            "2.4 - A CONTRATADA deverá prestar os serviços objeto da presente contratação, dentro das dependências da CONTRATANTE, em caráter não habitual.",
            "2.5 - Visando atender a Política de Atendimento Público à Saúde, a CONTRATADA se compromete a prestar seus serviços de acordo com os agendamentos dos procedimentos realizados pela CONTRATANTE.",
            "2.6 - Quando por qualquer razão, a CONTRATADA por meio de seu profissional indicado, não puder atender os serviços contratados por algum período, se compromete a comunicar a CONTRATANTE com 30 (trinta) dias de antecedência, para que haja tempo hábil para a contratação de outra empresa especializada.",
            "2.6.1 - Na hipótese de não haver a prévia comunicação, deverá a CONTRATADA pagar uma multa no valor do dobro do plantão, que será descontada na próxima Nota Fiscal.",
            "2.7 - Caso haja considerável aumento no volume de serviços a serem prestados, a CONTRATANTE se compromete a comunicar a CONTRATADA, com 30 (trinta) dias de antecedência, para que esta se adeque às necessidades da demanda.",
            "<strong>Cláusula Terceira - PRAZO E RESCISÃO</strong>",
            "3.1 - O presente Contrato vigorará até o término do Contrato de Gestão celebrado com a Prefeitura Municipal de {$prestador['municipio']}, iniciando sua vigência a partir do dia {$prestador['dia_contratado']} de ".$meses[str_pad($prestador['mes_contratado'], 2, '0', STR_PAD_LEFT)]." de {$prestador['ano_contratado']}, podendo ser rescindido por qualquer das Partes, a qualquer momento, sem justo motivo, desde que haja prévia comunicação expressa, com antecedência mínima de 30 (trinta) dias.",
            "3.2 - A CONTRATANTE poderá rescindir o presente Contrato nas seguintes hipóteses:",
            "3.2.1 - desídia da CONTRATADA no cumprimento das obrigações assumidas para com a CONTRATANTE e terceiros;",
            "3.2.2 - caso haja descumprimento do Código de Ética Médica, à Moral, Ética e boas práticas dos serviços de saúde;",
            "3.2.3 - caso a CONTRATADA desrespeite as cláusulas previstas no presente contrato;",
            "3.2.4 - caso a CONTRATADA por si ou por seus empregados, prepostos ou sócios, por qualquer ato, meio ou forma, interromper ou tentar suspender, sem motivo justo e legal, ou prejudicar a eficaz e continua prestação de serviços;",
            "3.2.5 - a qualquer tempo e por qualquer motivo, desde que comunique a CONTRATADA de tal intenção, por escrito, com antecedência mínima de 30 (trinta) dias.",
            "3.3 - A CONTRATADA poderá rescindir o presente Contrato nas seguintes circunstâncias:",
            "3.3.1 - quando a CONTRATANTE exigir da CONTRATADA atividade que exceda a prestação dos serviços objeto do presente contrato;",
            "3.3.2 - caso a CONTRATANTE descumpra quaisquer das cláusulas previstas no presente Contrato;",
            "3.3.3 - caso haja decretação de falência, concordata, insolvência ou recuperação judicial da CONTRATANTE;",
            "3.3.4 - por motivos de força maior que inviabilizem a continuidade da prestação dos serviços em questão;",
            "3.3.5 - a qualquer tempo e por qualquer motivo, desde que comunique a CONTRATANTE de tal intenção, por escrito, com antecedência mínima de 60 (sessenta) dias.",
            "3.4 - A rescisão do presente Contrato não extingue os direitos e obrigações que as Partes tenham entre si e perante terceiros, adquiridas anteriormente.",
            "<strong>Cláusula Quarta - REMUNERAÇÃO</strong>",
            "4.1 - Em pagamento aos serviços prestados será pago à CONTRATADA o valor $valoresHorista de efetivo serviço prestado até o quinto dia útil do mês subsequente ao vencido.",
            "Parágrafo Primeiro - Para que a CONTRATANTE possa proceder ao pagamento da remuneração prevista na cláusula 4.1, a CONTRATADA deverá encaminhar à CONTRATANTE a respectiva Nota Fiscal de Prestação de Serviços, acompanhada de relatório detalhado contendo a descrição dos serviços prestados, até o dia 02 de cada mês, assim como as certidões negativas de FGTS, Previdenciária, Divida Ativa da União e Trabalhista.",
            "<strong>Cláusula Quinta- REAJUSTE ANUAL</strong>",
            "5.1 - Decorrido um prazo de 12 (doze) meses da presente contratação e, havendo interesse da CONTRATANTE na continuidade da prestação dos serviços da CONTRATADA, o valor da remuneração será reajustado de comum acordo.",
            "<strong>Cláusula Sexta- TRIBUTOS</strong>",
            "6.1 - Todos os tributos federais, estaduais ou municipais que incidam sobre a prestação dos serviços objeto do presente Contrato, serão de exclusiva responsabilidade da CONTRATADA, cabendo-lhe apresentar os respectivos comprovantes de recolhimento sempre que solicitado pela CONTRATANTE.",
            "<strong>Cláusula Sétima - CONFIDENCIALIDADE</strong>",
            "7.1 - A CONTRATADA, por si, por seus prepostos e empregados, obriga-se a manter absoluto sigilo, durante toda a vigência do Contrato e pelo prazo de 5 (cinco) anos contados de seu encerramento, sobre todas as informações confidenciais, de uso exclusivo da CONTRATANTE, obtidas em razão do exercício direto ou indireto de suas atividades.",
            "7.2 - Para os fins do termo mencionado na cláusula anterior, \"Informação Confidencial\" significa qualquer informação relacionada aos projetos e estudos da CONTRATANTE, incluindo, sem se limitar a: \n\r
            pesquisas, relatórios, avaliações e pareceres elaborados com base em qualquer Informação tida como confidencial pela CONTRATANTE, senhas, estratégias, segredos comerciais e propriedade intelectual, 
            os quais a CONTRATADA possa ter acesso por e-mail, carta, correspondência, telefone, conference call ou em reuniões e encontros realizados em nome da CONTRATANTE.",
            "7.3 - A CONTRATADA concorda que todos os segredos e informações confidenciais aos quais tenha tido acesso, em razão da prestação dos serviços ora contratados, são de propriedade da CONTRATANTE, obrigando-se a devolvê-las imediatamente à CONTRATANTE, quando da rescisão do presente Contrato.",
            "7.4 - Caso a CONTRATADA descumpra a obrigação elencada na cláusula sétima, arcará com uma multa indenizatória em favor da CONTRATANTE, cujo valor será apurado pela CONTRATANTE, no momento do conhecimento da infração, a seu exclusivo critério.",
            "<strong>Cláusula Oitava - INDEPENDÊNCIA ENTRE AS PARTES</strong>",
            "8.1 - A CONTRATADA é a única responsável pelas reclamações trabalhistas, previdenciárias, fiscais e securitárias, incluindo-se aquelas decorrentes de modificações na legislação em vigor, relativamente aos seus empregados e prepostos, ou terceiros por ela contratados, envolvidos direta ou indiretamente na prestação dos serviços objeto do presente Contrato.",
            "8.2 - O presente contrato não induz exclusividade entre as partes, podendo as mesmas se relacionar ou contratar terceiros, inclusive com o mesmo objeto.",
            "<strong>Cláusula Nona - DA RESPONSABILIDADE</strong>",
            "9.1 - A CONTRATADA por si, bem como solidariamente na pessoa de seus sócios, prepostos, empregados, agentes e colaboradores, se responsabilizam pela qualidade dos serviços prestados, respondendo em caso de descumprimento, pelos prejuízos causados à CONTRATANTE e à terceiros, inclusive por erro médico.",
            "9.2 - Como forma de identificação da experiência e qualificação, a CONTRATADA deverá apresentar local de atendimento, consultórios e referências de trabalho onde vem sendo prestado.",
            "<strong>Cláusula Décima - DISPOSIÇÕES GERAIS</strong>",
            "10.1 - Notificações: Todas as notificações e comunicações relativas a este Contrato serão feitas através dos gestores das Partes e enviadas para os endereços indicados no preâmbulo do presente Contrato.",
            "10.2 - Novação: O não exercício, pelas Partes, de quaisquer dos direitos ou prerrogativas previstos neste Contrato, ou mesmo na legislação aplicável, será tido como ato de mera liberalidade, não constituindo alteração ou novação das obrigações ora estabelecidas, cujo cumprimento poderá ser exigido a qualquer tempo, independentemente de comunicação prévia à Parte.",
            "10.3 - Caso Fortuito e Força Maior: Nenhuma das Partes será responsável por descumprimento de suas obrigações contratuais em conseqüência de caso fortuito ou força maior, nos termos da legislação em vigor, devendo, para tanto, comunicar a ocorrência de tal fato de imediato à outra Parte e informar os efeitos danosos do evento. ",
            "Parágrafo único - Constatada a ocorrência de caso fortuito ou de força maior, ficarão suspensas, enquanto essa perdurar, as obrigações que as Partes ficarem impedidas de cumprir.",
            "10.4 - Subcontratação e Cessão: É vedado à CONTRATADA a subcontratação ou cessão, total ou parcial, dos direitos e obrigações oriundos e/ou decorrentes deste Contrato, inclusive seus créditos, sem a prévia e expressa autorização da CONTRATANTE.",
            "10.5 - Aditivos: Este Contrato só poderá ser alterado, em qualquer de suas disposições, mediante a celebração, por escrito, de termo aditivo contratual assinado por ambas as Partes.",
            "<strong>Cláusula Décima Primeira - FORO</strong>",
            "11.1 - Elegem as partes o Foro da Comarca de Rio de Janeiro, Estado de Rio de Janeiro, para dirimir quaisquer controvérsias relacionadas ao presente Contrato, prevalecendo este sobre qualquer outro, por mais privilegiado que seja.",
            "&nbsp",
            "<strong>Rio de Janeiro, {$prestador['dia_contratado']} de ".$meses[str_pad($prestador['mes_contratado'], 2, '0', STR_PAD_LEFT)]." de {$prestador['ano_contratado']}.</strong>",
            "&nbsp",
            "<strong>Testemunhas</srong>",
            "1.",
            "Nome: _____________________________",
            "RG:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; _____________________________",
            "2.",
            "Nome: _____________________________",
            "RG:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; _____________________________"
            
        );
//        $arrayImpressao['assinatura'] = array(
//            //"ASSINATURA E NOME DO EMPREGADO" => array('EU',true),
//            "" =>  array($usuario['nome'],false)
//        );
//        $arrayImpressao['espacamento'] = 70;
//        $arrayImpressao['rodape'] = array(
//            4 => array($objEmpresa->razao),
//            6 => array(
//                "<small>$objEmpresa->endereco</small>",
//                "<small>$objEmpresa->cnpj</small>",
//                "<small>$objEmpresa->tel</small>"
//            )
//        );
            
        $data_b = date("Y-m-d");
        if ($objPrestador->getImprimir() == 0) {
            $objPrestador->setImprimir(1);
            $objPrestador->updatePrestador();
        }
    break;
    
    default:
        
        echo 'action: ' . $action;
        print_array($_REQUEST);
        break;
} 

 ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="ISO-8859-9">
        <title>:: Intranet ::</title>
        <link href="../../favicon.png" rel="shortcut icon">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/style-print.css" rel="stylesheet" media="all">
    </head>
    <body>
        <div class="no-print">
            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container-fluid">
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-3">
                        <div class="text-center">
                            <button type="button" id="voltar" class="btn btn-default navbar-btn" onclick="window.close()"><i class="fa fa-reply"></i> Voltar</button>
                            <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <div class="pagina">
            <div class="text-center">
                <img class="" src="../../imagens/logomaster<?=$objEmpresa->id_master?>.gif" alt="log" width="110" height="79">
            </div>

            <?php if(count($arrayImpressao['titulo']) > 0){
                foreach ($arrayImpressao['titulo'] as $h => $arrayTitulo) { 
                    foreach ($arrayTitulo as $titulo) { ?>
                        <h<?= $h ?> class="text-center text-uppercase"><?= $titulo ?></h<?= $h ?>>
                    <?php }
                } ?>
                <hr>
            <?php } ?>
            <br>
            <?php if(count($arrayImpressao['corpo']) > 0){
                foreach ($arrayImpressao['corpo'] as $corpo) { ?>
                    <p class="text-justify"><?= $corpo ?></p>
                <?php } ?>
                <hr>
            <?php } ?>
            <?php foreach ($arrayImpressao['assinatura'] as $key => $assinatura) { ?>
                <p class="text-center text-bold"><?= $key ?></p>
                <p class="text-center">__________________________________________________</p>
                <p class="text-center text-bold text-uppercase" style="font-family: 'Courier New', Courier, monospace;">(<?=str_replace(' ','&nbsp;',str_pad($assinatura[0], 50, ' ', STR_PAD_BOTH))?>)</p>
                <?php if($assinatura[1]){ ?><br><p class="text-left text-bold">Ciente em ____/____/____</p><?php } ?>
                <hr>
            <?php } ?>
            <br>
            <footer style="margin-top: <?=$arrayImpressao['espacamento']?>px;">
            <?php if(count($arrayImpressao['rodape']) > 0){
                foreach ($arrayImpressao['rodape'] as $h => $arrayRodape) { 
                    foreach ($arrayRodape as $rodape) { ?>
                        <h<?= $h ?> class="text-center"><?= $rodape ?></h<?= $h ?>>
                    <?php }
                }
            } ?>
            </footer>
        </div>
        <!-- javascript aqui -->
        <script src="../../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../../resources/js/print.js" type="text/javascript"></script>
    </body>
</html>