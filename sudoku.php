<?php
    require("classes.php");
    session_start();
    $currentGrid = strip_tags($_GET['grid']);
    $_SESSION["currentGrid"] = $currentGrid;

    if (!isset($_SESSION[$currentGrid])) {
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
                    break;
                default:
                    $warning = "Cette grille a plusieurs solutions.";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang='fr' prefix="og: https://ogp.me/ns#">
    <head>
        <meta charset='utf-8' />
        <meta name='viewport' content='width=device-width' />
        <title>Sudoku</title>
        <link rel='stylesheet' type='text/css' href='style.css' />
        <script src='sudoku.js'></script>
        <link rel="apple-touch-icon" href="thumbnail.php?size=57"  sizes="57x57">
        <link rel="apple-touch-icon" href="thumbnail.php?size=114" sizes="114x114">
        <link rel="apple-touch-icon" href="thumbnail.php?size=72"  sizes="72x72">
        <link rel="apple-touch-icon" href="thumbnail.php?size=144" sizes="144x144">
        <link rel="apple-touch-icon" href="thumbnail.php?size=60"  sizes="60x60">
        <link rel="apple-touch-icon" href="thumbnail.php?size=120" sizes="120x120">
        <link rel="apple-touch-icon" href="thumbnail.php?size=76"  sizes="76x76">
        <link rel="apple-touch-icon" href="thumbnail.php?size=152" sizes="152x152">
        <link rel="icon" type="image/png" href="thumbnail.php?size=196" sizes="196x196">
        <link rel="icon" type="image/png" href="thumbnail.php?size=160" sizes="160x160">
        <link rel="icon" type="image/png" href="thumbnail.php?size=96"  sizes="96x96">
        <link rel="icon" type="image/png" href="thumbnail.php?size=16"  sizes="16x16">
        <link rel="icon" type="image/png" href="thumbnail.php?size=32"  sizes="32x32">
        <link rel="manifest" href="manifest.php">
        <meta property="og:title" content="Sudoku"/>
        <meta property="og:type" content="website"/>
        <meta property="og:url" content="<?=$_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$_SERVER["DOCUMENT_URI"]?>"/>
        <meta property="og:image" content="<?=$_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].dirname($_SERVER["DOCUMENT_URI"])?>/thumbnail.php?size=200"/>
        <meta property="og:image:width" content="200"/>
        <meta property="og:image:height" content="200"/>
        <meta property="og:description" content="Remplissez la grille de sorte que chaque ligne, colonne et région (carré de 3×3 cases) contienne tous les chiffres de 1 à 9."/>
        <meta property="og:locale" content="fr_FR"/>
		<meta property="og:site_name" content="<?=$_SERVER["HTTP_HOST"]?>"/>
    </head>
    <body>
        <header>
            <h1>Sudoku</h1>
        </header>
        <form id='sudokuForm'>
            <table id='grid' class='grid'>
                <tbody>
<?php
        for ($row = 0; $row < 9; $row++) {
?>
                    <tr>
<?php
        for ($column = 0; $column < 9; $column++) {
            $value = $currentGrid[9*$row+$column];
            if ($value == UNKNOWN) {
?>
                        <td><input type='number' min='1' max='9' step='1' value='' title='Valeurs possibles [Clic-droit]'/></td>
<?php
                } else {
?>
                        <td><input type='number' min='1' max='9' step='1' value='<?=$value?>' disabled/></td>
<?php
            }                                                            
        }
?>
                    </tr>
<?php
   }
?>
                </tbody>
            </table>
        </form>
        <section class='tools'>
            <div id='insertRadioGroup' class='insertRadioGroup'>
<?php
        for($value=1; $value<=9; $value++) {
            echo "                <input type='radio' id='insertRadio$value' value='$value' name='insertRadioGroup' onclick='insert(this)' accesskey='$value'/>\n";
            echo "                <label for='insertRadio$value' title='Insérer un $value'>$value</label>\n";
        }
?>
            </div>
            <div>
                <input id='highlighterCheckbox' type="checkbox" onclick='highlight()'/>
                <label for='highlighterCheckbox' title='Surligner les cases interdites'><img src='img/highlighter.svg' alt='Surligneur'></label>
                <input type='radio' id='inkPenRadio' name='tool' onclick='grid.style.cursor = "url(img/ink-pen.svg) 2 22, auto"' checked/>
                <label for='inkPenRadio' title='Écrire au stylo'><img src='img/ink-pen.svg' alt='Stylo indélébile'/></label>
                <input type='radio' id='pencilRadio' name='tool' onclick='grid.style.cursor = "url(img/pencil.svg) 2 22, auto"'/>
                <label for='pencilRadio' title='Écrire au crayon'><img src='img/pencil.svg' alt='Crayon'/></label>
                <input type='radio' id='eraserRadio' name='tool' onclick='grid.style.cursor = "url(img/eraser.svg) 2 22, auto"'/>
                <label for='eraserRadio' title='Effacer une case'><img src='img/eraser.svg' alt='Gomme'/></label>
                <button type='button' class='warning' onclick='restart()' title='Recommencer'>
                    <img src='img/restart.svg' alt='Recommencer'/>
                </button>
                <button id='undoButton' type='button' onclick='undo()' disabled title='Annuler' accesskey='z'>
                    <img src='img/undo.svg' alt='Annuler'/>
                </button>
            </div>
        </section>
        <section>
<?php
    if (isset($warning))
        echo("            <strong>⚠️ $warning</strong><br/>\n");
    else
        echo("            Remplissez la grille de sorte que chaque ligne, colonne et région (carré de 3×3 cases) contienne tous les chiffres de 1 à 9.\n")
?>
        </section>
        <ul id="contextMenu" class="context-menu"></ul>
        <footer>
            <div id='links'>
                <a href=''>Lien vers cette grille</a><br/>
                <a href='.................................................................................'>Grille vierge</a><br/>
                <a href='.'>Nouvelle grille</a>
            </div>
            <div class='credits'>
                Icônes par <a href="https://www.flaticon.com/authors/freepik" title="Freepik">Freepik</a> chez <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a>
            </div>
        </footer>
    </body>
</html>
