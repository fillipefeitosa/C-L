<?php
include("bd.inc");
include("httprequest.inc");

// Cen�rio - Lembrar senha 

//Objetivo:	 Permitir o usu�rio cadastrado, que esqueceu sua senha,  receber  a mesma por email	
//Contexto:	 Sistema est� aberto, Usu�rio esqueceu sua senha Usu�rio na tela de lembran�a de 
//           senha. 
//           Pr�-Condi��o: Usu�rio ter acessado ao sistema	
//Atores:	 Usu�rio, Sistema	
//Recursos:	 Banco de Dados	
//Epis�dios: O sistema verifica se o login informado � cadastrado no banco de dados.     
//           Se o login informado for cadastrado, sistema consulta no banco de dados qual 
//           o email e senha do login informado.           
 
$connect = bd_connect() or die("Erro ao conectar ao SGBD");

$query_select_sql = "SELECT * FROM user WHERE login='$login'";

$qyery_result_sql = mysql_query($query_select_sql) or die("Erro ao executar a query");


?>

<html>
<head>
<title>Enviar senha</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF">
<?php
if ( !mysql_num_rows($query_result_sql) )
{

?>
<p style="color: red; font-weight: bold; text-align: center">Login inexistente!</p>
<center><a href="JavaScript:window.history.go(-1)">Voltar</a></center>
<?php
}
else
{
   $row = mysql_fetch_row($qrr);
   $name  = $row[1];
   $mail  = $row[2];
   $login = $row[3];
   $password = $row[4];
   
// Cen�rio - Lembrar senha 

//Objetivo:	 Permitir o usu�rio cadastrado, que esqueceu sua senha,  receber  a mesma por email	
//Contexto:	 Sistema est� aberto, Usu�rio esqueceu sua senha Usu�rio na tela de lembran�a de 
//           senha. 
//           Pr�-Condi��o: Usu�rio ter acessado ao sistema	
//Atores:	 Usu�rio, Sistema	
//Recursos:	 Banco de Dados	
//Epis�dios: Sistema envia a senha para o email cadastrado correspondente ao login que 
//           foi informado pelo usu�rio.     
//           Caso n�o exista nenhum login cadastrado igual ao informado pelo usu�rio, 
//           sistema exibe mensagem de erro na tela dizendo que login � inexistente, e 
//           exibe um bot�o voltar, que redireciona o usu�rio para a tela de login novamente.

   //$Vemail = ini_set("SMTP","mail.gmail.com");  

   //require("class.phpmailer.php");
   // Seta o SMTP sem alterar o config
   //ini_set("SMTP","mail.hotpop.com");
   
   //Funcao que gera uma senha randomica de 6 caracteres

	function createandonstring($n)
	{	
		$string = "ABCDEFGHIJKLMNOPQRSTUVXYWZabcdefghijklmnopqrstuvxywz0123456789";
		$cod = "";
		for($a = 0;$a < $n;$a++)
		{		
			$rand = rand(0,61);
			$cod .= substr($str,$rand,1);
		}	
		return $cod;
	}
	
   $new_password = createrandonstring(6);
   
   $new_password_cript = md5($new_password);
   
   // Substitui senha antiga pela nova senha no banco de dados
   
   $query_update_sql = "update user set password = '$new_password_cript' where login = '$login'";
   $query_resutl_upadate_sql = mysql_query($qUp) or die("Erro ao executar a query de update na tabela usuario");
   
   $body_email = "Caro $name,\n Como solicitado, estamos enviando sua nova senha para acesso ao sistema C&L.\n\n login: $login \n senha: $new_password \n\n Para evitar futuros transtornos altere sua senha o mais breve poss�vel. \n Obrigado! \n Equipe de Suporte do C&L.";
   $headers = "";
   if(mail("$mail", "Nova senha do C&L" , "$body_email" , $headers))
   { 	
   ?>
		<p style="color: red; font-weight: bold; text-align: center">Uma nova senha foi criada e enviada para seu e-mail cadastrado.</p>
	    <center><a href="JavaScript:window.history.go(-2)">Voltar</a></center>
   <?php
   }else{
	?>
		<p style="color: red; font-weight: bold; text-align: center">Ocorreu um erro durante o envio do e-mail!</p>
		<center><a href="JavaScript:window.history.go(-2)">Voltar</a></center>
	<?php
   
   }

}
?>


</body>
</html>
