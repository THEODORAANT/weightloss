
  /*document.getElementById('review-btn').addEventListener('click', function() {
    alert('Redirecting to review your answers.');
    
});*/


document.addEventListener("DOMContentLoaded", function() {
  const btn = document.querySelector(".btn-dark");
  btn.addEventListener("mouseover", function() {
      btn.classList.add("btn-outline-light");
  });
  btn.addEventListener("mouseleave", function() {
      btn.classList.remove("btn-outline-light");
  });
});



document.addEventListener("DOMContentLoaded", function () {
  console.log("Page Loaded Successfully!");
});






