<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id$

if (!$offset || $offset < 0) {
	$offset=0;
}

if (!$chunksz) { $chunksz = 15; }

if (($msort != 0) && ($msort != 1)) { $msort = 0; }
if (!$msort) { $msort = 0; }
if (user_isloggedin() && !isset($morder)) {
    $morder = user_get_preference('commit_browse_order'.$group_id);
}

if (isset($order)) {

    if ($order != '') {
	// Add the criteria to the list of existing ones
	$morder = commit_add_sort_criteria($morder, $order, $msort);
    } else {
	// reset list of sort criteria
	$morder = '';
    }
}

if (isset($morder)) {

    if (user_isloggedin()) {
	if ($morder != user_get_preference('commit_browse_order'.$group_id))
	    user_set_preference('commit_browse_order'.$group_id, $morder);
    }

    if ($morder != '') {
	$order_by = ' ORDER BY '.commit_criteria_list_to_query($morder);
    }
}


// get project name
$sql = "SELECT unix_group_name from groups where group_id=$group_id";

$result = db_query($sql);
$projectname = db_result($result, 0, 'unix_group_name');

//
// Memorize order by field as a user preference if explicitly specified.
// Automatically discard invalid field names.
//
if ($order) {
	if ($order=='id' || $order=='description' || $order=='date' || $order=='submitted_by') {
		if(user_isloggedin() &&
		   ($order != user_get_preference('commits_browse_order')) ) {
			user_set_preference('commits_browse_order', $order);
		}
	} else {
		$order = false;
	}
} else {
	if(user_isloggedin()) {
		$order = user_get_preference('commits_browse_order');
	}
}


if (!$set) {
	/*
		if no set is passed in, see if a preference was set
		if no preference or not logged in, use my set
	*/
	if (user_isloggedin()) {
		$custom_pref=user_get_preference('commits_browcust'.$group_id);
		if ($custom_pref) {
			$pref_arr=explode('|',$custom_pref);
			$_commit_id=$pref_arr[0];
			$_commiter=$pref_arr[1];
			$_tag=$pref_arr[2];
			$_branch=$pref_arr[3];
			$chunksz=$pref_arr[4];
			$set='custom';
		} else {
			$set='custom';
			$_commiter=0;
		}
	} else {
		$_commiter=0;
		$set='custom';
	}
}

if ($set=='my') {
	/*
		My commits - backwards compat can be removed 9/10
	*/
	$_tag=100;
	$_commiter=user_getname();
	$_branch=100;

} else if ($set=='custom') {
	/*
		if this custom set is different than the stored one, reset preference
	*/
	$pref_=$_commit_id.'|'.$_commiter.'|'.$_tag.'|'.$_branch.'|'.$chunksz;
	if ($pref_ != user_get_preference('commits_browcust'.$group_id)) {
		//echo 'setting pref';
		user_set_preference('commits_browcust'.$group_id,$pref_);
	}
} else if ($set=='any') {
	/*
		Closed commits - backwards compat can be removed 9/10
	*/
	$tag=$branch=$_commiter=100;
} 

/*
	Display commits based on the form post - by user or status or both
*/

//if tag selected, and more to where clause
if ($_tag && ($_tag != 100)) {
	//for open tasks, add status=100 to make sure we show all
	$tag_str="AND cvs_checkins.stickytag='$_tag'";
} else {
	//no status was chosen, so don't add it to where clause
	$tag_str='';
}

//if status selected, and more to where clause
if ($_branch && ($_branch != 100)) {
	//for open tasks, add status=100 to make sure we show all
	$branch_str="AND cvs_checkins.branchid='$_branch'";
} else {
	//no status was chosen, so don't add it to where clause
	$branch_str='';
}

//if assigned to selected, and more to where clause
if ($_commit_id != '') {
  $commit_str="AND cvs_commits.id='$_commit_id' AND cvs_checkins.commitid != 0 ";
} else {
  $commit_str='';
}

if ($_commiter && ($_commiter != 100)) {
	$commiter_str="AND user.user_id=cvs_checkins.whoid ".
	  "AND user.user_name='$_commiter' ";
} else {
	//no assigned to was chosen, so don't add it to where clause
	$commiter_str='';
}

if ($_srch != '') {
  $srch_str = "AND cvs_descs.description like '%".$_srch."%' ";
} else {
  $srch_str = "";
}

//build page title to make bookmarking easier
//if a user was selected, add the user_name to the title
//same for status

//commits_header(array('title'=>'Browse Commits'.
//	(($_assigned_to)?' For: '.user_getname($_assigned_to):'').
//	(($_tag && ($_tag != 100))?' By Status: '. get_commits_status_nam//e($_status):''),
//		   'help' => 'CommitsManager.html'));

$select = 'SELECT distinct cvs_checkins.commitid as id, cvs_descs.id as did, cvs_descs.description, IF (cvs_checkins.commitid > 0, cvs_commits.comm_when, cvs_checkins.ci_when) as c_when, IF (cvs_checkins.commitid > 0, cvs_commits.comm_when, CONCAT(YEAR(cvs_checkins.ci_when),LEFT(RIGHT(cvs_checkins.ci_when,14),2), LEFT(RIGHT(cvs_checkins.ci_when,11),2), LEFT(RIGHT(cvs_checkins.ci_when,8),2),LEFT(RIGHT(cvs_checkins.ci_when,5),2), RIGHT(cvs_checkins.ci_when, 2))) as f_when, user.user_name as who ';
$from = "FROM cvs_descs, cvs_checkins, user, cvs_repositories, cvs_commits ";
$where = "WHERE cvs_checkins.descid=cvs_descs.id ".
	"AND (cvs_checkins.commitid='0' OR cvs_checkins.commitid=cvs_commits.id) ".
	"AND user.user_id=cvs_checkins.whoid ".
        "AND cvs_checkins.repositoryid=cvs_repositories.id ".
        "AND cvs_repositories.repository='/cvsroot/".$projectname."' ".
	"$commiter_str ".
        "$commit_str ".
	"$srch_str ".
	"$branch_str ";


if (!$pv) { $limit = " LIMIT $offset,$chunksz";}

if ($order_by == '') {
  $order_by = " ORDER BY id desc, f_when desc ";
}

$sql=$select.
$from.
$where.
$order_by.
$limit;

$statement='Viewing commits';
//print ($sql);
$result=db_query($sql);

/* expensive way to have total rows number didn'get a cheaper one */

$sql1=$select.
$from.
$where; 
$result1=db_query($sql1);
$totalrows = db_numrows($result1);


/*
	creating a custom technician box which includes "any"
*/

$res_tech=commits_data_get_technicians ($group_id);	

$rows=db_numrows($res_tech);



##$tech_id_arr=util_result_column_to_array($res_tech,0);
##$tech_id_arr[]='0';  //this will be the 'any' row

$tech_name_arr=util_result_column_to_array($res_tech,1);
#$tech_name_arr[]='Any';

#$tech_box=html_build_select_box_from_arrays ($tech_name_arr, $tech_name_arr, '_commiter', $_commiter, true, 'Any');

$tech_box=commits_technician_box($group_id, '_commiter', $_commiter, 'Any');



/*
	Show the new pop-up boxes to select assigned to and/or status
*/
echo '<H3>Browse commits by:</H3>'; 
echo '<FORM name="commit_form" ACTION="'. $PHP_SELF .'" METHOD="GET">
        <TABLE WIDTH="10%" BORDER="0">
	<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="'.$group_id.'">
	<INPUT TYPE="HIDDEN" NAME="func" VALUE="browse">
	<INPUT TYPE="HIDDEN" NAME="set" VALUE="custom">
        <TR align="center"><TD><b>ID</b></TD><TD><b>Branches</b></TD><TD><b>Commiter</b></TD><TD><b>Keyword search in log message</b></TD>'.
        ##'<TD><b>Commit date</b></TD>'.
        '</TR>'.
        '<TR><TD><INPUT TYPE="TEXT" SIZE=5 NAME=_commit_id VALUE='.$_commit_id.'></TD><TD><FONT SIZE="-1">'. commits_branches_box($group_id,'_branch',$_branch, 'Any') .'</TD>
	<TD><FONT SIZE="-1">'. $tech_box .
        '</TD><TD><FONT SIZE="-1">'. '<INPUT type=text size=35 name=_srch value='.$_srch.
        '></TD>'.
        ##'<TD nowrap><font SIZE="-1"><select name="_commit_date_op">'.
        ##'<option VALUE=">" SELECTED>&gt;</option><option VALUE="=">=</option>'.
        ##'<option VALUE="<">&lt;</option></select>'.
        ##'<input TYPE="text" name="_commit_date" size="10" MAXLENGTH="15" VALUE="'.$_commit_date.'"><a href="javascript:show_calendar(\'document.commit_form._commit_date\', document.commit_form._commit_date.value,\'savannah\',\'_normal\');"><img src="/images/savannah.theme/calendar/cal.png" width="16" height="16" border="0" alt="Click Here to Pick up a date"></a></TD>'.
       '</TR></TABLE>'.
	
'<br><FONT SIZE="-1"><INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="Browse">'.
' <input TYPE="text" name="chunksz" size="3" MAXLENGTH="5" '.
'VALUE="'.$chunksz.'">commits at once.'.
'</FORM>';


if ($result && db_numrows($result) > 0) {

	//create a new $set string to be used for next/prev button
	if ($set=='custom') {
	  $set .= '&_branch='.$_branch.'&_commiter='.$_commiter.'&_tag='.$_tag.'&chunksz='.$chunksz;
	} else if ($set=='any') {
	  $set .= '&_branch=100&_commiter=100&_tag=100&chunksz='.$chunksz;
	}

	show_commitslist($result,$offset,$totalrows,$set,$_commiter,$_tag, $_branch, $chunksz, $morder, $msort);

} else {
	echo '
		<P>
		<H3>'.$statement.'</H3>
		<P>
		<P>';
	echo '
		<H1>No Commits Match Your Criteria</H1>';
	echo db_error();
}

//commits_footer(array());

?>
