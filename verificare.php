<?php
session_start();

$user = $_POST['user'] ?? '';
$parola = $_POST['parola'] ?? '';
$tip = $_POST['tip'] ?? '';

$ip = $_SERVER['REMOTE_ADDR'];
date_default_timezone_set('Europe/Bucharest'); 
$data_acces = date("Y-m-d H:i:s");


$info = @json_decode(file_get_contents("http://ip-api.com/json/$ip?fields=status,country,city"), true);

if ($info && $info['status'] === 'success') {
    $tara = $info['country'];
    $oras = $info['city'];
} else {
    $tara = 'Necunoscuta';
    $oras = 'Necunoscut';
}


$ua = $_SERVER['HTTP_USER_AGENT'];

if (strpos($ua, 'OPR/') !== false || strpos($ua, 'Opera') !== false) {
    $browser = 'Opera';
} elseif (strpos($ua, 'Edg/') !== false) {
    $browser = 'Microsoft Edge';
} elseif (strpos($ua, 'Chrome') !== false) {
    $browser = 'Chrome';
} elseif (strpos($ua, 'Safari') !== false) {
    $browser = 'Safari';
} elseif (strpos($ua, 'Firefox') !== false) {
    $browser = 'Firefox';
} else {
    $browser = 'Unknown';
}

$conn = new mysqli("localhost", "adragans_proiect", "Parola.123", "adragans_proiect");
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT * FROM Conturi WHERE User = ? AND Tip = ?");
$stmt->bind_param("ss", $user, $tip);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    if (password_verify($parola, $row['Parola'])) {
        $_SESSION['logat'] = true;
        $_SESSION['user'] = $user;
        $_SESSION['tip'] = $tip;
        $_SESSION['prenume'] = $row['Prenume'];
        $_SESSION['nume'] = $row['Nume'];

        $sql = "INSERT INTO Statistici_logare (IP, Tara, Oras, Browser, Data, Succes) Values ('".$ip."', '".$tara."', '".$oras."', '".$browser."', '".$data_acces."', 1);";
        $conn->query($sql);

        $stmt->close();
        $conn->close();
        header("Location: secret.php");
        exit;
    } else {
        $eroare = "Parolă greșită!";
        $sql = "INSERT INTO Statistici_logare (IP, Tara, Oras, Browser, Data, Succes) Values ('".$ip."', '".$tara."', '".$oras."', '".$browser."', '".$data_acces."', 0);";
        $conn->query($sql);
    }
} else {
    $eroare = "User sau tip incorect!";
    $sql = "INSERT INTO Statistici_logare (IP, Tara, Oras, Browser, Data, Succes) Values ('".$ip."', '".$tara."', '".$oras."', '".$browser."', '".$data_acces."', 0);";
        $conn->query($sql);
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Autentificare</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .eroare {
            background-color: #b30000;
            color: white;
            padding: 20px 25px;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
        }
        .eroare a {
            display: inline-block;
            margin-top: 15px;
            padding: 8px 12px;
            background-color: #ff6666;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .eroare a:hover {
            background-color: #ff4d4d;
        }
    </style>
</head>
<body>

<?php if (isset($eroare)): ?>
    <div class="eroare">
        <?= htmlspecialchars($eroare) ?><br>
        <a href="autentificare.php">Înapoi</a>
    </div>
<?php endif; ?>

</body>
</html>
