
<p>Dear <?= Html::encode($model->Name) ?>,</p>

<p>Thank you for your inquiry. Here are the details:</p>

<p><strong>Inquiry Type:</strong> <?= Html::encode($model->Inquiry_Type) ?></p>
<p><strong>Description:</strong><br><?= nl2br(Html::encode($model->Description)) ?></p>

<p>We will get back to you soon.</p>

<p>Best regards,</p>
<p>Your Company</p>     
