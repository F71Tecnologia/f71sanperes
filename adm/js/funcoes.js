function selecionar_tudo(){
	for (i=0;i<document.curso.elements.length;i++)
		if(document.curso.elements[i].type == "checkbox")	
			document.curso.elements[i].checked=1
}
function deselecionar_tudo(){
	for (i=0;i<document.curso.elements.length;i++)
		if(document.curso.elements[i].type == "checkbox")	
			document.curso.elements[i].checked=0
}
function exibe() 
{
        if (document.getElementById("seleciona").style.display == "none")  { 
                  document.getElementById("seleciona").style.display = "block";
          } else {
                  document.getElementById("seleciona").style.display = "none";         
          }
		  if (document.getElementById("deseleciona").style.display == "none")  { 
                  document.getElementById("deseleciona").style.display = "block";
          } else {
                  document.getElementById("deseleciona").style.display = "none";         
          }
}
function mudar_cor(linha)
{
        var chk = linha.getElementsByTagName("input");

        chk[0].checked = !chk[0].checked;

        if(chk[0].checked)
        {
                linha.style.backgroundColor = "#DEE3ED";
        }
        else
        {
                linha.style.backgroundColor = "#FFFFFF";
        }
}

function mudar_cor_chk(obj,linha)
{
        var tab = document.getElementById("tab");
        
        obj.checked = !obj.checked;
        
        if(obj.checked)
        {
                tab.rows[linha].style.backgroundColor = "#DEE3ED";
        }
        else
        {
                tab.rows[linha].backgroundColor = "#FFFFFF";
        }
}

function mostraDiv(sID)
{
        var chks = document.getElementById("form1").getElementsByTagName("input");
        var sDiv = document.getElementById(sID);
        var exibir = false;
        
        for(i = 0; i < chks.length; i++)
        {
                if((chks[i].type == "checkbox") && (chks[i].checked))
                {
                        exibir = true;
                        break;
                }
        }
        
        if(exibir)
        {
                sDiv.style.display = "block";
        }
        else
        {
                sDiv.style.display = "none";
        }
}