<!DOCTYPE html>
<html lang='fr' prefix="og: https://ogp.me/ns#">

    <head>
<?php require_once("head.php") ?>
    </head>

    <body>
        <nav class="navbar mb-4">
            <h1 class="display-4 text-center m-auto">Sudoku</h1>
        </nav>
        <div class="row g-0">
            <main class="col-md-6 order-md-1">
                <div class="text-center m-auto" style="width: min-content;">
                    <div class='d-flex justify-content-between mb-2'>
                        <div class='btn-group'>
                            <input type='radio' id='inkPenRadio' class='btn-check' name='penRadioGroup' checked />
                            <label for='inkPenRadio' class='btn btn-primary' title='Écrire un chiffre'><i class="ri-ball-pen-fill"></i></label>
                            <input type='radio' id='pencilRadio' class='btn-check' name='penRadioGroup' />
                            <label for='pencilRadio' class='btn btn-primary' title='Prendre des notes'><i class="ri-pencil-fill"></i></label>
                            <input type='radio' id='eraserRadio' class='btn-check' name='penRadioGroup' />
                            <label for='eraserRadio' class='btn btn-primary' title='Effacer une case'><i class="ri-eraser-fill"></i></label>
                        </div>
                        <input type="color" class="btn-check" id="colorPickerInput" title="Changer la couleur" oninput="changeColor()"/>
                        <label id="colorPickerLabel" for="colorPickerInput" class="btn btn-primary" title="Changer de couleur"><i class="ri-palette-fill"></i></label>
                        <div class='btn-group'>
                            <input type='checkbox' id='sightCheckbox' class='btn-check' onclick='highlighterCheckbox.checked = false; highlight()' />
                            <label for='sightCheckbox' class='btn btn-info' title='Surligner la ligne, la colonne et la région de la case survolée'><i class="ri-focus-3-line"></i></label>
                            <input type='checkbox' id='highlighterCheckbox' class='btn-check' onclick='sightCheckbox.checked = false; highlight()' />
                            <label for='highlighterCheckbox' class='btn btn-info' title='Surligner les lignes, colonnes et régions contenant déjà le chiffre sélectionné'><i class="ri-mark-pen-fill"></i></label>
                        </div>
                        <button id="hintButton" type="button" class='btn btn-info' onclick="showHint()" title="Montrer une case avec une seule possibilité" accesskey="H" disabled=""><i class="ri-lightbulb-line"></i></button>
                        <a id='restartLink' class='btn btn-primary disabled' href="" title='Recommencer'><i class="ri-restart-line"></i></a>
                        <button id='undoButton' type='button' class='btn btn-primary' onclick='window.history.back()' disabled title='Annuler' accesskey='Z'><i class="ri-arrow-go-back-fill"></i></button>
                    </div>
                    <form id='sudokuForm' class='needs-validation' novalidate>
                        <table id='grid' class='table mb-2'>
                            <tbody>
<?php
        for ($row = 0; $row < 81; $row += 9) {
?>
                                <tr class="input-group d-inline-block w-auto">
<?php
        for ($column = 0; $column < 9; $column++) {
            $value = $currentGrid[$row+$column];
            if ($value == UNKNOWN) {
?>
                                    <td><input type='number' min='1' max='9' step='1' value=''  class='form-control' /></td>
<?php
                } else {
?>
                                    <td><input type='number' min='1' max='9' step='1' value='<?=$value?>' class='form-control' disabled /></td>
<?php
            }                                                            
        }
?>
                            </tr>
<?php
   }
?>
                        </tbody>
                    </table>
                </form>
                <div class='d-flex mb-4'>
                    <div id='insertRadioGroup' class='radioGroup btn-group flex-fill'>
                        <input type='radio' class='btn-check' id='insertRadio0' value=''  name='insertRadioGroup' onclick='insert(this)' accesskey='0' checked  /><label for='insertRadio0' class='btn btn-primary' title='Clavier'><i class="ri-input-cursor-move"></i></label>
<?php
        for($value=1; $value<=9; $value++) {
            echo "                        <input type='radio' class='btn-check' id='insertRadio$value' value='$value' name='insertRadioGroup' onclick='insert(this)' accesskey='$value' disabled /><label for='insertRadio$value' class='btn btn-primary' title='Insérer un $value'>$value</label>\n";
        }
?>
                    </div>
                </div>
                <div class='mb-3'><?php
    if (isset($warning))
        echo("<strong>⚠️ $warning ⚠️</strong><br/>");
    else
        echo("Remplissez la grille de sorte que chaque ligne, colonne et région (carré de 3×3 cases) contienne tous les chiffres de 1 à 9.")
?></div>
            </main>
            <aside class="col-md-3 text-center text-md-start">
                <div class="d-flex flex-column flex-shrink-0 p-3">
                    <ul class="nav nav-pills flex-column">
                        <li><a href="." class="nav-link link-body-emphasis">Nouvelle grille</a></li>
                        <li><a href="" class="nav-link link-body-emphasis">Lien vers cette grille</a></li>
                        <li><a href="?................................................................................." class="nav-link link-body-emphasis">Grille vierge</a></li>
                        <li><a id="fixGridLink" href="" class="nav-link link-body-emphasis">Figer la grille</a></li>
                        <li><a href="https://git.malingrey.fr/adrien/Sudoku" class="nav-link link-body-emphasis">Code source</a></li>
                        <li><a href=".." class="nav-link link-body-emphasis">Autres jeux</a></li>
                    </ul>
                </div>
            </aside>
        </div>
        <ul id='contextMenu' class='context-menu modal-content shadow list-group w-auto position-absolute'></ul>
        <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
            <symbol id="check2" viewBox="0 0 16 16">
                <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"></path>
            </symbol>
            <symbol id="circle-half" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"></path>
            </symbol>
            <symbol id="moon-stars-fill" viewBox="0 0 16 16">
                <path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"></path>
                <path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z"></path>
            </symbol>
            <symbol id="sun-fill" viewBox="0 0 16 16">
                <path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"></path>
            </symbol>
        </svg>
        <div class="dropdown position-fixed bottom-0 end-0 mb-3 me-3 mode-toggle">
            <button class="btn btn-primary py-2 dropdown-toggle d-flex align-items-center" id="theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" aria-label="Toggle theme (dark)">
                <svg class="bi my-1 theme-icon-active" width="1em" height="1em"><use href="#moon-stars-fill"></use></svg>
                <span class="visually-hidden" id="theme-text">Toggle theme</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="theme-text">
                <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
                    <svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em"><use href="#sun-fill"></use></svg>
                    Light
                    <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
                </button>
                </li>
                <li>
                <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="dark" aria-pressed="true">
                    <svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em"><use href="#moon-stars-fill"></use></svg>
                    Dark
                    <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
                </button>
                </li>
                <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="auto" aria-pressed="false">
                    <svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em"><use href="#circle-half"></use></svg>
                    Auto
                    <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
                </button>
                </li>
            </ul>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
        <script src='sudoku.js' defer></script>
    </body>

</html>