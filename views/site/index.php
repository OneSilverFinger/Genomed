<?php

/** @var yii\web\View $this */

use yii\helpers\Url;

$this->title = Yii::$app->name;
?>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold"><?= Yii::$app->name ?></h1>
            <p class="lead text-muted">Вставьте длинный URL и получите короткую ссылку с QR-кодом</p>
        </div>

        <form id="shorten-form" class="mb-4" novalidate>
            <div class="input-group input-group-lg">
                <input type="url"
                       id="url-input"
                       class="form-control"
                       placeholder="https://example.com/very-long-url..."
                       autocomplete="off">
                <button type="submit" class="btn btn-primary px-4" id="btn-shorten">OK</button>
            </div>
            <div id="input-error" class="text-danger mt-2 d-none"></div>
        </form>

        <div id="loader" class="text-center py-4 d-none">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
            <p class="text-muted mt-2">Проверяем доступность URL...</p>
        </div>

        <div id="result" class="d-none"></div>

    </div>
</div>

<?php
$shortenUrl = Url::to(['site/shorten']);
$js = <<<JS
(function() {
    var form = $('#shorten-form');
    var btn = $('#btn-shorten');
    var loader = $('#loader');
    var result = $('#result');
    var inputError = $('#input-error');

    form.on('submit', function(e) {
        e.preventDefault();

        var url = $.trim($('#url-input').val());
        if (!url) {
            showError('Введите URL');
            return;
        }

        btn.prop('disabled', true);
        result.addClass('d-none');
        inputError.addClass('d-none');
        loader.removeClass('d-none');

        $.ajax({
            url: '{$shortenUrl}',
            method: 'POST',
            data: {url: url},
            dataType: 'json',
            success: function(resp) {
                loader.addClass('d-none');
                btn.prop('disabled', false);

                if (resp.success) {
                    showResult(resp);
                } else {
                    var msgs = [];
                    $.each(resp.errors, function(_, msg) { msgs.push(msg); });
                    showError(msgs.join('<br>'));
                }
            },
            error: function() {
                loader.addClass('d-none');
                btn.prop('disabled', false);
                showError('Произошла ошибка. Попробуйте позже.');
            }
        });
    });

    function showError(message) {
        inputError.html(message).removeClass('d-none');
        result.addClass('d-none');
    }

    function showResult(data) {
        inputError.addClass('d-none');

        var html =
            '<div class="card shadow-sm animate-in">' +
            '  <div class="card-body p-4">' +
            '    <div class="row align-items-center">' +
            '      <div class="col-md-5 text-center mb-3 mb-md-0">' +
            '        <img src="' + data.qr_code + '" alt="QR Code" class="img-fluid rounded" style="max-width:220px">' +
            '      </div>' +
            '      <div class="col-md-7">' +
            '        <h5 class="mb-3">Ваша короткая ссылка:</h5>' +
            '        <div class="input-group mb-3">' +
            '          <input type="text" class="form-control" value="' + data.short_url + '" id="short-url-input" readonly>' +
            '          <button class="btn btn-outline-secondary" type="button" id="copy-btn" title="Скопировать">' +
            '            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">' +
            '              <path d="M13 0H6a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2v-2h2a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm-3 14H2V6h2v4a2 2 0 0 0 2 2h4v2zm5-4H6V2h7v8z"/>' +
            '            </svg>' +
            '          </button>' +
            '        </div>' +
            '        <a href="' + data.short_url + '" target="_blank" class="text-decoration-none">' +
            '          Открыть ссылку &nearr;' +
            '        </a>' +
            '      </div>' +
            '    </div>' +
            '  </div>' +
            '</div>';

        result.html(html).removeClass('d-none');

        $('#copy-btn').on('click', function() {
            var copyBtn = $(this);
            if (navigator.clipboard) {
                navigator.clipboard.writeText(data.short_url).then(function() {
                    copyBtn.addClass('btn-success').removeClass('btn-outline-secondary');
                    setTimeout(function() {
                        copyBtn.removeClass('btn-success').addClass('btn-outline-secondary');
                    }, 1500);
                });
            } else {
                $('#short-url-input').select();
                document.execCommand('copy');
            }
        });
    }
})();
JS;

$this->registerJs($js);
?>
