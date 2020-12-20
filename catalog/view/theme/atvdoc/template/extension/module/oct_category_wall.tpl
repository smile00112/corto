<?php //echo $heading_title; ?>
<div class="container mb-2">
          <nav class="catalog-nav">
            <ul class="catalog-nav__list">
  <?php foreach ($categories as $category) { ?>
		   <li class="catalog-nav__item">
			<a class="catalog-nav__link" href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a>
			</li>
		    		<?php /*if ($category['children']) { ?>
		    		
			      <?php $countstop = 1; 
				  foreach ($category['children'] as $child) { $countstop++; ?>
			        <li><a href="<?php echo $child['href']; ?>"><?php echo $child['name']; ?></a></li>
			        <?php //if ($countstop > $limit) 
					{ ?>
			        <li class="oct-category-see-more"><a href="<?php echo $category['href']; ?>" ><?php echo $text_see_more; ?></a></li>
			        <?php break; } ?>
			      <?php }?>
			    
			    <?php }*/  ?>
  <?php } ?>
		</ul><a class="catalog-nav__catalog" href="/catalog"> <span>Посмотреть <br> все товары</span><i class="icm icm-icon_right_arrow"></i></a>
	  </nav>
</div> 