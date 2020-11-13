<?php
    const UNKNOWN = ".";

    function isKnown($box) {
        return $box->value != UNKNOWN;
    }

    function isUnknown($box) {
        return $box->value == UNKNOWN;
    }
    
    function easyFirst($box1, $box2) {
        return count($box1->candidates) - count($box2->candidates);
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
            $this->value = UNKNOWN;
            $this->rowId = $rowId;
            $this->columnId = $columnId;
            $this->regionId = $regionId;
            $this->candidates = $this->values;
            $this->candidateRemoved = array();
            $this->neighbourhood = array();
        }
        
        function searchCandidates() {
            $this->candidates = $this->values;
            forEach($this->neighbourhood as $neighbour) {
                if ($neighbour->value != UNKNOWN)
                    array_unset_value($neighbour->value, $this->candidates);
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
                for ($rowId = 3*$regionRowId; $rowId < 3*($regionRowId+1); $rowId++) {
                    for($regionColumnId = 0; $regionColumnId < 3; $regionColumnId++) {
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
        }

        function import($gridStr) {
            foreach ($this->boxes as $i => $box) { 
                $box->value = $gridStr[$i];
            }
            forEach($this->boxes as $box) {
                forEach($box->neighbourhood as $neighbour)
                    array_unset_value($box->value, $neighbour->candidates);
            }
        }

        function containsDuplicates() {
            foreach(array_merge($this->rows, $this->columns, $this->regions) as $area) {
                $knownBoxes = array_filter($area, "isKnown");
                foreach($area as $box1) {
                    if ($box1->value != UNKNOWN) {
                        foreach($area as $box2) {
                            if (($box1 !== $box2) && ($box1->value == $box2->value)) {
                                return true;
                            }
                        }
                    }
                }
                return false;
            }
        }
            
        function generate() {
            // Init with a shuffle row
            $values = array("1", "2", "3", "4", "5", "6", "7", "8", "9");
            shuffle($values);
            forEach($this->rows[0] as $columnId => $box) {
                $box->value = $values[$columnId];
                forEach($box->neighbourhood as $neighbour)
                    array_unset_value($box->value, $neighbour->candidates);
            }
            // Fill grid
            $this->solutionsGenerator(true)->current();
            
            // Remove clues while there is still a unique solution
            shuffle($this->boxes);
            $nbClues = count($this->boxes);
            foreach($this->boxes as $testBox) {
                $testBoxes = array($testBox);
                if ($nbClues >=30)
                    $testBoxes[] = $this->rows[8-$testBox->rowId][8-$testBox->columnId];
                if ($nbClues >=61) {
                    $testBoxes[] = $this->rows[8-$testBox->rowId][$testBox->columnId];
                    $testBoxes[] = $this->rows[$testBox->rowId][8-$testBox->columnId];
                }
                $testBoxes = array_filter($testBoxes, "isKnown");
                $erasedValues = array();
                forEach($testBoxes as $testBox) {
                    $erasedValues[] = $testBox->value;
                    $testBox->value = UNKNOWN;
                    forEach($testBox->neighbourhood as $neighbour)
                        $neighbour->searchCandidates();
                }
                if ($this->isValid()) {
                    $nbClues -= count($testBoxes);
                } else {
                    forEach($testBoxes as $i => $testBox) {
                        $testBox->value = $erasedValues[$i];
                        forEach($testBox->neighbourhood as $neighbour) array_unset_value($testBox->value, $neighbour->candidates);
                    }
                }
            }
        }
        
        function countSolutions($max=2) {
            $solutions = $this->solutionsGenerator(false);
            $solutionsWithoutDuplicates = array();
            $nbSolutions = 0;
            foreach($solutions as $solution) {
                $solutionsWithoutDuplicates[$solution] = true;
                $nbSolutions = count($solutionsWithoutDuplicates);
                if ($nbSolutions >= $max) {
                    $solutions->send(true);
                }
            }
            return $nbSolutions;
        }
        
        function isValid() {
            return $this->countSolutions(2) == 1;
        }
        
        function solutionsGenerator($randomized=false) {
            $emptyBoxes = array_filter($this->boxes, "isUnknown");
            if (count($emptyBoxes)) {
                if ($randomized) shuffle($emptyBoxes);
                usort($emptyBoxes, "easyFirst");
                $testBox = $emptyBoxes[0];
                $nbTries = 0;
                if ($randomized) shuffle($testBox->candidates);
                $stop = null;
                foreach($testBox->candidates as $testBox->value) {
                    foreach(array_filter($testBox->neighbourhood, "isUnknown") as $neighbour)
                        $neighbour->candidateRemoved[] = array_unset_value($testBox->value, $neighbour->candidates);
                    if ($this->candidatesOnEachUnknownBoxeOf($testBox->neighbourhood)) {
                        $solutions = $this->solutionsGenerator($randomized);
                        foreach($solutions as $solution) {
                            $stop = (yield $solution);
                            if ($stop) {
                                $solutions->send($stop);
                                break;
                            }
                        }
                    }
                    foreach(array_filter($testBox->neighbourhood, "isUnknown") as $neighbour)
                        if (array_pop($neighbour->candidateRemoved))
                            $neighbour->candidates[] = $testBox->value;
                    if ($stop) break;
                }
                $testBox->value = UNKNOWN;
            } else {
                foreach(array($this->rows, $this->columns, $this->regions) as $areas) {
                    foreach ($areas as $area) {
                        foreach($area as $box1) {
                            if (($box1->value == UNKNOWN) && (count($box1->candidates) == 0)) {
                                return;
                            }
                            foreach($area as $box2) {
                                if (($box1 !== $box2) && ($box1->value != UNKNOWN) && ($box1->value == $box2->value)) {
                                    return;
                                }
                            }
                        }
                    }
                }
                yield $this->toString();
            }
        }

        function candidatesOnEachUnknownBoxeOf($area) {
            foreach($area as $box) {
                if (($box->value == UNKNOWN) && (count($box->candidates) == 0)) {
                    return false;
                }
            }
            return true;
        }
        
        function toString() {
            $str = "";
            foreach($this->rows as $row) {
                forEach($row as $box) {
                    $str .= $box->value;
                }
            }
            return $str;
        }
    }
?>
