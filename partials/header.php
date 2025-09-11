<?php
session_start(); 
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DGS Tercih Robotu</title>
    <link href="https:
    <link rel="stylesheet" href="https:
    <link href="https:
    <link href="https:
    <link rel="stylesheet" href="style.css">
    <script>
      (function() {
        const theme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', theme);
      })();
    </script>
</head>
<body class="bg-body-tertiary">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
      <div class="container">
        <a class="navbar-brand" href="index.php">Tercih Robotu</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">DGS (Puanlı)</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="index.php?tur=hepsi">Tümü</a></li>
                <li><a class="dropdown-item" href="index.php?tur=bilgisayar">Bilgisayar Müh.</a></li>
                <li><a class="dropdown-item" href="index.php?tur=yazilim">Yazılım Müh.</a></li>
              </ul>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Yeni Kontenjanlar (Yeni)</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="yeni_kontenjanlar.php?tur=hepsi">Tümü (Yeni)</a></li>
                <li><a class="dropdown-item" href="yeni_kontenjanlar.php?tur=bilgisayar">Bilgisayar Müh. (Yeni)</a></li>
                <li><a class="dropdown-item" href="yeni_kontenjanlar.php?tur=yazilim">Yazılım Müh. (Yeni)</a></li>
              </ul>
            </li>
          </ul>
          <ul class="navbar-nav ms-auto align-items-center">
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <li class="nav-item"><a class="nav-link" href="tercih-listeleri.php">Listelerim</a></li>
                <li class="nav-item"><a class="nav-link" href="listelerim2.php">Listelerim 2 (Yeni)</a></li>
                <li class="nav-item"><a class="nav-link" href="notlarim.php">Notlarım</a></li>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="logout.php">Çıkış Yap</a></li>
                  </ul>
                </li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="login.php">Giriş Yap</a></li>
                <li class="nav-item"><a class="nav-link" href="register.php">Kayıt Ol</a></li>
            <?php endif; ?>
            <li class="nav-item ms-2">
                <button class="btn btn-outline-light btn-sm" id="theme-toggle-btn"><i class="bi bi-moon-stars-fill"></i></button>
            </li>
          </ul>
        </div>
      </div>
    </nav>