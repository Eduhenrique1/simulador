<?php
$host = 'junction.proxy.rlwy.net';
$port = 51792;
$dbname = 'railway';
$username = 'root';
$password = 'qSzGzgmOouqYbaosLtrZmqviQOCjMPIC';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
