<?php
session_start();
$servername = "localhost";
$username = "adragans_proiect";
$password = "Parola.123";
$dbname = "adragans_proiect";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$sql="SELECT Nr_Crt, CNP
        FROM Conturi
        WHERE User='".$_SESSION['user']."';";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$cnp = $row['CNP'];
$id = $row['Nr_Crt'];

$sql="SELECT CONCAT(c.Nume, ' ', c.Prenume) AS Medic, m.specializare
        FROM Conturi c JOIN Pacient_Medic pm
        ON pm.Medic=c.Nr_crt
        JOIN Medici m ON c.Nr_crt=m.nr_crt
        WHERE pm.Pacient = ".$id.";";

$result = $conn->query($sql);
if($result->num_rows == 0)
{
    $medic="Nu exista asociat un medic.";
}
else{
$row = $result->fetch_assoc();
$medic=$row['Medic'];
$specializare=$row['specializare'];
}

$sql="SELECT a.Nume_analiza, IFNULL(r.Valoare, 'in lucru') AS Valoare, IFNULL(r.Observatii, '-') AS Observatii, r.Data_recoltarii, r.Laborator
    FROM Rezultate r JOIN Analize a
    ON r.Nr_analiza=a.Nr_crt
    WHERE Nr_pacient=".$id.";";

$result = $conn->query($sql);
$cap=['Nume analiza', 'Valoare', 'Observatii', 'Data recoltarii', 'Laborator'];
$date_tabel=[];
$i=0;
while($row = $result->fetch_assoc())
{
    $date_tabel[$i]=[$row['Nume_analiza'], $row['Valoare'], $row['Observatii'], $row['Data_recoltarii'], $row['Laborator']];
    $i++;
}
$conn->close();

require('fpdf/fpdf.php');

function utf8_to_cp1250($text) {
    return iconv('UTF-8','CP1250//TRANSLIT//IGNORE',$text);
}

class PDF extends FPDF
{
function __construct() {
    parent::__construct();
    $this->AddFont('DejaVuSans','','DejaVuSans.php');
}

function Header()
{
    $this->Image("ciupercaluifertig.jpeg",10,6,30);
    $this->SetFont('Arial','B',16);
    $this->Cell(70);
    $this->Cell(50,10,'Raport analize',1,0,'C');
    $this->Ln(30);
}

function Footer()
{
    $this->SetY(-15);
    $this->SetFont('Arial','I',8);
    $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,1,'C');
    $this->Cell(0,0, 'Ciuperca lui Fertig - Analize medicale', 0, 0, 'C');
}

function NbLines($w, $txt)
{
    $cw = &$this->CurrentFont['cw'];
    if($w == 0) $w = $this->w - $this->rMargin - $this->x;
    $wmax = ($w - 2*$this->cMargin) * 1000 / $this->FontSize;
    $s = str_replace("\r", '', $txt);
    $nb = strlen($s);
    if($nb > 0 && $s[$nb-1] == "\n") $nb--;
    $sep = -1;
    $i = 0; $j = 0; $l = 0; $nl = 1;
    while ($i < $nb) {
        $c = $s[$i];
        if ($c == "\n") { $i++; $sep=-1; $j=$i; $l=0; $nl++; continue; }
        if ($c == ' ') $sep=$i;
        $l += $cw[$c];
        if ($l > $wmax) {
            if ($sep == -1) {
                if ($i == $j) $i++;
            } else $i = $sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        } else $i++;
    }
    return $nl;
}

function FancyTable($header, $data)
{
    $w = array(60, 40, 40, 30, 20);
    $this->SetFillColor(255,0,0);
    $this->SetTextColor(255);
    $this->SetDrawColor(128,0,0);
    $this->SetLineWidth(.3);
    $this->SetFont('DejaVuSans','');

    for($i=0;$i<count($header);$i++)
        $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
    $this->Ln();
    
    $this->SetFillColor(224,235,255);
    $this->SetTextColor(0);
    $this->SetFont('');

    $fill = false;
    $lineHeight = 8;

    foreach($data as $row) {
        $x = $this->GetX();
        $y = $this->GetY();

        $nb1 = $this->NbLines($w[0], $row[0]);
        $nb2 = $this->NbLines($w[1], $row[1]);
        $nb3 = $this->NbLines($w[2], $row[2]);
        $maxLines = max($nb1, $nb2, $nb3);
        $rowHeight = $maxLines * $lineHeight;

        $this->MultiCell($w[0], $rowHeight/$nb1, $row[0], 'LR', 'L', $fill);
        $this->SetXY($x + $w[0], $y);
        $this->MultiCell($w[1], $rowHeight/$nb2, $row[1], 'LR', 'C', $fill);
        $this->SetXY($x + $w[0] + $w[1], $y);
        $this->MultiCell($w[2], $rowHeight/$nb3, $row[2], 'LR', 'L', $fill);
        $this->SetXY($x + $w[0] + $w[1] + $w[2], $y);
        $this->Cell($w[3], $rowHeight, $row[3], 'LR', 0, 'C', $fill);
        $this->Cell($w[4], $rowHeight, $row[4], 'LR', 1, 'C', $fill);
        $fill = !$fill;
    }
    $this->Cell(array_sum($w),0,'','T');
}
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$y = $pdf->GetY();
$pdf->SetFont('Times','B',12);
$pdf->Cell(14,7,utf8_to_cp1250("Nume:"), 0,0,'L');

$pdf->SetFont('DejaVuSans','',12);
$pdf->Cell(0,7,utf8_to_cp1250($_SESSION['nume']),0,1,'L');

$pdf->SetFont('Times','B',12);
$pdf->Cell(19,7,utf8_to_cp1250("Prenume:"), 0,0,'L');

$pdf->SetFont('DejaVuSans','',12);
$pdf->Cell(0,7,utf8_to_cp1250($_SESSION['prenume']),0,1,'L');

$pdf->SetFont('Times','B',12);
$pdf->Cell(12,7,utf8_to_cp1250("CNP:"), 0,0,'L');

$pdf->SetFont('DejaVuSans','',12);
$pdf->Cell(0,7,utf8_to_cp1250($cnp),0,1,'L');

$pdf->SetY($y);
$pdf->SetX(95);
if($medic == 'Nu exista asociat un medic.')
{
    $pdf->SetFont('DejaVuSans','',12);
    $pdf->Cell(27,7,utf8_to_cp1250($medic), 0,0,'L');
}
else
{
    $pdf->SetFont('Times','B',12);
    $pdf->Cell(27,7,utf8_to_cp1250("Medic curant:"), 0,0,'L');

    $pdf->SetFont('DejaVuSans','',12);
    $pdf->Cell(0,7,utf8_to_cp1250($medic),0,1,'L');

    $pdf->SetX(95);

    $pdf->SetFont('Times','B',12);
    $pdf->Cell(26,7,utf8_to_cp1250("Specializare:"), 0,0,'L');

    $pdf->SetFont('DejaVuSans','',12);
    $pdf->Cell(0,7,utf8_to_cp1250($specializare),0,1,'L');
}

$pdf->Ln(15);

$cap_conv = array_map('utf8_to_cp1250',$cap);
$date_conv = [];
foreach($date_tabel as $r) {
    $date_conv[] = array_map('utf8_to_cp1250',$r);
}
$pdf->FancyTable($cap_conv, $date_conv);

$pdf->Output();
?>
