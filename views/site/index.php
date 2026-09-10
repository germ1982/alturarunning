<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'My Yii Application';
$this->params['meta_description'] = 'A high-performance PHP framework best for developing web applications. Fast, secure, and professional.';
$this->params['meta_keywords'] = 'yii, yii2, php, framework, web application, high-performance';
?>
<div class="site-index">

<!-- Incluimos el banner diagonal partido con transparencia glassmorphism -->
    <?= $this->render('index_banner') ?>

   

    <!-- Extensions grid -->
    <div class="row g-3">
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-3 extension-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <span class="extension-icon" aria-hidden="true">&#128270;</span>
                        <h3 class="h6 fw-bold mb-0 ms-2">yii2-debug</h3>
                    </div>
                    <p class="text-body-secondary small mb-0">
                        Debug toolbar and debugger for Yii2. Inspect logs, database queries,
                        request data, and application performance in real time.
                    </p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <?= Html::a(
                        'Learn more &raquo;',
                        'https://www.yiiframework.com/extension/yiisoft/yii2-debug',
                        [
                            'class' => 'btn btn-sm btn-outline-secondary',
                            'rel' => 'noopener',
                            'target' => '_blank',
                        ],
                    ) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-3 extension-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <span class="extension-icon" aria-hidden="true">&#9881;</span>
                        <h3 class="h6 fw-bold mb-0 ms-2">yii2-gii</h3>
                    </div>
                    <p class="text-body-secondary small mb-0">
                        Automatic code generator for models, controllers, CRUD, forms, and modules.
                        Boost your productivity with scaffolding.
                    </p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <?= Html::a(
                        'Learn more &raquo;',
                        'https://www.yiiframework.com/extension/yiisoft/yii2-gii',
                        [
                            'class' => 'btn btn-sm btn-outline-secondary',
                            'rel' => 'noopener',
                            'target' => '_blank',
                        ],
                    ) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-3 extension-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <span class="extension-icon" aria-hidden="true">&#128203;</span>
                        <h3 class="h6 fw-bold mb-0 ms-2">yii2-queue</h3>
                    </div>
                    <p class="text-body-secondary small mb-0">
                        Asynchronous job queue with support for DB, Redis, AMQP, Beanstalk,
                        and SQS drivers. Run background tasks with ease.
                    </p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <?= Html::a(
                        'Learn more &raquo;',
                        'https://www.yiiframework.com/extension/yiisoft/yii2-queue',
                        [
                            'class' => 'btn btn-sm btn-outline-secondary',
                            'rel' => 'noopener',
                            'target' => '_blank',
                        ],
                    ) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-3 extension-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <span class="extension-icon" aria-hidden="true">&#9889;</span>
                        <h3 class="h6 fw-bold mb-0 ms-2">yii2-redis</h3>
                    </div>
                    <p class="text-body-secondary small mb-0">
                        Redis integration providing cache, session, and ActiveRecord support.
                        Leverage in-memory storage for blazing-fast data access.
                    </p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <?= Html::a(
                        'Learn more &raquo;',
                        'https://www.yiiframework.com/extension/yiisoft/yii2-redis',
                        [
                            'class' => 'btn btn-sm btn-outline-secondary',
                            'rel' => 'noopener',
                            'target' => '_blank',
                        ],
                    ) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-3 extension-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <span class="extension-icon" aria-hidden="true">&#128269;</span>
                        <h3 class="h6 fw-bold mb-0 ms-2">yii2-elasticsearch</h3>
                    </div>
                    <p class="text-body-secondary small mb-0">
                        Elasticsearch integration with ActiveRecord and query builder.
                        Add powerful full-text search capabilities to your application.
                    </p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <?= Html::a(
                        'Learn more &raquo;',
                        'https://www.yiiframework.com/extension/yiisoft/yii2-elasticsearch',
                        [
                            'class' => 'btn btn-sm btn-outline-secondary',
                            'rel' => 'noopener',
                            'target' => '_blank',
                        ],
                    ) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-3 extension-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <span class="extension-icon" aria-hidden="true">&#9993;</span>
                        <h3 class="h6 fw-bold mb-0 ms-2">yii2-symfonymailer</h3>
                    </div>
                    <p class="text-body-secondary small mb-0">
                        Email sending integration powered by Symfony Mailer.
                        Compose and deliver rich HTML emails with attachments and templates.
                    </p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <?= Html::a(
                        'Learn more &raquo;',
                        'https://github.com/yiisoft/yii2-symfonymailer',
                        [
                            'class' => 'btn btn-sm btn-outline-secondary',
                            'rel' => 'noopener',
                            'target' => '_blank',
                        ],
                    ) ?>
                </div>
            </div>
        </div>
    </div>

</div>
