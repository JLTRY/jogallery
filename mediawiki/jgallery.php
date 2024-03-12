<?php


class JGallery {
	public static function onBeforePageDisplay( $article ) {
        $script = '<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>';  
        $article->addHeadItem("jgallery script", $script);
        $style = '<link href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" rel="stylesheet">';
        $article->addHeadItem("jgallery style", $style);
		//$article->addModules("ext.jgallery.jgallery");
	}

	public static function extractOptions( array $options ) {
		$results = array();
		foreach ( $options as $option ) {
            $pair = explode( '=', $option );
            if ( count( $pair ) == 2 ) {
                    $name = trim( $pair[0] );
                    $value = trim( $pair[1] );
                    $results[$name] = $value;
            }
		}
		//Now you've got an array that looks like this:
		//      [foo] => bar
		//      [apple] => orange

		return $results;
	}

	public static function Setup(Parser &$parser) {
		$parser->setHook( 'jgallery', 'JGallery::Render' );
		return true;
	}
    
    public static function RenderImages($options) {
        $thumbnail_s = "thumbs/phoca_thumb_s_";
        $thumbnail_l = "thumbs/phoca_thumb_l_";
        $dir = $options['dir'];
        $image = $options['img'];
        if (array_key_exists('thumb', $options) && $options['thumb']== 'm') {
             $thumbnail_s = "thumbs/phoca_thumb_m_";
        }
        $script = '';//<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>';
        return $script . sprintf('<a data-fancybox="gallery" href="%s/%s%s" data-caption=""><img src="%s/%s%s" max-width="none"></a>',
                $dir, $thumbnail_l, $image,  $dir, $thumbnail_s, $image);
    }


	/**
	 * Render the output
	 * 
	 *
	 * 
	 * 
	 */

	public static function Render($input, $args, $parser, PPFrame $frame ) {
		//Suppose the user invoked the parser function like so:
		//<jgallery 
		$opts = array();
		// Argument 0 is $parser, so begin iterating at 1
		for ( $i = 1; $i < func_num_args(); $i++ ) {
			$opts[] = func_get_arg( $i );
		}
		//The $opts array now looks like this:
		//      [0] => 'foo=bar'
		//      [1] => 'apple=orange'

		//Now we need to transform $opts into a more useful form...
		$options = $args;
        if (array_key_exists('dir', $options)){
            $directory = $options['dir'];        
        } else {
            return "error no directory dir=xxx";
        }
        if (array_key_exists('img', $options)){
            $img = $options['img'];        
        } else {
            return "error no img img=xxx";
        }
        $output = self::RenderImages($options);
        return $output;
		
	}
}
?>