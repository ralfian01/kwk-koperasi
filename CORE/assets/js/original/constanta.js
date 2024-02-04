if (baseUrl === undefined) {
    var baseUrl = 'http://localhost:6060/';
}

// Constanta
$.const = {};
$.const.baseUrl = baseUrl;
$.const.cdnUrl = $.const.baseUrl + 'pts_cdn/';
$.const.apiUrl = $.const.baseUrl + 'pts_api/';
$.const.accountUrl = $.const.baseUrl.replace('://', '://accounts.');