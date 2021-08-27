const formL = document.querySelector("#app .modal .modal-card .modal-card-body #loginForm");
const formR = document.querySelector("#app .modal .modal-card .modal-card-body #registerForm");
let postButtonR = formR.querySelector(".field .buttons #register");
let postButtonL = formL.querySelector(".field .buttons #login");
let messageDivR = formR.querySelector(".message");
let messageDivL = formL.querySelector(".message");

function showLoading() {
    if (vue.$data.casesLogin == true || vue.$data.casesRegister == true) {
        let div = document.createElement("div");
        div.className += "wrapper-loading";
        div.id += "loader";
        let body = document.body;
        body.appendChild(div);
        document.getElementById("loader").innerHTML = '<div class="half-circle-spinner"> <div class="circle circle-1"></div> <div class="circle circle-2"></div> </div>';
        document.getElementById("app").style.cssText = "opacity: 0.1;";
    }
}

function delLoading() {
    if (document.contains(document.getElementById("loader"))) {
        document.getElementById("loader").remove();
        document.getElementById("app").style.cssText = "opacity: 1";
    }
}


formR.onsubmit = (e) => {
    e.preventDefault();
}

formL.onsubmit = (e) => {
    e.preventDefault();
}

postButtonR.onclick = () => {
    showLoading();
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "./register/", true);
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                let data = xhr.response.trim();
                if (data === "Sucesso") {
                    messageDivR.classList.remove("is-danger");
                    messageDivR.classList.add("is-success");
                    data = "A sua conta foi cadastrada com sucesso! Para acessar o sistema, entre em seu email e clique no link de verificação que enviamos a você!";
                    resetR();
                    vue.$data.casesRegister = false;
                }
                else {
                    messageDivR.classList.remove("is-success");
                    messageDivR.classList.add("is-danger");
                }
                messageDivR.style.display = "block";
                messageDivR.querySelector(".message-body").textContent = data;

                if (vue.$data.isActiveLoadingRegister === true) {
                    vue.$data.isActiveLoadingRegister = !vue.$data.isActiveLoadingRegister;
                }
                messageDivR.focus();
                delLoading();
            }
        }
    }
    let formData = new FormData(formR);
    xhr.send(formData);
}

postButtonL.onclick = () => {
    showLoading();
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "./login/", true);
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                let data = xhr.response.trim();
                if (data === "CUSTOMER") {
                    window.location.replace("./customermenu/");
                }
                else if (data === "DEVELOPER") {
                    window.location.replace("./developermenu/");
                }
                else {
                    messageDivL.style.display = "block";
                    messageDivL.querySelector(".message-body").textContent = data;
                    if (vue.$data.isActiveLoadingLogin === true) {
                        vue.$data.isActiveLoadingLogin = !vue.$data.isActiveLoadingLogin;
                    }
                    resetL();
                    vue.$data.casesLogin = false;
                    delLoading();
                }
            }
        }
    }
    let formData = new FormData(formL);
    xhr.send(formData);
}

function resetR() {
    vue.$data.registerName = "", vue.$data.registerCpf = "", vue.$data.registerEmail = "", vue.$data.registerDate = "", vue.$data.registerSelect = "", vue.$data.passwd1 = "", vue.$data.passwd2 = "";
    document.getElementById("NAME_REGISTER").value = "";
    document.getElementById("CPF_REGISTER").value = "";
    document.getElementById("EMAIL_REGISTER").value = "";
    document.getElementById("PASSWORD1").value = "";
    document.getElementById("PASSWORD2").value = "";
    document.getElementById("BIRTH_DATE").value = "";
    document.getElementById("TYPE_REGISTER").value = "";
}
function resetL() {
    vue.$data.loginPasswd = "", vue.$data.loginSelect = "";
    document.getElementById("PASSWORD_LOGIN").value = "";
    document.getElementById("TYPE_LOGIN").value = "";
}

let inputPasswordR = formR.querySelector(".field .control input[type='password']"),
    inputPasswordL = formL.querySelector(".field .control input[type='password']"),
    toggleIconR = formR.querySelector(".field .control #toggleIconR"),
    toggleIconL = formL.querySelector(".field .control #toggleIconL");
    

toggleIconR.onmouseout = () => {
    if (inputPasswordR.type === "text") {
        inputPasswordR.type = "password";
        toggleIconR.classList.remove("fa-eye-slash");
        toggleIconR.classList.add("fa-eye");
    }
}

toggleIconL.onmouseout = () => {
    if (inputPasswordL.type === "text") {
        inputPasswordL.type = "password";
        toggleIconL.classList.remove("fa-eye-slash");
        toggleIconL.classList.add("fa-eye");
    }
}

function pwdShowR() {
    if (inputPasswordR.type === "password") {
        inputPasswordR.type = "text";
        toggleIconR.classList.remove("fa-eye");
        toggleIconR.classList.add("fa-eye-slash");
    } else {
        inputPasswordR.type = "password";
        toggleIconR.classList.remove("fa-eye-slash");
        toggleIconR.classList.add("fa-eye");
    }
}
function pwdShowL() {
    if (inputPasswordL.type === "password") {
        inputPasswordL.type = "text";
        toggleIconL.classList.remove("fa-eye");
        toggleIconL.classList.add("fa-eye-slash");
    } else {
        inputPasswordL.type = "password";
        toggleIconL.classList.remove("fa-eye-slash");
        toggleIconL.classList.add("fa-eye");
    }
}
