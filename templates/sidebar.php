<?php
global $db, $configuration;

function renderAddonList(array $addons) {
	$output = '';
	if (is_array($addons) && count($addons))
	{
		$output .= '<ul>';
		foreach ($addons as $addon)
		{
			$output .= "<li><a href='" . createLinkUrl('addon', $addon->id) . "'>";
			$output .= "<img src='" . getAddonThumbnail($addon->id, 'addonThumbnailSmall') . "' width='60' height='60' alt='$addon->name' class='pic alignleft' />";
			$output .= "<b>$addon->name</b></a>";
			$output .= "<span class='date'>".$addon->updated."</span>";
			$output .= "</li>";
		}
		$output .= '</ul>';
	}
	return $output;
}
?>		<!-- Sidebar -->
		<div id="sidebar">
			<!-- Tabbed Box -->
			<div class="widget-container">
				<!-- Start Tabbed Box Container -->
				<div id="tabs">
					<!-- Tabs Menu -->
					<ul id="tab-items">
						<li><a href="#tabs-1" title="Popular">Updated</a></li>
						<li><a href="#tabs-2" title="Recent">Newest</a></li>
						<li><a href="#tabs-3" title="Comments">Popular</a></li>
					</ul>
					<!-- Tab Container for menu with ID tabs-1 -->
					<div class="tabs-inner" id="tabs-1">
						<?php
						// Build the Recent Add-ons right hand slider slider
						$recent = $db->get_results("SELECT * FROM addon WHERE 1=1 " . $configuration['addonExcludeClause'] . "  ORDER BY updated DESC LIMIT 5");
						echo renderAddonList($recent);
						?>
					</div>
					<!-- Tab Container for menu with ID tabs-2 -->
					<div class="tabs-inner" id="tabs-2">
						<?php
						$newest = $db->get_results("SELECT * FROM addon WHERE 1=1 " . $configuration['addonExcludeClause'] . " ORDER BY created DESC LIMIT 5");
						echo renderAddonList($newest);
						?>
					</div>
					<!-- Tab Container for menu with ID tabs-3 -->
					<div class="tabs-inner" id="tabs-3">
						<?php
					
					/* // This is a sidebar to show the latest comments. Disabled for now as we are not launching comments yet.
						
						$comment = $db->get_results("SELECT * FROM comment ORDER BY date DESC LIMIT 4"); 
						foreach ($comment as $comments)
						{
							echo "<li><b><a href='details.php?t=".$comments->addonid."'>".$comments->name." says '".$comments->comment."'</a></b>";
							echo "<span class='date'>".$comments->date."</span>";						
							echo "</li>";
						} 
					*/
					
						// Build the Popular Add-ons right hand slider slider
						$popular = $db->get_results("SELECT * FROM addon WHERE 1=1 " . $configuration['addonExcludeClause'] . " AND NOT broken ORDER BY downloads DESC LIMIT 5");
						echo renderAddonList($popular);
						?>
					</div>
				</div>
				<!-- End Tabbed Box Container -->
			</div>
			<!-- Recent Projects Slider -->
			<div class="widget-container widget_recent_projects">
				<h2>Random Add-ons</h2>
				<div class="carousel_container">
					<a class="buttons prev" href="#">left</a>
					<div class="viewport">
						<?php
						// Show some random Add-ons
						$random = $db->get_results("SELECT * FROM addon WHERE 1=1 " . $configuration['addonExcludeClause'] . " AND NOT broken ORDER BY RAND() DESC LIMIT 5");
						if (is_array($popular) && count($popular))
						{
							echo '<ul class="overview">';
							foreach ($random as $randoms)
							{
								echo "<li><div class='thumb'><a href='" . createLinkUrl('addon', $randoms->id) ."'><img src='" . getAddonThumbnail($randoms->id, 'addonThumbnail') . "' height='125' alt='$randoms->name' class='pic' /></a></div>";
								echo "<h5>".substr($randoms->name,0,22)." by ".substr($randoms->provider_name,0,15)."</h5>";
								echo "<p>".str_replace("[CR]","",substr($randoms->description,0,100))."...</p></li>";
							}
							echo '</ul>';
						}
						?>
					</div>
					<a class="buttons next" href="#">right</a>
				</div>
				<div class="clear"></div>
			</div>

			<?php
			$top5 = $db->get_results("SELECT *, COUNT( provider_name ) AS counttotal FROM addon WHERE 1=1 " . $configuration['addonExcludeClause'] . " GROUP BY provider_name ORDER BY counttotal DESC LIMIT 9");
			$counter = 0;
			$iconMap = array(
				1 => 'gold.png',
				2 => 'silver.png',
				3 => 'bronze.png',
			);
			if (is_array($popular) && count($popular)):
			?>
			<!-- Any Widget -->
			<div class="widget-container">
				<h2>Top Developers</h2>
				<ul>
			<?php
				foreach ($top5 as $top5s) {
					$author = cleanupAuthorName($top5s->provider_name);
					$counter++;
					$icon = 'images/' . (isset($iconMap[$counter]) ? $iconMap[$counter] : $counter . '.png');
					echo "<li><img src='$icon' height='20' width='20' alt='Rank $counter' /><a href='" . createLinkUrl('author', $author) . "' title='Show all addons from this author'> " . substr($author, 0, 15) . " ($top5s->counttotal uploads)</a></li>";
				}
			?>
				</ul>
			</div>
			
			<div class="widget-container">
				<h2>Guides</h2>
				<ul>
			<?php
					echo "<li><img src='images/pin.png' height='22' width='22' /><a href='http://wiki.xbmc.org/index.php?title=How_to_install_an_Add-on_from_a_zip_file' > Install an Add-on from a zip file</a></li>";
					echo "<li><img src='images/pin.png' height='22' width='22' /><a href='http://wiki.xbmc.org/index.php?title=How_to_install_an_Add-on_using_the_GUI' > Install an Add-on from the GUI</a></li>";
					echo "<li><img src='images/pin.png' height='22' width='22' /><a href='http://wiki.xbmc.org/index.php?title=Official_add-on_repository' > How to submit an Add-on</a></li>";
					echo "<li><img src='images/pin.png' height='22' width='22' /><a href='http://wiki.xbmc.org/index.php?title=Add-on_development' > Add-on Development</a></li>";
					echo "<li><img src='images/pin.png' height='22' width='22' /><a href='http://wiki.xbmc.org/index.php?title=HOW-TO:HelloWorld_addon' > Hello World Tutorial</a></li>";
					echo "<li><img src='images/pin.png' height='22' width='22' /><a href='http://wiki.xbmc.org/index.php?title=XBMC_Skinning_Tutorials'> Skinning Tutorials</a></li>";
			?>
				</ul>
			</div>
			<?php endif; ?>
		<!-- End Content Wrapper -->
		</div>
