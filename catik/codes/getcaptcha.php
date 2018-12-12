<!DOCTYPE html>
<html>
<head>
	<title>Mundut Captcha</title>
</head>
<body>
<?php
require "Airlines.php";

//$airlines = new Airlines($session_id = $_POST['session_id']);

if( isset($_GET['captcha' ]) ){
    echo "<img src=". $_GET['captcha' ] . ">";
}
?>

<div>
	<form  method="post" margin=20% action='search.php'>
	        <div>
			<br>Masukkan Captcha : <input type="text" name="captcha_code">
        </div>
        <div><br>
        <div><input type="text" name="session_id" readonly value="<?=$_GET['session_id']?>" ></div>
        <div><input type="text" name="booking_id" readonly value="<?=$_GET['booking_id']?>" ></div>
        <input type="submit" name="ok" value="OK" ></div>
	</form>
</div>
</body>
</html>