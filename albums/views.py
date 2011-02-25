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
    queryset = Album.objects.filter(section='album')
    return list_detail.object_list(
                request,
                queryset,
                extra_context = {
                    'section' : 'album',
                }
            )
            
def album_detail(request, object_id):
    queryset = Album.objects.filter(section='album')
    return list_detail.object_detail(
                request,
                queryset,
                object_id,
                extra_context = {
                    'section' : 'album',
                }
            )
