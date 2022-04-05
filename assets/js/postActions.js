import {BootstrapModalConfirmation} from "./bootstrap-modal-confirmation";
import {getChildFromDataLabel, getParentFromTagName} from "./includes/DOMHelpers";

const publishActionButtons = document.getElementsByClassName('action-post_publish');

let postPublished = false;

Array.from(publishActionButtons).forEach(function (button) {
    button.addEventListener('click', function(event) {
        if (postPublished) {
            postPublished = false;
            return;
        }

        event.preventDefault();
        const dataRow = getParentFromTagName(event.target, 'tr')
        const dataTitle = getChildFromDataLabel(dataRow, "Title")

        const postPublish = function(result) {
            postPublished = result
            if (result){
                event.target.click()
            }
        }

        const modal = new BootstrapModalConfirmation(dataRow, 'publish', dataTitle.innerHTML,  postPublish)
        modal.display()
    })
})