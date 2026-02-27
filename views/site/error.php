<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */

use yii\bootstrap5\Html;

$this->title = $name;
?>

<div class="row justify-content-center">
    <div class="col-lg-6 text-center">
        <h1 class="display-1 fw-bold text-muted"><?= Html::encode($this->title) ?></h1>
        <p class="lead"><?= nl2br(Html::encode($message)) ?></p>
        <a href="<?= Yii::$app->homeUrl ?>" class="btn btn-primary mt-3">На главную</a>
    </div>
</div>
