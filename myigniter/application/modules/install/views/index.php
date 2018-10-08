<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Installation</title>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/plugins/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/plugins/AdminLTE/dist/css/AdminLTE.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/plugins/AdminLTE/dist/css/skins/skin-red.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/plugins/alertify-js/build/css/alertify.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/plugins/iCheck/skins/square/blue.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/css/a-design.css">
</head>
<body class="login-page">
	<div class="container">
		<div class="text-center">
			<h1>MyIgniter <small>Expert installation</small></h1>
		</div>
		<br>
		<div class="row">
			<div class="col-xs-6 col-xs-offset-3">
				<div class="login-box-body">
					<?php echo form_open('install/index'); ?>
						<div class="form-group">
							<label>Site Name</label>
							<input type="text" name="site_name"  class="form-control" placeholder="Site Name" required="required" autofocus />
						</div>
						<div class="form-group">
							<label>Database Hostname / IP</label>
							<input type="text" name="hostname" class="form-control" placeholder="Database Hostname / IP" required="required" value="localhost" />
						</div>
						<div class="form-group">
							<label>Database Driver</label>
							<select name="driver" class="form-control" required="required">
								<option value="mysqli">mysqli</option>
								<option value="cubrid">cubrid</option>
								<option value="ibase">ibase</option>
								<option value="mssql">mssql</option>
								<option value="oci8">oci8</option>
								<option value="odbc">odbc</option>
								<option value="pdo">pdo</option>
								<option value="postgre">postgre</option>
								<option value="sqlite">sqlite</option>
								<option value="sqlite3">sqlite3</option>
								<option value="sqlsrv">sqlsrv</option>
							</select>
						</div>
						<div class="form-group">
							<label>Database User</label>
							<input type="text" name="username" class="form-control" placeholder="Database User" required="required" value="root" />
						</div>
						<div class="form-group">
							<label>Database Password</label>
							<input type="password" name="password" class="form-control" placeholder="Database Password" />
						</div>
						<div class="form-group">
							<label>Database Name</label>
							<input type="text" name="db_name" class="form-control" placeholder="Database Name" required="required" />
						</div>
						<div class="form-group">
							<label>Modules</label>
							<?php foreach ($modules as $key => $value): ?>
								<div class="checkbox">
									<label style="padding-left: 0">
										<input name="module[]" class="check" type="checkbox" value="<?php echo $key ?>" checked="checked">
										<?php echo $value ?>
									</label>
								</div>
							<?php endforeach ?>
						</div>
						<button id='btnInstall' type="submit" class="btn  btn-primary btn-lg btn-block"><i class="fa fa-download"></i> Install</button>
					<?php echo form_close(); ?>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		base_url = '<?php echo base_url() ?>';
	</script>
    <script type="text/javascript" src="<?php echo base_url() ?>assets/plugins/jquery/dist/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>assets/plugins/bootstrap/dist/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>assets/plugins/AdminLTE/dist/js/app.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>assets/plugins/alertify-js/build/alertify.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>assets/plugins/iCheck/icheck.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>assets/install/js/install.js"></script>
</body>
</html>