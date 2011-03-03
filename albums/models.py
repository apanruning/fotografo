from django.db import models
from django.contrib import admin

class Album(models.Model):
    name = models.CharField(max_length=60)
    section = models.SlugField()
    description = models.TextField(null=True, blank=True)
    created = models.DateTimeField(auto_now_add=True, editable = False)

    def __unicode__(self):
        return self.name
    
class Picture(models.Model):
    image = models.ImageField(upload_to='pictures')
    album = models.ForeignKey('Album')
    created = models.DateTimeField(auto_now_add=True, editable = False)

    def __unicode__(self):
        return self.image.name

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
