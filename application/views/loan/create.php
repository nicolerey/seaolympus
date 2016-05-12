<?php $url = base_url('loan')?>
<section class="content-header">
  <h1>
    Loan
    <small></small>
  </h1>
</section>
<section class="content">

  <!-- Default box -->
  <div class="box box-solid">
    <div class="box-header with-border">
      <h3 class="box-title"><?= $title ?></h3>
    </div>
    <form class="form-horizontal" data-action="<?= "{$url}/store"?>" onsubmit="return confirm('Are you sure?')">
      <div class="box-body">
        <div class="alert alert-info"><p>Fields marked with <span class="fa fa-asterisk text-danger"></span> are required.</p></div>
        <div class="alert alert-danger hidden"><ul class="list-unstyled"></ul></div>
        <div class="form-group">
          <label class="col-sm-2 control-label"><span class="fa fa-asterisk text-danger"></span> Date</label>
          <div class="col-sm-2">     
            <input type="text" class="form-control datepicker" name="loan_date"/>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label"><span class="fa fa-asterisk text-danger"></span> Employee</label>
          <div class="col-sm-3">
            <?= form_dropdown('employee_number', [ 'all' => '*All employees*' ]+ $employees, FALSE, 'class="form-control" required="required"')?>
          </div>
          <div class="col-sm-1"></div>
          <label class="col-sm-2 control-label"><span class="fa fa-asterisk text-danger"></span> Loan amount</label>
          <div class="col-sm-2">
            <input name="loan_amount"  min="0" step="0.01" value="0.00" class="form-control pformat loan_amount"/>
          </div>
        </div>
        <hr>
        <div class="form-group">
          <label class="col-sm-2 control-label"> Loan payment series count</label>
          <div class="col-sm-1">            
              <input type="number" class="form-control payment_count" value="0" name="payment_count"/>
          </div>
          <div class="col-sm-2">            
            <button type="button" class="btn btn-default btn-flat" onclick="generate_payment_terms(this);">Generate</button>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label"><span class="fa fa-asterisk text-danger"></span></label>
          <div class="col-sm-6">
            <table class="table table-hover table-striped">
              <thead>
                <tr>
                  <th class="col-sm-1">Date</th>
                  <th class="col-sm-1">Amount</th>
                </tr>
              </thead>
              <tbody class="payment_terms_tbody">
                <tr class="payment_terms_fields hidden">
                  <td>
                    <input type="text" class="form-control datepicker col-sm-1 loan_date_field" value=""/>
                  </td>
                  <td>
                    <input min="0" step="0.01" value="0.00" class="form-control pformat loan_amount_field"/>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <!-- /.box-body -->
      </div>
      <div class="box-footer clearfix">
        <a href="<?=$url?>" class="btn btn-default cancel pull-right btn-flat">Cancel</a>
        <button type="submit" class="btn btn-success btn-flat">Submit</button>
      </div><!-- /.box-footer -->
    </form>
  </div><!-- /.box -->
</section>