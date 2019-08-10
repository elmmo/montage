let base = 'http://192.168.64.2/montage/';

// the default error handler
let errorMessage = (message) => {
    console.log("Redirected here because " + message); 
}

// uses submitPostRequest to validate the token passed
function validate(callback) {
    submitPostRequest('api/validate_token.php', callback, 'validate', null, (status) => {
        if (status == 401) {
            // in the case that the token expired or couldn't be authenticated
            window.location.replace(base + '?redirect=' + 'token'); 
        } else { 
            window.location.replace(base + 'error/?code=' + status); 
        }
    }); 
}

// a general function for submitting ajax requests
function submitPostRequest(apiPath, callback, type, form = null, errorHandler = errorMessage) {
    let xhr = new XMLHttpRequest(); 
    let params = new URLSearchParams(window.location.search); 
    let token = params.has('token') ? params.get('token') : null; 

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
                    console.log(type); 
                    console.log(xhr.responseText); 
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
        throw new Exception("Form data came back null"); 
    }
}

export { base, validate, submitPostRequest }; 