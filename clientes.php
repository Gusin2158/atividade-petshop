<?php include 'header.php'; ?>
<?php include 'conexao.php'; ?>

<main>
  <h2>👤 Clientes</h2>

  <?php
  
  $sql = "SELECT * FROM clientes";
  $stmt = $conn->query($sql);
  $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

  
  $editando = false;
  $cliente = ['id_cliente' => '', 'nome' => '', 'endereco' => '', 'telefone' => ''];


  if (isset($_GET['editar'])) {
      $sql = "SELECT * FROM clientes WHERE id_cliente = ?";
      $stmt = $conn->prepare($sql);
      $stmt->execute([$_GET['editar']]);
      $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
      $editando = true;
  }

  
  if (isset($_POST['salvar'])) {
      $sql = "INSERT INTO clientes (nome, endereco, telefone) VALUES (?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->execute([$_POST['nome'], $_POST['endereco'], $_POST['telefone']]);
      header("Location: clientes.php");
      exit;
  }
  if (isset($_POST['atualizar'])) {
      $sql = "UPDATE clientes SET nome=?, endereco=?, telefone=? WHERE id_cliente=?";
      $stmt = $conn->prepare($sql);
      $stmt->execute([$_POST['nome'], $_POST['endereco'], $_POST['telefone'], $_POST['id_cliente']]);
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

  <table border="1" cellpadding="5">
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

  <h3><?= $editando ? "Editar Cliente" : "Cadastrar Cliente" ?></h3>
  <form method="post" action="">
    <input type="hidden" name="id_cliente" value="<?= $cliente['id_cliente'] ?>">
    <input type="text" name="nome" placeholder="Nome" value="<?= $cliente['nome'] ?>" required>
    <input type="text" name="endereco" placeholder="Endereço" value="<?= $cliente['endereco'] ?>" required>
    <input type="text" name="telefone" placeholder="Telefone" value="<?= $cliente['telefone'] ?>">
    
    <?php if ($editando): ?>
      <button type="submit" name="atualizar">Atualizar</button>
    <?php else: ?>
      <button type="submit" name="salvar">Salvar</button>
    <?php endif; ?>
  </form>
</main>

<?php include 'footer.php'; ?>
