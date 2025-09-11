<?php
session_start();
require 'config.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $error_message = '';
    if (empty($username) || empty($password)) {
        $error_message = "Kullanıcı adı ve şifre boş bırakılamaz.";
    } else {
        $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $password_hash);
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            if (password_verify($password, $password_hash)) {
                $_SESSION['loggedin'] = true;
                $_SESSION['id'] = $id;
                $_SESSION['username'] = $username;
                header("location: index.php");
                exit;
            } else {
                $error_message = "Yanlış şifre.";
            }
        } else {
            $error_message = "Böyle bir kullanıcı bulunamadı.";
        }
        $stmt->close();
    }
    $conn->close();
}
include 'partials/header.php'; 
?>
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h3 class="card-title text-center mb-4">Giriş Yap</h3>
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <form action="login.php" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Kullanıcı Adı</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Şifre</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Giriş Yap</button>
                        </div>
                    </form>
                    <p class="text-center mt-3">Hesabın yok mu? <a href="register.php">Kayıt Ol</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https:
</body>
</html>