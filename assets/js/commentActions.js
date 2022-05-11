import {BootstrapModalConfirmation} from "./bootstrap-modal-confirmation";
import {getChildFromDataLabel, getParentFromTagName} from "./includes/DOMHelpers";

const publishActionButtons = document.getElementsByClassName('action-comment_publish');

let commentPublished = false;

Array.from(publishActionButtons).forEach(function (button) {
    button.addEventListener('click', function(event) {
        if (commentPublished) {
            commentPublished = false;
            return;
        }

        event.preventDefault();
        const dataRow = getParentFromTagName(event.target, 'tr')
        const dataTitle = getChildFromDataLabel(dataRow, "Content")

        const commentPublish = function(result) {
            commentPublished = result
            if (result){
                event.target.click()
            }
        }

        const modal = new BootstrapModalConfirmation(dataRow, 'publish', dataTitle.innerHTML,  commentPublish)
        modal.display()
    })
})


const cancelActionButtons = document.getElementsByClassName('action-comment_cancel');

let commentCancelled = false;

Array.from(cancelActionButtons).forEach(function (button) {
    button.addEventListener('click', function(event) {
        if (commentCancelled) {
            commentCancelled = false;
            return;
        }

        event.preventDefault();
        const dataRow = getParentFromTagName(event.target, 'tr')
        const dataTitle = getChildFromDataLabel(dataRow, "Content")

        const commentCancel = function(result) {
            commentCancelled = result
            if (result){
                event.target.click()
            }
        }

        const modal = new BootstrapModalConfirmation(dataRow, 'cancel', dataTitle.innerHTML,  commentCancel)
        modal.display()
    })
})