<?php
$chartSecondarySeries = $chartSecondarySeries ?? null;
$chartPrimaryLabel = $chartPrimaryLabel ?? 'Count';
$chartSecondaryLabel = $chartSecondaryLabel ?? null;
$allChartPoints = $chartSecondarySeries === null
    ? $chartSeries
    : array_merge($chartSeries, $chartSecondarySeries);
$chartValues = array_values(array_filter(
    array_column($allChartPoints, 'value'),
    static fn ($value): bool => is_int($value),
));
$chartMaxValue = max(4, (int) (ceil((max($chartValues ?: [0])) / 4) * 4));
$chartLeft = 44;
$chartRight = 452;
$chartTop = 24;
$chartBottom = 174;
$chartPlotHeight = $chartBottom - $chartTop;
$chartBandWidth = ($chartRight - $chartLeft) / max(1, count($chartSeries));
$chartBarWidth = $chartSecondarySeries === null
    ? min(30, $chartBandWidth * .58)
    : min(17, $chartBandWidth * .32);
$chartHasPositiveValues = max($chartValues ?: [0]) > 0;
$chartHasSuppressedPeriods = in_array(null, array_column($allChartPoints, 'value'), true);
?>
<figure class="admin-chart" aria-labelledby="<?= esc($chartId) ?>-heading">
    <figcaption class="admin-chart-heading">
        <div><h3 id="<?= esc($chartId) ?>-heading"><?= esc($chartTitle) ?></h3><p><?= esc($chartDescription) ?></p></div>
    </figcaption>
    <?php if ($chartHasPositiveValues): ?>
        <svg class="admin-chart-svg" viewBox="0 0 460 222" role="img" aria-labelledby="<?= esc($chartId) ?>-title <?= esc($chartId) ?>-description">
            <title id="<?= esc($chartId) ?>-title"><?= esc($chartTitle) ?></title>
            <desc id="<?= esc($chartId) ?>-description"><?= esc($chartDescription) ?><?php if ($chartSecondaryLabel !== null): ?> Series compare <?= esc($chartPrimaryLabel) ?> with <?= esc($chartSecondaryLabel) ?>.<?php endif ?> Bars below the privacy threshold are hidden.</desc>
            <?php foreach ([0, $chartMaxValue / 2, $chartMaxValue] as $tick): ?>
                <?php $tickY = $chartBottom - (($tick / $chartMaxValue) * $chartPlotHeight); ?>
                <line class="admin-chart-gridline" x1="<?= $chartLeft ?>" y1="<?= number_format($tickY, 1, '.', '') ?>" x2="<?= $chartRight ?>" y2="<?= number_format($tickY, 1, '.', '') ?>"></line>
                <text class="admin-chart-axis-label" x="<?= $chartLeft - 8 ?>" y="<?= number_format($tickY + 4, 1, '.', '') ?>" text-anchor="end"><?= esc((string) (int) $tick) ?></text>
            <?php endforeach ?>
            <?php foreach ($chartSeries as $index => $point): ?>
                <?php
                $centerX = $chartLeft + ($chartBandWidth * $index) + ($chartBandWidth / 2);
                $seriesPoints = [['value' => $point['value'], 'class' => 'admin-chart-bar', 'offset' => $chartSecondarySeries === null ? 0 : -($chartBarWidth * .62)]];
                if ($chartSecondarySeries !== null) {
                    $seriesPoints[] = ['value' => $chartSecondarySeries[$index]['value'], 'class' => 'admin-chart-bar admin-chart-bar-secondary', 'offset' => $chartBarWidth * .62];
                }
                ?>
                <?php foreach ($seriesPoints as $seriesPoint): ?>
                    <?php if (is_int($seriesPoint['value']) && $seriesPoint['value'] > 0): ?>
                        <?php
                        $barX = $centerX + $seriesPoint['offset'] - ($chartBarWidth / 2);
                        $barHeight = max(2, ($seriesPoint['value'] / $chartMaxValue) * $chartPlotHeight);
                        $barY = $chartBottom - $barHeight;
                        ?>
                        <rect class="<?= esc($seriesPoint['class']) ?>" x="<?= number_format($barX, 1, '.', '') ?>" y="<?= number_format($barY, 1, '.', '') ?>" width="<?= number_format($chartBarWidth, 1, '.', '') ?>" height="<?= number_format($barHeight, 1, '.', '') ?>" rx="4">
                            <title><?= esc($point['label']) ?>: <?= (int) $seriesPoint['value'] ?></title>
                        </rect>
                        <text class="admin-chart-value<?= str_contains($seriesPoint['class'], 'secondary') ? ' admin-chart-value-secondary' : '' ?>" x="<?= number_format($centerX + $seriesPoint['offset'], 1, '.', '') ?>" y="<?= number_format(max($chartTop + 12, $barY - 7), 1, '.', '') ?>" text-anchor="middle"><?= number_format((int) $seriesPoint['value']) ?></text>
                    <?php endif ?>
                <?php endforeach ?>
                <?php if ($point['value'] === null): ?>
                    <line class="admin-chart-suppressed" x1="<?= number_format($centerX - 8, 1, '.', '') ?>" y1="<?= $chartBottom - 2 ?>" x2="<?= number_format($centerX + 8, 1, '.', '') ?>" y2="<?= $chartBottom - 2 ?>"><title><?= esc($point['label']) ?>: hidden for privacy</title></line>
                <?php endif ?>
                <text class="admin-chart-period-label" x="<?= number_format($centerX, 1, '.', '') ?>" y="204" text-anchor="middle"><?= esc($point['label']) ?></text>
            <?php endforeach ?>
        </svg>
        <?php if ($chartSecondaryLabel !== null): ?>
            <ul class="admin-chart-legend" aria-label="Chart series">
                <li><span class="admin-chart-legend-swatch" aria-hidden="true"></span><?= esc($chartPrimaryLabel) ?></li>
                <li><span class="admin-chart-legend-swatch admin-chart-legend-swatch-secondary" aria-hidden="true"></span><?= esc($chartSecondaryLabel) ?></li>
            </ul>
        <?php endif ?>
        <?php if ($chartHasSuppressedPeriods): ?>
            <p class="admin-chart-note">Periods with fewer than five contributing students are hidden.</p>
        <?php endif ?>
    <?php elseif ($chartHasSuppressedPeriods): ?>
        <p class="admin-chart-empty">No periods meet the minimum group size for this trend yet.</p>
    <?php else: ?>
        <div class="admin-chart-empty admin-chart-empty-zero" role="status">
            <span class="admin-chart-empty-mark" aria-hidden="true">0</span>
            <p>Nothing recorded in this range yet. Period details are available below.</p>
        </div>
    <?php endif ?>
    <details class="admin-chart-data">
        <summary>View chart data</summary>
        <table>
            <thead><tr><th scope="col">Period</th><th scope="col"><?= esc($chartPrimaryLabel) ?></th><?php if ($chartSecondaryLabel !== null): ?><th scope="col"><?= esc($chartSecondaryLabel) ?></th><?php endif ?></tr></thead>
            <tbody><?php foreach ($chartSeries as $index => $point): ?><tr><th scope="row"><?= esc($point['label']) ?></th><td><?= $point['value'] === null ? 'Hidden for privacy' : number_format($point['value']) ?></td><?php if ($chartSecondaryLabel !== null): ?><?php $secondaryPoint = $chartSecondarySeries[$index]; ?><td><?= $secondaryPoint['value'] === null ? 'Hidden for privacy' : number_format($secondaryPoint['value']) ?></td><?php endif ?></tr><?php endforeach ?></tbody>
        </table>
    </details>
</figure>
