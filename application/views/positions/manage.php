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
          <label class="col-sm-2 control-label"><span class="fa fa-asterisk text-danger"></span> Attendance type</label>
          <div class="col-sm-5">
            <?= form_dropdown('attendance_type', ['' => '', 're' => 'Regular Employee', 'fl' => 'Flexible Employee'], preset($data, 'attendance_type', ''), 'class="form-control" onChange="AttendanceTypeFunc(this.value)"')?>
          </div>
        </div>
        <hr>
        <div class="form-group">
          <label class="col-sm-2 control-label"><span class="fa fa-asterisk text-danger"></span> Work days</label>
          <div class="col-sm-8">
            <?php for($workday_counter=1; $workday_counter<8; $workday_counter++){ ?>
              <label class="checkbox-inline">
                <input type="checkbox" name="workday[]" value="<?= $workday_counter?>"<?= ($data['workday']!=NULL) ? ((in_array($workday_counter, json_decode($data['workday']))) ? " checked" : "") : "";?>/> 
                <?= $days[$workday_counter]?>
              </label>
            <?php }?>
          </div>
        </div>

        <div class="flexible_time_fields"<?= (isset($data['attendance_type']) && $data['attendance_type']!="fl")?"style=display:none;":"";?> >
          <div class="form-group">
            <label class="col-sm-2 control-label"><span class="fa fa-asterisk text-danger"></span> Required work hours</label>
            <div class="col-sm-3">
              <input class="form-control" value="" step="0.01" min="0" name="required_work_hours"/>
            </div>
          </div>
        </div>

        <div class="regular_time_fields"<?= (isset($data['attendance_type']) && $data['attendance_type']=="fl")?"style=display:none;":"";?> >
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
        </div>

      </div><!-- /.box-body -->
      <div class="box-footer clearfix">
        <a href="<?=$url?>" class="btn btn-default cancel pull-right btn-flat">Cancel</a>
        <button type="submit" class="btn btn-success btn-flat">Submit</button>
      </div><!-- /.box-footer -->
    </form>
  </div><!-- /.box -->
</section>

<script>
function AttendanceTypeFunc(attendance_type_value){
  if(attendance_type_value=="fl"){
    $('.flexible_time_fields').show();
    $('.regular_time_fields').hide();
  }
  else if(attendance_type_value=="re"){
    $('.flexible_time_fields').hide();
    $('.regular_time_fields').show();
  }
}
</script>