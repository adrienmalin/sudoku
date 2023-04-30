<meta charset="utf-8" />
<title>Sudoku</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />

<link href="https://cdn.jsdelivr.net/npm/bootstrap-dark-5@1.1.3/dist/css/bootstrap-dark.min.css" rel="stylesheet" type="text/css" title="Automatique" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="alternate stylesheet" type="text/css" title="Clair" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-dark-5@1.1.3/dist/css/bootstrap-night.min.css" rel="alternate stylesheet" type="text/css" title="Sombre" />
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="apple-touch-icon" href="thumbnail.php?size=57&grid=<?=$currentGrid?>" sizes="57x57">
<link rel="apple-touch-icon" href="thumbnail.php?size=114&grid=<?=$currentGrid?>" sizes="114x114">
<link rel="apple-touch-icon" href="thumbnail.php?size=72&grid=<?=$currentGrid?>" sizes="72x72">
<link rel="apple-touch-icon" href="thumbnail.php?size=144&grid=<?=$currentGrid?>" sizes="144x144">
<link rel="apple-touch-icon" href="thumbnail.php?size=60&grid=<?=$currentGrid?>" sizes="60x60">
<link rel="apple-touch-icon" href="thumbnail.php?size=120&grid=<?=$currentGrid?>" sizes="120x120">
<link rel="apple-touch-icon" href="thumbnail.php?size=76&grid=<?=$currentGrid?>" sizes="76x76">
<link rel="apple-touch-icon" href="thumbnail.php?size=152&grid=<?=$currentGrid?>" sizes="152x152">
<link rel="icon" type="image/png" href="thumbnail.php?size=196&grid=<?=$currentGrid?>" sizes="196x196">
<link rel="icon" type="image/png" href="thumbnail.php?size=160&grid=<?=$currentGrid?>" sizes="160x160">
<link rel="icon" type="image/png" href="thumbnail.php?size=96&grid=<?=$currentGrid?>" sizes="96x96">
<link rel="icon" type="image/png" href="thumbnail.php?size=16&grid=<?=$currentGrid?>" sizes="16x16">
<link rel="icon" type="image/png" href="thumbnail.php?size=32&grid=<?=$currentGrid?>" sizes="32x32">
<link rel="manifest" href="manifest.php?grid=<?=$currentGrid?>">
<meta property="og:title" content="Sudoku" />
<meta property="og:type" content="website" />
<meta property="og:url"
    content="<?=$_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$_SERVER["DOCUMENT_URI"]?>" />
<meta property="og:image"
    content="<?=$_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].dirname($_SERVER["DOCUMENT_URI"])?>/thumbnail.php?size=200&grid=<?=$currentGrid?>" />
<meta property="og:image:width" content="200" />
<meta property="og:image:height" content="200" />
<meta property="og:description"
    content="Remplissez la grille de sorte que chaque ligne, colonne et région (carré de 3×3 cases) contienne tous les chiffres de 1 à 9." />
<meta property="og:locale" content="fr_FR" />
<meta property="og:site_name" content="<?=$_SERVER["HTTP_HOST"]?>" />
<script src='js/sudoku.js'></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>