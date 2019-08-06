let xhr = new XMLHttpRequest(); 
let createUserForm = document.getElementById('createNewUser');
let base = 'ORIGIN';

let createNewUser = () => {
    // collect and format the sign in form data
    let input = new FormData(createUserForm);
    let object = {};
    input.forEach((value, key) => {object[key] = value});
    let json = JSON.stringify(object);
    // configure xhr request 
    xhr.open('POST', base + 'api/create.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json'); 
    xhr.onreadystatechange = () => {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status == 200) {
                console.log(xhr.responseText); 
            }
        }
    }
    // submit xhr request
    xhr.send(json); 
}

// load event listener once window loads 
window.addEventListener("load", () => {
    // trigger create function when submit button pushed
    createUserForm.addEventListener('submit', (event) => {
        createNewUser();
        event.preventDefault(); 
    });
});