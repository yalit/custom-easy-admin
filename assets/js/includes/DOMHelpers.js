export function getParentFromTagName(node, tag) {
    let parentNode = node.parentNode
    if (parentNode === undefined || parentNode.tagName === tag.toUpperCase()) {
        return parentNode;
    }

    return getParentFromTagName(parentNode, tag);
}


export function getChildFromDataLabel(node, dataLabel) { //only 1 level
    let foundChild = null
    Array.from(node.children).forEach(function(element) {
        if (element.dataset.label === dataLabel) {
            foundChild = element
        }
    })

    return foundChild
}