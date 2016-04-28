<section class="content-header">
  <h1>
    Positions
    <a class="btn btn-flat btn-default pull-right btn-sm" href="<?= base_url('positions/create')?>"><i class="fa fa-plus"></i> Add new position</a>
  </h1>
</section>
<section class="content">

  <!-- Default box -->
  <div class="box box-solid">
    <div class="box-body no-padding">
      <table class="table table-hover table-striped">
      	<thead>
			<tr><th>Name</th><th>Login account type</th></tr>      		
      	</thead>
      	<tbody>
      		<?php foreach($items AS $row):?>
      			<tr>
              <td><a href="<?= base_url("positions/edit/{$row['id']}")?>"><?= $row['name']?></a></td>
              <td><?= account_type($row['login_type'])?></td>
      			</tr>
      		<?php endforeach;?>
      	</tbody>
      </table>
    </div><!-- /.box-body -->
  </div><!-- /.box -->
</section>