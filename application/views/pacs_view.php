<?php 	$this->load->view('home/header');?>
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span2">
          <div class="well sidebar-nav">
            <ul class="nav nav-list">
              <li class="nav-header">患者信息</li>
				<li class="active"><a href="<?=base_url()?>index.php/datasheet/subject" target="table_frame">基本资料</a></li>
				<li><a href="<?=base_url()?>index.php/datasheet/subjectbyg"  target="table_frame">分组查看</a></li>
              <li class="nav-header">扫描序列</li>
				<li><a href="<?=base_url()?>index.php/datasheet/instance"  target="table_frame">基本资料</a></li>
				<li><a href="<?=base_url()?>index.php/datasheet/instancebyg"  target="table_frame">分组查看</a></li>
              <li class="nav-header">添加信息</li>
				<li><a href="<?=base_url()?>index.php/datasheet/newinstance"  target="table_frame">上传序列文件</a></li>
           </ul>
          </div><!--/.well -->
        </div><!--/span2-->
        <div class="span10">
          <div class="row-fluid">
            <div class="span12">			
				<ul class="nav nav-tabs" id="pacs-tabs">
					<li class="active"><a href="de" data-toggle="tab">信息</a></li>
					<li><a href="#code" data-toggle="tab">数据</a></li>
				</ul>
				<div class="tab-content" id="pacs-tabs-content">
					<div id="show" class="tab-pane fade in active" >
					<iframe onload="iframeLoaded(this)" name="table_frame" id="mainframe" frameborder="0" width="100%" height="600" src="<?=base_url()?>index.php/datasheet/subject" ></iframe>
					</div>
				  
					<div id="code" class="tab-pane fade">
					</div>
				  
				</div>

            </div><!--/span12-->
          </div><!--/row-->
        </div><!--/span10-->

      </div><!--/row-->
		<script src="<?=base_url()?>bootstrap/js/jquery.js"></script>
		<script src="<?=base_url()?>bootstrap/js/bootstrap.min.js"></script>
	<script>
	$("li").click(function(){
	  // If this isn't already active
	  if (!$(this).hasClass("active")) {
	    // Remove the class from anything that is active
	    $("li.active").removeClass("active");
	    // And make this active
	    $(this).addClass("active");
	  }
	});
	</script>
 
      <footer>
        <p>&copy; 朝阳医院</p>
      </footer>

    </div><!--/.fluid-container-->

<?php 	$this->load->view('home/footer');?>
