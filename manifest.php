<?php
    if (isset($_GET["grid"]))
        $currentGrid = $_GET["grid"];
    else
        $currentGrid = ".";
?>
{
    "short_name": "Sudoku",
    "name": "Sudoku",
    "description": "Remplissez la grille de sorte que chaque ligne, colonne et région (carré de 3×3 cases) contienne tous les chiffres de 1 à 9.",
    "icons": [{
        "src": "thumbnail.php?size=48&grid=<?=$currentGrid?>",
        "sizes": "48x48",
        "type": "image/png"
    }, {
        "src": "thumbnail.php?size=72&grid=<?=$currentGrid?>",
        "sizes": "72x72",
        "type": "image/png"
    }, {
        "src": "thumbnail.php?size=96&grid=<?=$currentGrid?>",
        "sizes": "96x96",
        "type": "image/png"
    }, {
        "src": "thumbnail.php?size=144&grid=<?=$currentGrid?>",
        "sizes": "144x144",
        "type": "image/png"
    }, {
        "src": "thumbnail.php?size=168&grid=<?=$currentGrid?>",
        "sizes": "168x168",
        "type": "image/png"
    }, {
        "src": "thumbnail.php?size=192&grid=<?=$currentGrid?>",
        "sizes": "192x192",
        "type": "image/png"
    }],
    "start_url": ".",
    "background_color": "#fff",
    "display": "standalone",
    "scope": ".",
    "theme_color": "#fff",
    "orientation": "portrait-primary",
    "shortcuts": [
        {
            "name": "Sudoku : cette grille",
            "short_name": "Ce sudoku",
            "description": "Continuer cette grille de sudoku",
            "url": "<?=$currentGrid?>",
            "icons": [{
                    "src": "thumbnail.php?size=48&grid=<?=$currentGrid?>",
                    "sizes": "48x48",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.php?size=72&grid=<?=$currentGrid?>",
                    "sizes": "72x72",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.php?size=96&grid=<?=$currentGrid?>",
                    "sizes": "96x96",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.php?size=144&grid=<?=$currentGrid?>",
                    "sizes": "144x144",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.php?size=168&grid=<?=$currentGrid?>",
                    "sizes": "168x168",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.php?size=192&grid=<?=$currentGrid?>",
                    "sizes": "192x192",
                    "type": "image/png"
            }]
        },
        {
            "name": "Sudoku : Grille vierge",
            "short_name": "Sudoku vierge",
            "description": "Grille de sudoku vierge",
            "url": ".................................................................................",
            "icons": [{
                    "src": "thumbnail.php?size=48&grid=.................................................................................",
                    "sizes": "48x48",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.php?size=72&grid=.................................................................................",
                    "sizes": "72x72",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.php?size=96&grid=.................................................................................",
                    "sizes": "96x96",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.php?size=144&grid=.................................................................................",
                    "sizes": "144x144",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.php?size=168&grid=.................................................................................",
                    "sizes": "168x168",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.php?size=192&grid=.................................................................................",
                    "sizes": "192x192",
                    "type": "image/png"
            }]
        },
        {
            "name": "Sudoku : Nouvelle grille",
            "short_name": "Nouveau sudoku",
            "description": "Nouvelle grille de sudoku",
            "url": ".",
            "icons": [{
                    "src": "thumbnail.php?size=48&grid=.528.3....4.9.1...39.562......73.129...1.64.7...42.3656.13.5...28.6.4...4.5287...",
                    "sizes": "48x48",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.php?size=72&grid=.528.3....4.9.1...39.562......73.129...1.64.7...42.3656.13.5...28.6.4...4.5287...",
                    "sizes": "72x72",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.php?size=96&grid=.528.3....4.9.1...39.562......73.129...1.64.7...42.3656.13.5...28.6.4...4.5287...",
                    "sizes": "96x96",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.php?size=144&grid=.528.3....4.9.1...39.562......73.129...1.64.7...42.3656.13.5...28.6.4...4.5287...",
                    "sizes": "144x144",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.php?size=168&grid=.528.3....4.9.1...39.562......73.129...1.64.7...42.3656.13.5...28.6.4...4.5287...",
                    "sizes": "168x168",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.php?size=192&grid=.528.3....4.9.1...39.562......73.129...1.64.7...42.3656.13.5...28.6.4...4.5287...",
                    "sizes": "192x192",
                    "type": "image/png"
            }]
        }
    ]
}
