<?php

require("database.php");
try {
	$pdo = new PDO($DB_DSN_NO_BASE, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "CREATE DATABASE IF NOT EXISTS $DB_NAME";
	$pdo->exec($sql);
	echo "Base " . $DB_NAME . " created with success." . "<br>";
} catch (PDOException $e) {
	echo "Failed to create database " . $e->getMessage() . "<br>";
}
$pdo = null;

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "CREATE TABLE IF NOT EXISTS `users` (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		nom VARCHAR(255) NOT NULL,
		prenom VARCHAR (255) NOT NULL,
		login VARCHAR(15) NOT NULL,
		password VARCHAR(255) NOT NULL,
		email VARCHAR(255) NOT NULL,
		cle VARCHAR(255) NOT NULL,
		checked ENUM('N', 'Y') NOT NULL DEFAULT 'N',
		last_log TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		online ENUM('N', 'Y') NOT NULL DEFAULT 'N'
	)";
	$pdo->exec($sql);
	echo "Table users created with success." . "<br>";
} catch (PDOException $e) {
	echo "Failed to create users table " . $e->getMessage() . "<br>";
}
$pdo = null;

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "CREATE TABLE IF NOT EXISTS `profil` (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		uid INT(6) UNSIGNED NOT NULL,
		genre ENUM('M', 'F', 'ALL') NOT NULL DEFAULT 'ALL',
		orient_sex ENUM('M', 'F', 'ALL') NOT NULL DEFAULT 'ALL',
		age INT(6) UNSIGNED NOT NULL,
		birthday DATE NOT NULL,
		bio TEXT NOT NULL,
		nb_like INT(6) UNSIGNED NOT NULL DEFAULT 0,
		nb_view INT(6) UNSIGNED NOT NULL DEFAULT 0
	)";
	$pdo->exec($sql);
	echo "Table profil created with success." . "<br>";
} catch (PDOException $e) {
	echo "Failed to create profil table " . $e->getMessage() . "<br>";
}
$pdo = null;

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "CREATE TABLE IF NOT EXISTS `img` (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		uid INT(6) UNSIGNED NOT NULL,
		img_path VARCHAR(255) NOT NULL,
		is_profil ENUM('N', 'Y') NOT NULL DEFAULT 'N'
	)";
	$pdo->exec($sql);
	echo "Table img created with success." . "<br>";
} catch (PDOException $e) {
	echo "Failed to create img table " . $e->getMessage() . "<br>";
}
$pdo = null;

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "CREATE TABLE IF NOT EXISTS `tags` (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		uid INT(6) UNSIGNED NOT NULL,
		tag VARCHAR(255) NOT NULL
	)";
	$pdo->exec($sql);
	echo "Table tags created with success." . "<br>";
} catch (PDOException $e) {
	echo "Failed to create tags table " . $e->getMessage() . "<br>";
}
$pdo = null;

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "CREATE TABLE IF NOT EXISTS `adresse` (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		uid INT(6) UNSIGNED NOT NULL,
		pays VARCHAR(255) NOT NULL,
		region VARCHAR(255) NOT NULL,
		ville VARCHAR(255) NOT NULL,
		arrond VARCHAR(10) NOT NULL,
		rue VARCHAR(255) NOT NULL,
		num VARCHAR(10) NOT NULL,
		latitude DOUBLE NOT NULL,
		longitude DOUBLE NOT NULL,
		hidden ENUM('N', 'Y') NOT NULL DEFAULT 'N'
	)";
	$pdo->exec($sql);
	echo "Table adresse created with success." . "<br>";
} catch (PDOException $e) {
	echo "Failed to create adresse table " . $e->getMessage() . "<br>";
}
$pdo = null;

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "CREATE TABLE IF NOT EXISTS `chat` (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		src_id INT(6) UNSIGNED NOT NULL,
		dst_id INT(6) UNSIGNED NOT NULL,
		message TEXT NOT NULL,
		is_read ENUM('N', 'Y') NOT NULL DEFAULT 'N',
		dates TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
	)";
	$pdo->exec($sql);
	echo "Table chat created with success." . "<br>";
} catch (PDOException $e) {
	echo "Failed to create chat table " . $e->getMessage() . "<br>";
}
$pdo = null;

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "CREATE TABLE IF NOT EXISTS `notif` (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		uid INT(6) UNSIGNED NOT NULL,
		src_id INT(6) UNSIGNED NOT NULL,
		likes ENUM('N', 'Y') NOT NULL DEFAULT 'N',
		retour_likes ENUM('N', 'Y') NOT NULL DEFAULT 'N',
		match_unlikes ENUM('N', 'Y') NOT NULL DEFAULT 'N',
		view ENUM('N', 'Y') NOT NULL DEFAULT 'N',
		message ENUM('N', 'Y') NOT NULL DEFAULT 'N',
		is_read ENUM('N', 'Y') NOT NULL DEFAULT 'N'
	)";
	$pdo->exec($sql);
	echo "Table notif created with success." . "<br>";
} catch (PDOException $e) {
	echo "Failed to create notif table " . $e->getMessage() . "<br>";
}
$pdo = null;

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "CREATE TABLE IF NOT EXISTS `likes` (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		src_id INT(6) UNSIGNED NOT NULL,
		dst_id INT(6) UNSIGNED NOT NULL
	)";
	$pdo->exec($sql);
	echo "Table likes created with success." . "<br>";
} catch (PDOException $e) {
	echo "Failed to create likes table " . $e->getMessage() . "<br>";
}
$pdo = null;

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "CREATE TABLE IF NOT EXISTS `view` (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		src_id INT(6) UNSIGNED NOT NULL,
		dst_id INT(6) UNSIGNED NOT NULL
	)";
	$pdo->exec($sql);
	echo "Table view created with success." . "<br>";
} catch (PDOException $e) {
	echo "Failed to create view table " . $e->getMessage() . "<br>";
}
$pdo = null;

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "CREATE TABLE IF NOT EXISTS `match` (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		user1 INT(6) UNSIGNED NOT NULL,
		user2 INT(6) UNSIGNED NOT NULL
	)";
	$pdo->exec($sql);
	echo "Table match created with success." . "<br>";
} catch (PDOException $e) {
	echo "Failed to create match table " . $e->getMessage() . "<br>";
}
$pdo = null;

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "CREATE TABLE IF NOT EXISTS `blackList` (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		uid INT(6) UNSIGNED NOT NULL,
		dst_id INT(6) UNSIGNED NOT NULL
	)";
	$pdo->exec($sql);
	echo "Table blackList created with success." . "<br>";
} catch (PDOException $e) {
	echo "Failed to create blackList table " . $e->getMessage() . "<br>";
}
$pdo = null;

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "CREATE TABLE IF NOT EXISTS `report` (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		uid INT(6) UNSIGNED NOT NULL,
		dst_id INT(6) UNSIGNED NOT NULL
	)";
	$pdo->exec($sql);
	echo "Table report created with success." . "<br>";
} catch (PDOException $e) {
	echo "Failed to create report table " . $e->getMessage() . "<br>";
}
$pdo = null;
