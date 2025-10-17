<?php
// Output the top of the page
perch_layout('client/header', [
    'page_title' => perch_page_title(true),
]);
?>

<section class="main_order_summary">
    <div class="container mt-5">
        <div class="row">
            <!-- Left Section -->
            <div class="col-md-7">
                <div class="main_page">
                    <!-- Create an Account Section -->
                    <div class="login_sec"></div>

                    <?php
                    if (perch_member_logged_in()) {
                        if (!perch_member_get('affID')) {
                            echo "<a href='/client' class='btn btn-primary w-100'>Become an Affiliate</a><span id='tooltip' class='tooltip'>Copied!</span>";
                        } else {
                            $affiliateLink = "https://" . $_SERVER['HTTP_HOST'] . "/?ref=" . perch_member_get('affID');
                            $credit = perch_member_credit(); // Added missing credit definition
                            $payouts = perch_member_aff_payouts();
                    ?>

                    <div class="affiliate-box">
                        <div class="affiliate-title">Your Affiliate Link</div>
                        <div class="affiliate-link-container">
                            <a id="affiliateLink" class="affiliate-link" href="<?php echo $affiliateLink; ?>" target="_blank">
                                <?php echo $affiliateLink; ?>
                            </a>
                            <button class="copy-button" onclick="copyAffiliateLink()">Copy</button>
                        </div>
                    </div>

                    <?php
                            // Handle payout request
                            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_payout'])) {

                                $result = perch_member_requestPayout();
                               // echo $result["status"];

                              //  print_r($result);
                            }

                            /*echo "<p> Referral ID: <b><span>" . perch_member_get('affID') . "</span></b></p>";*/
                    ?>

                    <div class="affiliate-box">
                        <div class="affiliate-title"> <h2>Your Payouts</h2></div>
                        <div class="affiliate-link-container">

                    <p><strong>Available Credit:</strong> £<?= number_format($credit, 2) ?></p>
<?php if($credit!=0){?>
                    <form method="post">
                        <button style="margin-left: 49px;" type="submit" class="copy-button" name="request_payout">Request Payout</button>
                    </form>
<?php } ?>
                    </div>
                    </div>
                    <table class='commission-table' border="1" cellpadding="6">
                        <thead>
                            <tr><th>Date</th><th>Amount</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            <?php if ($payouts): foreach ($payouts as $p): ?>
                            <tr>
                                <td><?= date("Y-m-d", strtotime($p['requested_at'])) ?></td>
                                <td>£<?= number_format($p['amount'], 2) ?></td>
                                <td><?= ucfirst($p['status']) ?></td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="3">No payouts yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <?php
                            /*
                            $commissions = perch_member_commissions();
                            if ($commissions) {
                                echo "<h2>Your Commissions</h2>";
                                echo "<table class='commission-table'>";
                                echo "<tr><th>Member ID</th><th>Tier</th><th>Amount</th><th>Date</th></tr>";
                                foreach ($commissions as $c) {
                                    echo "<tr>
                                            <td>{$c['member_id']}</td>
                                            <td>{$c['tier']}</td>
                                            <td>£" . number_format($c['amount'], 2) . "</td>
                                            <td>{$c['created_at']}</td>
                                          </tr>";
                                }
                                echo "</table>";
                            } else {
                                echo "<h2>No Commissions</h2>";
                            }
                            */
                        }
                    } // END perch_member_logged_in()
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .affiliate-box {
        max-width: 600px;
        margin: 50px auto;
        background-color: #f9f9f9;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        font-family: Arial, sans-serif;
    }
    .affiliate-title {
        font-size: 1.4rem;
        margin-bottom: 10px;
        font-weight: bold;
        color: #333;
    }
    .affiliate-link-container {
        display: flex;
        align-items: center;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 10px;
        overflow: hidden;
    }
    .affiliate-link {
        flex-grow: 1;
        word-break: break-all;
        color: #0066cc;
        text-decoration: none;
    }
    .copy-button {
        margin-left: 10px;
        padding: 8px 12px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .copy-button:hover {
        background-color: #45a049;
    }
    .tooltip {
        visibility: hidden;
        background-color: #333;
        color: #fff;
        text-align: center;
        border-radius: 4px;
        padding: 4px 8px;
        position: absolute;
        z-index: 1;
        top: -35px;
        left: 50%;
        transform: translateX(-50%);
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 12px;
    }
    .tooltip.show {
        visibility: visible;
        opacity: 1;
    }
    .commission-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-family: Arial, sans-serif;
        font-size: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .commission-table th, .commission-table td {
        padding: 12px 15px;
        border: 1px solid #ddd;
        text-align: center;
    }
    .commission-table th {
        background-color: #f4f4f4;
        color: #333;
        font-weight: bold;
    }
    .commission-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .commission-table tr:hover {
        background-color: #f1f1f1;
    }
    h2 {
        font-family: Arial, sans-serif;
        color: #333;
    }
</style>

<script>
function copyAffiliateLink() {
    const link = document.getElementById("affiliateLink").href;
    navigator.clipboard.writeText(link).then(() => {
        alert("Affiliate link copied to clipboard!");
    });
}
</script>

<?php perch_layout('getStarted/footer'); ?>
