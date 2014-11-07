<?php

include '../spherical-geometry.class.php';

// Locations (coords by Google Maps)
$places = array(
    'donostia' => array('lat' => 43.320812, 'lng' => -1.984447),
    'london' => array('lat' => 51.508129, 'lng' => -0.128005),
    'newyork' => array('lat' => 40.71417, 'lng' => -74.00639),
    'sydney' => array('lat' => -33.873651, 'lng' => 151.20689),
    'moscow' => array('lat' => 55.7522222, 'lng' => 37.6155556),
    'buenosaires' => array('lat' => -34.608418, 'lng' => -58.373161)
);

function h2($content) {
    echo '<h2>'.htmlspecialchars($content).'</h2>'."\n";
}

function h3($content) {
    echo '<h3>'.htmlspecialchars($content).'</h3>'."\n";
}

function p($content) {    
    $content = is_string($content) || is_int($content) || is_float($content) 
        ? $content 
        : var_export($content, true);
    echo '<p><code>'.htmlspecialchars($content).'</code></p>'."\n";
}

function float_to_string($float, $decimals = 6) {
    return number_format($float, $decimals, '.', '');
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Spherical geometry tests</title>
	<style type="text/css">
	    html, body, .doc{width:100%;margin:0;padding:0;}
	    h1{padding:0 30px;}
	    h2{border-top: 1px solid #36C;background-color: #E5ECF9;padding:3px 10px;}
	    code{font-size:10pt;font-family: monospace;color: #007000;}
	    .col{width:50%;float:left;}
	    .col .inner{border:1px solid black;padding:20px;}
	</style>
	<script src="http://maps.googleapis.com/maps/api/js?v=3.18&sensor=false&libraries=geometry&language=es&region=ES"></script>
</head>
<body>
    <div class="doc">
        <div class="col">
            <h1>PHP Spherical Geometry class TESTS</h1>
            <div class="inner">
                <?php include 'tests.inc.php'; ?>
            </div>
        </div>
        <div class="col">
            <h1>Google Maps JS API v3.18 TESTS</h1>
            <div class="inner" id="jsresults">
                <script>
                    // A streaming API for the innerHTML property
                    // Define a simple "streaming" API for setting the innerHTML of an element. 
                    function ElementStream(elt) {
                        if (typeof elt === "string") elt = document.getElementById(elt); 
                        this.elt = elt; 
                        this.buffer = "";
                    }
                    // Concatenate all arguments and append to the buffer 
                    ElementStream.prototype.write = function() {
                        this.buffer += Array.prototype.join.call(arguments, "");
                    };
                    // Just like write(), but add a newline 
                    ElementStream.prototype.writeln = function() {
                        this.buffer += Array.prototype.join.call(arguments, "") + "\n";
                    };
                    // Set element content from buffer and empty the buffer. 
                    ElementStream.prototype.close = function() {
                        this.elt.innerHTML = this.buffer; 
                        this.buffer = "";
                    };
                    
                    var places = <?php echo json_encode($places) ?>,
                        bounds = bounds2 = null,
                        spherical = google.maps.geometry.spherical,
                        doc = new ElementStream("jsresults"),
                        h2 = function(content){
                            doc.writeln("<h2>"+content+"</h2>");
                        },
                        h3 = function(content){
                            doc.writeln("<h3>"+content+"</h3>");
                        },
                        p = function(content){
                            doc.writeln("<p><code>"+content+"</code></p>");
                        };
                </script>
                <script src="tests.js"></script>        
                <script>
                    doc.close();
                </script>
            </div>
        </div>
    </div>
</body>
</html>


