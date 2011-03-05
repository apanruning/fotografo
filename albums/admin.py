from django.contrib import admin
from albums.models import Album, Picture

class PictureInLine(admin.TabularInline):
    model = Picture
    extra = 1


class PictureAdmin(admin.ModelAdmin):
    list_display = ('image', 'album', 'created')
    list_filter = ('album', )
    
class AlbumAdmin(admin.ModelAdmin):
    inlines = [PictureInLine]
    list_display = ('name', 'section', 'created')
    list_filter = ('section', )
    
admin.site.register(Album, AlbumAdmin)
admin.site.register(Picture, PictureAdmin)
