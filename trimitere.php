<?php
session_start();

if (!isset($_SESSION['logat']) || $_SESSION['logat'] !== true || $_SESSION['tip'] != 'medic') {
    header('Location: autentificare.php');
    exit;
}

$data_curenta = date('Y-m-d');
$analiza = $_POST['analiza'] ?? '';
$laborator = $_POST['laborator'] ?? '';
$trimis = 0;
$eroare = '';

if ($analiza) {
    $servername = "localhost";
    $username = "adragans_proiect";
    $password = "Parola.123";
    $dbname = "adragans_proiect";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Conexiunea a eșuat: " . $conn->connect_error);
    }

    $sql = "SELECT Nume_analiza FROM Analize WHERE Nr_crt=" . $analiza . ";"; 
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $nume_analiza = $row['Nume_analiza'];

        $sql = "SELECT Nr_crt FROM Conturi WHERE user='" . $_SESSION['user'] . "';";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $medic_id = $row['Nr_crt'];

        $sql = "SELECT Nr_crt, CONCAT(Nume, ' ', Prenume) AS Nume_Pacient FROM Conturi WHERE user='" . $_SESSION['optiune'] . "';";
        unset($_SESSION['optiune']);
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $pacient_id = $row['Nr_crt'];
        $nume_pacient = $row['Nume_Pacient'];

        if ($laborator) {
            $sql = "SELECT * FROM Laboratoare WHERE cod_laborator='" . $laborator . "';";
            $result = $conn->query($sql);
            if ($result->num_rows == 1) {
                $sql = "INSERT INTO Rezultate (Nr_analiza, Nr_pacient, Data_recoltarii, Medic_recoltare, Laborator)
                        VALUES(" . $analiza . "," . $pacient_id . ",'" . $data_curenta . "'," . $medic_id . "," . $laborator . ");";
                $conn->query($sql);
                $trimis = 1;
            } else {
                $eroare = "Cod de laborator invalid!";
            }
        } else {
            $sql = "INSERT INTO Rezultate (Nr_analiza, Nr_pacient, Data_recoltarii, Medic_recoltare, Laborator)
                    VALUES(" . $analiza . "," . $pacient_id . ",'" . $data_curenta . "'," . $medic_id . ",1010);";
            $conn->query($sql);
            $trimis = 1;
            $laborator='1010';
        }
    } else {
        $eroare = "Nu exista aceasta analiza. Consulta pagina de analize pentru a gasi codul corect.";
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Trimitere analize</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #fff5f5;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            background-color: #ffe6e6;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 6px 12px rgba(179,0,0,0.2);
        }

        h2 {
            color: #b30000;
            text-align: center;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #b30000;
        }

        button {
            margin-top: 20px;
            padding: 10px 18px;
            background-color: #b30000;
            color: #ffd6d6;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        button:hover {
            background-color: #c84747;
            color: white;
            transform: translateY(-1px);
        }

        button:active {
            transform: translateY(0);
        }

        p {
            background-color: #ffcccc;
            color: #b30000;
            padding: 12px;
            border-radius: 6px;
            font-weight: bold;
        }

        a.btn {
            display: inline-block;
            padding: 10px 18px;
            background-color: #b30000;
            color: #ffd6d6;
            text-decoration: none;
            font-weight: bold;
            border-radius: 6px;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        a.btn:hover {
            background-color: #c84747;
            color: white;
            transform: translateY(-1px);
        }

        a.btn:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>
<div class="container">
    <?php if ($eroare): ?>
        <p>Eroare: <?= $eroare ?></p>
    <?php endif; ?>

    <?php if ($trimis == 0): ?>
        <h2>Analize noi</h2>
        <form method="POST" autocomplete="off">
            <label for="analiza">Cod analiza:</label>
            <input type="text" id="analiza" name="analiza" required pattern="\d+" title="Introduceți doar cifre">

            <label for="laborator">Cod laborator:</label>
            <input type="text" id="laborator" name="laborator" pattern="\d+" title="Introduceți doar cifre">

            <button type="submit">Trimite la laborator</button>
        </form>
    <?php endif; ?>

    <?php if ($trimis == 1): ?>
        <p>S-a trimis proba pacientului <?= $nume_pacient ?> la laboratorul <?= $laborator ?> pentru analiza <?= $nume_analiza ?>.</p>
        <a href="secret.php" class="btn">Continua</a>
    <?php endif; ?>
</div>
</body>
</html>
