<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);


//-------- START SESSION TO REMEMBER PAGES
session_start();


//-------- FUNCTION GET DATA

function getData($poke)
{
    $dataPoke = file_get_contents("https://pokeapi.co/api/v2/pokemon/" . $poke);
    $decodeDataPoke = json_decode($dataPoke, true);
    return $decodeDataPoke;
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

//-------- FUNCTION GET IMAGE

function getImg($poke)
{
    $imgPoke = getData($poke)['sprites']['front_shiny'];
    return $imgPoke;
}

//-------- FUNCTION GET PAGE

$page_length=21;

if (isset ($_GET['page'])){
    if ($_GET['page'] == 0){
       echo "previous";
    }
    elseif ($_GET['page'] == 1){
        echo "page 1";
    }
    elseif ($_GET['page'] == 2){
        echo "page 2";
    }
}

?>

<!doctype html>
<html lang="en">
<head>
    <title>Category pokemon</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>

<div class="container">
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <li class="page-item"><a class="page-link" href="?page=0">Previous</a></li>
            <li class="page-item"><a class="page-link" href="?page=1">1</a></li>
            <li class="page-item"><a class="page-link" href="?page=2">2</a></li>
            <li class="page-item"><a class="page-link" href="?page=3">3</a></li>
            <li class="page-item"><a class="page-link" href="?page=4">Next</a></li>
        </ul>
    </nav>
    <div class="row">
        <?php for($i=1;$i<$page_length;$i++): ?>
        <div class="col-3">
            <p><?php echo "Pokemon name : " . ucwords(getName($i)); ?></p>
            <p><?php echo "Pokemon Id : " . getId($i); ?></p>
            <img src="<?php echo getImg($i); ?>" alt="pokemon image">
            <p> <a href=<?php echo "https://pokeapi.co/api/v2/pokemon/".$i; ?>>Link to Pokemon page</a></p>
        </div>
        <?php endfor; ?>
    </div>

</div>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
</body>
</html>