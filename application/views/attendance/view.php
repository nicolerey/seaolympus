<section class="content-header">
  <h1>
    Attendance
    <small></small>
  </h1>
</section>
<section class="content">

  <!-- Default box -->
  <div class="box box-solid">
      <div class="box-body">

        <?php if(is_numeric($num = $this->session->flashdata('upload_status'))):?>
          <div class="alert<?= ($num==1)?' alert-success':' alert-danger';?>">
            <ul class="list-unstyled">
              <?php if($num==1):?>
                <li>File upload was successful.</li>
              <?php else:?>
                <li>File upload was unsuccessful.</li>
              <?php endif;?>
            </ul>
          </div>
        <?php endif;?>

        <div class="form-group">
          <form class="form-inline" method="GET" action="<?= current_url()?>">
            <?php if(isset($search_employee)):?>
               <div class="form-group">
                  <label>Employee number</label>
                  <input type="number" class="form-control" name="employee_number" value="<?= $this->input->get('employee_number')?>">
                </div>
            <?php endif;?>
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

        <div class="row form-group">
          <?= form_open_multipart('attendance/upload_attendance', 'class="form-inline"');?>
          <div class="col-sm-1">
            <button type="submit" class="btn btn-primary btn-flat"><span class="glyphicon glyphicon-upload"></span> Upload</button>
          </div>
          <div class="col-sm-3" style="padding-top: 5px;">
            <input type="file" name="userfile"/>
          </div>
        </div>

        <hr>
        <table class="table table-bordered table-condensed table-striped">
          <thead><tr class="active"><th>Employee Name</th><th>Time in</th><th>Time out</th><th></th></tr></thead>
          <tbody>
            <?php if(empty($data)):?>
              <tr><td class="text-center" colspan="5">Nothing to display</td></tr>
            <?php else:?>
              <?php foreach($data as $attendance):?>
                <tr>
                  <td>
                    <?= $attendance['name'];?>
                  </td>
                  <td>
                    <a href="#" data-type="combodate" data-pk="<?= $attendance['emp_attendance_id'];?>" data-url="<?= base_url("attendance/save_datetime");?>" data-title="Select time in" class="editable_time time_in" data-name="datetime_in">
                      <?= ($attendance['datetime_in'])?$attendance['datetime_in']:"-"; ?>
                    </a>
                  </td>
                  <td>
                    <a href="#" data-type="combodate" data-pk="<?= $attendance['emp_attendance_id'];?>" data-url="<?= base_url("attendance/save_datetime");?>" data-title="Select time out" class="editable_time time_out" data-name="datetime_out">
                      <?= ($attendance['datetime_out'])?$attendance['datetime_out']:"-"; ?>
                    </a>
                  </td>
                  <td class="time_diff"><?= $attendance['total_hours'];?> hrs</td>
                </tr>
              <?php endforeach;?>
            <?php endif;?>
          </tbody>
        </table>
      </div><!-- /.box-body -->
  </div><!-- /.box -->
</section>