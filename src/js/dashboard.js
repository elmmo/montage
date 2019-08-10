import { base, validate } from './util.js';

let loadProfile = (data) => {
    let xhr = new XMLHttpRequest(); 
    let user = data.user; 
    xhr.open('GET', base + 'api/profile.php/?user=' + user, true); 
    xhr.onreadystatechange = () => {
        // if token is valid
        if (xhr.readyState == XMLHttpRequest.DONE && xhr.status == 200) {
            console.log(); 
        }
    };
    // submit xhr request
    xhr.send(); 
}

validate(loadProfile); 