<?php
session_start();

if (!isset($_SESSION['logat']) || $_SESSION['logat'] !== true) {
    header('Location: autentificare.php');
    exit;
}

$tip = $_SESSION['tip'];
$_SESSION['optiune']=$_POST['optiune'] ?? '';

$servername = "localhost";
$username = "adragans_proiect";
$password = "Parola.123";
$dbname = "adragans_proiect";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}
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
            display: flex;
            flex-direction: column;
            background-color: #fff5f5; /* nuanță roșiatică deschisă */
        }

        main {
            flex: 1;
            padding: 20px;
        }

        h2 {
            color: #b30000; /* roșu mai închis */
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #b30000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #ffcccc;
        }

        tr:nth-child(even) {
            background-color: #ffe6e6;
        }

        a.btn {
            display: inline-block;
            padding: 10px 18px;
            background-color: #b30000;
            color: #ffd6d6;
            text-decoration: none;
            font-weight: bold;
            border-radius: 6px;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        a.btn:hover {
            background-color: #c84747;
            color: white;
            transform: translateY(-1px);
        }

        a.btn:active {
            transform: translateY(0);
        }
        nav {
            background-color: #b30000;
            padding: 12px 20px;
            display: flex;
             gap: 20px;
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

        .analize-container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 15px;
}

.analiza-card {
    flex: 1 1 200px;
    padding: 12px 15px;
    background-color: #ffe6e6; /* acea nuanță deschisă */
    border: 1px solid #b30000;
    border-radius: 6px; /* ca la butoanele existente */
    text-align: center;
    cursor: pointer;
    font-weight: bold;
    color: #b30000;
    transition: background-color 0.2s ease, transform 0.1s ease;
}

.analiza-card:hover {
    background-color: #ffcccc;
    color: white;
    transform: translateY(-1px);
}

.analiza-card:active {
    transform: translateY(0);
}

.analiza-card span {
    display: block;
    font-weight: normal;
    color: #b30000;
    margin-top: 5px;
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

<main>
<h2>Bine ai venit, <?= htmlspecialchars($_SESSION['prenume']) ?>!</h2>

<?php if ($tip === 'medic'): ?>
    <?php
    $sql = "SELECT c1.User AS user_pacient, CONCAT(c1.Nume,' ', c1.Prenume) AS Pacient
            FROM Conturi c JOIN Pacient_Medic pm
            ON c.Nr_crt=pm.Medic JOIN Conturi c1
            ON c1.Nr_crt=pm.Pacient
            WHERE c.tip='medic' AND c.user='".$_SESSION['user']."';"; 
    $result = $conn->query($sql);
    ?>
    <form method="POST">
        <label for="optiune">Alege un pacient:</label>
        <select name="optiune" id="optiune" required>
            <option value="">-- Selectează --</option>
            <?php
            if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
            ?>
            <option value="<?= htmlspecialchars($row['user_pacient']) ?>"
                <?= $_SESSION['optiune'] == $row['user_pacient'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($row['Pacient']) ?>
            </option>
            <?php
            }

            } else {
                echo '<option value="">Nu aveți pacienți atribuiți!</option>';
            }
            ?>
        </select>
        <button type="submit">Vizualizeaza analizele pacientului</button>
    </form>
<?php endif; ?>
<?php if ($tip === 'pacient' || $_SESSION['optiune']): ?>
    <h4>Analize in lucru</h4>
    <?php
    if($_SESSION['optiune'])
    {$user_analize=$_SESSION['optiune'];}
    else
    {$user_analize=$_SESSION['user'];}
    $sql = "SELECT a.Nume_analiza, r.Data_recoltarii
            FROM Conturi c LEFT JOIN Rezultate r ON
            c.Nr_crt=r.Nr_pacient
            LEFT JOIN Analize a ON r.Nr_analiza=a.Nr_crt
            WHERE c.user='".$user_analize."' AND r.stare=0;"; 
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        echo "Nu exista analize in lucru.<br>";
    } else {
        echo "<table>";
        echo "<tr>
                <th>Analiză</th>
                <th>Data recoltare</th>
              </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Nume_analiza']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Data_recoltarii']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } ?>
    <h4>Rezultate analize</h4>
    <?php
    $sql = "SELECT a.Nume_analiza, r.Data_rezultatului, r.Valoare, r.Observatii
            FROM Conturi c LEFT JOIN Rezultate r ON
            c.Nr_crt=r.Nr_pacient
            LEFT JOIN Analize a ON r.Nr_analiza=a.Nr_crt
            WHERE c.User='".$user_analize."' AND r.stare=1;"; 
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        echo "Nu exista analize efectuate.<br>";
    } else {
        echo "<table>";
        echo "<tr>
                <th>Analiză</th>
                <th>Data rezultat</th>
                <th>Valoare</th>
                <th>Observații</th>
              </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Nume_analiza']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Data_rezultatului']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Valoare']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Observatii']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    ?>
<?php endif; ?>
<?php if ($tip === 'pacient'): ?>
<br>
<a href="raport.php" class="btn">Generare raport</a>
<?php endif; ?>
<?php if ($tip === 'medic' AND $_SESSION['optiune']): ?>
<br><br>
<a href="trimitere.php" class="btn">Trimite o proba la laborator</a>
<?php endif; ?>
<?php if ($tip === 'laborator'): ?>
    <?php
    $sql = "SELECT a.Nume_analiza, r.Nr_crt, r.Data_recoltarii
            FROM Rezultate r JOIN Analize a
            ON r.Nr_analiza=a.Nr_crt
            WHERE r.Stare=0 AND r.Laborator=(SELECT l.cod_laborator
                                            FROM Conturi c JOIN Laboratoare l
                                            ON c.Nr_crt=l.nr_crt
                                            WHERE c.user='".$_SESSION['user']."')
            ORDER BY Data_recoltarii;"; 
    $result = $conn->query($sql);
    ?>
    <?php if ($result && $result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Analize de lucrat</th>
                <th>Data recoltării</th>
                <th>Nr. proba</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr onclick="document.getElementById('form-<?= $row['Nr_crt'] ?>').submit();" style="cursor:pointer;">
                    <td><?= htmlspecialchars($row['Nume_analiza']) ?></td>
                    <td><?= htmlspecialchars($row['Data_recoltarii']) ?></td>
                    <td><?= htmlspecialchars($row['Nr_crt']) ?></td>
                    <form method="POST" action="adaugare_rezultat.php" id="form-<?= $row['Nr_crt'] ?>">
                        <input type="hidden" name="Nr_crt" value="<?= $row['Nr_crt'] ?>">
                    </form>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Nu există analize disponibile pentru a fi completate.</p>
<?php endif; ?>



<?php endif; ?>
</main>

</body>
</html>

<?php 
$conn->close();
?>
