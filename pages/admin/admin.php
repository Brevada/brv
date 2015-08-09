<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }

$this->addResource('/css/admin.css');
$this->addResource('/js/jquery.tablesorter.min.js');
$this->addResource('/js/admin.js');

/*
	Promo Codes
	Finance
	
	Account (Edit/Link to Corporate)
	Companies
	Stores
	
	Add/Remove Categories/Keywords/AspectTypes
	
	Tablets
	
	Stats
	
	Logs
*/

$show = Brevada::FromGET('show');
if($show != 'overview' && $show != 'companies' && $show != 'stores' && $show != 'accounts' && $show != 'tablets' && $show != 'finance' && $show != 'promotions' && $show != 'newclient' && $show != 'salesfaq'){
	$show = 'overview';
}

?>
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Brevada Admin Panel</a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
      <ul class="nav navbar-nav navbar-right">
        <li><a href="/home/logout.php">Logout</a></li>
      </ul>
      <form class="navbar-form navbar-right">
        <input type="text" class="form-control" placeholder="Search...">
      </form>
    </div>
  </div>
</nav>

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-3 col-md-2 sidebar">
      <ul class="nav nav-sidebar">
        <li data-page='overview'><a href="?show=overview">Overview</a></li>
      </ul>
      <ul class="nav nav-sidebar">
        <li data-page='companies'><a href="?show=companies">Companies</a></li>
        <li data-page='stores'><a href="?show=stores">Stores</a></li>
        <li data-page='accounts'><a href="?show=accounts">Accounts</a></li>
      </ul>
      <ul class="nav nav-sidebar">
		<li data-page='tablets'><a href="?show=tablets">Tablets</a></li>
		<li data-page='finance'><a href="?show=finance">Finance</a></li>
		<li data-page='promotions'><a href="?show=promotions">Promo Codes</a></li>
      </ul>
      <ul class="nav nav-sidebar">
		<li data-page='newclient'><a href="?show=newclient">Setup New Client</a></li>
      </ul>
      <ul class="nav nav-sidebar">
		<li data-page='salesfaq'><a href="?show=salesfaq">Sales FAQ</a></li>
      </ul>
    </div>
	
<script type='text/javascript'>
$('ul.nav-sidebar > li').removeClass('active');
$('ul.nav-sidebar > li[data-page="<?php echo $show;?>"]').addClass('active');
</script>
	
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
<?php
$this->add(new View("../pages/admin/page_{$show}.php", array('valid' => true)));
?>	
	</div>
</div>
</div>

