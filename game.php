<?php
    require("classes.php");

    $gridStr = basename(strip_tags($_SERVER["REQUEST_URI"]));
    // URL contains grid
    if (preg_match("#^[1-9.]{81}$#", $gridStr)) {
?>
<!DOCTYPE html>
<html lang='fr'>
    <head>
        <meta charset='utf-8' />
        <meta name='viewport' content='width=device-width' />
        <title>Sudoku</title>
        <link rel='stylesheet' type='text/css' href='style.css' />
        <script src='app.js'></script>
        <link rel="icon" type="image/png" href="favicon.png">
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
<?php
        for ($row = 0; $row < 9; $row++) {
?>
                        <tr>
<?php
            for ($column = 0; $column < 9; $column++) {
                $value = $gridStr[9*$row+$column];
?>
                            <td>
<?php
                if ($value == UNKNOWN) {
?>
                                <input type='number' min='1' max='9' step='1' value='' title='Valeurs possibles [Clic-droit]'/>
<?php
                } else {
?>
                                <input type='number' min='1' max='9' step='1' value='<?=$value?>' disabled/>
<?php
                }
?>
                            </td>
<?php
            }
?>
                        </tr>
<?php
        }
?>
                    </tbody>
                </table>
            </div>
            <div id='buttons' class='highlight-buttons'>
<?php
        for($value=1; $value<=9; $value++) {
            echo "                <button type='button' onclick='highlight(\"$value\")' title='Surligner les $value'  accesskey='$value'>$value</button>\n";
        }
?>
            </div>
            <div>
                <button id='inkPenButton' type='button' onclick='useInkPen()' title='Stylo' class='pressed'>
                    <img src="img/ink-pen.png" alt='Stylo' width=16 height=16/>
                </button>
                <button id='pencilButton' type='button' onclick='usePencil()' title='Crayon'>
                    <img src="img/pencil.png" alt='Crayon' width=16 height=16/>
                </button>
                <button type='button' onclick='erasePencil()' title='Effacer le crayon'>
                    <img src="img/pencil-eraser.png" alt="Gomme blanche" width=16 height=16/>
                </button>
                <button class="warning" type='button' onclick='eraseAll()' title='Effacer tout'>
                    <img src="img/ink-eraser.png" alt="Gomme bleue" width=16 height=16/>
                </button>
                <button id='undoButton' type='button' onclick='undo()' disabled title='Annuler' accesskey='z'>
                    <img src="img/undo.png" alt="Annuler" width=16 height=16/>
                </button>
                <!--<input id='colorPicker' type='color' title='Changer de couleur de stylo' value='#00008b'/> -->
            </div>
        </form>
        <ul id="contextMenu" class="context-menu">
        </ul>
        <footer>
            <a href=''>Lien vers cette grille</a><br/>
            <a href='.'>Nouvelle grille</a>
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
<html>
    <head>
        <meta charset='utf-8' />
        <meta name='viewport' content='width=device-width' />
        <title>Grille incorrecte</title>
        <link rel='stylesheet' type='text/css' href='style.css' />
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
