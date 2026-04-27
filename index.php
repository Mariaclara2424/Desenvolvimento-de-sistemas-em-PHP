<?php
// index.php
// FRAMEWORK: Bootstrap 5
// Importado via CDN em layout.php: https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css
session_start();

// Verificar sessão — redireciona se não estiver logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'conexao.php';

// Buscar tarefas do usuário logado
$stmt = $pdo->prepare(
    "SELECT id, titulo, descricao, status, criado_em
     FROM tarefas
     WHERE usuario_id = ?
     ORDER BY criado_em DESC"
);
$stmt->execute([$_SESSION['usuario_id']]);
$tarefas = $stmt->fetchAll();

// Contadores para os cards de resumo
$total     = count($tarefas);
$pendentes = count(array_filter($tarefas, fn($t) => $t['status'] === 'pendente'));
$concluidas= $total - $pendentes;

include 'layout.php';
?>

<div class="container py-4">

    <!-- Cards de resumo -->
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card text-center p-3">
                <div class="fs-1 fw-bold text-primary"><?= $total ?></div>
                <div class="text-muted small text-uppercase">Total de Tarefas</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card text-center p-3">
                <div class="fs-1 fw-bold text-warning"><?= $pendentes ?></div>
                <div class="text-muted small text-uppercase">Pendentes</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card text-center p-3">
                <div class="fs-1 fw-bold text-success"><?= $concluidas ?></div>
                <div class="text-muted small text-uppercase">Concluídas</div>
            </div>
        </div>
    </div>

    <!-- Cabeçalho + botão Nova Tarefa -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-list-task me-2 text-primary"></i>Minhas Tarefas
        </h5>
        <a href="nova.php" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nova Tarefa
        </a>
    </div>

    <!-- Tabela de tarefas -->
    <div class="card">
        <div class="card-body p-0">
            <?php if (empty($tarefas)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    Nenhuma tarefa cadastrada ainda.<br>
                    <a href="nova.php" class="btn btn-outline-primary mt-3">
                        <i class="bi bi-plus-lg me-1"></i>Adicionar primeira tarefa
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Título</th>
                                <th>Status</th>
                                <th>Criado em</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tarefas as $tarefa): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?= htmlspecialchars($tarefa['titulo']) ?></div>
                                    <?php if ($tarefa['descricao']): ?>
                                        <div class="text-muted small"><?= htmlspecialchars(mb_strimwidth($tarefa['descricao'], 0, 60, '…')) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($tarefa['status'] === 'concluida'): ?>
                                        <span class="badge badge-concluida rounded-pill px-3 py-2">
                                            <i class="bi bi-check-circle me-1"></i>Concluída
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-pendente rounded-pill px-3 py-2">
                                            <i class="bi bi-clock me-1"></i>Pendente
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small">
                                    <?= date('d/m/Y H:i', strtotime($tarefa['criado_em'])) ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <!-- Editar -->
                                        <a href="editar.php?id=<?= $tarefa['id'] ?>"
                                           class="btn btn-sm btn-outline-secondary"
                                           title="Editar">
                                            <i class="bi bi-pencil"></i> Editar
                                        </a>

                                        <!-- Concluir (só se pendente) -->
                                        <?php if ($tarefa['status'] === 'pendente'): ?>
                                        <a href="concluir.php?id=<?= $tarefa['id'] ?>"
                                           class="btn btn-sm btn-outline-success"
                                           title="Marcar como concluída"
                                           onclick="return confirm('Marcar tarefa como concluída?')">
                                            <i class="bi bi-check2"></i> Concluir
                                        </a>
                                        <?php endif; ?>

                                        <!-- Excluir -->
                                        <a href="excluir.php?id=<?= $tarefa['id'] ?>"
                                           class="btn btn-sm btn-outline-danger"
                                           title="Excluir"
                                           onclick="return confirm('Deseja excluir esta tarefa?')">
                                            <i class="bi bi-trash"></i> Excluir
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
