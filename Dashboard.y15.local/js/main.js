
function showRegister() {
    document.querySelector(".tabs").classList.add("register");
    document.querySelectorAll(".tab")[0].classList.remove("active");
    document.querySelectorAll(".tab")[1].classList.add("active");

    document.getElementById("loginForm").classList.add("hidden");
    document.getElementById("registerForm").classList.remove("hidden");

    document.getElementById("mainIcon").className = "fa fa-user-plus";
}

function showLogin() {
    document.querySelector(".tabs").classList.remove("register");
    document.querySelectorAll(".tab")[1].classList.remove("active");
    document.querySelectorAll(".tab")[0].classList.add("active");

    document.getElementById("registerForm").classList.add("hidden");
    document.getElementById("loginForm").classList.remove("hidden");

    document.getElementById("mainIcon").className = "fa fa-lock";
}

/*función del ojito*/

document.querySelectorAll('.toggle-password').forEach(icon => {

    icon.addEventListener('click', function () {

        const input = this.parentElement.querySelector('.password-input');

        if (input.type === "password") {
            input.type = "text";
            this.src = "img/logos/ojo-cruzado.png"; // 👁️ cambia imagen
        } else {
            input.type = "password";
            this.src = "img/logos/ojo.png"; // 👁️ vuelve a la normal
        }

    });

});


/*Pestaña Deslizables RH*/

function mostrarTab(tab){

document.querySelectorAll(".tab-content").forEach(c=>{
c.classList.remove("active");
});

document.querySelectorAll(".tab-btn").forEach(b=>{
b.classList.remove("active");
});

document.getElementById(tab).classList.add("active");

event.target.classList.add("active");

}
