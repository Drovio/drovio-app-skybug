jq = jQuery.noConflict();
jq(document).one("ready", function() {
	
	// Switch action
	jq(document).on("skybug.switch", function(ev, value) {
		switch (value) {
			case "issues":
				switchToIssues();
				break;
			case "projects":
				switchToHome();
				break;
		}
	});
	
	// Home reset action
	jq(document).on("click", ".issueTrackerMainPage .navs .navitem", function() {
		if (jq(this).hasClass("home"))
			switchToHome();
	});
	
	function switchToHome()
	{
		jq(".projectListMainContainer").removeClass("closed");
		jq(".trackerContext").removeClass("open");
	}
	
	function switchToIssues()
	{
		jq(".projectListMainContainer").addClass("closed");
		jq(".trackerContext").addClass("open");
	}
});