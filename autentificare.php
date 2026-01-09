
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Autentificare</title>
    <style>
        /* Body */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Container formular */
        .login-container {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            width: 360px;
        }

        /* Titlu */
        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        /* Input-uri și select */
        .login-container input[type="text"],
        .login-container input[type="password"],
        .login-container select {
            width: 100%;
            padding: 10px 12px;
            margin: 8px 0 15px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
        }

        /* Buton submit */
        .login-container button {
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

        .login-container button:hover {
            background-color: #45a049;
        }

        /* Link-uri */
        .login-container a {
            display: block;
            text-align: center;
            text-decoration: none;
            color: #555;
            margin-top: 15px;
            font-size: 14px;
        }

        .login-container a:hover {
            color: #4CAF50;
        }
    </style>
    <link rel="icon" type="image/jpeg" href="ciupercaluifertig.jpeg">
</head>
<body>

<div class="login-container">
    <h2>Autentificare</h2>

    <form action="verificare.php" method="POST" autocomplete="off">
        <label for="tip">Tip cont:</label>
        <select id="tip" name="tip" required>
            <option value="pacient">Pacient</option>
            <option value="medic">Medic clinician</option>
            <option value="laborator">Medic laborator</option>
        </select>

        <label for="user">User:</label>
        <input type="text" id="user" name="user" required>

        <label for="parola">Parola:</label>
        <input type="password" id="parola" name="parola" required>

        <button type="submit">Login</button>
    </form>

    <a href="cont_nou.php">Nu ai cont? Creează unul nou.</a>
    <a href="grafice.php">Statistici autentificare</a>
    <a href="formular.php">Formular de contact</a>
</div>

</body>
</html>
