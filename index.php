<?
require_once('lib/slim/Slim.php');
require_once('lib/TwigView.php');
require_once('lib/idiorm.php');
require_once('utils.php');

ORM::configure('mysql:host=127.0.0.1;dbname=fotografo');
ORM::configure('username', 'root');
ORM::configure('password', 'mysql');

Slim::init('TwigView');

Slim::get('/', 'home');
Slim::get('/album/', 'album_list');
Slim::post('/album/', 'album_add');
Slim::get('/album/new', 'album_form');
Slim::post('/picture/', 'picture_add');
Slim::get('/picture/new', 'picture_form');



function home(){
    Slim::render('index.html');
}

function album_list(){
    $albums = ORM::for_table('album')->find_many();

    Slim::render( 
        'index.html',
        array(
            'albums' => $albums,
        )
    );
}


function album_form(){
    $albums = ORM::for_table('album')->find_many();

    Slim::render( 
        'album_form.html',
        array(
            'albums' => $albums,
        )
    );
}

function album_add(){
    $params = Slim::request()->post();
    $album = ORM::for_table('album')->create();
    $album->name = $params['name'];
    $album->slug = slugify($params['name']);
    $album->description = $params['description'];
    $album->created = getdate();
    $album->save();
    Slim::redirect( 
        '/album/',
        201
    );
}

function picture_form(){
    $pictures = ORM::for_table('picture')->find_many();

    Slim::render( 
        'picture_form.html',
        array(
            'pictures' => $pictures,
        )
    );
}

function picture_add(){
    $pictures = ORM::for_table('picture')->create();
    $pictures->image = $_POST['image'];
    $pictures->slug = slugify($_POST['name']);
    $pictures->save();
    Slim::redirect( 
        '/picture/',
        201
    );
}


Slim::run();

?>
