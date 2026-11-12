<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'AfroTouch' ?></title>
    <link rel="stylesheet" href="/AFROTOUCH/public/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <?php include __DIR__ . '/../layouts/header.php'; ?>
    
    <main>
        <?= $content ?? '' ?>
    </main>
    
    <?php include __DIR__ . '/../layouts/footer.php'; ?>
    <script>lucide.createIcons();</script>
</body>
</html>
