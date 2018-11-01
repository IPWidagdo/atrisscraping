<?php	
	if(isset($_POST['cari']) && isset ($_POST['berangkat']) && isset($_POST['datang']) && isset($_POST['harga']) && isset($_POST['dateFrom']) && isset($_POST['flightID']) && isset($_POST['adult_passenger_num']) && isset($_POST['child_passenger_num']) && isset($_POST['infant_passenger_num'])){
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>A cup, a cup, a cup, a cup, boy~</title>
</head>
<body>
<div>
	<form  method="post" margin=20% action="getfare.php">
		<div>
			<div><br>Keberangkatan : <input type="text" name="berangkat"></div>
			<div><br>Kedatangan : <input type="text" name="datang"></div>
			<div><br>Tanggal Keberangkatan : <input type="date" name="dateFrom" value="<?php echo date('d-m-y'); ?>" /></div>
			<div><br>Harga : <input type="text" name="harga"></div>
			<div><br>Flight ID : <input type="text" name="flightID"></div>
			<div><br>
			<div><br>
				Penumpang Dewasa:
				<select name="adult_passenger_num">
					<option value="0">0</option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="6">6</option>
					<option value="7">7</option>
				</select>
			</div>
			<div><br>
				Penumpang Anak:
				<select name="child_passenger_num">
					<option value="0">0</option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="6">6</option>
					<option value="7">7</option>
				</select>
			</div>
			<div><br>
				Penumpang Balita:
				<select name="infant_passenger_num">:
					<option value="0">0</option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="6">6</option>
					<option value="7">7</option>
				</select>
			</div>
			<div><br><input type="submit" name="cari" value="Cari" ></div>
		</div>
	</form>
</div>
</body>
</html>