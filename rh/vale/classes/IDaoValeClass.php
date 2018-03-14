<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IDaoValeClass
 *
 * @author F71 Note 2
 */
interface IDaoValeClass {
    
    
    /** seta o nome do vale * sodexo, aelo... */
    public function setIdTipo($id);
    
    /** retorna o nome do vale * sodexo, aelo... */
    public function getIdTipo();
    
    /** seta o nome do vale * sodexo, aelo... */
    public function setValeNome($vale_nome);
    
    /** retorna o nome do vale * sodexo, aelo... */
    public function getValeNome();
   
    /** itens do menu */
    public function getItensMenu();

    /** regiѕes */
    public function getRegioesFuncionario($usuario);
    
    public function getProjetos($id_regiao, $encode = FALSE);

    /** valores diсrios da regiуo */
    public function getValoresDiarios($regiao);
    
    public function salvaValorDiario($dados);
    
    public function atualizaValorDiario($dados);
    
    public function excluiValorDiario($dados);
    
    public function salvaCltValorDiario($dados);

    /** funcionсrios do projeto */
    public function getFuncionariosByProjeto(Array $dados);

    /** get pedidos  */
    public function getPedidos($status = 1);

    /** relacуo do pedido  */
    public function geraRelacaoCltPedido(Array $dados);
    
    public function verRelacaoCltPedido($id_pedido);

    public function exportaUsuario(Array $relacao);
//    
    public function exportaPedido(Array $arr);
    /** get relacуo do pedido  */
//    public function get_relacao_clt_pedido($id_projeto);

    /** criaчуo do arquivo  */
//    public function criar_csv_aelo($relacao_funcionarios);

}
