<?php

class ESocial extends ConsultasESocial {

    public $id_master;
    public $matriz;
    public $iniValidade;
    public $fimValidade;
    public $sequencial = 1;
    public $tpevento;
    public $iniValidadeN;
    public $fimValidadeN;

    public function __construct($idMaster, $iniValidade, $fimValidade = null, $tpevento = null, $iniValidadeN = null, $fimValidadeN = null) {
        $this->id_master = $idMaster;
        $this->iniValidade = $iniValidade;
        $this->fimValidade = $fimValidade;
        $this->tpevento = $tpevento;
        $this->iniValidadeN = $iniValidadeN;
        $this->fimValidadeN = $fimValidadeN;
    }

    
    function formataValor($valor) {
        $vformatado = str_replace(',', '.', str_replace('.', '', $valor));
        return $vformatado;
    }

    function formataCnae($cnae) {
        $cnae = RemoveEspacos(RemoveCaracteres($cnae));
        return sprintf("%09s", substr($cnae, 0, 4) . '-' . substr($cnae, -3, 1) . '/' . substr($cnae, 5, 7));
    }

    function validaHora($hora) {
        $hora = explode(":", $hora);
        $h = $hora[0] * 60;
        $m = $h + $hora[1];
        if ($hora[1] > 59 || $h <= 0 || $h > 1439 || empty($h)) {
            return false;
        } else {
            return true;
        }
    }

    public function gravaLog($idUsuario, $evento, $regiao = 'NULL', $projeto = 'NULL') {
        $dt_proc = date("Y-m-d H:i:s", time());
        $eventos = implode(',', $evento);
        if (empty($this->fimValidade)) {
            $fimValidade = 'NULL';
        } else {
            $fimValidade = $this->fimValidade;
        }
        $qrlog = "INSERT INTO log_e_social (`id_usuario`, `data_proc`, `ini_validade`, `fim_validade`, `tp_evento`, `evento`, `id_master`, `id_regiao`, `id_projeto`)
                  VALUES ($idUsuario, '$dt_proc', $this->iniValidade, $fimValidade, '$this->tpevento', '$eventos', $this->id_master, $regiao, $projeto);";
//        print_r($qrlog);exit;

        mysql_query($qrlog)or die("ERRO AO GRAVAR O LOG.");
    }

    public function montaMatrizIncid(array $dados) {
        $arrayTipo = array(0 => "INSS", 1 => "IRRF", 2 => "FGTS", 3 => "Contribuição Sindical Laboral");
        foreach ($arrayTipo as $key => $vtipo) {
            if ($vtipo == $dados["tipo"]) {
                $this->matriz[$dados["id_mov"]][$key][] = $dados["cod_incid"];
            }
        }
    }

    public function montaMatrizCondDif(array $dados, $evento) {
        if ($evento == '2360') {
            $tpCondicaoPer = $dados['tpCondicaoPer'];
            $tpCondicaoIns = $dados['tpCondicaoIns'];
        } else {
            $tpCondicaoPer = $dados['tpCondicaoPer_de'];
            $tpCondicaoIns = $dados['tpCondicaoIns_de'];
        }

        if (!empty($tpCondicaoPer)) {
            $this->matriz[$dados['id_clt']]['tpCondicao'][$tpCondicaoPer] = $dados;
        }
        if (!empty($tpCondicaoIns)) {
            $this->matriz[$dados['id_clt']]['tpCondicao'][$tpCondicaoIns] = $dados;
        }
    }

    public function getMatriz() {
        return $this->matriz;
    }

    public function montaMatrizErro($id, $msg) {
        $this->matriz['erro'][$id][] = $msg;
    }

    public function montaMatrizDetDeducoes(array $dados, $pensaoA) {
        if (empty($dados['a5021']) && !$dados['a5049']) {
            $valorRtpd = $dados['a5049'];
        } elseif (!empty($dados['a5021'])) {
            $valorRtpd = $dados['a5021'];
        }
        $this->matriz['detDeducao']['RTPO'] = $dados['inss'];
        $this->matriz['detDeducao']['RTDP'] = $valorRtpd;
        if ($pensaoA) {
            $this->matriz['RTPA'] = $pensaoA['valor_movimento'];
        }
//        print_r($this->matriz); exit;
    }

    public function montaMatrizRendIsento($ajudaCusto, $rescisao, $abonoPecuniario) {
        if ($ajudaCusto) {
            $this->matriz['RendIsentos']['RIDAC'] = $ajudaCusto['valor'];
        }
        if ($rescisao) {
            $this->matriz['RendIsentos']['RIIRP'] = $rescisao['sal_base'] - $rescisao['saldo_salario'];
        }
        if ($abonoPecuniario) {
            $this->matriz['RendIsentos']['RIAP'] = $abonoPecuniario['abono_pecuniario'];
        }
    }

    public function zeraMatriz() {
        unset($this->matriz);
    }

    public function reiniciaSequencial() {
        unset($this->sequencial);
        $this->sequencial = 1;
    }

    //aki
    public function montaCabecalho($empregador, $dom, $ev, $dados = null) {
        $tpInscricao = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricao = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));
        $dt_hs_atual = str_replace(" ", "", RemoveCaracteres(date("Y-m-d H:i:s", time())));
        $sequencial = sprintf("%05s", $this->sequencial);

        $id = sprintf("%-36s", "ID" . $tpInscricao . $nrInscricao . $dt_hs_atual . $sequencial);
        $versao = sprintf("%-11s", "1.2 - beta2"); // VERSÃO DO LAYOUT
        $tpAmb = sprintf("%01s", 1); // CONSTANTE
        $procEmi = sprintf("%01s", 1); // CONSTANTE
        $indSeguimento = sprintf("%01s", 1); // CONSTANTE
        $verProc = sprintf("%-20s", "Sei lah que versao ah essa"); // FALTA SABER VERSÃO DO App EMISSOR DO EVENTO
        // *** INÍCIO CABECALHO *** //
        $evtInfoEmpregador = $dom->createElement($ev);
        $evtInfoEmpregador->setAttribute("Id", $id);
        $evtInfoEmpregador->setAttribute("versao", $versao);

        $ideEvento = $dom->createElement("ideEvento");


//        $infnrRecibo = $dom->createElement("nrRecibo", $nrRecibo);  //ac cab

        $inftpAmb = $dom->createElement("tpAmb", $tpAmb);
        $infprocEmi = $dom->createElement("procEmi", $procEmi);
        $infindSeguimento = $dom->createElement("indSeguimento", $indSeguimento);
        $infverProc = $dom->createElement("verProc", $verProc);

        #adiciona os nós (informacaoes do evento) em ideEvento
        //INFORMAÇÕES VARIAVEIS, DEPENDENDO DO TIPO VAI GERAR OU NÃO
        if (isset($dados['indRetificacao'])) {
            $infindRetificacao = $dom->createElement("indRetificacao", $dados['indRetificacao']); //ac cab
            $ideEvento->appendChild($infindRetificacao);
        }
        if (isset($dados['nrRecibo'])) {
            $infnrRecibo = $dom->createElement("nrRecibo", $dados['nrRecibo']);  //ac cab
            $ideEvento->appendChild($infnrRecibo);
        }
        if (isset($dados['terceiro'])) {
            $infindApuracao = $dom->createElement("indApuracao", $dados['indApuracao']);
            $infperApuracao = $dom->createElement("perApuracao", $dados['perApuracao']);
            $ideEvento->appendChild($infindApuracao);
            $ideEvento->appendChild($infperApuracao);
        }

        $ideEvento->appendChild($inftpAmb);
        $ideEvento->appendChild($infprocEmi);
        $ideEvento->appendChild($infindSeguimento);
        $ideEvento->appendChild($infverProc);



        #adiciona o nó (ideEvento) em evtInfoEmpregador
        $evtInfoEmpregador->appendChild($ideEvento);

        $evtInfoEmpregador->setAttribute("Id", $id);
        $evtInfoEmpregador->setAttribute("versao", $versao);

        #adiciona o nó evtInfoEmpregador em eSocial
        //$eSocial->appendChild($evtInfoEmpregador);
        // *** FINAL CABECALHO *** //
        return $evtInfoEmpregador;
    }

//INFORMAÇÕES DO EMPREGADOR
    public function montas1000($arquivo, $empregador, $aliquota, $percentNovo, $sHouse) {

        $tpInscricao = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricao = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));

        $nomeRazao = sprintf("%-115s", RemoveAcentos(RemoveEspacos($empregador["nomeRazao"])));
        $classTrib = sprintf("%02s", RemoveEspacos($empregador["classTrib"]));
        $natJuridica = sprintf("%04s", RemoveEspacos(RemoveCaracteres($empregador["nat_juridica"])));
        $cnae = sprintf("%07s", RemoveEspacos(RemoveCaracteres($empregador["cnae"])));
        $indCooperativa = sprintf("%01s", RemoveEspacos($empregador["indCooperativa"]));
        $indConstrutora = sprintf("%01s", RemoveEspacos($empregador["indConstrutora"]));
        $indDesFolha = sprintf("%01s", RemoveEspacos($empregador["indDesFolha"]));
        $indOptRegEletronico = sprintf("%01s", RemoveEspacos($empregador["indOptRegEletronico"]));
        $aliqRat = sprintf("%01s", RemoveEspacos($empregador["aliquotaRat"]));
        $fapNovo = ((int) RemoveEspacos($empregador["fap"]) / 100);
        $fap = sprintf("%06s", $fapNovo);
        $aliqRatAjustada = $aliqRat * $fapNovo;
        $aliqRatAjustada = sprintf("%06s", $aliqRatAjustada);

        if ($aliqRat != $aliquota) {
            $tpProcessoRat = sprintf("%01s", RemoveEspacos($empregador["tpProcessoRat"]));
            $nrProcessoRat = sprintf("%-20s", RemoveEspacos($empregador["nrProcessoRat"]));
        }

        if ($fapNovo != $percentNovo) {
            $tpProcessoFap = sprintf("%01s", RemoveEspacos($empregador["tpProcessoFap"]));
            $nrProcessoFap = sprintf("%-20s", RemoveEspacos($empregador["nrProcessoFap"]));
        }

        if ($classTrib == 80) {
            // INFO INICIO EMPRESAS ISENTAS
            $siglaMin = sprintf("%-08s", RemoveEspacos($empregador["siglaMin"])); // SIGLA DO MINISTÉRIO QUE CONCEDEU O CERTIFICADO DE ISENÇÃO
            $nrCerificado = sprintf("%-40s", RemoveEspacos($empregador["nrCertificado"]));
            $dtEmissaoCertificado = sprintf("%-10s", $empregador["dtEmissaoCertificado"]);
            $dtVenctoCertificado = sprintf("%-10s", $empregador["dtVencCertificado"]);
            $nrProtRenovacao = sprintf("%-40s", RemoveEspacos($empregador["dtVencCertificado"]));
            $dtProtRenovacao = sprintf("%-10s", $empregador["dtProtRenovacao"]);
            $dtDou = sprintf("%-10s", $empregador["dtDou"]);
            $pagDou = sprintf("%-05s", RemoveEspacos($empregador["pagDou"]));
            // INFO FIM EMPRESA ISENTAS
        }

        $nomeContato = sprintf("%-60s", RemoveAcentos(RemoveEspacos($empregador["responsavel"])));
        $cpfContato = sprintf("%11s", RemoveCaracteres(RemoveEspacos($empregador["cpf"])));
        $foneFixo = sprintf("%-13s", RemoveCaracteres(RemoveEspacos($empregador["tel"])));
        $foneCelular = sprintf("%-13s", RemoveCaracteres(RemoveEspacos($empregador["celular"])));
        $fax = sprintf("%-13s", RemoveCaracteres(RemoveEspacos($empregador["fax"])));
        $email = sprintf("%-13s", RemoveEspacos($empregador["email"]));

        if ($classTrib == 60) {
            $indAcordoIsencaoMulta = sprintf("%01s", (RemoveEspacos($empregador["indAcordoIsencaoMulta"])));
        }

        //INÍCIO INFO SOFTWARE HOUSE
        $cnpjSH = sprintf("%14s", RemoveCaracteres(RemoveEspacos($sHouse["cnpj"])));
        $nomeRazaoSH = sprintf("%-115s", RemoveAcentos(RemoveEspacos($sHouse["razao"])));
        $nomeContatoSH = sprintf("%-60s", RemoveAcentos(RemoveEspacos($sHouse["responsavel"])));
        $telefoneSH = sprintf("%-13s", RemoveCaracteres(RemoveEspacos($sHouse["tel"])));
        $codMunicipioSH = sprintf("%07s", RemoveCaracteres(RemoveEspacos($sHouse["cod_municipio"])));
        $ufSH = sprintf("%-02s", RemoveEspacos($sHouse["uf"]));
        $emailSH = sprintf("%-60s", RemoveEspacos($sHouse["email"]));
        //FIM INFO SOFTWARE HOUSE

        $indSocioOstencivo = sprintf("%-01s", RemoveCaracteres(RemoveEspacos($empregador["indSocioOstensivo"])));
        $indSituacaoEspecial = sprintf("%01s", RemoveEspacos($sHouse["indSituacaoEspecial"])); //INDICATIVO DE SITUAÇÃO ESPECIAL : 0- NORMAL  1- EXTINÇAO  2- FUSÃO 3-CISÃO  4-INCLORPORAÇÃO
        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        #cria os elementos e inclui os atributos
        $eSocial = $dom->createElement("esocial");

        $xmlCabecalho = $this->montaCabecalho($empregador, $dom, "evtInfoEmpregador");
        $eSocial->appendChild($xmlCabecalho);

        // *** INÍCIO DADOS EMPREGADOR *** //
        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricao = $dom->createElement("tpInscricao", $tpInscricao);
        $infnrInscricao = $dom->createElement("nrInscricao", $nrInscricao);

        $infoEmpregador = $dom->createElement("infoEmpregador");

        $evento = $dom->createElement($this->tpevento);
        $idePeriodo = $dom->createElement("idePeriodo");

        if (!empty($this->iniValidade)) {
            $iniValidade = $dom->createElement("iniValidade", $this->limpaData($this->iniValidade));
            #adiciona o nó($iniValidade) em idPeriodo
            $idePeriodo->appendChild($iniValidade);
        }
        if (!empty($this->fimValidade)) {
            $fimValidade = $dom->createElement("fimValidade", $this->limpaData($this->fimValidade));
            #adiciona o nó($fimValidade) em idPeriodo
            $idePeriodo->appendChild($fimValidade);
        }

        #adiciona o nó($idPeriodo) em $evento
        $evento->appendChild($idePeriodo);

        if ($this->tpevento != "exclusao") {
            $infoCadastro = $dom->createElement("infoCadastro");
            $infnomeRazao = $dom->createElement("nomeRazao", $nomeRazao);
            $infclassTrib = $dom->createElement("classTrib", $classTrib);
            $infnatJuridica = $dom->createElement("natJuridica", $natJuridica);
            $cnaePreponderante = $dom->createElement("cnaePreponderante", $cnae);
            $infindCooperativa = $dom->createElement("indCooperativa", $indCooperativa);
            $infindConstrutora = $dom->createElement("indConstrutora", $indConstrutora);
            $infindDesFolha = $dom->createElement("indDesFolha", $indDesFolha);
            $infindOptRegEletronico = $dom->createElement("indOptRegEletronico", $indOptRegEletronico);

            $aliqGilrat = $dom->createElement("aliqGilrat");
            $infaliqRat = $dom->createElement("aliqRat", $aliqRat);
            $inffap = $dom->createElement("fap", $fap);
            $infaliqRatAjustada = $dom->createElement("aliqRatAjustada", $aliqRatAjustada);

            #adiciona os nós($infaliqRat, $inffap, $infaliqRatAjustada) em $aliqGilrat
            $aliqGilrat->appendChild($infaliqRat);
            $aliqGilrat->appendChild($inffap);
            $aliqGilrat->appendChild($infaliqRatAjustada);


            if ($aliqRat != $aliquota) { // Se a aliquota que está na tabela do master for diferente da tabela RAT fornecida pela RF 
                $procAdmJudRat = $dom->createElement("procAdmJudRat");
                $inftpProcessoRat = $dom->createElement("tpProcesso", $tpProcessoRat);
                $infnrProcessoRat = $dom->createElement("nrProcesso", $nrProcessoRat);

                #adiciona o nó($procAdmJudRat) em $aliqGilrat
                $aliqGilrat->appendChild($procAdmJudRat);

                #adiciona os nós($inftpProcesso,$infnrProcesso) em $procAdmJudRat
                $procAdmJudRat->appendChild($inftpProcessoRat);
                $procAdmJudRat->appendChild($infnrProcessoRat);
            }

            if ($fapNovo != $percentNovo) { // Se o percentual que está na tabela do master for diferente da tabela FAP fornecida pela RF
                $procAdmJudRat = $dom->createElement("procAdmJudFap");
                $inftpProcessoFap = $dom->createElement("tpProcesso", $tpProcessoFap);
                $infnrProcessoFap = $dom->createElement("nrProcesso", $nrProcessoFap);

                #adiciona o nó($procAdmJudFap) em $aliqGilrat
                $aliqGilrat->appendChild($procAdmJudRat);

                #adiciona os nós($inftpProcesso,$infnrProcesso) em $procAdmJudFap
                $procAdmJudRat->appendChild($inftpProcessoFap);
                $procAdmJudRat->appendChild($infnrProcessoFap);
            }

            #adiciona os nós($infnomeRazao, $infclassTrib,$infnatJuridica,$cnaePreponderante,$infindCooperativa,$infindConstrutora,$infindDesFolha,$infindOptRegEletronico) em $infoCadastro
            $infoCadastro->appendChild($infnomeRazao);
            $infoCadastro->appendChild($infclassTrib);
            $infoCadastro->appendChild($infnatJuridica);
            $infoCadastro->appendChild($cnaePreponderante);
            $infoCadastro->appendChild($infindCooperativa);
            $infoCadastro->appendChild($infindConstrutora);
            $infoCadastro->appendChild($infindDesFolha);
            $infoCadastro->appendChild($infindOptRegEletronico);
            $infoCadastro->appendChild($aliqGilrat);


            if ($classTrib == 80) { // INFORMAÇÕES COMPLEMENTARES - EMPRESAS ISENTAS
                $dadosIsencao = $dom->createElement("dadosIsencao");
                $infsiglaMin = $dom->createElement("siglaMin", $siglaMin);
                $infnrCerificado = $dom->createElement("nrCertificado", $nrCerificado);
                $infdtEmissaoCertificado = $dom->createElement("dtEmissaoCertificado", $dtEmissaoCertificado);
                $infdtVenctoCertificado = $dom->createElement("dtVenctoCertificado", $dtVenctoCertificado);
                $infnrProtRenovacao = $dom->createElement("nrProtRenovacao", $nrProtRenovacao);
                $infdtProtRenovacao = $dom->createElement("dtProtRenovacao", $dtProtRenovacao);
                $infdtDou = $dom->createElement("dtDou", $dtDou);
                $infpagDou = $dom->createElement("pagDou", $pagDou);

                #adiciona o nó($dadosIsencao) em $infoCadastro
                $infoCadastro->appendChild($dadosIsencao);

                #adiciona os nós($infsiglaMin) em $dadosIsencao
                $dadosIsencao->appendChild($infsiglaMin);
                $dadosIsencao->appendChild($infnrCerificado);
                $dadosIsencao->appendChild($infdtEmissaoCertificado);
                $dadosIsencao->appendChild($infdtEmissaoCertificado);
                $dadosIsencao->appendChild($infdtVenctoCertificado);
                $dadosIsencao->appendChild($infnrProtRenovacao);
                $dadosIsencao->appendChild($infdtProtRenovacao);
                $dadosIsencao->appendChild($infdtDou);
                $dadosIsencao->appendChild($infpagDou);
            }


            $contato = $dom->createElement("contato");
            $infnomeContato = $dom->createElement("nomeContato", $nomeContato);
            $infcpfContato = $dom->CreateElement("cpfContato", $cpfContato);


            #adiciona os nós($infnomeContato,$infcpfContato,$inffoneFixo,$inffoneCelular,$inffax,$infemail) em $contato
            $contato->appendChild($infnomeContato);
            $contato->appendChild($infcpfContato);

            if (empty($empregador["celular"])) {
                $inffoneFixo = $dom->CreateElement("foneFixo", $foneFixo);
                $contato->appendChild($inffoneFixo);
            } else {
                if (!empty($empregador["tel"])) {
                    $inffoneFixo = $dom->CreateElement("foneFixo", $foneFixo);
                    $contato->appendChild($inffoneFixo);
                }
                $inffoneCelular = $dom->CreateElement("foneCelular", $foneCelular);
                $contato->appendChild($inffoneCelular);
            }
            if (!empty($empregador["fax"])) {
                $inffax = $dom->CreateElement("fax", $fax);
                $contato->appendChild($inffax);
            }
            $infemail = $dom->CreateElement("email", $email);
            $contato->appendChild($infemail);

            #adiciona o nó($contato) em $infoCadastro
            $infoCadastro->appendChild($contato);


            if ($classTrib == 60) { // INFORMAÇÕES EXCLUSIVAS PARA ORG INTERNACIONAIS
                $infoOrgInternacional = $dom->createElement("infoOrgInternacional");
                $infindAcordoIsencaoMulta = $dom->createElement("indAcordoIsencaoMulta", $indAcordoIsencaoMulta);

                #adiciona o nó($infoOrgInternacional) em $infoCadastro
                $infoCadastro->appendChild($infoOrgInternacional);

                #adiciona os nós($indAcordoIsencaoMulta) em $infoOrgInternacional
                $infoOrgInternacional->appendChild($infindAcordoIsencaoMulta);
            }


            $softwareHouse = $dom->createElement("softwareHouse");
            $infcnpjSoftwareHouse = $dom->createElement("cnpjSoftwareHouse", $cnpjSH);
            $infnomeRazao = $dom->createElement("nomeRazao", $nomeRazaoSH);
            $infnomeContato = $dom->createElement("nomeContato", $nomeContatoSH);
            $inftelegone = $dom->createElement("telefone", $telefoneSH);
            $infcodMunicipio = $dom->createElement("codMunicipio", $codMunicipioSH);
            $uf = $dom->createElement("uf", $ufSH);
            $email = $dom->createElement("email", $emailSH);

            #adiciona o nó($softwareHouse) em $infoCadastro
            $infoCadastro->appendChild($softwareHouse);

            #adiciona os nós($infcnpjSoftwareHouse,$infnomeRazao,$infnomeContato,$inftelegone,$infcodMunicipio,$uf,$email) em $softwareHouse
            $softwareHouse->appendChild($infcnpjSoftwareHouse);
            $softwareHouse->appendChild($infnomeRazao);
            $softwareHouse->appendChild($infnomeContato);
            $softwareHouse->appendChild($inftelegone);
            $softwareHouse->appendChild($infcodMunicipio);
            $softwareHouse->appendChild($uf);
            $softwareHouse->appendChild($email);

            $infoComplementares = $dom->createElement("infoComplementares");
            $infindSocioOstencivo = $dom->createElement("indSocioOstencivo", $indSocioOstencivo);
            $infindSituacaoEspecial = $dom->createElement("indSituacaoEspecial", $indSituacaoEspecial);

            #adiciona o nó($infoComplementares) em $infoCadastro
            $infoCadastro->appendChild($infoComplementares);

            #adiciona os nós($infoComplementares,$infindSituacaoEspecial) em $infoComplementares
            $infoComplementares->appendChild($infindSocioOstencivo);
            $infoComplementares->appendChild($infindSituacaoEspecial);
            #adiciona o nó($infoCadastro) em $evento
            $evento->appendChild($infoCadastro);
        }
        if ($this->tpevento == "alteracao") {
            $novaValidade = $dom->createElement("novaValidade");
            if (!empty($this->iniValidadeN)) {
                $infiniValidadeNova = $dom->createElement("iniValidade", $this->limpaData($this->iniValidadeN));
                #adiciona o nó($infiniValidade) em $inclusao
                $novaValidade->appendChild($infiniValidadeNova);
            }
            if (!empty($this->fimValidadeN)) {
                $inffimValidadeNova = $dom->createElement("fimValidade", $this->limpaData($this->fimValidadeN));
                #adiciona o nó($inffimValidade) em $inclusao
                $novaValidade->appendChild($inffimValidadeNova);
            }
            $evento->appendChild($novaValidade);
        }

        #adiciona os nós (informacaoes do empregador) em ideEmpregador
        $ideEmpregador->appendChild($inftpInscricao);
        $ideEmpregador->appendChild($infnrInscricao);
        $infoEmpregador->appendChild($evento);

        #adiciona os nós ($ideEmpregador, $infoEmpregador) em evtInfoEmpregador
        $xmlCabecalho->appendChild($ideEmpregador);
        $xmlCabecalho->appendChild($infoEmpregador);
        // *** FINAL DADOS EMPREGADOR *** //  

        $dom->appendChild($eSocial);

        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
        # Para salvar o arquivo, descomente a linha
//        $dom->save("./e_social/s1000.xml");
//       return "s1000.xml";
        # imprime o xml na tela
//        echo "<pre>";
//        echo htmlentities($xml);
//        echo "</pre>";
    }

//TABELA RUBRICAS
    public function montas1010($arquivo, $empregador, $movimento, $incidencia) {
        $tpInscricao = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricao = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));
//        $dt_hs_atual = str_replace(" ", "", RemoveCaracteres(date("Y-m-d H:i:s", time())));
//        $sequencial = sprintf("%05s", $this->sequencial);
//
//        $id = sprintf("%-36s", "ID" . $tpInscricao . $nrInscricao . $dt_hs_atual . $sequencial);
//        $versao = sprintf("%-11s", "1.2 - beta2"); // VERSÃO DO LAYOUT
//        $tpAmb = sprintf("%01s", 1); // CONSTANTE
//        $procEmi = sprintf("%01s", 1); // CONSTANTE
//        $indSeguimento = sprintf("%01s", 1); // CONSTANTE
//        $verProc = sprintf("%-20s", "Sei lah que versao ah essa"); // FALTA SABER VERSÃO DO App EMISSOR DO EVENTO

        $codRubrica = sprintf("%-30s", $movimento["cod"]);
//        if (!empty($movimento["data_ini"]) && $movimento["data_ini"] != "0000-00-00") {
//            $data_ini = sprintf("%-7s", substr($movimento["data_ini"], 0, 7));
//        }
//        if (!empty($movimento["data_fim"]) && $movimento["data_fim"] != "0000-00-00") {
//            $data_fim = sprintf("%-7s", substr($movimento["data_fim"], 0, 7));
//        }
        $descRubrica = sprintf("%-100s", RemoveEspacos(RemoveAcentos($movimento["descicao"])));
        $natRubrica = sprintf("%04s", $movimento["cod_rubrica"]);
        $indProvDesc = sprintf("%-01s", $movimento["indProvDesc"]);
        $repDSR = sprintf("%-01s", $movimento["repDSR"]);
        $repDecTerceiro = sprintf("%-01s", $movimento["repDecTerceiro"]);
        $repFerias = sprintf("%-01s", $movimento["repFerias"]);
        $repRescisao = sprintf("%-01s", $movimento["repRescisao"]);

        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        #cria os elementos e inclui os atributos
        $eSocial = $dom->createElement("esocial");
//aki
        $xmlCabecalho = $this->montaCabecalho($empregador, $dom, "evtTabRubrica");
        //var_dump($xmlCabecalho);exit;
        $eSocial->appendChild($xmlCabecalho);
//        $evtTabRubrica->setAttribute("Id", $id);
//        $evtTabRubrica->setAttribute("versao", $versao);
//
//        $ideEvento = $dom->createElement("ideEvento");
//
//        $inftpAmb = $dom->createElement("tpAmb", $tpAmb);
//        $infprocEmi = $dom->createElement("procEmi", $procEmi);
//        $infindSeguimento = $dom->createElement("indSeguimento", $indSeguimento);
//        $infverProc = $dom->createElement("verProc", $verProc);
        #adiciona os nós (informacaoes do evento) em ideEvento
//        $ideEvento->appendChild($inftpAmb);
//        $ideEvento->appendChild($infprocEmi);
//        $ideEvento->appendChild($infindSeguimento);
//        $ideEvento->appendChild($infverProc);

        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricao = $dom->createElement("tpInscricao", $tpInscricao);
        $infnrInscricao = $dom->createElement("nrInscricao", $nrInscricao);

        #adiciona os nós ($inftpInscricao,$infnrInscricao) em $ideEmpregador
        $ideEmpregador->appendChild($inftpInscricao);
        $ideEmpregador->appendChild($infnrInscricao);

        $infoRubrica = $dom->createElement("infoRubrica");
        $evento = $dom->createElement($this->tpevento);
        $ideRubrica = $dom->createElement("ideRubrica");
        $infcodRubrica = $dom->createElement("codRubrica", $codRubrica);

//        if (!empty($data_ini)) {
//            $infiniValidade = $dom->createElement("iniValidade", $data_ini);
//            #adiciona o nó($infiniValidade) em $inclusao
//            $evento->appendChild($infiniValidade);
//        }
//        if (!empty($data_fim)) {
//            $inffimValidade = $dom->createElement("fimValidade", $data_fim);
//            #adiciona o nó($inffimValidade) em $inclusao
//            $evento->appendChild($inffimValidade);
//        }
        #adiciona os nós ($ideRubrica,$infcodRubrica) em $inclusao
        $evento->appendChild($ideRubrica);
        $ideRubrica->appendChild($infcodRubrica);
        if (!empty($this->iniValidade)) {
            $iniValidade = $dom->createElement("iniValidade", $this->limpaData($this->iniValidade));
            #adiciona o nó($iniValidade) em idPeriodo
            $ideRubrica->appendChild($iniValidade);
        }
        if (!empty($this->fimValidade)) {
            $fimValidade = $dom->createElement("fimValidade", $this->limpaData($this->fimValidade));
            #adiciona o nó($fimValidade) em idPeriodo
            $ideRubrica->appendChild($fimValidade);
        }

        if ($this->tpevento != "exclusao") {
            $dadosRubrica = $dom->createElement("dadosRubrica");
            $infdescRubrica = $dom->createElement("descRubrica", $descRubrica);
            $infnatRubrica = $dom->createElement("natRubrica", $natRubrica);
            $infindProvDesc = $dom->createElement("indProvDesc", $indProvDesc);


            #adiciona os nós ($infdescRubrica,$infnatRubrica,$infindProvDesc) em $dadosRubrica
            $dadosRubrica->appendChild($infdescRubrica);
            $dadosRubrica->appendChild($infnatRubrica);
            $dadosRubrica->appendChild($infindProvDesc);

            foreach ($incidencia as $idMov => $arrayTipo) {
                foreach ($arrayTipo as $tipo => $arrayCodIncid) {
                    foreach ($arrayCodIncid as $codIncid) {
                        //REGRA_TABRUBRICA_COMPAT_CODINDCIDCP_INDPROVDESC
                        if ($tipo != 3) {
                            if (substr($codIncid, 0, 1) == 3 && $indProvDesc != "D") {
                                print_r("Erro de classificação tributária ou no indicativo do tipo da rubrica " . $codRubrica . " - " . $descRubrica);
                                exit;
                            }
                        }

                        if ($codIncid == 51 && $indProvDesc != "P") {
                            print_r("Erro de classificação do indicativo do tipo da rubrica " . $codRubrica . " - " . $descRubrica);
                            exit;
                        }

                        #adiciona os nós ($infcodIncidCP,$infcodIncidIRRF,$infcodIncidFGTS, $infcodIncidSIND)em $dadosRubrica
                        switch ($tipo) {
                            case 0:
                                $codIncidCP = sprintf("%02s", $codIncid);
                                $infcodIncidCP = $dom->createElement("codIncidCP", $codIncidCP);
                                $dadosRubrica->appendChild($infcodIncidCP);
                                break;

                            case 1:
                                $codIncidIRRF = sprintf("%02s", $codIncid);
                                $infcodIncidIRRF = $dom->createElement("codIncidIRRF", $codIncidIRRF);
                                $dadosRubrica->appendChild($infcodIncidIRRF);
                                break;

                            case 2:
                                $codIncidFGTS = sprintf("%02s", $codIncid);
                                $infcodIncidFGTS = $dom->createElement("codIncidFGTS", $codIncidFGTS);
                                $dadosRubrica->appendChild($infcodIncidFGTS);
                                break;

                            case 3:
                                $codIncidSIND = sprintf("%02s", $codIncid);
                                $infcodIncidSIND = $dom->createElement("codIncidSIND", $codIncidSIND);
                                $dadosRubrica->appendChild($infcodIncidSIND);
                                break;
                        }
                    }
                }
            }

            #adiciona os nós ($infrepDSR,$infrepDecTerceiro,$infrepFerias,$infrepRescisao,$inffatorRubrica) em $dadosRubrica
            $infrepDSR = $dom->createElement("repDSR", $repDSR);
            $infrepDecTerceiro = $dom->createElement("repDecTerceiro", $repDecTerceiro);
            $infrepFerias = $dom->createElement("repFerias", $repFerias);
            $infrepRescisao = $dom->createElement("repRescisao", $repRescisao);

            #adiciona os nó ($infdescRubrica,$infnatRubrica,$infindProvDesc,$infcodIncidCP,$infcodIncidIRRF,$infcodIncidFGTS,
            #$infcodIncidSIND,$infrepDSR,$infrepDecTerceiro,$infrepFerias,$infrepRescisao,$inffatorRubrica) em $dadosRubrica        
            $dadosRubrica->appendChild($infrepDSR);
            $dadosRubrica->appendChild($infrepDecTerceiro);
            $dadosRubrica->appendChild($infrepFerias);
            $dadosRubrica->appendChild($infrepRescisao);
            if (!empty($movimento["fator"])) {
                $fatorRubrica = sprintf("%05s", $movimento["fator"]);
                $inffatorRubrica = $dom->createElement("fatorRubrica", $fatorRubrica);
                $dadosRubrica->appendChild($inffatorRubrica);
            }

            //Manual de Orientação do eSocial V. 1.2 - beta 2
            //Linha 32 a 44 foram omitidas

            if ($this->tpevento == "alteracao") {
                $novaValidade = $dom->createElement("novaValidade");
                if (!empty($this->iniValidadeN)) {
                    $infiniValidadeNova = $dom->createElement("iniValidade", $this->limpaData($this->iniValidadeN));
                    #adiciona o nó($infiniValidade) em $inclusao
                    $novaValidade->appendChild($infiniValidadeNova);
                }
                if (!empty($this->fimValidadeN)) {
                    $inffimValidadeNova = $dom->createElement("fimValidade", $this->limpaData($this->fimValidadeN));
                    #adiciona o nó($inffimValidade) em $inclusao
                    $novaValidade->appendChild($inffimValidadeNova);
                }
                $evento->appendChild($novaValidade);
            }
            #adiciona os nós ($dadosRubrica) em $inclusao
            $evento->appendChild($dadosRubrica);
        }

        #adiciona o nó ($inclusao) em $infoRubrica
        $infoRubrica->appendChild($evento);

        #adiciona os nós ($infid,$infversao,$ideEvento,$ideEmpregador) em $evtTabRubrica
//        $evtTabRubrica->appendChild($ideEvento);
        $xmlCabecalho->appendChild($ideEmpregador);
        $xmlCabecalho->appendChild($infoRubrica);


        $dom->appendChild($eSocial);
        $this->sequencial++;

        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

//TABELA LOTAÇÕES
    public function montas1020($arquivo, $empregador, $empresa) {
        $tpInscricao = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricao = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));

        $codLotacao = sprintf("%-30s", $empresa["id_projeto"]);
//        if (!empty($empresa["inicio"]) && $empresa["inicio"] != "0000-00-00") {
//            $data_ini = sprintf("%-7s", substr($empresa["inicio"], 0, 7));
//        }
//        if (!empty($empresa["termino"]) && $empresa["termino"] != "0000-00-00") {
//            $data_fim = sprintf("%-7s", substr($empresa["termino"], 0, 7));
//        }

        $tpLotacao = sprintf("%02s", $empresa["tpLotacao"]);
        if ($tpLotacao == 1 || ($tpLotacao > 3 && $tpLotacao < 7) || ($tpLotacao > 7 && $tpLotacao < 10) || $tpLotacao == 11) {
            $tpInscEstab = sprintf("%01s", 1);
        } elseif (($tpLotacao > 1 && $tpLotacao < 4) || $tpLotacao = 22) {
            $tpInscEstab = sprintf("%01s", 4);
        } elseif ($tpLotacao == 23) {
            $tpInscEstab = sprintf("%01s", 3);
        } else {
            $tpInscEstab = NULL;
        }
        $nrInscEstab = sprintf("%015s", RemoveEspacos(RemoveCaracteres($empresa["cnpj"])));
        $tpLogradouro = sprintf("%-3s", RemoveEspacos($empresa["tpLogradouro"]));
        $descLogradouro = sprintf("%-80s", RemoveEspacos(RemoveAcentos($empresa["logradouro"])));
        $nrLogradouro = $empresa["numero"];
        if (empty($nrLogradouro)) {
            $nrLogradouro = sprintf("%-10s", "SN");
        } else {
            $nrLogradouro = sprintf("%-10s", RemoveEspacos($nrLogradouro));
        }
        $complemento = $empresa["complemento"];
        $bairro = sprintf("%-60s", RemoveEspacos(RemoveAcentos($empresa["bairro"])));
        $cep = sprintf("%08s", RemoveEspacos(RemoveCaracteres($empresa["cep"])));
        $codMunicipio = sprintf("%07s", RemoveEspacos(RemoveCaracteres($empresa["cod_municipio"])));
        $uf = sprintf("%-2s", RemoveEspacos($empresa["uf"]));
        $fpas = sprintf("%03s", RemoveEspacos(RemoveCaracteres($empresa["fpas"])));
        $codTerceiros = sprintf("%04s", RemoveEspacos($empresa["terceiros"]));

        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        #cria os elementos e inclui os atributos
        $eSocial = $dom->createElement("esocial");

        $xmlCabecalho = $this->montaCabecalho($empregador, $dom, "evtTabLotacao");
        $eSocial->appendChild($xmlCabecalho);

        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricao = $dom->createElement("tpInscricao", $tpInscricao);
        $infnrInscricao = $dom->createElement("nrInscricao", $nrInscricao);

        #adiciona os nós ($inftpInscricao,$infnrInscricao) em $ideEmpregador
        $ideEmpregador->appendChild($inftpInscricao);
        $ideEmpregador->appendChild($infnrInscricao);

        $infoLotacao = $dom->createElement("infoLotacao");
        $evento = $dom->createElement($this->tpevento);

        $infoLotacao->appendChild($evento);

        $ideLotacao = $dom->createElement("ideLotacao");
        $infcodLotacao = $dom->createElement("codLotacao", $codLotacao);


        $ideLotacao->appendChild($infcodLotacao);
//        if (!empty($data_ini)) {
//            $infiniValidade = $dom->createElement("iniValidade", $data_ini);
//            #adiciona o nó($infiniValidade) em $inclusao
//            $ideLotacao->appendChild($infiniValidade);
//        }
//        if (!empty($data_fim)) {
//            $inffimValidade = $dom->createElement("fimValidade", $data_fim);
//            #adiciona o nó($inffimValidade) em $inclusao
//            $ideLotacao->appendChild($inffimValidade);
//        }
        if (!empty($this->iniValidade)) {
            $iniValidade = $dom->createElement("iniValidade", $this->limpaData($this->iniValidade));
            #adiciona o nó($iniValidade) em idPeriodo
            $ideLotacao->appendChild($iniValidade);
        }
        if (!empty($this->fimValidade)) {
            $fimValidade = $dom->createElement("fimValidade", $this->limpaData($this->fimValidade));
            #adiciona o nó($fimValidade) em idPeriodo
            $ideLotacao->appendChild($fimValidade);
        }

        $evento->appendChild($ideLotacao);

        if ($this->tpevento != "exclusao") {
            $dadosLotacao = $dom->createElement("dadosLotacao");
            if ($tpLotacao != 07 && $tpLotacao != 08 && $tpLotacao != 09 && $tpLotacao != 11 && $tpLotacao != 22 && $tpLotacao != 24) {
                if ($tpLotacao == 01) {
                    $descLotacao = sprintf("%-100s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($empresa["nomeSetor"]))));
                } elseif ($tpLotacao == 02 || $tpLotacao == 03 || $tpLotacao == 22) {
                    $descLotacao = sprintf("%-100s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($empresa["nomeSetorObra"]))));
                } elseif ($tpLotacao == 04 || $tpLotacao == 05 || $tpLotacao == 06) {
                    $descLotacao = sprintf("%-100s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($empresa["razao"]))) . "-" . RemoveEspacos(RemoveCaracteres(RemoveAcentos($empresa["nomeSetor"]))));
                } elseif ($tpLotacao == 10) {
                    $descLotacao = sprintf("%-100s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($empresa["nomeEmbarcacao"]))));
                } elseif ($tpLotacao == 21) {
                    $descLotacao = sprintf("%-100s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($empresa["nome"]))));
                } elseif ($tpLotacao == 23) {
                    $descLotacao = sprintf("%-100s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($empresa["nomeEstRural"]))) . "-" . RemoveEspacos(RemoveCaracteres(RemoveAcentos($empresa["nomeResponsavel"]))));
                } elseif ($tpLotacao == 90) {
                    $descLotacao = sprintf("%-100s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($empresa["nomeLotForaPais"]))));
                }
                $infdescLotacao = $dom->createElement("descLotacao", $descLotacao);
                $dadosLotacao->appendChild($infdescLotacao);
            }

            $inftpLotacao = $dom->createElement("tpLotacao", $tpLotacao);
            $dadosLotacao->appendChild($inftpLotacao);

            if (!empty($tpInscEstab)) {
                $inftpInscEstab = $dom->createElement("tpInscEstab", $tpInscEstab);
                $dadosLotacao->appendChild($inftpInscEstab);
            }
            if ($tpLotacao != 07 && $tpLotacao != 10 && $tpLotacao != 24 && $tpLotacao != 90) {
                if ($tpLotacao == 01 || $tpLotacao == 11) {
                    $nrInscEstab = sprintf("%015s", RemoveEspacos(RemoveCaracteres($empresa["cnpj"])));
                } elseif ($tpLotacao == 02 || $tpLotacao == 03 || $tpLotacao == 22) {
                    $nrInscEstab = sprintf("%015s", RemoveEspacos(RemoveCaracteres($empresa["cno"])));
                } elseif (($tpLotacao > 03 && $tpLotacao < 07) || $tpLotacao == 09) {
                    $nrInscEstab = sprintf("%015s", RemoveEspacos(RemoveCaracteres($empresa["cnpjContratante"])));
                } elseif ($tpLotacao == 08) {
                    $nrInscEstab = sprintf("%015s", RemoveEspacos(RemoveCaracteres($empresa["cnpjOpPortuario"])));
                } elseif ($tpLotacao == 21 || $tpLotacao == 23) {
                    $nrInscEstab = sprintf("%015s", RemoveEspacos(RemoveCaracteres($empresa["CAEPF"])));
                }
                $infnrInscEstab = $dom->createElement("nrInscEstab", $nrInscEstab);
                $dadosLotacao->appendChild($infnrInscEstab);
            }


            $endereco = $dom->createElement("endereco");
            $inftpLogradouro = $dom->createElement("tpLogradouro", $tpLogradouro);
            $infdescLogradouro = $dom->createElement("descLogradouro", $descLogradouro);
            $infnrLogradouro = $dom->createElement("nrLogradouro", $nrLogradouro);
            if (!empty($complemento)) {
                $complemento = sprintf("%-30s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($complemento))));
                $infcomplemento = $dom->createElement("complemento", $complemento);
                $endereco->appendChild($infcomplemento);
            }
            $infbairro = $dom->createElement("bairro", $bairro);
            $infcep = $dom->createElement("cep", $cep);
            $infcodMunicipio = $dom->createElement("codMunicipio", $codMunicipio);
            $infuf = $dom->createElement("uf", $uf);

            $endereco->appendChild($inftpLogradouro);
            $endereco->appendChild($infdescLogradouro);
            $endereco->appendChild($infnrLogradouro);
            if (!empty($complemento)) {
                $endereco->appendChild($infcomplemento);
            }
            $endereco->appendChild($infbairro);
            $endereco->appendChild($infcep);
            $endereco->appendChild($infcodMunicipio);
            $endereco->appendChild($infuf);

            $dadosLotacao->appendChild($endereco);

            if ($tpLotacao == 09 || $tpLotacao == 11 || $tpLotacao == 90) {
                $fpasLotacao = $dom->createElement("fpasLotacao");
                $inffpas = $dom->createElement("faps", $fpas);
                $infcodTerceiros = $dom->createElement("codTerceiros", $codTerceiros);

                $fpasLotacao->appendChild($inffpas);
                $fpasLotacao->appendChild($infcodTerceiros);
                $dadosLotacao->appendChild($fpasLotacao);
            }

            if ($tpLotacao == 03) {
                $infoEmpParcial = $dom->createElement("infoEmpParcial");
                $inftpInscContratante = $dom->createElement("tpInscContratante", $tpInscContratante);
                $infnrInscContratante = $dom->createElement("nrInscContratante", $nrInscContratant);
                $inftpInscProprietario = $dom->createElement("tpInscContratante", $tpInscProprietario);
                $infnrInscProprietario = $dom->createElement("nrInscContratante", $nrInscProprietario);

                $infoEmpParcial->appendChild($inftpInscContratante);
                $infoEmpParcial->appendChild($infnrInscContratante);
                $infoEmpParcial->appendChild($inftpInscProprietario);
                $infoEmpParcial->appendChild($infnrInscProprietario);
                $dadosLotacao->appendChild($infoEmpParcial);
            }
            #adiciona os nós ($ideLotacao,$dadosLotacao) em $inclusao
            $evento->appendChild($dadosLotacao);

            if ($this->tpevento == "alteracao") {
                $novaValidade = $dom->createElement("novaValidade");
                if (!empty($this->iniValidadeN)) {
                    $infiniValidadeNova = $dom->createElement("iniValidade", $this->limpaData($this->iniValidadeN));
                    #adiciona o nó($infiniValidade) em $inclusao
                    $novaValidade->appendChild($infiniValidadeNova);
                }
                if (!empty($this->fimValidadeN)) {
                    $inffimValidadeNova = $dom->createElement("fimValidade", $this->limpaData($this->fimValidadeN));
                    #adiciona o nó($inffimValidade) em $inclusao
                    $novaValidade->appendChild($inffimValidadeNova);
                }
                $evento->appendChild($novaValidade);
            }
        }


        #adiciona os nós ($infid,$infversao,$ideEvento,$ideEmpregador) em $evtTabLotacao
        $xmlCabecalho->appendChild($ideEmpregador);
        $xmlCabecalho->appendChild($infoLotacao);


        $dom->appendChild($eSocial);
        $this->sequencial++;

        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

//TABELA CARGO
    public function montas1030($arquivo, $empregador, $cargo) {
        $tpInscricao = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricao = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));
//        $dt_hs_atual = str_replace(" ", "", RemoveCaracteres(date("Y-m-d H:i:s", time())));
//        $sequencial = sprintf("%05s", $this->sequencial);
//
//        $id = sprintf("%-36s", "ID" . $tpInscricao . $nrInscricao . $dt_hs_atual . $sequencial);
//        $versao = sprintf("%-11s", "1.2 - beta2"); // VERSÃO DO LAYOUT
//        $tpAmb = sprintf("%01s", 1); // CONSTANTE
//        $procEmi = sprintf("%01s", 1); // CONSTANTE
//        $indSeguimento = sprintf("%01s", 1); // CONSTANTE
//        $verProc = sprintf("%-20s", "Sei lah que versao ah essa"); // FALTA SABER VERSÃO DO App EMISSOR DO EVENTO 

        $codCargo = sprintf("%30s", RemoveEspacos($cargo['id_curso']));
        $descCargo = sprintf("%-100s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($cargo['nome']))));
//        if (!empty($cargo['inicio']) && $cargo['inicio'] != "0000-00-00") {
//            $data_ini = sprintf("%-07s", RemoveEspacos(substr($cargo['inicio'], 0, 7)));
//        }
//        if (!empty($cargo['termino']) && $cargo['termino'] != "0000-00-00") {
//            $data_fim = sprintf("%-07s", RemoveEspacos(substr($cargo['termino'], 0, 7)));
//        }
        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        #cria os elementos e inclui os atributos
        $eSocial = $dom->createElement("esocial");
        $xmlCabecalho = $this->montaCabecalho($empregador, $dom, "evtTabCargo");
        $eSocial->appendChild($xmlCabecalho);
//        $evtTabCargo->setAttribute("Id", $id);
//        $evtTabCargo->setAttribute("versao", $versao);
//
//        $ideEvento = $dom->createElement("ideEvento");
//
//        $inftpAmb = $dom->createElement("tpAmb", $tpAmb);
//        $infprocEmi = $dom->createElement("procEmi", $procEmi);
//        $infindSeguimento = $dom->createElement("indSeguimento", $indSeguimento);
//        $infverProc = $dom->createElement("verProc", $verProc);
        #adiciona os nós (informacaoes do evento) em ideEvento
//        $ideEvento->appendChild($inftpAmb);
//        $ideEvento->appendChild($infprocEmi);
//        $ideEvento->appendChild($infindSeguimento);
//        $ideEvento->appendChild($infverProc);

        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricao = $dom->createElement("tpInscricao", $tpInscricao);
        $infnrInscricao = $dom->createElement("nrInscricao", $nrInscricao);

        #adiciona os nós ($inftpInscricao,$infnrInscricao) em $ideEmpregador
        $ideEmpregador->appendChild($inftpInscricao);
        $ideEmpregador->appendChild($infnrInscricao);

        $infoCargo = $dom->createElement("infoCargo");
        $evento = $dom->createElement($this->tpevento);

        $infoCargo->appendChild($evento);

        $ideCargo = $dom->createElement("ideCargo");
        $infcodCargo = $dom->createElement("codCargo", $codCargo);


        $ideCargo->appendChild($infcodCargo);
//        if (!empty($data_ini)) {
//            $infiniValidade = $dom->createElement("iniValidade", $data_ini);
//            #adiciona o nó($infiniValidade) em $inclusao
//            $ideCargo->appendChild($infiniValidade);
//        }
//        if (!empty($data_fim)) {
//            $inffimValidade = $dom->createElement("fimValidade", $data_fim);
//            #adiciona o nó($inffimValidade) em $inclusao
//            $ideCargo->appendChild($inffimValidade);
//        }
        if (!empty($this->iniValidade)) {
            $iniValidade = $dom->createElement("iniValidade", $this->limpaData($this->iniValidade));
            #adiciona o nó($iniValidade) em idPeriodo
            $ideCargo->appendChild($iniValidade);
        }
        if (!empty($this->fimValidade)) {
            $fimValidade = $dom->createElement("fimValidade", $this->limpaData($this->fimValidade));
            #adiciona o nó($fimValidade) em idPeriodo
            $ideCargo->appendChild($fimValidade);
        }



        $evento->appendChild($ideCargo);
        if ($this->tpevento != "exclusao") {
            $dadosCargo = $dom->createElement("dadosCargo");
            $infdescCargo = $dom->createElement("descCargo", $descCargo);

            $dadosCargo->appendChild($infdescCargo);


            $evento->appendChild($dadosCargo);
        }

        #adiciona os nós ($infid,$infversao,$ideEvento,$ideEmpregador) em $evtTabLotacao
//        $evtTabCargo->appendChild($ideEvento);
        $xmlCabecalho->appendChild($ideEmpregador);
        $xmlCabecalho->appendChild($infoCargo);

        $dom->appendChild($eSocial);
        $this->sequencial++;

        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

//TABELA HORARIO
    public function montas1050($arquivo, $empregador, $horario) {
        $tpInscricao = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricao = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));

        $codHorContratual = sprintf("%-20s", $horario["id_curso"]);
//        if (!empty($horario["inicio"]) && $horario["inicio"] != "0000-00-00") {
//            $data_ini = sprintf("%-07s", RemoveEspacos(substr($horario["inicio"], 0, 7)));
//        }
//        if (!empty($horario["termino"]) && $horario["termino"] != "0000-00-00") {
//            $data_fim = sprintf("%-07s", RemoveEspacos(substr($horario["termino"], 0, 7)));
//        }
        $horaEntrada = $horario["horaEntrada"];
        $entradaEmMin = (substr($horaEntrada, 0, 2) * 60) + substr($horaEntrada, 3, 4);
        $horaSaida = $horario["horaSaida"];
        $saidaEmMin = (substr($horaSaida, 0, 2) * 60) + substr($horaSaida, 3, 4);
        $perHorFlexivel = 0; // É PERMITIDA A FLEXIBILIDADE DE HORARIO? O-NÃO 1-SIM

        $inicioIntervalo = $horario["inicioIntervalo"];
        $inicioEmMin = (substr($inicioIntervalo, 0, 2) * 60) + substr($inicioIntervalo, 3, 4);
        $terminoIntervalo = $horario["terminoIntervalo"];
        $terminoEmMin = (substr($terminoIntervalo, 0, 2) * 60) + substr($terminoIntervalo, 3, 4);
        $tpIntervalo = 1; //1-INTERVALO EM HORÁRIO FIXO  2-INTERVALO EM HORÁRIO VARIÁVEL
        $durIntervalo = $terminoEmMin - $inicioEmMin;
        $durJornada = $saidaEmMin - $entradaEmMin - $durIntervalo;

        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        #cria os elementos e inclui os atributos
        $eSocial = $dom->createElement("esocial");
        $xmlCabecalho = $this->montaCabecalho($empregador, $dom, "evtTabHorContratual");
        $eSocial->appendChild($xmlCabecalho);

        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricao = $dom->createElement("tpInscricao", $tpInscricao);
        $infnrInscricao = $dom->createElement("nrInscricao", $nrInscricao);

        $infoHorContratual = $dom->createElement("infoHorContratual");
        $evento = $dom->createElement($this->tpevento);

        $ideHorContratual = $dom->createElement("ideHorContratual");
        $infcodHorContratual = $dom->createElement("codHorContratual", $codHorContratual);

        $ideHorContratual->appendChild($infcodHorContratual);

//        if (!empty($horario["inicio"]) && $horario["inicio"] != "0000-00-00") {
//            $iniValidade = $dom->createElement("iniValidade", $data_ini);
//            $ideHorContratual->appendChild($iniValidade);
//        }
//        if (!empty($horario["termino"]) && $horario["termino"] != "0000-00-00") {
//            $fimValidade = $dom->createElement("fimValidade", $data_fim);
//            $ideHorContratual->appendChild($fimValidade);
//        }

        if (!empty($this->iniValidade)) {
            $iniValidade = $dom->createElement("iniValidade", $this->limpaData($this->iniValidade));
            #adiciona o nó($iniValidade) em idPeriodo
            $ideHorContratual->appendChild($iniValidade);
        }
        if (!empty($this->fimValidade)) {
            $fimValidade = $dom->createElement("fimValidade", $this->limpaData($this->fimValidade));
            #adiciona o nó($fimValidade) em idPeriodo
            $ideHorContratual->appendChild($fimValidade);
        }

        $evento->appendChild($ideHorContratual);
        if ($this->tpevento != "exclusao") {
            $dadosHorContratual = $dom->createElement("dadosHorContratual");
//            if ($this->validaHora($horaEntrada)) {
            $infhoraEntrada = $dom->createElement("horaEntrada", sprintf("%04s", $horaEntrada));
            $dadosHorContratual->appendChild($infhoraEntrada);
//            }
//            if ($this->validaHora($horaSaida)) {
            $infhoraSaida = $dom->createElement("horaSaida", sprintf("%04s", $horaSaida));
            $dadosHorContratual->appendChild($infhoraSaida);
//            }
            $infdurJornada = $dom->createElement("durJornada", $durJornada);
            $infperHorFlexivel = $dom->createElement("perHorFlexivel", $perHorFlexivel);


            $horarioIntervalo = $dom->createElement("horarioIntervalo");
            $inftpIntervalo = $dom->createElement("tpIntervalo", $tpIntervalo);
            $infdurIntervalo = $dom->createElement("durIntervalo", $durIntervalo);
            $horarioIntervalo->appendChild($inftpIntervalo);
            $horarioIntervalo->appendChild($infdurIntervalo);
//            if ($this->validaHora($inicioIntervalo)) {
            $infinicioIntervalo = $dom->createElement("inicioIntervalo", sprintf("%04s", $inicioIntervalo));
            $horarioIntervalo->appendChild($infinicioIntervalo);
//            }
//            if ($this->validaHora($terminoIntervalo)) {
            $infterminoIntervalo = $dom->createElement("terminoIntervalo", sprintf("%04s", $terminoIntervalo));
            $horarioIntervalo->appendChild($infterminoIntervalo);
//            }

            if ($this->tpevento == "alteracao") {
                $novaValidade = $dom->createElement("novaValidade");
                if (!empty($this->iniValidadeN)) {
                    $infiniValidadeNova = $dom->createElement("iniValidade", $this->limpaData($this->iniValidade));
                    #adiciona o nó($infiniValidade) em $inclusao
                    $novaValidade->appendChild($infiniValidadeNova);
                }
                if (!empty($this->fimValidadeN)) {
                    $inffimValidadeNova = $dom->createElement("fimValidade", $this->limpaData($this->fimValidade));
                    #adiciona o nó($inffimValidade) em $inclusao
                    $novaValidade->appendChild($inffimValidadeNova);
                }
                $evento->appendChild($novaValidade);
            }

            $dadosHorContratual->appendChild($infdurJornada);
            $dadosHorContratual->appendChild($infperHorFlexivel);
            $dadosHorContratual->appendChild($horarioIntervalo);

            $evento->appendChild($dadosHorContratual);
        }

        $infoHorContratual->appendChild($evento);

        $ideEmpregador->appendChild($inftpInscricao);
        $ideEmpregador->appendChild($infnrInscricao);

        $xmlCabecalho->appendChild($ideEmpregador);
        $xmlCabecalho->appendChild($infoHorContratual);

        $dom->appendChild($eSocial);
        $this->sequencial++;

        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

//TABELA ESTABELECIMENTOS         
    public function montas1060($arquivo, $empregador, $estab, $aliquota, $percentNovo) {
        $tpInscricao = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricao = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));
        $tpInscricaoEstab = sprintf("%01s", $estab["tpInscricao"]);
        $nrInscricaoEstab = sprintf("%015s", RemoveEspacos(RemoveCaracteres($estab["nrInscricao"])));
        $fpas = sprintf("%03s", RemoveEspacos(RemoveCaracteres($estab["fpas"])));
        $codTerceiros = sprintf("%04s", RemoveEspacos(RemoveCaracteres($estab["terceiros"])));
        $cnaePreponderante = sprintf("%07s", RemoveEspacos(RemoveCaracteres($estab["cnae"])));

        $aliqRat = sprintf("%01s", RemoveEspacos($estab["aliquotaRat"]));
        $fapNovo = ((int) RemoveEspacos($estab["fap"]) / 100);
        $fap = sprintf("%06s", $fapNovo);
        $aliqRatAjustada = $aliqRat * $fapNovo;
        $aliqRatAjustada = sprintf("%06s", $aliqRatAjustada);

        if ($aliqRat != $aliquota) {
            $tpProcessoRat = sprintf("%01s", RemoveEspacos($empregador["tpProcessoRat"]));
            $nrProcessoRat = sprintf("%-20s", RemoveEspacos($empregador["nrProcessoRat"]));
        }

        if ($fapNovo != $percentNovo) {
            $tpProcessoFap = sprintf("%01s", RemoveEspacos($empregador["tpProcessoFap"]));
            $nrProcessoFap = sprintf("%-20s", RemoveEspacos($empregador["nrProcessoFap"]));
        }

        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        #cria os elementos e inclui os atributos
        $eSocial = $dom->createElement("esocial");
        $xmlCabecalho = $this->montaCabecalho($empregador, $dom, "evtTabEstab");
        $eSocial->appendChild($xmlCabecalho);
        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricao = $dom->createElement("tpInscricao", $tpInscricao);
        $infnrInscricao = $dom->createElement("nrInscricao", $nrInscricao);

        $infoEstab = $dom->createElement("infoEstab");

        $evento = $dom->createElement($this->tpevento);
        $ideEstab = $dom->createElement("ideEstab");
        $inftpInscricaoEstab = $dom->createElement("tpInscricao", $tpInscricaoEstab);
        $infnrInscricaoEstab = $dom->createElement("nrInscricao", $nrInscricaoEstab);

        $ideEstab->appendChild($inftpInscricaoEstab);
        $ideEstab->appendChild($infnrInscricaoEstab);
        if (!empty($this->iniValidade)) {
            $iniValidade = $dom->createElement("iniValidade", $this->limpaData($this->iniValidade));
            #adiciona o nó($iniValidade) em idPeriodo
            $ideEstab->appendChild($iniValidade);
        }

        if (!empty($this->fimValidade)) {
            $fimValidade = $dom->createElement("fimValidade", $this->limpaData($this->fimValidade));
            #adiciona o nó($fimValidade) em idPeriodo
            $ideEstab->appendChild($fimValidade);
        }

        $evento->appendChild($ideEstab);

        if ($this->tpevento != "excluir") {
            $dadosEstab = $dom->createElement("dadosEstab");
            $inffpas = $dom->createElement("fpas", $fpas);
            $infcodTerceiros = $dom->createElement("codTerceiros", $codTerceiros);
            $infcnaePreponderante = $dom->createElement("cnaePreponderante", $cnaePreponderante);

            $aliqGilrat = $dom->createElement("aliqGilRat");
            $infaliqRat = $dom->createElement("aliqRat", $aliqRat);
            $inffap = $dom->createElement("fap", $fap);
            $infaliqRatAjustada = $dom->createElement("aliqRatAjustada", $aliqRatAjustada);

            #adiciona os nós($infaliqRat, $inffap, $infaliqRatAjustada) em $aliqGilrat
            $aliqGilrat->appendChild($infaliqRat);
            $aliqGilrat->appendChild($inffap);
            $aliqGilrat->appendChild($infaliqRatAjustada);


            if ($aliqRat != $aliquota) { // Se a aliquota que está na tabela do master for diferente da tabela RAT fornecida pela RF 
                $procAdmJudRat = $dom->createElement("procAdmJudRat");
                $inftpProcessoRat = $dom->createElement("tpProcesso", $tpProcessoRat);
                $infnrProcessoRat = $dom->createElement("nrProcesso", $nrProcessoRat);

                #adiciona o nó($procAdmJudRat) em $aliqGilrat
                $aliqGilrat->appendChild($procAdmJudRat);

                #adiciona os nós($inftpProcesso,$infnrProcesso) em $procAdmJudRat
                $procAdmJudRat->appendChild($inftpProcessoRat);
                $procAdmJudRat->appendChild($infnrProcessoRat);
            }

            if ($fapNovo != $percentNovo) { // Se o percentual que está na tabela do master for diferente da tabela FAP fornecida pela RF
                $procAdmJudRat = $dom->createElement("procAdmJudFap");
                $inftpProcessoFap = $dom->createElement("tpProcesso", $tpProcessoFap);
                $infnrProcessoFap = $dom->createElement("nrProcesso", $nrProcessoFap);

                #adiciona o nó($procAdmJudFap) em $aliqGilrat
                $aliqGilrat->appendChild($procAdmJudRat);

                #adiciona os nós($inftpProcesso,$infnrProcesso) em $procAdmJudFap
                $procAdmJudRat->appendChild($inftpProcessoFap);
                $procAdmJudRat->appendChild($infnrProcessoFap);
            }

            $dadosEstab->appendChild($inffpas);
            $dadosEstab->appendChild($infcodTerceiros);
            $dadosEstab->appendChild($infcnaePreponderante);
            $dadosEstab->appendChild($aliqGilrat);

            if ($estab["indConstrutora"] == 1 && $estab["indDesFolha"] == 2 && $estab["tpInscricao"] == 4) {
                $indSubstPatronalObra = sprintf("%01s", $estab["indSubstPatronalObra"]);
                $infObra = $dom->createElement("infObra");
                $infindSubstPatronalObra = $dom->createElement("indSubstPatronalObra", $indSubstPatronalObra);

                $infObra->appendChild($infindSubstPatronalObra);

                $dadosEstab->appendChild($infObra);
            }

            if (!empty($estab["procJudTerceiro"])) { // Se existir processos judiciais com sentença favorável ao contribuinte relativo às contribuições destinadas a outras entidades e fundos
                $nrProcJud = $estab["nrProcJud"];
                $infoProcJudTerceiro = $dom->createElement("infoProcJudTerceiro");
                $infprocTerceiro = $dom->createElement("procTerceiro");
                $infcodTerceiros = $dom->createElement("codTerceiros", $codTerceiros);
                $infnrProcJud = $dom->createElement("nrProcJud", $nrProcJud);

                $infprocTerceiro->appendChild($infcodTerceiros);
                $infprocTerceiro->appendChild($infnrProcJud);

                $infoProcJudTerceiro->appendChild($infprocTerceiro);

                $dadosEstab->appendChild($infoProcJudTerceiro);
            }

            if ($this->tpevento == "alteracao") {
                $novaValidade = $dom->createElement("novaValidade");
                if (!empty($this->iniValidadeN)) {
                    $infiniValidadeNova = $dom->createElement("iniValidade", $this->limpaData($this->iniValidade));
                    #adiciona o nó($infiniValidade) em $inclusao
                    $novaValidade->appendChild($infiniValidadeNova);
                }
                if (!empty($this->fimValidadeN)) {
                    $inffimValidadeNova = $dom->createElement("fimValidade", $this->limpaData($this->fimValidade));
                    #adiciona o nó($inffimValidade) em $inclusao
                    $novaValidade->appendChild($inffimValidadeNova);
                }
                $evento->appendChild($novaValidade);
            }

            $evento->appendChild($dadosEstab);
        }

        #adiciona os nós (informacaoes do empregador) em ideEmpregador
        $ideEmpregador->appendChild($inftpInscricao);
        $ideEmpregador->appendChild($infnrInscricao);
        $infoEstab->appendChild($evento);

        #adiciona os nós ($ideEmpregador, $infoEmpregador) em evtInfoEmpregador
        $xmlCabecalho->appendChild($ideEmpregador);
        $xmlCabecalho->appendChild($infoEstab);
        // *** FINAL DADOS EMPREGADOR *** //  

        $dom->appendChild($eSocial);
        $this->sequencial++;

        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

//TABELA PROCESSO
    public function montas1070($arquivo, $empregador, $processo) {
        $tpInscricao = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricao = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));

        $tpProcesso = sprintf("%01s", $processo["tpProcesso"]);
        $nrProcesso = sprintf("%-20s", $processo["nrProcesso"]);
//        $indDecisao = sprintf("%-02s", $processo["indDecisao"]);
//        $dtDecisao = sprintf("%10s", $processo["dtDescisao"]);
//        $indDeposito = sprintf("%01s", $processo["indDecisao"]);
        $ufVara = sprintf("%-2s", RemoveEspacos(RemoveCaracteres($processo["proc_vara_uf"])));
        $codMunicipio = sprintf("%07s", RemoveEspacos(RemoveCaracteres($processo["cod_municipio"])));
//        $idVara = sprintf("%02s", $processo["idVara"]);
        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        #cria os elementos e inclui os atributos
        $eSocial = $dom->createElement("esocial");
        $xmlCabecalho = $this->montaCabecalho($empregador, $dom, "evtTabProcesso");
        $eSocial->appendChild($xmlCabecalho);
        // *** INÍCIO DADOS EMPREGADOR *** //
        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricao = $dom->createElement("tpInscricao", $tpInscricao);
        $infnrInscricao = $dom->createElement("nrInscricao", $nrInscricao);

        $infoProcesso = $dom->createElement("infoProcesso");

        $evento = $dom->createElement($this->tpevento);
        $ideProcesso = $dom->createElement("ideProcesso");
        $inftpProcesso = $dom->createElement("tpProcesso", $tpProcesso);
        $infnrProcesso = $dom->createElement("nrProcesso", $nrProcesso);

        $ideProcesso->appendChild($inftpProcesso);
        $ideProcesso->appendChild($infnrProcesso);
        if (!empty($this->iniValidade)) {
            $iniValidade = $dom->createElement("iniValidade", $this->limpaData($this->iniValidade));
            #adiciona o nó($iniValidade) em idPeriodo
            $ideProcesso->appendChild($iniValidade);
        }
        if (!empty($this->fimValidade)) {
            $fimValidade = $dom->createElement("fimValidade", $this->limpaData($this->fimValidade));
            #adiciona o nó($fimValidade) em idPeriodo
            $ideProcesso->appendChild($fimValidade);
        }

        $evento->appendChild($ideProcesso);

        if ($this->tpevento != "excluir") {
            $dadosProcesso = $dom->createElement("dadosProcesso");
            $infindDecisao = $dom->createElement("indDecisao", $indDecisao);
            $infdtDecisao = $dom->createElement("dtDecisao", $dtDecisao);
            $infindDeposito = $dom->createElement("indDeposito", $indDeposito);

            $dadosProcJud = $dom->createElement("dadosProcJud");
            $infufVara = $dom->createElement("ufVara", $ufVara);
            $infcodMunicipio = $dom->createElement("codMunicipio", $codMunicipio);
            $infidVara = $dom->createElement("idVara", $idVara);
            $infindAutoria = $dom->createElement("indAutoria", $indAutoria);


            if ($this->tpevento == "alteracao") {
                $novaValidade = $dom->createElement("novaValidade");
                if (!empty($this->iniValidadeN)) {
                    $infiniValidadeNova = $dom->createElement("iniValidade", $this->limpaData($this->iniValidade));
                    #adiciona o nó($infiniValidade) em $inclusao
                    $novaValidade->appendChild($infiniValidadeNova);
                }
                if (!empty($this->fimValidadeN)) {
                    $inffimValidadeNova = $dom->createElement("fimValidade", $this->limpaData($this->fimValidade));
                    #adiciona o nó($inffimValidade) em $inclusao
                    $novaValidade->appendChild($inffimValidadeNova);
                }
                $evento->appendChild($novaValidade);
            }


            $dadosProcJud->appendChild($infufVara);
            $dadosProcJud->appendChild($infcodMunicipio);
            $dadosProcJud->appendChild($infidVara);
            $dadosProcJud->appendChild($infindAutoria);

            $evento->appendChild($dadosProcesso);

            $dadosProcesso->appendChild($infindDecisao);
            $dadosProcesso->appendChild($infdtDecisao);
            $dadosProcesso->appendChild($infindDeposito);
            $dadosProcesso->appendChild($dadosProcJud);
        }

        #adiciona os nós (informacaoes do empregador) em ideEmpregador
        $ideEmpregador->appendChild($inftpInscricao);
        $ideEmpregador->appendChild($infnrInscricao);
        $infoProcesso->appendChild($evento);

        #adiciona os nós ($ideEmpregador, $infoEmpregador) em evtInfoEmpregador
        $xmlCabecalho->appendChild($ideEmpregador);
        $xmlCabecalho->appendChild($infoProcesso);
        // *** FINAL DADOS EMPREGADOR *** //  

        $dom->appendChild($eSocial);
        $this->sequencial++;

        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

//EVENTOS PERIÓDICOS - ABERTURA
    public function montas1100($arquivo, $empregador, $trabalhador, $nrRecibo) {
        if ($this->tpevento == "inclusao") {
            $indRetificacao = 1;
        } elseif ($this->tpevento == "alteracao") {
            $indRetificacao = 2;
            $nrRecibo = sprintf("%15s", RemoveEspacos($nrRecibo));
        }
        $indRetificacao = sprintf("%01s", $indRetificacao); //1 - ARQUIVO ORIGINAL 2 - ARQUIVO DE RETIFICAÇÃO
        if (RemoveEspacos($trabalhador["terceiro"]) == 1) {
            $indApuracao = sprintf("%01s", 2); // ANUAL(FOLHA DO 13°)
            $perApuracao = sprintf("%-7s", $this->ano);
        } else {
            $indApuracao = sprintf("%01s", 1); // MENSAL
            $perApuracao = sprintf("%-7s", $this->ano . "-" . $this->mes);
        }

        $tpInscricao = sprintf("%01s", $trabalhador["tpInscricao"]);
        $nrInscricao = sprintf("%014s", RemoveEspacos(RemoveCaracteres($trabalhador["nrInscricao"])));
        $nomeResponsavel = sprintf("%-60s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador["responsavel"]))));
        $cpfResponsavel = sprintf("%11s", RemoveEspacos(RemoveCaracteres($trabalhador["cpf"])));
        $telefone = sprintf("%-13s", RemoveEspacos(RemoveCaracteres($trabalhador["tel"])));
        $fax = sprintf("%-13s", RemoveEspacos(RemoveCaracteres($trabalhador["fax"])));
        $email = sprintf("%-60s", RemoveEspacos($trabalhador["email"]));

        $infoApurGrauRisco = 1; // 1- CNAE PREPONDERANTE DA EMPRESA; 2- CNAE PREPONDERANTE DE CADA ESTABELECIMENTO 
        $indApurAliqFap = 1; // 1- FAP ATRIBUIDO A EMPRESA; 2- FAP ATRIBUIDO A CADA ESTABELECIMENTO 
        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        $eSocial = $dom->createElement("eSocial");
        $dadosCabecalho['indRetificacao'] = $indRetificacao;
        if ($indRetificacao == 2) {
            //$infnrRecibo = $dom->createElement("nrRecibo", $nrRecibo);  //ac cab
            $dadosCabecalho['nrRecibo'] = $nrRecibo;
        }
        $dadosCabecalho['terceiro'] = $trabalhador["terceiro"];
        $dadosCabecalho['indApuracao'] = $indApuracao;
        $dadosCabecalho['perApuracao'] = $perApuracao;

        $xmlCabecalho = $this->montaCabecalho($empregador, $dom, "evtFpAbertura", $dadosCabecalho);
        $eSocial->appendChild($xmlCabecalho);

        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricao = $dom->createElement("tpInscricao", $tpInscricao);
        $infnrInscricao = $dom->createElement("nrIncricao", $nrInscricao);
        $ideRespInformacao = $dom->createElement("ideRespInformacao");
        $infnomeResponsavel = $dom->createElement("nomeResponsavel", $nomeResponsavel);
        $infcpfResponsavel = $dom->createElement("cpfResponsavel", $cpfResponsavel);
        $inftelefone = $dom->createElement("telefone", $telefone);
        $inffax = $dom->createElement("fax", $fax);
        $infemail = $dom->createElement("email", $email);
        $infoApuracao = $dom->createElement("infoApuracao");
        $infinfoApurGrauRisco = $dom->createElement("infoApurGrauRisco", $infoApurGrauRisco);
        $infindApurAliqFap = $dom->createElement("indApurAliqFap", $indApurAliqFap);

        $ideEmpregador->appendChild($inftpInscricao);
        $ideEmpregador->appendChild($infnrInscricao);
        $ideRespInformacao->appendChild($infnomeResponsavel);
        $ideRespInformacao->appendChild($infcpfResponsavel);
        $ideRespInformacao->appendChild($inftelefone);
        if (!empty($empregador["fax"])) {
            $ideRespInformacao->appendChild($inffax);
        }
        $ideRespInformacao->appendChild($infemail);
        $infoApuracao->appendChild($infinfoApurGrauRisco);
        $infoApuracao->appendChild($infindApurAliqFap);

        $xmlCabecalho->appendChild($ideEmpregador);
        $xmlCabecalho->appendChild($ideRespInformacao);
        $xmlCabecalho->appendChild($infoApuracao);

        $eSocial->appendChild($xmlCabecalho);
        $dom->appendChild($eSocial);
        $this->sequencial++;
        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

//EVENTO REMUNERAÇÂO INCOMPLETO    
    public function montas1200($arquivo, $empregador, $trabalhador, $qtdDepSF, $qtdDepIRRF, $teto, $valBase, $descontoCP, $valorProventos, $valorDescontos, $valorLiquido, $nrRecibo, $fichaFinanceira) {
        $bcCPtot = $bcIRRFtot = $bcFGTStot = $descCPtot = 0;
        $tpInscricaoMaster = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricaoMaster = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));

        if ($this->tpevento == "inclusao") {
            $indRetificacao = 1;
        } elseif ($this->tpevento == "alteracao") {
            $indRetificacao = 2;
            $nrRecibo = sprintf("%15s", RemoveEspacos($nrRecibo));
        }
        $indRetificacao = sprintf("%01s", $indRetificacao); //1 - ARQUIVO ORIGINAL 2 - ARQUIVO DE RETIFICAÇÃO
        if (RemoveEspacos($trabalhador["terceiro"]) == 1) {
            $indApuracao = sprintf("%01s", 2); // ANUAL(FOLHA DO 13°)
            $perApuracao = sprintf("%-7s", $this->ano);
        } else {
            $indApuracao = sprintf("%01s", 1); // MENSAL
            $perApuracao = sprintf("%-7s", $this->ano . "-" . $this->mes);
        }

        $cpfTrab = sprintf("%011s", RemoveEspacos(RemoveCaracteres($trabalhador["cpf"])));
        $nisTrab = sprintf("%011s", RemoveEspacos(RemoveCaracteres($trabalhador["pis"])));
        $qtdDepSF = sprintf("%02s", $qtdDepSF);
        $qtdDepIRRF = sprintf("%02s", $qtdDepIRRF);
        if ($trabalhador["trabalha_outra_empresa"] == "sim") {
            if (empty($trabalhador["desconto_outra_empresa"])) {
                $indMV = sprintf("%01s", 1);
            } elseif ($trabalhador["desconto_outra_empresa"] < $teto) {
                $indMV = sprintf("%01s", 2);
            } else {
                $indMV = sprintf("%01s", 3);
            }
//            $tpInscricaoOutraEmp = sprintf("%01s", $tpInscricaoOutraEmp);
//            $nrInscricaoOutraEmp = sprintf("%-15s", $nrInscricaoOutraEmp);
            $vlrRemuneracao = sprintf("%14s", $trabalhador["desconto_outra_empresa"]);
        }
        $nomeTrab = sprintf("%-60s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador["nome"]))));
        $dtNascto = sprintf("%-10s", RemoveEspacos($trabalhador["data_entrada"]));
        $codCbo = sprintf("%06s", RemoveEspacos(RemoveCaracteres($trabalhador["codCbo"])));
        $tpInscricao = sprintf("%01s", $trabalhador["tpInscricao"]);
        $nrInscricao = sprintf("%015s", RemoveEspacos(RemoveCaracteres($trabalhador["nrInscricao"])));
        $codLotacao = sprintf("%-30s", RemoveEspacos($trabalhador["id_projeto"]));
        $matricula = sprintf("%-30s", RemoveEspacos($trabalhador["id_trab"]));
        $codCateg = sprintf("%03s", $trabalhador["codCateg"]);
        $bcCP = sprintf("%014s", $valBase["base_inss"]);
        $bcIRRF = sprintf("%14s", $valBase["base_irrf"]);
        $bcFGTS = sprintf("%14s", $valBase["base_fgts"]);
        $descCP = sprintf("%14s", $descontoCP);
        $vlrProventos = sprintf("%14s", $valorProventos);
        $vlrDescontos = sprintf("%14s", $valorDescontos);
        $vlrLiquido = sprintf("%14s", $valorLiquido);
        switch ($trabalhador['tipo_insalubridade']) {
            case 0:
                $grauExp = 1; // Não exposto a agente nocivo na atividade atual
                break;
            case 1:
                $grauExp = 3; // Exposição a agente nocivo  - aposentadoria especial aos 20 anos de trabalho
                break;
            case 2:
                $grauExp = 2; // Exposição a agente nocivo  - aposentadoria especial aos 25 anos de trabalho
                break;
        }
        $grauExp = sprintf("%1s", $grauExp);
        $bcCPtot = $bcCPtot + $bcCP;
        $bcIRRFtot = $bcIRRFtot + $bcIRRF;
        $bcFGTStot = $bcFGTStot + $bcFGTS;
        $descCPtot = $descCPtot + $descCP;

        foreach ($fichaFinanceira as $codMov => $arrayMov) {
            if ($codMov == '6004' || $codMov == '50222' || $codMov = '7009') {
//                $cpfBeneficiario = sprintf("%11s", RemoveEspacos(RemoveCaracteres($trabalhador["cpfBeneficiario"])));
//                $dtNasctoBeneficiario = sprintf("%10s", $trabalhador["dtNascBeneficiario"]);
//                $nomeBeneficiario = sprintf("%-60s", RemoveEspacos(RemoveAcentos(RemoveCaracteres($trabalhador["nomeBeneficiario"]))));
                $vlrPensao = sprintf("%14s", $arrayMov[$this->mes]);
            }
        }

        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        $eSocial = $dom->createElement("eSocial");
        $dadosCabecalho['indRetificacao'] = $indRetificacao;
        if ($indRetificacao == 2) {
            //$infnrRecibo = $dom->createElement("nrRecibo", $nrRecibo);  //ac cab
            $dadosCabecalho['nrRecibo'] = $nrRecibo;
        }
        $dadosCabecalho['terceiro'] = $trabalhador["terceiro"];
        $dadosCabecalho['indApuracao'] = $indApuracao;
        $dadosCabecalho['perApuracao'] = $perApuracao;

        $xmlCabecalho = $this->montaCabecalho($empregador, $dom, "evtFpRemuneracao", $dadosCabecalho);
        $eSocial->appendChild($xmlCabecalho);

        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpIncricaoMaster = $dom->createElement("tpInscricao", $tpInscricaoMaster);
        $infnrIncricaoMaster = $dom->createElement("nrIncricao", $nrInscricaoMaster);
        $ideTrabalhador = $dom->createElement("ideTrabalhador");
        $infcpfTrab = $dom->createElement("cpfTrab", $cpfTrab);
        $infnisTrab = $dom->createElement("nisTrab", $nisTrab);
        $infqtdDepSF = $dom->createElement("qtdDepSF", $qtdDepSF);
        $infqtdDepIRRF = $dom->createElement("qtdDepIRRF", $qtdDepIRRF);
        if ($trabalhador["trabalha_outra_empresa"] == "sim") {
            $infoMultiplosVinculos = $dom->createElement("infoMultiplosVinculos");
            $infindMV = $dom->createElement("indMV", $indMV);
            $remunOutrasEmpresas = $dom->createElement("remunOutrasEmpresas");
//            $inftpInscricaoOutraEmp = $dom->createElement("tpIncricao", $tpInscricaoOutraEmp);
//            $infnrInscricaoOutraEmp = $dom->createElement("nrInscricao", $nrInscricaoOutraEmp);
            $infvlrRemunOutraEmp = $dom->createElement("vlrRemuneracao", $vlrRemuneracao);
        }
        if ($empregador["classTrab"] == 3) {
            $infoSimplesAtivConcomitante = $dom->createElement("infoSimplesAtivConcomitante");
            $infindSimples = $dom->createElement("indSimples", $indSimples);
        }
        $infoComplementares = $dom->createElement("infoComplementares");
        $infnomeTrab = $dom->createElement("nomeTrab", $nomeTrab);
        $infdtNascto = $dom->createElement("dtNascto", $dtNascto);
        $infcodCbo = $dom->createElement("codCbo", $codCbo);
        if (($empregador["classTrib"] > 5 && $empregador["classTrib"] < 9) || (($empregador["classTrib"] == 21 || $empregador["classTrib"] == 22) && $tpInscricaoMaster == 3)) {
            $infnatAtividade = $dom->createElement("natAtividade", $natAtividade);
        }
        if ($trabalhador["codCateg"] == 203) {
            $infoTrabAvulso = $dom->createElement("infoTrabAvulso");
            $infcnpjSindicato = $dom->createElement("cnpjSindicato", $cnpjSindicato);
            $inffpasSindicato = $dom->createElement("fpasSindicato", $fpasSindicato);
            $infcodTerceiro = $dom->createElement("codTerceiro", $codTeceiro);
        }
//        Parte de processo
//        $procJudTrabalhador = $dom->createElement("procJudTrabalhador");
//        $inftpTributo = $dom->createElement("tpTributo",$tpTributo);
//        $infnrProcJud = $dom->createElement("nrProcJud",$nrProcJud);
        $infoPeriodoApuracao = $dom->createElement("infoPeriodoApuracao");
        $ideEstabLotacao = $dom->createElement("ideEstabLotacao");
        $inftpInscricaoEstab = $dom->createElement("tpInscricao", $tpInscricao);
        $infnrInscricaoEstab = $dom->createElement("nrInscricao", $nrInscricao);
        $infcodLotacao = $dom->createElement("codLotacao", $codLotacao);
        $remunPeriodoApuracao = $dom->createElement("remunPeriodoApuracao");
        $infmatricula = $dom->createElement("matricula", $matricula);
        $infcodCateg = $dom->createElement("codCateg", $codCateg);
        $infbcCP = $dom->createElement("bcCP", $bcCP);
        $infbcIRRF = $dom->createElement("bcIRRF", $bcIRRF);
        $infbcFGTS = $dom->createElement("bcFGTS", $bcFGTS);
        $infdescCP = $dom->createElement("descCP", $descCP);
        $infvlrProventos = $dom->createElement("vlrProventos", $vlrProventos);
        $infvlrDescontos = $dom->createElement("vlrDescontos", $vlrDescontos);
        $infvlrLiquido = $dom->createElement("vlrLiquido", $vlrLiquido);
        $itensRemun = $dom->createElement("itensRemun");

//        Parte de classificação de grau de exposição a agentes nocivos
        if ($trabalhador["grupo"] == "Empregado" || $trabalhador["codCateg"] == 741 || (($trabalhador["codCateg"] == 731 || $trabalhador["codCateg"] == 734) && $grauExp < 1)) {
            $infoAgenteNocivo = $dom->createElement("infoAgenteNocivo");
            $infgrauExp = $dom->createElement("grauExp", $grauExp);
        }

//        Parte de informação de rescisão
//        if(!empty($trabalhador["id_recisao"])){
//            $infoRescisao = $dom->createElement("infoRescisao");
//            $nrReciboDeslig = $dom->createElement("nrReciboDeslig", $nrReciboDeslig);
//        }
//        Parte de pensão alimentícia
        foreach ($fichaFinanceira as $codMov => $arrayMov) {
            if ($codMov == '6004' || $codMov == '50222' || $codMov = '7009') {
                $pensaoAlimenticia = $dom->createElement("pensaoAlimenticia");
//                $infcpfBeneficiario = $dom->createElement("cpfBeneficiario", $cpfBeneficiario);
//                $infdtNasctoBeneficiario = $dom->createElement("dtNasctoBeneficiario", $dtNasctoBeneficiario);
//                $infnomeBeneficiario = $dom->createElement("nomeBeneficiario", $nomeBeneficiario);
                $infvlrPensao = $dom->createElement("vlrPensao", $vlrPensao);
            }
        }
        // Remuneração período anterior ??

        $totRemuneracao = $dom->createElement("totRemuneracao");
        $infperReferencia = $dom->createElement("perReferencia", $perApuracao);
        $infmatricula = $dom->createElement("matricula", $matricula);
        $infcodCateg = $dom->createElement("codCateg", $codCateg);
        $infbcCPtot = $dom->createElement("bcCP", $bcCPtot);
        $infbcFGTStot = $dom->createElement("bcFGTS", $bcFGTStot);
        $infbcIRRFtot = $dom->createElement("bcIRRF", $bcIRRFtot);
        $infdescCPtot = $dom->createElement("descCP", $descCPtot);


        $totRemuneracao->appendChild($infperReferencia);
        $totRemuneracao->appendChild($infmatricula);
        $totRemuneracao->appendChild($infcodCateg);
        $totRemuneracao->appendChild($infbcCPtot);
        $totRemuneracao->appendChild($infbcFGTStot);
        $totRemuneracao->appendChild($infbcIRRFtot);
        $totRemuneracao->appendChild($infdescCPtot);


//        Parte de classificação de grau de exposição a agentes nocivos
        $infoAgenteNocivo->appendChild($infgrauExp);

//        Parte de informação de rescisão
//        $infoRescisao->appendChild($infnrReciboDeslig);
//        Parte de pensão alimentícia
        if (!empty($vlrPensao)) {
//            $pensaoAlimenticia->appendChild($infcpfBeneficiario);
//            $pensaoAlimenticia->appendChild($infdtNasctoBeneficiario);
//            $pensaoAlimenticia->appendChild($infnomeBeneficiario);
            $pensaoAlimenticia->appendChild($infvlrPensao);
        }

//        Periodo Anterior

        $remunPeriodoApuracao->appendChild($infmatricula);
        $remunPeriodoApuracao->appendChild($infcodCateg);
        $remunPeriodoApuracao->appendChild($infbcCP);
        $remunPeriodoApuracao->appendChild($infbcIRRF);
        $remunPeriodoApuracao->appendChild($infbcFGTS);
        $remunPeriodoApuracao->appendChild($infdescCP);
        $remunPeriodoApuracao->appendChild($infvlrProventos);
        $remunPeriodoApuracao->appendChild($infvlrDescontos);
        $remunPeriodoApuracao->appendChild($infvlrLiquido);

//        Parte de itens da remuneração
        foreach ($fichaFinanceira as $codMov => $arrayMov) {
            $codRubrica = RemoveEspacos(RemoveCaracteresGeral($codMov));
            if (($arrayMov[$this->mes] > 0) && !empty($codRubrica)) {
                $codRubrica = sprintf("%-30s", $codRubrica);
                //            $qtdRubrica = sprintf("%6s", $trabalhador['qtdRubrica']);
                //            $valorUnitario = sprintf("%14s", $trabalhador['vltUnitario']);
                $valorRubrica = sprintf("%14s", RemoveEspacos(RemoveLetras(RemoveAcentos(str_replace('(', "", str_replace(')', "", $arrayMov[$this->mes]))))));
                $infcodRubrica = $dom->createElement("codRubrica", $codRubrica);
                //            $infqtdRubrica = $dom->createElement("qtdRubrica", $qtdRubrica);
                //            $infvalorUnitario= $dom->createElement("valorUnitario", $valorUnitario);
                $infvalorRubrica = $dom->createElement("valorRubrica", $valorRubrica);
                $itensRemun->appendChild($infcodRubrica);
                //            $itensRemun->appendChild($infqtdRubrica);
                //            $itensRemun->appendChild($infvalorUnitario);
                $itensRemun->appendChild($infvalorRubrica);
                $remunPeriodoApuracao->appendChild($itensRemun);
            }
        }
//        $remunPeriodoApuracao->appendChild($itensRemun);
//        
//        Parte de classificação de grau de exposição a agentes nocivos        
        $remunPeriodoApuracao->appendChild($infoAgenteNocivo);

//        Parte de informação de rescisão
//        $remunPeriodoApuracao->appendChild($infoRescisao);
//        Parte de pensão alimentícia      
        if (!empty($vlrPensao)) {
            $remunPeriodoApuracao->appendChild($pensaoAlimenticia);
        }

//        Periodo Anterior


        $ideEstabLotacao->appendChild($inftpInscricaoEstab);
        $ideEstabLotacao->appendChild($infnrInscricaoEstab);
        $ideEstabLotacao->appendChild($infcodLotacao);
        $ideEstabLotacao->appendChild($remunPeriodoApuracao);
        $infoPeriodoApuracao->appendChild($ideEstabLotacao);
        $ideTrabalhador->appendChild($infcpfTrab);
        $ideTrabalhador->appendChild($infnisTrab);
        $ideTrabalhador->appendChild($infqtdDepSF);
        $ideTrabalhador->appendChild($infqtdDepIRRF);
        if ($trabalhador["trabalha_outra_empresa"] == "sim") {
//            $remunOutrasEmpresas->appendChild($inftpInscricaoOutraEmp);
//            $remunOutrasEmpresas->appendChild($infnrInscricaoOutraEmp);
            $remunOutrasEmpresas->appendChild($infvlrRemunOutraEmp);
            $infoMultiplosVinculos->appendChild($remunOutrasEmpresas);
            $infoMultiplosVinculos->appendChild($infindMV);
            $ideTrabalhador->appendChild($infoMultiplosVinculos);
        }
        if ($empregador["classTrab"] == 3) {
            $infoSimplesAtivConcomitante->appendChild($infindSimples);
            $ideTrabalhador->appendChild($infoSimplesAtivConcomitante);
        }
        $infoComplementares->appendChild($infnomeTrab);
        $infoComplementares->appendChild($infdtNascto);
        $infoComplementares->appendChild($infcodCbo);
        if (($empregador["classTrib"] > 5 && $empregador["classTrib"] < 9) || (($empregador["classTrib"] == 21 || $empregador["classTrib"] == 22) && $tpInscricaoMaster == 3)) {
            $infoComplementares->appendChild($infnatAtividade);
        }
        $ideTrabalhador->appendChild($infoComplementares);
        if ($trabalhador["codCateg"] == 203) {
            $infoTrabAvulso->appendChild($infcnpjSindicato);
            $infoTrabAvulso->appendChild($inffpasSindicato);
            $infoTrabAvulso->appendChild($infcodTerceiro);
            $ideTrabalhador->appendChild($infoTrabAvulso);
        }


//        parte de processo
//        $procJudTrabalhador->appendChild($infTpTributo);
//        $procJudTrabalhador->appendChild($infnrProcJud);
//        $ideTrabalhador->appendChild($procJudTrabalhador);

        $ideEmpregador->appendChild($inftpIncricaoMaster);
        $ideEmpregador->appendChild($infnrIncricaoMaster);

        $xmlCabecalho->appendChild($ideEmpregador);
        $xmlCabecalho->appendChild($ideTrabalhador);
        $xmlCabecalho->appendChild($infoPeriodoApuracao);
        $xmlCabecalho->appendChild($totRemuneracao);
        $eSocial->appendChild($xmlCabecalho);
        $dom->appendChild($eSocial);
        $this->sequencial++;

        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

//EVENTO CADASTRAMENTO INICIAL DO VÍNCULO
//EVENTO ADMISSAO   
//EVENTO ALTERAÇÃO DE DADOS CADASTRAIS
//EVENTO ALTERACAO DE CONTRATO DE TRABALHO    
    public function montas2100a2240($arquivo, $empregador, $trabalhador, $dependente, $contrato, $transferecia, $numRecibo, $evento) {
        switch ($evento) {
            case "s2100":
                $evt = "evtCadInicial";
                $tagcontrato = "contrato";
                $tagtrabalhador = "trabalhador";
                break;

            case "s2200":
                $evt = "evtAdmissao";
                $tagcontrato = "infoContrato";
                $tagtrabalhador = "trabalhador";
                break;
            case "s2220":
                $evt = "evtAltCadastral";
                $tagtrabalhador = "ideTrabalhador";
                break;
            case "s2240":
                $evt = "evtAltContratual";
                $tagcontrato = "infoContrato";
                $tagtrabalhador = "ideVinculo";
                break;
        }

        $tpInscricaoMaster = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricaoMaster = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));

        if ($this->tpevento == "inclusao") {
            $indRetificacao = 1;
        } elseif ($this->tpevento == "alteracao") {
            $indRetificacao = 2;
            $nrRecibo = $numRecibo;
            $nrRecibo = sprintf("%15s", RemoveEspacos($nrRecibo));
        }
        $indRetificacao = sprintf("%01s", $indRetificacao); //1 - ARQUIVO ORIGINAL 2 - ARQUIVO DE RETIFICAÇÃO

        $cpfTrab = sprintf("%11s", RemoveEspacos(RemoveCaracteres($trabalhador['cpf'])));
        $nisTrab = sprintf("%11s", RemoveEspacos(RemoveCaracteres($trabalhador['pis'])));
        if ($evento != "s2240") {
            $nomeTrab = sprintf("%-60s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador['nomeTrab']))));
            $sexo = sprintf("%-1s", RemoveEspacos($trabalhador['sexo']));
            $racaCor = sprintf("%1s", RemoveEspacos(substr($trabalhador['racaCor'], 1)));
            $estadoCivil = sprintf("%1s", RemoveEspacos($trabalhador['cod_estado_civil']));
            $grauInstrucao = sprintf("%2s", RemoveEspacos($trabalhador['grauInstrucao']));
            $dtNascimento = sprintf("%10s", RemoveEspacos($trabalhador['data_nasci']));
            $codMunicipioNasc = sprintf("%7s", RemoveEspacos($trabalhador['codMunicipioNasc']));
            $ufNasc = sprintf("%-2s", RemoveEspacos($trabalhador['uf_nasc']));
            $paisNascto = sprintf("%3s", RemoveEspacos($trabalhador['cod_pais_nasc']));
            $paisNacionalidade = sprintf("%3s", RemoveEspacos($trabalhador['cod_pais_nacionalidade']));
            $nomeMae = sprintf("%-60s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador['mae']))));
            $nomePai = sprintf("%-60s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador['pai']))));
            $nrCtps = sprintf("%-11s", RemoveEspacos($trabalhador['nrCtps']));
            $serieCtps = sprintf("%-5s", RemoveEspacos(RemoveCaracteres($trabalhador['serie_ctps'])));
            $ufCtps = sprintf("%-2s", RemoveEspacos($trabalhador['uf_ctps']));
            $nrRg = sprintf("%14s", RemoveEspacos(RemoveCaracteres($trabalhador['rg'])));
            $orgaoEmissor = sprintf("%20s", RemoveEspacos($trabalhador['orgao']));
            $dtExpedicao = sprintf("%-10s", $trabalhador['data_emissao']);
            //        $tpLogradouro = sprintf("%-3s", RemoveEspacos($trabalhador['tpLogradouro']));
            $descLogradouro = sprintf("%-80s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador['endereco']))));
            $nrLogradouro = sprintf("%-10s", RemoveEspacos(RemoveCaracteres($trabalhador['numero'])));
            $complemento = sprintf("%-30s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador['complemento']))));
            $bairro = sprintf("%-60s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador['bairro']))));
            $cep = sprintf("%08s", RemoveEspacos(RemoveCaracteres($trabalhador['cep'])));
            $codMunicipioEnd = sprintf("%07s", RemoveEspacos($trabalhador['codMunicipioEnd']));
            $ufEnd = sprintf("%-02s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador['ufEnd']))));
            //        $paisResidencia = sprintf("%-03s", RemoveEspacos($trabalhador['paisResidencia']));
            //        $descLogradouro = sprintf("%-80s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador['endereco']))));
            //        $nrLogradouro = sprintf("%-10s", RemoveEspacos(RemoveCaracteres($trabalhador['numero'])));
            //        $complemento = sprintf("%-30s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador['complemento']))));
            //        $bairro = sprintf("%-60s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador['bairro']))));
            //        $nomeCidade = sprintf("%-30s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador['nomeCidade']))));
            //        $codPostal = sprintf("%-10s", RemoveEspacos(RemoveCaracteres($trabalhador['codPostal'])));
            $dtChegada = sprintf("%-10s", RemoveEspacos($trabalhador['dtChegadaPais']));
            //        $dtNaturalizacao = sprintf("%-10s", RemoveEspacos($trabalhador['dtNaturalizacao']));
            //        $casadoBr = sprintf("%-1s", RemoveEspacos($trabalhador['casadoBr']));
            //        $filhosBr = sprintf("-1s", RemoveEspacos($trabalhador['filhosBr']));
            $defFisica = sprintf("%-1s", $trabalhador['defFisica']);
            $defVisual = sprintf("%-1s", $trabalhador['defVisual']);
            $defAuditiva = sprintf("%-1s", $trabalhador['defAuditiva']);
            $defMental = sprintf("%-1s", $trabalhador['defMental']);
            $defIntelectual = sprintf("%-1s", $trabalhador['defIntelectual']);
            $reabilitado = sprintf("%-1s", $trabalhador['reabilitado']);
            //        $observacao = sprintf("%-255s", RemoveEspacos(RemoveAcentos($trabalhador['observacao'])));
            //        $tpDep = sprintf("%-2s", $trabalhador['tpDep']);
            //        $trabAposentadoria = sprintf("%1s", $trabalhador['trabAposentadoria']);
            $fonePrincipal = sprintf("%13s", RemoveEspacos(RemoveCaracteres($trabalhador['tel_fixo'])));
            $foneAlternativo = sprintf("%13s", RemoveEspacos(RemoveCaracteres($trabalhador['tel_cel'])));
            $emailPrincipal = sprintf("%60s", RemoveEspacos($trabalhador['email']));
        }
        if ($evento != "s2220") {
            $matricula = sprintf("%30s", RemoveEspacos($trabalhador['id_trab']));
            if ($evento != "s2240") {
                $dtAdmissao = sprintf("%10s", RemoveEspacos($trabalhador['data_entrada']));
                $tpAdmissao = $transferecia['satus_transf'];
                $indAdmissao = 1;
                $indPrimeiroEmprego = sprintf("%1s", $trabalhador['indPrimeiroEmprego']);
            }
            $tpRegimeTrab = 1;
            $tpRegimePrev = 1;
            $tpRegimeJor = 1;
            $natAtividade = 1;
            $codCateg = sprintf("%3s", $trabalhador['codCateg']);
            $codCargo = sprintf("%30s", $trabalhador['id_curso']);
            $codCbo = sprintf("%-6s", RemoveCaracteres($trabalhador['codCbo']));
            //        $dataBase = sprintf("%-2s", );
            $valSalFixo = sprintf("%14s", RemoveEspacos($trabalhador['salario']));
            $unidSalFixo = sprintf("%1s", 5); // PAGAMENTO POR MES
            //        $descSalVariavel = sprintf("%90s", RemoveEspacos(RemoveAcentos($trabalhador['descSalVariavel'])));
            $tpContrato = sprintf("%1s", $contrato['tpContrato']);
            $dtTermino = sprintf("%10s", $contrato['dtTermino']);
            $tpInscricao = sprintf("%1s", $trabalhador['tpInscricao']);
            $nrInscricao = sprintf("%015s", RemoveEspacos(RemoveCaracteres($trabalhador['nrInscricao'])));
            $codLotacao = sprintf("%-30s", $trabalhador['id_projeto']);
            //        $descComplementar = sprintf("%-80s", $trabalhador['descComplementar']);
            $qtdHorasSemanal = sprintf("%04s", $trabalhador['horas_semanais']);
            $tpJornada = sprintf("%01s", $trabalhador['tpJornada']);
            $descTpJornada = sprintf("%-100s", $trabalhador['descTpJornada']);
            $codHorContratual = sprintf("%-30s", $trabalhador['id_horario']);
            $cnpjSindTrabalhador = sprintf("%-14s", RemoveCaracteres($trabalhador['cnpjSindTrabalhador']));
            //        $optanteFgts = sprintf("%01s", $trabalhador['optanteFgts']);
            //        $dtOpcaoFgts = sprintf("%-10s", $trabalhador['dtOpcaoFgts']);
        }

        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        $eSocial = $dom->createElement("eSocial");
        $dadosCabecalho['indRetificacao'] = $indRetificacao;
        if ($indRetificacao == 2) {
            //$infnrRecibo = $dom->createElement("nrRecibo", $nrRecibo);  //ac cab
            $dadosCabecalho['nrRecibo'] = $nrRecibo;
        }

        $evt = $this->montaCabecalho($empregador, $dom, $evt, $dadosCabecalho);

        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricaoMaster = $dom->createElement("tpInscricao", $tpInscricaoMaster);
        $infnrInscricaoMaster = $dom->createElement("nrInscricao", $nrInscricaoMaster);
        $tagtrabalhador = $dom->createElement($tagtrabalhador);
        $infcpfTrab = $dom->createElement("cpfTrab", $cpfTrab);
        $infnisTrab = $dom->createElement("nisTrab", $nisTrab);

        if ($evento != "s2240") {
            $infnomeTrab = $dom->createElement("nomeTrab", $nomeTrab);
            $infsexo = $dom->createElement("sexo", $sexo);
            $infracaCor = $dom->createElement("racaCor", $racaCor);
            $infestadoCivil = $dom->createElement("estadoCivil", $estadoCivil);
            $infgrauInstrucao = $dom->createElement("grauInstrucao", $grauInstrucao);
            $nascimento = $dom->createElement("nascimento");
            $infdtNascto = $dom->createElement("dtNascto", $dtNascimento);
            $infcodMunicipioNasc = $dom->createElement("codMunicipio", $codMunicipioNasc);
            $infufNasc = $dom->createElement("uf", $ufNasc);
            $infpaisNascto = $dom->createElement("paisNascto", $paisNascto);
            $infpaisNacionalidade = $dom->createElement("paisNacionalidade", $paisNacionalidade);
            $infnomeMae = $dom->createElement("nomeMae", $nomeMae);
            $infnomePai = $dom->createElement("nomePai", $nomePai);
            $documentos = $dom->createElement("documentos");
            $ctps = $dom->createElement("CTPS");
            $infnrCtps = $dom->createElement("nrCtps", $nrCtps);
            $infserieCtps = $dom->createElement("serieCtps", $serieCtps);
            $infufCtps = $dom->createElement("ufCtps", $ufCtps);
            $rg = $dom->createElement("RG");
            $infnrRg = $dom->createElement("nrRg", $nrRg);
            $inforgaoEmissor = $dom->createElement("orgaoEmissor", $orgaoEmissor);
            $infdtExpedicao = $dom->createElement("dtExpedicao", $dtExpedicao);
            $endereco = $dom->createElement("endereco");
            $brasil = $dom->createElement("brasil");
            //        $inftpLogradouro = $dom->createElement("tpLogradouro", $tpLogradouro);
            $infdescLogradouro = $dom->createElement("descLogradouro", $descLogradouro);
            $infnrLogradouro = $dom->createElement("nrLogradouro", $nrLogradouro);
            $infcomplemento = $dom->createElement("complemento", $complemento);
            $infbairro = $dom->createElement("bairro", $bairro);
            $infcep = $dom->createElement("cep", $cep);
            $infcodMunicipioEnd = $dom->createElement("codMunicipio", $codMunicipioEnd);
            $infufEnd = $dom->createElement("uf", $ufEnd);
            //        $exterior = $dom->createElement("exterior");
            //        $infpaisResidencia = $dom->createElement("paisResidencia", $paisResidencia);
            $trabEstrangeiro = $dom->createElement("trabEstrangeiro");
            $infdtChegada = $dom->createElement("dtChegada", $dtChegada);
            //        $infdtNaturalizacao = $dom->createElement("dtNaturalizacao", $dtNaturalizacao);
            //        $infcasadoBr = $dom->createElement("casadoBr", $casadoBr);
            //        $inffilhosBr = $dom->createElement("filhosBr", $filhosBr);
            $infoDeficiencia = $dom->createElement("infoDeficiencia");
            $infdefFisica = $dom->createElement("defFisica", $defFisica);
            $infdefVisual = $dom->createElement("defVisual", $defVisual);
            $infdefAuditiva = $dom->createElement("defAuditiva", $defAuditiva);
            $infdefMental = $dom->createElement("defMental", $defMental);
            $infdefIntelectual = $dom->createElement("defIntelectual", $defIntelectual);
            $infreabilitado = $dom->createElement("reabilitado", $reabilitado);
            //        $infobservacao = $dom->createElement("observacao", $observacao);
            //        $inftpDep = $dom->createElement("tpDep", $tpDep);
            $aposentadoria = $dom->createElement("aposentadoria");
            //        $inftrabAposentadoria = $dom->createElement("trabAposentadoria", $trabAposentadoria);
            $contato = $dom->createElement("contato");
            $inffonePrincipal = $dom->createElement("fonePrincipal", $fonePrincipal);
            $inffoneAleternativo = $dom->createElement("foneAlternativo", $foneAlternativo);
            $infemailPrincipal = $dom->createElement("emailPrincipal", $emailPrincipal);
            //        $infemailAlternativo = $dom->createElement("emailAlternativo", $emailAletenativo);
        }

        if ($evento != "s2220") {

            $vinculo = $dom->createElement("vinculo");
            $infmatricula = $dom->createElement("matricula", $matricula);
            if ($evento != "s2240") {
                $infdtAdmissao = $dom->createElement("dtAdmissao", $dtAdmissao);
                $inftpAdmissao = $dom->createElement("tpAdmissao", $tpAdmissao);
                $infindAdmissao = $dom->createElement("indAdmissao", $indAdmissao);
                $infindPrimeiroEmprego = $dom->createElement("indPrimeiroEmprego", $indPrimeiroEmprego);
            }

            $tagcontrato = $dom->createElement($tagcontrato);
            $inftpRegimeTrab = $dom->createElement("tpRegimeTrab", $tpRegimeTrab);
            $inftpRegimePrev = $dom->createElement("tpRegimePrev", $tpRegimePrev);
            $inftpRegimeJor = $dom->createElement("tpRegimeJor", $tpRegimeJor);
            $infnatAtividade = $dom->createElement("natAtividade", $natAtividade);
            $infcodCateg = $dom->createElement("codCateg", $codCateg);
            $infcodCargo = $dom->createElement("codCargo", $codCargo);
            $infcodCbo = $dom->createElement("codCbo", $codCbo);
            //        $infdtBase = $dom->createElement("dtBase", $dtBase);
            $remuneracao = $dom->createElement("remuneracao");
            $infvlrSalFixo = $dom->createElement("vlrSalFixo", $valSalFixo);
            $infunidSalFixo = $dom->createElement("unidSalFixo", $unidSalFixo);
            //        $infdescSalVariavel = $dom->createElement("descSalVariavel", $descSalVariavel);
            $duracao = $dom->createElement("duracao");
            $inftpContrato = $dom->createElement("tpContrato", $tpContrato);
            $infdtTermino = $dom->createElement("dtTermino", $dtTermino);
            $localTrabalho = $dom->createElement("localTrabalho");
            $inftpInscricao = $dom->createElement("tpInscricao", $tpInscricao);
            $infnrInscricao = $dom->createElement("nrInscricao", $nrInscricao);
            $infcodLotacao = $dom->createElement("codLotacao", $codLotacao);
            //        $infdescComplementar = $dom->createElement("descComplementar", $descComplementar);
            $horContratual = $dom->createElement("horContratual");
            $infqtdHorasSemanal = $dom->createElement("qtdHorasSemanal", $qtdHorasSemanal);
            $inftpJornada = $dom->createElement("tpJornada", $tpJornada);
            if ($trabalhador['tpJornada'] == 2) {
                $infdescTpJornada = $dom->createElement("descTpJornada", $descTpJornada);
            }
            $filiacaoSindical = $dom->createElement("filiacaoSindical");
            $infcnpjSindTrabalhador = $dom->createElement("cnpjSindTrabalhador", $cnpjSindTrabalhador);
            //        $alvaraJudicial = $dom->createElement("alvaraJudicial");
            //        $nrProcJud = $dom->createElement("nrProcJud", $nrProcJud);
            //        $fgts = $dom->createElement("FGTS");
            //        $infoptanteFgts = $dom->createElement("optanteFGTS", $optanteFgts);
            //        $infdtOpcaoFgts = $dom->createElement("dtOpcoesFGTS", $dtOpcaoFgts);
        }

        switch ($evento) {
            case "s2220":
                $alteracao = $dom->createElement("alteracao");
                $dadosTrab = $dom->createElement("dadosTrabalhador");
                $tag = $dadosTrab;
                break;
            case "s2240":
                $alteracao = $dom->createElement("altContratual");
                $vinculo = $tagtrabalhador;
                $tag = $tagtrabalhador;
            default:
                $tag = $tagtrabalhador;
                break;
        }

        if ($evento != "s2220") {
            if ($evento != "s2240") {
                $vinculo->appendChild($infmatricula);
                $vinculo->appendChild($infdtAdmissao);
                $vinculo->appendChild($inftpAdmissao);
                $vinculo->appendChild($infindAdmissao);
                $vinculo->appendChild($infindPrimeiroEmprego);
            }
            $tagcontrato->appendChild($inftpRegimeTrab);
            $tagcontrato->appendChild($inftpRegimePrev);
            $tagcontrato->appendChild($inftpRegimeJor);
            $tagcontrato->appendChild($infnatAtividade);
            $tagcontrato->appendChild($infcodCateg);
            $tagcontrato->appendChild($infcodCargo);
            $tagcontrato->appendChild($infcodCbo);
            //        $tagcontrato->appendChild($infdtBase);
            $remuneracao->appendChild($infvlrSalFixo);
            $remuneracao->appendChild($infunidSalFixo);
            //        $remuneracao->appendChild($infdescSalVariavel);
            $tagcontrato->appendChild($remuneracao);
            $duracao->appendChild($inftpContrato);
            if (!empty($contrato['dtTermino']) && $contrato['dtTermino'] != '0000-00-00') {
                $duracao->appendChild($infdtTermino);
            }
            $tagcontrato->appendChild($duracao);
            $localTrabalho->appendChild($inftpInscricao);
            $localTrabalho->appendChild($infnrInscricao);
            $localTrabalho->appendChild($infcodLotacao);
            //        $localTrabalho->appendChild($infdescComplementar);
            $tagcontrato->appendChild($localTrabalho);
            if ($tpRegimeTrab == 1 || ($tpRegimeTrab == 2 && !empty($trabalhador['horas_semanais']) && !empty($trabalhador['tpJornada']))) {
                $horContratual->appendChild($infqtdHorasSemanal);
                $horContratual->appendChild($inftpJornada);
                if ($tpJornada == 2) {
                    $horContratual->appendChild($infdescTpJornada);
                }
                if ($trabalhador['folga'] == 5) {
                    $horario = $dom->createElement("horario");
                    $dia = $dom->createElement("dia", 8);
                    $infcodHorContratual = $dom->createElement("codHorContratual", $codHorContratual);
                    $horario->appendChild($dia);
                    $horario->appendChild($infcodHorContratual);
                } else {
                    $cont = 7 - $trabalhador['folga'];
                    for ($i = 1; $i <= $cont; $i++) {
                        $horario = $dom->createElement("horario");
                        $dia = $dom->createElement("dia", $i);
                        $infcodHorContratual = $dom->createElement("codHorContratual", $codHorContratual);
                        $horario->appendChild($dia);
                        $horario->appendChild($infcodHorContratual);
                    }
                }
                $horContratual->appendChild($horario);
                $tagcontrato->appendChild($horContratual);
            }

            if (!empty($trabalhador['descricao'])) {
                $infoAtividadeDesemp = $dom->createElement("infoAtividadeDesemp");
                $atividade = explode(';', RemoveAcentos($trabalhador['descricao']));
                $totalAtiv = count($atividade);
                for ($i = 0; $i < $totalAtiv; $i++) {
                    if (!empty($atividade[$i])) {
                        $descAtividade = $dom->createElement("descAtividade");
                        $descAtividadeDesemp = $dom->createElement("descAtividadeDesemp", $atividade[$i]);
                        $descAtividade->appendChild($descAtividadeDesemp);
                        $infoAtividadeDesemp->appendChild($descAtividade);
                    }
                }
                $tagcontrato->appendChild($infoAtividadeDesemp);
            }
            if ($cnpjSindTrabalhador) {
                $filiacaoSindical->appendChild($infcnpjSindTrabalhador);
                $tagcontrato->appendChild($filiacaoSindical);
            }

            //        $tagcontrato->appendChild($alvaraJudicial);
            //        $alvaraJudicial->appendChild($nrProcesso);   
            //        $fgts->appendChild($optanteFgts);
            //        $fgts->appendChild($dtOpcaoFgts);

            if ($evento != "s2240") {
                $vinculo->appendChild($tagcontrato);
//              $vinculo->appendChild($fgts);
            }
        }

        $tagtrabalhador->appendChild($infcpfTrab);
        if ($trabalhador['pis']) {
            $tag->appendChild($infnisTrab);
        }
        if ($evento != "s2240") {
            $tag->appendChild($infnomeTrab);
            $tag->appendChild($infsexo);
            $tag->appendChild($infracaCor);
            $tag->appendChild($infestadoCivil);
            $tag->appendChild($infgrauInstrucao);
            $nascimento->appendChild($infdtNascto);
            if ($trabalhador['codMunicipioNasc']) {
                $nascimento->appendChild($infcodMunicipioNasc);
            }
            if ($trabalhador['uf_nasc']) {
                $nascimento->appendChild($infufNasc);
            }
            $nascimento->appendChild($infpaisNascto);
            $nascimento->appendChild($infpaisNacionalidade);
            if ($trabalhador['mae']) {
                $nascimento->appendChild($infnomeMae);
            }
            if ($trabalhador['pai']) {
                $nascimento->appendChild($infnomePai);
            }
            $tag->appendChild($nascimento);
            if (!empty($trabalhador['nrCtps']) || !empty($trabalhador['rg'])) {
                if ($trabalhador['nrCtps']) {
                    $ctps->appendChild($infnrCtps);
                    $ctps->appendChild($infserieCtps);
                    $ctps->appendChild($infufCtps);
                    $documentos->appendChild($ctps);
                }
                if ($trabalhador['rg']) {
                    $rg->appendChild($infnrRg);
                    $rg->appendChild($inforgaoEmissor);
                    $rg->appendChild($infdtExpedicao);
                    $documentos->appendChild($rg);
                }
                $tag->appendChild($documentos);
            }

            //        $brasil->appendChild($inftpLogradouro);
            $brasil->appendChild($infdescLogradouro);
            if ($trabalhador['numero']) {
                $brasil->appendChild($infnrLogradouro);
            }
            if ($trabalhador['complemento']) {
                $brasil->appendChild($infcomplemento);
            }
            if ($trabalhador['bairro']) {
                $brasil->appendChild($infbairro);
            }
            $brasil->appendChild($infcep);
            $brasil->appendChild($infcodMunicipioEnd);
            $brasil->appendChild($infufEnd);
            $endereco->appendChild($brasil);
            $tag->appendChild($endereco);

            if (!empty($trabalhador['cod_pais_nasc']) && $trabalhador['cod_pais_nasc'] != '001') {
                $trabEstrangeiro->appendChild($infdtChegada);
                //        $trabEstrangeiro->appendChild($infdtNaturalizacao);
                //        $trabEstrangeiro->appendChild($infcasadoBr);
                //        $trabEstrangeiro->appendChild($inffilhosBr);
                $tag->appendChild($trabEstrangeiro);
            }
            if (!empty($trabalhador['deficiencia'])) {
                $infoDeficiencia->appendChild($infdefFisica);
                $infoDeficiencia->appendChild($infdefVisual);
                $infoDeficiencia->appendChild($infdefAuditiva);
                $infoDeficiencia->appendChild($infdefMental);
                $infoDeficiencia->appendChild($infdefIntelectual);
                $infoDeficiencia->appendChild($infreabilitado);
                //        $infoDeficiencia->appendChild($infobservacao);
                $tag->appendChild($infoDeficiencia);
            }

            foreach ($dependente as $key => $value) {
                $i = substr($key, -1);
                if (!empty($value)) {
                    switch ($key) {
                        case 'nome' . $i:
                            $nomeDep = sprintf("%-60s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($value))));
                            $infnomeDep = $dom->createElement("nomeDep");
                            $infnomeDep->appendChild($dom->createTextNode($nomeDep));
                            break;

                        case 'data' . $i:
                            $dtNasctoDep = sprintf("%-10s", RemoveEspacos($value));
                            $infdtNasctoDep = $dom->createElement("dtNascto");
                            $infdtNasctoDep->appendChild($dom->createTextNode($dtNasctoDep));
                            break;

                        case 'depIR' . $i:
                            $depIr = sprintf("%1s", RemoveEspacos($value));
                            $infdepIr = $dom->createElement("depIRRF");
                            $infdepIr->appendChild($dom->createTextNode($depIr));
                            break;

                        case 'depSF' . $i:
                            $depSF = sprintf("%1s", RemoveEspacos($value));
                            $infdepSF = $dom->createElement("depSF");
                            $infdepSF->appendChild($dom->createTextNode($depSF));
                            $dependente = $dom->createElement("dependente");
                            //                        $dependente->appendChild($inftpDep);
                            $dependente->appendChild($infnomeDep);
                            $dependente->appendChild($infdtNasctoDep);
                            $dependente->appendChild($infdepIr);
                            $dependente->appendChild($infdepSF);
                            $tag->appendChild($dependente);
                            break;
                    }
                }
            }

            //        $aposentadoria->appendChild($inftrabAposentado);
            $tag->appendChild($aposentadoria);
            if (!empty($trabalhador['tel_fixo']) || !empty($trabalhador['tel_cel']) || !empty($trabalhador['email'])) {
                if ($trabalhador['tel_fixo']) {
                    $contato->appendChild($inffonePrincipal);
                }
                if ($trabalhador['tel_cel']) {
                    $contato->appendChild($inffoneAleternativo);
                }
                if ($trabalhador['email']) {
                    $contato->appendChild($infemailPrincipal);
                }

                //            $contato->appendChild($infemailAlternativo);
                $tag->appendChild($contato);
            }
        }
        if ($evento == "s2220" || $evento == "s2240") {
            $dtAlteracao = sprintf("%-10s", date("Y-m-d"));
            $infdtAlteracao = $dom->createElement("dtAlteracao", $dtAlteracao);
            $alteracao->appendChild($infdtAlteracao);
            $alteracao->appendChild($tag);
            if ($evento == "s2240") {
                $vinculo->appendChild($infmatricula);
                $alteracao->appendChild($tagcontrato);
                //              $alteracao->appendChild($fgts);
            }
        }

        $ideEmpregador->appendChild($inftpInscricaoMaster);
        $ideEmpregador->appendChild($infnrInscricaoMaster);

        $evt->appendChild($ideEmpregador);
        $evt->appendChild($tagtrabalhador);

        if ($evento == "s2220") {
            $evt->appendChild($alteracao);
        }

        if ($evento != "s2220") {
            $evt->appendChild($vinculo);
        }
        if ($evento == "s2240") {
            $evt->appendChild($alteracao);
        }

        $eSocial->appendChild($evt);
        $dom->appendChild($eSocial);
        $this->sequencial++;

//        echo $dom->saveXML();
        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

//EVENTO COMUNICAÇÃO DE ACIDENTE DE TRABALHO
    public function montas2260($arquivo, $empregador, $trabalhador, $numRecibo, $cat, $testemunha) {
        $tpInscricaoMaster = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricaoMaster = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));
//        $dt_hs_atual = str_replace(" ", "", RemoveCaracteres(date("Y-m-d H:i:s", time())));
//        $sequencial = sprintf("%05s", $this->sequencial);
//
//        $id = sprintf("%-36s", "ID" . $tpInscricaoMaster . $nrInscricaoMaster . $dt_hs_atual . $sequencial);
//        $versao = sprintf("%-11s", "1.2 - beta2"); // VERSÃO DO LAYOUT
//        $tpAmb = sprintf("%01s", 1); // CONSTANTE
//        $procEmi = sprintf("%01s", 1); // CONSTANTE
//        $indSeguimento = sprintf("%01s", 1); // CONSTANTE
//        $verProc = sprintf("%-20s", "Sei lah que versao ah essa"); // FALTA SABER VERSÃO DO App EMISSOR DO EVENTO

        if ($this->tpevento == "inclusao") {
            $indRetificacao = 1;
        } elseif ($this->tpevento == "alteracao") {
            $indRetificacao = 2;
            $nrRecibo = $numRecibo;
            $nrRecibo = sprintf("%15s", RemoveEspacos($nrRecibo));
        }
        $indRetificacao = sprintf("%01s", $indRetificacao); //1 - ARQUIVO ORIGINAL 2 - ARQUIVO DE RETIFICAÇÃO
        $cpfTrab = sprintf("%11s", RemoveEspacos(RemoveCaracteres($trabalhador['cpf'])));
        $nisTrab = sprintf("%11s", RemoveEspacos(RemoveCaracteres($trabalhador['pis'])));
        $dtAcidente = sprintf("%10s", $cat['dtAcident']);
        $horaAcidente = sprintf("%04s", $cat['horaAcidente']);
        $horasTrabAntesAcidente = sprintf("%04s", $cat['horasTrabAntesAcidente']);
        $tpAcidente = sprintf("%01s", $cat['tpAcidente']);
        $tpCat = sprintf("%01s", $cat['tpCat']);
        $indCatParcial = sprintf("%01s", $cat['indCatParcial']);
        $indCatObito = sprintf("%-1s", $cat['indCatObito']);
        $indComunicPolicia = sprintf("%-1s", $cat['indComunicPolicia']);
        $codSitGeradora = sprintf("%09s", $cat['codSitGeradora']);
        $tpLocal = sprintf("%01s", $cat['tpLocal']);
        $descLocal = sprintf("%-80s", $cat['descLocal']);
        $descLogradouroAcid = sprintf("%-80s", RemoveAcentos($cat['descLogradouro']));
        $nrLogradouroAcid = sprintf("%-10s", $cat['nrLogradouro']);
        $codMunicipioAcid = sprintf('%07s', $cat['codMunicipio']);
        $ufAcid = sprintf("%-2s", $cat['uf']);
        $cnpjLocalAcidente = sprintf('%014s', RemoveCaracteres($cat['cnpjLocalAcidente']));
        $codParteAtingida = sprintf("%09s", $cat['codParteAtingida']);
        $codAgenteCausador = sprintf("%09s", $cat['codAgenteCausador']);

        $codCNES = sprintf("%07s", $cat['codCNES']);
        $dtAtendimento = sprintf("%10s", $cat['dtAtendimento']);
        $hrAtendimento = sprintf("%04s", $cat['hrAtendimento']);
        $indInternacao = sprintf("%-1s", $cat['indInternacao']);
        $durTratamento = sprintf("%04s", $cat['durTratamento']);
        $indAfastamento = sprintf("%-1s", $cat['indAfastamento']);
        $descLesao = sprintf("%-400s", $cat['desLesao']);
        $diagProvavel = sprintf("%-100s", $cat['diagProvavel']);
        $codCid = sprintf("%05s", $cat['codCid']);
        $observacao = sprintf("%-255s", $cat['observacao']);
        $nomeEmitente = sprintf("%-60s", RemoveAcentos($cat['nomeEmitente']));
        $nrOc = sprintf("%014s", $cat['nrOc']);
        $ufOc = sprintf("%-2s", $cat['ufOc']);
        $dtCatOrigem = sprintf("%-10s", $cat['dtCatOrigem']);
        $nrCatOrigem = sprintf("%-15s", $cat['nrCatOrigem']);

        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        $eSocial = $dom->createElement("eSocial");
        $dadosCabecalho['indRetificacao'] = $indRetificacao;
        if ($indRetificacao == 2) {
            //$infnrRecibo = $dom->createElement("nrRecibo", $nrRecibo);  //ac cab
            $dadosCabecalho['nrRecibo'] = $nrRecibo;
        }

        $xmlCabecalho = $this->montaCabecalho($empregador, $dom, "evtCAT", $dadosCabecalho);
        $eSocial->appendChild($xmlCabecalho);
//        $evtCat = $dom->createElement("evtCAT");
//        $ideEvento = $dom->createElement("ideEvento");
//        $infindRetificacao = $dom->createElement("indRetificacao", $indRetificacao);
//        if ($indRetificacao == 2) {
//            $infnrRecibo = $dom->createElement("nrRecibo", $nrRecibo);
//        }
//        $inftpAmb = $dom->createElement("tpAmb", $tpAmb);
//        $infprocEmi = $dom->createElement("procEmi", $procEmi);
//        $infindSeguimento = $dom->createElement("indSeguimento", $indSeguimento);
//        $infverProc = $dom->createElement("verProc", $verProc);
        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricaoMaster = $dom->createElement("tpInscricao", $tpInscricaoMaster);
        $infnrInscricaoMaster = $dom->createElement("nrInscricao", $nrInscricaoMaster);
        $ideTrabalhador = $dom->createElement("ideTrabalhador");
        $infcpfTrab = $dom->createElement("cpfTrab", $cpfTrab);
        $infnisTrab = $dom->createElement("nisTrab", $nisTrab);
        $tagCat = $dom->createElement("cat");
        $infdtAcidente = $dom->createElement("dtAcidente", $dtAcidente);
        $infhoraAcidente = $dom->createElement("horaAcidente", $horaAcidente);
        $infhorasTrabAntesAcidente = $dom->createElement("horasTrabAntesAcidente", $horasTrabAntesAcidente);
        $inftpAcidente = $dom->createElement("tpAcidente", $tpAcidente);
        $inftpCat = $dom->createElement("tpCat", $tpCat);
        $infindCatParcial = $dom->createElement("indCatParcial", $indCatParcial);
        $infindCadObito = $dom->createElement("indCadObito", $indCatObito);
        $infindComunicPolicia = $dom->createElement("indComunicPolicia", $indComunicPolicia);
        $infcodSitGeradora = $dom->createElement("codSitGeradora", $codSitGeradora);
        $inftpLocal = $dom->createElement("tpLocal", $tpLocal);
        $localAcidente = $dom->createElement("localAcidente");
        $infdescLocal = $dom->createElement("descLocal", $descLocal);
        $infdescLogradouroAcid = $dom->createElement("descLogradouro", $descLogradouroAcid);
        $infnrLogradouroAcid = $dom->createElement("nrLogradouro", $nrLogradouroAcid);
        $infcodMunicipioAcid = $dom->createElement("codMunicipio", $codMunicipioAcid);
        $infufAcid = $dom->createElement("ufAcid", $ufAcid);
        $infcnpjLocalAcidente = $dom->createElement("cnpjLocalAcidente", $cnpjLocalAcidente);
        $parteAtingida = $dom->createElement("parteAtingida");
        $infcodParteAtingida = $dom->createElement("codParteAtingida", $codParteAtingida);
        $agenteCausador = $dom->createElement("agenteCausador");
        $infcodAgenteCausador = $dom->createElement("codAgenteCausador", $codAgenteCausador);
        while ($testemunha) {
            $tagtestemunha = $dom->createElement("testemunha");
            $nomeTestemunha = sprintf("%-60s", $testemunha['nomeTestemunha']);
            $descLogradouro = sprintf("%-80s", $testemunha['descLogradouro']);
            $nrLogradouro = sprintf("%10s", $testemunha['nrLogradouro']);
            $bairro = sprintf("%-60s", $testemunha['bairro']);
            $codMunicipio = sprintf("%07s", $testemunha['codMunicipio']);
            $uf = sprintf("%-2s", $testemunha['uf']);
            $cep = sprintf("%08s", $testemunha['cep']);
            $telefone = sprintf("%-13s", $testemunha['telefone']);
            $infnomeTestemunha = $dom->createElement("nomeTestemunha", $nomeTestemunha);
            $infdescLogradouro = $dom->createElement("descLogradouro", $descLogradouro);
            $infnrLogradouro = $dom->createElement("nrLogradouro", $nrLogradouro);
            $infbairro = $dom->createElement("bairro", $bairro);
            $infcodMunicipio = $dom->createElement("codMunicipio", $codMunicipio);
            $infuf = $dom->createElement("uf", $uf);
            $infcep = $dom->createElement("cep", $cep);
            $inftelefone = $dom->createElement("telefone", $telefone);
        }
        $atestado = $dom->createElement("atestado");
        $infcodCnes = $dom->createElement("codCNES", $codCNES);
        $infdtAtendimento = $dom->createElement("dtAtendimento", $dtAtendimento);
        $infhrAtendimento = $dom->createElement("hrAtendimento", $hrAtendimento);
        $infindInternacao = $dom->createElement("indInternacao", $indInternacao);
        $infdurTratamento = $dom->createElement("durTratamento", $durTratamento);
        $infindAfastamento = $dom->createElement("indAfastamento", $indAfastamento);
        $infdescLesao = $dom->createElement("descLesao", $descLesao);
        $infdiagProvavel = $dom->createElement("diagProvavel", $diagProvavel);
        $infcodCid = $dom->createElement("codCID", $codCid);
        $infobservacao = $dom->createElement("observacao", $observacao);
        $emitente = $dom->createElement("emitente");
        $infnomeEmitente = $dom->createElement("nomeEmitente", $nomeEmitente);
        $infnrOc = $dom->createElement("nrOc", $nrOc);
        $infufOc = $dom->createElement("ufOc", $ufOc);
        $catOrigem = $dom->createElement("catOrigem");
        $infdtCatOrigem = $dom->createElement("dtCatOrigem", $dtCatOrigem);
        $infnrCatOrigem = $dom->createElement("nrCatOrigem", $nrCatOrigem);

        $tagCat->appendChild($infdtAcidente);
        $tagCat->appendChild($infhoraAcidente);
        $tagCat->appendChild($infhorasTrabAntesAcidente);
        $tagCat->appendChild($inftpAcidente);
        $tagCat->appendChild($inftpCat);
        $tagCat->appendChild($infindCatParcial);
        $tagCat->appendChild($infindCadObito);
        $tagCat->appendChild($infindComunicPolicia);
        $tagCat->appendChild($infcodSitGeradora);

        $localAcidente->appendChild($inftpLocal);
        $localAcidente->appendChild($infdescLocal);
        $localAcidente->appendChild($infdescLogradouroAcid);
        $localAcidente->appendChild($infnrLogradouroAcid);
        $localAcidente->appendChild($infcodMunicipioAcid);
        $localAcidente->appendChild($infufAcid);
        $localAcidente->appendChild($infcnpjLocalAcidente);
        $tagCat->appendChild($localAcidente);
        $parteAtingida->appendChild($infcodParteAtingida);
        $tagCat->appendChild($parteAtingida);
        $agenteCausador->appendChild($infcodAgenteCausador);
        $tagCat->appendChild($agenteCausador);
        while ($testemunha) {
            $tagtestemunha = $dom->createElement("testemunha");
            $nomeTestemunha = sprintf("%-60s", $testemunha['nomeTestemunha']);
            $descLogradouro = sprintf("%-80s", $testemunha['descLogradouro']);
            $nrLogradouro = sprintf("%10s", $testemunha['nrLogradouro']);
            $bairro = sprintf("%-60s", $testemunha['bairro']);
            $codMunicipio = sprintf("%07s", $testemunha['codMunicipio']);
            $uf = sprintf("%-2s", $testemunha['uf']);
            $cep = sprintf("%08s", $testemunha['cep']);
            $telefone = sprintf("%-13s", $testemunha['telefone']);
            $infnomeTestemunha = $dom->createElement("nomeTestemunha", $nomeTestemunha);
            $infdescLogradouro = $dom->createElement("descLogradouro", $descLogradouro);
            $infnrLogradouro = $dom->createElement("nrLogradouro", $nrLogradouro);
            $infbairro = $dom->createElement("bairro", $bairro);
            $infcodMunicipio = $dom->createElement("codMunicipio", $codMunicipio);
            $infuf = $dom->createElement("uf", $uf);
            $infcep = $dom->createElement("cep", $cep);
            $inftelefone = $dom->createElement("telefone", $telefone);
            $tagtestemunha->appendChild($infnomeTestemunha);
            $tagtestemunha->appendChild($infdescLogradouro);
            $tagtestemunha->appendChild($infnrLogradouro);
            $tagtestemunha->appendChild($infbairro);
            $tagtestemunha->appendChild($infcodMunicipio);
            $tagtestemunha->appendChild($infuf);
            $tagtestemunha->appendChild($infcep);
            $tagtestemunha->appendChild($telefone);
            $tagCat->appendChild($tagtestemunha);
        }
        $atestado->appendChild($infcodCnes);
        $atestado->appendChild($infdtAtendimento);
        $atestado->appendChild($infhrAtendimento);
        $atestado->appendChild($infindInternacao);
        $atestado->appendChild($infdurTratamento);
        $atestado->appendChild($infindAfastamento);
        $atestado->appendChild($infdescLesao);
        $atestado->appendChild($infdiagProvavel);
        $atestado->appendChild($infcodCid);
        $atestado->appendChild($infobservacao);
        $tagCat->appendChild($atestado);
        $emitente->appendChild($infnomeEmitente);
        $emitente->appendChild($infnrOc);
        $emitente->appendChild($infufOc);
        $tagCat->appendChild($emitente);
        $catOrigem->appendChild($infdtCatOrigem);
        $catOrigem->appendChild($infnrCatOrigem);
        $tagCat->appendChild($catOrigem);

        $ideTrabalhador->appendChild($infcpfTrab);
        $ideTrabalhador->appendChild($infnisTrab);
        $ideEmpregador->appendChild($inftpInscricaoMaster);
        $ideEmpregador->appendChild($infnrInscricaoMaster);
//        $ideEvento->appendChild($infindRetificacao);
//        if ($indRetificacao == 2) {
//            $ideEvento->appendChild($infnrRecibo);
//        }
//
//        $ideEvento->appendChild($inftpAmb);
//        $ideEvento->appendChild($infprocEmi);
//        $ideEvento->appendChild($infindSeguimento);
//        $ideEvento->appendChild($infverProc);
//        $evtCat->appendChild($ideEvento);
        $xmlCabecalho->appendChild($ideEmpregador);
        $xmlCabecalho->appendChild($ideTrabalhador);
        $xmlCabecalho->appendChild($tagCat);
//        $evtCat->setAttribute("Id", $id);
//        $evtCat->setAttribute("versao", $versao);
        $eSocial->appendChild($xmlCabecalho);
        $dom->appendChild($eSocial);
        $this->sequencial++;

//        echo $dom->saveXML();
        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

//EVENTO ATESTADO DE SAÚDE OCUPACIONAL    
    public function montas2280($arquivo, $empregador, $trabalhador, $aso, $resultMonitoracao) {
        $tpInscricaoMaster = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricaoMaster = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));
//        $dt_hs_atual = str_replace(" ", "", RemoveCaracteres(date("Y-m-d H:i:s", time())));
//        $sequencial = sprintf("%05s", $this->sequencial);
//
//        $id = sprintf("%-36s", "ID" . $tpInscricaoMaster . $nrInscricaoMaster . $dt_hs_atual . $sequencial);
//        $versao = sprintf("%-11s", "1.2 - beta2"); // VERSÃO DO LAYOUT
//        $tpAmb = sprintf("%01s", 1); // CONSTANTE
//        $procEmi = sprintf("%01s", 1); // CONSTANTE
//        $indSeguimento = sprintf("%01s", 1); // CONSTANTE
//        $verProc = sprintf("%-20s", "Sei lah que versao ah essa"); // FALTA SABER VERSÃO DO App EMISSOR DO EVENTO

        if ($this->tpevento == "inclusao") {
            $indRetificacao = 1;
        } elseif ($this->tpevento == "alteracao") {
            $indRetificacao = 2;
            $nrRecibo = $numRecibo;
            $nrRecibo = sprintf("%15s", RemoveEspacos($nrRecibo));
        }
        $indRetificacao = sprintf("%01s", $indRetificacao); //1 - ARQUIVO ORIGINAL 2 - ARQUIVO DE RETIFICAÇÃO
        $cpfTrab = sprintf("%11s", RemoveEspacos(RemoveCaracteres($trabalhador['cpf'])));
        $nisTrab = sprintf("%11s", RemoveEspacos(RemoveCaracteres($trabalhador['pis'])));
        $matricula = sprintf("%-30s", RemoveEspacos($trabalhador['id_trab']));
        $dtAso = sprintf("%-10s", $aso['dtAso']);
        $tpAso = sprintf("%01s", $aso['tpAso']);
        $resultadoAso = sprintf("%01s", $aso['resultadoAso']);
        $dtExame = sprintf("%-10s", $aso['diExame']);
        $descExame = sprintf("%-80s", RemoveAcentos($aso['descExame']));
        $codAgente = sprintf("%-6s", $aso['codAgente']); // TABELA 7
        $codAgenteQuimico = sprintf("%-2s", $resultMonitoracao['codAgenteQuimico']);
        $matBiologico = sprintf("%01s", $resultMonitoracao['matBiologico']); // TABELA 21
        $codAnalise = sprintf("%-4s", $resultMonitoracao['codAnalise']);
        $expExcessiva = sprintf("%-1s", $resultMonitoracao['expExcessiva']);
        $ordemExame = sprintf("%01s", $resultMonitoracao['ordemExame']);
        $indResultado = sprintf("%01s", $resultMonitoracao['indResultado']);
        $dtIniMonitoracao = sprintf("%-10s", $resultMonitoracao['dtIniMonitoracao']);
        $dtFimMonitoracao = sprintf("%-10s", $resultMonitoracao['dtFimMonitoracao']);
        $nisResponsavel = sprintf("%01s", $resultMonitoracao['nisResponsavel']);
        $nrCrmResp = sprintf("%-1s", $resultMonitoracao['nrCrmResp']);
        $ufCrmResp = sprintf("%-2s", $resultMonitoracao['ufCrmResp']);
        $nomeMedico = sprintf("%-60s", $resultMonitoracao['nomeMedico']);
        $foneContato = sprintf("%-13s", $resultMonitoracao['foneContato']);
        $nrCrm = sprintf("%-1s", $resultMonitoracao['nrCrm']);
        $ufCrm = sprintf("%-2s", $resultMonitoracao['ufCrm']);

        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        $eSocial = $dom->createElement("eSocial");
        $dadosCabecalho['indRetificacao'] = $indRetificacao;
        if ($indRetificacao == 2) {
            //$infnrRecibo = $dom->createElement("nrRecibo", $nrRecibo);  //ac cab
            $dadosCabecalho['nrRecibo'] = $nrRecibo;
        }

        $xmlCabecalho = $this->montaCabecalho($empregador, $dom, "evtASO", $dadosCabecalho);
        $eSocial->appendChild($xmlCabecalho);
//        $evtASO = $dom->createElement("evtASO");
//        $ideEvento = $dom->createElement("ideEvento");
//        $infindRetificacao = $dom->createElement("indRetificacao", $indRetificacao);
//        if ($indRetificacao == 2) {
//            $infnrRecibo = $dom->createElement("nrRecibo", $nrRecibo);
//        }
//        $inftpAmb = $dom->createElement("tpAmb", $tpAmb);
//        $infprocEmi = $dom->createElement("procEmi", $procEmi);
//        $infindSeguimento = $dom->createElement("indSeguimento", $indSeguimento);
//        $infverProc = $dom->createElement("verProc", $verProc);
        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricaoMaster = $dom->createElement("tpInscricao", $tpInscricaoMaster);
        $infnrInscricaoMaster = $dom->createElement("nrInscricao", $nrInscricaoMaster);
        $ideVinculo = $dom->createElement("ideVinculo");
        $infcpfTrab = $dom->createElement("cpfTrab", $cpfTrab);
        $infnisTrab = $dom->createElement("nisTrab", $nisTrab);
        $infmatricula = $dom->createElement("matricula", $matricula);
        $tagAso = $dom->createElement("aso");
        $infdtAso = $dom->createElement("dtAso", $dtAso);
        $inftpAso = $dom->createElement("tpAso", $tpAso);
        $infresultadoAso = $dom->createElement("resultadoAso", $resultadoAso);
        $exame = $dom->createElement("exame");
        $infdtExame = $dom->createElement("dtExame", $dtExame);
        $infdescExame = $dom->createElement("descExame", $descExame);
        $risco = $dom->createElement("risco");
        $infcodAgente = $dom->createElement("codAgente", $codAgente);
        $monitBiologica = $dom->createElement("monitBiologica");
        $tagResultMonitoracao = $dom->createElement("resultMonitoracao");
        $infcodAgenteQuimico = $dom->createElement("codAgenteQuimico", $codAgenteQuimico);
        $infmatBiologico = $dom->createElement("matBiologico", $matBiologico);
        $infcodAnalise = $dom->createElement("codAnalise", $codAnalise);
        $infexpExcessiva = $dom->createElement("expeExcessiva", $expExcessiva);
        $infordemExame = $dom->createElement("ordemExame", $ordemExame);
        $infindResultado = $dom->createElement("indResultado", $indResultado);
        $infdtIniMonitoracao = $dom->createElement("dtIniMonitoracao", $dtIniMonitoracao);
        $infdtFimMonitoracao = $dom->createElement("dtFimMonitoracao", $dtFimMonitoracao);
        $respMonitoracao = $dom->createElement("respMonitoracao");
        $infnisResponsavel = $dom->createElement("nisResponsavel", $nisResponsavel);
        $infnrCrmResp = $dom->createElement("nrCRM", $nrCrmResp);
        $infufCrmResp = $dom->createElement("ufCRM", $ufCrmResp);
        $medico = $dom->createElement("medico");
        $infnomeMedico = $dom->createElement("nomeMedico", $nomeMedico);
        $inffoneContato = $dom->createElement("foneContato", $foneContato);
        $crm = $dom->createElement("crm");
        $infnrCrm = $dom->createElement("nrCRM", $nrCrm);
        $infufCrm = $dom->createElement("ufCRM", $ufCrm);

        $tagAso->appendChild($infdtAso);
        $tagAso->appendChild($inftpAso);
        $tagAso->appendChild($infresultadoAso);
        $exame->appendChild($infdtExame);
        $exame->appendChild($infdescExame);
        $tagAso->appendChild($exame);
        $risco->appendChild($infcodAgente);
        $tagAso->appendChild($risco);
        $tagResultMonitoracao->appendChild($infcodAgenteQuimico);
        $tagResultMonitoracao->appendChild($infmatBiologico);
        $tagResultMonitoracao->appendChild($infcodAnalise);
        $tagResultMonitoracao->appendChild($infexpExcessiva);
        $tagResultMonitoracao->appendChild($infordemExame);
        $tagResultMonitoracao->appendChild($infindResultado);
        $tagResultMonitoracao->appendChild($infdtIniMonitoracao);
        $tagResultMonitoracao->appendChild($infdtFimMonitoracao);
        $monitBiologica->appendChild($tagResultMonitoracao);
        $respMonitoracao->appendChild($infnisResponsavel);
        $respMonitoracao->appendChild($infnrCrmResp);
        $respMonitoracao->appendChild($infufCrmResp);
        $monitBiologica->appendChild($respMonitoracao);
        $tagAso->appendChild($monitBiologica);
        $medico->appendChild($infnomeMedico);
        $medico->appendChild($inffoneContato);
        $crm->appendChild($infnrCrm);
        $crm->appendChild($infufCrm);
        $medico->appendChild($crm);
        $tagAso->appendChild($medico);
        $ideVinculo->appendChild($infcpfTrab);
        $ideVinculo->appendChild($infnisTrab);
        $ideVinculo->appendChild($infmatricula);
        $ideEmpregador->appendChild($inftpInscricaoMaster);
        $ideEmpregador->appendChild($infnrInscricaoMaster);
//        $ideEvento->appendChild($infindRetificacao);
//        if ($indRetificacao == 2) {
//            $ideEvento->appendChild($infnrRecibo);
//        }
//
//        $ideEvento->appendChild($inftpAmb);
//        $ideEvento->appendChild($infprocEmi);
//        $ideEvento->appendChild($infindSeguimento);
//        $ideEvento->appendChild($infverProc);
//        $evtASO->appendChild($ideEvento);
        $xmlCabecalho->appendChild($ideEmpregador);
        $xmlCabecalho->appendChild($ideVinculo);
        $xmlCabecalho->appendChild($tagAso);
//        $evtASO->setAttribute("Id", $id);
//        $evtASO->setAttribute("versao", $versao);
        $eSocial->appendChild($xmlCabecalho);
        $dom->appendChild($eSocial);
        $this->sequencial++;

//        echo $dom->saveXML();
        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

//EVENTO AFASTAMENTO TEMPORARIO
//EVENTO ALTERACAO DE MITIVO DE AFASTAMENTO
//EVENTO RETORNO DE AFASTAMENTO TEMPORÁRIO
//EVENTO ESTABILIDADE INÍCIO
//EVENTO ESTABILIDADE TÉRMINO
    public function montas2320a2345($arquivo, $empregador, $trabalhador, $numRecibo, $evento, $estabilidade) {
        switch ($evento) {
            case "2320":
                $evt = "evtAfastTemp";
                $info = "infoAfastamento";
                break;
            case "2325":
                $evt = "evtAltMotAfast";
                $info = "infoAltMotivo";
                break;
            case "2330":
                $evt = "evtAfastRetorno";
                $info = "infoRetorno";
                break;
            case "2340":
                $evt = "evtEstabInicio";
                $info = "infoEstabInicio";
                break;
        }

        $tpInscricaoMaster = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricaoMaster = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));

        if ($this->tpevento == "inclusao") {
            $indRetificacao = 1;
        } elseif ($this->tpevento == "alteracao") {
            $indRetificacao = 2;
            $nrRecibo = $numRecibo;
            $nrRecibo = sprintf("%15s", RemoveEspacos($nrRecibo));
        }
        $indRetificacao = sprintf("%01s", $indRetificacao); //1 - ARQUIVO ORIGINAL 2 - ARQUIVO DE RETIFICAÇÃO
        $cpfTrab = sprintf("%11s", RemoveEspacos(RemoveCaracteres($trabalhador['cpf'])));
        $nisTrab = sprintf("%11s", RemoveEspacos(RemoveCaracteres($trabalhador['pis'])));
        $matricula = sprintf("%-30s", RemoveEspacos($trabalhador['id_trab']));
        //Inicio Afastamento
        $dtAfastamento = sprintf("%-10s", $trabalhador['data']);
        $codMotAfastamento = sprintf("%02s", $trabalhador['codMotAfastamento']);
//        $tpAcidenteTransito = sprintf("%01s", $afastamento['tpAcidenteTransito']);
//        $observacao = sprintf("%-255s", $afastamento['observacao']);
//        $codCID = sprintf("%-5s", $afastamento['codCID']);
        $qtdDiasAfastamento = sprintf("%03s", $trabalhador['dias']);
//        $nomeEmitente = sprintf("%011s", $afastamento['nomeEmitente']);
//        $nrOc = sprintf("%08s", $afastamento['nrOc']);
//        $ufOc = sprintf("%-2s", $afastamento['ufOc']);
//        $cnpjCessionario = sprintf("%-60s", $afastamento['cnpjCessionario']);
//        $infoOnus = sprintf("%-13s", $afastamento['infoOnus']);
//        $cnpjSindicato = sprintf("%014s", $afastamento['cnpjSindicato']);
//        $infoOnusRemuneracao = sprintf("%01s", $afastamento['infoOnusRemuneracao']);
//Alteracao Motivo Afastamento
        $dtAltMotivo = sprintf("%-10s", $trabalhador['data_mod']);
        $codMotivoAnterior = sprintf("%02s", $trabalhador['codMotAfastamentoAnterior']);
        $codMotAfastamentoAtl = sprintf("%02s", $trabalhador['codMotAfastamentoNovo']);
        $indEfeitoRetroativo = sprintf("%-1s", $trabalhador['indEfeitoRetroativo']);
        //Retorno Afastamento
        $dtRetorno = sprintf("%-10s", $trabalhador['data_retorno']);
//        $observacaoRetorno = sprintf("%-60s", $trabalhador['observacaoRetorno']);
        //Inicio Estabilidade
        $dtIniEstabilidade = sprintf("%-10s", $estabilidade['dtIniEstabilidade']);
        $codMotivoEstabilidade = sprintf("%02s", $estabilidade['$codMotEstabilidade']);
        $observacaoEstabilidade = sprintf("%-255s", $estabilidade['observacaoEstabilidade']);
        //Fim Estabilidade
        $dtFimEstabilidade = sprintf("%-10s", $estabilidade['dtFimEstabilidade']);
        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        $eSocial = $dom->createElement("eSocial");
        $dadosCabecalho['indRetificacao'] = $indRetificacao;
        if ($indRetificacao == 2) {
            //$infnrRecibo = $dom->createElement("nrRecibo", $nrRecibo);  //ac cab
            $dadosCabecalho['nrRecibo'] = $nrRecibo;
        }

        $xmlCabecalho = $this->montaCabecalho($empregador, $dom, $evt, $dadosCabecalho);
        $eSocial->appendChild($xmlCabecalho);
        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricaoMaster = $dom->createElement("tpInscricao", $tpInscricaoMaster);
        $infnrInscricaoMaster = $dom->createElement("nrInscricao", $nrInscricaoMaster);

        $ideVinculo = $dom->createElement("ideVinculo");
        $infcpfTrab = $dom->createElement("cpfTrab", $cpfTrab);
        $infnisTrab = $dom->createElement("nisTrab", $nisTrab);
        $infmatricula = $dom->createElement("matricula", $matricula);
        $info = $dom->createElement($info);

        switch ($evento) {
            case "2320":
                $infdtAfastamento = $dom->createElement("dtAfastamento", $dtAfastamento);
                $infcodMotAfastamento = $dom->createElement("codMotAfastamento", $codMotAfastamento);
//                $inftpAcidenteTransito = $dom->createElement("tpAcidenteTransito", $tpAcidenteTransito);
//                $infobservacao = $dom->createElement("observacao", $observacao);
                $infoAtestado = $dom->createElement("infoAtestado");
//                $infcodCID = $dom->createElement("codCID", $codCID);
                $infqtdDiasAfastamento = $dom->createElement("qtdDiasAfastamento", $qtdDiasAfastamento);
                $emitente = $dom->createElement("emitente");
//                $infnomeEmitente = $dom->createElement("nomeEmitente", $nomeEmitente);
//                $infnrOc = $dom->createElement("nrOc", $nrOc);
//                $infufOc = $dom->createElement("ufOc", $ufOc);
                //        $infoCessao = $dom->createElement("infoCessao");
                //        $infcnpjCessionario = $dom->createElement("cnpjCessionario", $cnpjCessionario);
                //        $infinfoOnus = $dom->createElement("infoOnus", $infoOnus);
                //        $infoMandadoSindical = $dom->createElement("infoMandadoSindical");
                //        $infcnpjSindicato = $dom->createElement("cnpjSindicato", $cnpjSindicato);
                //        $infinfoOnusRemuneracao = $dom->createElement("infoOnusRemuneracao", $infoOnusRemuneracao);        

                break;
            case "2325":
                $infdtAltMotivo = $dom->createElement("dtAltMotivo", $dtAltMotivo);
                $infcodMotivoAnterior = $dom->createElement("codMotivoAnterior", $codMotivoAnterior);
                $infcodMotivoAfastamento = $dom->createElement("codMotAfastamento", $codMotAfastamentoAtl);
                $infindEfeitoRetroativo = $dom->createElement("indEfeitoRetroativo", $indEfeitoRetroativo);
                break;
            case "2330":
                $infdtRetorno = $dom->createElement("dtRetorno", $dtRetorno);
                $infcodMotAfastamento = $dom->createElement("codMotAfastamento", $codMotAfastamento);
//                $infobservacaoRetorno = $dom->createElement("observacao", $observacaoRetorno);
                break;
            case "2340":
                $infdtIniEstabilidade = $dom->createElement("dtIniEstabilidade", $dtIniEstabilidade);
                $infcodMotivoEstabilidade = $dom->createElement("codMotivoEstabilidade", $codMotivoEstabilidade);
                $infobservacaoEstabilidade = $dom->createElement("observacaoEstabilidade", $observacaoEstabilidade);
                break;
            case "2345":
                $infdtFimEstabilidade = $dom->createElement("dtFimEstabilidade", $dtFimEstabilidade);
                $infcodMotivoEstabilidade = $dom->createElement("codMotivoEstabilidade", $codMotivoEstabilidade);
                $infobservacaoEstabilidade = $dom->createElement("observacaoEstabilidade", $observacaoEstabilidade);
                break;
        }
        switch ($evento) {
            case "2320":
                $info->appendChild($infdtAfastamento);
                $info->appendChild($infcodMotAfastamento);
//                $info->appendChild($inftpAcidenteTransito);
//                $info->appendChild($infobservacao);
//                $infoAtestado->appendChild($infcodCID);
                $infoAtestado->appendChild($infqtdDiasAfastamento);
//                $emitente->appendChild($infnomeEmitente);
//                $emitente->appendChild($infnrOc);
//                $emitente->appendChild($infufOc);
                $infoAtestado->appendChild($emitente);
                $info->appendChild($infoAtestado);
                //        $infoCessao->appnedChild($infcnpjCessionario);
                //        $infoCessao->appnedChild($infinfoOnus);
                //        $info->appendChild($infoCessao);
                //        $infoMandadoSindical->appendChild($infcnpjSindicato);
                //        $infoMandadoSindical->appendChild($infinfoOnusRemuneracao);
                //        $info->appendChild($infoMandadoSindical);
                break;
            case "2325":
                $info->appendChild($infdtAltMotivo);
                $info->appendChild($infcodMotivoAnterior);
                $info->appendChild($infcodMotivoAfastamento);
                $info->appendChild($infindEfeitoRetroativo);
                break;
            case "2330":
                $info->appendChild($infdtRetorno);
                $info->appendChild($infcodMotAfastamento);
//                $info->appendChild($infobservacaoRetorno);
                break;
            case "2340":
                $info->appendChild($infdtIniEstabilidade);
                $info->appendChild($infcodMotivoEstabilidade);
                $info->appendChild($infobservacaoEstabilidade);
                break;
            case "2345":
                $info->appendChild($infdtFimEstabilidade);
                $info->appendChild($infcodMotivoEstabilidade);
                $info->appendChild($infobservacaoEstabilidade);
                break;
        }
        $ideVinculo->appendChild($infcpfTrab);
        $ideVinculo->appendChild($infnisTrab);
        $ideVinculo->appendChild($infmatricula);
        $ideEmpregador->appendChild($inftpInscricaoMaster);
        $ideEmpregador->appendChild($infnrInscricaoMaster);
        $xmlCabecalho->appendChild($ideEmpregador);
        $xmlCabecalho->appendChild($ideVinculo);
        $xmlCabecalho->appendChild($info);
        $eSocial->appendChild($xmlCabecalho);
        $dom->appendChild($eSocial);
        $this->sequencial++;
        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

//EVENTO CONDICAO DIFERENCIADA DE TRABALHO - INICIO   
//EVENTO CONDICAO DIFERENCIADA DE TRABALHO - FIM   

    public function montas2360a2365($arquivo, $empregador, $trabalhador, $tpCond, $risco, $numRecibo, $evento) {

        switch ($evento) {
            case "2360":
                $evt = "evtCDTInicio";
                $info = "infoCDTInicio";
                break;

            case "2365":
                $evt = "evtCDTTermino";
                $info = "infoCDTTermino";
                break;
        }


        $tpInscricaoMaster = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricaoMaster = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));
        /* $dt_hs_atual = str_replace(" ", "", RemoveCaracteres(date("Y-m-d H:i:s", time())));
          $sequencial = sprintf("%05s", $this->sequencial);

          $id = sprintf("%-36s", "ID" . $tpInscricaoMaster . $nrInscricaoMaster . $dt_hs_atual . $sequencial);
          $versao = sprintf("%-11s", "1.2 - beta2"); // VERSÃO DO LAYOUT
          $tpAmb = sprintf("%01s", 1); // CONSTANTE
          $procEmi = sprintf("%01s", 1); // CONSTANTE
          $indSeguimento = sprintf("%01s", 1); // CONSTANTE
          $verProc = sprintf("%-20s", "Sei lah que versao ah essa"); // FALTA SABER VERSÃO DO App EMISSOR DO EVENTO
         */

        if ($this->tpevento == "inclusao") {
            $indRetificacao = 1;
        } elseif ($this->tpevento == "alteracao") {
            $indRetificacao = 2;
            $nrRecibo = $numRecibo;
            $nrRecibo = sprintf("%15s", RemoveEspacos($nrRecibo));
        }
        $indRetificacao = sprintf("%01s", $indRetificacao); //1 - ARQUIVO ORIGINAL 2 - ARQUIVO DE RETIFICAÇÃO
        $cpfTrab = sprintf("%11s", RemoveEspacos(RemoveCaracteres($trabalhador['cpf'])));
        $nisTrab = sprintf("%11s", RemoveEspacos(RemoveCaracteres($trabalhador['pis'])));
        $matricula = sprintf("%-30s", RemoveEspacos($trabalhador['id_trab']));
        //CONDIAÇÃO DIFERENCIADA DE TRABALHO - INICIO
        $dtIniCondicao = sprintf("%-10s", $trabalhador['data_entrada']);
        $tpCondicao = sprintf("%02s", $tpCond);
        $codAgente = sprintf("%-6s", $risco['codAgente']);
        $intesidadConcentracao = sprintf("%-15s", $risco['intensidadConcentracao']);
        $tecMedicao = sprintf("%-40s", $risco['tecMedicao']);
        $utilizacaoEPC = sprintf("%01s", $risco['utilizacaoEPC']);
        $utilizacaoEPI = sprintf("%01s", $risco['utilizacaoEPI']);
        $caEPI = sprintf("%-20s", $risco['caEPI']);
        $medProtecao = sprintf("%-1s", $risco['medProtecao']);
        $condFuncionamento = sprintf("%-1s", $risco['condFuncionamento']);
        $prazoValidade = sprintf("%-1s", $risco['prazoValidade']);
        $periodicTroca = sprintf("%-1s", $risco['priodicTroca']);
        $higienizacao = sprintf("%-1s", $risco['higienizacao']);

        //CONDIAÇÃO DIFERENCIADA DE TRABALHO - FIM
        $dtFimCondicao = sprintf("%-10s", $trabalhador['data_proc']);

        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        $eSocial = $dom->createElement("eSocial");
        //aki

        $dadosCabecalho['indRetificacao'] = $indRetificacao;
        if ($indRetificacao == 2) {
            //$infnrRecibo = $dom->createElement("nrRecibo", $nrRecibo);  //ac cab
            $dadosCabecalho['nrRecibo'] = $nrRecibo;
        }

        $xmlCabecalho = $this->montaCabecalho($empregador, $dom, $evt, $dadosCabecalho);



        //$evt = $dom->createElement($evt);
        //$ideEvento = $dom->createElement("ideEvento");
        //$infindRetificacao = $dom->createElement("indRetificacao", $indRetificacao); //ac cab

        /*
          $inftpAmb = $dom->createElement("tpAmb", $tpAmb);
          $infprocEmi = $dom->createElement("procEmi", $procEmi);
          $infindSeguimento = $dom->createElement("indSeguimento", $indSeguimento);
          $infverProc = $dom->createElement("verProc", $verProc); */


        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricaoMaster = $dom->createElement("tpInscricao", $tpInscricaoMaster);
        $infnrInscricaoMaster = $dom->createElement("nrInscricao", $nrInscricaoMaster);
        $ideVinculo = $dom->createElement("ideVinculo");

        $infcpfTrab = $dom->createElement("cpfTrab", $cpfTrab);
        $infnisTrab = $dom->createElement("nisTrab", $nisTrab);
        $infmatricula = $dom->createElement("matricula", $matricula);

        $info = $dom->createElement($info);
        $infdtIniCondicao = $dom->createElement("dtIniCondicao", $dtIniCondicao);
        $infdtFimCondicao = $dom->createElement("dtFimCondicao", $dtFimCondicao);
        $inftpCondicao = $dom->createElement("tpCondicao", $tpCondicao);
        $fatoresRisco = $dom->createElement("fatoresRisco");
        $infcodAgente = $dom->createElement("codAgente", $codAgente);

        $infintesidadConcentracao = $dom->createElement("intesidadConcentracao", $intesidadConcentracao);
        $inftecMedicao = $dom->createElement("tecMedicao", $tecMedicao);
        $infutilizacaoEPC = $dom->createElement("utilizacaoEPC", $utilizacaoEPC);
        $infutilizacaoEPI = $dom->createElement("utilizacaoEPI", $utilizacaoEPI);
        $epi = $dom->createElement("epi");
        $infcaEPI = $dom->createElement("caEPI", $caEPI);
        $requisitosEPI = $dom->createElement("requisitosEPI");
        $infmedProtecao = $dom->createElement("medProtecao", $medProtecao);
        $infcondFuncionamento = $dom->createElement("condFuncionamento", $condFuncionamento);
        $infprazoValidade = $dom->createElement("prazoValidade", $prazoValidade);
        $infperiodicTroca = $dom->createElement("periodicTroca", $periodicTroca);
        $infhigienizacao = $dom->createElement("higienizacao", $higienizacao);


        if ($evento == "2360") {
            $info->appendChild($infdtIniCondicao);
        } else {
            $info->appendChild($infdtFimCondicao);
        }
        $info->appendChild($inftpCondicao);
        if ($tpCondicao == '03') {
            $fatoresRisco->appendChild($infcodAgente);
            if ($evento == "2360") {
                $fatoresRisco->appendChild($infintesidadConcentracao);
                $fatoresRisco->appendChild($inftecMedicao);
                $fatoresRisco->appendChild($infutilizacaoEPC);
                $fatoresRisco->appendChild($infutilizacaoEPI);
                $epi->appendChild($infcaEPI);
                $fatoresRisco->appendChild($epi);
            }
            $info->appendChild($fatoresRisco);

            if ($evento == "2360") {
                $requisitosEPI->appendChild($infmedProtecao);
                $requisitosEPI->appendChild($infcondFuncionamento);
                $requisitosEPI->appendChild($infprazoValidade);
                $requisitosEPI->appendChild($infperiodicTroca);
                $requisitosEPI->appendChild($infhigienizacao);
                $info->appendChild($requisitosEPI);
            }
        }
        $ideVinculo->appendChild($infcpfTrab);
        $ideVinculo->appendChild($infnisTrab);
        $ideVinculo->appendChild($infmatricula);
        $ideEmpregador->appendChild($inftpInscricaoMaster);
        $ideEmpregador->appendChild($infnrInscricaoMaster);

        /* $ideEvento->appendChild($infindRetificacao);
          if ($indRetificacao == 2) {
          $ideEvento->appendChild($infnrRecibo);
          }
          $ideEvento->appendChild($inftpAmb);
          $ideEvento->appendChild($infprocEmi);
          $ideEvento->appendChild($infindSeguimento);
          $ideEvento->appendChild($infverProc); */

        //$evt->appendChild($ideEvento);
        $xmlCabecalho->appendChild($ideEmpregador);
        $xmlCabecalho->appendChild($ideVinculo);
        $xmlCabecalho->appendChild($info);

        $eSocial->appendChild($xmlCabecalho);
        $dom->appendChild($eSocial);
        $this->sequencial++;

//        echo $dom->saveXML();
        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

    public function montas2400e2405($arquivo, $empregador, $trabalhador, $numRecibo, $evento) {
        switch ($evento) {
            case "2400":
                $evt = "evtAvisoPrevio";
                $info = "infoAvisoPrevio";
                break;

            case "2405":
                $evt = "evtCancAvisoPrevio";
                $info = "infoCancAvisoPrevio";
                break;
        }
        $tpInscricaoMaster = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricaoMaster = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));

        if ($this->tpevento == "inclusao") {
            $indRetificacao = 1;
        } elseif ($this->tpevento == "alteracao") {
            $indRetificacao = 2;
            $nrRecibo = $numRecibo;
            $nrRecibo = sprintf("%15s", RemoveEspacos($nrRecibo));
        }
        $indRetificacao = sprintf("%01s", $indRetificacao); //1 - ARQUIVO ORIGINAL 2 - ARQUIVO DE RETIFICAÇÃO
        $cpfTrab = sprintf("%11s", RemoveEspacos(RemoveCaracteres($trabalhador['cpf'])));
        $nisTrab = sprintf("%11s", RemoveEspacos(RemoveCaracteres($trabalhador['pis'])));
        $matricula = sprintf("%-30s", RemoveEspacos($trabalhador['id_trab']));
        $dtAvisoPrevio = sprintf("%-10s", $trabalhador['data_aviso']);
        $dtPrevDesligamento = sprintf("%-10s", $trabalhador['data_demi']);
        $dtCancAvisoPrevio = sprintf("%-10s", $trabalhador['data_proc']);
        $motivoCancAvisoPrevio = sprintf("%01s", $trabalhador['codAvicoPre']);
        $tpAvisoPrevio = sprintf("%01s", $trabalhador['codAvicoPre']);
        $observacao = sprintf("%-255s", $trabalhador['obs']);

        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        $eSocial = $dom->createElement("eSocial");
        $dadosCabecalho['indRetificacao'] = $indRetificacao;
        if ($indRetificacao == 2) {
            //$infnrRecibo = $dom->createElement("nrRecibo", $nrRecibo);  //ac cab
            $dadosCabecalho['nrRecibo'] = $nrRecibo;
        }

        $xmlCabecalho = $this->montaCabecalho($empregador, $dom, $evt, $dadosCabecalho);

        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricaoMaster = $dom->createElement("tpInscricao", $tpInscricaoMaster);
        $infnrInscricaoMaster = $dom->createElement("nrInscricao", $nrInscricaoMaster);
        $ideVinculo = $dom->createElement("ideVinculo");
        $infcpfTrab = $dom->createElement("cpfTrab", $cpfTrab);
        $infnisTrab = $dom->createElement("nisTrab", $nisTrab);
        $infmatricula = $dom->createElement("matricula", $matricula);
        $info = $dom->createElement($info);
        $infdtAvisoPrevio = $dom->createElement("dtAvisoPrevio", $dtAvisoPrevio);

        $infdtPrevDesligamento = $dom->createElement("dtPrevDesligamento", $dtPrevDesligamento);
        $inftpAvisoPrevio = $dom->createElement("tpAvisoPrevio", $tpAvisoPrevio);
        $infodtCancAvisoPrevio = $dom->createElement("dtCancAvisoPrevio", $dtCancAvisoPrevio);
        $infoObservacao = $dom->createElement("observacao", $observacao);
        $infmotivoCancAvisoPrevio = $dom->createElement("motivoCancAvisoPrevio", $motivoCancAvisoPrevio);

        switch ($evento) {
            case "2400":
                $info->appendChild($infdtAvisoPrevio);
                $info->appendChild($infdtPrevDesligamento);
                $info->appendChild($inftpAvisoPrevio);
                $info->appendChild($infoObservacao);
                break;

            case "2405":
                $info->appendChild($infodtCancAvisoPrevio);
                $info->appendChild($infoObservacao);
                $info->appendChild($infmotivoCancAvisoPrevio);
                break;
        }
        $ideVinculo->appendChild($infcpfTrab);
        $ideVinculo->appendChild($infnisTrab);
        $ideVinculo->appendChild($infmatricula);
        $ideEmpregador->appendChild($inftpInscricaoMaster);
        $ideEmpregador->appendChild($infnrInscricaoMaster);

        $xmlCabecalho->appendChild($ideEmpregador);
        $xmlCabecalho->appendChild($ideVinculo);
        $xmlCabecalho->appendChild($info);

        $eSocial->appendChild($xmlCabecalho);
        $dom->appendChild($eSocial);
        $this->sequencial++;

        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

    public function montas2600($arquivo, $empregador, $trabalhador, $dependente, $numRecibo, $evento, $estagio = null) {
        switch ($evento) {
            case "2600":
                $evt = "evtTSVInicio";
                $info = "infoTSVInicio";
                break;

            case "2620":
                $evt = "evtTSVAltContratual";
                $info = "infoTSVAlteracao";
                break;
        }

        $tpInscricaoMaster = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricaoMaster = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));

        if ($this->tpevento == "inclusao") {
            $indRetificacao = 1;
        } elseif ($this->tpevento == "alteracao") {
            $indRetificacao = 2;
            $nrRecibo = $numRecibo;
            $nrRecibo = sprintf("%15s", RemoveEspacos($nrRecibo));
        }
        $indRetificacao = sprintf("%01s", $indRetificacao); //1 - ARQUIVO ORIGINAL 2 - ARQUIVO DE RETIFICAÇÃO

        $cpfTrab = sprintf("%11s", RemoveEspacos(RemoveCaracteres($trabalhador['cpf'])));
        $nisTrab = sprintf("%11s", RemoveEspacos(RemoveCaracteres($trabalhador['pis'])));
        $nomeTrab = sprintf("%-60s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador['nome']))));
        $sexo = sprintf("%-1s", RemoveEspacos($trabalhador['sexo']));
        $racaCor = sprintf("%1s", RemoveEspacos(substr($trabalhador['racaCor'], 1)));
        $estadoCivil = sprintf("%1s", RemoveEspacos($trabalhador['cod_estado_civil']));
        $grauInstrucao = sprintf("%2s", RemoveEspacos($trabalhador['grauInstrucao']));
        $dtNascimento = sprintf("%10s", RemoveEspacos($trabalhador['data_nasci']));
//            $codMunicipioNasc = sprintf("%7s", RemoveEspacos($trabalhador['codMunicipioNasc']));
//            $ufNasc = sprintf("%-2s", RemoveEspacos($trabalhador['uf_nasc']));
//            $paisNascto = sprintf("%3s", RemoveEspacos($trabalhador['cod_pais_nasc'])); 
//            $paisNacionalidade = sprintf("%3s", RemoveEspacos($trabalhador['cod_pais_nacionalidade']));
        $nomeMae = sprintf("%-60s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador['mae']))));
        $nomePai = sprintf("%-60s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador['pai']))));
        $nrCtps = sprintf("%-11s", RemoveEspacos($trabalhador['nrCtps']));
        $serieCtps = sprintf("%-5s", RemoveEspacos(RemoveCaracteres($trabalhador['serie_ctps'])));
        $ufCtps = sprintf("%-2s", RemoveEspacos($trabalhador['uf_ctps']));
        $nrRg = sprintf("%14s", RemoveEspacos(RemoveCaracteres($trabalhador['rg'])));
        $orgaoEmissor = sprintf("%20s", RemoveEspacos($trabalhador['orgao']));
        $dtExpedicao = sprintf("%-10s", $trabalhador['data_emissao']);
//        $tpLogradouro = sprintf("%-3s", RemoveEspacos($trabalhador['tpLogradouro']));
        $descLogradouro = sprintf("%-80s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador['endereco']))));
        $nrLogradouro = sprintf("%-10s", RemoveEspacos(RemoveCaracteres($trabalhador['numero'])));
        $complemento = sprintf("%-30s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador['complemento']))));
        $bairro = sprintf("%-60s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador['bairro']))));
        $cep = sprintf("%08s", RemoveEspacos(RemoveCaracteres($trabalhador['cep'])));
//            $codMunicipioEnd = sprintf("%07s", RemoveEspacos($trabalhador['codMunicipioEnd']));
        $ufEnd = sprintf("%-02s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador['ufEnd']))));
//        $paisResidencia = sprintf("%-03s", RemoveEspacos($trabalhador['paisResidencia']));
//        $descLogradouro = sprintf("%-80s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador['endereco']))));
//        $nrLogradouro = sprintf("%-10s", RemoveEspacos(RemoveCaracteres($trabalhador['numero'])));
//        $complemento = sprintf("%-30s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador['complemento']))));
//        $bairro = sprintf("%-60s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador['bairro']))));
//        $nomeCidade = sprintf("%-30s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($trabalhador['nomeCidade']))));
//        $codPostal = sprintf("%-10s", RemoveEspacos(RemoveCaracteres($trabalhador['codPostal'])));
//            $dtChegada = sprintf("%-10s", RemoveEspacos($trabalhador['dtChegadaPais']));
//        $dtNaturalizacao = sprintf("%-10s", RemoveEspacos($trabalhador['dtNaturalizacao']));
//        $casadoBr = sprintf("%-1s", RemoveEspacos($trabalhador['casadoBr']));
//        $filhosBr = sprintf("-1s", RemoveEspacos($trabalhador['filhosBr']));
        $defFisica = sprintf("%-1s", $trabalhador['defFisica']);
        $defVisual = sprintf("%-1s", $trabalhador['defVisual']);
        $defAuditiva = sprintf("%-1s", $trabalhador['defAuditiva']);
        $defMental = sprintf("%-1s", $trabalhador['defMental']);
        $defIntelectual = sprintf("%-1s", $trabalhador['defIntelectual']);
        $reabilitado = sprintf("%-1s", $trabalhador['reabilitado']);
//        $observacao = sprintf("%-255s", RemoveEspacos(RemoveAcentos($trabalhador['observacao'])));
//        $tpDep = sprintf("%-2s", $trabalhador['tpDep']);
        $fonePrincipal = sprintf("%13s", RemoveEspacos(RemoveCaracteres($trabalhador['tel_fixo'])));
        $foneAlternativo = sprintf("%13s", RemoveEspacos(RemoveCaracteres($trabalhador['tel_cel'])));
        $emailPrincipal = sprintf("%60s", RemoveEspacos($trabalhador['email']));
        $tpInscricao = sprintf("%1s", $trabalhador['tpInscricao']);
        $nrInscricao = sprintf("%015s", RemoveEspacos(RemoveCaracteres($trabalhador['nrInscricao'])));
        $dtInicoOgmo = sprintf("%-10s", $trabalhador['dtInicioOgmo']);
//            $codCategOgmo = sprintf("%03s", $trabalhador['codCategOgmo']);
        $codCateg = sprintf("%03s", $trabalhador['codCateg']);
        $dtAdmissao = sprintf("%10s", RemoveEspacos($trabalhador['data_entrada']));
        $codCargo = sprintf("%30s", $trabalhador['id_curso']);
        $codCbo = sprintf("%-6s", RemoveCaracteres($trabalhador['codCbo']));
        $valSalFixo = sprintf("%14s", RemoveEspacos($trabalhador['salario']));
        $unidSalFixo = sprintf("%1s", 5); // PAGAMENTO POR MES
//        $descSalVariavel = sprintf("%90s", RemoveEspacos(RemoveAcentos($trabalhador['descSalVariavel'])));
//        $optanteFgts = sprintf("%01s", $trabalhador['optanteFgts']);
//        $dtOpcaoFgts = sprintf("%-10s", $trabalhador['dtOpcaoFgts']);
        $dtInicoEstag = sprintf("%-10s", $estagio["dtInicioEstag"]);
        $codCategEstag = sprintf("%03s", $estagio["codCategEstag"]);
        $natEstagio = sprintf("%01s", $estagio["natEstagio"]);
        $nivEstagio = sprintf("%01s", $estagio["nivEstagio"]);
        $areaAtuacao = sprintf("%-50s", $estagio["areaAtuacao"]);
        $nrApolice = sprintf("%-30s", $estagio["nrApolice"]);
        $vlrBolsa = sprintf("%14s", $estagio["vlrBolsa"]);
        $dtPrevistaTermino = sprintf("%-10s", $estagio["dtPrevistaTermino"]);
        $cnpjInstEnsino = sprintf("%14s", $estagio["cnpjInstEnsino"]);
        $nomeRazao = sprintf("%-115s", $estagio["nomeRazao"]);
        $descLogradouroInst = sprintf("%-80s", $estagio["descLogradouro"]);
        $nrLogradouroInst = sprintf("%-10s", $estagio["nrLogradouro"]);
        $bairroInst = sprintf("%-60s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($estagio['bairro']))));
        $cepInst = sprintf("%08s", RemoveEspacos(RemoveCaracteres($estagio['cep'])));
//            $codMunicipioInst = sprintf("%07s", RemoveEspacos($estagio['codMunicipio']));
        $ufInst = sprintf("%-02s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($estagio['uf']))));
        $cnpjAgeIntegracao = sprintf("%14s", $estagio["cnpjAgeIntegracao"]);
        $nomeRazaoAgeIntegracao = sprintf("%-115s", $estagio["nomeRazaoAgeIntegracao"]);
        $descLogradouroAgeIntegracao = sprintf("%-80s", $estagio["descLogradouroAgeIntegracao"]);
        $nrLogradouroAgeIntegracao = sprintf("%-10s", $estagio["nrLogradouroAgeIntegracao"]);
        $bairroAgeIntegracao = sprintf("%-60s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($estagio['bairroAgeIntegracao']))));
        $cepAgeIntegracao = sprintf("%08s", RemoveEspacos(RemoveCaracteres($estagio['cepAgeIntegracao'])));
//            $codMunicipioAgeIntegracao = sprintf("%07s", RemoveEspacos($estagio['codMunicipioAgeIntegracao']));
        $ufAgeIntegracao = sprintf("%-02s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($estagio['ufAgeIntegracao']))));
        $cpfSupervisor = sprintf("%-11s", RemoveCaracteres($estagio["cpfSupervisor"]));
        $nomeSupervisor = sprintf("%-60s", RemoveAcentos(RemoveCaracteres($estagio["nomeSupervisor"])));

        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        $eSocial = $dom->createElement("eSocial");
        $dadosCabecalho['indRetificacao'] = $indRetificacao;
        if ($indRetificacao == 2) {
            //$infnrRecibo = $dom->createElement("nrRecibo", $nrRecibo);  //ac cab
            $dadosCabecalho['nrRecibo'] = $nrRecibo;
        }

        $xmlCabecalho = $this->montaCabecalho($empregador, $dom, $evt, $dadosCabecalho);

        $tagtrabalhador = $dom->createElement("trabalhador");
        $infcpfTrab = $dom->createElement("cpfTrab", $cpfTrab);
        $infnisTrab = $dom->createElement("nisTrab", $nisTrab);

        if ($evento == "2620") {

            $dtAlteracao = sprintf("%-10s", date("Y-m-d"));
            $infdtAlteracao = $dom->createElement("dtAlteracao", $dtAlteracao);
        }
        $infnomeTrab = $dom->createElement("nomeTrab", $nomeTrab);
        $infsexo = $dom->createElement("sexo", $sexo);
        $infracaCor = $dom->createElement("racaCor", $racaCor);
        $infestadoCivil = $dom->createElement("estadoCivil", $estadoCivil);
        $infgrauInstrucao = $dom->createElement("grauInstrucao", $grauInstrucao);
        $nascimento = $dom->createElement("nascimento");
        $infdtNascto = $dom->createElement("dtNascto", $dtNascimento);
//        $infcodMunicipioNasc = $dom->createElement("codMunicipio", $codMunicipioNasc);
//        $infufNasc = $dom->createElement("uf", $ufNasc);
//        $infpaisNascto = $dom->createElement("paisNascto", $paisNascto);
//        $infpaisNacionalidade = $dom->createElement("paisNacionalidade", $paisNacionalidade);
        $infnomeMae = $dom->createElement("nomeMae", $nomeMae);
        $infnomePai = $dom->createElement("nomePai", $nomePai);
        $documentos = $dom->createElement("documentos");
        $ctps = $dom->createElement("CTPS");
        $infnrCtps = $dom->createElement("nrCtps", $nrCtps);
        $infserieCtps = $dom->createElement("serieCtps", $serieCtps);
        $infufCtps = $dom->createElement("ufCtps", $ufCtps);
        $rg = $dom->createElement("RG");
        $infnrRg = $dom->createElement("nrRg", $nrRg);
        $inforgaoEmissor = $dom->createElement("orgaoEmissor", $orgaoEmissor);
        $infdtExpedicao = $dom->createElement("dtExpedicao", $dtExpedicao);
        $endereco = $dom->createElement("endereco");
        $brasil = $dom->createElement("brasil");
//        $inftpLogradouro = $dom->createElement("tpLogradouro", $tpLogradouro);
        $infdescLogradouro = $dom->createElement("descLogradouro", $descLogradouro);
        $infnrLogradouro = $dom->createElement("nrLogradouro", $nrLogradouro);
        $infcomplemento = $dom->createElement("complemento", $complemento);
        $infbairro = $dom->createElement("bairro", $bairro);
        $infcep = $dom->createElement("cep", $cep);
//        $infcodMunicipioEnd = $dom->createElement("codMunicipio", $codMunicipioEnd);
        $infufEnd = $dom->createElement("uf", $ufEnd);
//        $exterior = $dom->createElement("exterior");
//        $infpaisResidencia = $dom->createElement("paisResidencia", $paisResidencia);
        $trabEstrangeiro = $dom->createElement("trabEstrangeiro");
//        $infdtChegada = $dom->createElement("dtChegada", $dtChegada);
//        $infdtNaturalizacao = $dom->createElement("dtNaturalizacao", $dtNaturalizacao);
//        $infcasadoBr = $dom->createElement("casadoBr", $casadoBr);
//        $inffilhosBr = $dom->createElement("filhosBr", $filhosBr);
        $infoDeficiencia = $dom->createElement("infoDeficiencia");
        $infdefFisica = $dom->createElement("defFisica", $defFisica);
        $infdefVisual = $dom->createElement("defVisual", $defVisual);
        $infdefAuditiva = $dom->createElement("defAuditiva", $defAuditiva);
        $infdefMental = $dom->createElement("defMental", $defMental);
        $infdefIntelectual = $dom->createElement("defIntelectual", $defIntelectual);
        $infreabilitado = $dom->createElement("reabilitado", $reabilitado);
//        $infobservacao = $dom->createElement("observacao", $observacao);
//        $inftpDep = $dom->createElement("tpDep", $tpDep);
        $contato = $dom->createElement("contato");
        $inffonePrincipal = $dom->createElement("fonePrincipal", $fonePrincipal);
        $inffoneAleternativo = $dom->createElement("foneAlternativo", $foneAlternativo);
        $infemailPrincipal = $dom->createElement("emailPrincipal", $emailPrincipal);
//        $infemailAlternativo = $dom->createElement("emailAlternativo", $emailAletenativo);
        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricao = $dom->createElement("tpInscricao", $tpInscricao);
        $infnrInscricao = $dom->createElement("nrInscricao", $nrInscricao);
        $info = $dom->createElement($info);
        $trabalhadorAvulso = $dom->createElement("trabalahdorAvulso");
        $infdtInicoOgmo = $dom->createElement("dtInico", $dtInicoOgmo);
//        $infcodCategOgmo = $dom->createElement("codCateg", $codCategOgmo);
        $contribIndividual = $dom->createElement("contribIndividual");
        $infcodCateg = $dom->createElement("codCateg", $codCateg);
        $infdtAdmissao = $dom->createElement("dtInicio", $dtAdmissao);
        $infcodCargo = $dom->createElement("codCargo", $codCargo);
        $infcodCbo = $dom->createElement("codCbo", $codCbo);
        $remuneracao = $dom->createElement("remuneracao");
        $infvlrSalFixo = $dom->createElement("vlrSalFixo", $valSalFixo);
        $infunidSalFixo = $dom->createElement("unidSalFixo", $unidSalFixo);
//        $infdescSalVariavel = $dom->createElement("descSalVariavel", $descSalVariavel);
//        $fgts = $dom->createElement("FGTS");
//        $infoptanteFgts = $dom->createElement("optanteFGTS", $optanteFgts);
//        $infdtOpcaoFgts = $dom->createElement("dtOpcoesFGTS", $dtOpcaoFgts);
        $estagiario = $dom->createElement("estagiario");
        $infdtInicoEstag = $dom->createElement("dtInico", $dtInicoEstag);
        $infcodCategEstag = $dom->createElement("codCateg", $codCategEstag);
        $infnatEstagio = $dom->createElement("natEstagio", $natEstagio);
        $infnivEstagio = $dom->createElement("nivEstagio", $nivEstagio);
        $infareaAtuacao = $dom->createElement("areaAtuacao", $areaAtuacao);
        $infnrApolice = $dom->createElement("nrApolice", $nrApolice);
        $infvlrBolsa = $dom->createElement("vlrBolsa", $vlrBolsa);
        $infdtPrevistaTermino = $dom->createElement("dtPrevistaTermino", $dtPrevistaTermino);
        $instEnsino = $dom->createElement("instEnsino");
        $infcnpjInstEnsino = $dom->createElement("cnpjInstEnsino", $cnpjInstEnsino);
        $infnomeRazao = $dom->createElement("nomeRazao", $nomeRazao);
        $infdescLogradouroInst = $dom->createElement("descLogradouro", $descLogradouroInst);
        $infnrLogradouroInst = $dom->createElement("nrLogradouro", $nrLogradouroInst);
        $infbairroInst = $dom->createElement("bairro", $bairroInst);
        $infcepInst = $dom->createElement("cep", $cepInst);
//            $infcodMunicipioInst = $dom->createElemente("codMunicipio", $codMunicipioInst);
        $infufInst = $dom->createElement("uf", $ufInst);
        $ageIntegracao = $dom->createElement("ageIntegracao");
        $infcnpjAgeIntegracao = $dom->createElement("cnpjAgeIntegracao", $cnpjAgeIntegracao);
        $infnomeRazaoAgeIntegracao = $dom->createElement("nomeRazao", $nomeRazaoAgeIntegracao);
        $infdescLogradouroAgeIntegracao = $dom->createElement("descLogradouro", $descLogradouroAgeIntegracao);
        $infnrLogradouroAgeIntegracao = $dom->createElement("nrLogradouro", $nrLogradouroAgeIntegracao);
        $infbairroAgeIntegracao = $dom->createElement('bairro', $bairroAgeIntegracao);
        $infcepAgeIntegracao = $dom->createElement('cep', $cepAgeIntegracao);
//            $infcodMunicipioAgeIntegracao = $dom->createElemente('codMunicipio', $codMunicipioAgeIntegracao);
        $infufAgeIntegracao = $dom->createElement('uf', $ufAgeIntegracao);
        $supervisorEstagio = $dom->createElement("supervisorEstagio");
        $infcpfSupervisor = $dom->createElement("cpfSupervisor", $cpfSupervisor);
        $infnomeSupervisor = $dom->createElement("nomeSupervisor", $nomeSupervisor);

        if ($evento == "2600") {
            $trabalhadorAvulso->appendChild($infdtInicoOgmo);
//        $trabalhadorAvulso->appendChild($infcodCategOgmo);
            $info->appendChild($trabalhadorAvulso);
        }

        $contribIndividual->appendChild($infcodCateg);
        if ($evento == "2600") {
            $contribIndividual->appendChild($infdtAdmissao);
        }
        $contribIndividual->appendChild($infcodCargo);
        $contribIndividual->appendChild($infcodCbo);
        $remuneracao->appendChild($infvlrSalFixo);
        $remuneracao->appendChild($infunidSalFixo);
//        $remuneracao->appendChild($inddescricaoSalVariavel);
        $contribIndividual->appendChild($remuneracao);
//        $fgts->appendChild($optanteFgts);
//        $fgts->appendChild($dtOpcaoFgts);
//        $contribIndividual->appendChild($fgts);

        if (!empty($estagio)) {
            if ($evento == "2600") {

                $estagiario->appendChild($infdtInicoEstag);
                $estagiario->appendChild($infcodCategEstag);
            }
            $estagiario->appendChild($infnatEstagio);
            $estagiario->appendChild($infnivEstagio);
            $estagiario->appendChild($infareaAtuacao);
            $estagiario->appendChild($infnrApolice);
            $estagiario->appendChild($infvlrBolsa);
            $estagiario->appendChild($infdtPrevistaTermino);
            $instEnsino->appendChild($infcnpjInstEnsino);
            $instEnsino->appendChild($infnomeRazao);
            $instEnsino->appendChild($infdescLogradouroInst);
            $instEnsino->appendChild($infnrLogradouroInst);
            $instEnsino->appendChild($infbairroInst);
            $instEnsino->appendChild($infcepInst);
//        $instEnsino->appendChild($infcodMunicipioInst);
            $instEnsino->appendChild($infufInst);
            $estagiario->appendChild($instEnsino);
            $ageIntegracao->appendChild($infcnpjAgeIntegracao);
            $ageIntegracao->appendChild($infnomeRazaoAgeIntegracao);
            $ageIntegracao->appendChild($infdescLogradouroAgeIntegracao);
            $ageIntegracao->appendChild($infnrLogradouroAgeIntegracao);
            $ageIntegracao->appendChild($infbairroAgeIntegracao);
            $ageIntegracao->appendChild($infcepAgeIntegracao);
//        $ageIntegracao->appendChild($infcodMunicipioAgeIntegracao);
            $ageIntegracao->appendChild($infufAgeIntegracao);
            $estagiario->appendChild($ageIntegracao);
            $supervisorEstagio->appendChild($infcpfSupervisor);
            $supervisorEstagio->appendChild($infnomeSupervisor);
            $estagiario->appendChild($supervisorEstagio);
            $info->appendChild($estagiario);
        }
        if ($evento != "2600") {
            $info->appendChild($infcpfTrab);
            if ($trabalhador['pis']) {
                $info->appendChild($infnisTrab);
            }
            if ($evento == "2620") {
                $info->appendChild($infdtAlteracao);
            }
            $info->appendChild($contribIndividual);
            $ideEmpregador->appendChild($inftpInscricao);
            $ideEmpregador->appendChild($infnrInscricao);
        } else {
            $info->appendChild($contribIndividual);
            $ideEmpregador->appendChild($inftpInscricao);
            $ideEmpregador->appendChild($infnrInscricao);
            $tagtrabalhador->appendChild($infcpfTrab);
            if ($trabalhador['pis']) {
                $tagtrabalhador->appendChild($infnisTrab);
            }

            $tagtrabalhador->appendChild($infnomeTrab);
            $tagtrabalhador->appendChild($infsexo);
            $tagtrabalhador->appendChild($infracaCor);
            $tagtrabalhador->appendChild($infestadoCivil);
            $tagtrabalhador->appendChild($infgrauInstrucao);
            $nascimento->appendChild($infdtNascto);
//            if($trabalhador['codMunicipioNasc']){
//                $nascimento->appendChild($infcodMunicipioNasc);
//            }
//            if($trabalhador['uf_nasc']){
//                $nascimento->appendChild($infufNasc);
//            }
//            $nascimento->appendChild($infpaisNascto);
//            $nascimento->appendChild($infpaisNacionalidade);
            if ($trabalhador['mae']) {
                $nascimento->appendChild($infnomeMae);
            }
            if ($trabalhador['pai']) {
                $nascimento->appendChild($infnomePai);
            }
            $tagtrabalhador->appendChild($nascimento);
            if (!empty($trabalhador['nrCtps']) || !empty($trabalhador['rg'])) {
                if ($trabalhador['nrCtps']) {
                    $ctps->appendChild($infnrCtps);
                    $ctps->appendChild($infserieCtps);
                    $ctps->appendChild($infufCtps);
                    $documentos->appendChild($ctps);
                }
                if ($trabalhador['rg']) {
                    $rg->appendChild($infnrRg);
                    $rg->appendChild($inforgaoEmissor);
                    $rg->appendChild($infdtExpedicao);
                    $documentos->appendChild($rg);
                }
                $tagtrabalhador->appendChild($documentos);
            }

            //        $brasil->appendChild($inftpLogradouro);
            $brasil->appendChild($infdescLogradouro);
            if ($trabalhador['numero']) {
                $brasil->appendChild($infnrLogradouro);
            }
            if ($trabalhador['complemento']) {
                $brasil->appendChild($infcomplemento);
            }
            if ($trabalhador['bairro']) {
                $brasil->appendChild($infbairro);
            }
            $brasil->appendChild($infcep);
//            $brasil->appendChild($infcodMunicipioEnd);
            $brasil->appendChild($infufEnd);
            $endereco->appendChild($brasil);
            $tagtrabalhador->appendChild($endereco);

            if (!empty($trabalhador['cod_pais_nasc']) && $trabalhador['cod_pais_nasc'] != '001') {
                $trabEstrangeiro->appendChild($infdtChegada);
                //        $trabEstrangeiro->appendChild($infdtNaturalizacao);
                //        $trabEstrangeiro->appendChild($infcasadoBr);
                //        $trabEstrangeiro->appendChild($inffilhosBr);
                $tagtrabalhador->appendChild($trabEstrangeiro);
            }
            if (!empty($trabalhador['deficiencia'])) {
                $infoDeficiencia->appendChild($infdefFisica);
                $infoDeficiencia->appendChild($infdefVisual);
                $infoDeficiencia->appendChild($infdefAuditiva);
                $infoDeficiencia->appendChild($infdefMental);
                $infoDeficiencia->appendChild($infdefIntelectual);
                $infoDeficiencia->appendChild($infreabilitado);
                //        $infoDeficiencia->appendChild($infobservacao);
                $tagtrabalhador->appendChild($infoDeficiencia);
            }

            foreach ($dependente as $key => $value) {
                $i = substr($key, -1);
                if (!empty($value)) {
                    switch ($key) {
                        case 'nome' . $i:
                            $nomeDep = sprintf("%-60s", RemoveEspacos(RemoveCaracteres(RemoveAcentos($value))));
                            $infnomeDep = $dom->createElement("nomeDep");
                            $infnomeDep->appendChild($dom->createTextNode($nomeDep));
                            break;

                        case 'data' . $i:
                            $dtNasctoDep = sprintf("%-10s", RemoveEspacos($value));
                            $infdtNasctoDep = $dom->createElement("dtNascto");
                            $infdtNasctoDep->appendChild($dom->createTextNode($dtNasctoDep));
                            break;

                        case 'depIR' . $i:
                            $depIr = sprintf("%1s", RemoveEspacos($value));
                            $infdepIr = $dom->createElement("depIRRF");
                            $infdepIr->appendChild($dom->createTextNode($depIr));
                            break;

                        case 'depSF' . $i:
                            $depSF = sprintf("%1s", RemoveEspacos($value));
                            $infdepSF = $dom->createElement("depSF");
                            $infdepSF->appendChild($dom->createTextNode($depSF));
                            $dependente = $dom->createElement("dependente");
                            //                        $dependente->appendChild($inftpDep);
                            $dependente->appendChild($infnomeDep);
                            $dependente->appendChild($infdtNasctoDep);
                            $dependente->appendChild($infdepIr);
                            $dependente->appendChild($infdepSF);
                            $tagtrabalhador->appendChild($dependente);
                            break;
                    }
                }
            }
            if (!empty($trabalhador['tel_fixo']) || !empty($trabalhador['tel_cel']) || !empty($trabalhador['email'])) {
                if ($trabalhador['tel_fixo']) {
                    $contato->appendChild($inffonePrincipal);
                }
                if ($trabalhador['tel_cel']) {
                    $contato->appendChild($inffoneAleternativo);
                }
                if ($trabalhador['email']) {
                    $contato->appendChild($infemailPrincipal);
                }

                //            $contato->appendChild($infemailAlternativo);
                $tagtrabalhador->appendChild($contato);
            }
        }

        if ($evento == "2600") {
            $xmlCabecalho->appendChild($tagtrabalhador);
        }
        $xmlCabecalho->appendChild($ideEmpregador);
        $xmlCabecalho->appendChild($info);
        $eSocial->appendChild($xmlCabecalho);
        $dom->appendChild($eSocial);
        $this->sequencial++;

        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

    public function montas2680($arquivo, $empregador, $trabalhador, $numRecibo, $estagio = null) {
        $tpInscricaoMaster = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricaoMaster = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));
//        $dt_hs_atual = str_replace(" ", "", RemoveCaracteres(date("Y-m-d H:i:s", time())));
//        $sequencial = sprintf("%05s", $this->sequencial);
//
//        $id = sprintf("%-36s", "ID" . $tpInscricaoMaster . $nrInscricaoMaster . $dt_hs_atual . $sequencial);
//        $versao = sprintf("%-11s", "1.2 - beta2"); // VERSÃO DO LAYOUT
//        $tpAmb = sprintf("%01s", 1); // CONSTANTE
//        $procEmi = sprintf("%01s", 1); // CONSTANTE
//        $indSeguimento = sprintf("%01s", 1); // CONSTANTE
//        $verProc = sprintf("%-20s", "Sei lah que versao ah essa"); // FALTA SABER VERSÃO DO App EMISSOR DO EVENTO

        if ($this->tpevento == "inclusao") {
            $indRetificacao = 1;
        } elseif ($this->tpevento == "alteracao") {
            $indRetificacao = 2;
            $nrRecibo = $numRecibo;
            $nrRecibo = sprintf("%15s", RemoveEspacos($nrRecibo));
        }
        $indRetificacao = sprintf("%01s", $indRetificacao); //1 - ARQUIVO ORIGINAL 2 - ARQUIVO DE RETIFICAÇÃO
        $tpInscricao = sprintf("%01s", $trabalhador["tpInscricao"]);
        $nrInscricao = sprintf("%015s", RemoveEspacos(RemoveCaracteres($trabalhador["nrInscricao"])));
        $cpfTrab = sprintf("%11s", RemoveEspacos(RemoveCaracteres($trabalhador['cpf'])));
        $nisTrab = sprintf("%11s", RemoveEspacos(RemoveCaracteres($trabalhador['pis'])));
        $dtTerminoAvulso = sprintf("%-10s", $trabalhador["dtTerminoAvulso"]);
        $codCateg = sprintf("%03s", $trabalhador["codCateg"]);
        $dtTerminoConInd = sprintf("%-10s", $trabalhador['dtTermino']);
        $motDesligDirNaoEmpregado = sprintf("%2s", $trabalhador["motDesligDirNaoEmpregado"]);
        $dtTerminoEstag = sprintf("%-10s", $estagio['dtTermino']);

        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        $eSocial = $dom->createElement("eSocial");
        $dadosCabecalho['indRetificacao'] = $indRetificacao;
        if ($indRetificacao == 2) {
            //$infnrRecibo = $dom->createElement("nrRecibo", $nrRecibo);  //ac cab
            $dadosCabecalho['nrRecibo'] = $nrRecibo;
        }

        $xmlCabecalho = $this->montaCabecalho($empregador, $dom, "evtTSVTermino", $dadosCabecalho);
//        $evt = $dom->createElement("evtTSVTermino");
//        $ideEvento = $dom->createElement("ideEvento");
//        $infindRetificacao = $dom->createElement("indRetificacao", $indRetificacao);
//        if ($indRetificacao == 2) {
//            $infnrRecibo = $dom->createElement("nrRecibo", $nrRecibo);
//        }
//
//        $inftpAmb = $dom->createElement("tpAmb", $tpAmb);
//        $infprocEmi = $dom->createElement("procEmi", $procEmi);
//        $infindSeguimento = $dom->createElement("indSeguimento", $indSeguimento);
//        $infverProc = $dom->createElement("verProc", $verProc);
        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricao = $dom->createElement("tpInscricao", $tpInscricao);
        $infnrInscricao = $dom->createElement("nrInscricao", $nrInscricao);

        $infoTSVTermino = $dom->createElement("infoTSVTermino");
        $infcpfTrab = $dom->createElement("cpfTrab", $cpfTrab);
        $infnisTrab = $dom->createElement("nisTrab", $nisTrab);
        $trabalhadorAvulso = $dom->createElement("trabalhadorAvulso");
        $infdtTerminoAvulso = $dom->createElement("dtTermino", $dtTerminoAvulso);
        $contribIndividual = $dom->createElement("contribIndividual");
        $infcodCateg = $dom->createElement("codCateg", $codCateg);
        $infdtTerminoConInd = $dom->createElement("dtTermino", $dtTerminoConInd);
        $infmotDesligDirNaoEmpregado = $dom->createElement("motDesligDirNaoEmpregado", $motDesligDirNaoEmpregado);
        $estagiario = $dom->createElement("estagiario");
        $infdtTerminoEstag = $dom->createElement("dtTermino", $dtTerminoEstag);

        $infoTSVTermino->appendChild($infcpfTrab);
        $infoTSVTermino->appendChild($infnisTrab);
        $trabalhadorAvulso->appendChild($infdtTerminoAvulso);
        $infoTSVTermino->appendChild($trabalhadorAvulso);
        $contribIndividual->appendChild($infcodCateg);
        $contribIndividual->appendChild($infdtTerminoConInd);
        $contribIndividual->appendChild($infmotDesligDirNaoEmpregado);
        $infoTSVTermino->appendChild($contribIndividual);
        if (!empty($estagio)) {
            $estagiario->appendChild($infdtTerminoEstag);
            $infoTSVTermino->appendChild($estagiario);
        }
        $ideEmpregador->appendChild($inftpInscricao);
        $ideEmpregador->appendChild($infnrInscricao);
//        $ideEvento->appendChild($infindRetificacao);
//        if ($indRetificacao == 2) {
//            $ideEvento->appendChild($infnrRecibo);
//        }
//
//        $ideEvento->appendChild($inftpAmb);
//        $ideEvento->appendChild($infprocEmi);
//        $ideEvento->appendChild($infindSeguimento);
//        $ideEvento->appendChild($infverProc);
//        $evt->appendChild($ideEvento);
        $xmlCabecalho->appendChild($ideEmpregador);
        $xmlCabecalho->appendChild($infoTSVTermino);
//        $evt->setAttribute("Id", $id);
//        $evt->setAttribute("versao", $versao);
        $eSocial->appendChild($xmlCabecalho);
        $dom->appendChild($eSocial);
        $this->sequencial++;

//        echo $dom->saveXML();
        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

    public function montas2800($arquivo, $empregador, $trabalhador, $nrEmpSuces, $bcFgtsAnt, $statusSaida, $fichaFinanceira, $rescisao, $evento, $numRecibo) {
        switch ($evento) {
            case "2800":
                $evt = "evtDesligamento";
                $info = "infoDesligamento";
                break;

            case "2820":
                $evt = "evtReintegracao";
                $info = "infoReintegracao";
                break;
        }
        $tpInscricaoMaster = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricaoMaster = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));
//        $dt_hs_atual = str_replace(" ", "", RemoveCaracteres(date("Y-m-d H:i:s", time())));
//        $sequencial = sprintf("%05s", $this->sequencial);
//
//        $id = sprintf("%-36s", "ID" . $tpInscricaoMaster . $nrInscricaoMaster . $dt_hs_atual . $sequencial);
//        $versao = sprintf("%-11s", "1.2 - beta2"); // VERSÃO DO LAYOUT
//        $tpAmb = sprintf("%01s", 1); // CONSTANTE
//        $procEmi = sprintf("%01s", 1); // CONSTANTE
//        $indSeguimento = sprintf("%01s", 1); // CONSTANTE
//        $verProc = sprintf("%-20s", "Sei lah que versao ah essa"); // FALTA SABER VERSÃO DO App EMISSOR DO EVENTO

        if ($this->tpevento == "inclusao") {
            $indRetificacao = 1;
        } elseif ($this->tpevento == "alteracao") {
            $indRetificacao = 2;
            $nrRecibo = $numRecibo;
            $nrRecibo = sprintf("%15s", RemoveEspacos($nrRecibo));
        }
        $indRetificacao = sprintf("%01s", $indRetificacao); //1 - ARQUIVO ORIGINAL 2 - ARQUIVO DE RETIFICAÇÃO
        $tpInscricao = sprintf("%01s", $trabalhador["tpInscricao"]);
        $nrInscricao = sprintf("%015s", RemoveEspacos(RemoveCaracteres($trabalhador["nrInscricao"])));
        $cpfTrab = sprintf("%11s", RemoveEspacos(RemoveCaracteres($trabalhador['cpf'])));
        $nisTrab = sprintf("%11s", RemoveEspacos(RemoveCaracteres($trabalhador['pis'])));
        $matricula = sprintf("%-30s", RemoveEspacos($trabalhador['id_trab']));
        if (!empty($nrEmpSuces)) {
            $motDesl = $nrEmpSuces["motDesligamento"];
            $cnpjSucessora = sprintf("%14s", RemoveEspacos(RemoveCaracteres($nrEmpSuces['cnpj'])));
        } else {
            $motDesl = $rescisao["cod_esocial"];
        }
        $motivoDesligamento = sprintf("%02s", $motDesl);
        if ($motDesl != 11 && $motDesl != 12) {
            $dtDesligamento = sprintf("%-10s", $rescisao["data_demi"]);
            $indPagtoAPI = sprintf("%-1s", $rescisao["indPagtoAPI"]);
            $observacao = sprintf("%-255s", $rescisao['observacao']);
        } else {
            $indPagtoAPI = sprintf("%-1s", 'N');
        }
        if ($indPagtoAPI == 'S') {
            $dtTerminoAPI = sprintf("%-10s", $rescisao["data_fim_aviso"]);
        }
        if ($motDesl == 09 || $motDesl == 10) {
            $nrAtestadoObito = sprintf("%-30s", $trabalhador["nrAtestadoObito"]);
            $nrProcTrabalhista = sprintf("%-20s", $trabalhador["nrProcTrabalhista"]);
        }
        if ($statusSaida == 1 || empty($statusSaida)) {
            $bcFgtsMesAnt = sprintf("%14s", $bcFgtsAnt["bcFgtsMesAnt"]);
        }
        if (!empty($rescisao)) {
            $bcCP = sprintf("%14s", $rescisao['base_inss_ss']);
            $bcIRRF = sprintf("%14s", $rescisao['base_irrf_ss']);
            $bcFGTS = sprintf("%14s", $rescisao['sal_base']);
            $bcFGTSVerbasIndeniz = sprintf("%14s", $rescisao['base_fgts_ss']);
            $descCP = sprintf("%14s", $rescisao['inss_ss']);
            $valorPorventos = sprintf("%14s", $rescisao['total_rendimento']);
            $valorDescontos = sprintf("%14s", $rescisao['total_deducao']);
            $valorLiquido = sprintf("%14s", $rescisao['total_liquido']);
            switch ($trabalhador['tipo_insalubridade']) {
                case 0:
                    $grauExp = 1; // Não exposto a agente nocivo na atividade atual
                    break;
                case 1:
                    $grauExp = 3; // Exposição a agente nocivo  - aposentadoria especial aos 20 anos de trabalho
                    break;
                case 2:
                    $grauExp = 2; // Exposição a agente nocivo  - aposentadoria especial aos 25 anos de trabalho
                    break;
            }
            $grauExp = sprintf("%1s", $grauExp);
        }
        $tpReintegracao = sprintf("%1s", $trabalhador['tpReintegracao']);
        $nrProcJud = sprintf("%-20s", $trabalhador['nrProcJud']);
        $nrLeiAnistia = sprintf("%-20s", $trabalhador['nrLeiAnistia']);
        $dtEfeito = sprintf("%-10s", $trabalhador['dtEfeito']);
        $dtEfeitoRetorno = sprintf("%-10s", $trabalhador['dtEfeitoRetorno']);

        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        $eSocial = $dom->createElement("eSocial");
        $dadosCabecalho['indRetificacao'] = $indRetificacao;
        if ($indRetificacao == 2) {
            //$infnrRecibo = $dom->createElement("nrRecibo", $nrRecibo);  //ac cab
            $dadosCabecalho['nrRecibo'] = $nrRecibo;
        }

        $xmlCabecalho = $this->montaCabecalho($empregador, $dom, $evt, $dadosCabecalho);
//        $evt = $dom->createElement($evt);
//        $ideEvento = $dom->createElement("ideEvento");
//        $infindRetificacao = $dom->createElement("indRetificacao", $indRetificacao);
//        if ($indRetificacao == 2) {
//            $infnrRecibo = $dom->createElement("nrRecibo", $nrRecibo);
//        }
//
//        $inftpAmb = $dom->createElement("tpAmb", $tpAmb);
//        $infprocEmi = $dom->createElement("procEmi", $procEmi);
//        $infindSeguimento = $dom->createElement("indSeguimento", $indSeguimento);
//        $infverProc = $dom->createElement("verProc", $verProc);
        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricao = $dom->createElement("tpInscricao", $tpInscricao);
        $infnrInscricao = $dom->createElement("nrInscricao", $nrInscricao);

        $ideVinculo = $dom->createElement("ideVinculo");
        $infcpfTrab = $dom->createElement("cpfTrab", $cpfTrab);
        $infnisTrab = $dom->createElement("nisTrab", $nisTrab);
        $infmatricula = $dom->createElement("matricula", $matricula);
        $info = $dom->createElement($info);
        $infmotivoDesligamento = $dom->createElement("motivoDesligamento", $motivoDesligamento);
        if ($motDesl != 11 && $motDesl != 12) {
            $infdtDesligamento = $dom->createElement("dtDesligamento", $dtDesligamento);
            $infobservacao = $dom->createElement("observacao", $observacao);
        }
        $infindPagtoAPI = $dom->createElement("indPagtoAPI", $indPagtoAPI);
        if ($indPagtoAPI == 'S') {
            $infdtTerminoAPI = $dom->createElement("dtTerminoAPI", $dtTerminoAPI);
        }
        if ($motDesl == 09 || $motDesl == 10) {
            $infnrAtestadoObito = $dom->createElement("nrAtestadoObito", $nrAtestadoObito);
            $infnrProcTrabalhista = $dom->createElement("nrProcTrabalhista", $nrProcTrabalhista);
        }
        if ($statusSaida == 1 || empty($statusSaida)) {
            $infbcFgtsMesAnt = $dom->createElement("bcFgtsMesAnt", $bcFgtsMesAnt);
        }
        if (!empty($nrEmpSuces)) {
            $sucessaoVinculo = $dom->createElement("sucessaoVinculo");
            $infcnpjSucessora = $dom->createElement("cnpjSucessora", $cnpjSucessora);
        }
        $verbasRescisorias = $dom->createElement("verbasRescisorias");
        $infbcCP = $dom->createElement("bcCP", $bcCP);
        $infbcIRRF = $dom->createElement("bcIRRF", $bcIRRF);
        $infbcFGTS = $dom->createElement("bcFGTS", $bcFGTS);
        $infbcFGTSVerbasIndeniz = $dom->createElement("bcFGTSVerbasIndeniz", $bcFGTSVerbasIndeniz);
        $infdescCP = $dom->createElement("descCP", $descCP);
        $infvalorProventos = $dom->createElement("valorPorventos", $valorPorventos);
        $infvalorDescontos = $dom->createElement("valorDescontos", $valorDescontos);
        $infvalorLiquido = $dom->createElement("valorLiquido", $valorLiquido);
        $itensRemuneracao = $dom->createElement("itensRemuneracao");
        $infoAgenteNocivo = $dom->createElement("infoAgenteNocivo");
        $infgrauExp = $dom->createElement("grauExp", $grauExp);

        $inftpReintegracao = $dom->createElement("tpReintegracao", $tpReintegracao);
        $infnrProcJud = $dom->createElement("nrProcJud", $nrProcJud);
        $infnrLeiAnistia = $dom->createElement("nrLeiAnistia", $nrLeiAnistia);
        $infdtEfeito = $dom->createElement("dtEfeito", $dtEfeito);
        $infdtEfeitoRetorno = $dom->createElement("dtEfeitoRetorno", $dtEfeitoRetorno);

        if ($evento == '2800') {
            $info->appendChild($infmotivoDesligamento);
            if ($nrEmpSuces["motDesligamento"] != 11 && $nrEmpSuces["motDesligamento"] != 12) {
                $info->appendChild($infdtDesligamento);
            }
            $info->appendChild($infindPagtoAPI);
            if ($indPagtoAPI == 'S') {
                $info->appendChild($infdtTerminoAPI);
            }
//            if ($trabalhador["cod_esocial"] == 09 || $trabalhador["cod_esocial"] == 10) {
//                $info->appendChild($infnrAtestadoObito);
//                $info->appendChild($infnrProcTrabalhista);
//            }
            if ($statusSaida == 1 || empty($statusSaida)) {
                $info->appendChild($infbcFgtsMesAnt);
            }
            if ($motDesl != 11 && $motDesl != 12) {
                $info->appendChild($infobservacao);
            }
            if (!empty($nrEmpSuces)) {
                $sucessaoVinculo->appendChild($infcnpjSucessora);
                $info->appendChild($sucessaoVinculo);
            }
            if (!empty($rescisao)) {
                $verbasRescisorias->appendChild($infbcCP);
                $verbasRescisorias->appendChild($infbcIRRF);
                $verbasRescisorias->appendChild($infbcFGTS);
                $verbasRescisorias->appendChild($infbcFGTSVerbasIndeniz);
                $verbasRescisorias->appendChild($infdescCP);
                $verbasRescisorias->appendChild($infvalorProventos);
                $verbasRescisorias->appendChild($infvalorDescontos);
                $verbasRescisorias->appendChild($infvalorLiquido);
                foreach ($fichaFinanceira as $codMov => $arrayMov) {
                    $codRubrica = RemoveEspacos(RemoveCaracteresGeral($codMov));
                    if (($arrayMov[$this->mes] > 0) && !empty($codRubrica)) {
                        $codRubrica = sprintf("%-30s", $codRubrica);
                        //            $qtdRubrica = sprintf("%6s", $trabalhador['qtdRubrica']);
                        //            $valorUnitario = sprintf("%14s", $trabalhador['vltUnitario']);
                        $valorRubrica = sprintf("%14s", RemoveEspacos(RemoveLetras(RemoveAcentos(str_replace('(', "", str_replace(')', "", $arrayMov[$this->mes]))))));
                        $infcodRubrica = $dom->createElement("codRubrica", $codRubrica);
                        //            $infqtdRubrica = $dom->createElement("qtdRubrica", $qtdRubrica);
                        //            $infvalorUnitario= $dom->createElement("valorUnitario", $valorUnitario);
                        $infvalorRubrica = $dom->createElement("valorRubrica", $valorRubrica);
                        $itensRemuneracao->appendChild($infcodRubrica);
                        //            $itensRemuneracao->appendChild($infqtdRubrica);
                        //            $itensRemuneracao->appendChild($infvalorUnitario);
                        $itensRemuneracao->appendChild($infvalorRubrica);
                        $verbasRescisorias->appendChild($itensRemuneracao);
                    }
                }

                $infoAgenteNocivo->appendChild($infgrauExp);
                $verbasRescisorias->appendChild($infoAgenteNocivo);
                $info->appendChild($verbasRescisorias);
            }
        } else {
            $info->appendChild($inftpReintegracao);
            $info->appendChild($infnrProcJud);
            $info->appendChild($infnrLeiAnistia);
            $info->appendChild($infdtEfeito);
            $info->appendChild($infdtEfeitoRetorno);
        }
        $ideVinculo->appendChild($infcpfTrab);
        $ideVinculo->appendChild($infnisTrab);
        $ideVinculo->appendChild($infmatricula);
        $ideEmpregador->appendChild($inftpInscricao);
        $ideEmpregador->appendChild($infnrInscricao);
//        $ideEvento->appendChild($infindRetificacao);
//        if ($indRetificacao == 2) {
//            $ideEvento->appendChild($infnrRecibo);
//        }
//        $ideEvento->appendChild($inftpAmb);
//        $ideEvento->appendChild($infprocEmi);
//        $ideEvento->appendChild($infindSeguimento);
//        $ideEvento->appendChild($infverProc);
//        $evt->appendChild($ideEvento);
        $xmlCabecalho->appendChild($ideEmpregador);
        $xmlCabecalho->appendChild($ideVinculo);
        $xmlCabecalho->appendChild($info);
//        $evt->setAttribute("Id", $id);
//        $evt->setAttribute("versao", $versao);
        $eSocial->appendChild($xmlCabecalho);
        $dom->appendChild($eSocial);
        $this->sequencial++;

        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

    public function montas2900($arquivo, $empregador, $trabalhador, $numRecibo, $evento, $indApuracao, $perApuracao) {
        $tpInscricaoMaster = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricaoMaster = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));
        $dt_hs_atual = str_replace(" ", "", RemoveCaracteres(date("Y-m-d H:i:s", time())));
        $sequencial = sprintf("%05s", $this->sequencial);

        $id = sprintf("%-36s", "ID" . $tpInscricaoMaster . $nrInscricaoMaster . $dt_hs_atual . $sequencial);
        $versao = sprintf("%-11s", "1.2 - beta2"); // VERSÃO DO LAYOUT
        $tpAmb = sprintf("%01s", 1); // CONSTANTE
        $procEmi = sprintf("%01s", 1); // CONSTANTE
        $indSeguimento = sprintf("%01s", 1); // CONSTANTE
        $verProc = sprintf("%-20s", "Sei lah que versao ah essa"); // FALTA SABER VERSÃO DO App EMISSOR DO EVENTO
        $tpInscricao = sprintf("%01s", $trabalhador["tpInscricao"]);
        $nrInscricao = sprintf("%015s", RemoveEspacos(RemoveCaracteres($trabalhador["nrInscricao"])));
        $tpEvento = sprintf("%-6s", "S-" . $evento);
        $nrReciboEvento = sprintf("%15s", RemoveEspacos($numRecibo));
        $cpfTrab = sprintf("%11s", RemoveEspacos(RemoveCaracteres($trabalhador['cpf'])));
        $nisTrab = sprintf("%11s", RemoveEspacos(RemoveCaracteres($trabalhador['pis'])));
        $indApuracao = sprintf("%01s", $indApuracao);
        $perApuracao = sprintf("%-7s", $perApuracao);

        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        $eSocial = $dom->createElement("eSocial");
        $evt = $dom->createElement("evtExclusao");
        $ideEvento = $dom->createElement("ideEvento");
        $inftpAmb = $dom->createElement("tpAmb", $tpAmb);
        $infprocEmi = $dom->createElement("procEmi", $procEmi);
        $infindSeguimento = $dom->createElement("indSeguimento", $indSeguimento);
        $infverProc = $dom->createElement("verProc", $verProc);
        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricao = $dom->createElement("tpInscricao", $tpInscricao);
        $infnrInscricao = $dom->createElement("nrInscricao", $nrInscricao);
        $infoExclusao = $dom->createElement("infoExclusao");
        $inftpEvento = $dom->createElement("tpEvento", $tpEvento);
        $infnrReciboEvento = $dom->createElement("nrReciboEvento", $nrReciboEvento);
        $ideTrabalhador = $dom->createElement("ideTrabalhador");
        $infcpfTrab = $dom->createElement("cpfTrab", $cpfTrab);
        $infnisTrab = $dom->createElement("nisTrab", $nisTrab);
        $ideFolhaPagto = $dom->createElement("ideFolhaPagto");
        $infindApuracao = $dom->createElement("indApuracao", $indApuracao);
        $infperApuracao = $dom->createElement("perApuracao", $perApuracao);

        $infoExclusao->appendChild($inftpEvento);
        $infoExclusao->appendChild($infnrReciboEvento);
        if (($evento >= 2100 && $evento <= 2820) || ($evento == 1200)) {
            $ideTrabalhador->appendChild($infcpfTrab);
            $ideTrabalhador->appendChild($infnisTrab);
            $infoExclusao->appendChild($ideTrabalhador);
        } else {
            $ideFolhaPagto->appendChild($infindApuracao);
            $ideFolhaPagto->appendChild($infperApuracao);
            $infoExclusao->appendChild($ideFolhaPagto);
        }
        $ideEmpregador->appendChild($inftpInscricao);
        $ideEmpregador->appendChild($infnrInscricao);
        $ideEvento->appendChild($inftpAmb);
        $ideEvento->appendChild($infprocEmi);
        $ideEvento->appendChild($infindSeguimento);
        $ideEvento->appendChild($infverProc);
        $evt->appendChild($ideEvento);
        $evt->appendChild($ideEmpregador);
        $evt->appendChild($infoExclusao);
        $evt->setAttribute("Id", $id);
        $evt->setAttribute("versao", $versao);
        $eSocial->appendChild($evt);
        $dom->appendChild($eSocial);
        $this->sequencial++;

//        echo $dom->saveXML();
        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

    public function montas1300($arquivo, $empregador, $beneficiario, $salario, $benPJ, $valPJ) {
        $tpInscricaoMaster = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricaoMaster = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));
        $dt_hs_atual = str_replace(" ", "", RemoveCaracteres(date("Y-m-d H:i:s", time())));
        $sequencial = sprintf("%05s", $this->sequencial);

        $id = sprintf("%-36s", "ID" . $tpInscricaoMaster . $nrInscricaoMaster . $dt_hs_atual . $sequencial);
        $versao = sprintf("%-11s", "1.2 - beta2"); // VERSÃO DO LAYOUT

        if ($this->tpevento == "inclusao") {
            $indRetificacao = 1;
        } elseif ($this->tpevento == "alteracao") {
            $indRetificacao = 2;
            $nrRecibo = $numRecibo;
            $nrRecibo = sprintf("%15s", RemoveEspacos($nrRecibo));
        }
        $indRetificacao = sprintf("%01s", $indRetificacao); //1 - ARQUIVO ORIGINAL 2 - ARQUIVO DE 
        $indApuracao = sprintf("%01s", 1);
        $perApuracao = sprintf("%-7s", '2014-14');
        $tpAmb = sprintf("%01s", 1); // CONSTANTE
        $procEmi = sprintf("%01s", 1); // CONSTANTE
        $indSeguimento = sprintf("%01s", 1); // CONSTANTE
        $verProc = sprintf("%-20s", "Sei lah que versao ah essa"); // FALTA SABER VERSÃO DO App EMISSOR DO EVENTO        
        $tpInscricao = sprintf("%01s", $beneficiario["tpInscricao"]);
        $nrInscricao = sprintf("%-14s", RemoveEspacos(RemoveCaracteres($beneficiario["nrInscricao"])));
        $codRendimento = sprintf("%04s", '0561');
        $tpInscricaoBeneficiario = sprintf("%01s", $beneficiario['tpInscricaoBeneficiario']);
        $nrInscricaoBeneficiario = sprintf("%-14s", $beneficiario['nrInscricaoBeneficiario']);
//        $dtLaudo = sprintf("%-10s", $beneficiario['dtLaudo']);
        $dtPagto = sprintf("%-10s", $beneficiario['dtPagto']);
        $indSuspExigibilidade = sprintf("%-1s", 'N'); // CONSTANTE
        $indDecTerceiro = sprintf("%-1s", $beneficiario['indDecTerceiro']);
        $vlrRendTributavel = sprintf("%14s", $beneficiario['vlrRendTrib']);
        $vlrIRRF = sprintf("%14s", $beneficiario['vlrIRRF']);
//        $descRendimento = sprintf("%-100s", $beneficiario['descRendimento']);
//        $LINHA_RTPO[$mes] = number_format($rowValorClt['inss'], 2, '', '');
//            $LINHA_RTDP[$mes] = (!empty($rowValorClt['a5021'])) ? number_format($rowValorClt['a5049'], 2, '', '') : 0;
        if($indDecTerceiro == 'N'){
           $indPerReferencia = 1;
           $perRefPagto = $beneficiario['ano'].'-'.$beneficiario['mes'];
        }else{
            $indPerReferencia = 2;
            $perRefPagto = $beneficiario['ano'];
        }
        $indPerReferencia = sprintf("%01s", $indPerReferencia);
        $perRefPagto = sprintf("%-7s", $perRefPagto);
        $vlrRendTributavelDetComp = sprintf("%14s", $beneficiario['vlrRendTrib']);
//        $vlrCompAnoCalendario = sprintf("%14s", $beneficiario['vlrCompAnoCalendario']);
//        $vlrCompAnosAnteriores = sprintf("%14s", $beneficiario['vlrCompAnosAnteriores']);
//        $vlrDepJudicial = sprintf("%14s", $beneficiario['vlrDepJudicial']);
//        $tpProcRRA = sprintf("%01s", $beneficiario['tpProcRRA']);
//        $nrProcRRA = sprintf("%-20s", $beneficiario['nrProcRRA']);
//        $natRRA = sprintf("%-50s", $beneficiario['natRRA']);
//        $qtdMesesRRA = sprintf("%04s", $beneficiario['qtdMesesRRA']);
//        $vlrDespCustas = sprintf("%14s", $beneficiario['vlrDespCustas']);
//        $vlrDespAdvogados = sprintf("%14s", $beneficiario['vlrDespAdvogados']);
//        $tpInscricaoAdvogado = sprintf("%01s", $beneficiario['tpInscricaoAdvogado']);
//        $nrInscricaoAdvogado = sprintf("%-14ss", $beneficiario['nrInscricaoAdvogado']);
//        $vlrAdvogado = sprintf("%14s", $beneficiario['vlrAdvogado']);
//        $nrProcJud = sprintf("%-20s", $beneficiario['nrProcJud']);
//        $indOrigemRecursos = sprintf("%01s", $beneficiario['indOrigemRecursos']);
//        $valorDespCustas = sprintf("%14s", $beneficiario['valorDespCustas']);
//        $valorDespAdvogados = sprintf("%14s", $beneficiario['valorDespAdvogados']);
//        $tpInscricaoAd = sprintf("%01s", $beneficiario['tpInscricaoAdv']);
//        $nrInscricaoAd = sprintf("%-14ss", $beneficiario['nrInscricaoAdv']);
//        $vlrAd = sprintf("%14s", $beneficiario['vlrAd']);
//        $cnpjOrigemRecursos = sprintf("%14s", $beneficiario['cnpjOrigemRecursos']);
////        BPJ
//        $dataPagto = sprintf("%-10s", $beneficiario['dataPagto']);
//        $vlrRendTrib = sprintf("%14s", $beneficiario['vlrRendTrib']);
//        $valorIRRF = sprintf("%14s", $beneficiario['valorIRRF']);
        $cnpjOperadora = sprintf("%14s", $beneficiario['cnpjOperadora']);
        $vlrPagoTitular = sprintf("%14s", $beneficiario['vlrPagoTitular']);
        $cpfDep = sprintf("%11s", $dependente['cpfDep']);
        $dtNascDep = sprintf("%-10s", $dependente['dtNascDep']);
        $nomeDep = sprintf("%-60s", $dependente['nomeDep']);
        $relDependencia = sprintf("%02s", $dependente['relDependencia']);
        $vlrPagoDep = sprintf("%14s", $dependente['vlrPagtoDep']);

        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        $eSocial = $dom->createElement("eSocial");
        $evt = $dom->createElement("evtPagtosDiversos");
        $ideEvento = $dom->createElement("ideEvento");
        $infindRetificacao = $dom->createElement("indRetificacao", $indRetificacao);
        if ($indRetificacao == 2) {
            $infnrRecibo = $dom->createElement("nrRecibo", $nrRecibo);
        }

        $inftpAmb = $dom->createElement("tpAmb", $tpAmb);
        $infprocEmi = $dom->createElement("procEmi", $procEmi);
        $infindSeguimento = $dom->createElement("indSeguimento", $indSeguimento);
        $infverProc = $dom->createElement("verProc", $verProc);
        $infindApuracao = $dom->createElement("indApuracao", $indApuracao);
        $infperApuracao = $dom->createElement("perApuracao", $perApuracao);
        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricao = $dom->createElement("tpInscricao", $tpInscricao);
        $infnrInscricao = $dom->createElement("nrInscricao", $nrInscricao);
        $ideBeneficiario = $dom->createElement("ideBeneficiario");
        $infcodRendimento = $dom->createElement("codRendimento", $codRendimento);
        $inftpInscricaoBeneficiario = $dom->createElement("tpInscBeneficiario", $tpInscricaoBeneficiario);
        $infnrInscricaoBeneficiario = $dom->createElement("nrInscBeneficiario", $nrInscricaoBeneficiario);
//        $dadosMolestiaGrave = $dom->createElement("dadosMolestiaGrave");
//        $infdtLaudo = $dom->createElement("dtLaudo", $dtLaudo);
        $infoPagto = $dom->createElement("infoPagto");
        $pagtoResidente = $dom->createElement("pagtoResidente");
        $pagtoBPF = $dom->createElement("pagtoBPF");
        $infdtPagto = $dom->createElement("dtPagto", $dtPagto);
        $infindSuspExigibilidade = $dom->createElement("indSuspExigibilidade", $indSuspExigibilidade);
        $infindDecTerceiro = $dom->createElement("indDecTerceiro", $indDecTerceiro);
        $infvlrRendTributavel = $dom->createElement("vlrRendTributavel", $vlrRendTributavel);
        $infvlrIRRF = $dom->createElement("vlrIRRF", $vlrIRRF);
//        $detOutros = $dom->createElement("detOutros");
//        $infdescRendimento = $dom->createElement("descRendimento",$descRendimento);
        $detCompetencia = $dom->createElement("detCompetencia");
        $infindPerReferencia = $dom->createElement("indPerReferencia", $indPerReferencia);
        $infperRefPagto = $dom->createElement("perRefPagto", $perRefPagto);
        $infvlrRendTributavelDetComp = $dom->createElement("vlrRendTributavelDetComp", $vlrRendTributavelDetComp);
//        $compJudicial = $dom->createElement("compJudicial");
//        $infvlrCompAnoCalendario = $dom->createElement("vlrCompAnoCalendario", $vlrCompAnoCalendario);
//        $infvlrCompAnosAnteriores = $dom->createElement("vlrcompAnosAnteriores", $$vlrCompAnosAnteriores);
//        $depJudicial = $dom->createElement("depJudicial");
//        $infvlrDepJudicial = $dom->createElement("vlrDepJudicial", $vlrDepJudicial);
//        $infoRRA = $dom->createElement("infoRRA");
//        $inftpProcRRA = $dom->createElement("tpProcRRA", $tpProcRRA);
//        $infnrProcRRA = $dom->createElement("nrProcRRA", $nrProcRRA);
//        $infnatRRA = $dom->createElement("natRRA", $natRRA);
//        $infqtdMesesRRA = $dom->createElement("qtdMesesRRA", $qtdMesesRRA);
//        $despProcJudicial = $dom->createElement("despProcJudicial");
//        $infvlrDespCustas = $dom->createElement("vlrDespCustas", $vlrDespCustas);
//        $infvlrDespAdvogados = $dom->createElement("vlrDespAdvogados", $vlrDespAdvogados);
//        $ideAdvogado = $dom->createElement("ideAdvogado");
//        $inftpInscricaoAdvogado = $dom->createElement("tpInscAdvogado", $tpInscricaoAdvogado);
//        $infnrInscricaoAdvogado = $dom->createElement("nrInscAdvogado", $nrInscricaoAdvogado);
//        $infvlrAdvogado = $dom->createElement("vlrAdvogado", $vlrAdvogado);
//        $infoProcJudicial = $dom->createElement("infoProcJudicail");
//        $infnrProcJud = $dom->createElement("nrProcJud", $nrProcJud);
//        $infindOrigemRecursos = $dom->createElement("indOrigemRecusos", $indOrigemRecursos);
//        $despProcJudicial2 = $dom->createElement("despProcJudicial");
//        $valorDespCustas = $dom->createElement("vlrDespCustas", $valorDespCustas);
//        $valorDespAdvogados = $dom->createElement("vlrDespAdvogados", $valorDespAdvogados);
//        $ideAdvogado2 = $dom->createElement("ideAdvogado");
//        $inftpInscricaoAd = $dom->createElement("tpInscAdvogado", $tpInscricaoAd);
//        $infnrInscricaoAd = $dom->createElement("nrInscAdvogado", $nrInscricaoAd);
//        $vlrAd = $dom->createElement("vlrAdvogado", $vlrAd);
//        $origemRecursos = $dom->createElement("origemRecursos");
//        $infcnpjOrigemRecursos = $dom->createElement("cnpjOrigemRecursos", $cnpjOrigemRecursos);
//        BPJ
//        $pagtoBPJ = $dom->createElement("pagtoBPJ");
//        $infdataPagto = $dom->createElement("dataPagto", $dataPagto);
//        $infvlrRendTrib = $dom->createElement("vlrRendTrib", $vlrRendTrib);
//        $infvalorIRRF = $dom->createElement("vlrIRRF", $valorIRRF);
        $infoPlanoSaudeColetivo = $dom->createElement("infoPlanoSaudeColetivo");
        $detOperadora = $dom->createElement("detOperadora");
        $infcnpjOperadora = $dom->createElement("cnpjOperadora", $cnpjOperadora);
        $infvlrPagoTitular = $dom->createElement("vlrPagoTitular", $vlrPagoTitular);
        $depPlano = $dom->createElement("depPlano");
        $infcpfDep = $dom->createElement("cpfDep", $cpfDep);
        $infdtNascDep = $dom->createElement("dtNascDep", $dtNascDep);
        $infnomeDep = $dom->createElement("nomeDep", $nomeDep);
        $infrelDependencia = $dom->createElement("relDependencia", $relDependencia);
        $infvlrPagoDep = $dom->createElement("vlrPagoDep", $vlrPagoDep);
        $ideBeneficiario->appendChild($infcodRendimento);
        $ideBeneficiario->appendChild($inftpInscricaoBeneficiario);
        $ideBeneficiario->appendChild($infnrInscricaoBeneficiario);
//        $dadosMolestiaGrave->appendChild($infdtLaudo);
//        $ideBeneficiario->appendChild($dadosMolestiaGrave);
        $pagtoBPF->appendChild($infdtPagto);
        $pagtoBPF->appendChild($infindSuspExigibilidade);
        $pagtoBPF->appendChild($infindDecTerceiro);
        $pagtoBPF->appendChild($infvlrRendTributavel);
        $pagtoBPF->appendChild($infvlrIRRF);
                        
        if (!empty($salario)) {
            foreach ($salario AS $ano => $array) {
                foreach ($array as $mes => $dados) {
                    foreach ($dados as $key => $value) {
                        if ($key == 'B2') {
                            foreach ($value as $chave => $arrayDet) {
                                foreach ($arrayDet as $det => $valor) {
                                    switch ($det) {
                                        case 'RTPO':
                                            $indTpDeducao = sprintf("%01s", '1');
                                            break;

                                        case 'RTPP':
                                            $indTpDeducao = sprintf("%01s", '2');
                                            break;
                                        case 'RTPA':
                                            $indTpDeducao = sprintf("%01s", '3');
                                            break;
                                        case 'RTDP':
                                            $indTpDeducao = sprintf("%01s", '4');
                                            break;
                                    }

                                    $vlrDeducao = sprintf("%14s", $valor);
                                    $detDeducao = $dom->createElement("detDeducao");
                                    $infindTpDeducao = $dom->createElement("indTpDeducao", $indTpDeducao);
                                    $infvlrDeducao = $dom->createElement("vlrDeducao", $vlrDeducao);
                                    $detDeducao->appendChild($infindTpDeducao);
                                    $detDeducao->appendChild($infvlrDeducao);
                                    $pagtoBPF->appendChild($detDeducao);
                                }
                            }
                        }
                        if ($key == 'B3') {
                            foreach ($value as $chave => $arrayRend) {
                                foreach ($arrayRend as $rend => $valor) {
                                    switch ($rend) {
                                        case 'RIDAC':
                                            $tpIsencao = sprintf("%01s", '2');
                                            break;
                                        case 'RIIRP':
                                            $tpIsencao = sprintf("%01s", '3');
                                            break;
                                        case 'RIAP':
                                            $tpIsencao = sprintf("%01s", '4');
                                            break;
                                    }
                                    $vlrIsento = sprintf("%14s", $valor);
                                    $rendIsento = $dom->createElement("rendIsento");
                                    $inftpIsencao = $dom->createElement("tpIsencao", $tpIsencao);
                                    $infvlrIsento = $dom->createElement("vlrIsento", $vlrIsento);
                                    $rendIsento->appendChild($inftpIsencao);
                                    $rendIsento->appendChild($infvlrIsento);
                                    $pagtoBPF->appendChild($rendIsento);
                                }
                            }
                        }
                    }

                    //        $detOutros->appendChild($infdescRendimento);
                    //        $rendIsento->appendChild($detOutros);
                }
            }
        }
        $detCompetencia->appendChild($infindPerReferencia);
        $detCompetencia->appendChild($infperRefPagto);
        $detCompetencia->appendChild($infvlrRendTributavelDetComp);
        $pagtoBPF->appendChild($detCompetencia);
//        $compJudicial->appendChild($infvlrCompAnoCalendario);
//        $compJudicial->appendChild($infvlrCompAnosAnteriores);
//        $pagtoBPF->appendChild($compJudicial);
//        $depJudicial->appendChild($infvlrDepJudicial);
//        $pagtoBPF->appendChild($depJudicial);
//        $infoRRA->appendChild($inftpProcRRA);
//        $infoRRA->appendChild($infnrProcRRA);
//        $infoRRA->appendChild($infnatRRA);
//        $infoRRA->appendChild($infqtdMesesRRA);
//        $despProcJudicial->appendChild($infvlrDespCustas);
//        $despProcJudicial->appendChild($infvlrDespAdvogados);
//        $ideAdvogado->appendChild($inftpInscricaoAdvogado);
//        $ideAdvogado->appendChild($infnrInscricaoAdvogado);
//        $ideAdvogado->appendChild($infvlrAdvogado);
//        $despProcJudicial->appendChild($ideAdvogado);
//        $infoRRA->appendChild($despProcJudicial);
//        $pagtoBPF->appendChild($infoRRA);
//        $infoProcJudicial->appendChild($infnrProcJud);
//        $infoProcJudicial->appendChild($infindOrigemRecursos);
//        $despProcJudicial2->appendChild($valorDespCustas);
//        $ideAdvogado2->appendChild($inftpInscricaoAd);
//        $ideAdvogado2->appendChild($infnrInscricaoAd);
//        $ideAdvogado2->appendChild($infvlrAd);
//        $despProcJudicial2->appendChild($ideAdvogado2);
//        $origemRecursos->appendChild($infcnpjOperadora);
//        $despProcJudicial2->appendChild($origemRecursos);
//        $infoProcJudicial->appendChild($despProcJudicial2);
//        $pagtoBPF->appendChild($infoProcJudicial);
        $pagtoResidente->appendChild($pagtoBPF);
//        BPJ
//        $pagtoBPJ->appendChild($infdataPagto);
//        $pagtoBPJ->appendChild($infvlrRendTrib);
//        $pagtoBPJ->appendChild($infvalorIRRF);
//        $pagtoResidente->appendChild($pagtoBPJ);
        $infoPagto->appendChild($pagtoResidente);
        $ideBeneficiario->appendChild($infoPagto);
//        $detOperadora->appendChild($infcnpjOperadora);
//        $detOperadora->appendChild($infvlrPagoTitular);
//        $depPlano->appendChild($infcpfDep);
//        $depPlano->appendChild($infdtNascDep);
//        $depPlano->appendChild($infnomeDep);
//        $depPlano->appendChild($infcpfDep);
//        $depPlano->appendChild($infrelDependencia);
//        $depPlano->appendChild($infvlrPagoDep);
//        $detOperadora->appendChild($depPlano);
//
//        $infoPlanoSaudeColetivo->appendChild($detOperadora);
//        $ideBeneficiario->appendChild($infoPlanoSaudeColetivo);
        $ideEmpregador->appendChild($inftpInscricao);
        $ideEmpregador->appendChild($infnrInscricao);
        $ideEvento->appendChild($infindRetificacao);
        if ($indRetificacao == 2) {
            $ideEvento->appendChild($infnrRecibo);
        }
        $ideEvento->appendChild($infindApuracao);
        $ideEvento->appendChild($infperApuracao);
        $ideEvento->appendChild($inftpAmb);
        $ideEvento->appendChild($infprocEmi);
        $ideEvento->appendChild($infindSeguimento);
        $ideEvento->appendChild($infverProc);
        $evt->appendChild($ideEvento);
        $evt->appendChild($ideEmpregador);
        $evt->appendChild($ideBeneficiario);
        $evt->setAttribute("Id", $id);
        $evt->setAttribute("versao", $versao);
        $eSocial->appendChild($evt);
        $dom->appendChild($eSocial);
        $this->sequencial++;

//        echo $dom->saveXML();
        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

    public function montas1330($arquivo, $empregador, $servTomados, $numRecibo) {     

        if ($this->tpevento == "inclusao") {
            $indRetificacao = 1;
        } elseif ($this->tpevento == "alteracao") {
            $indRetificacao = 2;
            $nrRecibo = $numRecibo;
            $nrRecibo = sprintf("%15s", RemoveEspacos($nrRecibo));
        }
        $indRetificacao = sprintf("%01s", $indRetificacao); //1 - ARQUIVO ORIGINAL 2 - ARQUIVO DE RETIFICAÇÃO
        $tpInscricaoEmpreg = sprintf("%01s", $servTomados["tpInscricaoEmpreg"]);
        $nrInscricaoEmpreg = sprintf("%015s", $servTomados["nrInscricaoEmpreg"]);
        $tpInscricaoEstab = sprintf("%01s", $servTomados["tpInscricaoEstab"]);
        $nrInscricaoEstab = sprintf("%015s", $servTomados["nrInscricaoEstab"]);
        $cnpjCooperativa = sprintf("%014s", $servTomados["cnpjCooperativa"]);
        $serie = sprintf("%-5s", $servTomados['serie']);
        $numDocto = sprintf("%-10s", $servTomados['numero_documento']);
        $dtEmissaoNF = sprintf("%-10s", $servTomados['data_emissao_nf']);
        $indObra = sprintf("%01s", 0); // CONSTANTE
        if ($indObra != 0){
        $nrCno = sprintf("%12s", $servTomados['nrCno']);
        }
        $vlrBruto = sprintf("%014s", $servTomados['valor_bruto_nf']);
        $vlrMatEquip = sprintf("%014s", $servTomados['vlrMatEquip']);
        $vlrServicos = sprintf("%014s", $servTomados['vlrServicos']);
        $vlrDeducoes = sprintf("%014s", $servTomados['vlrDeducoes']);
        $vlrBaseCoop = sprintf("%014s", $servTomados['vlrBaseCoop']);
        $vlrServicos15 = sprintf("%014s", $servTomados['vlrServicos15']);
        $vlrServicos20 = sprintf("%014s", $servTomados['vlrServicos20']);
        $vlrServicos25 = sprintf("%014s", $servTomados['vlrServicos25']);
        $valorBaseCoop = sprintf("%014s", $servTomados['valorBaseCoop']);
        $valorBaseCoop15 = sprintf("%014s", $servTomados['valorBaseCoop15']);
        $valorBaseCoop20 = sprintf("%014s", $servTomados['valorBaseCoop20']);
        $valorBaseCoop25 = sprintf("%014s", $servTomados['valorBaseCoop25']);
        $tpInscricaoProp = sprintf("%01s", $servTomados["tpInscricaoProp"]);
        $nrInscricaoProp = sprintf("%015s", $servTomados["nrInscricaoProp"]);
        $indIncidencia = sprintf("%01s", 1); // CONSTANTE PQ NOSSA CLASS_TRIB = 99
        $vlrBaseCooperado = sprintf("%14s", $servTomados['vlrBaseCooperado']);
        $vlrBaseCooperado15 = sprintf("%14s", $servTomados['vlrBaseCooperado15']);
        $vlrBaseCooperado20 = sprintf("%14s", $servTomados['vlrBaseCooperado20']);
        $vlrBaseCooperado25 = sprintf("%14s", $servTomados['vlrBaseCooperado25']);
//        $tpProcesso = sprintf("%01s", $servTomados['tpProcesso']);
//        $nrProcesso = sprintf("%-20s", $servTomados['nrProcesso']);
//        $indAbrangencia = sprintf("%01s", $servTomados['indAbrangencia']);
//        $tpInscricaoDetEstab = sprintf("%01s", $servTomados["tpInscricaoDetEstab"]);
//        $nrInscricaoDetEstab = sprintf("%015s", $servTomados["nrInscricaoDetEstab"]);

        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        $eSocial = $dom->createElement("eSocial");
        
        $dadosCabecalho['indRetificacao'] = $indRetificacao;
        if ($indRetificacao == 2) {
            //$infnrRecibo = $dom->createElement("nrRecibo", $nrRecibo);  //ac cab
            $dadosCabecalho['nrRecibo'] = $nrRecibo;
        }

        $xmlCabecalho = $this->montaCabecalho($empregador, $dom, 'evtFpServTomadosCoop', $dadosCabecalho);
        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricaoEmpreg = $dom->createElement("tpInscricao", $tpInscricaoEmpreg);
        $infnrInscricaoEmpreg = $dom->createElement("nrInscricao", $nrInscricaoEmpreg);
        $infoServTomados = $dom->createElement("infoServTomados");
        $ideEstabelecimento = $dom->createElement("ideEstabelecimento");
        $inftpInscricaoEstab = $dom->createElement("tpInscricao", $tpInscricaoEstab);
        $infnrInscricaoEstab = $dom->createElement("nrInscricao", $nrInscricaoEstab);
        $nfsTerceiros = $dom->createElement("nfsTerceiros");
        $infcnpjCooperativa = $dom->createElement("cnpjCooperativa", $cnpjCooperativa);
        $infserie = $dom->createElement("serie", $serie);
        $infnumDocto = $dom->createElement("numDocto", $numDocto);
        $infdtEmissaoNF = $dom->createElement("dtEmissaoNF", $dtEmissaoNF);
        $infindObra = $dom->createElement("indObra", $indObra);
        $infnrCno = $dom->createElement("nrCno", $nrCno);
        $infvlrBruto = $dom->createElement("vlrBruto", $vlrBruto);
        $infvlrMatEquip = $dom->createElement("vlrMatEquip", $vlrMatEquip);
        $infvlrServicos = $dom->createElement("vlrServicos", $vlrServicos);
        $infvlrDeducoes = $dom->createElement("vlrDeducoes", $vlrDeducoes);
        $infvlrBaseCoop = $dom->createElement("vlrBaseCoop", $vlrBaseCoop);
        $servCoopCondEspeciais = $dom->createElement("servCoopCondEspeciais");
        $infvlrServicos15 = $dom->createElement("vlrServicos15", $vlrServicos15);
        $infvlrServicos20 = $dom->createElement("vlrServicos20", $vlrServicos20);
        $infvlrServicos25 = $dom->createElement("vlrServicos25", $vlrServicos25);
        $servPrestAtivConcomitantes = $dom->createElement("servPrestAtivConcomitantes");
        $infvalorBaseCoop = $dom->createElement("vlrBaseCoop", $valorBaseCoop);
        $infvalorBaseCoop15 = $dom->createElement("vlrBaseCoop15", $valorBaseCoop15);
        $infvalorBaseCoop20 = $dom->createElement("vlrBaseCoop20", $valorBaseCoop20);
        $infvalorBaseCoop25 = $dom->createElement("vlrBaseCoop25", $valorBaseCoop25);
        $proprietarioCno = $dom->createElement("proprietarioCNO");
        $inftpInscricaoProp = $dom->createElement("tpInscricao", $tpInscricaoProp);
        $infnrInscricaoProp = $dom->createElement("nrInscricao", $nrInscricaoProp);
        $totalBaseCoop = $dom->createElement("totBaseCoop");
        $infindIncidencia = $dom->createElement("indIncidencia", $indIncidencia);
        $infvlrBaseCooperado = $dom->createElement("vlrBaseCoop", $vlrBaseCooperado);
        $infvlrBaseCooperado15 = $dom->createElement("vlrBaseCoop", $vlrBaseCooperado15);
        $infvlrBaseCooperado20 = $dom->createElement("vlrBaseCoop", $vlrBaseCooperado20);
        $infvlrBaseCooperado25 = $dom->createElement("vlrBaseCoop", $vlrBaseCooperado25);
//        $infoProcesso = $dom->createElement("infoProcesso");
//        $ideProcesso = $dom->createElement("ideProcesso");
//        $inftpProcesso = $dom->createElement("tpProcesso", $tpProcesso);
//        $infnrProcesso = $dom->createElement("nrProcesso", $nrProcesso);
//        $infindAbrangencia = $dom->createElement("indAbrangencia", $indAbrangencia);
//        $detEstabelevimento = $dom->createElement("detEstabelecimento");
//        $inftpInscricaoDetEstb = $dom->createElement("tpInscricao", $tpInscricaoDetEstab);
//        $infnrInscricaoDetEstb = $dom->createElement("nrInscricao", $nrInscricaoDetEstab);


        $ideEstabelecimento->appendChild($inftpInscricaoEstab);
        $ideEstabelecimento->appendChild($infnrInscricaoEstab);
        $nfsTerceiros->appendChild($infcnpjCooperativa);
        $nfsTerceiros->appendChild($infserie);
        $nfsTerceiros->appendChild($infnumDocto);
        $nfsTerceiros->appendChild($infdtEmissaoNF);
        $nfsTerceiros->appendChild($infindObra);
        if ($indObra != 0){
            $nfsTerceiros->appendChild($infnrCno);
        }
        $nfsTerceiros->appendChild($infvlrBruto);
        $nfsTerceiros->appendChild($infvlrMatEquip);
        $nfsTerceiros->appendChild($infvlrServicos);
        $nfsTerceiros->appendChild($infvlrDeducoes);
        $nfsTerceiros->appendChild($infvlrBaseCoop);
        $servCoopCondEspeciais->appendChild($infvlrServicos15);
        $servCoopCondEspeciais->appendChild($infvlrServicos20);
        $servCoopCondEspeciais->appendChild($infvlrServicos25);
        $nfsTerceiros->appendChild($servCoopCondEspeciais);
        $servPrestAtivConcomitantes->appendChild($infvalorBaseCoop);
        $servPrestAtivConcomitantes->appendChild($infvalorBaseCoop15);
        $servPrestAtivConcomitantes->appendChild($infvalorBaseCoop20);
        $servPrestAtivConcomitantes->appendChild($infvalorBaseCoop25);
        $nfsTerceiros->appendChild($servPrestAtivConcomitantes);
        $proprietarioCno->appendChild($inftpInscricaoProp);
        $proprietarioCno->appendChild($infnrInscricaoProp);
        $nfsTerceiros->appendChild($proprietarioCno);
        $ideEstabelecimento->appendChild($nfsTerceiros);
        $totalBaseCoop->appendChild($infindIncidencia);
        $totalBaseCoop->appendChild($infvlrBaseCooperado);
        $totalBaseCoop->appendChild($infvlrBaseCooperado15);
        $totalBaseCoop->appendChild($infvlrBaseCooperado20);
        $totalBaseCoop->appendChild($infvlrBaseCooperado25);
        $ideEstabelecimento->appendChild($totalBaseCoop);
        $infoServTomados->appendChild($ideEstabelecimento);
//        $ideProcesso->appendChild($inftpProcesso);
//        $ideProcesso->appendChild($infnrProcesso);
//        $ideProcesso->appendChild($infindAbrangencia);
//        $detEstabelevimento->appendChild($inftpInscricaoDetEstb);
//        $detEstabelevimento->appendChild($infnrInscricaoDetEstb);
//        $ideProcesso->appendChild($detEstabelevimento);
//        $infoProcesso->appendChild($ideProcesso);
//        $infoServTomados->appendChild($infoProcesso);
        $ideEmpregador->appendChild($inftpInscricaoEmpreg);
        $ideEmpregador->appendChild($infnrInscricaoEmpreg);
        
        $xmlCabecalho->appendChild($ideEmpregador);
        $xmlCabecalho->appendChild($infoServTomados);
        
        $eSocial->appendChild($xmlCabecalho);
        $dom->appendChild($eSocial);
        $this->sequencial++;

//        echo $dom->saveXML();
        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

    public function montas1340($arquivo, $empregador, $servPrestados, $nfs) {
        $tpInscricaoMaster = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricaoMaster = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));
        $dt_hs_atual = str_replace(" ", "", RemoveCaracteres(date("Y-m-d H:i:s", time())));
        $sequencial = sprintf("%05s", $this->sequencial);

        $id = sprintf("%-36s", "ID" . $tpInscricaoMaster . $nrInscricaoMaster . $dt_hs_atual . $sequencial);
        $versao = sprintf("%-11s", "1.2 - beta2"); // VERSÃO DO LAYOUT
        $tpAmb = sprintf("%01s", 1); // CONSTANTE
        $procEmi = sprintf("%01s", 1); // CONSTANTE
        $indSeguimento = sprintf("%01s", 1); // CONSTANTE
        $verProc = sprintf("%-20s", "Sei lah que versao ah essa"); // FALTA SABER VERSÃO DO App EMISSOR DO EVENTO

        if ($this->tpevento == "inclusao") {
            $indRetificacao = 1;
        } elseif ($this->tpevento == "alteracao") {
            $indRetificacao = 2;
            $nrRecibo = $numRecibo;
            $nrRecibo = sprintf("%15s", RemoveEspacos($nrRecibo));
        }
        $indRetificacao = sprintf("%01s", $indRetificacao); //1 - ARQUIVO ORIGINAL 2 - ARQUIVO DE RETIFICAÇÃO
        $tpInscricaoEmpreg = sprintf("%01s", $servPrestados["tpInscricaoEm"]);
        $nrInscricaoEmpreg = sprintf("%015s", $servPrestados["nrInscricaoEm"]);
        $tpInscricaoPres = sprintf("%01s", $servPrestados["tpInscricaoPres"]);
        $nrInscricaoPres = sprintf("%014s", $servPrestados["nrInscricaoPres"]);
        $tpInscricaoCon = sprintf("%01s", $servPrestados["tpInscricaoCon"]);
        $nrInscricaoCon = sprintf("%015s", $servPrestados["nrInscricaoCon"]);
        $vlrBruto = sprintf("%14s", $servPrestados['vlrBruto']);
        $vlrBaseCoop = sprintf("%14s", $servPrestados['vlrCoop']);
        $serie = sprintf("%-5s", $nfs['serie']);
        $numDocto = sprintf("%-10s", $nfs['numDocto']);
        $dtEmissaoNF = sprintf("%-10s", $nfs['dtEmissaoNF']);
        $indObra = sprintf("%01s", $nfs['indObra']);
        $nrCno = sprintf("%012s", $nfs['nrCno']);
        $vlrBrutoNF = sprintf("%14s", $nfs['vlrBruto']);
        $vlrMatEquipNF = sprintf("%14s", $nfs['vlrMatEquip']);
        $vlrServicosNF = sprintf("%14s", $nfs['vlrServicos']);
        $vlrDeducoesNF = sprintf("%14s", $nfs['vlrDeducoes']);
        $vlrBaseCoopNF = sprintf("%14s", $nfs['vlrBaseCoop']);
        $vlrServicos15 = sprintf("%14s", $nfs['vlrServicos15']);
        $vlrServicos20 = sprintf("%14s", $nfs['vlrServicos20']);
        $vlrServicos25 = sprintf("%14s", $nfs['vlrServicos25']);
        $tpInscricaoPro = sprintf("%01s", $servPrestados["tpInscricaoPro"]);
        $nrInscricaoPro = sprintf("%014s", $servPrestados["nrInscricaoPro"]);

        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        $eSocial = $dom->createElement("eSocial");
        $evt = $dom->createElement("evtFpServPrestadosCoop");
        $ideEvento = $dom->createElement("ideEvento");
        $infindRetificacao = $dom->createElement("indRetificacao", $indRetificacao);
        if ($indRetificacao == 2) {
            $infnrRecibo = $dom->createElement("nrRecibo", $nrRecibo);
        }

        $inftpAmb = $dom->createElement("tpAmb", $tpAmb);
        $infprocEmi = $dom->createElement("procEmi", $procEmi);
        $infindSeguimento = $dom->createElement("indSeguimento", $indSeguimento);
        $infverProc = $dom->createElement("verProc", $verProc);
        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricaoEmpreg = $dom->createElement("tpInscricao", $tpInscricaoEmpreg);
        $infnrInscricaoEmpreg = $dom->createElement("nrInscricao", $nrInscricaoEmpreg);
        $infoServPrestados = $dom->createElement("infoServPrestados");
        $ideEstabPrestador = $dom->createElement("ideEstabPrestador");
        $inftpInscricaoPres = $dom->createElement("tpInscricao", $tpInscricaoPres);
        $infnrInscricaoPres = $dom->createElement("nrInscricao", $nrInscricaoPres);
        $ideContratante = $dom->createElement("ideContratante");
        $inftpInscricaoCon = $dom->createElement("tpInscricao", $tpInscricaoCon);
        $infnrInscricaoCon = $dom->createElement("nrInscricao", $nrInscricaoCon);
        $infvlrBruto = $dom->createElement("vlrBruto", $vlrBruto);
        $infvlrBaseCoop = $dom->createElement("vlrBaseCoop", $vlrBaseCoop);
        $nfsEmitida = $dom->createElement("nfsEmitida");
        $infserie = $dom->createElement("serie");
        $infnumDocto = $dom->createElement("numDocto", $numDocto);
        $infdtEmissaoNF = $dom->createElement("dtEmissaoNF", $dtEmissaoNF);
        $infindObraNF = $dom->createElement("infObra", $indObra);
        $infnrCnoNF = $dom->createElement("nrCno", $nrCno);
        $infvlrBrutoNF = $dom->createElement("vlrBruto", $vlrBrutoNF);
        $infvlrMatEquipNF = $dom->createElement("vlrMatEquip", $vlrMatEquipNF);
        $infvlrServicos = $dom->createElement("vlrServicos", $vlrServicosNF);
        $infvlrDeducoes = $dom->createElement("vlrDeducoes", $vlrDeducoesNF);
        $infvlrBaseCoopNF = $dom->createElement("vlrBaseCoop", $vlrBaseCoopNF);
        $servCoopCondEspeciais = $dom->createElement("servCoopCondEspeciais");
        $infvlrServicos15 = $dom->createElement("vlrServcos15", $vlrServicos15);
        $infvlrServicos20 = $dom->createElement("vlrServicos20", $vlrServicos20);
        $infvlrServicos25 = $dom->createElement("vlrServicos25", $vlrServicos25);
        $proprietarioCNO = $dom->createElement("proprietarioCNO");
        $inftpInscricaoPro = $dom->createElement("tpInscricao", $tpInscricaoPro);
        $infnrInscricaoPro = $dom->createElement("nrInscricao", $nrInscricaoPro);

        $ideEstabPrestador->appendChild($inftpInscricaoPres);
        $ideEstabPrestador->appendChild($infnrInscricaoPres);
        $ideContratante->appendChild($inftpInscricaoCon);
        $ideContratante->appendChild($infnrInscricaoCon);
        $ideContratante->appendChild($infvlrBruto);
        $ideContratante->appendChild($infvlrBaseCoop);
        $nfsEmitida->appendChild($infserie);
        $nfsEmitida->appendChild($infnumDocto);
        $nfsEmitida->appendChild($infdtEmissaoNF);
        $nfsEmitida->appendChild($infdtEmissaoNF);
        $nfsEmitida->appendChild($infindObraNF);
        $nfsEmitida->appendChild($infnrCnoNF);
        $nfsEmitida->appendChild($infvlrBrutoNF);
        $nfsEmitida->appendChild($infvlrMatEquipNF);
        $nfsEmitida->appendChild($infvlrServicos);
        $nfsEmitida->appendChild($infvlrDeducoes);
        $nfsEmitida->appendChild($infvlrBaseCoopNF);
        $servCoopCondEspeciais->appendChild($infvlrServicos15);
        $servCoopCondEspeciais->appendChild($infvlrServicos20);
        $servCoopCondEspeciais->appendChild($infvlrServicos25);
        $nfsEmitida->appendChild($servCoopCondEspeciais);
        $proprietarioCNO->appendChild($inftpInscricaoPro);
        $proprietarioCNO->appendChild($infnrInscricaoPro);
        $nfsEmitida->appendChild($proprietarioCNO);
        $ideContratante->appendChild($nfsEmitida);
        $ideEstabPrestador->appendChild($ideContratante);
        $infoServPrestados->appendChild($ideEstabPrestador);
        $ideEmpregador->appendChild($inftpInscricaoEmpreg);
        $ideEmpregador->appendChild($infnrInscricaoEmpreg);
        $ideEvento->appendChild($infindRetificacao);
        if ($indRetificacao == 2) {
            $ideEvento->appendChild($infnrRecibo);
        }

        $ideEvento->appendChild($inftpAmb);
        $ideEvento->appendChild($infprocEmi);
        $ideEvento->appendChild($infindSeguimento);
        $ideEvento->appendChild($infverProc);
        $evt->appendChild($ideEvento);
        $evt->appendChild($ideEmpregador);
        $evt->appendChild($infoServPrestados);
        $evt->setAttribute("Id", $id);
        $evt->setAttribute("versao", $versao);
        $eSocial->appendChild($evt);
        $dom->appendChild($eSocial);
        $this->sequencial++;

//        echo $dom->saveXML();
        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

    public function montas1370($arquivo, $empregador, $assocDesp, $numRecibo) {
        $tpInscricaoMaster = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricaoMaster = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));
        $dt_hs_atual = str_replace(" ", "", RemoveCaracteres(date("Y-m-d H:i:s", time())));
        $sequencial = sprintf("%05s", $this->sequencial);

        $id = sprintf("%-36s", "ID" . $tpInscricaoMaster . $nrInscricaoMaster . $dt_hs_atual . $sequencial);
        $versao = sprintf("%-11s", "1.2 - beta2"); // VERSÃO DO LAYOUT
        $tpAmb = sprintf("%01s", 1); // CONSTANTE
        $procEmi = sprintf("%01s", 1); // CONSTANTE
        $indSeguimento = sprintf("%01s", 1); // CONSTANTE
        $verProc = sprintf("%-20s", "Sei lah que versao ah essa"); // FALTA SABER VERSÃO DO App EMISSOR DO EVENTO

        if ($this->tpevento == "inclusao") {
            $indRetificacao = 1;
        } elseif ($this->tpevento == "alteracao") {
            $indRetificacao = 2;
            $nrRecibo = $numRecibo;
            $nrRecibo = sprintf("%15s", RemoveEspacos($nrRecibo));
        }
        $indRetificacao = sprintf("%01s", $indRetificacao); //1 - ARQUIVO ORIGINAL 2 - ARQUIVO DE RETIFICAÇÃO
        $tpInscricaoEmpreg = sprintf("%01s", $assocDesp["tpInscricaoEm"]);
        $nrInscricaoEmpreg = sprintf("%015s", $assocDesp["nrInscricaoEm"]);
        $cnpjEmpOrigemRecurso = sprintf("%014s", $assocDesp['cnpjEmpOrigemRecurso']);
        $tpRepasse = sprintf("%01s", $assocDesp['tpRepasse']);
        $dtRepasse = sprintf("%-10s", $assocDesp['dtRepasse']);
        $vlrRepasse = sprintf("%14s", $assocDesp['vlrRepasse']);
        $vlrRetencao = sprintf("%14s", $assocDesp['vlrRetencao']);
        $tpProcesso = sprintf("%01s", $assocDesp['tpProcesso']);
        $nrProcesso = sprintf("%20s", $assocDesp['nrProcesso']);
        $tpInscricaoEstab = sprintf("%01s", $assocDesp['tpInscricaoEstab']);
        $nrInscricaoEstab = sprintf("%015s", $assocDesp['nrInscricaoEstab']);
        $vlrRepasseTotal = sprintf("%14s", $assocDesp['vlrRepasseTotal']);
        $cnpjAssocDesportiva = sprintf("%014s", $assocDesp['cnpjAssocDesportiva']);
        $tpRepasseDesp = sprintf("%01s", $assocDesp['tpRepasse']);
        $dtRepasseDesp = sprintf("%-10s", $assocDesp['dtRepasseDesp']);
        $vlrRepasseDesp = sprintf("%14s", $assocDesp['vlrRepasseDesp']);
        $vlrRetencaoDesp = sprintf("%14s", $assocDesp['vlrRetencaoDesp']);
        $tpProcessoDesp = sprintf("%01s", $assocDesp['tpProcessoDesp']);
        $nrProcessoDesp = sprintf("%20s", $assocDesp['nrProcessoDesp']);

        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        $eSocial = $dom->createElement("eSocial");
        $evt = $dom->createElement("evtFpAssocDesportiva");
        $ideEvento = $dom->createElement("ideEvento");
        $infindRetificacao = $dom->createElement("indRetificacao", $indRetificacao);
        if ($indRetificacao == 2) {
            $infnrRecibo = $dom->createElement("nrRecibo", $nrRecibo);
        }

        $inftpAmb = $dom->createElement("tpAmb", $tpAmb);
        $infprocEmi = $dom->createElement("procEmi", $procEmi);
        $infindSeguimento = $dom->createElement("indSeguimento", $indSeguimento);
        $infverProc = $dom->createElement("verProc", $verProc);
        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricaoEmpreg = $dom->createElement("tpInscricao", $tpInscricaoEmpreg);
        $infnrInscricaoEmpreg = $dom->createElement("nrInscricao", $nrInscricaoEmpreg);
        $infoRecursoRecebido = $dom->createElement("infoRecursoRecebido");
        $recursosRecebidos = $dom->createElement("recursosRecebidos");
        $infcnpjEmpOrigemRecurso = $dom->createElement("cnpjEmpOrigemRecurso", $cnpjEmpOrigemRecurso);
        $inftpRepasse = $dom->createElement("tpRepasse", $tpRepasse);
        $infdtRepasse = $dom->createElement("dtRepasse", $dtRepasse);
        $infvlrRepasse = $dom->createElement("vlrRepasse", $vlrRepasse);
        $infvlrRetencao = $dom->createElement("vlrRetencao", $vlrRetencao);
        $infoProcessos = $dom->createElement("infoProcessos");
        $ideProcesso = $dom->createElement("ideProcesso");
        $inftpProcesso = $dom->createElement("tpProcesso", $tpProcesso);
        $infnrProcesso = $dom->createElement("nrProcesso", $nrProcesso);
        $infoRecursoRepassado = $dom->createElement("infoRecursoRepassado");
        $ideEstabelecimento = $dom->createElement("ideEstabelecimento");
        $inftpInscricaoEstab = $dom->createElement("tpInscricao", $tpInscricaoEstab);
        $infnrInscricaoEstab = $dom->createElement("nrIsncricao", $nrInscricaoEstab);
        $infvlrRepasseTotal = $dom->createElement("vlrTotalRepasses", $vlrRepasseTotal);
        $recursosRepassados = $dom->createElement("recursosRepassados");
        $infcnpjAssocDesportiva = $dom->createElement("cnpjAssocDesportiva", $cnpjAssocDesportiva);
        $inftpRepasseDesp = $dom->createElement("tpRepasse", $tpRepasseDesp);
        $infdtRepasseDesp = $dom->createElement("dtRepasse", $dtRepasseDesp);
        $infvlrRepasseDesp = $dom->createElement("vlrRepasse", $vlrRepasseDesp);
        $infvlrRetencaoDesp = $dom->createElement("vlrRetencao", $vlrRetencaoDesp);
        $infoProcessosDesp = $dom->createElement("infoProcesso");
        $ideProcessoDesp = $dom->createElement("ideProcesso");
        $inftpProcessoDesp = $dom->createElement("tpProcesso", $tpProcessoDesp);
        $infnrProcessoDesp = $dom->createElement("nrProcessoDesp", $nrProcessoDesp);

        $ideEstabelecimento->appendChild($inftpInscricaoEstab);
        $ideEstabelecimento->appendChild($infnrInscricaoEstab);
        $ideEstabelecimento->appendChild($infvlrRepasseTotal);
        $recursosRepassados->appendChild($infcnpjAssocDesportiva);
        $recursosRepassados->appendChild($inftpRepasseDesp);
        $recursosRepassados->appendChild($infdtRepasseDesp);
        $recursosRepassados->appendChild($infvlrRepasseDesp);
        $recursosRepassados->appendChild($infvlrRetencaoDesp);
        $ideProcessoDesp->appendChild($inftpProcessoDesp);
        $ideProcessoDesp->appendChild($infnrProcessoDesp);
        $infoProcessosDesp->appendChild($ideProcessoDesp);
        $recursosRepassados->appendChild($infoProcessosDesp);
        $ideEstabelecimento->appendChild($recursosRepassados);
        $infoRecursoRepassado->appendChild($ideEstabelecimento);
        $recursosRecebidos->appendChild($infcnpjEmpOrigemRecurso);
        $recursosRecebidos->appendChild($inftpRepasse);
        $recursosRecebidos->appendChild($infdtRepasse);
        $recursosRecebidos->appendChild($infvlrRepasse);
        $recursosRecebidos->appendChild($infvlrRetencao);
        $ideProcesso->appendChild($inftpProcesso);
        $ideProcesso->appendChild($infnrProcesso);
        $infoProcessos->appendChild($ideProcesso);
        $recursosRecebidos->appendChild($infoProcessos);
        $infoRecursoRecebido->appendChild($recursosRecebidos);
        $ideEmpregador->appendChild($inftpInscricaoEmpreg);
        $ideEmpregador->appendChild($infnrInscricaoEmpreg);
        $ideEvento->appendChild($infindRetificacao);
        if ($indRetificacao == 2) {
            $ideEvento->appendChild($infnrRecibo);
        }

        $ideEvento->appendChild($inftpAmb);
        $ideEvento->appendChild($infprocEmi);
        $ideEvento->appendChild($infindSeguimento);
        $ideEvento->appendChild($infverProc);
        $evt->appendChild($ideEvento);
        $evt->appendChild($ideEmpregador);
        $evt->appendChild($infoRecursoRecebido);
        $evt->appendChild($infoRecursoRepassado);
        $evt->setAttribute("Id", $id);
        $evt->setAttribute("versao", $versao);
        $eSocial->appendChild($evt);
        $dom->appendChild($eSocial);
        $this->sequencial++;

//        echo $dom->saveXML();
        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

    public function montas1399($arquivo, $empregador, $fechamento) {
        $tpInscricaoMaster = sprintf("%01s", $empregador["tpInscricao"]);
        $nrInscricaoMaster = sprintf("%014s", RemoveEspacos(RemoveCaracteres($empregador["nrInscricao"])));
        $dt_hs_atual = str_replace(" ", "", RemoveCaracteres(date("Y-m-d H:i:s", time())));
        $sequencial = sprintf("%05s", $this->sequencial);

        $id = sprintf("%-36s", "ID" . $tpInscricaoMaster . $nrInscricaoMaster . $dt_hs_atual . $sequencial);
        $versao = sprintf("%-11s", "1.2 - beta2"); // VERSÃO DO LAYOUT
        $tpAmb = sprintf("%01s", 1); // CONSTANTE
        $procEmi = sprintf("%01s", 1); // CONSTANTE
        $indSeguimento = sprintf("%01s", 1); // CONSTANTE
        $verProc = sprintf("%-20s", "Sei lah que versao ah essa"); // FALTA SABER VERSÃO DO App EMISSOR DO EVENTO

        if ($this->tpevento == "inclusao") {
            $indRetificacao = 1;
        } elseif ($this->tpevento == "alteracao") {
            $indRetificacao = 2;
            $nrRecibo = $numRecibo;
            $nrRecibo = sprintf("%15s", RemoveEspacos($nrRecibo));
        }
        $indRetificacao = sprintf("%01s", $indRetificacao); //1 - ARQUIVO ORIGINAL 2 - ARQUIVO DE RETIFICAÇÃO
        $tpInscricaoEmpreg = sprintf("%01s", $fechamento["tpInscricaoEm"]);
        $nrInscricaoEmpreg = sprintf("%015s", $fechamento["nrInscricaoEm"]);
        $evtRemuneracao = sprintf("%-1s", $fechamento);
        $evtServTomadosCMO = sprintf("%-1s", 'N');
        $evtServPrestadosCMO = sprintf("%-1s", 'N');
        $evtServTomadosCOOP = sprintf("%-1s", $fechamento);
        $evtServPrestadosCOOP = sprintf("%-1s", $fechamento);
        $evtAquisProdRural = sprintf("%-1s", 'N');
        $evtComercProducao = sprintf("%-1s", 'N');
        $evtRepasseClubeFutebol = sprintf("%-1s", $fechamento);
//        $evtInfoDesoneracao = sprintf("%-1s", 'N');
//        $evtReceitaAtivConcomitantes = sprintf("%-1s", 'N');
        #versao do encoding xml
        $dom = new DOMDocument("1.0", "ISO-8859-1");

        #retirar os espacos em branco
        $dom->preserveWhiteSpace = false;

        #gerar o codigo
        $dom->formatOutput = true;

        $eSocial = $dom->createElement("eSocial");
        $evt = $dom->createElement("evtFpFechamento");
        $ideEvento = $dom->createElement("ideEvento");
        $infindRetificacao = $dom->createElement("indRetificacao", $indRetificacao);
        if ($indRetificacao == 2) {
            $infnrRecibo = $dom->createElement("nrRecibo", $nrRecibo);
        }

        $inftpAmb = $dom->createElement("tpAmb", $tpAmb);
        $infprocEmi = $dom->createElement("procEmi", $procEmi);
        $infindSeguimento = $dom->createElement("indSeguimento", $indSeguimento);
        $infverProc = $dom->createElement("verProc", $verProc);
        $ideEmpregador = $dom->createElement("ideEmpregador");
        $inftpInscricaoEmpreg = $dom->createElement("tpInscricao", $tpInscricaoEmpreg);
        $infnrInscricaoEmpreg = $dom->createElement("nrInscricao", $nrInscricaoEmpreg);
        $infoFechamento = $dom->createElement("infoFechamento");
        $infevtRemuneracao = $dom->createElement("evtRemuneracao", $evtRemuneracao);
        $infevtServTomadosCMO = $dom->createElement("evtServTomadosCMO", $evtServTomadosCMO);
        $infevtServPrestadosCMO = $dom->createElement("evtServPrestadosCMO", $evtServPrestadosCMO);
        $infevtServTomadosCOOP = $dom->createElement("evtServTomadosCOOP", $evtServTomadosCOOP);
        $infevtServPrestadosCOOP = $dom->createElement("evtServPrestadosCOOP", $evtServPrestadosCOOP);
        $infevtAquisProdRural = $dom->createElement("evtAquisProdRural", $evtAquisProdRural);
        $infevtComercProducao = $dom->createElement("evtComercProducao", $evtComercProducao);
        $infevtRepasseClubeFutebol = $dom->createElement("evtRepasseClubeFutebol", $evtRepasseClubeFutebol);
//        $infevtInfoDesoneracao = $dom->createElement("evtInfoDesoneracao", $evtInfoDesoneracao);
//        $infevtReceitaAtivConcomitantes = $dom->createElement("evtReceitaAtivConcomitantes", $evtReceitaAtivConcomitantes);

        $infoFechamento->appendChild($infevtRemuneracao);
        $infoFechamento->appendChild($infevtServTomadosCMO);
        $infoFechamento->appendChild($infevtServPrestadosCMO);
        $infoFechamento->appendChild($infevtServTomadosCOOP);
        $infoFechamento->appendChild($infevtServPrestadosCOOP);
        $infoFechamento->appendChild($infevtAquisProdRural);
        $infoFechamento->appendChild($infevtComercProducao);
        $infoFechamento->appendChild($infevtRepasseClubeFutebol);
//        $infoFechamento->appendChild($infevtInfoDesoneracao);
//        $infoFechamento->appendChild($infevtReceitaAtivConcomitantes);
        $ideEmpregador->appendChild($inftpInscricaoEmpreg);
        $ideEmpregador->appendChild($infnrInscricaoEmpreg);
        $ideEvento->appendChild($infindRetificacao);
        if ($indRetificacao == 2) {
            $ideEvento->appendChild($infnrRecibo);
        }

        $ideEvento->appendChild($inftpAmb);
        $ideEvento->appendChild($infprocEmi);
        $ideEvento->appendChild($infindSeguimento);
        $ideEvento->appendChild($infverProc);
        $evt->appendChild($ideEvento);
        $evt->appendChild($ideEmpregador);
        $evt->appendChild($infoFechamento);
        $evt->setAttribute("Id", $id);
        $evt->setAttribute("versao", $versao);
        $eSocial->appendChild($evt);
        $dom->appendChild($eSocial);
        $this->sequencial++;

//        echo $dom->saveXML();
        $xml = $dom->saveXML();
        fwrite($arquivo, $xml);
    }

}
?>




