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

//-------- FUNCTION GET DATA SPECIES

function getDataSpecies($poke)
{
    $dataPoke = file_get_contents("https://pokeapi.co/api/v2/pokemon-species/" . $poke);
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

if(!empty($_POST['name']))
{
    $pokemon = 1;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['name'])) {
        $pokemon = strtolower($_POST['name']);// convert to lower case
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
}
else{$pokemon = 1;}


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

getDataSpecies($pokemon);
$chainUrl = getDataSpecies($pokemon)['evolution_chain']['url'];
//$Species = file_get_contents("https://pokeapi.co/api/v2/pokemon-species/" . $pokemon);
//$dataSpecies = json_decode($Species, true);
//$chainUrl = $dataSpecies['evolution_chain']['url'];


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

//____________ GET PREVIOUS EVOLUTION

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['previous'])) {
        if (isset($_POST['name'])){ $pokemon = strtolower($_POST['name']);
        }
        else{$pokemon =1;}
        $previousPokemon = getDataSpecies($pokemon)['evolves_from_species'];
        if ($previousPokemon==null){echo "This is the first pokemon of the evolution, press next to see its evolution.";}
        else{ $pokemonName=getDataSpecies($pokemon)['evolves_from_species']['name'];
            $pokemon=$pokemonName;
        }
    }
}
//____________ GET NEXT EVOLUTION

var_dump($evolutionNames);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   if (isset($_POST['next'])) {
       if (isset($_POST['name'])) {
           $pokemon = strtolower($_POST['name']);
       } else {
           $pokemon = 1;
       }
       for ($i = 0; $i <count($evolutionNames) ; $i++) {
           if ($evolutionNames[$i] == $pokemon) {
               if (isset($evolutionNames[$i + 1])) {
                   $pokemonEvol = $evolutionNames[$i + 1];
               } else {
                   $pokemonEvol=$pokemon;
                   echo 'This is the last pokemon of this evolution';
               }
           }
       }
       $pokemon=$pokemonEvol;
   }
//foreach ($evolutionNames as $key=>$value) {
//            if ($value==$pokemon){
//               if (isset($evolutionNames[$key+1])){$pokemonNext=$evolutionNames[$key+1];
//                     }
//               else {echo 'This is the last pokemon of this evolution';
//                   }
//            }
//           $pokemon=$pokemonNext;
//       }
//   }
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
<form action="index.php" method="post">
    <p>Pokemon: <input type="text" name="name" value="<?php echo $_POST['name'] ?? ''; ?>"/></p>
    <p><input type="submit" name="submit" value="OK"></p>
    <p><input type="submit" name="previous" value="previous"></p>
    <p><input type="submit" name="next" value="next"></p>

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
    <?php for ($i = 0;
               $i < count($evolutionNames);
               $i++) {
        $evoName = ucwords(getName($evolutionNames[$i]));
        $evoId = getId($evolutionNames[$i]);
        $evoImg = getImg($evolutionNames[$i]);
        echo "<p>Evolution name : " . $evoName . "<p> Evolution Id : " . $evoId . "</p>" . "</p>" . "<img src=" . $evoImg . ">";
    }
    ?>
</section>

</body>
</html>