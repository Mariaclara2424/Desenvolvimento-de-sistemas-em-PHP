<?php
// concluir.php
session_start();

// Verificar sessão
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'conexao.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    // Atualizar status para "concluida" garantindo que pertence ao usuário logado
    $stmt = $pdo->prepare(
        "UPDATE tarefas SET status = 'concluida' WHERE id = ? AND usuario_id = ?"
    );
    $stmt->execute([$id, $_SESSION['usuario_id']]);
}

header('Location: index.php');
exit;
?>
