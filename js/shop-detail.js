
// //color-choose-button-active
// // Get the container element
// var btnContainer = document.getElementById("color-choose");

// // Get all buttons with class="btn" inside the container
// var btns = btnContainer.getElementsByClassName("button");

// // Loop through the buttons and add the active class to the current/clicked button
// for (var i = 0; i < btns.length; i++) {
//   btns[i].addEventListener("click", function() {
//     var current = document.getElementsByClassName("act");
//     current[0].className = current[0].className.replace(" act", "");
//     this.className += " act";
//   });
// }

//img slider
var mainImg = document.getElementById("mainImg");
function showImg(pic){
    mainImg.src = pic;
}

