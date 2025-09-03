

    <?php  // output the top of the page
    perch_layout('getStarted/header', [
        'page_title' => perch_page_title(true),
    ]);

        /* main navigation
        perch_pages_navigation([
            'levels'   => 1,
            'template' => 'main_nav.html',
        ]);*/

    ?>
    <!-- ==========================================================================consultation section start ============================================================================================-->



    <section class="consultation">


        <div class="bar">
            <div class="bar_1">
            </div>
        </div>
        <div class="terms_content tab-buttons">
            <div data-tab="tab1" id="div-tab1" onclick="myTabChange('tab1')" class="terms tab-btn active">

         Terms

            </div>
            <div data-tab="tab2" id="div-tab2"  onclick="myTabChange('tab2')" class="terms tab-btn">

              Consultation

            </div>
            <div data-tab="tab3" id="div-tab3" onclick="myTabChange('tab3')" class="terms tab-btn">
Medication
            </div>
        </div>


        <div class="consent_title">
            <h2>Do you agree and consent to the following?</h2>
        </div>



        <div class="agree_content tab-content active" >
<div id="tab1">
            <ul class="" style="list-style-type: square;" >

                <li class="" >You are completing this consultation for yourself, providing information to the best of your knowledge. </li>
                <li class="" >You agree to disclose any medical conditions, serious illnesses, or past surgeries, as well as any prescription medications you are currently taking. Additionally, you acknowledge that you will use only one weight loss treatment at a time. </li>
               


                <li class="menu_list" >By proceeding, you confirm your acceptance of our <a style="text-decoration: none;color: #288881;" target="_blank" href="/terms-and-conditions">Terms & Conditions</a>, <a  target="_blank" style="text-decoration: none;color: #288881;" href="/privacy-notice">Privacy Policy</a> and acknowledge that you have read our Privacy Policy</a>. </li>




                <li class="menu_list" >It is essential to provide honest and accurate responses to this online questionnaire. Withholding or misrepresenting information can pose serious health risks, including life-threatening consequences. By submitting this questionnaire, you affirm that your responses are truthful and understand the potential dangers of misinformation.</li>

            </ul>
</div>
<div id="tab2"></div>
<div id="tab3"></div>
        </div>

        <div class="get_started">
            <div class="started_button">
                <div class="get_btn">
                    <a href="/get-started/questionnaire?step=howold"><button>Agree and start consultation</button></a><i class="fa-solid fa-arrow-right" style="margin-top: 6px;font-size: 20px;" ></i>
                </div>
            </div>
           </div>





    </section>

<style>
.tab-content {
  display: none;

}
.tab-buttons {
  display: flex;
  cursor: pointer;
}
.tab-content.active {
  display: block;
}
</style>
<script>
function myTabChange(eltab){
console.log("eltab");
console.log(eltab);
const tabButtons = document.querySelectorAll('.tab-btn');
console.log(tabButtons);
const tabContents = document.querySelectorAll('.tab-content');
    tabButtons.forEach(btn => btn.classList.remove('active'));
    tabContents.forEach(content => content.classList.remove('active'));
                document.getElementById("div-"+eltab).classList.add('active');

        document.getElementById(eltab).classList.add('active');
}
/*
console.log("target");
const tabButtons = document.querySelectorAll('.tab-btn');
console.log(tabButtons);
const tabContents = document.querySelectorAll('.tab-content');

tabButtons.forEach(button => {
console.log("button in");console.log(button);
  button.addEventListener('click', () => {
    const target = button.getAttribute('data-tab');
console.log("target in");console.log(target);
    // Remove active classes
    tabButtons.forEach(btn => btn.classList.remove('active'));
    tabContents.forEach(content => content.classList.remove('active'));

    // Add active classes
    button.classList.add('active');
    document.getElementById(target).classList.add('active');
  });
});*/

</script>
    <!-- ==========================================================================consultation section End ============================================================================================-->


    


















    <!-- ==================================================================coding End======================================================================================================== -->




    <?php
  perch_layout('getStarted/footer');?>













    
