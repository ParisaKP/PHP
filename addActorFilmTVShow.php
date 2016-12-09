<?php
header("location:formtoAdd.php");
/*när admin registrerar ny info, vill man fortsätta med nya, så man ska tillbaka till
formtoAdd.php hela tiden, men info om registreringen har gått bra eller ej sänds i session.*/
session_start();
/*Man kommer hit från formen (formtoAdd.php).sql operationerna för att lägga till nya 
skådespelare, film, tvshow görs här. htmlspecialchars() används till de string-värden som 
hämtas so "makes sure any characters that are special in html are 
properly encoded so people can't inject HTML tags or Javascript into your page". 
De värde som inte är ifyllda t.ex. land, måste ändras till "" eller NULL (när det är year)*/
/*gör så att felmeddelande visas*/
error_reporting(E_ALL);
ini_set("display_errors", 1);
/*upprätta kontakt mellan php-sidan och databasen*/
include $_SERVER['DOCUMENT_ROOT'].'/ag1248/me105a/connect.php';
//$pdo = new PDO('mysql:host=localhost;dbname=databasbaserad_programering;charset=utf8', 'root', '');
/*en funktion för att få fram actor id när man har namnet på actor*/
$_SESSION['addactor']=false;
$_SESSION['addactorfilm']=false;
$_SESSION['addactortvshow']=false;
$_SESSION['addtvshow']=false;
$_SESSION['addfilm']=false;
function actorid($nameactor) {
    $sql = "SELECT id FROM actor WHERE actor.name='$nameactor'";
    global $pdo;
    try {
        $result = $pdo->query($sql); 
    } catch(PDOException $ex) {
        echo "PDOException";
    }
    /*vi vet att resultatet innehåller bara en rad*/
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $actorid = $row['id'];
    return $actorid;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title> Lägga till nya skådespelare, film, TV serie i databasen </title>  
</head>
<body>
<?php
    if(isset($_POST['submitActor'])) {
		$sinsert = "(name, ";
		$svalues = "(:nameactor, ";
		/*man har även namn = Values.. för att även om man vill registrera bara namn på en actor, ON DUPLICATE UPDATE ska funka.*/
		$sonduplicate = "name = VALUES(name), ";
		$bindvaluebirth = false;
		$bindvaluedeath = false;
		$bindvaluesex = false;
		$bindvaluecountry = false;
		$bindvalueimage = false;
		$nameactor = addslashes($_POST['nameactor']);
				
        $birth = strlen($_POST['birth']) > 0 ? $_POST['birth'] : NULL;
		if ($birth != NULL) {
			$bindvaluebirth = true;
			$sinsert = $sinsert."birth, ";
			$svalues = $svalues.":birth, ";
			$sonduplicate = $sonduplicate."birth = VALUES(birth), ";
		}
		
        $death = strlen($_POST['dead']) > 0 && $_POST['dead'] != '0000' ? $_POST['dead'] : NULL;
		if ($death != NULL) {
			$bindvaluedeath = true;
			$sinsert = $sinsert."death, ";
			$svalues = $svalues.":death, ";
			$sonduplicate = $sonduplicate."death = VALUES(death), ";
		}
		
		$sex = strlen($_POST['sex']) > 0 ? $_POST['sex'] : "";
		if ($sex != "") {
			$bindvaluesex = true;
			$sinsert = $sinsert."sex, ";
			$svalues = $svalues.":sex, ";
			$sonduplicate = $sonduplicate."sex = VALUES(sex), ";
		}
		
        $country = htmlspecialchars($_POST['country']);
		$country = strlen($_POST['country']) > 0 ? $_POST['country'] : "";
		if ($country != "") {
			$bindvaluecountry = true;
			$sinsert = $sinsert."countryofbirth, ";
			$svalues = $svalues.":countryofbirth, ";
			$sonduplicate = $sonduplicate."countryofbirth = VALUES(countryofbirth), ";
		}
		
        $imageactor = strlen($_POST['imageactor']) > 0 ? $_POST['imageactor'] : "";
        if ($imageactor != "") {
			$bindvalueimage = true;
			$sinsert = $sinsert."image";
			$svalues = $svalues.":image, ";
			$sonduplicate = $sonduplicate."image = VALUES(image)";
		}
		//ta bort eventuella whitespaces på höger sida
		$sinsert = rtrim($sinsert);
		$svalues = rtrim($svalues);
		$sonduplicate = rtrim($sonduplicate);
		
		//remove eventuella ',' i slutet av strängen
		$sinsert = chop($sinsert, ",");
		$svalues = chop($svalues, ",");
		$sonduplicate = chop($sonduplicate, ",");
		
		$ifilm = $_POST['ifilm'];
        $itvshow = $_POST['itvshow'];
        /*man skickar tillbaka namn på actor till formtoAdd.php för att kunna fortsätta
        att lägga till nya info, samtidigt få info om hur det har gått med senaste registreringen.*/
        $_SESSION['actor']=$nameactor;
        //$_SESSION['addactor']=false;
		$sql = "INSERT INTO actor $sinsert) VALUES $svalues) ON DUPLICATE KEY UPDATE $sonduplicate";					 
        try {
            $s = $pdo->prepare($sql); 
            $s->bindValue(':nameactor', $nameactor);
			if ($bindvaluebirth) {
            	$s->bindValue(':birth', $birth);
			}
			if ($bindvaluedeath) {
            	$s->bindValue(':death', $death);
			}
			if ($bindvaluesex) {
            	$s->bindValue(':sex', $sex);
			}
			if ($bindvaluecountry) {
            	$s->bindValue(':countryofbirth', $country);
			}
			if ($bindvalueimage) {
	            $s->bindValue(':image', $imageactor);
			}
            $s->execute();
        } catch(PDOException $ex) {
            echo $ex->getMessage();
        }
		
        if (!empty($s) && $s->rowCount() > 0) {
            $_SESSION['addactor']=true;
        }
        /*else {
            $_SESSION['notaddactor']=true;
        }*/
        if ($ifilm != "") {
			//$_SESSION['addactorfilm']=false;
            $actorid = actorid($nameactor);
            $sql = "SELECT id FROM film WHERE film.name='$ifilm'";
            try {
                $result = $pdo->query($sql); 
            } catch(PDOException $ex) {
                echo "PDOException";
            }
            /*vi vet att resultatet innehåller bara en rad*/
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $filmid = $row['id'];
            $sql = "INSERT INTO actorfilm (actor_id, film_id) VALUES (?, ?)";
            try {
                $s = $pdo->prepare($sql); 
                $s->bindValue(1, $actorid);
                $s->bindValue(2, $filmid);
                $s->execute();
            } catch(PDOException $ex) {
                echo "PDOException";
            }
            if (!empty($s) && $s->rowCount() > 0) {
                $_SESSION['addactorfilm']=true;
            }
            /*else {
                $_SESSION['notaddactorfilm']=true;
            }*/
        }
        if ($itvshow != "") {
			//$_SESSION['addactortvshow']=false;
            $actorid = actorid($nameactor);
            $sql = "SELECT id FROM tvshow WHERE tvshow.name='$itvshow'";
            try {
                $result = $pdo->query($sql); 
            } catch(PDOException $ex) {
                echo "PDOException";
            }
            /*vi vet att resultatet innehåller bara en rad*/
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $tvshowid = $row['id'];
            $sql = "INSERT INTO actortvshow (actor_id, tvshow_id) VALUES (?, ?)";
            try {
                $s = $pdo->prepare($sql); 
                $s->bindValue(1, $actorid);
                $s->bindValue(2, $tvshowid);
                $s->execute();
            } catch(PDOException $ex) {
                echo "PDOException";
            }
            if (!empty($s) && $s->rowCount() > 0) {
                $_SESSION['addactortvshow']=true;
            }
            /*else {
                $_SESSION['notaddactortvshow']=true;
            }*/
        }    
    }
	/*om filmnamnet finns redan i databasen ,uppdaterar man yearrelease*/
    else if (isset($_POST['submitFilm'])) {
		//$_SESSION['addfilm']=false;
        $namefilm = htmlspecialchars($_POST['namefilm']);
        $releasefilm = strlen($_POST['releasefilm']) > 0 ? $_POST['releasefilm'] : NULL;
        $imagefilm = strlen($_POST['imagefilm']) > 0 ? $_POST['imagefilm'] : "";
        $_SESSION['film']=$namefilm;
        $sql = "INSERT INTO film (name, yearRelease, image) VALUES (?, ?, ?)
				ON DUPLICATE KEY UPDATE
  				yearRelease = VALUES(yearRelease),
                image = VALUES(image)";
        try {
            $s = $pdo->prepare($sql);
            $s->bindValue(1, $namefilm);
            $s->bindValue(2, $releasefilm);
            $s->bindValue(3, $imagefilm);
            $s->execute();
        } catch(PDOException $ex) {
            echo "PDOException";
        }
        if (!empty($s) && $s->rowCount() > 0) {
            $_SESSION['addfilm']=true;
        }
        /*else {
            $_SESSION['notaddfilm']=true;
        }*/
    }
	/*om tvserienamnet finns redan i databasen ,uppdaterar man yearrelease och yearend*/
    else if (isset($_POST['submitTvshow'])) {
		//$_SESSION['addtvshow']=false;
        $nametvshow = htmlspecialchars($_POST['nametvshow']);
		$releasetvshow = strlen($_POST['releasetvshow']) > 0 ? $_POST['releasetvshow'] : NULL;
		$endtvshow = strlen($_POST['endtvshow']) > 0 ? $_POST['endtvshow'] : NULL;
        $imagetvshow = strlen($_POST['imagetvshow']) > 0 ? $_POST['imagetvshow'] : "";
        $_SESSION['tvshow']=$nametvshow;
        $sql = "INSERT INTO tvshow (name, yearRelease, yearEnd, image) VALUES (?, ?, ?, ?)
				ON DUPLICATE KEY UPDATE
  				yearRelease = VALUES(yearRelease),
				yearEnd = VALUES(yearEnd),
                image = VALUES(image)";
        try {
            $s = $pdo->prepare($sql); 
            $s->bindValue(1, $nametvshow);
            $s->bindValue(2, $releasetvshow);
            $s->bindValue(3, $endtvshow);
            $s->bindValue(4, $imagetvshow);
            $s->execute();
        } catch(PDOException $ex) {
            echo "PDOException";
        }
        if (!empty($s) && $s->rowCount() > 0) {
            $_SESSION['addtvshow']=true;
        }
        /*else {
            $_SESSION['notaddtvshow']=true;
        }*/
    }
?>    
</body>
</html>