<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" type="text/css" media="screen" href="<?=base_url()?>lib/js/themes/redmond/jquery-ui.custom.css"></link>	
	<link rel="stylesheet" type="text/css" media="screen" href="<?=base_url()?>lib/js/jqgrid/css/ui.jqgrid.css"></link>	
	
	<script src="<?=base_url()?>lib/js/jquery.min.js" type="text/javascript"></script>
	<script src="<?=base_url()?>lib/js/jqgrid/js/i18n/grid.locale-en.js" type="text/javascript"></script>
	<script src="<?=base_url()?>lib/js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>	
	<script src="<?=base_url()?>lib/js/themes/jquery-ui.custom.min.js" type="text/javascript"></script>

</head>
<body>

<?php echo $error;?>

<?php echo form_open_multipart('datasheet/do_upload');?>

<input type="file" name="userfile" size="20" />

<br /><br />

<input type="submit" value="upload" />

</form>

</body>
</html>