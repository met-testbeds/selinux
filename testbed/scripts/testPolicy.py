#!/usr/bin/python
# testPolicy.py
#
# Usage: python testPolicy.py url
#        where   url = testbed (based on lilCMS) http address
#
#        The program contacts our CMS, provides benchmarking queries, and
#        provides several result vectors
#
# Example: 
#          python testPolicy.py http://localhost/lilcms/
#


import sys
import struct
import socket
import time
import select
import re
from optparse import OptionParser

options = OptionParser(usage='%prog server [options]', description='CMS SELinux policy tests')
options.add_option('-p', '--port', type='int', default=80, help='TCP port to test (default: 80)')

#========================================================================
def h2bin(x):
    return x.replace(' ', '').replace('\n', '').decode('hex')


hello = h2bin('''16 03 02 00  dc 01 00 00 d8 03 02 53''')

#========================================================================


#========================================================================
def main():
    opts, args = options.parse_args()
    if len(args) < 1:
        options.print_help()
        return

    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    print 'Connecting'
    sys.stdout.flush()
    s.connect((args[0], opts.port))
    print 'Sending initial message'
    sys.stdout.flush()
    s.send(hello)
    print 'Waiting the initial reply'
    sys.stdout.flush()
    while True:
        typ, ver, pay = recvmsg(s)
        if typ == None:
            print 'Server closed connection'
            return

main ()
