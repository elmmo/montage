import { validate, getParams} from './util.js';

let token = getParams('token', true); 

// callback once data has loaded
let load = (res) => {
    res = res.data; 
    setTokenCookies(res); 
    populateDashboard(res.user); 
}

let populateDashboard = (user) => {
    // create profile button
    let a = document.createElement('a'); 
    let linkText = document.createTextNode("Profile"); 
    a.appendChild(linkText);
    a.href = "../profile/?user=" + user; 
    document.body.appendChild(a); 
}

// get information from the token 
let setTokenCookies = (tokenData) => {
    let date = new Date(); 
    // makes cookies expire in 3 hours
    date.setTime(date.getTime() + 60*60*1000*3);
    let expires = "; expires=" + date.toUTCString(); 
    for (let key in tokenData) {
        document.cookie = key + "=" + tokenData[key] + expires + "; path=/"; 
    }
    // set logged in status
    document.cookie = "logged_in=true" + expires + "; path=/"; 
}

validate(load, token); 