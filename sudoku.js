const VALUES = "123456789"
const UNKNOWN = '.'
const SUGESTION_DELAY = 60000 //ms

let boxes = []
let rows = Array.from(Array(9), x => [])
let columns = Array.from(Array(9), x => [])
let regions = Array.from(Array(9), x => [])
let suggestionTimer = null
let valueToInsert = ""
let history = []
let accessKeyModifiers = "AccessKey+"

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

window.onload = function () {
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

    loadSavedGame()

    boxes.forEach(box => {
        box.neighbourhood = new Set(rows[box.rowId].concat(columns[box.columnId]).concat(regions[box.regionId]))
        box.neighbourhood.delete(box)
        box.neighbourhood = Array.from(box.neighbourhood)
        searchCandidatesOf(box)
    })

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
    const savedGame = localStorage[location.pathname]
    if (savedGame) {
        boxes.forEach((box, i) => {
            if (!box.disabled && savedGame[i] != UNKNOWN) {
                box.value = savedGame[i]
                box.previousValue = savedGame[i]
            }
        })
        fixGridLink.href = savedGame
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

function onfocus() {
    if (pencilRadio.checked) {
        this.value = this.placeholder
        this.classList.add("pencil")
    } else {
        this.select()
    }
    this.style.caretColor = valueToInsert? "transparent": "auto"
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
        if (valueToInsert)
            this.value += valueToInsert
            this.oninput()
    } else if (eraserRadio.checked) {
        this.value = ""
        this.placeholder = ""
        this.oninput()
    }
}

function oninput() {
    history.push({ box: this, value: this.previousValue, placeholder: this.previousPlaceholder })
    undoButton.disabled = false
    if (pencilRadio.checked) {
        this.value = Array.from(new Set(this.value)).sort().join("")
        this.previousValue = ""
        this.previousPlaceholder = this.value
    } else {
        this.previousValue = this.value
        this.previousPlaceholder = this.placeholder
        refreshBox(this)
    }

    if (suggestionTimer) clearTimeout(suggestionTimer)
    suggestionTimer = setTimeout(showSuggestion, SUGESTION_DELAY)
}

function refreshBox(box) {
    saveGame()
    checkBox(box)
    refreshUI()
}

function saveGame() {
    let saveGame = boxes.map(box => box.value || UNKNOWN).join("")
    localStorage[location.pathname] = saveGame
    fixGridLink.href = saveGame
}

function refreshUI() {
    enableRadio()
    highlight()
    showEasyBoxes()
}

function enableRadio() {
    for (radio of insertRadioGroup.getElementsByTagName("input")) {
        if (boxes.filter(box => box.value == "").some(box => box.candidates.has(radio.value))) {
            radio.disabled = false
            radio.label.title = `Insérer un ${radio.value} [${radio.accessKeyLabel||(accessKeyModifiers+radio.accessKey)}]`
        } else {
            radio.disabled = true
            radio.label.title = `Tous les ${radio.value} sont posés.`
            if (valueToInsert == radio.value) {
                let nextRadio = document.querySelector(".insertRadioGroup :checked ~ input:enabled") || document.querySelector(".insertRadioGroup :enabled")
                if (nextRadio) {
                    nextRadio.click()
                    nextRadio.onfocus()
                } else {
                    valueToInsert = ""
                }
            }
        }
    }
}

function highlight() {
    boxes.forEach(box => {
        if (valueToInsert && box.value == valueToInsert) {
            box.classList.add("same-value")
            box.tabIndex = -1
        } else {
            box.classList.remove("same-value")
            if (box.disabled) {
                box.classList.add("forbidden")
            } else {
                if (valueToInsert && highlighterCheckbox.checked && !box.candidates.has(valueToInsert)) {
                    box.classList.add("forbidden")
                    box.tabIndex = -1
                } else {
                    box.classList.remove("forbidden")
                    box.tabIndex = 0
                }
            }
        }
    })
    highlighterCheckbox.label.title = "Surligner les lignes, colonnes et régions contenant déjà " + (valueToInsert? "un " + valueToInsert: "le chiffre sélectionné")
}

function showEasyBoxes() {
    boxes.filter(box => !box.disabled).forEach(box => {
        if (!box.value && box.candidates.size == 1) {
            box.classList.add("one-candidate")
            box.onclick = function() {
                valueToInsert = this.candidates.values().next().value
                document.getElementById("insertRadio" + valueToInsert).checked = true
                onclick.apply(box)
            }
        } else {
            box.classList.remove("one-candidate")
            box.onclick = onclick
        }
    })
}

function checkBox(box) {
    box.neighbourhood.concat([box]).forEach(neighbour => {
        searchCandidatesOf(neighbour)
        neighbour.setCustomValidity("")
    })

    for (neighbour1 of box.neighbourhood) {
        if (neighbour1.value) {
            for (area of [
                { name: "région", neighbours: regions[neighbour1.regionId] },
                { name: "ligne", neighbours: rows[neighbour1.rowId] },
                { name: "colonne", neighbours: columns[neighbour1.columnId] },
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
            }
        }
    }

    if (box.form.checkValidity()) { // Correct grid
        if (boxes.filter(box => box.value == "").length == 0)
            setTimeout(() => alert(`Bravo ! Vous avez résolu la grille.`), 500)
    } else { // Errors on grid
        box.form.reportValidity()
    }
}

function onblur() {
    if (this.classList.contains("pencil")) {
        this.placeholder = this.value
        this.value = ""
        this.classList.remove("pencil")
    }
}

function insert(radio) {
    if (radio.value == valueToInsert) {
        valueToInsert = ""
        radio.checked = false
    } else {
        valueToInsert = radio.value
    }
    if (inkPenRadio.checked) customCursor = "url(img/ink-pen.svg) 2 22"
    if (pencilRadio.checked) customCursor = "url(img/pencil.svg) 2 22"
    if (eraserRadio.checked) customCursor = "url(img/eraser.svg) 2 22"
    fallbackCursor = valueToInsert? "copy": "text"
    grid.style.cursor = `${customCursor}, ${fallbackCursor}`
    highlight()
}

function undo() {
    if (history.length) {
        const previousState = history.pop()
        previousState.box.value = previousState.value
        previousState.box.placeholder = previousState.placeholder
        refreshBox(previousState.box)
        if (history.length < 1) undoButton.disabled = true
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
        boxes.forEach(searchCandidatesOf)
        refreshUI()
    }
}

function showSuggestion() {
    const easyBoxes = boxes.filter(box => box.value == "" && box.candidates.size == 1)
    if (easyBoxes.length) {
        let randomEasyBox = shuffle(easyBoxes)[0]
        randomEasyBox.placeholder = "💡"
        randomEasyBox.focus()
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
        Array.from(box.candidates).sort().forEach(candidate => {
            li = document.createElement("li")
            li.innerText = candidate
            li.onclick = function (event) {
                contextMenu.style.display = "none"
                box.onfocus()
                box.value = event.target.innerText
                box.oninput()
                box.onblur()
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
    console.log(event.target)
    contextMenu.style.display = "block"
    return false
}

document.onclick = function (event) {
    if (contextMenu.style.display == "block")
        contextMenu.style.display = "none"
}

document.onkeydown = function(event) {
    if (event.key == "Escape" && contextMenu.style.display == "block") {
        event.preventDefault()
        contextMenu.style.display = "none"
    }
}
