<?php
declare(strict_types = 1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);



if (isset($_GET['name'])) {
    $pokemon=$_GET['name'];
}
else{$pokemon=1;}

$dataPokemon = file_get_contents("https://pokeapi.co/api/v2/pokemon/".$pokemon);


$decodeData= json_decode($dataPokemon, true);

$pokemonName=$decodeData['name'];
$pokemonId=$decodeData['id'];
$pokemonImg=$decodeData['sprites']['front_shiny'];


$randomMove=array();
$maxMove=4;
//$pokemonMove=$decodeData['moves'][$randomNumber]['move']['name'];
if(count($decodeData['moves'])<$maxMove){
    $numberMove=count($decodeData['moves']);
}
else($numberMove=$maxMove);
for ($i = 0; $i < $numberMove; $i++) {
    $randomNumber = rand(0, count($decodeData['moves'])-1);
    array_push($randomMove, $decodeData['moves'][$randomNumber]['move']['name']);
}

$uniqueMoves = array_unique($randomMove);
$stringMoves = implode(", ", $uniqueMoves);
//var_dump($uniqueMoves);
//echo $stringMoves;

$Species = file_get_contents("https://pokeapi.co/api/v2/pokemon-species/".$pokemon);
$dataSpecies= json_decode($Species, true);
$evoUrl=$dataSpecies['evolution_chain']['url'];
var_dump($evoUrl);
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
<section>
    <p><?php echo "Pokemon :".$pokemonName; ?></p>
    <p><?php echo "Pokemon Id :".$pokemonId; ?></p>
    <p><?php echo "Moves :".$stringMoves; ?></p>
    <img src="<?php echo $pokemonImg; ?>" alt="pokemon image">
</section>
</body>
</html>