<?php
include(__DIR__ . '/../../../../core/inc/api.php');

$API = new PerchAPI(1.0, 'perch_shop');
$DB  = PerchDB::fetch();

$orders_table    = PERCH_DB_PREFIX . 'shop_orders';
$customers_table = PERCH_DB_PREFIX . 'shop_customers';

$OrderStatuses = new PerchShop_OrderStatuses($API);
$paid_statuses = $OrderStatuses->get_status_and_above('paid');

if (count($paid_statuses)) {
    $order_status_condition = 'orderStatus IN (' . $DB->implode_for_sql_in($paid_statuses) . ')';
} else {
    $order_status_condition = 'orderStatus = ' . $DB->pdb('paid');
}

$Currencies = new PerchShop_Currencies($API);
$Currency   = $Currencies->get_default();

$format_currency = function ($amount) use ($Currency) {
    $amount = (float)$amount;

    if ($Currency instanceof PerchShop_Currency) {
        return $Currency->format_display($amount);
    }

    return number_format($amount, 2);
};

$currency_symbol_before       = '';
$currency_symbol_after        = '';
$currency_decimals            = 2;
$currency_decimal_separator   = '.';
$currency_thousands_separator = ',';

if ($Currency instanceof PerchShop_Currency) {
    if ($Currency->currencySymbolPosition() === 'before') {
        $currency_symbol_before = $Currency->currencySymbol();
    } else {
        $currency_symbol_after = $Currency->currencySymbol();
    }

    $currency_decimals = (int)$Currency->currencyDecimals();
    $currency_decimal_separator = $Currency->currencyDecimalSeparator();
    $currency_thousands_separator = $Currency->currencyThousandsSeparator();
}

$notDeletedCondition = function ($column) {
    $column = trim($column);
    return sprintf('%1$s IS NULL OR %1$s = "" OR %1$s = "0000-00-00 00:00:00"', $column);
};

$daily_orders_limit = 30;

$daily_orders_sql = sprintf(
    'SELECT DATE(orderCreated) AS period, COUNT(*) AS total_orders'
    . ' FROM %1$s'
    . ' WHERE (%3$s) AND (%2$s)'
    . ' GROUP BY period'
    . ' ORDER BY period DESC'
    . ' LIMIT %4$d',
    $orders_table,
    $notDeletedCondition('orderDeleted'),
    $order_status_condition,
    (int)$daily_orders_limit
);
$daily_orders = $DB->get_rows($daily_orders_sql) ?: [];

$monthly_orders_sql = sprintf(
    'SELECT DATE_FORMAT(orderCreated, "%%Y-%%m") AS period, COUNT(*) AS total_orders'
    . ' FROM %1$s'
    . ' WHERE (%3$s) AND (%2$s)'
    . ' GROUP BY period'
    . ' ORDER BY period DESC',
    $orders_table,
    $notDeletedCondition('orderDeleted'),
    $order_status_condition
);
$monthly_orders = $DB->get_rows($monthly_orders_sql) ?: [];
$monthly_orders_chart = array_reverse($monthly_orders);
$monthly_orders_map = [];
foreach ($monthly_orders_chart as $row) {
    $monthly_orders_map[$row['period']] = (int)$row['total_orders'];
}

$monthly_profits_sql = sprintf(
    'SELECT DATE_FORMAT(orderCreated, "%%Y-%%m") AS period, '
    . 'SUM((COALESCE(CAST(orderTotal AS DECIMAL(18,2)), 0) - COALESCE(CAST(orderTotalRefunded AS DECIMAL(18,2)), 0))) AS total_profit'
    . ' FROM %1$s'
    . ' WHERE (%3$s) AND (%2$s)'
    . ' GROUP BY period'
    . ' ORDER BY period DESC',
    $orders_table,
    $notDeletedCondition('orderDeleted'),
    $order_status_condition
);
$monthly_profits = $DB->get_rows($monthly_profits_sql) ?: [];
$monthly_profits_chart = array_reverse($monthly_profits);
$monthly_profits_map = [];
foreach ($monthly_profits_chart as $row) {
    $monthly_profits_map[$row['period']] = (float)$row['total_profit'];
}

$yearly_orders_sql = sprintf(
    'SELECT DATE_FORMAT(orderCreated, "%%Y") AS period, COUNT(*) AS total_orders'
    . ' FROM %1$s'
    . ' WHERE (%3$s) AND (%2$s)'
    . ' GROUP BY period'
    . ' ORDER BY period DESC',
    $orders_table,
    $notDeletedCondition('orderDeleted'),
    $order_status_condition
);
$yearly_orders = $DB->get_rows($yearly_orders_sql) ?: [];

$yearly_profits_sql = sprintf(
    'SELECT DATE_FORMAT(orderCreated, "%%Y") AS period, '
    . 'SUM((COALESCE(CAST(orderTotal AS DECIMAL(18,2)), 0) - COALESCE(CAST(orderTotalRefunded AS DECIMAL(18,2)), 0))) AS total_profit'
    . ' FROM %1$s'
    . ' WHERE (%3$s) AND (%2$s)'
    . ' GROUP BY period'
    . ' ORDER BY period DESC',
    $orders_table,
    $notDeletedCondition('orderDeleted'),
    $order_status_condition
);
$yearly_profits = $DB->get_rows($yearly_profits_sql) ?: [];

$monthly_conversions_sql = sprintf(
    'SELECT DATE_FORMAT(customerCreated, "%%Y-%%m") AS period, COUNT(*) AS total_conversions'
    . ' FROM %1$s'
    . ' WHERE memberID IS NOT NULL AND memberID <> 0 AND (%2$s)'
    . ' GROUP BY period'
    . ' ORDER BY period DESC',
    $customers_table,
    $notDeletedCondition('customerDeleted')
);
$monthly_conversions = $DB->get_rows($monthly_conversions_sql) ?: [];
$monthly_conversions_chart = array_reverse($monthly_conversions);
$monthly_conversions_map = [];
foreach ($monthly_conversions_chart as $row) {
    $monthly_conversions_map[$row['period']] = (int)$row['total_conversions'];
}

$monthly_chart_labels_map = [];
foreach (array_keys($monthly_orders_map) as $period) {
    $monthly_chart_labels_map[$period] = true;
}
foreach (array_keys($monthly_conversions_map) as $period) {
    $monthly_chart_labels_map[$period] = true;
}
foreach (array_keys($monthly_profits_map) as $period) {
    $monthly_chart_labels_map[$period] = true;
}
$monthly_chart_labels = array_keys($monthly_chart_labels_map);
sort($monthly_chart_labels);

$monthly_orders_values = [];
$monthly_conversions_values = [];
$monthly_profits_values = [];
foreach ($monthly_chart_labels as $period) {
    $monthly_orders_values[] = $monthly_orders_map[$period] ?? 0;
    $monthly_conversions_values[] = $monthly_conversions_map[$period] ?? 0;
    $monthly_profits_values[] = isset($monthly_profits_map[$period])
        ? round((float)$monthly_profits_map[$period], 2)
        : 0;
}

$yearly_conversions_sql = sprintf(
    'SELECT DATE_FORMAT(customerCreated, "%%Y") AS period, COUNT(*) AS total_conversions'
    . ' FROM %1$s'
    . ' WHERE memberID IS NOT NULL AND memberID <> 0 AND (%2$s)'
    . ' GROUP BY period'
    . ' ORDER BY period DESC',
    $customers_table,
    $notDeletedCondition('customerDeleted')
);
$yearly_conversions = $DB->get_rows($yearly_conversions_sql) ?: [];

$orders_total = array_sum(array_map(function ($row) {
    return (int)$row['total_orders'];
}, $yearly_orders));

$profits_total = array_sum(array_map(function ($row) {
    return (float)$row['total_profit'];
}, $yearly_profits));

$total_conversions = array_sum(array_map(function ($row) {
    return (int)$row['total_conversions'];
}, $yearly_conversions));

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Orders &amp; Conversion Report</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
            margin: 24px;
            color: #1f2933;
            background: #f8fafc;
        }
        h1 {
            margin-top: 0;
            font-size: 28px;
        }
        h2 {
            margin-top: 32px;
            font-size: 22px;
        }
        .grid {
            display: grid;
            gap: 24px;
        }
        @media (min-width: 900px) {
            .grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
        }
        thead {
            background: #e2e8f0;
        }
        th, td {
            padding: 12px 16px;
            text-align: left;
        }
        tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .summary {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }
        .summary-card {
            flex: 1 1 200px;
            background: #fff;
            border-radius: 8px;
            padding: 16px 20px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
        }
        .chart-card {
            background: #fff;
            border-radius: 8px;
            padding: 16px 20px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
            margin-bottom: 24px;
        }
        .chart-card h2 {
            margin: 0 0 16px;
        }
        .chart-card canvas {
            width: 100%;
            max-height: 320px;
        }
        .summary-card h3 {
            margin: 0 0 8px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
        }
        .summary-card p {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .empty {
            padding: 16px;
            color: #64748b;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js" crossorigin="anonymous"></script>
</head>
<body>
    <h1>Orders &amp; Conversion Report</h1>
    <div class="summary">
        <div class="summary-card">
            <h3>Total Recorded Orders</h3>
            <p><?= number_format($orders_total) ?></p>
        </div>
        <div class="summary-card">
            <h3>Members Converted to Customers</h3>
            <p><?= number_format($total_conversions) ?></p>
        </div>
        <div class="summary-card">
            <h3>Total Net Profit</h3>
            <p><?= htmlspecialchars($format_currency($profits_total), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    </div>

    <div class="chart-card">
        <h2>Monthly Orders, Conversions &amp; Net Profit Trend</h2>
        <?php if (!empty($monthly_chart_labels)): ?>
            <canvas id="monthly-trends" height="280"></canvas>
        <?php else: ?>
            <div class="empty">No monthly order, conversion, or profit data available to chart.</div>
        <?php endif; ?>
    </div>

    <div class="grid">
        <section>
            <h2>Orders by Day (Last <?= (int)$daily_orders_limit ?> Days)</h2>
            <?php if (!empty($daily_orders)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Total Orders</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($daily_orders as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['period'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= number_format((int)$row['total_orders']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">No daily order records available.</div>
            <?php endif; ?>
        </section>
        <section>
            <h2>Orders by Month</h2>
            <?php if (!empty($monthly_orders)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Total Orders</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($monthly_orders as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['period'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= number_format((int)$row['total_orders']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">No order records available.</div>
            <?php endif; ?>
        </section>
        <section>
            <h2>Orders by Year</h2>
            <?php if (!empty($yearly_orders)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Total Orders</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($yearly_orders as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['period'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= number_format((int)$row['total_orders']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">No order records available.</div>
            <?php endif; ?>
        </section>
        <section>
            <h2>Conversions by Month</h2>
            <?php if (!empty($monthly_conversions)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Converted Members</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($monthly_conversions as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['period'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= number_format((int)$row['total_conversions']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">No conversions recorded.</div>
            <?php endif; ?>
        </section>
        <section>
            <h2>Conversions by Year</h2>
            <?php if (!empty($yearly_conversions)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Converted Members</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($yearly_conversions as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['period'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= number_format((int)$row['total_conversions']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">No conversions recorded.</div>
            <?php endif; ?>
        </section>
        <section>
            <h2>Net Profit by Month</h2>
            <?php if (!empty($monthly_profits)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Net Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($monthly_profits as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['period'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($format_currency((float)($row['total_profit'] ?? 0)), ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">No profit data available.</div>
            <?php endif; ?>
        </section>
        <section>
            <h2>Net Profit by Year</h2>
            <?php if (!empty($yearly_profits)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Net Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($yearly_profits as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['period'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($format_currency((float)($row['total_profit'] ?? 0)), ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">No profit data available.</div>
            <?php endif; ?>
        </section>
    </div>
</body>
<script>
(function () {
    const canvas = document.getElementById('monthly-trends');
    if (!canvas) return;

    const labels = <?= json_encode($monthly_chart_labels) ?>;
    const ordersData = <?= json_encode($monthly_orders_values) ?>;
    const conversionsData = <?= json_encode($monthly_conversions_values) ?>;
    const profitsData = <?= json_encode($monthly_profits_values) ?>;
    const currencyFormatting = <?= json_encode([
        'before' => $currency_symbol_before,
        'after' => $currency_symbol_after,
        'decimals' => $currency_decimals,
        'decimalSeparator' => $currency_decimal_separator,
        'thousandsSeparator' => $currency_thousands_separator,
    ]) ?>;

    const formatCurrency = (value) => {
        if (!currencyFormatting) {
            return Number(value ?? 0).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        }

        const {
            before = '',
            after = '',
            decimals = 2,
            decimalSeparator = '.',
            thousandsSeparator = ',',
        } = currencyFormatting;

        const number = Number(value ?? 0);
        if (!Number.isFinite(number)) {
            return `${before}${after}`.trim();
        }

        const fixed = number.toFixed(decimals);
        const [integerPart, decimalPart = ''] = fixed.split('.');
        const integerWithSep = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSeparator || ',');

        if (decimals === 0) {
            return `${before}${integerWithSep}${after}`;
        }

        return `${before}${integerWithSep}${decimalSeparator || '.'}${decimalPart}${after}`;
    };

    if (!labels.length) {
        return;
    }

    new Chart(canvas, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Orders',
                    data: ordersData,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.15)',
                    tension: 0.3,
                    borderWidth: 2,
                    fill: true,
                    yAxisID: 'y',
                },
                {
                    label: 'Conversions',
                    data: conversionsData,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.15)',
                    tension: 0.3,
                    borderWidth: 2,
                    fill: true,
                    yAxisID: 'y',
                },
                {
                    label: 'Net Profit',
                    data: profitsData,
                    borderColor: '#7c3aed',
                    backgroundColor: 'rgba(124, 58, 237, 0.15)',
                    tension: 0.3,
                    borderWidth: 2,
                    fill: true,
                    yAxisID: 'yProfit',
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        callback(value) {
                            return Number(value ?? 0).toLocaleString();
                        },
                    },
                    title: {
                        display: true,
                        text: 'Orders & Conversions',
                    },
                },
                yProfit: {
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false,
                    },
                    ticks: {
                        callback(value) {
                            return formatCurrency(value);
                        },
                    },
                    title: {
                        display: true,
                        text: 'Net Profit',
                    },
                },
                x: {
                    title: {
                        display: true,
                        text: 'Month',
                    },
                },
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        title(context) {
                            return context[0]?.label ?? '';
                        },
                        label(context) {
                            const datasetLabel = context.dataset?.label ?? '';
                            const value = context.raw ?? 0;

                            if (datasetLabel === 'Net Profit') {
                                return `${datasetLabel}: ${formatCurrency(value)}`;
                            }

                            return `${datasetLabel}: ${Number(value ?? 0).toLocaleString()}`;
                        },
                    },
                },
            },
        },
    });
})();
</script>
</html>
