let base = 'http://192.168.64.2/montage/';

// the default error handler
let errorMessage = (message) => {
    console.log("Redirected here because " + message); 
}

// takes out all special chars except periods, and underscores and strips trailing whitespace
let stripSpecialChars = (input, spaceSeparator = " ") => {
    return input != null ? input.replace(/[^a-zA-Z._0-9 ]/g, '')
        .trim()
        .replace(/\s+/g, spaceSeparator) : ""; 
}

// gets desired params from the url
// query: string with the desired query to search for 
// required: whether or not to redirect as an error if the param isn't found 
let getParams = (query, required) => {
    let params = new URLSearchParams(window.location.search); 
    if (params.has(query)) return params.get(query); 
    if (required) { 
        console.log("Parameter not found."); 
        window.location.replace(base + 'error/?code=400'); 
    }
    return null; 
}

// uses submitPostRequest to validate the token passed
function validate(callback, token) {
    submitPostRequest('api/validate_token.php/?token=' + token, callback, 'validate', null, (status) => {
        if (status == 401) {
            // in the case that the token expired or couldn't be authenticated
            window.location.replace(base + '?redirect=' + 'token'); 
        } else { 
           //window.location.replace(base + 'error/?code=' + status); 
        }
    }); 
}

// a general function for submitting ajax post requests
let submitPostRequest = (apiPath, callback, type, form = null, errorHandler = errorMessage) => {
    let xhr = new XMLHttpRequest(); 
    let token = (getCookie('logged_in')) ? getCookie('token') : getParams('token', false);

    // collect and format the data 
    if (type == 'validate') {
        // to format the json web token
        var json = null; 
        if (token) {
            json = JSON.stringify({'jwt': token});
        } else {
            window.location.replace(base + '?redirect=' + 'token'); 
        }
    } else if (form != null) {
        // to submit a form request 
        let input = new FormData(form); 
        let object = {}; 
        input.forEach((value, key) => {object[key] = value});
        json = JSON.stringify(object);
    }
    // ensure that the json input has something in it
    if (json != null) {
        // configure xhr request 
        xhr.open('POST', base + apiPath, true);
        xhr.setRequestHeader('Content-Type', 'application/json'); 
        xhr.onreadystatechange = () => {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                if (xhr.status == 200) {
                    let res = JSON.parse(xhr.responseText); 
                    callback(res); 
                } else {
                    errorHandler(); 
                }
            }
        }
        // submit xhr request
        xhr.send(json); 
    } else {
        throw "Form data came back null"; 
    }
}

// a general function for submitting ajax get requests
let submitGetRequest = (apiPath, callback, param = "") => {
    let xhr = new XMLHttpRequest(); 
    if (param != "") param = stripSpecialChars(param, ""); 
    xhr.open('GET', base + apiPath + param, true); 
    xhr.onreadystatechange = () => {
        // if token is valid
        if (xhr.readyState == XMLHttpRequest.DONE && xhr.status == 200) {
            console.log(xhr.responseText); 
            let res = JSON.parse(xhr.responseText); 
            callback(res); 
        }
    };
    // submit xhr request
    xhr.send(); 
}

// general function for submitting ajax put requests 
let submitPutRequest = (form, apiPath, callback, errorHandler = errorMessage) => {
    let xhr = new XMLHttpRequest(); 
    let input = new FormData(form); 
    let object = {}; 
    input.forEach((value, key) => {object[key] = value});
    let json = JSON.stringify(object);

    // ensure that the json input has something in it
    if (json != null) {
        // configure xhr request 
        xhr.open('PUT', base + apiPath, true);
        xhr.setRequestHeader('Content-Type', 'application/json'); 
        xhr.onreadystatechange = () => {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                if (xhr.status == 200) {
                    console.log(xhr.responseText); 
                    callback(); 
                } else {
                    console.log(xhr.responseText); 
                    errorHandler(); 
                }
            }
        }
        // submit xhr request
        xhr.send(json); 
    } else {
        throw new Exception("Form data came back null"); 
    }
}

// gets the value of any stored cookie by name from local storage 
let getCookie = (name) => {
    name = name + "=";
    let cookieArray = document.cookie.split(';');
    if (cookieArray != "") {
        for (let i = 0; i <= cookieArray.length; i++) {
            let cookie = cookieArray[i]; 
            cookie = cookie.substring(1, cookie.length);
            if (cookie.indexOf(name) == 0) {
                return cookie.substring(name.length, cookie.length); 
            }
        }
    }
    return null; 
}

export { base, stripSpecialChars, getParams, validate, submitPostRequest, submitGetRequest, submitPutRequest, getCookie }; 