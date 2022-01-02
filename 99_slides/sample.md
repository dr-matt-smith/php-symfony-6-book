---
title: My Title
author: Jake Zimmerman
date: \today
fontsize: 12pt
monofont: Menlo
mainfont: Avenir
header-includes:
- \usepackage{pandoc-solarized}
- \input{beamer-includes}
---

<!-- BEGIN COMMENT -->

# Section Title

## First Slide

- Bullets
    - subitem

I am some plain text

<!-- END COMMENT -->


# What I want to work

## Slide 1 - test

I should be ignored

- Shoping
    - apples
    - banananans

## Slide 2 - code

Here is the listing

```php
    <?php
    class Fred
    {
        private $id;

        public function __construct($id)
        {
            $this->id = $id;
        }
    }
```

<!-- BEGIN COMMENT -->

## Slide Title

```python
# This does a thing
def foo():
    return 'bar'
```

<!-- vim:tw=60
-->

<!-- END COMMENT -->
