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
importer::import("AEL", "Literals");
importer::import("DEV", "BugTracker");

// Import APP Packages
//#section_end#
//#section#[view]
use \API\Profile\team;
use \API\Profile\account;
use \AEL\Literals\appLiteral;
use \UI\Apps\APPContent;
use \DEV\BugTracker\btLibrary;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "skybug", TRUE);


// Set navigation
$accountInfo = account::info();
$teamID = team::getTeamID();
$navigationContainer = HTML::select(".issueTrackerMainPage .header .actions")->item(0);
if (empty($accountInfo))
{
	$title = appLiteral::get("main", "lbl_register");
	$weblink = $appContent->getWeblink($href = "http://my.redback.gr/register.php", $title, $target = "_blank");
	
	// Empty navigation
	DOM::innerHTML($navigationContainer, "");
	DOM::append($navigationContainer, $weblink);
}
else if (empty($teamID))
{
	$title = appLiteral::get("main", "lbl_createTeam");
	$weblink = $appContent->getWeblink($href = "http://my.redback.gr/relations/", $title, $target = "_blank");
	
	// Empty navigation
	DOM::innerHTML($navigationContainer, "");
	DOM::append($navigationContainer, $weblink);
}
else
{
	// Add action to create new project
	$createProjectButton = HTML::select(".issueTrackerMainPage .header .actions .createProject")->item(0);
	$actionFactory->setAppAction($createProjectButton, $appID, "createNewProject");
}

// Load initial project's list
$projectListContainer = HTML::select(".projectListMainContainer")->item(0);
$projectList = $appContent->getAppContainer($appID, $viewName = "issues/projectList", $attr = array(), $startup = TRUE, $containerID = "projectList");
DOM::append($projectListContainer, $projectList);

// Return output
return $appContent->getReport();
//#section_end#
?>