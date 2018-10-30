<?php
	require "Airlines.php";?>
<?php	
	if(isset($_POST['book'])){
		if(isset ($_POST['fname'])){
			if(isset($_POST['lname']))	
				if(isset ($_POST['email'])){
					if(isset($_POST['telepon'])){
							$airlines= new Airlines();
								$schedules = $airlines->getAvailability( $_POST['dateFrom'], $_POST['berangkat'], $_POST['datang']);

	}
}}}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Masukkan Data</title>
</head>
<body>
<div>
	<br>Nama Depan :<input type="text" name="fname">
	<br>Nama Belakang :<input type="text" name="lname">
	<br>Email :<input type="text" name="email">
	<br>Telepon : <input type="text" name="telepon">
	<input type="submit" name="book" value="Pesan">
</div>
</body>
</html>