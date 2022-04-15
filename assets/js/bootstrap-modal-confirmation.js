const modal_template = `
<div id="confirmation-modal" class="modal show" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="confirmation-modal-title" class="modal-title">Confirm %%action%% ?</h5>
            </div>
            <div id="confirmation-modal-body" class="modal-body">
                <p>Do you confirm to %%action%% : %%objectName%% Post?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn-confirm">%%modal_confirm_button%%</button>
                <button type="button" class="btn btn-secondary" id="btn-cancel" data-dismiss="modal">%%modal_close_button%%</button>
            </div>
        </div>
    </div>
</div>
`

export class BootstrapModalConfirmation {
    constructor(contextNode, action, objectName, confirmCallback) {
        this.contextNode = contextNode
        this.action = action
        this.objectName = objectName
        this.confirmCallBack = confirmCallback;
        this.modal = this.createModal(contextNode)
    }

    createModal(contextNode) {
        let element = document.createElement('div');
        element.innerHTML = modal_template;
        element = element.firstElementChild
        contextNode.appendChild(element)

        const modalTitle = document.getElementById('confirmation-modal-title')
        modalTitle.innerHTML = modalTitle.innerHTML.replace('%%action%%', this.action)
        const modalBody = document.getElementById('confirmation-modal-body')
        modalBody.innerHTML = modalBody.innerHTML.replace('%%action%%', this.action)
        modalBody.innerHTML = modalBody.innerHTML.replace('%%objectName%%', this.objectName)

        const btnConfirm = document.getElementById('btn-confirm')
        btnConfirm.innerHTML = "Confirm"
        const btnCancel = document.getElementById('btn-cancel')
        btnCancel.innerHTML = "Cancel"

        const confirmationModal = this;
        btnCancel.addEventListener('click', function () {
            confirmationModal.confirmCallBack(false)
            confirmationModal.hide()
        })

        btnConfirm.addEventListener('click', function () {
            confirmationModal.confirmCallBack(true)
            confirmationModal.hide();
        })

        return element;
    }

    display() {
        this.modal.classList.add('show');
        this.modal.style['display'] = 'block';
    }

    hide() {
        this.modal.remove()
    }
}