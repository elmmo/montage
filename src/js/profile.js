//  if token is valid, go to profile, pulling information from the db 
    // if first time login, display first time message prompting for profile details 
// import validate from './validate.js';

let loadProfile = (user) => {
    let xhr = new XMLHttpRequest(); 
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

// validate(loadProfile); 