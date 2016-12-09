<?php
/*admin kommer till denna sida för att logga in för att kunna lägga actor,fil,tvshow.
om man loggar in rätt, sparas info i session och man går vidare till formtoAdd.php som är
en form med plats för att lägga till actor,film,tvshow.*/
session_start();
/*gör så att felmeddelande visas*/
error_reporting(E_ALL);
ini_set("display_errors", 1);
/*upprätta kontakt mellan php-sidan och databasen*/
include $_SERVER['DOCUMENT_ROOT'].'/ag1248/me105a/connect.php';
//$pdo = new PDO('mysql:host=localhost;dbname=databasbaserad_programering;charset=utf8', 'root', '');
if(isset($_POST['loggin'])) {
	$username = $_POST['username'];
	$salt = "reki";
	$password = md5($_POST['password'] . $salt);
	$sql = "SELECT * FROM admin WHERE username='$username' and password='$password'";
	try {
		$result = $pdo->query($sql); 
	} catch(PDOException $ex) {
		echo "PDOException";
	}
	if (!empty($result) && $result->rowCount() > 0) {
		$_SESSION['loggedin']=true;
		header("location:formtoAdd.php");
	}
	else {
		$_SESSION['loggedin']=false;
		echo "Fel användarnamn eller lösenord.";
	}
}
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title> Inloggning </title>
</head>
<body>
<!-- lösenord:tfdr-2015 salt:reki  -->
    <h2><font color="#00cc00"> Logga in </font></h2>
    <form name="frm" method="POST" action="">
        <table>
            <tr>
                <td>Användarnamn</td>
                <td><input type="text" name="username"></td>
            </tr>
            <tr>
                <td>Lösenord</td>
                <td><input type="password" name="password"></td>
            </tr>
            <tr>
                <td><input type="submit" name="loggin" value="Logga in"></td>
            </tr>
        </table>
    </form>
</body>
</html>