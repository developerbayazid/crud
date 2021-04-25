document.addEventListener('DOMContentLoaded', function(){
    var links = document.querySelectorAll(".delete");
    for(i = 0; i<links.length; i++){
        links[i].addEventListener('click', function(e){
            if(!confirm("Are You Sure?")){
                e.preventDefault();
            }
        });
    }
});