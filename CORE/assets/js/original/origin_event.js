// Image loading lazy
$('*[loading="lazy"]').on('load', function() {

    $(this).removeAttr('loading');
});

// Switch
const _sw = {
    lb: (elem) => {

        let range = $(elem).find('input[type="range"][min="0"][max="1"]')[0];

        if($(range).length >= 1) {

            let rangeValue = $(range).val();

            if(rangeValue == 0) {

                $(range).val(1).change();
            } else if (rangeValue == 1) {

                $(range).val(0).change();
            }
        }
    },
    inpR: (elem) => {

        let labelSwitch = $(elem).parents('label[type="switch"]')[0];
        let value = elem.value;

        if($(labelSwitch).length >= 1) {

            if(value == 0) {

                $(labelSwitch).attr('switch', false);
            } else if (value == 1) {

                $(labelSwitch).attr('switch', true);
            }
        }
    }
}
$('body').on('click', 'label[type="switch"]', function(elem) {

    elem.preventDefault();
    _sw.lb(this);
}).on('change', 'input[type="range"][min="0"][max="1"]', function(elem) {

    elem.preventDefault();
    _sw.inpR(this);
});


// Label checkbox
const _cbx = {
    lb: (elem) => {

        let checkbox = $(elem).find('input[type="checkbox"]');

        if($(checkbox).prop('checked')) {

            $(checkbox).prop('checked', false).change();
        } else {

            $(checkbox).prop('checked', true).change();
        }
    },
    inpCx: (elem) => {

        let label = $(elem).parents('label[type="checkbox"]');

        if($(elem).prop('checked')) {

            $(label).attr('checked', '');
        } else {

            $(label).removeAttr('checked');
        }
    }
}
$('body').on('click', 'label[type="checkbox"]', function(evt) {

    evt.preventDefault();
    let checkbox = $(this).find('input[type="checkbox"]');

    if($(checkbox).prop('checked')) {

        $(checkbox).prop('checked', false).change();
    } else {

        $(checkbox).prop('checked', true).change();
    }
}).on('change', 'input[type="checkbox"]', function(evt) {

    evt.preventDefault();
    let label = $(this).parents('label[type="checkbox"]');

    if($(this).prop('checked')) {

        $(label).attr('checked', '');
    } else {

        $(label).removeAttr('checked');
    }
});


// Resizable textarea
const _resTx = (elem) => {

    elem.setAttribute('rows', 1);

    lh = parseInt($(elem).css('line-height'), 10),
    rows = Math.floor(elem.scrollHeight / lh);

    elem.setAttribute('rows', rows);
}
$('body').on('keyup change focusin', 'textarea[resizable="true"]', function(event) {

    _resTx(this);
});


// Onload content
HTMLHtmlElement.prototype.prepSwitch = function() {

    let status = this.getAttribute('status') || false,
    range = $(this).find('input[type="range"][min="0"][max="1"]')[0];

    // Check range
    if($(range).length >= 1) {

        if(status
            && range.value == 0) {
            
            $(range).val(1).change();
        } else if(!status
            && range.value == 1){

            $(range).val(0).change();
        }
    }
}
HTMLElement.prototype.prepCheckbox = function() {

    console.log(this.getAttribute('checked'));

    let status = ([undefined, null].indexOf(this.getAttribute('checked')) < 0) ? true : false;  

    // Checkbox status
    if(status) {

        $(this).find('input[type="checkbox"]').prop('checked', true);
    } else {

        $(this).find('input[type="checkbox"]').prop('checked', false);
    }
}

$(window).on('load', function() {

    $('body').find('label[type="switch"]').each(function() {

        // Find switches from label
        this.prepSwitch();
    });

    $('body').find('label[type="checkbox"]').each(function() {

        // Find switches from label
        this.prepCheckbox();
    });
    
    $('body').find('textarea[resizable="true"]').each(function() {

        // Resizable textarea
        _resTx(this);
    });
});


// // Element title
// let _htitle = {
//     timeout: setTimeout(() => { }, 1),
//     html: {}
// }

// $('*[htitle]').on('mouseenter', function(evt) {

//     clearTimeout(_htitle.timeout);

//     _htitle.timeout = setTimeout((elem) => {
        
//         _htitle.html = elem;

//         $('body').append(`
//             <div class="freefloat">
//                 ${elem.getAttribute('htitle')}
//             </div>
//         `)
//     }, 500, this);
// });