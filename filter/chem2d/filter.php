<?php // $id$
////////////////////////////////////////////////////////////////////////
// Chemistry 2D plugin filter for embedding 2D chemical structures in
// Moodle
//
// Use the Moodle text editor to add terms similar to the following
//
// chem2d::aspirin::250
// generates a 250px x 250px gif image of aspirin
//
// chem2d::P4O10::400
// generates a 400px x 400px gif image of phosphorus (V) oxide
//
// chem2d::Cl/C=C\Cl::300
// generates a 300px x 300px gif image of cis-1,2-dichloroethene
//
// 
// Uses the URL API of the NCI/CADD Chemical Identifier Resolver service
//
// http://cactus.nci.nih.gov/chemical/structure
//
// to generate a .gif of the chemical structure
// 
// Can use a variety of search terms including
// * Chemical name (may not be specific)
// * SMILES 
// * InChI
// * InChIKeys
// 
// For details of using the Chemical Identifier Resolver service see:
// http://cactus.nci.nih.gov/chemical/structure/documentation
// Markus Sitzmann, 2009-2010
//
//
// Moodle filter written by Geoffrey Rowland, August 2010
// geoff dot rowland at yeovil dot ac dot uk
//
////////////////////////////////////////////////////////////////////////

class filter_chem2d extends moodle_text_filter {
//  public function filter($text) {
    function filter($text, array $options = array()){
        global $CFG;
           
        // Just return if text does not contain any chem2d: tags
        if(strpos($text, "chem2d::") === FALSE) {
            return $text;
        }

        if (!function_exists('chem2d_replace')){
            function chem2d_replace($matches){   
                // Replace SMILES triple bond # with URL-safe code
                $matches[2] = str_replace("#", "%23",$matches[2]);
                // Build filter replace string
                $divwidth  =  '<div style="width:'.$matches[3].'px">'; 
                $divborder =  '<div style="width:'.$matches[3].'px; height:'.$matches[3].'px; border:1px solid lightgray">';
                $cactus =     'http://cactus.nci.nih.gov/chemical/structure/';
                //$image =    '<img src="'.$cactus.''.$matches[2].'/image?height='.$matches[3].'&width='.$matches[3].'&bgcolor=transparent&antialiasing=0" alt="" />';
                if(@file_get_contents($cactus,0,NULL,0,1)){
                    $image =      '<img src="'.$cactus.''.$matches[2].'/image?height='.$matches[3].'&width='.$matches[3].'&showstereo=0" alt="" />';
                }else{
                    $image="";
                }
                $divleft =    '<div style="text-align: left">';
                //$divright = '<div style="text-align: right">';
                $title =      get_string('title', 'filter_chem2d');
                $imagefile =  get_string('imagefile', 'filter_chem2d');   
                $control =    '<a title="'.$title.'" target="chem2d" href="'.$cactus.''.$matches[2].'/image?height=500&width=500&linewidth=2&symbolfontsize=16">'.$imagefile.'</a>';          
                $divend  =    '</div>';
    
                $replace =    $divwidth.$divborder.$image.$divend.$divleft.$control.$divend.$divend;
                return $replace;
            }
        }
        $pattern = '/(chem2d::)(.+)::(\d{1,3})/';
        $text = preg_replace_callback($pattern, 'chem2d_replace', $text);

        return $text;
    }
}
?>
