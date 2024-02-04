    /*
    -----
    ### Classes
*/

// API Console using fetch
class _apiConsole {

    constructor(host, auth) {
        this.host = host,
        this.headers = {
            'Authorization': auth
        },
        this.segment = '',
        this.code = '',
        this.method = 'GET',
        this.body = {}
    }

    // Method
    init(host, auth) {

        this.host = host;
        this.headers = {
            'Authorization': auth
        };
        this.segment = '';
        this.code = '';
        this.method = 'GET';
        this.body = {};
        return this;
    }

    beforeSend(cb) {

        if(typeof cb == 'function') cb();
        return this;
    }

    setup(obj = {}) {

        const empty = [undefined, null, ''];

        // Default value
        if(empty.indexOf(obj['segment']) >= 0) obj['segment'] = '';
        if(empty.indexOf(obj['code']) >= 0) obj['code'] = '';
        if(empty.indexOf(obj['method']) >= 0) obj['method'] = 'GET';
        if(empty.indexOf(obj['body']) >= 0) obj['body'] = {};

        this.segment = obj['segment'];
        this.code = obj['code'];
        this.method = obj['method'].toUpperCase();
        this.body = obj['body'];

        // Headers
        if(empty.indexOf(obj.headers) < 0) {

            if(Object.keys(obj.headers).length >= 1) {

                Object.keys(obj.headers).forEach((key) => {

                    this.headers[key] = obj.headers[key];
                });
            }
        }

        return this;
    }

    get(cb = {}) {

        this.method = 'GET';
        this.fire(cb);
        return this;
    }

    post(cb = {}) {

        this.method = 'POST';
        this.fire(cb);
        return this;
    }

    patch(cb = {}) {

        this.method = 'PATCH';
        this.fire(cb);
        return this;
    }

    delete(cb = {}) {

        this.method = 'DELETE';
        this.fire(cb);
        return this;
    }

    put(cb = {}) {

        this.method = 'PUT';
        this.fire(cb);
        return this;
    }

    file(cb = {}) {

        let formData = new FormData();

        if(Object.keys(this.body).length >= 1) {

            Object.keys(this.body).forEach((key) => {

                formData.append(key, this.body[key]);
            });
        }

        this.body = formData;
        this.method = 'POST';
        this.fire(cb);
        return;
    }

    formData(data = {}) {

        let formData = new FormData();

        if(Object.keys(data).length >= 1) {

            Object.keys(data).forEach((key) => {

                formData.append(key, data[key]);
            });
        }

        this.body = formData;
        return this;
    }

    fire(cb = {}) {

        let fetchOpt = {
            url: this.host + this.segment,
            option: {
                method: this.method,
                headers: this.headers,
                body: this.body
            }
        }

        if(['GET', 'PATCH'].indexOf(this.method) != -1) {

            if(this.code.length >= 1) fetchOpt['url'] += '/' + this.code;

            if(Object.keys(this.body).length >= 1) {

                let query = null;

                Object.keys(this.body).forEach((key) => {

                    if(query == null) {

                        query = key + '=' + this.body[key];
                    } else {

                        query += '&' + key + '=' + this.body[key];
                    }
                });

                fetchOpt['url'] += '?' + query;
            }

            delete fetchOpt['option']['body'];
        }

        Object.keys(this.headers).forEach((key) => {

            if(this.headers[key].indexOf('json') >= 0) {

                fetchOpt['option']['body'] = JSON.stringify(fetchOpt['option']['body'])
            }
        });

        // Call beforeSend function
        this.beforeSend();

        fetch(
            fetchOpt['url'],
            fetchOpt['option'],
        )
        // .then(response => consume(response))
        .then(response => response.json())
        .then(data => {

            if(data.code == 200) {

                if(typeof cb.success == 'function') {

                    cb.success(data);
                }
            } else {

                if(typeof cb.error == 'function') {

                    cb.error(data);
                }
            }

            return this.init(this.host, this.headers['Authorization']);
        })
        .catch(error => {

            if(typeof cb.error == 'function') {

                cb.error(error);
            }

            return this.init(this.host, this.headers['Authorization']);
        });
    }
}

// Redirect to url with post data
class _redirectPost {

    constructor() {
        this.postData = {}
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

        if(Object.keys(this.postData).length >= 1) {

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

        if(
            empty.indexOf(url) < 0
            && url.length >= 1
        ) {

            if(Object.keys(this.postData).length >= 1) {

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
class _boxConsole {


}

class _notifConsole {

    constructor() {
        this.option = {
            text: '',
            duration: 1000,
            background: 'rgb(70, 70, 70)',
            colorType: 'bold'
        }
    }

    // Method
    removeElem(elem) {

        elem.style.transform = 'translate3d(-50%, 100%, 1px) scale(1)';
        elem.style.opacity = '0';

        setTimeout((elem) => {
            
            elem.remove();
        }, 301, elem);
    }

    base() {

        const elem = document.createElement('div');

        elem.innerHTML = this.option.text;
        
        elem.style.cursor = 'default';
        elem.style.transition = '300ms';
        elem.style.width = 'fit-content';
        elem.style.textAlign = 'center';
        elem.style.maxWidth = '90vw';
        elem.style.borderRadius = '10px';
        elem.style.fontSize = '14px';
        elem.style.padding = '15px 20px';
        elem.style.boxSizing = 'border-box';
        elem.style.fontWeight = 'normal';
        elem.style.position = 'fixed';
        elem.style.bottom = '20px';
        elem.style.left = '50%';
        elem.style.transform = 'translate3d(-50%, 0, 1px) scale(0)';
        elem.style.zIndex = '999';
        elem.style.background = this.option.background;

        if(this.option.colorType == 'bold') {

            elem.style.color = '#fff';
            elem.style.border = '1px solid transparent';
        } else if (this.colorType == 'light') {

            elem.style.color = 'rgb(20, 20, 20)';
            elem.style.border = this.option.border;
        }

        document.getElementsByTagName('body')[0].appendChild(elem);

        setTimeout((elem) => {
            
            elem.style.transform = 'translate3d(-50%, 0, 1px) scale(1)';

            elem.addEventListener('click', () => {

                this.removeElem(elem);
            });

            setTimeout((elem) => {

                this.removeElem(elem);
            }, this.option.duration, elem);
        }, 100, elem);
    }

    default(text = '', duration = 1500, colorType = 'bold') {

        this.option.background = 'rgb(70, 70, 70)';
        this.option.border = '1px solid rgb(70, 70, 70)';

        if(colorType == 'light') this.option.background = 'rgb(200, 200, 200)';

        this.option.colorType = colorType;
        this.option.text = text;
        this.option.duration = duration;
        this.base();

        return this;
    }

    success(text = '', duration = 1500, colorType = 'bold') {
        
        this.option.background = 'rgba(111, 167, 77, 1)';
        this.option.border = '1px solid rgba(111, 167, 77, 1)';

        if(colorType == 'light') this.option.background = 'rgb(225, 255, 207)';

        this.option.colorType = colorType;
        this.option.text = text;
        this.option.duration = duration;
        this.base();

        return this;
    }

    info(text = '', duration = 1500, colorType = 'bold') {

        this.option.background = 'rgba(56, 167, 171, 1)';
        this.option.border = '1px solid rgba(56, 167, 171, 1)';

        if(colorType == 'light') this.option.background = 'rgb(184, 252, 255)';

        this.option.colorType = colorType;
        this.option.text = text;
        this.option.duration = duration;
        this.base();

        return this;
    }

    warning(text = '', duration = 1500, colorType = 'bold') {
        
        this.option.background = 'rgba(233, 132, 41, 1)';
        this.option.border = '1px solid rgba(233, 132, 41, 1)';

        if(colorType == 'light') this.option.background = 'rgb(255, 197, 145)';

        this.option.colorType = colorType;
        this.option.text = text;
        this.option.duration = duration;
        this.base();

        return this;
    }

    error(text = '', duration = 1500, colorType = 'bold') {
        
        this.option.background = 'rgba(233, 70, 89, 1)';
        this.option.border = '1px solid rgba(233, 70, 89, 1)';

        if(colorType == 'light') this.option.background = 'rgb(255, 140, 154)';

        this.option.colorType = colorType;
        this.option.text = text;
        this.option.duration = duration;
        this.base();

        return this;
    }
}

class _formCheck {
    
    constructor() {
        this.requiredData = [],
        this.formTarget = {}
    }

    // Method
    init() {

        this.requiredData = [];
        this.formTarget = {};
        return this;
    }

    target(form) {

        this.formTarget = form;
        return this;
    }

    required(data = []) {

        this.requiredData = data;
        return this;
    }

    check(cb = {}) {

        let returnData = {};

        // Check form
        this.requiredData.every((value, key) => {

            // Check form availabilty
            const form = this.formTarget.querySelectorAll(`${this.requiredData[key]['type']}[name="${this.requiredData[key]['name']}"]`);

            if(form.length >= 1) {

                // Check form value
                if([undefined, null, ''].indexOf(form[0].value) < 0) {

                    // When form is filled
                    if([undefined, null, ''].indexOf(this.requiredData[key]['format']) >= 0) {

                        this.requiredData[key]['format'] = 'text';
                    }

                    switch(this.requiredData[key]['format']) {

                        case 'text':

                            returnData[this.requiredData[key]['name']] = form[0].value;
                            break;

                        case 'file':

                            returnData[this.requiredData[key]['name']] = form[0].files;
                            break;
                    }

                    if(Object.keys(returnData).length >= this.requiredData.length) {

                        if(typeof cb.success == 'function') {

                            cb.success(returnData);
                        }

                        this.init()
                        return false;
                    }

                    return true;
                } else {

                    if(typeof cb.error == 'function') {

                        // When form is empty
                        cb.error({
                            status: 'EMPTY',
                            form: form[0]
                        });
                    }

                    this.init();
                    return false;
                }
            } else {

                if(typeof cb.error == 'function') {

                    // When form is empty
                    cb.error({
                        status: 'FORM_NOT_FOUND'
                    });
                }

                this.init();
                return false;
            }
        });
    }
}


/*
    -----
    ### Dot functions
*/


// Last item of array
Array.prototype.lastItem = function() {

    return this[this.length - 1];
}

// Value of some array item
Array.prototype.valueOfItem = function(item = 0) {

    return this[item];
}

// Value of some array item
String.prototype.firstLetterUppercase = function() {

    return this[0].toUpperCase() + this.slice(1);
}

// Pick utm code
String.prototype.pickUTM = function() {

    return this.substr(6).slice(0, -8);
}

// Get value of query parameter from URL
String.prototype.query = function(key) {

    try {

        const url = new URL(this);
        return url.searchParams.get(key);
    }
    catch {

        return console.error('Invalid URL');
    }

}

// Change value of query parameter
String.prototype.queryValue = function(key, value = '') {

    try {

        // Check url first
        new URL(this);

        const reg = new RegExp("([?&])" + key + "=.*?(&|$)", "i"),
        separator = this.indexOf('?') !== -1 ? "&" : "?";

        if (this.match(reg)) {

            return this.replace(reg, '$1' + key + "=" + value + '$2');
        } else {

            return this + separator + key + "=" + value;
        }
    }
    catch {

        return console.error('Invalid URL');
    }
}

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
}

// Animate value
HTMLElement.prototype.animateValue = function(start, end, duration) {

    if (start === end) return;

    let range = end - start;
    let current = start;
    let increment = end > start? 1 : -1;
    let stepTime = Math.abs(Math.floor(duration / range));

    let timer = setInterval((elem) => {

        current += increment;

        elem.innerHTML = current;

        if (current == end) {

            clearInterval(timer);
        }
    }, stepTime, this);
}

/*
    -----
    ### Functions
*/


// Rupiah
const rupiah = (value = 0) => {

    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}


// var baseurl = 'https://api.projectkps.my.id/';
var baseurl = 'http://api.localhost:8083/';
var authorization = 'Basic cHJvamVjdGtwczoxMjMxMjM=';

const dexaApp = {};

// App Functions
$.redirect = new _redirectPost();  // Redirect to url with post data
$.notif = new _notifConsole();  // Notification
$.formCheck = new _formCheck();  // Check form

dexaApp.date = function() { // Date

    $dtObj = new Date();
    return dtObj.getFullYear().toString() + (dtObj.getMonth() + 1).toString().padStart(2, '0') + dtObj.getDate().toString().padStart(2, '0') + dtObj.getHours().toString().padStart(2, '0') + dtObj.getMinutes().toString().padStart(2, '0');
}

dexaApp.fileToBase64 = function(file, load, error) {

    const reader = new FileReader();
    reader.readAsDataURL(file)

    if(typeof load == 'function') {

        reader.onload = () => load(reader.result);
    }

    if(typeof error == 'function') {

        reader.onerror = error => error(error);
    }
}

dexaApp.base64toBlob = function(dataURI) {
    
    var byteString = atob(dataURI.split(',')[1]);
    var arrayBuffer = new ArrayBuffer(byteString.length);
    var uint = new Uint8Array(arrayBuffer);
    
    for (let i = 0; i < byteString.length; i++) {

        uint[i] = byteString.charCodeAt(i);
    }

    return new Blob([arrayBuffer], { type: 'image/jpeg' });
}

// Constanta
$.const.bearer = '';


// Ajax
$.ajaxSetup({
    headers: {
        'Authorization': authorization
    },
    crossDomain: true,
    processData: false,
});
