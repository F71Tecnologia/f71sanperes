<?php
include_once("../wfunction.php");
class FuncoesClass {
    
    public static function listaFuncoesClt($projetoR) {
        /*$qr = "select curso.id_curso, curso.nome, rh_cbo.cod, curso.salario, curso.qnt_maxima, projeto.tipo_contratacao
                    from curso, projeto, rh_cbo
                    where projeto.id_regiao=45
                    and curso.cbo_codigo=rh_cbo.id_cbo
                    and projeto.id_projeto={$projetoR} and projeto.tipo_contratacao='clt' order by curso.nome";*/
                    
          $qr="select curso.id_curso, curso.nome , rh_cbo.cod, curso.salario, curso.qnt_maxima from curso, projeto where curso.id_regiao=projeto.id_regiao and projeto.id_regiao=45 and projeto.id_projeto={$projetoR} and projeto.tipo_contratacao='clt' group by curso.nome order by curso.nome;";
          echo $qr;
          $rs = mysql_query($qr) or die(mysql_error());
        while ($row = mysql_fetch_assoc($rs)) {
            //$funcoes = array("id_curso" => $row['id_curso'], "nome" => $row['nome'], "codCbo" => $row['cod'], "salario" => $row['salario'], "qnt_maxima" => $row['qnt_maxima'], "tipo_cont"=>$row['tipo_contratacao']);
            $funcoes[] = $row;
            
        }
    
        return $funcoes;
    }
    
    public static function listaFuncoesAutonomos($projetoR) {
        $qr = "";
                    #echo $qr;
        $rs = mysql_query($qr) or die(mysql_error());
        while ($row = mysql_fetch_assoc($rs)) {
            //$funcoes = array("id_curso" => $row['id_curso'], "nome" => $row['nome'], "codCbo" => $row['cod'], "salario" => $row['salario'], "qnt_maxima" => $row['qnt_maxima'], "tipo_cont"=>$row['tipo_contratacao']);
            $funcoes[] = $row;
            
        }
       
        return $funcoes;
    }
    
    public static function getFuncao($id_funcao){
        $qr = montaQueryFirst("curso", "*", "id_curso = {$id_curso}");
        return $qr;
    }

    public static function getCursos($id_regiao, $id_projeto) {
        $query = "SELECT A.id_curso, A.nome, B.cod, A.salario, A.qnt_maxima, A.tipo, C.tipo_contratacao_nome
                FROM curso AS A
                LEFT JOIN rh_cbo AS B ON (A.cbo_codigo = B.id_cbo)
                LEFT JOIN tipo_contratacao AS C ON (A.tipo = C.tipo_contratacao_id)
                WHERE A.status = 1 AND A.id_regiao = '{$id_regiao}' AND A.campo3 = '{$id_projeto}' ORDER BY C.tipo_contratacao_nome";

        if($id_regiao == false and $id_projeto == false){
            $query = "SELECT A.id_curso, A.nome, B.cod, A.salario, A.qnt_maxima, A.tipo, C.tipo_contratacao_nome
                FROM curso AS A
                LEFT JOIN rh_cbo AS B ON (A.cbo_codigo = B.id_cbo)
                LEFT JOIN tipo_contratacao AS C ON (A.tipo = C.tipo_contratacao_id)
                WHERE A.status = 1 ORDER BY C.tipo_contratacao_nome ASC, A.nome ASC";
        }

        $curso = mysql_query($query) or die(mysql_error());
        return $curso;
    }
    
    public static function getSindicato($id_regiao, $id_sindicato) {
        $query = "SELECT *
                FROM rhsindicato
                WHERE id_sindicato = '{$id_sindicato}' AND id_regiao = '{$id_regiao}'";
        $sindicato_query = mysql_query($query) or die(mysql_error());
        $sindicato_row = mysql_fetch_assoc($sindicato_query);
        return $sindicato_row;
    }
    
    public static function getDepartamento($id_departamento) {
        $query = "SELECT *
                FROM setor
                WHERE id_setor = '{$id_departamento}'";
        $departamento_query = mysql_query($query) or die(mysql_error());
        $departamento_row = mysql_fetch_assoc($departamento_query);
        return $departamento_row;
    }


    public static function getCursosID($id_curso) {
        $query = "SELECT A.*, A.nome AS nome_funcao, DATE_FORMAT(A.inicio,'%d/%m/%Y') AS data_ini, DATE_FORMAT(A.termino,'%d/%m/%Y') AS data_fim, A.id_departamento AS departamento, B.cod, B.nome AS nome_cbo, C.tipo_contratacao_nome, D.regiao, E.nome AS nome_projeto, 
                F.nome AS nome_horario, F.obs, F.entrada_1, F.saida_1, F.entrada_2, F.saida_2, F.horas_mes, F.dias_mes, F.dias_semana, F.folga
                FROM curso AS A
                LEFT JOIN rh_cbo AS B ON (A.cbo_codigo = B.id_cbo)
                LEFT JOIN tipo_contratacao AS C ON (A.tipo = C.tipo_contratacao_id)
                LEFT JOIN regioes AS D ON (A.id_regiao = D.id_regiao)
                LEFT JOIN projeto AS E ON (A.campo3 = E.id_projeto)
                LEFT JOIN rh_horarios AS F ON (A.id_horario = F.id_horario)
                WHERE A.status = 1 AND A.id_curso = '{$id_curso}'";

        $curso = mysql_query($query) or die(mysql_error());
        $row = mysql_fetch_assoc($curso);
        return $row;
    }

    public static function getRhHorario($id_curso){
        $sql = "SELECT * FROM rh_horarios WHERE funcao = '{$id_curso}' ORDER BY nome";
        $horario = mysql_query($sql) or die(mysql_error());
        return $horario;
    }
    
    public function getRhClt($id_curso) {
        $sql = "SELECT * FROM rh_clt WHERE id_curso = '{$id_curso}' AND (status < 60 OR status = 200)";
        $clt = mysql_query($sql);
        $tot = mysql_num_rows($clt);
        return $tot;
    }
    
    public function getAutonomo($id_curso) {
        $sql = "SELECT * FROM autonomo WHERE id_curso = {$id_curso} AND status = 1 AND status_reg = 1";
        $clt = mysql_query($sql);
        $tot = mysql_num_rows($clt);
        return $tot;
    }
    
    public function alteraStatusCurso($id_curso, $usuario) {
        $sql = "UPDATE curso SET status_reg = 0, status = 0 WHERE id_curso = '{$id_curso}'";
        $qry = mysql_query($sql);
        $res = mysql_fetch_assoc($qry);

        //dados usuario para cadastro de log
        $local = "Exclusão de Curso";
        $ip = $_SERVER['REMOTE_ADDR'];
        $acao = "{$usuario['nome']} desativou o curso " . $id_curso;
        $id_usuario = $usuario['id_funcionario'];
        $tipo_usuario = $usuario['tipo_usuario'];
        $grupo_usuario = $usuario['grupo_usuario'];
        $regiao = $usuario['id_regiao'];
        
        $insere_log = mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) VALUES
                                            ('{$id_usuario}', '{$regiao}', '{$tipo_usuario}', '{$grupo_usuario}', '{$local}', NOW(), '{$ip}', '{$acao}')") or die(mysql_error());
        return $res;
        
    }
    
    public function alteraStatusHorario($id_horario) {
        $sql = "UPDATE rh_horarios SET status_reg = 0 WHERE id_horario = '{$id_horario}' LIMIT 1";
        $qry = mysql_query($sql);
    }
    
    public static function getCBO($nome_cbo){
        $sql_cbo = "SELECT * FROM rh_cbo WHERE nome = '{$nome_cbo}'";
        $cbo_qry = mysql_query($sql_cbo);
        return $cbo_qry;
    }        
    
    //cadastro de funcoes
    public static function cadastraFuncao($usuario, $id_regiao, $id_usuario) {
        
        if (isset($_REQUEST['cadastrar'])) {
            
            $log = new Log();
            
            //variaveis recuperadas
            $departamento = $_REQUEST['departamento'];
            $projeto                = $_REQUEST['projeto'];
            $nome_funcao            = acentoMaiusculo($_REQUEST['nome_funcao']);
            $area                   = acentoMaiusculo($_REQUEST['area']);
            $local_fun              = acentoMaiusculo($_REQUEST['local']);            
            $data_ini               = date('Y/m/d', strtotime($_REQUEST['data_ini']));
            $data_fim               = date('Y/m/d', strtotime($_REQUEST['data_fim']));
            $salario                = ($_REQUEST['salario'] == "" OR $_REQUEST['salario'] <= 0) ? 0.00 : str_replace(',', '.', str_replace(".", "", $_REQUEST['salario']));
            $qtd_salarios           = $_REQUEST['qtd_salarios'];		               
            $qtd_contratacao        = $_REQUEST['qtd_contratacao'];
            $descricao              = acentoMaiusculo($_REQUEST['descricao']);
            $data_cad               = date('Y-m-d');
            $contratacao            = $_REQUEST['contratacao'];
            $cbo                    = explode("*", $_REQUEST['cbo']);
            $horas                  = $_REQUEST['horas'];
            $valor_hora_cooperado   = str_replace('R$ ', "", str_replace(',', ".",  str_replace('.', "", $_REQUEST['valor_hora_cooperado'])));
            $insalubridade          = $_REQUEST['insalubridade'];
            $periculosidade         = $_REQUEST['periculosidade'];
            $departamento           = $_REQUEST['departamento'];
            $sindicato              = $_REQUEST['sindicato'];
            $gratificacao_funcao    = $_REQUEST['gratificacao_funcao'];
            
            $retem_fgts             = $_REQUEST['retem_fgts'];
            $diretor_empregado      = $_REQUEST['diretor_empregado'];
            $diretor_nao_empregado  = $_REQUEST['diretor_nao_empregado'];
            $proprietario_ou_socio  = $_REQUEST['proprietario_ou_socio'];
            $socio_cotista          = $_REQUEST['socio_cotista'];
            $tipo_diretor           = 0;
            
            if ($diretor_empregado) {
                $tipo_diretor       = 1;
            } else if ($diretor_nao_empregado) {
                $tipo_diretor       = 2;
            } else if ($proprietario_ou_socio) {
                $tipo_diretor       = 3;
            } else if ($socio_cotista) {
                $tipo_diretor       = 4;
            }
            
            //trata insalubridade/periculosidade
            if($insalubridade == '-1'){
                $insalubridade      = '0';
            }elseif($periculosidade == '1'){
                $insalubridade      = '0';
            }
            
            if($periculosidade == ''){
                $periculosidade     = '0';
            }
            
            $regiao_selecionada = $_REQUEST['regiao_selecionada'];
            $regiao_logado          = $_REQUEST['regiao_logado'];
            
            //horarios
            $nome_horario = $_REQUEST['nome_horario'];
            $obs = $_REQUEST['obs'];
            $entrada = $_REQUEST['entrada'];
            $ida_almoco = $_REQUEST['ida_almoco'];
            $volta_almoco = $_REQUEST['volta_almoco'];
            $saida = $_REQUEST['saida'];
            $horas_mes = $_REQUEST['horas_mes'];
            $dias_mes = $_REQUEST['dias_mes'];
            $horas_semana = $_REQUEST['hora_semana'];
            $dias_semana = $_REQUEST['dias_semana'];
            $folgaDef = $_REQUEST['folga'];       
            $noturno = $_REQUEST['noturno'];
            $horas_noturno = $_REQUEST['horas_noturno'];
            $porcentagem_adicional = $_REQUEST['porcentagemAd'];
            
            $mes_abono = $_REQUEST['mes_abono'];
            $horista_plantonista = $_REQUEST['horista_plantonista'];
            
            //trata dados de cooperado
            if($contratacao == 3){
                $parcelas = $_REQUEST['parcelas'];
                $quota = str_replace(',', '.', str_replace(".", "", $_REQUEST['quota']));
                $parcela_quotas = $_REQUEST['parcela_quotas'];
            }else{
                $parcelas = '';
                $quota = '';
                $parcela_quotas = '';
            }
            
            //provisorio, pegar valor pelo lista_cbo.php (autocomplete)
            //$cbo = explode('*',$cbo[0]);
            $cbo = trim($cbo[0]);
            $sql_cbo = FuncoesClass::getCBO($cbo);
            $total_cbo = mysql_num_rows($sql_cbo);                        
            
            $rst_idCBO = mysql_fetch_assoc($sql_cbo); 
            $id_cbo = $rst_idCBO['id_cbo'];
            $cbo_nome = $rst_idCBO['nome'];  
            
            $valor_ad_cargo_confianca = $_REQUEST['valor_ad_cargo_confianca'];
            $percentual_ad_cargo_confianca = $_REQUEST['percentual_ad_cargo_confianca'] / 100;
            $quebra_caixa    = $_REQUEST['quebra_caixa'];
            $risco_vida = $_REQUEST['risco_vida'];
            
            //trata mes abono
            if ($mes_abono == -1) {
                $mes_abono = 0;
            } else {
                $mes_abono = $mes_abono;
            }
            
            //pesquisa informacoes, para nao permitir dados duplicados
            $sql_checa_curso = "SELECT * FROM curso
                                    WHERE nome = '$nome_funcao' AND campo3 = '$projeto' AND tipo = '$contratacao' AND status=1";            
            $result_cont = mysql_query($sql_checa_curso);
            $total_cursos = mysql_num_rows($result_cont);
            
            if ($total_cursos != 0) {
                $_SESSION['MESSAGE'] = 'Já Existe uma Atividade cadastrada com este nome, nesse Projeto!';
                $_SESSION['MESSAGE_COLOR'] = 'message-red';
                $_SESSION['regiao'] = $regiao_selecionada;                

            } elseif($total_cbo != 1){
                $_SESSION['MESSAGE'] = "CBO '{$_REQUEST['cbo']}' não permitido, pois não existe em nosso Banco de Dados";
                $_SESSION['MESSAGE_COLOR'] = 'message-red';   
                $_SESSION['regiao'] = $regiao_selecionada;                

            } elseif(($_REQUEST['cbo'] == '') || ($salario == 0) || ($salario < 0) || ($nome_funcao == '')){
                $_SESSION['MESSAGE'] = "Verifique os campos CBO, Salário e Nome da função, eles não podem ser vazios, nulos, ou negativos";
                $_SESSION['MESSAGE_COLOR'] = 'message-red';
                $_SESSION['regiao'] = $regiao_selecionada;                

            }  else {                                                                               
                
                $sql_curso = "INSERT INTO curso
                                        (risco_vida,quebra_caixa,percentual_ad_cargo_confianca,valor_ad_cargo_confianca,gratificacao_funcao, id_sindicato, nome, area, id_regiao, local, inicio, termino, descricao, valor, parcelas, campo2, campo3, cbo_nome, cbo_codigo, salario,
                                        mes_abono, id_user, data_cad, tipo, hora_semana, hora_mes, qnt_maxima, tipo_insalubridade, periculosidade_30, qnt_salminimo_insalu, quota, num_quota, valor_hora, id_departamento, horista_plantonista, tipo_diretor, retem_fgts) VALUES
                                        ('$risco_vida','$quebra_caixa','$percentual_ad_cargo_confianca','$valor_ad_cargo_confianca','$gratificacao_funcao','{$sindicato}','{$nome_funcao}', '{$area}', '{$regiao_selecionada}', '{$local_fun}', '{$data_ini}', '{$data_fim}', '{$descricao}', '{$salario}', '{$parcelas}',
                                        '{$nome_funcao}', '{$projeto}', '{$cbo_nome}', '{$id_cbo}', '{$salario}', '{$mes_abono}', '{$id_usuario}',
                                        '{$data_cad}', '{$contratacao}', '{$horas_semana}', '{$horas}', '{$qtd_contratacao}', '{$insalubridade}', '{$periculosidade}', '{$qtd_salarios}', '{$quota}', '{$parcela_quotas}', '{$valor_hora_cooperado}', '{$departamento}', '{$horista_plantonista}', '{$tipo_diretor}', '{$retem_fgts}')";

                                        
                $insere_curso = mysql_query($sql_curso) or die(mysql_error());
                                        $id_curso = mysql_insert_id();
                
                $log->log(2,"Curso ID $id_curso alterado.",'curso');
                
                //calculo do salario para retirar o valor diario e o valor hora
//                $diaria = $salario / 30;
//                $hora = $diaria / 8;
//                $diaria = str_replace(",", ".", $diaria);
//                $diaria_f = number_format($diaria, 2, ",", ".");
//                $hora = str_replace(",", ".", $hora);
//                $hora_f = number_format($hora, 2, ",", ".");
                
                //somente clt
//                if($contratacao == 2){
//                
//                    foreach($nome_horario as $k => $valor) {
//
//                        //trata folga
//                        if($folgaDef[$k][0] == "1" && $folgaDef[$k][1] == "2"){//SEGUNDA A SEXTA
//                            $folga = "3";
//                        }elseif($folgaDef[$k][0] == "1"){//FOLGA NO SABADO
//                            $folga = "1";
//                        }elseif($folgaDef[$k][1] == "2"){//FOLGA NO DOMINGO
//                            $folga = "2";
//                        }elseif($folgaDef[$k][2] == "5"){//PLANTONISTA
//                            $folga = "5";
//                        }else{
//                            $folga = "0";//SEM FOLGAS( SEGUNDA À SEGUNDA )
//                        }
//                        
//                        if($folgaDef[$k][2] == "5"){
//                            $folga = "5";
//                        }
//                        
//                        $insere_horario = mysql_query("INSERT INTO rh_horarios (id_regiao,nome,obs,entrada_1,saida_1,entrada_2,saida_2,dias_semana,horas_mes,horas_semanais,salario,funcao,valor_dia,valor_hora,folga,dias_mes,adicional_noturno,horas_noturnas,porcentagem_adicional) VALUES
//                          ('{$regiao_selecionada}', '".acentoMaiusculo($nome_horario[$k])."', '".acentoMaiusculo($obs[$k])."', '{$entrada[$k]}', '{$ida_almoco[$k]}', '{$volta_almoco[$k]}', '{$saida[$k]}', '{$dias_semana[$k]}',
//                          '{$horas_mes[$k]}', '{$horas_semana[$k]}' ,'{$salario}', '{$id_curso}', '{$diaria_f}','{$hora_f}','{$folga}', '{$dias_mes[$k]}', '{$noturno[$k]}', '{$horas_noturno[$k]}', '{$porcentagem_adicional[$k]}')") or die(mysql_error());
//                    }
//                
//                }
                
                if($insere_curso){
                  $_SESSION['MESSAGE'] = 'Informações gravadas com sucesso!';
                  $_SESSION['MESSAGE_COLOR'] = 'message-blue';
                  $_SESSION['regiao'] = $regiao_selecionada;
                  $_SESSION['projeto'] = $projeto;
                  header('Location: index.php');
                }else{
                  $_SESSION['MESSAGE'] = 'Erro ao cadastrar a função';
                  $_SESSION['MESSAGE_COLOR'] = 'message-red';
                  $_SESSION['regiao'] = $regiao_selecionada;
                  $_SESSION['projeto'] = $projeto;
                  header('Location: index.php');
                }
            }
        }
    }
    
    public static function alteraFuncao($usuario, $id_regiao, $id_usuario) {
        if (isset($_REQUEST['atualizar'])) {
            
            $log = new Log();
            
            //variaveis recuperadas
            $departamento = $_REQUEST['departamento'];
            $nome_funcao = acentoMaiusculo($_REQUEST['nome_funcao']);
            $area = strtoupper($_REQUEST['area']);
            /*if(strpos('-', $_REQUEST['cbo'])){
                $cbo = explode("-", $_REQUEST['cbo']);
            }else{
                $cbo[0] = $_REQUEST['cbo'];
            }*/
            $local_fun = acentoMaiusculo($_REQUEST['local']);            
            $parcelas = $_REQUEST['parcelas'];
            $qtd_contratacao = $_REQUEST['qtd_contratacao'];
            $descricao = acentoMaiusculo($_REQUEST['descricao']);
            $salario = $_REQUEST['salario'];
            $mes_abono = $_REQUEST['mes_abono'];
            $regiao = $_REQUEST['regiao'];
            $projeto = $_REQUEST['projeto'];
            $horas = $_REQUEST['horas'];
            $horas_semana = $_REQUEST['hora_semana'];
            $contratacao = $_REQUEST['contratacao_curso'];
            $valor_hora_cooperado = str_replace('R$ ', "", str_replace(',', ".",  str_replace('.', "", $_REQUEST['valor_hora_cooperado'])));
            $insalubridade = $_REQUEST['insalubridade'];
            $periculosidade = $_REQUEST['periculosidade'];
            $qtd_salarios = $_REQUEST['qtd_salarios'];
            $departamento = $_REQUEST['departamento'];
            $sindicato = $_REQUEST['sindicato'];
            $gratificacao_funcao    = $_REQUEST['gratificacao_funcao'];
            $quebra_caixa    = $_REQUEST['quebra_caixa'];
            
            $retem_fgts             = $_REQUEST['retem_fgts'];
            $diretor_empregado      = $_REQUEST['diretor_empregado'];
            $diretor_nao_empregado  = $_REQUEST['diretor_nao_empregado'];
            $proprietario_ou_socio  = $_REQUEST['proprietario_ou_socio'];
            $socio_cotista          = $_REQUEST['socio_cotista'];
            $tipo_diretor           = 0;
            
            if ($diretor_empregado) {
                $tipo_diretor       = 1;
            } else if ($diretor_nao_empregado) {
                $tipo_diretor       = 2;
            } else if ($proprietario_ou_socio) {
                $tipo_diretor       = 3;
            } else if ($socio_cotista) {
                $tipo_diretor       = 4;
            }
            
            //trata insalubridade/periculosidade
            if($insalubridade == '-1'){
                $insalubridade = '0';
            }elseif($periculosidade == '1'){
                $insalubridade = '0';
                $qtd_salarios = '0';
            }
            
            if($periculosidade == ''){
                $periculosidade = '0';
            }
            
            //informacoes
            $data_cad = date('Y-m-d');
            $id_curso = $_REQUEST['id_curso'];
            $id_horario = $_REQUEST['id_horario'];
            
            //horarios
            $nome_horario = $_REQUEST['nome_horario'];
            $obs = $_REQUEST['obs'];
            $entrada = $_REQUEST['entrada'];
            $ida_almoco = $_REQUEST['ida_almoco'];
            $volta_almoco = $_REQUEST['volta_almoco'];
            $saida = $_REQUEST['saida'];
            $horas_mes = $_REQUEST['horas_mes'];
            $dias_mes = $_REQUEST['dias_mes'];
            $dias_semana = $_REQUEST['dias_semana'];
            
            $folgaDef = $_REQUEST['folga'];       
            $noturno = $_REQUEST['noturno'];
            $horas_noturno = $_REQUEST['horas_noturno'];
            $horista_plantonista = $_REQUEST['horista_plantonista'];

            //dados usuario para cadastro de log
//            $local = "Edição de Cursos";
//            $ip = $_SERVER['REMOTE_ADDR'];
//            $acao = "{$usuario['nome']} editou o curso $id_curso";
//            $tipo_usuario = $usuario['tipo_usuario'];
//            $grupo_usuario = $usuario['grupo_usuario'];
//            $regiao_logado = $usuario['id_regiao'];        
            
            /**
             * FEITO POR SINESIO 
             * 04/04/2017
             */
//            $sobre_aviso = isset($_REQUEST['sobre_aviso'])?$_REQUEST['sobre_aviso']:0;

            //calculo do salario para retirar o valor diario e o valor hora
//            $diaria = $salario / 30;
//            $hora = $diaria / 8;
//            $diaria = str_replace(",", ".", $diaria);
//            $diaria_f = number_format($diaria, 2, ",", ".");
//            $hora = str_replace(",", ".", $hora);
//            $hora_f = number_format($hora, 2, ",", ".");    

            //provisorio, pegar valor pelo lista_cbo.php (autocomplete)
            $cbo = explode("*", $_REQUEST['cbo']);
            $cbo = trim($cbo[0]);
            $sql_cbo = FuncoesClass::getCBO($cbo);
            $total_cbo = mysql_num_rows($sql_cbo);
            $rst_idCBO = mysql_fetch_assoc($sql_cbo);
            $cbo_cod = $rst_idCBO['id_cbo'];
            $cbo_nome = $rst_idCBO['nome'];
            
            $valor_ad_cargo_confianca = $_REQUEST['valor_ad_cargo_confianca'];
            $percentual_ad_cargo_confianca = $_REQUEST['percentual_ad_cargo_confianca'] / 100;
            
            $risco_vida = $_REQUEST['risco_vida'];
            
            if(($cbo == '') || ($nome_funcao == '')){
                $_SESSION['MESSAGE'] = "Verifique os campos CBO, Salário e Nome da função, eles não podem ser vazios, nulos, ou negativos";
                $_SESSION['MESSAGE_COLOR'] = 'message-red';
                
            } elseif($total_cbo != 1){
                $_SESSION['MESSAGE'] = "CBO '{$cbo}' não permitido, pois não existe em nosso Banco de Dados";
                $_SESSION['MESSAGE_COLOR'] = 'message-red';                        
                
            } else {
                $sql_curso = "UPDATE curso SET risco_vida = '$risco_vida', quebra_caixa = '$quebra_caixa', percentual_ad_cargo_confianca = '$percentual_ad_cargo_confianca', valor_ad_cargo_confianca = '$valor_ad_cargo_confianca', gratificacao_funcao = '$gratificacao_funcao', id_sindicato = '{$sindicato}', sobre_aviso = '{$sobre_aviso}', nome = '{$nome_funcao}', campo2 = '{$nome_funcao}', cbo_nome = '{$cbo_nome}', cbo_codigo = '{$cbo_cod}', area = '{$area}', local = '{$local_fun}', parcelas = '{$parcelas}', hora_semana = '{$horas_semana}',
                                        descricao = '{$descricao}', data_alter = '{$data_cad}', hora_mes = '{$horas}', user_alter = '{$id_usuario}', qnt_maxima = '{$qtd_contratacao}', tipo_insalubridade = '{$insalubridade}',  mes_abono = '{$mes_abono}', qnt_salminimo_insalu = '{$qtd_salarios}', periculosidade_30 = '{$periculosidade}', valor_hora='{$valor_hora_cooperado}', id_departamento='{$departamento}', horista_plantonista = '{$horista_plantonista}', tipo_diretor = '{$tipo_diretor}', retem_fgts = '{$retem_fgts}' WHERE id_curso = {$id_curso} LIMIT 1";
                
//                exit($sql_curso);
                
                $antigo = $log->getLinha('curso', $id_curso);
                $altera_curso = mysql_query($sql_curso) or die(mysql_error());
                $novo = $log->getLinha('curso', $id_curso);
                
                $log->log(2,"Curso ID $id_curso alterado.",'curso',$antigo,$novo);
                
                
                //somente clt
//                if($contratacao == 2){
//                                        
//                    foreach($nome_horario as $k => $valor) {
//
//                        //trata folga
//                        if($folgaDef[$k][0] == "1" && $folgaDef[$k][1] == "2"){//SEGUNDA A SEXTA
//                            $folga = "3";
//                        }elseif($folgaDef[$k][0] == "1"){//FOLGA NO SABADO
//                            $folga = "1";
//                        }elseif($folgaDef[$k][1] == "2"){//FOLGA NO DOMINGO
//                            $folga = "2";
//                        }elseif($folgaDef[$k][2] == "5"){//PLANTONISTA
//                            $folga = "5";
//                        }else{
//                            $folga = "0";//SEM FOLGAS (SEGUNDA À SEGUNDA)
//                        }
//
//                        if($folgaDef[$k][2] == "5"){
//                            $folga = "5";
//                        }
//
//                        if ($id_horario[$k] != 0 && $id_horario[$k] != NULL && $id_horario[$k] != '') {                                        
//                            $altera_horario = mysql_query("UPDATE rh_horarios SET nome = '".acentoMaiusculo($nome_horario[$k])."', obs = '".acentoMaiusculo($obs[$k])."', entrada_1 = '{$entrada[$k]}', saida_1 = '{$ida_almoco[$k]}',
//                                            entrada_2 = '{$volta_almoco[$k]}', saida_2 = '{$saida[$k]}', dias_semana = '{$dias_semana[$k]}', horas_mes = '{$horas_mes[$k]}', horas_semanais ='{$horas_semana[$k]}', salario = '{$salario}', 
//                                            valor_dia = '{$diaria_f}', valor_hora = '{$hora_f}', folga = '{$folga}', dias_mes = '{$dias_mes[$k]}', adicional_noturno = '{$noturno[$k]}', horas_noturnas = '{$horas_noturno[$k]}' WHERE id_horario = {$id_horario[$k]}") or die(mysql_error());
//                        }
//
//                        if ($id_horario[$k] == 0 || $id_horario[$k] == NULL && $id_horario[$k] == ''){
//                            $insere_horario = mysql_query("INSERT INTO rh_horarios (id_regiao,nome,obs,entrada_1,saida_1,entrada_2,saida_2,dias_semana,horas_mes,horas_semanais,salario,funcao,valor_dia,valor_hora,folga,dias_mes,adicional_noturno,horas_noturnas) VALUES
//                                            ('{$regiao}', '".acentoMaiusculo($nome_horario[$k])."', '".acentoMaiusculo($obs[$k])."', '{$entrada[$k]}', '{$ida_almoco[$k]}', '{$volta_almoco[$k]}', '{$saida[$k]}', '{$dias_semana[$k]}',
//                                            '{$horas_mes[$k]}', '{$horas_semana[$k]}', '{$salario}', '{$id_curso}', '{$diaria_f}','{$hora_f}','{$folga}', '{$dias_mes[$k]}', '{$noturno[$k]}', '{$horas_noturno[$k]}')") or die(mysql_error());
//                        }
//                    }
//                
//                }
                if ($altera_curso) {
                    $_SESSION['MESSAGE'] = 'Informações atualizadas com sucesso!';
                    $_SESSION['MESSAGE_COLOR'] = 'message-blue';
                    $_SESSION['regiao'] = $regiao;
                    $_SESSION['projeto'] = $projeto;
                    header('Location: index.php');
                } else {
                    $_SESSION['MESSAGE'] = 'Erro ao alterar a função';
                    $_SESSION['MESSAGE_COLOR'] = 'message-red';
                    $_SESSION['regiao'] = $regiao;
                    $_SESSION['projeto'] = $projeto;
                    header('Location: index.php');
                }
            }
            //exit('passou'); Coisa de Leozinho
        }
    }

    public function montaTabelaFuncoesNiveis($arrayCursosNovo, $editar = null, $nova = null) {
        $percentuais = str_replace(",", ".", $_REQUEST['percent'][0]);
        if (count($arrayCursosNovo) > 0) {
            $tabelaFuncoesNova = "<table class='table table-bordered table-condensed text-sm valign-middle'><tr><td>Cargo</td><td class='text-center'>Valor</td><td class='text-center'>Diferença</td><td class='text-center'>Novo Valor</td></tr>";
            foreach ($arrayCursosNovo as $nome => $value) {
                $tabelaFuncoesNova .= "<tr><td rowspan='" . (count($value)) . "'>" . $nome . "</td><td class='text-center'>". number_format($value['']['valor'], 2, ',', '.'). "</td><td class='text-center'>". number_format($value['']['valor']*$percentuais, 2, ',', '.')."</td><td class='text-center'>". number_format($value['']['valor']*($percentuais+1), 2, ',', '.')."</td>";

                $tabelaFuncoesNova .= '</tr>';
            }
            $tabelaFuncoesNova .= '<table>';
        }

        return $tabelaFuncoesNova;
    }

    /**
     * Método que grava os dados de horário em rh_horarios
     *
     * @param $id_regiao Id Região
     */
    public static function cadastraHorario($id_regiao) {

        if (isset($_REQUEST['cadastrar'])) {

            //$regiao_selecionada = $_REQUEST['regiao_selecionada'];
            $regiao_logado = $_REQUEST['regiao_logado'];

            //horarios
            $nome_horario = $_REQUEST['nome_horario'];
            $obs = $_REQUEST['obs'];
            $entrada = $_REQUEST['entrada'];
            $ida_almoco = $_REQUEST['ida_almoco'];
            $volta_almoco = $_REQUEST['volta_almoco'];
            $saida = $_REQUEST['saida'];
            $horas_mes = $_REQUEST['horas_mes'];
            $dias_mes = $_REQUEST['dias_mes'];
            $horas_semana = $_REQUEST['horas_semana'];
            $dias_semana = $_REQUEST['dias_semana'];
            $folgaDef = $_REQUEST['folga'];
            $noturno = $_REQUEST['noturno'];
            $horas_noturno = $_REQUEST['horas_noturno'];
            $porcentagemAd = $_REQUEST['porcentagemAd'];

            foreach ($nome_horario as $k => $valor) {

                //trata folga
                if ($folgaDef[$k][0] == "1" && $folgaDef[$k][1] == "2") {//SEGUNDA A SEXTA
                    $folga = "3";
                } elseif ($folgaDef[$k][0] == "1") {//FOLGA NO SABADO
                    $folga = "1";
                } elseif ($folgaDef[$k][1] == "2") {//FOLGA NO DOMINGO
                    $folga = "2";
                } elseif ($folgaDef[$k][2] == "5") {//PLANTONISTA
                    $folga = "5";
                } else {
                    $folga = "0"; //SEM FOLGAS( SEGUNDA À SEGUNDA )
                }

                if ($folgaDef[$k][2] == "5") {
                    $folga = "5";
                }

                $insere_horario = mysql_query("INSERT INTO rh_horarios (id_regiao,nome,obs,entrada_1,saida_1,entrada_2,saida_2,dias_semana,horas_mes,horas_semanais,salario,funcao,valor_dia,valor_hora,folga,dias_mes,adicional_noturno,horas_noturnas, porcentagem_adicional) VALUES
                      ('{$id_regiao}', '" . acentoMaiusculo($nome_horario[$k]) . "', '" . acentoMaiusculo($obs[$k]) . "', '{$entrada[$k]}', '{$ida_almoco[$k]}', '{$volta_almoco[$k]}', '{$saida[$k]}', '{$dias_semana[$k]}',
                      '{$horas_mes[$k]}', '{$horas_semana[$k]}' ,'', '', '','','{$folga}', '{$dias_mes[$k]}', '{$noturno[$k]}', '{$horas_noturno[$k]}' , '{$porcentagemAd[$k]}')") or die(mysql_error());
            }
        }
    }

    /**
     * Método que altera o horário de dados horário em rh_horarios
     *
     * @param $id_horario Id do horário que vai ser alterado
     * @param $id_regiao Id região
     */
    public static function alteraHorario($id_horario, $id_regiao) {

        if (isset($_REQUEST['atualizar'])) {

           // $regiao_selecionada = $_REQUEST['regiao_selecionada'];
            $regiao_logado = $_REQUEST['regiao_logado'];

            //horarios
            $nome_horario = $_REQUEST['nome_horario'];
            $obs = $_REQUEST['obs'];
            $entrada = $_REQUEST['entrada'];
            $ida_almoco = $_REQUEST['ida_almoco'];
            $volta_almoco = $_REQUEST['volta_almoco'];
            $saida = $_REQUEST['saida'];
            $horas_mes = $_REQUEST['horas_mes'];
            $dias_mes = $_REQUEST['dias_mes'];
            $horas_semana = $_REQUEST['horas_semana'];
            $dias_semana = $_REQUEST['dias_semana'];
            $folgaDef = $_REQUEST['folga'];
            $noturno = $_REQUEST['noturno'];
            $horas_noturno = $_REQUEST['horas_noturno'];
            $porcentagemAd = $_REQUEST['porcentagemAd'];

            foreach ($nome_horario as $k => $valor) {

                if (!$noturno[$k])
                    $horas_noturno[$k] = null;

                //trata folga

                if ($folgaDef[$k][0] == "1" && $folgaDef[1][1] == "2") {//SEGUNDA A SEXTA
                    $folga = "3";
                    break;

                } elseif ($folgaDef[$k][0] == "1") {//FOLGA NO SABADO
                    $folga = "1";
                    break;

                } elseif ($folgaDef[$k][1] == "2") {//FOLGA NO DOMINGO
                    $folga = "2";
                    break;

                } elseif ($folgaDef[$k][2] == "5") {//PLANTONISTA
                    $folga = "5";
                    break;
                } else {
                    $folga = "0"; //SEM FOLGAS( SEGUNDA À SEGUNDA )
                    break;
                }
            }

            $altera_horario = mysql_query("UPDATE rh_horarios SET id_regiao = '{$id_regiao}', nome = '" . acentoMaiusculo($nome_horario[$k]) . "',  obs = '" . acentoMaiusculo($obs[$k]) . "',  entrada_1 = '{$entrada[$k]}',  saida_1 = '{$ida_almoco[$k]}',  entrada_2 = '{$volta_almoco[$k]}',  saida_2 = '{$saida[$k]}',  dias_semana = '{$dias_semana[$k]}',  horas_mes = '{$horas_mes[$k]}',  horas_semanais = '{$horas_semana[$k]}',  salario = '', valor_dia = '',  valor_hora = '',  folga = '{$folga}',  dias_mes = '{$dias_mes[$k]}',  adicional_noturno = '{$noturno[$k]}', horas_noturnas = '{$horas_noturno[$k]}', porcentagem_adicional = '{$porcentagemAd[$k]}' WHERE id_horario = '{$id_horario}' LIMIT 1") or die(mysql_error());
        }
    }
    
    public function listFuncoesHistoricoSalarial($id_regiao,$ano){
        
        $qr = "SELECT A.id_curso,A.nome,/*A.letra,A.numero,*/A.salario,B.salario_antigo,B.salario_novo,B.diferenca,B.motivo,DATE_FORMAT(B.`data`, '%m/%Y') AS competencia
                FROM curso AS A
                INNER JOIN rh_salario AS B ON (A.id_curso = B.id_curso AND YEAR(B.`data`) = {$ano})
                WHERE A.id_regiao = {$id_regiao}
                ORDER BY A.nome";
        $result = mysql_query($qr);
        return $result;
    }
}
?>
