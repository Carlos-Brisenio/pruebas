const movPag = document.querySelector(".movPag");
const btn_adelante2 = document.querySelector(".sigPag");

const btn_atras1 = document.querySelector(".volver-pag1");
const btn_adelante3 = document.querySelector(".adelante-pag3");
const btn_atras2 = document.querySelector(".volver-pag2");
const btn_adelante4 = document.querySelector(".adelante-pag4");
const btn_final = document.querySelector(".final");
const btn_salir = document.querySelector(".salir");


const progressText = document.querySelectorAll(".paso p");
const progressCheck = document.querySelectorAll(".paso .check" );
const num = document.querySelectorAll(".paso .num");

let max = 4;
let cont = 1;

btn_adelante2.addEventListener("click",function(e){
    e.preventDefault();
    movPag.style.marginLeft = "-25%";
    num[cont-1].classList.add("active");
    progressCheck[cont-1].classList.add("active");
    progressText[cont-1].classList.add("active");
    cont += 1;
});

btn_adelante3.addEventListener("click",function(e){
    e.preventDefault();
    movPag.style.marginLeft = "-50%";
    num[cont-1].classList.add("active");
    progressCheck[cont-1].classList.add("active");
    progressText[cont-1].classList.add("active");
    cont += 1;
});

btn_adelante4.addEventListener("click",function(e){
    e.preventDefault();
    movPag.style.marginLeft = "-75%";
    num[cont-1].classList.add("active");
    progressCheck[cont-1].classList.add("active");
    progressText[cont-1].classList.add("active");
    cont += 1;
});

btn_atras1.addEventListener("click",function(e){
    e.preventDefault();
    movPag.style.marginLeft = "0%";
    num[cont-2].classList.remove("active");
    progressCheck[cont-2].classList.remove("active");
    progressText[cont-2].classList.remove("active");
    cont -= 1;
});

btn_atras2.addEventListener("click",function(e){
    e.preventDefault();
    movPag.style.marginLeft = "-25%";
    num[cont-2].classList.remove("active");
    progressCheck[cont-2].classList.remove("active");
    progressText[cont-2].classList.remove("active");
    cont -= 1;
});

btn_final.addEventListener("click",function(e){
    e.preventDefault();
    num[cont-1].classList.add("active");
    progressCheck[cont-1].classList.add("active");
    progressText[cont-1].classList.add("active");
    cont += 1;
    alert("Fin del registro");
    setTimeout( function() { window.location.href = "/principal-Usuario.html"; }, 1000 );
});

btn_salir.addEventListener("click",function(e){
    e.preventDefault();
    setTimeout( function() { window.location.href = "/principal-Usuario.html"; }, 200 );
});