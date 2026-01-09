<?php
session_start();

if (!isset($_SESSION['logat']) || $_SESSION['logat'] !== true) {
    header('Location: autentificare.php');
    exit;
}

$tip = $_SESSION['tip'];
$servername = "localhost";
$username = "adragans_proiect";
$password = "Parola.123";
$dbname = "adragans_proiect";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}
if($tip=='pacient'){
$sql = "SELECT c.Nume, c.Prenume, c.CNP, c.Email, c.User, CONCAT(m.Nume, ' ', m.Prenume) AS Medicul, c.Parola, c.Nr_crt
        FROM Conturi c 
        LEFT JOIN Pacient_Medic pm ON c.Nr_crt=pm.Pacient 
        LEFT JOIN Conturi m ON pm.Medic=m.Nr_crt
        WHERE c.User='".$_SESSION['user']."';";
}
if($tip=='medic'){
$sql = "SELECT c.Nume, c.Prenume, c.CNP, c.Email, c.User, m.cod_parafa, m.specializare, c.Parola, c.Nr_crt
        FROM Conturi c JOIN Medici m
        ON c.Nr_crt=m.nr_crt
        WHERE User='".$_SESSION['user']."';";
}
if($tip=='laborator'){
$sql = "SELECT c.Nume, c.Prenume, c.CNP, c.Email, c.User, l.cod_laborator, c.Parola, c.Nr_crt
        FROM Conturi c JOIN Laboratoare l
        ON c.Nr_crt=l.nr_crt
        WHERE User='".$_SESSION['user']."';";
}
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$_SESSION['parola']=$row['Parola'];
$_SESSION['id']=$row['Nr_crt'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Panou utilizator</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            min-height: 100vh;
            background-color: #fff5f5;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        nav {
            background-color: #b30000;
            padding: 12px 20px;
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        nav a {
            color: #ffd6d6;
            text-decoration: none;
            font-weight: bold;
        }

        nav a:hover {
            color: white;
        }

        nav a.active {
            color: white;
            border-bottom: 2px solid white;
        }

        .medic-container {
            background: #fff;
            padding: 15px 20px;
            border-radius: 8px;
            width: 100%;
            max-width: 500px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: bold;
            color: #b30000;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .modificare-container {
            background: #fff;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 500px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            color: #b30000;
            display: block;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button, .btn, .delete-btn {
            background-color: #b30000;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            font-size: 14px;
        }

        .modificare-container form {
            display: flex;
            flex-direction: column;
            gap: 8px;
            width: 100%;
        }

        .modificare-container form button {
            display: inline-block;
            width: auto;
            align-self: flex-start; /* face ca butonul să nu se întindă pe tot formularul */
        }


        button:hover, .btn:hover, .delete-btn:hover {
            background-color: #c84747;
        }

        .delete-btn {
            margin-top: 20px;
        }

        form {
            margin-bottom: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
    </style>
</head>
<body>

<?php $pagina_curenta = basename($_SERVER['PHP_SELF']); ?>
<nav>
    <a href="secret.php" class="<?= $pagina_curenta=='secret.php'?'active':'' ?>">Home</a>
    <a href="analize_afisare.php" class="<?= $pagina_curenta=='analize_afisare.php'?'active':'' ?>">Analize</a>
    <a href="cont.php" class="<?= $pagina_curenta=='cont.php'?'active':'' ?>">Cont</a>
    <a href="formular.php" class="<?= $pagina_curenta=='formular.php'?'active':'' ?>">Formular</a>
    <a href="logout.php">Logout</a>
</nav>
<?php if ($tip == 'pacient'): ?>
<?php if ($row['Medicul']): ?>
    <div class="medic-container">
        <span>Medicul curent: <?= htmlspecialchars($row['Medicul']) ?></span>
        <a href="schimba_medic.php" class="btn">Schimbă medicul</a>
    </div>
<?php else: ?>
    <div class="medic-container">
        <span>Nu ai un medic asociat!</span>
        <a href="schimba_medic.php" class="btn">Alege medic</a>
    </div>
<?php endif; ?>
<?php endif; ?>


<div class="modificare-container">

    <form method="POST" action="modifica_cont.php" autocomplete="off">
        <label>Nume: <?= htmlspecialchars($row['Nume']) ?></label>
        <input type="text" name="nume" placeholder="Introduceți noul nume">
        <button type="submit" name="modifica_coloana" value="nume">Modifica</button>
    </form>

    <form method="POST" action="modifica_cont.php" autocomplete="off">
        <label>Prenume: <?= htmlspecialchars($row['Prenume']) ?></label>
        <input type="text" name="prenume" placeholder="Introduceți noul prenume">
        <button type="submit" name="modifica_coloana" value="prenume">Modifica</button>
    </form>

    <form method="POST" action="modifica_cont.php" autocomplete="off">
        <label>CNP: <?= htmlspecialchars($row['CNP']) ?></label>
        <input type="text" name="cnp" placeholder="Introduceți noul CNP">
        <button type="submit" name="modifica_coloana" value="cnp">Modifica</button>
    </form>

    <form method="POST" action="modifica_cont.php" autocomplete="off">
        <label>Email: <?= htmlspecialchars($row['Email']) ?></label>
        <input type="email" name="email" placeholder="Introduceți noul email">
        <button type="submit" name="modifica_coloana" value="email">Modifica</button>
    </form>

    <form method="POST" action="modifica_cont.php" autocomplete="off">
        <label>User: <?= htmlspecialchars($row['User']) ?></label>
        <input type="text" name="user" placeholder="Introduceți noul user">
        <button type="submit" name="modifica_coloana" value="user">Modifica</button>
    </form>

    <form method="POST" action="modifica_cont.php" autocomplete="off">
        <label>Parola</label>
        <input type="password" name="parola" placeholder="Introduceți noua parola">
        <button type="submit" name="modifica_coloana" value="parola">Modifica</button>
    </form>

    <?php if ($tip == 'medic'): ?>
    <form method="POST" action="modifica_cont.php" autocomplete="off">
        <label>Specializare: <?= htmlspecialchars($row['specializare']) ?></label>
        <input type="text" name="specializare" placeholder="Introduceți noua specializare">
        <button type="submit" name="modifica_coloana" value="specializare">Modifica</button>
    </form>

    <form method="POST" action="modifica_cont.php" autocomplete="off">
        <label>Cod parafa: <?= htmlspecialchars($row['cod_parafa']) ?></label>
        <input type="text" name="cod_parafa" placeholder="Introduceți noul cod de parafa">
        <button type="submit" name="modifica_coloana" value="cod_parafa">Modifica</button>
    </form>
    <?php endif; ?>

    <?php if ($tip == 'laborator'): ?>
    <form method="POST" action="modifica_cont.php" autocomplete="off">
        <label>Laborator: <?= htmlspecialchars($row['cod_laborator']) ?></label>
        <input type="text" name="cod_laborator" placeholder="Introduceți noul cod al laboratorului">
        <button type="submit" name="modifica_coloana" value="cod_laborator">Modifica</button>
    </form>
    <?php endif; ?>

</div>


<a href="sterge_cont.php" class="delete-btn">Șterge contul</a>

</body>
</html>

<?php $conn->close(); ?>
