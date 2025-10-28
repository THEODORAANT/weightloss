function validateHeight(height) {


    if (isNaN(height)) {
        return false;
    }

    if (height < 0.5 || height > 12) {
        return false;
    }


    return true;
}
function validateEl(elemntName) {
    console.log(elemntName);
    console.log(document.querySelector('input[name=' + elemntName + ']').type);
    if(document.querySelector('input[name=' + elemntName + ']').type=="text"){
        if (document.querySelector('input[name=' + elemntName + ']').value == "") {
            return false;
        }

    }else  if(document.querySelector('input[name=' + elemntName + ']').type=="checkbox"){

        const checkboxes = document.querySelectorAll('input[name=' + elemntName + ']');
        const isAnyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

        if (isAnyChecked) {
            return true;
        } else {
            return false;
        }
    }
    if(elemntName=="height"){

        const selectedRadio = document.querySelector('input[name=heightunit-radio]:checked');
        if(selectedRadio.value="ft-in"){

            return validateHeight( document.getElementById("height2").value);

        }

    }
    /*   const selectedRadio = document.querySelector('input[name=' + elemntName + ']:checked');

       if (selectedRadio.value != "") {
       return true;
       }*/
    return true;
}
function submitForm(elemntName) {
    if(validateEl(elemntName)) {
        //alert("submitForm");
        if (document.getElementById("nextstep").value == "") {
            const selectedRadio = document.querySelector('input[name=' + elemntName + ']:checked');


            document.getElementById("nextstep").value = selectedRadio.value;

        }
        document.getElementById("form1_questionnaire").submit();
    }
}
function setValuesreorderForm(id,value) {
    document.getElementById(id).value = value;
    if(id=="side_effects"){

        if(value=="yes"){
            document.getElementById("nextstep").value="more_side_effects";
        }else{

            document.getElementById("nextstep").value="additional-medication";
        }
    }
    if(id=="additional-medication"){

        if(value=="yes"){
            document.getElementById("nextstep").value="list_additional_medication";
        }else{

            document.getElementById("nextstep").value="rate_current_experience";
        }
    }
    if(id=="rate_current_experience"){

        if(value=="no"){
            document.getElementById("nextstep").value="no-happy";
        }else{

            document.getElementById("nextstep").value="chat_with_us";
        }
    }

    if(id=="chat_with_us"){

        if(value=="no"){
            document.getElementById("nextstep").value="cart";
        }else{

            document.getElementById("nextstep").value="cart";
        }
    }


}
function setValuesForm(id,value){
    document.getElementById(id).value=value;
    if(id=="pregnancy"){
        if(value=="yes"){
            document.getElementById("nextstep").value="pregnancy";
        }else{

            document.getElementById("nextstep").value="weight";
        }

    }

    if(id=="medical_conditions"){
        if(value=="yes"){
            document.getElementById("nextstep").value="list_any";
        }else{

            document.getElementById("nextstep").value="medications";
        }

    }
    if(id=="effects_with_wegovy"){

        if(value=="yes"){
            document.getElementById("nextstep").value="wegovy_side_effects";
        }else{

            document.getElementById("nextstep").value="medication_allergies";
        }
    }
    if(id=="more_side_effects"){

        if(value=="yes"){
            document.getElementById("nextstep").value="wegovy_side_effects";
        }else{

            document.getElementById("nextstep").value="medication_allergies";
        }
    }


    if(id=="gp_informed"){

        if(value=="yes"){
            document.getElementById("nextstep").value="gp_address";
        }else{

            document.getElementById("nextstep").value="access_special_offers";
        }
    }
    if(id=="bariatricoperation"){

        if(value=="yes"){
            document.getElementById("nextstep").value="history_pancreatitis";
        }else{

            document.getElementById("nextstep").value="more_pancreatitis";
        }
    }

    document.getElementById("form1_questionnaire").submit();


}
/*
document.addEventListener("DOMContentLoaded", function() {
    let radioButtons = document.querySelectorAll("input[type='radio']");
    let form = document.getElementById("myForm");

    radioButtons.forEach(function(radio) {
        radio.addEventListener("change", function() {
            alert("here");
            if (this.checked) {
                submitForm();
            }
        });
    });
});


document.getElementById('nextPageLabel').addEventListener('click', function() {
    // Redirect to a new page when the label is clicked
    //window.location.href = "Under18.html"; // Replace with your desired page URL
    document.getElementById("questionnaire").submit();

    alert('here');
  });
/*




document.getElementById('nextPageLab').addEventListener('click', function() {
    // Redirect to a new page when the label is clicked
    //window.location.href = "18to74.html"; // Replace with your desired page URL
    document.getElementById("questionnaire").submit();
    alert('here');
  });




document.getElementById('nextPageLabe').addEventListener('click', function() {
    // Redirect to a new page when the label is clicked
  //  window.location.href = "Under18.html"; // Replace with your desired page URL
    document.getElementById("questionnaire").submit();

    alert('here');
  });
  








    


  document.getElementById('review-btn').addEventListener('click', function() {
    alert('Redirecting to review your answers.');
    
});


// 1




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




*/


