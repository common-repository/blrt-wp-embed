<?php
add_shortcode( 'blrt-gallery', 'blrt_gallery' );
function blrt_gallery($atts){
	global $blrt_wp_embed_used;
	$blrt_wp_embed_used = true;
	$a = shortcode_atts( array(
		'id' => '',
        'title' => '',
        'size' => 'medium',
        'position' => 'right',
        'skin' => 'light',
        'url' => '',
    ), $atts );
    
    ini_set("allow_url_fopen", 1);    
    $html = file_get_contents('html/gallery.html',true);
    if($a['id'] !== ''){//id exists, do query to database to get content
    	$id = $a['id'];
    	global $wpdb;
		$table = $wpdb->prefix . "blrtwpembed"; 
		$result = $wpdb->get_row("SELECT * FROM $table WHERE ID = $id", ARRAY_A);
		if($result === null){
	        $html = "Fail to query the following id: ".$a['id'];
	    }
	    else{
	    	$a['title'] = $result['title'];
    		$a['url'] = $result['url'];
	    }
    }
   
    $no_whitespaces = preg_replace( '/\s*,\s*/', ',', filter_var( $a['url'], FILTER_SANITIZE_STRING ) ); //truncate white spaces
    $array_url = explode( ',', $no_whitespaces );
    if (empty(array_values(array_slice($array_url, -1))[0])) {
    	array_pop($array_url);
    }
    
    $direction = 'horizontal';
    if($a['position'] == 'left' || $a['position'] == 'right'){
    	$direction = 'vertical';
    }
    $no_title = "";
    if($a['title'] === ''){
			$no_title = "hidden";
	}
    $desktop = '';
    $mobile = '';
    $count = 1;
    $playing = '';
    $src = '';
    $fallBack ='';
    $hidden = '';
    
    $logo  = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
			 width="28px" height="28px" viewBox="0 0 58.352 26.672" enable-background="new 0 0 58.352 26.672" xml:space="preserve">
		<g>
			<path fill="#00E7A8" d="M21.587,13.902c-1.346-1.355-3.207-2.182-5.262-2.182c0,0-0.183,0-0.424,0.003
				c1.412-0.324,2.622-1.131,3.463-2.255c0.731-0.986,1.183-2.201,1.183-3.53C20.547,2.656,17.889,0,14.61,0H5.281
				C2.365,0,0,2.362,0,5.284v16.045c0,2.916,2.365,5.283,5.281,5.283h11.026c0.364,0,0.72-0.037,1.062-0.105
				c3.609-0.514,6.397-3.588,6.397-7.338C23.768,17.108,22.934,15.241,21.587,13.902 M12.654,20.703
				c-0.097,0.007-0.205,0.015-0.309,0.015H9.787c-1.637,0-2.967-1.332-2.967-2.969V8.743c0-1.638,1.33-2.967,2.967-2.967
				c0,0,1.741-0.007,1.79-0.007c1.79,0,3.243,1.449,3.243,3.241c0,0.791-0.279,1.508-0.748,2.071c-0.002,0.005-0.006,0.011-0.01,0.014
				c-0.082,0.108-0.189,0.221-0.31,0.317c-0.399,0.363-1.27,0.705-2.151,0.969c0.056-0.002,0.103-0.008,0.142-0.013
				c0.188-0.026,0.37-0.04,0.563-0.04c2.355,0,4.263,1.878,4.263,4.193C16.569,18.728,14.846,20.524,12.654,20.703"/>
			<path fill="#00E7A8" d="M33.556,22.63H32.48c-0.018,0-0.038,0.002-0.06,0.002c-0.02,0-0.039-0.002-0.059-0.002h-0.057l-0.004-0.004
				c-0.836-0.061-1.492-0.758-1.492-1.605c0-0.025,0.001-0.042,0.002-0.063l-0.001-0.001V20.31V2.938H30.81
				c0-1.602-1.29-2.899-2.88-2.906l0,0h-1.633l0,0c-0.376,0-0.684,0.306-0.684,0.685l0,0v19.937v1.26c0,0.125,0.002,0.23,0.007,0.346
				c0.101,2.418,2.052,4.348,4.476,4.413h3.596c0.232-0.58,0.354-1.204,0.354-1.862C34.046,24.03,33.872,23.286,33.556,22.63z"/>
			<path fill="#00E7A8" d="M53.537,12.646h4.483c0.209-0.561,0.332-1.169,0.332-1.799c0-0.771-0.174-1.502-0.483-2.153h-4.332V6.827
				V3.878l0,0c0-0.378-0.31-0.687-0.685-0.687V3.189h-1.627h-0.002c-1.594,0.014-2.885,1.312-2.885,2.91l0,0v0.728v13.75v1.27
				c0,0.114,0.008,0.221,0.013,0.338c0.101,2.414,2.048,4.344,4.472,4.408h4.816c0.227-0.576,0.354-1.203,0.354-1.861
				c0-0.775-0.18-1.52-0.495-2.179h-2.292c-0.021,0.001-0.035,0.001-0.059,0.001c-0.018,0-0.038,0-0.059-0.001H55.03v-0.005
				c-0.836-0.061-1.493-0.76-1.493-1.604c0-0.024,0-0.04,0-0.062v-0.002v-0.646V12.646z"/>
			<path fill="#00E7A8" d="M45.727,7.984L45.727,7.984c-3.569-0.055-5.435,2.785-5.435,2.785c-0.81-1.391-1.993-2.325-3.711-2.325
				c-0.406,0-0.797,0.053-1.172,0.153c-0.729,0.187-1.387,0.544-1.93,1.029c-0.125,0.112-0.252,0.233-0.363,0.357
				c-0.17,0.188-0.328,0.393-0.469,0.611c0.253-0.131,0.533-0.22,0.832-0.258h0.004c0.109-0.014,0.217-0.021,0.334-0.021
				c0.974,0,1.822,0.543,2.257,1.343c0.198,0.366,0.315,0.79,0.315,1.229c0,0.078,0,1.485,0,3.336c0,2.229,0.001,5.467,0.001,7.721
				c0,0.667,0,0.625,0,2.006c0,0.377,0.306,0.684,0.683,0.684h1.146h1.641h0.826c0.375,0,0.684-0.307,0.684-0.684
				c0-3.193,0-3.227,0-3.227l0,0l0.001-6.982c0.33-1.573,2.149-3.223,4.382-2.611c0.008,0.003,0.015,0.005,0.025,0.006
				c0.285,0.085,0.631-0.119,0.631-0.578V12.46V8.657C46.401,8.282,46.1,7.984,45.727,7.984z"/>
		</g>
		</svg>';
						
	$playIcon = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
			 width="35px" height="35px" viewBox="0 0 102.053 102.05" enable-background="new 0 0 102.053 102.05"
			 xml:space="preserve">
		<path opacity="0.9" fill-rule="evenodd" clip-rule="evenodd" fill="#00E7A8" stroke="#FFFFFF" stroke-miterlimit="10" d="
			M47.584-144.917c-27.906,0-50.529,22.621-50.529,50.524s22.623,50.526,50.529,50.526c27.904,0,50.524-22.623,50.524-50.526
			S75.488-144.917,47.584-144.917z M63.916-102.472c0,0.102,1.312,0.146,1.99,0.301c2.912,0.686,4.55,3.74,4.521,5.884
			c-0.052,7.956-0.062,15.899-0.062,23.846c0,3.091-1.842,5.444-4.846,6.181c-0.463,0.116-0.982,0.158-1.471,0.158
			c-10.941,0.004-21.875-0.01-32.836,0.006c-3.386,0-5.725-2.365-6.25-5.201c-0.054-0.267-0.055-0.539-0.055-0.808
			c-0.018-8.009-0.018-16.017,0-24.027c0-3.588,2.748-6.242,6.322-6.313c0.17-0.012,0.686-0.005,0.686-0.005c0-0.194,0-0.364,0-0.519
			c0-1.789-0.172-3.577-0.166-5.368c0-7.697,5.049-14.183,12.578-15.817c8.854-1.931,16.984,3.758,18.922,11.971
			c0.291,1.253,0.5,2.547,0.538,3.834c0.088,1.847,0.128,3.695,0.128,5.55C63.916-102.687,63.916-102.563,63.916-102.472z
			 M57.162-102.405c0-1,0.016-3.894,0-5.816c-0.008-0.661-0.035-1.326-0.172-1.975c-0.977-5.085-5.812-8.57-11.131-7.621
			c-4.715,0.83-7.943,4.777-7.943,9.559c0,1.771,0,3.55,0,5.318c0,0.164,0,0.535,0,0.535
			C44.916-102.405,50.781-102.405,57.162-102.405z M47.575-73.405c1.021,0,2.031,0,3.078,0c0.034,0,0.055,0,0.055,0
			c1.263,0,1.966-0.918,1.761-2.143c-0.506-3.109-1.021-6.268-1.535-9.378c-0.039-0.253,0-0.423,0.206-0.589
			c0.67-0.566,1.185-1.275,1.498-2.106c0.759-2.057,0.408-3.917-1.048-5.539c-1.296-1.487-2.993-2.024-4.91-1.657
			c-2.182,0.421-3.619,1.835-4.158,3.932c-0.534,2.051,0.077,3.89,1.705,5.323c0.216,0.182,0.271,0.356,0.216,0.622
			c-0.553,3.086-1.101,6.171-1.661,9.254c-0.234,1.248,0.471,2.183,1.742,2.193C45.545-73.48,46.553-73.405,47.575-73.405z"/>
		<path opacity="0.9" fill-rule="evenodd" clip-rule="evenodd" fill="#00E7A8" stroke="#FFFFFF" stroke-miterlimit="10" d="
			M51.029,0.5C23.123,0.5,0.5,23.12,0.5,51.023s22.623,50.526,50.529,50.526c27.904,0,50.523-22.623,50.523-50.526
			S78.934,0.5,51.029,0.5z M76.378,51.881l-2.043,1.482l-37.524,27.29c-0.186,0.136-0.406,0.203-0.626,0.203
			c-0.165,0-0.93-0.038-1.084-0.114c-0.354-0.181-1.185-0.547-1.185-0.942V22.25c0-0.398,0.83-0.764,1.185-0.943
			c0.354-0.179,1.095-0.147,1.413,0.088l39.714,28.773c0.275,0.2,0.512,0.517,0.512,0.855C76.739,51.363,76.653,51.68,76.378,51.881z"
			/>
		</svg>';
    
    $baseItem = 3;//number of snippet per slide
    
    foreach($array_url as $url){
    	$meta = explode('+',$url);
        $url = $meta[0];
        $url = $url.'?';
        $fallback = $meta[2];
        if($url != '' && $fallback != ''){
        	$url = $url.'&fallback=yt&yturl='.urlencode($fallback);
        }
    	if($count == 1){
    		$src = $url;
    		$playing = 'playing';
    		if ($fallback) {
    			$fallBack = 'fallback';
    		}
    	}
    	else{
    		$hidden = 'hidden';
    		$playing = '';
    	}
    	$rem  = $count % $baseItem;
    	if($rem == 1){
    	    $mobile .= '<ul class="slide">';
    	}
		if($url != '?'){
			
	    	$index = strpos($url,'embed/blrt/');
			$index = $index + strlen('embed/blrt/');
			$id = substr($url, $index, 10);
			$stream = "https://m.blrt.co/blrt/".$id.".json";
			
			$json = file_get_contents($stream);
			$json_data = json_decode($json, true);
			
			$data = $json_data['data'];
		
			$createdAt = date("d M Y", strtotime($data['createdAt']));
			$duration = $data['duration'];
			$duration = substr($duration, 0, strlen($duration));
			$public = 'Private ';
			if($data['isPublicBlrt']){
				$public = 'Public ';
			}
			$int = (int) $duration;
			$min = (int) ($int / 60);
			$sec = $int % 60;
			if($sec < 10){
			    $sec = '0'.$sec;
			}
			if($min < 10){
			    $duration = '0'.$min.':'.$sec;
			}
			else{
			    $duration = $min.':'.$sec;
			}
			
	    	$desktop .='<li class="blrt-item '. $playing. '" data-blrt="'.$url.'" data-fallback="'.($fallback ? "true" : "false").'">
	    					<div class = "canvas-thumbnail" style="background-image: url('.$data['thumbnail'].')"></div>
	    					<div class = "frame"><span>Now playing</span></div>
	    					<div class = "status-icon">'. $playIcon .'</div>
	    					<p>'.$data['title'].'</p>
	    				</li>';
	    	
	    	$mobile .= '<li class="snippet" data-blrt="https://e.blrt.com/blrt/'.$id.'">'
			    			.'<div class = "thumbnail"><img src= "'.$data['thumbnail'].'"/></div>'
			    			.'<div class = "content">'
			    				.'<div class = "meta"><span class = "public">'.$public.' </span>'.$logo.' <a href = "http://www.blrt.com/whats-a-blrt" target="_blank" ><span class="what"> What&rsquo;s a Blrt? </span> </a></div>'
			    				.'<div class = "name">'.$data['title'].'</div>'
			    				.'<div class = "creator"> By '.$data['creator'].'</div>'
			    				.'<div class = "desc"><span class= "createAt">'.$createdAt.'</span><span class = "duration">Duration '.$duration.'</span></div>'
			    			.'</div>'
		    			.'</li>';
		    			
		    if($rem == 0 || $count == count($array_url)){
	    	    $mobile .= '</ul>';
	    	}
		    $count += 1;
		}
    }
    
    
	$version = time();
	$plugin_dir = plugins_url('', dirname(__FILE__));
    $html = sprintf($html, $a['title'], $a['size'], $a['position'], $a['skin'], $desktop, $mobile, $src, $version, $direction, $no_title, $plugin_dir, $fallBack);
    //$html = $script.$style.$html;
    return $html;
}
?>