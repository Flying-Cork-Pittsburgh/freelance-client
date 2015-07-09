Freelance Client
=================

WordPress plugin for freelance client sites

You're a web professional that works with multiple clients. What do you wish
you had? I had a couple of ideas, and implemented them in this plugin. You
probably wish for a way to prevent clients from changing things about the way
you've set up the site.  Perhaps you need a way to communicate your contact
information the users of the site.

* An editable dashboard widget for your contact info
* A new role - Site Administrator - that disables some of the more dangerous
  administrator capabilities
* The wordpress update nag is disabled for all non-Administrators, so clients
  don't worry about it.
* you can receive messages from a frelance- manager.


I'd love to hear what your thoughts/needs are.

## Configuration

To prevent unauthorized sending of messages appearing in your dashboard,
you'll need to add the next two lines to your wp-config.php file.

```
define( 'FRECLI_CLIENT_ID', 'client sha goes here' );
define( 'FRECLI_MANAGER_ID', 'manager sha goes here' );
```

