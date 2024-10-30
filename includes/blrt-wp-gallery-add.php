<?php
$id = '';
$title = 'Add new gallery';
$publish = 'Publish';
$generation = 'Publish this Blrt gallery to see the generated shortcode.';
$shortcode = '';
$class = '';
$prev_id = 0;
$url_list = '';
$add_new = 'hidden';
$error = '';

global $wpdb;
$table = $wpdb->prefix . "blrtwpembed"; 
$result =  $wpdb->get_col("SELECT ID FROM $table ORDER BY ID DESC LIMIT 1");

if (!$result) {
    $prev_id = 0;
}
else{
    foreach ( $result as $prev_id ) {
    }
}

if ( isset( $_POST["publish"] ) && $_POST["name"] != "" ) {
    $name = strip_tags($_POST["name"], " ");
    if($_POST['publish'] == 'Update'){
        $id = $_GET['gallery'];
        $wpdb->update( 
            $table, 
            array( 
                'name' => $name,  // string
                'url' => $_POST['url'],   // string
                'time' => current_time('mysql'),
                'title' => $_POST['title']
            ), 
            array( 'ID' => $id ), 
            array( 
                '%s',   // value1
                '%s',    // value2
                '%s',
                '%s'
            ), 
            array( '%d' ) 
        );
    }
    else{
        $wpdb->insert( 
            $table, 
            array( 
                'name' => $name,
                'time' => current_time( 'mysql' ),
                'title' => $_POST['title'],
                'url' => $_POST['url']
            )
        );
    }
}



if(isset($_GET['gallery'])){
    $id = $_GET['gallery'];
    $title = 'Edit gallery';
    $publish = 'Update';
    $generation = 'Paste this shortcode in a page or post to insert this gallery.';
    $shortcode = '[blrt-gallery id='.$id.']';
    $class = 'shortcode-holder';
    $result = $wpdb->get_row("SELECT * FROM $table WHERE ID = $id", ARRAY_A);
    if($result === null){
        $error = "Please enter title and Blrt url(s) for the gallery.";
    }
    $result_name = $result['name'];
    $result_title = $result['title'];
    $url_list = $result['url'];
    $result_time = $result['time'];
    $add_new = "page-title-action";
}
?>
<div class = "wrap blrt-embed-plugin">
    <h2><?php echo $title?> 
    <a href="./admin.php?page=blrt-wp-gallery-add" class="<?php echo $add_new ?>">Add New</a>
    </h2>
    <form action="./admin.php?page=blrt-wp-gallery-add&gallery=<?php if(isset($_GET['gallery'])){ echo $id; } else { echo $prev_id + 1; } ?>&action=edit" method="post">
        <div class="blrt-embed-col-8">
            <input type='text' spellcheck="true" name='name' placeholder="Gallery name" autocomplete="off" value="<?php if(isset($_GET['gallery'])){ echo $result_name; } ?>">
            <div class = "container-add-new-gallery" >
                <h3> Gallery Title </h3>
                <p> This optional title will appear above the gallery </p>
                <input type='text' spellcheck="true" name='title' placeholder="Title" autocomplete="off" value="<?php if(isset($_GET['gallery'])){ echo $result_title; } ?>">
                <h3> Add a Blrt </h3>
                <p>Paste a URL for a Blrt to add to your gallery. Find the direct link to your Blrt in Blrt Web, Blrt for iOS or Android.</p>
                <input type="text" name="link" autocomplete="off" id="blrt-embed-link-input">
                <div class = "button-add-new-link" > Add </div> 
                <span class="spinner"></span>
                <div class = "message-add-new-link"></div>
                <h3> Blrts in Gallery </h3>
                <p> These Blrts will appear in this gallery, in whatever page or post it's inserted into. </p>
                <ul id = "blrt-embed-url-placeholder">
                    <input type="hidden" name='url' value="<?php if(isset($_GET['gallery'])){ echo $url_list; }?>">
                    <?php 
                        if($url_list !== ''){
                            $no_whitespaces = preg_replace( '/\s*,\s*/', ',', filter_var( $url_list, FILTER_SANITIZE_STRING ) ); 
                            $array_url = explode( ',', $no_whitespaces );
                            foreach($array_url as $url){
                                if($url !== ''){
                                    $meta = explode('+',$url);
                                    $link = $meta[0];
                                    $title = $meta[1];
                                    $fallback = $meta[2];
                                    ?>
                                    <li class="blrt-wp-url-single">
                                        <h4 ><?php echo $title; ?> </h4><span class="dashicons dashicons-trash"></span><span class="dashicons dashicons-arrow-up-alt"></span><span class="dashicons dashicons-arrow-down-alt"></span>
                                        <p><a class="blrt-link" href="<?php echo $link; ?>">Blrt link</a><a class="fallback-link" href="<?php echo $fallback; ?>">
                                        <?php if($fallback !== ''){
                                            echo "Fallback video";
                                            }else{
                                                echo "Add fallback video";
                                            }
                                         ?> </a>
                                         <span class="fallback-field">
                                             <input type="text" name="fallback_link" value="<?php echo $fallback; ?>"><button class="fallback-add">Add</button><button class="fallback-cancel">Cancel</button>
                                         </span>
                                         </p>
                                        
                                    </li> <?php
                                }
                            }
                        }
                    ?>
                </ul>
            </div>
        </div>
        <div class="blrt-embed-col-4">
            <div class ="container-publish">
                <h3>Publish</h3>
                <div class ="publish-time"><?php if(isset($_GET['gallery'])){ echo "Updated at: ".$result_time; }?></div>
                <div class = "container-publish-action">
                    <div id="publishing-action">
                        <span class="spinner"></span>
                        <input type="submit" name="publish" id="publish" class="button button-primary button-large" value="<?php echo $publish ?>">
                    </div>
                </div>
            </div>
            <div class = "container-shortcode">
                <h3>Shortcode</h3>
                <p><?php echo $generation ?></p> 
                <div class="<?php echo $class ?>"><?php echo $shortcode ?></div>
            </div>
        </div>
    </form>
</div>
