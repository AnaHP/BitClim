<?php
//prro usamos variables de sesion???
	
session_start();
/*session is started if you don't write this line can't use $_Session  global variable*/

 

$usuario = $_POST['username'];
$pass = $_POST['pass'];

if(empty($usuario) || empty($pass)){
	header("Location: dashboard.php");
	exit();
}

//mysql_connect('localhost','root','','paulin') or die("Error al conectar " . mysql_error());
	$con= mysqli_connect("localhost","root","","bitclim");
    $query= "Select * from usuarios where usernameUsuario='".$usuario."' and passwordUsuario='".$pass."'";

	$result=mysqli_query($con,$query);
	$n=mysqli_num_rows($result);

	$fila= mysqli_fetch_row($result);
	$user = $fila[0];
	$password = $fila[1];

	if($usuario == $user){
		header("Location: dashboard.php");
		$_SESSION["usuario"]=$usuario;
	}else{
		header("Location: dashboard.php");
		exit();
	}

	/*
if($row = mysql_fetch_array($result)){
	if($row['Password'] ==  $pass){
		session_start();
		$_SESSION['usuario'] = $usuario;
		header("Location: contenido.php");
	}else{
		header("Location: index.html");
		exit();
	}
}else{
	header("Location: index.html");
	exit();
}
*/

?>