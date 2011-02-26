from django import forms

class EmailForm(forms.Form):
    
    email = forms.EmailField()
    message = forms.CharField(widget=forms.Textarea)
