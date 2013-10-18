<?php
require_once dirname(__FILE__).'/../Functions/project_Functions.php';

class project_FunctionsTest extends PHPUnit_Framework_TestCase{
	
	function setUp(){
		
		
		$_POST['name'] = 'wilker';
		$_POST['email'] = 'wilker@mail.com';
		$_POST['password'] = '123456';
		
		  
	}
	public function testinclude_projectCompleto(){
		$POST['project.name']= "Projeto";
		$POST['project.description']= "Descri��o";
		
		$id_project = include_project( $POST, $SESSION[$_POST]);
		
		$this->assertNotNull(TRUE, $id_project);
	}
	
	public function testinclude_projectSemDescri��o(){
		$id_project = include_project("Projeto","");
	
		$this->assertNotNull($id_project);
	}
	
}
?>