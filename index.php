<?
require_once('lib/slim/Slim.php');
require_once('lib/idiorm.php');

require_once('views/TwigView.php');
require_once('views/ImageView.php');

require_once('utils.php');
require_once('config.php');

ORM::configure('mysql:host=127.0.0.1;dbname='.$DATABASE_NAME);
ORM::configure('username', $DATABASE_USER);
ORM::configure('password', $DATABASE_PASS);

Slim::init('TwigView');

Slim::config('log', true);


Slim::get('/', 'home');
Slim::get('/vaivendo/', 'shop');
Slim::get('/vaivendo/shop', 'shop');
Slim::get('/vaivendo/autor', 'bio');
Slim::get('/contato/', 'contact');
Slim::get('/contato/success', 'contact_success');
Slim::post('/contato/', 'contact_send');
Slim::get('/album/edit/(:id)', 'album_form');
Slim::get('/album/', 'album_index');
Slim::get('/album/:id', 'album_list');
Slim::delete('/album/:id', 'album_delete');
Slim::post('/album/', 'album_add');
Slim::get('/picture/:id*', 'show_picture');

Slim::notFound('nada');
Slim::error('roto');

function home(){
    $album = ORM::for_table('album')->where('section','home')->find_one();
    $pictures = ORM::for_table('picture')->where('album_id', $album->id)->find_many();
    Slim::render(
        'index.html',
        array(
            'album' => $album,
            'pictures' => $pictures,
        )
    );
}

function bio(){
    Slim::render('bio.html',
            array(
            'section_shop' => true,
            'title' => 'O autor',
        )
    );
}

function shop(){
    $album = ORM::for_table('album')->where('section','shop')->find_one();
    $pictures = ORM::for_table('picture')->where('album_id', $album->id)->find_many();
    Slim::render(
        'shop.html',
        array(
            'album' => $album,
            'thumbs' => $pictures,
            'pictures' => $pictures,
            'section_shop' => true,
            'title' => $album->name,
        )
    );
}

function album_index(){
    $albums = ORM::for_table('album')->where('section','album')->find_many();
    Slim::render('album_index.html',
            array(
            'albums' => $albums,
            'section_albums' => true,
            'title' => 'Essaios fotograficos',

        )
    );
}

function album_list($id=null){
    $albums = ORM::for_table('album')->where('section','album')->find_many();
    if ($id){
        $album = ORM::for_table('album')->find_one($id);
    } else {
        $album = $albums[array_rand($albums)];
    }
    $pictures = ORM::for_table('picture')->where('album_id', $album->id)->find_many();
    Slim::render( 
        'album.html',
        array(
            'albums' => $albums,
            'album' => $album,
            'thumbs' => $pictures,
            'pictures' => $pictures,
            'section_albums' => true,
            'title' => $album->name,
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

function album_add($id=null){
    $params = Slim::request()->post();
    Slim::log('Id give: '.$id);
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
    Slim::redirect('/album/edit/', 301);
}

function album_delete($id){
    Slim::log('Trying to delete: '.$id);
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
function contact_success(){
    Slim::render('success.html');
}

function contact_send(){
    $params = Slim::request()->post();
    $from_addr = $params['name']." <".$params['email'].">";
    $to = "Regivaldo Freitas <regivaldo@hotmail.com >";
    $subject =  "Novo messagem da nossa web";
    $body = $params['message'];

    $headers = array ("From" => $from_addr,
                      "To" => $to,
                      "Subject" => $subject);
    $smtp = Mail::factory("smtp", array ('host' => "smtp.webfaction.com",
                                         'auth' => true,
                                         'username' => "fotografo_contacto ",
                                         'password' => $DATABASE_NAME));

    $mail = $smtp->send($to, $headers, $body);
    Slim::redirect('/contato/success', 301);
}
function nada() {
    Slim::render('404.html', array('title' => '404 - Nada aqui'));
}
function roto(){
    Slim::render('404.html', array('title' => '500 - error de servidor'));
}

Slim::run();
?>
