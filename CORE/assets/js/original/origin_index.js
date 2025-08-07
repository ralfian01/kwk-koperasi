if (baseUrl === undefined)
    var baseUrl = 'http://localhost:6060/';

// Constanta
$.const = {};
$.const.baseUrl = baseUrl;
// $.const.cdnUrl = $.const.baseUrl.replace('://', '://cdn.');
// $.const.apiUrl = $.const.baseUrl.replace('://', '://api.');
$.const.cdnUrl = $.const.baseUrl + 'pts_cdn/';
$.const.apiUrl = $.const.baseUrl + 'pts_api/';
$.const.memberUrl = $.const.baseUrl + 'member/';
$.const.accountUrl = $.const.baseUrl.replace('://', '://accounts.');
$.const.basic = 'Basic ZGFwdXJmaXJfdXNlcjpkYXB1cmZpcl8xMjMxMjM=';
$.const.bearer = jsCookie.get('token');
if ([undefined, null, ''].indexOf($.const.bearer) >= 0) {

    $.const.bearer = null;
} else {

    $.const.bearer = 'Bearer ' + $.const.bearer;
}

$.const.authorization = $.const.bearer != null ? $.const.bearer : $.const.basic;


// Functions
// ## Collect form data
$.formCollect = new _formCollect();

// ## Notification
$.notif = () => {
    let callClass = new _notifConsole();

    // ### Notification - success
    callClass.overrideNotifContext('success', {
        colorTheme: 'var(--colorGreen)'
    });

    // ### Notification - error
    callClass.overrideNotifContext('error', {
        colorTheme: 'var(--colorRed)'
    });

    // ### Notification - warning
    callClass.overrideNotifContext('warning', {
        colorTheme: 'var(--colorOrange)'
    });

    // ### Notification - info
    callClass.overrideNotifContext('info', {
        colorTheme: 'var(--colorBlue)'
    });

    return callClass;
};


// ## Modal box
$.modalBox = new _modalBox({
    overrideModal: {
        modalParent: '<div class="fw wd1"></div>',
        modalBody: '<div class="content_container block rad_hf1 tx_ct p2" style="width: 350px; background: var(--colorWhite);"></div>',
        modalTitle: '<div class="tx_bg1 tx_w_bolder tx_al_ct"></div>',
        modalDescription: '<div class="mt2 tx_al_ct"></div>',
        modalButton: {
            confirm: '<button class="button1"></button>',
            cancel: '<button class="button1 semi_color" style="--bt_bg: var(--colorRed); --bt_color: var(--colorRed); --bt_border_color: var(--colorRed);"></button>',
            alternative: '<button class="button1" style="--bt_bg: var(--colorDarkGrey) --bt_color: var(--colorDarkGrey); --bt_border_color: var(--colorDarkGrey);"></button>'
        }
    },
    closeAnimation: 'rem'
});

// ## Compile URL
$.makeURL = {
    base: function () {

        const base = new _compileURL($.const.baseUrl);
        base.init();
        return base;
    },
    api: function () {

        const base = new _compileURL($.const.apiUrl);
        base.init();
        return base;
    },
    account: function () {

        const base = new _compileURL($.const.accountUrl);
        base.init();
        return base;
    },
    member: function () {

        const base = new _compileURL($.const.memberUrl);
        base.init();
        return base;
    },
    cdn: function () {

        const base = new _compileURL($.const.cdnUrl);
        base.init();
        return base;
    }
};

$.token = { // Update API Token
    update: function (value = null) {

        // Update token
        let baseUrl = $.const.baseUrl.strReplace(['https', 'http', '://', ':8083'], '');
        baseUrl = baseUrl.substr(0, baseUrl.length - 1);

        jsCookie.save({
            name: 'token',
            value: value,
            domain: '.' + baseUrl
        });

        let accountURL = $.const.accountUrl.strReplace(['https', 'http', '://', ':8083'], '');
        accountURL = accountURL.substr(0, accountURL.length - 1);

        jsCookie.save({
            name: 'token',
            value: value,
            domain: accountURL
        });

        // Get token
        $.const.bearer = jsCookie.get('token');
    }
};

// Ajax setup
$.ajaxSetup({
    headers: {
        'Authorization': $.const.authorization
    },
    crossDomain: true,
    processData: false,
    contentType: false
});


/*
    ***********
    jQuery Method
    ***********
*/
(function ($) {

    // Set attribute to multiple elements at once
    $.fn.attrs = function (attribute, value = '') {

        if (typeof attribute == 'object') {

            $.each(this, function () {

                $(this).attr(attribute);
            });
        } else {

            $.each(this, function () {

                $(this).attr(attribute, value);
            });
        }
    };

    // Remove attribute from multiple elements at once
    $.fn.removeAttrs = function (attribute) {

        $.each(this, function () {

            $(this).removeAttr(attribute);
        });
    };

    // Get element class list
    $.fn.classList = function () {

        return this[0].classList;
    };

    // Get element class exist
    $.fn.classExists = function (attribute) {

        let returnValue = $.inArray(attribute, this[0].classList);

        return returnValue >= 0 ? true : false;
    };

    // Remove Element with fade
    $.fn.removeFading = function (duration = 500) {

        $.each(this, function () {

            this.style.transition = duration + 'ms';
            this.style.opacity = '0';

            setTimeout((elem) => {

                elem.remove();
            }, (duration + 1), this);

        });
    };

    // Button on loading mode
    $.fn.buttonOnLoading = function (activate = false, loadingElement = null) {

        loadingElement ??= `
            <div class="loader_animation anim_spin flex y_center x_center mr0c5" style="width: 15px; height: 15px;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="inherit" style="width: 100%; height: 100%;">
                    <path stroke="inherit" stroke-width="2" d="M18.364 5.63604L16.9497 7.05025C15.683 5.7835 13.933 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19C15.866 19 19 15.866 19 12H21C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C14.4853 3 16.7353 4.00736 18.364 5.63604Z">
                    </path>
                </svg>
            </div>
        `;

        $(this).html(`
            ${loadingElement}
            ${$(this).text().trim()}
        `);

        if (activate) {
            $(this).attr('disabled', '');
        } else {
            $(this).removeAttr('disabled')
                .find('.loader_animation').remove().end();
        }
    };
})(jQuery);


/*
    ***********
    jQuery Events
    ***********
*/

// ## Collapse box
$('body').on('click', '.collapse1 .clp_init', function () {

    let parent = $(this).parents('.collapse1')[0];

    // Expand with animation
    if (Array.from(parent.classList).indexOf('clp_anim') >= 0) {

        let clpContainer = $(parent).find('.clp_container')[0];
        let cntMxHeight = clpContainer.scrollHeight;

        $(clpContainer).css('--clp_cntr_mx_height', cntMxHeight + 'px');
    }

    $(parent).toggleClass('expand');
}).end();

// ### Foldable box
$('body').on('click', '.fold_box > .initial', function () {

    $($(this).parents('.fold_box')[0]).toggleClass('expand');
});


// ### Image loading lazy
$('*[loading="lazy"]').on('load', function () {

    $(this).removeAttr('loading');
});

// ### Force input caret to end of value
$('body').on('focusin', 'input[forcetoend="true"]', function () {

    setTimeout(() => {

        this.selectionStart = this.selectionEnd = this.value.length;
    }, 1);
});


// ### Quantity control
HTMLElement.prototype.qtyControls = function () {

    let jsonFunc = {};

    // Prepare quantity controller
    jsonFunc.prep = () => {

        let inpTarget = $(this).find('input'),
            min = parseInt($(inpTarget).attr('min')),
            max = parseInt($(inpTarget).attr('max')),
            value = parseInt($(inpTarget).val());

        if (isNaN(value)) value = min;

        if (value >= max) $(this).find('button#inc').attr('disabled', '');
        else $(this).find('button#inc').removeAttr('disabled');

        if (value <= min) $(this).find('button#dec').attr('disabled', '');
        else $(this).find('button#dec').removeAttr('disabled');
    };

    // Control quantity with button
    jsonFunc.btnQty = () => {

        let elemPar = $(this).parents('*[class*="qty_field"]')[0],
            inpTarget = $(elemPar).find('input'),
            value = $(inpTarget).val(),
            buttonId = $(this).attr('id');

        if (buttonId == 'dec') value--;
        else if (buttonId == 'inc') value++;

        $(inpTarget).focus();
        $(inpTarget).val(value).blur().change();
    };

    // Control quantity with input
    jsonFunc.inputQty = () => {

        let elemPar = $(this).parents('*[class*="qty_field"]')[0],
            min = $(this).attr('min'),
            max = $(this).attr('max'),
            value = parseInt($(this).val());

        if ($.inArray(value, [undefined, null, '']) < 0
            && !isNaN(value)) {

            // Decrease button
            if (value > min) $(elemPar).find('button#dec').removeAttr('disabled');
            else $(elemPar).find('button#dec').attr('disabled', '');

            // Increase button
            if (value < max) $(elemPar).find('button#inc').removeAttr('disabled');
            else $(elemPar).find('button#inc').attr('disabled', '');
        }
    };

    // Default value
    jsonFunc.defaultVal = () => {

        let elemPar = $(this).parents('*[class*="qty_field"]')[0],
            min = $(this).attr('min'),
            max = $(this).attr('max'),
            value = parseInt($(this).val());

        if ($.inArray(value, [undefined, null, '']) < 0
            && !isNaN(value)) {

            if (value < min) $(this).val(min);
            else if (value > max) $(this).val(max);
        }
    };

    return jsonFunc;
};
$('body').on('click', '*[class*="qty_field"] button#dec, *[class*="qty_field"] button#inc', function () {

    this.qtyControls().btnQty();
}).on('input change', 'input', function () {

    this.qtyControls().inputQty();
}).on('focusin focusout', 'input', function () {

    this.qtyControls().defaultVal();
}).find('*[class*="qty_field"]').each(function () {

    this.qtyControls().prep();
}).end();


// ### Switches
HTMLElement.prototype.switches = function () {

    jsonFunc = {};

    // Prepare switches
    jsonFunc.prep = () => {

        let obey = this.getAttribute('obey-for') || null;
        let status = this.getAttribute('switch') == 'true' ? true : false || false;

        if (obey != null) {

            // This switch must obey other switch
            let obeyTarget = $('body').find('input[type="checkbox"][id="' + obey + '"]')[0];

            $(obeyTarget).prop('checked')
                ? $(this).removeAttr('disabled')
                : $(this).attr('disabled', '');
        }

        $(this).find('input[type="checkbox"]')
            .prop('checked', status).change();
    };

    // Switch switches
    jsonFunc.switch = () => {

        let label = $(this).parents('label[type="switch"]');
        let mandatoryId = $(this).attr('mandatory-id') || null;

        $(this).prop('checked')
            ? $(label).attr('switch', 'true')
            : $(label).attr('switch', 'false');

        if (mandatoryId != null) {

            if ($(this).prop('checked')) {

                $('body')
                    .find(`label[type="switch"][obey-for="${mandatoryId}"]`).removeAttr('disabled')
                    .find(`input[type="checkbox"]`).removeAttr('disabled');
            } else {

                $('body')
                    .find(`label[type="switch"][obey-for="${mandatoryId}"]`).attr('disabled', '')
                    .find(`input[type="checkbox"]`).attr('disabled', '');
            }
        }
    };

    return jsonFunc;
};
$('body').on('change', 'input[type="checkbox"]', function (elem) {

    this.switches().switch();
}).find('label[type="switch"]').each(function () {

    this.switches().prep();
});


// ### Label checkbox
HTMLElement.prototype.checkboxes = function () {

    let jsonFunc = {};

    // Prepare checkboxes
    jsonFunc.prep = () => {

        let status = ([undefined, null].indexOf(this.getAttribute('checked')) < 0) ? true : false;

        // Checkbox status
        if (status) {

            $(this)
                .find('input[type="checkbox"]')
                .prop('checked', true)
                .change();
        } else {

            $(this).find('input[type="checkbox"]')
                .prop('checked', false)
                .change();
        }
    };

    // When label checkbox clicked
    jsonFunc.cxLabel = () => {

        // Label checkbox single
        let checkbox = $(this).find('input[type="checkbox"]');

        $('body')
            .find(`input[type="checkbox"][name="${$(checkbox).attr('name')}"][id!="${$(checkbox).attr('id')}"]`)
            .prop('checked', false)
            .change();
    };

    // When input checkbox changed
    jsonFunc.cxInput = () => {

        let label = $(this).parents('label[class*="checkbox"]');

        $(this).prop('checked')
            ? $(label).attr('checked', '')
            : $(label).removeAttr('checked');
    };

    return jsonFunc;
};
$('body').on('click', 'label[class*="checkbox"][multi=false]', function (evt) {

    this.checkboxes().cxLabel();
}).on('change', 'input[class*="checkbox"]', function (evt) {

    this.checkboxes().cxInput();
}).find('label[class*="checkbox"] input').each(function () {

    this.checkboxes().prep();
});


// ### Resizable textarea
HTMLElement.prototype.textareas = function () {

    let jsonFunc = {};

    // Resize textarea
    jsonFunc.resize = () => {

        let minRow = this.getAttribute('min-row') || 1;

        const txareaHelper = document.createElement('textarea');
        txareaHelper.id = 'tx_resizer';
        txareaHelper.style.overflow = 'scroll';
        txareaHelper.style.zIndex = '-1';
        txareaHelper.style.boxSizing = 'border-box';
        txareaHelper.style.padding = $(this).css('padding');
        txareaHelper.style.lineHeight = parseInt($(this).css('line-height'), 10) - 2;
        txareaHelper.style.width = this.offsetWidth + 'px';
        txareaHelper.style.position = 'fixed';
        txareaHelper.value = this.value;

        document.body.appendChild(txareaHelper);

        txareaHelper.setAttribute('rows', minRow);
        lh = parseInt($(txareaHelper).css('line-height'), 10),
            rows = Math.floor(txareaHelper.scrollHeight / lh);

        txareaHelper.setAttribute('rows', rows);
        txareaHelper.remove();

        this.setAttribute('rows', rows);
    };

    return jsonFunc;
};
$('body').on('keyup change focusin', 'textarea[ptx_resizable]', function (event) {

    this.textareas().resize();
}).find('textarea[ptx_resizable]').each(function () {

    this.textareas().resize();
});


// ### Textarea chars counter
HTMLElement.prototype.countChars = function () {

    let valLength = this.value.length,
        maxLength = this.getAttribute('maxlength');

    let parent = $(this).parents('.input_item');

    if ($(parent).find('.counter').length <= 0)
        $(parent).append(`<div class="counter">${valLength}/${maxLength}</div>`);

    $(parent).find('.counter').html(`${valLength}/${maxLength}`);
};
$('body').on('input', '*[class*="tx_field"] textarea[maxlength]', function () {

    this.countChars();
}).find('*[class*="tx_field"] textarea[maxlength]').each(function () {

    this.countChars();
});


// ### Input currency
HTMLElement.prototype.inputCurrency = function () {

    let jsonFunc = {};

    // Prepare input currency
    jsonFunc.prep = () => {

        this.setAttribute('pattern', '[0-9]*');

        // Placeholder
        if ([undefined, null, ''].indexOf(this.getAttribute('placeholder')) < 0) {

            let placeholder = this.getAttribute('placeholder');
            placeholder = parseInt(placeholder.replaceAll('.', ''));

            this.setAttribute('placeholder', rupiah(placeholder));
        }

        // Value
        if ([undefined, null, ''].indexOf(this.getAttribute('value')) < 0) {

            let value = this.getAttribute('value');
            value = parseInt(value.replaceAll('.', ''));

            this.setAttribute('value', rupiah(value));
        }
    };

    // Format input number to currency
    jsonFunc.format = () => {

        // Only allow number 0-9
        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\.*?)\.*/g, '$1');

        let value = this.value.replaceAll('.', '');
        value = rupiah(value);

        this.value = value;
    };

    return jsonFunc;
};
$('body').on('change input', 'input[ptx_format="currency"]', function () {

    this.inputCurrency().format();
}).find('input[ptx_format="currency"]').each(function () {

    this.inputCurrency().prep();
});

// ### Input number maxlength
$('body').on('keypress', 'input[type="number"][maxlength]', function (evt) {

    let maxLength = $(this).attr('maxlength'),
        value = $(this).val();

    if ($.inArray(maxLength, [undefined, null, '']) < 0) {

        if (evt.keyCode != 8
            && value.length >= maxLength) {

            return false;
        }
    }
});

// ### On scroll animation
$('body').on('scroll', function (evt) {

    $(this).find('.anim_onscroll').each(function () {

        if (this.boundingStatus(20, -30)['vertical']) {

            let classList = $(this).classList();

            $.each(classList, (key, val) => {

                if (val.includes('anim')
                    && val != 'anim_onscroll') {

                    $(this)
                        .removeClass(val)
                        .removeClass('anim_onscroll');

                    setTimeout(() => {

                        $(this).addClass(val);
                    }, 1);
                    return false;
                }
            });
        }
    });
});


// ## File upload
let drgCount = 0;
let tempFileList = [];

$('body')
    .on('dragenter', '*[class*="tx_field"] .input_file_item', function () {

        $(this)
            .attr('ptx_ondrag', '')
            .find('input[type="file"]')
            .css({
                zIndex: 2
            });

        drgCount++;
    })
    .on('drop dragleave', '.input_file_item', function () {

        drgCount--;

        if (drgCount === 0) {

            $(this)
                .removeAttr('ptx_ondrag')
                .find('input[type="file"]')
                .css({
                    zIndex: 0
                });
        }
    })
    .on('change', '.input_file_item input[type="file"]', function (elem) {

        let inputParent = $(this).parents('.input_file_item')[0],
            previewParent = $(inputParent).find('.file_preview');

        // ### Modify input[type="file"] FileList
        const modFileList = () => {

            // Combine temporary FileList and active input[type="file"] FileList
            if (elem.target.files.length >= 0) {

                if (tempFileList.length >= 1) {

                    let newList = new DataTransfer(),
                        inputedJson = [];

                    // Input FileList from input[type="file"] to new List
                    Array.from(elem.target.files).forEach((data) => {

                        inputedJson.push(JSON.stringify({
                            modified: data['lastModified'],
                            size: data['size'],
                            name: data['name']
                        }));

                        newList.items.add(data);
                    });

                    // Input FileList from temporary FileList to new List
                    Array.from(tempFileList).forEach((data) => {

                        let jsonString = JSON.stringify({
                            modified: data['lastModified'],
                            size: data['size'],
                            name: data['name']
                        });

                        // Only input data that does not exist
                        if (inputedJson.indexOf(jsonString) < 0) {

                            newList.items.add(data);
                        }
                    });

                    return elem.target.files = newList.files;
                }
            }

            if (tempFileList.length >= 1)
                return elem.target.files = tempFileList;
        };

        // If multiple file is allowed
        if ($(this).attr('multiple'))
            modFileList();


        // ### Show selected file with HTML element
        const showElem = () => {

            let appendElement = '';
            $.each(elem.target.files, (key, data) => {

                let url = URL.createObjectURL(data);

                appendElement += `
                    <div class="preview_list">
                        <div class="file_info">
                            <div class="thumb">
                                <img src="${url}">
                            </div>

                            <div class="title">
                                <span>
                                    ${data['name'].slice(0, -5)}
                                </span>
                                ${data['name'].slice(-5)}
                            </div>
                        </div>

                        <div class="action">
                            <button class="remove" data-id="${key}">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </div>
                `;
            });

            $(previewParent).html(appendElement);
        };

        showElem();

        tempFileList = elem.target.files;
    })
    .on('click', '.input_file_item .file_preview .remove', function () {

        let inputParent = $(this).parents('.input_file_item')[0],
            listParent = $(this).parents('.preview_list'),
            inputFile = $(inputParent).find('input[type="file"]')[0],
            elem = this,
            id = $(this).attr('id');

        // Remove element
        $(listParent).remove();

        // ### Remove data from input[type="file"]
        // Duplicate array
        let fileArray = Array.from(inputFile.files);
        // Remove selected key
        fileArray.splice(id, 1);

        // Remake File object with FormData
        let listFile = new DataTransfer();
        fileArray.forEach((data) => {

            listFile.items.add(data);
        });

        // Override Object File in input[type="file"]
        inputFile.files = listFile.files;
        tempFileList = inputFile.files;
    }).end();