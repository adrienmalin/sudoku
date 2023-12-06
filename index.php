<?php 
    require("classes.php");
    session_start();
    if (!array_key_exists("nbSolutions", $_SESSION)) {
        $_SESSION["nbSolutions"] = array();
    }

    $fullUrl = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$_SERVER["DOCUMENT_URI"];
    $dirUrl = dirname($fullUrl);
    $currentGrid = strip_tags($_SERVER['QUERY_STRING']);

    if (preg_match("/^[1-9.]{81}$/", $currentGrid)) {
        if (!array_key_exists($currentGrid, $_SESSION["nbSolutions"])) {
            $grid = new Grid();
            $grid->import($currentGrid);
            $_SESSION["nbSolutions"][$currentGrid] = $grid->containsDuplicates() ? -1 : $grid->countSolutions(2);
        }
        switch($_SESSION["nbSolutions"][$currentGrid]) {
            case -1:
                $warning = "Cette grille contient des doublons.";
                break;
            case 0:
                $warning = "Cette grille n'a pas de solution.";
                break;
            case 1:
                break;
            default:
                $warning = "Cette grille a plusieurs solutions.";
        }
        require("sudoku.php");
    } else {
        $grid = new Grid();
        $grid->generate();
        $gridAsString = $grid->toString();
        $newGridUrl = "$dirUrl/?$gridAsString";
        $_SESSION["nbSolutions"][$gridAsString] = 1;
        if ($currentGrid) {
            require("400.php");
        } else {
            header("Location: $newGridUrl");
        }
    }
?>