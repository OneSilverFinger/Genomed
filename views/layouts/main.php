<?php

use app\assets\AppAsset;
use yii\bootstrap5\Html;
use yii\bootstrap5\NavBar;

/** @var yii\web\View $this */
/** @var string $content */

AppAsset::register($this);

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column min-vh-100">
<?php $this->beginBody() ?>

<header>
<?php
NavBar::begin([
    'brandLabel' => Html::encode(Yii::$app->name),
    'brandUrl' => Yii::$app->homeUrl,
    'options' => ['class' => 'navbar navbar-expand-lg navbar-dark bg-dark'],
]);
NavBar::end();
?>
</header>

<main class="flex-grow-1">
    <div class="container py-5">
        <?= $content ?>
    </div>
</main>

<footer class="py-3 bg-light text-muted border-top">
    <div class="container text-center">
        <small>&copy; <?= date('Y') ?> <?= Html::encode(Yii::$app->name) ?></small>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
