<?php



if(empty($_COOKIE['logado'])){

print "Efetue o Login<br><a href='login.php'>Logar</a> ";

}else{




/*
$radio1 = $_REQUEST['radio1'];

$radio2 = $_REQUEST['radio2'];

$radio3 = $_REQUEST['radio3'];

$radio4 = $_REQUEST['radio4'];

$radio5 = $_REQUEST['radio5'];

$radio6 = $_REQUEST['radio6'];
*/




switch ($radio1) {

    case 1:

    $msg1 = "O funcionário demonstrou competência apenas para cumprir sua rotina, dentro do que era esperado. O funcionário não realizou tarefas ou não teve a iniciativa de ações que demonstrassem claramente sua competência.";

    break;



    case 2:

	$msg1 = "Só você como superior imediato teve condições de avaliar os conhecimentos, habilidades e competência demonstrados pelo funcionário no período. O funcionário ainda não teve oportunidade de realizar tarefas que demonstrassem para outras pessoas a sua competência. O reconhecimento da competência do funcionário ficou restrito a seu superior imediato.";

    break;



    case 3:

	$msg1 = "Os conhecimentos, habilidades e competência demonstrados pelo funcionário no período foram reconhecidos pelos colegas da sua equipe.  O reconhecimento pode ter vindo na forma de elogio ou comentários sobre a qualidade do serviço ou das tarefas realizadas pelo funcionário. O reconhecimento da competência do funcionário ficou restrito ao seu departamento.";

    break;



    case 4:

	$msg1 = "Os conhecimentos, habilidades e competência demonstrados pelo funcionário no período foram reconhecidos por pelo menos um Diretor, Gerente ou Supervisor de outra área. O reconhecimento pode ter vindo na forma de elogio, comentário sobre um trabalho realizado, solicitação para que o funcionário fizesse determinada tarefa ou qualquer fato que indicasse o reconhecimento da competência do avaliado por pessoas de fora do seu departamento. ";

    break;



    }



switch ($radio2) {

    case 1:

    $msg2 = "O funcionário só tomou a iniciativa para adquirir novos conhecimentos ou habilidades quando fortemente incentivado, ou direta ou indiretamente pressionado pelo superior ou outras pessoas para realizar algum curso ou aprender novas tarefas.";

    break;



    case 2:

	$msg2 = "O funcionário só tomou a iniciativa de aprender coisas novas quando apareceu uma oportunidade única ou recebeu alguma sugestão do superior ou outras pessoas para aproveitar determinada oportunidade de fazer um curso fora, participar de um treinamento interno, ler um livro ou aprender novas tarefas. O funcionário responde bem às oportunidades de desenvolvimento profissional apenas quando recebe um leve empurrão do superior ou outras pessoas.";

    break;



    case 3:

	$msg2 = "O funcionário às vezes tomou a iniciativa para melhorar sua competência profissional. Isso ficou comprovado pelo grande interesse demonstrado pelo funcionário em determinado assunto relacionado com seu trabalho ou sua evolução profissional. Pode-se dizer que o funcionário responde bem às oportunidades que a empresa oferece ou sugestões de outras pessoas sobre o seu desenvolvimento profissional.";

    break;



    case 4:

	$msg2 = "O funcionário esteve sempre procurando adquirir novos conhecimentos e habilidades e se desenvolver profissionalmente. Isso ficou comprovado pelos comentários fundamentados do funcionário sobre leituras e pesquisas que realizou no período, pelos cursos que realizou por iniciativa própria, pelo aproveitamento e assimilação de conteúdos de treinamentos realizados ou patrocinados pela empresa. Pode-se dizer que o funcionário cuida do seu próprio desenvolvimento profissional, independente do apoio da empresa.";

    break;



    }



switch ($radio3) {

    case 1:

    $msg3 = "O funcionário precisará, além do treinamento específico, de uma preparação especial ou amadurecimento profissional para ser promovido.  No momento, não existem elementos que garantam que o funcionário tem potencial para ser promovido para um cargo maior.";

    break;



    case 2:

	$msg3 = "O funcionário dependerá de treinamento específico para ser promovido. O funcionário ainda não demonstrou estar preparado para tarefas que exijam conhecimentos superiores aos exigidos pelo seu cargo.  O funcionário ainda não demonstrou que tem o estilo e o potencial necessário para uma promoção para um cargo maior.";

    break;



    case 3:

	$msg3 = "O funcionário precisará de pouco treinamento ou orientação para ser promovido. No período ele realizou algumas das tarefas de responsabilidade de seu superior com grau de competência satisfatório, ou realizou tarefas que exigiam conhecimentos acima dos exigidos para o seu cargo.  O funcionário é reconhecido como tendo potencial para um dia suceder seu superior imediato ou ocupar um cargo maior em outra área.";

    break;



    case 4:

	$msg3 = "O funcionário está plenamente capacitado a ser promovido para um cargo maior. Isso foi comprovado pelo número de vezes que o funcionário realizou tarefas que exigiam conhecimentos e habilidades acima daquelas exigidas pelo seu cargo. Por exemplo, ele pode ter realizado tarefas importantes, de responsabilidade do seu superior imediato com o mesmo grau de competência.  Além dos aspectos técnicos, o funcionário tem perfil para o cargo do seu superior imediato e é reconhecido como o sucessor natural deste.";

    break;



    }



switch ($radio4) {

    case 1:

    $msg4 = "O funcionário não conseguiu apresentar os resultados esperados, mesmo tendo recebido ajuda dos demais membros da equipe.  Ou só conseguiu apresentar os resultados esperados com a ajuda de outras pessoas da equipe e constante orientação superior.";

    break;



    case 2:

	$msg4 = "Para conseguir os resultados esperados, o funcionário dependeu de significativa participação da equipe como um todo.  O mérito de sua participação não ficou evidente dentro da equipe.  Mesmo sem se destacar, foi importante como membro da equipe.";

    break;



    case 3:

	$msg4 = "Para conseguir os resultados esperados, o funcionário em alguns momentos dependeu de uma atenção especial de outras pessoas para ajudá-lo a completar seu trabalho. Foi possível reconhecer o mérito da sua participação no resultado do trabalho, mesmo no contexto de um trabalho de equipe.  Em vários momentos sua atuação foi destacada.";

    break;



    case 4:

	$msg4 = "Para conseguir os resultados esperados, o funcionário dependeu essencialmente do seu próprio esforço e competência. A participação de outras pessoas foi apenas subsidiária, dentro dos padrões normais do trabalho em equipe. O funcionário foi facilmente reconhecido como uma das pessoas que fizeram a diferença no grupo. Foi um dos principais destaques na equipe.";

    break;



    }



switch ($radio5) {

    case 1:

    $msg5 = "O funcionário soube resolver apenas os problemas de rotina, com precedentes bem conhecidos e contando com o apoio constante do superior imediato. As habilidades demonstradas pelo funcionário não foram suficientes para solucionar os problemas que saíram da estrita rotina do seu cargo. As habilidades de análise e julgamento demonstradas pelo funcionário foram apenas para fazer escolhas óbvias, em situações com precedentes bem conhecidos.";

    break;



    case 2:

	$msg5 = "O funcionário conseguiu resolver os problemas de rotina, contando com o apoio do superior quando necessário. Em algumas situações, o funcionário precisou fazer escolhas entre alternativas de solução já conhecidas, mas sem necessidade de uma reflexão mais profunda. As habilidades de raciocínio demonstradas pelo funcionário são compatíveis com os requisitos do seu cargo, ou seja, são habilidades que constam do perfil do seu cargo.";

    break;



    case 3:

	$msg5 = "O funcionário conseguiu resolver os problemas mais complexos que surgiram, descritos no item anterior, com alguma ajuda e orientação do seu superior imediato.  O funcionário conseguiu demonstrar habilidades para analisar e encontrar a solução para alguns dos problemas mais complexos, porém a participação do seu superior foi decisiva na escolha das alternativas de solução analisadas.";

    break;



    case 4:

	$msg5 = "O funcionário conseguiu resolver os problemas mais complexos que surgiram, cuja solução exigiu um grau de raciocínio lógico ou abstrato claramente mais alto do que aquele exigido pelo seu cargo. O raciocínio exercido envolveu alguma criatividade ou uma abordagem original para encontrar a melhor solução para esses problemas. O funcionário soube resolver os problemas mais complexos que surgiram, praticamente sem orientação superior.";

    break;



    }



switch ($radio6) {

    case 1:

    $msg6 = "Praticamente não contribuíram para a qualidade ou resultados do trabalho do funcionário ou para melhorar a competência e desempenho do funcionário. O desempenho do funcionário ficou abaixo do esperado, ou foi minimamente aceitável.";

    break;



    case 2:

	$msg6 = "Contribuíram para melhorar a competência do funcionário, porém não tiveram reflexos significativos na qualidade ou resultados do trabalho do funcionário.  O desempenho do funcionário ficou dentro dos padrões já apresentados por ele anteriormente.";

    break;



    case 3:

	$msg6 = "Contribuíram para melhorar a competência e desempenho do funcionário.  Tiveram alguns reflexos positivos na qualidade ou resultados do trabalho do funcionário.  Os resultados do trabalho do funcionário foram ligeiramente superiores àqueles apresentados antes da aquisição dos novos conhecimentos e habilidades.  Para notar a melhora no desempenho do funcionário, é necessária uma análise mais atenta e detalhada do trabalho realizado no período.  Geralmente só o superior do funcionário conseguiria notar essa melhora de desempenho.";

    break;



    case 4:

	$msg6 = "Foram decisivos para melhorar a sua competência e desempenho.  Os novos conhecimentos e habilidades tiveram reflexos positivos visíveis na qualidade ou resultados do trabalho do funcionário.  Os resultados do trabalho do funcionário foram claramente superiores àqueles apresentados antes da aquisição dos novos conhecimentos e habilidades. A diferença de antes e depois, em termos de desempenho do funcionário, são percebidas mesmo numa análise superficial.";

    break;



    }





}

?>