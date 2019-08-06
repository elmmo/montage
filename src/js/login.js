let xhr = new XMLHttpRequest(); 
let form = document.querySelector('form');
let loginForm = document.getElementById('login');
let base = 'ORIGIN';
let params = new URLSearchParams(window.location.search); 

let login = (form) => {
    // collect and format the sign in form data
    let input = new FormData(form);
    let object = {};
    input.forEach((value, key) => {object[key] = value});
    let json = JSON.stringify(object);
    // configure xhr request 
    xhr.open('POST', base + 'api/login.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json'); 
    xhr.onreadystatechange = () => {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status == 200) {
                let res = JSON.parse(xhr.responseText); 
                window.location.href = base + 'profile/?token=' + res.jwt;
            } else {
                errorMessage(xhr.responseText.message); 
            }
        }
    }
    // submit xhr request
    xhr.send(json); 
}

let errorMessage = (message) => {
    console.log("Redirected here because " + message); 
}

// load event listener once window loads 
window.addEventListener("load", () => {
    // trigger loginForm function when submit button pushed
    loginForm.addEventListener('submit', (event) => {
        login(loginForm);
        event.preventDefault(); 
    });

    // if the user is being redirected from the same origin because of an error 
    if (params.has('redirect')) {
        errorMessage(params.get('redirect')); 
    }
});