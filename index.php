<?php
    function isUnknown($box) {
        return $box->value == "?";
    }
    
    function easyFirst($box1, $box2) {
        return count($box1->allowedValues) - count($box2->allowedValues);
    }
    
    function array_unset_value($value, &$array) {
        $key = array_search($value, $array);
        if ($key !== false) {
            unset($array[$key]);
            return true;
        } else {
            return false;
        }
    }
        
    class Box {
        public $values = array("1", "2", "3", "4", "5", "6", "7", "8", "9");
    
        function __construct($rowId, $columnId, $regionId) {
            $this->value = "?";
            $this->rowId = $rowId;
            $this->columnId = $columnId;
            $this->regionId = $regionId;
            $this->allowedValues = $this->values;
            $this->testValueWasAllowed = array();
            $this->neighbourhood = array();
        }
        
        function searchAllowedValues() {
            $this->allowedValues = $this->values;
            forEach($this->neighbourhood as $neighbour) {
                if ($neighbour->value != "?")
                    array_unset_value($neighbour->value, $this->allowedValues);
            }
        }
    }
    
    class Grid {
        function __construct() {
            $this->boxes = array();
            $this->rows = array_fill(0, 9, array());
            $this->columns = array_fill(0, 9, array());
            $this->regions = array_fill(0, 9, array());
            for ($regionRowId = 0; $regionRowId < 3; $regionRowId++) {
                for($regionColumnId = 0; $regionColumnId < 3; $regionColumnId++) {
                    for ($rowId = 3*$regionRowId; $rowId < 3*($regionRowId+1); $rowId++) {
                        for ($columnId = 3*$regionColumnId; $columnId < 3*($regionColumnId+1); $columnId++) {
                            $regionId = 3*$regionRowId + $regionColumnId;
                            $box = new Box($rowId, $columnId, $regionId);
                            $this->boxes[] = $box;
                            $this->rows[$rowId][] = $box;
                            $this->columns[$columnId][] = $box;
                            $this->regions[$regionId][] = $box;
                        }
                    }
                }
            }
            
            // box.neighbourhood: boxes in the same row, column and region as box
            foreach($this->boxes as $box) {
                foreach(array_merge($this->rows[$box->rowId], $this->columns[$box->columnId], $this->regions[$box->regionId]) as $neighbour)
                    if ($box != $neighbour && !in_array($neighbour, $box->neighbourhood))
                        $box->neighbourhood[] = $neighbour;
            }
            
            // Init with a shuffle row
            $values = array("1", "2", "3", "4", "5", "6", "7", "8", "9");
            shuffle($values);
            forEach($values as $columnId => $value) {
                $box = $this->rows[0][$columnId];
                $box->value = $value;
                forEach($box->neighbourhood as $neighbour)
                    array_unset_value($box->value, $neighbour->allowedValues);
            }
            // Fill grid
            $this->findSolutions(true, 1, 4)->current();
            
            // Remove clues while there is still a unique solution
            $untestedBoxes = $this->boxes;
            shuffle($untestedBoxes);
            $nbClues = count($untestedBoxes);
            while(count($untestedBoxes)) {
                $testBoxes = array(array_pop($untestedBoxes));
                if ($nbClues >=30)
                    $testBoxes[] = $this->rows[8-$testBoxes[0]->rowId][8-$testBoxes[0]->columnId];
                if ($nbClues >=61) {
                    $testBoxes[] = $this->rows[8-$testBoxes[0]->rowId][$testBoxes[0]->columnId];
                    $testBoxes[] = $this->rows[$testBoxes[0]->rowId][8-$testBoxes[0]->columnId];
                }
                $erasedValues = array();
                forEach($testBoxes as $testBox) {
                    $erasedValues[] = $testBox->value;
                    $testBox->value = "?";
                    forEach($testBox->neighbourhood as $neighbour)
                        $neighbour->searchAllowedValues();
                }
                $solutions = array();
                forEach($this->findSolutions(false, 2, 4) as $solution) $solutions[$solution] = true;
                if (count($solutions) == 1) {
                    $nbClues -= count($testBoxes);
                    forEach($testBoxes as $testBox) array_unset_value($testBox, $untestedBoxes);
                } else {
                    forEach($testBoxes as $i => $box) {
                        $box->value = $erasedValues[$i];
                        forEach($box->neighbourhood as $neighbour) array_unset_value($box->value, $neighbour->allowedValues);
                    }
                }
            }
        }
        
        function findSolutions($randomized=false, $maxSolutions=1, $maxTries=4) {
            $emptyBoxes = array_filter($this->boxes, "isUnknown");
            if (count($emptyBoxes)) {
                if ($randomized) shuffle($emptyBoxes);
                usort($emptyBoxes, "easyFirst");
                $testBox = $emptyBoxes[0];
                $nbSolutionsFound = 0;
                $nbTries = 0;
                if ($randomized) shuffle($testBox->allowedValues);
                foreach($testBox->allowedValues as $testBox->value) {
                    foreach($testBox->neighbourhood as $neighbour)
                        $neighbour->testValueWasAllowed[] = array_unset_value($testBox->value, $neighbour->allowedValues);
                    $correctGrid = true;
                    foreach(array_filter($testBox->neighbourhood, "isUnknown") as $neighbour) {
                        if (count($neighbour->allowedValues) == 0) $correctGrid = false;
                    }
                    if ($correctGrid) {
                        foreach($this->findSolutions($randomized, $maxSolutions-$nbSolutionsFound, $maxTries) as $solution) {
                            yield $solution;
                            $nbSolutionsFound++;
                        }
                        
                    }
                    forEach($testBox->neighbourhood as $neighbour)
                        if (array_pop($neighbour->testValueWasAllowed))
                            $neighbour->allowedValues[] = $testBox->value;
                    if (($maxSolutions && $nbSolutionsFound >= $maxSolutions) || ++$nbTries >= $maxTries) break;
                }
                $testBox->value = "?";
            } else {
                yield $this->toString();
            }
        }
        
        function toString() {
            $str = "";
            foreach($this->rows as $row) {
                forEach($row as $box) {
                    $str .= ($box->value? $box->value : "?");
                }
            }
            return $str;
        }
    }
    
    $grid = new Grid();
    header("Location: " . $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["DOCUMENT_URI"]) . "/" . $grid->toString());
    exit();
?>
