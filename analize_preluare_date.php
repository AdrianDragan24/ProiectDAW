<?php

// Datele de conectare la baza de date
$servername = "localhost";   // sau adresa serverului tău
$username = "adragans_proiect";          // numele de utilizator MySQL
$password = "Parola.123";              // parola utilizatorului MySQL
$dbname = "adragans_proiect";           // numele bazei de date

// Creăm conexiunea
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificăm conexiunea
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

// Inițializează sesiunea cURL
$ch = curl_init();

$url = 'https://bioclinica.ro/analize'; 

// Setează opțiunile pentru cURL
curl_setopt($ch, CURLOPT_URL, $url); // URL-ul
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Returnează rezultatul ca string
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Urmează redirecționările
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36 Firefox/89.0");


//Aflu nr de pagini
$content = curl_exec($ch);
if ($content === FALSE) {
    echo "cURL a eșuat: " . curl_error($ch);
} else {
    $nr_pag_brut=explode('<a class="p-4" href="/analize?page=', $content);
    $nr_pag=explode('"', $nr_pag_brut[count($nr_pag_brut)-1]);
}

//Pentru fiecare pagina iau datele
for($a=1; $a<=(int)$nr_pag[0]; $a++)
{
//Schimbare URL
$url = 'https://bioclinica.ro/analize?page='.$a; 

curl_setopt($ch, CURLOPT_URL, $url); // URL-ul

// Execută cererea și obține răspunsul
$content = curl_exec($ch);


if ($content === FALSE) {
    echo "cURL a eșuat: " . curl_error($ch);
} else {

    $nr_ana=explode('<div class="flex-1"><a href="/analize/', $content);

    for ($x = 1; $x < count($nr_ana); $x++) { 

    $categorie=explode('/', $nr_ana[$x]);
    $nume_brut=explode('>', $nr_ana[$x]);
    $nume=explode('<', $nume_brut[1]);
    $pret_brut=explode('<div>', $nr_ana[$x]);
    $pret=explode('<', $pret_brut[1]);


    $sql = "INSERT into Analize (Nume_analiza, Categorie, Pret) VALUES ('".$nume[0]."', '". $categorie[0]."', ".$pret[0].");";
    $conn->query($sql);
    }
}
}
// Închide sesiunea cURL si la BD
curl_close($ch);
$conn->close();
?>