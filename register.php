<?php
require 'config.php';
$success_message = '';
$error_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    if (empty($username) || empty($password)) {
        $error_message = "Kullanıcı adı ve şifre boş bırakılamaz.";
    } elseif (strlen($password) < 6) {
        $error_message = "Şifre en az 6 karakter olmalıdır.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error_message = "Bu kullanıcı adı zaten alınmış. Lütfen başka bir tane deneyin.";
        } else {
            $stmt_insert = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
            $stmt_insert->bind_param("ss", $username, $password_hash);
            if ($stmt_insert->execute()) {
                $success_message = "Kayıt başarılı! Şimdi giriş yapabilirsiniz.";
            } else {
                $error_message = "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
            }
            $stmt_insert->close();
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
                    <h3 class="card-title text-center mb-4">Kayıt Ol</h3>
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                        <div class="d-grid"><a href="login.php" class="btn btn-primary">Giriş Yap</a></div>
                    <?php else: ?>
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php endif; ?>
                        <form action="register.php" method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">Kullanıcı Adı</label>
                                <input type="text" name="username" id="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Şifre (En az 6 karakter)</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">Kayıt Ol</button>
                            </div>
                        </form>
                    <?php endif; ?>
                    <p class="text-center mt-3">Zaten bir hesabın var mı? <a href="login.php">Giriş Yap</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https:
</body>
</html>