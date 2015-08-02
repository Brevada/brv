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
        <li<?php echo empty($show) || $show == 'overview' ? ' class="active" ' : ''; ?>><a href="?show=overview">Overview<?php echo empty($show) || $show == 'overview' ? ' <span class="sr-only">(current)</span>' : ''; ?></a></li>
        <li<?php echo $show == 'companies' ? ' class="active" ' : ''; ?>><a href="?show=companies">Companies<?php echo $show == 'companies' ? ' <span class="sr-only">(current)</span>' : ''; ?></a></li>
        <li<?php echo $show == 'stores' ? ' class="active" ' : ''; ?>><a href="?show=stores">Stores<?php echo $show == 'stores' ? ' <span class="sr-only">(current)</span>' : ''; ?></a></li>
        <li<?php echo $show == 'accounts' ? ' class="active" ' : ''; ?>><a href="?show=accounts">Accounts<?php echo $show == 'accounts' ? ' <span class="sr-only">(current)</span>' : ''; ?></a></li>
		<li<?php echo $show == 'tablets' ? ' class="active" ' : ''; ?>><a href="?show=tablets">Tablets<?php echo $show == 'tablets' ? ' <span class="sr-only">(current)</span>' : ''; ?></a></li>
		<li<?php echo $show == 'finance' ? ' class="active" ' : ''; ?>><a href="?show=finance">Finance<?php echo $show == 'finance' ? ' <span class="sr-only">(current)</span>' : ''; ?></a></li>
		<li<?php echo $show == 'promotions' ? ' class="active" ' : ''; ?>><a href="?show=promotions">Promo Codes<?php echo $show == 'promotions' ? ' <span class="sr-only">(current)</span>' : ''; ?></a></li>
      </ul>
    </div>
	
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
<?php
if(empty($show) || $show == 'overview'){
	$this->add(new View('../pages/admin/page_overview.php', array('valid' => true)));
} else if($show == 'companies'){
	$this->add(new View('../pages/admin/page_companies.php', array('valid' => true)));
} else if($show == 'stores'){
	$this->add(new View('../pages/admin/page_stores.php', array('valid' => true)));
} else if($show == 'accounts'){
	$this->add(new View('../pages/admin/page_accounts.php', array('valid' => true)));
} else if($show == 'tablets'){
	$this->add(new View('../pages/admin/page_tablets.php', array('valid' => true)));
} else if($show == 'finance'){
	$this->add(new View('../pages/admin/page_finance.php', array('valid' => true)));
} else if($show == 'promotions'){
	$this->add(new View('../pages/admin/page_promotions.php', array('valid' => true)));
}
?>	
	</div>
</div>
</div>