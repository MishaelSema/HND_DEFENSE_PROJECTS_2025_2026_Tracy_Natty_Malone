<?php
$page_title = $page_title ?? 'Page';
?>

<div class="static-page">
    <div class="static-container">
        <h1><?= $page_title ?></h1>
        
        <div class="static-content">
            <?= $content ?? '' ?>
        </div>
    </div>
</div>

<style>
.static-page {
    max-width: 800px;
    margin: 0 auto;
    padding: 60px 20px;
}

.static-container h1 {
    font-size: 36px;
    margin-bottom: 40px;
    color: #241611;
}

.static-content {
    font-size: 16px;
    line-height: 1.8;
    color: #333;
}

.static-content h2 {
    font-size: 24px;
    margin: 30px 0 15px;
    color: #241611;
}

.static-content h3 {
    font-size: 18px;
    margin: 20px 0 10px;
    color: #241611;
}

.static-content p {
    margin-bottom: 15px;
}

.static-content ul, .static-content ol {
    margin-bottom: 15px;
    padding-left: 25px;
}

.static-content li {
    margin-bottom: 8px;
}

.static-content a {
    color: #D35400;
}

.static-content .highlight-box {
    background: #FAF7F5;
    border-left: 4px solid #D35400;
    padding: 20px;
    margin: 20px 0;
    border-radius: 0 8px 8px 0;
}
</style>
