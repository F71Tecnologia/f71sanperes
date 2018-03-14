<?php

include('include/restricoes.php');
include('../funcoes.php');
include('include/criptografia.php');
include('../classes/formato_data.php');




$regiao = mysql_real_escape_string($_GET['regiao']);
$link_master = $_POST['master'];
$id_prestador = $_POST['prestador_id'];
$regiao = $_POST['regiao'];
$compra = $_POST['compra'];

$qr_prestador = mysql_query("SELECT * FROM prestadorservico WHERE id_prestador = '$prestador'");
$row = mysql_fetch_assoc($qr_prestador);
if ($_COOKIE['logado'] == 87) {

    print_r($_REQUEST);
}


if (isset($_POST['Submit'])) {



    //VERIFICA SE O PRESTADOR É DO TIPO PESSSOAL FÍSICA		
    if ($_POST['tipo_prestador'] == 3) {

        if (!empty($_POST['dep_nome'])) {

            //RECEBE ARRAYS PARA ATUALIZAR O DEPENDENTE
            $dependente_nome = $_POST['dep_nome'];
            $dependente_parentesco = $_POST['dep_parentesco'];
            $dependente_nascimento = $_POST['dep_data_nasc'];
            $dependente_id = $_POST['ids_dependente'];
            $sql = array();

            mysql_query("DELETE FROM prestador_dependente WHERE prestador_id = '$id_prestador' ");
            foreach ($dependente_nome as $chave => $nome) {

                $data_nasc2 = implode('-', array_reverse(explode('/', trim($dependente_nascimento[$chave]))));
                $parentesco = trim($dependente_parentesco[$chave]);
                $nome = trim($dependente_nome[$chave]);

                $sql[] = "('$id_prestador', '$nome', '$parentesco', '$data_nasc2',1)";
            }
            $sql = implode(',', $sql);

            mysql_query("INSERT INTO prestador_dependente (prestador_id,prestador_dep_nome, prestador_dep_parentesco, prestador_dep_data_nasc, prestador_dep_status)
							VALUES $sql ") or die(mysql_error());
        }
    }

    /* 	
      foreach($dependente_id  as $chave => $valor){
      if(!empty($dependente_nome[$chave]) or !empty($dependente_parentesco[$chave]) or  !empty($dependente_nascimento[$chave])) {

      $data_nasc2 = implode('-',array_reverse(explode('/',trim($dependente_nascimento[$chave]))));
      $parentesco = trim($dependente_parentesco[$chave]);
      $nome = trim($dependente_nome[$chave]);

      mysql_query("UPDATE prestador_dependente SET prestador_dep_nome = '$nome', prestador_dep_parentesco = '$parentesco',  	prestador_dep_data_nasc = '$data_nasc2' WHERE prestador_dep_id = '$dependente_id[$chave]';") or die(mysql_error());
      }
      unset($data_nasc, $parentesco,$nome);
      }
      }
      //FIM ATUALIZAR DEPENDENTE




      //INSERE NOVOS DEPENDENTES
      if(!empty($_POST['add_dep_nome'])) {

      $add_dep_nome = $_POST['add_dep_nome'];
      $add_dep_parentesco = $_POST['add_dep_parentesco'];
      $add_dep_data_nasc = $_POST['add_dep_data_nasc'];


      foreach($add_dep_nome as $chave =>$valor){

      if(empty($add_dep_nome[$chave]) or empty($add_dep_parentesco[$chave]) or  empty($add_dep_data_nasc[$chave])) continue;


      $data_nasc2 = implode('-',array_reverse(explode('/',$add_dep_data_nasc[$chave])));

      mysql_query("INSERT INTO prestador_dependente (prestador_id,prestador_dep_nome, prestador_dep_parentesco, prestador_dep_data_nasc, prestador_dep_status)
      VALUES
      ('$id_prestador', '$add_dep_nome[$chave]', '$add_dep_parentesco[$chave]', '$data_nasc2', '1');") or die(mysql_error());
      }

      }//FIM INSERE DEPENDENTES

      } else {

      $qr_dependente2 = mysql_query("SELECT * FROM prestador_dependente WHERE  prestador_id = '$id_prestador'");
      $verifica = mysql_num_rows($qr_dependente2);
      if( $verifica != 0)
      mysql_query("UPDATE prestador_dependente SET prestador_dep_status = 0 WHERE prestador_id = '$id_prestador' ");

      }
     */


    $id_prestador = $_REQUEST['prestador_id'];
    $id_projeto = $_REQUEST['projeto'];
    $id_user = $_COOKIE['logado'];
    $regiao = $_REQUEST['regiao'];
    $endereco = $_REQUEST['endereco'];
    $cnpj = $_REQUEST['cnpj'];
    $c_fantasia = $_REQUEST['c_fantasia'];
    $c_razao = $_REQUEST['c_razao'];
    $c_endereco = $_REQUEST['c_endereco'];
    $c_cnpj = $_REQUEST['c_cnpj'];
    $c_ie = $_REQUEST['c_ie'];
    $c_im = $_REQUEST['c_im'];
    $c_tel = $_REQUEST['c_tel'];
    $c_fax = $_REQUEST['c_fax'];
    $c_email = $_REQUEST['c_email'];
    $c_responsavel = $_REQUEST['c_responsavel'];
    $c_civil = $_REQUEST['c_civil'];
    $c_nacionalidade = $_REQUEST['c_nacionalidade'];
    $c_formacao = $_REQUEST['c_formacao'];
    $c_rg = $_REQUEST['c_rg'];
    $c_cpf = $_REQUEST['c_cpf'];
    $c_email2 = $_REQUEST['c_email2'];
    $c_site = $_REQUEST['c_site'];
    $co_responsavel = $_REQUEST['co_responsavel'];
    $co_tel = $_REQUEST['co_tel'];
    $co_fax = $_REQUEST['co_fax'];
    $co_civil = $_REQUEST['co_civil'];
    $co_nacionalidade = $_REQUEST['co_nacionalidade'];
    $co_email = $_REQUEST['co_email'];
    $co_municipio = $_REQUEST['co_municipio'];
    $assunto = $_REQUEST['assunto'];
    $objeto = $_REQUEST['objeto'];
    $especificacao = $_REQUEST['especificacao'];
    $data_proc = $_REQUEST['data_proc'];
    $prestador_tipo = $_POST['tipo_prestador'];
    $valor = str_replace('.', '', $_REQUEST['valor']);
    $data_inicio = implode('-', array_reverse(explode('/', $_REQUEST['data_inicio'])));
    $valor_limite = str_replace(',', '.', str_replace('.', '', $_POST['valor_limite']));
    $co_nome_banco = $_REQUEST['co_nome_banco'];
    $co_agencia = $_REQUEST['co_agencia'];
    $co_conta = $_REQUEST['co_conta'];

    $co_responsavel_socio1 = $_REQUEST['co_responsavel_socio1'];
    $co_tel_socio1 = $_REQUEST['co_tel_socio1'];
    $co_fax_socio1 = $_REQUEST['co_fax_socio1'];
    $co_email_socio1 = $_REQUEST['co_email_socio1'];
    $co_civil_socio1 = $_REQUEST['co_civil_socio1'];
    $co_nacionalidade_socio1 = $_REQUEST['co_nacionalidade_socio1'];
    $co_data_nasc_socio1 = implode('-', array_reverse(explode('/', $_REQUEST['co_data_nasc_socio1'])));
    $co_municipio_socio1 = $_REQUEST['co_municipio_socio1'];

    $co_responsavel_socio2 = $_REQUEST['co_responsavel_socio2'];
    $co_tel_socio2 = $_REQUEST['co_tel_socio2'];
    $co_fax_socio2 = $_REQUEST['co_fax_socio2'];
    $co_email_socio2 = $_REQUEST['co_email_socio2'];
    $co_civil_socio2 = $_REQUEST['co_civil_socio2'];
    $co_nacionalidade_socio2 = $_REQUEST['co_nacionalidade_socio2'];
    $co_data_nasc_socio2 = implode('-', array_reverse(explode('/', $_REQUEST['co_data_nasc_socio2'])));
    $co_municipio_socio2 = $_REQUEST['co_municipio_socio2'];
    $contratado_em = $_REQUEST['dataInicio'];
    $encerrado_em = $_REQUEST['dataFinal'];
    $contratado_em = date("Y-m-d", strtotime(str_replace("/", "-", $contratado_em)));
    $encerrado_em = date("Y-m-d", strtotime(str_replace("/", "-", $encerrado_em)));
    $prestacao_contas = $_REQUEST['prestacao_contas'];
    $medida = $_REQUEST['medida'];

    
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
            return "Data invalida";
        }
    }

    $data_proc_f = ConverteData($data_proc);

    mysql_query("UPDATE prestadorservico SET 
		id_medida = '$medida', 
		endereco = '$endereco', 
		cnpj = '$cnpj', 
		c_fantasia = '$c_fantasia', 
		c_razao = '$c_razao', 
		c_endereco = '$c_endereco', 
		c_cnpj = '$c_cnpj', 
		c_ie = '$c_ie', 
		c_im = '$c_im', 
		c_tel = '$c_tel', 
		c_fax = '$c_fax', 
		c_email = '$c_email', 
		c_responsavel = '$c_responsavel', 
		c_civil = '$c_civil', 
		c_nacionalidade = '$c_nacionalidade', 
		c_formacao = '$c_formacao', 
		c_rg = '$c_rg', 
		c_cpf = '$c_cpf', 
		c_email2 = '$c_email2', 
		c_site = '$c_site', 
		co_responsavel = '$co_responsavel', 
		co_tel = '$co_tel', 
		co_fax = '$co_fax', 
		co_civil = '$co_civil', 
		co_nacionalidade = '$co_nacionalidade', 
		co_email = '$co_email', 
		co_municipio = '$co_municipio', 
		assunto = '$assunto', 
		objeto = '$objeto', 
		especificacao = '$especificacao',
		prestador_tipo = '$prestador_tipo',
		data_proc = '$data_proc_f',
                data        = '$data_inicio',
                valor = '$valor',
		valor_limite = '$valor_limite',
                co_responsavel_socio1 = '$co_responsavel_socio1',
                co_tel_socio1         = '$co_tel_socio1',
                co_fax_socio1         = '$co_fax_socio1',
                co_civil_socio1       = '$co_civil_socio1',
                co_nacionalidade_socio1 = '$co_nacionalidade_socio1',
                co_email_socio1         = '$co_email_socio1',
                co_municipio_socio1     = '$co_municipio_socio1',
                data_nasc_socio1        = '$co_data_nasc_socio1',
                co_responsavel_socio2 = '$co_responsavel_socio2',
                co_tel_socio2         = '$co_tel_socio2',
                co_fax_socio2         = '$co_fax_socio2',
                co_civil_socio2       = '$co_civil_socio2',
                co_nacionalidade_socio2 = '$co_nacionalidade_socio2',
                co_email_socio2         = '$co_email_socio2',
                co_municipio_socio2     = '$co_municipio_socio2',
                data_nasc_socio2        = '$co_data_nasc_socio2',
                nome_banco = '$co_nome_banco',    
                agencia = '$co_agencia',
                conta = '$co_conta',
                contratado_em           = '$contratado_em',
                encerrado_em            = '$encerrado_em',
                prestacao_contas = '$prestacao_contas'
                WHERE id_prestador = '$id_prestador' ") or die("Erro<br>" . mysql_error());

    if ($compra <> 0) {

        mysql_query("UPDATE compra2 SET acompanhamento='9' where id_compra = '$compra'") or die("<center>ERRO!<br> tente novamente mais tarde<br><br>" . mysql_error());
    }



    print "
		<script>
		alert (\"$id_prestador - Dasos Atualizados!\"); 
		location.href=\"prestadorservico.php?id=1&regiao=$regiao\"
		</script>";
}