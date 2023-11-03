# Netflex Toolbox

This toolbox is just collection of nice to haves that have been created during development of
sites. Quickly share or reuse your helpers.

## Enforcing Trailing slashes

We have two components that helps us deal with the old netflex issue of all URLs enforcing
slashes at the end of the url. If you need to ensure this is the case, we can fake this quite easy.

* We have a middleware that redirects all urls that does not end with a slash, to the same url with the trailing slash.
  It is called `add-slash` and takes an optional parameter for redirect status code. For example `add-slash:307`. This
  middleware only supports GET routes. Default status code is 302.
* We have a new URLGenerator that can wrap your existing url generator but will ensure that urls returned by laravel
  will have slashes.

## Removing trailing slashes

We also have a middleware that will redirect to routes without a trailing slash, should one be supplied. This middleware
is called `remove-slash`


## Commands