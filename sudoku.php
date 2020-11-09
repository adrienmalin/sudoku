<?php
    require("classes.php");

    $gridStr = basename(strip_tags($_SERVER["DOCUMENT_URI"]));
    // URL contains grid
    if (preg_match("#^[1-9.]{81}$#", $gridStr)) {
?>
<!DOCTYPE html>
<html lang='fr' prefix="og: https://ogp.me/ns#">
    <head>
        <meta charset='utf-8' />
        <meta name='viewport' content='width=device-width' />
        <title>Sudoku</title>
        <link rel='stylesheet' type='text/css' href='style.css' />
        <script src='app.js'></script>
        <link rel="apple-touch-icon" href="thumbnail.png.php?grid=<?=$gridStr?>&size=57"  sizes="57x57">
        <link rel="apple-touch-icon" href="thumbnail.png.php?grid=<?=$gridStr?>&size=114" sizes="114x114">
        <link rel="apple-touch-icon" href="thumbnail.png.php?grid=<?=$gridStr?>&size=72"  sizes="72x72">
        <link rel="apple-touch-icon" href="thumbnail.png.php?grid=<?=$gridStr?>&size=144" sizes="144x144">
        <link rel="apple-touch-icon" href="thumbnail.png.php?grid=<?=$gridStr?>&size=60"  sizes="60x60">
        <link rel="apple-touch-icon" href="thumbnail.png.php?grid=<?=$gridStr?>&size=120" sizes="120x120">
        <link rel="apple-touch-icon" href="thumbnail.png.php?grid=<?=$gridStr?>&size=76"  sizes="76x76">
        <link rel="apple-touch-icon" href="thumbnail.png.php?grid=<?=$gridStr?>&size=152" sizes="152x152">
        <link rel="icon" type="image/png" href="thumbnail.png.php?grid=<?=$gridStr?>&size=196" sizes="196x196">
        <link rel="icon" type="image/png" href="thumbnail.png.php?grid=<?=$gridStr?>&size=160" sizes="160x160">
        <link rel="icon" type="image/png" href="thumbnail.png.php?grid=<?=$gridStr?>&size=96"  sizes="96x96">
        <link rel="icon" type="image/png" href="thumbnail.png.php?grid=<?=$gridStr?>&size=16"  sizes="16x16">
        <link rel="icon" type="image/png" href="thumbnail.png.php?grid=<?=$gridStr?>&size=32"  sizes="32x32">
        <link rel="manifest" href="manifest.json.php?grid=<?=$gridStr?>">
        <meta property="og:title" content="Sudoku"/>
        <meta property="og:type" content="website"/>
        <meta property="og:url" content="<?=$_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["DOCUMENT_URI"];
?>"/>
        <meta property="og:image" content="<?=$_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["DOCUMENT_URI"])?>/thumbnail.png.php?grid=<?=$gridStr?>&size=200"/>
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
        <section>
            Remplissez la grille de sorte que chaque ligne, colonne et région (carré de 3×3 cases) contienne tous les chiffres de 1 à 9.
        </section>
        <form id='sudokuForm'>
            <div>
                <table id='grid' class='grid'>
                    <tbody>
<?php   for ($row = 0; $row < 9; $row++) {                                   ?>
                        <tr>
<?php
            for ($column = 0; $column < 9; $column++) {
                $value = $gridStr[9*$row+$column];
                if ($value == UNKNOWN) {
?>
                            <td><input type='number' min='1' max='9' step='1' value='' title='Valeurs possibles [Clic-droit]'/></td>
<?php           } else {                                                     ?>
                            <td><input type='number' min='1' max='9' step='1' value='<?=$value?>' disabled/></td>
<?php
                }                                                            
            }
?>
                        </tr>
<?php   }                                                                    ?>
                    </tbody>
                </table>
            </div>
            <div id='buttons' class='select-buttons'>
<?php
        for($value=1; $value<=9; $value++) {
            echo "                <button type='button' onclick='highlight(\"$value\")' title='Écrire un $value' accesskey='$value'>$value</button>\n";
        }
?>
            </div>
            <div>
                <button id='highlighterButton' type='button' onclick='toggleHighlighting()' title='Surligner les chiffres sélectionnés'>
                    <img src="img/highlighter.svg" alt='Surligneur'/>
                </button>
                <button id='inkPenButton' type='button' onclick='useInkPen()' title='Écrire au stylo' class='pressed'>
                    <img src="img/ink-pen.svg" alt='Stylo'/>
                </button>
                <button id='pencilButton' type='button' onclick='usePencil()' title='Écrire au crayon'>
                    <img src="img/pencil.svg" alt='Crayon'/>
                </button>
                <button type='button' onclick='erasePencil()' title='Effacer le crayon'>
                    <img src="img/pencil-eraser.svg" alt="Gomme blanche"/>
                </button>
                <button class="warning" type='button' onclick='eraseAll()' title='Effacer tout'>
                    <img src="img/ink-eraser.svg" alt="Gomme bleue"/>
                </button>
                <button id='undoButton' type='button' onclick='undo()' disabled title='Annuler' accesskey='z'>
                    <img src="img/undo.svg" alt="Annuler"/>
                </button>
            </div>
        </form>
        <ul id="contextMenu" class="context-menu">
        </ul>
        <footer>
            <a href=''>Lien vers cette grille</a><br/>
            <a href='.'>Nouvelle grille</a>
            <div class="credits">Icons made by <a href="https://www.flaticon.com/authors/freepik" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a></div>
        </footer>
    </body>
</html>
<?php
    } else {
        $grid = new Grid();
        $grid->generate();

        header("HTTP/1.0 400 Bad Request", true, 400);

        $urlDir = $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["DOCUMENT_URI"]);
        $urlExample = $urlDir . "/" . $grid->toString();
?>
<!DOCTYPE html>
<html lang='fr'>
    <head>
        <meta charset='utf-8' />
        <meta name='viewport' content='width=device-width' />
        <title>Grille incorrecte</title>
        <link rel='stylesheet' type='text/css' href='style.css' />
        <link rel="icon" type="image/png" href="favicon.png">
    </head>
    <body>
        <header>
            <h1>Grille incorrecte</h1>
        </header>
        L'adresse URL doit être de la forme : <?=$urlDir?>/<em>grille</em>,<br/>
        <em>grille</em> étant une suite de 81 caractères représentant la grille de gauche à droite puis de haut en bas, soit :
        <ul>
            <li>un chiffre entre 1 et 9 pour les cases connues</li>
            <li>un point pour les case vides</li>
        </ul>
        Exemple : <a href='<?=$urlExample?>'><?=$urlExample?></a><br/>
    </body>
</html>
<?php
    }
?>
