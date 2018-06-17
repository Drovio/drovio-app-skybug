<?php
//#section#[header]
// Use Important Headers
use \API\Platform\importer;
use \Exception;

// Check Platform Existance
if (!defined('_RB_PLATFORM_')) throw new Exception("Platform is not defined!");

// Import DOM, HTML
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

use \UI\Html\DOM;
use \UI\Html\HTML;

// Import application for initialization
importer::import("AEL", "Platform", "application");
use \AEL\Platform\application;

// Increase application's view loading depth
application::incLoadingDepth();

// Set Application ID
$appID = 37;

// Init Application and Application literal
application::init(37);
// Secure Importer
importer::secure(TRUE);

// Import SDK Packages
importer::import("API", "Profile");
importer::import("UI", "Apps");
importer::import("DEV", "BugTracker");

// Import APP Packages
//#section_end#
//#section#[view]
use \API\Profile\team;
use \AEL\Literals\appLiteral;
use \UI\Apps\APPContent;
use \DEV\BugTracker\btProject;
use \DEV\BugTracker\btLibrary;
use \DEV\BugTracker\btException;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "projectListContainer", TRUE);


// Get team projects (if active team)
$projectList = HTML::select(".projectList .pList.team")->item(0);
$teamProjectIDs = array();
$teamID = team::getTeamID();
if (empty($teamID))
	DOM::replace($projectList, NULL);
else
{
	$teamProjects = btLibrary::getTeamProjects();
	foreach ($teamProjects as $project)
	{
		$teamProjectIDs[] = $project['id'];
		$pTile = getProjectTitle($project, $appID, $actionFactory);
		DOM::append($projectList, $pTile);
	}
}


// Get public projects
$projectList = HTML::select(".projectList .pList.public")->item(0);
$publicProjects = btLibrary::getPublicProjects();
$counter = 0;
foreach ($publicProjects as $project)
	if (in_array($project['id'], $teamProjectIDs))
		continue;
	else
	{
		$counter++;
		$pTile = getProjectTitle($project, $appID, $actionFactory);
		DOM::append($projectList, $pTile);
	}

if ($counter == 0)
	DOM::replace($projectList, NULL);


// Return output
return $appContent->getReport();


function getProjectTitle($project, $appID, $actionFactory)
{
	$pTile = DOM::create("div", "", "", "ptile");
	
	// Project details
	$details = DOM::create("div", "", "", "details");
	DOM::append($pTile, $details);
	
	// Project versions
	$btPrj = new btProject($project['id']);
	$attr = array();
	$attr['count'] = count($btPrj->getReleases());
	$text = appLiteral::get("projectList", "lbl_versionCount", $attr);
	$literal = DOM::create("span", $text, "", "det_pver");
	DOM::append($details, $literal);
	
	$bull = DOM::create("span", "", "", "bull");
	DOM::innerHTML($bull, "&bull;");
	DOM::append($details, $bull);
	
	// Project issues
	$issues = $btPrj->getIssues();
	$issuesCount = 0;
	foreach ($issues as $ver => $is)
		$issuesCount += count($is);
	$attr = array();
	$attr['count'] = $issuesCount;
	$text = appLiteral::get("projectList", "lbl_issueCount", $attr);
	$literal = DOM::create("span", $text, "", "det_issues");
	DOM::append($details, $literal);
	
	$bull = DOM::create("span", "", "", "bull");
	DOM::innerHTML($bull, "&bull;");
	DOM::append($details, $bull);
	
	// Project exceptions
	$btExc = new btException($project['id']);
	$excs = $btExc->getAll();
	$attr = array();
	$attr['count'] = count($excs);
	$text = appLiteral::get("projectList", "lbl_exceptionCount", $attr);
	$literal = DOM::create("span", $text, "", "det_excs");
	DOM::append($details, $literal);
	
	// Project title
	$pTitle = DOM::create("div", $project['title'], "", "title");
	DOM::append($pTile, $pTitle);
	
	$attr = array();
	$attr['pid'] = $project['id'];
	$actionFactory->setAppAction($pTitle, $appID, "issues/projectIssues", ".trackerContext", $attr);
	
	return $pTile;
}
//#section_end#
?>