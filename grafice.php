<?php

    if ($_GET['oras'] == 123) {
    header('Content-Type: image/png');

    $conn = new mysqli("localhost", "adragans_proiect", "Parola.123", "adragans_proiect");
    if ($conn->connect_error) die("Conexiune esuata: " . $conn->connect_error);

    require 'jpgraph/src/jpgraph.php';
    require 'jpgraph/src/jpgraph_pie.php';
    require 'jpgraph/src/jpgraph_pie3d.php';

    $sql = "SELECT Tara, Oras, COUNT(IP) AS ips
            FROM Statistici_logare
            GROUP BY Tara, Oras;";
    $result = $conn->query($sql);
    $conn->close();

    $orase = [];
    $incercari = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $orase[] =  $row['Oras'] . " (". $row['Tara'] . ")";
            $incercari[] = intval($row['ips']);
        }
    }

    $graph = new PieGraph(480, 400);
    $graph->SetShadow();
    $graph->SetTheme(new VividTheme);

    $p1 = new PiePlot3D($incercari);
    $p1->SetCenter(0.5, 0.45);
    $p1->SetLegends($orase);

    foreach ($incercari as $i => $val) $p1->ExplodeSlice($i);

    $graph->legend->SetPos(0.5, 0.99, 'center', 'bottom');
    $graph->legend->SetColumns(2);
    $graph->Add($p1);
    $graph->Stroke();

    exit;
}

    if ($_GET['zi'] == 123) {
    header('Content-Type: image/png');

    $conn = new mysqli("localhost", "adragans_proiect", "Parola.123", "adragans_proiect");
    if ($conn->connect_error) die("Conexiune esuata: " . $conn->connect_error);

    require 'jpgraph/src/jpgraph.php';
    require 'jpgraph/src/jpgraph_bar.php';

    $zile = [];
    $incercari = [];

    for ($i = 6; $i >= 0; $i--) {
        $data = date('Y-m-d', strtotime("-$i days"));
        $zile[] = ($i==0) ? 'Azi' : (($i==1) ? 'Ieri' : "Acum $i zile");

        $sql = "SELECT COUNT(IP) AS ips FROM Statistici_logare WHERE DATE(Data) = '$data'";
        $row = $conn->query($sql)->fetch_assoc();
        $incercari[] = intval($row['ips']);
    }
    $conn->close();

    $graph = new Graph(480, 400, 'auto');
    $graph->SetScale('textlin'); // axa X = text, axa Y = numerică
    $graph->SetShadow();

    $graph->xaxis->SetTickLabels($zile);
    $graph->xaxis->SetLabelAngle(45);

    $barplot = new BarPlot($incercari);
    $barplot->value->SetFont(FF_FONT1, FS_BOLD);

    $graph->Add($barplot);
    $graph->Stroke();
    exit;
}


    if ($_GET['suc'] == 123) {
    header('Content-Type: image/png');

    // Conexiune DB
    $conn = new mysqli("localhost", "adragans_proiect", "Parola.123", "adragans_proiect");
    if ($conn->connect_error) die("Conexiune esuata: " . $conn->connect_error);

    require 'jpgraph/src/jpgraph.php';
    require 'jpgraph/src/jpgraph_pie.php';

    // Preluare date
    $sql = "SELECT COUNT(IP) AS ips, Succes FROM Statistici_logare GROUP BY Succes";
    $result = $conn->query($sql);
    if(!$result) die("Eroare SQL: " . $conn->error);
    $conn->close();

    $succes = [];
    $incercari = [];

    // Construiește legenda și valorile
    while ($row = $result->fetch_assoc()) {
        if(intval($row['Succes']) === 0){
            $succes[] = "Esec";
        } else {
            $succes[] = "Succes";
        }
        $incercari[] = intval($row['ips']);
    }

    // Completează cu 0 dacă lipsește vreo categorie
    if(!in_array("Succes", $succes)){ $succes[]="Succes"; $incercari[]=0; }
    if(!in_array("Esec", $succes)){ $succes[]="Esec"; $incercari[]=0; }

    // Creare grafic
    $graph = new PieGraph(400,300);
    $graph->SetShadow();

    $p1 = new PiePlot($incercari);
    $p1->SetLegends($succes);
    $p1->SetCenter(0.5,0.5);
    $p1->SetLabelType(PIE_VALUE_PER); // procente pe felii
    $p1->SetSliceColors(['#FF0000','#00AA00']); // Esec rosu, Succes verde

    $graph->Add($p1);
    $graph->Stroke();
    exit;
}

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Statistici conectare</title>
    <style>
        body { font-family: Arial; text-align: center; margin: 50px; }
        h2 { color: #333; margin-bottom: 10px; }
        .grafice-container {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
        }
        .grafice-item {
            text-align: center;
        }
        .grafice-item img {
            border: 1px solid #ccc;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>

<div class="grafice-container">
    <div class="grafice-item">
        <h2>Incercari de conectare pe orase</h2>
        <img src="?oras=123" alt="Grafic_orase">
    </div>

    <div class="grafice-item">
        <h2>Incercari de conectare in ultima saptamana</h2>
        <img src="?zi=123" alt="Grafic_zile">
    </div>

    <div class="grafice-item">
        <h2>Rata de succes</h2>
        <img src="?suc=123" alt="Rata_succes">
    </div>
</div>

</body>
</html>
