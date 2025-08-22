

document.addEventListener("DOMContentLoaded", function() {
    const plans = document.querySelectorAll(".plan");
    const summary = document.querySelector(".summary");
    const totalPrice = document.querySelector(".total-price");
    
    plans.forEach(plan => {
        plan.addEventListener("click", function() {
            document.querySelector(".plan.active").classList.remove("active");
            this.classList.add("active");
            
            let months = this.getAttribute("data-months");
            let price = parseFloat(this.getAttribute("data-price"));
            let oldPrice = parseFloat(this.getAttribute("data-old-price"));
            let total = (months * price).toFixed(2);
            let oldTotal = (months * oldPrice).toFixed(2);
            
            summary.innerHTML = `${months} months x &pound;${price} / mo`;
            totalPrice.innerHTML = `<span class='old-price'>&pound;${oldTotal}</span> &pound;${total}`;
        });
    });
});


function addToCartDose(productid){
    console.log("addToCartDose");
var elbutton=document.getElementById("submitbtn"+productid);
    var id= document.getElementById("product"+productid).value;
    console.log(id);


    const xhr = new XMLHttpRequest();
    xhr.open("POST", "/order/add-to-cart");
    xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
    const body = JSON.stringify({
        m: id
    });
    xhr.onload = () => {
        if (xhr.readyState == 4 && (xhr.status == 201 || xhr.status == 200)) {
           // console.log(JSON.parse(xhr.responseText));
            elbutton.classList.add("added");
            elbutton.style.border="1px solid black";
            elbutton.innerHTML = '<i class="fas fa-check"></i> Added'; // "✔ Added" দেখাবে
        } else {
            console.log(`Error: ${xhr.status}`);
        }
    };
    xhr.send(body);
    /*  if (elbutton.classList.contains("added")) {
          elbutton.classList.remove("added");
          elbutton.style.border="none";

          elbutton.innerHTML = '<i class="fas fa-plus"></i> Add'; // "+" আইকনসহ "Add" ফিরে আসবে
      } else {
          elbutton.classList.add("added");
          elbutton.style.border="1px solid black";
          elbutton.innerHTML = '<i class="fas fa-check"></i> Added'; // "✔ Added" দেখাবে
     // }*/

}

// secont page recommended-addons
function addToCart(elbutton){
    // প্রথমেই + আইকন সেট করা হচ্ছে
  /*  if (!elbutton.classList.contains("added")) {
        elbutton.innerHTML = '<i class="fas fa-plus"></i> Add';
        elbutton.style.color="black";
    }*/
    var id=elbutton.id;
    console.log(id);


    const xhr = new XMLHttpRequest();
    xhr.open("POST", "/order/add-to-cart");
    xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
    const body = JSON.stringify({
        m: id
    });
    xhr.onload = () => {
        if (xhr.readyState == 4 && (xhr.status == 201 || xhr.status == 200)) {
           // console.log(JSON.parse(xhr.responseText));
            elbutton.classList.add("added");
            elbutton.style.border="1px solid black";
            elbutton.innerHTML = '<i class="fas fa-check"></i> Added'; // "✔ Added" দেখাবে
        } else {
            console.log(`Error: ${xhr.status}`);
        }
    };
    xhr.send(body);
      /*  if (elbutton.classList.contains("added")) {
            elbutton.classList.remove("added");
            elbutton.style.border="none";

            elbutton.innerHTML = '<i class="fas fa-plus"></i> Add'; // "+" আইকনসহ "Add" ফিরে আসবে
        } else {
            elbutton.classList.add("added");
            elbutton.style.border="1px solid black";
            elbutton.innerHTML = '<i class="fas fa-check"></i> Added'; // "✔ Added" দেখাবে
       // }*/

}
/*
document.addEventListener("DOMContentLoaded", function () {
    const addButtons = document.querySelectorAll(".add-btn");

    addButtons.forEach((button) => {
        // প্রথমেই + আইকন সেট করা হচ্ছে
        if (!button.classList.contains("added")) {
            button.innerHTML = '<i class="fas fa-plus"></i> Add';
            button.style.color="black";
        }

        button.addEventListener("click", function () {
            if (this.classList.contains("added")) {
                this.classList.remove("added");
                this.style.border="none";
                
                this.innerHTML = '<i class="fas fa-plus"></i> Add'; // "+" আইকনসহ "Add" ফিরে আসবে
            } else {
                this.classList.add("added");
                this.style.border="1px solid black";
                this.innerHTML = '<i class="fas fa-check"></i> Added'; // "✔ Added" দেখাবে
            }
        });
    });
});
*/
// third page order summary

/*
    document.getElementById("submit-btn").addEventListener("click", function(event) {
        event.preventDefault();
        let valid = true;
        document.querySelectorAll(".required").forEach(input => {
            if (input.value.trim() === "") {
                valid = false;
                input.classList.add("border-danger");
                input.nextElementSibling.textContent = `${input.previousElementSibling.textContent} field is required`;
            } else {
                input.classList.remove("border-danger");
                input.nextElementSibling.textContent = "";
            }
        });
        if (valid) alert("Form submitted successfully!");
    });

*/
