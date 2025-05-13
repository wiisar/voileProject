
const displaylog = document.getElementById("loginModal");


function displaylogin(){
    console.log(displaylog.style.display);
    if (displaylog.style.display === 'block') {
        displaylog.style.display = 'none';
    } else {
        displaylog.style.display = 'block';
        console.log(displaylog.style.display);
    }


}

document.addEventListener('DOMContentLoaded', () => {
    displaylog.addEventListener('click', displaylogin);
});
