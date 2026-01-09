<?php
session_start();

if (!isset($_SESSION['logat']) || $_SESSION['logat'] !== true) {
    header('Location: autentificare.php');
    exit;
}

$pas = $_POST['pas'] ?? 0;
$parola = $_POST['parola'] ?? '';

if ($pas == 0) {
    $_SESSION['nounume']        = $_POST['nume'] ?? '';
    $_SESSION['nouprenume']     = $_POST['prenume'] ?? '';
    $_SESSION['noucnp']         = $_POST['cnp'] ?? '';
    $_SESSION['nouemail']       = $_POST['email'] ?? '';
    $_SESSION['nouuser']        = $_POST['user'] ?? '';
    $_SESSION['nouaparola']     = $_POST['parola'] ?? '';
    $_SESSION['nouspecializare']= $_POST['specializare'] ?? '';
    $_SESSION['nouparafa']      = $_POST['cod_parafa'] ?? '';
    $_SESSION['noulaborator']   = $_POST['laborator'] ?? '';
}
if ($pas == 1 && !password_verify($parola, $_SESSION['parola'])) {
    $pas = 3;
}
if ($pas == 1) {
    $pas = 2;
}

if ($pas == 2) {
    $servername = "localhost";
    $username = "adragans_proiect";
    $password = "Parola.123";
    $dbname = "adragans_proiect";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Conexiunea a eșuat: " . $conn->connect_error);
    }

    if ($_SESSION['nounume']) {
        $conn->query("UPDATE Conturi SET Nume='" . $conn->real_escape_string($_SESSION['nounume']) . "' WHERE Nr_crt=" . $_SESSION['id'] . ";");
        $_SESSION['nume'] = $_SESSION['nounume'];
        unset($_SESSION['nounume']);
    }

    if ($_SESSION['nouprenume']) {
        $conn->query("UPDATE Conturi SET Prenume='" . $conn->real_escape_string($_SESSION['nouprenume']) . "' WHERE Nr_crt=" . $_SESSION['id'] . ";");
        $_SESSION['prenume'] = $_SESSION['nouprenume'];
        unset($_SESSION['nouprenume']);
    }

    if ($_SESSION['noucnp']) {
        $conn->query("UPDATE Conturi SET CNP='" . $conn->real_escape_string($_SESSION['noucnp']) . "' WHERE Nr_crt=" . $_SESSION['id'] . ";");
        unset($_SESSION['noucnp']);
    }

    if ($_SESSION['nouemail']) {
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $eroare = "Email invalid! Introdu o adresă corectă.";
        } else {
        $domain = substr(strrchr($mail, "@"), 1);
        if (!checkdnsrr($domain, "MX")) {
            $eroare = "Email invalid: domeniu nu există sau nu primește emailuri.";
        }else {
        $conn->query("UPDATE Conturi SET Email='" . $conn->real_escape_string($_SESSION['nouemail']) . "' WHERE Nr_crt=" . $_SESSION['id'] . ";");
        unset($_SESSION['nouemail']);}}
    }

    if ($_SESSION['nouuser']) {
        $result=$conn->query("SELECT * FROM Conturi WHERE User='".$_SESSION['nouuser']."';");
        if($result->num_rows === 0)
        {
        $conn->query("UPDATE Conturi SET User='" . $conn->real_escape_string($_SESSION['nouuser']) . "' WHERE Nr_crt=" . $_SESSION['id'] . ";");
        $_SESSION['user'] = $_SESSION['nouuser'];}
        else
        {
            $eroare = "User-ul introdus există deja!";
        }
        unset($_SESSION['nouuser']);
    }

    if ($_SESSION['nouaparola']) {
        $hash = password_hash($_SESSION['nouaparola'], PASSWORD_DEFAULT);
        $conn->query("UPDATE Conturi SET Parola='" . $hash . "' WHERE Nr_crt=" . $_SESSION['id'] . ";");
        $_SESSION['parola'] = $hash;
        unset($_SESSION['nouaparola']);
    }

    if ($_SESSION['nouspecializare']) {
        $conn->query("UPDATE Medici SET specializare='" . $conn->real_escape_string($_SESSION['nouspecializare']) . "' WHERE nr_crt=" . $_SESSION['id'] . ";");
        unset($_SESSION['nouspecializare']);
    }

    if ($_SESSION['nouparafa']) {
        $conn->query("UPDATE Medici SET cod_parafa='" . $conn->real_escape_string($_SESSION['nouparafa']) . "' WHERE nr_crt=" . $_SESSION['id'] . ";");
        unset($_SESSION['nouparafa']);
    }

    if ($_SESSION['noulaborator']) {
        $conn->query("UPDATE Laboratoare SET cod_laborator='" . $conn->real_escape_string($_SESSION['noulaborator']) . "' WHERE nr_crt=" . $_SESSION['id'] . ";");
        unset($_SESSION['noulaborator']);
    }
    if(!$eroare){
    header('Location: cont.php');
    exit;}
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>Modificare cont - Ciuperca lui Fertig</title>
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

<?php if ($eroare): ?>
<p class="error"><?= $eroare ?></p>
<a href="cont.php">Inapoi</a>
<?php endif; ?>

<?php if ($pas == 1 && !password_verify($parola, $_SESSION['parola'])): ?>
<p class="error">Parola incorecta!</p>
<?php $pas = 0; endif; ?>

<?php if ($pas == 3): ?>
<p>Parola incorecta!</p>
<?php endif; ?>

<?php if ($pas == 0 || $pas == 3): ?>
<form method="POST" autocomplete="off">
    <label>Introduceti parola pentru a efectua modificarea:</label>
    <input type="password" name="parola" required>
    <button type="submit" name="pas" value="1">Continua</button>
</form>
<?php endif; ?>

</div>

</body>
</html>
