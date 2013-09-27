<?php
/*************************************************************
 * File: /Functions/check_User.php
 * purpose: Checks if the user is autenticated. If so, keeps running
 * the program. Otherwise, it will force a logon window.
 * 
 ************************************************************/
if (!(function_exists("check_User"))) 
{
    function check_User($url)
    {
        if(!(isset($_SESSION['id_usuario_corrente'])))
        {
			?>
			
			<script language="javascript1.3">
			
				open('login.php?url=<?=$url?>', 'login', 'dependent,height=430,width=490,resizable,scrollbars,titlebar');
			
			</script>
			
			<?php
			exit();
        }
    }
}


?>