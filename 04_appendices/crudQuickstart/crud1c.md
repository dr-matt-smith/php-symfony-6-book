# Explore existing phone/make CRUD pages

## Add `make` to end of URL for phone make admin pages

Visit `localhost:8000/make` - the default for CRUD admin pages is to add the lower case name of the entity to the end of the URL.

    - try adding a new make or editing an existing one ...

See Figure \ref{phone_make}.

![Screenshot of phone make CRUD pages.\label{phone_make}](./03_figures/app_crud/phone_make.png){ width=100% }

## Browse the Phone records

Visit `localhost:8000/phone` for the Phone object admin pages.

    - try adding a new make or editing an existing one ...

See Figure \ref{phone_index}.

![Screenshot of phone model CRUD pages.\label{phone_index}](./03_figures/app_crud/phone_index.png){ width=75% }


## Many Phones to One Model

Edit a Phone record, and you'll see a list of the makes appear as a drop-down list to choose from. This is a **relationship** between each Phone object and a Phone Make object (many-to-one)

See Figure \ref{phone_edit}.

![Editing phone - makes as dropdown menu.\label{phone_edit}](./03_figures/app_crud/phone_edit.png){ width=75% }
