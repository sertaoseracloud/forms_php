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

        <form class="w3-margin-bottom" onsubmit="return false;">
            <input class="w3-input w3-border w3-round" style="max-width:300px;display:inline-block" name="task"
                placeholder="Digite uma tarefa">
            <button class="w3-button w3-teal w3-round" type="button">Adicionar</button>
        </form>

        <ul class="w3-ul w3-card-4" style="max-width:700px; margin:auto; padding:24px 0;">
            <li style="display:flex; justify-content:space-between; align-items:center;">
                <span>Estudar PHP</span>
                <a class="w3-button w3-red w3-small w3-round" style="margin-left:12px;" href="#">Remover</a>
            </li>
            <li style="display:flex; justify-content:space-between; align-items:center;">
                <span>Ler documentação do W3.css</span>
                <a class="w3-button w3-red w3-small w3-round" style="margin-left:12px;" href="#">Remover</a>
            </li>
            <li style="display:flex; justify-content:space-between; align-items:center;">
                <span>Fazer um ToDo List</span>
                <a class="w3-button w3-red w3-small w3-round" style="margin-left:12px;" href="#">Remover</a>
            </li>
        </ul>
    </div>
</body>

</html>