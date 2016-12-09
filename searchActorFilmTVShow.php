<?php
/*Här kan alla komma för att söka efter info om skådespelare, film, tvserier. Man behöver
ingen inloggning eller lösenord.*/
error_reporting(E_ALL);
ini_set("display_errors", 1);
/*upprätta kontakt mellan php-sidan och databasen*/
include $_SERVER['DOCUMENT_ROOT'].'/ag1248/me105a/connect.php';
//$pdo = new PDO('mysql:host=localhost;dbname=databasbaserad_programering;charset=utf8', 'root', '');
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Search</title>
<script>
<!--
    /*man måste skriva något (bestående av bokstäver, siffror och ' och " i searchboxen innan man söker.*/
    function checkValues() {
        var str = /[\w"'åÅäÄöÖ]+$/;
        var text = document.form.search.value;
        if (str.test(text) != 1) {
            alert('Fel inmatning');
            return false;
        }
    }
-->
</script>
<noscript>
    Din webläsare kan inte hantera script
</noscript>
</head>
<body>
    <h3>Sökning efter skådespelare, film, TV serier</h3>
    <form name="form" method="post" action="">
        <table>
            <tr>
                <td><input type="text" name="search"><br></td>
                <td>
                <select name="choice">
                	<option value="">Select...</option>
                    <option value="actor">Skådespelare(namn, land)</option>
                    <option value="film">Film(namn)</option>
                    <option value="tvshow">TV serie(namn)</option>
                </select>
                </td>
            </tr>
            <tr>
                <td><input type="submit" name="submitsearch" value="sök" onClick="return checkValues();"></td>
            </tr>
        </table>
    </form>
<?php
/*om man vill söka efter skådespelare kollar man i namn och land. för varje resultat
hittar man (förutom namn)image,födelseår och bortgång om de finns. Sedan kollar man i actorfilm och 
actortvshow för att se om man hittar filmer/tvserier som skåespelaren har spelat i.*/
    if(isset($_POST['submitsearch']) && $_POST['choice']=="actor") {
        $search = $_POST['search'];
        $sql = "SELECT * FROM actor WHERE name LIKE '%$search%' or 
                countryofbirth LIKE '%$search%' ORDER BY name";
        try {
            $result = $pdo->query($sql); 
        } catch(PDOException $ex) {
            echo "PDOException";
        }
        if (!empty($result) && $result->rowCount() > 0) {
            foreach ($result as $row) {
                $filmExist = false;
                $name = $row['name'];
                $birth = $row['birth'];
                $death = $row['death'];
                $sex = $row['sex'];
                if ($sex != "") {
                    $hen = $sex=="man" ? "Han" : "Hon";
                }
                else    
                    $hen = "Hen";
                $image = $row['image'];
                $countryofbirth = $row['countryofbirth'];
                if ($image != "") {
                    //echo '<img src="http://localhost/databasbaserad_programmering/projekt/images/'.$image . '"/>';
					echo '<img src="images/'.$image.'">';
                }
                echo "<br>" . $name;
                if ($birth != "") {
                    echo " är född " . $birth; 
                    if ($countryofbirth != "")
                        echo " i " . $countryofbirth . ".";
                }
                if ($death != 0000) {
                        echo " " . $hen . " har gått bort " . $death . ".";
                }
                $sql = "SELECT * FROM actor 
                        INNER JOIN actorfilm 
                        ON actor.name='$name' AND actor.id=actorfilm.actor_id 
                        INNER JOIN film 
                        ON film.id=actorfilm.film_id";
                try {
                    $result = $pdo->query($sql); 
                } catch(PDOException $ex) {
                    echo "PDOException";
                }
                if (!empty($result) && $result->rowCount() > 0) {
                    $filmExist = true;
                    $nr = $result->rowCount();
                    echo " " . $hen . " har spelat i ";
                    foreach ($result as $row) {
                        echo $row['name'];
                        $nr -=1;
                        if ($nr > 1)
                            echo " , ";
                        elseif ($nr == 1)   
                            echo " och ";
                        else 
                            echo ".";
                    }
                }
                $sql = "SELECT * FROM actor 
                INNER JOIN actortvshow 
                ON actor.name='$name' AND actor.id=actortvshow.actor_id 
                INNER JOIN tvshow 
                ON tvshow.id=actortvshow.tvshow_id";
                try {
                    $result = $pdo->query($sql); 
                } catch(PDOException $ex) {
                    echo "PDOException";
                }
                if (!empty($result) && $result->rowCount() > 0) {
                    $nr = $result->rowCount();
                    $also = "";
                    if ($filmExist)
                        $also = " även";
                    echo " " . $hen . " har" . $also . " spelat i ";
                    foreach ($result as $row) {
                        echo $row['name'];
                        $nr -=1;
                        if ($nr > 1)
                            echo " , ";
                        elseif ($nr > 0)   
                            echo " och ";
                        else 
                            echo ".";
                    }
                }
			echo "<br><br>";
            }
        }
        else {
            echo "Inga resultat funna.";
        }
    }
/*om man vill söka efter film kollar man efter namn. för varje resultat
hittar man (förutom namn) image och releaseår om de finns. Sedan kollar man i actorfilm 
för att se om man hittar vilka skåespelaren som har spelat i filmen.*/
    if(isset($_POST['submitsearch']) && $_POST['choice']=="film") {
        $search = $_POST['search'];
        $sql = "SELECT * FROM film WHERE name LIKE '%$search%' ORDER BY name";
        try {
            $result = $pdo->query($sql); 
        } catch(PDOException $ex) {
            echo "PDOException";
        }
        if (!empty($result) && $result->rowCount() > 0) {
            foreach ($result as $row) {
                $name = $row['name'];
                $yearRelease = $row['yearRelease'];
                $image = $row['image'];
                if ($image != "") {
                    //echo '<img src="http://localhost/databasbaserad_programmering/projekt/images/'.$image . '" height="200" width="200"/>';
					echo '<img src="images/'.$image.'">';
                }
                echo "<br>" . $name;
                if ($yearRelease != "") {
                    echo " var utgiven " . $yearRelease . ". "; 
                }
                $sql = "SELECT * FROM film 
                        INNER JOIN actorfilm 
                        ON film.name='$name' AND film.id=actorfilm.film_id 
                        INNER JOIN actor 
                        ON actor.id=actorfilm.actor_id";
                try {
                    $result = $pdo->query($sql); 
                } catch(PDOException $ex) {
                    echo "PDOException";
                }
                if (!empty($result) && $result->rowCount() > 0) {
                    $nr = $result->rowCount();
                    foreach ($result as $row) {
                        echo $row['name'];
                        $nr -=1;
                        if ($nr > 1)
                            echo ", ";
                        elseif ($nr == 1)   
                            echo "och ";
                    }
                    echo " har spelat i filmen.";
                }
				echo "<br><br>";
            }
        }
        else {
            echo "Inga resultat funna.";
        }
    }
/*om man vill söka efter tvshow kollar man efter namn. för varje resultat
hittar man (förutom namn) image, releaseår och slutår om de finns. Sedan kollar man i 
actorfilm och actortvshow för att se om man hittar vilka skåespelaren som har spelat i tvshowen.*/
    if(isset($_POST['submitsearch']) && $_POST['choice']=="tvshow") {
        $search = $_POST['search'];
        $sql = "SELECT * FROM tvshow WHERE name LIKE '%$search%' ORDER BY name";
        try {
            $result = $pdo->query($sql); 
        } catch(PDOException $ex) {
            echo "PDOException";
        }
        if (!empty($result) && $result->rowCount() > 0) {
            foreach ($result as $row) {
                $tmp= ".";
                $name = $row['name'];
                $yearRelease = $row['yearRelease'];
                $yearEnd = $row['yearEnd'];
                if ($yearRelease != "" && $yearEnd != "") {
                    $tmp = " och";
                }
                $image = $row['image'];
                if ($image != "") {
                    //echo '<img src="http://localhost/databasbaserad_programmering/projekt/images/'.$image . '" height="200" width="200"/>';
					echo '<img src="images/'.$image.'">';
                }
                echo "<br>" . $name;
                if ($yearRelease != "") {
                    echo " var utgiven " . $yearRelease . $tmp; 
                }
                if ($yearEnd != "") {
                    echo " slutade visas " . $yearEnd . ". "; 
                }
                $sql = "SELECT * FROM tvshow 
                        INNER JOIN actortvshow 
                        ON tvshow.name='$name' AND tvshow.id=actortvshow.tvshow_id 
                        INNER JOIN actor 
                        ON actor.id=actortvshow.actor_id";
                try {
                    $result = $pdo->query($sql); 
                } catch(PDOException $ex) {
                    echo "PDOException";
                }
                if (!empty($result) && $result->rowCount() > 0) {
                    $nr = $result->rowCount();
                    foreach ($result as $row) {
                        echo $row['name'];
                        $nr -=1;
                        if ($nr > 1)
                            echo ", ";
                        elseif ($nr == 1)   
                            echo " och ";
                    }
                    echo " har spelat i TVserien.";
                }
				echo "<br><br>";
            }
        }
        else {
            echo "Inga result";
        }
    }
?>
</body>
</html>