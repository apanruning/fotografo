from django.contrib import admin
from albums.models import Album, Picture

class PictureInLine(admin.TabularInline):
    model = Picture
    extra = 1

class AlbumAdmin(admin.ModelAdmin):
    inlines = [PictureInLine]

admin.site.register(Album, AlbumAdmin)
admin.site.register(Picture)
