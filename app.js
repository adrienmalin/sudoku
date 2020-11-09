const VALUES = "123456789"
const UNKNOWN = '.'
const SUGESTION_DELAY = 60000 //ms

let boxes = []
let rows = Array.from(Array(9), x => [])
let columns = Array.from(Array(9), x => [])
let regions = Array.from(Array(9), x => [])
let suggestionTimer= null
let valueToInsert = ""
let history = []
let accessKeyModifiers = "AccessKey+"

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
                box.onclick = onclick
                box.previousPlaceholder = ""
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
    
    let savedGame = localStorage[location.href]
    if (savedGame) {
        boxes.forEach((box, i) => {
            if (!box.disabled && savedGame[i] != UNKNOWN) {
                box.value = savedGame[i]
                box.previousValue = savedGame[i]
            }
        })
    }
    
    boxes.forEach(box => {
        box.neighbourhood = new Set(rows[box.rowId].concat(columns[box.columnId]).concat(regions[box.regionId]))
        box.neighbourhood.delete(box)
        box.neighbourhood = Array.from(box.neighbourhood)
        searchCandidatesOf(box)
    })
    
    if (/Win/.test(navigator.platform) || /Linux/.test(navigator.platform)) accessKeyModifiers = "Alt+Maj+"
    else if (/Mac/.test(navigator.platform)) accessKeyModifiers = "⌃⌥"
    for(node of document.querySelectorAll("*[accesskey]")) {
        node.title += " [" + (node.accessKeyLabel || accessKeyModifiers + node.accessKey) + "]"
    }
    
    enableRadios()
    highlight()
    
    document.onclick = function (event) {
        contextMenu.style.display = "none"
    }
    suggestionTimer = setTimeout(showSuggestion, SUGESTION_DELAY)
    
    if ("serviceWorker" in navigator) {
        navigator.serviceWorker.register("service-worker.js")
    }
}

function searchCandidatesOf(box) {
    box.candidates = new Set(VALUES)
    box.neighbourhood.forEach(neighbour => box.candidates.delete(neighbour.value))
    if (!box.disabled) {
        switch (box.candidates.size) {
            case 0:
                box.title = "Aucune possibilité !"
            break
            case 1:
                box.title = "1 possibilité [Clic-droit]"
            break
            default:
                box.title = box.candidates.size + " possibilités [Clic-droit]"
        }
    }
}

function onclick() {
    if (valueToInsert) {
        if (inkPenRadio.checked) {
            this.value = valueToInsert
        } else {
            if (!this.value.includes(valueToInsert))
                this.value += valueToInsert
        }
        oninput.apply(this)
    }
}

function onfocus() {
    if (pencilRadio.checked && this.value == "") {
        this.value = this.placeholder
        this.placeholder = ""
        this.classList.add("pencil")
    } else {
        this.select()
    }
    if (valueToInsert) {
        this.style.caretColor = "transparent"
    } else {
        this.style.caretColor = "auto"
    }
}

function oninput() {
    history.push({box: this, value: this.previousValue, placeholder: this.previousPlaceholder})
    this.previousValue = this.value
    this.previousPlaceholder = this.placeholder
    undoButton.disabled = false
    if (inkPenRadio.checked) {
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
    localStorage[location.href] = boxes.map(box => box.value || ".").join("")
    
    box.neighbourhood.concat([box]).forEach(neighbour => {
        searchCandidatesOf(neighbour)
        neighbour.setCustomValidity("")
        neighbour.required = false
    })
    
    enableRadios()
    highlight()
    
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
            setTimeout(() => alert(`Bravo ! Vous avez résolu la grille.`), 0)
        } else {
            boxes.filter(box => box.value == "" && box.tabIndex == 0)[0].focus()
            if (suggestionTimer) clearTimeout(suggestionTimer)
            suggestionTimer = setTimeout(showSuggestion, SUGESTION_DELAY)
        }
    } else { // Errors on grid
        box.form.reportValidity()
        box.select()
    }
}

function enableRadios() {
    for (radio of insertRadioGroup.getElementsByTagName("input")) {
        if (boxes.filter(box => box.value == "").some(box => box.candidates.has(radio.value))) {
            radio.disabled = false
            if (radio.previousTitle) {
                radio.title = radio.previousTitle
                radio.previousTitle = null
            }
        } else {
            radio.disabled = true
            radio.previousTitle = radio.title
            radio.title = "Tous les " + radio.value + " sont posés"
            if (valueToInsert == radio.value) valueToInsert = ""
        }
    }
}

function insert(radio) {
    if (radio.value == valueToInsert) {
        valueToInsert = ""
        radio.checked = false
    } else {
        valueToInsert = radio.value
    }
    highlight()
}

function highlight() {
    if (highlighterCheckbox.checked && valueToInsert) {
        boxes.forEach(box => {
            if (box.value == valueToInsert) {
                box.classList.add("same-value")
                box.tabIndex = -1
            }
            else { 
                box.classList.remove("same-value")
                if (box.candidates.has(valueToInsert) && !box.disabled) {
                    box.classList.add("allowed")
                    box.classList.remove("forbidden")
                    box.tabIndex = 0
                } else {
                    box.classList.add("forbidden")
                    box.classList.remove("allowed")
                    box.tabIndex = -1
                }
            }
        })
    } else {
        boxes.forEach(box => {
            box.classList.remove("same-value", "allowed", "forbidden")
            if (box.disabled) {
                box.classList.add("forbidden")
            } else if (valueToInsert) {
                box.classList.add("allowed")
            }
            box.tabIndex = 0
        })
    }
}

function onblur() {
    if (this.classList.contains("pencil")) {
        this.placeholder = this.value
        this.value = ""
        this.classList.remove("pencil")
    }
}

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
                onfocus.apply(box)
                box.value = event.target.innerText
                oninput.apply(box)
                onblur.apply(box)
            }
            contextMenu.appendChild(li)
        })
    } else {
        li = document.createElement("li")
        li.innerText = "Aucune possibilité !"
        li.classList.add("error")
        contextMenu.appendChild(li)
    }
    contextMenu.style.left = `${event.pageX}px`
    contextMenu.style.top = `${event.pageY}px`
    contextMenu.style.display = "block"
    return false
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
        enableRadios()
        highlight()
    }
}