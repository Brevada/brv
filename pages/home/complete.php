<?php
$this->add(new View('../template/home_header.php'));
$this->addResource('/css/index.css');
$this->addResource('/css/complete.css');
$this->setTitle('Complete Feedback Systems');
?>
<div>
  <div id="complete_title">
  	<h1>The Components of a Complete Feedback System</h1>
    <h2>Brevada is the world's first <strong>Complete</strong> Feedback System - using <span id="emphasis">Feedback Marketing, Feedback Gathering, and Feedback Management</span> tools to guarantee businesses effectively gather and use feedback.</h2>
  </div>
  <div id='tab_bar_container'>
	  <div id='tab_bar'>
	   <ul class='tabs'>
		<li><a href='#tab1'><div id="tab_button">1. Feedback Marketing</div></a></li>
		<li><a href='#tab2'><div id="tab_button">2. Feedback Gathering</div></a></li>
		<li><a href='#tab3'><div id="tab_button">3. Feedback Management</div></a></li>
		 <br style="clear:both;" />
		</ul>
	  </div>
  </div>
  <!-- FEEDBACK MARKETING -->
  <div class="page" id='tab1'>
    <div class="tab_info">
        <img id="tab_logo" src="/images/logo_marketing.png" />
        <br style="clear:both;" />
        Feedback Marketing
        <div id="tab_description">
        Feedback Marketing is the practice of communicating your feedback presence to your customers. This means linking your customers to your Brevada Page. Feedback Marketing also involves showing off your positive feedback, and just showing off your efforts to gather feedback!
        </div>      
    </div>
    <div class="tab_features">
    	Brevada's Feedback Marketing Features:
        <div class="tab_feature">
            <div id="tab_feature_title">Custom URL and Barcodes</div>
            Businesses using Brevada recieve their own custom URL and QR Barcode to make directing their customers to their <a href="#tab2">Brevada Page</a> easy.
            For example, a restaurant called <span id="emphasis">Deli Room</span> would direct their customers to 
            <span id="emphasis"><a href='http://brevada.com/deliroom' target='_blank'>brevada.com/deliroom</a></span> (Try it!) or to their mobile page through their QR code. 
            <br /><br />
        </div>
        
        <img id="feature_img" src="/images/promo_computer.png" />
     
        <div class="tab_feature">
        	<div id="tab_feature_title">Auto Generated Marketing Materials</div>
            Businesses using Brevada have all they need properly employ feedback marketing. Once signed up, businesses get 
            auto-generated, printable marketing material - showing their brevada URL and QR code that link to their <span id="emphasis">Brevada Page</span>. Displaying Brevada's printable 
            marketing material in store is the most common method of feedback marketing used by businesses.  
        </div>
        
    </div>
  </div>
  
   <!-- FEEDBACK GATHERING -->
  <div class="page" id='tab2'>
    <div class="tab_info">
    	
        <img id="tab_logo" src="<?php echo p('HTML','path_images','logo_page.png'); ?>" />
        <br style="clear:both;" />
        Feedback Gathering
        
        <div id="tab_description">
        Feedback Gathering is the practice of using various tools to gather feedback from your customers. The principle behind Brevada's Feedback Gathering tools is <span id="emphasis">groundbreaking simplicity and ease of use</span>. Unlike tedious survey solutions, Brevada's feedback gathering tools allow customers to give feedback with just one click!
        </div>      
        
    </div>
    
    <div class="tab_features">
    	Brevada's Feedback Gathering Tools:
        
        
        <br style="clear:both;" />
        
        <img id="feature_img" src="<?php echo p('HTML','path_images','page_computer.png'); ?>" />
     
        
        <div class="tab_feature">
        	<div id="tab_feature_title">The Brevada Page</div>
           	The Brevada Page is the <span id="emphasis">ultimate, one stop location for customers to give feedback</span>. Here customers can rate or comment on any of the products, services, or aspects of a business in seconds. Customers can also provide general comments or suggestions as well as attach their email to the feedback they leave.
            <br /> <br />
            The Brevada Page is compatible on all mobile devices, allowing customers to give feedback from anywhere 
        </div>
        
        
        <div class="tab_feature">
        	<div id="tab_feature_title">Email Feedback Acquisition</div>
			Brevada's Email Feedback Acquisition system allows Businesses to send feedback request emails to customers - allowing their customers to give feedback right from the email!
        </div>
        
        <div class="tab_feature">
        	<div id="tab_feature_title">Widgets and Website Integration</div>
            Brevada provides code for integration Brevada into websites, third party email systems, or other software. This ensures that a business <span id="emphasis">can recieve feedback from wherever they want</span> in addition to feedback tools hosted on Brevada.
        </div>
        
        <div class="tab_feature">
        	<div id="tab_feature_title">Voting Station</div>
            The voting station is designed to run on tablets at a businesses location. The voting station auto-refreshes every 10 seconds without activity - meaning that it can be left in a waiting room or store front.
            <br style="clear:both;" />
        </div>
        
    </div>
    
  </div>
  
  
   <!-- FEEDBACK MANAGEMENT -->
  <div class="page" id="tab3">
    <div class="tab_info">
    	
        <img id="tab_logo" src="/images/logo_communicate.png" />
        <br style="clear:both;" />
        Feedback Management
        
        <div id="tab_description">
        Feedback Management is taking full advantage of feedback you recieve to ensure the satisfaction of your customers and the growth of your business.
        </div>      
        
    </div>
    
    <div class="tab_features">
    	Brevada's Feedback Management Tools:
        
                <br style="clear:both;" />
        
        <img id="feature_img" src="/images/2014_hub.png" />
        
        <div class="tab_feature">
        	<div id="tab_feature_title">Graphing and Data Display</div>
        	Brevada's robust backend system allows businesses to properly view and interpret their responses.   	
        </div>
        
        <div class="tab_feature">
        	<div id="tab_feature_title">2 Way Communication</div>
			With all Brevada Feedback Gathering tools, customers have the option to provide their email address. Brevada's backend system combines, averages, and displays each customers feedback - allowing you to properly deal with any issues or suggestions and ensure customer retention.
        </div>
        
           <div class="tab_feature">
        	<div id="tab_feature_title">Brevada Certificates</div>
            Brevada's certificate system allows businesses to <span id="emphasis">advertise their positive reviews</span> on their website, through social media, or with printable material.
        </div>
        
        <div class="tab_feature">
        	<div id="tab_feature_title">Brevada Approved</div>
            Businesses using brevada are <span id="emphasis">taking the necesarry steps to ensure their customers are satisfied</span> - and this is something we believe is worth sharing. Brevada Approved allows businesses to let their efforts be known on their website, through social media, or in store.
            <a href="approved" id="emphasis">Learn More</a>
            <br style="clear:both;" />
        </div>
        
 
        
    </div>
    
  </div>
  
  <br style="clear:both;"/>
  
  <script type='text/javascript'>
    $('ul.tabs').each(function(){
    // For each set of tabs, we want to keep track of
    // which tab is active and it's associated content
    var $active, $content, $links=$(this).find('a');

    // If the location.hash matches one of the links, use that as the active tab.
    // If no match is found, use the first link as the initial active tab.
    $active=$($links.filter('[href="'+location.hash+'"]')[0] || $links[0]);
    $active.addClass('active');

    $content=$($active[0].hash);

    // Hide the remaining content
    $links.not($active).each(function () {
      $(this.hash).hide();
    });

    // Bind the click event handler
    $(this).on('click', 'a', function(e){
      // Make the old tab inactive.
      $active.removeClass('active');
      $content.hide();

      // Update the variables with the new link and content
      $active=$(this);
      $content=$(this.hash);

      // Make the tab active.
      $active.addClass('active');
      $content.show();

      // Prevent the anchor's default click action
      e.preventDefault();
    });
  });
  </script>
</div>
<?php $this->add(new View('../template/long_footer.php')); ?>