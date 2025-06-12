<?php
session_start();

try {
    $db = new PDO('sqlite:todos.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $error = "[e001] Erro na conexão com o banco de dados: ".htmlspecialchars($e->getMessage());
}

try {
    $db->exec("CREATE TABLE IF NOT EXISTS todos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        task TEXT NOT NULL,
        completed INTEGER NOT NULL DEFAULT 0
    )");
} catch (PDOException $e) {
    $error = "[e002] Erro ao criar tabela: " . htmlspecialchars($e->getMessage());
}

// Inicializar a lista na sessão caso não exista
if (!isset($_SESSION['todos'])) {
    $_SESSION['todos'] = [];
}

// Adcionar nova tarefa
if (
    isset($_SERVER['REQUEST_METHOD']) &&
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['task']) &&
    !empty(trim($_POST['task']))
) {
    //$_SESSION['todos'][] = trim($_POST['task']);
    try {
        $stmt = $db->prepare("INSERT INTO todos(task) VALUES (:task)");
        $stmt->execute([':task' => trim($_POST['task'])]);
        // Redirecionar para evitar o reenvio do formulário
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        $error = "[e003] Erro ao adicionar tarefa: " . htmlspecialchars($e->getMessage());
    }
}

// Remover tarefa
if (isset($_GET['remove'])) {
    //$index = intval($_GET['remove']);
    //if (isset($_SESSION['todos'][$index])) {
        //unset($_SESSION['todos'][$index]);
        // Reindexar o array para evitar buracos
        //$_SESSION['todos'] = array_values($_SESSION['todos']);
    //}

      // Redirecionar para evitar o reenvio do formulário
      //header("Location: " . $_SERVER['PHP_SELF']);
      //exit();
    $id = intval($_GET['remove']);

    try {
        $stmt = $db->prepare("DELETE FROM todos WHERE id = :id");
        $stmt->execute([':id' => trim($id)]);
        // Redirecionar para evitar o reenvio do formulário
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        $error = "[e004] Erro ao remover tarefa: " . htmlspecialchars($e->getMessage());
    }
}

if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);

    try {
        $stmt = $db->prepare("SELECT completed FROM todos WHERE id = :id");
        $stmt->execute([':id' => trim($id)]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);
        if($current) {
            $newStatus = $current['completed'] ? 0 : 1;
            $stmt = $db->prepare("UPDATE todos SET completed = :completed WHERE id = :id");
            $stmt->execute([':completed' => $newStatus, ':id' => $id]);
        }
        // Redirecionar para evitar o reenvio do formulário
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        $error = "[e006] Erro ao remover tarefa: " . htmlspecialchars($e->getMessage());
    }
}

  try {
    $stmt = $db->query("SELECT id, task, completed FROM todos ORDER BY id ASC");
    $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    $error = "[e005] Erro ao carregar tabela: " . htmlspecialchars($e->getMessage());
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

        <form class="w3-margin-bottom" method="post">
            <input class="w3-input w3-border w3-round" style="max-width:300px;display:inline-block" name="task"
                placeholder="Digite uma tarefa">
            <button class="w3-button w3-teal w3-round" type="submit">Adicionar</button>
        </form>

        <ul class="w3-ul w3-card-4" style="max-width:700px; margin:auto; padding:24px 0;">
            <?php foreach ($todos as $todo): ?>
                <li style="display:flex; justify-content:space-between; align-items:center;">
                    <?php echo htmlspecialchars($todo['task']); ?>
                    <a href="?toggle=<?php echo $todo['id']; ?>"
                        class="w3-button <?php echo $todo['completed'] ? 'Desfazer' : 'Concluir' ?> w3-small w3-round"
                    >
                    <?php echo $todo['completed'] ? 'Desfazer' : 'Concluir' ?>
                    </a>
                    <a class="w3-button w3-red w3-small w3-round" style="margin-left:12px;"
                        href="?remove=<?php echo $todo['id']; ?>">Remover</a>
                </li>
            <?php endforeach; ?>
            <?php if (empty($todos)): ?>
                <li class="w3-center w3-text-grey">Nenhuma tarefa adicionada.</li>
            <?php endif; ?>
        </ul>
    </div>
</body>

</html>