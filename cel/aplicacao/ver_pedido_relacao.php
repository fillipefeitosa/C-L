<?php

/*************************************************************
 * File: ver_pedido_relacao.php
 * purpose: show lots of requires from user, related to the 
 * list of requires. It is called by heading.php
 * 
 ************************************************************/
session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

check_User("index.php"); 
if (isset($submit)) {
        $DB = new PGDB () ;
        $select = new QUERY ($DB) ;
        $update = new QUERY ($DB) ;
        $delete = new QUERY ($DB) ;
        for($count = 0; $count < sizeof($pedidos); $count++)
        {
                 $update->execute("update pedidorel set aprovado= 1 where id_pedido = $pedidos[$count]") ;
                 tratarPedidoRelacao($pedidos[$count]) ;
        }
      for($count = 0; $count < sizeof($remover); $count++)
         {
                  $delete->execute("delete from pedidorel where id_pedido = $remover[$count]") ;
         }
?>

<script language="javascript1.3">

opener.parent.frames['code'].location.reload();
opener.parent.frames['text'].location.replace('main.php?id_projeto=' + '<?=$_SESSION['id_projeto_corrente']?>');

</script>

<h4>Opera��o efetuada com sucesso!</h4>
<script language="javascript1.3">

self.close();

</script>

<?php
} else {?>
<html>
  <head>
     <title>Pedidos de altera��o das Rela�oes</title>
  </head>
<body>
<h2>Pedidos de Altera��o no Conjunto de Rela��es</h2>
<form action="?id_projeto=<?=$id_projeto?>" method="post">

<?php

                $DB = new PGDB () ;
                $select = new QUERY ($DB) ;
                $select2 = new QUERY ($DB) ;
                $select->execute("SELECT * FROM pedidorel WHERE id_projeto = $id_projeto") ;
                if ($select->getntuples() == 0){
                      echo "<BR>Nenhum pedido.<BR>" ;
                }else{
                    $i = 0 ;
                    $record = $select->gofirst () ;
                    
                    while($record != 'LAST_RECORD_REACHED'){
                            $id_usuario = $record['id_usuario'] ;
                            $id_pedido = $record['id_pedido'] ;
                            $tipo_pedido = $record['tipo_pedido'] ;
                            $aprovado = $record['aprovado'] ;
                            $select2->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario") ;
                            $usuario = $select2->gofirst () ;
                            if(strcasecmp($tipo_pedido,'remover')){?>
        
        <br>
                <h3>O usu�rio <a  href="mailto:<?=$usuario['email']?>" ><?=$usuario['nome']?></a> pede para <?=$tipo_pedido?> a rela��o <font color="#ff0000"><?=$record['nome']?></font> <?  if(!strcasecmp($tipo_pedido,'alterar')){echo"para conceito abaixo:</h3>" ;}else{echo"</h3>" ;}?>
                    <table>
                <td><b>Nome:</b></td>
                <td><?=$record['nome']?></td>
            <tr>
                <td><b>Justificativa:</b></td>
                <td><textarea name="justificativa" cols="48" rows="2"><?=$record['justificativa']?></textarea></td>
            </tr>
        </table>
<?php    }else{?>
            <h3>O usu�rio <a  href="mailto:<?=$usuario['email']?>" ><?=$usuario['nome']?></a> pede para <?=$tipo_pedido?> a rela��o <font color="#ff0000"><?=$record['nome']?></font></h3>
<?php }
				if ($aprovado == 1)
                {
    			   echo "[<font color=\"#ff0000\"><STRONG>Aprovado</STRONG></font>]<BR>";
	    		} else
                {
				   echo "[<input type=\"checkbox\" name=\"pedidos[]\" value=\"$id_pedido\"> <STRONG>Aprovar</STRONG>]<BR>  " ;
//                 echo "Rejeitar<input type=\"checkbox\" name=\"remover[]\" value=\"$id_pedido\">" ;
                }
                   echo "[<input type=\"checkbox\" name=\"remover[]\" value=\"$id_pedido\"> <STRONG>Remover da lista</STRONG>]" ;
                   print( "<br>\n<hr color=\"#000000\"><br>\n") ;
		    	   $record = $select->gonext () ;
			}
    }
?>
<input name="submit" type="submit" value="Processar">
</form>
<br><i><a href="showSource.php?file=ver_pedido_cenario.php">Veja o c�digo fonte!</a></i>
</body>
</html>
<?php
}
?>

