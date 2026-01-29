<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <?php if(isset($extra_css)) echo $extra_css; ?>
</head>
<body>
    <?php
    // Display flash messages
    $flash = getFlashMessage();
    if ($flash):
    ?>
    <div class="flash-message flash-<?php echo $flash['type']; ?>" id="flashMessage">
        <?php echo escape($flash['message']); ?>
    </div>
    <?php endif; ?>
