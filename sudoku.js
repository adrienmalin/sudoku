const VALUES  = "123456789"
const UNKNOWN = '.'

let boxes         = []
let rows          = Array.from(Array(9), x => [])
let columns       = Array.from(Array(9), x => [])
let regions       = Array.from(Array(9), x => [])
let areaNames     = {
    ligne:   rows,
    colonne: columns,
    région:  regions,
}
let valueToInsert = ""
let easyBoxes     = []
let insertRadios  = []

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
                box.onfocus             = onfocus
                box.oninput             = oninput
                box.onblur              = onblur
                box.onclick             = onclick
                box.onmouseenter        = onmouseenter
                box.onmouseleave        = onmouseleave
            }
            box.oncontextmenu = oncontextmenu
            box.rowId         = rowId
            box.columnId      = columnId
            box.regionId      = regionId
            boxes.push(box)
            rows[rowId].push(box)
            columns[columnId].push(box)
            regions[regionId].push(box)
            columnId++
        }
        rowId++
    }

    if (localStorage["tool"] == "sight") sightCheckbox.checked = true
    else if (localStorage["tool"] == "highlighter") highlighterCheckbox.checked = true

    colorPickerInput.value = window.getComputedStyle(grid).getPropertyValue("--bs-body-color")

    boxes.forEach(box => {
        box.neighbourhood = new Set(rows[box.rowId].concat(columns[box.columnId]).concat(regions[box.regionId]))
        box.andNeighbourhood = Array.from(box.neighbourhood)
        box.neighbourhood.delete(box)
        box.neighbourhood = Array.from(box.neighbourhood)
    })

    insertRadios = Array.from(insertRadioGroup.getElementsByTagName("input")).slice(1)

    for (label of document.getElementsByTagName("label")) {
        label.control.label = label
    }
    let accessKeyModifiers = (/Win/.test(navigator.userAgent) || /Linux/.test(navigator.userAgent)) ? "Alt+Maj+"
                           : (/Mac/.test(navigator.userAgent)) ? "⌃⌥"
                           : "AccessKey+"
    for (node of document.querySelectorAll("*[accesskey]")) {
        shortcut = ` [${node.accessKeyLabel||(accessKeyModifiers+node.accessKey)}]`
        if (node.title) node.title += shortcut
        else if (node.label) node.label.title += shortcut
    }

    loadGame(history.state)

    if ("serviceWorker" in navigator) {
        navigator.serviceWorker.register(`service-worker.js`)
    }
}

window.onpopstate = (event) => loadGame(event.state)

function loadGame(state) {
    if (state) {
        boxes.forEach((box, i) => {
            if (!box.disabled) {
                box.value = state.boxesValues[i]
                box.placeholder = state.boxesPlaceholders[i]
            }
        })
        restartLink.classList.remove("disabled")
        undoButton.disabled = false
        fixGridLink.href = "?" + state.boxesValues.map(value => value || UNKNOWN).join("")
    } else {
        boxes.filter(box => !box.disabled).forEach(box => {
            box.value = ""
            box.placeholder = ""
        })
        restartLink.classList.add("disabled")
        undoButton.disabled = true
        fixGridLink.href = ""
    }

    checkBoxes()
    enableRadio()
    highlight()
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
        this.type        = "text"
        this.value       = this.placeholder
        this.placeholder = ""
        this.classList.add("pencil")
    } else {
        this.select()
    }
    if (penColor && inkPenRadio.checked) {
        this.style.setProperty("color", penColor)
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
    if (inkPenRadio.checked) {
        checkBoxes()
        enableRadio()
        highlight()
        fixGridLink.href = "?" + boxes.map(box => box.value || UNKNOWN).join("")
    }
    saveGame()
    restartLink.classList.remove("disabled")
    undoButton.disabled = false
}

function checkBoxes() {
    boxes.forEach(box => {
        box.setCustomValidity("")
        box.classList.remove("is-invalid")
        box.parentElement.classList.remove("table-danger")
        searchCandidatesOf(box)
        if (box.candidates.size == 0) {
            box.setCustomValidity("Aucun chiffre possible !")
            box.classList.add("is-invalid")
        }
    })

    for (let [areaName, areas] of Object.entries(areaNames))
        for (area of areas)
            area.filter(box => box.value).sort((box, neighbour) => {
                if(box.value == neighbour.value) {
                    area.forEach(neighbour => neighbour.parentElement.classList.add("table-danger"))
                    for (neighbour of [box, neighbour]) {
                        neighbour.setCustomValidity(`Il y a un autre ${box.value} dans cette ${areaName}.`)
                        neighbour.classList.add("is-invalid")
                    }
                }
                return box.value - neighbour.value
            })

    if (sudokuForm.checkValidity()) { // Correct grid
        if (boxes.filter(box => box.value == "").length == 0) {
            grid.classList.add("table-success")
            setTimeout(() => {
                if (confirm(`Bravo ! Vous avez résolu la grille. En voulez-vous une autre ?`))
                    location = "."
            }, 400)
        } else {
            grid.classList.remove("table-success")
        }
    } else { // Errors on grid
        grid.classList.remove("table-success")
        sudokuForm.reportValidity()
    }
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
        this.type = "number"
        this.classList.remove("pencil")
    }
}

function saveGame() {
    history.pushState({
        boxesValues: boxes.map(box => box.value),
        boxesPlaceholders: boxes.map(box => box.placeholder)
    }, "")
}

function onmouseenter(event) {
    if (sightCheckbox.checked){
        box = event.target
        box.andNeighbourhood.forEach(neighbour => {
            neighbour.parentElement.classList.add("table-active")
        })

        box.neighbourhood.forEach(neighbour => {
            if (valueToInsert && neighbour.value == valueToInsert) {
                for (neighbour of [box, neighbour]) {
                    neighbour.parentElement.classList.add("table-danger", "not-allowed")
                }
            }
        })
    }
}

function onmouseleave(event) {
    if (sightCheckbox.checked){
        box = event.target
        box.andNeighbourhood.forEach(neighbour => {
            neighbour.parentElement.classList.remove("table-active", "table-danger", "not-allowed")
        })
    }
}

function insert(radio) {
    if (radio.value && valueToInsert == radio.value) {
        radio.blur()
        insertRadio0.checked = true
        insert(0)
    } else {
        valueToInsert = radio.value
        grid.style.cursor = valueToInsert ? "copy" : "text"
        highlight()
    }
}

let penColor

function changeColor() {
    penColor = colorPickerInput.value
    colorPickerLabel.style.color = colorPickerInput.value
}

function restart() {
    if (confirm("Effacer toutes les cases ?")) {
        restartButton.disabled = true
        location.hash = ""
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
        li.classList = "list-group-item list-group-item-action disabled"
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

window.onbeforeunload = function(event) {
    saveGame()
    if (sightCheckbox.checked) localStorage["tool"] = "sight"
    else if (highlighterCheckbox.checked) localStorage["tool"] = "highlighter"
}