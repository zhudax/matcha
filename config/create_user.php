<?php

require("database.php");

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $str = file_get_contents("seed/users.csv");
	$requete = "INSERT INTO `users`
    (`id`, `nom`, `prenom`, `login`, `password`, `email`, `cle`, `checked`)
    VALUES $str ";
    $sql = $pdo->prepare($requete);
	$sql->execute();
	echo "Insert users with success." . "<br>";
} catch (PDOException $e) {
	echo "Failed to insert users table " . $e->getMessage() . "<br>";
}
$pdo = null;

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $str = file_get_contents("seed/profil.csv");
	$requete = "INSERT INTO `profil`
    (`uid`, `genre`, `orient_sex`, `age`, `birthday`, `bio`, `nb_like`, `nb_view`)
    VALUES $str ";
    $sql = $pdo->prepare($requete);
	$sql->execute();
	echo "Insert profil with success." . "<br>";
} catch (PDOException $e) {
	echo "Failed to insert profil table " . $e->getMessage() . "<br>";
}
$pdo = null;

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $str = file_get_contents("seed/adresse.csv");
	$requete = "INSERT INTO `adresse`
    (`uid`, `pays`, `region`, `ville`, `arrond`, `rue`, `num`, `latitude`, `longitude`)
    VALUES $str ";
    $sql = $pdo->prepare($requete);
	$sql->execute();
	echo "Insert adresse with success." . "<br>";
} catch (PDOException $e) {
	echo "Failed to insert adresse table " . $e->getMessage() . "<br>";
}
$pdo = null;

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $str = file_get_contents("seed/img.csv");
	$requete = "INSERT INTO `img`
    (`uid`, `img_path`, `is_profil`) VALUES $str ";
    $sql = $pdo->prepare($requete);
	$sql->execute();
	echo "Insert img with success." . "<br>";
} catch (PDOException $e) {
	echo "Failed to insert img table " . $e->getMessage() . "<br>";
}
$pdo = null;

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $str = file_get_contents("seed/view.csv");
	$requete = "INSERT INTO `view`
    (`src_id`, `dst_id`) VALUES $str ";
    $sql = $pdo->prepare($requete);
	$sql->execute();
	echo "Insert view with success." . "<br>";
} catch (PDOException $e) {
	echo "Failed to insert view table " . $e->getMessage() . "<br>";
}
$pdo = null;

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $str = file_get_contents("seed/likes.csv");
	$requete = "INSERT INTO `likes`
    (`src_id`, `dst_id`) VALUES $str ";
    $sql = $pdo->prepare($requete);
	$sql->execute();
	echo "Insert likes with success." . "<br>";
} catch (PDOException $e) {
	echo "Failed to insert likes table " . $e->getMessage() . "<br>";
}
$pdo = null;

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $str = file_get_contents("seed/match.csv");
	$requete = "INSERT INTO `match`
    (`user1`, `user2`) VALUES $str ";
    $sql = $pdo->prepare($requete);
	$sql->execute();
	echo "Insert match with success." . "<br>";
} catch (PDOException $e) {
	echo "Failed to insert match table " . $e->getMessage() . "<br>";
}
$pdo = null;

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $str = file_get_contents("seed/tags.csv");
	$requete = "INSERT INTO `tags`
    (`uid`, `tag`) VALUES $str ";
    $sql = $pdo->prepare($requete);
	$sql->execute();
	echo "Insert tags with success." . "<br>";
} catch (PDOException $e) {
	echo "Failed to insert tags table " . $e->getMessage() . "<br>";
}
$pdo = null;
