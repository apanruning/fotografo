from random import choice
from django.views.generic import list_detail, simple
from albums.models import Album, Picture

def index(request):
    try:
        album = Album.objects.filter(section='home')[0]
        pictures =  album.picture_set.all()
    except:
        album = None
        pictures = None
    return simple.direct_to_template(
                request,
                'index.html',
                extra_context = {
                    'album' : album,
                    'pictures' : pictures,
                }
            )
            
def album_list(request):
    queryset = Album.object.filter(section='album')
    
    return list_detail.object_list(
                request,
                queryset
            )
