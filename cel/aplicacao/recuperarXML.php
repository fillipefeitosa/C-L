<?php

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

check_User("index.php");     

$XML = "";

?>
<html>
<body>
    <head>
        <title>Recuperar XML</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">        
    </head>

<?php

//Cen�rio -  Gerar Relat�rios XML 

//Objetivo:   Permitir ao administrador gerar relat�rios em formato XML de um projeto,
//             identificados por data.
//Contexto:   Gerente deseja gerar um relat�rio para um dos projetos da qual � administrador.
//              Pr�-Condi��o: Login, projeto cadastrado.
//Atores:     Administrador
//Recursos:   Sistema, dados do relat�rio, dados cadastrados do projeto, banco de dados.
//Epis�dios:  Restri��o: Recuperar os dados em XML do Banco de dados e os transformar
//                       por uma XSL para a exibi��o.

$bd_recupera = bd_connect() or die("Erro ao conectar ao SGBD");
if (isset($erase))
{
	if ( $erase )
	{
		$query_delete_sql = "DELETE FROM publicacao WHERE id_projeto = '$id_projeto' AND versao = '$versao' ";
		$query_erase_result_sql = mysql_query($query_delete_sql);	
	}
}
$query_select_sql = "SELECT * FROM publicacao WHERE id_projeto = '$id_projeto'";
$query_result_sql = mysql_query($query_select_sql) or die("Erro ao enviar a query");

?>

<h2>Recupera XML/XSL</h2><br>

<?php

while ( $result = mysql_fetch_row($query_result_sql) )
{
   $date  = $result[1];
   $version = $result[2];
   $XML    = $result[3];	
?>

<table>
   <tr>
                <th>Vers�o:</th><td><?=$versao?></td>
                <th>Data:</th><td><?=$data?></td>
                <th><a href="mostraXML.php?id_projeto=<?=$id_projeto?>&versao=<?=$versao?>">XML</a></th>
                <th><a href="recuperarXML.php?id_projeto=<?=$id_projeto?>&versao=<?=$versao?>&apaga=true">Apaga XML</a></th>
                
   </tr>


</table>

<?php
}

?>

<br><i><a href="showSource.php?file=recuperarXML.php">Veja o c�digo fonte!</a></i>
    
    </body>

</html>
