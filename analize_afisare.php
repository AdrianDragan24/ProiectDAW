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

$categorie = $_GET['categorie'] ?? 'Toate';

$sql = "SELECT Nr_crt, Nume_analiza, Categorie, Pret FROM Analize";
if ($categorie !== 'Toate') {
    $sql .= " WHERE Categorie = '".$conn->real_escape_string($categorie)."'";
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Analize</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #fff5f5;
    margin: 0;
    padding: 20px;
}

h2 {
    color: #b30000;
    margin-bottom: 10px;
}

form {
    margin-bottom: 20px;
}

select {
    padding: 8px;
    font-size: 14px;
}

.container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

article {
    background: white;
    border: 1px solid #f2b3b3;
    border-radius: 10px;
    padding: 15px;
}

article h3 {
    margin: 0 0 8px 0;
    color: #b30000;
}

article p {
    margin: 4px 0;
}

.pret {
    font-weight: bold;
    color: #800000;
}

@media (max-width: 768px) {
    .container {
        grid-template-columns: 1fr;
    }
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

</style>
</head>

<?php $pagina_curenta = basename($_SERVER['PHP_SELF']); ?>
<nav>
    <a href="secret.php" class="<?= $pagina_curenta=='secret.php'?'active':'' ?>">Home</a>
    <a href="analize_afisare.php" class="<?= $pagina_curenta=='analize_afisare.php'?'active':'' ?>">Analize</a>
    <a href="cont.php" class="<?= $pagina_curenta=='cont.php'?'active':'' ?>">Cont</a>
    <a href="formular.php" class="<?= $pagina_curenta=='formular.php'?'active':'' ?>">Formular</a>
    <a href="logout.php">Logout</a>
</nav>


<body>

<h2>Lista analizelor</h2>

<form method="GET">
    <label>Categorie:</label>
    <select name="categorie" onchange="this.form.submit()">
    <option value="Toate" <?= ($categorie=='Toate')?'selected':'' ?>>Toate</option>
    <option value="biochimie" <?= ($categorie=='biochimie')?'selected':'' ?>>Biochimie</option>
    <option value="imunologie" <?= ($categorie=='imunologie')?'selected':'' ?>>Imunologie</option>
    <option value="dozare-medicamente" <?= ($categorie=='dozare-medicamente')?'selected':'' ?>>Dozare medicamente</option>
    <option value="genetica-moleculara" <?= ($categorie=='genetica-moleculara')?'selected':'' ?>>Genetică moleculară</option>
    <option value="hematologie" <?= ($categorie=='hematologie')?'selected':'' ?>>Hematologie</option>
    <option value="toxicologie" <?= ($categorie=='toxicologie')?'selected':'' ?>>Toxicologie</option>
    <option value="microbiologie" <?= ($categorie=='microbiologie')?'selected':'' ?>>Microbiologie</option>
    <option value="coagulare" <?= ($categorie=='coagulare')?'selected':'' ?>>Coagulare</option>
    <option value="biologie-moleculara" <?= ($categorie=='biologie-moleculara')?'selected':'' ?>>Biologie moleculară</option>
    <option value="parazitologie" <?= ($categorie=='parazitologie')?'selected':'' ?>>Parazitologie</option>
    <option value="detalii" <?= ($categorie=='detalii')?'selected':'' ?>>Detalii</option>
    <option value="alergologie" <?= ($categorie=='alergologie')?'selected':'' ?>>Alergologie</option>
    <option value="anatomie-patologica" <?= ($categorie=='anatomie-patologica')?'selected':'' ?>>Anatomie patologică</option>
    <option value="virusologie" <?= ($categorie=='virusologie')?'selected':'' ?>>Virusologie</option>
    <option value="imunohematologie" <?= ($categorie=='imunohematologie')?'selected':'' ?>>Imunohematologie</option>
    <option value="markeri-tumorali" <?= ($categorie=='markeri-tumorali')?'selected':'' ?>>Markeri tumorali</option>
    <option value="alergeni-recombinati-si-nativi" <?= ($categorie=='alergeni-recombinati-si-nativi')?'selected':'' ?>>Alergeni recombinați și nativi</option>
    <option value="alergologie-igg-specifice" <?= ($categorie=='alergologie-igg-specifice')?'selected':'' ?>>Alergologie IgG specifice</option>
    <option value="intoleranta-alimentara" <?= ($categorie=='intoleranta-alimentara')?'selected':'' ?>>Intoleranță alimentară</option>
</select>

</form>

<div class="container">
<?php
    while ($row = $result->fetch_assoc()) {
        echo "<article>";
        echo "<h3>".htmlspecialchars($row['Nr_crt']).". ".htmlspecialchars($row['Nume_analiza'])."</h3>";
        echo "<p>Categorie: ".htmlspecialchars($row['Categorie'])."</p>";
        echo "<p class='pret'>Preț: ".htmlspecialchars($row['Pret'])." lei</p>";
        echo "</article>";
    }
?>
</div>

</body>
</html>

<?php
$conn->close();
?>
