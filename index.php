<?php 
    require("classes.php");

    global $sudokuGridSolutions;
    if (!isset($sudokuGridSolutions)) $sudokuGridSolutions = array();

    $fullUrl = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$_SERVER["DOCUMENT_URI"];
    $dirUrl = dirname($fullUrl);
    $currentGrid = strip_tags($_SERVER['QUERY_STRING']);

    if (preg_match("/^[1-9.]{81}$/", $currentGrid)) {
        if (!array_key_exists($currentGrid, $sudokuGridSolutions)) {
            $grid = new Grid($currentGrid);
            $sudokuGridSolutions[$currentGrid] = $grid->containsDuplicates() ? -1 : $grid->countSolutions(2);
        }
        switch($sudokuGridSolutions[$currentGrid]) {
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
        $gridAsString = $grid->toString();
        $newGridUrl = "$dirUrl/?$gridAsString";
        $sudokuGridSolutions[$gridAsString] = 1;
        if ($currentGrid) {
            require("400.php");
        } else {
            header("Location: $newGridUrl");
        }
    }
?>