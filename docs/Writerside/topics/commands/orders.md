# Orders

## Pruning test orders

After testing, it is good practice to remove your test orders. In order to facilitate this, we have added
a command that facilitates bulk deletion of orders based on criterias of your choosing. Since we're using elastic
search,
there is a lot of the order object that isn't very searchable and therefore is not queryable from the function.

```Bash
$ php artisan tb:orders:prune --status=n --email-suffix=apility.no --data=is_app=1`
```

will for example find all orders with status `n`, where the email ends with `apility.no` and the order has a data
attribute called `is_app`. The data
attribute can be repeated.

Before any orders are actually deleted a table with all orders that are going to be deleted will be shown. A positive
confirmation is also required before deletion commences.

### Events & Dependent actions

Since we don't use the model in project there might be observers that will not run, if the observers require the in
project
model to work.

In order to facilitate this to some extent, we do emit a `OrderManuallyPruned` event on top of normal lifecycle events
for the `Netflex\Commerce\Order` model. You can add your own listeners to this event if you for example want to cancel
test bookings and\or similar things in response to an order being deleted.
