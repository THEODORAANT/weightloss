<?php

echo $HTML->title_panel([
    'heading' => $heading1,
    'button'  => [
        'text' => $Lang->get('Edit template'),
        'link' => $API->app_nav() . '/templates/edit/?id=' . $TemplateItem->id(),
        'icon' => 'core/o-edit',
        'priv' => 'perch_mailer.templates.manage',
    ],
], $CurrentUser);

if (isset($message)) {
    echo $message;
}

$active_section = 'templates';
include(__DIR__ . '/../_subnav.php');

echo '<div class="inner">';
echo '<div class="columns">';

echo '<div class="column">';
echo $HTML->heading2($heading2);
echo '<table class="d">';
echo '<tbody>';
echo '<tr><th>' . $Lang->get('Title') . '</th><td>' . $HTML->encode($TemplateItem->templateTitle()) . '</td></tr>';
echo '<tr><th>' . $Lang->get('Slug') . '</th><td>' . $HTML->encode($TemplateItem->templateSlug()) . '</td></tr>';
echo '<tr><th>' . $Lang->get('Subject') . '</th><td>' . $HTML->encode($TemplateItem->templateSubject()) . '</td></tr>';
echo '<tr><th>' . $Lang->get('From name') . '</th><td>' . $HTML->encode($TemplateItem->templateFromName()) . '</td></tr>';
echo '<tr><th>' . $Lang->get('From email') . '</th><td>' . $HTML->encode($TemplateItem->templateFromEmail()) . '</td></tr>';
echo '<tr><th>' . $Lang->get('Updated') . '</th><td>' . $HTML->encode($TemplateItem->templateUpdated()) . '</td></tr>';
echo '</tbody>';
echo '</table>';
echo '</div>';

echo '<div class="column">';
echo $HTML->heading2($Lang->get('HTML body'));
if ($render_html) {
    echo '<div class="panel"><div class="desc">' . $render_html . '</div></div>';
} else {
    echo $HTML->warning_message($Lang->get('No HTML content set.'));
}
echo '</div>';

echo '</div>'; // columns

echo '<div class="columns">';

echo '<div class="column">';
echo $HTML->heading2($Lang->get('Plain text body'));
if ($render_plain) {
    echo '<pre class="code">' . $HTML->encode($render_plain) . '</pre>';
} else {
    echo $HTML->warning_message($Lang->get('No plain text content set.'));
}
echo '</div>';

echo '</div>'; // columns
echo '</div>'; // inner
