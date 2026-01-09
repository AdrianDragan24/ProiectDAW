<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Formular contact</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        /* Body */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        /* Container formular */
        .contact-container {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            width: 360px;
        }

        /* Titlu */
        .contact-container h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        /* Descriere */
        .contact-container p {
            text-align: center;
            margin-bottom: 25px;
            color: #555;
            font-size: 14px;
        }

        /* Label-uri */
        .contact-container label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-size: 14px;
        }

        /* Input-uri și textarea */
        .contact-container input[type="text"],
        .contact-container textarea {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
            resize: vertical;
        }

        /* Buton submit */
        .contact-container input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .contact-container input[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Captcha centrat */
        .g-recaptcha {
            margin: 15px 0 25px 0;
        }

        /* Link-uri / mesaje */
        #message {
            text-align: center;
            margin-bottom: 15px;
            font-size: 14px;
            color: #d9534f; /* rosu pentru erori */
        }

    </style>
</head>
<body>

<div class="contact-container">
    <h1>Formular contact</h1>
    <p>Te rugăm să ne transmiți informațiile de mai jos:</p>
    
    <div id="message"></div>

    <form action="verify_recaptcha.php" method="post">
        <label for="nume">Nume:</label>
        <input type="text" id="nume" name="nume" required>

        <label for="prenume">Prenume:</label>
        <input type="text" id="prenume" name="prenume" required>

        <label for="email">Email:</label>
        <input type="text" id="email" name="email" required>

        <label for="mesaj">Mesaj:</label>
        <textarea id="mesaj" name="mesaj" rows="2" required></textarea>

        <div class="g-recaptcha" data-sitekey="6LcoUTksAAAAAENIEXGsgASU62_15kEDOFqkPCfq"></div>

        <input type="submit" name="submit" value="Trimite">
    </form>
</div>

</body>
</html>