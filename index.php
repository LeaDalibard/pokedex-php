<?php
declare(strict_types = 1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);


$pokemonName=$_GET['name'];

if($pokemonName==null){$dataPokemon = file_get_contents("https://pokeapi.co/api/v2/pokemon/1");}
else{$dataPokemon = file_get_contents("https://pokeapi.co/api/v2/pokemon/$pokemonName");}
echo $pokemonName;


//echo $dataPokemon;
$decodeData= json_decode($dataPokemon, true);

//$pokemonId=$decodeData.id;
var_dump($decodeData);


?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pokedex</title>
</head>
<body>
<form action="index.php" method="get">
    <p>Pokemon: <input type="text" name="name" /></p>
    <p><input type="submit" value="OK"></p>
</form>
</body>
</html>