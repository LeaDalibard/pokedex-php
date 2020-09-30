<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);


if (isset($_GET['name'])) {
    $pokemon = $_GET['name'];
} else {
    $pokemon = 1;
}

$dataPokemon = file_get_contents("https://pokeapi.co/api/v2/pokemon/" . $pokemon);


$decodeData = json_decode($dataPokemon, true);

$pokemonName = $decodeData['name'];
$pokemonId = $decodeData['id'];
$pokemonImg = $decodeData['sprites']['front_shiny'];

//------------------- GET 4 RANDOM MOVES -----------------------------

$randomMove = array();
$maxMove = 4;
//$pokemonMove=$decodeData['moves'][$randomNumber]['move']['name'];
if (count($decodeData['moves']) < $maxMove) {
    $numberMove = count($decodeData['moves']);
} else($numberMove = $maxMove);
for ($i = 0; $i < $numberMove; $i++) {
    $randomNumber = rand(0, count($decodeData['moves']) - 1);
    array_push($randomMove, $decodeData['moves'][$randomNumber]['move']['name']);
}

$uniqueMoves = array_unique($randomMove);
$stringMoves = implode(", ", $uniqueMoves);
//var_dump($uniqueMoves);
//echo $stringMoves;

//------------------- GET THE ALL NAMES FROM THE EVOLUTION -----------------------------

//------------ 1. get the url of evolution chain
$Species = file_get_contents("https://pokeapi.co/api/v2/pokemon-species/" . $pokemon);
$dataSpecies = json_decode($Species, true);
$chainUrl = $dataSpecies['evolution_chain']['url'];

//------------ 2. extract the names from the evolution chain
$evo = file_get_contents($chainUrl);
$dataEvo = json_decode($evo, true);

$evolutionNames = array($dataEvo['chain']['species']['name']);
$lengthEvo = count($dataEvo['chain']['evolves_to']);

if (isset($dataEvo['chain']['evolves_to'][0]['evolves_to'])) {
    $lengthAll = count($dataEvo['chain']['evolves_to'][0]['evolves_to']);
}

if ($lengthEvo > 0) {
    for ($i = 0; $i < $lengthEvo; $i++) {
        array_push($evolutionNames, $dataEvo['chain']['evolves_to'][$i]['species']['name']);
    }
}

if (isset($lengthAll)) {
    if ($lengthAll > 0) {
        for ($i = 0; $i < $lengthAll; $i++) {
            array_push($evolutionNames, $dataEvo['chain']['evolves_to'][0]['evolves_to'][$i]['species']['name']);
        }
    }
}

//var_dump($evolutionNames);

//-------------------GET THE DATA FROM THE POKEMON FROM EVOLUTION -----------------------------

function getData($poke){
    $dataPoke = file_get_contents("https://pokeapi.co/api/v2/pokemon/" . $poke);
    $decodeDataPoke = json_decode($dataPoke, true);
    return $decodeDataPoke;
}

$test=getData($evolutionNames[0]);
var_dump($test);

//var_dump($dataEvo);
//var_dump(count($lengthAll));
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
    <p>Pokemon: <input type="text" name="name"/></p>
    <p><input type="submit" value="OK"></p>
</form>
<section id="MainPokemon">
    <p><?php echo "Pokemon :" . $pokemonName; ?></p>
    <p><?php echo "Pokemon Id :" . $pokemonId; ?></p>
    <p><?php echo "Moves :" . $stringMoves; ?></p>
    <img src="<?php echo $pokemonImg; ?>" alt="pokemon image">
</section>
<section id="Evolution">

</section>
</body>
</html>