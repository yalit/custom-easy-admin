import {getParentFromClassName} from "./includes/DOMHelpers";

function triggerRemoveRow() {
    Array.from(document.getElementsByClassName('remove_row')).forEach(elem => {
        elem.removeEventListener('click', removeCollectionElement)
        elem.addEventListener('click', removeCollectionElement)
    })
}

function triggerAddRow() {
    Array.from(document.getElementsByClassName('new_item_row')).forEach(elem => {
        elem.removeEventListener('click', addToCollectionElement)
        elem.addEventListener('click', addToCollectionElement)
    })
}

triggerAddRow()
triggerRemoveRow()

function removeCollectionElement(event) {
    event.target.parentElement.remove()
}

function addToCollectionElement(event) {
    const collection = getParentFromClassName(event.target, 'custom_collection')
    let  newElement = document.createElement('div')
    const index = Number(collection.dataset.index)
    newElement.innerHTML = collection.dataset.prototype.replace(/__name__/g, index);
    collection.children[collection.children.length -1].before(newElement.children[0])
    collection.dataset.index = index + 1
    triggerRemoveRow()
}