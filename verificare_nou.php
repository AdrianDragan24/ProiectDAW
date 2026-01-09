<?php
session_start();
require_once('mail/class.phpmailer.php');
require_once('mail/mail_config.php');

$message = "Bine ai venit, ".$_SESSION['pending_user']['prenume']." ".$_SESSION['pending_user']['nume']."!<br>Codul tau de verificare este: ". $_SESSION['verif_code'];
$message = wordwrap($message, 160, "<br />\n");

$mail = new PHPMailer(true); 

$mail->IsSMTP();

try {
  $mail->SMTPDebug  = 0;                     
  $mail->SMTPAuth   = true; 

  $to=$_SESSION['pending_user']['mail'];
  $nume=$_SESSION['pending_user']['nume'];

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
?>

<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>Verificare cont - Ciuperca lui Fertig</title>
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
label {
    font-weight: bold;
    color: #a83232;
    display: block;
    margin-top: 15px;
}
input[type="text"] {
    width: 100%;
    padding: 10px 12px;
    margin-top: 5px;
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
    margin-top: 20px;
    transition: background-color 0.3s ease;
}
button:hover {
    background-color: #c84747;
}
</style>
</head>
<body>
<div class="container">
<p>Am trimis un cod de verificare pe adresa ta de email! Verifica si introdu mai jos.</p>
<form action="definitivare_cont.php" method="POST" autocomplete="off">
    <label>Cod de verificare:</label>
    <input type="text" name="cod" required>
    <button type="submit">Confirmă cont</button>
</form>
</div>
</body>
</html>
