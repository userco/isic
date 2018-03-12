<?php
try{
		//$link = new PDO("sqlsrv:Server=62.44.109.144;Database=SU_STUDENTDATABASE", 'maria', '123456');
		$link = new PDO("dblib:version=7.0;charset=UTF-8;host=62.44.109.144;dbname=SU_STUDENTDATABASE", 'maria', '123456');
	} catch (PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
			exit();
	}
	try{
	$dbh = new PDO('mysql:dbname=isic10;host=localhost;charset=utf8', 'root', 'strongly');
	} catch (PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
			exit();
	}
