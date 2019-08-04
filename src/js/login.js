let xhr = new XMLHttpRequest(); 
let reqUrl = "http://192.168.64.2/montage/api/login.php"; 
let form = document.querySelector('form');
let loginForm = document.getElementById('login');

let login = (form) => {
    console.log('Hello World'); 
    let input = new FormData(form);
    var object = {};
    input.forEach((value, key) => {object[key] = value});
    var json = JSON.stringify(object);
    xhr.open("POST", 'http://192.168.64.2/montage/api/login.php', true);
    xhr.setRequestHeader('Access-Control-Allow-Origin', 'http://192.168.64.2');
    xhr.setRequestHeader('Content-Type', 'application/json'); 
    console.log(xhr); 
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            alert(xhr.responseText);
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