from random import choice
from django.views.generic import list_detail, simple
from django.core.mail import send_mail
from django.shortcuts  import redirect
from albums.models import Album, Picture
from albums.forms import EmailForm


def index(request):
    try:
        album = Album.objects.filter(section='home')[0]
        queryset =  album.picture_set.all()
    except:
        queryset = None
    return list_detail.object_list(
        request,
        queryset,
        template_name='index.html',
        extra_context = {
            'album' : album,
        }
    )
    
def album_list(request):
    queryset = Album.objects.filter(section='album')
    return list_detail.object_list(
        request,
        queryset,
        extra_context = {
            'section': 'album',
        }
    )
            
def album_detail(request, object_id=None):
    queryset = Album.objects.filter(section='album')
    return list_detail.object_detail(
        request,
        queryset,
        object_id,
        extra_context = {
            'section': 'album',
        }
    )
def shop(request):
    queryset = Album.objects.filter(section='shop')
    return list_detail.object_list(
        request,
        queryset,
        template_name='shop.html',
        extra_context={
            'section': 'shop',
        }
    )

def bio(request):
    return simple.direct_to_template(
        request,
        'bio.html',
        extra_context={
            'section': 'shop',
        }
    )

def contact(request,*args,**kwargs):
    form = EmailForm()
    if request.method == 'POST':
        form = EmailForm(request.POST)
        if form.is_valid():

            send_mail(
                'Novo mensagem da nossa web',
                form.data['message'],
                form.data['email'],
                [
                    'vaivendojeri@gmail.com', 
                    'maturburu@gmail.com', 
                    'anacomes@gmail.com'
                ],
            )
            return redirect('/obrigado/')

    return simple.direct_to_template(
        request, 
        'contact.html',
        extra_context={
            'form': form,
            'section': 'contact',
        }
    )

def email_success(request):
    return simple.direct_to_template(request, 'email_success.html')
