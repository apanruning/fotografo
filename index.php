<?
require_once('lib/slim/Slim.php');
require_once('lib/MustacheView.php');

Slim::init('MustacheView');

Slim::get('/:album', 'home');

function home($what){
    Slim::render( 
        'index.html',
        array('what' => $what)
    );
}

Slim::run();

?>
