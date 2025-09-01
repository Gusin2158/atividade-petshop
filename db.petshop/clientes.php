<?php include 'header.php'; ?>
<?php include 'conexao.php'; ?>

<main>
  <h2>👤 Clientes</h2>

  <?php
  // Buscar clientes
  $sql = "SELECT * FROM clientes";
  $stmt = $conn->query($sql);
  $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
  ?>

  <table>
    <tr><th>Nome</th><th>Endereço</th><th>Telefone</th><th>Ações</th></tr>
    <?php foreach ($clientes as $c): ?>
      <tr>
        <td><?= htmlspecialchars($c['nome']) ?></td>
        <td><?= htmlspecialchars($c['endereco']) ?></td>
        <td><?= htmlspecialchars($c['telefone']) ?></td>
        <td>
          <a href="clientes.php?editar=<?= $c['id_cliente'] ?>">Editar</a>
          <a href="clientes.php?excluir=<?= $c['id_cliente'] ?>" onclick="return confirm('Excluir cliente?')">Excluir</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>

  <h3>Cadastrar Cliente</h3>
  <form method="post" action="">
    <input type="text" name="nome" placeholder="Nome" required>
    <input type="text" name="endereco" placeholder="Endereço" required>
    <input type="text" name="telefone" placeholder="Telefone">
    <button type="submit" name="salvar">Salvar</button>
  </form>

  <?php
  // Salvar cliente
  if (isset($_POST['salvar'])) {
      $sql = "INSERT INTO clientes (nome, endereco, telefone) VALUES (?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->execute([$_POST['nome'], $_POST['endereco'], $_POST['telefone']]);
      header("Location: clientes.php");
      exit;
  }

  // Excluir cliente
  if (isset($_GET['excluir'])) {
      $sql = "DELETE FROM clientes WHERE id_cliente = ?";
      $stmt = $conn->prepare($sql);
      $stmt->execute([$_GET['excluir']]);
      header("Location: clientes.php");
      exit;
  }
  ?>
</main>

<?php include 'footer.php'; ?>
