<?php

if (empty($_COOKIE['logado'])) {
   print "Efetue o Login<br><a href='login.php'>Logar</a> ";

} else {
   include "conn.php";
   $mesnagem_erro = "O servidor não respondeu conforme deveria. Contate o Administrador, Obrigado!";
//////////////////////////////////CONDIÇÃO para exibir os erros do php
   ini_set('display_errors', '1');
   /*
     Função para converter a data
     De formato nacional para formato americano.
     Muito útil para você inserir data no mysql e visualizar depois data do mysql.
    */
     function ConverteData($Data) {
       if (strstr($Data, "/")) {//verifica se tem a barra /
           $d = explode("/", $Data); //tira a barra
           $rstData = "$d[2]-$d[1]-$d[0]"; //separa as datas $d[2] = ano $d[1] = mes etc...
           return $rstData;
       } elseif (strstr($Data, "-")) {
           $d = explode("-", $Data);
           $rstData = "$d[2]/$d[1]/$d[0]";
           return $rstData;
       } else {
           return "0";
       }
   }
//$data_rg = ConverteData($data_rg);
   $id = $_REQUEST['id_cadastro'];
   $id_user_login = $_COOKIE['logado'];
   $result_userM = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user_login'");
   $row_userM = mysql_fetch_array($result_userM);
   $result_masterM = mysql_query("SELECT * FROM master WHERE id_master = '$row_userM[id_master]'");
   $row_masterM = mysql_fetch_array($result_masterM);
   print "
   <html><head><title>:: Intranet ::</title>
   <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
   <link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
   </head><body bgcolor='#D7E6D5'>";
//////////////////////////////////////
/// CASE 1: CADASTRANDO PROJETOS ////
////////////////////////////////////
   switch ($id) {
       case 1:
       $id_projeto = $_REQUEST['id_projeto'];
       $nome = $_REQUEST['nome'];
       $tema = $_REQUEST['tema'];
       $area = $_REQUEST['area'];
       $local = $_REQUEST['local'];
       $ini_dia = $_REQUEST['ini_dia'];
       $ini_mes = $_REQUEST['ini_mes'];
       $ini_ano = $_REQUEST['ini_ano'];
       $inicio = "$ini_ano$ini_mes$ini_dia";
       $ter_dia = $_REQUEST['ter_dia'];
       $ter_mes = $_REQUEST['ter_mes'];
       $ter_ano = $_REQUEST['ter_ano'];
       $termino = "$ter_ano$ter_mes$ter_dia";
       $descricao = $_REQUEST['caracteres'];
       $valor_ini = $_REQUEST['valor_ini'];
       $bolsista = $_REQUEST['bolsista'];
       $user = $_REQUEST['user'];
       $id_regiao = $_REQUEST['id_regiao'];
       $result_regiao = mysql_query("SELECT * FROM regioes where id_regiao = '$id_regiao'");
       $row_regiao = mysql_fetch_array($result_regiao);
       $regiao = "$row_regiao[regiao] - $row_regiao[sigla]";
       mysql_query("Insert INTO projeto(id_master,nome,tema,area,local,id_regiao,regiao,inicio,termino,descricao,valor_ini,bolsista,sis_user) VALUES
           ('$row_userM[id_master]','$nome','$tema','$area','$local','$id_regiao','$regiao','$inicio','$termino','$descricao','$valor_ini','$bolsista','$id_user_login')") or die(mysql_error() . "<BR><br><a href='vajascript:window.back()'>Voltar</a>");
       print "
       <link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\"><body bgcolor='#D7E6D5'>
       <table width='660' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='linha' align='center'>
       <tr><td><center><br>
       Projeto Cadastrado com Sucesso! <br> <a href='javascript:window.close()' class='link'>Continuar</a><br> <a href='cadastro.php?id=1&regiao=$id_regiao' class='link'>Cadastrar outro Projeto</a>
       <br><br></center>
       </td></tr></table></body></html>
       ";
       break;
       case 2:
       $regiao = $_REQUEST['regiao'];
       $sigla = $_REQUEST['sigla'];
       $master = $_REQUEST['master'];
       mysql_query("INSERT INTO regioes (id_master,regiao,sigla,criador,status,status_reg) values ('$master','$regiao','$sigla','$row_userM[nome1]','1','1')")
       or die("$mensagem_erro <br>" . mysql_error());
// Log - Cadastro de Regiões
       $qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
       $funcionario = mysql_fetch_array($qr_funcionario);
       $local = "Cadastro de Regiões";
       $ip = $_SERVER['REMOTE_ADDR'];
       $acao = "$funcionario[nome] cadastrou a região $regiao ($sigla)";
       mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao)
           VALUES ('$funcionario[0]', '$funcionario[id_regiao]', '$funcionario[tipo_usuario]', '$funcionario[grupo_usuario]', '$local', NOW(), '$ip', '$acao')") or die("Erro Inesperado<br><br>" . mysql_error());
//
       $id_Regiao = mysql_insert_id();
       $novoDir = sprintf("%03d", $id_Regiao);
       $diretorio_padrao = $_SERVER["DOCUMENT_ROOT"] . "/";
       $diretorio_padrao .= "intranet/documentos/";
       if (!is_dir($diretorio_padrao . $novoDir)) {
           mkdir($diretorio_padrao . $novoDir, 0777);
       }
       chmod($diretorio_padrao . $novoDir, 0777);
       print "
       <link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\"><body bgcolor='#D7E6D5'>
       <table width='90%' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='linha' align='center'>
       <tr><td>
       Região Cadastrada com Sucesso! <br> <a href='javascript:window.close()' class='link'>Continuar</a><br> <a href='cadastro.php?id=2' class='link'>Cadastrar outra Região</a>
       <br>
       </td></tr></table></body></html>
       ";
       break;
       case 3: ///CADASTRANDO USUARIOS
       $id_regiao = $_REQUEST['id_regiao'];
       $id_user = $_COOKIE['logado'];
       $data_cad = date('Y-m-d');
       $result_regiao = mysql_query("SELECT * FROM regioes where id_regiao = '$id_regiao'");
       $row_regiao = mysql_fetch_array($result_regiao);
       $regiao = "$row_regiao[regiao] - $row_regiao[sigla]";
       $funcao = $_REQUEST['funcao'];
       $locacao = $_REQUEST['locacao'];
       $nome = $_REQUEST['nome'];
       $endereco = $_REQUEST['endereco'];
       $bairro = $_REQUEST['bairro'];
       $cidade = $_REQUEST['cidade'];
       $uf = $_REQUEST['uf'];
       $cep = $_REQUEST['cep'];
       $tel_fixo = $_REQUEST['tel_fixo'];
       $tel_cel = $_REQUEST['tel_cel'];
       $tel_rec = $_REQUEST['tel_rec'];
       $data_nasci = $_REQUEST['data_nasci'];
       $naturalidade = $_REQUEST['naturalidade'];
       $nacionalidade = $_REQUEST['nacionalidade'];
       $civil = $_REQUEST['civil'];
       $ctps = $_REQUEST['ctps'];
       $serie_ctps = $_REQUEST['serie_ctps'];
       $uf_ctps = $_REQUEST['uf_ctps'];
       $pis = $_REQUEST['pis'];
       $rg = $_REQUEST['rg'];
       $secao = $_REQUEST['secao'];
       $data_rg = $_REQUEST['data_rg'];
       $cpf = $_REQUEST['cpf'];
       $titulo = $_REQUEST['titulo'];
       $zona = $_REQUEST['zona'];
       $orgao = $_REQUEST['orgao'];
       $pai = $_REQUEST['pai'];
       $mae = $_REQUEST['mae'];
       $estuda = $_REQUEST['estuda'];
       $escola_dia = $_REQUEST['escola_dia'];
       $escola_mes = $_REQUEST['escola_mes'];
       $escola_ano = $_REQUEST['escola_ano'];
       $data_escola = "$escola_ano$escola_mes$escola_dia";
       $escolaridade = $_REQUEST['escolaridade'];
       $instituicao = $_REQUEST['instituicao'];
       $curso = $_REQUEST['curso'];
       $banco = $_REQUEST['banco'];
       $agencia = $_REQUEST['agencia'];
       $conta = $_REQUEST['conta'];
       $login = $_REQUEST['login'];
       $senha = $_REQUEST['senha'];
       $tipo_usuario = $_REQUEST['tipo_usuario'];
       //formata o salário.
       $salario = $_REQUEST['salario'];
       $salario = preg_replace('/\./','',$salario);
       $salario = preg_replace('/\,/','.',$salario);

       $grupo_usuario = $_REQUEST['grupo_usuario'];
       $alt_senha = 1;
       $nome1 = $_REQUEST['nome1'];
       $foto = $_REQUEST['foto'];
       $empresa = $_REQUEST['empresa'];
           /*
             Função para converter a data
             De formato nacional para formato americano.
             Muito útil para você inserir data no mysql e visualizar depois data do mysql.
            */
           /*
             function ConverteData($Data){
             if (strstr($Data, "/"))//verifica se tem a barra /
             {
             $d = explode ("/", $Data);//tira a barra
             $rstData = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mes etc...
             return $rstData;
             } elseif(strstr($Data, "-")){
             $d = explode ("-", $Data);
             $rstData = "$d[2]/$d[1]/$d[0]";
             return $rstData;
             }else{
             return "0";
             }
         } */
         $data_nasci = ConverteData($data_nasci);
         $data_rg = ConverteData($data_rg);
           if ($foto == "1") {           //AQUI TEM ARQUIVO
               $arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;
               if ($arquivo[type] != "image/x-png" && $arquivo[type] != "image/pjpeg" && $arquivo[type] != "image/gif"
                       && $arquivo[type] != "image/jpe") {     //aki a imagem nao corresponde com as extenções especificadas
                   print "<center>
               <hr><font size=2><b>
               Tipo de arquivo não permitido, os únicos padrões permitidos são .gif, .jpg , .jpeg ou .png<br>
               $arquivo[type] <br><br>
               <a href='cadastro.php?id=3&regiao=$regiao'>Voltar</a>
               </b></font>";
               exit;
               } else {  //aqui o arquivo é realente de imagem e vai ser carregado para o servidor
                   $arr_basename = explode(".", $arquivo['name']);
                   $file_type = $arr_basename[1];
                   if ($file_type == "gif") {
                       $tipo_name = ".gif";
                   } if ($file_type == "jpg" or $arquivo[type] == "jpeg") {
                       $tipo_name = ".jpg";
                   } if ($file_type == "png") {
                       $tipo_name = ".png";
                   }
                   $foto = $tipo_name;
                   mysql_query("insert into funcionario(id_master,tipo_usuario,grupo_usuario,nome,salario,id_regiao,regiao,funcao,locacao,endereco,bairro,cidade,uf,cep,tel_fixo,tel_cel,tel_rec,data_nasci,naturalidade,nacionalidade,civil,ctps,serie_ctps,uf_ctps,pis,rg,orgao,data_rg,cpf,titulo,zona,secao,pai,mae,estuda,data_escola,escolaridade,instituicao,curso,foto,banco,agencia,conta,login,senha,alt_senha,user_cad,data_cad,nome1) values
                       ('$empresa','$tipo_usuario','$grupo_usuario','$nome','$salario','$id_regiao','$regiao','$funcao','$locacao','$endereco','$bairro','$cidade','$uf','$cep','$tel_fixo','$tel_cel','$tel_rec','$data_nasci','$naturalidade','$nacionalidade','$civil','$ctps','$serie_ctps','$uf_ctps','$pis','$rg','$orgao','$data_rg','$cpf','$titulo','$zona','$secao','$pai','$mae','$estuda','$data_escola','$escolaridade','$instituicao','$curso','$foto','$banco','$agencia','$conta','$login','$senha','$alt_senha','$id_user','$data_cad','$nome1')") or die("$mensagem_erro<br><Br>" . mysql_error());
                   $id_insert = mysql_insert_id();
                   // Resolvendo o nome e para onde o arquivo será movido
                   $diretorio = "fotos/";
                   $nome_tmp = $id_regiao . "funcionario" . $id_insert . $tipo_name;
                   $nome_arquivo = "$diretorio$nome_tmp";
                   move_uploaded_file($arquivo['tmp_name'], $nome_arquivo) or die("Erro ao enviar o Arquivo: $nome_arquivo");
               } //aqui fecha o IF que verificar se o arquivo tem a extenção especificada
           } else {    //AQUI ESTÁ SEM A FOTO
               $foto = "0";
               mysql_query("insert into funcionario(tipo_usuario,grupo_usuario,nome,salario,id_regiao,regiao,funcao,locacao,endereco,bairro,cidade,uf,cep,tel_fixo,tel_cel,tel_rec,data_nasci,naturalidade,nacionalidade,civil,ctps,serie_ctps,uf_ctps,pis,rg,orgao,data_rg,cpf,titulo,zona,secao,pai,mae,estuda,data_escola,escolaridade,instituicao,curso,foto,banco,agencia,conta,login,senha,alt_senha,user_cad,data_cad,nome1) values
                   ('$tipo_usuario','$grupo_usuario','$nome','$salario','$id_regiao','$regiao','$funcao','$locacao','$endereco','$bairro','$cidade','$uf','$cep','$tel_fixo','$tel_cel','$tel_rec','$data_nasci','$naturalidade','$nacionalidade','$civil','$ctps','$serie_ctps','$uf_ctps','$pis','$rg','$orgao','$data_rg','$cpf','$titulo','$zona','$secao','$pai','$mae','$estuda','$data_escola','$escolaridade','$instituicao','$curso','$foto','$banco','$agencia','$conta','$login','$senha','$alt_senha','$id_user','$data_cad','$nome1')") or die("$mensagem_erro<br><BR>" . mysql_error());
               $id_insert = mysql_insert_id();
           }
           $password = md5($senha);
           mysql_query("INSERT INTO doc_tecnico_acesso (usuario, senha, funcionario_id) VALUES ( '$login','$password','$id_insert' )") or die(mysql_error());
           $id_funcionario = $id_insert;
/////CADASTRANDO E-MAIL DO USUÁRIO
           $array_master_email = $_POST['master_email'];
           $array_email = $_POST['email'];
           $array_senha = $_POST['senha_email'];
           foreach ($array_master_email as $chave => $master_id) {
               $sql[] = "( '" . $master_id . "', '" . $id_funcionario . "', '" . $array_email[$chave] . "', '" . $array_senha[$chave] . "' )";
               $sql2[] = "( '" . $master_id . "', '" . $id_funcionario . "' )";
           }
           $sql = implode(',', $sql);
           $sql2 = implode(',', $sql2);
           mysql_query("INSERT INTO 	funcionario_email_assoc (id_master, id_funcionario, email, senha) VALUES $sql");
           mysql_query("INSERT INTO 	funcionario_master (id_master, id_funcionario) VALUES $sql2");
//////////////////////////////////////////
           $botoes = $_REQUEST['botoes'];
           $array_acoes = $_REQUEST['acoes'];
           $array_regioes = $_REQUEST['regioes_permitidas'];
           $array_empresas = (array_unique($_REQUEST['empresas']));
           $array_regioes_folha = $_REQUEST['regiao_folhas'];
////////////////////////////////////////////////////
///////////  ADICONA OS BOTÕES MARCADOS  ///////////
////////////////////////////////////////////////////
           if (!empty($botoes)) {
               foreach ($botoes as $chave => $valor) {
                   $qr_botoes_assoc = mysql_query("SELECT * FROM botoes_assoc WHERE botoes_id = '$valor'  AND id_funcionario = '$id_funcionario'");
                   if (mysql_num_rows($qr_botoes_assoc) == 0) {
                       mysql_query("INSERT INTO botoes_assoc (botoes_id, id_funcionario) VALUES ('$valor', '$id_funcionario');");
                   }
               }
           }
///////////////////////////
////////////////////////////////////////////////////
///// Adicionando permissoes na tabela ações  //////
////////////////////////////////////////////////////
           if (count($array_acoes) != 0) {
               foreach ($array_acoes as $acoes) {
                   mysql_query("INSERT INTO funcionario_acoes_assoc  (id_funcionario, acoes_id ) VALUES('$id_funcionario', '$acoes')") or die(mysql_error());
               }
           }
////////////////////////////////////////////////////
//// ADICINANDO AS REGIÕES PERMITADAS PARA O USUÁRIO
////////////////////////////////////////////////////
           if (count($array_regioes) != 0) {
               foreach ($array_empresas as $id_master) {
                   if ($array_regioes[$id_master] != 0) {
                       foreach ($array_regioes[$id_master] as $id_regiao) {
                           mysql_query("INSERT INTO funcionario_regiao_assoc (id_funcionario, id_regiao, id_master) VALUES ('$id_funcionario', '$id_regiao','$id_master' ) ") or die(mysql_error());
                       }
                   }
               }
           }
////PERMISSOES DE REGIÃO PARA AS FOLHAS
           $qr_acoes = mysql_query("SELECT * FROM acoes WHERE botoes_id  = 33 or botoes_id = 60");
           while ($row_acoes = mysql_fetch_assoc($qr_acoes)):
               if (@in_array($row_acoes['acoes_id'], $array_acoes) and (sizeof($array_regioes_folha[$row_acoes['botoes_id']])) >= 1) {
                   foreach ($array_regioes_folha[$row_acoes['botoes_id']] as $id_regiao) {
                       mysql_query("INSERT INTO funcionario_acoes_assoc  (id_funcionario, botoes_id, id_regiao,acoes_id ) VALUES('$id_funcionario', '$row_acoes[botoes_id]', '$id_regiao', '$row_acoes[acoes_id]')") or die(mysql_error());
                   }
               } else if (in_array($row_acoes['acoes_id'], $array_acoes) and (sizeof($array_regioes_folha[$row_acoes['botoes_id']])) == 0) {
                   mysql_query("INSERT INTO funcionario_acoes_assoc  (id_funcionario,acoes_id ) VALUES('$id_funcionario', '$row_acoes[acoes_id]')");
               }
               endwhile;
           /* $qr_acoes = mysql_query("SELECT * FROM acoes WHERE botoes_id  = 33 or botoes_id = 60");
             while($row_acoes = mysql_fetch_assoc($qr_acoes)):
             mysql_query("DELETE FROM funcionario_acoes_assoc WHERE id_funcionario = '$id_funcionario'  AND acoes_id = '$row_acoes[acoes_id]'");
             if(in_array($row_acoes['acoes_id'],$array_acoes)) {
             $id_regiao = $array_regioes_folha[$row_acoes['botoes_id']][0] ;
             mysql_query("INSERT INTO funcionario_acoes_assoc  (id_funcionario, botoes_id, id_regiao,acoes_id ) VALUES('$id_funcionario', '$row_acoes[botoes_id]', '$id_regiao', '$row_acoes[acoes_id]')")or die(mysql_error());
             }
             endwhile;
            */
             print "
             <script>
             alert (\"Funcionario cadastrado com sucesso!\");
             window.close()
             </script>";
             break;
       case 4:               //CADASTRO DE AUTONOMOS/COOPERADOS
       $regiao = $_REQUEST['regiao'];
       $id_projeto = $_REQUEST['id_projeto'];
//DADOS CONTRATAÇÃO
       $tipo_contratacao = $_REQUEST['contratacao'];
       $id_curso = $_REQUEST['idcurso'];
       $locacao = $_REQUEST['locacao'];
       if ($tipo_contratacao == 3) {
           $cooperativa = $_REQUEST['cooperativa'];
       } else {
           $cooperativa = "0";
       }
//DADOS CADASTRAIS
       $nome = $_REQUEST['nome'];
       $sexo = $_REQUEST['sexo'];
       $endereco = $_REQUEST['endereco'];
       $bairro = $_REQUEST['bairro'];
       $cidade = $_REQUEST['cidade'];
       $uf = $_REQUEST['uf'];
       $cep = $_REQUEST['cep'];
       $tel_fixo = $_REQUEST['tel_fixo'];
       $tel_cel = $_REQUEST['tel_cel'];
       $tel_rec = $_REQUEST['tel_rec'];
       $data_nasci = $_REQUEST['data_nasci'];
       $naturalidade = $_REQUEST['naturalidade'];
       $nacionalidade = $_REQUEST['nacionalidade'];
       $civil = $_REQUEST['civil'];
//DOCUMENTAÇÃO
       $rg = $_REQUEST['rg'];
       $uf_rg = $_REQUEST['uf_rg'];
       $secao = $_REQUEST['secao'];
       $data_rg = $_REQUEST['data_rg'];
       $cpf = $_REQUEST['cpf'];
       $titulo = $_REQUEST['titulo'];
       $zona = $_REQUEST['zona'];
       $orgao = $_REQUEST['orgao'];
       $pai = $_REQUEST['pai'];
       $mae = $_REQUEST['mae'];
       $nacionalidade_pai = $_REQUEST['nacionalidade_pai'];
       $nacionalidade_mae = $_REQUEST['nacionalidade_mae'];
       $estuda = $_REQUEST['estuda'];
       $data_escola = $_REQUEST['data_escola'];
       $escolaridade = $_REQUEST['escolaridade'];
       $instituicao = $_REQUEST['instituicao'];
       $curso = $_REQUEST['curso'];
       $data_entrada = $_REQUEST['data_entrada'];
       $banco = $_REQUEST['banco'];
       $agencia = $_REQUEST['agencia'];
       $conta = $_REQUEST['conta'];
       $nomebanco = $_REQUEST['nomebanco'];
       $tipoDeConta = $_REQUEST['radio_tipo_conta'];
       $localpagamento = $_REQUEST['localpagamento'];
       $apolice = $_REQUEST['apolice'];
       $campo1 = $_REQUEST['trabalho'];
       $campo2 = $_REQUEST['dependente'];
       $campo3 = $_REQUEST['codigo'];
       $data_cadastro = date('Y-m-d');
       $nome_banco = $_REQUEST['nome_banco'];
       $pis = $_REQUEST['pis'];
       $fgts = $_REQUEST['fgts'];
       $tipopg = $_REQUEST['tipopg'];
       $filhos = $_REQUEST['filhos'];
       $observacoes = $_REQUEST['observacoes'];
       $medica = $_REQUEST['medica'];
       $assinatura2 = $_REQUEST['assinatura2'];
       $assinatura3 = $_REQUEST['assinatura3'];
       if (empty($_REQUEST['insalubridade'])) {
           $insalubridade = "0";
       } else {
           $insalubridade = $_REQUEST['insalubridade'];
       }
       if (empty($_REQUEST['transporte'])) {
           $transporte = "0";
       } else {
           $transporte = $_REQUEST['transporte'];
       }
       if (empty($_REQUEST['impressos2'])) {
           $impressos = "0";
       } else {
           $impressos = $_REQUEST['impressos2'];
       }
       $plano_medico = $_REQUEST['plano_medico'];
       $serie_ctps = $_REQUEST['serie_ctps'];
       $uf_ctps = $_REQUEST['uf_ctps'];
       $pis_data = $_REQUEST['data_pis'];
       $tipo_vale = $_REQUEST['tipo_vale'];
       $num_cartao = $_REQUEST['num_cartao'];
       $valor_cartao = $_REQUEST['valor_cartao'];
       $tipo_cartao_1 = $_REQUEST['tipo_cartao_1'];
       $num_cartao2 = $_REQUEST['num_cartao2'];
       $valor_cartao2 = $_REQUEST['valor_cartao2'];
       $tipo_cartao_2 = $_REQUEST['tipo_cartao_2'];
       $vale_qnt_1 = $_REQUEST['vale_qnt_1'];
       $vale_valor_1 = $_REQUEST['vale_valor_1'];
       $tipo1 = $_REQUEST['tipo1'];
       $vale_qnt_2 = $_REQUEST['vale_qnt_2'];
       $vale_valor_2 = $_REQUEST['vale_valor_2'];
       $tipo2 = $_REQUEST['tipo2'];
       $vale_qnt_3 = $_REQUEST['vale_qnt_3'];
       $vale_valor_3 = $_REQUEST['vale_valor_3'];
       $tipo3 = $_REQUEST['tipo3'];
       $vale_qnt_4 = $_REQUEST['vale_qnt_4'];
       $vale_valor_4 = $_REQUEST['vale_valor_4'];
       $tipo4 = $_REQUEST['tipo4'];
       $ad_noturno = $_REQUEST['ad_noturno'];
       $exame_data = $_REQUEST['data_exame'];
       $trabalho_data = $_REQUEST['data_ctps'];
       $reservista = $_REQUEST['reservista'];
       $cabelos = $_REQUEST['cabelos'];
       $peso = $_REQUEST['peso'];
       $altura = $_REQUEST['altura'];
       $olhos = $_REQUEST['olhos'];
       $defeito = $_REQUEST['defeito'];
       $cipa = $_REQUEST['cipa'];
       $etnia = $_REQUEST['etnia'];
       $filho_1 = $_REQUEST['filho_1'];
       $filho_2 = $_REQUEST['filho_2'];
       $filho_3 = $_REQUEST['filho_3'];
       $filho_4 = $_REQUEST['filho_4'];
       $filho_5 = $_REQUEST['filho_5'];
       $data_filho_1 = $_REQUEST['data_filho_1'];
       $data_filho_2 = $_REQUEST['data_filho_2'];
       $data_filho_3 = $_REQUEST['data_filho_3'];
       $data_filho_4 = $_REQUEST['data_filho_4'];
       $data_filho_5 = $_REQUEST['data_filho_5'];
       if (empty($_REQUEST['foto'])) {
           $foto = "0";
       } else {
           $foto = $_REQUEST['foto'];
       }
       if ($foto == "1") {
           $foto_banco = "1";
           $foto_up = "1";
       } else {
           $foto_banco = "0";
           $foto_up = "0";
       }
           /*
             Função para converter a data
             De formato nacional para formato americano.
             Muito útil para você inserir data no mysql e visualizar depois data do mysql.
             function ConverteData($Data){
             if (strstr($Data, "/"))//verifica se tem a barra /
             {
             $d = explode ("/", $Data);//tira a barra
             $rstData = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mes etc...
             return $rstData;
             } elseif(strstr($Data, "-")){
             $d = explode ("-", $Data);
             $rstData = "$d[2]/$d[1]/$d[0]";
             return $rstData;
             }else{
             return "Data invalida";
             }
             }
            */
             $data_filho_1 = ConverteData($data_filho_1);
             $data_filho_2 = ConverteData($data_filho_2);
             $data_filho_3 = ConverteData($data_filho_3);
             $data_filho_4 = ConverteData($data_filho_4);
             $data_filho_5 = ConverteData($data_filho_5);
             $data_nasci = ConverteData($data_nasci);
             $data_rg = ConverteData($data_rg);
             $data_escola = ConverteData($data_escola);
             $data_entrada = ConverteData($data_entrada);
             $pis_data = ConverteData($pis_data);
             $exame_data = ConverteData($exame_data);
             $trabalho_data = ConverteData($trabalho_data);
           //VERIFICANDO SE O FUNCIONÁRIO JA ESTÁ CADASTRADO NA TABELA AUTONOMO
             $verificando_auto = mysql_query("SELECT nome FROM autonomo where nome = '$nome' and tel_fixo = '$tel_fixo' and data_nasci = '$data_nasci' and rg = '$rg'");
             $row_verificando_auto = mysql_num_rows($verificando_auto);
           if ($row_verificando_auto >= "1") { //JA EXISTE UM AUTONOMO CADASTRADO COM O MESMO NOME DATE NASC RG E TEL
               print "
               <br>
               <link href='../net.css' rel='stylesheet' type='text/css'>
               <body bgcolor='#D7E6D5'>
               <center>
               <br>ESTE PARTICIPANTE JA ESTÁ CADASTRADO: <font color=#FFFFFF><b>$nome</b></font>
               </center>
               </body>
               ";
               exit;
           } else {              //AQUI VAI RODAR O CADASTRO
               $result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$id_projeto'");
               $row_projeto = mysql_fetch_array($result_projeto);
               $data_cadastro = date('Y-m-d');
// PEGANDO O MAIOR NUMERO
               $resut_maior = mysql_query("SELECT CAST(campo3 AS UNSIGNED) campo3 ,
                   MAX(campo3)
                   FROM autonomo
                   WHERE id_regiao= '$row_projeto[id_regiao]'
                   AND id_projeto ='$id_projeto'
                   AND campo3 != 'INSERIR'
                   GROUP BY campo3 DESC
                   LIMIT 0,1");
               $row_maior = mysql_fetch_array($resut_maior);
               $codigoNOVO = $row_maior['0'] + 1;
               mysql_query("insert into autonomo
                   (id_projeto,id_regiao,localpagamento,locacao,nome,sexo,endereco,bairro,cidade,uf,cep,tel_fixo,tel_cel,tel_rec,
                       data_nasci,naturalidade,nacionalidade,civil,rg,orgao,data_rg,cpf,titulo,zona,secao,pai,nacionalidade_pai,mae,nacionalidade_mae,
                       estuda,data_escola,escolaridade,instituicao,curso,tipo_contratacao,banco,agencia,conta,tipo_conta,id_curso,apolice,data_entrada,campo1,campo2,
                       campo3,data_exame,reservista,etnia,cabelos,altura,olhos,peso,defeito,cipa,ad_noturno,plano,assinatura,distrato,
                       outros,pis,dada_pis,data_ctps,serie_ctps,uf_ctps,uf_rg,fgts,insalubridade,transporte,medica,tipo_pagamento,nome_banco,num_filhos,
                       observacao,impressos,sis_user,data_cad,foto,id_cooperativa,rh_vinculo,rh_status,rh_horario,rh_sindicato,rh_cbo)

VALUES
                        ('$id_projeto','$regiao','$localpagamento','$locacao','$nome','$sexo','$endereco','$bairro','$cidade','$uf',
   '$cep','$tel_fixo','$tel_cel','$tel_rec','$data_nasci','$naturalidade','$nacionalidade','$civil','$rg',
   '$orgao','$data_rg','$cpf','$titulo','$zona','$secao','$pai','$nacionalidade_pai','$mae','$nacionalidade_mae','$estuda',
   '$data_escola','$escolaridade','$instituicao','$curso','$tipo_contratacao','$banco','$agencia','$conta','$tipoDeConta','$id_curso','$apolice',
   '$data_entrada','$campo1','$campo2','$codigoNOVO','$exame_data','$reservista','$etnia','$cabelos','$altura','$olhos','$peso','$defeito','$cipa',
   '$ad_noturno','$plano_medico','$impressos','$assinatura2','$assinatura3','$pis','$pis_data','$trabalho_data','$serie_ctps',
   '$uf_ctps','$uf_rg','$fgts','$insalubridade','$transporte','$medica','$tipopg','$nome_banco','$filhos','$observacoes','$impressos',
   '$id_user_login','$data_cadastro','$foto_banco','$cooperativa','$rh_vinculo','$rh_status','$rh_horario','$rh_sindicato','$rh_cbo')")

or die("$mensagem_erro<br><BR>" . mysql_error());

$row_id_participante = mysql_insert_id();

}

$id_bolsista = $row_id_participante;

if ($transporte == "1") {
   mysql_query("insert into vale(id_regiao,id_projeto,id_bolsista,nome,cpf,tipo_vale,
       numero_cartao,valor_cartao,quantidade,qnt1,valor1,qnt2,valor2,qnt3,valor3,qnt4,valor4,tipo1,tipo2,tipo3,tipo4,
       tipo_cartao_1,tipo_cartao_2,numero_cartao2,valor_cartao2,status_vale) values
   ('$regiao','$id_projeto','$row_id_participante','$nome','$cpf','$tipo_vale','$num_cartao','$valor_cartao',
       '','$vale_qnt_1','$vale_valor_1','$vale_qnt_2','$vale_valor_2','$vale_qnt_3','$vale_valor_3',
       '$vale_qnt_4','$vale_valor_4','$tipo1','$tipo2','$tipo3','$tipo4','$tipo_cartao_1','$tipo_cartao_2','$num_cartao2',
       '$valor_cartao2','$transporte')") or die("$mensagem_erro - 2.3<br><br>" . mysql_error());

}

if ($filhos == "" or $filhos == "0") {
   $naa = "0";

} else {
   mysql_query("insert into dependentes(id_regiao,id_projeto,id_bolsista,contratacao,nome,data1,nome1,data2,nome2,data3,nome3,data4,nome4,data5,nome5) values
       ('$regiao','$id_projeto','$row_id_participante','$tipo_contratacao','$nome','$data_filho_1','$filho_1','$data_filho_2','$filho_2','$data_filho_3','$filho_3','$data_filho_4','$filho_4','$data_filho_5','$filho_5')") or die("$mensagem_erro 2.4<br><br>" . mysql_error());
   $naa = "2";

}

$n_id_curso = sprintf("%04d", $id_curso);

$n_regiao = sprintf("%04d", $regiao);

$n_id_bolsista = sprintf("%04d", $row_id_participante);

$cpf2 = str_replace(".", "", $cpf);

$cpf2 = str_replace("-", "", $cpf2);
           // GERANDO A SENHA ALEATÓRIA

$target = "%%%%%%";

$senha = "";

$dig = "";

$consoantes = "bcdfghjkmnpqrstvwxyz1234567890bcdfghjkmnpqrstvwxyz123456789";

$vogais = "aeiou";

$numeros = "123456789bcdfghjkmnpqrstvwxyzaeiou";

$a = strlen($consoantes) - 1;

$b = strlen($vogais) - 1;

$c = strlen($numeros) - 1;

for ($x = 0; $x <= strlen($target) - 1; $x++) {
   if (substr($target, $x, 1) == "@") {
       $rand = mt_rand(0, $c);
       $senha .= substr($numeros, $rand, 1);
   } elseif (substr($target, $x, 1) == "%") {
       $rand = mt_rand(0, $a);
       $senha .= substr($consoantes, $rand, 1);
   } elseif (substr($target, $x, 1) == "&") {
       $rand = mt_rand(0, $b);
       $senha .= substr($vogais, $rand, 1);
   } else {
       die("<b>Erro!</b><br><i>$target</i> é uma expressão inválida!<br><i>" . substr($target, $x, 1) . "</i> é um caractér inválido.<br>");
   }

}

$matricula = "$n_id_curso.$n_regiao.$n_id_bolsista-00";

mysql_query("insert into tvsorrindo(id_bolsista,id_projeto,nome,cpf,matricula,senha,inicio) values
   ('$row_id_participante','$id_projeto','$nome','$cpf','$matricula','$senha','$inicio')") or die("$mensagem_erro<br><Br>");
           //FAZENDO O UPLOAD DA FOTO

$arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;

if ($foto_up == "1") {
   if (!$arquivo) {
       $mensagem = "Não acesse esse arquivo diretamente!";
   }
               // Imagem foi enviada, então a move para o diretório desejado
   else {
       $nome_arq = str_replace(" ", "_", $nome);
       $tipo_arquivo = ".gif";
                   // Resolvendo o nome e para onde o arquivo será movido
       $diretorio = "fotos/";
       $nome_tmp = $regiao . "_" . $id_projeto . "_" . $row_id_participante . $tipo_arquivo;
       $nome_arquivo = "$diretorio$nome_tmp";
       move_uploaded_file($arquivo['tmp_name'], $nome_arquivo) or die("Erro ao enviar o Arquivo: $nome_arquivo");
   }

}

$link_fim = "ver_bolsista.php?reg=$regiao&bol=$row_id_participante&pro=$id_projeto";

print"

<html>

<head>

<title>:: Intranet ::</title>

<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>

<link href='../net.css' rel='stylesheet' type='text/css'>

<style type='text/css'>

<!--
            .style1 {color: #FF0000;
   font-weight: bold;}
   .style5 {font-size: 12px}
   .style6 {font-family: Arial, Helvetica, sans-serif;
       font-size: 12px;
       font-weight: bold;}
       .style7 {color: #FF0000}
       .style11 {font-family: Arial, Helvetica, sans-serif; font-size: 11px; font-weight: bold; }
       .style13 {font-family: Arial, Helvetica, sans-serif; font-size: 11px; }
       .style15 {color: #FF0000; font-weight: bold; font-family: Arial, Helvetica, sans-serif; font-size: 11px; }
       .style16 {font-size: 11px}
       -->
       </style>
       </head>
       <body bgcolor='#FFFFFF'>
       <center>
       <br>FUNCIONÁRIO CADASTRADO COM SUCESSO!<br>
       <a href='cadastro.php?id=4&pro=$id_projeto&regiao=$regiao'>Cadastrar Outro Funcionário</a>
       <br>
       <a href='$link_fim'> Continuar </a>
       </center>
       </body>
       </html>
       ";
       break;
       case 5:  //	ENIANDO TAREFAS
////PEGA INFORMAÇÕES DO ANEXO E MOVE O MESMO PARA A PASTA
       if ($_POST['op_anexo'] == 'sim') {
           $anexo = $_FILES['anexo'];
           $nome = date('d_m_Y') . md5($anexo['name']);
           $pasta = 'anexo_tarefa/';
           switch ($anexo['type']) {
               case 'image/jpeg': $extensao = '.jpg';
               break;
               case 'image/gif': $extensao = '.gif';
               break;
               case 'image/png': $extensao = '.png';
               break;
               case 'application/pdf': $extensao = '.pdf';
               break;
               case 'application/msword': $extensao = '.doc';
               break;
           }
           if (!move_uploaded_file($anexo['tmp_name'], $pasta . $nome . $extensao)) {
               echo 'Erro no envio do anexo';
           }
       }
///// FIM ANEXO
       $id_regiao = $_REQUEST['id_regiao'];
       $tipo = $_REQUEST['radio'];
       if ($tipo == "1") {
           $usuario = $_REQUEST['user'];
           $grupo = "";
           $tarefa_todos = 0;
       } elseif ($tipo == 2) {
           $usuario = "TODOS DO SEU GRUPO";
           $grupo = $_REQUEST['grupo'];
           $tarefa_todos = 0;
           }/* elseif($tipo == '3') {     ////tipo  a tarefa é exibida para todos os usuários
             $usuario = $_REQUEST['user'];
             $grupo = "";
             $tarefa_todos = 1;
         } */
         $criador = $_REQUEST['criador'];
         $tarefa = $_REQUEST['tarefa'];
         $descricao = $_REQUEST['descricao'];
         $data_entrega = $_REQUEST['data_entrega'];
         if (empty($_REQUEST['copia'])) {
           $copia = "0";
       } else {
           $copia = $_REQUEST['copia'];
       }
       $data_entrega = ConverteData($data_entrega);
       $data_criacao = Date('Y-m-d');
       mysql_query("insert into tarefa(id_regiao,tipo_tarefa,criador,grupo,usuario,tarefa,descricao,data_criacao,data_entrega,copia,anexo_nome,anexo_extensao,tarefa_todos) values
           ('$id_regiao','$tipo','$criador','$grupo','$usuario','$tarefa','$descricao','$data_criacao','$data_entrega','$copia','$nome','$extensao','$tarefa_todos')") or die("$mensagem_erro<br><br>" . mysql_error());
       print "
       <script>
       alert (\"Tarefa enviada com sucesso!\");
       window.close()
       </script>";
       break;
// AVALIAÇÃO PSICOLOGICA
       case 6:
       $radio = $_REQUEST['radio'];
       $id_bolsista = $_REQUEST['id_bolsista'];
       $id_projeto = $_REQUEST['id_projeto'];
       $id_regiao = $_REQUEST['id_regiao'];
       mysql_query("UPDATE autonomo set psicologia = '$radio' where id_autonomo = '$id_bolsista'") or die("$mensagem_erro<br><a href='javascript:history.go(-1)>Voltar</a><br><BR>" . mysql_error());
       print "
       <html><head><title>:: Intranet ::</title>
       <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
       <link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
       </head><body bgcolor='#D7E6D5'>";
       print "<br><br><br><font color='#FFFFFF'>Informações gravadas com sucesso! </font><br><a href='bolsista_class.php?id=2&projeto=$id_projeto&regiao=$id_regiao'><img src='imagens/voltar.gif' border=0></a>";
       print "</body></hrml>";
       break;
// REAVALIAÇÃO PSICOLOGICA
       case 7:
       $texto = $_REQUEST['texto'];
       $descricao = $_REQUEST['descricao'];
       $id_regiao = $_REQUEST['id_regiao'];
       mysql_query("INSERT INTO psicologia(texto,descricao) values ('$texto','$descricao')") or die("$mensagem_erro <br><a href='javascript:history.go(-1)>Voltar</a><br><BR>" . mysql_error());
       print "
       <html><head><title>:: Intranet ::</title>
       <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
       <link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
       </head><body bgcolor='#D7E6D5'>";
       print "<br><br><br><span class='style27'> Informações gravadas com sucesso! </span><br><a href='bolsista_class.php?id=4&id_regiao=$id_regiao'><img src='imagens/voltar.gif' border=0></a>";
       break;
       case 8:
       $regiao = $_REQUEST['regiao'];
       $banco = $_REQUEST['banco'];
       $razao = $_REQUEST['razao'];
       $apolice = $_REQUEST['apolice'];
       $contrato = $_REQUEST['contrato'];
       $tel = $_REQUEST['tel'];
       $gerente = $_REQUEST['gerente'];
       mysql_query("INSERT INTO apolice(id_regiao,banco,razao,apolice,contrato,tel,gerente) values
           ('$regiao','$banco','$razao','$apolice','$contrato','$tel','$gerente')") or die("$mensagem_erro");
           // Log - Cadastro de Apólices
       $qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
       $funcionario = mysql_fetch_array($qr_funcionario);
       $local = "Cadastro de Apólices";
       $ip = $_SERVER['REMOTE_ADDR'];
       $acao = "$funcionario[nome] cadastrou a apólice $apolice";
       mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao)
           VALUES ('$funcionario[0]', '$funcionario[id_regiao]', '$funcionario[tipo_usuario]', '$funcionario[grupo_usuario]', '$local', NOW(), '$ip', '$acao')") or die("Erro Inesperado<br><br>" . mysql_error());
//
       print "
       <html><head><title>:: Intranet ::</title>
       <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
       <link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
       </head><body bgcolor='#D7E6D5'>";
       print "<br><br><br><span class='style27'> Informações gravadas com sucesso! </span><br><a href='cadastro.php?id=5&regiao=$regiao'><img src='imagens/voltar.gif' border=0></a>";
       break;
       case 9:                               // cadastro de bancos
       $regiao = $_REQUEST['regiao'];
       $projeto = $_REQUEST['projeto'];
       $banco = $_REQUEST['nom_banco'];
       $razao = $_REQUEST['banco'];
       $localidade = $_REQUEST['localidade'];
       $conta = $_REQUEST['conta'];
       $agencia = $_REQUEST['agencia'];
       $endereco = $_REQUEST['endereco'];
       $tel = $_REQUEST['tel'];
       $gerente = $_REQUEST['gerente'];
       $interno = $_REQUEST['interno'];
       $result_listabancos = mysql_query("SELECT * FROM listabancos WHERE id_lista = '$razao'");
       $row_listabancos = mysql_fetch_array($result_listabancos);
       mysql_query("INSERT INTO bancos(id_regiao,id_nacional,nome,razao,localidade,conta,agencia,endereco,tel,gerente,site,id_projeto,interno) values
           ('$regiao','$row_listabancos[1]','$banco','$row_listabancos[2]','$localidade','$conta','$agencia','$endereco','$tel','$gerente','$row_listabancos[site]','$projeto','$interno')") or die("$mensagem_erro");
           // Log - Cadastro de Bancos
       $qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
       $funcionario = mysql_fetch_array($qr_funcionario);
       $local = "Cadastro de Bancos";
       $ip = $_SERVER['REMOTE_ADDR'];
       $acao = "$funcionario[nome] cadastrou o banco $banco";
       mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao)
           VALUES ('$funcionario[0]', '$funcionario[id_regiao]', '$funcionario[tipo_usuario]', '$funcionario[grupo_usuario]', '$local', NOW(), '$ip', '$acao')") or die("Erro Inesperado<br><br>" . mysql_error());
           //
       print "
       <html><head><title>:: Intranet ::</title>
       <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
       <link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
       </head><body bgcolor='#D7E6D5'>";
       print "<br><br><br><span class='style27'> Informações gravadas com sucesso! </span><br><a href='cadastro.php?id=6&regiao=$regiao'><img src='imagens/voltar.gif' border=0></a>";
       break;
       case 10:                    //RESULTADOS DA AVALaliaçlão
       $radio1 = $_REQUEST['radio1'];
       $radio2 = $_REQUEST['radio2'];
       $radio3 = $_REQUEST['radio3'];
       $radio4 = $_REQUEST['radio4'];
       $radio5 = $_REQUEST['radio5'];
       $radio6 = $_REQUEST['radio6'];
       $obs = $_REQUEST['descricao'];
       $id_regiao = $_REQUEST['id_regiao'];
       $projeto = $_REQUEST['id_projeto'];
       $id_bolsista = $_REQUEST['bolsista'];
       $tipo = $_REQUEST['tipo'];
       include "include_avaliacao.php";
       $radio_total = "$radio1,$radio2,$radio3,$radio4,$radio5,$radio6";
       if ($tipo == 'clt') {
           $result_bol = mysql_query("SELECT * FROM rh_clt where id_clt = '$id_bolsista'");
           $row_bol = mysql_fetch_array($result_bol);
       } else {
           $result_bol = mysql_query("SELECT * FROM autonomo where id_autonomo = '$id_bolsista'");
           $row_bol = mysql_fetch_array($result_bol);
       }
       $result_pro = mysql_query("SELECT * FROM projeto where id_projeto = '$row_bol[id_projeto]'");
       $row_pro = mysql_fetch_array($result_pro);
       $result_curso = mysql_query("Select * from curso where id_curso = $row_bol[id_curso]");
       $row_curso = mysql_fetch_array($result_curso);
       $data_hj = date('d/m/Y');
       if ($status != 'avaliado') {
           if ($tipo == 'clt') {
               mysql_query("UPDATE rh_clt SET psicologia = '$radio_total', obs = '$obs', id_psicologia = '1' where id_clt = '$id_bolsista'");
           } else {
               mysql_query("UPDATE autonomo SET psicologia = '$radio_total', obs = '$obs', id_psicologia = '1' where id_autonomo = '$id_bolsista'");
           }
       }
       print "
       <html><head><title>:: Intranet ::</title>
       <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
       <link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
       </head><body bgcolor='#D7E6D5'>";
       print "
       <table width='100%' height='100%' border='0' cellpadding='0' cellspacing='0'>
       <tr>
       <td align='center' valign='top'><table width='750' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'>
       <tr align='center' valign='top'>
       <td width='20' rowspan='2'> <div align='center'></div></td>
       <td align='left'>
       <table width='100%' border='0' cellspacing='0' cellpadding='0'>
       <tr>
       <td align='center'><br>
       <span class='style4'><img src='imagens/certificadosrecebidos.gif' width='120' height='86' align='middle'>
       <strong>RESULTADOS DA AVALI&Ccedil;&Atilde;O DE DESEMPENHO INDIVIDUAL</strong></span></td>
       </tr>
       </table>
       <blockquote>
       <center><font face='arial' size='3' color='red'>Projeto: $row_pro[nome]</font></center><BR><BR>
       <font face='arial' size='2' color='red'>Nome:&nbsp; <strong>$row_bol[nome]  &nbsp;-&nbsp;RG: $row_bol[rg]</strong></font><br><br>
       <font face='arial' size='2' color='red'> Unidade:&nbsp; <strong>$row_bol[locacao]</strong></font><br><br>
       <font face='arial' size='2' color='red'>1. Compet&ecirc;ncia  Demonstrada</font><BR>
       <font face='arial' size='1' color='black'>$msg1</font>
       <font face='arial' size='2' color='red'>
       <br><br> 2.  Iniciativa Para o Desenvolvimento Profissional</font><br>
       <font face='arial' size='1' color='black'>$msg2</font>
       <font face='arial' size='2' color='red'>
       <br><br>3.  Potencial Para Promo&ccedil;&atilde;o</font><br>
       <font face='arial' size='1' color='black'>$msg3</font>
       <font face='arial' size='2' color='red'>
       <br><br>4.  Resultados e Contribui&ccedil;&atilde;o&nbsp;</font><br>
       <font face='arial' size='1' color='black'>$msg4</font>
       <font face='arial' size='2' color='red'>
       <br><br>5.  Solu&ccedil;&atilde;o de Problemas</font><br>
       <font face='arial' size='1' color='black'>$msg5</font>
       <font face='arial' size='2' color='red'>
       <br><br>6.  Desenvolvimento Profissional &nbsp;                &nbsp;</font><br>
       <font face='arial' size='1' color='black'>$msg6</font><br><br><br>
       <font face='arial' size='2' color='red'>OBSERVA&Ccedil;&Otilde;ES:</font><BR><BR>
       <font face='arial' size='2' color='red'><strong>$obs</strong></font><br>
       </p>
       <p class='style4'>&nbsp;</p>
       <br>
       <table width='80%' border='0' cellspacing='0' cellpadding='0' align='center'>
       <tr>
       <td width='50%' align='center'> <img src='imagens/assinaturafatima.gif' width='121' height='150'></td>
       <td width='50%' align='center'>_______________________________________________<br>COORDENADOR RESPONSÁVEL</td>
       </tr>
       </table>
       <br>
       <hr align='center'>
       <div align='center'><strong>INSTITUTO  &ldquo;SORRINDO PARA A VIDA&rdquo; - C.G.C. 06.888.897/0001-18</strong><br>
       Av. S&atilde;o Luiz, 112 - 18&ordm;. andar - Cj 1802 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Rua da Assembleia,   10 - Cj 2617- Centro &nbsp;&nbsp;&nbsp;<BR>
       S&atilde;o Paulo - SP -   CEP 01046-000   - (11) 3255-6959&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;Rio de Janeiro - RJ - CEP 20011-901 - (21) 2252-8901<BR>
       </div>
       <div align='center'><BR>
       <BR>
       </p>
       </div>
       </blockquote>          </td>
       <td width='20' rowspan='2'>&nbsp;</td>
       </tr>
       <tr>
       <td bgcolor='#8FC2FC' class='igreja' height='12'>
       <div align='center'></div></td>
       </tr>
       </table>    </td>
       </tr>
       </table>";
       print "<br><br><center><span class='style27'>Informações gravadas com sucesso! </span><br><br><a href='bolsista_class.php?id=2&projeto=$row_bol[id_projeto]&regiao=$id_regiao'><img src='imagens/voltar.gif' border=0></a><center>";
       break;
       case 11:
       $id_tarefa = $_REQUEST['id_tarefa'];
       $mensagem = $_REQUEST['texto'];
       $result = mysql_query("SELECT * FROM tarefa WHERE id_tarefa = '$id_tarefa'");
       $row = mysql_fetch_array($result);
       $date = Date('Ymd');
       mysql_query("UPDATE tarefa SET status_tarefa = '0' , tarefa_todos = '0' where id_tarefa = '$id_tarefa'");
       mysql_query("INSERT INTO tarefa(id_regiao,tipo_tarefa,criador,grupo,usuario,tarefa,descricao,data_criacao,data_entrega,status_tarefa) VALUES
           ('$row[id_regiao]','5','$row[usuario]','$row[grupo]','$row[criador]','$row[tarefa] - Concluída','$mensagem','$date','$date','0')") or die("$mensagem_erro");
       print "
       <html><head><title>:: Intranet ::</title>
       <script>
       function fechar_janela(x) {
           opener.location.href = 'principal.php';
           return eval(x)
       }
       </script>
       <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
       <link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
       </head><body bgcolor='#D7E6D5'>";
       print "<br><br><center><span class='style27'><br> Informações gravadas com sucesso! </span><br><br><a href='javascript:fechar_janela(window.close())'><img src='imagens/voltar.gif' border=0></a><center>";
       break;
       case 12:      //CADASRTO DE CURSOS - ATIVIDADE
       //print_r($_REQUEST);
       ?>
       <html>
       <title>:: Intranet ::</title>
       <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
       <head>
           <link href="adm/css/estrutura.css" rel="stylesheet" type="text/css">
           <style>
           .btn{
               padding:5px;
               text-decoration: none;
               background-color: #004E9B;
               color:#FFF;
               border: 2px solid  #9D9D9D;
           }
           .btn:hover{
               background-color:#D3D3D3;
               color:#000;
           }
           </style>
           <div id="corpo">
               <div id="conteudo">
                   <?php
                   $id_regiao = $_REQUEST['regiao'];
                   $projeto = $_REQUEST['projeto'];
                   $contratacao = $_REQUEST['contratacao'];
                   $atividade = $_REQUEST['atividade'];
                   $id_cbo = $_REQUEST['id_cbo'];
                   $salario = str_replace(',', '.', str_replace(".","",$_REQUEST['salario']));
                   $mes_abono = $_REQUEST['mes_abono'];
                   $enquadramento = $_REQUEST['enquadramento'];
                   $tipo = $_REQUEST['tipo'];
                   $nome = $_REQUEST['atividade'];
                   $area = $_REQUEST['area'];
                   $local = $_REQUEST['local'];
                   $ini = $_REQUEST['ini'];
                   $fim = $_REQUEST['fim'];
                   $valor = str_replace(',','.',str_replace('.','',$_REQUEST['valor']));
                   $qnt_maxima = $_POST['qnt_maxima'];
                   $inicio = ConverteData($ini);
                   $termino = ConverteData($fim);
                   $parcelas = $_REQUEST['parcelas'];
                   $descricao = $_REQUEST['descricao'];
                   $id_user = $_COOKIE['logado'];
                   $data_cad = date('Y-m-d');
           ///horarios
                   $nome_horario = $_REQUEST['nome_horario'];
                   $obs = $_REQUEST['obs'];
                   $entrada1 = $_REQUEST['entrada1'];
                   $saida1 = $_REQUEST['saida1'];
                   $entrada2 = $_REQUEST['entrada2'];
                   $saida2 = $_REQUEST['saida2'];
                   $hora_mes = $_REQUEST['hora_mes'];
                   $dias_semana = $_REQUEST['dias_semana'];
                   $dias_mes = $_REQUEST['dias_mes'];
                   $folga1 = $_REQUEST['folga1'];
                   $folga2 = $_REQUEST['folga2'];
                   $folga3 = $_REQUEST['folga3'];
           if ($folga1 == "1" and $folga2 == "2") {// SEGUNDA A SEXTA
               $folga = "3";
           } elseif ($folga1 == "1") {// FOLGA NO SABADO
               $folga = "1";
           } elseif ($folga2 == "2") {// FOLGA NO DOMINGO
               $folga = "2";
           } elseif ($folga3 == "5") {// PLANTONISTA
               $folga = "5";
           } else {
               $folga = "0";  //SEM FOLGAS ( SEGUNDA À SEGUNDA )
           }
           if ($contratacao == "2") {
               $nome = $atividade;
           }
           $id_user = $_COOKIE['logado'];
           $data_cad = date('Y-m-d');
           //-- INICIANDO O CALCULO DO SALARIO PARA RETIRAR O VALOR DIARIO E O VALOR HORA
           $diaria = $salario / 30;
           $hora = $diaria / 8;
           $diaria = str_replace(",", ".", $diaria);
           $diaria_f = number_format($diaria, 2, ",", ".");
           $hora = str_replace(",", ".", $hora);
           $hora_f = number_format($hora, 2, ",", ".");

           $result_cbo = mysql_query("SELECT * FROM rh_cbo where id_cbo = '$id_cbo'");
           $row_cbo = mysql_fetch_array($result_cbo);

           $result_cont = mysql_query("SELECT COUNT(*) FROM curso WHERE nome = '$nome' AND campo3 = '$projeto' AND tipo = '$contratacao'");
           $row_cont = mysql_fetch_array($result_cont);
           if ($row_cont['0'] >= "1") {
               ?>
               <table class="relacao" align="center" style="margin-top:20px;">
                   <tr class="">
                       <td align="center">Ja Existe uma Atividade cadastrada com este nome nesse Projeto!<br>Favor Cadastrar outra Atividade</td>
                   </tr>
                   <tr>
                       <td align="center"><a href='javascript:history.go(-1)'><img src='imagens/voltar.gif' border=0></a></td>
                   </tr>
               </table>
               <?php
           } else {
               mysql_query("INSERT INTO curso(nome,area,id_regiao,local,inicio,termino,descricao,valor,parcelas,campo1,campo2,campo3,cbo_nome,cbo_codigo,salario,ir,mes_abono,id_user,data_cad,tipo,qnt_maxima, hora_mes) VALUES
                   ('$nome','$area','$id_regiao','$local','$inicio','$termino','$descricao','$salario','$parcela','$tipo','$atividade','$projeto','$row_cbo[nome]','$id_cbo','$salario','$enquadramento','$mes_abono','$id_user','$data_cad','$contratacao','$qnt_maxima','$horas_mes')") or die("$mensagem_erro");
               $id_curso = mysql_insert_id();
                           // Log - Cadastro de Cursos
               $qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
               $funcionario = mysql_fetch_array($qr_funcionario);
               $local = "Cadastro de Cursos";
               $ip = $_SERVER['REMOTE_ADDR'];
               $acao = "$funcionario[nome] cadastrou o curso $atividade";
               mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao)
                   VALUES ('$funcionario[0]', '$funcionario[id_regiao]', '$funcionario[tipo_usuario]', '$funcionario[grupo_usuario]', '$local', NOW(), '$ip', '$acao')") or die("Erro Inesperado<br><br>" . mysql_error());
                           /////////////////////////////////////////////////
                           /////////////////CADASTRO DE HORÁRIOS ////////////
                           //////////////////////////////////////////////////
               mysql_query("INSERT INTO rh_horarios(id_regiao,nome,obs,entrada_1,saida_1,entrada_2,saida_2,dias_semana,horas_mes,
                   salario,funcao,valor_dia,valor_hora,folga, dias_mes)
               VALUES ('$id_regiao','$nome_horario','$obs','$entrada1','$saida1','$entrada2','$saida2','$dias_semana','$hora_mes',
                   '$salario','$id_curso','$diaria_f','$hora_f','$folga', '$dias_mes') ") or die("<hr>Erro no insert<br><hr>" . mysql_error());
                           /////////////////////////////////////////////// FIM CADASTRO horário ////////////////////////
                   ?>
                   <table class="relacao" align="center" style="margin-top:20px;">
                       <tr class="">
                           <td align="center">Informações gravadas com sucesso!</td>
                       </tr>
                       <tr height="60">
                           <td align="center"><a href='javascript:window.close()' class="btn">FECHAR</a> &nbsp;&nbsp;&nbsp;<a href='atividades/cadastrar_atividade_clt.php?regiao=<?php echo $id_regiao ?>&id_user=<?php echo $_COOKIE['logado']; ?>'  class="btn">CADASTRAR NOVA FUNÇÃO</a></td>
                       </tr>
                   </table>
                   <?php } ?>
               </div>
           </div>
       </body>
       </html>
       <?php
       break;
                   case 13:     //TROCANDO DE REGIÃO
                   $user = $_REQUEST['user'];
                   $regiao = $_REQUEST['regiao'];
                   $regiao_de = $_REQUEST['regiao_de'];
                       //GRAVANDO SESSÃO
                   $_SESSION['id_regiao'] = $regiao;
                       //GRAVANDO SESSÃO
                   $result_regiao_1 = mysql_query("SELECT regiao FROM regioes where id_regiao = '$regiao_de'");
                   $row_regiao_1 = mysql_fetch_array($result_regiao_1);
                   $result_regiao_2 = mysql_query("SELECT regiao FROM regioes where id_regiao = '$regiao'");
                   $row_regiao_2 = mysql_fetch_array($result_regiao_2);
                   mysql_query("UPDATE funcionario set id_regiao = '$regiao' where id_funcionario = '$user'");
                       //----- INI -- GRAVANDO AS INFORMAÇÕES DO LOGIN NA TABELA LOG
                   $result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$user'");
                   $row_user = mysql_fetch_array($result_user);
                       $ip = $_SERVER['REMOTE_ADDR'];  //PEGANDO O IP
                       $local = "TROCA DE REGIÃO";
                       $horario = date('Y-m-d H:i:s');
                       $acao = "SAIU DE: $row_regiao_1[0] PARA: $row_regiao_2[0]";
                       mysql_query("INSERT INTO log (id_user,id_regiao,tipo_user,grupo_user,local,horario,ip,acao)
                           VALUES ('$user','$regiao_de','$row_user[tipo_usuario]',
                               '$row_user[grupo_usuario]','$local','$horario','$ip','$acao')") or die("Erro Inesperado<br><br>" . mysql_error());
                       //----- FIM -- GRAVANDO AS INFORMAÇÕES DO LOGIN NA TABELA LOG
                       /* if($user == 87) {
                         print "
                         <script>
                         location.href = 'teste_index.php';
                         </script>
                         ";
                         } else {
                        */
                           print "
                           <script>
                           location.href = 'index.php';
                           </script>
                           ";
                       //}
                           break;
                   case 14:       //MARCANDO PONTO
                   $user = $_REQUEST['user'];
                   $regiao = $_REQUEST['regiao'];
                   $data = date('Y/m/d');
                   $hora = date('H:i');
                   $radio = $_REQUEST['radiobutton'];
                   print "
                   <html><head><title>:: Intranet ::</title>
                   <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
                   <link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
                   </head><body bgcolor='#D7E6D5'>";
                   switch ($radio) {
                       case 1:
                       $hora1 = $hora;
                       $hora2 = "";
                       $hora3 = "";
                       $hora4 = "";
                       $justifica = $_REQUEST['justifica1'];
                       mysql_query("INSERT INTO ponto(id_regiao,id_funcionario,data,entrada1,saida1,entrada2,saida2,justifica1) VALUES
                           ('$regiao','$user','$data','$hora1','$hora2','$hora3','$hora4','$justifica')") or die("$mensagem_erro");
                       break;
                       case 2:
                       $hora2 = $hora;
                       $justifica = $_REQUEST['justifica2'];
                       mysql_query("UPDATE ponto SET saida1 = '$hora2', justifica2 = '$justifica' where id_funcionario = '$user' and id_regiao = '$regiao' and data = '$data' ");
                       break;
                       case 3:
                       $hora3 = $hora;
                       $justifica = $_REQUEST['justifica3'];
                       mysql_query("UPDATE ponto SET entrada2 = '$hora3', justifica3 = '$justifica' where id_funcionario = '$user' and id_regiao = '$regiao' and data = '$data' ");
                       break;
                       case 4:
                       $hora4 = $hora;
                       $justifica = $_REQUEST['justifica4'];
                       mysql_query("UPDATE ponto SET saida2 = '$hora4', justifica4 = '$justifica' where id_funcionario = '$user' and id_regiao = '$regiao' and data = '$data' ");
                       break;
                   }
                       //alert(\"Informações cadastradas com sucesso!\");
                   print "
                   <script>
                   location.href=\"cadastro.php?id=10&id_reg=$regiao&id_user=$user\"
                   </script>";
                       //cadastro.php?id=10&id_reg=3&id_user=1
                   break;
                   case 15:    //CADASTRO DE ATIVIDADES
                   $nome = $_REQUEST['nome'];
                   $area = $_REQUEST['area'];
                   $id_regiao = $_REQUEST['regiao'];
                   $descricao = $_REQUEST['descricao'];
                   mysql_query("INSERT INTO atividade(id_regiao,nome,area,descricao) VALUES
                       ('$id_regiao','$nome','$area','$descricao')") or die("$mensagem_erro");
                   print "<br><br><center><span class='style27'>Informações gravadas com sucesso! </span><br><br><a href='javascript:window.close()'><img src='imagens/sair.gif' border=0></a><center>";
                   break;
                   case 16:     //EDITANDO O CADASTRO DE FUNCIONÁRIO
                       /* print_r($_REQUEST['permissao_regiao_por_area']);
                         exit;
                        */
   ////PERMISSÃO PARA VISUALIZAR AS REGIÕES NA PARTE DE RELATÓRIOS FINANCEIROS
                         $array_regioes_relatorio = $_REQUEST['regiao_relatorios'];
                         mysql_query("DELETE FROM regioes_relatorios_assoc WHERE id_funcionario = '$_COOKIE[logado]'");
                         if(sizeof($array_regioes_relatorio) >0){
                           foreach($array_regioes_relatorio as $id_regiao_relatorio){
                               mysql_query("INSERT INTO regioes_relatorios_assoc (id_regiao, id_funcionario) VALUES ('$id_regiao_relatorio', '$_COOKIE[logado]' )") or die (mysql_error());
                           }
                       }
                       $id_regiao = $_REQUEST['id_regiao'];
                       $id_funcionario = $_REQUEST['id_funcionario'];
                       $result_regiao = mysql_query("SELECT * FROM regioes where id_regiao = '$id_regiao'");
                       $row_regiao = mysql_fetch_array($result_regiao);
                       $regiao = "$row_regiao[regiao] - $row_regiao[sigla]";
                       $pag = $_REQUEST['pag'];
                       $master = $_REQUEST['master'];
                       $funcao = $_REQUEST['funcao'];
                       $locacao = $_REQUEST['locacao'];
                       $nome = $_REQUEST['nome'];
                       $endereco = $_REQUEST['endereco'];
                       $bairro = $_REQUEST['bairro'];
                       $cidade = $_REQUEST['cidade'];
                       $uf = $_REQUEST['uf'];
                       $cep = $_REQUEST['cep'];
                       $tel_fixo = $_REQUEST['tel_fixo'];
                       $tel_cel = $_REQUEST['tel_cel'];
                       $tel_rec = $_REQUEST['tel_rec'];
                       $data_nasci = implode('-',array_reverse(explode('/',$_REQUEST['nasc_dia'])));
                       $naturalidade = $_REQUEST['naturalidade'];
                       $nacionalidade = $_REQUEST['nacionalidade'];
                       $civil = $_REQUEST['civil'];
                       $rg = $_REQUEST['rg'];
                       $secao = $_REQUEST['secao'];
                       $data_rg = $_REQUEST['data_rg'];
                       $cpf = $_REQUEST['cpf'];
                       $titulo = $_REQUEST['titulo'];
                       $zona = $_REQUEST['zona'];
                       $orgao = $_REQUEST['orgao'];
                       $pai = $_REQUEST['pai'];
                       $mae = $_REQUEST['mae'];
                       $estuda = $_REQUEST['estuda'];
                       $escola_dia = $_REQUEST['escola_dia'];
                       $escola_mes = $_REQUEST['escola_mes'];
                       $escola_ano = $_REQUEST['escola_ano'];
                       $data_escola = "$escola_ano$escola_mes$escola_dia";
                       $escolaridade = $_REQUEST['escolaridade'];
                       $instituicao = $_REQUEST['instituicao'];
                       $curso = $_REQUEST['curso'];
                       $ctps = $_REQUEST['ctps'];
                       $serie_ctps = $_REQUEST['serie_ctps'];
                       $uf_ctps = $_REQUEST['uf_ctps'];
                       $pis = $_REQUEST['pis'];
                       $banco = $_REQUEST['banco'];
                       $agencia = $_REQUEST['agencia'];
                       $conta = $_REQUEST['conta'];
                       $salario = $_REQUEST['salario'];
                       $grupo_usuario = $_REQUEST['grupo_usuario'];
                       $nome1 = $_REQUEST['nome1'];
                       $tipo_usuario = $_REQUEST['tipo_usuario'];
                       $foto = $_REQUEST['foto'];
                       if ($foto == "3") {
                           $foto_banco = "0";
                           $foto_up = "0";
                       } elseif ($foto == "1") {
                           $foto_banco = "1";
                           $foto_up = "1";
                       } else {
                           $vendo_foto = mysql_query("SELECT foto FROM funcionario WHERE id_funcionario = '$id_funcionario'");
                           $row_vendo_foto = mysql_fetch_array($vendo_foto);
                           $foto_banco = "$row_vendo_foto[foto]";
                           $foto_up = "0";
                       }
                       /*
                         function ConverteData($Data){
                         if (strstr($Data, "/"))//verifica se tem a barra /
                         {
                         $d = explode ("/", $Data);//tira a barra
                         $rstData = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mes etc...
                         return $rstData;
                         } elseif(strstr($Data, "-")){
                         $d = explode ("-", $Data);
                         $rstData = "$d[2]/$d[1]/$d[0]";
                         return $rstData;
                         }else{
                         return "";
                         }
                     } */
                       /////////////////////////////////////////////
                       ///// CADASTRANDO E-MAIL DO USUÁRIO /////////
                       /////////////////////////////////////////////
                     $array_master_email = $_POST['master_email'];
                     $array_email = $_POST['email'];
                     $array_senha = $_POST['senha_email'];
                     mysql_query("DELETE FROM funcionario_email_assoc WHERE id_funcionario = '$id_funcionario'");
                     mysql_query("DELETE FROM funcionario_master WHERE id_funcionario = '$id_funcionario' ");
                     foreach ($array_master_email as $chave => $master_id) {
                       $sql[] = "( '" . $master_id . "', '" . $id_funcionario . "', '" . $array_email[$master_id] . "', '" . $array_senha[$master_id] . "' )";
                       $sql2[] = "( '" . $master_id . "', '" . $id_funcionario . "' )";
                   }
                   $sql = implode(',', $sql);
                   $sql2 = implode(',', $sql2);
                   mysql_query("INSERT INTO 	funcionario_email_assoc (id_master, id_funcionario, email, senha) VALUES $sql");
                   mysql_query("INSERT INTO 	funcionario_master (id_master, id_funcionario) VALUES $sql2");
                       //////////////////////////////////////////
                   $botoes = $_REQUEST['botoes'];
                   $array_acoes = $_REQUEST['acoes'];
                   $array_regioes = $_REQUEST['regioes_permitidas'];
                   $array_empresas = (array_unique($_REQUEST['empresas']));
                   $array_regioes_folha = $_REQUEST['regiao_folhas'];
                       ////////////////////////////////////////////////////////////////////////////////////////////////////////
                       ////    VERIFICA SE ALGUM BOTÃO EXISTENTE NA TABELA FOI DESMARCADO E DELETA CASO ESTEJA DESMARCADO  /////
                       ////////////////////////////////////////////////////////////////////////////////////////////////////////
                   $qr_botoes_assoc = mysql_query("SELECT * FROM botoes_assoc WHERE id_funcionario = '$id_funcionario' ");
                   while ($row_botoes_assoc = mysql_fetch_assoc($qr_botoes_assoc)):
                       if (!in_array($row_botoes_assoc['botoes_id'], $botoes)) {
                           mysql_query("DELETE FROM botoes_assoc WHERE botoes_id = '$row_botoes_assoc[botoes_id]' AND id_funcionario = '$id_funcionario'");
                       }
                       endwhile;
                       ////////////////////////////////////////////////////////////////////////////////////////////
                       ////////////////////////////////////////////////////
                       ///////////  ADICONA OS BOTÕES MARCADOS  ///////////
                       ////////////////////////////////////////////////////
                       if (!empty($botoes)) {
                           foreach ($botoes as $chave => $valor) {
                               $qr_botoes_assoc = mysql_query("SELECT * FROM botoes_assoc WHERE botoes_id = '$valor'  AND id_funcionario = '$id_funcionario'");
                               if (mysql_num_rows($qr_botoes_assoc) == 0) {
                                   mysql_query("INSERT INTO botoes_assoc (botoes_id, id_funcionario) VALUES ('$valor', '$id_funcionario');");
                               }
                           }
                       }
                       ///////////////////////////
                       ////////////////////////////////////////////////////
                       ///// Adicionando permissoes na tabela ações  //////
                       ////////////////////////////////////////////////////
                       if (count($array_acoes) != 0) {
                           mysql_query("DELETE FROM funcionario_acoes_assoc WHERE id_funcionario = '$id_funcionario' ");
                           foreach ($array_acoes as $acoes) {
                               mysql_query("INSERT INTO funcionario_acoes_assoc  (id_funcionario, acoes_id ) VALUES('$id_funcionario', '$acoes')") or die(mysql_error());
                           }
                       }
                       ////////////////////////////////////////////////////
                       //// ADICINANDO AS REGIÕES PERMITADAS PARA O USUÁRIO
                       ////////////////////////////////////////////////////
                       if (count($array_regioes) != 0) {
                           mysql_query("DELETE FROM funcionario_regiao_assoc WHERE id_funcionario = '$id_funcionario'") or die(mysql_error());
                           foreach ($array_empresas as $id_master) {
                               if ($array_regioes[$id_master] != 0) {
                                   foreach ($array_regioes[$id_master] as $id_regiao) {
                                       mysql_query("INSERT INTO funcionario_regiao_assoc (id_funcionario, id_regiao, id_master) VALUES ('$id_funcionario', '$id_regiao','$id_master' ) ") or die(mysql_error());
                                   }
                               }
                           }
                       }
                       /////////////////////////////////////////////////
                       ////PERMISSOES DE REGIÃO PARA AS FOLHAS	////////
                       ///////////////////////////////////////////////
                       $qr_acoes = mysql_query("SELECT * FROM acoes WHERE botoes_id  = 33 or botoes_id = 60");
                       while ($row_acoes = mysql_fetch_assoc($qr_acoes)):
                           mysql_query("DELETE FROM funcionario_acoes_assoc WHERE id_funcionario = '$id_funcionario'  AND acoes_id = '$row_acoes[acoes_id]'");
                       if (in_array($row_acoes['acoes_id'], $array_acoes) and (sizeof($array_regioes_folha[$row_acoes['botoes_id']])) >= 1) {
                           foreach ($array_regioes_folha[$row_acoes['botoes_id']] as $id_regiao) {
                               mysql_query("INSERT INTO funcionario_acoes_assoc  (id_funcionario, botoes_id, id_regiao,acoes_id ) VALUES('$id_funcionario', '$row_acoes[botoes_id]', '$id_regiao', '$row_acoes[acoes_id]')") or die(mysql_error());
                           }
                       } else if (in_array($row_acoes['acoes_id'], $array_acoes) and (sizeof($array_regioes_folha[$row_acoes['botoes_id']])) == 0) {
                           mysql_query("INSERT INTO funcionario_acoes_assoc  (id_funcionario,acoes_id ) VALUES('$id_funcionario', '$row_acoes[acoes_id]')");
                       }
                       endwhile;
                       ////////////////////////////////////////////////////////////////////////////////////
                       ////PERMISSOES PARA VISUALIZAR OS ACOMPANHAMENTOS DA GESTÃO DE COMPRAS	////////
                       ///////////////////////////////////////////////	////////////////////////////////////////
                       $array_acomp_compras = $_POST['acomp_compra'];
                       mysql_query("DELETE FROM func_acompanhamento_assoc WHERE id_funcionario = '$id_funcionario'");
                       $sql = array();
                       foreach ($array_acomp_compras as $id_acompanhamento) {
                           $sql[] = "($id_funcionario, $id_acompanhamento)";
                       }
                       $sql = implode(',', $sql);
                       $inserir = mysql_query("INSERT INTO func_acompanhamento_assoc (id_funcionario, id_acompanhamento) VALUES $sql");
                       if ($foto_up == "1") {          //AQUI TEM ARQUIVO
                           $arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;
                           if ($arquivo[type] != "image/x-png" && $arquivo[type] != "image/pjpeg" && $arquivo[type] != "image/gif"
                                   && $arquivo[type] != "image/jpe") {     //aki a imagem nao corresponde com as extenções especificadas
                               print "<center>
                           <hr><font size=2><b>
                           Tipo de arquivo não permitido, os únicos padrões permitidos são .gif, .jpg , .jpeg ou .png<br>
                           $arquivo[type] <br><br>
                           <a href='cadastro.php?id=12&regiao=$id_regiao'>Voltar</a>
                           </b></font>";
                           exit;
                           } else {  //aqui o arquivo é realente de imagem e vai ser carregado para o servidor
                               $arr_basename = explode(".", $arquivo['name']);
                               $file_type = $arr_basename[1];
                               if ($file_type == "gif") {
                                   $tipo_name = ".gif";
                               } if ($file_type == "jpg" or $arquivo[type] == "jpeg") {
                                   $tipo_name = ".jpg";
                               } if ($file_type == "png") {
                                   $tipo_name = ".png";
                               }
                               $foto = $tipo_name;
                               mysql_query("update funcionario set id_master = '$master', tipo_usuario = '$tipo_usuario', grupo_usuario = '$grupo_usuario', nome = '$nome' ,salario = '$salario',  funcao = '$funcao', locacao = '$locacao', endereco = '$endereco', bairro = '$bairro', cidade = '$cidade', uf = '$uf', cep = '$cep', tel_fixo = '$tel_fixo', tel_cel = '$tel_cel', tel_rec = '$tel_rec', data_nasci = '$data_nasci', naturalidade = '$naturalidade', nacionalidade = '$nacionalidade', civil = '$civil', ctps = '$ctps', serie_ctps = '$serie_ctps',uf_ctps = '$uf_ctps', pis = '$pis', rg = '$rg', orgao = '$orgao', data_rg = '$data_rg', cpf = '$cpf', titulo = '$titulo', zona = '$zona', secao = '$secao', pai = '$pai', mae = '$mae', estuda = '$estuda', data_escola = '$data_escola', escolaridade = '$escolaridade', instituicao = '$instituicao', curso = '$curso', foto = '$foto',  banco = '$banco', agencia = '$agencia' ,conta = '$conta' where id_funcionario = '$id_funcionario'") or die("$mensagem_erro<br>Favor Voltar");
                               // Resolvendo o nome e para onde o arquivo será movido
                               $diretorio = "fotos/";
                               $nome_tmp = $id_regiao . "funcionario" . $id_funcionario . $tipo_name;
                               $nome_arquivo = "$diretorio$nome_tmp";
                               move_uploaded_file($arquivo['tmp_name'], $nome_arquivo) or die("Erro ao enviar o Arquivo: $nome_arquivo");
                           } //aqui fecha o IF que verificar se o arquivo tem a extenção especificada
                       } else { //caso não venha a foto
                           mysql_query("update funcionario set id_master = '$master', tipo_usuario = '$tipo_usuario', grupo_usuario = '$grupo_usuario', nome = '$nome' ,salario = '$salario',funcao = '$funcao', locacao = '$locacao', endereco = '$endereco', bairro = '$bairro', cidade = '$cidade', uf = '$uf', cep = '$cep', tel_fixo = '$tel_fixo', tel_cel = '$tel_cel', tel_rec = '$tel_rec', data_nasci = '$data_nasci', naturalidade = '$naturalidade', nacionalidade = '$nacionalidade', civil = '$civil', ctps = '$ctps', serie_ctps = '$serie_ctps',uf_ctps = '$uf_ctps', pis = '$pis', rg = '$rg', orgao = '$orgao', data_rg = '$data_rg', cpf = '$cpf', titulo = '$titulo', zona = '$zona', secao = '$secao', pai = '$pai', mae = '$mae', estuda = '$estuda', data_escola = '$data_escola', escolaridade = '$escolaridade', instituicao = '$instituicao', curso = '$curso', foto = '$foto_banco',  banco = '$banco', agencia = '$agencia' ,conta = '$conta' where id_funcionario = '$id_funcionario'") or die("$mensagem_erro<br>Favor Voltar");
                       }
                       if ($pag == "1") {
                           $link = "ver_tudo.php?id=19";
                       } else {
                           $link = "ver_tudo.php?id=6&regiao=$id_regiao";
                       }
                       print "
                       <script>
                       alert(\"Informações cadastradas com sucesso!\");
                       location.href=\"$link\";
                       </script>";
                       break;
                   case 17:       //CADASTRO DE UNIDADES
                   $id_regiao = $_REQUEST['regiao'];
                   $nome = $_REQUEST['nome'];
                   $local = $_REQUEST['local'];
                   $tel = $_REQUEST['tel'];
                   $tel2 = $_REQUEST['tel2'];
                   $tel = $_REQUEST['tel'];
                   $responsavel = $_REQUEST['responsavel'];
                   $cel = $_REQUEST['cel'];
                   $email = $_REQUEST['email'];
                   $projeto = $_REQUEST['projeto'];
                   $endereco = $_REQUEST['endereco'];
                   $bairro = $_REQUEST['bairro'];
                   $cidade = $_REQUEST['cidade'];
                   $cep = $_REQUEST['cep'];
                   $ponto_referencia = $_REQUEST['ponto_referencia'];
                   mysql_query("INSERT INTO unidade(id_regiao,unidade,local,tel,tel2,responsavel,cel,email,campo1, endereco, bairro, cidade, cep, ponto_referencia) VALUES
                       ('$id_regiao','$nome','$local','$tel','$tel2','$responsavel','$cel','$email','$projeto','$endereco','$bairro','$cidade','$cep','$ponto_referencia')") or die(mysql_error());
                       // Log - Cadastro de Unidades
                   $qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
                   $funcionario = mysql_fetch_array($qr_funcionario);
                   $local = "Cadastro de Unidades";
                   $ip = $_SERVER['REMOTE_ADDR'];
                   $acao = "$funcionario[nome] cadastrou a unidade $nome";
                   mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao)
                       VALUES ('$funcionario[0]', '$funcionario[id_regiao]', '$funcionario[tipo_usuario]', '$funcionario[grupo_usuario]', '$local', NOW(), '$ip', '$acao')") or die("Erro Inesperado<br><br>" . mysql_error());
                   print "<br><br><center><span class='style27'>Informações gravadas com sucesso!</span><br><br><a href='javascript:window.close()'><img src='imagens/sair.gif' border=0></a><center>";
                   break;
                   case 18:   //JOGANDO AS FALTAS DO AUTONOMO PARA CALCULO DA FOLHA DE PAGAMENTO
                   $id_regiao = $_REQUEST['id_regiao'];
                   $id_bolsista = $_REQUEST['id_bolsista'];
                   $id_projeto = $_REQUEST['projeto'];
                   $faltas = $_REQUEST['faltas'];
                   $adicional = $_REQUEST['adicional'];
                   $desconto = $_REQUEST['desconto'];
                   $mes = $_REQUEST['mes'];
                   $qnt_dias = $_REQUEST['qnt_dias'];
                   $adicional = str_replace(".", "", $adicional);
                   $desconto = str_replace(".", "", $desconto);
                   $result = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$id_bolsista'") or die("Erro no SELECT 1 $id_projeto - $id_bolsista");
                   $row = mysql_fetch_array($result);
                   $result_cur = mysql_query("SELECT * FROM curso WHERE id_curso = '$row[id_curso]'") or die("Erro no SELECT 2");
                   $row_cur = mysql_fetch_array($result_cur);
                   if (empty($_REQUEST['terceiro'])) {
                       $terceiro = "0";
                       $qnt_13 = "";
                       $ini_13 = "";
                       $valor_13 = "";
                   } else {
                       $terceiro = "1";
                       $qnt_13 = $_REQUEST['parcelas'];
                       $ini_13 = $_REQUEST['mes_pagamento'];
                       $salario = $row_cur['salario'];
                       $valor_13 = "$salario" / "$qnt_13";
                   }
                   if ($row['tipo_contratacao'] == "2") {
                       print "<br><br><center><span class='style27'>O Código digitado pertence a um CLT.<br> $row[nome]
                       </span><br><br><a href='ver_tudo.php?id=9&regiao=$id_regiao&id_projeto=$id_projeto'><img src='imagens/voltar.gif' border=0></a><center>";
                   } else {
                       mysql_query("UPDATE folha_$id_projeto SET faltas = '$faltas', adicional = '$adicional', desconto =  '$desconto', terceiro = '$terceiro', valor_13 = '$valor_13', ini_13 = '$mes', qnt_13 = '$qnt_13' WHERE id_autonomo = '$id_bolsista' and mes = '$mes'") or die("Erro no Insert");
                       print "
                       <html><head><title>:: Intranet ::</title>
                       <script>
                       alert(\"Informações gravadas\");
                       location.href=\"ver_tudo.php?id=14&id_projeto=$id_projeto&mes=$mes&qnt_dias=$qnt_dias&id_bolsista=$id_bolsista\"
                       </script>
                       <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
                       <link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
                       </head><body bgcolor='#D7E6D5'>";
                       print "<br><br><center><span class='style27'><br> Informações gravadas com sucesso! </span><br><br><a href='javascript:fechar_janela(window.close())'><img src='imagens/voltar.gif' border=0></a><center>";
                   }
                   break;
                   case 19:                    //CADASTRANDO TIPOS DE PAGAMENTOS
                   $id_regiao = $_REQUEST['regiao'];
                   $tipopg = $_REQUEST['tipopg'];
                   $projeto = $_REQUEST['projeto'];
                   $num = ($tipopg == "Depósito em Conta Corrente") ? "1" : "2";
                   mysql_query("INSERT INTO tipopg(id_regiao,id_projeto,tipopg,campo1) VALUES ('$id_regiao','$projeto','$tipopg','$num')") or die("$mensagem_erro");
                       // Log - Cadastro de Tipos de Pagamentos
                   $qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
                   $funcionario = mysql_fetch_array($qr_funcionario);
                   $local = "Cadastro de Tipos de Pagamentos";
                   $ip = $_SERVER['REMOTE_ADDR'];
                   $acao = "$funcionario[nome] cadastrou o tipo de pagamento $tipopg";
                   mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao)
                       VALUES ('$funcionario[0]', '$funcionario[id_regiao]', '$funcionario[tipo_usuario]', '$funcionario[grupo_usuario]', '$local', NOW(), '$ip', '$acao')") or die("Erro Inesperado<br><br>" . mysql_error());
                   print "<br><br><center><span class='style27'>Informações gravadas com sucesso! </span><br><br><a href='javascript:window.close()'><img src='imagens/sair.gif' border=0></a><center>";
                   break;
                   case 20:    //CADASTRANDO AS FALTAS DOS BOLSISTA PARA SER GERADO A FOLHA DE PAGAMENTO / ZERANDO O STATUS DA FOLHA
                   if (empty($_REQUEST['zokpower'])) {
                       $id_projeto = $_REQUEST['id_projeto'];
                       $id_bolsista = $_REQUEST['id_bolsista'];
                       $adicional = $_REQUEST['adicional'];
                       $desconto = $_REQUEST['desconto'];
                       $falta = $_REQUEST['falta'];
                       print "Primeiro else não manda o ZOKPOWER";
                       } elseif ($_REQUEST['zokpower'] == "321") {   //COLOCA E TIRA TODOS DAS LISTAS DA FOLHA E DE ADIANTAMENTO
                           $regiao = $_REQUEST['regiao'];
                           $id_projeto = $_REQUEST['id_projeto'];
                           $mes = $_REQUEST['mes'];
                           $sit_1 = $_REQUEST['sit_1'];
                           $sit_2 = $_REQUEST['sit_2'];
                           $qnt_dias = $_REQUEST['qnt_dias'];
                           $data_ini = $_REQUEST['data_ini'];
                           $data_fim = $_REQUEST['data_fim'];
                           if (empty($_REQUEST['tabela'])) {
                               $tabela = "_$id_projeto";
                               $link = "ver_tudo.php?id=13&id_projeto=$id_projeto&regiao=$regiao&data_ini=$data_ini&data_fim=$data_fim&qnt_dias=$qnt_dias&mes=$mes";
                           } else {
                               $tabela = "ad_$id_projeto";
                               $link = "adiantamento.php?id=2&id_projeto=$id_projeto&regiao=$regiao&data_ini=$data_ini&data_fim=$data_fim&mes=$mes";
                           }
                           mysql_query("UPDATE folha$tabela SET sit = '$sit_1' WHERE sit = '$sit_2' and mes = '$mes'");
                           print "
                           <html><head><title>:: Intranet ::</title>
                           <script>
                           function fechar_janela(x) {
                               opener.location.href = '$link';
                               return eval(x)
                           }
                           </script>
                           <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
                           <link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
                           </head><body bgcolor='#D7E6D5'>";
                           print "<br><br><center><span class='style27'><br> Informações gravadas com sucesso! </span><br><br><a href='javascript:fechar_janela(window.close())'><img src='imagens/voltar.gif' border=0></a><center>";
                       } elseif ($_REQUEST['zokpower'] == "322") {               //FOLHA ADICIONAL
                           $regiao = $_REQUEST['regiao'];
                           $id_projeto = $_REQUEST['id_projeto'];
                           $mes = $_REQUEST['mes'];
                           $sit_1 = $_REQUEST['sit_1'];
                           $qnt_dias = $_REQUEST['qnt_dias'];
                           $data_ini = $_REQUEST['data_ini'];
                           $data_fim = $_REQUEST['data_fim'];
                           $id_bolsista = $_REQUEST['id_bolsista'];
                           if ($sit_1 == "1") {
                               $sit_1 = "0";
                               $sit_2 = "1";
                           } else {
                               $sit_1 = "1";
                               $sit_2 = "0";
                           }
                           mysql_query("UPDATE folhaad_$id_projeto SET sit = '$sit_1' WHERE sit = '$sit_2' and mes = '$mes' and id_autonomo = '$id_bolsista'");
                           print "
                           <html><head><title>:: Intranet ::</title>
                           <script>
                           function fechar_janela(x) {
                               opener.location.href = 'adiantamento.php?id=2&id_projeto=$id_projeto&regiao=$regiao&data_ini=$data_ini&data_fim=$data_fim&mes=$mes';
                               return eval(x)
                           }
                           </script>
                           <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
                           <link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
                           </head><body bgcolor='#D7E6D5'>";
                           print "<br><br><center><span class='style27'><br> Informações gravadas com sucesso! </span><br><br><a href='javascript:fechar_janela(window.close())'><img src='imagens/voltar.gif' border=0></a><center>";
                       } else {  //DESATIVANDO BOLSISTA DA FOLHA NORMAL
                           $regiao = $_REQUEST['regiao'];
                           $id_projeto = $_REQUEST['id_projeto'];
                           $mes = $_REQUEST['mes'];
                           $sit_1 = $_REQUEST['sit_1'];
                           $qnt_dias = $_REQUEST['qnt_dias'];
                           $data_ini = $_REQUEST['data_ini'];
                           $data_fim = $_REQUEST['data_fim'];
                           $id_bolsista = $_REQUEST['id_bolsista'];
                           if ($sit_1 == "1") {
                               $sit_1 = "0";
                               $sit_2 = "1";
                           } else {
                               $sit_1 = "1";
                               $sit_2 = "0";
                           }
                           mysql_query("UPDATE folha_$id_projeto SET sit = '$sit_1' WHERE sit = '$sit_2' and mes = '$mes' and id_autonomo = '$id_bolsista'");
                           print "
                           <html><head><title>:: Intranet ::</title>
                           <script>
                           function fechar_janela(x) {
                               opener.location.href = 'ver_tudo.php?id=13&id_projeto=$id_projeto&regiao=$regiao&data_ini=$data_ini&data_fim=$data_fim&qnt_dias=$qnt_dias&mes=$mes';
                               return eval(x)
                           }
                           </script>
                           <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
                           <link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
                           </head><body bgcolor='#D7E6D5'>";
                           print "<br><br><center><span class='style27'>Informações gravadas com sucesso! </span><br><br><a href='javascript:fechar_janela(window.close())'><img src='imagens/voltar.gif' border=0></a><center>";
                       }
                       break;
                   case 21:  //CADASTRANDO SAIDAS
                   $id_user = $_COOKIE['logado'];
                   $regiao = $_REQUEST['regiao'];
                   $projeto = $_REQUEST['projeto'];
                   $banco = $_REQUEST['banco'];
                   $nome = $_REQUEST['nome'];
                   $especifica = $_REQUEST['especifica'];
                   $tipo = $_REQUEST['tipo'];
                   $valor = str_replace(".", "", $_REQUEST['valor']);
                   $adicional = str_replace(".", "", $_REQUEST['adicional']);
                   $data_credito = $_REQUEST['data_credito'];
                   $comprovante = $_REQUEST['comprovante'];
                   $data_proc = date('Y-m-d H:i:s');
                   $data_proc2 = date('Y-m-d');
                   $arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;
                   $data_credito2 = ConverteData($data_credito);
                   /*VERIFICA O BANCO E A REGIÃO*/
                   $result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$banco'");
                   $row_banco = mysql_fetch_array($result_banco);
                   if($row_banco['id_regiao'] != $regiao){
                       $regiao = $row_banco['id_regiao'];
                   }
                       if ($tipo == "19") {   //VERIFICA SE É IGUAL A SAÍDA DE CAIXA
                           $saldo_atual = $row_banco['saldo'];
                           $adicional = str_replace(",", ".", $adicional);
                           $valor = str_replace(",", ".", $valor);
                           $saldo_atual = str_replace(",", ".", $saldo_atual);
                           $valor_adicional = $adicional + $valor;
                           $sobra = $saldo_atual - $valor_adicional;
                           $adicional = number_format($adicional, 2, ",", "");
                           $valor = number_format($valor, 2, ",", "");
                           $valor_adicional = number_format($valor_adicional, 2, ",", "");
                           $sobra = number_format($sobra, 2, ",", "");
                           $verifica_caixinha = mysql_query("SELECT * FROM caixinha where id_regiao = '$regiao'");
                           $row_verifica = mysql_num_rows($verifica_caixinha);
                           if ($row_verifica >= "1") {  //VERIFICA SE JA HOUVE SAÍDA DE CAIXA PARA REGIÃO SELECIONADA
                               $row_saldo_verifica = mysql_fetch_array($verifica_caixinha);
                               $saldo_atual_caixinha = str_replace(",", ".", $row_saldo_verifica['saldo']);
                               $valor_adicional_ff = str_replace(",", ".", $valor_adicional);
                               $soma_do_caixinha = $saldo_atual_caixinha + $valor_adicional_ff;
                               $saldo_somado_caixinha = number_format($soma_do_caixinha, 2, ",", "");
                               mysql_query("UPDATE caixinha SET saldo = '$saldo_somado_caixinha' where id_caixinha = '$row_saldo_verifica[0]'");
                           } else {  // SE NÃO HOUVE SAÍDA DE CAIXA, ELE INSERE A 1ª SAÍDA DE CAIXA DESSA REGIÃO
                               mysql_query("INSERT INTO caixinha(id_projeto,id_regiao,saldo,id_banco) values  ('$projeto','$regiao','$valor_adicional','$banco')") or die("$mensagem_erro<br><br>" . mysql_error());
                           }  //AQUI TERMINA SE JA HOUVE OU NÃO SAÍDA DE CAIXA
                           mysql_query("INSERT INTO saida
                               (id_regiao,id_projeto,id_banco,id_user,nome,especifica,tipo,adicional,valor,data_proc,data_vencimento,comprovante,status)
                               values
                               ('$regiao','$projeto','$banco','$id_user','$nome','$especifica','$tipo','$adicional','$valor','$data_proc','$data_proc2','$comprovante','2')") or die("$mensagem_erro<br><br>" . mysql_error());
                           mysql_query("UPDATE bancos SET saldo = '$sobra' where id_banco = '$banco'");
                       }
                       ////AQUI TERMINA CAIXINHA (SAÍDA DE CAIXA)
                       //VERIFICANDO SE ESSA SAÍDA JA FOI CADASTRADA POR OUTRO USUÁRIO
                       $result_verifica = mysql_query("SELECT * FROM saida WHERE valor='$valor' and data_vencimento='$data_credito2'");
                       $row_num_verifica = mysql_num_rows($result_verifica);
                       if ($row_num_verifica > '0') {
                           print "
                           <script>
                           alert (\"Atenção!\\n\\n Existem $row_num_verifica contas com o mesmo valor e data de vencimento!\\n\\n Atenção na hora de pagar!\");
                           </script>";
                       } else {
                       }
                       //  CASO NÃO HAJA NENHUMA CONTA CADASTRADA COM O VALOR E A DATA DE VENCIMENTO IGUAIS ELE CONTINUA
                       //AQUI TERA QUE VERIVICAR SE ESTÁ ENVIANDO O ARQUIVO
                       if (empty($_REQUEST['comprovante'])) {    //AQUI ESTÁ SEM O ARQUIVO
                           $comprovante = "0";
                           $tipo_arquivo = "0";
                           mysql_query("INSERT INTO saida(id_regiao,id_projeto,id_banco,id_user,nome,especifica,tipo,adicional,valor,data_proc,data_vencimento,comprovante,tipo_arquivo) values
                               ('$regiao','$projeto','$banco','$id_user','$nome','$especifica','$tipo','$adicional','$valor','$data_proc','$data_credito2','$comprovante','$tipo_arquivo')") or die("$mensagem_erro<br><br>" . mysql_error());
                       } else { // AQUI TEM ARQUIVO
                           if ($arquivo[type] != "image/x-png" && $arquivo[type] != "image/jpeg" && $arquivo[type] != "image/gif" && $arquivo[type] != "image/jpe") {
                               print "<center>
                               <hr><font size=3 color=#FFFFFF><b>
                               Tipo de arquivo não permitido, os únicos padrões permitidos são .gif, .jpg , .jpeg ou .png<br>
                               <br>
                               <a href='saidas.php?regiao=$regiao'>Voltar</a>
                               </b></font>
                               ";
                               exit;
                           } else { //AQUI É UM DOS ARQUIVOS MENCIONADOS ACIMA
                               $comprovante = "1";
                               $arr_basename = explode(".", $arquivo['name']);
                               $file_type = $arr_basename[1];
                               if ($file_type == "gif") {
                                   $tipo_name = ".gif";
                               } if ($file_type == "jpg" or $arquivo[type] == "jpeg") {
                                   $tipo_name = ".jpg";
                               } if ($file_type == "png") {
                                   $tipo_name = ".png";
                               }
                               mysql_query("INSERT INTO saida(id_regiao,id_projeto,id_banco,id_user,nome,especifica,tipo,adicional,valor,data_proc,data_vencimento,comprovante,tipo_arquivo) values
                                   ('$regiao','$projeto','$banco','$id_user','$nome','$especifica','$tipo','$adicional','$valor','$data_proc','$data_credito2','$comprovante','$tipo_name')") or die("$mensagem_erro<br><br>" . mysql_error());
                               $result_insert = mysql_query("SELECT * FROM saida WHERE id_regiao = '$regiao' and id_projeto = '$projeto' and id_banco = $banco and nome = '$nome' and especifica = '$especifica' and valor = '$valor'");
                               $row_select = mysql_fetch_array($result_insert);
                               $tipo_arquivo = "$tipo_name";
                               // Resolvendo o nome e para onde o arquivo será movido
                               $diretorio = "comprovantes/";
                               $nome_tmp = "$row_select[0]" . $tipo_arquivo;
                               $nome_arquivo = "$diretorio$nome_tmp";
                               move_uploaded_file($arquivo['tmp_name'], $nome_arquivo) or die("Erro ao enviar o Arquivo: $nome_arquivo");
                           }
                       }
                       if (!empty($_REQUEST['reembolso'])) {
                           $ree = $_REQUEST['reembolso'];
                           mysql_query("UPDATE fr_reembolso SET data_apro='$data_proc2', user_apro='$id_user', status='2' WHERE id_reembolso = '$ree'") or die(mysql_error());
                           print "
                           <script>
                           alert(\"Informações cadastradas com sucessooooo!\");
                           location.href=\"frota/ver_reembolso.php?reembolso=$ree&id=1\"
                           parent.window.location.reload();
                           if (parent.window.hs) {
                               var exp = parent.window.hs.getExpander();
                               if (exp) { exp.close(); }
                           }
                           </script>";
                           exit;
                       }
                       print "
                       <br><br><center><span class='style27'><br>
                       VALOR: $valor
                       <br><br>
                       $nome_arquivo
                       <br><br>
                       Informações gravadas com sucesso! </span><br><br><a href='../financeiro/novofinanceiro.php?regiao=$regiao'><img src='imagens/voltar.gif' border=0></a><center>
                       <script>
                       alert(\"Informações cadastradas com sucesso!\");
                       location.href=\"../financeiro/novofinanceiro.php?regiao=$regiao\";
                       parent.window.location.reload();
                       if (parent.window.hs) {
                           var exp = parent.window.hs.getExpander();
                           if (exp) { exp.close(); }
                       }
                       </script>";
                       break;
                   case 22:                         //CADASTRANDO ENTRADAS
                   $id_user = $_COOKIE['logado'];
                   $regiao = $_REQUEST['regiao'];
                   $projeto = $_REQUEST['projeto'];
                   $banco = $_REQUEST['banco'];
                   $nome = $_REQUEST['nome'];
                   $especifica = $_REQUEST['especifica'];
                   $tipo = $_REQUEST['tipo'];
                   $adicional = $_REQUEST['adicional'];
                   $valor = $_REQUEST['valor'];
                   $data_credito = $_REQUEST['data_credito'];
                   $data_proc = date('Y-m-d H:i:s');
                   $valor = str_replace(".", "", $valor);
                   $adicional = str_replace(".", "", $adicional);
                       /*
                         function ConverteData($Data){
                         if (strstr($Data, "/"))//verifica se tem a barra /
                         {
                         $d = explode ("/", $Data);//tira a barra
                         $rstData = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mes etc...
                         return $rstData;
                         } elseif(strstr($Data, "-")){
                         $d = explode ("-", $Data);
                         $rstData = "$d[2]/$d[1]/$d[0]";
                         return $rstData;
                         }else{
                         return "";
                         }
                     } */
                     $data_credito2 = ConverteData($data_credito);
                     mysql_query("INSERT INTO entrada(id_regiao,id_projeto,id_banco,id_user,nome,especifica,tipo,adicional,valor,data_proc,data_vencimento) values
                       ('$regiao','$projeto','$banco','$id_user','$nome','$especifica','$tipo','$adicional','$valor','$data_proc','$data_credito2')") or die("$mensagem_erro<br><br>" . mysql_error());
                     $result_banco = mysql_query("SELECT saldo FROM bancos where id_banco = '$banco'");
                     $row_banco = mysql_fetch_array($result_banco);
                     $valor_antigo = str_replace(",", ".", $row_banco['saldo']);
                     $valor_novo = str_replace(",", ".", $valor);
                     $adicional_novo = str_replace(",", ".", $adicional);
                     $valor_agora = $adicional_novo + $valor_novo;
                     $valor_update = $valor_antigo + $valor_agora;
                     $valor_update_f = number_format($valor_update, 2, ",", "");
                       //mysql_query("UPDATE bancos set saldo = '$valor_update_f' where id_banco = '$banco'")  or die ("O servidor não respondeu conforme deveria, tente novamente mais tarde, Obrigado!<br><br>".mysql_error());
                     print "
                     <html><head><title>:: Intranet ::</title>
                     <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
                     <link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
                     </head><body bgcolor='#D7E6D5'>";
                     print "<br><br><center><span class='style27'><br>Informações gravadas com sucesso! </span><br><br><a href='../financeiro/novofinanceiro.php?regiao=$regiao'><img src='imagens/voltar.gif' border=0></a><center>";
                     break;
                     case 23:
                       //CADASTRANDO TIPOS DE ENTRADAS E SAIDAS
                       //QUANDO O TIPO FOR 0 (ZERO) SERÁ SAÍDA... SE FOR 1 SERÁ ENTRADA
                     $tipo = $_REQUEST['tipo'];
                     $regiao = $_REQUEST['regiao'];
                     $nome = $_REQUEST['nome'];
                     $descricao = $_REQUEST['descricao'];
                     mysql_query("INSERT INTO entradaesaida(nome,descricao,tipo) values
                       ('$nome','$descricao','$tipo')") or die("$mensagem_erro<br><br>" . mysql_error());
                     print "
                     <html><head><title>:: Intranet ::</title>
                     <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
                     <link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
                     </head><body bgcolor='#D7E6D5'>";
                     print "<br><br><center><span class='style27'><br>Informações gravadas com sucesso! </span><br><br><a href='../financeiro/novofinanceiro.php?regiao=$regiao'><img src='imagens/voltar.gif' border=0></a><center>";
                     break;
                     case 24:
                       //CADASTRANDO SAIDA DE CAIXINHA
                     $regiao = $_REQUEST['regiao'];
                     $projeto = $_REQUEST['projeto'];
                     $nome = $_REQUEST['nome'];
                     $descricao = $_REQUEST['descricao'];
                     $adicional = $_REQUEST['adicional'];
                     $valor = $_REQUEST['valor'];
                     $id_user = $_COOKIE['logado2'];
                     $data_proc = date('Y-m-d H:i:s');
                     mysql_query("INSERT INTO caixa(id_projeto,id_regiao,id_user,nome,descricao,valor,data_proc,adicional) values
                       ('$projeto','$regiao','$id_user','$nome','$descricao','$valor','$data_proc','$adicional')") or die("$mensagem_erro<br><br>" . mysql_error());
                     $result1 = mysql_query("SELECT id_caixinha,saldo FROM caixinha where id_regiao = '$regiao'");
                     $row1 = mysql_fetch_array($result1);
                     $valor_atual_banco = str_replace(".", "", $row1['saldo']);
                     $valor_atual_banco = str_replace(",", ".", $valor_atual_banco);
                     $adicional = str_replace(".", "", $adicional);
                     $adicional = str_replace(",", ".", $adicional);
                     $valor = str_replace(".", "", $valor);
                     $valor = str_replace(",", ".", $valor);
                     $valor_total = $adicional + $valor;
                     $valor_banco_novo = $valor_atual_banco - $valor_total;
                     $valor_banco_novo = number_format($valor_banco_novo, 2, ",", "");
                     mysql_query("UPDATE caixinha SET saldo = '$valor_banco_novo' where id_regiao = '$regiao'");
                     print "
                     <html><head><title>:: Intranet ::</title>
                     <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
                     <link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
                     <script>
                     opener.location.reload();
                     </script>
                     </head><body bgcolor='#D7E6D5'>";
                     print "<br><br><center><span class='style27'>$valor_banco_novo<br>Informações gravadas com sucesso! </span><br><br>
                     <a href='javascript:window.close()'><img src='imagens/voltar.gif' border=0></a><center>";
                     break;
                   case 25:                         //CADASTRANDO SAIDAS
                   $id_user = $_COOKIE['logado'];
                   $funcionario = $_REQUEST['funcionario'];
                       // GERANDO A SENHA ALEATÓRIA
                   $target = "%%%%";
                   $senha = "";
                   $dig = "";
                   $consoantes = "bcdfghjkmnpqrstvwxyzbcdfghjkmnpqrstvwxyz123456789";
                   $vogais = "aeiu";
                   $numeros = "123456789";
                   $a = strlen($consoantes) - 1;
                   $b = strlen($vogais) - 1;
                   $c = strlen($numeros) - 1;
                   for ($x = 0; $x <= strlen($target) - 1; $x++) {
                       if (substr($target, $x, 1) == "@") {
                           $rand = mt_rand(0, $c);
                           $senha .= substr($numeros, $rand, 1);
                       } elseif (substr($target, $x, 1) == "%") {
                           $rand = mt_rand(0, $a);
                           $senha .= substr($consoantes, $rand, 1);
                       } elseif (substr($target, $x, 1) == "&") {
                           $rand = mt_rand(0, $b);
                           $senha .= substr($vogais, $rand, 1);
                       } else {
                           die("<b>Erro!</b><br><i>$target</i> é uma expressão inválida!<br><i>" . substr($target, $x, 1) . "</i> é um caractér inválido.<br>");
                       }
                   }
                   $senha = "net" . $senha;
                   if (empty($_REQUEST['excluir'])) {
                       mysql_query("UPDATE funcionario SET senha = '$senha', alt_senha = '1' WHERE id_funcionario =
                           '$funcionario'") or die("Tela 25 <br> $mesnagem_erro<br><br>" . mysql_error());
                       $mensagem_1 = "SENHA ALTERADA COM SUCESSO, NÃO ESQUEÇA DE ANOTAR E ENVIAR PARA O USUÁRIO";
                       $mensagem_2 = "SENHA ALTERADA COM SUCESSO!<BR><BR>NOVA SENHA: $senha";
                   } else {
                       mysql_query("UPDATE funcionario SET status_reg = '0' WHERE id_funcionario = '$funcionario'") or die
                       ("Tela 25 <br> $mesnagem_erro<br><br>" . mysql_error());
                       $mensagem_1 = "FUNCIONARIO DELETADO";
                       $mensagem_2 = "FUNCIONARIO DELETADO COM SUCESSO";
                   }
                   print "
                   <script>
                   alert (\"$mensagem_1\");
                   </script>
                   <center>
                   <font color='#FFFFFF'>
                   <br><b>
                   $mensagem_2
                   </b><br><br>
                   </font><br><br>
                   <a href='ver_tudo.php?id=19'><img src='imagens/voltar.gif' border=0></a></center>";
                   break;
                   case 26:
                       // ----------- TORCA DE MASTER -------------------------
                   $user = $_REQUEST['user'];
                   $master = $_REQUEST['master'];
                   $master_de = $_REQUEST['master_de'];
                   $result_master_1 = mysql_query("SELECT * FROM master where id_master = '$master_de'");
                   $row_master_1 = mysql_fetch_array($result_master_1);
                   $result_master_2 = mysql_query("SELECT * FROM master where id_master = '$master'");
                   $row_master_2 = mysql_fetch_array($result_master_2);
                   $result_regiao_master = mysql_query("SELECT regioes.id_regiao  FROM regioes
                      INNER JOIN  funcionario_regiao_assoc
                      ON funcionario_regiao_assoc.id_regiao = regioes.id_regiao
                      WHERE regioes.id_master = '$master'
                      AND funcionario_regiao_assoc.id_funcionario = '$user'
                      ORDER BY regioes.regiao LIMIT 1 ");
                   $row_regiao_master = mysql_fetch_array($result_regiao_master);
                   mysql_query("UPDATE funcionario set id_master = '$master' , id_regiao = '$row_regiao_master[id_regiao]' where id_funcionario = '$user'");
                   $_SESSION['id_regiao'] = $row_regiao_master[id_regiao];
                       //----- INI -- GRAVANDO AS INFORMAÇÕES DO LOGIN NA TABELA LOG
                   $result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$user'");
                   $row_user = mysql_fetch_array($result_user);
                       $ip = $_SERVER['REMOTE_ADDR'];  //PEGANDO O IP
                       $local = "TROCA DE MASTER";
                       $horario = date('Y-m-d H:i:s');
                       $acao = "SAIU DE: $row_master_1[nome] PARA: $row_master_2[nome]";
                       mysql_query("INSERT INTO log (id_user,id_regiao,tipo_user,grupo_user,local,horario,ip,acao)
                           VALUES ('$user','$master_de','$row_user[tipo_usuario]',
                               '$row_user[grupo_usuario]','$local','$horario','$ip','$acao')") or die("Erro Inesperado<br><br>" . mysql_error());
                       //----- FIM -- GRAVANDO AS INFORMAÇÕES DO LOGIN NA TABELA LOG
                       print "
                       <script>
                       location.href = 'index.php';
                       </script>
                       ";
                       break;
                   }
           }   // FECHANDO O IF QUE VERIFICA O LOGIN
           ?>