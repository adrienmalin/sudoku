<!DOCTYPE html>
<html lang='fr' prefix="og: https://ogp.me/ns#">

    <head>
        <?php require_once("head.php") ?>
    </head>

    <body class="text-center">
        <header>
            <h1 class="display-4 mb-3">Sudoku</h1>
        </header>
        <div class='d-flex justify-content-between mb-2'>
            <div class='btn-group'>
                <input type='radio' id='inkPenRadio' class='btn-check' name='penRadioGroup' checked />
                <label for='inkPenRadio' class='btn btn-primary' title='Écrire un chiffre'>
                    <i class="bi bi-pen-fill"></i>
                </label>
                <input type='radio' id='pencilRadio' class='btn-check' name='penRadioGroup' />
                <label for='pencilRadio' class='btn btn-primary' title='Prendre des notes'>
                    <i class="bi bi-pencil-fill"></i>
                </label>
                <input type='radio' id='eraserRadio' class='btn-check' name='penRadioGroup' />
                <label for='eraserRadio' class='btn btn-primary' title='Effacer une case'>
                    <i class="bi bi-eraser-fill"></i>
                </label>
            </div>
            <div class='btn-group'>
                <input type='checkbox' id='sightCheckbox' class='btn-check' onclick='highlighterCheckbox.checked = false; refreshUI()' />
                <label for='sightCheckbox' class='btn btn-info' title='Surligner la ligne, la colonne et la région de la case survolée'>
                    <i class="bi bi-plus-square-fill"></i>
                </label>
                <input type='checkbox' id='highlighterCheckbox' class='btn-check' onclick='sightCheckbox.checked = false; refreshUI()' />
                <label for='highlighterCheckbox' class='btn btn-info' title='Surligner les lignes, colonnes et régions contenant déjà le chiffre sélectionné'>
                    <i class="bi bi-ui-checks-grid"></i>
                </label>
            </div>
            <button id="hintButton" type="button" class='btn btn-info' onclick="showHint()" title="Montrer une case avec une seule possibilité" accesskey="H" disabled="">
                <i class="bi bi-lightbulb-fill"></i>
            </button>
            <button id='restartButton' type='button' class='btn btn-primary' onclick='restart()' disabled title='Recommencer'>
                <i class="bi bi-skip-start-fill"></i>
            </button>
            <button id='undoButton' type='button' class='btn btn-primary' onclick='undo()' disabled title='Annuler' accesskey='Z'>
                <i class="bi bi-arrow-left"></i>
            </button>
            <button id='saveButton' type='button' class='btn btn-primary' onclick='save()' disabled title='Sauvegarder' accesskey='S'>
                <i class="bi bi-save-fill"></i>
            </button>
        </div>
        <form id='sudokuForm' class='needs-validation' novalidate>
            <table id='grid' class='table mb-2'>
                <tbody>
                    <?php
        for ($row = 0; $row < 81; $row += 9) {
?>
                    <tr class="input-group d-inline-block">
                        <?php
        for ($column = 0; $column < 9; $column++) {
            $value = $currentGrid[$row+$column];
            if ($value == UNKNOWN) {
?>
                        <td><input type='number' min='1' max='9' step='1' value='' class='form-control'
                                title='Valeurs possibles [Clic-droit]' /></td>
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
        <div class='d-flex mb-2'>
            <div id='insertRadioGroup' class='radioGroup btn-group flex-fill'>
                <?php
        for($value=1; $value<=9; $value++) {
            echo "                <input type='radio'class='btn-check' id='insertRadio$value' value='$value' name='insertRadioGroup' onclick='insert(this)' accesskey='$value' disabled /><label for='insertRadio$value' class='btn btn-primary' title='Insérer un $value'>$value</label>\n";
        }
?>
                <input type='radio'class='btn-check' id='insertRadio0' value='' name='insertRadioGroup' onclick='insert(this)' accesskey='0' checked />
                <label for='insertRadio0' class='btn btn-primary' title='Clavier'>
                    <i class="bi bi-cursor-text"></i>
                </label>
            </div>
        </div>
        <div class='mb-3'>
            <?php
    if (isset($warning))
        echo("            <strong>⚠️ $warning ⚠️</strong><br/>\n");
    else
        echo("            Remplissez la grille de sorte que chaque ligne, colonne et région (carré de 3×3 cases) contienne tous les chiffres de 1 à 9.\n")
?>
        </div>
        <ul id='contextMenu' class='context-menu modal-content shadow list-group w-auto position-absolute'></ul>
        <footer>
            <div id='links' class='list-group mb-2'>
                <a href='.' class='list-group-item list-group-item-action'>Nouvelle grille</a>
                <a href='' class='list-group-item list-group-item-action'>Lien vers cette grille</a>
                <a href='?.................................................................................' class='list-group-item list-group-item-action'>Grille
                    vierge</a>
                <a href='' id='fixGridLink' class='list-group-item list-group-item-action'>Figer la grille enregistrée</a>
            </div>
        </footer>
    </body>

</html>