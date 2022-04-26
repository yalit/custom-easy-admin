/**
 * Get the first parent element being the same tag as in input
 */
export function getParentFromTagName(node, tag) {
    let parentNode = node.parentNode
    if (parentNode === undefined || parentNode.tagName === tag.toUpperCase()) {
        return parentNode;
    }

    return getParentFromTagName(parentNode, tag);
}

/**
 * Get the first parent element having the className in input
 */
export function getParentFromClassName(node, className) {
    let parentNode = node.parentNode
    if (parentNode === undefined || parentNode.classList.contains(className)) {
        return parentNode;
    }

    return getParentFromClassName(parentNode, className);
}

/**
 * Get all the child elements in the direct children with the class "className"
 */
export function getChildsWithClassName(node, className) {
    return Array.from(node.children).filter(elem => elem.classList.contains(className))
}

/**
 * Get the child in the direct children with the 'data-label' value (= dataLabel in input)
 */
export function getChildFromDataLabel(node, dataLabel) {
    let foundChild = null
    Array.from(node.children).forEach(function(element) {
        if (element.dataset.label === dataLabel) {
            foundChild = element
        }
    })

    return foundChild
}