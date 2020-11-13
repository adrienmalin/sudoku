<?php
    require("classes.php");
    $grid = new Grid();
    $grid->generate();

    header("HTTP/1.0 404 Not Found", true, 404);

    $urlDir = $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["DOCUMENT_URI"]);
    $urlExample = $urlDir . "/" . $grid->toString();
?>
<!DOCTYPE html>
<html lang='fr'>
    <head>
        <meta charset='utf-8' />
        <meta name='viewport' content='width=device-width' />
        <title>Sudoku non trouvé</title>
        <link rel='stylesheet' type='text/css' href='style.css' />
        <link rel="icon" type="image/png" href="favicon.png">
    </head>
    <body>
        <header>
            <h1>#404</h1>
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
