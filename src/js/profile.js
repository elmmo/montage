//  if token is valid, go to profile, pulling information from the db 
    // if first time login, display first time message prompting for profile details 

// need support for if the status code is incorrect (login.js); 

let xhr = new XMLHttpRequest(); 
let params = new URLSearchParams(window.location.search); 
let token = params.get('token'); 
let base = 'ORIGIN';

// verifies the token passed through the url 
if (token) {
    let json = JSON.stringify({'jwt': token});
    xhr.open('POST', base + 'api/validate_token.php', true); 
    xhr.setRequestHeader('Content-Type', 'application/json'); 
    xhr.onreadystatechange = () => {
        // if token is valid
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status == 200) {
                console.log(xhr.responseText); 
                let res = JSON.parse(xhr.responseText); 
                let name = document.createTextNode("Welcome, " + res.data.name);
                document.body.appendChild(name);
            } else {
                window.location.replace(base + 'error/?code=' + xhr.status); 
            }
        }
    };
    // submit xhr request
    xhr.send(json); 
} else {
    window.location.replace(base + '?redirect=' + 'token'); 
}