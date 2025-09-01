<?php include 'conexao.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>🐾 PetShop - Sistema</title>
  <style>
    /* Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background: #f8f9fa;
      color: #333;
    }

    /* Topo (navbar) */
    header {
      background: #00838f; /* turquesa Cobasi */
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header h1 {
      color: #fff;
      font-size: 22px;
    }

    header nav a {
      color: #fff;
      text-decoration: none;
      margin: 0 12px;
      font-weight: 500;
      transition: 0.3s;
    }

    header nav a:hover {
      color: #ffd54f; /* amarelo destaque */
    }

    /* Container principal */
    main {
      max-width: 1000px;
      margin: 30px auto;
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      text-align: center;
    }

    main h2 {
      margin-bottom: 15px;
      color: #00838f;
    }

    footer {
      text-align: center;
      margin-top: 30px;
      padding: 15px;
      background: #00838f;
      color: #fff;
    }

    .carousel {
      position: relative;
      max-width: 500px;
      margin: 20px auto;
      overflow: hidden;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .carousel img {
      width: 100%;
      display: none;
      border-radius: 12px;
    }

    .carousel img.active {
      display: block;
      animation: fade 1s;
    }

    @keyframes fade {
      from { opacity: 0.4; }
      to { opacity: 1; }
    }

    .carousel-controls {
      position: absolute;
      top: 50%;
      width: 100%;
      display: flex;
      justify-content: space-between;
      transform: translateY(-50%);
      padding: 0 10px;
    }

    .carousel-controls span {
      background: rgba(0,0,0,0.5);
      color: #fff;
      padding: 10px;
      cursor: pointer;
      border-radius: 50%;
      font-size: 18px;
      transition: 0.3s;
    }

    .carousel-controls span:hover {
      background: rgba(0,0,0,0.8);
    }
  </style>
</head>
<body>
  <header>
    <h1>🐾 Sistema de Gestão - PetShop</h1>
    <nav>
      <a href="clientes.php">👤 Clientes</a>
      <a href="animais.php">🐶 Animais</a>
      <a href="agendamento.php">📅 Agendamentos</a>
    </nav>
  </header>

  <!-- Carrossel -->
  <div class="carousel">
    <img src="cachorro fofinho.webp" class="active" alt="Cachorro feliz">
    <img src="gato.png" alt="Gato fofo">
    <img src="petshop logo.png" alt="PetShop ambiente">
    <div class="carousel-controls">
      <span id="prev">&#10094;</span>
      <span id="next">&#10095;</span>
    </div>
  </div>

  <main>
    <h2>Bem-vindo ao sistema do PetShop!</h2>
    <p>Use o menu acima para gerenciar clientes, animais e agendamentos.</p>
  </main>

  <footer>
    <p>PetShop &copy; <?= date("Y") ?> - Sistema de Gestão</p>
  </footer>

  <script>
    // JS do carrossel
    const images = document.querySelectorAll(".carousel img");
    const prev = document.getElementById("prev");
    const next = document.getElementById("next");
    let index = 0;

    function showImage(i) {
      images.forEach(img => img.classList.remove("active"));
      images[i].classList.add("active");
    }

    prev.addEventListener("click", () => {
      index = (index > 0) ? index - 1 : images.length - 1;
      showImage(index);
    });

    next.addEventListener("click", () => {
      index = (index < images.length - 1) ? index + 1 : 0;
      showImage(index);
    });

    // Troca automática a cada 5s
    setInterval(() => {
      index = (index < images.length - 1) ? index + 1 : 0;
      showImage(index);
    }, 5000);
  </script>
</body>
</html>
