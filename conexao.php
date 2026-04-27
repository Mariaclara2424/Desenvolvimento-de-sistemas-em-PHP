<?php
// conexao.php - Conexão com o banco de dados MySQL via PDO

$host     = "127.0.0.1";
$dbname   = "tarefas";
$username = "root";
$password = "ceub123456";
$porta    = "3306";

try {
    $pdo = new PDO("mysql:host=$host;port=$porta;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>
