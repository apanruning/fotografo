from django.conf.urls.defaults import *
from django.contrib import admin
from django.conf import settings

admin.autodiscover()

urlpatterns = patterns('',
    (r'^$', 'albums.views.index'),

    (r'^album/$', 'albums.views.album_list',),
    (r'^album/(?P<album_id>\d+)$', 'albums.views.album', {'section':'album'}),
    (r'^shop/$', 'albums.views.album', {'section':'shop'}),
    (r'^shop/(?P<album_id>\d+)$', 'albums.views.album', {'section':'shop'}),
    # Uncomment the admin/doc line below to enable admin documentation:
    # (r'^admin/doc/', include('django.contrib.admindocs.urls')),

    # Uncomment the next line to enable the admin:
    (r'^admin/', include(admin.site.urls)),
    (r'^robots\.txt$', lambda r: HttpResponse("User-agent: *\nDisallow: /media/*", mimetype="text/plain")),
)

if settings.DEBUG:
    urlpatterns += patterns('',
        (r'^media/(?P<path>.*)$', 'django.views.static.serve', 
         {'document_root': settings.MEDIA_ROOT})
    )
