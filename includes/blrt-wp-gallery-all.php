<?php

global $wpdb;
$table = $wpdb->prefix . "blrtwpembed";


if(isset($_GET['action']) && isset($_GET['gallery'])){
    $action = $_GET['action'];
    if( $action === 'trash'){   
        $id = $_GET['gallery'];
        $wpdb->delete( $table, array( 'ID' => $id ) );
    }
}

$result = $wpdb->get_results("SELECT * FROM $table");
?>

<div class = "wrap blrt-embed-plugin">
    <h2>Blrt Galleries
    	<a href="./admin.php?page=blrt-wp-gallery-add" class="page-title-action">Add New</a>
    </h2>
    <table class="blrt-wp-table">
    	<thead>
    		<tr>
		      <th class="title"> <input type="checkbox" name="master-checkbox">Name</th>
              <th>Title</th>
              <th>Shortcode</th>
		      <th>Blrts</th>
		      <th>Last Updated</th>
		    </tr>
    	</thead>
    	<tbody>
    		<?php foreach($result as $row){ 
    			$no_whitespaces = preg_replace( '/\s*,\s*/', ',', filter_var( $row->url, FILTER_SANITIZE_STRING ) ); 
                $array_url = explode( ',', $no_whitespaces );
    			?>
				<tr>
					<td><input type="checkbox" name ="checkbox"> 
                        <strong><a class ="row-title" href = "./admin.php?page=blrt-wp-gallery-add&gallery=<?php echo $row->id ?>&action=edit" ><?php echo $row->name ?></a></strong>
                        <div class = "row-action">
                            <span class = "edit"> 
                                <a class ="row-title" href = "./admin.php?page=blrt-wp-gallery-add&gallery=<?php echo $row->id ?>&action=edit" > Edit</a> |
                            </span>
                            
                            <span class = "trash">
                                <a class ="row-title" href = "./admin.php?page=blrt-wp-gallery-all&gallery=<?php echo $row->id ?>&action=trash" > Trash </a>
                            </span>
                        </div>
                    </td>
                    <td><?php echo $row->title ?></td>
                    <td>[blrt-gallery id=<?php echo $row->id ?>]</td>
					<td><?php echo count($array_url) - 1 ?></td>
					<td><?php echo $row->time ?></td>
				</tr> <?php
			} ?>
    	</tbody>
    </table>
</div>

