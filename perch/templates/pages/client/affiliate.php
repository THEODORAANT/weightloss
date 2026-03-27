<?php
perch_layout('client/header', [
    'page_title' => perch_page_title(true),
]);

$couponResult = null;
if (perch_member_logged_in() && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['convert_credit_to_coupon'])) {
    $couponResult = perch_member_convert_credit_to_coupon();
}

?>

<section class="client-page">
  <div class="container all_content">
    <div class="client-hero">
      <h1>Affiliate dashboard</h1>
      <p>Share Get Weight Loss with your community and earn commission on every successful referral. Track your link and payouts below.</p>
    </div>

    <div class="row justify-content-center g-4">
      <div class="col-xl-8 col-lg-10">
        <div class="client-card">
          <?php if (!perch_member_logged_in()) { ?>
            <div class="client-card__section text-center">
              <h2 class="client-card__title">Log in to access affiliate tools</h2>
              <p class="client-card__intro">Sign in to generate your referral link and monitor your commission payouts.</p>
              <div class="client-actions justify-content-center">
                <a class="btn btn-primary px-4" href="/client">Go to client login</a>
              </div>
            </div>
          <?php } else if (!perch_member_get('affID')) { ?>
            <div class="client-card__section text-center">
              <h2 class="client-card__title">Become an affiliate</h2>
              <p class="client-card__intro">Activate your affiliate account to receive a personalised referral link and start earning commission for every successful sign-up.</p>
              <a href="/client" class="btn btn-primary px-4">Activate affiliate account</a>
            </div>
          <?php } else { ?>
            <?php
              $affiliateLink = 'https://' . $_SERVER['HTTP_HOST'] . '/?ref=' . perch_member_get('affID');
              $credit = perch_member_credit();
              $referrals = perch_member_aff_referrals();
            ?>
            <div class="client-card__section">
              <h2 class="client-card__title">Your referral link</h2>
              <p class="client-card__intro">Share this link with friends or clients. When they complete their consultation, we&apos;ll attribute the commission to you.</p>
              <div class="client-panel p-3">
                <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">
                  <a id="affiliateLink" class="text-decoration-none fw-semibold" href="<?php echo $affiliateLink; ?>" target="_blank" rel="noopener">
                    <?php echo $affiliateLink; ?>
                  </a>
                  <button class="btn btn-outline-primary px-4" type="button" onclick="copyAffiliateLink()">Copy link</button>
                </div>
                <span id="tooltip" class="d-inline-block mt-2 text-success" style="display:none;">Link copied!</span>
              </div>
            </div>

            <div class="client-card__section">
              <h2 class="client-card__title">Referrals</h2>
              <p class="client-card__intro">Track who you&apos;ve referred and how many orders each person has placed.</p>
              <div class="client-panel p-3">
                <div class="table-responsive">
                  <table class="client-table">
                    <thead>
                      <tr>
                        <th scope="col">Referred user</th>
                        <th scope="col">Orders</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if ($referrals) { ?>
                        <?php foreach ($referrals as $referral) { ?>
                          <tr>
                            <td>
                              <?php
                                $displayName = $referral['name'] ?: 'Member #' . $referral['member_id'];
                                echo htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8');
                              ?>
                            </td>
                            <td><?php echo (int) $referral['orders']; ?></td>
                          </tr>
                        <?php } ?>
                      <?php } else { ?>
                        <tr>
                          <td colspan="2">No referrals yet. Your referred clients will appear here once they place orders.</td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="client-card__section">
              <h2 class="client-card__title">Credit to coupon</h2>
              <p class="client-card__intro">Convert your available affiliate credit into a one-time discount coupon you can apply at checkout.</p>
              <div class="client-panel__body">
                <div class="client-panel p-3">
                  <?php if (is_array($couponResult) && !empty($couponResult['status'])) { ?>
                    <div class="alert <?php echo !empty($couponResult['ok']) ? 'alert-success' : 'alert-warning'; ?> mb-3" role="alert">
                      <?php echo htmlspecialchars($couponResult['status'], ENT_QUOTES, 'UTF-8'); ?>
                      <?php if (!empty($couponResult['coupon_code'])) { ?>
                        <div class="mt-2"><strong>Coupon code:</strong> <?php echo htmlspecialchars($couponResult['coupon_code'], ENT_QUOTES, 'UTF-8'); ?></div>
                      <?php } ?>
                    </div>
                  <?php } ?>

                  <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                    <div>
                      <span class="text-uppercase text-muted small">Available credit</span>
                      <div class="display-6 fw-semibold mb-0">£<?php echo number_format($credit, 2); ?></div>
                    </div>
                    <?php if ($credit > 0) { ?>
                      <form method="post" class="client-actions">
                        <button type="submit" class="btn btn-primary px-4" name="convert_credit_to_coupon">Convert to coupon</button>
                      </form>
                    <?php } else { ?>
                      <span class="text-muted small">No credit available to convert yet.</span>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
function copyAffiliateLink() {
    const link = document.getElementById('affiliateLink');
    if (!link) return;

    const tooltip = document.getElementById('tooltip');
    navigator.clipboard.writeText(link.textContent.trim()).then(() => {
        if (tooltip) {
            tooltip.style.display = 'inline-block';
            setTimeout(() => {
                tooltip.style.display = 'none';
            }, 2000);
        }
    });
}
</script>

<?php perch_layout('getStarted/footer'); ?>
