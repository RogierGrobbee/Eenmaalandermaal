/**
 * Created by leon on 16/12/2016.
 */
document.getElementsByClassName("tijd")[0].addEventListener("change", function() {
    removeBieden()}
);

$(".tijd").change(function(){
   alert("aadksjdadnaskdj");
});

function removeBieden(){
    alert("ww");

    $(".bieden").remove();

    if(this.innerHTML == 'Beëindigd!') {
        $("div").remove(".bieden");
    }
}