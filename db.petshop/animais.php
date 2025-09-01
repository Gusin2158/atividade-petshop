<?php include 'header.php'; ?>
<?php include 'conexao.php'; ?>

<main>
  <h2>🐶 Animais</h2>

  <?php
  // CONSULTA
  $sql = "SELECT a.id_animal, a.nome AS nome_animal, a.raca, c.nome AS nome_cliente 
          FROM animais a
          JOIN clientes c ON a.id_cliente = c.id_cliente";
  $stmt = $conn->query($sql);
  $animais = $stmt->fetchAll(PDO::FETCH_ASSOC);
  ?>

  <!-- LISTAGEM -->
  <table>
    <tr><th>Nome</th><th>Raça</th><th>Dono</th><th>Ações</th></tr>
    <?php foreach ($animais as $a): ?>
      <tr>
        <td><?= htmlspecialchars($a['nome_animal']) ?></td>
        <td><?= htmlspecialchars($a['raca']) ?></td>
        <td><?= htmlspecialchars($a['nome_cliente']) ?></td>
        <td>
          <a href="animais.php?editar=<?= $a['id_animal'] ?>">Editar</a>
          <a href="animais.php?excluir=<?= $a['id_animal'] ?>" 
             onclick="return confirm('Excluir este animal?')">Excluir</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>

  <h3>Cadastrar Animal</h3>
  <form method="post" action="">
    <input type="text" name="nome" placeholder="Nome do animal" required>
    <input type="text" name="raca" placeholder="Raça" required>
    <input type="number" name="id_cliente" placeholder="ID do Cliente" required>
    <button type="submit" name="salvar">Salvar</button>
  </form>

  <?php
  // SALVAR ANIMAL
  if (isset($_POST['salvar'])) {
      $sql = "INSERT INTO animais (nome, raca, id_cliente) VALUES (?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->execute([$_POST['nome'], $_POST['raca'], $_POST['id_cliente']]);
      header("Location: animais.php");
      exit;
  }

  // EXCLUIR ANIMAL
  if (isset($_GET['excluir'])) {
      $sql = "DELETE FROM animais WHERE id_animal = ?";
      $stmt = $conn->prepare($sql);
      $stmt->execute([$_GET['excluir']]);
      header("Location: animais.php");
      exit;
  }
  if (isset($_GET['editar'])) {
      $sql = "SELECT * FROM animais WHERE id_animal = ?";
      $stmt = $conn->prepare($sql);   
      $stmt->execute([$_GET['editar']]);
      $animal = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($animal) {

      
          ?>
          <h3>Editar Animal</h3>
          <form method="post" action="">
            <input type="hidden" name="id_animal" value="<?= $animal['id_animal'] ?>">
            <input type="text" name="nome" value="<?= htmlspecialchars($animal['nome']) ?>" required>
            <input type="text" name="raca" value="<?= htmlspecialchars($animal['raca']) ?>" required>
            <input type="number" name="id_cliente" value="<?= $animal['id_cliente'] ?>" required>
            <button type="submit" name="atualizar">Atualizar</button>
          </form>
          <?php
      }
  }
  ?>
</main>

<?php include 'footer.php'; ?>
