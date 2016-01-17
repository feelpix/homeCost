<?php
/**
 * @var array $header
 * @var array $data
 * @var array $total
 * @var int   $year
 */
use yii\helpers\Url;

$currentYear = date('Y');
$lastYear = $year - 1;
?>

<div class="page-header">
    <h1>Dashboard</h1>
</div>
<div class="row">
    <div class="row row-centered">
        <div class="col-md-offset-4 col-md-4 col-centered">
            <div class="btn-group text-center" data-toggle="buttons">
                <?php for($i = 2015; $i <= $currentYear; $i++):?>
                    <label class="btn btn-primary btn-year <?php echo ($i == $year ? 'active':'');?>">
                        <input type="radio" name="year" value="<?php echo $i;?>" autocomplete="off" <?php echo ($i == $year ? 'checked':'');?>> <?php echo $i;?>
                    </label>
                <?php endfor;?>
            </div>
        </div>
    </div>
</div>
<div><br></div>
<table class="table table-striped table-condensed table-bordered">
    <thead> <!-- En-tête du tableau -->
    <tr>
        <th>Catégorie</th>
        <th class="text-center"><a href="<?php echo Url::to(['dashboard/detail', 'year' => $lastYear]); ?>" target="_blank"><?php echo $lastYear;?></a></th>
        <th class="text-center"><a href="<?php echo Url::to(['dashboard/detail', 'year' => $year]); ?>" target="_blank"><?php echo $year;?></a></th>
        <?php foreach ($header as $m => $row): ?>
            <th class="text-center"><a href="<?php echo Url::to(['dashboard/detail', 'month' => $m]); ?>"
                                       target="_blank"><?php echo $row; ?></a></th>
        <?php endforeach; ?>
    </tr>
    </thead>

    <tfoot> <!-- Pied de tableau -->
    <tr>
        <th class="text-right"><strong>Total</strong></th>
        <th class="text-center"><?php echo number_format($total['previous'], 2); ?></th>
        <th class="text-center"><?php echo number_format($total['year'], 2); ?></th>
        <?php foreach ($total['month'] as $amount): ?>
            <th class="text-center <?php echo intval($amount) > $total['year'] ? 'success' : 'danger'; ?>">
                <strong><?php echo $amount; ?></strong></th>
        <?php endforeach; ?>
    </tr>
    </tfoot>

    <tbody> <!-- Corps du tableau -->
    <?php foreach ($data as $row): ?>
        <tr>
            <td><?php echo $row['label']; ?></td>
            <td class="text-center"><a
                    href="<?php echo Url::to(['dashboard/detail', 'year' => $lastYear, 'category' => $row['categId']]); ?>"
                    target="_blank"><?php echo $row['previous']; ?></a></td>
            <td class="text-center"><a
                    href="<?php echo Url::to(['dashboard/detail', 'year' => $year, 'category' => $row['categId']]); ?>"
                    target="_blank"><?php echo $row['current']; ?></a></td>
            <?php foreach ($row['month'] as $m => $amount): ?>
                <td class="text-center <?php echo intval($amount) > $row['yearAmount'] ? 'success' : 'danger'; ?>">
                    <a
                        href="<?php echo Url::to(['dashboard/detail', 'month' => $m , 'category' => $row['categId']]); ?>"
                        target="_blank"><?php echo $amount;?></a>
                </td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>

    </tbody>
</table>
