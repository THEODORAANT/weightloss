<?php
    perch_layout('client/header', [
        'page_title' => perch_page_title(true),
    ]);

if (perch_member_logged_in()) {
    $order_id = isset($_GET['id']) ? trim($_GET['id']) : null;
?>
    <section class="client-order py-5">
        <style>
            .client-order {
                background: #f4f6fb;
                min-height: 100vh;
            }

            .client-order__intro {
                max-width: 640px;
                margin: 0 auto 2.5rem;
                text-align: center;
            }

            .client-order__intro h1 {
                font-size: clamp(1.75rem, 2.5vw + 1.5rem, 2.75rem);
                font-weight: 700;
                margin-bottom: 0.75rem;
            }

            .client-order__intro p {
                color: #5f6b7d;
                margin-bottom: 0;
            }

            .order-card,
            .tracking-card,
            .client-order__empty {
                background: #fff;
                border-radius: 18px;
                box-shadow: 0 18px 45px rgba(15, 35, 95, 0.08);
                border: 1px solid rgba(15, 35, 95, 0.05);
            }

            .order-card .card-header,
            .tracking-card .card-header {
                background: transparent;
                border-bottom: none;
                padding-bottom: 0;
            }

            .order-card .card-header h3,
            .tracking-card .card-header h3 {
                font-size: 1.1rem;
                font-weight: 600;
                margin-bottom: 0.25rem;
            }

            .order-card .card-header p,
            .tracking-card .card-header p {
                font-size: 0.9rem;
                color: #6c7a91;
                margin-bottom: 0;
            }

            .order-summary-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 1rem;
            }

            .order-summary-grid__item {
                background: #f8f9fb;
                border-radius: 14px;
                padding: 0.9rem 1rem;
                display: flex;
                flex-direction: column;
                gap: 0.35rem;
            }

            .order-summary-grid__label {
                font-size: 0.8rem;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                color: #6c7a91;
            }

            .order-summary-grid__value {
                font-size: 1rem;
                font-weight: 600;
                color: #152045;
                word-break: break-word;
            }

            .order-items {
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
            }

            .order-item {
                border: 1px solid rgba(21, 32, 69, 0.06);
                border-radius: 14px;
                padding: 0.85rem 1rem;
                background: #fdfdff;
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                gap: 0.75rem;
            }

            .order-item__label {
                font-size: 0.8rem;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                color: #6c7a91;
                margin-bottom: 0.25rem;
            }

            .order-item__value {
                font-size: 1.05rem;
                font-weight: 600;
                color: #152045;
            }

            .tracking-card__grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 1rem;
            }

            .tracking-card__item {
                padding: 0.9rem 1rem;
                background: #f8f9fb;
                border-radius: 14px;
            }

            .tracking-card__item strong {
                display: block;
                font-size: 0.85rem;
                letter-spacing: 0.04em;
                text-transform: uppercase;
                color: #6c7a91;
                margin-bottom: 0.35rem;
            }

            .tracking-card__item span {
                font-size: 1.05rem;
                font-weight: 600;
                color: #152045;
                word-break: break-word;
            }

            .client-order__empty {
                padding: 2.5rem 2rem;
                text-align: center;
            }

            .client-order__empty h3 {
                font-weight: 600;
                margin-bottom: 0.75rem;
            }

            .client-order__empty p {
                color: #6c7a91;
                margin-bottom: 1.5rem;
            }

            .client-order__actions {
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
                align-items: center;
            }

            .client-order__actions .btn {
                border-radius: 999px;
                padding: 0.65rem 1.5rem;
            }

            @media (max-width: 575px) {
                .client-order {
                    padding-top: 2rem !important;
                }

                .order-card,
                .tracking-card,
                .client-order__empty {
                    border-radius: 14px;
                }
            }
        </style>

        <div class="container">
            <?php if ($order_id) { ?>
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-11 col-xl-9">
                        <div class="client-order__intro">
                         <?php if (isset($_GET["success"])) {
                                    perch_shop_empty_cart();
                                ?>
                                  <span class="client-documents__eyebrow">Payment complete</span>
                              <?php  } ?>
                            <p class="text-uppercase text-muted small mb-2">Order reference</p>
                            <h1 class="mb-2">#<?php echo htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8'); ?></h1>
                            <p>Below you'll find a clear breakdown of your order and the latest delivery information.</p>
                        </div>

                        <div class="card order-card mb-4">
                            <div class="card-header">
                                <h3>Order summary</h3>
                                <p>Key information about this purchase at a glance.</p>
                            </div>
                            <div class="card-body">
                                <div class="order-summary">
                                    <?php perch_shop_order($order_id); ?>
                                </div>

                                <hr class="my-4" />

                                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                    <h4 class="h6 fw-semibold mb-0">Items in this order</h4>
                                    <a class="text-decoration-none small fw-semibold" href="/client/orders">View all orders</a>
                                </div>

                                <div class="order-items">
                                    <?php perch_shop_order_items($order_id); ?>
                                </div>
                            </div>
                        </div>

                        <div class="card tracking-card mb-4">
                            <div class="card-header">
                                <h3>Shipping &amp; tracking</h3>
                                <p>Stay informed about where your package is in its journey.</p>
                            </div>
                            <div class="card-body">
                                <?php
                                $tracking = perch_shop_track_order($order_id);
                                if ($tracking) {
                                    $status = isset($tracking['status']) && $tracking['status'] !== '' ? $tracking['status'] : '-';
                                    $dispatch = isset($tracking['dispatchDate']) && $tracking['dispatchDate'] !== '' ? $tracking['dispatchDate'] : '-';
                                    $tracking_no = isset($tracking['trackingNo']) && $tracking['trackingNo'] !== '' ? $tracking['trackingNo'] : '-';
                                ?>
                                    <div class="tracking-card__grid">
                                        <div class="tracking-card__item">
                                            <strong>Status</strong>
                                            <span><?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?></span>
                                        </div>
                                        <div class="tracking-card__item">
                                            <strong>Dispatch date</strong>
                                            <span><?php echo htmlspecialchars($dispatch, ENT_QUOTES, 'UTF-8'); ?></span>
                                        </div>
                                        <div class="tracking-card__item">
                                            <strong>Tracking number</strong>
                                            <span><?php echo htmlspecialchars($tracking_no, ENT_QUOTES, 'UTF-8'); ?></span>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div class="client-order__empty bg-transparent shadow-none border-0 p-0 text-start">
                                        <h3 class="h6 fw-semibold">Tracking updates are on their way</h3>
                                        <p class="mb-3">We're waiting on the courier to provide tracking details. Check back soon or contact us if you need help.</p>
                                        <a class="btn btn-outline-primary btn-sm" href="mailto:support@getweightloss.co.uk">Contact support</a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="client-order__empty">
                            <h3>Need a hand?</h3>
                            <p>If anything looks incorrect or you have questions about your treatment plan, we're here to assist.</p>
                            <div class="client-order__actions">
                                <a class="btn btn-primary" href="mailto:support@getweightloss.co.uk">Email our support team</a>
                                <a class="btn btn-outline-secondary" href="/client/orders">Back to all orders</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 col-lg-8">
                        <div class="client-order__empty">
                            <h3>Choose an order to view</h3>
                            <p>We couldn't find an order reference in your link. Select an order from your history to see the details.</p>
                            <div class="client-order__actions">
                                <a class="btn btn-primary" href="/client/orders">Go to my orders</a>
                                <a class="btn btn-outline-secondary" href="/client">Back to profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>

<?php
} else {
?>
    <section class="client-order py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-7">
                    <div class="client-order__empty">
                        <h3>Please sign in to continue</h3>
                        <p>You need to be logged in to view your order history and tracking updates.</p>
                        <div class="client-order__actions">
                            <a class="btn btn-primary" href="/client/login">Log into my account</a>
                            <a class="btn btn-outline-secondary" href="/client/forgot-password">Forgot your password?</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
}
?>

<?php
perch_layout('getStarted/footer');
?>
