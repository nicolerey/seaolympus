<section class="content-header">
  <h1>
    Departments
    <a class="btn btn-flat btn-default pull-right btn-sm" href="<?= base_url('departments/create')?>"><i class="fa fa-plus"></i> Add new department</a>
  </h1>
</section>
<section class="content">

  <!-- Default box -->
  <div class="box box-solid">
    <div class="box-body no-padding">
      <table class="table table-hover table-striped">
      	<thead>
			<tr><th>Department #</th><th>Name</th><th>Supervisor</th><th>Division</th></tr>      		
      	</thead>
      	<tbody>  
      		<?php foreach($items AS $row):?>
      			<tr>
              <td><a href="<?= base_url("departments/edit/{$row['id']}")?>"><?= $row['id_number']?></a></td>
      				<td><?= $row['name']?></td>
              <td><?= $row['supervisor']?></td>
              <td><?= $row['division']?></td>
      			</tr>
      		<?php endforeach;?>
      	</tbody>
      </table>
    </div><!-- /.box-body -->
  </div><!-- /.box -->
</section>