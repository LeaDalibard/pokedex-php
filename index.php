<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

//-------- FUNCTION GET DATA

function getData($poke)
{
    $dataPoke = file_get_contents("https://pokeapi.co/api/v2/pokemon/" . $poke);
    $decodeDataPoke = json_decode($dataPoke, true);
    return $decodeDataPoke;
}

//-------- FUNCTION GET IMAGE

function getImg($poke)
{
    $imgPoke = getData($poke)['sprites']['front_shiny'];
    return $imgPoke;
}

//-------- FUNCTION GET ID

function getId($poke)
{
    $pokeId = getData($poke)['id'];
    return $pokeId;
}

//-------- FUNCTION GET NAME

function getName($poke)
{
    $pokeName = getData($poke)['name'];
    return $pokeName;
}

//________________________________
//$nameErr="";
//if ($_SERVER["REQUEST_METHOD"] == "GET") {
//if (empty($_GET["name"])) {
//    $nameErr = "Pokemon name is required";
//}

if (isset($_GET['name'])) {
    $pokemon = strtolower($_GET['name']);// convert to lower case
    $patterns = array();
    $patterns[0] = ' ';//Replace spaces by dashes part 1
    $patterns[1] = '/[^A-Za-z0-9\-]/'; //Remove special characters part 1
    $replacements = array();
    $replacements[0] = '-';//Replace spaces by dashes part 2
    $replacements[1] = '';//Remove special characters part 2
    $pokemon = str_replace($patterns, $replacements, $pokemon);
    if (file_get_contents("https://pokeapi.co/api/v2/pokemon/" . $pokemon) == FALSE) {
        echo "Please enter a correct pokemon name";
        $pokemon = 1;
    }//checking if input is a pokemon
} else {
    $pokemon = 1;
}//if not input go to Bulbasaur


//------------------- GET 4 RANDOM MOVES -----------------------------

$randomMove = array();
$maxMove = 4;

if (count(getData($pokemon)['moves']) < $maxMove) {
    $numberMove = count(getData($pokemon)['moves']);
} else($numberMove = $maxMove);

$uniqueMoves = array();
for ($i = 0; count($uniqueMoves) < $numberMove; $i++) {
    $randomNumber = rand(0, count(getData($pokemon)['moves']) - 1);
    array_push($randomMove, getData($pokemon)['moves'][$randomNumber]['move']['name']);
    $uniqueMoves = array_unique($randomMove);
}


$stringMoves = implode(", ", $uniqueMoves);// turning array to string with spaces and coma in between

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
    <h1>Pokemon information</h1>
    <p><?php echo "Pokemon name : " . ucwords(getName($pokemon)); ?></p>
    <p><?php echo "Pokemon Id : " . getId($pokemon); ?></p>
    <p><?php echo "Moves : " . $stringMoves; ?></p>
    <img src="<?php echo getImg($pokemon); ?>" alt="pokemon image">
</section>
<section id="Evolution">
    <h1>Pokemon evolution chain</h1>
    <?php for ($i = 0; $i < count($evolutionNames); $i++) {
        $evoName = ucwords(getName($evolutionNames[$i]));
        $evoId = getId($evolutionNames[$i]);
        $evoImg = getImg($evolutionNames[$i]);
        echo "<p>Evolution name : " . $evoName . "<p> Evolution Id : " . $evoId . "</p>" . "</p>" . "<img src=" . $evoImg . ">";
    }
    ?>
</section>
<form action="previous.php" method="get">
    <p><input type="button" value="Previous"></p>
</form>
</body>
</html>