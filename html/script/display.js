const logbutton = document.querySelector(".login-signup a");
const faqbutton = document.getElementsByClassName("question-FAQ");
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

function displayFAQ(toDisplay){

    let display = document.getElementById(toDisplay);
    console.log(display.style.display);
    if (display.style.display === 'block') {
        display.style.display = 'none';
    } else {
        display.style.display = 'block';
        console.log(display.style.display);
    }
}

Array.from(faqbutton).forEach(button => {
    button.addEventListener("click", () => displayFAQ(button.id));
});
logbutton.addEventListener("click",displaylogin);
