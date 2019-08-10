import { submitPostRequest } from './util.js';
let createUserForm = document.getElementById('createNewUser');

let createNewUser = () => {
    submitPostRequest('api/create.php', 
    (res) => {
        console.log(res); 
    }, 
    'create', 
    createUserForm); 
}

// load event listener once window loads 
window.addEventListener("load", () => {
    // trigger create function when submit button pushed
    createUserForm.addEventListener('submit', (event) => {
        createNewUser();
        event.preventDefault(); 
    });
});