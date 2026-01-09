<?php
session_start();

$pas = $_POST['pas'] ?? 0;
$tip = $_POST['tip'] ?? $_SESSION['tip'] ?? '';
$nume = $_POST['nume'] ?? $_SESSION['nume'] ?? '';
$prenume = $_POST['prenume'] ?? $_SESSION['prenume'] ?? '';
$cnp = $_POST['cnp'] ?? $_SESSION['cnp'] ?? '';
$parafa = $_POST['parafa'] ?? $_SESSION['parafa'] ?? '';
$specializare = $_POST['specializare'] ?? $_SESSION['specializare'] ?? '';
$cod_laborator = $_POST['cod_laborator'] ?? $_SESSION['cod_laborator'] ?? '';
$mail = $_POST['mail'] ?? $_SESSION['mail'] ?? '';
$user = $_POST['user'] ?? '';
$parola = $_POST['parola'] ?? $_SESSION['parola'] ?? '';

$eroare = '';

if ($pas == 1) {
    if ($tip == '') $pas = 0;
    else $_SESSION['tip'] = $tip;
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
    $result=$conn->query("SELECT * FROM Conturi WHERE User='".$user."';");
    $conn->close();
    if($result->num_rows !== 0)
    {
         $eroare = "User deja existent.";
    }
    else
    {
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $eroare = "Email invalid! Introdu o adresă corectă.";
    } else {
        $domain = substr(strrchr($mail, "@"), 1);
        if (!checkdnsrr($domain, "MX")) {
            $eroare = "Email invalid: domeniu nu există sau nu primește emailuri.";
        } else {
            $_SESSION['pending_user'] = [
                'tip' => $tip,
                'nume' => $nume,
                'prenume' => $prenume,
                'cnp' => $cnp,
                'parafa' => $parafa,
                'specializare' => $specializare,
                'cod_laborator' => $cod_laborator,
                'mail' => $mail,
                'user' => $user,
                'parola' => password_hash($parola, PASSWORD_DEFAULT)
            ];

            $verif_code = random_int(100000, 999999);
            $_SESSION['verif_code'] = $verif_code;

            header("Location: verificare_nou.php");
            exit;
        }
    }
    }
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

.form-container {
    background-color: #fff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    width: 400px;
}

h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #a83232;
}

label {
    font-weight: bold;
    color: #a83232;
}

input[type="text"],
input[type="password"],
input[type="email"],
select {
    width: 100%;
    padding: 10px 12px;
    margin: 8px 0 15px 0;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    box-sizing: border-box;
}

button {
    width: 100%;
    padding: 12px;
    background-color: #a83232;
    color: white;
    font-size: 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #c84747;
}

a {
    display: block;
    text-align: center;
    text-decoration: none;
    color: #a83232;
    margin-top: 15px;
    font-size: 14px;
}

a:hover {
    color: #c84747;
}

.error-message {
    background-color: #ffe6e6;
    border: 1px solid #a83232;
    color: #a83232;
    padding: 15px 20px;
    border-radius: 8px;
    text-align: center;
    font-weight: bold;
    margin-bottom: 15px;
}
</style>
</head>
<body>

<div class="form-container">
<h2>Creare cont - Ciuperca lui Fertig</h2>

<?php if ($eroare): ?>
    <div class="error-message">
        <?= htmlspecialchars($eroare) ?><br>
        <a href="javascript:history.back()">Înapoi</a>
    </div>
<?php endif; ?>

<form method="POST" autocomplete="off">

<?php if ($pas == 0): ?>
<label>Tip cont:</label>
<select name="tip" required>
<option value="pacient" <?= $tip=='pacient'?'selected':'' ?>>Pacient</option>
<option value="medic" <?= $tip=='medic'?'selected':'' ?>>Medic clinician</option>
<option value="laborator" <?= $tip=='laborator'?'selected':'' ?>>Medic laborator</option>
</select>

<button type="submit" name="pas" value="1">Continua</button>
<?php endif; ?>

<?php if ($pas == 1): ?>
<label>Nume:</label>
<input type="text" name="nume" value="<?= htmlspecialchars($nume) ?>" required>

<label>Prenume:</label>
<input type="text" name="prenume" value="<?= htmlspecialchars($prenume) ?>" required>

<label>CNP:</label>
<input type="text" name="cnp" value="<?= htmlspecialchars($cnp) ?>" required>

<?php if ($tip == 'medic'): ?>
<label>Cod parafa:</label>
<input type="text" name="parafa" value="<?= htmlspecialchars($parafa) ?>" required>

<label>Specializare:</label>
<input type="text" name="specializare" value="<?= htmlspecialchars($specializare) ?>" required>
<?php endif; ?>

<?php if ($tip == 'laborator'): ?>
<label>Cod laborator:</label>
<input type="text" name="cod_laborator" value="<?= htmlspecialchars($cod_laborator) ?>" required>
<?php endif; ?>

<label>Email:</label>
<input type="email" name="mail" value="<?= htmlspecialchars($mail) ?>" required>

<label>User:</label>
<input type="text" name="user" value="<?= htmlspecialchars($user) ?>" required>

<label>Parola:</label>
<input type="password" name="parola" value="<?= htmlspecialchars($parola) ?>" required>

<button type="submit" name="pas" value="2">Trimite cod de verificare</button>
<?php endif; ?>

</form>
</div>

</body>
</html>
