const logbutton = document.querySelector(".login-signup a");
const faqbutton = document.getElementsByClassName("question-FAQ");
const userAddBtn = document.getElementById("addUserBtn");

function displaylogin(){
    let displaylog = document.getElementById("loginModal");
    // console.log(displaylog.style.display);
    if (displaylog.style.display === 'block') {
        displaylog.style.display = 'none';
    } else {
        displaylog.style.display = 'block';
        // console.log(displaylog.style.display);
    }
}

function displayuseradd(){
    let displaylog = document.getElementById("userAddModal");
    // console.log(displaylog.style.display);
    if (displaylog.style.display === 'block') {
        displaylog.style.display = 'none';
    } else {
        displaylog.style.display = 'block';
        // console.log(displaylog.style.display);
    }
}


function displayFAQ(toDisplay) {
    let question = document.getElementById(toDisplay);
    let answer = document.getElementById(`r${toDisplay.substring(1)}`); // Assuming the answer ID is 'r' + question ID without 'q'
    
    if (answer.style.display === 'block') {
        answer.style.display = 'none';
    } else {
        answer.style.display = 'block';
    }
}

Array.from(faqbutton).forEach(button => {
    button.addEventListener("click", () => displayFAQ(button.id));
});
if(logbutton){
    logbutton.addEventListener("click",displaylogin);
}
if(userAddBtn){
    userAddBtn.addEventListener("click",displayuseradd);

}
