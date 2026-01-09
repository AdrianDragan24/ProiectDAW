<?php
session_start();

if (!isset($_SESSION['logat']) || $_SESSION['logat'] !== true) {
    header('Location: autentificare.php');
    exit;
}

$pas = $_POST['pas'] ?? 0;
$parola = $_POST['parola'] ?? '';

if ($pas == 2) {
    $servername = "localhost";
    $username = "adragans_proiect";
    $password = "Parola.123";
    $dbname = "adragans_proiect";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Conexiunea a eșuat: " . $conn->connect_error);
    }

    if($_SESSION['tip']=='pacient') {
        $conn->query("DELETE FROM Pacient_Medic WHERE Pacient=".$_SESSION['id']);
        $conn->query("DELETE FROM Rezultate WHERE Nr_pacient=".$_SESSION['id']);
    }
    if($_SESSION['tip']=='medic') {
        $conn->query("DELETE FROM Pacient_Medic WHERE Medic=".$_SESSION['id']);
        $conn->query("DELETE FROM Medici WHERE nr_crt=".$_SESSION['id']);
    }
    if($_SESSION['tip']=='laborator') {
        $conn->query("DELETE FROM Laboratoare WHERE nr_crt=".$_SESSION['id']);
    }
    $conn->query("DELETE FROM Conturi WHERE Nr_crt=".$_SESSION['id']);
    header("Location: logout.php");
    exit;
}

if ($pas == 3) {
    header("Location: cont.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>Ștergere cont - Ciuperca lui Fertig</title>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f8f8f8;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
}

.container {
    background-color: #fff;
    padding: 25px 30px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    width: 100%;
    max-width: 400px;
    text-align: center;
}

label {
    display: block;
    font-weight: bold;
    margin-bottom: 10px;
    color: #b30000;
}

input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    box-sizing: border-box;
}

button {
    background-color: #b30000;
    color: white;
    padding: 10px 18px;
    margin: 5px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
}

button:hover {
    background-color: #c84747;
}

p.error {
    color: red;
    font-weight: bold;
    margin-bottom: 15px;
}
</style>
</head>
<body>

<div class="container">

<?php if ($pas == 1 && !password_verify($parola, $_SESSION['parola'])): ?>
<p class="error">Parola incorecta!</p>
<?php $pas = 0; endif; ?>

<?php if ($pas == 0): ?>
<form method="POST" autocomplete="off">
    <label>Introduceti parola pentru a finaliza stergerea:</label>
    <input type="password" name="parola" required>
    <button type="submit" name="pas" value="1">Continua</button>
</form>
<?php endif; ?>

<?php if ($pas == 1): ?>
<form method="POST" autocomplete="off">
    <label>Sunteti sigur ca vreti sa stergeti contul?</label>
    <button type="submit" name="pas" value="2">Da</button>
    <button type="submit" name="pas" value="3">Nu</button>
</form>
<?php endif; ?>

</div>

</body>
</html>
