<meta charset="utf-8" />
<title>Sudoku</title>
<meta name="viewport" content="width=device-width" />
<link rel="stylesheet" type="text/css" href="css/v4.css" title="Par défaut" />
<link href="css/v1.css" rel="alternate stylesheet" type="text/css" title="v1">
<link href="css/v2.css" rel="alternate stylesheet" type="text/css" title="v2">
<link href="css/v3.css" rel="alternate stylesheet" type="text/css" title="v3">
<link rel="stylesheet" type="text/css" href="fonts/remixicon.css">
<link rel="apple-touch-icon" href="thumbnail.php?size=57" sizes="57x57">
<link rel="apple-touch-icon" href="thumbnail.php?size=114" sizes="114x114">
<link rel="apple-touch-icon" href="thumbnail.php?size=72" sizes="72x72">
<link rel="apple-touch-icon" href="thumbnail.php?size=144" sizes="144x144">
<link rel="apple-touch-icon" href="thumbnail.php?size=60" sizes="60x60">
<link rel="apple-touch-icon" href="thumbnail.php?size=120" sizes="120x120">
<link rel="apple-touch-icon" href="thumbnail.php?size=76" sizes="76x76">
<link rel="apple-touch-icon" href="thumbnail.php?size=152" sizes="152x152">
<link rel="icon" type="image/png" href="thumbnail.php?size=196" sizes="196x196">
<link rel="icon" type="image/png" href="thumbnail.php?size=160" sizes="160x160">
<link rel="icon" type="image/png" href="thumbnail.php?size=96" sizes="96x96">
<link rel="icon" type="image/png" href="thumbnail.php?size=16" sizes="16x16">
<link rel="icon" type="image/png" href="thumbnail.php?size=32" sizes="32x32">
<link rel="manifest" href="manifest.php">
<meta property="og:title" content="Sudoku" />
<meta property="og:type" content="website" />
<meta property="og:url"
    content="<?=$_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].$_SERVER["DOCUMENT_URI"]?>" />
<meta property="og:image"
    content="<?=$_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].dirname($_SERVER["DOCUMENT_URI"])?>/thumbnail.php?size=200" />
<meta property="og:image:width" content="200" />
<meta property="og:image:height" content="200" />
<meta property="og:description"
    content="Remplissez la grille de sorte que chaque ligne, colonne et région (carré de 3×3 cases) contienne tous les chiffres de 1 à 9." />
<meta property="og:locale" content="fr_FR" />
<meta property="og:site_name" content="<?=$_SERVER["HTTP_HOST"]?>" />
<script src='sudoku.js'></script>