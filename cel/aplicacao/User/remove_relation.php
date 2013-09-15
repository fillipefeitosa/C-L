<?php

// remove_relacao.php: This script make a request to remove a relation


session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
check_User("index.php");        

insertRequestRemoveRelation($_SESSION['id_projeto_corrente'], $id_relacao, $_SESSION['id_usuario_corrente']);

?>  

<script language="javascript1.3">

opener.parent.frames['code'].location.reload();
opener.parent.frames['text'].location.replace('main.php?id_projeto=<?=$_SESSION['id_projeto_corrente']?>');

<?php

// Scenario - Remove Relation

//Objective: Allows a user to remove a relation 
//Contexto:	Usu�rio deseja excluir um conceito
//              Pr�-Condi��o: Login, cen�rio cadastrado no sistema
//Atores:	Usu�rio, Sistema
//Exce��o:	Se todos os campos n�o foram preenchidos, retorna para o usu�rio uma mensagem
//              avisando que todos os campos devem ser preenchidos e um bot�o de voltar para a pagina anterior.

?>

</script>

<h4>Opera��o efetuada com sucesso!</h4>

<script language="javascript1.3">

self.close();

</script>
