<?php
require_once 'check_login.php';
require_once 'config.php';

$student_id = $_SESSION['user_id'];
$search_term = "";

$sql = "SELECT subject, grade, date FROM grades WHERE student_id = :student_id";

if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $search_term = $_GET['search'];

    // DIFESA (Audit/Logging):
    // Rileva pattern sospetti nella barra di ricerca e li registra nel log.
    $suspicious_patterns = [
        "/UNION\s+SELECT/i",
        "/;\s*(UPDATE|DELETE|DROP|INSERT)/i",
        "/SLEEP\s*\(/i",
        "/EXTRACTVALUE/i",
        "/information_schema/i",
        "/ORDER\s+BY\s+\d+/i",
        "/--\s*$/",
    ];
    foreach ($suspicious_patterns as $pattern) {
        if (preg_match($pattern, $search_term)) {
            $log_entry = date('[Y-m-d H:i:s]') . " ⚠️  TENTATIVO SQLi RILEVATO (ricerca)" .
                         " | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') .
                         " | Search: " . substr($search_term, 0, 200) . "\n";
            file_put_contents('/var/www/html/audit.log', $log_entry, FILE_APPEND);
            break;
        }
    }
    
    // DIFESA (Prepared Statements):
    // Nell'app vulnerabile usavamo $sql .= " AND subject LIKE '%$search_term%'"; 
    // permettendo l'iniezione diretta di UNION SELECT o UPDATE (Piggybacked queries).
    // Qui usiamo un placeholder :search_term. Il database sanitizza automaticamente
    // il contenuto e non esegue mai payload accodati (niente UNION, niente UPDATE/DELETE).
    $sql .= " AND subject LIKE :search_term";
}

$sql .= " ORDER BY date DESC";

try {
    $stmt = $db_connection->prepare($sql);
    
    // Bind del parametro base (l'ID studente preso in modo sicuro dalla sessione)
    $stmt->bindValue(':student_id', $student_id, PDO::PARAM_INT);
    
    // Bind del parametro di ricerca se presente
    if (isset($search_term) && $search_term !== '') {
        $search_like = "%" . $search_term . "%";
        $stmt->bindValue(':search_term', $search_like, PDO::PARAM_STR);
    }

    $stmt->execute();
    $voti = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Stampare l'errore a schermo è utile per le Error-Based SQL Injection
    $db_error = "Errore SQL: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Elettronico - Voti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background: #f3f4f6;
            font-family: "Segoe UI", sans-serif;
            min-height:100vh;
        }
        .container{
            min-height:100vh;
            padding: 20px;
        }
        .dashboard-card{
            width:100%;
            max-width:800px; /* Allargata per ospitare comodamente la tabella */
            border:none;
            border-radius:14px;
            box-shadow:0 10px 35px rgba(0,0,0,.12);
            overflow:hidden;
            background: white;
        }
        .top-bar{
            background:#1976d2;
            height:6px;
        }
        .btn-custom {
            background-color: #1976d2;
            color: white;
            border: none;
        }
        .btn-custom:hover {
            background-color: #115293;
            color: white;
        }
        .badge-voto {
            font-size: 1rem;
            padding: 8px 12px;
        }
    </style>
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center">
        <div class="dashboard-card p-4">
            <div class="top-bar mb-4"></div>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="m-0 text-primary">I tuoi Voti</h3>
                <a href="logout.php" class="btn btn-outline-secondary btn-sm">Esci</a>
            </div>

            <?php if (isset($db_error)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($db_error); ?>
                </div>
            <?php endif; ?>

            <form method="GET" action="index.php" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cerca per materia (es. Matematica)..." value="<?php echo htmlspecialchars($search_term); ?>">
                    <button class="btn btn-custom" type="submit">Cerca</button>
                    <?php if (!empty($search_term)): ?>
                        <a href="index.php" class="btn btn-outline-primary">Annulla</a>
                    <?php endif; ?>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Materia</th>
                            <th>Data</th>
                            <th class="text-center">Voto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($voti) && count($voti) > 0): ?>
                            <?php foreach ($voti as $riga): ?>
                                <tr>
                                    <td class="fw-bold"><?php echo htmlspecialchars($riga['subject'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($riga['date'] ?? ''); ?></td>
                                    <td class="text-center">
                                        <?php 
                                            // Assegna il colore del badge in base alla sufficienza
                                            $voto = floatval($riga['grade'] ?? 0);
                                            $badge_class = ($voto >= 6) ? 'bg-success' : 'bg-danger';
                                        ?>
                                        <span class="badge <?php echo $badge_class; ?> badge-voto">
                                            <?php echo htmlspecialchars($riga['grade'] ?? ''); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    Nessun voto trovato.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            


        </div>
    </div>
</body>
</html>