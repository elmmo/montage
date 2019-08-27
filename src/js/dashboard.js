import { validate, getParams } from './util.js';

let token = getParams('token', true); 

let load = (data) => {
    console.log("Hello World, I've just loaded from Dashboard."); 
}

validate(load, token); 