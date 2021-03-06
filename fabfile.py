#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""Deployment script.

This script is coded so it can make the deployments automagically in the 
designed servers.

USE: fab <hosts>:<username> <action>
EX: fab staging:admin release
"""

import os
import sys
import tempfile
import datetime
from string import Template
from fabric.api import env, run, local, require, put, sudo, prompt, cd

BASE_DIR = os.path.dirname(__file__)

def development():
    env.hosts = ["localhost"]
    env.project_name = BASE_DIR.split('/')[-1:].pop()
    env.deploy_dir = '/opt/sites/%s' %env.project_name
    env.virtual_env = '/opt/venvs/%s' %env.project_name
    env.apache_command = 'apache2ctl restart'

def staging():
    pass
    
def production(username="mherrero", hosts=["vaivendo.net"]):
    env.user = username
    env.hosts = hosts
    env.project_name = BASE_DIR.split('/')[-1:].pop()
    env.deploy_dir = '/home/mherrero/webapps/fotografo/src/fotografo'
    env.virtual_env = '/home/mherrero/webapps/fotografo/'
    env.apache_command = '/home/mherrero/webapps/fotografo/apache2/bin/restart'
    
def write_template(file_name, template_name):
    '''
    Pretty much self explanatory
    '''
    template = Template(open(template_name).read())
    rendered_file = open(file_name, 'w')
    rendered_file.write(template.safe_substitute(env))
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
    run(env.apache_command)
