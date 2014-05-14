#!/usr/bin/env python
# Filename : consider_action

from selinux import *
from sys import argv

post= argv[2]
post_type = argv[1]

setcon('unconfined_u:unconfined_r:publisher_t:s0-s0:c0.c1023')
os.system('chcon -t ' + post_type + '_post_public_t /var/www/html/posts/'+post)
