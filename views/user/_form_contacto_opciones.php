<?php
/* @var $waNum string */
/* @var $callNum string */

?>

<div class="list-group list-group-flush">
    <a href="https://wa.me/<?= $waNum ?>" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center py-3 border-0" data-pjax="0">
        <div class="icon-circle bg-success-subtle text-success me-3">
            <i class="bi bi-whatsapp fs-4"></i>
        </div>
        <div>
            <h6 class="mb-0 fw-bold">WhatsApp</h6>
            <small class="text-muted">Iniciar chat directo</small>
        </div>
    </a>

    <a href="tel:<?= $callNum ?>" class="list-group-item list-group-item-action d-flex align-items-center py-3 border-0">
        <div class="icon-circle bg-primary-subtle text-primary me-3">
            <i class="bi bi-telephone-fill fs-4"></i>
        </div>
        <div>
            <h6 class="mb-0 fw-bold">Llamada telefónica</h6>
            <small class="text-muted">Llamar al dispositivo</small>
        </div>
    </a>

    <a href="sms:<?= $callNum ?>" class="list-group-item list-group-item-action d-flex align-items-center py-3 border-0">
        <div class="icon-circle bg-info-subtle text-info me-3">
            <i class="bi bi-chat-dots-fill fs-4"></i>
        </div>
        <div>
            <h6 class="mb-0 fw-bold">Mensaje de Texto (SMS)</h6>
            <small class="text-muted">Enviar SMS tradicional</small>
        </div>
    </a>
</div>

