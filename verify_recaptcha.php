<?php 
$returnMsg = ''; 
 
if(isset($_POST['submit'])){ 
    
	// Form fields validation check
    if(!empty($_POST['nume']) && !empty($_POST['prenume']) && !empty($_POST['email']) && !empty($_POST['mesaj'])){ 
         
        // reCAPTCHA checkbox validation
        if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){ 
            // Google reCAPTCHA API secret key 
            $secret_key = "..."; 
             
            // reCAPTCHA response verification
            $verify_captcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$_POST['g-recaptcha-response']); 
            
            // Decode reCAPTCHA response 
            $verify_response = json_decode($verify_captcha); 
             
            // Check if reCAPTCHA response returns success 
            if($verify_response->success){ 

            $nume = $_POST['nume'];
            $prenume = $_POST['prenume'];
            $email = $_POST['email']; 
            $mesaj = $_POST['mesaj'];

            require_once('mail/class.phpmailer.php');
            require_once('mail/mail_config.php');

            $message = "Formular de contact<br>Nume: ".$nume."<br>Prenume: ".$prenume."<br>Email: ".$email."<br><br>Mesaj:<br>".$mesaj;
            $message = wordwrap($message, 160, "<br />\n");
            $mail = new PHPMailer(true);
            $mail->IsSMTP();

            try {
            $mail->SMTPDebug  = 0;                     
            $mail->SMTPAuth   = true; 

            $to="asdm@adragan.daw.ssmr.ro";

            $mail->SMTPSecure = "ssl";             
            $mail->Host       = "mail.adragan.daw.ssmr.ro.";      
            $mail->Port       = 465;                   
            $mail->Username   = $username;  			
            $mail->Password   = $password;            
            $mail->AddReplyTo($email, $nume." ".$prenume);
            $mail->AddAddress($to, $nume);
 
            $mail->SetFrom('asdm@adragan.daw.ssmr.ro', $nume);
            $mail->Subject = 'Formular contact';
            $mail->AltBody = 'To view this post you need a compatible HTML viewer!'; 
            $mail->MsgHTML($message);
            $mail->Send();
            $returnMsg="Mesajul dumneavoastra a fost trimis cu succes!";
            } catch (phpmailerException $e) {
            echo $e->errorMessage();
            } catch (Exception $e) {
            echo $e->getMessage();
            }
            } 
        }
		else{ 
			$returnMsg = 'Bifati casuta "Nu sunt robot"!'; 
        } 
    }
	 else
			{ 
				$returnMsg = 'Completeaza toate campurile formularului!.'; 
			} 
} 
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Panou utilizator</title>
    <style>
        /* Body centrat */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        /* Container */
        .panel-container {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            width: 360px;
            text-align: center;
        }

        /* Mesaj */
        .panel-container p {
            font-size: 16px;
            color: #333;
            margin-bottom: 25px;
        }

        /* Link continuare */
        .panel-container a {
            display: inline-block;
            text-decoration: none;
            background-color: #4CAF50;
            color: white;
            padding: 12px 25px;
            border-radius: 6px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .panel-container a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="panel-container">
    <p><?= $returnMsg ?></p>
    <a href="secret.php">Continuă</a>
</div>

</body>
</html>
