<?php
use yii\helpers\Html;

$this->title = 'Dashboard Debug Information';
?>
<div style="padding: 20px; font-family: monospace; background: #f5f5f5;">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <h2>Database Query Results</h2>
    
    <?php foreach ($debugData as $key => $data): ?>
        <div style="margin: 20px 0; padding: 15px; background: white; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="color: #333; margin-bottom: 10px;"><?= Html::encode(ucfirst(str_replace('_', ' ', $key))) ?></h3>
            
            <?php if (is_array($data)): ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Metric</th>
                            <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $metric => $value): ?>
                            <tr>
                                <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">
                                    <?= Html::encode(ucfirst(str_replace('_', ' ', $metric))) ?>
                                </td>
                                <td style="padding: 8px; border: 1px solid #ddd;">
                                    <?php if (is_array($value)): ?>
                                        <pre><?= Html::encode(print_r($value, true)) ?></pre>
                                    <?php else: ?>
                                        <?= Html::encode($value) ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="color: #dc3545; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 3px;">
                    <strong>Error:</strong> <?= Html::encode($data) ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    
    <div style="margin-top: 30px; padding: 15px; background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 5px;">
        <h3>Data Sync Status</h3>
        <p><strong>Last Updated:</strong> <?= date('Y-m-d H:i:s') ?></p>
        <p><strong>PHP Version:</strong> <?= PHP_VERSION ?></p>
        <p><strong>Yii Version:</strong> <?= Yii::getVersion() ?></p>
    </div>
</div> 