
const displaylog = document.getElementById("loginModal");
const logbutton = document.getElementById("login-signup")


function displaylogin(){
    let displaylog = document.getElementById("loginModal");
    console.log(displaylog.style.display);
    if (displaylog.style.display === 'block') {
        displaylog.style.display = 'none';
    } else {
        displaylog.style.display = 'block';
        console.log(displaylog.style.display);
    }


}

logbutton.addEventListener('click',displaylogin);
