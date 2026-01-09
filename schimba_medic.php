<?php
session_start();

if (!isset($_SESSION['logat']) || $_SESSION['logat'] !==true || $_SESSION['tip']!=='pacient') {
    header('Location: logout.php');
    exit;
}

$id_medic = $_POST['optiune'] ?? 0;

$tip = $_SESSION['tip'];
$servername = "localhost";
$username = "adragans_proiect";
$password = "Parola.123";
$dbname = "adragans_proiect";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}
if(!$id_medic){
$sql="SELECT CONCAT(c.Nume, ' ', c.Prenume, '   parafa: ', m.cod_parafa) AS Medic, c.Nr_crt AS id_medic
        FROM Conturi c JOIN Medici m
        ON c.Nr_crt=m.nr_crt
        ORDER BY c.Nume";
$result = $conn->query($sql);}
else
{
$conn->query("DELETE FROM Pacient_Medic WHERE Pacient=".$_SESSION['id'].";");
$conn->query("INSERT INTO Pacient_Medic (Pacient, Medic) VALUES (".$_SESSION['id'].", ".$id_medic.");");
$conn->close;
header('Location: cont.php');
exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Alege medic</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
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
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 12px;
            color: #b30000;
            font-size: 16px;
        }

        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
            box-sizing: border-box;
        }

        button {
            background-color: #b30000;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s;
        }

        button:hover {
            background-color: #c84747;
        }
    </style>
</head>
<body>

<div class="container">
    <form method="POST">
        <label for="optiune">Alege o opțiune:</label>
        <select name="optiune" id="optiune" required>
            <option value="">-- Selectează --</option>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="'.htmlspecialchars($row['id_medic']).'">'.htmlspecialchars($row['Medic']).'</option>';
                }
            } else {
                echo '<option value="">Nu există opțiuni</option>';
            }
            ?>
        </select>
        <button type="submit">Alege medicul</button>
    </form>
</div>

</body>
</html>
