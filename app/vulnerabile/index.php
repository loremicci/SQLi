<?php

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Elettronico - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background: #f3f4f6;
            font-family: "Segoe UI", sans-serif;
            min-height:100vh;
        }
        .container{
            min-height:100vh;
        }
        .welcome-card{
            width:100%;
            max-width:500px;
            border:none;
            border-radius:14px;
            box-shadow:0 10px 35px rgba(0,0,0,.12);
            overflow:hidden;
        }
        .top-bar{
            background:#d32f2f;
            height:6px;
        }
        .logo-circle{
            width:85px;
            height:85px;
            margin:auto;
            border-radius:50%;
            background:#d32f2f;
            color:white;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:32px;
            font-weight:bold;
            box-shadow:0 6px 15px rgba(211,47,47,.25);
        }
    </style>
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center">
        <div class="welcome-card p-4">
            <div class="top-bar"></div>
            <div class="logo-circle mt-4 mb-3">RE</div>
            <h3 class="text-center mb-4">Benvenuto nel Registro Elettronico</h3>
            <p class="text-center mb-4">
                Accedi per visualizzare i tuoi voti e le tue assenze.
            </p>
            <p class="text-center mb-4">
                 Se sei un insegnante, accedi con le tue credenziali per gestire i voti e le assenze dei tuoi studenti.
            </p>
            <p class="text-center mb-4">
                    Se sei uno studente, accedi con le tue credenziali per visualizzare i tuoi voti e le tue assenze.
            </p>
            <p class="text-center mb-4">
                Se sei un genitore, accedi con le tue credenziali per visualizzare i voti e le assenze dei tuoi figli.
            </p>
        </div>
    </div>
</body>
</html>