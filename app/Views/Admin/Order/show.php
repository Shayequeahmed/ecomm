<?php $this->use('templates/main.php',['title'=>'Ecomm | Product','mainPage'=>'Order','page'=>'']) ?>

<div class="content-wrapper">
  <?php include VIEW_PATH . '/partials/_newmessage.php' ?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="card">
        	<div class="card-body">
        		<div class="row">
		          <div class="col-sm-6">
		            <h1>Order Details</h1>
		          </div>
		          <div class="col-sm-6">
		            <ol class="breadcrumb float-sm-right">
		              <li class="breadcrumb-item"><a href="#">Home</a></li>
		              <li class="breadcrumb-item active">Order</li>
		              <li class="breadcrumb-item active">Order Details</li>
		            </ol>
		          </div>
        		</div>
        	</div>
       </div>
      </div><!-- /.container-fluid -->
    </section>


  <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header bg-primary">
                <h3 class="card-title">Order</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0">
                <table class="table table-borderless">
                  <tbody>
                    <tr>
                      <th>Order Id</th>
                      <td><?= e($order->id) ?></td>
                    </tr>
                    <tr>
                      <th>Order Total</th>
                      <td><?= e($order->amount) ?></td>
                    </tr>
                    
                    <tr>
                      <th>Gross Total</th>
                      <td><?= e($order->amount) ?></td>
                    </tr>
                    <tr>
                      <th>Discount</th>
                      <td><?= 0 ?></td>
                    </tr>
                    <tr>
                      <th>Order Type</th>
                      <td><?= 'credit' ?></td>
                    </tr>
                    <tr>
                      <th>Status</th>
                      <td><?= e($order->status)==0 ? '<span class="badge bg-danger">Created</span>':'<span class="badge bg-success">Success</span>' ?>
                      </td>
                    </tr>
                    
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header bg-primary">
                <h3 class="card-title">Order Items</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>Product Code</th>
                      <th>Title</th>
                      <th>Marked Price</th>
                      <th>Selling Price</th>
                      <th>Color</th>
                      <th>Size</th>
                      <th>Qty</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($order_items as $oi): ?>
                      <tr>
                        <td><?= $oi->product()->code ?></td>
                        <td><?= $oi->product()->title ?></td>
                        <td><?= $oi->price_mp ?></td>
                        <td><?= $oi->price_sp ?></td>
                        <td><?= $oi->color()->color ?></td>
                        <td><?= $oi->size()->size ?></td>
                        <td><?= $oi->qty ?></td>
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
      </div>
  </section>
</div>