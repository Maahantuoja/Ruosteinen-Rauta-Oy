<!-- http://omena.winnova.fi/phpmyadmin/index.php -->

<?php
    // Tallennetaan tiedot muuttujiin
    $a = $_REQUEST['nimi'] ?? "Tieto puuttuu";
    $b = $_REQUEST['puh'] ?? "Tieto puuttuu";
    $c = $_REQUEST['email'] ?? "Tieto puuttuu";
    $d = $_REQUEST['viesti'] ?? "Tieto puuttuu";

    // Tulostetaan vastaanotetut tiedot näkyville
    echo "<h2>Lähetetty viesti (ei vielä tallennettu tietokantaan)</h2>";
    echo "Nimi: " . $a;
    echo "<br>Puh: " . $b;
    echo "<br>Email: " . $c;
    echo "<br>Viesti: " . $d;
    echo "<br>";

    // Avataan sql yhteys
    $servername = "localhost";
    $username = "A53105_user";
    $password = "P13n1&&R3nk1";
    $dbname = "A53105_DB";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<br>Connected successfully<br>";

        // Valmistellaan muuttujat
        $stmt = $conn->prepare("INSERT INTO palaute (nimi, puh, email, viesti) 
                                VALUES (:nimi, :puh, :email, :viesti)");
        $stmt->bindParam(':nimi', $a);
        $stmt->bindParam(':puh', $b);
        $stmt->bindParam(':email', $c);
        $stmt->bindParam(':viesti', $d);

        // Tallennetaan tiedot
        $stmt->execute();

        echo "New records created successfully";
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    $conn = null;
?>
