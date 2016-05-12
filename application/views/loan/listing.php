<section class="content-header">
  <h1>
    Loan
    <a class="btn btn-flat btn-default pull-right btn-sm" href="<?= base_url('loan/create')?>"><i class="fa fa-plus"></i> Make a loan</a>
  </h1>
</section>
<section class="content">

  <!-- Default box -->
  <div class="box box-solid">
      <div class="box-body">
        <div class="form-group">
          <form class="form-inline" method="GET" action="<?= current_url()?>">
            <?php //if(isset($search_employee)):?>
               <div class="form-group">
                  <label>Employee number</label>
                  <input type="number" class="form-control" name="employee_number" value="<?= $this->input->get('employee_number')?>">
                </div>
            <?php //endif;?>
            <div class="form-group">
              <label for="start-date">Start date</label>
              <input type="text" class="form-control datepicker" id="start-date" name="start_date" value="<?= $this->input->get('start_date')?>">
            </div>
            <div class="form-group">
              <label for="end-date">End date</label>
              <input type="text" class="form-control datepicker" id="end-date" name="end_date" value="<?= $this->input->get('end_date')?>">
            </div>
            <button type="submit" class="btn btn-default btn-flat">Go!</button>
          </form>
        </div>
        <hr>
        <table class="table table-bordered table-condensed table-striped">
          <thead><tr class="active"><th>Employee Name</th><th>Loan Date</th><th>Loan Amount</th></tr></thead>
          <tbody>
            <?php if(empty($data)):?>
              <tr><td class="text-center" colspan="5">Nothing to display</td></tr>
            <?php else:?>
              <?php foreach($data as $loan):?>
                <tr>
                  <td>
                    <?= $loan['name'];?>
                  </td>
                  <td>
                    <?= date_format(date_create($loan['loan']['loan_date']), 'm/d/Y');?>
                  </td>
                  <td>
                    <?= $loan['loan']['loan_amount']; ?>
                  </td>
                </tr>
              <?php endforeach;?>
            <?php endif;?>
          </tbody>
        </table>
      </div><!-- /.box-body -->
  </div><!-- /.box -->
</section>