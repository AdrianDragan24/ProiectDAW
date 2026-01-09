<?php
session_start();

$cod_introdus = $_POST['cod'] ?? '';

if ($cod_introdus == $_SESSION['verif_code']) {

    $servername = "localhost";
    $username = "adragans_proiect";
    $password = "Parola.123";
    $dbname = "adragans_proiect";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Conexiunea a eșuat: " . $conn->connect_error);
    }

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("
            INSERT INTO Conturi (Nume, Prenume, CNP, Email, User, Parola, Tip)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        if (!$stmt) {
            throw new Exception("Eroare prepare Conturi: " . $conn->error);
        }

        $stmt->bind_param(
            "sssssss",
            $_SESSION['pending_user']['nume'],
            $_SESSION['pending_user']['prenume'],
            $_SESSION['pending_user']['cnp'],
            $_SESSION['pending_user']['mail'],
            $_SESSION['pending_user']['user'],
            $_SESSION['pending_user']['parola'],
            $_SESSION['pending_user']['tip']
        );

        if (!$stmt->execute()) {
            throw new Exception("Eroare execute Conturi: " . $stmt->error);
        }

        $nr_crt = $conn->insert_id;
        $stmt->close();

        if ($_SESSION['pending_user']['tip'] === 'medic') {
            $stmt2 = $conn->prepare("
                INSERT INTO Medici (nr_crt, cod_parafa, specializare)
                VALUES (?, ?, ?)
            ");
            if (!$stmt2) {
                throw new Exception("Eroare prepare Medici: " . $conn->error);
            }

            $stmt2->bind_param(
                "iss",
                $nr_crt,
                $_SESSION['pending_user']['parafa'],
                $_SESSION['pending_user']['specializare']
            );

            if (!$stmt2->execute()) {
                throw new Exception("Eroare execute Medici: " . $stmt2->error);
            }
            $stmt2->close();
        }

        if ($_SESSION['pending_user']['tip'] === 'laborator') {
            $stmt3 = $conn->prepare("
                INSERT INTO Laboratoare (nr_crt, cod_laborator)
                VALUES (?, ?)
            ");
            if (!$stmt3) {
                throw new Exception("Eroare prepare Laboratoare: " . $conn->error);
            }

            $stmt3->bind_param(
                "is",
                $nr_crt,
                $_SESSION['pending_user']['cod_laborator']
            );

            if (!$stmt3->execute()) {
                throw new Exception("Eroare execute Laboratoare: " . $stmt3->error);
            }
            $stmt3->close();
        }

        $conn->commit();
        unset($_SESSION['verif_code']);
        unset($_SESSION['pending_user']);

    } catch (Exception $e) {
        $conn->rollback();
        echo "Eroare la creare cont: " . $e->getMessage();
    }

    $conn->close();

    $mesaj = "Contul a fost creat cu succes!";
} else {
    $mesaj = "Cod invalid! Te rugăm să încerci din nou.";
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>Creare cont - Ciuperca lui Fertig</title>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #fff2f2;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
}
.container {
    background-color: #fff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    width: 400px;
    text-align: center;
}
h2, p {
    color: #a83232;
}
a.button {
    display: inline-block;
    text-decoration: none;
    padding: 12px 18px;
    background-color: #a83232;
    color: white;
    border-radius: 6px;
    margin-top: 20px;
    transition: background-color 0.3s ease;
}
a.button:hover {
    background-color: #c84747;
}
</style>
</head>
<body>
<div class="container">
<p><?= htmlspecialchars($mesaj) ?></p>
<a href="https://adragan.daw.ssmr.ro/proiect/autentificare.php" class="button">Întoarce-te în pagina de autentificare</a>
</div>
</body>
</html>