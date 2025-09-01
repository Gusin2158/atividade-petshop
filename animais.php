<?php
include 'conexao.php';
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$erro = '';
$ok = '';
$animal = null;

// --------- POST: SALVAR NOVO ----------
if (isset($_POST['salvar'])) {
    try {
        $nome = trim($_POST['nome'] ?? '');
        $raca = trim($_POST['raca'] ?? '');
        $idCliente = $_POST['id_cliente'] ?? '';

        // validações básicas
        if ($nome === '' || $raca === '' || $idCliente === '') {
            throw new Exception('Preencha todos os campos.');
        }

        // valida se o cliente existe
        $chk = $conn->prepare("SELECT 1 FROM clientes WHERE id_cliente = ?");
        $chk->execute([$idCliente]);
        if (!$chk->fetchColumn()) {
            throw new Exception('Cliente selecionado não existe. Cadastre o cliente primeiro.');
        }

        $sql = "INSERT INTO animais (nome, raca, id_cliente) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nome, $raca, $idCliente]);

        header("Location: animais.php?ok=1");
        exit;
    } catch (Throwable $e) {
        $erro = $e->getMessage();
    }
}

// --------- POST: ATUALIZAR EXISTENTE ----------
if (isset($_POST['atualizar'])) {
    try {
        $idAnimal = $_POST['id_animal'] ?? '';
        $nome = trim($_POST['nome'] ?? '');
        $raca = trim($_POST['raca'] ?? '');
        $idCliente = $_POST['id_cliente'] ?? '';

        if ($idAnimal === '' || $nome === '' || $raca === '' || $idCliente === '') {
            throw new Exception('Preencha todos os campos.');
        }

        // valida se o animal existe
        $chkA = $conn->prepare("SELECT 1 FROM animais WHERE id_animal = ?");
        $chkA->execute([$idAnimal]);
        if (!$chkA->fetchColumn()) {
            throw new Exception('Animal não encontrado.');
        }

        // valida se o cliente existe
        $chkC = $conn->prepare("SELECT 1 FROM clientes WHERE id_cliente = ?");
        $chkC->execute([$idCliente]);
        if (!$chkC->fetchColumn()) {
            throw new Exception('Cliente selecionado não existe.');
        }

        $sql = "UPDATE animais SET nome = ?, raca = ?, id_cliente = ? WHERE id_animal = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nome, $raca, $idCliente, $idAnimal]);

        header("Location: animais.php?ok=2");
        exit;
    } catch (Throwable $e) {
        $erro = $e->getMessage();
    }
}

// --------- GET: EXCLUIR ----------
if (isset($_GET['excluir'])) {
    try {
        $sql = "DELETE FROM animais WHERE id_animal = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$_GET['excluir']]);
        header("Location: animais.php?ok=3");
        exit;
    } catch (Throwable $e) {
        $erro = $e->getMessage();
    }
}

// --------- GET: BUSCAR ANIMAL PARA EDIÇÃO ----------
if (isset($_GET['editar'])) {
    $sql = "SELECT * FROM animais WHERE id_animal = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_GET['editar']]);
    $animal = $stmt->fetch(PDO::FETCH_ASSOC);
}

// --------- CONSULTAS PARA LISTAGEM / SELECT ----------
$sql = "SELECT a.id_animal, a.nome AS nome_animal, a.raca, c.nome AS nome_cliente, c.id_cliente
        FROM animais a
        JOIN clientes c ON a.id_cliente = c.id_cliente";
$stmt = $conn->query($sql);
$animais = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT id_cliente, nome FROM clientes ORDER BY nome";
$stmt = $conn->query($sql);
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// mensagens por querystring
if (isset($_GET['ok'])) {
    $okCodes = ['1' => 'Animal cadastrado com sucesso.',
                '2' => 'Animal atualizado com sucesso.',
                '3' => 'Animal excluído com sucesso.'];
    $ok = $okCodes[$_GET['ok']] ?? '';
}

include 'header.php';
?>

<main>
  <h2>🐶 Animais</h2>

  <?php if ($erro): ?>
    <div style="background:#ffd7d7;color:#7a0000;padding:10px;border-radius:8px;margin:10px 0;">
      ⚠️ <?= htmlspecialchars($erro) ?>
    </div>
  <?php endif; ?>

  <?php if ($ok): ?>
    <div style="background:#d7ffe2;color:#005c2e;padding:10px;border-radius:8px;margin:10px 0;">
      ✅ <?= htmlspecialchars($ok) ?>
    </div>
  <?php endif; ?>

  <!-- LISTAGEM -->
  <table border="1" cellpadding="5" cellspacing="0" width="100%">
    <tr><th>Nome</th><th>Raça</th><th>Dono</th><th>Ações</th></tr>
    <?php foreach ($animais as $a): ?>
      <tr>
        <td><?= htmlspecialchars($a['nome_animal']) ?></td>
        <td><?= htmlspecialchars($a['raca']) ?></td>
        <td><?= htmlspecialchars($a['nome_cliente']) ?></td>
        <td>
          <a href="animais.php?editar=<?= (int)$a['id_animal'] ?>">Editar</a>
          <a href="animais.php?excluir=<?= (int)$a['id_animal'] ?>"
             onclick="return confirm('Excluir este animal?')">Excluir</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>

  <?php if ($animal): ?>
    <!-- FORMULÁRIO DE EDIÇÃO -->
    <h3>Editar Animal</h3>
    <form method="post" action="">
      <input type="hidden" name="id_animal" value="<?= (int)$animal['id_animal'] ?>">
      <input type="text" name="nome" value="<?= htmlspecialchars($animal['nome']) ?>" required>
      <input type="text" name="raca" value="<?= htmlspecialchars($animal['raca']) ?>" required>

      <select name="id_cliente" required>
        <option value="">Selecione o dono</option>
        <?php foreach ($clientes as $c): ?>
          <option value="<?= (int)$c['id_cliente'] ?>"
            <?= ($animal['id_cliente'] == $c['id_cliente']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['nome']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <button type="submit" name="atualizar">Atualizar</button>
    </form>

  <?php else: ?>
    <!-- FORMULÁRIO DE CADASTRO -->
    <h3>Cadastrar Animal</h3>

    <?php if (count($clientes) === 0): ?>
      <p>⚠️ Nenhum cliente cadastrado. <a href="clientes.php">Cadastre um cliente primeiro</a>.</p>
    <?php else: ?>
      <form method="post" action="">
        <input type="text" name="nome" placeholder="Nome do animal" required>
        <input type="text" name="raca" placeholder="Raça" required>

        <select name="id_cliente" required>
          <option value="">Selecione o dono</option>
          <?php foreach ($clientes as $c): ?>
            <option value="<?= (int)$c['id_cliente'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
          <?php endforeach; ?>
        </select>

        <button type="submit" name="salvar">Salvar</button>
      </form>
    <?php endif; ?>
  <?php endif; ?>
</main>

<?php include 'footer.php'; ?>