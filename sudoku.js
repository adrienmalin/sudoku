const VALUES = "123456789"
suggestionTimer = null
highlightedValue = null

function createGrid() {
    rows = Array.from(Array(9), x => [])
    columns = Array.from(Array(9), x => [])
    regions = Array.from(Array(9), x => [])
    for (let regionRow = 0; regionRow < 3; regionRow++) {
        const gridRow = document.createElement("tr")
        for(let regionColumn = 0; regionColumn < 3; regionColumn++) {
            const gridCell = document.createElement("td")
            const regionTable = document.createElement("table")
            regionTable.className = "region"
            for (let row = 3*regionRow; row < 3*(regionRow+1); row++) {
                const regionTr = document.createElement("tr")
                for (let column = 3*regionColumn; column < 3*(regionColumn+1); column++) {
                    const regionCell = document.createElement("td")
                    const box = document.createElement("input")
                    box.type = "text"
                    box.oninput = oninput
                    box.oninvalid = oninvalid
                    box.onfocus = box.select
                    box.onkeydown = keyboardBrowse
                    box.setAttribute("inputmode", "numeric")
                    box.required = false
                    box.region = 3*regionRow + regionColumn
                    box.column = column
                    box.row = row
                    box.minLength = 0
                    box.maxLength = 1
                    box.tabIndex = 9
                    rows[row].push(box)
                    columns[column].push(box)
                    regions[box.region].push(box)
                    regionCell.appendChild(box)
                    regionTr.appendChild(regionCell)
                }
                regionTable.appendChild(regionTr)
            }
            gridCell.appendChild(regionTable)
            gridRow.appendChild(gridCell)
        }
        gridTable.appendChild(gridRow)
    }
    grid = rows.reduce((grid, row) => grid.concat(row), [])
    // box.neighbourhood: boxes in the same row, column and region as box
    grid.forEach(box => {
        box.neighbourhood = new Set(rows[box.row].concat(columns[box.column]).concat(regions[box.region]))
        box.neighbourhood.delete(box)
        box.neighbourhood = Array.from(box.neighbourhood)
    })
}

function clearGrid() {
    clearTimeout(suggestionTimer)
    suggestionTimer = null
    grid.forEach(box => {
        box.value = ""
        box.allowedValues = new Set(VALUES)
        box.testValueWasAllowed = []
        box.pattern = "[1-9]?"
        box.placeholder = ""
        box.disabled = false
        box.required = false
    })
    enableHighlightOptions()
}

function loadGrid() {
    window.onhashchange = function() {
        if (location.hash) {
            if (location.hash.match(/^#[1-9?]{81}$/)) {
                gridAsString = location.hash.substring(1)
                if (gridAsString != grid.map(box => box.disabled? box.value : "?").join("")) {
                    // Load grid from hash if it differs
                    clearGrid()
                    if (savedGame = localStorage.getItem(gridAsString + "SavedGame")) {
                        // We trust grid is valid if it was previously saved
                        grid.forEach((box, i) => {
                            box.value = savedGame[i] == "?"? "" : savedGame[i]
                            box.disabled = Boolean(gridAsString[i] != "?")
                        })
                        startTime = Date.now() - localStorage.getItem(gridAsString + "Time")
                        finishGridLoad()
                    } else {
                        // Else we need to check if grid is valid
                        grid.forEach((box, i) => box.value = gridAsString[i] == "?"? "" : gridAsString[i])
                        grid.forEach(box => searchAllowedValuesOf(box))
                        checkGrid()
                    }
                }
            } else {
                alert(location.hash.substring(1) + " n'est pas une grille valide.")
            }
        }
    }
    
    if (location.hash)
        window.onhashchange()
    else if ((lastPlayedGrid = localStorage.getItem("lastPlayedGrid")) && localStorage.getItem(lastPlayedGrid + "SavedGame").match("[?]"))
        location.hash = lastPlayedGrid
    else
        generateGrid()
}

function checkGrid() {
    if (sudokuForm.checkValidity()) {
        switch(new Set(findSolutions(false, 2, 4)).size) {
            case 0:
                this.reportValidity()
                alert("Cette grille n'a pas de solution. Veuillez la corriger.")
            break
            case 1:
                freezeGrid()
            break
            default:
                alert("Cette grille a plusieurs solutions. Rajoutez des indices.")
        }
    } else {
        sudokuForm.reportValidity()
    }
}

function freezeGrid() {
    grid.forEach(box => box.disabled = Boolean(box.value.length))
    startTime = Date.now()
    gridAsString = grid.map(box => box.disabled? box.value : "?").join("")
    location.hash = gridAsString
    finishGridLoad()
}

function finishGridLoad() {
    grid.forEach(box => {
        searchAllowedValuesOf(box)
        showAllowedValuesOn(box)
        box.pattern = `[${Array.from(box.allowedValues).join("")}]?`
    })
    enableHighlightOptions()
    highlightAndTabOrder()
    shareA.href = location
    shareA.innerHTML = location.hostname + location.pathname + "#<br/>" + Array(9).fill().map((_,i) => gridAsString.slice(9*i, 9*i+9)).join("<br/>")
    solutions = findSolutions(false, 1, 4)
    localStorage.setItem("lastPlayedGrid", gridAsString)
    saveGame()
    suggestionTimer = setTimeout(showSuggestion, 30000)
}

function saveGame() {
    localStorage.setItem(gridAsString + "SavedGame", grid.map(box => box.value || "?").join(""))
    localStorage.setItem(gridAsString + "Time", Date.now() - startTime)
}

fromEasyToDifficult = (box1, box2) => box1.allowedValues.size - box2.allowedValues.size

function shuffle(iterable) {
    array = Array.from(iterable)
    if (array.length > 1) {
        let i, j, tmp
        for (i = array.length - 1; i > 0; i--) {
            j = Math.floor(Math.random() * (i+1))
            tmp = array[i]
            array[i] = array[j]
            array[j] = tmp
        }
    } 
    return array
}

function* findSolutions(randomized=false, maxSolutions=1, maxTries=4) {
    let emptyBoxes = grid.filter(box => box.value == "")
    if (emptyBoxes.length) {
        if (randomized) // Generate random grids
            emptyBoxes = shuffle(emptyBoxes)
        const testBox = emptyBoxes.sort(fromEasyToDifficult)[0] // Pick an easy box to solve
        // Try every allowed value
        let nbSolutionsFound = 0
        let nbTries = 0
        for (testBox.value of randomized? shuffle(testBox.allowedValues) : testBox.allowedValues) {
            testBox.neighbourhood.forEach(neighbour => {
                // Remember if testBox.value was in neighbour.allowedValues in case we rewrite it
                neighbour.testValueWasAllowed.push(neighbour.allowedValues.has(testBox.value))
                neighbour.allowedValues.delete(testBox.value)
            })
            // If grid still correct, yield all solutions with this hypothesis
            if (testBox.neighbourhood.filter(box => box.value == "").every(emptyBox => emptyBox.allowedValues.size)) {
                for (const solution of findSolutions(randomized, maxSolutions-nbSolutionsFound, maxTries)) {
                    yield solution
                    nbSolutionsFound++
                }
            }
            testBox.neighbourhood.filter(
                neighbour => neighbour.testValueWasAllowed.pop()
            ).forEach(neighbour => neighbour.allowedValues.add(testBox.value))
            if (maxSolutions && nbSolutionsFound >= maxSolutions) break
            if (++nbTries >= maxTries) break
        }
        testBox.value = ""
    } else {
        yield grid.map(box => box.value).join("")
    }
    return "No more solutions"
}

function generateGrid() {
    clearGrid()
    shuffle(Array.from(VALUES)).forEach((value, c) => {
        rows[0][c].value = value
        rows[0][c].neighbourhood.forEach(neighbour => neighbour.allowedValues.delete(rows[0][c].value))
    })
    // Generate a random valid grid where all boxes are clues
    while (findSolutions(true, 1, 4).next().done) {}
    // Remove clues while there is still a unique solution
    let untestedBoxes = shuffle(grid)
    let nbClues = 81
    while(untestedBoxes.length) {
        const testBoxes = [untestedBoxes.pop()]
        if (nbClues >= 30)
        testBoxes.push(rows[8-testBoxes[0].row][8-testBoxes[0].column])
        if (nbClues >= 61) {
            testBoxes.push(rows[8-testBoxes[0].row][testBoxes[0].column])
            testBoxes.push(rows[testBoxes[0].row][8-testBoxes[0].column])
        }
        const erasedValues = testBoxes.map(box => box.value)
        testBoxes.forEach(box => {
            box.value = ""
            box.neighbourhood.forEach(neighbour => searchAllowedValuesOf(neighbour))
        })
        if (Array.from(findSolutions(false, 2, 4)).length == 1) {
            nbClues -= testBoxes.length
            testBoxes.slice(1).forEach(box => untestedBoxes.splice(untestedBoxes.indexOf(box), 1))
        } else {
            testBoxes.forEach((box, i) => {
                box.value = erasedValues[i]
                box.neighbourhood.forEach(neighbour => neighbour.allowedValues.delete(box.value))
            })
        }
    }
    freezeGrid()
}

function customGrid() {
    clearGrid()
    grid.forEach(box => showAllowedValuesOn(box))
    enableHighlightOptions()
    highlightAndTabOrder()
    solutions = findSolutions(false, 1, 4)
    customGridButton.innerText = "Figer la grille"
    customGridButton.onclick = checkGrid
    location.hash = ""
}

function searchAllowedValuesOf(box) {
    box.allowedValues = new Set(VALUES)
    box.neighbourhood.forEach(neighbour => box.allowedValues.delete(neighbour.value))
}

function keyboardBrowse(event) {
    switch(event.key) {
        case "ArrowLeft":
            event.preventDefault()
            moveOn(rows[this.row], this.column, 8)
        break
        case "ArrowRight":
            event.preventDefault()
            moveOn(rows[this.row], this.column, 1)
        break
        case "ArrowUp":
            event.preventDefault()
            moveOn(columns[this.column], this.row, 8)
        break
        case "ArrowDown":
            event.preventDefault()
            moveOn(columns[this.column], this.row, 1)
        break
    }
}

function moveOn(area, position, direction) {
    if (area.filter(box => box.disabled).length < 9) {
        do {
            position = (position + direction) % 9
        } while (area[position].disabled)
        area[position].focus()
    }
}

timeFormat = new Intl.DateTimeFormat("fr-FR", {
    hour: "numeric",
    minute: "2-digit",
    second: "2-digit",
    timeZone: "UTC"
}).format

function oninput() {
    saveGame()

    this.neighbourhood.concat([this]).forEach(box => {
        box.setCustomValidity("")
        searchAllowedValuesOf(box)
        box.pattern = `[${Array.from(box.allowedValues).join("")}]?`
    })

    highlightAndTabOrder()
    enableHighlightOptions()
    this.neighbourhood.concat([this]).forEach(neighbour => showAllowedValuesOn(neighbour))

    if (this.form.checkValidity()) { // Correct grid
        if (grid.filter(box => box.value == "").length == 0) {
            alert(`Bravo ! Vous avez résolu la grille en ${timeFormat(Date.now() - startTime)}.`)
        } else {
            solutions = findSolutions(false, 1, 4) // Reset solutions generator
            if (suggestionTimer) clearTimeout(suggestionTimer)
            suggestionTimer = setTimeout(showSuggestion, 30000)
        }
    } else { // Errors on grid
        this.select()
        this.reportValidity()
    }
}

// Help functions

function showAllowedValuesOn(box) {
    box.required = box.allowedValues.size == 0
    if (box.value.length) {
        box.title = ""
    } else if (box.allowedValues.size) {
        const allowedValuesArray = Array.from(box.allowedValues).sort()
        box.title = allowedValuesArray.length ==1 ? allowedValuesArray[0] : allowedValuesArray.slice(0, allowedValuesArray.length-1).join(", ") + " ou " + allowedValuesArray[allowedValuesArray.length-1]
    } else {
        box.title = "Aucune valeur possible !"
    }
}

function oninvalid() {
    if (this.value.length && !this.value.match(/[1-9]/))
        this.setCustomValidity("Entrez un chiffre entre 1 et 9.")
    else if (sameValueIn(regions[this.region]))
        this.setCustomValidity(`Il y a un autre ${this.value} dans cette région.`)
    else if (sameValueIn(rows[this.row]))
        this.setCustomValidity(`Il y a un autre ${this.value} dans cette ligne.`)
    else if (sameValueIn(columns[this.column]))
        this.setCustomValidity(`Il y a un autre ${this.value} dans cette colonne.`)
    else if (this.allowedValues.size == 0)
        this.setCustomValidity("La grille est incorrecte.")
}

function sameValueIn(area) {
    for (const box1 of area) {
        for (const box2 of area) {
            if (box1 != box2 && box1.value.length && box1.value == box2.value) {
                return true
            }
        }
    }
    return false
}

function highlight(value) {
    highlightedValue = value
    highlightAndTabOrder()
}

function highlightAndTabOrder() {
    if (highlightedValue) {
        for (const box of grid) {
            box.tabIndex = -1
            box.className = "unhighlighted"
            if (box.value == "") {
                if (box.allowedValues.has(highlightedValue)) {
                    box.tabIndex = 0
                    box.className = "highlighted"
                }
            }
        }
    } else {
        for (const box of grid) {
            box.className = "unhighlighted"
            if (box.value == "" && box.allowedValues.size == 1) {
                box.tabIndex = 0
            } else {
                box.tabIndex = -1
            }
        }
    }
}

function enableHighlightOptions() {
    for (value of VALUES) {
        let highlightRadio = document.getElementById("highlightRadio" + value)
        let highlightLabel = document.getElementById("highlightLabel" + value)
        if (grid.filter(box => box.value == "").some(box => box.allowedValues.has(value))) {
            highlightRadio.disabled = false
            highlightLabel.className = ""
        } else {
            highlightRadio.disabled = true
            highlightRadio.checked = false
            highlightLabel.className = "disabled"
        }
    }
}

function showSuggestion() {
    const emptyBoxes = grid.filter(box => box.value == "")
    if (emptyBoxes.length) {
        shuffle(emptyBoxes).sort(fromEasyToDifficult)[0].placeholder = "!"
    } else {
        clearTimeout(suggestionTimer)
        suggestionTimer = null
    }
}

function erase() {
    grid.filter(box => !box.disabled).forEach(box => {
        box.value = ""
        box.placeholder = ""
    })
    grid.filter(box => !box.disabled).forEach(box => {
        searchAllowedValuesOf(box)
        showAllowedValuesOn(box)
    })
    sudokuForm.checkValidity()
    sudokuForm.reportValidity()
    enableHighlightOptions()
    solutions = findSolutions(false, 1, 4)
    solveButton.innerText = "Résoudre"
    solveButton.type = "button"
    solveButton.onclick = solve
    solveButton.disabled = false
    startTime = Date.now()
}

function solve() {
    if (sudokuForm.checkValidity()) {
        if (solutions.next().done) { // End of solutions generator
            solveButton.innerText = "Montrer la solution"
            solutions = findSolutions(false, 1, 4)
        } else {
            solveButton.innerText = "Effacer la solution"
        }
        grid.forEach(box => showAllowedValuesOn(box))
    } else {
        sudokuForm.reportValidity()
    }
}
