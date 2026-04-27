<?php
// editar.php
session_start();

// Verificar sessão
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'conexao.php';

$id   = (int)($_GET['id'] ?? 0);
$erro = '';

// Buscar tarefa garantindo que pertence ao usuário logado
$stmt = $pdo->prepare(
    "SELECT * FROM tarefas WHERE id = ? AND usuario_id = ?"
);
$stmt->execute([$id, $_SESSION['usuario_id']]);
$tarefa = $stmt->fetch();

if (!$tarefa) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo    = trim($_POST['titulo']    ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $status    = $_POST['status'] ?? 'pendente';

    // Validar status
    if (!in_array($status, ['pendente', 'concluida'])) {
        $status = 'pendente';
    }

    if ($titulo === '') {
        $erro = 'O título é obrigatório.';
    } else {
        $stmt = $pdo->prepare(
            "UPDATE tarefas SET titulo = ?, descricao = ?, status = ?
             WHERE id = ? AND usuario_id = ?"
        );
        $stmt->execute([$titulo, $descricao, $status, $id, $_SESSION['usuario_id']]);

        header('Location: index.php');
        exit;
    }

    // Atualiza objeto local para repreencher o form em caso de erro
    $tarefa['titulo']    = $titulo;
    $tarefa['descricao'] = $descricao;
    $tarefa['status']    = $status;
}

include 'layout.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Tarefas</a></li>
                    <li class="breadcrumb-item active">Editar Tarefa</li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i>Editar Tarefa
                    </h5>
                </div>
                <div class="card-body p-4">

                    <?php if ($erro !== ''): ?>
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?= htmlspecialchars($erro) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="editar.php?id=<?= $id ?>">
                        <div class="mb-3">
                            <label for="titulo" class="form-label fw-semibold">
                                Título <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="titulo"
                                name="titulo"
                                value="<?= htmlspecialchars($tarefa['titulo']) ?>"
                                required
                                autofocus
                            >
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label fw-semibold">Descrição</label>
                            <textarea
                                class="form-control"
                                id="descricao"
                                name="descricao"
                                rows="4"
                            ><?= htmlspecialchars($tarefa['descricao'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="status" class="form-label fw-semibold">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="pendente"
                                    <?= $tarefa['status'] === 'pendente'  ? 'selected' : '' ?>>
                                    ⏳ Pendente
                                </option>
                                <option value="concluida"
                                    <?= $tarefa['status'] === 'concluida' ? 'selected' : '' ?>>
                                    ✅ Concluída
                                </option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-floppy me-1"></i>Salvar Alterações
                            </button>
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Cancelar
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
