<?php http_response_code(400); ?>
<!DOCTYPE html>
<html lang='fr'>
    <head>
        <?php require("head.php"); ?>
        <title>Requête incorrecte</title>
    </head>
    <body>
        <nav class="navbar mb-4">
            <h1 class="display-4 text-center m-auto">Sudoku</h1>
        </nav>
        <main class="container my-4">
            <header>
                <h1 class="mb-4">Requête incorrecte</h1>
            </header>
            L'adresse URL doit être de la forme :<br/>
            <?=$dirUrl?>/?<em>grille</em><br/>
            <em>grille</em> étant une suite de 81 caractères représentant la grille de gauche à droite puis de haut en bas, soit :
            <ul>
                <li>un chiffre entre 1 et 9 pour les cases connues</li>
                <li>un point pour les case vides</li>
            </ul>
            Exemple :<br/>
            <a href='<?=$newGridUrl?>'><?=$newGridUrl?></a>
        </main>
    </body>
</html>
