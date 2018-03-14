<?php

include_once("../../rh/fpdf/fpdf.php");
/*
 * PHP-DOC - ContraChequeClass.php 
 * 
 * Classe para criação e impressão do contra-cheque
 *
 * ??/??/????
 * 
 * @package ContraCheque 
 * @access public   
 * 
 * @version
 * 
 * Versão: 3.0.0000 - ??/??/???? - N. Def. - Versão Inicial
 * Versão: 3.0.6293 - 11/02/2016 - Jacques - Adição de número de dependentes na impressão da base de IRRF para referências. Solicitação de Sinésio.
 * 
 */

class ContraCheque extends FPDF {

    private $duplicado;
    private $tipo;
    private $pdf;
    private $dados;
    private $obs;
    private $faixas_irrf;


    /**
     * 
     * @param type $dados
     * @param type $tipo define o tipo de retorno (pdf, csv, txt)
     */
    public function __construct($duplicado = false, $tipo = array("pdf")) {

        $this->permissaoContraCheque();

        //VERIFICA A ORIENTAÇÃO DA PÁGINA
        $orientacao = ($duplicado) ? "L" : "P";
        $tamanho_folha = ($duplicado) ? "A4" : "A5";

        //OBJETO PDF
        $this->pdf = new FPDF($orientacao, "cm", $tamanho_folha);
        $this->setDuplicado($duplicado);
        $this->setTipo($tipo);
//        $this->setDados($dados);
//        echo "<pre>";
//            print_r($dados);
//        echo "</pre>";
    }

    public function getAnosFolha($regiao) {
        $qry = "SELECT MIN(A.ano) AS primeiro_ano, MAX(A.ano) AS ultimo_ano
            FROM rh_folha AS A
            WHERE A.regiao = '{$regiao}' AND A.status = 3";
        $sql = mysql_query($qry) or die(mysql_error());
        $res = mysql_fetch_assoc($sql);

        $this->ano_ini = $res['primeiro_ano'];
        $this->ano_fim = $res['ultimo_ano'];
    }

    public function getFolhaCC($projeto, $ano) {
        $qry = "SELECT *, DATE_FORMAT(A.data_inicio, '%d/%m/%Y') AS data_inicio, DATE_FORMAT(A.data_fim, '%d/%m/%Y') AS data_fim
            FROM rh_folha AS A
            WHERE A.projeto = '{$projeto}' AND A.status = '3' AND A.ano = '{$ano}'
            ORDER BY A.projeto, A.ano";
        $RE = mysql_query($qry) or die(mysql_error($qry));

        return $RE;
    }

    public function listaTodos($id_folha) {
        $RE = mysql_query("SELECT * FROM rh_folha where id_folha = '$id_folha' and status = '3' ");
        $Row = mysql_fetch_array($RE);

        $max = 50;
        $pedaco = ceil($Row['clts'] / $max);

        $a = 1;
        $maxini = $maxfim = 0;

        for ($i = 1; $i <= $pedaco; $i ++) {
            $maxfim = $maxfim + $max;
            if ($i != 1) {
                $maxini = $maxini + $max;
            }

            if ($pedaco == $i) {
                $maxfim = $Row['clts'];
            }

            $array[$i] = array(
                'maxini' => $maxini,
                'maxfim' => $maxfim
            );
        }
        return $array;
    }

    /**
     * MÉTODO PARA RETORNO DOS DADOS DO CLT 
     * @param type $clt
     */
    public function getContraCheque() {
        $this->getFaixas($this->dados['ano'], $this->dados['mes']);

        //VERIFICA CARGO ATUAL
        $cargo = $this->getCargoPeriodo($this->dados['id'], $this->dados['ano'], $this->dados['mes'], $this->dados['cargo']);

        $this->pdf->AddPage();
        $this->pdf->SetFont('Arial', '', 8);
        $this->pdf->SetTopMargin(1);

        //EMPRESA
        $add = 0;
        $contador = ($this->duplicado) ? 2 : 1;
        for ($i = 1; $i <= $contador; $i++) {

            $totalDesconto = 0;
            $totalLiquido = 0;

            if ($i == $this->duplicado) {
                $this->pdf->SetY(1.0);
            }

            //PREDEFINIÇÕES
            $altura_celula = 0.5;
            $largura_celula = 13;
            $distancia = 1.7;

            if ($this->duplicado) {
                if ($i != $contador) {
                    $add = 0;
                } else {
                    $add = $largura_celula + $distancia;
                }
            }

            //INFORMAÇÕES DA EMPRESA
            $x = ($i == 2) ? 15.7 : 1;


            // imagem
            $x = ($i == 2) ? 15.7 : 1;
            $this->pdf->SetXY($x, 1);
            $this->pdf->Cell(2.5, 2.4, '', 1, '0', 'C');
            $this->pdf->Image($this->dados['logo'], 1.1 + $add, 1.3, 2.3, 1.4, 'gif');

            // predefinicoes
            $this->pdf->SetFont('Arial', '', 7);
            $altura_cabecalho = 0.4;
            
            // linha 1
            $x = ($i == 2) ? 15.7 + 2.5 : 1 + 2.5;
            $this->pdf->SetXY($x, 1);
            $this->pdf->Cell(10.5, $altura_cabecalho, $this->dados['empresa'], 1);

            // linha 2
            $x = ($i == 2) ? 15.7 + 2.5 : 1 + 2.5;
            $this->pdf->SetXY($x, 1.4);
            $this->pdf->Cell(5.25, $altura_cabecalho, "CNPJ: " . $this->dados['cnpj'], 1);
            
            $x = ($i == 2) ? 15.7 + 7.75 : 1 + 7.75;
            $this->pdf->SetXY($x, 1.4);
            $this->pdf->Cell(5.25, $altura_cabecalho, "TEL: " . $this->dados['telefone'], 1);
            
            // linha 3
            $x = ($i == 2) ? 15.7 + 2.5 : 1 + 2.5;
            $this->pdf->SetXY($x, 1.8);
            $this->pdf->Cell(8.5, $altura_cabecalho, 'END: ' . $this->dados['endereco'], 1);
            
            $x = ($i == 2) ? 15.7 + 11 : 1 + 11;
            $this->pdf->SetXY($x, 1.8);
            $this->pdf->Cell(2, $altura_cabecalho, 'Nº: ' . $this->dados['numero'], 1);
            
            // linha 4
            $x = ($i == 2) ? 15.7 + 2.5 : 1 + 2.5;
            $this->pdf->SetXY($x, 2.2);
            $this->pdf->Cell(10.5, $altura_cabecalho, 'COMP: ' . $this->dados['complemento'], 1);
                        
            // linha 5
            $x = ($i == 2) ? 15.7 + 2.5 : 1 + 2.5;
            $this->pdf->SetXY($x, 2.6);
            $this->pdf->Cell(5.25, $altura_cabecalho, 'BAIRRO: ' . $this->dados['bairro'], 1);
            
            $x = ($i == 2) ? 15.7 + 7.75 : 1 + 7.75;
            $this->pdf->SetXY($x, 2.6);
            $this->pdf->Cell(5.25, $altura_cabecalho, 'MUN: ' . $this->dados['cidade'], 1);
            
            
            // linha 6
            $x = ($i == 2) ? 15.7 + 2.5 : 1 + 2.5;
            $this->pdf->SetXY($x, 3);
            $this->pdf->Cell(5.25, $altura_cabecalho, 'CEP: ' . $this->dados['cep'], 1);
            
            $x = ($i == 2) ? 15.7 + 7.75: 1 + 7.75;
            $this->pdf->SetXY($x, 3);
            $this->pdf->Cell(5.25, $altura_cabecalho, 'UF: ' . $this->dados['uf'], 1);
            
            //$this->pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');
            //$this->pdf->Ln();
            
            //PREDEFINIÇÕES
            $altura_atual = 2.7;
            $altura_celula_n = $altura_celula + 0.3;

            //INFORMAÇÕES DE DADOS DO FUNCIONARIO
            //LINHA 2
            $this->pdf->SetFont('Arial', '', 7);
            $this->pdf->Ln($altura_celula);

            if ($i == 2) {
                $this->pdf->SetX(15.7);
            } else {
                $this->pdf->SetX(1);
            }

            $this->pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');

            $this->pdf->Line(2.9 + $add, $altura_atual + 0.8, 2.9 + $add, $altura_atual + 1.6);
            $this->pdf->Line(11.7 + $add, $altura_atual + 0.8, 11.7 + $add, $altura_atual + 1.6);
            $this->pdf->Text(1.2 + $add, 1.1 + $altura_atual, 'Mês: ');
            $this->pdf->Text(1.2 + $add, 1.5 + $altura_atual, $this->getMes($this->dados['mes']) . '/' . $this->dados['ano']);
            $this->pdf->Text(3.1 + $add, 1.1 + $altura_atual, 'Nome:');
            $this->pdf->Text(3.1 + $add, 1.5 + $altura_atual, $this->dados['nome']);
            $this->pdf->Text(11.8 + $add, 1.1 + $altura_atual, 'Cod Funcionário: ');
            $this->pdf->Text(11.8 + $add, 1.5 + $altura_atual, $this->dados['cod_funcionario']);

            //LINHA 3
            $altura_atual += 0.8;
            $this->pdf->Ln($altura_celula + 0.3);

            if ($i == 2) {
                $this->pdf->SetX(15.7);
            } else {
                $this->pdf->SetX(1);
            }

            $this->pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');

            $this->pdf->Line(11.7 + $add, $altura_atual + 0.8, 11.7 + $add, $altura_atual + 1.6);
            $this->pdf->Text(1.2 + $add, 1.1 + $altura_atual, 'Cargo:');
            $this->pdf->Text(1.2 + $add, 1.5 + $altura_atual, $cargo);
            $this->pdf->Text(11.8 + $add, 1.1 + $altura_atual, 'Data de Admissão:');
            $this->pdf->Text(11.8 + $add, 1.5 + $altura_atual, $this->dados['data_admissao']);

            //LINHA 4
            $altura_atual += 0.8;
            $this->pdf->Ln($altura_celula + 0.3);

            if ($i == 2) {
                $this->pdf->SetX(15.7);
            } else {
                $this->pdf->SetX(1);
            }

            $this->pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L'); 

            $this->pdf->Line(11.7 + $add, 0.8 + $altura_atual, 11.7 + $add, $altura_atual + 1.6);
            $this->pdf->Text(1.2 + $add, 1.1 + $altura_atual, 'Unidade:'); 
            $this->pdf->Text(1.2 + $add, 1.5 + $altura_atual, $this->dados['unidade']);
            $this->pdf->Text(11.8 + $add, 1.1 + $altura_atual, 'PIS:');
            $this->pdf->Text(11.8 + $add, 1.5 + $altura_atual, $this->dados['pis']);

            //LINHA 5
            $altura_atual += 0.8;
            $this->pdf->Ln($altura_celula + 0.3);

            if ($i == 2) {
                $this->pdf->SetX(15.7);
            } else {
                $this->pdf->SetX(1);
            }

            $this->pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');

            $this->pdf->Line(3.7 + $add, 0.8 + $altura_atual, 3.7 + $add, $altura_atual + 1.6);
            $this->pdf->Line(7.3 + $add, 0.8 + $altura_atual, 7.3 + $add, $altura_atual + 1.6);
            $this->pdf->Line(11.7 + $add, 0.8 + $altura_atual, 11.7 + $add, $altura_atual + 1.6);
            $this->pdf->Text(1.2 + $add, 1.1 + $altura_atual, 'CPF:');
            $this->pdf->Text(1.2 + $add, 1.5 + $altura_atual, $this->dados['cpf']);
            $this->pdf->Text(4 + $add, 1.1 + $altura_atual, 'RG:');
            $this->pdf->Text(4 + $add, 1.5 + $altura_atual, $this->dados['rg']);
            $this->pdf->Text(7.5 + $add, 1.1 + $altura_atual, 'Carteira de Trabalho:');
            $this->pdf->Text(7.5 + $add, 1.5 + $altura_atual, $this->dados['carteira_trabalho']);
            $this->pdf->Text(11.8 + $add, 1.1 + $altura_atual, 'Série:');
            $this->pdf->Text(11.8 + $add, 1.5 + $altura_atual, $this->dados['serie_carteira_trabalho']);

            //LINHA 6
            $altura_atual += 0.8;
            $this->pdf->Ln($altura_celula + 0.3);

            if ($i == 2) {
                $this->pdf->SetX(15.7);
            } else {
                $this->pdf->SetX(1);
            }

            $this->pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');

            $this->pdf->Line(7.3 + $add, 0.8 + $altura_atual, 7.3 + $add, $altura_atual + 1.6);
            $this->pdf->Line(11.7 + $add, 0.8 + $altura_atual, 11.7 + $add, $altura_atual + 1.6);
            $this->pdf->Text(1.2 + $add, 1.1 + $altura_atual, 'Banco:');
            $this->pdf->Text(1.2 + $add, 1.5 + $altura_atual, $this->dados['banco']);
            $this->pdf->Text(7.5 + $add, 1.1 + $altura_atual, 'Agência:');
            $this->pdf->Text(7.5 + $add, 1.5 + $altura_atual, $this->dados['agencia']);
            $this->pdf->Text(11.8 + $add, 1.1 + $altura_atual, 'Conta corrente:');
            $this->pdf->Text(11.8 + $add, 1.5 + $altura_atual, $this->dados['conta_corrente']);

            //LINHA 7 
            $this->pdf->Ln($altura_celula + 0.3);
            $this->pdf->SetFont('Arial', 'B', 6);

            $this->pdf->Line(2.3 + $add, 1.6 + $altura_atual, 2.3 + $add, $altura_atual + 2);
            $this->pdf->Line(8.5 + $add, 1.6 + $altura_atual, 8.5 + $add, $altura_atual + 2);
            $this->pdf->Line(10 + $add, 1.6 + $altura_atual, 10 + $add, $altura_atual + 2);
            $this->pdf->Line(12 + $add, 1.6 + $altura_atual, 12 + $add, $altura_atual + 2);

            if ($i == 2) {
                $this->pdf->SetX(15.7);
            } else {
                $this->pdf->SetX(1);
            }

            $this->pdf->Cell($largura_celula, $altura_celula_n - 0.4, '', 1, '0', 'L');

            $this->pdf->Text(1.3 + $add, 1.9 + $altura_atual, 'Código');
            $this->pdf->Text(4.6 + $add, 1.9 + $altura_atual, 'Descrição');
            $this->pdf->Text(8.7 + $add, 1.9 + $altura_atual, 'Frequência');
            $this->pdf->Text(10.4 + $add, 1.9 + $altura_atual, 'Vencimento');
            $this->pdf->Text(12.4 + $add, 1.9 + $altura_atual, 'Descontos');

            //MOVIMENTOS
            $this->pdf->Ln(0.6);

//            echo "<pre>";
//                print_r($this->dados["movimentos"]);
//            echo "</pre>"; 

            foreach ($this->dados["movimentos"] as $tipo => $movimentos) {
                foreach ($movimentos as $mov => $dados) {
                    if ($dados[$this->dados["mes"]] != "0.00") {
                        $this->pdf->Ln(0.35);

                        if ($i != 2) {
                            $this->pdf->SetX(1.02);
                        } else {
                            $this->pdf->SetX(15.74);
                        }

                        $this->pdf->Cell(1.3, $altura_mov, $mov, $borda, '0', 'C');
                        $this->pdf->Cell(6.2, $altura_mov, $dados["nome"], $borda, '0', 'L');
                        $this->pdf->Cell(1.5, $altura_mov, $dados["ref"], $borda, '0', 'C');
                        if ($tipo == "credito") {
                            $this->pdf->Cell(2, $altura_mov, number_format($dados[$this->dados["mes"]], 2, ",", "."), $borda, '0', 'R');
                            $this->pdf->Cell(2, $altura_mov, "", $borda, '0', 'R');
                            $totalLiquido += $dados[$this->dados["mes"]];

                            /**
                             * FEITO POR SINESIO LUIZ 
                             * PARA SITUAÇÃO DE CONTRACHEQUE DE FERIAS 
                             * 5037 - FÉRIAS NO MÊS 
                             */
//                           if($mov == 5037){
//                               $totalDesconto += $dados[$this->dados["mes"]];
//                           }

                            /**
                             * FEITO POR SINESIO LUIZ 
                             * PARA SITUAÇÃO DE CONTRACHEQUE DE FERIAS 
                             * 5037 - FÉRIAS NO MÊS 
                             */
//                            if($mov == 5037){
//                                $this->pdf->Cell(1.3, $altura_mov, $mov, $borda, '0', 'C');
//                                $this->pdf->Cell(6.2, $altura_mov, $dados["nome"], $borda, '0', 'L');
//                                $this->pdf->Cell(1.5, $altura_mov, $dados["ref"], $borda, '0', 'C');
//                                $this->pdf->Cell(2, $altura_mov, "", $borda, '0', 'R');
//                                $this->pdf->Cell(2, $altura_mov, $dados[$this->dados["mes"]], $borda, '0', 'R');
//                                $this->pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');
//                            }
                        } else {

                            $this->pdf->Cell(2, $altura_mov, "", $borda, '0', 'R');
                            //$this->pdf->Cell(2, $altura_mov, $dados[$this->dados["mes"]], $borda, '0', 'R');
                            $this->pdf->Cell(2, $altura_mov, number_format($dados[$this->dados["mes"]], 2, ",", "."), $borda, '0', 'R');
                            $totalDesconto += $dados[$this->dados["mes"]];
                        }
                    }

                    $this->pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');
                }
            }


            //DESENHANDO FUNDO DA TABELA DE MOVIMENTOS
            $altura_fundo = 6.7;
            $altura_atual += 2;

            if ($i != $contador) {
                $this->pdf->SetY(0);
                $this->pdf->Rect(15.7, $altura_atual, 1.3, $altura_fundo);
                $this->pdf->Rect(17, $altura_atual, 6.2, $altura_fundo);
                $this->pdf->Rect(23.2, $altura_atual, 1.5, $altura_fundo);
                $this->pdf->Rect(24.70, $altura_atual, 2, $altura_fundo);
                $this->pdf->Rect(26.70, $altura_atual, 2, $altura_fundo);
            } else {
                $this->pdf->Rect(1, $altura_atual, 1.3, $altura_fundo);
                $this->pdf->Rect(2.29, $altura_atual, 6.2, $altura_fundo);
                $this->pdf->Rect(8.5, $altura_atual, 1.5, $altura_fundo);
                $this->pdf->Rect(10, $altura_atual, 2, $altura_fundo);
                $this->pdf->Rect(12, $altura_atual, 2, $altura_fundo);
            }


            //LIINHA TOTAIS
            $altura_ag = 13.8;

            $this->pdf->Rect(1, 14.6, 13, 0.8); // retangulo da esquerda 
            $this->pdf->Rect(15.7, 14.6, 13, 0.8); // retangulo da direita

            $this->pdf->Line(8.5 + $add, 0.8 + $altura_ag, 8.5 + $add, $altura_ag + 1.6);
            $this->pdf->Line(12 + $add, 0.8 + $altura_ag, 12 + $add, $altura_ag + 1.6);
            $this->pdf->Text(1.2 + $add, 1.1 + $altura_ag, 'Valor Bruto:');
            $this->pdf->Text(3.4 + $add, 1.5 + $altura_ag, "R$ " . number_format($totalLiquido, 2, ',', '.')); //$this->dados["valor_bruto"] + $this->dados["rend"]
            $this->pdf->Text(8.7 + $add, 1.1 + $altura_ag, 'Total dos Descontos:');
            $this->pdf->Text(9.5 + $add, 1.5 + $altura_ag, "R$ " . number_format($totalDesconto, 2, ',', '.'));
            $this->pdf->Text(12.1 + $add, 1.1 + $altura_ag, 'Valor Líquido:');
            $this->pdf->Text(12.2 + $add, 1.5 + $altura_ag, "R$ " . number_format($totalLiquido - $totalDesconto, 2, ",", "."));

            /*
             * VALIDAR LÍQUIDO 
             * PRA ACHAR DIFERENÇA NO COMPARATIVO CC x FOLHA
             */
            if ($_REQUEST['validate']) {
                $info_validate = array(
                    "totalLiquido" => $totalLiquido,
                    "totalDesconto" => $totalDesconto,
                    "dados" => $this->dados
                );

//                print_array($this->dados);

                if ($aux123 != $this->dados['id']) {
                    $this->getComparaLiquidoFP($info_validate);
                    $aux123 = $this->dados['id'];
                }
            }

            //BASES
            $this->pdf->Ln($altura_celula + 0.7);
            $altura_ag += 1.2;

            $this->pdf->Rect(1 + $add, 15.6, 2.16, 0.8);    // retangulo Salário Base 
            $this->pdf->Rect(3.16 + $add, 15.6, 2.16, 0.8); // retangulo Base INSS
            $this->pdf->Rect(5.32 + $add, 15.6, 2.16, 0.8); // retangulo Base FGTS
            $this->pdf->Rect(7.48 + $add, 15.6, 2.16, 0.8); // retangulo FGTS Mês
            $this->pdf->Rect(9.64 + $add, 15.6, 2.16, 0.8); // retangulo Base IRRF
            $this->pdf->Rect(11.80 + $add, 15.6, 2.2, 0.8); // retangulo Faixa IRRF
            
            $this->pdf->Text(1.2 + $add, 0.9 + $altura_ag, 'Salário Base:');
            $this->pdf->Text(1.2 + $add, 1.3 + $altura_ag, "R$ " . number_format($this->dados["valor_bruto"], 2, ',', '.'));
            $this->pdf->Text(3.36 + $add, 0.9 + $altura_ag, 'Base INSS:');
            $this->pdf->Text(3.36 + $add, 1.3 + $altura_ag, "R$ " . number_format($this->dados["base_inss"], 2, ',', '.'));
            $this->pdf->Text(5.52 + $add, 0.9 + $altura_ag, 'Base FGTS:');
            $this->pdf->Text(5.52 + $add, 1.3 + $altura_ag, "R$ " . number_format($this->dados["base_fgts"], 2, ',', '.'));
            $this->pdf->Text(7.68 + $add, 0.9 + $altura_ag, 'FGTS Mês:');
            $this->pdf->Text(7.68 + $add, 1.3 + $altura_ag, "R$ " . number_format($this->dados["fgts"], 2, ',', '.'));
            $this->pdf->Text(9.84 + $add, 0.9 + $altura_ag, 'Base IRRF:');
            $this->pdf->Text(9.84 + $add, 1.3 + $altura_ag, "R$ " . number_format($this->dados["base_irrf"], 2, ",", ".") . (!empty($this->dados["dependentes"]) ? " (" . $this->dados["dependentes"] . " Dep.)" : ""));
            $this->pdf->Text(12 + $add, 0.9 + $altura_ag, 'Faixa IRRF:');
            $this->pdf->Text(12 + $add, 1.3 + $altura_ag, $this->faixas_irrf[$this->dados['t_imprenda']], 2);


//            $this->pdf->Rect(1, 15.2, 13, 0.8); // retangulo da esquerda 
//            $this->pdf->Rect(15.7, 15.2, 13, 0.8); // retangulo da direita
            
//            $this->pdf->Line(4, 0.6 + $altura_ag, 4, $altura_ag + 1.4);
//            $this->pdf->Line(8.5 + $add, 0.6 + $altura_ag, 8.5 + $add, $altura_ag + 1.4);
//            $this->pdf->Line(11.3 + $add, 0.6 + $altura_ag, 11.3 + $add, $altura_ag + 1.4);
//            $this->pdf->Text(1.2 + $add, 0.9 + $altura_ag, 'Salário Base:');
//            $this->pdf->Text(1.5 + $add, 1.3 + $altura_ag, "R$ " . number_format($this->dados["valor_bruto"], 2, ',', '.'));
//            $this->pdf->Text(4.3 + $add, 0.9 + $altura_ag, 'Salário:');
//            $this->pdf->Text(4.6 + $add, 1.3 + $altura_ag, "R$ " . number_format($this->dados["salario_base"], 2, ',', '.'));
//            $this->pdf->Text(8.7 + $add, 0.9 + $altura_ag, 'FGTS:');
//            $this->pdf->Text(9.2 + $add, 1.3 + $altura_ag, "R$ " . number_format($this->dados["fgts"], 2, ',', '.'));
//            $this->pdf->Text(11.5 + $add, 0.9 + $altura_ag, 'Base Calculo IRRF:');
//            $this->pdf->Text(11.8 + $add, 1.3 + $altura_ag, "R$ " . number_format($this->dados["base_irrf"], 2, ",", ".") . (!empty($this->dados["dependentes"]) ? " (" . $this->dados["dependentes"] . " Dep.)" : ""));

            $this->pdf->Ln($altura_celula + 0.7);

            //DATA E ASSINATURA
            $this->pdf->Rect(1, 16.8, 13, 1.9);
            $this->pdf->Rect(15.7, 16.8, 13, 1.9);

            $this->pdf->SetFontSize('9');

            $y = 18.2;

            if ($i == $contador) {
                $this->pdf->Text(1.3, 17.2, 'Declaro ter recebido a importância líquida discriminada acima');
                $this->pdf->Text(1.3, $y, '____/____/_____');
                $this->pdf->Text(2, $y + 0.4, 'Data');

                $this->pdf->Text(6, $y, '__________________________________________');
                $this->pdf->Text(7.6, $y + 0.4, 'Assinatura do funcionário');
            } else {
                $this->pdf->Text(16.2, 17.2, 'Declaro ter recebido a importância líquida discriminada acima');
                $this->pdf->Text(16.3, $y, '____/____/_____');
                $this->pdf->Text(17, $y + 0.4, 'Data');

                $this->pdf->Text(21, $y, '__________________________________________');
                $this->pdf->Text(23, $y + 0.4, 'Assinatura do funcionário');
            }



            $this->pdf->SetFontSize(6);
            $obs = $this->getObs();

            $d = (count($obs) > 0) ? count($obs) - 1 : 0;
            $this->pdf->Rect(1, 19, 13, 0.5 + ($d * 0.5));
            $this->pdf->Rect(15.7, 19, 13, 0.5 + ($d * 0.5));

            $count = 0;
            foreach ($obs as $key => $value) {
                $this->pdf->Text(1.3, 19.3 + ($count * 0.5), $value);
                $this->pdf->Text(16.3, 19.3 + ($count * 0.5), $value);
                $count++;
            }


            //$obs = $this->getObsDeFaltaNoContraCheque($this->dados['id']);
//            $this->pdf->SetFontSize('6');
//            $this->pdf->Rect(1, 19, 13, 0.8);
//            $this->pdf->Rect(15.7, 19, 13, 0.8);
//            $this->pdf->Text(1.3, 19.5, $saudacao  . $obs);
//            $this->pdf->Text(16.3, 19.5, $saudacao . $obs);
        }

//      $this->pdf->Output('as.pdf', 'I');
    }

    public function getComparaLiquidoFP($info_validate) {
        $totalLiquido = $info_validate['totalLiquido'];
        $totalDesconto = $info_validate['totalDesconto'];
        $id = $info_validate['dados']['id'];
        $nome = $info_validate['dados']['nome'];
        $status_desc = $info_validate['dados']['status_especifica'];
        $status = $info_validate['dados']['status'];

        $salLiquidoFP_F = formataMoeda($info_validate['dados']['salario_liq']);
        $tt_F = formataMoeda((($totalLiquido - $totalDesconto) > 0) ? $totalLiquido - $totalDesconto : 0, 2);
        $salLiquidoFP = number_format($info_validate['dados']['salario_liq'], 2, ".", "");
        $tt_ = number_format((($totalLiquido - $totalDesconto) > 0) ? $totalLiquido - $totalDesconto : 0, 2, ".", "");

        if ($tt_ > $salLiquidoFP) {
            $dif_liquido = $tt_ - $salLiquidoFP;
            $dif_liquidoF = formataMoeda($tt_ - $salLiquidoFP);
        } elseif ($tt_ < $salLiquidoFP) {
            $dif_liquido = $salLiquidoFP - $tt_;
            $dif_liquidoF = formataMoeda($salLiquidoFP - $tt_);
        }

        if (($status < 60) || ($status > 69)) {
            if ($tt_ != $salLiquidoFP) {
                if ($dif_liquido >= 0.02) {
                    echo "<tr><td>{$id}</td><td>{$nome}</td><td>{$status_desc}</td><td>{$tt_F}</td><td>{$salLiquidoFP_F}</td><td>{$dif_liquidoF}</td></tr>";
                }
            }
        }
    }

    /**
     * MÉTODO PARA RETORNO DOS DADOS DO CLT 
     * @param type $clt
     */
    public function getContraChequeNovo() {

        $this->pdf->AddPage();
        $this->pdf->SetFont('Arial', '', 8);
        $this->pdf->SetTopMargin(1);

        //EMPRESA
        $add = 0;
        $contador = ($this->duplicado) ? 2 : 1;
        for ($i = 1; $i <= $contador; $i++) {

            $totalDesconto = 0;
            $totalLiquido = 0;

            if ($i == $this->duplicado) {
                $this->pdf->SetY(1.0);
            }

            //PREDEFINIÇÕES
            $altura_celula = 0.5;
            $largura_celula = 13;
            $distancia = 1.7;

            if ($this->duplicado) {
                if ($i != $contador) {
                    $add = 0;
                } else {
                    $add = $largura_celula + $distancia;
                }
            }

            //INFORMAÇÕES DA EMPRESA
            if ($i == 2) {
                $this->pdf->SetX(15.7);
            } else {
                $this->pdf->SetX(1);
            }

            $this->pdf->Cell($largura_celula, 2.1, null, 1, '0', 'C');
            $this->pdf->Text(3.5 + $add, 1.6, $this->dados['empresa']);
            $this->pdf->Text(3.5 + $add, 2, "CNPJ: " . $this->dados['cnpj']);
            $this->pdf->SetFont('Arial', '', 7.7);
            $this->pdf->Text(3.5 + $add, 2.4, $this->dados['endereco']);
            $this->pdf->Text(3.5 + $add, 2.8, "CEP: " . $this->dados['cep']);
            $this->pdf->Text(5.8 + $add, 2.8, "TEL: " . $this->dados['telefone']);
            $this->pdf->Image($this->dados['logo'], 1.1 + $add, 1.3, 2.3, 1.4, 'gif');

            $this->pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');
            $this->pdf->Ln(2.1);

            //PREDEFINIÇÕES
            $altura_atual = 2.8;
            $altura_celula_n = $altura_celula + 0.3;

            //INFORMAÇÕES DE DADOS DO FUNCIONARIO
            //LINHA 2
            $this->pdf->SetFont('Arial', '', 7);
            $this->pdf->Ln($altura_celula);

            if ($i == 2) {
                $this->pdf->SetX(15.7);
            } else {
                $this->pdf->SetX(1);
            }

            $this->pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');

            $this->pdf->Line(2.9 + $add, $altura_atual + 0.8, 2.9 + $add, $altura_atual + 1.6);
            $this->pdf->Line(11.7 + $add, $altura_atual + 0.8, 11.7 + $add, $altura_atual + 1.6);
            $this->pdf->Text(1.2 + $add, 1.1 + $altura_atual, 'Mês: ');
            $this->pdf->Text(1.2 + $add, 1.5 + $altura_atual, $this->getMes($this->dados['mes']) . '/' . $this->dados['ano']);
            $this->pdf->Text(3.1 + $add, 1.1 + $altura_atual, 'Nome:');
            $this->pdf->Text(3.1 + $add, 1.5 + $altura_atual, $this->dados['nome']);
            $this->pdf->Text(11.8 + $add, 1.1 + $altura_atual, 'Cod Funcionário: ');
            $this->pdf->Text(11.8 + $add, 1.5 + $altura_atual, $this->dados['cod_funcionario']);

            //LINHA 3
            $altura_atual += 0.8;
            $this->pdf->Ln($altura_celula + 0.3);

            if ($i == 2) {
                $this->pdf->SetX(15.7);
            } else {
                $this->pdf->SetX(1);
            }

            $this->pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');

            $this->pdf->Line(11.7 + $add, $altura_atual + 0.8, 11.7 + $add, $altura_atual + 1.6);
            $this->pdf->Text(1.2 + $add, 1.1 + $altura_atual, 'Cargo:');
            $this->pdf->Text(1.2 + $add, 1.5 + $altura_atual, $this->dados['cargo']);
            $this->pdf->Text(11.8 + $add, 1.1 + $altura_atual, 'Data de Admissão:');
            $this->pdf->Text(11.8 + $add, 1.5 + $altura_atual, $this->dados['dataEntrada']);

            //LINHA 4
            $altura_atual += 0.8;
            $this->pdf->Ln($altura_celula + 0.3);

            if ($i == 2) {
                $this->pdf->SetX(15.7);
            } else {
                $this->pdf->SetX(1);
            }

            $this->pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');

            $this->pdf->Line(11.7 + $add, 0.8 + $altura_atual, 11.7 + $add, $altura_atual + 1.6);
            $this->pdf->Text(1.2 + $add, 1.1 + $altura_atual, 'Unidade:');
            $this->pdf->Text(1.2 + $add, 1.5 + $altura_atual, $this->dados['unidade']);
            $this->pdf->Text(11.8 + $add, 1.1 + $altura_atual, 'PIS:');
            $this->pdf->Text(11.8 + $add, 1.5 + $altura_atual, $this->dados['pis']);

            //LINHA 5
            $altura_atual += 0.8;
            $this->pdf->Ln($altura_celula + 0.3);

            if ($i == 2) {
                $this->pdf->SetX(15.7);
            } else {
                $this->pdf->SetX(1);
            }

            $this->pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');

            $this->pdf->Line(3.7 + $add, 0.8 + $altura_atual, 3.7 + $add, $altura_atual + 1.6);
            $this->pdf->Line(7.3 + $add, 0.8 + $altura_atual, 7.3 + $add, $altura_atual + 1.6);
            $this->pdf->Line(11.7 + $add, 0.8 + $altura_atual, 11.7 + $add, $altura_atual + 1.6);
            $this->pdf->Text(1.2 + $add, 1.1 + $altura_atual, 'CPF:');
            $this->pdf->Text(1.2 + $add, 1.5 + $altura_atual, $this->dados['cpf']);
            $this->pdf->Text(4 + $add, 1.1 + $altura_atual, 'RG:');
            $this->pdf->Text(4 + $add, 1.5 + $altura_atual, $this->dados['rg']);
            $this->pdf->Text(7.5 + $add, 1.1 + $altura_atual, 'Carteira de Trabalho:');
            $this->pdf->Text(7.5 + $add, 1.5 + $altura_atual, $this->dados['carteira_trabalho']);
            $this->pdf->Text(11.8 + $add, 1.1 + $altura_atual, 'Série:');
            $this->pdf->Text(11.8 + $add, 1.5 + $altura_atual, $this->dados['serie_carteira_trabalho']);

            //LINHA 6
            $altura_atual += 0.8;
            $this->pdf->Ln($altura_celula + 0.3);

            if ($i == 2) {
                $this->pdf->SetX(15.7);
            } else {
                $this->pdf->SetX(1);
            }

            $this->pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');

            $this->pdf->Line(4.7 + $add, 0.8 + $altura_atual, 4.7 + $add, $altura_atual + 1.6);
            $this->pdf->Line(11.7 + $add, 0.8 + $altura_atual, 11.7 + $add, $altura_atual + 1.6);
            $this->pdf->Text(1.2 + $add, 1.1 + $altura_atual, 'Banco:');
            $this->pdf->Text(1.2 + $add, 1.5 + $altura_atual, $this->dados['razaoBanco']);
            $this->pdf->Text(5 + $add, 1.1 + $altura_atual, 'Agência:');
            $this->pdf->Text(5 + $add, 1.5 + $altura_atual, $this->dados['agencia']);
            $this->pdf->Text(11.8 + $add, 1.1 + $altura_atual, 'Conta corrente:');
            $this->pdf->Text(11.8 + $add, 1.5 + $altura_atual, $this->dados['conta_corrente']);

            //LINHA 7 
            $this->pdf->Ln($altura_celula + 0.3);
            $this->pdf->SetFont('Arial', 'B', 6);

            $this->pdf->Line(2.3 + $add, 1.6 + $altura_atual, 2.3 + $add, $altura_atual + 2);
            $this->pdf->Line(8.5 + $add, 1.6 + $altura_atual, 8.5 + $add, $altura_atual + 2);
            $this->pdf->Line(10 + $add, 1.6 + $altura_atual, 10 + $add, $altura_atual + 2);
            $this->pdf->Line(12 + $add, 1.6 + $altura_atual, 12 + $add, $altura_atual + 2);

            if ($i == 2) {
                $this->pdf->SetX(15.7);
            } else {
                $this->pdf->SetX(1);
            }

            $this->pdf->Cell($largura_celula, $altura_celula_n - 0.4, '', 1, '0', 'L');

            $this->pdf->Text(1.3 + $add, 1.9 + $altura_atual, 'Código');
            $this->pdf->Text(4.6 + $add, 1.9 + $altura_atual, 'Descrição');
            $this->pdf->Text(8.7 + $add, 1.9 + $altura_atual, 'Frequência');
            $this->pdf->Text(10.4 + $add, 1.9 + $altura_atual, 'Vencimento');
            $this->pdf->Text(12.4 + $add, 1.9 + $altura_atual, 'Descontos');

            //MOVIMENTOS
            $this->pdf->Ln(0.6);

            foreach ($this->dados["movimentos"] as $tipo => $movimentos) {
                foreach ($movimentos as $mov => $dados) {
                    if ($dados['valor_movimento'] != "0.00") {
                        $this->pdf->Ln(0.35);

                        if ($i != 2) {
                            $this->pdf->SetX(1.02);
                        } else {
                            $this->pdf->SetX(15.74);
                        }

                        $this->pdf->Cell(1.3, $altura_mov, $mov, $borda, '0', 'C');
                        $this->pdf->Cell(6.2, $altura_mov, $dados["descicao"], $borda, '0', 'L');
                        $this->pdf->Cell(1.5, $altura_mov, $dados["ref"], $borda, '0', 'C');
                        if ($tipo == "credito") {
                            $this->pdf->Cell(2, $altura_mov, number_format($dados['valor_movimento'], 2, ",", "."), $borda, '0', 'R');
                            $this->pdf->Cell(2, $altura_mov, "", $borda, '0', 'R');
                            $totalLiquido += $dados['valor_movimento'];
                        } else {
                            $this->pdf->Cell(2, $altura_mov, "", $borda, '0', 'R');
                            $this->pdf->Cell(2, $altura_mov, number_format($dados['valor_movimento'], 2, ",", "."), $borda, '0', 'R');
                            $totalDesconto += $dados['valor_movimento'];
                        }
                    }

                    $this->pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');
                }
            }

            //DESENHANDO FUNDO DA TABELA DE MOVIMENTOS
            $altura_fundo = 6.7;
            $altura_atual += 2;

            if ($i != $contador) {
                $this->pdf->SetY(0);
                $this->pdf->Rect(15.7, $altura_atual, 1.3, $altura_fundo);
                $this->pdf->Rect(17, $altura_atual, 6.2, $altura_fundo);
                $this->pdf->Rect(23.2, $altura_atual, 1.5, $altura_fundo);
                $this->pdf->Rect(24.70, $altura_atual, 2, $altura_fundo);
                $this->pdf->Rect(26.70, $altura_atual, 2, $altura_fundo);
            } else {
                $this->pdf->Rect(1, $altura_atual, 1.3, $altura_fundo);
                $this->pdf->Rect(2.29, $altura_atual, 6.2, $altura_fundo);
                $this->pdf->Rect(8.5, $altura_atual, 1.5, $altura_fundo);
                $this->pdf->Rect(10, $altura_atual, 2, $altura_fundo);
                $this->pdf->Rect(12, $altura_atual, 2, $altura_fundo);
            }

            //LIINHA TOTAIS
            $altura_ag = 13.9;

            $this->pdf->Rect(1, 14.7, 13, 0.8);
            $this->pdf->Rect(15.7, 14.7, 13, 0.8);

            $this->pdf->Line(8.5 + $add, 0.8 + $altura_ag, 8.5 + $add, $altura_ag + 1.6);
            $this->pdf->Line(12 + $add, 0.8 + $altura_ag, 12 + $add, $altura_ag + 1.6);
            $this->pdf->Text(1.2 + $add, 1.1 + $altura_ag, 'Valor Bruto:');
            $this->pdf->Text(3.4 + $add, 1.5 + $altura_ag, "R$ " . number_format($totalLiquido, 2, ',', '.'));
            $this->pdf->Text(8.7 + $add, 1.1 + $altura_ag, 'Total dos Descontos:');
            $this->pdf->Text(9.5 + $add, 1.5 + $altura_ag, "R$ " . number_format($totalDesconto, 2, ',', '.'));
            $this->pdf->Text(12.1 + $add, 1.1 + $altura_ag, 'Valor Líquido:');
            $this->pdf->Text(12.2 + $add, 1.5 + $altura_ag, "R$ " . number_format($totalLiquido - $totalDesconto, 2, ",", "."));

            if ($_COOKIE['debug'] == 'liquido_contra') {
                if ($auxDebug != $this->dados["id"]) {
                    if (round($totalLiquido - $totalDesconto, 2) != round($this->dados["valor_liquido"], 2)) {
                        echo "{$this->dados["id"]} - {$this->dados["nome"]} '" . ($totalLiquido - $totalDesconto) . "' == '{$this->dados["valor_liquido"]}'<br>";
                    }
                    $auxDebug = $this->dados["id"];
                }
            }
            //BASES
            $this->pdf->Ln($altura_celula + 0.7);
            $altura_ag += 1.2;

            $this->pdf->Rect(1, 15.7, 13, 0.8);
            $this->pdf->Rect(15.7, 15.7, 13, 0.8);

            $this->pdf->Line(4 + $add, 0.6 + $altura_ag, 4 + $add, $altura_ag + 1.4);
            $this->pdf->Line(8.5 + $add, 0.6 + $altura_ag, 8.5 + $add, $altura_ag + 1.4);
            $this->pdf->Line(11.3 + $add, 0.6 + $altura_ag, 11.3 + $add, $altura_ag + 1.4);
            $this->pdf->Text(1.2 + $add, 0.9 + $altura_ag, 'Salário Base:');
            $this->pdf->Text(1.5 + $add, 1.3 + $altura_ag, "R$ " . number_format($this->dados["salario_base"], 2, ',', '.'));
            $this->pdf->Text(4.3 + $add, 0.9 + $altura_ag, 'Salário:');
            $this->pdf->Text(4.6 + $add, 1.3 + $altura_ag, "R$ " . number_format($this->dados["salario"], 2, ',', '.'));
            $this->pdf->Text(8.7 + $add, 0.9 + $altura_ag, 'FGTS:');
            $this->pdf->Text(9.2 + $add, 1.3 + $altura_ag, "R$ " . number_format($this->dados["fgts"], 2, ',', '.'));
            $this->pdf->Text(11.5 + $add, 0.9 + $altura_ag, 'Base Calculo IRRF:');
            $this->pdf->Text(11.8 + $add, 1.3 + $altura_ag, "R$ " . number_format($this->dados["base_irrf"], 2, ",", ".") . (!empty($this->dados["dependentes"]) ? " (" . $this->dados["dependentes"] . " Dep.)" : ""));

            $this->pdf->Ln($altura_celula + 0.7);

            //DATA E ASSINATURA
            $this->pdf->Rect(1, 16.8, 13, 1.9);
            $this->pdf->Rect(15.7, 16.8, 13, 1.9);

            $this->pdf->SetFontSize('9');

            $y = 18.2;

            if ($i == $contador) {
                $this->pdf->Text(1.3, 17.2, 'Declaro ter recebido a importância líquida discriminada acima');
                $this->pdf->Text(1.3, $y, '____/____/_____');
                $this->pdf->Text(2, $y + 0.4, 'Data');

                $this->pdf->Text(6, $y, '__________________________________________');
                $this->pdf->Text(7.6, $y + 0.4, 'Assinatura do funcionário');
            } else {
                $this->pdf->Text(16.2, 17.2, 'Declaro ter recebido a importância líquida discriminada acima');
                $this->pdf->Text(16.3, $y, '____/____/_____');
                $this->pdf->Text(17, $y + 0.4, 'Data');

                $this->pdf->Text(21, $y, '__________________________________________');
                $this->pdf->Text(23, $y + 0.4, 'Assinatura do funcionário');
            }

            $this->pdf->SetFontSize(7);
            $obs = $this->getObs();

            $d = (count($obs) > 0) ? count($obs) - 1 : 0;
            $this->pdf->Rect(1, 19, 13, 0.5 + ($d * 0.5));
            $this->pdf->Rect(15.7, 19, 13, 0.5 + ($d * 0.5));

            $count = 0;
            foreach ($obs as $key => $value) {
                $this->pdf->Text(1.3, 19.3 + ($count * 0.5), $value);
                $this->pdf->Text(16.3, 19.3 + ($count * 0.5), $value);
                $count++;
            }
        }
    }

    /**
     * Fecha o PDF
     * @param type $mes
     * @return string
     */
    public function closePdf() {
        $this->pdf->Output('as.pdf', 'I');
    }

    /**
     * 
     * @param type $mes
     * @return string
     */
    public function getMes($mes) {
        $mes_ar = array("01" => "jan", "02" => "fev", "03" => "mar", "04" => "abr", "05" => "mai", "06" => "jun", "07" => "jul", "08" => "ago", "09" => "set", "10" => "out", "11" => "nov", "12" => "dez");
        return $mes_ar[$mes];
    }

    public function getInfoEventos() {
        $retorno = "";
        $queyVerificaEvento = " 
        SELECT A.id_evento, A.id_clt, A.id_projeto, A.`data`, A.data_retorno, nome_status, DATE_FORMAT(A.data, '%Y%m%d') indice
        FROM rh_eventos AS A 
        WHERE A.id_clt = '{$this->dados['id']}' AND A.`status` = 1 
        AND ((CONCAT('{$this->dados['ano']}-{$this->dados['mes']}-01') BETWEEN A.data AND A.data_retorno) OR CONCAT('{$this->dados['ano']}-{$this->dados['mes']}-01') >= A.data AND A.data_retorno = '0000-00-00')
        AND cod_status NOT IN (10, 67, 68)
        ORDER BY id_evento DESC
        LIMIT 1";
        $sqlVerificaEvento = mysql_query($queyVerificaEvento) or die("Erro ao selecionar da Evento");
        while ($rows = mysql_fetch_assoc($sqlVerificaEvento)) {
            $retorno = "{$rows['nome_status']} de " . implode('/', array_reverse(explode('-', $rows['data']))) . " até ";
            $retorno .= ($rows['data_retorno'] == '0000-00-00') ? "data indeterminada" : implode('/', array_reverse(explode('-', $rows['data_retorno'])));
            $this->obs[$rows['indice']] = $retorno;
//            print_array($this->obs);
        }

//        return $retorno;
    }

    public function getInfoFerias() {
        $retorno = "";
        $queyVerificaFerias = " 
        SELECT A.id_ferias, A.id_clt, A.projeto, A.data_fim, A.data_ini, nome, DATE_FORMAT(A.data_ini, '%Y%m%d') indice
        FROM rh_ferias AS A 
        WHERE A.id_clt = '{$this->dados['id']}' AND A.`status` = 1 
        AND (CONCAT('{$this->dados['ano']}-{$this->dados['mes']}-01') BETWEEN A.data_ini AND A.data_fim OR LAST_DAY(CONCAT('{$this->dados['ano']}-{$this->dados['mes']}-01')) BETWEEN A.data_ini AND A.data_fim)
        ORDER BY id_ferias DESC
        LIMIT 1";
        $sqlVerificaFerias = mysql_query($queyVerificaFerias) or die("Erro ao selecionar da ferias");
        while ($rows = mysql_fetch_assoc($sqlVerificaFerias)) {
            $this->obs[$rows['indice']] = "FICOU DE FÉRIAS DE " . implode('/', array_reverse(explode('-', $rows['data_ini']))) . " A " . implode('/', array_reverse(explode('-', $rows['data_fim'])));
        }
//        if($_COOKIE['debug'] == 666){
//            echo '/////////////////////////////////////////////$queyVerificaFerias///////////////////////////////////////';
//            print_array($queyVerificaFerias);
//            
//        }
//        return $retorno;
    }

    public function getObs() {
        $this->obs = [];
        $this->obs = ['A partir de 01/08/2017 seu contracheque e outros serviços estarão disponíveis através do site do ', 'Instituto dos Lagos Rio - www.institutolagosrio.com.br, no ícone Extranet']; 
        $this->getInfoFerias();
        $this->getInfoEventos();
        ksort($this->obs);
//        $retorno = implode('<br>', $this->obs);

        if ($_COOKIE['debug'] == 666) {
            echo '/////////////////////////////////////////////getObs///////////////////////////////////////';
            print_array($this->obs);
        }
        return $this->obs;
    }

    public function getObsDeFaltaNoContraCheque($clt) {
        $retorno = "";
        $query = "SELECT A.obs
                    FROM rh_movimentos_clt AS A
                    WHERE A.id_clt = '{$clt}' AND A.cod_movimento = 50249
                    AND A.id_mov = 232 AND A.`status` = 5";
        $sql = mysql_query($query);
        if ($sql) {
            while ($rows = mysql_fetch_assoc($sql)) {
                $retorno = "Faltas no(s) dia(s): " . $rows['obs'];
            }
        }

        return $retorno;
    }

    public function getCargoPeriodo($clt, $anoFolha, $meFolha, $cargoAtual) {
        $query = "SELECT A.id_curso_para, A.id_curso_de, B.nome
            FROM rh_transferencias AS A
            LEFT JOIN curso AS B ON(A.id_curso_de = B.id_curso)
            WHERE id_clt = '{$clt}' 
            AND LAST_DAY(data_proc) >= LAST_DAY('$anoFolha-$meFolha-01')
            ORDER BY data_proc ASC LIMIT 1";

        $rows = mysql_fetch_assoc(mysql_query($query));

        if (!empty($rows['id_curso_de'])) {
            $cargo = $rows['nome'];
        } else {
            $cargo = $cargoAtual;
        }

        return $cargo;
    }

    /**
     * 
     * @return type
     */
    public function getDuplicado() {
        return $this->duplicado;
    }

    /**
     * 
     */
    public function getTipo() {
        return $this->tipo;
    }

    /**
     * 
     * @param type $duplicado
     */
    public function setDuplicado($duplicado) {
        $this->duplicado = $duplicado;
    }

    /**
     * 
     * @param type $tipo
     */
    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    /**
     * 
     * @return type
     */
    public function getPdf() {
        return $this->pdf;
    }

    /**
     * 
     * @return type
     */
    public function getDados() {
        return $this->dados;
    }

    /**
     * 
     * @param type $pdf
     */
    public function setPdf($pdf) {
        $this->pdf = $pdf;
    }

    /**
     * 
     * @param type $dados
     */
    public function setDados($dados) {
        $this->dados = $dados;
    }

    public function listaParticipantesContra($id_folha) {
        $query = "SELECT a.cod,a.id_folha,a.id_folha_proc,b.id_clt,b.nome,a.salliquido,a.ano,a.mes,a.id_regiao,a.status_clt,
                    (SELECT nome FROM curso WHERE id_curso = b.id_curso) AS nome_curso,
                    IF((DATE_FORMAT(b.data_demi,'%Y-%m') <= CONCAT(a.ano,'-',a.mes)) AND (b.data_demi != '0000-00-00'), '1','0') AS demitido
                FROM rh_folha_proc AS a
                INNER JOIN rh_clt AS b ON (a.id_clt = b.id_clt)
                WHERE a.id_folha = '$id_folha' and a.status = '3' ORDER BY b.nome;";
        echo "<!-- $query -->";
        $resp = mysql_query($query) or die("Erro na query: " . $query . "\n" . mysql_error());

        while ($Row = mysql_fetch_assoc($resp)) {

            $lista[$Row['id_clt']] = $Row;
//        $ClassCLT->MostraClt($Row['id_clt']);
//        //PEGA A CURSO DO PERIODO
            $sql_transf = $this->checkCurso($Row['id_clt'], $Row['ano'], $Row['mes']);
            if (!empty($sql_transf['id_curso_de'])) {
                $lista[$Row['id_clt']]['id_curso'] = $sql_transf['id_curso_de'];
                $lista[$Row['id_clt']]['nome_curso'] = $sql_transf['curso_de'];
            }
        }
        return $lista;
    }

    protected function checkCurso($id_clt, $ano, $mes) {
        $sql_transf = "SELECT id_curso_para, id_curso_de,
                        (SELECT nome FROM curso WHERE id_curso = id_curso_de) AS curso_de,
                        (SELECT nome FROM curso WHERE id_curso = id_curso_para) AS curso_para
                        FROM rh_transferencias 
                        WHERE id_clt = $id_clt
                        AND LAST_DAY(data_proc) >= LAST_DAY('$ano-$mes-01')
                        ORDER BY data_proc ASC LIMIT 1";

        $transf = mysql_fetch_assoc(mysql_query($sql_transf));
        return $transf;
    }

    private function permissaoContraCheque() {
        $verifica_acoes = mysql_num_rows(mysql_query("SELECT * FROM botoes_assoc WHERE id_funcionario = '$_COOKIE[logado]' AND botoes_id  = 55"));
        return (!$verifica_acoes) ? die('Você não tem permissão para acessar esta página!') : true;
    }
    
    private function getFaixas($ano,$mes){
        $query = "SELECT faixa,percentual FROM rh_movimentos WHERE cod = 5021 AND ('$ano-$mes' BETWEEN DATE_FORMAT(data_ini,'%Y-%m') AND DATE_FORMAT(data_fim,'%Y-%m'));";
        $result = mysql_query($query);
        while ($row = mysql_fetch_array($result)) {
            $this->faixas_irrf[$row['percentual']] = $row['faixa'];
        }
    }

}

?>