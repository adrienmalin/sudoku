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
let penStyle = "ink-pen"

window.onload = function() {
    let rowId = 0
    for (let row of grid.getElementsByTagName('tr')) {
        let columnId = 0
        for (let box of row.getElementsByTagName('input')) {
            let regionId = rowId - rowId%3 + Math.floor(columnId/3)
            if (!box.disabled) {
                box.onfocus = onfocus
                box.oninput = oninput
                box.onblur = onblur
            }
            box.oncontextmenu = oncontextmenu
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
        searchCandidatesOf(box)
    })
    
    enableButtons()
    highlightAndTab()
    
    if (/Win/.test(navigator.platform) || /Linux/.test(navigator.platform)) accessKeyModifiers = "Alt+Maj+"
    else if (/Mac/.test(navigator.platform)) accessKeyModifiers = "⌃⌥"
    for(node of document.querySelectorAll("*[accesskey]")) {
        node.title += " [" + (node.accessKeyLabel || accessKeyModifiers + node.accessKey) + "]"
    }
    
    document.onclick = function (event) {
        contextMenu.style.display = "none"
    }
    suggestionTimer = setTimeout(showSuggestion, 30000)
}

function searchCandidatesOf(box) {
    box.candidates = new Set(VALUES)
    box.neighbourhood.forEach(neighbour => box.candidates.delete(neighbour.value))
    if (!box.disabled)
        box.title = box.candidates.size + (box.candidates.size <= 1 ? " possibilité [Clic-droit]" : " possibilités [Clic-droit]")
}

function onfocus() {
    if (penStyle == "pencil" && this.value == "") {
        this.value = this.placeholder
        this.placeholder = ""
        this.classList.add("pencil")
    } else {
        this.select()
    }
}

function oninput() {
    history.push({box: this, value: this.previousValue, placeholder: this.previousPlaceholder})
    undoButton.disabled = false
    if (penStyle != "pencil") {
        refresh(this)
    }
}

function undo() {
    if (history.length) {
        previousState = history.pop()
        previousState.box.value = previousState.value
        previousState.box.placeholder = previousState.placeholder
        refresh(previousState.box)
        if (history.length < 1) undoButton.disabled = true
    }
}

function refresh(box) {
    box.neighbourhood.concat([box]).forEach(neighbour => {
        searchCandidatesOf(neighbour)
        neighbour.setCustomValidity("")
        neighbour.required = false
    })
    
    enableButtons()
    highlightAndTab()
    
    for (neighbour1 of box.neighbourhood) {
        if (neighbour1.value.length == 1) {
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
            if (neighbour1.candidates.size == 0) {
                neighbour1.setCustomValidity("Aucun chiffre possible !")
                neighbour1.required = true
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
        box.form.reportValidity()
        box.select()
    }
}

function onblur() {
    if (this.classList.contains("pencil")) {
        this.placeholder = this.value
        this.value = ""
        this.classList.remove("pencil")
    }
    this.previousValue = this.value
    this.previousPlaceholder = this.placeholder
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
        if (button.textContent == highlightedValue) button.classList.add("pressed")
        else button.classList.remove("pressed")
    }
    highlightAndTab()
    boxes.filter(box => box.value == "" && box.tabIndex == 0)[0].focus()
}

function highlightAndTab() {
    if (highlightedValue) {
        boxes.forEach(box => {
            if (box.value == highlightedValue) {
                box.classList.add("same-value")
                box.tabIndex = -1
            }
            else { 
                box.classList.remove("same-value")
                if (box.candidates.has(highlightedValue)) {
                    box.classList.remove("forbidden-value")
                    box.tabIndex = 0
                } else {
                    box.classList.add("forbidden-value")
                    box.tabIndex = -1
                }
            }
        })
    } else {
        boxes.forEach(box => {
            box.classList.remove("same-value", "forbidden-value")
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
        shuffle(emptyBoxes)[0].placeholder = "💡"
    } else {
        clearTimeout(suggestionTimer)
        suggestionTimer = null
    }
}

function oncontextmenu(event) {
    event.preventDefault()
    while (contextMenu.firstChild) contextMenu.firstChild.remove()
    const box = event.target
    if (box.candidates.size) {
        candidatesArray = Array.from(box.candidates).sort().forEach(candidate => {
            li = document.createElement("li")
            li.innerText = candidate
            li.onclick = function (event) {
                contextMenu.style.display = "none"
                box.value = event.target.innerText
                oninput.apply(box)
            }
            contextMenu.appendChild(li)
        })
    } else {
        li = document.createElement("li")
        li.innerText = "Aucun chiffre possible"
        li.classList.add("error")
        contextMenu.appendChild(li)
    }
    contextMenu.style.left = `${event.pageX}px`
    contextMenu.style.top = `${event.pageY}px`
    contextMenu.style.display = "block"
    return false
}

function useInkPen() {
    inkPenButton.classList.add("pressed")
    pencilButton.classList.remove("pressed")
    penStyle = "ink-pen"
}

function usePencil() {
    pencilButton.classList.add("pressed")
    inkPenButton.classList.remove("pressed")
    penStyle = "pencil"
}

function erase(someBoxes) {
    for (box of someBoxes) {
        box.value = ""
        box.placeholder = ""
        searchCandidatesOf(box)
        refresh(box)
    }
    enableButtons()
    highlightAndTab()
}

function erasePencil() {
    if (confirm("Effacer les chiffres écrits au crayon ?")) {
        boxes.filter(box => !box.disabled).forEach(box => {
            box.placeholder = ""
        })
    }
}

function eraseAll() {
    if (confirm("Effacer tous les chiffres écrits au crayon et au stylo ?")) {
        boxes.filter(box => !box.disabled).forEach(box => {
            box.value = ""
            box.placeholder = ""
            box.setCustomValidity("")
            box.required = false
        })
        boxes.forEach(searchCandidatesOf)
        enableButtons()
        highlightAndTab()
    }
}
