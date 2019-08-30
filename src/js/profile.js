import { submitGetRequest, submitPutRequest, stripSpecialChars, getParams, getCookie } from './util.js';

// social media options 
let supportedSocial = ["insta", "snap"]; 

let loadProfile = () => {
    let user = getParams('user', true); 
    if (user != null) {
        submitGetRequest('api/profile.php/?user=', addProfileToDOM, user); 
    }
}

let addProfileToDOM = (res) => {
    let profileData = document.getElementById("profileData"); 
    for (let key in res) {
        if (res.hasOwnProperty(key) && res[key] != null) {
            if (supportedSocial.includes(key)) {
                // for social media profile info 
                let valueNode = document.createTextNode(res[key]); 
                let socialIcon = document.getElementById(key);
                socialIcon.appendChild(valueNode); 
                socialIcon.setAttribute("visibility", "visible"); 
            } else { 
                // adds the key to the table 
                let row = document.createElement("tr"); 
                let cell = document.createElement("td"); 
                row.appendChild(cell); 
                cell.appendChild(document.createTextNode(key)); 
                // adds the value to the table
                cell = document.createElement("td"); 
                row.appendChild(cell); 
                cell.appendChild(document.createTextNode(res[key])); 
                // append row to table 
                profileData.appendChild(row); 
            }
        }
    }
}

let updateUser = (form) => {
    let username = getCookie("user");
    let id = getCookie("id");
    // sanitize data 
    let elements = form.elements; 
    for (let i = 0; i < elements.length; i++) {
        if (elements[i].value != "" && elements[i].type != "email") {
            elements[i].value = stripSpecialChars(elements[i].value); 
        }
    }
    console.log(form); 
    submitPutRequest(form, "api/profile.php/?user=" + username + "&id=" + id, () => {
        console.log("success"); 
    }); 
}

// load event listener once window loads 
window.addEventListener("load", () => {
    loadProfile(); 

    // trigger update when submit button pushed 
    let updateForm = document.getElementById("editUser"); 
    updateForm.addEventListener('submit', (event) => {
        event.preventDefault(); 
        updateUser(updateForm); 
    })
});