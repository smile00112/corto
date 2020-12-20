<?php
/**
 * 	2015 lemweb.ru
 */

defined('_JEXEC') or die;

/* The following line loads the MooTools JavaScript Library */
JHtml::_('behavior.framework', true);

/* The following line gets the application object for things like displaying the site name */
$app = JFactory::getApplication();
$menu = $app->getMenu();
$main = 0;
$prodid = 0;
if ($menu->getActive() == $menu->getDefault()) {
	$main = 1;	//mainpage
}
$input = JFactory::getApplication()->input;
$prodid = $input->getCmd('product_id', '');
$catid = $input->getCmd('category_id', '');

if($catid > 0){
  $catDescHelp = JSFactory::getTable('category', 'jshop');
  $catDescHelp->load($catid);
  $catColDescr = $catDescHelp->getDescription();
}

?>
<!DOCTYPE html>
<!--[if lt IE 9]> <html lang="ru" class="lt-ie9"> <![endif]-->
<!--[if IE 9]><html lang="ru" class="ie9"><![endif]-->
<!--[if gt IE 9]><!-->
<html class="no-js" xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language;?>" >
<!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<jdoc:include type="head" />	

	<link rel="shortcut icon" href="templates/<?php echo $this->template ?>/favicon.ico">

	<!-- СSS here -->
	<link rel="stylesheet" href="templates/<?php echo $this->template ?>/css/style.css">
    <link rel="stylesheet" href="templates/<?php echo $this->template ?>/css/menu.css">

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
 	<!--[if lt IE 9]>
	    <script src="templates/<?php echo $this->template ?>/js/lib/html5shiv.min.js"></script>
	    <script src="templates/<?php echo $this->template ?>/js/lib/respond.min.js"></script>
    <![endif]-->

    <!-- modernizr.js -->
 	<script src="templates/<?php echo $this->template ?>/js/lib/modernizr.js"></script>

    <!-- fonts -->
    <link href='https://fonts.googleapis.com/css?family=Ubuntu:500&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js" type="text/javascript"></script>
<script src="/templates/<?php echo $this->template ?>/js/jquery.cookie.js" type="text/javascript"></script>
<script src="/templates/<?php echo $this->template ?>/js/fancybox/jquery.fancybox.pack.js" type="text/javascript"></script>
<link href='/templates/<?php echo $this->template ?>/js/fancybox/jquery.fancybox.css' rel='stylesheet' type='text/css'>
    <script type="text\javascript">	
jQuery(document).ready(function(){	    
   jQuery(".mouse-scroll").click(function(){
       alert('ПРоверка клика по кнопке');	   
       jQuery("html, body").animate({scrollTop:jQuery(document).height()},"slow")            
   })
})	  
</script>
</head>
<body>

<div id="container" class="mainpage">
  <div class="page-wrapper">
    <!-- nav panel -->
    <div id="nav-block" class="cube-wrap col site-pane menu-close">
      <div class="cube">
      <div class="front-pane hiddenBackface">
      	<div class="nav-block__inside">
      		<div class="logo"><a href="/" class="logo-link"><img src="templates/<?php echo $this->template ?>/images/logo.png" alt="corto"></a></div>
            <div class="menu-btn-block" id="jsMenuBtnContainer">
                <div class="menu-btn">
                    <input type="checkbox" id="menu-btn">
                    <span></span>
                </div>
            </div>

            <div class="menu-block">
                <!-- menu -->
                <?php if ($this->countModules('menu')) : ?>
                    <jdoc:include type="modules" name="menu" style="mod" />
                <?php endif; ?>
                <section class="footer-social"><jdoc:include type="modules" name="footer-social" style="mod" /></section>
            </div>

      		<div class="b-bottom">
      			<!-- rights -->
            <?php if ($this->countModules('copyrights')) : ?>
              <jdoc:include type="modules" name="copyrights" style="mod" />
            <?php endif; ?>
      
      			<!-- lang -->
      			<?php if ($this->countModules('lang')) : ?>
      				<jdoc:include type="modules" name="lang" style="mod" />
      			<?php endif; ?>
      		</div>
      
      	</div>
      </div>
      <div class="right-pane hiddenBackface"></div>
      <div class="left-pane hiddenBackface"></div>
      </div>
    </div><!-- nav panel END-->

    <!-- pic panel -->
    <div id="pic-block" class="cube-wrap col site-pane">
      <div class="cube">
      <div class="front-pane hiddenBackface">
      	<div class="pic-block__inside">
    		<?php if ($this->countModules('2ndcol')) { ?>
    				<jdoc:include type="modules" name="2ndcol" style="mod" />
        <?php }elseif($catid > 0){ ?>
			     	<div class="category-info" style="background-image: url(templates/<?php echo $this->template ?>/images/slide2.jpg);">
	      			<div class="category-info__desc b-bottom">
	      				<?php echo $catColDescr;?>
	      			</div>
	      		</div>
  			<?php }else{ ?>
            <div class="category-info" style="background-image: url(templates/<?php echo $this->template ?>/images/slide2.jpg);">
              <div class="category-info__desc b-bottom">

              </div>
            </div>
        <?php  } ?>

      	</div>
      </div>
      <div class="right-pane hiddenBackface"></div>
      <div class="left-pane hiddenBackface"></div>
      </div>
    </div><!-- pic panel END -->

    <!-- content panel -->
    <div id="page-block" class="cube-wrap-с site-pane">
      <div class="cube-с">
      <div class="right-pane hiddenBackface"></div>
      <div class="left-pane hiddenBackface"></div>
      <div class="front-pane hiddenBackface">
      	<div class="page-block__inside<?php if($main==1) echo '--main'; ?>">
		  	<jdoc:include type="message" />

      		<!-- download catalog btn -->
  			<?php if ($this->countModules('download-btn')) : ?>
  				<jdoc:include type="modules" name="download-btn" style="mod" />
  			<?php endif; ?>
			
			<?php if($main==0){ ?>

				<?php if ($this->countModules('catmenu') && $prodid == 0) : ?>
					<div class="shop-category-sort">
	  					<jdoc:include type="modules" name="catmenu" style="mod" />
	  				</div>
	  			<?php endif; ?>

  				<jdoc:include type="component" />
				<?php if ($this->countModules('pravila')) {?>
				<div class="pravila">
                                    <jdoc:include type="modules" name="pravila" style="xhtml"/> 
                                </div>
				<?php } ?>
  			<?php }else{ ?>	
      
	            <!-- mainpage slider -->  
	            <?php if ($this->countModules('mainpage-products')) : ?>
	  				<jdoc:include type="modules" name="mainpage-products" style="mod" />
	  			<?php endif; ?>

  			<?php } ?>
      
      	</div>
      </div>
      </div>
    </div><!-- content panel END -->

  </div>
</div>


<!-- popup -->
<div class="wrap-popup" id="popup-form">
  <div class="main-container">
      <div class="easybook-popup">
        <h3>Задать вопрос</h3>
        <div class="easybook-form" id="easybook-form-container">       
        </div>
      </div>
    <span class="close">Х</span>
  </div>
  <div class="wrap-popup-bg"></div>
</div>


<!-- scripts -->
<script src="templates/<?php echo $this->template ?>/js/plugins.js"></script>
<script src="templates/<?php echo $this->template ?>/js/main.js"></script>
<?php if ($this->countModules('podpiska')) {?>
<noindex>
<div style="display:none;">
    <div class="podpiska" id="popuppodpiska">
        <jdoc:include type="modules" name="podpiska" /> 
    </div>
</div>
</noindex>
<?php } ?>
<?php if ($this->countModules('adrespopup')) {?>
<noindex>
<div style="display:none;">
    <div class="adrespopup" id="popupadres">
        <jdoc:include type="modules" name="adrespopup" /> 
    </div>
</div>
</noindex>
<?php } ?>

<!-- Код тега ремаркетинга Google -->
<!--------------------------------------------------
С помощью тега ремаркетинга запрещается собирать информацию, по которой можно идентифицировать личность пользователя. Также запрещается размещать тег на страницах с контентом деликатного характера. Подробнее об этих требованиях и о настройке тега читайте на странице http://google.com/ads/remarketingsetup.
--------------------------------------------------->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 918681930;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/918681930/?guid=ON&amp;script=0"/>
</div>
</noscript>

<script type="text/javascript">(window.Image ? (new Image()) : document.createElement('img')).src = 'https://vk.com/rtrg?p=VK-RTRG-121361-eJJQo';</script>

</body>
</html>

