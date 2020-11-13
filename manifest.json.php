<?php
    $gridStr = strip_tags($_GET['grid']);
?>
{
    "short_name": "Sudoku",
    "name": "Sudoku",
    "description": "Remplissez la grille de sorte que chaque ligne, colonne et région (carré de 3×3 cases) contienne tous les chiffres de 1 à 9.",
    "icons": [{
        "src": "thumbnail.png?grid=<?=$gridStr?>&size=48",
        "sizes": "48x48",
        "type": "image/png"
    }, {
        "src": "thumbnail.png?grid=<?=$gridStr?>&size=72",
        "sizes": "72x72",
        "type": "image/png"
    }, {
        "src": "thumbnail.png?grid=<?=$gridStr?>&size=96",
        "sizes": "96x96",
        "type": "image/png"
    }, {
        "src": "thumbnail.png?grid=<?=$gridStr?>&size=144",
        "sizes": "144x144",
        "type": "image/png"
    }, {
        "src": "thumbnail.png?grid=<?=$gridStr?>&size=168",
        "sizes": "168x168",
        "type": "image/png"
    }, {
        "src": "thumbnail.png?grid=<?=$gridStr?>&size=192",
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
            "url": "<?=$gridStr?>",
            "icons": [{
                    "src": "thumbnail.png?grid=<?=$gridStr?>&size=48",
                    "sizes": "48x48",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.png?grid=<?=$gridStr?>&size=72",
                    "sizes": "72x72",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.png?grid=<?=$gridStr?>&size=96",
                    "sizes": "96x96",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.png?grid=<?=$gridStr?>&size=144",
                    "sizes": "144x144",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.png?grid=<?=$gridStr?>&size=168",
                    "sizes": "168x168",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.png?grid=<?=$gridStr?>&size=192",
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
                    "src": "thumbnail.png?grid=.................................................................................&size=48",
                    "sizes": "48x48",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.png?grid=.................................................................................&size=72",
                    "sizes": "72x72",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.png?grid=.................................................................................&size=96",
                    "sizes": "96x96",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.png?grid=.................................................................................&size=144",
                    "sizes": "144x144",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.png?grid=.................................................................................&size=168",
                    "sizes": "168x168",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.png?grid=.................................................................................&size=192",
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
                    "src": "thumbnail.png?grid=.528.3....4.9.1...39.562......73.129...1.64.7...42.3656.13.5...28.6.4...4.5287...&size=48",
                    "sizes": "48x48",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.png?grid=.528.3....4.9.1...39.562......73.129...1.64.7...42.3656.13.5...28.6.4...4.5287...&size=72",
                    "sizes": "72x72",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.png?grid=.528.3....4.9.1...39.562......73.129...1.64.7...42.3656.13.5...28.6.4...4.5287...&size=96",
                    "sizes": "96x96",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.png?grid=.528.3....4.9.1...39.562......73.129...1.64.7...42.3656.13.5...28.6.4...4.5287...&size=144",
                    "sizes": "144x144",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.png?grid=.528.3....4.9.1...39.562......73.129...1.64.7...42.3656.13.5...28.6.4...4.5287...&size=168",
                    "sizes": "168x168",
                    "type": "image/png"
                }, {
                    "src": "thumbnail.png?grid=.528.3....4.9.1...39.562......73.129...1.64.7...42.3656.13.5...28.6.4...4.5287...&size=192",
                    "sizes": "192x192",
                    "type": "image/png"
            }]
        }
    ]
}
