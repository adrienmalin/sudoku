const VALUES = "123456789"
const UNKNOWN = '.'
const SUGESTION_DELAY = 60000 //ms

let boxes = []
let rows = Array.from(Array(9), x => [])
let columns = Array.from(Array(9), x => [])
let regions = Array.from(Array(9), x => [])
let valueToInsert = ""
let history = []
let accessKeyModifiers = "AccessKey+"
let easyBoxes = []
let insertRadios = []

function shuffle(iterable) {
    array = Array.from(iterable)
    if (array.length > 1) {
        let i, j, tmp
        for (i = array.length - 1; i > 0; i--) {
            j = Math.floor(Math.random() * (i + 1))
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
            let regionId = rowId - rowId % 3 + Math.floor(columnId / 3)
            if (!box.disabled) {
                box.onfocus = onfocus
                box.oninput = oninput
                box.onblur = onblur
                box.onclick = onclick
                box.onmouseenter = onmouseenter
                box.onmouseleave = onmouseleave
                box.previousValue = ""
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

    if (localStorage["sightCheckbox.checked"] == "true") sightCheckbox.checked = true
    if (localStorage["highlighterCheckbox.checked"] == "true") highlighterCheckbox.checked = true
    loadSavedGame()

    boxes.forEach(box => {
        box.neighbourhood = new Set(rows[box.rowId].concat(columns[box.columnId]).concat(regions[box.regionId]))
        box.andNeighbourhood = Array.from(box.neighbourhood)
        box.neighbourhood.delete(box)
        box.neighbourhood = Array.from(box.neighbourhood)
        searchCandidatesOf(box)
    })

    insertRadios = Array.from(insertRadioGroup.getElementsByTagName("input"))
    insertRadios.pop()

    for (label of document.getElementsByTagName("label")) {
        label.control.label = label
    }

    if (/Win/.test(navigator.platform) || /Linux/.test(navigator.platform)) accessKeyModifiers = "Alt+Maj+"
    else if (/Mac/.test(navigator.platform)) accessKeyModifiers = "⌃⌥"
    for (node of document.querySelectorAll("*[accesskey]")) {
        shortcut = ` [${node.accessKeyLabel||(accessKeyModifiers+node.accessKey)}]`
        if (node.title) node.title += shortcut
        else if (node.label) node.label.title += shortcut
    }

    refreshUI()

    if ("serviceWorker" in navigator) {
        navigator.serviceWorker.register(`service-worker.php?location=${location.pathname}`)
    }
}

function loadSavedGame() {
    const savedGame = localStorage[location.search]
    if (savedGame) {
        boxes.forEach((box, i) => {
            if (!box.disabled && savedGame[i] != UNKNOWN) {
                box.value = savedGame[i]
                box.previousValue = savedGame[i]
            }
        })
        fixGridLink.href = "?" + savedGame
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
                box.title = "Une seule possibilité [Clic-droit]"
                break
            default:
                box.title = box.candidates.size + " possibilités [Clic-droit]"
        }
    }
}

function onfocus() {
    if (pencilRadio.checked) {
        //this.type = "text"
        this.value = this.placeholder
        this.classList.add("pencil")
    } else {
        this.select()
    }
    this.style.caretColor = valueToInsert ? "transparent" : "auto"
}

function onclick() {
    if (inkPenRadio.checked) {
        if (valueToInsert) {
            this.value = valueToInsert
            this.oninput()
        } else {
            this.select()
        }
    } else if (pencilRadio.checked) {
        if (valueToInsert) {
            this.value = Array.from(new Set(this.value + valueToInsert)).join("")
            this.oninput()
        }
    } else if (eraserRadio.checked) {
        this.value = ""
        this.placeholder = ""
        this.oninput()
    }
}

function oninput() {
    history.push({
        box: this,
        value: this.previousValue,
        placeholder: this.previousPlaceholder
    })
    undoButton.disabled = false
    saveButton.disabled = false
    restartButton.disabled = false
    if (pencilRadio.checked) {
        this.previousValue = ""
        this.previousPlaceholder = this.value
    } else {
        this.previousValue = this.value
        this.previousPlaceholder = this.placeholder
        refreshBox(this)
    }
}

function refreshBox(box) {
    checkBox(box)
    refreshUI()
}

function checkBox(box) {
    box.andNeighbourhood.forEach(neighbour => {
        neighbour.setCustomValidity("")
        neighbour.classList.remove("is-invalid")
        searchCandidatesOf(neighbour)
        if (neighbour.candidates.size == 0) {
            neighbour.setCustomValidity("Aucun chiffre possible !")
        }
    })

    if (box.value) {
        for (area of[{
                name: "région",
                neighbours: regions[box.regionId]
            }, {
                name: "ligne",
                neighbours: rows[box.rowId]
            }, {
                name: "colonne",
                neighbours: columns[box.columnId]
            }, ])
            for (neighbour of area.neighbours)
                if (box != neighbour && box.value == neighbour.value) {
                    for (neighbour of[box, neighbour]) {
                        neighbour.setCustomValidity(`Il y a un autre ${box.value} dans cette ${area.name}.`)
                        neighbour.classList.add("is-invalid")
                    }
                }
    }

    if (box.form.checkValidity()) { // Correct grid
        if (boxes.filter(box => box.value == "").length == 0) {
            grid.classList.add("table-success")
            saveButton.disabled = true
            setTimeout(() => {
                if (confirm(`Bravo ! Vous avez résolu la grille. En voulez-vous une autre ?`))
                    location = "."
            }, 400)
        }
    } else { // Errors on grid
        box.form.reportValidity()
    }
}

function refreshUI() {
    enableRadio()
    highlight()
}

function enableRadio() {
    for (radio of insertRadios) {
        if (boxes.filter(box => box.value == "").some(box => box.candidates.has(radio.value))) {
            radio.disabled = false
            radio.label.title = `Insérer un ${radio.value} [${radio.accessKeyLabel||(accessKeyModifiers+radio.accessKey)}]`
        } else {
            radio.disabled = true
            radio.label.title = `Tous les ${radio.value} sont posés.`
            if (valueToInsert == radio.value) {
                insertRadio0.checked = true
                valueToInsert = ""
                grid.style.cursor = "text"
            }
        }
    }
}

function highlight() {
    hintButton.disabled = true
    easyBoxes = []
    boxes.forEach(box => {
        if (valueToInsert && box.value == valueToInsert) {
            box.parentElement.classList.add("table-primary")
            box.tabIndex = -1
        } else {
            box.parentElement.classList.remove("table-primary")
            box.tabIndex = 0
        }
        
        if (valueToInsert && highlighterCheckbox.checked && !box.candidates.has(valueToInsert)) {
            box.parentElement.classList.add("table-active")
            box.tabIndex = -1
        } else {
            box.parentElement.classList.remove("table-active")
            box.tabIndex = 0
        }
        
        if (!box.value && box.candidates.size == 1) {
            hintButton.disabled = false
            easyBoxes.push(box)
        }
    })
    highlighterCheckbox.label.title = "Surligner les lignes, colonnes et régions contenant déjà " + (valueToInsert ? "un " + valueToInsert : "le chiffre sélectionné")
}

function onblur() {
    if (this.classList.contains("pencil")) {
        this.placeholder = this.value
        this.value = ""
        //this.type = "number"
        this.classList.remove("pencil")
    }
}

function onmouseenter(event) {
    if (sightCheckbox.checked){
        box = event.target
        box.neighbourhood.concat([box]).forEach(neighbour => {
            neighbour.parentElement.classList.add("table-active")
        })

        box.neighbourhood.forEach(neighbour => {
            if (valueToInsert && neighbour.value == valueToInsert) {
                for (neighbour of[box, neighbour]) {
                    neighbour.parentElement.classList.add("table-danger")
                }
            }
        })
}
}

function onmouseleave(event) {
    if (sightCheckbox.checked){
        box = event.target
        box.neighbourhood.concat([box]).forEach(neighbour => {
            neighbour.parentElement.classList.remove("table-active")
            neighbour.parentElement.classList.remove("table-danger")
        })
    }
}

function insert(radio) {
    valueToInsert = radio.value
    grid.style.cursor = valueToInsert ? "copy" : "text"
    highlight()
}

function undo() {
    if (history.length) {
        const previousState = history.pop()
        previousState.box.value = previousState.value
        previousState.box.placeholder = previousState.placeholder
        refreshBox(previousState.box)
        if (history.length < 1) {
            undoButton.disabled = true
            saveButton.disabled = true
        }
    }
}

function restart() {
    if (confirm("Effacer toutes les cases ?")) {
        boxes.filter(box => !box.disabled).forEach(box => {
            box.value = ""
            box.previousValue = ""
            box.placeholder = ""
            box.previousPlaceholder = ""
            box.setCustomValidity("")
        })
        let history = []
        undoButton.disabled = true
        restartButton.disabled = true
        boxes.forEach(searchCandidatesOf)
        refreshUI()
    }
}

function save() {
    let saveGame = boxes.map(box => box.value || UNKNOWN).join("")
    localStorage[location.search] = saveGame
    fixGridLink.href = "?" + saveGame
    saveButton.disabled = true
    alert("Partie sauvegardée")
}

window.onbeforeunload = function(event) {
    localStorage["sightCheckbox.checked"] = sightCheckbox.checked
    localStorage["highlighterCheckbox.checked"] = highlighterCheckbox.checked
    if (!saveButton.disabled) {
        event.preventDefault()
        event.returnValue = "La partie n'est pas sauvegardée. Quitter quand même ?"
    }
}

function showHint() {
    if (easyBoxes.length) {
        shuffle(easyBoxes)
        let box = easyBoxes.pop()
        box.placeholder = "💡"
        box.focus()
        return box
    }
    hintButton.disabled = true
}

function oncontextmenu(event) {
    event.preventDefault()
    while (contextMenu.firstChild) contextMenu.firstChild.remove()
    const box = event.target
    if (box.candidates.size) {
        Array.from(box.candidates).sort().forEach(candidate => {
            li = document.createElement("li")
            li.innerText = candidate
            li.classList = "list-group-item list-group-item-action"
            li.onclick = function(e) {
                contextMenu.style.display = "none"
                valueToInsert = e.target.innerText
                grid.style.cursor = "copy"
                document.getElementById("insertRadio" + valueToInsert).checked = true
                box.onclick()
            }
            li.oncontextmenu = function(e) {
				e.preventDefault()
				li.onclick(e)
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

    document.onclick = function(event) {
        contextMenu.style.display = "none"
        document.onclick = null
    }
    return false
}

document.onkeydown = function(event) {
    if (event.key == "Escape" && contextMenu.style.display == "block") {
        event.preventDefault()
        contextMenu.style.display = "none"
    }
}