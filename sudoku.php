<!DOCTYPE html>
<html lang='fr' prefix="og: https://ogp.me/ns#">

    <head>
        <?php require_once("head.php") ?>
    </head>

    <body>
        <header>
            <h1>Sudoku</h1>
        </header>
        <div class='toolBar'>
            <div class='radioGroup'>
                <input type='radio' id='inkPenRadio' name='tool' checked /><label for='inkPenRadio'
                    title='Écrire un chiffre'><i class="ri-ball-pen-fill"></i></label>
                <input type='radio' id='pencilRadio' name='tool' /><label for='pencilRadio' title='Prendre des notes'><i
                        class="ri-pencil-fill"></i></label>
                <input type='radio' id='eraserRadio' name='tool' '/><label for='eraserRadio'
                    title='Effacer une case'><i class="ri-eraser-fill"></i></label>
            </div>
            <input id='highlighterCheckbox' type="checkbox" onclick='highlight()' /><label for='highlighterCheckbox'
                title='Surligner les lignes, colonnes et régions contenant déjà le chiffre sélectionné'><i
                    class="ri-mark-pen-fill"></i></label>
            <button id="hintButton" type="button" onclick="showHint()" title="Afficher un indice" accesskey="H"
                disabled=""><i class="ri-lightbulb-flash-fill"></i></button>
            <button id='restartButton' type='button' class='warning' onclick='restart()' disabled title='Recommencer'><i
                    class="ri-restart-fill"></i></button>
            <button id='undoButton' type='button' onclick='undo()' disabled title='Annuler' accesskey='Z'><i
                    class="ri-arrow-go-back-fill"></i></button>
            <button id='saveButton' type='button' onclick='save()' disabled title='Sauvegarder' accesskey='S'><i
                    class="ri-save-3-fill"></i></button>
        </div>
        <form id='sudokuForm'>
            <table id='grid' class='grid'>
                <tbody>
                    <?php
        for ($row = 0; $row < 81; $row += 9) {
?>
                    <tr>
                        <?php
        for ($column = 0; $column < 9; $column++) {
            $value = $currentGrid[$row+$column];
            if ($value == UNKNOWN) {
?>
                        <td><input type='number' min='1' max='9' step='1' value=''
                                title='Valeurs possibles [Clic-droit]' /></td>
                        <?php
                } else {
?>
                        <td><input type='number' min='1' max='9' step='1' value='<?=$value?>' disabled /></td>
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
        <div class='toolBar'>
            <div id='insertRadioGroup' class='radioGroup'>
                <?php
        for($value=1; $value<=9; $value++) {
            echo "                <input type='radio' id='insertRadio$value' value='$value' name='insertRadioGroup' onclick='insert(this)' accesskey='$value'/><label for='insertRadio$value' title='Insérer un $value'>$value</label>\n";
        }
?>
            </div>
        </div>
        <div>
            <?php
    if (isset($warning))
        echo("            <strong>⚠️ $warning ⚠️</strong><br/>\n");
    else
        echo("            Remplissez la grille de sorte que chaque ligne, colonne et région (carré de 3×3 cases) contienne tous les chiffres de 1 à 9.\n")
?>
        </div>
        <ul id='contextMenu' class='context-menu'></ul>
        <footer>
            <div id='links'>
                <a href='.'>Nouvelle grille</a>
                <a href=''>Lien vers cette grille</a>
                <a href='?.................................................................................'>Grille
                    vierge</a>
                <a href='' id='fixGridLink'>Figer la grille enregistrée</a>
            </div>
        </footer>
    </body>

</html>