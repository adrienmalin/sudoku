        <script src="https://getbootstrap.com/docs/5.3/assets/js/color-modes.js"></script>
        <meta charset="utf-8" />
        <title>Sudoku</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
        <link href="style.css" rel="stylesheet" type="text/css" />

        <link href="thumbnail.php?grid=<?=$currentGrid?>&size=196" sizes="196x196" rel="icon" type="image/png">
        <link href="thumbnail.php?grid=<?=$currentGrid?>&size=160" sizes="160x160" rel="icon" type="image/png">
        <link href="thumbnail.php?grid=<?=$currentGrid?>&size=96"  sizes="96x96"   rel="icon" type="image/png">
        <link href="thumbnail.php?grid=<?=$currentGrid?>&size=16"  sizes="16x16"   rel="icon" type="image/png">
        <link href="thumbnail.php?grid=<?=$currentGrid?>&size=32"  sizes="32x32"   rel="icon" type="image/png">
        <link href="thumbnail.php?grid=<?=$currentGrid?>&size=152" sizes="152x152" rel="apple-touch-icon">
        <link href="thumbnail.php?grid=<?=$currentGrid?>&size=144" sizes="144x144" rel="apple-touch-icon">
        <link href="thumbnail.php?grid=<?=$currentGrid?>&size=120" sizes="120x120" rel="apple-touch-icon">
        <link href="thumbnail.php?grid=<?=$currentGrid?>&size=114" sizes="114x114" rel="apple-touch-icon">
        <link href="thumbnail.php?grid=<?=$currentGrid?>&size=57"  sizes="57x57"   rel="apple-touch-icon">
        <link href="thumbnail.php?grid=<?=$currentGrid?>&size=72"  sizes="72x72"   rel="apple-touch-icon">
        <link href="thumbnail.php?grid=<?=$currentGrid?>&size=60"  sizes="60x60"   rel="apple-touch-icon">
        <link href="thumbnail.php?grid=<?=$currentGrid?>&size=76"  sizes="76x76"   rel="apple-touch-icon">
        <link href="manifest.php?grid=<?=$currentGrid?>" rel="manifest">

        <meta property="og:title" content="Sudoku" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="<?=$_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$_SERVER["DOCUMENT_URI"]?>" />
        <meta property="og:image" content="<?=$_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].dirname($_SERVER["DOCUMENT_URI"])?>/thumbnail.php?size=200&grid=<?=$currentGrid?>" />
        <meta property="og:image:width" content="200" />
        <meta property="og:image:height" content="200" />
        <meta property="og:description" content="Remplissez la grille de sorte que chaque ligne, colonne et région (carré de 3×3 cases) contienne tous les chiffres de 1 à 9." />
        <meta property="og:locale" content="fr_FR" />
        <meta property="og:site_name" content="<?=$_SERVER["HTTP_HOST"]?>" />
