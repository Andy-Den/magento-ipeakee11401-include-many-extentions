<?php

$helper = Mage::helper('balance_contentsetup');

$vorwerkStoreCode = 'vorwerk';
$vorwerkStoreId   = intval(Mage::getModel('core/store')->load($vorwerkStoreCode, 'code')->getId());


$pages = array(

    array(
        'root_template' => 'one_column',
        'identifier'    => 'home',
        'title'         => 'Home',
        'content'       => '<!-- -->',
    ),

    array(
        'identifier'      => 'about-us',
        'title'           => 'About Us',
        'content_heading' => 'Learn More About Vorwerk',
        'content'         => '
            <div class="content-image-right"><img src="{{skin url="images/vorwerk/about_us/vk150_01.jpg"}}" alt="Vac" /></div>
            <h2>Cleanliness and well-being since 1930</h2>
            <p>Vorwerk Vacuum Cleaners are very popular - and have been for generations:</p>
            <p>One in every four households in Germany own a Vorwerk Vacuum Cleaner. Since its invention in 1930, more than 80 million Kobolds have been sold.</p>
            <h2>Our best for your family: Quality with tradition</h2>
            <p>There are good reasons why the Vorwerk Kobold has been so successful. Even today Vorwerk are a step ahead with their innovative Vacuum Cleaners and Vacuum Cleaner Accessories - all with convincing quality and durability.</p>
            <p>"Our Best for you Family" is the foundation of each and every Vorwerk product in the Vorwerk group.</p>
        ',
    ),

    array(
        'identifier'      => 'contact-us',
        'title'           => 'Contact Us',
        'content_heading' => 'Contact Us',
        'content'         => '<!-- -->',
        'layout_update_xml' => '
            <reference name="content">
                <block type="core/template" name="content.contact_form" after="-" template="cms/contact_form.phtml"/>
            </reference>
        ',
    ),

);

foreach ($pages as $page) {
    $page['stores'] = $vorwerkStoreId;
    $helper->createPage($page);
}


$staticBlocks = array(

    array(
        'identifier' => 'vorwerk_home_slide_1',
        'title'      => 'Home - Slide 1',
        'content'    => '
                <p><img src="{{skin url="images/vorwerk/homepage/slideshow/slide3.png"}}" alt="Kobold VC100 - Handheld"></p>
                <p><a href="/" class="button-lookalike orange"><span>Order now!</span></a></p>
            ',
    ),
    array(
        'identifier' => 'vorwerk_home_slide_2',
        'title'      => 'Home - Slide 2',
        'content'    => '
                <p><img src="{{skin url="images/vorwerk/homepage/slideshow/slide1.png"}}" alt="A new generation - 130th anniversary"></p>
                <p><a href="our_products.html" class="button-lookalike"><span>Discover More</span></a></p>
            ',
    ),
    array(
        'identifier' => 'vorwerk_home_slide_3',
        'title'      => 'Home - Slide 3',
        'content'    => '
                <p><img src="{{skin url="images/vorwerk/homepage/slideshow/slide2.png"}}" alt="Kobold SP530 - Vacuum &amp; mop. In one."></p>
                <p><a href="hard_floor_cleaner.html" class="button-eyecatcher"><span>Read More</span></a></p>
            ',
    ),
    array(
        'identifier' => 'vorwerk_home_slide_4',
        'title'      => 'Home - Slide 4',
        'content'    => '
                <p><img src="{{skin url="images/vorwerk/homepage/slideshow/slide4.png"}}" alt="Kobold VR100 - Robot vacuum"></p>
                <p><a href="robotic_vr100.html" class="button-lookalike orange"><span>Find Out More</span></a></p>
            ',
    ),
    array(
        'identifier' => 'vorwerk_home_slide_5',
        'title'      => 'Home - Slide 5',
        'content'    => '
                <p><img src="{{skin url="images/vorwerk/homepage/slideshow/slide5.png"}}" alt="Kobold VK150 - Upright vacuum cleaner"></p>
                <p><a href="kobold_150.html" class="button-lookalike"><span>Find Out More</span></a></p>
            ',
    ),

    array(
        'identifier' => 'vorwerk_home_banner',
        'title'      => 'Home - Banner',
        'content'    => '<a href="press.html"><img src="{{skin url="images/vorwerk/homepage/press_teaser.jpg"}}" alt="Vorwerk Quality - Read all about it!"></a>',
    ),

    array(
        'identifier' => 'vorwerk_home_left',
        'title'      => 'Home - Left',
        'content'    => '
                <div class="image"><a href="book_a_demonstration.html"><img src="{{skin url="images/vorwerk/test_vorwerk_vacuum_teaser.jpg"}}" alt="Test the Vorwerk Vacuum Cleaner" border="0" title="Test the Vorwerk Vacuum Cleaner"/></a></div>
                <h3>Test the kobold Range <br /><span>Book a Demonstration</span></h3>
                <div class="text">
                <p class="body2">Try our Vorwerk Vacuum Cleaner in the comfort of your own home. Our expert advisors will personally give you helpful tips and advice.<br /><br /><br />
                <a href="book_a_demonstration.html" class="link-intern-150">Book a Demonstration</a><br /></p>
                </div>
            ',
    ),
    array(
        'identifier' => 'vorwerk_home_middle',
        'title'      => 'Home - Middle',
        'content'    => '
                <div class="image"><a href="shop_home.html"><img src="{{skin url="images/vorwerk/online_shop_teaser.jpg"}}" alt="Test the Vorwerk Vacuum Cleaner" border="0" title="Test the Vorwerk Vacuum Cleaner"/></a></div>
                <h3>Discover more about how<br /><span>to buy Kobold products</span></h3>
                <div class="text">
                <p class="body2">Whether by contacting us by phone, email or via your personal advisor, we make sure you get the best service for your Kobold products. <br /><br /><br />
                <a href="shop_home.html" class="link-intern-150">More</a><br /></p>
                </div>
            ',
    ),
    array(
        'identifier' => 'vorwerk_home_right',
        'title'      => 'Home - Right',
        'content'    => '
                <div class="image"><a href="careers.html"><img src="{{skin url="images/vorwerk/careers_teaser.jpg"}}" alt="Test the Vorwerk Vacuum Cleaner" border="0" title="Test the Vorwerk Vacuum Cleaner"/></a></div>
                <h3>Work at Vorwerk and<br /><span>Start your Career</span></h3>
                <div class="text">
                <p class="body2">Are you looking for a new professional and challenging career where you can influence your own success and income?<br /><br /><br />
                <a href="careers.html" class="link-intern-150">Become a Vorwerk Advisor</a></p>
                </div>
            ',
    ),

    array(
        'identifier' => 'vorwerk_cms_navigation',
        'title'      => 'CMS Navigation',
        'content'    => '
                <h2>About Us</h2>
                <ol>
                    <li><a href="{{store url="about-us"}}">About Us</a></li>
                    <li><a href="#">Example 1</a></li>
                    <li><a href="#">Example 2</a></li>
                </ol>
                <h2>Example Heading</h2>
                <ol>
                    <li><a href="{{store url="contact-us"}}">Contact Us</a></li>
                    <li><a href="#">Example 4</a></li>
                    <li><a href="#">Example 5</a></li>
                </ol>
            ',
    ),

    array(
        'identifier' => 'vorwerk_right_sidebar_1',
        'title'      => 'Vorwerk Right Sidebar 1',
        'content'    => '
            <h3>Book a Demonstration</h3>
            <p><img src="{{skin url="images/vorwerk/side_bar/book_demo.jpg"}}" alt="Book a demo" style="float: left;">Experience Vorwerk Kobold in the comfort of your own home.</p>
            <p><a href="{{store url="about-us"}}" class="button-lookalike"><span>Book Now</span></a>
        ',
    ),
    array(
        'identifier' => 'vorwerk_right_sidebar_2',
        'title'      => 'Vorwerk Right Sidebar 2',
        'content'    => '
            <h3>Where to Purchase</h3>
            <p><img src="{{skin url="images/vorwerk/side_bar/shop_online.jpg"}}" alt="Shop online" style="float: left;">See details about where and how our products can be purchased.</p>
            <p><a href="{{store url="about-us"}}" class="button-lookalike"><span>More</span></a>
        ',
    ),
    array(
        'identifier' => 'vorwerk_right_sidebar_3',
        'title'      => 'Vorwerk Right Sidebar 3',
        'content'    => '
            <h3>Customer Services</h3>
            <p><img src="{{skin url="images/vorwerk/side_bar/customer_services.jpg"}}" alt="Customer services" style="float: left;"><strong>Hotline: 012346789</strong><br>Monday - Friday<br>9am - 5pm</p>
            <p><a href="{{store url="about-us"}}" class="button-lookalike"><span>Contact Us</span></a>
        ',
    ),

);

foreach ($staticBlocks as $staticBlock) {
    $staticBlock['stores'] = $vorwerkStoreId;
    $helper->createStaticBlock($staticBlock);
}
