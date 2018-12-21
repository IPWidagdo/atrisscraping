<!DOCTYPE html>
<html>
<head>
	<title>Mundut Captcha</title>
</head>
<body>
<?php
require "Airlines.php";

//$airlines = new Airlines($session_id = $_POST['session_id']);
//var_dump($_GET['captcha'], $_GET['origin']);

if( isset($_GET['origin']) && $_GET['origin'] == "getfare" ){
        echo "<img src=". $_GET['captcha' ] . ">";
        $page = "getfare.php";
} elseif( isset($_GET['origin' ]) && $_GET['origin' ] == "booking"){
        echo "<img src=". $_GET['captcha' ] . ">";
        $page = "booking.php";
} elseif( isset($_GET['captcha' ])  ){
        echo "<img src=". $_GET['captcha' ] . ">";
        $page = "search.php";
}


?>

<div>
	<form  method="post" margin=20% action=<?php echo $page; ?>>
                <div>
                        <br>Masukkan Captcha : <input type="text" name="captcha_code">
                </div>
                <div><input type="text" name="session_id" readonly value="<?=$_GET['session_id']?>" ></div>
                <?php if( array_key_exists('booking', $_GET)){ ?>
                        <div><input type='text' name='booking_id' hidden value='<?=$_GET['booking_id']?>' ></div>
                <?php } ?>
                <?php foreach($_GET as $key=>$val) {?>
                        <input type="text" hidden name="<?php echo $key; ?>" value="<?php echo $val; ?>">
                <?php } ?>
                <div>
                <br><input type="submit" name="ok" value="OK" >
                </div>
	</form>
</div>
</body>
</html>