<?php 	$this->load->view('home/header');?>
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span2">
          <div class="well sidebar-nav">
            <ul class="nav nav-list">
              <li class="nav-header">患者信息</li>
              <li class="active"><a href="#">患者信息</a></li>
              <li><a href="#">年龄</a></li>
              <li><a href="#">种族</a></li>
              <li><a href="#">扫描序列</a></li>
              <li class="nav-header">典型部位</li>
              <li><a href="#">膝关节</a></li>
              <li><a href="#">髋关节</a></li>
              <li><a href="#">腰椎</a></li>
              <li><a href="#">胸椎</a></li>
              <li><a href="#">颈椎</a></li>
              <li><a href="#">股骨骨折</a></li>
              <li class="nav-header">添加信息</li>
              <li><a href="#">增加患者信息</a></li>
              <li><a href="#">增加影像数据</a></li>
           </ul>
          </div><!--/.well -->
        </div><!--/span-->
        <div class="span10">
          <div class="row-fluid">
            <div class="span12">
			
				<ul class="nav nav-tabs" id="grid-demo-tabs">
					<li class="active"><a href="#demo" data-toggle="tab">Demo</a></li>
					<li><a href="#code" data-toggle="tab">Code</a></li>
				</ul>
				
				<div class="tab-content" id="grid-demo-tabs-content">
				  
					<div id="demo" class="tab-pane fade in active">
					<iframe onload="iframeLoaded(this)" name="se" frameborder="0" width="100%" height="600" src="index.php/datasheet/index"></iframe>
					</div>
				  
					<div id="code" class="tab-pane fade">
					</div>
				  
				</div>

            </div><!--/span-->
          </div><!--/row-->
        </div><!--/span-->

      </div><!--/row-->
      <footer>
        <p>&copy; 朝阳医院</p>
      </footer>

    </div><!--/.fluid-container-->

<?php 	$this->load->view('home/footer');?>
