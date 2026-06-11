<?php 
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM students WHERE username = '$username' AND password = '$password'";
    
    try {
        $stmt = $db_connection->query($sql);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['logged_in'] = true;
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['login_error'] = true;
            header("Location: login.php");
            exit();
        }
    } catch (PDOException $e) {
        die("Errore nella query: " . $e->getMessage());
    }
}

?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Registro Elettronico - Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

    :root{
        --main-red:#d32f2f;
        --main-red-dark:#b71c1c;
        --main-red-light:#ffcdd2;
    }

    body{
        background: #f3f4f6;
        font-family: "Segoe UI", sans-serif;
        min-height:100vh;
    }

    .login-wrapper{
        min-height:100vh;
    }

    .login-card{
        width:100%;
        max-width:420px;
        border:none;
        border-radius:14px;
        box-shadow:0 10px 35px rgba(0,0,0,.12);
        overflow:hidden;
    }

    .top-bar{
        background:var(--main-red);
        height:6px;
    }

    .logo-circle{
        width:85px;
        height:85px;
        margin:auto;
        border-radius:50%;
        background:var(--main-red);
        color:white;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:32px;
        font-weight:bold;
        box-shadow:0 6px 15px rgba(211,47,47,.25);
    }

    .portal-title{
        color:var(--main-red);
        font-weight:700;
    }

    .form-control{
        height:46px;
        border-radius:8px;
    }

    .form-control:focus{
        border-color:var(--main-red);
        box-shadow:0 0 0 .2rem rgba(211,47,47,.15);
    }

    .btn-login{
        background:var(--main-red);
        border:none;
        height:46px;
        border-radius:8px;
        font-weight:600;
    }

    .btn-login:hover{
        background:var(--main-red-dark);
    }

    .school-info{
        text-align:center;
        color:#6c757d;
        font-size:0.85rem;
        margin-top:15px;
    }

    .version{
        text-align:center;
        color:#adb5bd;
        font-size:0.75rem;
        margin-bottom:15px;
    }

</style>
</head>

<body>

<div class="container login-wrapper d-flex align-items-center justify-content-center">

    <div class="card login-card">

        <div class="top-bar"></div>

        <div class="card-body p-4 text-center">

            <!-- LOGO -->
            <div class="logo-circle mb-3">
                RE
            </div>

            <h5 class="portal-title">
                Registro Elettronico
            </h5>

            <p class="text-muted mb-4">
                Accesso studenti
            </p>

            <form action="login.php" method="POST" class="text-start">

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control"
                           placeholder="Inserisci username" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control"
                           placeholder="Inserisci la password" required>
                </div>
                <?php if (isset($_SESSION['login_error']) && $_SESSION['login_error'] === true): ?>
                    <p class="text-danger my-3">
                        Username o password errati
                    </p>
                    <?php unset($_SESSION['login_error']); ?>
                <?php endif; ?>
                <button type="submit" class="btn btn-login w-100 text-white">
                    ACCEDI
                </button>

            </form>

        </div>

    </div>

</div>

</body>
</html>
