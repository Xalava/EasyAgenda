<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <title>Inscription en ligne</title>
    <link rel="stylesheet" href="./styles/style_general.css" type="text/css">
    <link rel="stylesheet" href="./styles/personnes.css" type="text/css">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>


<H1>Agenda collectif <IMG align="right" src="adresse d'une petite image sympa"></H1>
<p class=".default"><br><br><br><br></p>


<?php 

//precondition:  IL faut que le dossier ./data contienne des fichier  "ordre"+date+.txt
//ou ordre est l'odre dans lequel vous voulez que les date apparaissent
//************* phase d'éxécution d'une requete ********************************************************
$err="Erreur d'accès au fichier, veuillez rééssayer dans un instant";
if($_GET['lePrenom']!="") {
	switch($_GET["requete"]){
		
//ajout
		case 1:
		//On utilise flock a chaque ouverture de fichier, pour éviter les problemes si plusieurs
		// personnes se connectent en meme temps.
		($freq=fopen($_GET["fichier"],'a')) or die ("$err") ;
		fputs($freq,$_GET["niv"]."/ ".$_GET["lePrenom"]."\n");
/*attenion , si vous éditez les fichiers a la main n'oubliez pas de mettre "entrée ala fin de chaque fichier*/
// $niv représente le type de personne qui s'inscrit déterminela couleur dans laquelle ça s'afficheras
		fclose($freq);
		break;
		
		
		//retrait
		case 2:
		
		//copie du fichier de départ dans un fichier "tmp.txt
		($ancien = fopen("$_GET[fichier]",r)) or die ("$err") ;
		flock($ancien,LOCK_EX);
		touch('tmp.txt');
		($tmp = fopen('tmp.txt',w)) or die ("$err");
		flock($tmp,LOCK_EX);//lock_ex=2 protege le fichier en ériture et en lecture
			while (!feof( $ancien )){
			//echo "@$ligne@";  --> juste pour le débuggage
			$ligne=fgets( $ancien , 1024 );
			(($ligne!="\n")&&(fputs($tmp,"$ligne")));
			}
		flock($ancien,LOCK_UN);	
		fclose($ancien);
		flock($tmp,LOCK_UN);
		fclose($tmp);
			
		//recopie dans le fichier de départ , sauf la ligne contenant le prénom cherché ou leslignes vides		
		
		($tmp = fopen('tmp.txt',r)) or die ("$err") ;
		flock($tmp,LOCK_EX);
		($freqq=fopen("$_GET[fichier]",w)) or die ("$err");
		flock($freqq,LOCK_EX);
			while (!feof($tmp)){
			$ligne=fgets($tmp,1024);
				//echo "!$ligne!";
				if (($ligne!="")&&(!(strstr($ligne,$_GET['lePrenom'])))){
				fputs($freqq,$ligne);//echo "@$ligne@";
				}
			}
		flock($reqq,LOCK_UN);	
		fclose($freqq);
		flock($tmp,LOCK_UN);
		fclose($tmp);
		unlink('tmp.txt');	
	
		
		
		//unlink($_SERVER["REMOTE_ADDR"].'.txt');
		break;
		
	
	}
}
//affichage rapide du tableau _get
/*echo "<pre>";          --->  encore pour le debuggage
print_r($_GET);
echo "</pre>";*/
//remise a zero du tableau get ça sert a rien mais ca a l'air plus propre ;)
$_GET=array();
/*echo "<pre>";
print_r($_GET);
echo "</pre>";*/
//*************** fin execution requete *********************************************************

//**************initialisation du tableau a partir du contenu de ./data";**
$dh = opendir("./data");
while ($fichier = readdir($dh)){
	if(substr($fichier,-5)=="+.txt"){
	$listeDates[strtok("$fichier","+")]= strtok("+");	
	}
}
closedir($dh);
ksort($listeDates);

//affichage su tableau
echo '<table width="100%" border="1">';
 // 1ligne: titre
 echo"<tr><td><H3>Date</h3></td><td><H3>Participants</h3></td><td><H3>Commandes</h3></td><tr>";	
 
 
 foreach($listeDates as $cle=>$date){
	echo "<tr>";
	//premiere colone date
	echo '<td><DIV class="dateclass">'.$date.'</DIV></td>';
	
	//deuxieme colone avec le contenu du fichier
	echo    "<td>";
	$fchemin="./data/$cle+$date+.txt";
	$fp=fopen("$fchemin",r);
		while (!feof($fp)){
		
		list($niv,$ligne)=split("/",fgets($fp,1024));// yavais plus simple?
			if($ligne!=""){
			echo "<DIV class=\"".$niv."class\">".$ligne."<br></DIV>";
			}
		}
	fclose($fp);
	echo "</td>";
	
	//troisiem colone avec les commandes entierement en html
	?>
	<td>
		
		<form method="GET" action="<?php $_SERVER['PHP_SELF']; ?>">
			S'ajouter
			<input type="text" value="" name="lePrenom"/>
			<select name="niv">
				<option value="t1">Type d'incrit 1</option>
				<option value="t2">Type d'inscrit 2</option>
				
			</select> 
			<input type="hidden" name="fichier" value="<?php echo $fchemin; ?>" />
			<input type="hidden" name="requete" value="1" />	
			<input type="submit" value="envoyer"/>
		</form>
		
		<form method="GET" action="<?php $_SERVER['PHP_SELF']; ?>">
			Se desinscrire
			 <select name="lePrenom">     
				<?php
				$fp=fopen("$fchemin",r);
				while (!feof($fp)){
				list($niv,$ligne)=split("/",fgets($fp,1024));
					if($ligne!=""){
					echo "<option value=$ligne>$ligne<br>";
					}
				}
				fclose($fp);
				?>
			</select>
			<input type="hidden" name="fichier" value="<?php echo $fchemin; ?>" />
			<input type="hidden" name="requete" value="2" />	
			<input type="submit" value="envoyer"/>
			</form>
		
	</td>
	
	<?php
	//finligne 	
	echo "</tr>"; 
} 

echo "</table>";

//*************************** fin affichage tableau**********
//
/*
echo "<pre>";
print_r($listeDates);
echo "</pre>";
*/
?>



    <link rel="stylesheet" href="./styles/style_general.css" type="text/css">

Design: <a href="https://www.linkedin.com/in/lavayssiere"> Xavier Lavayssière</a>

</body>
</html>