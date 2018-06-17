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
importer::import("UI", "Apps");
importer::import("UI", "Presentation");
importer::import("AEL", "Literals");

// Import APP Packages
//#section_end#
//#section#[view]
use \AEL\Literals\appLiteral;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;


if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Library Name
	if (empty($_POST['title']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = appLiteral::get("newProjectDialog", "title");
		$err = $errFormNtf->addErrorHeader("title_h", $err_header);
		$errFormNtf->addErrorDescription($err, "title_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	$project = new btProject();
	$result = $project->create($_POST['title'], $_POST['description']);
	
	// If there is an error in creating the library, show it
	if (!$result)
	{
		$err_header = appLiteral::get("newProjectDialog", "title");
		$err = $errFormNtf->addErrorHeader("project_h", $err_header);
		$errFormNtf->addErrorDescription($err, "project_desc", DOM::create("span", "Error creating project..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}



// Create the dialog frame
$frame = new dialogFrame();
$title = appLiteral::get("newProjectDialog", "title");
$frame->build($title, $action = "", $background = FALSE)->engageApp($appID, "createNewProject");

// Get the form factory to insert controls
$formFactory = $frame->getFormFactory();
// Create simple form template
$form = new simpleForm();


// Project title
$title = appLiteral::get("newProjectDialog", "lbl_projectTitle");
$input = $form->getInput($type = "text", $name = "title", $value = "", $class = "", $autofocus = TRUE, $required = TRUE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);

// Project description
$title = appLiteral::get("newProjectDialog", "lbl_projectDesc");
$input = $form->getTextarea($name = "description", $value = "", $class = "", $autofocus = "", $required = TRUE);
$inputRow = $form->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($inputRow);



// Return output
return $frame->getFrame();
//#section_end#
?>