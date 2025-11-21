// Function like product
<<<<<<< HEAD
let changeIcon = function (icon) {
  icon.classList.toggle('fas')
}

=======
let changeIcon = function(icon){
	icon.classList.toggle('fas')
}

// Function Remove Items from Offcanvas - Like
// if(document.readyState == 'loading'){
// 	document.addEventListener('DOMContentLoaded', ready)
// }
// else{
// 	ready();
// }

// function ready(){
// 	var removeButtons = document.getElementsByClassName('remove-button')
// 	console.log(removeButtons)
// 	for(var i=0; i< removeButtons.length; i++){
// 		var button = removeButtons[i];
// 		button.addEventListener('click', removeItem);
// 	}
// }

// function removeItem(event){
// 	var buttonClicked = event.target;
// 	buttonClicked.parentElement.parentElement.remove();
// }
>>>>>>> a35a6cb48d5e68ef90dd1afcdb21499ab3f4514b


//tooltip
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})


//quantity in shop-detail $ cart
<<<<<<< HEAD
function increaseCount(a, b) {
  var input = b.previousElementSibling;
  var value = parseInt(input.value, 10);
  value = isNaN(value) ? 0 : value;
  value++;
  input.value = value;
}
function decreaseCount(a, b) {
  var input = b.nextElementSibling;
  var value = parseInt(input.value, 10);
  if (value > 1) {
    value = isNaN(value) ? 0 : value;
    value--;
    input.value = value;
  }
=======
function increaseCount(a,b){
    var input = b.previousElementSibling;
    var value = parseInt(input.value, 10);
    value = isNaN(value)? 0 : value;
    value++;
    input.value = value;
}
function decreaseCount(a,b){
    var input = b.nextElementSibling;
    var value = parseInt(input.value, 10);
    if(value>1){
        value = isNaN(value)? 0 : value;
        value--;
        input.value = value;
    }
>>>>>>> a35a6cb48d5e68ef90dd1afcdb21499ab3f4514b
}


//color-choose-button-active
// Get the container element
var btnnav = document.getElementById("navbarSupportedContent");

// Get all buttons with class="btn" inside the container
var btnnavs = btnContainer.getElementsByClassName("nav-link");

// Loop through the buttons and add the active class to the current/clicked button
for (var i = 0; i < btns.length; i++) {
<<<<<<< HEAD
  btns[i].addEventListener("click", function () {
=======
  btns[i].addEventListener("click", function() {
>>>>>>> a35a6cb48d5e68ef90dd1afcdb21499ab3f4514b
    var current = document.getElementsByClassName("active");
    current[0].className = current[0].className.replace(" active", "");
    this.className += " active";
  });
}