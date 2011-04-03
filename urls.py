from django.conf.urls.defaults import *
from django.contrib import admin
from django.conf import settings
from django.http import HttpRespose
admin.autodiscover()

urlpatterns = patterns('',
    (r'^$', 'albums.views.index'),

    (r'^album/$', 'albums.views.album_list',),
    (r'^album/(?P<object_id>\d+)$', 'albums.views.album_detail'),
    (r'^vaivendo/$', 'albums.views.shop'),
    (r'^vaivendo/shop$', 'albums.views.shop'),
    (r'^vaivendo/autor$', 'albums.views.bio'),
    (r'^contato/$', 'albums.views.contact'),
    (r'^obrigado/$', 'albums.views.email_success'),
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
