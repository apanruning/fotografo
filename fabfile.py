#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import sys
import tempfile
import datetime
from string import Template
from fabric.api import env, run, local, require, put, sudo, prompt

BASE_DIR = os.path.dirname(__file__)
env.project_name = BASE_DIR.split('/')[-1:].pop()
    
APACHE_TEMPLATE = Template('''<VirtualHost *:80>
    ServerAdmin maturburu@gmail.com
    ServerName $project_name.com
    ServerAlias *.$project_name.com
    DocumentRoot $deploy_dir
    ErrorLog /var/log/apache2/error.$project_name.log
    LogLevel warn
    CustomLog /var/log/apache2/access.$project_name.log combined
	<Directory "$deploy_dir">
        RewriteEngine On
        RewriteBase /
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule .* /index.php [L,QSA]
		Options -Indexes FollowSymLinks Includes
		AllowOverride All
		Order allow,deny
		Allow from All
	</Directory>
</VirtualHost>
''')


def development():
    env.deploy_dir = BASE_DIR
    env.hosts = ["localhost"]

def staging():
    env.user = 'mherrero'
    env.hosts = ['mherrero.webfactional.com']
    env.deploy_dir = '/home/mherrero/webapps/fotografo'
    
def production():
    env.user = 'mherrero'
    env.hosts = ['mherrero.webfactional.com']
    env.deploy_dir = '/home/mherrero/webapps/fotografo'

def apache_config():
    require("deploy_dir", provided_by=[development, staging, production])
    file_name = '%s.conf'%env.project_name
    rendered_file = open(file_name, 'w')
    rendered_file.write(APACHE_TEMPLATE.safe_substitute(env))
    rendered_file.close()

    return rendered_file

def release(rev='HEAD'):
    """Creates a tarball, uploads it and decompresses it in the rigth path."""

    require("hosts", provided_by=[development, staging, production])    
    tar = "%s-%s.tar.gz" % (env.project_name ,datetime.datetime.now().strftime("%Y%m%d%H%M%S"),)
    local("git archive %s| gzip > %s" %(rev,tar))
    put(tar, tar)
    run("tar xfz %s -C %s" % (tar, env.deploy_dir))
    run("rm %s" %tar)
    local("rm %s" %tar)

def apache_restart():
    """Restarts the program in the servers."""
    require("hosts", provided_by=[development, staging, production])
    sudo("apache2ctl restart")
