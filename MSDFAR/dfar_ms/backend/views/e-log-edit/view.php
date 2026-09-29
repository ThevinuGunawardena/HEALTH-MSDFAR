<?php
$this->title = 'E-Log #' . $model->id;
?>

<div class="container mt-3">
    <div class="card">
        <div class="card-header">
            <h5>E-Log Entry — #<?= $model->id ?></h5>
        </div>
        <div class="card-body">
            <p><strong>Vessel:</strong> <?= $model->vessel_id ?></p>
            <p><strong>Skipper:</strong> <?= $model->skipper_id ?></p>
            <p><strong>Phone:</strong> <?= $model->phone_number ?></p>
            <p><strong>Departure:</strong> <?= $model->departure_date ?> — <?= $model->departure_harbour ?></p>
            <p><strong>Arrival:</strong> <?= $model->arrival_date ?> — <?= $model->arrival_harbour ?></p>
        </div>
    </div>
</div>