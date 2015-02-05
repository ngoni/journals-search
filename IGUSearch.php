<?php
/*
	Plugin Name: IGU Journals Search
	Description: IGU Journals front-end search
*/

	wp_enqueue_script("iguScript-js", plugin_dir_url( __FILE__ ) . "js/iguScript.js", array("jquery"), "" );
	wp_enqueue_script("iguScript-js");
	
	wp_register_style( 'front', plugin_dir_url( __FILE__ ) . "css/frontEndCSS.css", "", ""  );
	wp_enqueue_style( 'front' );
	
	wp_localize_script( 'iguScript-js', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	
	add_action('wp_ajax_the_ajax_hook', 'igutoggle');
	add_action('wp_ajax_nopriv_the_ajax_hook', 'igutoggle');

	function igutoggle(){

		if($_POST === " "){
			echo "<p>Oops please type something...</p>";
			die();
		}
			
		$needle = ($_POST['name']);
		$filter = strtolower($_POST['filter']);
		
		if($needle === null || $needle == "" || $filter === null || $filter == ""){
			echo "<p>Oops please type something...</p>";
			die();
		}
		
		$categories = array ("name_of_journal", "country", "issn", "editor", "isi_category", "all", "keyword");
		
		$columns = array(
		'country' => 'Country',
		'name_of_journal' => 'Journal Name',
'print_issn' => 'Print ISSN',
		'e_issn' => 'eISSN',
		'city_of_publication' => 'City of Publication',
		'name_of_publishing_company' => 'Publishing Company',
		'editor' => 'Editor',
		'editor_email_address' => 'Editor email Addres',
		'language' => 'Publication language',
		'since' => 'Since',
		'isi' => 'ISI',
		'isi_category' => 'ISI Category',
		'5_year_impact_factor' => '5 Year Impact Factor'
		);
		
		if( in_array( $filter, $categories ) ){
			$result = getBy( $filter, $needle, null, null );
			if($result != null ){
				echo json_encode($result);//toHTML( $result, $columns ); echo json_encode($result);
				die();
			}else
				json_encode (new stdClass);//"<p>Oops we could not find a matching result..</p>";//echo json_encode()
		}else
				json_encode (new stdClass);//echo "<p>Oops please type something...</p>";
		die();
	}

	function getBy( $column = null, $needle = null){
	
		global $wpdb;

		if($column == "name_of_journal")
			$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM wp_igu_journals WHERE name_of_journal LIKE '%".$needle."%'";
		if($column == "country")
			$sql = country($needle);
		if($column == "keyword")
			$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM wp_igu_journals WHERE isi_category LIKE '%".$needle."%'";
		if($column == "editor")
			$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM wp_igu_journals WHERE editor LIKE '%".$needle."%'";
		if($column == "issn")
		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM wp_igu_journals WHERE print_issn LIKE '%".$needle."%' OR e_issn LIKE '%".$needle."%'";
		if($column == "all")
			$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM wp_igu_journals WHERE name_of_journal LIKE '%".$needle."%' OR country LIKE '%".$needle."%' OR editor LIKE '%".$needle."%' OR isi_category LIKE '%".$needle."%' OR print_issn LIKE '%".$needle."%' OR e_issn LIKE '%".$needle."%'";
			
		return $wpdb->get_results( $sql, ARRAY_A );
	}

	function country($needle){
	
		//$uk = array('united kingdom', 'u.k', 'england', 'wales', 'scotland', 'britain', 'great britain', 'g.b');
		$uk = array('united kingdom', 'u.k', 'uk', 'england');
		$usa = array('united states', 'united states of america', 'u.s.a', 'usa', 'america');
		$sa = array('south africa', 's.a');

		if( in_array($needle, $uk) == true)
			return "SELECT SQL_CALC_FOUND_ROWS * FROM wp_igu_journals WHERE country LIKE 'united kingdom'";
		if( in_array($needle, $usa) == true )
			return "SELECT SQL_CALC_FOUND_ROWS * FROM wp_igu_journals WHERE country LIKE 'united states of america'";
		if( in_array($needle, $sa) == true )
			return "SELECT SQL_CALC_FOUND_ROWS * FROM wp_igu_journals WHERE country LIKE 'south africa'";
		else
			return "SELECT SQL_CALC_FOUND_ROWS * FROM wp_igu_journals WHERE country LIKE '%".$needle."%'";
	}
	
	/*
		GetBy should return a json object to be sent to client browser.
		Creating table to display data should occur in client browser.
		Code should be in javascript or jquery.
	*
	function toHTML( $result = null, $columns = null ){
		
		$toHtml =
		'<table id="result">
			<thead>
				<tr>
					<th></th>
				</tr>
			</thead>';
			foreach($result as $journal){
	$toHtml.='<tr class="name">
				<td>
					<div class="outer">
						<div class="inner">
							<strong>  '.$journal["name_of_journal"].'</strong>
						</div>
						<div class="arrow"></div>
					</div>
				</td>
			</tr>
			<tr class="journal">
				<td>';
foreach($columns as $key=>$value){if($journal[$key]!=null || $journal[$key]!="")$toHtml .= '<div class="journal_content"><div class="journal_content_columns"><strong>  '.$value.' :	</strong></div><div class="journal_content_data">'.$journal[$key].'</div></div>';}
	
				if($journal["website"]!=null) 
	$toHtml .=
	'<div class="journal_content">
		<div class="journal_content_columns">
			<strong> Website :	</strong>
		</div>
		<div class="journal_content_data">
			<a href="'.$journal["website"].'">Click Here</a>
		</div>
	</div>

				</td>
			</tr>';
			}
$toHtml.='</table>
<script type="text/javascript">
<!--
tableS();

//-->
</script>
';
	echo $toHtml;
	}*/
	

?>