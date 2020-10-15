const VALUES = "123456789"
const SUGESTION_DELAY = 60000 //ms

let boxes = []
let rows = Array.from(Array(9), x => [])
let columns = Array.from(Array(9), x => [])
let regions = Array.from(Array(9), x => [])
let suggestionTimer= null
let highlightedValue = ""
let history = []
let accessKeyModifiers = "AccessKey+"

window.onload = function() {
    let rowId = 0
    for (row of grid.getElementsByTagName('tr')) {
        let columnId = 0
        for (box of row.getElementsByTagName('input')) {
            let regionId = rowId - rowId%3 + Math.floor(columnId/3)
            if (!box.disabled) {
                box.onfocus = onfocus
                box.oninput = oninput
            }
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
    boxes.forEach(searchCandidatesOf)
    boxes.forEach(showCandidatesOn)
    enableButtons()
    highlightAndTab()
    
    if (/Win/.test(navigator.platform) || /Linux/.test(navigator.platform)) accessKeyModifiers = "Alt+Maj+"
    else if (/Mac/.test(navigator.platform)) accessKeyModifiers = "⌃⌥ "
    for(node of document.querySelectorAll("*[accesskey]")) {
        node.title += " [" + (node.accessKeyLabel || accessKeyModifiers + "+" + node.accessKey) + "]"
    }
    
    suggestionTimer = setTimeout(showSuggestion, 30000)
}

function searchCandidatesOf(box) {
    box.candidates = new Set(VALUES)
    box.neighbourhood.forEach(neighbour => box.candidates.delete(neighbour.value))
}

function showCandidatesOn(box) {
    if (!box.disabled) {
        while (box.list.firstChild) box.list.firstChild.remove()
        if (!box.value && box.candidates.size) {
            const candidatesArray = Array.from(box.candidates).sort()
            candidatesArray.forEach(candidate => {
                option = document.createElement('option')
                option.value = candidate
                box.list.appendChild(option)
            })
        }
    }
}

function onfocus() {
    this.previousValue = this.value
    this.select()
}

function oninput() {
    history.push({input: this, value: this.previousValue})
    undoButton.disabled = false
    refresh(this)
}

function undo() {
    if (history.length) {
        previousState = history.pop()
        previousState.input.value = previousState.value
        refresh(previousState.input)
        if (history.length < 1) undoButton.disabled = true
    }
}

function refresh(box) {
    box.style.color = colorPicker.value

    box.neighbourhood.concat([box]).forEach(neighbour => {
        searchCandidatesOf(neighbour)
        showCandidatesOn(neighbour)
        neighbour.setCustomValidity("")
    })
    
    enableButtons()
    highlightAndTab()
    
    for (neighbour1 of box.neighbourhood) {
        neighbour1.setCustomValidity("")
        if (neighbour1.value.length) {
            if (neighbour1.value) {
                for (area of [
                    {name: "région", neighbours: regions[neighbour1.regionId]},
                    {name: "ligne", neighbours: rows[neighbour1.rowId]},
                    {name: "colonne", neighbours: columns[neighbour1.columnId]},
                ])
                    for (neighbour2 of area.neighbours)
                        if (neighbour2 != neighbour1 && neighbour2.value == neighbour1.value) {
                            for (neighbour of [neighbour1, neighbour2]) {
                                neighbour.setCustomValidity(`Il y a un autre ${neighbour.value} dans cette ${area.name}.`)
                            }
                        }
            } else {
                if (neighbour1.candidates.size == 0)
                    neighbour1.setCustomValidity("Aucun value possible !")
            }
        }
    }
            
    if (box.form.checkValidity()) { // Correct grid
        if (boxes.filter(box => box.value == "").length == 0) {
            alert(`Bravo ! Vous avez résolu la grille.`)
        } else {
            if (suggestionTimer) clearTimeout(suggestionTimer)
            suggestionTimer = setTimeout(showSuggestion, SUGESTION_DELAY)
        }
    } else { // Errors on grid
        box.reportValidity()
        box.select()
    }
}

function enableButtons() {
    for (button of buttons.getElementsByTagName("button")) {
        if (boxes.filter(box => box.value == "").some(box => box.candidates.has(button.textContent))) {
            button.disabled = false
        } else {
            button.disabled = true
            if (highlightedValue == button.textContent) highlightedValue = ""
        }
    }
}

function highlight(value) {
    if (value == highlightedValue) {
        highlightedValue = ""
    } else {
        highlightedValue = value
    }
    for (button of buttons.getElementsByTagName("button")) {
        if (button.textContent == highlightedValue) button.className = "same-value"
        else button.className = ""
    }
    highlightAndTab()
    boxes.filter(box => box.value == "" && box.tabIndex == 0)[0].focus()
}

function highlightAndTab() {
    if (highlightedValue) {
        boxes.forEach(box => {
            if (box.value == highlightedValue) {
                box.className = "same-value"
                box.tabIndex = -1
            }
            else if (box.candidates.has(highlightedValue)) {
                box.className = ""
                box.tabIndex = 0
            } else {
                box.className = "forbidden-value"
                box.tabIndex = -1
            }
        })
    } else {
        boxes.forEach(box => {
            box.className = ""
            box.tabIndex = 0
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

easyFirst = (box1, box2) => box1.candidates.size - box2.candidates.size

function showSuggestion() {
    const emptyBoxes = boxes.filter(box => box.value == "" && box.candidates.size == 1)
    if (emptyBoxes.length) {
        shuffle(emptyBoxes)[0].placeholder = "?"
    } else {
        clearTimeout(suggestionTimer)
        suggestionTimer = null
    }
}

function clearAll() {
    boxes.filter(box => !box.disabled).forEach(box => {
        box.value = ""
        box.placeholder = ""
    })
    boxes.forEach(searchCandidatesOf)
    boxes.forEach(showCandidatesOn)
    enableButtons()
    highlightAndTab()
}
