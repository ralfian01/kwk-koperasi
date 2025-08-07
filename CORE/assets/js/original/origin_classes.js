/*
    **********
    # Dexa Classes
    **********
*/

window.$dexa = function () {

    // 
};

// Redirect to url with post data
class _redirectPost {

    constructor() {
        this.postData = {};
    }

    init() {

        this.postData = {};
        return this;
    }

    setPostData(object = {}) {

        Object.keys(object).forEach((key) => {

            this.postData[key] = object[key];
        });

        return this;
    }

    to(url = '') {

        let form;

        if (Object.keys(this.postData).length >= 1) {

            form = document.createElement('form');
            form.setAttribute('action', url);
            form.style.display = 'none';
            form.setAttribute('method', 'post');

            Object.keys(this.postData).forEach((key) => {

                const input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('value', this.postData[key]);
                input.setAttribute('name', key);

                form.append(input);
            });
        }

        const empty = [undefined, null, ''];

        if (
            empty.indexOf(url) < 0
            && url.length >= 1
        ) {

            if (Object.keys(this.postData).length >= 1) {

                const submit = document.createElement('input');
                submit.setAttribute('type', 'submit');
                submit.setAttribute('value', 'submit');

                form.append(submit);

                document.getElementsByTagName('body')[0].appendChild(form);

                form.submit();

                setTimeout(($this) => {

                    $this.remove();
                }, 150, form);

                return this.init();
            } else {

                window.location.href = url;

                return this.init();
            }
        }
    }
}

// App Console
class _notifConsole {

    /**
     * Valid notification context
     * @var {Array}
     */
    #notifContextOptions = [
        'success',
        'error',
        'warning',
        'info',
        'netral'
    ];

    #notifContainer;
    #notifContainerStyle;
    #notifCloseAnimation;

    #notifParts = {};
    #overrideNotif = {};

    #notifContext = {};
    #overrideNotifContext = {};

    constructor(options = {
        overrideNotif: {
            parent: null,
            content: null
        },
        closeAnimation: null,
    }
    ) {
        let parser = new DOMParser();

        this.#notifParts = {
            parent: parser.parseFromString(
                `<div class="dx_ntf_org-q3im0c"></div>`,
                'text/html'
            ),
            content: parser.parseFromString(
                `<div class="ntf_content-q3im0c"></div>`,
                'text/html'
            ),
        };

        this.#notifContext = {
            netral: {
                colorTheme: 'rgba(100, 100, 100, 1)',
            },
            success: {
                colorTheme: 'rgba(111, 167, 77, 1)',
            },
            error: {
                colorTheme: 'rgba(233, 70, 89, 1)',
            },
            warning: {
                colorTheme: 'rgba(233, 132, 41, 1)',
            },
            info: {
                colorTheme: 'rgba(56, 167, 171, 1)',
            }
        };

        this.#overrideNotif = options.overrideNotif;
        this.#initOverrider();

        this.#notifCloseAnimation = options.closeAnimation ?? 'remove-q3im0c';
    }

    /**
     * @returns {void}
     */
    #initOverrider() {
        let parser = new DOMParser();

        // Notif container
        if (typeof this.#overrideNotif.parent !== 'undefined') {
            this.#notifParts.parent = parser.parseFromString(
                this.#overrideNotif.parent ?? this.#notifParts.parent.body.innerHTML,
                'text/html');
        }

        // Notif body
        if (typeof this.#overrideNotif.content !== 'undefined') {
            this.#notifParts.content = parser.parseFromString(
                this.#overrideNotif.content ?? this.#notifParts.content.body.innerHTML,
                'text/html');
        }
    }

    /**
     * Notif context options:
     * - success
     * - error
     * - warning
     * - info
     * 
     * @param {String} context Notif context
     * @param {Object} options 
     * @returns {this}
     */
    overrideNotifContext(
        context = null,
        options = {
            colorTheme: null
        }) {

        if (this.#notifContextOptions.indexOf(context) < 0) {
            return this;
        }

        if (typeof this.#overrideNotifContext[context] === 'undefined') {
            this.#overrideNotifContext[context] = {
                colorTheme: null
            };
        }

        this.#overrideNotifContext[context].colorTheme = options.colorTheme ?? null;

        return this;
    }

    /**
     * @returns {void}
     */
    #initNotifContext(context = null) {
        if (this.#notifContextOptions.indexOf(context) < 0) {
            return;
        }

        if (typeof this.#overrideNotifContext[context] === 'undefined') {
            return;
        }

        this.#notifContext[context].colorTheme =
            this.#overrideNotifContext[context].colorTheme
            ?? this.#notifContext[context].colorTheme;
    }

    /**
     * @returns {this}
     */
    #init(context = null) {
        if (this.#notifContextOptions.indexOf(context) < 0) {
            return;
        }

        this.#notifContainer = document.createElement('div');
        this.#notifContainerStyle = document.createElement('style');

        return this;
    }

    /**
     * Render notif DOM
     * @returns {void}
     */
    #renderDOM(context = null, text = null, duration = null) {
        if (this.#notifContextOptions.indexOf(context) < 0) {
            return;
        }

        this.#notifContainerStyle.setAttribute('id', 'dx_ntf_org-q3im0c');
        this.#notifContainerStyle.innerHTML = `
            .dx_ntf_org-q3im0c {
                display: flex;
                align-items: center;
                justiry-content: center;
                gap: 15px;
                width: fit-content;
                max-width: 90vw;
                border-radius: 5px;
                box-sizing: border-box;
                padding: 10px 15px;
                padding-right: 25px;
                position: fixed;
                bottom: 40px;
                left: 50%;
                transform: translate3d(-50%, 0px, 1px) scale(1);
                z-index: 9999;
                background: #fff;
                color: #202020;
                cursor: default;
                border: 1px solid ${this.#notifContext[context].colorTheme};
                border-left-width: 4px;
                animation: dx_ntf_org-q3im0c ease 300ms;
                line-height: 1.3;
            }

            @media only screen and (max-width: 600px) {
                .dx_ntf_org-q3im0c {
                    max-width: none;
                }
            }

            @keyframes dx_ntf_org-q3im0c {
                from {
                    transform: translate3d(-50%, 0px, 1px) scale(0);
                }
                to {
                    transform: translate3d(-50%, 0px, 1px) scale(1);
                }
            }

            .dx_ntf_org-q3im0c::after {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                content: '';
                opacity: 0.15;
                background: ${this.#notifContext[context].colorTheme};
                z-index: 0;
            }

            .dx_ntf_org-q3im0c::before {
                position: relative;
                display: inline;
                content: '';
                height: 25px;
                width: 25px;
                min-height: 25px;
                min-width: 25px;
                max-height: 25px;
                max-width: 25px;
                mask-image: url("data:image/svg+xml;charset=utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' ><path d='M22 20H2V18H3V11.0314C3 6.04348 7.02944 2 12 2C16.9706 2 21 6.04348 21 11.0314V18H22V20ZM9.5 21H14.5C14.5 22.3807 13.3807 23.5 12 23.5C10.6193 23.5 9.5 22.3807 9.5 21Z'></path></svg>");
                mask-repeat: no-repeat;
                mask-position: center;
                mask-size: auto 100%;
                background: ${this.#notifContext[context].colorTheme};
                z-index: 0;
            }

            .dx_ntf_org-q3im0c .ntf_content-q3im0c,
            .ntf_content-q3im0c {
                font-family: Nunito-sans, sans-serif;
                text-align: left;
                font-size: 15.8px;
                font-weight: normal;
                display: flex;
                align-item: flex-start;
                justify-content: flex-start;
            }
            .ntf_content-q3im0c::before {
                content: '` + context[0].toUpperCase() + context.substring(1) + `:';
                font-weight: bold;
                margin-right: 5px;
                color: ${this.#notifContext[context].colorTheme};
            }

            .dx_ntf_org-q3im0c.remove-q3im0c {
                transform: translate3d(-50%, 100%, 1px) scale(1);
                opacity: 0;
                transition-duration: 300ms;
            }
        `;

        document.body.appendChild(this.#notifContainerStyle);

        let parentClass = Array.from(this.#notifParts.parent.body.firstChild.classList);
        parentClass = parentClass.join(' ');

        this.#notifParts.content.body.firstChild.innerHTML = text;

        this.#notifContainer.classList.add(parentClass);
        this.#notifContainer.innerHTML = this.#notifParts.content.body.innerHTML;

        // Notif events
        this.#notifContainer.addEventListener('click', (e) => {
            this.#close();
        });

        // Close notif on timeout
        setTimeout(() => {
            this.#close();
        }, duration);
    }

    /**
     * Close notification
     * @return {this}
     */
    #close() {

        this.#notifContainer.classList.add(this.#notifCloseAnimation);

        setTimeout(() => {

            this.#notifContainer.remove();
            this.#notifContainerStyle.remove();
        }, 301);

        return this;
    }


    /**
     * Show success notification
     * @param {String} text 
     * @param {Integer} duration
     * @returns {void}
     */
    success(text = null, duration = 2500) {
        // Initialize notif context
        this.#initNotifContext('success');

        // Initialize notif
        this.#init('success');

        // Render notif DOM
        this.#renderDOM('success', text, duration);

        // Append child
        document.body.appendChild(this.#notifContainer);
    }

    /**
     * Show error notification
     * @param {String} text 
     * @param {Integer} duration
     * @returns {void}
     */
    error(text = null, duration = 2500) {
        // Initialize notif context
        this.#initNotifContext('error');

        // Initialize notif
        this.#init('error');

        // Render notif DOM
        this.#renderDOM('error', text, duration);

        // Append child
        document.body.appendChild(this.#notifContainer);
    }

    /**
     * Show warning notification
     * @param {String} text 
     * @param {Integer} duration
     * @returns {void}
     */
    warning(text = null, duration = 2500) {
        // Initialize notif context
        this.#initNotifContext('warning');

        // Initialize notif
        this.#init('warning');

        // Render notif DOM
        this.#renderDOM('warning', text, duration);

        // Append child
        document.body.appendChild(this.#notifContainer);
    }

    /**
     * Show info notification
     * @param {String} text 
     * @param {Integer} duration
     * @returns {void}
     */
    info(text = null, duration = 2500) {
        // Initialize notif context
        this.#initNotifContext('info');

        // Initialize notif
        this.#init('info');

        // Render notif DOM
        this.#renderDOM('info', text, duration);

        // Append child
        document.body.appendChild(this.#notifContainer);
    }

    /**
     * Show netral notification
     * @param {String} text 
     * @param {Integer} duration
     * @returns {void}
     */
    netral(text = null, duration = 2500) {
        // Initialize notif context
        this.#initNotifContext('netral');

        // Initialize notif
        this.#init('netral');

        // Render notif DOM
        this.#renderDOM('netral', text, duration);

        // Append child
        document.body.appendChild(this.#notifContainer);
    }


    // Method
    removeElem(elem) {

        elem.style.transform = 'translate3d(-50%, 100%, 1px) scale(1)';
        elem.style.opacity = '0';

        setTimeout((elem) => {

            elem.remove();
        }, 301, elem);
    }
}

class _modalBox {

    #title;
    #description;
    #buttonOption;

    // Modal DOM
    #modalContainer;
    #modalContainerStyle;
    #modalCloseAnimation;
    #modalParts;
    #overrideModal;

    constructor(
        options = {
            overrideModal: {
                modalParent: null,
                modalDescription: null,
                modalTitle: null,
                modalBody: null,
                modalButton: {
                    confirm: null,
                    cancel: null,
                    alternative: null,
                },
            },
            closeAnimation: null,
        }) {

        let parser = new DOMParser();

        this.#modalParts = {
            modalParent: parser.parseFromString(
                `<div class="dx_mdl_org-q3im0c"></div>`,
                'text/html'
            ),
            modalDescription: parser.parseFromString(
                `<div class="mdl_description-q3im0c"></div>`,
                'text/html'
            ),
            modalTitle: parser.parseFromString(
                `<div class="mdl_title-q3im0c"></div>`,
                'text/html'
            ),
            modalBody: parser.parseFromString(
                `<div class="mdl_container-q3im0c"></div>`,
                'text/html'
            ),
            modalButton: {
                confirm: parser.parseFromString(
                    `<button class="mdl_button-q3im0c" id="confirm"></button>`,
                    'text/html'
                ),
                cancel: parser.parseFromString(
                    `<button class="mdl_button-q3im0c" id="cancel"></button>`,
                    'text/html'
                ),
                alternative: parser.parseFromString(
                    `<button class="mdl_button-q3im0c" id="alternative"></button>`,
                    'text/html'
                ),
            },
        };

        this.#overrideModal = options.overrideModal;
        this.#initOverrider();

        this.#modalCloseAnimation = options.closeAnimation ?? 'remove-q3im0c';
    }

    /**
     * Initialize overrider
     * @returns {void}
     */
    #initOverrider() {

        let parser = new DOMParser();

        // Modal container
        if (typeof this.#overrideModal.modalParent !== 'undefined') {
            this.#modalParts.modalParent = parser.parseFromString(this.#overrideModal.modalParent, 'text/html');
        }

        // Modal body
        if (typeof this.#overrideModal.modalBody !== 'undefined') {
            this.#modalParts.modalBody = parser.parseFromString(this.#overrideModal.modalBody, 'text/html');
        }

        // Modal title
        if (typeof this.#overrideModal.modalTitle !== 'undefined') {
            this.#modalParts.modalTitle = parser.parseFromString(this.#overrideModal.modalTitle, 'text/html');
        }

        // Modal description
        if (typeof this.#overrideModal.modalDescription !== 'undefined') {
            this.#modalParts.modalDescription = parser.parseFromString(this.#overrideModal.modalDescription, 'text/html');
        }

        // Modal buttons
        if (typeof this.#overrideModal.modalButton !== 'undefined') {
            // Confirm button
            if (typeof this.#overrideModal.modalButton.confirm !== 'undefined') {
                this.#modalParts.modalButton.confirm = parser.parseFromString(this.#overrideModal.modalButton.confirm, 'text/html');
                this.#modalParts.modalButton.confirm.body.firstChild.setAttribute('id', 'confirm');
            }

            // Cancel button
            if (typeof this.#overrideModal.modalButton.cancel !== 'undefined') {
                this.#modalParts.modalButton.cancel = parser.parseFromString(this.#overrideModal.modalButton.cancel, 'text/html');
                this.#modalParts.modalButton.cancel.body.firstChild.setAttribute('id', 'cancel');
            }

            // Alternative button
            if (typeof this.#overrideModal.modalButton.alternative !== 'undefined') {
                this.#modalParts.modalButton.alternative = parser.parseFromString(this.#overrideModal.modalButton.alternative, 'text/html');
                this.#modalParts.modalButton.alternative.body.firstChild.setAttribute('id', 'alternative');
            }
        }
    }

    /**
     * @returns {Boolean}
     */
    #ensureOverrider() {
        let numberParts = Object.keys(this.#modalParts).length;
        numberParts += Object.keys(this.#modalParts.modalButton).length;

        let found = Object.keys(this.#overrideModal).length;

        if (typeof this.#overrideModal.modalButton !== 'undefined') {
            found += Object.keys(this.#overrideModal.modalButton).length;
        }

        return !(found >= numberParts);
    }

    /**
     * Initialize Modal attributes
     * @returns {this}
     */
    #init() {
        this.#title = "Modal title";
        this.#description = "Modal description";
        this.#buttonOption = {
            confirmText: 'Confirm',
            cancelText: 'Cancel',
            alternativeText: null
        };

        this.#modalContainer = document.createElement('div');
        this.#modalContainerStyle = document.createElement('style');

        return this;
    }

    /**
     * Render Modal DOM
     * @returns {void}
     */
    #renderDOM() {

        if (this.#ensureOverrider()) {
            this.#modalContainerStyle.setAttribute('id', 'dx_mdl_org-q3im0c');
            this.#modalContainerStyle.innerHTML = `
                .dx_mdl_org-q3im0c {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    box-sizing: border-box;
                    padding: 15px;
                    z-index: 999999999;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100vw;
                    height: 100vh;
                    background: rgba(0, 0, 0, 0.25);
                    backdrop-filter: blur(4px);
                    animation: dx_mdl_org-q3im0c linear 200ms;
                    opacity: 1;
                }

                @keyframes dx_mdl_org-q3im0c {
                    from {
                        opacity: 0;
                    }
                    to {
                        opacity: 1;
                    }
                }

                .dx_mdl_org-q3im0c > .mdl_container-q3im0c,
                .mdl_container-q3im0c {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    padding: 15px;
                    box-sizing: border-box;
                    z-index: 1;
                    background: #fff;
                    border-radius: 5px;
                    border: 1px solid rgb(230, 230, 230); 
                    box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
                    font-family: verdana;
                    width: 350px;
                    font-size: 15px;
                    color: rgb(20, 20, 20);
                }

                .dx_mdl_org-q3im0c > .mdl_container-q3im0c .mdl_title-q3im0c,
                .mdl_title-q3im0c {
                    font-weight: bold;
                    font-size: 1.4em; 
                    text-align: center;
                    line-height: 1.2;
                }

                .dx_mdl_org-q3im0c > .mdl_container-q3im0c .mdl_description-q3im0c,
                .mdl_description-q3im0c {
                    font-size: 1em;
                    text-align: center;
                    line-height: 1.2;
                    margin-top: 20px;
                }

                .dx_mdl_org-q3im0c > .mdl_container-q3im0c .mdl_button-q3im0c,
                .mdl_button-q3im0c {
                    width: 100%;
                    padding: 10px;
                    font-size: 1em;
                    box-sizing: border-box;
                    cursor: pointer;
                    border-radius: 5px;
                    border: 1px solid rgb(200, 200, 200);
                }

                .remove-q3im0c {
                    transition-duration: 200ms;
                    opacity: 0;
                }
            `;

            document.body.appendChild(this.#modalContainerStyle);
        }

        // Modal Buttons
        this.#modalParts.modalButton.confirm.body.firstChild.innerHTML = this.#buttonOption.confirmText;
        this.#modalParts.modalButton.cancel.body.firstChild.innerHTML = this.#buttonOption.cancelText;
        this.#modalParts.modalButton.alternative.body.firstChild.innerHTML = this.#buttonOption.alternativeText;

        this.#modalParts.modalTitle.body.firstChild.innerHTML = this.#title;
        this.#modalParts.modalDescription.body.firstChild.innerHTML = this.#description;
        this.#modalParts.modalBody.body.firstChild.innerHTML = `
            ${this.#modalParts.modalTitle.body.innerHTML}
            ${this.#modalParts.modalDescription.body.innerHTML}

            <div style="margin-top: 15px; display: flex; align-items: flex-start; justify-content: space-between; width: 100%; box-sizing: border-box; gap: 10px;">
                ${this.#modalParts.modalButton.confirm.body.innerHTML}
                ${this.#modalParts.modalButton.cancel.body.innerHTML}
            </div>
        `;
        this.#modalParts.modalParent.body.firstChild.innerHTML = this.#modalParts.modalBody.body.innerHTML;

        this.#modalContainer.innerHTML = this.#modalParts.modalParent.body.innerHTML;
    }

    /**
     * Override modal
     * @param {*} options 
     * @returns this
     */
    overrideModal(
        options = {
            overrideModal: {
                modalParent: null,
                modalDescription: null,
                modalTitle: null,
                modalBody: null,
                modalButton: {
                    confirm: null,
                    cancel: null,
                    alternative: null,
                },
            },
            closeAnimation: null,
        }) {

        this.#overrideModal = options.overrideModal;
        this.#initOverrider();

        if (typeof options.closeAnimation !== 'undefined') {
            this.#modalCloseAnimation = options.closeAnimation;
        }

        return this;
    }

    /**
     * Set modal attribute
     * @param {Object} options {
     *      title,
     *      description,
     *      button: {
     *          confirmText,
     *          cancelText,
     *          alternativeText
     *      }
     *  }
     * @returns {this}
     */
    setup(
        options = {
            title: null,
            description: null,
            button: {
                confirmText: null,
                cancelText: null,
                alternativeText: null
            }
        }) {

        // Initialize
        this.#init();

        this.#title = options.title ?? this.#title;
        this.#description = options.description ?? this.#description;

        this.#buttonOption = {
            confirmText: options.button.confirmText ?? this.#buttonOption.confirmText,
            cancelText: options.button.cancelText ?? this.#buttonOption.cancelText,
            alternativeText: options.button.alternativeText ?? this.#buttonOption.alternativeText,
        };

        this.#renderDOM();

        return this;
    }

    /**
     * Open Modal
     * @param {Object} callback {
     *      onConfirm,
     *      onCancel,
     *      onAlternative
     *  }
     * @returns {this}
     */
    open(
        callback = {
            onConfirm: null,
            onCancel: null,
            onAlternative: null,
        }
    ) {
        if (typeof this.#modalContainer === 'undefined') {
            this.setup();
        }

        // ## Button events
        // Confirm button
        if (typeof callback.onConfirm !== 'undefined') {
            this.#modalContainer.querySelector('#confirm')
                .addEventListener('click', (e) => {
                    callback.onConfirm(this, e);
                });
        }

        // Cancel button
        if (typeof callback.onCancel !== 'undefined') {
            this.#modalContainer.querySelector('#cancel')
                .addEventListener('click', (e) => {
                    callback.onCancel(this, e);
                });
        }

        // Alternative button
        if (typeof callback.onAlternative !== 'undefined') {
            this.#modalContainer.querySelector('#alternative')
                .addEventListener('click', (e) => {
                    callback.onAlternative(this, e);
                });
        }

        document.body.appendChild(this.#modalContainer);

        return this;
    }

    /**
     * Close Modal
     * @param {Function} callback
     * @returns {this}
     */
    close(callback = null) {

        this.#modalContainer.firstChild.classList.add(this.#modalCloseAnimation);

        setTimeout(() => {

            this.#modalContainer.remove();
            this.#modalContainerStyle.remove();
        }, 201);

        return this;
    }
}

class _formCollect {

    // Class to collect value from form input, select, and textarea
    formTarget = null;
    requiredData = null;

    constructor() {

    }

    // Set value to default
    init() {

        this.formTarget = null;
        this.requiredData = null;
        return this;
    }

    target($target) {

        // Check $target is instance of HTMLElement
        this.formTarget = $target;
        return this;
    }

    required($required = []) {

        /*
            Array object
            {
                name: form name,
                type: form type,
                return: {
                    encode: encode return value (base64)
                }
            }
        */

        // A method for specifying which items in a form are required
        this.requiredData = $required;
        return this;
    }

    collect($cbSuccess, $cbError, option = {}) {

        if (this.formTarget == null) {

            console.error(
                `error from class %c${this.constructor.name}() %c\nMessage: Form target is empty\nDetail: %c.target() %cmethod must be filled`,
                'color: blue',
                'color: red',
                'color: blue',
                'color: red'
            );
        } else {

            let jsonData = {};

            // Collect all form data
            try {

                this.formTarget.querySelectorAll('input[type="text"], input[type="email"], input[type="password"], input[type="date"], input[type="file"], input[type="month"], input[type="range"], input[type="tel"], input[type="week"], input[type="text"], input[type="search"], input[type="number"], input[type="url"], input[type="hidden"], input[type="checkbox"]:checked, input[type="radio"]:checked, input[type="checkbox"][checked], input[type="radio"][checked], textarea, select').forEach((elem) => {

                    let formType = elem.getAttribute('type');

                    // Set form type by attribute format
                    switch (elem.getAttribute('format')) {

                        case 'currency':

                            formType = 'number';
                            break;
                    }

                    let value = {
                        value: '',
                        type: formType,
                        dom: elem
                    };

                    if (['file'].indexOf(formType) >= 0) {

                        value['value'] = elem.files.length >= 1 ? elem.files : null;
                    } else if (['number'].indexOf(formType) >= 0) {

                        value['value'] = elem.value.strReplace(['.', ','], '') || null;
                    } else {
                        value['value'] = elem.value || null;
                    }

                    if (['checkbox'].indexOf(formType) >= 0) {

                        if (typeof jsonData[elem.getAttribute('name')] === 'undefined') {
                            jsonData[elem.getAttribute('name')] = value;
                        }

                        if (!Array.isArray(jsonData[elem.getAttribute('name')]['value'])) {

                            jsonData[elem.getAttribute('name')] = Object.assign({}, value);
                            jsonData[elem.getAttribute('name')]['value'] = [];
                        }

                        jsonData[elem.getAttribute('name')]['value'].push(value['value']);
                    } else {

                        jsonData[elem.getAttribute('name')] = value;
                    }

                });
            } finally {

                // Check required data
                if (this.requiredData != null) {

                    try {

                        this.requiredData.forEach((value) => {

                            if (typeof jsonData[value['name']] !== 'undefined') {

                                // Type
                                if (typeof value['type'] !== 'undefined') {

                                    if (jsonData[value['name']]['type'] != value['type']
                                        || [undefined, null, ''].indexOf(jsonData[value['name']]['value']) >= 0
                                    ) {

                                        throw {
                                            code: 'REQUIRED_FORM_IS_EMPTY',
                                            form: jsonData[value['name']]
                                        };
                                    }
                                } else {

                                    if (typeof jsonData[value['name']]['value'] === 'object'
                                        && (jsonData[value['name']]['value'] instanceof Object || jsonData[value['name']]['value'] instanceof FileList)) {

                                        if (jsonData[value['name']]['value'].length <= 0) {

                                            throw {
                                                code: 'REQUIRED_FORM_IS_EMPTY',
                                                form: jsonData[value['name']]
                                            };
                                        }
                                    } else {

                                        if ([undefined, null, ''].indexOf(jsonData[value['name']]['value']) >= 0) {

                                            throw {
                                                code: 'REQUIRED_FORM_IS_EMPTY',
                                                form: jsonData[value['name']]
                                            };
                                        }
                                    }
                                }

                                // Return value
                                if (typeof value['return'] !== 'undefined') {

                                    // Encode value
                                    if (typeof value['return']['encode'] !== 'undefined') {

                                        switch (value['return']['encode']) {

                                            case 'base64':

                                                jsonData[value['name']]['value'] = btoa(jsonData[value['name']]['value']);
                                                break;
                                        }
                                    }
                                }
                            } else {

                                throw {
                                    code: 'FORM_NOT_FOUND',
                                    form: value
                                };
                            }
                        });
                    } catch (exp) {

                        if (typeof exp['code'] !== 'undefined') {

                            switch (exp['code']) {

                                case 'FORM_NOT_FOUND':

                                    console.error(
                                        `error from class %c${this.constructor.name}() %c\nMessage: Required form not found\nDetail: \nform-type: %c${exp.form['type']} "%c\nform-name: %c${exp.form['name']}`,
                                        'color: blue',
                                        'color: red',
                                        'color: blue',
                                        'color: red',
                                        'color: blue',
                                    );
                                    break;

                                case 'REQUIRED_FORM_IS_EMPTY':

                                    if (typeof $cbError === 'function') {

                                        $cbError(exp);
                                    } else {

                                        console.error(
                                            `error from class %c${this.constructor.name}() %c\nMessage: Required form is empty\nDetail: \nform-type: %c${exp.form['type']} %c\nform-name: %c${exp.form['name']}`,
                                            'color: blue',
                                            'color: red',
                                            'color: blue',
                                            'color: red',
                                            'color: blue',
                                        );
                                    }
                                    break;
                            }
                        }

                        this.init();
                        return this;
                    }
                }

                // Convert to json
                let json = {};

                Object.keys(jsonData).forEach((key) => {

                    // Remove null value
                    if ([undefined, null, ''].indexOf(jsonData[key]['value']) < 0) {

                        json[key] = jsonData[key]['value'];
                    }
                });

                if (typeof $cbSuccess === 'function') $cbSuccess(json);
                this.init();
                return this;
            }
        }
    }
}

class _compileURL {

    // Class to compile url snippets
    href = null; // Full url
    path = null; // Url path
    hash = null; // Url hash
    query = null; // Url query string

    constructor($origin = null) {

        this.origin = $origin;
    }

    // Check and set origin
    setBaseUrl() {

        if (this.origin != null) {

            if (this.origin[this.origin.length - 1] != '/') this.origin += '/';
        } else {

            this.origin = null;
        }

        return this;
    }

    // Set value to default
    init() {

        this.href = this.origin;
        this.path = null;
        this.hash = null;
        this.query = null;
        return this;
    }

    addPath($path = '') {

        // Method to fill in truncated url path to whole url
        if ($path[0] == '/') $path = $path.substring(1);
        this.path = $path;

        // Set default full url value
        this.setBaseUrl();

        let spHash = this.href.includes('#') ? this.href.split('#') : null,
            spQuery = this.href.includes('?') ? this.href.split('?') : null;

        // Insert url path
        if (this.path != null) {

            this.href = this.origin + this.path;

            if (spQuery != null) {

                if (spQuery[1].includes('#')) {

                    spQuery = spQuery[1].split('#');
                    !this.href.includes('?')
                        ? this.href += '?' + spQuery[0]
                        : this.href += '&' + spQuery[0];
                } else {

                    !this.href.includes('?')
                        ? this.href += '?' + spQuery[1]
                        : this.href += '&' + spQuery[1];
                }
            }

            if (spHash != null) this.href += '#' + spHash[1];
        }

        return this;
    }

    addQuery($query = {}) {

        // Method to populate query string to full url
        this.query = '';
        Object.keys($query).forEach((jsonKey) => {

            this.query += this.query == ''
                ? `${jsonKey}=${$query[jsonKey]}`
                : `&${jsonKey}=${$query[jsonKey]}`;
        });


        // Set default full url value
        this.setBaseUrl();

        let spHash = this.href.includes('#') ? this.href.split('#') : null;

        // Insert query string
        if (this.query != null) {

            this.href = this.origin;

            if (this.path != null) this.href += this.path;

            !this.href.includes('?')
                ? this.href += '?' + this.query
                : this.href += '&' + this.query;

            if (spHash != null) this.href += '#' + spHash[1];
        }

        return this;
    }

    addHash($hash = '') {

        // Method to fill in truncated url hash to whole url
        if ($hash[0] == '#') $hash = $hash.substring(1);
        this.hash = '#' + $hash;

        // Place hash at the end of url
        this.href += this.hash;
        return this;
    }
}


/*
    **********
    # Dexa dot function
    **********
*/

// Last item of array
Array.prototype.lsItem = function () {

    return this[this.length - 1];
};

// Remove item from array
Array.prototype.removeItem = function (valueItem = [] | null) {

    if (['', null, []].indexOf(valueItem) < 0) {

        if (Array.isArray(valueItem)) {

            valueItem.forEach((value) => {

                let index = this.indexOf(value);
                if (index >= 0) this.splice(index, 1);
            });
        } else {

            let index = this.indexOf(valueItem);
            if (index >= 0) this.splice(index, 1);
        }
    }

    return this;
};

// Replace character with another charater
String.prototype.strReplace = function (target = [] | null, to = null) {

    let str;

    if (!this instanceof String && !typeof this === 'string') str = this.toString();
    else str = this;

    try {

        if ((['', null].indexOf(target) >= 0 && to == null)
            || ([[]].indexOf(target) >= 0)
        ) throw {};

        if (Array.isArray(target)) {

            target.forEach((value) => {

                str = str.replaceAll(value, to);
            });
        } else {

            str = str.replaceAll(target, to);
        }

        return str;
    } catch (exp) {

        return console.error('Expected 2 Arguments');
    }
};

// Value of some array item
String.prototype.ucFirst = function () {

    return this[0].toUpperCase() + this.slice(1);
};

// Modify value of query parameter from URL
String.prototype.query = function (keys = {} | null, value = null) {

    try {

        if ((['', null].indexOf(keys) >= 0 && value == null)
            || ([{}].indexOf(keys) >= 0)
        ) throw {};

        const url = new URL(this);
        let href = url.href;

        if (!href.includes('?')) throw {};

        let query = url.href.split('?')[1].split('&'),
            queryString = null;

        query.forEach((lpVal) => {

            let split = lpVal.split('='),
                spVal = null;

            for (let i = 1; i < split.length; i++) {

                spVal == null
                    ? spVal = split[i]
                    : spVal += '=' + split[i];
            }

            if (typeof keys === 'object' && !Array.isArray(keys)) {

                if (isset(keys[split[0]])) {

                    queryString == null
                        ? queryString = split[0] + '=' + keys[split[0]]
                        : queryString += '&' + split[0] + '=' + keys[split[0]];
                } else {

                    queryString == null
                        ? queryString = split[0] + '=' + spVal
                        : queryString += '&' + split[0] + '=' + spVal;
                }
            } else {

                if (split[0] == keys) {

                    queryString == null
                        ? queryString = split[0] + '=' + value
                        : queryString += '&' + split[0] + '=' + value;
                } else {

                    queryString == null
                        ? queryString = split[0] + '=' + spVal
                        : queryString += '&' + split[0] + '=' + spVal;
                }
            }
        });

        return url.origin + url.pathname + '?' + queryString;
    } catch (exp) {

        return console.error('Invalid URL');
    }

};

// Get value of query parameter from URL
String.prototype.getQuery = function (key) {

    try {

        const url = new URL(this);
        return url.searchParams.get(key);
    } catch (exp) {

        return console.error('Invalid URL');
    }
};

// Get value of query parameter from URL
String.prototype.removeQuery = function (key) {

    let url = this.trim();
    urlParts = url.split('?');

    if (urlParts.length < 1) return null;

    let queryParts = urlParts[1].split('&'),
        finalQuery = null;

    queryParts.forEach((value) => {

        let split = value.split('='),
            spVal = null;

        if (split[0] != key) {

            for (let i = 1; i < split.length; i++) {

                spVal == null
                    ? spVal = split[i]
                    : spVal += '=' + split[i];
            }

            finalQuery == null
                ? finalQuery = split[0] + '=' + spVal
                : finalQuery += '&' + split[0] + '=' + spVal;
        }
    });

    return urlParts[0] + '?' + finalQuery;
};

// Client bounding status
HTMLElement.prototype.boundingStatus = function (
    offsetX = 20,
    offsetY = 40
) {

    let elemObj = {};

    elemObj['height'] = this.offsetHeight;
    elemObj['width'] = this.offsetWidth;

    let boundingObj = {};
    boundingObj['y'] = this.getBoundingClientRect().y;
    boundingObj['x'] = this.getBoundingClientRect().x;

    let status = {
        vertical: false,
        horizontal: false
    };

    // Vertical
    if (boundingObj['y'] - window.innerHeight + offsetY <= 0) {

        status['vertical'] = true;
    }

    // Vertical
    if (boundingObj['x'] - window.innerWidth + offsetX <= 0) {

        status['horizontal'] = true;
    }

    return status;
};

// Animate value
HTMLElement.prototype.animateValue = function (start, end, duration) {

    if (start === end) return;

    let range = end - start;
    let current = start;
    let increment = end > start ? 1 : -1;
    let stepTime = Math.abs(Math.floor(duration / range));

    let timer = setInterval((elem) => {

        current += increment;

        elem.innerHTML = current;

        if (current == end) {

            clearInterval(timer);
        }
    }, stepTime, this);
};



/*
    **********
    # Dexa function
    **********
*/

// Check is variable or object avaialable or not
const isset = function (variable = null) {

    if (variable === undefined
        || variable === null)
        return false;

    return typeof variable !== 'undefined';
};

// Rupiah
const rupiah = function (value = 0) {

    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
};

// Convert json key - value to form data
const jsonToFormData = function (json = null, option = {}) {

    const encodeValue = function (encode, value) {
        switch (encode) {
            case 'base64':
                return btoa(value);
        }
    };

    if (json == null) return null;

    let formData = new FormData();

    Object.keys(json).forEach((key) => {

        let value = json[key];

        if (!(value instanceof FileList)) {

            // value no file

            // Options
            if ([undefined, null, '', {}, []].indexOf(option) < 0) {

                // Encode value
                if (isset(option['encode'])) {

                    if (isset(option['keyToEncode'])) {

                        if (option['keyToEncode'].indexOf(key) >= 0
                            && typeof option['encode'] !== 'undefined'
                        ) {

                            value = encodeValue(option['encode'], value);
                        }
                    } else {

                        if (typeof option['encode'] !== 'undefined') {

                            value = encodeValue(option['encode'], value);
                        }
                    }
                }
            }
        } else {

            value = value[0];
        }

        if (Array.isArray(value)) {

            Array.from(value).forEach((flVal, flKey) => {

                formData.append(`${key}[]`, flVal);
            });
        } else {
            formData.append(key, value);
        }

    });

    return formData;
};

// Read or save cookies
const jsCookie = {
    save: function (
        args = {
            name: null,
            value: null,
            exp_time: null,
            path: null,
            domain: null,
        }
    ) {

        // Fill empty arguments
        if (!isset(args['name'])) args['name'] = null;
        if (!isset(args['value'])) args['value'] = null;
        if (!isset(args['exp_time'])) args['exp_time'] = null;
        if (!isset(args['path'])) args['path'] = null;
        if (!isset(args['domain'])) args['domain'] = null;

        let strCookie = '';

        if (
            args['name'] != null
            && args['value'] != null
        ) {

            strCookie = `${args['name']}=${args['value']};`;

            if (args['path'] != null) strCookie += `path=${args['path']};`;
            if (args['domain'] != null) strCookie += `domain=${args['domain']};`;
            if (args['exp_time'] != null) strCookie += `expires=${args['exp_time']};`;

            document.cookie = strCookie;
        }
    },
    get: function (name = null) {

        if (name == null) return null;

        let exp = document.cookie.strReplace('; ', ';').split(';'),
            resValue = null;

        exp.forEach((lpVal) => {

            let split = lpVal.split('=');

            if (split.length >= 1) {

                if (split[0] == name) {

                    for (let i = 1; i < split.length; i++) {

                        resValue == null
                            ? resValue = split[i]
                            : resValue += '=' + split[i];
                    }
                }
            }
        });

        return resValue;
    }
};


// Copy to clipboard
const copyToClipboard = (value) => {

    let tempInput = document.createElement("input");

    tempInput.value = value;
    document.body.appendChild(tempInput);

    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);

    if (document.execCommand("copy")) {

        (new _notifConsole).success('Disalin ke clipboard', 1500);
    }
};