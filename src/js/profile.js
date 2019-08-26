import { base, submitGetRequest, stripSpecialChars, getParams } from './util.js';

// social media options 
let supportedSocial = ["insta", "snap"]; 

let loadProfile = () => {
    let user = getParams('user'); 
    user = stripSpecialChars(user); 
    if (user != null) {
        submitGetRequest('api/profile.php/?user=', addToDOM, user); 
    } else { 
        // bad profile request
        window.location.replace(base + "error/?code=400"); 
    }
}

let addToDOM = (res) => {
    let profileData = document.getElementById("profileData"); 
    for (let key in res) {
        if (res.hasOwnProperty(key) && res[key] != null) {
            if (supportedSocial.includes(key)) {
                // for social media profile info 
                let valueNode = document.createTextNode(res[key]); 
                let socialIcon = document.getElementById(key);
                socialIcon.appendChild(valueNode); 
                socialIcon.setAttribute("visibility", "visible"); 
                //valueNode.appendChild(document.createElement("br")); 
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

// load event listener once window loads 
window.addEventListener("load", () => {
    loadProfile(); 
    event.preventDefault(); 
});