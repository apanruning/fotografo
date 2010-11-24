<?
require_once('lib/slim/Slim.php');
require_once('lib/MustacheView.php');
require_once('lib/ImageView.php');
require_once('lib/idiorm.php');
require_once('utils.php');
require_once('config.php');

ORM::configure('mysql:host=127.0.0.1;dbname='.$DATABASE_NAME);
ORM::configure('username', $DATABASE_USER);
ORM::configure('password', $DATABASE_PASS);

Slim::init('MustacheView');

Slim::config('log', true);


Slim::get('/', 'home');
Slim::get('/autor/', 'bio');
Slim::get('/vaivendo/', 'shop');
Slim::get('/contato/', 'contact');
Slim::post('/contato/', 'contact_send');
Slim::get('/album/edit/(:id)', 'album_form');
Slim::get('/album/(:id)', 'album_list');
Slim::delete('/album/:id', 'album_delete');
Slim::post('/album/', 'album_add');
Slim::get('/picture/:id*', 'show_picture');

Slim::notFound('nada');

function album_delete($id){
    Slim::log('Trying to delete: '.$id);

}

function home(){
    $albums = ORM::for_table('album')->where('section','album')->find_many();
    $album = $albums[array_rand($albums)];
    $pictures = ORM::for_table('picture')->where('album_id', $album->id)->find_many();
    Slim::render(
        'index.html',
        array(
            'album' => $album,
            'pictures' => $pictures,
        )
    );
}

function album_list($id=null){
    $albums = ORM::for_table('album')->where('section','album')->find_many();
    $album = is_null($id) ? ORM::for_table('album')->find_one($id) : $albums[array_rand($albums)];
    $pictures = ORM::for_table('picture')->where('album_id', $album->id)->find_many();

    Slim::render( 
        'album.html',
        array(
            'albums' => $albums,
            'album' => $album,
            'pictures' => $pictures,
        )
    );
}

function album_form($id=null){
    $albums = ORM::for_table('album')->find_many();
    $album = is_null($id) ? null : ORM::for_table('album')->find_one($id);
    $pictures = is_null($id) ? null : ORM::for_table('picture')->where('album_id', $id)->find_many();        
    Slim::render( 
        'album_form.html',
        array(
            'albums' => $albums,
            'album' => $album,
            'pictures' => $pictures,
        )
    );
}

function album_add(){
    $params = Slim::request()->post();
    $album = ORM::for_table('album')->create();
    $album->name = $params['name'];
    $album->section = $params['section'];
    $album->slug = slugify($params['name']);
    $album->description = $params['description'];
    $album->created = '';
    $album->save();
    foreach ($_FILES['picture']['error'] as $key => $error) {
        if ($error == UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['picture']['tmp_name'][$key];
            $name = $_FILES['picture']['name'][$key];
            if (!file_exists('pictures/'.$name)){
                move_uploaded_file($tmp_name, "pictures/$name");
                Slim::log('moved file to: pictures/'.$name);
                $picture = ORM::for_table('picture')->create();
                $picture->image = 'pictures/'.$name;
                $picture->album_id = $album->id;
                $picture->save();
            }
        }
    }
    Slim::redirect('/album/edit', 301);
}

function show_picture($id){
    $width = Slim::request()->get('width');
    $height = Slim::request()->get('height');
    $picture = ORM::for_table('picture')->find_one($id);
    ImageView::thumb($picture->image, $width, $height);
}

function contact(){
    Slim::render('contact.html');
}

function nada() {
    Slim::render('404.html');
}


Slim::run();
?>
