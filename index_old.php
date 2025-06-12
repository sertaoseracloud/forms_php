<?php
session_start();

// Caminho do banco SQLite
$dbFile = __DIR__ . '/todos.sqlite';

try {
    // Cria conexão e tabela se não existir
    $db = new PDO('sqlite:' . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $error = "Erro na conexão com o banco de dados: " . htmlspecialchars($e->getMessage());
}

// Cria a tabela todos se não existir, agora com coluna completed
try {
    $db->exec("CREATE TABLE IF NOT EXISTS todos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        task TEXT NOT NULL,
        completed INTEGER NOT NULL DEFAULT 0
    )");
} catch (PDOException $e) {
    $error = "Erro ao criar tabela: " . htmlspecialchars($e->getMessage());
}

if (!isset($error)) {
    // Adiciona nova tarefa
    if (
        isset($_SERVER['REQUEST_METHOD']) &&
        $_SERVER['REQUEST_METHOD'] === 'POST' &&
        isset($_POST['task']) &&
        trim($_POST['task']) !== ''
    ) {
        try {
            $stmt = $db->prepare("INSERT INTO todos (task) VALUES (:task)");
            $stmt->execute([':task' => trim($_POST['task'])]);
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        } catch (PDOException $e) {
            $error = "Erro ao adicionar tarefa: " . htmlspecialchars($e->getMessage());
        }
    }

    // Remove tarefa
    if (isset($_GET['remove'])) {
        $id = intval($_GET['remove']);
        try {
            $stmt = $db->prepare("DELETE FROM todos WHERE id = :id");
            $stmt->execute([':id' => $id]);
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        } catch (PDOException $e) {
            $error = "Erro ao remover tarefa: " . htmlspecialchars($e->getMessage());
        }
    }

    // Toggle concluir tarefa
    if (isset($_GET['toggle'])) {
        $id = intval($_GET['toggle']);
        try {
            // Busca o estado atual
            $stmt = $db->prepare("SELECT completed FROM todos WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $current = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($current) {
                $newStatus = $current['completed'] ? 0 : 1;
                $stmt = $db->prepare("UPDATE todos SET completed = :completed WHERE id = :id");
                $stmt->execute([':completed' => $newStatus, ':id' => $id]);
            }
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        } catch (PDOException $e) {
            $error = "Erro ao atualizar tarefa: " . htmlspecialchars($e->getMessage());
        }
    }

    // Busca tarefas
    try {
        $stmt = $db->query("SELECT id, task, completed FROM todos ORDER BY id ASC");
        $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Erro ao buscar tarefas: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Lista de Tarefas</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>

<body class="w3-light-grey">
    <div class="w3-container w3-center w3-margin-top">
        <h2 class="w3-text-teal">Lista de Tarefas</h2>

        <?php if (isset($error)): ?>
            <div class="w3-panel w3-red w3-round">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="w3-margin-bottom">
            <input class="w3-input w3-border w3-round" style="max-width:300px;display:inline-block" name="task"
                placeholder="Digite uma tarefa" required>
            <button class="w3-button w3-teal w3-round" type="submit">Adicionar</button>
        </form>

        <ul class="w3-ul w3-card-4" style="max-width:700px; margin:auto; padding:24px 0;">
            <?php if (isset($todos)): ?>
                <?php foreach ($todos as $todo): ?>
                    <li style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="<?php echo $todo['completed'] ? 'text-decoration: line-through; color: #888;' : ''; ?>">
                            <?php echo htmlspecialchars($todo['task']); ?>
                        </span>
                        <div>
                            <a href="?toggle=<?php echo $todo['id']; ?>"
                               class="w3-button <?php echo $todo['completed'] ? 'w3-green' : 'w3-grey'; ?> w3-small w3-round"
                               style="margin-left:12px;">
                                <?php echo $todo['completed'] ? 'Desfazer' : 'Concluir'; ?>
                            </a>
                            <a href="?remove=<?php echo $todo['id']; ?>" class="w3-button w3-red w3-small w3-round"
                                style="margin-left:12px;">Remover</a>
                        </div>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($todos)): ?>
                    <li class="w3-center w3-text-grey">Nenhuma tarefa adicionada.</li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>

    </div>
</body>

</html>