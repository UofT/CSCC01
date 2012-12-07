<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<link href="css/styles.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="css/base/jquery-ui.css" />
<link rel="stylesheet" href="css/smoothness/jquery-ui-1.9.1.custom.css">
<link rel="stylesheet" href="css/jquery.dataTables.css">
<link rel="stylesheet" href="css/fullcalendar.css">
<link rel="stylesheet" href="css/base/jquery.ui.timepicker.css">
<style type="text/css">
.validateTips { border: 1px solid transparent; padding: 0.3em; }
input.text { margin-bottom:12px; width:95%; padding: .4em; }
</style>
<script src="js/jquery-1.8.2.js" type="text/javascript"></script>
<script src="js/jquery-ui-1.9.1.custom.min.js" type="text/javascript" ></script>
<script src="js/jquery.raty.js" type="text/javascript" ></script>
<script src="js/jquery.dataTables.js" type="text/javascript" ></script>
<script src="js/fullcalendar.js" type="text/javascript" ></script>
<script src="js/jquery.ui.timepicker.js" type="text/javascript" ></script>
{$javascript}
<script src="js/script.js" type="text/javascript"></script>
<script type="text/javascript">
	$(function() {
        $( "#tutor_menu" ).menu({
			select: function (event, ui) {
						switch(ui.item.text()) {
							case 'My Profile':
								xajax_showMyProfile('{$userlogin}');
								break;
							case 'My Messages':
								xajax_showMyMessages('{$userlogin}');
								break;
							case 'My Courses':
								xajax_showMyTutorCourses('{$userlogin}');
								break;
							case 'My Students':
								xajax_showMyStudents('{$userlogin}');
								break;
							case 'My Schedule':
								xajax_showMySchedule('{$userlogin}');
								break;
							case 'Register for courses':
								xajax_showCoursesTutor();
								break;
							case 'Look for students':
								xajax_showStudents();
								break;
							case 'Log Out':
								xajax_logOut();
								break;
						}
					}
		});
		
		$( "#dialog-profile-form" ).dialog({
            autoOpen: false,
            height: 250,
            width: 500,
            modal: true,
            buttons: {
                "Save": function() {
                    	xajax_saveTutorDescription(xajax.getFormValues('profile-form'));
                	},
                Cancel: function() {
                    	$( this ).dialog( "close" );
                	}
            },
            close: function() {       }
        });
		
		$( "#dialog-event-form" ).dialog({
            autoOpen: false,
            height: 250,
            width: 500,
            modal: true,
            buttons: {
                "Save": function() {
						if($( "#event_title" ).val() != '' && $( "#event_start_date" ).val() != '' && $( "#event_end_date" ).val() != '' && $( "#event_start_time" ).val() != '' && $( "#event_end_time" ).val() != ''){
                    		xajax_saveTutorSchedule(xajax.getFormValues('event-form'));
						} else {
							alert("Information incomplete!!");
						}
                	},
				"Delete": function() {
                    	xajax_deleteTutorSchedule(xajax.getFormValues('event-form'));
                	},
                Cancel: function() {
                    	$( this ).dialog( "close" );
                	}
            },
            close: function() {       }
        });
		
		$( "#event_start_date" ).datepicker({ minDate: '{$todaydate}' });
		$( "#event_end_date" ).datepicker({ beforeShow: customRange });
		$( "#event_start_time" ).timepicker()
			.change(function () { 
				var start = new Date($( "#event_start_date" ).val());
				var end = new Date($( "#event_end_date" ).val());
				
				if( start.getTime() == end.getTime() ) {
					start = new Date("1/1/1900 " + $( this ).val());
					end = new Date("1/1/1900 " + $( "#event_end_time" ).val());
					
					if( start.getTime() > end.getTime() ) {
						$( "#event_end_time" ).val($( this ).val());
					}
				}
		});
		$( "#event_end_time" ).timepicker()
			.change(function () { 
				var start = new Date($( "#event_start_date" ).val());
				var end = new Date($( "#event_end_date" ).val());
				
				if( start.getTime() == end.getTime() ) {
					start = new Date("1/1/1900 " + $( "#event_start_time" ).val());
					end = new Date("1/1/1900 " + $( this ).val());
					
					if( start.getTime() > end.getTime() ) {
						$( "#event_start_time" ).val($( this ).val());
					}
				}
		});
		
		$( "#dialog-message" ).dialog({
            autoOpen: false,
            height: 450,
            width: 500,
            modal: true,
            buttons: {
                "Send": function() {
                    	xajax_sendMesage(xajax.getFormValues('message-form'));
                	},
				"Delete": function() {
                    	xajax_deleteMesage(xajax.getFormValues('message-form'));
                	},
                Cancel: function() {
                    	$( this ).dialog( "close" );
                	}
            },
            close: function() {       }
        });
	});
	
	function customRange(input) 
	{ 
		var min = new Date('{$todaydate}'), //Set this to your absolute minimum date
        dateMin = min

		if (input.id === "event_end_date") {
			dateMax = new Date; //Set this to your absolute maximum date
			if ($("#event_start_date").datepicker("getDate") != null) {
				dateMin = $("#event_start_date").datepicker("getDate");
			}
		}
		return {
			minDate: dateMin
		}; 
	}
	
	function showCourse(id) {
		xajax_loadCourseCommentRate(id)
	}
	
	function showStudent(id) {
		xajax_loadTutorCommentRate(id)
	}
	
	function showContactStudent(id) {
		xajax_showContactTutor(id);
	}
	
	function showMessage(id) {
		xajax_showMessage(id);
	}
</script>
<title>Tutoring - Tutor menu</title>
</head>

<body>
<div id="dialog-message" title="Message">
	<form id="message-form">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>
              <div id="div-from" hidden="true">
                <label for="message_from">From</label>
                <span id="message_sender" style="font-size:20px"></span>
                <input name="message_from" id="message_from" type="hidden" value="{$userlogin}">
                <input name="message_id" id="message_id" type="hidden" value="">
              </div>
              <div id="div-to" hidden="true">
                <label for="message_to">To</label>
                <span id="message_reciever" style="font-size:20px"></span>
                <input type="hidden" name="message_to" id="message_to">
              </div>
            </td>
          </tr>
          <tr>
            <td><label for="message_subject">Subject</label>
            <input type="text" name="message_subject" id="message_subject"></td>
          </tr>
          <tr>
            <td><textarea name="message_body" cols="60" rows="17" id="message_body"></textarea></td>
          </tr>
        </table>
	</form>
</div>
<div id="dialog-profile-form" title="Profile">
  <form id="profile-form">
    <fieldset>
      <label for="tutor_id">
        <input name="tutor_id" id="tutor_id" type="hidden" value="">
      <label for="tutor_comment">Description</label>
      <textarea name="tutor_comment" cols="50" rows="3" class="text ui-widget-content ui-corner-all" id="tutor_comment"></textarea>
    </fieldset>
  </form>
</div>
<div id="dialog-event-form" title="Schedule">
  <form id="event-form">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><label for="event_title">Title</label>
            <input name="event_title" type="text" id="event_title" size="20">
            <input type="hidden" name="event_id" id="event_id">
            <input type="hidden" name="userlogin" id="userlogin" value="{$userlogin}"></td>
          </tr>
          <tr>
            <td><label for="event_start_date">Start date</label>
            <input name="event_start_date" type="text" id="event_start_date" size="10">&nbsp;<input name="event_start_time" id="event_start_time" value="07:00 AM" size="8" /></td>
          </tr>
          <tr>
            <td><label for="event_end_date">End date</label>
            <input name="event_end_date" type="text" id="event_end_date" size="10">&nbsp;<input name="event_end_time" id="event_end_time" value="07:00 AM" size="8" /></td>
          </tr>
      </table>
  </form>
</div>
<div class="container" style="position:relative;z-index:1;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="150" valign="top"><div id="menu-div" style="z-index: 10; float: left; left: 0; top: 0;">
    <ul id="tutor_menu">
    <li>
    	<a href="#">My Account</a>
        <ul>
        	<li><a href="#">My Profile</a></li>
            <li><a href="#">My Messages</a></li>
            <li><a href="#">My Courses</a></li>
    		<li><a href="#">My Students</a></li>
    		<li><a href="#">My Schedule</a></li>
        </ul>
    </li>
    <li><a href="#">Register for courses</a></li>
    <li><a href="#">Look for students</a></li>
    <li class="ui-state-disabled"><a href="#">&nbsp;</a></li>
    <li><a href="#">Log Out</a></li>
</ul>
  </div>
    </td>
    <td align="left" valign="top"><div id="main">
      <h1>Welcome {$userfirstname}!</h1>
    </div></td>
  </tr>
</table>
    <div id="footer"></div>
</div>
</body>
</html>
