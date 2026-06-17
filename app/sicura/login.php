<?php 
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // DIFESA (Audit/Logging): 
    // Un sistema di logging rileva pattern sospetti nell'input (apici, UNION, OR 1=1, ecc.)
    // e li registra su un file di log. Questo permette al difensore di monitorare
    // i tentativi di attacco in tempo reale e intervenire prontamente.
    $suspicious_patterns = [
        "/' OR /i",            // Tautologia
        "/UNION\s+SELECT/i",   // Union-based injection
        "/;\s*(UPDATE|DELETE|DROP|INSERT)/i",  // Piggybacked queries
        "/SLEEP\s*\(/i",       // Time-based blind injection
        "/EXTRACTVALUE/i",     // Error-based injection
        "/information_schema/i", // Information gathering
        "/--\s*$/",            // End-of-line comment
    ];

    $is_suspicious = false;
    foreach ($suspicious_patterns as $pattern) {
        if (preg_match($pattern, $username) || preg_match($pattern, $password)) {
            $is_suspicious = true;
            break;
        }
    }

    if ($is_suspicious) {
        $log_entry = date('[Y-m-d H:i:s]') . " ⚠️  TENTATIVO SQLi RILEVATO" .
                     " | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') .
                     " | Username: " . substr($username, 0, 100) .
                     " | Password: " . substr($password, 0, 100) . "\n";
        file_put_contents('/var/www/html/audit.log', $log_entry, FILE_APPEND);
    }

    // DIFESA (Prepared Statements): 
    // Invece di concatenare le stringhe utente direttamente nella query (come nell'app vulnerabile: $sql = "... WHERE username = '$username'"),
    // utilizziamo dei "placeholder" (es. :username). Questo impedisce l'attacco di Tautologia (es. ' OR 1=1 --), 
    // perché il database tratterà l'input rigorosamente come stringa di testo e non come codice eseguibile SQL.
    // In questa versione sicura estraiamo l'hash della password corrispondente all'utente
    $sql = "SELECT * FROM users WHERE username = :username";
    
    try {
        $stmt = $db_connection->prepare($sql);
        // "Leghiamo" la variabile al placeholder in modo sicuro
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifichiamo se l'utente esiste e se la password inserita corrisponde all'hash nel database
        if ($user && password_verify($password, $user['password'])) {
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
        --main-blue:#1976d2;
        --main-blue-dark:#115293;
        --main-blue-light:#bbdefb;
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
        background:var(--main-blue);
        height:6px;
    }

    .logo-circle{
        width:85px;
        height:85px;
        margin:auto;
        border-radius:50%;
        background:var(--main-blue);
        color:white;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:32px;
        font-weight:bold;
        box-shadow:0 6px 15px rgba(25, 118, 210, .25);
    }

    .portal-title{
        color:var(--main-blue);
        font-weight:700;
    }

    .form-control{
        height:46px;
        border-radius:8px;
    }

    .form-control:focus{
        border-color:var(--main-blue);
        box-shadow:0 0 0 .2rem rgba(25, 118, 210, .15);
    }

    .btn-login{
        background:var(--main-blue);
        border:none;
        height:46px;
        border-radius:8px;
        font-weight:600;
    }

    .btn-login:hover{
        background:var(--main-blue-dark);
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
