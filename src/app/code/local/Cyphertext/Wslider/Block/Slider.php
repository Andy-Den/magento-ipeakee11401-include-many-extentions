<?php
class Cyphertext_Wslider_Block_Slider
    extends Mage_Core_Block_Abstract
    implements Mage_Widget_Block_Interface
{

    /**
     * Allows the users to publish their views about your site
     * Highly configurable and very easy installation
     * Author : Prateek Gupta
     * Cyphertext Solutions
     */
    
    protected function _toHtml()
    {
	$image1=$this->getData('image1');
	$image2=$this->getData('image2');
	$image3=$this->getData('image3');
	$image4=$this->getData('image4');
	$image5=$this->getData('image5');
	$image6=$this->getData('image6');
	$image7=$this->getData('image7');
	$image8=$this->getData('image8');
	$image9=$this->getData('image9');
	$image10=$this->getData('image10');
	$image11=$this->getData('image11');
	$image12=$this->getData('image12');
	$image13=$this->getData('image13');
	$image14=$this->getData('image14');
	$image15=$this->getData('image15');
	$image16=$this->getData('image16');
	$image17=$this->getData('image17');
	$height = $this->getData('height');
	$width = $this->getData('width');
	
	if($image1!="")
	{
	$img1=$this->getSkinUrl('logo/').$image1;
	}
	if($image2!="")
	{
	$img2=$this->getSkinUrl('logo/').$image2;
	}
	if($image3!="")
	{	
	$img3=$this->getSkinUrl('logo/').$image3;
	}
	if($image4!="")
	{	
	$img4=$this->getSkinUrl('logo/').$image4;
	}
	if($image5!="")
	{	
	$img5=$this->getSkinUrl('logo/').$image5;
	}
	if($image6!="")
	{	
	$img6=$this->getSkinUrl('logo/').$image6;	
	}
	if($image7!="")
	{	
	$img7=$this->getSkinUrl('logo/').$image7;
	}
	
	if($image8!="")
	{
	$img8=$this->getSkinUrl('logo/').$image8;
	}
	if($image9!="")
	{
	$img9=$this->getSkinUrl('logo/').$image9;
	}
	if($image10!="")
	{	
	$img10=$this->getSkinUrl('logo/').$image10;
	}
	if($image11!="")
	{	
	$img11=$this->getSkinUrl('logo/').$image11;
	}
	if($image12!="")
	{	
	$img12=$this->getSkinUrl('logo/').$image12;
	}
	if($image13!="")
	{	
	$img13=$this->getSkinUrl('logo/').$image13;	
	}
	if($image14!="")
	{	
	$img14=$this->getSkinUrl('logo/').$image14;
	}
	if($image15!="")
	{	
	$img15=$this->getSkinUrl('logo/').$image15;
	}
	if($image16!="")
	{	
	$img16=$this->getSkinUrl('logo/').$image16;
	}
?>
	<head>
	
     <script type="text/javascript" src="<?php echo $this->getSkinUrl('js/jquery.simplyscroll-1.0.4.js'); ?>"></script>
	<link rel="stylesheet" href="<?php echo $this->getSkinUrl('js/jquery.simplyscroll-1.0.4.css'); ?>" type="text/css" />
	</head>
	<script type="text/javascript">
	var j$ = jQuery.noConflict();	
(function(j$) {
	j$(function() { 
		j$("#scroller").simplyScroll({
			autoMode: 'loop'
		});
	});
})(jQuery);
</script>

	
	 
	
<?php
	$html = ' <ul id="scroller">
	<li><img src="'.$img1.'" width="'.$width.'" height="'.$height.'"></li>
	<li><img src="'.$img2.'" width="'.$width.'" height="'.$height.'"></li>
	<li><img src="'.$img3.'" width="'.$width.'" height="'.$height.'"></li>
	<li><img src="'.$img4.'" width="'.$width.'" height="'.$height.'"></li>
	<li><img src="'.$img5.'" width="'.$width.'" height="'.$height.'"></li>
	<li><img src="'.$img6.'" width="'.$width.'" height="'.$height.'"></li>
	<li><img src="'.$img7.'" width="'.$width.'" height="'.$height.'"></li>
	<li><img src="'.$img8.'" width="'.$width.'" height="'.$height.'"></li>
	<li><img src="'.$img9.'" width="'.$width.'" height="'.$height.'"></li>
	<li><img src="'.$img10.'" width="'.$width.'" height="'.$height.'"></li>
	<li><img src="'.$img11.'" width="'.$width.'" height="'.$height.'"></li>
	<li><img src="'.$img12.'" width="'.$width.'" height="'.$height.'"></li>
	<li><img src="'.$img13.'" width="'.$width.'" height="'.$height.'"></li>
	<li><img src="'.$img14.'" width="'.$width.'" height="'.$height.'"></li>
	<li><img src="'.$img15.'" width="'.$width.'" height="'.$height.'"></li>
	<li><img src="'.$img16.'" width="'.$width.'" height="'.$height.'"></li>
</ul>';
return $html;
			
	
	  
 
    }
	
}

?>





