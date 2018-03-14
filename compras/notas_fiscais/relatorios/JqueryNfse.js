/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function(){

$(".ExcluirArquivo").click(function(){
    var resposta = confirm("Deseja remover esse registro?");
	if (resposta == true) { 
            var idProjeto= $(this).data('idprojeto');
            var idArquivo= $(this).data('idarquivo');
            var NomeArquivo= $(this).data('nomearquivo');

            $.post("deleteAnexoNota.php",{ativa:"Excluir",
            idProjeto:idProjeto, idArquivo:idArquivo, NomeArquivo:NomeArquivo},function(retorno){
              if(retorno== 1){
                  $('#ver'+idArquivo).remove();
                  $('#exclui'+idArquivo).remove();

               // location.reload();
              }

                })
        }
	
})



})
