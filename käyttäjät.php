<!-- 
    Käyttäjät ja salasanat:
    Nuuskis@muumilaakso.fi, Muumilaakso
    Pelle.Hermanni@sirkus.hepokatti.fi, Änkeröinen
-->

<?php
    // Haetaan käyttäjän syöte lomakkeelta
    $a = $_REQUEST['kayttajanimi'] ?? "Tieto puuttuu";
    $b = $_REQUEST['salasana'] ?? "Tieto puuttuu";
    $c = password_hash($b, PASSWORD_ARGON2I, ['memory_cost' => 2048, 'time_cost' => 4, 'threads' => 3]);

    // Testi tulostus:
    echo "<h3>Annetut tiedot:</h3>";
    echo "Käyttäjänimi: " . $a . "<br>";
    echo "Salasana: " . $b . "<br>";
    echo "Hashatty salasana: " . $c . "<br>";
    echo "<br>";

    // Yhdistetään tietokantaan
    $servername = "localhost";
    $username = "A53105_user";
    $password = "P13n1&&R3nk1";
    $dbname = "A53105_DB";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected successfully<br>";

        // mysqli_real_escape_string vs prepared statements
        /*
            Käytä mysqli_real_escape_string tai prepared statements syötteiden turvallisuuden takaamiseksi.
            
            Prepared statements on turvallisempi. Prepared statements estää SQL-injektiohyökkäyksiä tehokkaammin kuin 
            mysqli_real_escape_string, koska käyttäjän syöte käsitellään erillisenä parametrina eikä suoraan kyselytekstin 
            osana. Tämä tekee SQL-injektiohyökkäysten suorittamisen käytännössä mahdottomaksi.

            Vanhat kysely:
            $sql = "SELECT salasana FROM kayttajat WHERE kayttajanimi = '$a'";
            $result = $conn->query($sql);

            $stmt = $conn->prepare("SELECT salasana FROM kayttajat WHERE kayttajanimi");
            $stmt->bind_param("s", $a);
            $stmt->execute();
        */

        // Luodaan prepared statement ja liitetään käyttäjän syöte statementtiin
        $stmt = $conn->prepare("SELECT salasana FROM kayttajat WHERE kayttajanimi = :kayttajanimi");
        $stmt->bindParam(':kayttajanimi', $a, PDO::PARAM_STR);
        $stmt->execute(); // Suoritetaan kysely

        // Otetaan tulokset talteen
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Tarkistetaan, että kysely onnistui
        if (!$result) {
            die("Kyselyssä tapahtui virhe: " . $conn->errorInfo()[2]);
        }

        // password_needs_rehash käyttö?
        /*
            // Luetaan tulos ja tallennetaan se PHP-muuttujaan
            $column_value = $result['salasana'];

            // Tarkistetaan, onko tallennettu salasana turvallinen
            if (password_needs_rehash($column_value, PASSWORD_DEFAULT)) {
                // Päivitetään tallennettu salasana uudelleen
                $new_hashed_password = password_hash($b, PASSWORD_DEFAULT);
                // tallennetaan $new_hashed_password tietokantaan
            }
        */

        // Vahvistetaan salasana
        if (password_verify($b, $result["salasana"])) {
            echo 'Password is valid!' . '<br>';
            echo '<h3>Kirjaudutaan sisään!</h3>';
        } else {
            echo 'Invalid password.';
        }

    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Suljetaan tietokantayhteys
    $conn = null;
?>
