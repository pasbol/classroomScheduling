<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html>
	<head>
		 <!--[if lt IE 9]>
		 	<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
		 <![endif]-->
		<meta charset="UTF-8">
		<title><?php echo $title; ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/bootstrap.min.css"/>
		<style type="text/css">
			html, body{
				font-family: Arial, sans-serif;
				color: #333333;
			}
			h1{
				font-weight: bold;
				font-size: 27px;
			}
			label{
				font-weight: normal;
			}
		</style>
	</head>
	<body>
		<!-- modal -->
		<div class="modal show" tabindex="-1" role="dialog" id="myModal" data-backdrop="static" aria-labelledby="myModalLabel">
			<div class="modal-dialog modal-md" role="document">
    			<div class="modal-content">
					<div class="modal-header">
						<div class="col-sm-12">
							<?php echo heading('User Authentication', 1); ?>
							<p>Please log in using your credentials</p>
						</div>
					</div><!-- /modal-header -->
					<div class="modal-body">
						<div>
							<?php echo validation_errors(); ?>
							<?php echo $content; ?>
						</div>
						<?php echo form_open('','class="form-horizontal"');?>
						  	<div class="form-group">
						    	<label for="Username" class="col-sm-2 control-label">Username</label>
							    <div class="col-sm-10">
							      	<div class="input-group">
										<span class="input-group-addon">
											<i class="glyphicon glyphicon-user"></i>
										</span>
										<?php echo form_input('username', '' ,'class="form-control", placeholder="Username"'); ?>
									</div>
							    </div>
						    </div>

						    <div class="form-group">
						    	<label for="Password" class="col-sm-2 control-label">Password</label>
							    <div class="col-sm-10">
							      	<div class="input-group">
										<span class="input-group-addon">
											<i class="glyphicon glyphicon-lock"></i>
										</span>
										<?php echo form_password('password', '' ,'class="form-control", placeholder="Password"'); ?>
									</div>	
							    </div>
						    </div>

						    <div class="form-group">
						    	<div class="col-sm-offset-2 col-sm-10">
						     		<?php echo form_submit('Loginbtn', 'Sign In', 'class="btn btn-sm btn-primary"'); ?>
						    	</div>
						  	</div>
						<?php echo form_close();?>
					</div><!-- /modal body -->
					<div class="modal-footer">
						<p>&copy; Copyright <?php echo date('Y'); ?>. All rights reserved.</p>
					</div>
				</div><!-- /modal content -->
			</div><!-- /modal-dialog modal-md -->
		</div><!-- /modal -->	
	</body>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>
</html>
