from django.db import models

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

