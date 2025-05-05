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
<?php for ($row = 0; $row < 81; $row += 9): ?>
                                <tr class="input-group d-inline-block w-auto">
    <?php for ($column = 0; $column < 9; $column++): $value = $currentGrid[$row+$column]; ?>
        <?php if ($value == UNKNOWN): ?>
                                    <td><input type='number' min='1' max='9' step='1' value=''  class='form-control' /></td>
        <?php else: ?>
                                    <td><input type='number' min='1' max='9' step='1' value='<?=$value?>' class='form-control' disabled /></td>
        <?php endif ?>
    <?php endfor?>
                                </tr>
<?php endfor?>
                            </tbody>
                        </table>
                    </form>
                    <div class='d-flex mb-4'>
                        <div id='insertRadioGroup' class='radioGroup btn-group flex-fill'>
                            <input type='radio' class='btn-check' id='insertRadio0' value=''  name='insertRadioGroup' onclick='insert(this)' accesskey='0' checked  /><label for='insertRadio0' class='btn btn-primary' title='Clavier'><i class="ri-input-cursor-move"></i></label>
<?php for($value=1; $value<=9; $value++): ?>
                            <input type='radio' class='btn-check' id='insertRadio<?=$value?>' value='<?=$value?>' name='insertRadioGroup' onclick='insert(this)' accesskey='<?=$value?>' disabled />
                            <label for='insertRadio<?=$value?>' class='btn btn-primary' title='Insérer un <?=$value?>'><?=$value?></label>
<?php endfor ?>
                        </div>
                    </div>
                    <div class='mb-3'>
<?php if (isset($warning)): ?>
                        <strong>⚠️ <?=$warning?> ⚠️</strong><br/>
<?php else: ?>
                    Remplissez la grille de sorte que chaque ligne, colonne et région (carré de 3×3 cases) contienne tous les chiffres de 1 à 9.
<?php endif?>
                    </div>
                </div>
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
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
        <script src='sudoku.js' defer></script>
        <script>navigator?.serviceWorker.register('service-worker.js')</script>
    </body>

</html>