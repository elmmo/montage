import { base, submitPostRequest } from './util.js';
let loginForm = document.getElementById('login');

let login = () => {
    submitPostRequest('api/login.php', 
    (res) => {
        window.location.href = base + 'dashboard/?token=' + res.jwt;
    }, 
    'login', 
    loginForm, 
    (status) => {
        if (status == 400) {
            console.log("Incorrect password or email."); 
            window.location.replace(base + '?redirect=' + 'incorrect-login'); 
        }
    }); 
}

// load event listener once window loads 
window.addEventListener("load", () => {
    // trigger loginForm function when submit button pushed
    loginForm.addEventListener('submit', (event) => {
        login();
        event.preventDefault(); 
    });
});