const logbutton = document.querySelector(".login-signup a");
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

logbutton.addEventListener("click",displaylogin);
