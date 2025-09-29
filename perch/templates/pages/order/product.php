

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

    <!--================================================================== Weekly injection section start ============================================================================================================-->

    <div class="main_product">
        <div id="product-selection">
        <?php
        perch_shop_products();
        ?>






        </div>

        <!-- Product Details -->
        <div id="product-details1" class="product_table">
            <a class="Mounjaro_button" href="../addons.html"><h2>Continue with <span id="product-name">Mounjaro</span> <i class="fa-solid fa-arrow-right"></i> </h2></a>
            <h2>What to expect with <span id="product-name">Mounjaro</span></h2>
            <button id="browse-plans">Browse all plans</button>
             <?php echo "variants";
             perch_shop_product('mounjaro-mounjaro',[
                                                       'template' => 'products/product_view.html',

                                                   ]);
                //   perch_shop_product_variants('mounjaro-mounjaro');
// perch_shop_product('mounjaro-mounjaro');
                    ?>

        </div>
        <div id="product-details2" style="display:none" class="product_table">
            <a class="Mounjaro_button" href="../addons.html"><h2>Continue with <span id="product-name">Wegovy</span> <i class="fa-solid fa-arrow-right"></i> </h2></a>
            <h2>What to expect with <span id="product-name">Wegovy</span></h2>
            <button id="browse-plans">Browse all plans</button>
            <table>
                <tr style="background-color: #e2e2e2;
                display: flex ;
                /* align-items: center; */
                gap: 60px; ">
                    <th>Schedule</th>
                    <th style="margin-left: 100px;" >Dose</th>
                    <th style="margin-left: 55px;">Pre-discount price</th>
                </tr>




                <tr style="background-color: #f6f6f6;
                display: flex ;
                /* align-items: center; */
                gap: 50px; padding: 0;">
                    <td>Weeks 1-4</td>
                    <td style="margin-left: 90px;">0.25mg</td>
                    <td style="margin-left: 55px;">£209</td>
                </tr>



                <tr style="background-color: #eeeeee;
                display: flex ;
                /* align-items: center; */
                gap: 50px; ">
                    <td>Weeks 5-8</td>
                    <td style="margin-left: 80px;">0.5mg</td>
                    <td style="margin-left: 65px;">£209</td>
                </tr>


                <tr style="background-color: #f6f6f6;
                display: flex ;
                /* align-items: center; */
                gap: 50px; ">
                    <td>Weeks 9-12</td>
                    <td style="margin-left: 70px;">1mg</td>
                    <td style="margin-left: 81px;">£209</td>
                </tr>


                <tr style="background-color: #eeeeee;
                display: flex ;
                /* align-items: center; */
                gap: 50px; ">
                    <td>Weeks 9-12</td>
                    <td style="margin-left: 70px;">7.5mg</td>
                    <td style="margin-left: 68px;">£229</td>
                </tr>


                <tr style="background-color: #f6f6f6;
                display: flex ;
                /* align-items: center; */
                gap: 50px; ">
                    <td>Weeks 13-16</td>
                    <td style="margin-left: 60px;">1.7mg</td>
                    <td style="margin-left: 70px;">£249</td>
                </tr>


                <tr style="background-color: #eeeeee;
                display: flex ;
                /* align-items: center; */
                gap: 50px; ">
                    <td>Weeks 17+</td>
                    <td style="margin-left: 70px;">2.4mg</td>
                    <td style="margin-left: 74px;">£299</td>
                </tr>
            </table>
        </div>
        <div id="product-details3" style="display:none" class="product_table">
            <a class="Mounjaro_button" href=""><h2>Continue with <span id="product-name">Mounjaro</span> <i class="fa-solid fa-arrow-right"></i> </h2></a>
            <h2>What to expect with <span id="product-name">Mounjaro</span></h2>
            <button id="browse-plans">Browse all plans</button>
            <table>
                <tr style="background-color: #e2e2e2;
                display: flex ;
                /* align-items: center; */
                gap: 60px; ">
                    <th>Schedule</th>
                    <th style="margin-left: 100px;" >Dose</th>
                    <th style="margin-left: 100px;">Pre-discount price</th>
                </tr>




                <tr style="background-color: #f6f6f6;
                display: flex ;
                /* align-items: center; */
                gap: 50px; padding: 0;">
                    <td>Weeks 1-4</td>
                    <td style="margin-left: 90px;">2.5mg</td>
                    <td style="margin-left: 85px;">£209</td>
                </tr>



                <tr style="background-color: #eeeeee;
                display: flex ;
                /* align-items: center; */
                gap: 50px; ">
                    <td>Weeks 5-8</td>
                    <td style="margin-left: 80px;">5mg</td>
                    <td style="margin-left: 95px;">£209</td>
                </tr>


                <tr style="background-color: #f6f6f6;
                display: flex ;
                /* align-items: center; */
                gap: 50px; ">
                    <td>Weeks 9-12</td>
                    <td style="margin-left: 70px;">7.5mg</td>
                    <td style="margin-left: 85px;">£229</td>
                </tr>


                <tr style="background-color: #eeeeee;
                display: flex ;
                /* align-items: center; */
                gap: 50px; ">
                    <td>Weeks 9-12</td>
                    <td style="margin-left: 70px;">7.5mg</td>
                    <td style="margin-left: 85px;">£229</td>
                </tr>


                <tr style="background-color: #f6f6f6;
                display: flex ;
                /* align-items: center; */
                gap: 50px; ">
                    <td>Weeks 9-12</td>
                    <td style="margin-left: 70px;">7.5mg</td>
                    <td style="margin-left: 85px;">£229</td>
                </tr>


                <tr style="background-color: #eeeeee;
                display: flex ;
                /* align-items: center; */
                gap: 50px; ">
                    <td>Weeks 9-12</td>
                    <td style="margin-left: 70px;">7.5mg</td>
                    <td style="margin-left: 85px;">£229</td>
                </tr>
            </table>
        </div>










            <!--================================================================== Weekly injection section start ============================================================================================================-->


        <section class="Weekly_injection ">

            <div class="containet">
                <div class="weekly">
                    <div class="weekly_content">
                        <div class="weekly_icon">
                            <i class="fa-solid fa-syringe"></i>
                        </div>
                        <div class="weekly_title">
                            <h6>Weekly injection</h6>
                        </div>
                    </div>
                    <div class="weekly_tex">
                        <p>
                            Taken as a once weekly pre-filled injection pen that contains 4 doses.
                        </p>
                    </div>
                </div>


                <div class="weekly">
                    <div class="weekly_content">
                        <div class="weekly_icon">
                            <i class="fa-solid fa-syringe"></i>
                        </div>
                        <div class="weekly_title">
                            <h6>You could lose up to 22% of your weight</h6>
                        </div>
                    </div>
                    <div class="weekly_tex">
                        <p>
                            Clinical trials* have shown up to 22% reduction in body weight when combined with a reduced calorie diet and exercise.
                        </p>
                    </div>
                </div>


                <div class="weekly">
                    <div class="weekly_content">
                        <div class="weekly_icon">
                            <i class="fa-solid fa-syringe"></i>
                        </div>
                        <div class="weekly_title">
                            <h6>Suppresses appetite</h6>
                        </div>
                    </div>
                    <div class="weekly_tex">
                        <p>
                            Contains tirzepatide which works by regulating blood sugar and energy balance levels, helping to reduce appetite and prevent cravings.
                        </p>
                    </div>
                </div>
            </div>


        </section>



        <!--================================================================== Weekly injection section End ============================================================================================================-->




        <!--================================================================== Over 500 section start ============================================================================================================-->

        <section class="over_500">

            <div class="container">

                <div class="over_500">

                    <div class="over_title">
                        <h4>Over 500,000 Brits choose getweightloss for amazing clinical care and support</h4>
                    </div>
                    <div class="over_start">
                        <h6>Start today from just <span>£4.46</span> / day</h6>
                    </div>
                    <a href="">
                        <button>Continue with Mounjaro <i class="fa-solid fa-arrow-right"></i> </button>
                    </a>
                    <div class="over_weight">
                        <div class="regulated">
                            <span>Getweightloss is regulated by:</span>
                        </div>
                        <div class="weight_image">
                            <img src="asset/weight.png" alt="">
                            <img src="asset/CER logo.png" alt="">
                        </div>
                    </div>

                </div>

            </div>


        </section>



        <!--================================================================== Over 500 section End ============================================================================================================-->



        <!--================================================================== Frequently asked questions section End ============================================================================================================-->


        <section class="frequently">

            <div class="container">

                <div class="frequently_content">
                    <div class="frequently_title">

                        <h4>Frequently asked questions</h4>

                    </div>
                    <div class="frequently_questions">

                        <div class="accordion" id="accordionPanelsStayOpenExample">
                            <div class="accordion-item">
                            <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                                    Who is the programme suitable for?
                                </button>
                            </h2>
                            <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                                <div class="accordion-body">
                                    The Weight Loss Programme is designed to help people who are overweight achieve a healthier weight. Our clinicians will assess your suitability for the programme on a case-by-case basis.
                                </div>
                            </div>
                            </div>
                            <div class="accordion-item">
                            <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                                    What does the programme involve?

                                </button>
                            </h2>
                            <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
                                <div class="accordion-body">
                                    The  Weight Loss Programme combines ongoing clinical support and expert-led advice with weight loss medication. By addressing the key areas of weight, you’ll have the tools to conquer sustainable weight loss.
                                </div>
                            </div>
                            </div>


                            <div class="accordion-item">
                            <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                                    Are weight loss medications safe to use long-term?
                                </button>
                            </h2>
                            <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
                                <div class="accordion-body">
                                    As a healthcare provider, we hold ourselves to the highest standards of quality and safety. All of our medications have been thoroughly researched and are evidence-based. <br> <br>

                                    Semaglutide has been approved for the long-term treatment of obesity. These trials also demonstrated that the medication reduced the risk of adverse cardiovascular events, including a heart attack or stroke.

                                    <br> <br>

                                    Orlistat has been licensed for weight loss in adults for over 20 years. In addition to weight loss, studies also show some other health benefits such as reduction in cholesterol and a decrease in waist circumference.

                                    <br> <br>

                                    Our clinicians will work with you to define the optimum time for you to stay on the prescribed medication based on your individual circumstances.
                                </div>
                            </div>
                            </div>


                            <div class="accordion-item">
                            <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                                    What are the potential side effects of weight loss medication
                                </button>
                            </h2>
                            <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
                                <div class="accordion-body">
                                    The most common side effects of GLP-1 medications are gastrointestinal including nausea, diarrhoea, stomach pain, and constipation. These side effects usually subside as your body adjusts to the medication. For a full list of side effects, including the less common ones, always read the patient information leaflet.

                                    <br> <br>

                                    The most common side effects of orlistat medications are wind (flatulence) with or without oily spotting, an urgent or increased need to defecate, fatty or oily stool consistency, soft stools, gastric discomfort and faecal incontinence. For a full list of side effects, including the less common ones, always read the patient information leaflet.

                                </div>
                            </div>
                            </div>


                            <div class="accordion-item">
                            <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                                    How long does it take to see results?
                                </button>
                            </h2>
                            <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
                                <div class="accordion-body">
                                    Results vary depending on each individual but healthy weight loss is gradual. Sustained weight loss is one of the biggest challenges when it comes to achieving weight goals which is why GetWeightLoss’s programme incorporates behaviour change. The programme focuses on key areas of health, including nutrition, exercise, and mindset.

                                </div>
                            </div>
                            </div>


                            <div class="accordion-item">
                            <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                                    How much does the weight loss programme cost
                                </button>
                            </h2>
                            <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
                                <div class="accordion-body">
                                    We offer a subscription service for our weight loss programme. You can expect to receive a delivery every 28 days.

                                    <br> <br>

                                    The price of our programme varies based on the type of medication you are taking, and the strength of the dose. The market price of the higher strength pens of Mounjaro and Semaglutide is higher than the lower strength pens.

                                    <br> <br>

                                    The following prices are per month, and exclude potential discounts:

                                    <br><br>

                                </div>
                            </div>
                            </div>



                        </div>

                    </div>
                </div>

            </div>

        </section>
    </div>

    <!-- Side Panel -->
    <div id="side-panel">
        <button style="display: inline-block; float: right;" id="close-panel">✖</button>
        <h4>Mounjaro</h4><br>


        <h6>
            What is Mounjaro?

        </h6>
        <br>
        <p>
            Mounjaro, containing the active ingredient tirzepatide, is a proven treatment which works by curbing appetite and aiding healthy weight loss. Tirzepatide works by mimicking the effects of two hormones that play a role in hunger and energy balance. This activates signals through the brain which make you feel full, decreasing how much you eat and leading to weight loss.

        </p>
        <br><br>
        <h6>Dosage</h6>
        <p>
            The dose of Mounjaro is gradually increased to reduce the risk of side effects and allow the body to adjust. Mounjaro is available in 6 different strengths: 2.5mg, 5mg , 7.5mg, 10mg, 12.5mg, and 15mg. Typically, patients start with 2.5mg weekly in the first month and increase by 2.5mg every month until the maintenance dose is reached. The maintenance dose is typically 15mg, but our clinicians will determine which is the most safe and effective dose for you.
        </p>


        <h6 >How to take Mounjaro</h6>
        <p>
            Mounjaro is administered by a pen injection once a week. It’s a subcutaneous (under the skin) injection in your stomach, thigh, or upper arm. <br>
            <br>
            It’s fairly straightforward to administer Mounjaro yourself, although your clinician or coach will guide you through the process.
        </p>



        <h6 >What are the side effects?</h6>
        <p>
            As with any medication, there are a few side effects to be aware of. The most common are nausea and diarrhoea, which affect around 12% of patients. Other less common side effects include vomiting, constipation, stomach pain, and indigestion.<br>
            <br>
            Patients mostly experience side effects when they first start taking Mounjaro, or when they increase their dose. This is normal, and any side effects usually get better once your body gets used to the medication. Your clinician will gradually increase your dose to reduce the risk of these side effects.
        </p>



        <h6 >Expected weight loss</h6>
        <p>
            Clinical trials have proven the safety and efficacy of Mounjaro. One study* found that together with a balanced diet and regular exercise, it led to an average 22% weight loss in patients. This makes it one of the most effective treatments for obesity on the market.
            <br>
            <br>
            *Based on 15mg tirzepatide over 72 weeks. Jastreboff, Aronne, et al. “Tirzepatide Once Weekly for the Treatment of Obesity.” New England Journal of Medicine (2022).
        </p>

        <span>Full safety information and instructions are provided with your medication, which you should read thoroughly before use.</span>



    </div>

    <div id="overlay"></div>



    <!--================================================================== Frequently asked questions section End ============================================================================================================-->





    <!-- ==================================================================coding End======================================================================================================== -->






    <script type="text/javascript" src="js/jquery-3.7.1.min.js"></script>
    <script type="text/javascript" src="js/popper.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/slick.min.js"></script>
    <script type="text/javascript" src="js/lazyload.min.js"></script>
    <script type="text/javascript" src="js/theme.js"></script>
    <script src="js/my.js"></script>



    <!-- product section Start-->
    <script>
       document.addEventListener("DOMContentLoaded", function () {
    const productsContainer = document.getElementById("product-selection");
    const products = document.querySelectorAll(".product");
    const productDetails1 = document.getElementById("product-details1");
    const productDetails2 = document.getElementById("product-details2");
    const productDetails3 = document.getElementById("product-details3");
    const browsePlansBtns = document.querySelectorAll("#browse-plans"); // সব browse-plans বাটন সিলেক্ট করা হবে
    const sidePanel = document.getElementById("side-panel");
    const closePanel = document.getElementById("close-panel");
    const overlay = document.getElementById("overlay");

    // প্রোডাক্ট সেটআপ ফাংশন
    function setActiveProduct(productKey) {
        document.querySelectorAll(".product").forEach(p => p.classList.remove("active"));
        document.querySelector(`[data-product="${productKey}"]`)?.classList.add("active");

        productDetails1.style.display = productKey === "mounjaro" ? "block" : "none";
        productDetails2.style.display = productKey === "wegovy" ? "block" : "none";
        productDetails3.style.display = productKey === "alli" ? "block" : "none";
    }

    // ডিফল্টভাবে "mounjaro" সিলেক্ট করা হবে
    setActiveProduct("mounjaro");

    function handleProductClick() {
        setActiveProduct(this.dataset.product);
    }

    products.forEach(product => {
        product.addEventListener("click", handleProductClick);
    });

    // Browse All Plans ক্লিক করলে Alli প্রোডাক্ট যোগ হবে
    function addAlliProduct() {
        if (!document.querySelector("[data-product='alli']")) {
            const alliProduct = document.createElement("div");
            alliProduct.classList.add("product", "d-flex");
            alliProduct.dataset.product = "alli";
            alliProduct.innerHTML = `
                <img src="asset/alli.png" alt="Alli">
                <div class="product_text">
                    <h6>Up to 10% weight loss</h6>
                    <h3>Alli</h3>
                    <p class="d-inline-block"><del>£209.00</del> </p> <span><strong>£125.00</strong></span>
                    <p>Contains semaglutide, which mimics a hormone responsible for stimulating insulin secretion and slowing stomach emptying to promote satiety.</p>
                    <a href="#" class="learn-more">Learn more</a>
                </div>
            `;

            productsContainer.appendChild(alliProduct);

            alliProduct.addEventListener("click", function () {
                setActiveProduct("alli");
            });

            alliProduct.querySelector(".learn-more").addEventListener("click", function (e) {
                e.preventDefault();
                sidePanel.classList.add("active");
                overlay.classList.add("active");
            });

            // Browse all plans বাটন হাইড করা হবে
            browsePlansBtns.forEach(btn => btn.style.display = "none");
        }
    }

    // সব browse-plans বাটনে ইভেন্ট লিস্টেনার যোগ করা হলো
    browsePlansBtns.forEach(btn => {
        btn.addEventListener("click", addAlliProduct);
    });

    // "Learn more" ক্লিক করলে সাইড প্যানেল ওপেন হবে
    document.querySelectorAll(".learn-more").forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            sidePanel.classList.add("active");
            overlay.classList.add("active");
        });
    });

    // সাইড প্যানেল বন্ধ করার ইভেন্ট
    closePanel.addEventListener("click", function () {
        sidePanel.classList.remove("active");
        overlay.classList.remove("active");
    });

    overlay.addEventListener("click", function () {
        sidePanel.classList.remove("active");
        overlay.classList.remove("active");
    });
});

    </script>

    <!-- product section End-->



    <script>

        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById("ethnicityForm");
            const textarea = document.getElementById("ethnicity");
            const errorMessage = document.getElementById("error-message");

            form.addEventListener("submit", function (event) {
                if (textarea.value.trim() === "") {
                    event.preventDefault(); // Prevent form submission
                    textarea.classList.add("error-border");
                    errorMessage.style.display = "block";
                } else {
                    textarea.classList.remove("error-border");
                    errorMessage.style.display = "none";
                }
            });

            textarea.addEventListener("input", function () {
                if (textarea.value.trim() !== "") {
                    textarea.classList.remove("error-border");
                    errorMessage.style.display = "none";
                }
            });
        });



    </script>





</body>

</html>
