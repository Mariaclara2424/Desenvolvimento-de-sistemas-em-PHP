<?php
// nova.php
session_start();

// Verificar sessão
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo    = trim($_POST['titulo']    ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    if ($titulo === '') {
        $erro = 'O título é obrigatório.';
    } else {
        // Inserção com prepared statement incluindo usuario_id
        $stmt = $pdo->prepare(
            "INSERT INTO tarefas (titulo, descricao, usuario_id) VALUES (?, ?, ?)"
        );
        $stmt->execute([$titulo, $descricao, $_SESSION['usuario_id']]);

        header('Location: index.php');
        exit;
    }
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
                    <li class="breadcrumb-item active">Nova Tarefa</li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i>Nova Tarefa
                    </h5>
                </div>
                <div class="card-body p-4">

                    <?php if ($erro !== ''): ?>
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?= htmlspecialchars($erro) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="nova.php">
                        <div class="mb-3">
                            <label for="titulo" class="form-label fw-semibold">
                                Título <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="titulo"
                                name="titulo"
                                placeholder="Ex.: Estudar PHP"
                                value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>"
                                required
                                autofocus
                            >
                        </div>

                        <div class="mb-4">
                            <label for="descricao" class="form-label fw-semibold">
                                Descrição <span class="text-muted fw-normal">(opcional)</span>
                            </label>
                            <textarea
                                class="form-control"
                                id="descricao"
                                name="descricao"
                                rows="4"
                                placeholder="Detalhes sobre a tarefa..."
                            ><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-floppy me-1"></i>Salvar Tarefa
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
