<?php $url = base_url('positions')?>
<section class="content-header">
  <h1>
    Positions
    <small></small>
  </h1>
</section>
<section class="content">

  <!-- Default box -->
  <div class="box box-solid">
    <div class="box-header with-border">
      <h3 class="box-title"><?= $title ?></h3>
    </div>
      <form class="form-horizontal" data-action="<?= $mode === MODE_CREATE ? "{$url}/store" : "{$url}/update/{$data['id']}" ?>">
      <div class="box-body">
        <div class="alert alert-info"><p>Fields marked with <span class="fa fa-asterisk text-danger"></span> are required.</p></div>
        <div class="alert alert-danger hidden"><ul class="list-unstyled"></ul></div>
        <div class="form-group">
          <label class="col-sm-2 control-label"><span class="fa fa-asterisk text-danger"></span> Position name</label>
          <div class="col-sm-5">
            <input type="text" class="form-control" name="name" value="<?= preset($data, 'name', '')?>" />
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label"><span class="fa fa-asterisk text-danger"></span> Login account type</label>
          <div class="col-sm-5">
            <?= form_dropdown('login_type', ['' => '', 're' => 'Regular Employee Account', 'sv' => 'Supervisor Account', 'po' => 'Payroll Officer Account', 'hr' => 'HR Officer Account'], preset($data, 'login_type', ''), 'class="form-control"')?>
          </div>
        </div>
        <hr>
        <div class="form-group">
          <label class="col-sm-2 control-label"><span class="fa fa-asterisk text-danger"></span> Work days</label>
          <div class="col-sm-3">
            <?= form_dropdown('day_of_week_start', ['' => ''] + $days, preset($data, 'day_of_week_start', ''), 'class="form-control"');?>
            <span class="help-block">From</span>
          </div>
          <div class="col-sm-3">
            <?= form_dropdown('day_of_week_end', ['' => ''] + $days, preset($data, 'day_of_week_end', ''), 'class="form-control"');?>
            <span class="help-block">To</span>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label"><span class="fa fa-asterisk text-danger"></span> Time (AM)</label>
          <div class="col-sm-3">
            <input type="text" name="hour_of_day_start_am" value="<?= display_time($data, 'hour_of_day_start_am')?>" class="form-control timepicker"/>
            <span class="help-block">In</span>
          </div>
          <div class="col-sm-3">
            <input type="text" name="hour_of_day_end_am" value="<?= display_time($data, 'hour_of_day_end_am')?>" class="form-control timepicker"/>
            <span class="help-block">Out</span>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label"><span class="fa fa-asterisk text-danger"></span> Time (PM)</label>
          <div class="col-sm-3">
            <input type="text" name="hour_of_day_start_pm" value="<?= display_time($data, 'hour_of_day_start_pm')?>" class="form-control timepicker"/>
            <span class="help-block">In</span>
          </div>
          <div class="col-sm-3">
            <input type="text" name="hour_of_day_end_pm" value="<?= display_time($data, 'hour_of_day_end_pm')?>" class="form-control timepicker"/>
            <span class="help-block">Out</span>
          </div>
        </div>
      </div><!-- /.box-body -->
      <div class="box-footer clearfix">
        <a href="<?=$url?>" class="btn btn-default cancel pull-right btn-flat">Cancel</a>
        <button type="submit" class="btn btn-success btn-flat">Submit</button>
      </div><!-- /.box-footer -->
    </form>
  </div><!-- /.box -->
</section>