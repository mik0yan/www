<?php 	$this->load->view('home/header');?>
    <div class="container">
 
      <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit">
        <h1>朝阳医院-医学影像分析平台<?php echo $username;?></h1>
        <p>本平台是用于管理骨科类的医学影像数据资料，整理和分析影像资料数据，基于Web云端方式处理医学影像数据，并进行统计分析</p>
        <p><a href="#" class="btn btn-primary btn-large">更多信息 &raquo;</a></p>
      </div>

      <!-- Example row of columns -->
      <div class="row">
        <div class="span4">
          <h2>关节资料库</h2>
          <p>存放有膝关节、髋关节、肩关节的医学影像资料有900多例，统计分析膝关节各项生理解剖指标</p>
          <p><a class="btn" href="#">进入 &raquo;</a></p>
        </div>
        <div class="span4">
          <h2>脊柱资料库</h2>
          <p>内容包括膝关节解剖数据分析，髋关节解剖数据分析。</p>
          <p><a class="btn" href="#">进入 &raquo;</a></p>
        </div>
        <div class="span4">
          <h2>创伤资料库</h2>
          <p>记录和保存创伤病例资料和有关信息</p>
          <p><a class="btn" href="#">进入 &raquo;</a></p>
       </div>
        <div class="span4 offset8">
          <h2>其他资料库</h2>
          <p>记录保存颅颌面、齿科等医学影像资料，可针对医学影像进行生理特征学统计和分析</p>
          <p><a class="btn" href="#">进入 &raquo;</a></p>
        </div>
      </div><!-- /row-->

		<script src="<?=base_url()?>bootstrap/js/jquery.js"></script>
		<script src="<?=base_url()?>bootstrap/js/bootstrap.min.js"></script>
	
	  

      <footer>
        <p>&copy; 朝阳医院 2013</p>
      </footer>

    </div> <!-- /container -->
<?php 	$this->load->view('home/footer');?>
