#!/usr/bin/env python
# Filename : read_cms_TE

from selinux import *
from sys import argv
import pwd, grp, getpass

def getgroups(user):
    gids = [g.gr_gid for g in grp.getgrall() if user in g.gr_mem]
    gid = pwd.getpwnam(user).pw_gid
    gids.append(grp.getgrgid(gid).gr_gid)
    return [grp.getgrgid(gid).gr_name for gid in gids]

try:
    CMS_groups = ['GN_sub' , 'EN_sub' , 'SN_sub' , 'ESN_sub', 'contributor', 'publisher']

    user_id = getpass.getuser()

    user_groups = getgroups(user_id)

    user_lilcms_group = ''

    for g in user_groups:
        if g in CMS_groups:
            user_lilcms_group = g

    if user_lilcms_group == '':
        raise Exception('user is not assigned to a lilcms group')



    post = argv[1]


    setcon(user_id + '_u:unconfined_r:' + user_lilcms_group + '_t:s0-s0:c0.c1023')
    os.system('cat /var/www/html/posts/'+post)


except Exception,e:
    print(e.message)