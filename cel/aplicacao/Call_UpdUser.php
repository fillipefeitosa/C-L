<?php
session_start();

include_once("bd.inc");

$r = bd_connect() or die("Erro ao conectar ao SGBD");

// Scenario - Alter Registration
//
//Objective:   Allow user to make alteration in his registered data	
//Context:     User want to change your data registered previously
//             Pre-Condition: User has accessed the system	
//Actor:       User, System.	
//Resources:   Interface	
//Episodes:    The system displays a screen for the User with the following fields completed:
//             nome, email, login, senha e confirmacao da senha; the User can change them.
//             he clicks on a button "Atualizar"

$id_usuario = $_SESSION['id_usuario_corrente'];


$q = "SELECT * FROM usuario WHERE id_usuario='$id_usuario'";

$qrr = mysql_query($q) or die("Erro ao executar a query");

  $row = mysql_fetch_row($qrr);
  $nome  = $row[1];
  $email = $row[2];
  $login = $row[3];
  $senha = $row[4];


?>
<html>
    <head>
        <title>Alterar dados de Usu�rio</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head>

    <script language="JavaScript">
<!--
function TestarBranco(form)
{
login      = form.login.value;
senha      = form.senha.value;
senha_conf = form.senha_conf.value;
nome       = form.nome.value;
email      = form.email.value;

  if (login == "")
    { alert ("Por favor, digite o seu Login.")
      form.login.focus()
      return false;
    }
   if ( email == "")
   {
      alert ( "Por favor, digite o seu e-mail.")
      form.email.focus();
      return false;
   }
  if (senha == "")
    { alert ("Por favor, digite a sua senha.")
      form.senha.focus()
      return false;
    }
    if (nome == "")
    { alert ("Por favor, digite o seu nome.")
      form.nome.focus()
      return false;
    }
   if ( senha != senha_conf )
   {
      alert ( "A senha e a confirmacao nao sao as mesmas!")
      form.senha.focus();
      return false;
   }

}


function checkEmail(email) {
  if(email.value.length > 0)
  {
     if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.value))
     {
        return (true)
     }
   alert("Aten��o: o E-mail digitado n�o � v�lido.")
   email.focus();
   email.select();
   return (false)
  }
}


//-->
</SCRIPT>
    <body>
    <h3 style="text-align: center">Por favor, preencha os dados abaixo:</h3>
        <form action="updUser.php" method="post">
        <table>
            <tr>
                <td>Nome:</td><td colspan="3"><input name="nome" maxlength="255" size="48" type="text" value="<?=$nome?>"></td>
            </tr>
            <tr>
                <td>E-mail:</td><td colspan="3"><input name="email" maxlength="64" size="48" type="text" value="<?=$email?>" OnBlur="checkEmail(this)"></td>
            </tr>
            <tr>
                <td>Login:</td><td><input name="login" maxlength="32" size="24" type="text" value="<?=$login?>"></td>
            </tr>
            <tr>
                <td>Senha:</td><td><input name="senha" maxlength="32" size="16" type="password" value=""></td>
			</tr>
			<tr>
				<td>Senha (confirma��o):</td><td><input name="senha_conf" maxlength="32" size="16" type="password" value=""></td>
            </tr>
            <tr>
                <td align="center" colspan="4" height="40" valign="bottom"><input name="submit" onClick="return TestarBranco(this.form);" type="submit" value="Atualizar"></td>
            </tr>
        </table>
        </form>
        <br><i><a href="showSource.php?file=Call_UpdUser.php">Veja o c�digo fonte!</a></i>
     </body>
</html>