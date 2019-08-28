import { validate, getParams } from './util.js';

let token = getParams('token', true); 

let load = (res) => {
    populateDashboard(res.data.user); 
    console.log("Hello World, I've just loaded from Dashboard."); 
}

let populateDashboard = (user) => {
    // create profile button
    let a = document.createElement('a'); 
    let linkText = document.createTextNode("Profile"); 
    a.appendChild(linkText);
    a.href = "../profile/?user=" + user; 
    document.body.appendChild(a); 
}

validate(load, token); 