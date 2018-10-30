<?php require("Airlines.php"); ?>
<?php	
	if(isset ($_POST['login'])){
		if(isset ($_POST['user'])){
			if(isset($_POST['pass'])){
					$airlines= new Airlines();
					$airlines->setUserNamePassword($_POST['user'], $_POST['pass']);
					$response = $airlines->login();
					
					if (isset($response['status']) && $response['status'] != "success" ) {
						var_dump($response);
					}


					$airlines->logout();
				
}}}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
</head>
<body>
<div>
	<form action="" method="post">
		<div><br>Username : <input type="text" name="user"></div>
		<div><br>Password : <input type="password" name="pass"></div>
		<div><input type="submit" name="login" value="Masuk"></div>
	</form>
</div>
</body>
</html>
