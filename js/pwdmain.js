let inputPassword = document.querySelector("#app .hero .hero-body .container .columns .column .box form .field .control input[type='password']"),
    toggleIcon = document.querySelector("#app .hero .hero-body .container .columns .column .box form .field .control #toggleIcon");

function pwdShow() {
    if (inputPassword.type === "password") {
        inputPassword.type = "text";
        toggleIcon.classList.remove("fa-eye");
        toggleIcon.classList.add("fa-eye-slash");
    } else {
        inputPassword.type = "password";
        toggleIcon.classList.remove("fa-eye-slash");
        toggleIcon.classList.add("fa-eye");
    }
}
if (toggleIcon !== null) {
    toggleIcon.addEventListener("touchstart", pwdShow, {passive: true});
    toggleIcon.addEventListener("touchend", pwdShow, {passive: true});
    
    toggleIcon.onmouseout = () => {
        if (inputPassword.type === "text") {
            inputPassword.type = "password";
            toggleIcon.classList.remove("fa-eye-slash");
            toggleIcon.classList.add("fa-eye");
        }
    }
    
}