<style type="text/css">
  table td{
    vertical-align: middle!important;
  }
  .form-group{
    margin-bottom:5px!important;
  }
</style>
<section class="content-header">
  <h1>
    My Payslips
    <small></small>
  </h1>
</section>
<section class="content">

  <!-- Default box -->

  <div class="box box-solid">
    <form class="form-horizontal" data-action="<?= base_url("payslip/adjust/{$payslip['id']}")?>">
      <div class="box-body">
        <div class="alert alert-danger hidden"><ul class="list-unstyled"></ul></div>
        <div class="form-group">
          <label class="col-sm-2 control-label">Name</label>
          <div class="col-sm-5">
            <p class="form-control-static"><?= "{$employee_data['firstname']} {$employee_data['middlename']} {$employee_data['lastname']}"?></p>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">Position</label>
          <div class="col-sm-5">
            <p class="form-control-static"><?= "{$employee_data['position']} @ {$employee_data['department']}"?></p>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label"> Month</label>
          <div class="col-sm-5">
            <p class="form-control-static"><?= format_date($payslip['start_date'], 'd-M-Y'). ' - '. format_date($payslip['end_date'], 'd-M-Y')?></p>
          </div>
        </div>
        <hr/>
        <div class="row">
          <?php $net_pay = 0;?>
          <div class="col-md-6">
              <table class="table table-bordered">
                <thead></thead>
                <tbody>
                  <tr class="active"><td colspan="3" class="text-center text-bold">Earnings</td></tr>
                  <tr><td colspan="2" class="text-center">Particulars</td><td>Amount</td></tr>
                  <?php $regular_pay = $payslip['days_rendered'] * $payslip['current_daily_wage']?>
                  <?php $net_pay += $regular_pay; ?>
                  <tr><td>Regular Pay</td><td><strong><?= number_format($payslip['days_rendered'], 2)?></strong> <small>Days X </small><strong><?= number_format($payslip['current_daily_wage'], 2)?></strong></td><td class="text-bold text-right"><?= number_format($regular_pay, 2)?></td></tr>
                  <?php $overtime_pay = $payslip['overtime_hours_rendered'] * $payslip['current_overtime_rate'] ?>
                  <?php $net_pay += $overtime_pay; ?>
                  <tr><td>Overtime Pay</td><td><strong><?= number_format($payslip['overtime_hours_rendered'], 2)?></strong> <small>Hours X </small><strong><?= number_format($payslip['current_overtime_rate'], 2)?></strong></td><td class="text-bold text-right"><?= number_format($overtime_pay, 2)?></td></tr>
                  <?php $net_pay += $payslip['wage_adjustment']; ?>
                  <tr>
                    <td colspan="2">Wage Adjustment</td>
                    <td class="text-right">
                      <?php if(role_is('po')):?>
                        <input type="hidden" name="id" value="<?= $payslip['id']?>"/>
                        <input type="number" name="amount" class="form-control" value="<?= $payslip['wage_adjustment']?>"/>
                      <?php else:?>
                        <strong><?= number_format($payslip['wage_adjustment'], 2)?></strong>
                      <?php endif;?>
                    </td>
                  </tr>
                  <tr class="active"><td colspan="3" class="text-center text-bold">Additional</td></tr>
                  <tr><td colspan="2" class="text-center">Particulars</td><td>Amount</td></tr>
                  <?php $additionals = 0;?>
                  <?php if(isset($payslip['particulars']['additionals'])):?>
                    <?php foreach($payslip['particulars']['additionals'] AS $row):?>
                      <?php $additionals += $row['amount']?>
                      <tr><td colspan="2"><?= $row['name']?></td><td class="text-bold text-right"><?= number_format($row['amount'], 2)?></td></tr>
                    <?php endforeach;?>
                  <?php else:?>
                    <tr><td colspan="3" class="text-center">-</td></tr>
                  <?php endif;?>
                  <?php $net_pay += $additionals; ?>
                </tbody>
              </table>
          </div>
          <div class="col-md-6">
             <table class="table table-bordered" >
              <tbody>
                <tr class="active"><td colspan="3" class="text-center text-bold">Deductions</td></tr>
                <tr><td colspan="2" class="text-center">Particulars</td><td>Amount</td></tr>
                <?php $deductions = 0;?>
                <?php if(isset($payslip['particulars']['deductions'])):?>
                  <?php foreach($payslip['particulars']['deductions'] AS $row):?>
                    <?php $deductions += $row['amount']?>
                    <tr><td colspan="2"><?= $row['name']?></td><td class="text-bold text-right"><?= number_format($row['amount'], 2)?></td></tr>
                  <?php endforeach;?>
                <?php else:?>
                  <tr><td colspan="3" class="text-center">-</td></tr>
                <?php endif;?>
                <?php $net_pay -= $deductions; ?>
                <?php $late = $payslip['late_minutes'] * $payslip['current_late_penalty']?>
                <?php $net_pay -= $late; ?>
                <tr><td>Late</td><td><strong><?= number_format($payslip['late_minutes'], 2)?></strong> <small>Minutes X </small><strong><?= number_format($payslip['current_late_penalty'], 2)?></strong></td><td class="text-bold text-right"><?= number_format($late, 2)?></td></tr>
                <tr class="success"><td colspan="2" class="text-center text-bold">NET PAY</td><td class="text-bold text-right" style="font-size:130%"><?= number_format($net_pay, 2)?></td></tr>
              </tbody>
             </table>
          </div>
        </div>
        <!-- <div class=> -->
      </div><!-- /.box-body -->
      <?php if(role_is('po')):?>
        <div class="box-footer clearfix">
          <button type="submit" class="btn btn-success btn-flat">Save payslip</button>
        </div><!-- /.box-footer -->
      <?php endif;?>
    </form>
  </div><!-- /.box -->
</section>