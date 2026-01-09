<?php
session_start();

if (!isset($_SESSION['logat']) || $_SESSION['logat'] !== true || $_SESSION['tip']!=='laborator') {
    header('Location: autentificare.php');
    exit;
}

$_SESSION['nr_analiza']=$_POST['Nr_crt'] ?? $_SESSION['nr_analiza'] ?? '';
$trimis=0;
$valoare=$_POST['valoare'] ?? '';
$observatii=$_POST['observatii'] ?? '';
$data_curenta = date('Y-m-d');

$servername = "localhost";
$username = "adragans_proiect";
$password = "Parola.123";
$dbname = "adragans_proiect";

if($valoare)
{
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}
if($observatii)
{
    $stmt = $conn->prepare(
    "UPDATE Rezultate SET Valoare = ? , Observatii = ? , Stare=1, Data_rezultatului = ? WHERE Nr_crt = ?");
    $stmt->bind_param("sssi", $valoare, $observatii, $data_curenta, $_SESSION['nr_analiza']);
}
else
{
    $stmt = $conn->prepare(
    "UPDATE Rezultate SET Valoare = ? , Stare=1, Data_rezultatului = ? WHERE Nr_crt = ?");
    $stmt->bind_param("ssi", $valoare, $data_curenta, $_SESSION['nr_analiza']);
}
$stmt->execute();

$sql="SELECT c.Nume, c.Prenume, c.Email
        FROM Conturi c JOIN Rezultate r
        ON c.Nr_crt=r.Nr_pacient
        WHERE r.Nr_crt = ".$_SESSION['nr_analiza'].";";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$nume_pacient=$row['Nume'];
$prenume_pacient=$row['Prenume'];
$mail_pacient=$row['Email'];

$conn->close();
$trimis=1;

//trimis mail
require_once('mail/class.phpmailer.php');
require_once('mail/mail_config.php');

$message = "Buna ziua, ".$prenume_pacient." ".$nume_pacient."!<br>Analizele tale sunt gata. Puteti accesa rezultatele pe site.";
$message = wordwrap($message, 160, "<br />\n");

$mail = new PHPMailer(true); 

$mail->IsSMTP();

try {
  $mail->SMTPDebug  = 0;                     
  $mail->SMTPAuth   = true; 

  $to=$mail_pacient;
  $nume=$nume_pacient;

  $mail->SMTPSecure = "ssl";             
  $mail->Host       = "mail.adragan.daw.ssmr.ro.";      
  $mail->Port       = 465;                   
  $mail->Username   = $username;  			
  $mail->Password   = $password;            
  $mail->AddReplyTo('asdm@adragan.daw.ssmr.ro', 'Ciuperca lui Fertig');
  $mail->AddAddress($to, $nume);
 
  $mail->SetFrom('asdm@adragan.daw.ssmr.ro', 'Ciuperca lui Fertig');
  $mail->Subject = 'Cod verificare';
  $mail->AltBody = 'To view this post you need a compatible HTML viewer!'; 
  $mail->MsgHTML($message);
  $mail->Send();
} catch (phpmailerException $e) {
  echo $e->errorMessage();
} catch (Exception $e) {
  echo $e->getMessage();
}
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Rezultate analize</title>
    <style>
    body {
    margin: 0;
    font-family: Arial, sans-serif;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center; /* centru vertical */
    align-items: center;     /* centru orizontal */
    background-color: #fff5f5;
    padding: 20px;
}

h2 {
    color: #b30000;
    margin-bottom: 20px;
    text-align: center;
}

form {
    display: flex;
    flex-direction: column;
    gap: 15px;
    width: 100%;
    max-width: 400px;
    background-color: #ffe6e6;
    padding: 20px;
    border: 1px solid #b30000;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(179,0,0,0.2);
}

label {
    font-weight: bold;
    color: #b30000;
}

input[type="text"] {
    padding: 8px 10px;
    border: 1px solid #b30000;
    border-radius: 6px;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s ease;
}

input[type="text"]:focus {
    border-color: #c84747;
}

button {
    padding: 6px 12px; /* doar cât textul */
    background-color: #b30000;
    color: #ffd6d6;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    width: auto; /* cât textul */
    align-self: flex-start; /* nu întinde butonul la lățimea formularului */
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

a {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 18px;
    background-color: #b30000;
    color: #ffd6d6;
    text-decoration: none;
    font-weight: bold;
    border-radius: 6px;
    transition: background-color 0.2s ease, transform 0.1s ease;
}

a:hover {
    background-color: #c84747;
    color: white;
    transform: translateY(-1px);
}

a:active {
    transform: translateY(0);
}

    </style>
</head>
<body>
<?php if ($trimis == 0): ?>
        <h2>Introduceti rezultatul analizelor</h2>
        <form method="POST" autocomplete="off">
            <label for="valoare">Valoare:</label>
            <input type="text" id="valoare" name="valoare" required">

            <label for="observatii">Observatii:</label>
            <input type="text" id="observatii" name="observatii">

            <button type="submit">Trimite rezultatul</button>
        </form>
<?php endif; ?>
<?php if ($trimis == 1): ?>
    <h2>Rezultate trimise cu succes</h2>
    <a href="secret.php">Continua</a>
<?php endif; ?>

</body>
</html>