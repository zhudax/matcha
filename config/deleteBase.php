<?php

include("database.php");

// Delete database matcha

try {
	$pdo = new PDO($DB_DSN_NO_BASE, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "DROP DATABASE IF EXISTS $DB_NAME";
	$pdo->exec($sql);
	echo "Deleted database with success." . "<br>";
} catch (PDOException $e) {
	echo "Failed to delete database " . $e->getMessage() . "<br>";
}
$pdo = null;
