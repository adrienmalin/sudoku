<?php 
    require("classes.php");
    session_start();
    $fullUrl = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$_SERVER["DOCUMENT_URI"];
    $dirUrl = dirname($fullUrl);
    $currentGrid = strip_tags($_SERVER['QUERY_STRING']);

    if (preg_match("/^[1-9.]{81}$/", $currentGrid)) {
        if (!isset($_SESSION[$currentGrid]) || $_SESSION[$currentGrid] != "checked") {
            $grid = new Grid();
            $grid->import($currentGrid);
            if ($grid->containsDuplicates()) {
                $warning = "Cette grille contient des doublons.";
            } else {
                switch($grid->countSolutions(2)) {
                    case 0:
                        $warning = "Cette grille n'a pas de solution.";
                        break;
                    case 1:
                        $validGrids[] = $currentGrid;
                        break;
                    default:
                        $warning = "Cette grille a plusieurs solutions.";
                }
            }
        }
        require("sudoku.php");
    } else {
        $grid = new Grid();
        $grid->generate();
        $gridAsString = $grid->toString();
        $newGridUrl = "$dirUrl/?$gridAsString";
        $_SESSION[$gridAsString] = "checked";
        if (!$currentGrid) {
            header("Location: $newGridUrl");
        } else {
            require("400.php");
        }
    }
?>