<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Teste ajax jquery tutsmais</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"> </script>
<script>
$(function(){
    $('input[type=submit]').click(function(){
 
    $.ajax({
            type      : 'post',
 
            url       : 'teste.php',
 
            data      : 'nome='+ $('#campo1').val() +'&sobrenome='+ $('#campo2').val(),
 
            dataType  : 'html',
 
            success : function(txt){
                    $('body p').html(txt);
                }
        });
 
        });
    });
</script>
</head>
 
<body>
 
 <h2>form via ajax</h2>
    Digite seu nome:</label> <input type="text" id="campo1" /><br />
    Digite seu sobrenome:</label> <input type="text" id="campo2" /><br />
 
    <input type="submit" /><br />
<p></p>
</body>
</html>