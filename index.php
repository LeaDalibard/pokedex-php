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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['previous'])) {
        if (isset($_POST['name'])) {
            $pokemon = strtolower($_POST['name']);
        } else {
            $pokemon = 1;
        }
        for ($i = 0; $i <count($evolutionNames) ; $i++) {
            if ($evolutionNames[$i] == getName($pokemon)) {
                if (isset($evolutionNames[$i - 1])) {
                    $pokemonPrev = $evolutionNames[$i - 1];
                } else {
                    $pokemonPrev=$pokemon;
                    echo '<script>alert("This is the first pokemon of this evolution")</script>';
                }
            }
        }
        $pokemon=$pokemonPrev;
        $_POST['name']=$pokemon;
    }
}
//____________ GET NEXT EVOLUTION


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   if (isset($_POST['next'])) {
       if (isset($_POST['name'])) {
           $pokemon = strtolower($_POST['name']);
       } else {
           $pokemon = 1;
       }
       for ($i = 0; $i <count($evolutionNames) ; $i++) {
           if ($evolutionNames[$i] == getName($pokemon)) {
               if (isset($evolutionNames[$i + 1])) {
                   $pokemonEvol= $evolutionNames[$i + 1];
               } else {
                   $pokemonEvol=$pokemon;
                   echo '<script>alert("This is the last pokemon of this evolution")</script>';
               }
           }
       }
       $pokemon=$pokemonEvol;
       $_POST['name']=$pokemon;
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
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/97e98690fe.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
          integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
</head>
<body>
<section class="container">
<section class="content text-center">

    <section class="search pt-5">
        <form action="index.php" method="post">
            <p>Pokemon: <input type="text" name="name" value="<?php echo $_POST['name'] ?? ''; ?>"/></p>
            <p><input type="submit" name="submit" class="btn btn-primary" value="search"></p>
            <p><input type="submit" class="btn btn-outline-light m-3" name="previous" value="Previous evolution"><input type="submit" class="btn btn-outline-light m-3" name="next" value="Next evolution"></p>
        </form>
    </section>

<section class="MainPokemon">
    <h2>Pokemon information</h2>
    <p><?php echo "Pokemon name : " . ucwords(getName($pokemon)); ?></p>
    <p><?php echo "Pokemon Id : " . getId($pokemon); ?></p>
    <p><?php echo "Moves : " . $stringMoves; ?></p>
    <img src="<?php echo getImg($pokemon); ?>" alt="pokemon image">
</section>
<section class="Evolution">
    <h2>Pokemon evolution chain</h2>
    <div class="card-group">
    <?php for ($i = 0;
               $i < count($evolutionNames);
               $i++) {
        $evoName = ucwords(getName($evolutionNames[$i]));
        $evoId = getId($evolutionNames[$i]);
        $evoImg = getImg($evolutionNames[$i]);
        echo " <div class='card-body'><p class='card-text'>Evolution name : " . $evoName . "</p> " ."<p class='card-text'> Evolution Id : " . $evoId . "</p>" . "<img src=" . $evoImg . "></div>";
    }
    ?>
    </div>
</section>
</section>
</section>
</body>
</html>