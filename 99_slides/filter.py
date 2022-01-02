"""
Pandoc filter to remove text, but leave headers, equations, and lists
"""

from pandocfilters import toJSONFilter, Str, Para

def noText(key, value, format, meta):
  if key == 'Para':
    return Para([Str("")])

if __name__ == "__main__":
  toJSONFilter(noText)

