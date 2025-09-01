<?php include 'header.php'; ?>
<?php include 'conexao.php'; ?>

<main>
  <h2>📅 Agendamentos</h2>

  <?php
  // Se for edição, buscar o registro
  $agendamentoEdicao = null;
  if (isset($_GET['editar'])) {
      $sql = "SELECT * FROM agendamentos WHERE id_agendamento = ?";
      $stmt = $conn->prepare($sql);
      $stmt->execute([$_GET['editar']]);
      $agendamentoEdicao = $stmt->fetch(PDO::FETCH_ASSOC);
  }

  // SALVAR (INSERT ou UPDATE)
  if (isset($_POST['salvar'])) {
      if (!empty($_POST['id_agendamento'])) {
          // UPDATE
          $sql = "UPDATE agendamentos 
                     SET data = ?, id_animal = ?, id_cliente = ?, id_procedimento = ?
                   WHERE id_agendamento = ?";
          $stmt = $conn->prepare($sql);
          $stmt->execute([
              $_POST['data'], 
              $_POST['id_animal'], 
              $_POST['id_cliente'], 
              $_POST['id_procedimento'],
              $_POST['id_agendamento']
          ]);
      } else {
          // INSERT
          $sql = "INSERT INTO agendamentos (data, id_animal, id_cliente, id_procedimento) 
                  VALUES (?, ?, ?, ?)";
          $stmt = $conn->prepare($sql);
          $stmt->execute([$_POST['data'], $_POST['id_animal'], $_POST['id_cliente'], $_POST['id_procedimento']]);
      }
      header("Location: agendamento.php");
      exit;
  }

  // EXCLUIR
  if (isset($_GET['excluir'])) {
      $sql = "DELETE FROM agendamentos WHERE id_agendamento = ?";
      $stmt = $conn->prepare($sql);
      $stmt->execute([$_GET['excluir']]);
      header("Location: agendamento.php");
      exit;
  }

  // CONSULTA COM JOIN
  $sql = "SELECT ag.id_agendamento, ag.data, 
                 p.descricao AS procedimento, 
                 a.nome AS nome_animal, 
                 c.nome AS nome_cliente
          FROM agendamentos ag
          JOIN procedimentos p ON ag.id_procedimento = p.id_procedimento
          JOIN animais a ON ag.id_animal = a.id_animal
          JOIN clientes c ON ag.id_cliente = c.id_cliente";
  $stmt = $conn->query($sql);
  $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Dados para os selects
  $animais = $conn->query("SELECT * FROM animais")->fetchAll(PDO::FETCH_ASSOC);
  $clientes = $conn->query("SELECT * FROM clientes")->fetchAll(PDO::FETCH_ASSOC);
  $procedimentos = $conn->query("SELECT * FROM procedimentos")->fetchAll(PDO::FETCH_ASSOC);
  ?>

  <!-- LISTAGEM -->
  <table border="1" cellpadding="5" cellspacing="0">
    <tr><th>Procedimento</th><th>Data</th><th>Animal</th><th>Cliente</th><th>Ações</th></tr>
    <?php foreach ($agendamentos as $ag): ?>
      <tr>
        <td><?= htmlspecialchars($ag['procedimento']) ?></td>
        <td><?= htmlspecialchars($ag['data']) ?></td>
        <td><?= htmlspecialchars($ag['nome_animal']) ?></td>
        <td><?= htmlspecialchars($ag['nome_cliente']) ?></td>
        <td>
          <a href="agendamento.php?editar=<?= $ag['id_agendamento'] ?>">Editar</a> | 
          <a href="agendamento.php?excluir=<?= $ag['id_agendamento'] ?>" 
             onclick="return confirm('Excluir agendamento?')">Excluir</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>

  <!-- FORM CADASTRO/EDIÇÃO -->
  <h3><?= $agendamentoEdicao ? "Editar Agendamento" : "Novo Agendamento" ?></h3>
  <form method="post" action="">
    <input type="hidden" name="id_agendamento" value="<?= $agendamentoEdicao['id_agendamento'] ?? '' ?>">

    <label>Data:</label>
    <input type="date" name="data" required value="<?= $agendamentoEdicao['data'] ?? '' ?>">

    <label>Procedimento:</label>
    <select name="id_procedimento" required>
      <option value="">Selecione</option>
      <?php foreach ($procedimentos as $p): ?>
        <option value="<?= $p['id_procedimento'] ?>" 
          <?= ($agendamentoEdicao && $p['id_procedimento'] == $agendamentoEdicao['id_procedimento']) ? "selected" : "" ?>>
          <?= htmlspecialchars($p['descricao']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label>Animal:</label>
    <select name="id_animal" required>
      <option value="">Selecione</option>
      <?php foreach ($animais as $a): ?>
        <option value="<?= $a['id_animal'] ?>" 
          <?= ($agendamentoEdicao && $a['id_animal'] == $agendamentoEdicao['id_animal']) ? "selected" : "" ?>>
          <?= htmlspecialchars($a['nome']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label>Cliente:</label>
    <select name="id_cliente" required>
      <option value="">Selecione</option>
      <?php foreach ($clientes as $c): ?>
        <option value="<?= $c['id_cliente'] ?>" 
          <?= ($agendamentoEdicao && $c['id_cliente'] == $agendamentoEdicao['id_cliente']) ? "selected" : "" ?>>
          <?= htmlspecialchars($c['nome']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <button type="submit" name="salvar"><?= $agendamentoEdicao ? "Atualizar" : "Salvar" ?></button>
  </form>
</main>

<?php include 'footer.php'; ?>
