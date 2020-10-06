<?php
    $gridStr = basename(strip_tags($_SERVER["REQUEST_URI"]));
    // URL contains grid
    if (preg_match("#^[1-9?]{81}$#", $gridStr)) {
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width" />
        <title>Sudoku</title>
        <link rel="stylesheet" type="text/css" href="style.css" />
        <script src="sudoku.js"></script>
    </head>
    <body>
        <header>
            <h1>Sudoku</h1>
        </header>
        <form id="sudokuForm">
            <div>
                <table id="grid">
                    <tbody>
<?php
        for ($row = 0; $row < 9; $row++) {
?>
                        <tr>
<?php
            for ($column = 0; $column < 9; $column++) {
                switch($row%3) {
                    case 0: $classRegionRow = "regionTop"; break;
                    case 1: $classRegionRow = "regionMiddle"; break;
                    case 2: $classRegionRow = "regionBottom"; break;
                }
                switch($column%3) {
                    case 0: $classRegionColumn = "regionLeft"; break;
                    case 1: $classRegionColumn = "regionCenter"; break;
                    case 2: $classRegionColumn = "regionRight"; break;
                }
                $value = $gridStr[9*$row+$column];
                if ($value == "?") {
                    $value = "";
                    $disabled = "";
                } else {
                    $disabled = " disabled";
                }
                echo "                            <td class='$classRegionRow $classRegionColumn'><input type='text' inputmode='numeric' minlength=0 maxlength=1 value='$value'$disabled/></td>\n";
            }
?>
                        </tr>
<?php
        }
?>
                    </tbody>
                </table>
            </div>
            <div id="buttons" class="buttons">
            	🔎
<?php
        for($value=1; $value<=9; $value++) {
            echo "                <button type='button' onclick='showValue(this.textContent)'>$value</button>\n";
        }
?>
            	<label for="colorPicjer">✏️</label>
                <input id='colorPicker' type="color" value='#00008b'/>
            </div>
            <div>
                <button type="reset">Tout effacer</button>
            </div>
        </form>
        <div id=help>
            Remplissez la grille de sorte que chaque ligne, colonne et région (carré de 3×3 cases) contienne tous les chiffres de 1 à 9.
        </div>
        <div id=links>
            <a href="">Lien vers cette grille</a><br/>
            <a href=".">Nouvelle grille</a>
        </div>
    </body>
</html>
<?php
    } else {
        header("HTTP/1.0 400 Bad Request", true, 400);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width" />
        <title>Grille incorrecte</title>
        <link rel="stylesheet" type="text/css" href="style.css" />
    </head>
    <body>
        <header>
            <h1>Grille incorrecte</h1>
        </header>
        L'adresse URL doit être de la forme : <?=$_SERVER["REQUEST_SCHEME"]?>://<?=$_SERVER["HTTP_HOST"] . dirname($_SERVER["DOCUMENT_URI"])?>/<em>grille</em>,<br/>
        <em>grille</em> étant une suite de 81 caractères représentant la grille de gauche à droite puis de haut en bas, soit :
        <ul>
            <li>un chiffre entre 1 et 9 pour les cases connues</li>
            <li>? pour les case vides</li>
        </ul>
        <a href=".">Cliquez ici pour générer une nouvelle grille</a>
    </body>
</html>
<?php
    }
?>
