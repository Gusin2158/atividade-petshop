<?php
include 'conexao.php';

// Verifica se recebeu o ID do agendamento
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Busca os dados do agendamento
    $sql = "SELECT ag.id_agendamento, ag.data, ag.horario, 
                   p.id_procedimento, p.descricao AS procedimento,
                   a.id_animal, a.nome AS nome_animal,
                   c.nome AS nome_cliente
            FROM agendamentos ag
            JOIN procedimentos p ON ag.id_procedimento = p.id_procedimento
            JOIN animais a ON ag.id_animal = a.id_animal
            JOIN clientes c ON a.id_cliente = c.id_cliente
            WHERE ag.id_agendamento = $id";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $agendamento = $result->fetch_assoc();
    } else {
        echo "Agendamento não encontrado!";
        exit;
    }
}

// Atualizar se o formulário foi enviado
if (isset($_POST['atualizar'])) {
    $data = $_POST['data'];
    $horario = $_POST['horario'];
    $procedimento = $_POST['procedimento'];
    $animal = $_POST['animal'];

    $sqlUpdate = "UPDATE agendamentos 
                  SET data='$data', horario='$horario', id_procedimento='$procedimento', id_animal='$animal'
                  WHERE id_agendamento=$id";

    if ($conn->query($sqlUpdate) === TRUE) {
        echo "<script>alert('Agendamento atualizado com sucesso!'); window.location='listar_agendamentos.php';</script>";
    } else {
        echo "Erro ao atualizar: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Editar Agendamento</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      padding: 20px;
    }
    .container {
      max-width: 500px;
      margin: auto;
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0px 2px 5px rgba(0,0,0,0.2);
    }
    h2 { text-align: center; margin-bottom: 15px; }
    label { display: block; margin-top: 10px; font-weight: bold; }
    input, select {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    button {
      margin-top: 15px;
      width: 100%;
      padding: 10px;
      background: #28a745;
      border: none;
      color: white;
      font-size: 16px;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover { background: #218838; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Editar Agendamento</h2>
    <form method="post">
      <label>Data:</label>
      <input type="date" name="data" value="<?= $agendamento['data'] ?>" required>

      <label>Horário:</label>
      <input type="time" name="horario" value="<?= $agendamento['horario'] ?>" required>

      <label>Procedimento:</label>
      <select name="procedimento" required>
        <?php
        $sqlProc = "SELECT * FROM procedimentos";
        $procRes = $conn->query($sqlProc);
        while ($row = $procRes->fetch_assoc()) {
            $selected = ($row['id_procedimento'] == $agendamento['id_procedimento']) ? "selected" : "";
            echo "<option value='{$row['id_procedimento']}' $selected>{$row['descricao']}</option>";
        }
        ?>
      </select>

      <label>Animal:</label>
      <select name="animal" required>
        <?php
        $sqlAni = "SELECT * FROM animais";
        $aniRes = $conn->query($sqlAni);
        while ($row = $aniRes->fetch_assoc()) {
            $selected = ($row['id_animal'] == $agendamento['id_animal']) ? "selected" : "";
            echo "<option value='{$row['id_animal']}' $selected>{$row['nome']}</option>";
        }
        ?>
      </select>

      <button type="submit" name="atualizar">Salvar Alterações</button>
    </form>
  </div>
</body>
</html>
