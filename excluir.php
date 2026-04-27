<?php
// excluir.php
session_start();

// Verificar sessão
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'conexao.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    // Deletar garantindo que a tarefa pertence ao usuário logado
    $stmt = $pdo->prepare(
        "DELETE FROM tarefas WHERE id = ? AND usuario_id = ?"
    );
    $stmt->execute([$id, $_SESSION['usuario_id']]);
}

header('Location: index.php');
exit;
?>
