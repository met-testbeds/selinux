#!/usr/bin/env python
# Filename : write_cms.py

from selinux import *
from sys import argv

domain = argv[1]
post = argv[2]

setcon('unconfined_u:unconfined_r:'+domain+'_t:s0-s0:c0.c1023')
os.system('echo -e "'+post+'\n" > /var/www/html/posts/'+post)
