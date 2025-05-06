<?php 
    require("classes.php");

    $fullUrl = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$_SERVER["DOCUMENT_URI"];
    $dirUrl = dirname($fullUrl);
    $currentGrid = strip_tags($_SERVER['QUERY_STRING']);

    if (preg_match("/^[1-9-]{81}$/", $currentGrid)) {
        session_id($currentGrid);
        session_start(["use_cookies" => false]);

        if (!array_key_exists("nbSolutions", $_SESSION)) {
            $grid = new Grid($currentGrid);
            $_SESSION["nbSolutions"] = $grid->containsDuplicates() ? -1 : $grid->countSolutions(2);
        }
        switch($_SESSION["nbSolutions"]) {
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
        if ($currentGrid) {
            require("400.php");
        } else {
            $grid = new Grid();
            $gridAsString = $grid->toString();
            $newGridUrl = "$dirUrl/?$gridAsString";
    
            session_id($gridAsString);
            session_start(["use_cookies" => false]);
    
            $_SESSION["nbSolutions"] = 1;

            header("Location: $newGridUrl");
        }
    }
?>