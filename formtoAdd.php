<?php
/*man kommer hit från inloggningsidan(logginAdmin.php) til en form med plats för att lägga till 
nya actor,film,tvshow. alla befintliga filmer o tvshow finns i en dropdown-lista som man kan koppla
till actor.härifrån redigeras man till addActorFilmTVShow.php där sker sql-operationerna för att 
lägga nya record i databasen.
Man kommer hit även efter registrering av ny info vilket sker på addActorFilmTvShow.php, 
eftersom admin brukar vija registrera många record.
om man kommit till sidan utan att vara inloggad,redigeras man till inloggningsidan.*/
session_start();
if (!isset($_SESSION['loggedin']) || (!$_SESSION['loggedin'])) {
    header("location:logginAdmin.php");
    exit;
}
if (($_SESSION['addactor']) && isset($_SESSION['actor'])) {
    $actor = $_SESSION['actor'];
    echo $actor . " har lagts till i actor-databasen.<br>";
}
else if (!($_SESSION['addactor']) && isset($_SESSION['actor'])) {
    $actor = $_SESSION['actor'];
    echo $actor . " har INTE lagts till i actor-databasen.<br>";
}
if (($_SESSION['addactorfilm'])) {
 echo "Ny record har lagts till actorfilm.<br>";
}
else if (!($_SESSION['addactorfilm'])) {
 echo "INGEN record har lagts till actorfilm.<br>";
}
if (($_SESSION['addactortvshow'])) {
 echo "Ny record har lagts till actortvshow.<br>";
}
else if (!($_SESSION['addactortvshow'])) {
 echo "INGEN record har lagts till actortvshow.<br>";
}
if (($_SESSION['addfilm']) && isset($_SESSION['film'])) {
    $film = $_SESSION['film'];
    echo $film . " har lagts till i film-databasen.<br>";
}
else if (!($_SESSION['addfilm']) && isset($_SESSION['film'])) {
    $film = $_SESSION['film'];
    echo $film . " har INTE lagts till i film-databasen.<br>";
}
if (($_SESSION['addtvshow']) && isset($_SESSION['tvshow'])) {
    $tvshow = $_SESSION['tvshow'];
    echo $tvshow . " har lagts till i tvshow-databasen.<br>";
}
else if (!($_SESSION['addtvshow']) && isset($_SESSION['tvshow'])) {
    $tvshow = $_SESSION['tvshow'];
    echo $tvshow . " har INTE lagts till i tvshow-databasen.<br>";
}
/*gör så att felmeddelande visas*/
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
<title>Form för att lägga till nya skådespelare, film, TV serier </title>
<script>
<!--
    function thisYear() {
        var idag = new Date();
        return parseInt(idag.getFullYear());
    }
    /*Namn består av bokstäver, siffror, ' och ". Födelseår/bortgång består av 4 siffror 
    eller tom och de kan inte vara större än årtalet för iår. Land består av bokstäver eller tom.*/
    function checkActor() {
        var str = /[a-zA-Z0-9"'åÅäÄöÖ]+$/;
        var str2 = /[\w.]+$/;
        var year = /\d{4}/;
        var name = document.form.nameactor.value;
        var birthYear = document.form.birth.value;
        var deathYear = document.form.dead.value;
        var country = document.form.country.value;
        var imageactor = document.form.imageactor.value;
        if (str.test(name) != 1) {
            alert('Fel inmatning namn');
            return false;
        }
        if (birthYear != "" && (year.test(birthYear) != 1 || parseInt(birthYear) > thisYear())) {
            alert('Fel inmatning födelseår');
            return false;
        }
        if (country != "" && str.test(country) != 1) {
            alert('Fel inmatning land');
            return false;
        }
        if (deathYear != "" && (year.test(deathYear) != 1 || parseInt(deathYear) > thisYear())) {
            alert('Fel inmatning bortgång');
            return false;
        }
        /*om man har angett födelseår, men inte dödsår, betyder det att hen lever, därför 0000*/
        if (birthYear != "" && deathYear == "") {
			document.form.dead.value = '0000';
        }
        if (imageactor != "" && str2.test(imageactor) != 1) {
            alert('Fel inmatning image');
            return false;
        }
    }
    /*om användaren trycker på rensa*/
    function rensaActor() {
        document.form.nameactor.value = "";
        document.form.birth.value = "";
        document.form.dead.value = "";
        document.form.country.value = "";
        document.form.imageactor.value = "";
        return false;
    }
    /*film-namn består of bokstäver/siffror. Release år kan inte vara större än iår.*/
    function checkFilm() {
        var str = /\w+$/;
        var str2 = /[\w.]+$/;
        var year = /\d{4}/;
        var name = document.form.namefilm.value;
        var releaseYear = document.form.releasefilm.value;
        var imagefilm = document.form.imagefilm.value;
        if (str.test(name) != 1) {
            alert('Fel inmatning filmnamn');
            return false;
        }
        if (releaseYear != "" && (year.test(releaseYear) != 1 || parseInt(releaseYear) > thisYear())) {
            alert('Fel inmatning utgivningsår');
            return false;
        }
        if (imagefilm != "" && str2.test(imagefilm) != 1) {
            alert('Fel inmatning image');
            return false;
        }
    }
    /*om användaren trycker på rensa*/
    function rensaFilm() {
        document.form.namefilm.value = "";
        document.form.releasefilm.value = "";
        document.form.imagefilm.value = "";
        return false;
    }
    /*TVserie-namn består of bokstäver/siffror. Releaseår och slutår kan inte vara större än iår.*/
    function checkTvShow() {
        var str = /\w+$/;
        var str2 = /[\w.]+$/;
        var year = /\d{4}/;
        var name = document.form.nametvshow.value;
        var releaseYear = document.form.releasetvshow.value;
        var endYear = document.form.endtvshow.value;
        var imagetvshow = document.form.imagetvshow.value;
        if (str.test(name) != 1) {
            alert('Fel inmatning TV-serie namn');
            return false;
        }
        if (releaseYear != "" && (year.test(releaseYear) != 1 || parseInt(releaseYear) > thisYear())) {
            alert('Fel inmatning utgivningsår');
            return false;
        }
        if (endYear != "" && (year.test(endYear) != 1 || parseInt(endYear) > thisYear())) {
            alert('Fel inmatning utgivningsår');
            return false;
        }
        if (imagetvshow != "" && str2.test(imagetvshow) != 1) {
            alert('Fel inmatning image');
            return false;
        }
    }
    /*om användaren trycker på rensa*/
    function rensaTvShow() {
        document.form.nametvshow.value = "";
        document.form.releasetvshow.value = "";
        document.form.endtvshow.value = "";
        document.form.imagetvshow.value = "";
        return false;
    }
-->
</script>
<noscript>
    Din webläsare kan inte hantera script
</noscript>
</head>
<body>
<!-- om man vill uppdatera en befintlig skådespelare, hittar man namnet på listan
	men datalist funkar inte i safari och internet explorer, bara i chrom-->
<h3 style="color:purple"> Lägg till skådespelare </h3>
	<form name="form" action="addActorFilmTVShow.php" method="POST">
    <table>
    	<tr>
            <td>Namn</td>
            <td> <input type="text" name="nameactor" maxlength="255" list="actors"> 
            <datalist id="actors">
<?php
			$sql = "SELECT * FROM actor ORDER BY name";
            try {
            	$result = $pdo->query($sql); 
            } catch(PDOException $ex) {
            	echo "PDOException";
            }
			if (!empty($result) && $result->rowCount() > 0) {
				foreach ($result as $row) {
					$name = $row['name'];
					echo "<option value='$name'>";
				}
            }    
?>      
            </datalist>
            </td>
        </tr>
        <tr>
            <td> Födelseår </td>
            <td><input type="text" name="birth" maxlength="4"></td>
        </tr>
        <tr>
            <td> Bortgång</td>
            <td><input type="text" name="dead" maxlength="4"> </td>
        </tr>
        <tr>
            <td> Födelseland</td>
            <td><input type="text" name="country" maxlength="255"> </td>
        </tr>
        <tr>
            <td> Image</td>
            <td><input type="text" name="imageactor" maxlength="255"> </td>
        </tr>
        <tr>
            <td> Kön</td>
            <td>
                <select name="sex">
                    <option value="">Select...</option>
                    <option value="man">Man</option>
                    <option value="kvinna">Kvinna</option>
                </select>
            </td>
        </tr>
        <tr>
            <td> Film</td>
            <td>
                <select name="ifilm">
                    <option value="">Select...</option>
<?php
                    $sql = "SELECT * FROM film ORDER BY name";
                    try {
                        $result = $pdo->query($sql); 
                    } catch(PDOException $ex) {
                        echo "PDOException";
                    }
                    if (!empty($result) && $result->rowCount() > 0) {
                        foreach ($result as $row) {
                            $filmname = $row['name'];
                            echo "<option value='$filmname'>$filmname</option>";
                        }
                    }            
?>                    
                </select>
            </td>
        </tr>
        <tr>
            <td> TV-serie</td>
            <td>
                <select name="itvshow">
                    <option value="">Select...</option>
<?php
                    $sql = "SELECT * FROM tvshow ORDER BY name";
                    try {
                        $result = $pdo->query($sql); 
                    } catch(PDOException $ex) {
                        echo "PDOException";
                    }
                    if (!empty($result) && $result->rowCount() > 0) {
                        foreach ($result as $row) {
                            $tvshowname = $row['name'];
                            echo "<option value='$tvshowname'>$tvshowname</option>";
                        }
                    }            
?>                    
                </select>
            </td>
        </tr>
        <tr><td><br></td></tr>
        <tr>
            <td><input type="submit" value="Lägg till/Uppdatera" name="submitActor" onClick="return checkActor();"></td>
            <td> <input type="Submit" value="Rensa" onClick="return rensaActor();"></td>
        </tr>
    </table> 
    <br>
<h3 style="color:blue"> Lägg till film </h3>
    <table>
    	<tr>
            <td>Namn</td>
            <td> <input type="text" name="namefilm" maxlength="255"> </td>
        </tr>
        <tr>
            <td> Utgivningsår </td>
            <td><input type="text" name="releasefilm" maxlength="4"></td>
        </tr>
        <tr>
            <td>Image</td>
            <td> <input type="text" name="imagefilm" maxlength="255"> </td>
        </tr>
        <tr><td><br></td></tr>
        <tr>
            <td> <input type="submit" value="Lägg till" name="submitFilm" onClick="return checkFilm();"></td>
            <td> <input type="Submit" value="Rensa" onClick="return rensaFilm();"></td>
        </tr>
    </table> 
    <br>
<h3 style="color:purple"> Lägg till tv serie </h3>
    <table>
    	<tr>
            <td>Namn</td>
            <td> <input type="text" name="nametvshow" maxlength="255"> </td>
        </tr>
        <tr>
            <td> Utgivningsår </td>
            <td><input type="text" name="releasetvshow" maxlength="4"></td>
        </tr>
        <tr>
            <td> Sista år </td>
            <td><input type="text" name="endtvshow" maxlength="4"></td>
        </tr>
        <tr>
            <td>Image</td>
            <td> <input type="text" name="imagetvshow" maxlength="255"> </td>
        </tr>
        <tr><td><br></td></tr>
        <tr>
            <td> <input type="submit" value="Lägg till" name="submitTvshow" onClick="return checkTvShow();"></td>
            <td> <input type="Submit" value="Rensa" onClick="return rensaTvShow();"></td>
        </tr>
    </table> 
    </form>
</body>
</html>