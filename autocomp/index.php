<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Exemplo de pesquisa dinâmica com AJAX</title>
<script src="ajax.js" type="text/javascript"></script>
<link href="css.css" type="text/css" rel="stylesheet">
<link href="../net.css" type="text/css" rel="stylesheet">
</head>
<body>
<table width="100%" border="0" cellspacing="1" cellpadding="5">
  <tr>
    <td width="30%" bgcolor="#F5F5F5">
    <input type="text" name="pesquisa_usuario" SIZE="30" id="pesquisa_usuario" tabindex="1" autocomplete="off">
    &nbsp;&nbsp;&nbsp;&nbsp;<span onClick="searchSuggest();">vai</span></td>
    <td width="70%"><div id="ajax"></div></td>
  </tr>
</table>
</body>
</html>
