# Events

Using the `webhooks` setting available in netflex, you can make Netflex send you webhooks whenever an entry is
`created`, `updated` or `deleted` through the Content API or through Netflexapp. The toolbox adds a standardized endpoint for
receiving all these events, in order to abstract this away and expose the webhooks through laravels event system.

## Setting up webhooks for a structure 

The toolbox registers the route `.well-known/netflex/structure-webhooks` when installed. This route can be inserted into
the `webhooks` config as such

```json
{
  "entry": {
    "10000": {
      "created": [
        "https://www.yoursite.no/.well-known/netflex/structure-webhooks"
      ],
      "updated": [
        "https://www.quotesfromnordnorge.no/.well-known/netflex/structure-webhooks"
      ],
      "deleted": [
        "https://www.arewelive.no/.well-known/netflex/structure-webhooks"
      ]
    }
  }
}
```

## Creating a listener

```PHP
<?php

  // All other code from the listener class has been omitted, run `php artisan make:listener` to make one
  // There are multiple events you can listen to, all of them extend the StructureEvent
  public function handle(StructureEvent $event)
    {
         if($event->event_id === 12345) {
            // Do something based on event id
         }
         
         if($event->directory_id === 12345) {
            // Do something based on directory id
         }
         
         $entry = $event->getEntry();
         
         if($entry instanceof CalendarEvent) {
            // Do something based on the model event
         }
         
    }
```

## Registering a listener
You can register event listeners as normal in your EventServiceProvider.

```PHP
<?php

  protected $listen = [
        EntryCreated::class => [
            ClearCalendarCache::class
        ],

        EntryUpdated::class => [
            ClearCalendarCache::class
        ],

        EntryDeleted::class => [
            ClearCalendarCache::class
        ]
    ];
```

Notice that you have to register Create, Update, and Delete events separately as Laravel does not let you listen to 
the parent event. If you want, its trivial to write a listener for the super class and just register for each child event
as all events have the same functions and fields.

## Caching
`EntryCreated` and `EntryUpdated` will only resolve the underlying entry once, then return the same instance on subsequent calls.
It is advised that you do not edit the event as you might break assumptions if there should be/are multiple listeners.

## EntryDeleted data
When EntryDeleted is triggered, the entry is already gone, and you can't fetch the entry from the database anymore.
We do get the entry as it existed when deleted however, so the event will return a hydrated object based on these data.

For all intents and purposes the event will look like it came from the database, but looking up relationships etc might
not work as expected. 

 

