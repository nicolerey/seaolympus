<section class="content-header">
  <h1>
    Pay Particulars
    <a class="btn btn-flat btn-default pull-right btn-sm" href="<?= base_url('pay_modifiers/create')?>"><i class="fa fa-plus"></i> Add new pay modifier</a>
  </h1>
</section>
<section class="content">

  <!-- Default box -->
  <div class="box box-solid">
    <div class="box-body no-padding">
      <table class="table table-hover table-striped">
      	<thead>
			<tr><th>Name</th><th>Type</th><th>Status</th></tr>      		
      	</thead>
      	<tbody>
      		<?php foreach($items AS $row):?>
      			<tr>
              <td><a href="<?= base_url("pay_modifiers/edit/{$row['id']}")?>"><?= $row['name']?></a></td>
      				<td><?= $row['type'] === 'a' ? 'Additionals' : 'Deductions' ?></td>
              <td>
              <?php if(intval($row['is_active'])):?>
                <span class="label label-success">Active</span>
              <?php else:?>
                <span class="label label-warning">Inactive</span>
              <?php endif;?>
              </td>
      			</tr>
      		<?php endforeach;?>
      	</tbody>
      </table>
    </div><!-- /.box-body -->
  </div><!-- /.box -->
</section>