<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title> 
<script type="text/javascript" src="jquery/jquery-1.4.2.min.js" ></script>
<script type="text/javascript" >
$(function(){
	
	
	var campo_categoria = $('.check_categoria');
	var campo_semana	= $('.check_semana');
	var campo_tipo 		= $('.check_tipo');
	
	campo_categoria.change(function(){
		
		if($(this).attr('checked')){
			$(this).parent().parent().find('.check_semana').attr('checked',true).change();
		}else{
			$(this).parent().parent().find('.check_semana').attr('checked',false).change();
			
		}
		
	});
	
	campo_semana.change(function(){
		
		var total_marcado = $(this).parent().parent().parent().find('.check_semana:checked').length;
		
		if(total_marcado == 0){
				
			$(this).parent().parent().parent().parent().find('.check_categoria').attr('checked',false);
			
		}else{

			$(this).parent().parent().parent().parent().find('.check_categoria').attr('checked',true);

		}
		
		//alert(total_marcado);
		
		if($(this).attr('checked')){
			
			$(this).parent().parent().find('.check_tipo').attr('checked',true).change();
			
		}else{
			
			$(this).parent().parent().find('.check_tipo').attr('checked',false).change();
			
		}
		
	});
	
	campo_tipo.change(function(){
			var total_marcado = $(this).parent().parent().parent().find('.check_tipo:checked').length;
			
			if(total_marcado == 0){
				
				$(this).parent().parent().parent().parent().find('.check_semana').attr('checked',false);
				
			}else{

				$(this).parent().parent().parent().parent().find('.check_semana').attr('checked',true);

			}
			
			
	});
	
	
	
	
	
});
</script>
<style type="text/css">
.menu { overflow:hidden; }
.menu ul { overflow: visible; padding:0px; margin: 0px; width:100px; }
.menu ul li { padding: 10px; float:left; clear:both; background-color:#CCC; width:100%; }
.menu ul li ul {  }
.menu ul li ul li {  }
</style>
</head>



<body>
<div class="menu">
	<ul>
    	<li><a href="#">ITEM 1<input type="checkbox"  name="check_categoria" class="check_categoria"/></a>
        	<ul>
            	<li><a href="#">SUBitem 1<input type="checkbox"  name="check_categoria" class="check_semana"/></a></li>
                <li><a href="#">SUBitem 1<input type="checkbox"  name="check_categoria" class="check_semana"/></a></li>
                <li><a href="#">SUBitem 1<input type="checkbox"  name="check_categoria" class="check_semana"/></a></li>
                <li><a href="#">SUBitem 1<input type="checkbox"  name="check_categoria" class="check_semana"/></a></li>
                <li><a href="#">SUBitem 1<input type="checkbox"  name="check_categoria" class="check_semana"/></a>
                	<ul>
                    	<li><a href="#">Subitem 2<input type="checkbox"  name="check_categoria" class="check_tipo"/></a></li>
                        <li><a href="#">Subitem 2<input type="checkbox"  name="check_categoria" class="check_tipo"/></a></li>
                        <li><a href="#">Subitem 2<input type="checkbox"  name="check_categoria" class="check_tipo"/></a></li>
                        <li><a href="#">Subitem 2<input type="checkbox"  name="check_categoria" class="check_tipo"/></a></li>
                        <li><a href="#">Subitem 2<input type="checkbox"  name="check_categoria" class="check_tipo"/></a></li>
                    </ul>
                </li>
            </ul>
        </li>
        <li><a href="#">Item 2</a></li>
        <li><a href="#">Item 2</a></li>
        <li><a href="#">Item 2</a></li>
        <li><a href="#">Item 2</a></li>
        <li><a href="#">Item 2</a></li>
        <li><a href="#">Item 2</a></li>
        <li><a href="#">Item 2</a></li>
        <li><a href="#">Item 2</a></li>
        <li><a href="#">Item 2</a></li>
        <li><a href="#">Item 2</a></li>
        <li><a href="#">Item 2</a></li>
        <li><a href="#">Item 2</a>
        	<input type="checkbox"  name="check_categoria" class="check_categoria"/>
        	<ul>
            	<li><a href="#">SUBitem 1<input type="checkbox"  name="check_categoria" class="check_semana"/></a></li>
                <li><a href="#">SUBitem 1<input type="checkbox"  name="check_categoria" class="check_semana"/></a></li>
                <li><a href="#">SUBitem 1<input type="checkbox"  name="check_categoria" class="check_semana"/></a></li>
                <li><a href="#">SUBitem 1<input type="checkbox"  name="check_categoria" class="check_semana"/></a></li>
                <li><a href="#">SUBitem 1<input type="checkbox"  name="check_categoria" class="check_semana"/></a>
                	<ul>
                    	<li><a href="#">Subitem 2<input type="checkbox"  name="check_categoria" class="check_tipo"/></a></li>
                        <li><a href="#">Subitem 2<input type="checkbox"  name="check_categoria" class="check_tipo"/></a></li>
                        <li><a href="#">Subitem 2<input type="checkbox"  name="check_categoria" class="check_tipo"/></a></li>
                        <li><a href="#">Subitem 2<input type="checkbox"  name="check_categoria" class="check_tipo"/></a></li>
                        <li><a href="#">Subitem 2<input type="checkbox"  name="check_categoria" class="check_tipo"/></a></li>
                    </ul>
                </li>
            </ul>
        </li>
    </ul>
</div>
</body>
</html>