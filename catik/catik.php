<?php
	require "Airlines.php";
?>

<!DOCTYPE html>
<html>
<head>
	<title>A cup, a cup, a cup, a cup, boy~</title>
</head>
<body>
<div>
	<form action="" method="post" margin=20%>
		<div><br>Keberangkatan : <input type="text" name="berangkat"></div>
		<div><br>Kedatangan : <input type="text" name="datang"></div>
		<div><br>Tanggal Keberangkatan : <input type="date" name="dateFrom" value="<?php echo date('d-m-y'); ?>" /></div>
		<div><input type="submit" name="cari" value="Cari"></div>
	</form>
</div>
</body>
</html>