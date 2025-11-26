//tooltip
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})


//quantity in shop-detail $ cart
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
}