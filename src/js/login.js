let xhr = new XMLHttpRequest(); 
let form = document.querySelector('form');
let loginForm = document.getElementById('login');
let base = 'ORIGIN';

let login = (form) => {
    console.log('Hello World'); 
    let input = new FormData(form);
    var object = {};
    input.forEach((value, key) => {object[key] = value});
    var json = JSON.stringify(object);
    xhr.open("POST", base + 'api/login.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json'); 
    console.log(xhr); 
    xhr.onreadystatechange = () => {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            console.log(xhr.responseText);
        }
    }
    xhr.send(json); 
}

window.addEventListener("load", () => {
    loginForm.addEventListener('submit', (event) => {
        console.log("hello world"); 
        login(loginForm);
        event.preventDefault(); 
    });
});