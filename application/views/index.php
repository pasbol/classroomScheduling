<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
	<head>
		 <!--[if lt IE 9]>
		 	<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
		 <![endif]-->
		<meta charset="UTF-8"/>
		<title><?php echo $title; ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<?php $this->load->view('inc/css'); ?>
		<?php $this->load->view('inc/js'); ?>
		<?php $this->load->view($manager.'/'.$js); ?>
	</head>
	<body class="default">
		<div class="container-fluid">
			<div class="row">
				<header class="col-xs-12 header">
					<div><h1>Room Scheduling System</h1></div>
				</header>
				<!-- ./header -->
				<nav class="col-xs-2 navigation">
					<div><?php $this->load->view($menu); ?></div>
				</nav>
				<!-- ./navigation -->
				<div class="col-xs-10 content">
					<section><?php echo $content; ?></section>
				</div>
				<!-- ./content -->
				<footer class="col-xs-12 footer">
					<div>Copyright &copy; <?php echo date('Y').'.'; ?> King's College of the Philippines. All Rights Reserved.</div>
				</footer>
				<!-- ./footer -->
				<div class="col-xs-12 benchmark">
					<div>
						<h3>Benchmark:</h3>
						<p>Total Execution Time: <?php echo $this->benchmark->elapsed_time(); ?></p>
						<p>Total Memory Usage: <?php echo $this->benchmark->memory_usage(); ?></p>
					</div>
				</div>
				<!-- ./benchmark -->
			</div>
			<!-- ./row -->
		</div>
		<!-- ./container-fluid -->
	</body> 
</html>

