const VALUES = "123456789"

let boxes = []
let rows = Array.from(Array(9), x => [])
let columns = Array.from(Array(9), x => [])
let regions = Array.from(Array(9), x => [])
let suggestionTimer= null
let highlightedValue = ""

window.onload = function() {
    let rowId = 0
    for (row of grid.getElementsByTagName('tr')) {
        let columnId = 0
        for (box of row.getElementsByTagName('input')) {
            let regionId = rowId - rowId%3 + Math.floor(columnId/3)
            if (!box.readOnly) box.select
            box.oninput = oninput
            box.oninvalid = oninvalid
            box.onkeydown = keyboardBrowse
            box.rowId = rowId
            box.columnId = columnId
            box.regionId = regionId
            boxes.push(box)
            rows[rowId].push(box)
            columns[columnId].push(box)
            regions[regionId].push(box)
            columnId++
        }
        rowId++
    }
    boxes.forEach(box => {
        box.neighbourhood = new Set(rows[box.rowId].concat(columns[box.columnId]).concat(regions[box.regionId]))
        box.neighbourhood.delete(box)
        box.neighbourhood = Array.from(box.neighbourhood)
    })
    boxes.forEach(searchAllowedValuesOf)
    enableButtons()
    boxes.forEach(showAllowedValuesOn)
    for(box of boxes) {
        if (!box.readOnly) {
            box.focus()
            break
        }
    }
    suggestionTimer = setTimeout(showSuggestion, 30000)
}

function searchAllowedValuesOf(box) {
    box.allowedValues = new Set(VALUES)
    box.neighbourhood.forEach(neighbour => box.allowedValues.delete(neighbour.value))
}

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

function oninput() {
    this.neighbourhood.concat([this]).forEach(box => {
        box.setCustomValidity("")
        searchAllowedValuesOf(box)
        box.pattern = `[${Array.from(box.allowedValues).join("")}]?`
    })
    
    enableButtons()
    refreshShowValue()
    this.neighbourhood.concat([this]).forEach(neighbour => showAllowedValuesOn(neighbour))

    if (this.form.checkValidity()) { // Correct grid
        if (boxes.filter(box => box.value == "").length == 0) {
            alert(`Bravo ! Vous avez résolu la grille.`)
        } else {
            if (suggestionTimer) clearTimeout(suggestionTimer)
            suggestionTimer = setTimeout(showSuggestion, 30000)
        }
    } else { // Errors on grid
        this.select()
        this.reportValidity()
    }
}

function enableButtons() {
    for (button of buttons.getElementsByTagName("button")) {
        if (boxes.filter(box => box.value == "").some(box => box.allowedValues.has(button.textContent))) {
            button.disabled = false
        } else {
            button.disabled = true
            if (highlightedValue == button.textContent) highlightedValue = ""
        }
    }
}

function oninvalid() {
    if (this.value.length && !this.value.match(/[1-9]/))
        this.setCustomValidity("Entrez un chiffre entre 1 et 9.")
    else if (sameValueIn(regions[this.regionId]))
        this.setCustomValidity(`Il y a un autre ${this.value} dans cette région.`)
    else if (sameValueIn(rows[this.rowId]))
        this.setCustomValidity(`Il y a un autre ${this.value} dans cette ligne.`)
    else if (sameValueIn(columns[this.columnId]))
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


function keyboardBrowse(event) {
    switch(event.key) {
        case "ArrowLeft":
            event.preventDefault()
            moveOn(rows[this.rowId], this.columnId, 8)
        break
        case "ArrowRight":
            event.preventDefault()
            moveOn(rows[this.rowId], this.columnId, 1)
        break
        case "ArrowUp":
            event.preventDefault()
            moveOn(columns[this.columnId], this.rowId, 8)
        break
        case "ArrowDown":
            event.preventDefault()
            moveOn(columns[this.columnId], this.rowId, 1)
        break
    }
}

function moveOn(area, position, direction) {
    position = (position + direction) % 9
    area[position].focus()
}

function showValue(button) {
    if (button.textContent == highlightedValue) {
        highlightedValue = ""
    } else {
        highlightedValue = button.textContent
    }
    refreshShowValue()
}

function refreshShowValue() {
    boxes.forEach(box => box.className = "")
    if (highlightedValue) {
        boxes.forEach(box => {
            if (box.value == highlightedValue) box.className = "same-value"
            if (!box.allowedValues.has(highlightedValue)) box.className = "forbidden-value"
        })
    }
}

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

easyFirst = (box1, box2) => box1.allowedValues.size - box2.allowedValues.size

function showSuggestion() {
    const emptyBoxes = boxes.filter(box => box.value == "")
    if (emptyBoxes.length) {
        shuffle(emptyBoxes).sort(easyFirst)[0].placeholder = "!"
    } else {
        clearTimeout(suggestionTimer)
        suggestionTimer = null
    }
}
