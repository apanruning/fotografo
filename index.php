<?
require_once('lib/slim/Slim.php');
require_once('lib/MustacheView.php');
require_once('lib/ImageView.php');
require_once('lib/idiorm.php');
require_once('utils.php');

ORM::configure('mysql:host=127.0.0.1;dbname=fotografo');
ORM::configure('username', 'root');
ORM::configure('password', 'mysql');

Slim::init('MustacheView');

Slim::config('log', true);


Slim::get('/', 'home');
Slim::get('/album/new', 'album_form');
Slim::get('/album/(:id)', 'album_list');
Slim::delete('/album/(:id)', 'album_delete');
Slim::post('/album/', 'album_add');
Slim::get('/picture/:id(/:width)(/:height)', 'show_picture');

function home(){
    $albums = ORM::for_table('album')->find_many();
    Slim::render(
        'index.html',
        array(
            'albums' => $albums,
        )
    );
}
function album_delete($id){
    Slim::log('Trying to delete: '.$id);
}
function album_list($id=null){
    $albums = ORM::for_table('album')->find_many();
    Slim::log(ORM::for_table('album')->count().' records in album');
    if ($id){
        $album = ORM::for_table('album')->find_one($id);
    }else {
        $album = $albums[array_rand($albums)];
    }
    Slim::log('picked: #'.$album->id.' '.$album->name);
    $pictures = ORM::for_table('picture')->where('album_id', $album->id)->find_many();
    Slim::render( 
        'index.html',
        array(
            'albums' => $albums,
            'album' => $album,
            'pictures' => $pictures,
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
    $album->created = '';
    $album->save();
    foreach ($_FILES['picture']['error'] as $key => $error) {
        if ($error == UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['picture']['tmp_name'][$key];
            $name = $_FILES['picture']['name'][$key];
            move_uploaded_file($tmp_name, "pictures/$name");
            Slim::log('moved file to: pictures/'.$name);
            $picture = ORM::for_table('picture')->create();
            $picture->image = 'pictures/'.$name;
            $picture->album_id = $album->id;
            $picture->save();
            
        }
    }
    Slim::redirect('/album/name', 301);
}

function show_picture($id, $width=null, $height=null){
    $picture = ORM::for_table('picture')->find_one($id);
    ImageView::thumb($picture->image, $width, $height);
}

Slim::run();
?>
