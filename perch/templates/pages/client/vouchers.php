<?php
perch_layout('client/header', [
    'page_title' => perch_page_title(true),
]);
?>

<section class="client-page">
  <div class="container all_content">
    <div class="client-hero">
      <h1>Unused vouchers</h1>
      <p>Review voucher details including code, amount and validity before you use them at checkout.</p>
    </div>

    <div class="row justify-content-center g-4">
      <div class="col-xl-10 col-lg-11">
        <div class="client-card">
          <?php if (!perch_member_logged_in()) { ?>
            <div class="client-card__section text-center">
              <h2 class="client-card__title">Log in to view your vouchers</h2>
              <p class="client-card__intro">Sign in to see all unused vouchers linked to your account.</p>
              <a class="btn btn-primary px-4" href="/client">Go to client login</a>
            </div>
          <?php } else if (!perch_member_get('affID')) { ?>
            <div class="client-card__section text-center">
              <h2 class="client-card__title">No vouchers available</h2>
              <p class="client-card__intro">Unused vouchers are created from affiliate credit. Activate your affiliate account to generate vouchers from commission credit.</p>
              <a href="/client/affiliate-dashboard" class="btn btn-primary px-4">Go to affiliate dashboard</a>
            </div>
          <?php } else { ?>
            <?php $vouchers = perch_member_unused_vouchers(); ?>

            <div class="client-card__section">
              <h2 class="client-card__title">Voucher details</h2>
              <p class="client-card__intro">These vouchers have not been used in an order yet.</p>

              <div class="client-panel p-3">
                <div class="table-responsive">
                  <table class="client-table">
                    <thead>
                      <tr>
                        <th scope="col">Code</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Status</th>
                        <th scope="col">Valid from</th>
                        <th scope="col">Valid to</th>
                        <th scope="col">Details</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (!empty($vouchers)) { ?>
                        <?php foreach ($vouchers as $voucher) { ?>
                          <tr>
                            <td><?php echo htmlspecialchars((string)($voucher['code'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                              <?php
                                $amount = isset($voucher['amount']) ? (float)$voucher['amount'] : null;
                                echo $amount !== null ? '£' . number_format($amount, 2) : '-';
                              ?>
                            </td>
                            <td><?php echo htmlspecialchars((string)($voucher['status'] ?? 'Active'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                              <?php
                                $validFrom = (string)($voucher['valid_from'] ?? '');
                                echo $validFrom ? date('d M Y', strtotime($validFrom)) : '-';
                              ?>
                            </td>
                            <td>
                              <?php
                                $validTo = (string)($voucher['valid_to'] ?? '');
                                echo $validTo ? date('d M Y', strtotime($validTo)) : '-';
                              ?>
                            </td>
                            <td><?php echo htmlspecialchars((string)($voucher['title'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                          </tr>
                        <?php } ?>
                      <?php } else { ?>
                        <tr>
                          <td colspan="6">No unused vouchers found yet. Convert affiliate credit to a coupon to see it here.</td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php perch_layout('getStarted/footer'); ?>
