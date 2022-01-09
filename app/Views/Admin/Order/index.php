<?php $this->use('templates/main.php',['title'=>'Ecomm | Order','mainPage'=>'Order','page'=>'Order List'])?>

 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
          <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-sm-6">
                    <h1>All Order List</h1>
                  </div>
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="#">Home</a></li>
                      <li class="breadcrumb-item active">Order</li>
                      <li class="breadcrumb-item active">Order List</li>
                    </ol>
                  </div>
                </div>
              </div>
          </div>
      </div><!-- /.container-fluid -->
    </section>
        <?php include VIEW_PATH . '/partials/_newmessage.php' ?>


    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        
        <!-- /.row -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Order List Table</h3>

                <div class="card-tools">
                  <div class="input-group input-group-sm" style="width: 150px;">
                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                    <div class="input-group-append">
                      <button type="submit" class="btn btn-default">
                        <i class="fas fa-search"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>Id</th>
                      <th>Amount</th>
                      <th>Tax</th>
                      <th>Rzp Order Id</th>
                      <th>Status</th>
                      <th>User Id</th>
                    </tr>
                  </thead>
                  <tbody>
                  	<?php foreach($orders as $order): ?>
                    <tr>
                      <td><a href="/admin/order/<?= e($order->id) ?>/show"><?= $order->id ?></a></td>
                      <td> <?= $order->amount; ?>  </td>
                      <td><?= $order->tax; ?></td>
                      <td><?= $order->rzp_order_id; ?></td>
                      <td><?= $order->status; ?></td>
                      <td><?= $order->user_id; ?></td>
                      <td>
                        <a href="/admin/order/<?= e($order->id) ?>/edit"><i class="far fa-edit"></i></a>
                        <a href="#"><i class="far fa-trash-alt"></i></a>
                        <a href="/admin/order/<?= e($order->id) ?>/download-bill"><i class="fas fa-download"></i></a>
                      </td>

                    </tr>
                <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>