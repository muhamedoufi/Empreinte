<?php
 include 'conn.php';



 $nni = $_POST["nni"];
 $nom_complet = $_POST["nom_complet"];
 $empreinte = $_POST["empreinte"];
 $base64 = $_POST["base64"];


// Inserisce una nuova compagnia
$insert="INSERT INTO patient (nni,nom_complet,empreinte,BPMBase64) values ('$nni','$nom_complet','$empreinte','$base64')";
$result=mysqli_query($conn,$insert) or die (mysqli_error($conn));

if($result !=FALSE)   {

 header("Location: Demo1.php");
  }
 else
{
echo "Impossibile aggiungere Compagnia<br>";
 echo "<a href='/Empreinte/Demo1.php'>Clicca Qui</a>  tornare a la page principale.";
}
 ?>