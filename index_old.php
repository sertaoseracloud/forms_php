<?php
session_start();

// Inicializa lista se ainda não existir
if (!isset($_SESSION['todos'])) {
    $_SESSION['todos'] = [];
}

// Adiciona nova tarefa
if (
    isset($_SERVER['REQUEST_METHOD']) &&
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['task']) &&
    trim($_POST['task']) !== ''
) {
    $_SESSION['todos'][] = trim($_POST['task']);
    header('Location: ' . $_SERVER['PHP_SELF']); // Evita re-envio ao atualizar
    exit();
}

// Remove tarefa
if (isset($_GET['remove'])) {
    $index = intval($_GET['remove']);
    if (isset($_SESSION['todos'][$index])) {
        unset($_SESSION['todos'][$index]);
        $_SESSION['todos'] = array_values($_SESSION['todos']); // Reindexa
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
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

        <form method="post" class="w3-margin-bottom">
            <input class="w3-input w3-border w3-round" style="max-width:300px;display:inline-block" name="task"
                placeholder="Digite uma tarefa" required>
            <button class="w3-button w3-teal w3-round" type="submit">Adicionar</button>
        </form>

        <ul class="w3-ul w3-card-4" style="max-width:700px; margin:auto; padding:24px 0;">
    <?php foreach ($_SESSION['todos'] as $index => $task): ?>
        <li style="display:flex; justify-content:space-between; align-items:center;">
            <span><?php echo htmlspecialchars($task); ?></span>
            <a href="?remove=<?php echo $index; ?>" class="w3-button w3-red w3-small w3-round" style="margin-left:12px;">Remover</a>
        </li>
    <?php endforeach; ?>
      <?php if (empty($_SESSION['todos'])): ?>
        <li class="w3-text-grey">Nenhuma tarefa por enquanto.</li>
      <?php endif; ?>
    </ul>

    </div>
</body>

</html>