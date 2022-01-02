#!/usr/bin/env python
from pandocfilters import toJSONFilter
import re

"""
Pandoc filter that causes everything  between
'<!-- BEGIN NOT SLIDE -->' and '<!-- END NOT SLIDE -->'
to be ignored.  The comment lines must appear on
lines by themselves, with blank lines surrounding
them.
"""

incomment = False


def comment(k, v, fmt, meta):
    global incomment
    if k == 'RawBlock':
        fmt, s = v
        if fmt == "html":
            if re.search("<!-- BEGIN NOT SLIDE -->", s):
                incomment = True
                return []
            elif re.search("<!-- END NOT SLIDE -->", s):
                incomment = False
                return []
    if incomment:
        return []  # suppress anything in between these comment

if __name__ == "__main__":
    toJSONFilter(comment)