
// /========chekbox section start=======
// function changeColor() {

//     document.getElementById('fullBox').style.background = "black";
//     document.getElementById('fullBox').style.color = "white";
//     document.getElementById('fullBox').style.color = "white";

    
// }


const showorhide = document.querySelector('#fullBox');
    isClicked = true;

let changeColor = function(){

    if(isClicked){

        showorhide.style.background = "black";
        showorhide.style.color = "white";
        showorhide.style.input = "white";

        isClicked = false;
    }
    else{
        showorhide.style.background = "white";
        showorhide.style.color = "black";
        isClicked = true;
    }

    
}

///////========//////////second 

const showorhide2 = document.querySelector('#fullBox2');
    isClicked2 = true;

let changeColor2 = function(){

    if(isClicked2){

        showorhide2.style.background = "black";
        showorhide2.style.color = "white";

        isClicked2 = false;
    }
    else{
        showorhide2.style.background = "white";
        showorhide2.style.color = "black";
        isClicked2 = true;
    }

    
}
//////////// three
const showorhide3 = document.querySelector('#fullBox3');
    isClicked3 = true;

let changeColor3 = function(){

    if(isClicked3){

        showorhide3.style.background = "black";
        showorhide3.style.color = "white";

        isClicked3 = false;
    }
    else{
        showorhide3.style.background = "white";
        showorhide3.style.color = "black";
        isClicked3 = true;
    }

    
}

//////////// four
const showorhide4 = document.querySelector('#fullBox4');
    isClicked4 = true;

let changeColor4 = function(){

    if(isClicked4){

        showorhide4.style.background = "black";
        showorhide4.style.color = "white";

        isClicked4 = false;
    }
    else{
        showorhide4.style.background = "white";
        showorhide4.style.color = "black";
        isClicked4 = true;
    }

    
}

//////////// five
const showorhide5 = document.querySelector('#fullBox5');
    isClicked5 = true;

let changeColor5 = function(){

    if(isClicked5){

        showorhide5.style.background = "black";
        showorhide5.style.color = "white";

        isClicked5 = false;
    }
    else{
        showorhide5.style.background = "white";
        showorhide5.style.color = "black";
        isClicked5 = true;
    }

    
}

//////////// six
const showorhide6 = document.querySelector('#fullBox6');
    isClicked6 = true;

let changeColor6 = function(){

    if(isClicked6){

        showorhide6.style.background = "black";
        showorhide6.style.color = "white";

        isClicked6 = false;
    }
    else{
        showorhide6.style.background = "white";
        showorhide6.style.color = "black";
        isClicked6 = true;
    }

    
}

//////////// Seven
const showorhide7 = document.querySelector('#fullBox7');
    isClicked7 = true;

let changeColor7 = function(){

    if(isClicked7){

        showorhide7.style.background = "black";
        showorhide7.style.color = "white";

        isClicked7 = false;
    }
    else{
        showorhide7.style.background = "white";
        showorhide7.style.color = "black";
        isClicked7 = true;
    }

    
}

//////////// Eight
const showorhide8 = document.querySelector('#fullBox8');
isClicked8 = true;

let changeColor8 = function(){

    if(isClicked8){

        showorhide8.style.background = "black";
        showorhide8.style.color = "white";

        isClicked8 = false;
    }
    else{
        showorhide8.style.background = "white";
        showorhide8.style.color = "black";
        isClicked8 = true;
    }

    
}

//////////// Nine
const showorhide9 = document.querySelector('#fullBox9');
isClicked9 = true;

let changeColor9 = function(){

    if(isClicked9){

        showorhide9.style.background = "black";
        showorhide9.style.color = "white";

        isClicked9 = false;
    }
    else{
        showorhide9.style.background = "white";
        showorhide9.style.color = "black";
        isClicked9 = true;
    }

    
}

//////////// Ten
const showorhide10 = document.querySelector('#fullBox10');
isClicked10 = true;

let changeColor10 = function(){

    if(isClicked10){

        showorhide10.style.background = "black";
        showorhide10.style.color = "white";

        isClicked10 = false;
    }
    else{
        showorhide10.style.background = "white";
        showorhide10.style.color = "black";
        isClicked10 = true;
    }

    
}

//////////// Eleven
const showorhide11 = document.querySelector('#fullBox11');
isClicked11 = true;

let changeColor11 = function(){

    if(isClicked11){

        showorhide11.style.background = "black";
        showorhide11.style.color = "white";

        isClicked11 = false;
    }
    else{
        showorhide11.style.background = "white";
        showorhide11.style.color = "black";
        isClicked11 = true;
    }

    
}

//////////// Twelve
const showorhide12 = document.querySelector('#fullBox12');
isClicked12 = true;

let changeColor12 = function(){

    if(isClicked12){

        showorhide12.style.background = "black";
        showorhide12.style.color = "white";

        isClicked12 = false;
    }
    else{
        showorhide12.style.background = "white";
        showorhide12.style.color = "black";
        isClicked12 = true;
    }

    
}
// /========chekbox section end=======

// next button active when any checkbox selected
document.addEventListener("DOMContentLoaded", function () {
    let checkboxes = document.querySelectorAll(".check1");
    let nextButton = document.getElementById("nextButton");

    if (nextButton) {
        let nextLink = nextButton.querySelector("span");


        // Rest of your code that depends on nextButton and nextLink
    }
    function toggleNextButton() {
        let isAnyChecked = Array.from(checkboxes).some(chk => chk.checked);
        let nonecheckbox = document.querySelector(`input[type="checkbox"][value="mentalhealth"]`);

        if(nonecheckbox){
            if(nonecheckbox.checked) {
                document.getElementById("nextstep").value = "more";
            }
    }
        let neoplasiacheckbox = document.querySelector(`input[type="checkbox"][value="neoplasia"]`);
        let pancreatitcheckbox = document.querySelector(`input[type="checkbox"][value="pancreatitishistory"]`);
        let eatingdisordercheckbox = document.querySelector(`input[type="checkbox"][value="eatingdisorder"]`);

        if(neoplasiacheckbox || pancreatitcheckbox ||  eatingdisordercheckbox) {
            if (neoplasiacheckbox.checked  || pancreatitcheckbox.checked ||  eatingdisordercheckbox.checked) {
                document.getElementById("nextstep").value ="history_pancreatitis";

            }
        }
        let thyroidoperationcheckbox = document.querySelector(`input[type="checkbox"][value="thyroidoperation"]`);
        if(thyroidoperationcheckbox){
            if(thyroidoperationcheckbox.checked) {
                document.getElementById("nextstep").value = "thyroidoperation";
            }
        }

        let  bariatricoperationcheckbox = document.querySelector(`input[type="checkbox"][value="bariatricoperation"]`);
        if(bariatricoperationcheckbox){
            if(bariatricoperationcheckbox.checked) {
                document.getElementById("nextstep").value = "bariatricoperation";
            }
        }

     /*   let  noneconditionscheckbox = document.querySelector(`input[type="checkbox"][value="none"]`);
        if(noneconditionscheckbox){
            if(noneconditionscheckbox.checked) {
                document.getElementById("nextstep").value = "conditions";
            }
        }*/
        const medicationscheckboxes = document.querySelectorAll('input[name="medications[]"]');
        const isAnymedicationsChecked = Array.from(medicationscheckboxes).some(checkbox => checkbox.checked);
        if (isAnymedicationsChecked) {

            medicationscheckboxes.forEach(function(medicationscheckbox) {
                if(medicationscheckbox.checked){
                    console.log("medicationscheckbox checked");
                    document.getElementById("nextstep").value = "starting_wegovy";
                    console.log(medicationscheckbox.value);
                    if(medicationscheckbox.value=="none"){

                        document.getElementById("nextstep").value = "medication_allergies";
                    }

                }
                console.log("medicationscheckbox");
                console.log(medicationscheckbox.value);

            });
        }
      /*  let  wegovycheckbox = document.querySelector(`input[type="checkbox"][value="wegovy"]`);
        console.log("wegovycheckbox");
        console.log(wegovycheckbox);

        //(`input[type="checkbox"][value="wegovy"]`);
        if(wegovycheckbox){
            if(wegovycheckbox.checked) {

               // document.getElementById("medication").html =wegovycheckbox.value;
                document.getElementById("nextstep").value = "starting_wegovy";
            }
        }
*/
        if (nextButton) {
            if (isAnyChecked) {
                nextButton.disabled = false;
                nextButton.style.backgroundColor = "#B0D136";
                nextLink.style.color = "black";
                nextButton.style.cursor = "pointer";
                nextLink.style.pointerEvents = "auto";
            } else {
                nextButton.disabled = true;
                nextButton.style.backgroundColor = "#d3d3d3";
                nextLink.style.color = "#a0a0a0";
                nextButton.style.cursor = "default";
                nextLink.style.pointerEvents = "none";
            }
        }
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener("change", toggleNextButton);
    });

    toggleNextButton();
});
// next button active when any checkbox selected
